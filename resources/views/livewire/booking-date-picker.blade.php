{{--
    BookingDatePicker view
    UI-1.1: Red border + "Invalid Date Range." on invalid input.
    UI-1.2: Loading spinner + disabled button while booking is in flight.
--}}
<div class="min-h-screen bg-zinc-950 px-6 py-8 space-y-6">

    {{-- Back nav --}}
    <a href="{{ route('tools.index') }}" wire:navigate
       class="inline-flex items-center gap-2 text-sm text-zinc-400 hover:text-amber-400 transition-colors">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        {{ __('All tools') }}
    </a>

    @if ($booked)

        {{-- ── Success state ──────────────────────────────────────────── --}}
        <div class="flex flex-col items-center py-20 text-center">
            <div class="mb-5 flex size-20 items-center justify-center rounded-full bg-emerald-500/10 ring-1 ring-emerald-500/20">
                <svg class="size-10 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <h1 class="text-2xl font-black text-white">{{ __('Booking confirmed!') }}</h1>
            <p class="mt-2 max-w-sm text-sm text-zinc-400">
                {{ __('Your reservation for') }} <span class="font-semibold text-white">{{ $this->tool->name }}</span>
                {{ __('has been created. You\'ll find it in My Bookings.') }}
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('bookings.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-2.5 text-sm font-semibold text-zinc-900 shadow-lg shadow-amber-400/20 hover:bg-amber-300 transition-colors">
                    {{ __('View my bookings') }}
                </a>
                <a href="{{ route('tools.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-xl bg-white/5 px-5 py-2.5 text-sm font-semibold text-zinc-300 ring-1 ring-white/10 hover:bg-white/10 transition-colors">
                    {{ __('Browse more tools') }}
                </a>
            </div>
        </div>

    @else

        {{-- ── Two-column layout: product info + booking form ─────────── --}}
        <div class="grid gap-8 lg:grid-cols-5">

            {{-- Left: tool info (3 cols) --}}
            <div class="space-y-6 lg:col-span-3">

                {{-- Tool hero image --}}
                @if ($this->tool->image_url)
                    <div class="overflow-hidden rounded-2xl ring-1 ring-white/10">
                        <img src="{{ $this->tool->image_url }}" alt="{{ $this->tool->name }}"
                             class="h-56 w-full object-cover" />
                    </div>
                @endif

                <div>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-black tracking-tight text-white">{{ $this->tool->name }}</h1>
                            <p class="mt-1 font-mono text-sm text-zinc-500">{{ $this->tool->sku }}</p>
                        </div>
                        @php
                            $statusClr = match($this->tool->status->value) {
                                'available' => 'bg-emerald-500/15 text-emerald-300 ring-emerald-500/20',
                                'reserved'  => 'bg-amber-500/15 text-amber-300 ring-amber-500/20',
                                default     => 'bg-zinc-500/15 text-zinc-400 ring-zinc-500/20',
                            };
                        @endphp
                        <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 {{ $statusClr }}">
                            {{ $this->tool->status->label() }}
                        </span>
                    </div>

                    @if ($this->tool->description)
                        <p class="mt-4 text-sm leading-relaxed text-zinc-400">
                            {{ $this->tool->description }}
                        </p>
                    @endif
                </div>

                {{-- Pricing breakdown (FR-5.5 — live breakdown using PricingCalculator) --}}
                @if ($startDate && $endDate && !$dateError)
                    @php
                        $days       = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate));
                        $taxRate    = $this->tool->depot?->tax_rate ?? \App\Services\PricingCalculator::DEFAULT_TAX_RATE;
                        $discountRate = $days >= 7 ? \App\Services\PricingCalculator::WEEKLY_DISCOUNT_RATE : 0.0;
                        $breakdown  = app(\App\Services\PricingCalculator::class)->calculate(
                            $this->tool->dailyRate(),
                            $this->tool->maintenanceFee(),
                            $days,
                            $discountRate,
                            $taxRate,
                        );
                        $currency   = $this->tool->currency();
                        $taxPct     = round($breakdown->taxRate * 100);
                    @endphp
                    <div class="space-y-3 rounded-2xl bg-white/5 ring-1 ring-white/10 p-5">
                        <p class="text-sm font-bold text-white">{{ __('Price breakdown') }}</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-zinc-400">
                                <span>{{ $breakdown->days }} {{ __('day(s)') }} × {{ $currency->format($this->tool->daily_rate_cents) }}</span>
                                <span>{{ $currency->format($breakdown->subtotal->cents) }}</span>
                            </div>
                            @if ($breakdown->discount->cents > 0)
                                <div class="flex justify-between text-emerald-400">
                                    <span>{{ __('Discount') }} ({{ round($breakdown->discountRate * 100) }}%)</span>
                                    <span>−{{ $currency->format($breakdown->discount->cents) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-zinc-400">
                                <span>{{ __('Tax') }} ({{ $taxPct }}%)</span>
                                <span>{{ $currency->format($breakdown->tax->cents) }}</span>
                            </div>
                            @if ($breakdown->maintenanceFee->cents > 0)
                                <div class="flex justify-between text-zinc-400">
                                    <span>{{ __('Maintenance fee') }}</span>
                                    <span>{{ $currency->format($breakdown->maintenanceFee->cents) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between border-t border-white/10 pt-2 font-semibold text-white">
                                <span>{{ __('Total') }}</span>
                                <span class="text-amber-400 tabular-nums">{{ $currency->format($breakdown->total->cents) }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Rate card when no dates yet --}}
                    <div class="flex items-center gap-4 rounded-2xl bg-white/5 ring-1 ring-white/10 p-5">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-amber-500/10">
                            <svg class="size-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.171-.879-1.171-2.303 0-3.182.879-.659 2.003-.659 3.006 0l.415.33M15 9H9"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ __('Daily rate') }}</p>
                            <p class="text-2xl font-black tabular-nums text-white">
                                {{ $this->tool->formattedDailyRate() }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Depot info --}}
                @if ($this->tool->depot)
                    <div class="flex items-start gap-3 rounded-2xl bg-white/5 ring-1 ring-white/10 p-5">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white/5">
                            <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">
                                {{ $this->tool->depot->name }}
                            </p>
                            <p class="text-xs text-zinc-500">{{ $this->tool->depot->shortAddress() }}</p>
                            @if ($this->tool->depot->phone)
                                <p class="mt-1 text-xs text-zinc-500">{{ $this->tool->depot->phone }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ── FR-12.2 — Availability calendar (booked date ranges) ── --}}
                @php $ranges = $this->bookedRanges; @endphp
                @if (count($ranges) > 0)
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-5 space-y-3">
                        <p class="text-sm font-bold text-white">{{ __('Booked periods') }}</p>
                        <div class="space-y-1.5">
                            @foreach ($ranges as $range)
                                <div class="flex items-center gap-2 text-xs text-zinc-400">
                                    <span class="inline-block size-2 rounded-full bg-red-400"></span>
                                    {{ \Carbon\Carbon::parse($range['start'])->format('M j, Y') }}
                                    &mdash;
                                    {{ \Carbon\Carbon::parse($range['end'])->format('M j, Y') }}
                                </div>
                            @endforeach
                        </div>
                        <p class="text-[11px] text-zinc-600">{{ __('Dates shown are already confirmed or active bookings.') }}</p>
                    </div>
                @endif

                {{-- ── FR-12.1 — Waitlist ─────────────────────────────────── --}}
                @if (! $this->tool->isAvailable())
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-5 space-y-3">
                        <p class="text-sm font-bold text-white">{{ __('Tool unavailable') }}</p>
                        <p class="text-xs text-zinc-400">
                            {{ __('This tool is currently :status. Join the waitlist to be notified when it becomes available.', ['status' => $this->tool->status->label()]) }}
                        </p>
                        @if ($this->isOnWaitlist)
                            <button wire:click="leaveWaitlist" class="inline-flex items-center gap-1.5 rounded-xl bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 ring-1 ring-red-500/20 hover:bg-red-500/20 transition-colors">
                                {{ __('Leave waitlist') }}
                            </button>
                        @else
                            <button wire:click="joinWaitlist" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-300 ring-1 ring-amber-500/20 hover:bg-amber-500/20 transition-colors">
                                {{ __('Join waitlist') }}
                            </button>
                        @endif
                    </div>
                @endif

                {{-- ── FR-11.2 — Reviews display ─────────────────────────── --}}
                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-bold text-white">{{ __('Reviews') }}</p>
                        @php
                            $avgRating = \App\Models\Review::where('tool_id', $toolId)->where('is_visible', true)->avg('rating');
                            $reviewCount = \App\Models\Review::where('tool_id', $toolId)->where('is_visible', true)->count();
                        @endphp
                        @if ($reviewCount > 0)
                            <span class="text-xs text-zinc-400">
                                <span class="text-amber-400">★</span> {{ number_format($avgRating, 1) }} ({{ $reviewCount }})
                            </span>
                        @endif
                    </div>

                    @forelse ($this->reviews as $review)
                        <div class="space-y-1 border-t border-white/5 pt-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-white">{{ $review->user?->name ?? __('Anonymous') }}</span>
                                    <span class="text-amber-400 text-xs">
                                        @for ($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                    </span>
                                </div>
                                @if (auth()->user()?->isStaff() || auth()->user()?->isAdmin())
                                    <button wire:click="hideReview({{ $review->id }})" class="text-[10px] text-red-400 hover:text-red-300">{{ __('Hide') }}</button>
                                @endif
                            </div>
                            @if ($review->body)
                                <p class="text-xs text-zinc-400 leading-relaxed">{{ $review->body }}</p>
                            @endif
                            <p class="text-[10px] text-zinc-600">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500">{{ __('No reviews yet.') }}</p>
                    @endforelse
                </div>

                {{-- ── FR-11.1 — Submit review (if eligible) ─────────────── --}}
                @if ($this->reviewableBooking)
                    <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-5 space-y-4">
                        <p class="text-sm font-bold text-white">{{ __('Leave a review') }}</p>
                        <p class="text-xs text-zinc-400">{{ __('You recently returned this tool. How was your experience?') }}</p>

                        <input type="hidden" wire:model="reviewBookingId" value="{{ $this->reviewableBooking->id }}" />

                        {{-- Star rating --}}
                        <div class="flex gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" wire:click="$set('reviewRating', {{ $i }}); $set('reviewBookingId', {{ $this->reviewableBooking->id }})"
                                        class="text-2xl transition-colors {{ $reviewRating >= $i ? 'text-amber-400' : 'text-zinc-600 hover:text-amber-400/50' }}">
                                    ★
                                </button>
                            @endfor
                        </div>

                        <textarea wire:model="reviewBody" rows="3" placeholder="{{ __('Write a review (optional)…') }}"
                                  class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:border-amber-400/50 focus:ring-2 focus:ring-amber-400/50 focus:outline-none"
                        ></textarea>

                        @error('reviewRating') <p class="text-xs text-red-400">{{ $message }}</p> @enderror

                        <button wire:click="submitReview" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-400 px-4 py-2 text-sm font-bold text-zinc-900 hover:bg-amber-300 transition-colors">
                            {{ __('Submit review') }}
                        </button>
                    </div>
                @endif

                {{-- ── FR-13.1 — Damage declaration return flow ──────────── --}}
                @if ($returningBookingId)
                    <div class="rounded-2xl bg-orange-500/5 ring-1 ring-orange-500/20 p-5 space-y-4">
                        <p class="text-sm font-bold text-white">{{ __('Return tool — declare condition') }}</p>

                        <div class="space-y-2">
                            @foreach (['undamaged' => __('Undamaged'), 'minor_damage' => __('Minor damage'), 'major_damage' => __('Major damage')] as $val => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="damageCondition" value="{{ $val }}"
                                           class="rounded-full border-white/20 bg-white/5 text-amber-400 focus:ring-amber-400/50" />
                                    <span class="text-sm text-zinc-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                        @if ($damageCondition !== 'undamaged')
                            <textarea wire:model="damageDescription" rows="3" placeholder="{{ __('Describe the damage…') }}"
                                      class="w-full rounded-xl border bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-400/50
                                             {{ $errors->has('damageDescription') ? 'border-red-500/50 ring-1 ring-red-500/50' : 'border-white/10' }}"
                                      aria-invalid="{{ $errors->has('damageDescription') ? 'true' : 'false' }}"
                            ></textarea>
                            @error('damageDescription') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                        @endif

                        <div class="flex gap-2">
                            <button wire:click="confirmReturn" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-400 px-4 py-2 text-sm font-bold text-zinc-900 hover:bg-amber-300 transition-colors">
                                {{ __('Confirm return') }}
                            </button>
                            <button wire:click="cancelReturn" class="inline-flex items-center gap-1.5 rounded-xl bg-white/5 px-4 py-2 text-sm font-semibold text-zinc-300 ring-1 ring-white/10 hover:bg-white/10 transition-colors">
                                {{ __('Cancel') }}
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right: booking form (2 cols) --}}
            <div class="lg:col-span-2">
                <div class="sticky top-6 space-y-5 rounded-2xl bg-zinc-900 ring-1 ring-white/10 p-6">
                    <h2 class="text-lg font-bold text-white">{{ __('Reserve this tool') }}</h2>

                    <form wire:submit.prevent="book" class="space-y-4" novalidate>

                        {{-- Start date --}}
                        <div class="space-y-1.5">
                            <label for="start-date" class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ __('Start date') }}</label>
                            <input
                                id="start-date"
                                type="date"
                                wire:model.live="startDate"
                                min="{{ now()->addDay()->toDateString() }}"
                                class="w-full rounded-xl border bg-white/5 px-3 py-2 text-sm text-zinc-100
                                       transition-colors focus:outline-none focus:ring-2 focus:ring-offset-0
                                       {{ $dateError
                                           ? 'border-red-500/50 ring-1 ring-red-500/50 focus:ring-red-500/50'
                                           : 'border-white/10 focus:border-amber-400/50 focus:ring-amber-400/50' }}"
                                aria-invalid="{{ $dateError ? 'true' : 'false' }}"
                            />
                        </div>

                        {{-- End date --}}
                        <div class="space-y-1.5">
                            <label for="end-date" class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ __('End date') }}</label>
                            <input
                                id="end-date"
                                type="date"
                                wire:model.live="endDate"
                                min="{{ now()->addDay()->toDateString() }}"
                                class="w-full rounded-xl border bg-white/5 px-3 py-2 text-sm text-zinc-100
                                       transition-colors focus:outline-none focus:ring-2 focus:ring-offset-0
                                       {{ $dateError
                                           ? 'border-red-500/50 ring-1 ring-red-500/50 focus:ring-red-500/50'
                                           : 'border-white/10 focus:border-amber-400/50 focus:ring-amber-400/50' }}"
                                aria-invalid="{{ $dateError ? 'true' : 'false' }}"
                            />
                        </div>

                        {{-- UI-1.1 inline error --}}
                        @if ($dateError)
                            <p id="date-error" role="alert"
                               class="flex items-center gap-1.5 rounded-xl bg-red-500/10 px-3 py-2 text-sm font-medium text-red-300 ring-1 ring-red-500/20">
                                <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                                {{ $dateError }}
                            </p>
                        @endif

                        {{-- UI-1.2 Submit with spinner --}}
                        <button
                            type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-400 px-5 py-3 text-sm font-bold text-zinc-900 shadow-lg shadow-amber-400/20 hover:bg-amber-300 transition-colors disabled:opacity-40"
                            :disabled="(bool) $dateError || $startDate === '' || $endDate === ''"
                            wire:loading.attr="disabled"
                            wire:target="book"
                            data-test="book-tool-button"
                        >
                            <span wire:loading.remove wire:target="book">{{ __('Confirm booking') }}</span>
                            <span wire:loading wire:target="book" class="flex items-center gap-2">
                                <svg class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                {{ __('Processing…') }}
                            </span>
                        </button>

                        @error('booking')
                            <p class="text-sm text-red-400">{{ $message }}</p>
                        @enderror

                    </form>
                </div>
            </div>

        </div>

    @endif
</div>
