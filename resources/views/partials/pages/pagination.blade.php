@if ($paginator->hasPages())
    @php
        $faDigits = fn ($value): string => \App\Support\PersianDigits::toPersian($value);

        $buttonBase = 'inline-flex min-w-11 items-center justify-center gap-1.5 rounded-lg border px-3.5 py-2 text-sm font-medium transition-colors';
        $buttonIdle = 'border-ink-900/12 bg-white text-ink-700 hover:border-brand-500 hover:text-brand-600 dark:border-white/12 dark:bg-white/5 dark:text-ink-100 dark:hover:text-brand-300';
        $buttonActive = 'border-ink-900 bg-ink-900 text-white dark:border-white dark:bg-white dark:text-ink-950';
        $buttonDisabled = 'cursor-not-allowed border-ink-900/8 bg-ink-50 text-slate-400 dark:border-white/8 dark:bg-white/5 dark:text-ink-500';
    @endphp

    <nav role="navigation" aria-label="صفحه‌بندی اخبار" class="flex flex-wrap items-center justify-center gap-2">
        @if ($paginator->onFirstPage())
            <span class="{{ $buttonBase }} {{ $buttonDisabled }}" aria-disabled="true">
                <x-icon name="arrow" class="size-4 rotate-180" />
                <span>قبلی</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $buttonBase }} {{ $buttonIdle }}">
                <x-icon name="arrow" class="size-4 rotate-180" />
                <span>قبلی</span>
            </a>
        @endif

        <ol class="flex flex-wrap items-center gap-1.5">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="inline-flex min-w-9 items-center justify-center px-2 py-2 text-sm text-slate-400 dark:text-ink-400">…</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="{{ $buttonBase }} {{ $buttonActive }}">{{ $faDigits($page) }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $buttonBase }} {{ $buttonIdle }}">{{ $faDigits($page) }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ol>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $buttonBase }} {{ $buttonIdle }}">
                <span>بعدی</span>
                <x-icon name="arrow" class="size-4" />
            </a>
        @else
            <span class="{{ $buttonBase }} {{ $buttonDisabled }}" aria-disabled="true">
                <span>بعدی</span>
                <x-icon name="arrow" class="size-4" />
            </span>
        @endif
    </nav>
@endif
