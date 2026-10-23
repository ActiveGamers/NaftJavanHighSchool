@php
    $faDigits = fn ($value): string => \App\Support\PersianDigits::toPersian($value);

    $albumNames = $albums->keys()->all();

    /* The notice about the placeholders only makes sense while a seeded
       sample image is still on the page: it disappears on its own as soon
       as the last placeholder is gone. */
    $hasPlaceholders = $images->isNotEmpty()
        && $images->contains(fn ($image): bool => str_starts_with((string) data_get($image, 'path', ''), '/images/gallery/placeholder'));
@endphp

<x-layouts.app
    title="گالری تصاویر"
    description="گالری تصاویر هنرستان فنی پسرانه جوار شرکت پالایش نفت بندرعباس؛ نمای کلاس‌ها، کارگاه‌های تخصصی، پروژه‌های دانش‌آموزی و مراسم افتخارآفرینی.">

    <x-page-hero
        title="گالری تصاویر"
        description="تصاویر کلاس‌ها، کارگاه‌های تخصصی، پروژه‌های دانش‌آموزی و مراسم افتخارآفرینی هنرستان. روی هر تصویر بزنید تا در اندازهٔ بزرگ‌تر ببینید."
        eyebrow="کلاس‌ها، کارگاه‌ها و رویدادها"
        :items="[
            ['label' => 'خانه', 'href' => route('home')],
            ['label' => 'گالری تصاویر'],
        ]"
        :facts="[
            ['label' => 'تصویر منتشرشده', 'value' => $faDigits($images->count()) . ' تصویر'],
            ['label' => 'بخش‌های گالری', 'value' => $faDigits(count($albumNames)) . ' بخش'],
        ]" />

    <section class="section-y">
        <div class="container-page">
            @if ($images->isNotEmpty())
                {{-- Album filters. The markup follows the contract of
                     resources/js/gallery.js; without JavaScript every image
                     simply stays visible. --}}
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-2" role="group" aria-label="فیلتر بخش‌های گالری">
                        <button type="button" data-filter="all" data-active="true" aria-pressed="true"
                                class="rounded-lg border border-ink-900/12 bg-white px-4 py-2 text-sm font-medium text-ink-700 transition-colors hover:border-brand-500 hover:text-brand-600 data-[active=true]:border-ink-900 data-[active=true]:bg-ink-900 data-[active=true]:text-white dark:border-white/12 dark:bg-white/5 dark:text-ink-100 dark:hover:text-brand-300 dark:data-[active=true]:border-white dark:data-[active=true]:bg-white dark:data-[active=true]:text-ink-950">
                            همهٔ تصاویر
                        </button>

                        @foreach ($albumNames as $albumName)
                            <button type="button" data-filter="{{ $albumName }}" data-active="false" aria-pressed="false"
                                    class="rounded-lg border border-ink-900/12 bg-white px-4 py-2 text-sm font-medium text-ink-700 transition-colors hover:border-brand-500 hover:text-brand-600 data-[active=true]:border-ink-900 data-[active=true]:bg-ink-900 data-[active=true]:text-white dark:border-white/12 dark:bg-white/5 dark:text-ink-100 dark:hover:text-brand-300 dark:data-[active=true]:border-white dark:data-[active=true]:bg-white dark:data-[active=true]:text-ink-950">
                                {{ $albumName }}
                            </button>
                        @endforeach
                    </div>

                    <p class="text-xs text-slate-500 dark:text-ink-300">
                        برای دیدن تصویر بزرگ‌تر، روی هر کارت بزنید.
                    </p>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($images as $image)
                        <x-gallery-item :image="$image" data-reveal />
                    @endforeach
                </div>

                @if ($hasPlaceholders)
                    <div class="mt-10">
                        <x-pages.notice
                            tone="accent"
                            icon="spark"
                            title="تصاویر این بخش جای‌نگهدار هستند"
                            description="عکس‌های واقعی کلاس‌ها، آزمایشگاه رایانه و کارگاه مکاترونیک هنوز از سوی هنرستان دریافت نشده است. تا زمان انتشار عکس‌های اصلی، همین تصاویر نمونه در جای آن‌ها نمایش داده می‌شود." />
                    </div>
                @endif
            @else
                <x-empty-state
                    title="هنوز تصویری منتشر نشده است"
                    description="به‌محض انتشار نخستین تصاویر کلاس‌ها و کارگاه‌های هنرستان، اینجا نمایش داده می‌شوند."
                    icon="spark">
                    <x-button :href="route('contact.index')" variant="secondary" size="sm">تماس با کادر آموزشی</x-button>
                </x-empty-state>
            @endif
        </div>
    </section>

    <x-cta-band
        title="گالری را دیدید؟ سری به کلاس‌ها بزنید"
        description="برنامهٔ درسی، ساعات هفتگی و معرفی هر دو رشتهٔ هنرستان در دسترس است. برای هر پرسشی دربارهٔ آموزش‌ها، فرم مشاوره را پر کنید." />
</x-layouts.app>
