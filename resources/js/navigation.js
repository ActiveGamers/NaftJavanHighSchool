/**
 * Navigation — mobile drawer, sticky header elevation and active link state.
 *
 * Markup contract (rendered by <x-site-header />):
 *   [data-drawer-open]      button that opens the drawer
 *   [data-drawer-close]     button(s) that close the drawer
 *   [data-drawer]           the off-canvas panel itself
 *   [data-drawer-backdrop]  the dimmed backdrop behind the panel
 *   [data-site-header]      the sticky header wrapper
 *   [data-header-bar]       the solid bar inside the header
 *   [data-nav-link]         any navigation link
 */

const FOCUSABLE = 'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])';

/** Lock the page behind the drawer without the layout jumping. */
function lockScroll() {
    const gutter = window.innerWidth - document.documentElement.clientWidth;

    document.body.style.overflow = 'hidden';

    if (gutter > 0) {
        document.body.style.paddingInlineEnd = `${gutter}px`;
    }
}

/** Release the scroll lock. */
function unlockScroll() {
    document.body.style.overflow = '';
    document.body.style.paddingInlineEnd = '';
}

/** The trigger that opened the drawer; the focus goes back to it on close. */
let drawerOpener = null;

/** Pending timer that hides the backdrop again after the close animation. */
let backdropTimer = null;

/** Open the mobile drawer. */
function openDrawer(drawer, backdrop, opener = null) {
    if (!drawer || drawer.dataset.open === 'true') {
        return;
    }

    drawer.dataset.open = 'true';
    drawer.setAttribute('aria-hidden', 'false');
    drawer.removeAttribute('inert');

    /* The trigger lives in the header, outside the drawer: it is kept aside
       so the focus can return to it when the drawer closes. */
    if (opener) {
        drawerOpener = opener;
    }

    document.querySelectorAll('[data-drawer-open]').forEach((trigger) => {
        trigger.setAttribute('aria-expanded', 'true');
    });

    if (backdrop) {
        /* A quick close/reopen pair leaves the hide timer of the close
           animation pending: it would hide the backdrop of the drawer that is
           open again, so it is cancelled before the backdrop is shown. */
        window.clearTimeout(backdropTimer);
        backdrop.classList.remove('hidden');
        window.requestAnimationFrame(() => backdrop.classList.add('opacity-100'));
    }

    lockScroll();
    drawer.querySelector('[data-drawer-close]')?.focus();
}

/** Give the keyboard back to the trigger that opened the drawer. */
function restoreFocus() {
    const opener = drawerOpener ?? document.querySelector('[data-drawer-open]');

    drawerOpener = null;

    /* The trigger is hidden on wide screens: there is nothing to focus then. */
    if (!opener || opener.hidden || opener.closest('[hidden]')) {
        return;
    }

    if (window.getComputedStyle(opener).display === 'none') {
        return;
    }

    opener.focus();
}

/** Close the mobile drawer. */
function closeDrawer(drawer, backdrop) {
    if (!drawer || drawer.dataset.open !== 'true') {
        return;
    }

    drawer.dataset.open = 'false';
    drawer.setAttribute('aria-hidden', 'true');
    drawer.setAttribute('inert', '');

    document.querySelectorAll('[data-drawer-open]').forEach((trigger) => {
        trigger.setAttribute('aria-expanded', 'false');
    });

    if (backdrop) {
        backdrop.classList.remove('opacity-100');
        backdropTimer = window.setTimeout(() => backdrop.classList.add('hidden'), 320);
    }

    unlockScroll();
    restoreFocus();
}

/** Trap Tab inside the drawer while it is open. */
function trapFocus(event, drawer) {
    if (event.key !== 'Tab') {
        return;
    }

    const items = Array.from(drawer.querySelectorAll(FOCUSABLE)).filter(
        (element) => element.offsetParent !== null,
    );

    if (!items.length) {
        return;
    }

    const first = items[0];
    const last = items[items.length - 1];

    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
}

/** Wire the drawer open/close behaviour. */
function initDrawer() {
    const drawer = document.querySelector('[data-drawer]');
    const backdrop = document.querySelector('[data-drawer-backdrop]');

    if (!drawer) {
        return;
    }

    drawer.dataset.open = 'false';
    drawer.setAttribute('aria-hidden', 'true');
    drawer.setAttribute('inert', '');

    document.querySelectorAll('[data-drawer-open]').forEach((trigger) => {
        trigger.setAttribute('aria-expanded', 'false');
    });

    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-drawer-open]');

        if (opener) {
            event.preventDefault();
            openDrawer(drawer, backdrop, opener);
            return;
        }

        const closer = event.target.closest('[data-drawer-close]');

        if (closer) {
            event.preventDefault();
            closeDrawer(drawer, backdrop);
        }
    });

    backdrop?.addEventListener('click', () => closeDrawer(drawer, backdrop));

    document.addEventListener('keydown', (event) => {
        if (drawer.dataset.open !== 'true') {
            return;
        }

        if (event.key === 'Escape') {
            closeDrawer(drawer, backdrop);
            return;
        }

        trapFocus(event, drawer);
    });

    // Leaving the mobile breakpoint must never leave the drawer stuck open.
    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
        if (event.matches) {
            closeDrawer(drawer, backdrop);
        }
    });
}

/** Add a shadow to the header once the page is scrolled. */
function initHeaderElevation() {
    const header = document.querySelector('[data-site-header]');

    if (!header) {
        return;
    }

    const update = () => {
        header.dataset.scrolled = window.scrollY > 8 ? 'true' : 'false';
    };

    update();
    window.addEventListener('scroll', update, { passive: true });
}

/** Mark the navigation link matching the current path. */
function initActiveLinks() {
    const path = window.location.pathname.replace(/\/+$/, '') || '/';

    document.querySelectorAll('[data-nav-link]').forEach((link) => {
        const href = link.getAttribute('href');

        if (!href) {
            return;
        }

        let linkPath;

        try {
            linkPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
        } catch (error) {
            return;
        }

        const isHome = linkPath === '/';
        const active = isHome
            ? path === '/'
            : path === linkPath || path.startsWith(`${linkPath}/`);

        link.dataset.active = active ? 'true' : 'false';

        if (active) {
            link.setAttribute('aria-current', 'page');
        } else {
            link.removeAttribute('aria-current');
        }
    });
}

export function initNavigation() {
    initDrawer();
    initHeaderElevation();
    initActiveLinks();
}
