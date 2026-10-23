@php
    /*
     * The page of a throttled request. The provider hands the Persian
     * message over as $throttleMessage; when the framework renders the view
     * on its own, the message of the exception is used - but only when it is
     * a Persian sentence, never the English default of the framework.
     */
    $exceptionMessage = isset($exception) && $exception instanceof \Throwable ? $exception->getMessage() : '';

    $isPersian = preg_match('/[\x{0600}-\x{06FF}]/u', $exceptionMessage) === 1;

    $defaultMessage = 'تعداد درخواست‌های ارسالی شما بیش از حد مجاز است. لطفاً چند دقیقه دیگر صبر کنید و دوباره تلاش کنید.';

    $throttleMessage = isset($throttleMessage) && is_string($throttleMessage) && $throttleMessage !== ''
        ? $throttleMessage
        : ($isPersian ? $exceptionMessage : $defaultMessage);
@endphp

<x-layouts.error
    code="429"
    title="درخواست بیش از حد"
    description="تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً چند دقیقه دیگر صبر کنید و دوباره تلاش کنید."
    heading="تعداد درخواست‌های شما بیش از حد مجاز است"
    :message="$throttleMessage"
    hint="چند دقیقه صبر کنید و سپس فرم را دوباره ارسال کنید. اگر پیام شما ارسال نشده است، پس از این زمان دوباره تلاش کنید.">

    <x-button :href="url()->current()" variant="primary" size="lg" icon="arrow" icon-position="end">
        تلاش دوباره
    </x-button>

    <x-button :href="route('home')" variant="outline" size="lg" icon="arrow" icon-position="end">
        صفحهٔ اصلی
    </x-button>

    <x-button :href="route('contact.index')" variant="secondary" size="lg" icon="arrow" icon-position="end">
        تماس با پشتیبانی
    </x-button>

</x-layouts.error>
