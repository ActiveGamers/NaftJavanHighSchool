@php
    $faDigits = fn ($value): string => \App\Support\PersianDigits::toPersian($value);

    /* The controller delivers every category of the archive as enum cases;
       the categories of the current page are only the fallback for the
       pages that do not have the full list. */
    $categoryFilters = collect($categories ?? [])
        ->map(fn ($category): array => [
            'value' => $category instanceof \BackedEnum ? (string) $category->value : (string) $category,
            'label' => is_object($category) && method_exists($category, 'label')
                ? (string) $category->label()
                : (string) $category,
        ])
        ->filter(fn (array $item): bool => filled($item['value']) && filled($item['label']))
        ->unique(fn (array $item): string => $item['value'])
        ->values();

    if ($categoryFilters->isEmpty()) {
        $categoryFilters = $posts->getCollection()
            ->map(fn ($post) => $post->category)
            ->filter()
            ->unique(fn ($category): string => $category->value)
            ->map(fn ($category): array => [
                'value' => (string) $category->value,
                'label' => (string) $category->label(),
            ])
            ->values();
    }

    $nextPage = $posts->currentPage() + 1;
@endphp

<x-layouts.app
    title="اخبار و اطلاعیه‌ها"
    description="آخرین اخبار، اطلاعیه‌ها، رویدادها و گزارش افتخارآفرینی هنرستان فنی پسرانه جوار شرکت پالایش نفت بندرعباس.">

    <x-page-hero
        title="اخبار و اطلاعیه‌ها"
        description="گزارش آغاز سال تحصیلی، اطلاعیه‌های مربوط به ثبت‌نام، رویدادهای هنرستان و دستاوردهای دانش‌آموزان در این بخش منتشر می‌شود."
        eyebrow="گزارش‌ها و اطلاعیه‌ها"
        :items="[
            ['label' => 'خانه', 'href' => route('home')],
            ['label' => 'اخبار'],
        ]"
        :facts="[
            ['label' => 'مطلب منتشرشده', 'value' => $faDigits($posts->total()) . ' مطلب'],
            ['label' => 'دسته‌بندی‌ها', 'value' => $faDigits($categoryFilters->count()) . ' دسته'],
        ]" />

    <section class="section-y">
        <div class="container-page">
            @if ($categoryFilters->isNotEmpty())
                {{-- Category filter. It is a pure client-side enhancement: with
                     JavaScript disabled every article stays visible. The
                     container stays hidden until news.js takes it over, so a
                     visitor without JavaScript never meets a dead control. --}}
                <div hidden class="flex flex-wrap items-center gap-2" role="group" aria-label="فیلتر دسته‌بندی اخبار">
                    <button type="button" data-news-filter="all" data-active="true" aria-pressed="true"
                            class="rounded-lg border border-ink-900/12 bg-white px-4 py-2 text-sm font-medium text-ink-700 transition-colors hover:border-brand-500 hover:text-brand-600 data-[active=true]:border-ink-900 data-[active=true]:bg-ink-900 data-[active=true]:text-white dark:border-white/12 dark:bg-white/5 dark:text-ink-100 dark:hover:text-brand-300 dark:data-[active=true]:border-white dark:data-[active=true]:bg-white dark:data-[active=true]:text-ink-950">
                        همهٔ مطالب
                    </button>

                    @foreach ($categoryFilters as $category)
                        <button type="button" data-news-filter="{{ $category['value'] }}" data-news-filter-label="{{ $category['label'] }}" data-active="false" aria-pressed="false"
                                class="rounded-lg border border-ink-900/12 bg-white px-4 py-2 text-sm font-medium text-ink-700 transition-colors hover:border-brand-500 hover:text-brand-600 data-[active=true]:border-ink-900 data-[active=true]:bg-ink-900 data-[active=true]:text-white dark:border-white/12 dark:bg-white/5 dark:text-ink-100 dark:hover:text-brand-300 dark:data-[active=true]:border-white dark:data-[active=true]:bg-white dark:data-[active=true]:text-ink-950">
                            {{ $category['label'] }}
                        </button>
                    @endforeach
                </div>
            @endif

            @if ($posts->isEmpty())
                <div class="mt-10">
                    <x-empty-state
                        title="هنوز خبری منتشر نشده است"
                        description="به‌محض انتشار نخستین خبر یا اطلاعیه، متن آن در همین صفحه نمایش داده می‌شود."
                        icon="calendar">
                        <x-button :href="route('contact.index')" variant="secondary" size="sm">تماس با کادر آموزشی</x-button>
                    </x-empty-state>
                </div>
            @else
                <div data-news-list class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @include('partials.posts.list')
                </div>

                {{-- Skeletons shown while the next page is loading. --}}
                <div data-news-skeleton class="mt-6 hidden" aria-hidden="true">
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <x-skeleton type="post" />
                        <x-skeleton type="post" />
                        <x-skeleton type="post" />
                    </div>
                </div>

                <div class="mt-12 flex flex-col items-center gap-6">
                    {{-- The numbered pagination is the no-JavaScript fallback;
                         news.js hides it once the "load more" button is live. --}}
                    <div data-news-pagination>
                        {{ $posts->links('partials.pages.pagination') }}
                    </div>

                    @if ($posts->hasMorePages())
                        <div data-news-more-wrapper class="hidden">
                            <button type="button"
                                    data-news-more
                                    data-url="{{ route('news.more', [], false) }}"
                                    data-next-page="{{ $nextPage }}"
                                    class="inline-flex items-center gap-2 rounded-xl border border-ink-900/15 bg-white px-6 py-3 text-sm font-semibold text-ink-800 transition-colors hover:border-brand-500 hover:text-brand-600 data-[loading=true]:opacity-60 dark:border-white/15 dark:bg-white/5 dark:text-white dark:hover:text-brand-300">
                                <span data-news-more-label>نمایش مطالب بیشتر</span>
                                <x-icon name="arrow" class="size-4 shrink-0" />
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <x-cta-band
        title="از اخبار هنرستان جا نمانید"
        description="برای اطلاع از تازه‌ترین اطلاعیه‌ها و پاسخ به پرسش‌های خود، فرم مشاوره و تماس را تکمیل کنید." />
</x-layouts.app>
