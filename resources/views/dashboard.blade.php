<x-layouts::app :title="__('Dashboard')">
    @php
        $user = auth()->user();

        $totalTools      = \App\Models\Tool::count();
        $availableTools  = \App\Models\Tool::where('status', \App\Enums\ToolStatus::Available)->count();
        $myBookings      = \App\Models\Booking::where('user_id', $user->id)->count();
        $activeBookings  = \App\Models\Booking::where('user_id', $user->id)
                               ->where('booking_status', 'active')->count();

        $recentBookings  = \App\Models\Booking::with('tool')
                               ->where('user_id', $user->id)
                               ->orderByDesc('start_date')
                               ->limit(5)
                               ->get();

        $upcomingBookings = \App\Models\Booking::with('tool')
                                ->where('user_id', $user->id)
                                ->where('booking_status', 'confirmed')
                                ->where('start_date', '>=', today())
                                ->orderBy('start_date')
                                ->limit(3)
                                ->get();
    @endphp

    <div class="space-y-8 bg-zinc-950 min-h-screen px-6 py-8">

        {{-- Welcome banner --}}
        <div class="relative overflow-hidden rounded-2xl bg-zinc-900 ring-1 ring-white/10" style="min-height: 220px;">

            {{-- Background hero image fading from right --}}
            <div class="pointer-events-none absolute inset-0">
                <img
                    src="https://images.unsplash.com/photo-1504148455328-c376907d081c?w=1200&q=80&fit=crop"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover object-center opacity-20"
                />
                {{-- Fade left: makes image dissolve into the card background --}}
                <div class="absolute inset-0" style="background: linear-gradient(to right, #18181b 35%, transparent 70%, #18181b 100%);"></div>
                {{-- Subtle top/bottom vignette --}}
                <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(24,24,27,0.6) 0%, transparent 40%, rgba(24,24,27,0.6) 100%);"></div>
            </div>

            {{-- Amber glow hint --}}
            <div class="pointer-events-none absolute -top-16 -left-16 size-64 rounded-full bg-amber-500/8 blur-3xl"></div>

            {{-- Content --}}
            <div class="relative z-10 px-8 py-10">
                <p class="text-sm font-medium text-zinc-500">{{ now()->format('l, F j') }}</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-white">
                    {{ __('Welcome back') }}, {{ $user->name }} 👋
                </h1>
                <p class="mt-2 text-zinc-400">
                    @if ($activeBookings > 0)
                        {{ __('You have') }} <span class="font-semibold text-white">{{ $activeBookings }}</span> {{ __('active rental(s) in progress.') }}
                    @else
                        {{ __('Ready to find your next tool? Browse the catalogue below.') }}
                    @endif
                </p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('tools.index') }}" wire:navigate
                       class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-4 py-2 text-sm font-semibold text-zinc-900 shadow-lg shadow-amber-400/20 hover:bg-amber-300 transition-colors">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>
                        {{ __('Browse Tools') }}
                    </a>
                    <a href="{{ route('bookings.index') }}" wire:navigate
                       class="inline-flex items-center gap-2 rounded-xl bg-white/5 px-4 py-2 text-sm font-semibold text-zinc-300 ring-1 ring-white/10 hover:bg-white/10 transition-colors">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        {{ __('My Bookings') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats row --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="flex items-center gap-4 rounded-2xl bg-white/5 ring-1 ring-white/10 p-5">
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-black tabular-nums text-white">{{ $totalTools }}</p>
                    <p class="text-xs text-zinc-500">{{ __('Total Tools') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-2xl bg-white/5 ring-1 ring-white/10 p-5">
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-black tabular-nums text-white">{{ $availableTools }}</p>
                    <p class="text-xs text-zinc-500">{{ __('Available Now') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-2xl bg-white/5 ring-1 ring-white/10 p-5">
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-400">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-black tabular-nums text-white">{{ $myBookings }}</p>
                    <p class="text-xs text-zinc-500">{{ __('My Bookings') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-2xl bg-white/5 ring-1 ring-white/10 p-5">
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-black tabular-nums text-white">{{ $activeBookings }}</p>
                    <p class="text-xs text-zinc-500">{{ __('Active Rentals') }}</p>
                </div>
            </div>
        </div>

        {{-- Two column lower section --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Recent bookings (2/3 width) --}}
            <div class="space-y-4 rounded-2xl bg-white/5 ring-1 ring-white/10 p-6 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-white">{{ __('Recent Bookings') }}</h2>
                    <a href="{{ route('bookings.index') }}" wire:navigate
                       class="text-sm text-zinc-400 hover:text-amber-400 transition-colors">
                        {{ __('View all') }} →
                    </a>
                </div>

                @if ($recentBookings->isEmpty())
                    <div class="flex flex-col items-center py-10 text-center">
                        <div class="mb-3 flex size-14 items-center justify-center rounded-full bg-white/5">
                            <svg class="size-7 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        </div>
                        <p class="text-sm text-zinc-500">{{ __('No bookings yet.') }}</p>
                        <a href="{{ route('tools.index') }}" wire:navigate
                           class="mt-4 inline-flex items-center gap-2 rounded-xl bg-amber-400 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-amber-300 transition-colors">
                            {{ __('Find a tool') }}
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-white/5">
                        @foreach ($recentBookings as $booking)
                            <div class="flex items-center gap-4 py-3">
                                <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-white/5">
                                    <svg class="size-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-white">
                                        {{ $booking->tool->name }}
                                    </p>
                                    <p class="text-xs text-zinc-500">
                                        {{ $booking->start_date->format('M j') }} – {{ $booking->end_date->format('M j, Y') }}
                                    </p>
                                </div>
                                @php
                                    $statusColour = match($booking->booking_status) {
                                        'active'    => 'bg-blue-500/15 text-blue-300 ring-blue-500/20',
                                        'confirmed' => 'bg-amber-500/15 text-amber-300 ring-amber-500/20',
                                        'returned'  => 'bg-emerald-500/15 text-emerald-300 ring-emerald-500/20',
                                        default     => 'bg-zinc-500/15 text-zinc-400 ring-zinc-500/20',
                                    };
                                @endphp
                                <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold ring-1 {{ $statusColour }}">
                                    {{ ucfirst($booking->booking_status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Upcoming confirmed (1/3 width) --}}
            <div class="space-y-4 rounded-2xl bg-white/5 ring-1 ring-white/10 p-6">
                <h2 class="text-base font-bold text-white">{{ __('Coming Up') }}</h2>

                @if ($upcomingBookings->isEmpty())
                    <div class="flex flex-col items-center py-6 text-center">
                        <svg class="size-10 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        <p class="mt-2 text-xs text-zinc-500">{{ __('No confirmed upcoming rentals.') }}</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($upcomingBookings as $booking)
                            <div class="rounded-xl bg-white/5 ring-1 ring-white/10 px-4 py-3">
                                <p class="text-sm font-semibold text-white truncate">
                                    {{ $booking->tool->name }}
                                </p>
                                <p class="mt-0.5 flex items-center gap-1 text-xs text-zinc-500">
                                    <svg class="inline size-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                    {{ $booking->start_date->format('M j') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <a href="{{ route('depots.index') }}" wire:navigate
                   class="flex w-full items-center justify-center gap-2 rounded-xl bg-white/5 px-4 py-2 text-sm font-medium text-zinc-300 ring-1 ring-white/10 hover:bg-white/10 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    {{ __('Find a depot near me') }}
                </a>
            </div>

        </div>
    </div>
</x-layouts::app>
