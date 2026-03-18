{{-- FR-9.1 — Depot Management (Admin) --}}
<div class="min-h-screen bg-zinc-950">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-black text-white">{{ __('Depot Management') }}</h1>
                <p class="text-sm text-zinc-400">{{ __('Create, edit, deactivate, and reactivate depots.') }}</p>
            </div>
            <button wire:click="create" type="button"
                    class="rounded-xl bg-amber-400 px-4 py-2 text-sm font-bold text-zinc-900 hover:bg-amber-300 transition-colors">
                {{ __('+ New Depot') }}
            </button>
        </div>

        {{-- Search --}}
        <div class="mb-6">
            <input type="search" wire:model.live.debounce.300ms="search"
                   placeholder="{{ __('Search by name or city…') }}"
                   class="w-full max-w-sm rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
        </div>

        {{-- Create/Edit Form --}}
        @if ($showForm)
            <div class="mb-6 rounded-2xl border border-white/10 bg-zinc-900 p-6">
                <h2 class="text-lg font-bold text-white mb-4">
                    {{ $editingDepotId ? __('Edit Depot') : __('New Depot') }}
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="name" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Name') }} *</label>
                        <input id="name" type="text" wire:model="name"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                               aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" />
                        @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="address_line1" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Address Line 1') }} *</label>
                        <input id="address_line1" type="text" wire:model="address_line1"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                               aria-invalid="{{ $errors->has('address_line1') ? 'true' : 'false' }}" />
                        @error('address_line1') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="address_line2" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Address Line 2') }}</label>
                        <input id="address_line2" type="text" wire:model="address_line2"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    <div>
                        <label for="city" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('City') }} *</label>
                        <input id="city" type="text" wire:model="city"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                               aria-invalid="{{ $errors->has('city') ? 'true' : 'false' }}" />
                        @error('city') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="state_province" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('State / Province') }}</label>
                        <input id="state_province" type="text" wire:model="state_province"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    <div>
                        <label for="postal_code" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Postal Code') }}</label>
                        <input id="postal_code" type="text" wire:model="postal_code"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    <div>
                        <label for="country_code" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Country Code') }} *</label>
                        <input id="country_code" type="text" wire:model="country_code" maxlength="2" placeholder="NA"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                               aria-invalid="{{ $errors->has('country_code') ? 'true' : 'false' }}" />
                        @error('country_code') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="country_name" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Country Name') }} *</label>
                        <input id="country_name" type="text" wire:model="country_name" placeholder="Namibia"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                               aria-invalid="{{ $errors->has('country_name') ? 'true' : 'false' }}" />
                        @error('country_name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="currency_code" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Currency') }} *</label>
                        <input id="currency_code" type="text" wire:model="currency_code" maxlength="3" placeholder="NAD"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    <div>
                        <label for="tax_rate" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Tax Rate') }} *</label>
                        <input id="tax_rate" type="number" step="0.0001" min="0" max="1" wire:model="tax_rate"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                               aria-invalid="{{ $errors->has('tax_rate') ? 'true' : 'false' }}" />
                        @error('tax_rate') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="latitude" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Latitude') }} *</label>
                        <input id="latitude" type="number" step="0.0000001" wire:model="latitude"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                               aria-invalid="{{ $errors->has('latitude') ? 'true' : 'false' }}" />
                        @error('latitude') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Longitude') }} *</label>
                        <input id="longitude" type="number" step="0.0000001" wire:model="longitude"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                               aria-invalid="{{ $errors->has('longitude') ? 'true' : 'false' }}" />
                        @error('longitude') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Phone') }}</label>
                        <input id="phone" type="text" wire:model="phone"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Email') }}</label>
                        <input id="email" type="email" wire:model="email"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>
                </div>

                <div class="mt-4 flex gap-2">
                    <button wire:click="save" type="button"
                            class="rounded-xl bg-amber-400 px-4 py-2 text-sm font-bold text-zinc-900 hover:bg-amber-300 transition-colors">
                        {{ $editingDepotId ? __('Update') : __('Create') }}
                    </button>
                    <button wire:click="$set('showForm', false)" type="button"
                            class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-zinc-300 hover:bg-white/10 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        @endif

        {{-- Depot Table --}}
        <div class="overflow-x-auto rounded-2xl border border-white/10">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-900 text-xs uppercase text-zinc-400 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3">{{ __('Name') }}</th>
                        <th class="px-4 py-3">{{ __('City') }}</th>
                        <th class="px-4 py-3">{{ __('Country') }}</th>
                        <th class="px-4 py-3">{{ __('Currency') }}</th>
                        <th class="px-4 py-3">{{ __('Tax Rate') }}</th>
                        <th class="px-4 py-3">{{ __('Tools') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($depots as $depot)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 font-medium text-white">{{ $depot->name }}</td>
                            <td class="px-4 py-3 text-zinc-400">{{ $depot->city }}</td>
                            <td class="px-4 py-3 text-zinc-400">{{ $depot->country_code }}</td>
                            <td class="px-4 py-3 text-zinc-300">{{ $depot->currency_code }}</td>
                            <td class="px-4 py-3 text-zinc-300">{{ number_format($depot->tax_rate * 100, 1) }}%</td>
                            <td class="px-4 py-3 text-zinc-300">{{ $depot->tools_count }}</td>
                            <td class="px-4 py-3">
                                @if ($depot->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-500/20 px-2 py-0.5 text-xs font-semibold text-green-400">{{ __('Active') }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-zinc-500/20 px-2 py-0.5 text-xs font-semibold text-zinc-400">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <button wire:click="edit({{ $depot->id }})" type="button"
                                            class="rounded-lg bg-white/5 px-2.5 py-1 text-xs font-medium text-zinc-300 hover:bg-white/10 transition-colors">
                                        {{ __('Edit') }}
                                    </button>
                                    @if ($depot->is_active)
                                        <button wire:click="deactivate({{ $depot->id }})"
                                                wire:confirm="{{ __('Deactivating this depot will hide all its tools from the catalogue. Continue?') }}"
                                                type="button"
                                                class="rounded-lg bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-400 hover:bg-red-500/20 transition-colors">
                                            {{ __('Deactivate') }}
                                        </button>
                                    @else
                                        <button wire:click="reactivate({{ $depot->id }})" type="button"
                                                class="rounded-lg bg-green-500/10 px-2.5 py-1 text-xs font-medium text-green-400 hover:bg-green-500/20 transition-colors">
                                            {{ __('Reactivate') }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-zinc-500">{{ __('No depots found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $depots->links() }}
        </div>
    </div>
</div>
