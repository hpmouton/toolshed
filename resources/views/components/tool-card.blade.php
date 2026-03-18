{{--
    Premium tool card — image-first design with glassmorphism overlay.
    Wrapped in a <button wire:click="openDetail(...)"> by the gallery.
--}}
@props(['tool'])

@php
    $available = $tool->isAvailable();
    $statusRing = match($tool->status->value) {
        'Available' => '',
        'Reserved'  => 'ring-2 ring-amber-400/60',
        'Out'       => 'ring-2 ring-blue-400/60',
        default     => 'ring-2 ring-zinc-500/40',
    };
    $statusDot = match($tool->status->value) {
        'Available' => 'bg-emerald-400 shadow-[0_0_6px_2px_rgba(52,211,153,0.5)]',
        'Reserved'  => 'bg-amber-400 shadow-[0_0_6px_2px_rgba(251,191,36,0.5)]',
        'Out'       => 'bg-blue-400 shadow-[0_0_6px_2px_rgba(96,165,250,0.5)]',
        default     => 'bg-zinc-500',
    };
    $gradient = $tool->categoryGradient();
@endphp

<div class="group relative flex flex-col rounded-2xl overflow-hidden
            bg-zinc-900 border border-white/5
            shadow-lg hover:shadow-2xl hover:shadow-black/40
            transition-all duration-300 ease-out
            hover:-translate-y-1.5 cursor-pointer
            {{ $statusRing }}
            h-full">

    {{-- ── Image / Hero ──────────────────────────────────────────────────── --}}
    <div class="relative h-48 overflow-hidden flex-shrink-0">

        @if ($tool->image_url)
            <img
                src="{{ $tool->image_url }}"
                alt="{{ $tool->name }}"
                loading="lazy"
                class="absolute inset-0 h-full w-full object-cover
                       scale-100 group-hover:scale-105
                       transition-transform duration-500 ease-out"
            />
        @else
            {{-- Gradient fallback when no image --}}
            <div class="absolute inset-0 bg-gradient-to-br {{ $gradient }}"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-6xl opacity-30 select-none">{{ $tool->categoryEmoji() }}</span>
            </div>
        @endif

        {{-- Gradient overlay — ensures text always legible --}}
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-900 via-zinc-900/40 to-transparent"></div>

        {{-- Category pill — top left --}}
        @if ($tool->category)
            <div class="absolute top-3 left-3">
                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold
                             ring-1 backdrop-blur-sm {{ $tool->categoryColour() }}">
                    {{ $tool->categoryEmoji() }} {{ $tool->category }}
                </span>
            </div>
        @endif

        {{-- Status indicator — top right --}}
        <div class="absolute top-3 right-3 flex items-center gap-1.5
                    rounded-full bg-black/50 backdrop-blur-sm px-2.5 py-1 ring-1 ring-white/10">
            <span class="size-2 rounded-full flex-shrink-0 {{ $statusDot }}"></span>
            <span class="text-[11px] font-semibold text-white">{{ $tool->status->label() }}</span>
        </div>

        {{-- SKU watermark — bottom left, inside image --}}
        <div class="absolute bottom-3 left-3">
            <span class="font-mono text-[10px] text-white/50 tracking-widest">{{ $tool->sku }}</span>
        </div>

    </div>

    {{-- ── Card body ─────────────────────────────────────────────────────── --}}
    <div class="flex flex-1 flex-col gap-3 px-4 pt-3 pb-4">

        {{-- Tool name --}}
        <div class="min-w-0">
            <h3 class="font-bold text-white leading-tight line-clamp-2 text-[15px]
                       group-hover:text-amber-300 transition-colors duration-200">
                {{ $tool->name }}
            </h3>
        </div>

        {{-- Description --}}
        @if ($tool->description)
            <p class="text-xs leading-relaxed text-zinc-400 line-clamp-2">
                {{ $tool->description }}
            </p>
        @endif

        {{-- Depot location --}}
        @if ($tool->relationLoaded('depot') && $tool->depot)
            <div class="flex items-center gap-1.5 text-xs text-zinc-500">
                <svg class="size-3.5 shrink-0 text-zinc-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                </svg>
                <span>{{ $tool->depot->city }}, {{ $tool->depot->country_code }}</span>
            </div>
        @endif

        {{-- Spacer --}}
        <div class="mt-auto"></div>

        {{-- ── Price + CTA row ──────────────────────────────────────────── --}}
        <div class="flex items-end justify-between pt-2 border-t border-white/5">

            {{-- Price --}}
            <div>
                <p class="text-[10px] uppercase tracking-widest text-zinc-500 mb-0.5">per day</p>
                <p class="text-xl font-black text-white tabular-nums">
                    {{ $tool->formattedDailyRate() }}
                </p>
            </div>

            {{-- CTA badge --}}
            @if ($available)
                <div class="flex items-center gap-1.5 rounded-xl bg-amber-400 px-3 py-2
                            text-xs font-bold text-zinc-900
                            group-hover:bg-amber-300
                            transition-colors duration-200 shadow-lg shadow-amber-400/20">
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                    </svg>
                    Book
                </div>
            @else
                <span class="rounded-xl bg-zinc-800 px-3 py-2 text-xs font-semibold text-zinc-500 ring-1 ring-white/5">
                    Unavailable
                </span>
            @endif

        </div>
    </div>

</div>
