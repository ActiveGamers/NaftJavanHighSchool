<x-layouts.app
    :title="'افتخارات کسب‌شده'"
    :description="'کارنامه افتخارات هنرستان جوار شرکت پالایش نفت بندرعباس؛ رتبه اول کشوری در سال تحصیلی ۱۴۰۳ و رکورد سه‌ساله رتبه‌های سه‌رقمی، دو‌رقمی و تک‌رقمی کشوری.'">

    @php
        $achievementCollection = collect($achievements)->values();

        $faDigits = fn ($value): string => \App\Support\PersianDigits::toPersian($value);

        /* The headline achievement: the national first rank, when it is
           recorded; otherwise the first entry of the list. */
        $featured = $achievementCollection->first(
            fn ($item) => str_contains((string) data_get($item, 'rank_label'), 'رتبه اول')
        ) ?? $achievementCollection->first();

        $featuredIndex = $featured ? $achievementCollection->search(fn ($item) => $item === $featured) + 1 : null;
    @endphp

    {{-- ۱ · سربرگ -------------------------------------------------------- --}}
    <x-page-hero
        eyebrow="کارنامه افتخارات"
        title="افتخارات کسب‌شده"
        description="هنرستان جوار شرکت پالایش نفت بندرعباس در سه سال اخیر رتبه‌های پیاپی کشوری را در حوزه شبکه و صنایع مکاترونیکی به خود اختصاص داده است."
        :items="[
            ['label' => 'خانه', 'href' => route('home')],
            ['label' => 'افتخارات'],
        ]"
        :facts="[
            ['label' => 'دستاوردهای ثبت‌شده', 'value' => $faDigits($achievementCollection->count()).' مورد'],
            ['label' => 'بازه زمانی', 'value' => '۱۴۰۱ تا ۱۴۰۳'],
            ['label' => 'سطح رقابت', 'value' => 'کشوری'],
        ]"
    />

    {{-- ۲ · آمار ----------------------------------------------------------- --}}
    <x-home.stat-strip
        :statistics="$statistics"
        :exclude="['رشته‌های فعال', 'پایه‌های تحصیلی']"
    />

    {{-- ۳ · افتخار شاخص ---------------------------------------------------- --}}
    @if ($featured)
        <section class="section-y">
            <div class="container-page">
                <x-section-heading
                    kicker="افتخار شاخص"
                    title="رتبه اول کشوری در سال تحصیلی ۱۴۰۳"
                    description="بالاترین عنوان کسب‌شده هنرستان در پرونده افتخاراتش ثبت شده است."
                    data-reveal
                />

                <div class="mt-9" data-reveal style="--reveal-delay: 90ms">
                    <x-achievement-card :achievement="$featured" variant="featured" :index="$featuredIndex" />
                </div>
            </div>
        </section>
    @endif

    {{-- ۴ · فهرست کامل دستاوردها ------------------------------------------- --}}
    <section class="border-y border-ink-900/8 bg-[var(--surface-sunken)] dark:border-white/8">
        <div class="container-page section-y">
            <div class="grid items-start gap-12 lg:grid-cols-12 lg:gap-14">
                <div class="lg:col-span-4">
                    <div class="lg:sticky lg:top-28">
                        <x-section-heading
                            kicker="فهرست کامل"
                            title="دستاوردها به تفکیک سال تحصیلی"
                            description="هر سال تحصیلی، دستاوردهای ثبت‌شده هنرستان در همان سال آمده است."
                            data-reveal
                        />

                        <p class="mt-7 flex items-start gap-3 rounded-xl border border-accent-200 bg-accent-50/70 p-4 text-xs leading-7 text-accent-900 dark:border-accent-500/25 dark:bg-accent-950/50 dark:text-accent-200" data-reveal style="--reveal-delay: 80ms">
                            <x-icon name="chart" class="mt-1 size-4 shrink-0 text-accent-600 dark:text-accent-400" />
                            <span>
                                ارقام بیش از ۲۰۰ رتبه سه‌رقمی، بیش از ۵۰ رتبه دو‌رقمی و ۳ رتبه تک‌رقمی، به‌صورت تجمعی برای سه سال تحصیلی اخیر (۱۴۰۱ تا ۱۴۰۳) ثبت شده‌اند و مربوط به یک سال واحد نیستند.
                            </span>
                        </p>

                        <p class="mt-4 flex items-start gap-3 text-xs leading-7 text-slate-500 dark:text-ink-300" data-reveal style="--reveal-delay: 120ms">
                            <x-icon name="quote" class="mt-1 size-4 shrink-0 text-brand-500" />
                            <span>
                                نام رسمی مسابقه‌ها هنوز از سوی هنرستان اعلام نشده است؛ به همین دلیل در هر ردیف فقط عنوان دستاورد، سطح رقابت و سال تحصیلی آمده است.
                            </span>
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <x-home.milestone-timeline :achievements="$achievements" variant="ledger" />
                </div>
            </div>
        </div>
    </section>

    {{-- ۵ · دعوت به ثبت‌نام --------------------------------------------------- --}}
    <x-cta-band
        title="می‌خواهید بخشی از دستاوردهای بعدی باشید؟"
        description="ثبت‌نام پایه دهم در دو رشته شبکه و نرم‌افزار رایانه و مکاترونیک انجام می‌شود؛ برای راهنمایی درباره مدارک و ظرفیت پذیرش، با کادر آموزشی تماس بگیرید."
        primary-label="ثبت‌نام پایه دهم"
        primary-href="{{ route('admission.index') }}"
        secondary-label="تماس با ما"
        secondary-href="{{ route('contact.index') }}"
    />
</x-layouts.app>
