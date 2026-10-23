@php
    $exceptionMessage = isset($exception) && $exception instanceof \Throwable ? $exception->getMessage() : '';

    $isPersian = preg_match('/[\x{0600}-\x{06FF}]/u', $exceptionMessage) === 1;

    $sessionMessage = $isPersian
        ? $exceptionMessage
        : 'نشست شما در این صفحه منقضی شده است. لطفاً صفحه را تازه‌سازی کنید و دوباره تلاش کنید.';
@endphp

{{-- An expired session is a self-service error: no contact link here, only a
     retry and a way back to the home page (AGENTS.md §10). --}}
<x-layouts.error
    code="419"
    title="نشست منقضی شده"
    description="نشست شما منقضی شده است. صفحه را تازه‌سازی کنید و دوباره تلاش کنید."
    heading="نشست شما منقضی شده است"
    :message="$sessionMessage"
    hint="صفحه را یک‌بار تازه‌سازی کنید و دوباره تلاش کنید.">

    <x-button :href="url()->current()" variant="primary" size="lg" icon="arrow" icon-position="end">
        تلاش دوباره
    </x-button>

    <x-button :href="route('home')" variant="outline" size="lg" icon="arrow" icon-position="end">
        صفحهٔ اصلی
    </x-button>

</x-layouts.error>
