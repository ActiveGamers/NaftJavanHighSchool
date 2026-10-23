@php
    $exceptionMessage = isset($exception) && $exception instanceof \Throwable ? $exception->getMessage() : '';

    $isPersian = preg_match('/[\x{0600}-\x{06FF}]/u', $exceptionMessage) === 1;

    $notFoundMessage = $isPersian
        ? $exceptionMessage
        : 'مطلب مورد نظر یافت نشد یا دیگر در دسترس نیست. لطفاً فهرست مطالب را مشاهده کنید یا با کادر آموزشی تماس بگیرید.';
@endphp

<x-layouts.error
    code="404"
    title="صفحه پیدا نشد"
    description="صفحهٔ مورد نظر پیدا نشد. می‌توانید به صفحهٔ اصلی، فهرست اخبار یا صفحهٔ تماس با کادر آموزشی بروید."
    heading="صفحه‌ای که دنبالش بودید پیدا نشد"
    :message="$notFoundMessage"
    hint="اگر نشانی را اشتباه وارد کرده‌اید، می‌توانید از فهرست زیر ادامه دهید. اگر فکر می‌کنید این صفحه باید وجود داشته باشد، به کادر آموزشی اطلاع دهید.">

    <x-button :href="route('home')" variant="primary" size="md" icon="arrow" icon-position="end">
        صفحهٔ اصلی
    </x-button>

    <x-button :href="route('news.index')" variant="outline" size="md">
        فهرست اخبار
    </x-button>

    <x-button :href="route('contact.index')" variant="outline" size="md" icon="phone" icon-position="start">
        تماس با کادر آموزشی
    </x-button>

</x-layouts.error>
