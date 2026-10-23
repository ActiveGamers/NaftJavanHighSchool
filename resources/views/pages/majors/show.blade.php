@php
    $gradeLabels = collect(\App\Enums\GradeLevel::ordered());

    $totalCourses = collect($coursesByGrade)->sum(fn ($items) => $items->count());

    $totalHours = collect($coursesByGrade)
        ->flatMap(fn ($items) => $items)
        ->sum(fn ($course) => (int) ($course->weekly_hours ?? 0));

    $faDigits = fn ($value): string => \App\Support\PersianDigits::toPersian($value);

    /* The announced capacity is only shown when the school has published it. */
    $seatsValue = filled($major->seats)
        ? $faDigits($major->seats) . ' نفر'
        : 'اعلام نشده';
@endphp

<x-layouts.app
    :title="$major->title"
    :description="'رشتهٔ ' . $major->title . ' در هنرستان فنی پسرانه جوار شرکت پالایش نفت بندرعباس؛ معرفی رشته و برنامهٔ درسی پایه به پایه.'">

    <x-pages.major-hero
        :major="$major"
        :items="[
            ['label' => 'خانه', 'href' => route('home')],
            ['label' => 'رشته‌های تحصیلی', 'href' => route('majors.index')],
            ['label' => $major->title],
        ]"
        :stats="[
            ['label' => 'تعداد دروس رشته', 'value' => $faDigits($totalCourses) . ' درس'],
            ['label' => 'ظرفیت اعلام‌شده', 'value' => $seatsValue],
        ]" />

    {{-- دربارهٔ رشته --}}
    <section class="section-y">
        <div class="container-page grid gap-10 lg:grid-cols-12 lg:gap-14">
            <div class="lg:col-span-8">
                <x-section-heading
                    kicker="دربارهٔ این رشته"
                    :title="$major->title" />

                @if (filled($major->description))
                    {{-- The summary is already told by the hero above: only the
                         long description is repeated here. --}}
                    <div class="prose-fa mt-6">
                        <p>{{ $major->description }}</p>
                    </div>
                @endif

                @if (filled($totalHours))
                    <p class="mt-6 inline-flex items-center gap-2 rounded-xl border border-ink-900/10 bg-white px-4 py-3 text-sm text-ink-700 dark:border-white/10 dark:bg-ink-950/40 dark:text-ink-100">
                        <x-icon name="calendar" class="size-4.5 shrink-0 text-brand-600 dark:text-brand-300" />
                        <span>
                            مجموع ساعات هفتگی دروس این رشته:
                            <span class="font-semibold">{{ $faDigits($totalHours) }}</span>
                            ساعت
                        </span>
                    </p>
                @endif
            </div>

            <aside class="lg:col-span-4">
                <div class="surface-card flex flex-col gap-5 p-6">
                    <h2 class="font-display text-base font-bold text-ink-900 dark:text-white">پیش از ثبت‌نام بخوانید</h2>

                    <ul class="flex flex-col gap-3.5 text-sm leading-7 text-slate-600 dark:text-ink-200">
                        <li class="flex items-start gap-2.5">
                            <x-icon name="check" class="mt-1 size-4 shrink-0 text-brand-600 dark:text-brand-300" />
                            <span>دروس هر پایه در ادامهٔ همین صفحه، با شرح و ساعات هفتگی آمده است.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <x-icon name="check" class="mt-1 size-4 shrink-0 text-brand-600 dark:text-brand-300" />
                            <span>
                                @if (filled($major->seats))
                                    ظرفیت اعلام‌شدهٔ این رشته {{ $seatsValue }} است و ممکن است در هر سال تحصیلی تغییر کند.
                                @else
                                    ظرفیت اعلام‌شدهٔ این رشته هنوز اعلام نشده است؛ برای وضعیت دقیق با کادر آموزشی تماس بگیرید.
                                @endif
                            </span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <x-icon name="check" class="mt-1 size-4 shrink-0 text-brand-600 dark:text-brand-300" />
                            <span>برای دیدن دروس هر دو رشته در یک نگاه، صفحهٔ آموزش را ببینید.</span>
                        </li>
                    </ul>

                    <div class="flex flex-col gap-3 border-t border-ink-900/8 pt-5 dark:border-white/10">
                        <x-button :href="route('education')" variant="outline" size="sm" icon="chart" icon-position="end">
                            فهرست دروس هنرستان
                        </x-button>

                        <x-button :href="route('admission.index')" variant="primary" size="sm" icon="arrow" icon-position="end">
                            درخواست مشاوره و ثبت‌نام
                        </x-button>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    {{-- فهرست دروس --}}
    <section class="section-y bg-ink-50/60 dark:bg-ink-950/50">
        <div class="container-page">
            <x-section-heading
                kicker="فهرست دروس"
                title="دروس این رشته، پایه به پایه"
                description="هر درس با عنوان، شرح کوتاه و ساعات هفتگی آمده است. روی رایانه و تلفن، جدول به کارت‌های خوانا تبدیل می‌شود." />

            <div class="mt-10 flex flex-col gap-12">
                @foreach ($gradeLabels as $grade)
                    @php $gradeCourses = $coursesByGrade[$grade->value] ?? collect(); @endphp

                    <div class="flex flex-col gap-5" data-reveal>
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-ink-900 text-sm font-bold text-white dark:bg-white dark:text-ink-950">
                                    {{ $faDigits($grade->order()) }}
                                </span>

                                <h3 class="font-display text-xl font-extrabold text-ink-900 dark:text-white">
                                    پایهٔ {{ $grade->label() }}
                                </h3>
                            </div>

                            <span class="text-xs text-slate-500 dark:text-ink-300">
                                {{ $faDigits($gradeCourses->count()) }} درس
                            </span>
                        </div>

                        <x-pages.curriculum-table
                            :courses="$gradeCourses"
                            :caption="'دروس پایهٔ ' . $grade->label() . ' رشتهٔ ' . $major->title" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- مسیر بعدی --}}
    <section class="section-y">
        <div class="container-page flex flex-col gap-6 rounded-2xl border border-ink-900/10 bg-white p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8 dark:border-white/10 dark:bg-ink-950/40">
            <div class="flex flex-col gap-2">
                <h2 class="font-display text-lg font-extrabold text-ink-900 dark:text-white">هنوز مطمئن نیستید کدام رشته مناسب شماست؟</h2>
                <p class="max-w-2xl text-sm leading-8 text-slate-600 dark:text-ink-200">
                    دروس رشته‌های هنرستان را در صفحهٔ آموزش کنار هم ببینید؛ تفاوت دروس و ساعات هفتگی آنجا روشن می‌شود. برای مشاورهٔ دقیق‌تر هم می‌توانید فرم مشاوره را پر کنید.
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap items-center gap-3">
                <x-button :href="route('majors.index')" variant="outline" size="md" icon="arrow" icon-position="end">
                    فهرست رشته‌ها
                </x-button>

                <x-button :href="route('admission.index')" variant="primary" size="md">
                    ثبت‌نام و مشاوره
                </x-button>
            </div>
        </div>
    </section>
</x-layouts.app>
