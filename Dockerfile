# syntax=docker/dockerfile:1
#
# Application image for the Naft Javan High School website.
#
#   PHP 8.4 (FPM)  +  Composer 2  +  Node.js 22 / npm
#
# Node is installed inside the app image on purpose: `npm run build` must be
# runnable inside the container (see AGENTS.md section 5 - Docker & Build
# Workflow) without depending on a host Node installation.

# ---------------------------------------------------------------------------
# Node.js 22 + npm are taken from the official Node image (no third party repo)
# ---------------------------------------------------------------------------
FROM node:22-bookworm-slim AS node

# ---------------------------------------------------------------------------
# Application image
# ---------------------------------------------------------------------------
FROM php:8.4-fpm-bookworm

ENV DEBIAN_FRONTEND=noninteractive \
    APP_DIR=/var/www/html \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/var/www/.composer \
    COMPOSER_NO_INTERACTION=1 \
    COMPOSER_MEMORY_LIMIT=-1 \
    npm_config_cache=/var/www/.npm

# ---------------------------------------------------------------------------
# System packages
# ---------------------------------------------------------------------------
# Build/runtime libraries for the PHP extensions, plus git/unzip for Composer.
#
# `imagemagick` is the runtime half of the only image engine the application
# uses (see the `image_driver` entry in config/media-library.php): the
# `convert`/`identify` binaries plus the codec libraries (jpeg, png, gif, webp,
# tiff, heic/avif, svg). `ghostscript` is the renderer ImageMagick hands PDF
# input to. The matching headers for the PECL build of the `imagick` extension
# are installed and purged again in the extension layer below.
#
# There is no `gd` counterpart here: no `libfreetype`, `libjpeg` or `libpng`
# headers, and no `gd` extension.
RUN apt-get update && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        ghostscript \
        git \
        gnupg \
        gosu \
        unzip \
        zip \
        imagemagick \
        libcurl4-openssl-dev \
        libgmp-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------------------------
# Composer 2
# ---------------------------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------------------------------------------------------------------------
# Node.js 22 + npm (copied from the official Node image - no third party repo)
# ---------------------------------------------------------------------------
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && node --version \
    && npm --version \
    && composer --version

