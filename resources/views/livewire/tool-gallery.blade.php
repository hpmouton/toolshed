{{-- ToolGallery — Premium dark catalogue with modal-first UX --}}
<div class="min-h-screen bg-zinc-950">

    {{-- ══════════════════════════════════════════════════════════════════════
         HERO BANNER
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden border-b border-white/5 bg-gradient-to-br from-zinc-900 via-zinc-900 to-zinc-950">

        {{-- Decorative radial glow --}}
        <div class="pointer-events-none absolute -top-32 -left-32 size-[500px] rounded-full bg-amber-500/5 blur-3xl"></div>
        <div class="pointer-events-none absolute -top-16 right-0 size-[400px] rounded-full bg-orange-600/5 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

                {{-- Left — heading --}}
                <div class="space-y-1.5">
                    @if ($activeDepot)
                        <div class="flex items-center gap-2 text-xs font-semibold text-amber-400 uppercase tracking-widest">
                            <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                            </svg>
                            {{ $activeDepot->city }}, {{ $activeDepot->country_name }}
                        </div>
                    @endif
                    <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                        @if ($activeDepot)
                            {{ $activeDepot->name }}
                        @else
                            Professional Tool Hire
                        @endif
                    </h1>
                    <p class="text-sm text-zinc-400 max-w-lg">
                        @if ($activeDepot)
                            {{ $tools->total() }} {{ Str::plural('tool', $tools->total()) }} at this depot
                            &middot; {{ $activeDepot->currency_code }}
                        @else
                            {{ $tools->total() }} {{ Str::plural('tool', $tools->total()) }} available across all depots.
                            Click any card to view details and book.
                        @endif
                    </p>
                </div>

                {{-- Right — action buttons --}}
                <div class="flex shrink-0 flex-wrap items-center gap-2">
                    @if ($activeDepot)
                        <a href="{{ route('tools.index') }}" wire:navigate
                           class="inline-flex items-center gap-1.5 rounded-xl border border-white/10
                                  bg-white/5 px-4 py-2 text-sm font-medium text-zinc-300
                                  hover:bg-white/10 hover:text-white transition-colors">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                            </svg>
                            All depots
                        </a>
                    @endif

                    {{-- My Bookings --}}
                    <flux:modal.trigger name="my-bookings">
                        <button type="button"
                                class="inline-flex items-center gap-2 rounded-xl
                                       bg-amber-400 px-4 py-2 text-sm font-bold text-zinc-900
                                       hover:bg-amber-300 transition-colors shadow-lg shadow-amber-400/20">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                            </svg>
                            My Bookings
                            @php $activeCount = $this->myBookings->where('booking_status', 'active')->count(); @endphp
                            @if ($activeCount > 0)
                                <span class="flex size-5 items-center justify-center rounded-full bg-zinc-900 text-[10px] font-black text-amber-400">
                                    {{ $activeCount }}
                                </span>
                            @endif
                        </button>
                    </flux:modal.trigger>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         FILTERS BAR
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="sticky top-0 z-20 border-b border-white/5 bg-zinc-950/90 backdrop-blur-xl">
        <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                {{-- Search --}}
                <div class="relative flex-1 max-w-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-500 pointer-events-none"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.197 5.197a7.5 7.5 0 0 0 10.606 10.606Z"/>
                    </svg>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search tools or SKUs…"
                        class="w-full rounded-xl border border-white/10 bg-white/5 pl-9 pr-4 py-2
                               text-sm text-zinc-100 placeholder-zinc-500
                               focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400/50
                               transition-colors"
                    />
                    @if ($search)
                        <button wire:click="$set('search', '')" type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Category tabs --}}
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button
                        wire:click="$set('category', '')"
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors
                               {{ $category === '' ? 'bg-amber-400 text-zinc-900' : 'bg-white/5 text-zinc-400 hover:bg-white/10 hover:text-zinc-200' }}">
                        All
                    </button>
                    @foreach ($categories as $cat)
                        <button
                            wire:click="$set('category', '{{ $cat }}')"
                            type="button"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors
                                   {{ $category === $cat ? 'bg-amber-400 text-zinc-900' : 'bg-white/5 text-zinc-400 hover:bg-white/10 hover:text-zinc-200' }}">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>

                {{-- Sort dropdown (FR-2.5) --}}
                <div class="flex items-center gap-1.5">
                    <label for="sort" class="text-xs font-medium text-zinc-500 whitespace-nowrap">Sort by</label>
                    <select
                        id="sort"
                        wire:model.live="sort"
                        class="rounded-xl border border-white/10 bg-white/5 px-3 py-1.5
                               text-xs font-semibold text-zinc-300
                               focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400/50
                               transition-colors">
                        <option value="name">Name</option>
                        <option value="price_asc">Price: low → high</option>
                        <option value="price_desc">Price: high → low</option>
                    </select>
                </div>

                {{-- Max daily rate filter (FR-2.6) --}}
                <div class="flex items-center gap-1.5">
                    <label for="maxRate" class="text-xs font-medium text-zinc-500 whitespace-nowrap">Max rate</label>
                    <input
                        id="maxRate"
                        type="number"
                        min="0"
                        step="100"
                        wire:model.live.debounce.400ms="maxRate"
                        placeholder="e.g. 5000"
                        class="w-28 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5
                               text-xs font-semibold text-zinc-300 placeholder-zinc-600
                               focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400/50
                               transition-colors"
                    />
                    <span class="text-[10px] text-zinc-600">cents</span>
                </div>

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         TOOL GRID
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        @if ($tools->isEmpty())
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center py-32 text-center">
                <div class="mb-6 flex size-20 items-center justify-center rounded-full
                            bg-white/5 ring-1 ring-white/10">
                    <svg class="size-10 text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.197 5.197a7.5 7.5 0 0 0 10.606 10.606Z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white">No tools found</h3>
                <p class="mt-1 text-sm text-zinc-500 max-w-xs">
                    Try adjusting your search or clearing the category filter.
                </p>
                @if ($search || $category)
                    <button
                        wire:click="$set('search', ''); $set('category', '')"
                        type="button"
                        class="mt-6 rounded-xl bg-white/5 px-4 py-2 text-sm font-medium text-zinc-300
                               ring-1 ring-white/10 hover:bg-white/10 transition-colors">
                        Clear filters
                    </button>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($tools as $tool)
                    <button
                        wire:click="openDetail({{ $tool->id }})"
                        class="group text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 focus-visible:rounded-2xl"
                        type="button"
                    >
                        <x-tool-card :tool="$tool" />
                    </button>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($tools->hasPages())
                <div class="mt-10 flex justify-center">
                    <div class="[&_.pagination]:flex [&_.pagination]:items-center [&_.pagination]:gap-1
                                [&_.page-item.active_.page-link]:bg-amber-400 [&_.page-item.active_.page-link]:text-zinc-900
                                [&_.page-link]:rounded-lg [&_.page-link]:px-3 [&_.page-link]:py-1.5
                                [&_.page-link]:text-sm [&_.page-link]:text-zinc-400
                                [&_.page-link]:bg-white/5 [&_.page-link]:border-0
                                [&_.page-link:hover]:bg-white/10 [&_.page-link:hover]:text-zinc-200">
                        {{ $tools->links() }}
                    </div>
                </div>
            @endif
        @endif
    </div>


    {{-- ════════════════════════════════════════════════════════════════════════
         TOOL DETAIL FLYOUT
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <flux:modal name="tool-detail" flyout variant="floating" class="md:w-[32rem] !bg-zinc-900 !border-white/10"
                @close="$wire.detailToolId = null">
        @if ($this->detailTool)
            @php $t = $this->detailTool; @endphp

            {{-- Hero image --}}
            <div class="relative -mx-6 -mt-6 h-56 overflow-hidden">
                @if ($t->image_url)
                    <img src="{{ $t->image_url }}" alt="{{ $t->name }}"
                         class="h-full w-full object-cover" />
                @else
                    <div class="h-full w-full bg-gradient-to-br {{ $t->categoryGradient() }}
                                flex items-center justify-center">
                        <span class="text-8xl opacity-20">{{ $t->categoryEmoji() }}</span>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-900 via-zinc-900/30 to-transparent"></div>

                {{-- Status on image --}}
                <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between">
                    <div>
                        <p class="font-mono text-[10px] text-white/40 tracking-widest mb-1">{{ $t->sku }}</p>
                        <h2 class="text-xl font-black text-white leading-tight">{{ $t->name }}</h2>
                    </div>
                    @php
                        $dot = match($t->status->value) {
                            'Available' => 'bg-emerald-400 shadow-[0_0_8px_2px_rgba(52,211,153,0.6)]',
                            'Reserved'  => 'bg-amber-400',
                            'Out'       => 'bg-blue-400',
                            default     => 'bg-zinc-500',
                        };
                    @endphp
                    <div class="flex items-center gap-1.5 rounded-full bg-black/60 backdrop-blur-sm
                                px-3 py-1.5 ring-1 ring-white/10 shrink-0">
                        <span class="size-2 rounded-full {{ $dot }}"></span>
                        <span class="text-xs font-semibold text-white">{{ $t->status->label() }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-5">

                {{-- Category --}}
                @if ($t->category)
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold
                                 ring-1 {{ $t->categoryColour() }}">
                        {{ $t->categoryEmoji() }} {{ $t->category }}
                    </span>
                @endif

                {{-- Description --}}
                @if ($t->description)
                    <p class="text-sm leading-relaxed text-zinc-400">{{ $t->description }}</p>
                @endif

                {{-- Pricing --}}
                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 mb-2">Pricing</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-white tabular-nums">{{ $t->formattedDailyRate() }}</span>
                        <span class="text-sm text-zinc-500">/ day</span>
                    </div>
                    @if ($t->maintenance_fee_cents > 0)
                        <p class="mt-2 text-xs text-zinc-500">
                            + {{ $t->currency()->format($t->maintenance_fee_cents) }} one-time maintenance fee
                        </p>
                    @endif
                </div>

                {{-- Depot --}}
                @if ($t->depot)
                    <div class="flex items-center gap-3 rounded-2xl bg-white/5 ring-1 ring-white/10 p-4">
                        <div class="flex size-10 shrink-0 items-center justify-center
                                    rounded-xl bg-blue-500/10 ring-1 ring-blue-500/20">
                            <svg class="size-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-zinc-100">{{ $t->depot->name }}</p>
                            <p class="text-xs text-zinc-500">{{ $t->depot->shortAddress() }}</p>
                        </div>
                    </div>
                @endif

                {{-- CTA --}}
                @if ($t->isAvailable())
                    <button
                        type="button"
                        x-on:click="$flux.modal('tool-detail').close(); setTimeout(() => $wire.openBooking({{ $t->id }}), 150)"
                        class="w-full flex items-center justify-center gap-2.5 rounded-2xl
                               bg-amber-400 px-6 py-3.5 text-base font-bold text-zinc-900
                               hover:bg-amber-300 active:scale-[0.98]
                               transition-all shadow-xl shadow-amber-400/20"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                        </svg>
                        Book this tool
                    </button>
                @else
                    <div class="flex items-center gap-3 rounded-2xl bg-zinc-800/60 ring-1 ring-white/5 px-4 py-3">
                        <svg class="size-5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <p class="text-sm text-zinc-400">This tool is currently <span class="font-semibold text-zinc-300">unavailable</span>.</p>
                    </div>
                @endif

            </div>
        @endif
    </flux:modal>


    {{-- ════════════════════════════════════════════════════════════════════════
         BOOKING MODAL  (UI-1.1 + UI-1.2)
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <flux:modal name="book-tool" class="md:w-[30rem] !bg-zinc-900 !border-white/10 !rounded-3xl" :dismissible="false"
                @close="$wire.closeBookingModal()">
        @if ($this->bookingTool)
            @php $bt = $this->bookingTool; @endphp

            @if ($bookingDone)
                {{-- ── Success state ───────────────────────────────────────────── --}}
                <div class="flex flex-col items-center py-10 text-center space-y-5">
                    <div class="relative flex size-20 items-center justify-center rounded-full
                                bg-emerald-500/10 ring-1 ring-emerald-500/30">
                        <svg class="size-10 text-emerald-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <div class="absolute -inset-2 rounded-full bg-emerald-400/10 animate-ping" style="animation-duration:2s"></div>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-black text-white">Booking confirmed!</h3>
                        <p class="text-sm text-zinc-400">
                            <span class="font-semibold text-zinc-200">{{ $bt->name }}</span>
                            has been reserved for you.
                        </p>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <flux:modal.close>
                            <button type="button"
                                    class="rounded-xl bg-amber-400 px-5 py-2.5 text-sm font-bold text-zinc-900 hover:bg-amber-300 transition-colors">
                                Done
                            </button>
                        </flux:modal.close>
                        <flux:modal.close>
                            <flux:modal.trigger name="my-bookings">
                                <button type="button"
                                        class="rounded-xl bg-white/5 px-5 py-2.5 text-sm font-medium text-zinc-300
                                               ring-1 ring-white/10 hover:bg-white/10 transition-colors">
                                    View bookings
                                </button>
                            </flux:modal.trigger>
                        </flux:modal.close>
                    </div>
                </div>

            @else
                {{-- ── Booking form ─────────────────────────────────────────────── --}}
                <div class="space-y-6">

                    {{-- Tool summary header --}}
                    <div class="flex items-center gap-4">
                        @if ($bt->image_url)
                            <div class="size-14 rounded-xl overflow-hidden flex-shrink-0 ring-1 ring-white/10">
                                <img src="{{ $bt->image_url }}" alt="{{ $bt->name }}" class="h-full w-full object-cover" />
                            </div>
                        @else
                            <div class="size-14 rounded-xl flex-shrink-0 flex items-center justify-center
                                        bg-gradient-to-br {{ $bt->categoryGradient() }} ring-1 ring-white/10">
                                <span class="text-2xl">{{ $bt->categoryEmoji() }}</span>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h3 class="font-bold text-white text-base leading-tight">{{ $bt->name }}</h3>
                            <p class="font-mono text-[11px] text-zinc-500 mt-0.5">{{ $bt->sku }}</p>
                            <p class="text-xs text-zinc-400 mt-1">
                                <span class="font-semibold text-amber-400">{{ $bt->formattedDailyRate() }}</span>
                                <span class="text-zinc-600"> / day</span>
                            </p>
                        </div>
                    </div>

                    {{-- Date pickers --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label for="bm-start" class="block text-xs font-semibold text-zinc-400 uppercase tracking-widest">
                                Start date
                            </label>
                            <input id="bm-start" type="date" wire:model.live="startDate"
                                min="{{ now()->addDay()->toDateString() }}"
                                class="block w-full rounded-xl border px-3 py-2.5 text-sm font-medium
                                       bg-white/5 text-zinc-100 placeholder-zinc-600
                                       focus:outline-none focus:ring-2 focus:ring-offset-0
                                       transition-colors
                                       {{ $dateError
                                           ? 'border-red-500/50 focus:ring-red-500/40 focus:border-red-500/50'
                                           : 'border-white/10 focus:ring-amber-400/50 focus:border-amber-400/50' }}"
                            />
                        </div>
                        <div class="space-y-1.5">
                            <label for="bm-end" class="block text-xs font-semibold text-zinc-400 uppercase tracking-widest">
                                End date
                            </label>
                            <input id="bm-end" type="date" wire:model.live="endDate"
                                min="{{ now()->addDay()->toDateString() }}"
                                class="block w-full rounded-xl border px-3 py-2.5 text-sm font-medium
                                       bg-white/5 text-zinc-100 placeholder-zinc-600
                                       focus:outline-none focus:ring-2 focus:ring-offset-0
                                       transition-colors
                                       {{ $dateError
                                           ? 'border-red-500/50 focus:ring-red-500/40 focus:border-red-500/50'
                                           : 'border-white/10 focus:ring-amber-400/50 focus:border-amber-400/50' }}"
                            />
                        </div>
                    </div>

                    {{-- UI-1.1 inline error --}}
                    @if ($dateError)
                        <div role="alert"
                             class="flex items-center gap-2.5 rounded-xl bg-red-500/10 px-4 py-3
                                    text-sm text-red-400 ring-1 ring-red-500/20">
                            <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                            </svg>
                            {{ $dateError }}
                        </div>
                    @endif

                    {{-- Live price breakdown --}}
                    @if ($startDate && $endDate && !$dateError)
                        @php
                            $days        = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate));
                            $taxRate     = $bt->depot?->tax_rate ?? \App\Services\PricingCalculator::DEFAULT_TAX_RATE;
                            $discountRate = $days >= 7 ? \App\Services\PricingCalculator::WEEKLY_DISCOUNT_RATE : 0.0;
                            $breakdown   = app(\App\Services\PricingCalculator::class)->calculate(
                                $bt->dailyRate(), $bt->maintenanceFee(), $days, $discountRate, $taxRate,
                            );
                            $currency    = $bt->currency();
                            $taxPct      = round($breakdown->taxRate * 100);
                        @endphp
                        <div class="rounded-2xl bg-white/5 ring-1 ring-white/8 overflow-hidden">
                            <div class="px-4 py-3 space-y-2">
                                <div class="flex justify-between text-sm text-zinc-400">
                                    <span>{{ $breakdown->days }}{{ __('d') }} × {{ $currency->format($bt->daily_rate_cents) }}</span>
                                    <span class="tabular-nums">{{ $currency->format($breakdown->subtotal->cents) }}</span>
                                </div>
                                @if ($breakdown->discount->cents > 0)
                                    <div class="flex justify-between text-sm text-emerald-400">
                                        <span>{{ __('Discount') }} ({{ round($breakdown->discountRate * 100) }}%)</span>
                                        <span class="tabular-nums">−{{ $currency->format($breakdown->discount->cents) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-sm text-zinc-400">
                                    <span>{{ __('Tax') }} ({{ $taxPct }}%)</span>
                                    <span class="tabular-nums">{{ $currency->format($breakdown->tax->cents) }}</span>
                                </div>
                                @if ($breakdown->maintenanceFee->cents > 0)
                                    <div class="flex justify-between text-sm text-zinc-400">
                                        <span>{{ __('Maintenance fee') }}</span>
                                        <span class="tabular-nums">{{ $currency->format($breakdown->maintenanceFee->cents) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex justify-between items-center border-t border-white/5 px-4 py-3">
                                <span class="text-sm font-bold text-zinc-300">{{ __('Total') }}</span>
                                <span class="text-xl font-black text-amber-400 tabular-nums">{{ $currency->format($breakdown->total->cents) }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <form wire:submit.prevent="book" novalidate>
                        <div class="flex items-center justify-end gap-3">
                            <flux:modal.close>
                                <button type="button"
                                        class="rounded-xl px-4 py-2.5 text-sm font-medium text-zinc-400
                                               hover:text-zinc-200 hover:bg-white/5 transition-colors">
                                    Cancel
                                </button>
                            </flux:modal.close>

                            {{-- UI-1.2 spinner button --}}
                            <button
                                type="submit"
                                :disabled="{{ $dateError ? 'true' : 'false' }} || '{{ $startDate }}' === '' || '{{ $endDate }}' === ''"
                                wire:loading.attr="disabled"
                                wire:target="book"
                                data-test="book-tool-button"
                                class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-2.5
                                       text-sm font-bold text-zinc-900
                                       hover:bg-amber-300 active:scale-[0.98]
                                       transition-all disabled:opacity-50 disabled:cursor-not-allowed
                                       shadow-lg shadow-amber-400/20"
                            >
                                <span wire:loading.remove wire:target="book">Confirm booking</span>
                                <span wire:loading wire:target="book" class="flex items-center gap-2">
                                    <svg class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Processing…
                                </span>
                            </button>
                        </div>
                    </form>

                </div>
            @endif
        @endif
    </flux:modal>


    {{-- ════════════════════════════════════════════════════════════════════════
         MY BOOKINGS FLYOUT
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <flux:modal name="my-bookings" flyout class="w-full md:w-[30rem] !bg-zinc-900 !border-white/10">
        <div class="space-y-6">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-black text-white">My Bookings</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Your current and past tool rentals</p>
                </div>
                @php $total = $this->myBookings->count(); @endphp
                @if ($total > 0)
                    <span class="rounded-full bg-white/5 ring-1 ring-white/10 px-3 py-1 text-xs font-semibold text-zinc-300">
                        {{ $total }} {{ Str::plural('booking', $total) }}
                    </span>
                @endif
            </div>

            @if ($this->myBookings->isEmpty())
                <div class="flex flex-col items-center py-16 text-center">
                    <div class="mb-4 flex size-16 items-center justify-center rounded-full
                                bg-white/5 ring-1 ring-white/10">
                        <svg class="size-8 text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-zinc-400">No bookings yet</p>
                    <p class="mt-1 text-xs text-zinc-600">Start browsing the catalogue to make your first booking.</p>
                </div>

            @else
                <div class="space-y-3">
                    @foreach ($this->myBookings as $booking)
                        @php
                            $isActive = $booking->isActive();
                            $barClass = match($booking->booking_status) {
                                'active'    => 'bg-blue-400',
                                'confirmed' => 'bg-amber-400',
                                'returned'  => 'bg-emerald-400',
                                default     => 'bg-zinc-600',
                            };
                            $statusLabel = match($booking->booking_status) {
                                'active'    => ['label' => 'Active',    'cls' => 'bg-blue-500/20 text-blue-300 ring-blue-500/30'],
                                'confirmed' => ['label' => 'Confirmed', 'cls' => 'bg-amber-500/20 text-amber-300 ring-amber-500/30'],
                                'returned'  => ['label' => 'Returned',  'cls' => 'bg-emerald-500/20 text-emerald-300 ring-emerald-500/30'],
                                default     => ['label' => ucfirst($booking->booking_status), 'cls' => 'bg-zinc-700 text-zinc-400 ring-zinc-600'],
                            };
                            $days = $booking->start_date->diffInDays($booking->end_date);
                        @endphp

                        <div class="group flex overflow-hidden rounded-2xl bg-white/5 ring-1 ring-white/5
                                    hover:bg-white/[0.07] transition-colors">
                            {{-- Colour bar --}}
                            <div class="w-1 shrink-0 {{ $barClass }}"></div>

                            {{-- Tool thumbnail --}}
                            @if ($booking->tool->image_url)
                                <div class="w-14 shrink-0 overflow-hidden">
                                    <img src="{{ $booking->tool->image_url }}"
                                         alt="{{ $booking->tool->name }}"
                                         class="h-full w-full object-cover" />
                                </div>
                            @else
                                <div class="w-14 shrink-0 flex items-center justify-center
                                            bg-gradient-to-b {{ $booking->tool->categoryGradient() }}">
                                    <span class="text-xl">{{ $booking->tool->categoryEmoji() }}</span>
                                </div>
                            @endif

                            {{-- Details --}}
                            <div class="flex flex-1 items-center justify-between gap-3 px-3 py-3 min-w-0">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-zinc-100">
                                        {{ $booking->tool->name }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-zinc-500">
                                        {{ $booking->start_date->format('M j') }} – {{ $booking->end_date->format('M j, Y') }}
                                        <span class="text-zinc-600">&middot; {{ $days }}d</span>
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-center gap-2">
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 {{ $statusLabel['cls'] }}">
                                        {{ $statusLabel['label'] }}
                                    </span>
                                    @if ($isActive)
                                        <button
                                            type="button"
                                            wire:click="returnTool({{ $booking->id }})"
                                            wire:confirm="Confirm return of {{ $booking->tool->name }}?"
                                            class="rounded-lg bg-white/5 px-2 py-1 text-[11px] font-semibold
                                                   text-zinc-400 ring-1 ring-white/10
                                                   hover:bg-red-500/10 hover:text-red-400 hover:ring-red-500/20
                                                   transition-colors"
                                        >
                                            Return
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @error('return')
                            <p class="text-xs text-red-400 px-1">{{ $message }}</p>
                        @enderror
                    @endforeach
                </div>
            @endif
        </div>
    </flux:modal>

</div>