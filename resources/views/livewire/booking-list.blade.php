<div class="min-h-screen bg-zinc-950 px-6 py-8 space-y-8">

    {{-- Header --}}
    <div class="flex items-end justify-between">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-white">{{ __('My Bookings') }}</h1>
            <p class="mt-1 text-zinc-400">{{ __('View and manage your current and past tool rentals.') }}</p>
        </div>
        <a href="{{ route('tools.index') }}" wire:navigate
           class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-4 py-2 text-sm font-semibold text-zinc-900 shadow-lg shadow-amber-400/20 hover:bg-amber-300 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('Book a tool') }}
        </a>
    </div>

    <div class="border-t border-white/5"></div>

    @if ($bookings->isEmpty())
        <div class="flex flex-col items-center py-20 text-center">
            <div class="mb-4 flex size-16 items-center justify-center rounded-2xl bg-white/5 ring-1 ring-white/10">
                <svg class="size-8 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            </div>
            <p class="font-semibold text-white">{{ __('No bookings yet') }}</p>
            <p class="mt-1 text-sm text-zinc-500">{{ __('Head over to the catalogue and pick your first tool.') }}</p>
            <a href="{{ route('tools.index') }}" wire:navigate
               class="mt-5 inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-2.5 text-sm font-semibold text-zinc-900 shadow-lg shadow-amber-400/20 hover:bg-amber-300 transition-colors">
                {{ __('Browse tools') }}
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($bookings as $booking)
                <div class="overflow-hidden rounded-2xl bg-white/5 ring-1 ring-white/10">
                    {{-- Status bar on left --}}
                    <div class="flex">
                        @php
                            $barColour = match($booking->booking_status) {
                                'active'    => 'bg-blue-400',
                                'confirmed' => 'bg-amber-400',
                                'returned'  => 'bg-emerald-400',
                                default     => 'bg-zinc-600',
                            };
                        @endphp
                        <div class="w-1 shrink-0 {{ $barColour }}"></div>

                        <div class="flex flex-1 flex-wrap items-center justify-between gap-4 px-5 py-4">
                            <div class="flex items-center gap-4">
                                {{-- Tool thumbnail or icon --}}
                                @if ($booking->tool->image_url)
                                    <img src="{{ $booking->tool->image_url }}" alt="{{ $booking->tool->name }}"
                                         class="size-12 shrink-0 rounded-xl object-cover ring-1 ring-white/10" />
                                @else
                                    <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-white/5 ring-1 ring-white/10">
                                        <svg class="size-6 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-white">
                                        {{ $booking->tool->name }}
                                        <span class="ml-1.5 font-mono text-xs text-zinc-500">{{ $booking->tool->sku }}</span>
                                    </p>
                                    <p class="mt-0.5 flex items-center gap-1.5 text-sm text-zinc-500">
                                        <svg class="size-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                        {{ $booking->start_date->format('M j') }}
                                        &ndash;
                                        {{ $booking->end_date->format('M j, Y') }}
                                        <span class="text-zinc-700">&middot;</span>
                                        {{ $booking->start_date->diffInDays($booking->end_date) }} {{ __('day(s)') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                {{-- Status pill --}}
                                @php
                                    $statusColour = match($booking->booking_status) {
                                        'active'    => 'bg-blue-500/15 text-blue-300 ring-blue-500/20',
                                        'confirmed' => 'bg-amber-500/15 text-amber-300 ring-amber-500/20',
                                        'returned'  => 'bg-emerald-500/15 text-emerald-300 ring-emerald-500/20',
                                        default     => 'bg-zinc-500/15 text-zinc-400 ring-zinc-500/20',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold ring-1 {{ $statusColour }}">
                                    {{ ucfirst($booking->booking_status) }}
                                </span>

                                @if ($booking->isActive())
                                    <button
                                        wire:click="returnTool({{ $booking->id }})"
                                        wire:confirm="{{ __('Confirm return of') }} {{ $booking->tool->name }}?"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-white/5 px-3 py-1.5 text-sm font-semibold text-zinc-300 ring-1 ring-white/10 hover:bg-white/10 transition-colors"
                                    >
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                                        {{ __('Return') }}
                                    </button>
                                @endif

                                {{-- FR-3.9 — Cancel a confirmed booking (> 48h before start) --}}
                                @if ($booking->isConfirmed())
                                    <button
                                        wire:click="cancelBooking({{ $booking->id }})"
                                        wire:confirm="{{ __('Cancel booking for') }} {{ $booking->tool->name }}?"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-red-500/10 px-3 py-1.5 text-sm font-semibold text-red-300 ring-1 ring-red-500/20 hover:bg-red-500/20 transition-colors"
                                    >
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                        {{ __('Cancel') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @error('return')
                    <div class="flex items-center gap-3 rounded-2xl bg-red-500/10 ring-1 ring-red-500/20 px-5 py-4 text-sm text-red-300">
                        <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        <span><strong class="font-semibold">{{ __('Return failed') }}</strong> — {{ $message }}</span>
                    </div>
                @enderror

                @error('cancel')
                    <div class="flex items-center gap-3 rounded-2xl bg-red-500/10 ring-1 ring-red-500/20 px-5 py-4 text-sm text-red-300">
                        <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        <span><strong class="font-semibold">{{ __('Cancellation failed') }}</strong> — {{ $message }}</span>
                    </div>
                @enderror
            @endforeach
        </div>
    @endif
</div>
