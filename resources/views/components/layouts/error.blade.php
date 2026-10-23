@props([
    'code',
    'title',
    'description' => null,
    'heading',
    'message',
    'hint' => null,
    'label' => null,
])

@php
    /* The single layout of every error page: the hero, the texture layers,
       the breadcrumb, the status code, the heading and the two paragraphs are
       identical on all of them, only the texts and the action buttons differ. */
    $breadcrumbLabel = $label ?? $title;
@endphp

<x-layouts.app :title="$title" :description="$description ?? $heading">

    <section class="dark relative isolate overflow-hidden bg-ink-950 text-white">
        <div class="pointer-events-none absolute inset-0 -z-10 texture-grid" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -top-28 start-[-12%] -z-10 h-80 w-[75%] hero-wash opacity-80" aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 -z-10 h-px bg-brand-500/35" aria-hidden="true"></div>

        <div class="container-page py-14 sm:py-18 lg:py-24">
            <x-breadcrumb
                :items="[
                    ['label' => 'خانه', 'href' => route('home')],
                    ['label' => $breadcrumbLabel],
                ]"
                class="mb-8" />

            <div class="flex flex-col gap-6">
                <p class="font-display text-6xl font-extrabold leading-none text-brand-300 sm:text-7xl">
                    <bdi dir="ltr">{{ $code }}</bdi>
                </p>

                <h1 class="max-w-3xl font-display text-3xl font-extrabold leading-[1.35] text-balance-fa sm:text-4xl">
                    {{ $heading }}
                </h1>

                <p class="max-w-2xl text-sm leading-8 text-ink-200/90 sm:text-base">
                    {{ $message }}
                </p>

                @if (filled($hint))
                    <p class="max-w-2xl text-sm leading-8 text-ink-300/90">
                        {{ $hint }}
                    </p>
                @endif
            </div>

            <div class="mt-9 flex flex-wrap items-center gap-3">
                {{-- The action buttons of the page: the only part that differs
                     between the error views besides the texts. --}}
                {{ $slot }}
            </div>
        </div>
    </section>

</x-layouts.app>
