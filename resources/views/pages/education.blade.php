@php
    $gradeLabels = collect(\App\Enums\GradeLevel::ordered());

    $allCourses = collect($coursesByGrade)->flatMap(fn ($items) => $items);

    $totalCourses = $allCourses->count();

    $totalHours = $allCourses->sum(fn ($course) => (int) ($course->weekly_hours ?? 0));

    $faDigits = fn ($value): string => \App\Support\PersianDigits::toPersian($value);
@endphp

<x-layouts.app
    title="برنامهٔ درسی"
    description="فهرست کامل دروس هنرستان فنی پسرانه جوار شرکت پالایش نفت بندرعباس پایه به پایه، همراه با شرح کوتاه و ساعات هفتگی هر درس.">

    <x-page-hero
        title="فهرست دروس و ساعات هفتگی"
        description="فهرست دروس هنرستان در سه پایهٔ تحصیلی ارائه می‌شود. در جدول‌های زیر، عنوان هر درس و شرح کوتاه آن آمده است."
        :items="[
            ['label' => 'خانه', 'href' => route('home')],
            ['label' => 'برنامهٔ درسی'],
        ]"
        :facts="[
            ['label' => 'مجموع دروس', 'value' => $faDigits($totalCourses) . ' درس'],
            ['label' => 'مجموع ساعات هفتگی', 'value' => $faDigits($totalHours) . ' ساعت'],
        ]" />

    {{-- رشته‌ها --}}
    <section class="section-y">
        <div class="container-page">
            <x-section-heading
                kicker="دو رشتهٔ هنرستان"
                title="دروس هر رشته، جدا از دیگری"
                description="دروس مشترک و دروس تخصصی در جدول‌های ادامه، با نام رشته مشخص شده‌اند. برای توضیح کامل هر رشته، صفحهٔ اختصاصی آن را ببینید." />

            <div class="mt-10 grid gap-6 lg:grid-cols-2">
                @foreach ($majors as $major)
                    <x-major-card :major="$major" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- فهرست دروس پایه به پایه --}}
    <section class="section-y bg-ink-50/60 dark:bg-ink-950/50">
        <div class="container-page">
            <x-section-heading
                kicker="فهرست دروس"
                title="دروس هر پایه، تفکیک‌شده بر اساس رشته"
                description="هر درس با نام رشته و ساعات هفتگی‌اش آمده است. در نمایش رایانه، جدول‌ها کنار هم دیده می‌شوند و در نمایش تلفن به کارت‌های خوانا تبدیل می‌شوند." />

            <div class="mt-10 flex flex-col gap-14">
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
                            :majors="$majors"
                            show-major
                            caption="فهرست دروس به تفکیک رشته" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- راهنمای انتخاب --}}
    <section class="section-y">
        <div class="container-page grid gap-6 lg:grid-cols-3">
            @foreach ([
                ['icon' => 'graduation', 'title' => 'پایهٔ دهم', 'text' => 'دورهٔ مشترک آشنایی با شبکه، سخت‌افزار، مدارهای الکتریکی و مبانی برنامه‌نویسی؛ نقطهٔ شروع کارگاه‌های تخصصی.'],
                ['icon' => 'chart', 'title' => 'پایهٔ یازدهم', 'text' => 'ورود به دروس تخصصی: طراحی و پیکربندی شبکه، امنیت، کنترل صنعتی، PLC و رباتیک.'],
                ['icon' => 'trophy', 'title' => 'پایهٔ دوازدهم', 'text' => 'پروژه‌های عملی و کارگاه پایانی؛ از مدیریت سرور و سرویس‌های سازمانی تا ساخت کامل یک سامانهٔ مکاترونیکی.'],
            ] as $step)
                <div class="surface-card flex flex-col gap-3.5 p-6">
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-accent-50 text-accent-600 dark:bg-accent-950/60 dark:text-accent-300">
                        <x-icon :name="$step['icon']" class="size-5" />
                    </span>

                    <h3 class="font-display text-base font-bold text-ink-900 dark:text-white">{{ $step['title'] }}</h3>

                    <p class="text-sm leading-8 text-slate-600 dark:text-ink-200">{{ $step['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <x-cta-band
        title="دروس را دیدید؟ حالا دربارهٔ رشته‌ها مشورت بگیرید"
        description="فرم مشاوره و ثبت‌نام را تکمیل کنید تا کادر آموزشی دربارهٔ رشتهٔ مناسب، ظرفیت و مدارک مورد نیاز با شما تماس بگیرد." />
</x-layouts.app>