# ---------------------------------------------------------------------------
# PHP extensions
# ---------------------------------------------------------------------------
# php:8.4-fpm already ships pdo_sqlite, mbstring, xml, curl and opcache, so only
# the missing ones are built here. `imagick` is not part of the PHP source tree
# and therefore comes from PECL; `$PHPIZE_DEPS` is the toolchain that build
# needs. Both the toolchain and the ImageMagick headers are purged in the same
# layer, so only the extension itself survives.
#
# `gd` is deliberately absent: ImageMagick is the single image engine of the
# application. The two assertions below fail the build when either half of it is
# missing - the extension, or the `convert` binary without the delegates the
# media library converts to.
RUN apt-get update \
    && apt-get install -y --no-install-recommends $PHPIZE_DEPS libmagickwand-dev \
    && pecl install imagick-3.8.1 \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql zip intl bcmath pcntl exif \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
        $PHPIZE_DEPS libmagickwand-dev \
    && rm -rf /var/lib/apt/lists/* /tmp/pear /tmp/*

RUN php -r '$required = ["pdo_sqlite", "pdo_mysql", "mbstring", "xml", "curl", "zip", "intl", "imagick", "bcmath", "pcntl", "exif"]; $loaded = get_loaded_extensions(); foreach ($required as $extension) { if (! in_array($extension, $loaded, true)) { fwrite(STDERR, "missing PHP extension: {$extension}\n"); exit(1); } } if (! in_array("Zend OPcache", $loaded, true)) { fwrite(STDERR, "missing PHP extension: opcache\n"); exit(1); } echo "all required PHP extensions are loaded\n";'

RUN convert -version \
    && php -r 'foreach (["JPEG", "PNG", "GIF", "WEBP", "TIFF", "HEIC", "AVIF"] as $format) { if (Imagick::queryFormats($format) === []) { fwrite(STDERR, "imagick has no {$format} delegate\n"); exit(1); } } echo "imagick delegates are available\n";'

# ---------------------------------------------------------------------------
# PDF input (ghostscript + the ImageMagick coder policy)
# ---------------------------------------------------------------------------
# Debian ships ghostscript, but its /etc/ImageMagick-6/policy.xml denies the
# PDF/PS/EPS/XPS coders - the hardening that followed the ghostscript remote
# code executions. Policy rules are evaluated in order and the last match wins,
# so appending a `read` rule re-opens PDF *input* only: PostScript, EPS and XPS
# stay denied, and writing a PDF stays denied too.
#
# The assertion converts a PDF generated on the spot, because the media library
# only thumbnails a PDF when both halves (the binary and the policy) are in
# place.
RUN sed -i 's|</policymap>|  <policy domain="coder" rights="read" pattern="PDF" />\n</policymap>|' /etc/ImageMagick-6/policy.xml \
    && grep -q 'rights="read" pattern="PDF"' /etc/ImageMagick-6/policy.xml \
    && printf '%s\n' '%!PS-Adobe-3.0' '/Helvetica findfont 12 scalefont setfont 10 10 moveto (pdf probe) show showpage' > /tmp/probe.ps \
    && gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=/tmp/probe.pdf /tmp/probe.ps \
    && convert /tmp/probe.pdf /tmp/probe.png \
    && test -s /tmp/probe.png \
    && rm -f /tmp/probe.ps /tmp/probe.pdf /tmp/probe.png \
    && echo "pdf input is available"

# ---------------------------------------------------------------------------
# Application code
# ---------------------------------------------------------------------------
WORKDIR ${APP_DIR}

# Composer dependencies first: this layer is only invalidated when the
# composer manifest/lock change, not on every code change.
COPY composer.json composer.lock ./
COPY artisan artisan
COPY bootstrap/ bootstrap/
COPY config/ config/
COPY app/ app/
COPY routes/ routes/
COPY resources/ resources/
COPY public/ public/
COPY database/ database/

# Framework scripts are allowed to run (post-autoload-dump -> package:discover).
#
# The build has no .env file, so the broadcaster falls back to the default in
# config/broadcasting.php (reverb) and Pusher would boot without a key. The
# variable is therefore scoped to this single build step: an ENV line would
# survive into the runtime container and override BROADCAST_CONNECTION from
# the mounted .env / env_file.
#
# Composer fetches the dist archives from api.github.com, which limits anonymous
# requests to 60 an hour - far below the ~110 packages installed here. The token
# is mounted as a build secret so it never lands in an image layer, and a build
# without the secret still works because COMPOSER_AUTH then falls back to an
# empty credential set.
RUN --mount=type=secret,id=composer_auth \
    BROADCAST_CONNECTION=null \
    COMPOSER_AUTH="$(cat /run/secrets/composer_auth 2>/dev/null || echo '{}')" \
    composer install --no-interaction --prefer-dist --no-progress

# Node dependencies (package-lock.json is the source of truth).
COPY package.json package-lock.json .npmrc ./
RUN npm ci --no-audit --no-fund

# Remaining application files.
COPY . .

# ---------------------------------------------------------------------------
# Frontend build
# ---------------------------------------------------------------------------
# The compiled Vite assets are part of the image: a standalone deployment must
# be able to render the pages without running a build on the host (the image
# would otherwise reach @vite without public/build/manifest.json).
#
# The pristine copy in /opt/app-build survives the bind mount of the working
# copy in the compose setup - the entrypoint restores it when the mounted
# public/build is empty.
RUN npm run build \
    && test -f public/build/manifest.json \
    && mkdir -p /opt/app-build \
    && cp -a public/build/. /opt/app-build/

# ---------------------------------------------------------------------------
# Runtime directories
# ---------------------------------------------------------------------------
# The home directory of www-data (/var/www) must be writable by the non-root
# user, otherwise npm and composer cannot keep their caches.
#
# The container itself starts as root: the entrypoint prepares the (bind
# mounted) application tree and then drops the privileges to www-data before
# php-fpm is started.
RUN mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/testing \
        storage/logs \
        bootstrap/cache \
        /var/www/.npm \
        /var/www/.composer \
        /var/www/.cache \
    && chown -R www-data:www-data storage bootstrap/cache vendor node_modules public/build /var/www \
    && chmod -R ug+rwX storage bootstrap/cache \
    && git config --system --add safe.directory /var/www/html

COPY --chmod=0755 docker/entrypoint.sh /usr/local/bin/entrypoint

# ---------------------------------------------------------------------------
# Runtime
# ---------------------------------------------------------------------------
# The entrypoint runs as root (it has to fix the ownership of the mounted
# application tree) and hands the process over to www-data through gosu.
WORKDIR ${APP_DIR}
EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["php-fpm"]
