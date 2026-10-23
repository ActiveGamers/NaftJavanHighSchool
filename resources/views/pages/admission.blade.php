@php
    $faDigits = fn ($value): string => \App\Support\PersianDigits::toPersian($value);

    $inputClasses = 'w-full rounded-xl border border-ink-900/15 bg-white px-4 py-3 text-sm text-ink-900 shadow-[0_1px_2px_rgb(7_30_42/0.04)] outline-none transition-colors placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-white/12 dark:bg-ink-950/60 dark:text-white dark:placeholder:text-ink-400 dark:focus:border-brand-400';
@endphp

<x-layouts.app
    title="درخواست مشاوره و ثبت‌نام"
    description="فرم مشاوره و ثبت‌نام هنرستان فنی پسرانه جوار شرکت پالایش نفت بندرعباس؛ انتخاب رشتهٔ شبکه و نرم‌افزار رایانه یا مکاترونیک و دریافت راهنمایی از کادر آموزشی.">

    <x-page-hero
        title="درخواست مشاوره و ثبت‌نام"
        description="اگر برای پایهٔ دهم دانش‌آموزی ثبت‌نام می‌کنید یا دربارهٔ رشته‌های شبکه و نرم‌افزار رایانه و مکاترونیک پرسشی دارید، فرم زیر را تکمیل کنید. کادر آموزشی با شمارهٔ تماسی که می‌نویسید، در اولین فرصت کاری با شما تماس می‌گیرد."
        eyebrow="فرم مشاوره و ثبت‌نام"
        :items="[
            ['label' => 'خانه', 'href' => route('home')],
            ['label' => 'درخواست مشاوره و ثبت‌نام'],
        ]" />

    {{-- فرآیند درخواست --}}
    <section class="section-y">
        <div class="container-page">
            <x-section-heading
                kicker="فرآیند درخواست"
                title="در سه گام تا راهنمایی"
                description="ثبت درخواست در این سایت به‌معنای ثبت‌نام قطعی نیست؛ این فرم فقط راه ارتباطی شما با کادر آموزشی است تا راهنمایی دقیق دریافت کنید." />

            <ol class="mt-10 grid gap-5 md:grid-cols-3">
                @foreach ([
                    ['title' => 'فرم را تکمیل کنید', 'text' => 'نام دانش‌آموز و والدین، شمارهٔ تماس و پایهٔ تحصیلی مورد نظر را بنویسید و اگر رشته‌ای را انتخاب کرده‌اید، آن را هم مشخص کنید.'],
                    ['title' => 'کادر آموزشی تماس می‌گیرد', 'text' => 'پس از ثبت فرم، با شمارهٔ تماسی که وارد کرده‌اید تماس گرفته می‌شود تا نیاز شما بررسی شود.'],
                    ['title' => 'راهنمایی و ثبت‌نام', 'text' => 'در گفت‌وگوی تلفنی دربارهٔ رشته، ظرفیت، مدارک مورد نیاز و زمان ثبت‌نام راهنمایی می‌شوید.'],
                ] as $index => $step)
                    <li class="surface-card flex flex-col gap-3.5 p-6" data-reveal>
                        <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-ink-900 text-sm font-bold text-white dark:bg-white dark:text-ink-950">
                            {{ $faDigits($index + 1) }}
                        </span>

                        <h3 class="font-display text-base font-bold text-ink-900 dark:text-white">{{ $step['title'] }}</h3>

                        <p class="text-sm leading-8 text-slate-600 dark:text-ink-200">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- فرم --}}
    <section class="section-y bg-ink-50/60 dark:bg-ink-950/50">
        <div class="container-page grid gap-10 lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-7">
                <div class="surface-card flex flex-col gap-7 p-6 sm:p-8">
                    <div class="flex flex-col gap-2">
                        <h2 class="font-display text-xl font-extrabold text-ink-900 dark:text-white">فرم درخواست</h2>
                        <p class="text-sm leading-8 text-slate-600 dark:text-ink-200">
                            فیلدهای ستاره‌دار اجباری هستند. هرچه اطلاعات دقیق‌تر باشد، راهنمایی که دریافت می‌کنید کاربردی‌تر است.
                        </p>
                    </div>

                    {{-- The no-JavaScript path: a plain POST that redirects back
                         with a flash message. The AJAX path shows the same
                         text inside a toast and never reloads the page. --}}
                    @if (session('success'))
                        <x-pages.notice
                            tone="success"
                            icon="check"
                            role="status"
                            data-flash-success
                            title="درخواست شما ثبت شد"
                            :description="session('success')" />
                    @endif

                    @if ($errors->any())
                        <x-pages.notice
                            tone="accent"
                            icon="spark"
                            role="alert"
                            title="چند مورد نیاز به اصلاح دارد"
                            description="لطفاً خطاهای مشخص‌شده در فرم را برطرف کنید و دوباره ارسال کنید." />
                    @endif

                    {{-- The action is origin relative on purpose: the form must
                         always post back to the host that served the page, even
                         behind a proxy that rewrites the host header. --}}
                    <form method="post" action="{{ route('admission.store', [], false) }}" data-app-form novalidate class="flex flex-col gap-6">
                        @csrf

                        <div class="grid gap-6 sm:grid-cols-2">
                            <x-form-field
                                name="student_name"
                                label="نام و نام خانوادگی دانش‌آموز"
                                :required="true"
                                :error="$errors->first('student_name')"
                                icon="user">
                                <input
                                    type="text"
                                    id="student_name"
                                    name="student_name"
                                    value="{{ old('student_name') }}"
                                    required
                                    autocomplete="name"
                                    placeholder="مثال: علی رضایی"
                                    class="{{ $inputClasses }}">
                            </x-form-field>

                            <x-form-field
                                name="parent_name"
                                label="نام و نام خانوادگی والدین"
                                :required="true"
                                :error="$errors->first('parent_name')"
                                icon="user">
                                <input
                                    type="text"
                                    id="parent_name"
                                    name="parent_name"
                                    value="{{ old('parent_name') }}"
                                    required
                                    autocomplete="name"
                                    placeholder="مثال: حسین رضایی"
                                    class="{{ $inputClasses }}">
                            </x-form-field>

                            <x-form-field
                                name="phone"
                                label="شمارهٔ تماس"
                                :required="true"
                                :error="$errors->first('phone')"
                                help="شماره را با ارقام انگلیسی وارد کنید؛ مثال: 09121234567"
                                icon="phone">
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    required
                                    inputmode="numeric"
                                    dir="ltr"
                                    autocomplete="tel"
                                    placeholder="09121234567"
                                    data-validate="mobile"
                                    class="{{ $inputClasses }} text-start">
                            </x-form-field>

                            <x-form-field
                                name="email"
                                label="نشانی ایمیل"
                                :error="$errors->first('email')"
                                help="اختیاری — برای دریافت پاسخ مکتوب."
                                icon="mail">
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    dir="ltr"
                                    autocomplete="email"
                                    placeholder="name@example.com"
                                    class="{{ $inputClasses }} text-start">
                            </x-form-field>
                        </div>

                        <x-form-field
                            name="grade_level"
                            label="پایهٔ تحصیلی"
                            :required="true"
                            :error="$errors->first('grade_level')"
                            icon="graduation">
                            <select id="grade_level" name="grade_level" required class="{{ $inputClasses }}">
                                <option value="">انتخاب کنید</option>
                                @foreach ($gradeLevels as $gradeLevel)
                                    <option value="{{ $gradeLevel->value }}" @selected(old('grade_level') === $gradeLevel->value)>
                                        {{ $gradeLevel->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </x-form-field>

                        <x-form-field
                            name="major_id"
                            label="رشتهٔ مورد نظر"
                            :for="''"
                            :error="$errors->first('major_id')"
                            help="اگر هنوز تصمیم نگرفته‌اید، می‌توانید این بخش را خالی بگذارید؛ در تماس تلفنی راهنمایی می‌شوید."
                            icon="chip">
                            <div class="grid gap-3 sm:grid-cols-2">
                                @forelse ($majors as $major)
                                    <label class="flex cursor-pointer flex-col gap-2 rounded-xl border border-ink-900/12 bg-white p-4 transition-colors hover:border-brand-500/60 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/70 dark:border-white/12 dark:bg-ink-950/50 dark:hover:border-brand-400/50 dark:has-[:checked]:border-brand-400 dark:has-[:checked]:bg-brand-950/40">
                                        <span class="flex items-start gap-2.5">
                                            <input
                                                type="radio"
                                                name="major_id"
                                                value="{{ $major->id }}"
                                                @checked((string) old('major_id') === (string) $major->id)
                                                class="mt-1 size-4 shrink-0 accent-brand-500">
                                            <span class="text-sm font-bold leading-6 text-ink-900 dark:text-white">{{ $major->title }}</span>
                                        </span>

                                        <span class="ps-6.5 text-xs leading-6 text-slate-600 dark:text-ink-200">{{ $major->summary }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-slate-500 dark:text-ink-300">هنوز رشته‌ای برای انتخاب ثبت نشده است.</p>
                                @endforelse
                            </div>
                        </x-form-field>

                        <x-form-field
                            name="note"
                            label="توضیحات"
                            :error="$errors->first('note')"
                            help="اختیاری — هر نکته‌ای که بهتر است کادر آموزشی از پیش بداند، اینجا بنویسید."
                            icon="quote">
                            <textarea
                                id="note"
                                name="note"
                                rows="4"
                                maxlength="1000"
                                placeholder="مثال: تمایل به رشتهٔ شبکه و نرم‌افزار رایانه دارم."
                                class="{{ $inputClasses }} min-h-32 resize-y">{{ old('note') }}</textarea>
                        </x-form-field>

                        <div class="flex flex-col gap-4 border-t border-ink-900/8 pt-6 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                            <p class="max-w-sm text-xs leading-7 text-slate-500 dark:text-ink-300">
                                اطلاعات شما فقط برای پیگیری درخواست و تماس کادر آموزشی استفاده می‌شود.
                            </p>

                            <x-button type="submit" variant="primary" size="lg" icon="arrow" icon-position="end" class="w-full sm:w-auto">
                                ثبت درخواست
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>

            <aside class="flex flex-col gap-5 lg:col-span-5">
                <div class="surface-card flex flex-col gap-5 p-6">
                    <h2 class="font-display text-base font-bold text-ink-900 dark:text-white">پیش از ارسال فرم</h2>

                    <ul class="flex flex-col gap-3.5 text-sm leading-7 text-slate-600 dark:text-ink-200">
                        <li class="flex items-start gap-2.5">
                            <x-icon name="check" class="mt-1 size-4 shrink-0 text-brand-600 dark:text-brand-300" />
                            <span>ثبت درخواست در این سایت به‌معنای ثبت‌نام قطعی نیست و ظرفیت نهایی در تماس کادر آموزشی تأیید می‌شود.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <x-icon name="check" class="mt-1 size-4 shrink-0 text-brand-600 dark:text-brand-300" />
                            <span>اگر شمارهٔ تماس در دسترس نبود، فرم تماس را پر کنید تا از همان مسیر پیگیری شود.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <x-icon name="check" class="mt-1 size-4 shrink-0 text-brand-600 dark:text-brand-300" />
                            <span>برای آشنایی با دروس هر رشته، پیش از انتخاب، صفحهٔ برنامهٔ درسی را ببینید.</span>
                        </li>
                    </ul>

                    <div class="flex flex-col gap-3 border-t border-ink-900/8 pt-5 dark:border-white/10">
                        <x-button :href="route('education')" variant="outline" size="sm" icon="chart" icon-position="end">
                            برنامهٔ درسی
                        </x-button>

                        <x-button :href="route('contact.index')" variant="ghost" size="sm" icon="phone" icon-position="start">
                            تماس مستقیم با کادر آموزشی
                        </x-button>
                    </div>
                </div>

                <div class="surface-card flex flex-col gap-4 p-6">
                    <h2 class="font-display text-base font-bold text-ink-900 dark:text-white">راه‌های ارتباطی</h2>

                    <x-pages.contact-item
                        icon="location"
                        label="نشانی هنرستان"
                        :value="config('school.address')" />

                    <x-pages.contact-item
                        icon="user"
                        label="مدیریت هنرستان"
                        :value="config('school.manager_name')" />

                    <x-pages.contact-item
                        icon="phone"
                        label="شمارهٔ تماس مدیریت"
                        :value="config('school.manager_phone')"
                        :href="'tel:' . config('school.manager_phone')"
                        ltr />

                    <x-pages.contact-item
                        icon="user"
                        label="معاونت هنرستان"
                        :value="config('school.deputy_name')" />

                    <x-pages.contact-item
                        icon="phone"
                        label="شمارهٔ تماس معاونت"
                        :value="config('school.deputy_phone')"
                        :href="'tel:' . config('school.deputy_phone')"
                        ltr />
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>
