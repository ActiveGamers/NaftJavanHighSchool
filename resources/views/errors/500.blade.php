@php
    /*
     * The technical detail of the failure stays in the log: a 500 can carry
     * a raw SQL message, a file path or any other internal information, and
     * the page must never show it to the visitor.
     */
    $serverMessage = 'مشکلی در نمایش این صفحه پیش آمد. لطفاً چند لحظه بعد دوباره تلاش کنید.';
@endphp

<x-layouts.error
    code="500"
    title="خطا در نمایش صفحه"
    description="در نمایش این صفحه خطایی رخ داد. لطفاً صفحه را تازه‌سازی کنید و در صورت تکرار مشکل با کادر آموزشی تماس بگیرید."
    heading="نمایش این صفحه با مشکل روبه‌رو شد"
    :message="$serverMessage"
    hint="صفحه را یک‌بار تازه‌سازی کنید و دوباره تلاش کنید. اگر مشکل تکرار شد، با کادر آموزشی تماس بگیرید تا پیگیری شود.">

    <x-button :href="route('home')" variant="primary" size="md" icon="arrow" icon-position="end">
        صفحهٔ اصلی
    </x-button>

    <x-button :href="route('contact.index')" variant="outline" size="md" icon="phone" icon-position="start">
        تماس با کادر آموزشی
    </x-button>

</x-layouts.error>
