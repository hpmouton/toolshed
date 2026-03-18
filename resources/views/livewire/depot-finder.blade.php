<div>
<div class="min-h-screen bg-zinc-950 px-6 py-8 space-y-8">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-zinc-900 ring-1 ring-white/10 px-8 py-8">
        <div class="pointer-events-none absolute -top-16 -right-16 size-72 rounded-full bg-amber-500/5 blur-3xl"></div>
        <div class="relative z-10">
            <h1 class="text-3xl font-black tracking-tight text-white">{{ __('Find a Depot') }}</h1>
            <p class="mt-1 text-zinc-400">{{ __('Locate the nearest ToolShed depot and browse available tools in your area.') }}</p>
        </div>
    </div>

    {{-- ── Location & Preferences ────────────────────────────────────────── --}}
    <div class="grid gap-6 md:grid-cols-2">

        {{-- Coordinates search --}}
        <div class="space-y-5 rounded-2xl bg-white/5 ring-1 ring-white/10 p-6">
            <div>
                <h2 class="text-base font-bold text-white">{{ __('Your Location') }}</h2>
                <p class="mt-1 text-sm text-zinc-400">{{ __('Enter your coordinates or pick a city to find nearby depots.') }}</p>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ __('Latitude') }}</label>
                        <input type="number" wire:model.live="lat" step="0.0001" min="-90" max="90" placeholder="40.7128"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:border-amber-400/50 focus:outline-none focus:ring-1 focus:ring-amber-400/50" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ __('Longitude') }}</label>
                        <input type="number" wire:model.live="lng" step="0.0001" min="-180" max="180" placeholder="-74.0060"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:border-amber-400/50 focus:outline-none focus:ring-1 focus:ring-amber-400/50" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ __('Search radius (km)') }}</label>
                    <input type="number" wire:model.live="radiusKm" min="10" max="5000" step="10"
                           class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:border-amber-400/50 focus:outline-none focus:ring-1 focus:ring-amber-400/50" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ __('Filter by country code') }}</label>
                    <input wire:model.live="countryFilter" placeholder="e.g. DE, AU, ZA" maxlength="2"
                           class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:border-amber-400/50 focus:outline-none focus:ring-1 focus:ring-amber-400/50" />
                </div>

                {{-- Quick-pick preset cities --}}
                <div>
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-widest text-zinc-500">{{ __('Quick picks') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ([
                            'New York'  => [40.7128,  -74.0060],
                            'London'    => [51.5074,   -0.1278],
                            'Sydney'    => [-33.8688, 151.2093],
                            'Cape Town' => [-33.9249,  18.4241],
                            'Tokyo'     => [35.6762,  139.6503],
                            'Dubai'     => [25.2048,   55.2708],
                            'São Paulo' => [-23.5505, -46.6333],
                        ] as $city => [$clat, $clng])
                            <button type="button"
                                    wire:click="$set('lat', {{ $clat }}); $set('lng', {{ $clng }})"
                                    class="rounded-full bg-white/5 px-3 py-1 text-xs font-medium text-zinc-400 ring-1 ring-white/10 hover:bg-white/10 hover:text-white transition-colors">
                                {{ $city }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- FR-6.6 — Browser geolocation --}}
                <div>
                    <button type="button"
                            x-data
                            x-on:click="
                                if (navigator.geolocation) {
                                    navigator.geolocation.getCurrentPosition(
                                        (pos) => $wire.setGeolocation(pos.coords.latitude, pos.coords.longitude),
                                        (err) => $wire.geolocationFailed(err.message)
                                    );
                                } else {
                                    $wire.geolocationFailed('{{ __('Geolocation is not supported by your browser.') }}');
                                }
                            "
                            class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-zinc-300 hover:bg-white/10 hover:text-white transition-colors">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                        </svg>
                        {{ __('Use my location') }}
                    </button>
                    @if ($geoError)
                        <p class="mt-1.5 text-xs text-red-400">{{ $geoError }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Preferences --}}
        <div class="space-y-5 rounded-2xl bg-white/5 ring-1 ring-white/10 p-6">
            <div>
                <h2 class="text-base font-bold text-white">{{ __('My Preferences') }}</h2>
                <p class="mt-1 text-sm text-zinc-400">{{ __('Saved to your profile and used across the app.') }}</p>
            </div>
            <form wire:submit.prevent="savePreferences" class="space-y-4">

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ __('Preferred currency') }}</label>
                    <select wire:model="preferredCurrency"
                            class="w-full rounded-xl border border-white/10 bg-zinc-900 px-3 py-2 text-sm text-zinc-100 focus:border-amber-400/50 focus:outline-none focus:ring-1 focus:ring-amber-400/50">
                        @foreach ($currencyOptions as $code => $label)
                            <option value="{{ $code }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ __('Your city') }}</label>
                    <input wire:model="preferredCity" placeholder="e.g. Berlin"
                           class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:border-amber-400/50 focus:outline-none focus:ring-1 focus:ring-amber-400/50" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ __('Country code (ISO 2-letter)') }}</label>
                    <input wire:model="preferredCountry" placeholder="e.g. DE" maxlength="2"
                           class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:border-amber-400/50 focus:outline-none focus:ring-1 focus:ring-amber-400/50" />
                </div>

                <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-2.5 text-sm font-semibold text-zinc-900 shadow-lg shadow-amber-400/20 hover:bg-amber-300 transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="savePreferences">{{ __('Save preferences') }}</span>
                    <span wire:loading wire:target="savePreferences" class="flex items-center gap-2">
                        <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        {{ __('Saving…') }}
                    </span>
                </button>

                @if ($savedMessage)
                    <div class="flex items-center gap-3 rounded-xl bg-emerald-500/10 ring-1 ring-emerald-500/20 px-4 py-3 text-sm text-emerald-300">
                        <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        {{ $savedMessage }}
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- ── Nearby Depots ───────────────────────────────────────────────────── --}}
    @if ($lat != 0 || $lng != 0)
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-bold text-white">
                    @if ($nearbyDepots->isNotEmpty())
                        {{ $nearbyDepots->count() }} {{ __('depot(s) within') }} {{ number_format($radiusKm) }} km
                    @else
                        {{ __('No depots found') }}
                    @endif
                </h2>
                @if ($nearbyDepots->isNotEmpty())
                    <span class="inline-flex items-center rounded-full bg-white/5 px-2.5 py-0.5 text-xs font-medium text-zinc-400 ring-1 ring-white/10">
                        {{ number_format($radiusKm) }} km radius
                    </span>
                @endif
            </div>

            @if ($nearbyDepots->isEmpty())
                <div class="flex flex-col items-center py-16 text-center">
                    <svg class="size-12 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    <p class="mt-3 text-sm text-zinc-500">{{ __('No depots found in this area. Try increasing the search radius.') }}</p>
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($nearbyDepots as $depot)
                        <div class="group flex flex-col overflow-hidden rounded-2xl bg-white/5 ring-1 ring-white/10
                                    transition-all duration-200 hover:-translate-y-0.5 hover:ring-amber-400/30">

                            {{-- Header band --}}
                            <div class="flex items-center justify-between bg-white/5 px-5 py-3">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-white">{{ $depot->name }}</p>
                                    <p class="text-xs text-zinc-500">{{ $depot->shortAddress() }}</p>
                                </div>
                                <span class="ml-2 shrink-0 inline-flex items-center rounded-full bg-white/5 px-2.5 py-0.5 font-mono text-xs text-zinc-400 ring-1 ring-white/10">
                                    {{ $depot->currency_code }}
                                </span>
                            </div>

                            <div class="flex flex-1 flex-col gap-3 p-5">
                                {{-- Distance --}}
                                <div class="flex items-center gap-2 text-sm text-zinc-400">
                                    <svg class="size-4 shrink-0 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                    <span class="font-semibold text-white">{{ number_format($depot->distance_km, 1) }} km</span>
                                    <span>{{ __('away') }}</span>
                                </div>

                                {{-- Available tool count --}}
                                @php
                                    $toolCount = $depot->tools()
                                        ->where('status', \App\Enums\ToolStatus::Available)
                                        ->count();
                                @endphp
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="size-4 shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>
                                    <span class="font-semibold text-white">{{ $toolCount }}</span>
                                    <span class="text-zinc-500">{{ __('tool(s) available') }}</span>
                                </div>

                                {{-- Contact --}}
                                @if ($depot->phone || $depot->email)
                                    <div class="space-y-0.5 text-xs text-zinc-500">
                                        @if ($depot->phone)<p>📞 {{ $depot->phone }}</p>@endif
                                        @if ($depot->email)<p>✉️ {{ $depot->email }}</p>@endif
                                    </div>
                                @endif

                                {{-- CTA --}}
                                <a href="{{ route('tools.index', ['depot' => $depot->id]) }}" wire:navigate
                                   class="mt-auto flex w-full items-center justify-center rounded-xl bg-amber-400 px-4 py-2 text-sm font-semibold text-zinc-900 shadow-lg shadow-amber-400/20 hover:bg-amber-300 transition-colors">
                                    {{ __('Browse tools here') }} →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>


    {{-- Ã¢â€â‚¬Ã¢â€â‚¬ Location & Preferences Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
    <div class="grid gap-6 md:grid-cols-2">

        {{-- Coordinates search --}}
        <flux:card class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('Your Location') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Enter your coordinates or pick a city to find nearby depots.') }}</flux:text>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Latitude') }}</flux:label>
                        <flux:input type="number" wire:model.live="lat" step="0.0001" min="-90" max="90" placeholder="40.7128" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Longitude') }}</flux:label>
                        <flux:input type="number" wire:model.live="lng" step="0.0001" min="-180" max="180" placeholder="-74.0060" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('Search radius (km)') }}</flux:label>
                    <flux:input type="number" wire:model.live="radiusKm" min="10" max="5000" step="10" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Filter by country code') }}</flux:label>
                    <flux:input wire:model.live="countryFilter" placeholder="e.g. DE, AU, ZA" maxlength="2" />
                </flux:field>

                {{-- Quick-pick preset cities --}}
                <div>
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-widest text-zinc-400">{{ __('Quick picks') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ([
                            'New York'  => [40.7128,  -74.0060],
                            'London'    => [51.5074,   -0.1278],
                            'Sydney'    => [-33.8688, 151.2093],
                            'Cape Town' => [-33.9249,  18.4241],
                            'Tokyo'     => [35.6762,  139.6503],
                            'Dubai'     => [25.2048,   55.2708],
                            'SÃƒÂ£o Paulo' => [-23.5505, -46.6333],
                        ] as $city => [$clat, $clng])
                            <flux:button
                                size="xs"
                                variant="ghost"
                                wire:click="$set('lat', {{ $clat }}); $set('lng', {{ $clng }})"
                                class="rounded-full border border-zinc-200 dark:border-zinc-700"
                            >
                                {{ $city }}
                            </flux:button>
                        @endforeach
                    </div>
                </div>
            </div>
        </flux:card>

        {{-- Preferences --}}
        <flux:card class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('My Preferences') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Saved to your profile and used across the app.') }}</flux:text>
            </div>
            <form wire:submit.prevent="savePreferences" class="space-y-4">

                <flux:field>
                    <flux:label>{{ __('Preferred currency') }}</flux:label>
                    <flux:select wire:model="preferredCurrency">
                        @foreach ($currencyOptions as $code => $label)
                            <option value="{{ $code }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Your city') }}</flux:label>
                    <flux:input wire:model="preferredCity" placeholder="e.g. Berlin" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Country code (ISO 2-letter)') }}</flux:label>
                    <flux:input wire:model="preferredCountry" placeholder="e.g. DE" maxlength="2" />
                </flux:field>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="savePreferences">{{ __('Save preferences') }}</span>
                    <span wire:loading wire:target="savePreferences">{{ __('SavingÃ¢â‚¬Â¦') }}</span>
                </flux:button>

                @if ($savedMessage)
                    <flux:callout variant="success" icon="check-circle">
                        <flux:callout.text>{{ $savedMessage }}</flux:callout.text>
                    </flux:callout>
                @endif
            </form>
        </flux:card>
    </div>

    {{-- Ã¢â€â‚¬Ã¢â€â‚¬ Nearby Depots Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
    @if ($lat != 0 || $lng != 0)
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <flux:heading size="lg">
                    @if ($nearbyDepots->isNotEmpty())
                        {{ $nearbyDepots->count() }} {{ __('depot(s) within') }} {{ number_format($radiusKm) }} km
                    @else
                        {{ __('No depots found') }}
                    @endif
                </flux:heading>
                @if ($nearbyDepots->isNotEmpty())
                    <flux:badge color="zinc">{{ number_format($radiusKm) }} km radius</flux:badge>
                @endif
            </div>

            @if ($nearbyDepots->isEmpty())
                <div class="flex flex-col items-center py-16 text-center">
                    <flux:icon.map-pin class="size-12 text-zinc-300 dark:text-zinc-600" />
                    <p class="mt-3 text-sm text-zinc-500">{{ __('No depots found in this area. Try increasing the search radius.') }}</p>
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($nearbyDepots as $depot)
                        <div class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm
                                    transition-all duration-200 hover:shadow-md hover:-translate-y-0.5
                                    dark:border-zinc-700/60 dark:bg-zinc-900">

                            {{-- Coloured header band --}}
                            <div class="flex items-center justify-between bg-zinc-50 px-5 py-3 dark:bg-zinc-800/50">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-zinc-900 dark:text-zinc-100">{{ $depot->name }}</p>
                                    <p class="text-xs text-zinc-400">{{ $depot->shortAddress() }}</p>
                                </div>
                                <flux:badge color="zinc" class="shrink-0 font-mono text-xs ml-2">
                                    {{ $depot->currency_code }}
                                </flux:badge>
                            </div>

                            <div class="flex flex-1 flex-col gap-3 p-5">
                                {{-- Distance --}}
                                <div class="flex items-center gap-2 text-sm text-zinc-500">
                                    <flux:icon.map-pin class="size-4 shrink-0 text-blue-500" />
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ number_format($depot->distance_km, 1) }} km</span>
                                    <span class="text-zinc-400">{{ __('away') }}</span>
                                </div>

                                {{-- Available tool count --}}
                                @php
                                    $toolCount = $depot->tools()
                                        ->where('status', \App\Enums\ToolStatus::Available)
                                        ->count();
                                @endphp
                                <div class="flex items-center gap-2 text-sm">
                                    <flux:icon.wrench-screwdriver class="size-4 shrink-0 text-emerald-500" />
                                    <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $toolCount }}</span>
                                    <span class="text-zinc-500">{{ __('tool(s) available') }}</span>
                                </div>

                                {{-- Contact --}}
                                @if ($depot->phone || $depot->email)
                                    <div class="space-y-0.5 text-xs text-zinc-400">
                                        @if ($depot->phone)<p>Ã°Å¸â€œÅ¾ {{ $depot->phone }}</p>@endif
                                        @if ($depot->email)<p>Ã¢Å“â€°Ã¯Â¸Â {{ $depot->email }}</p>@endif
                                    </div>
                                @endif

                                {{-- CTA --}}
                                <flux:button
                                    variant="primary"
                                    size="sm"
                                    class="mt-auto w-full"
                                    href="{{ route('tools.index', ['depot' => $depot->id]) }}"
                                    wire:navigate
                                >
                                    {{ __('Browse tools here') }} Ã¢â€ â€™
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>

</div>