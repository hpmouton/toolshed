{{-- FR-8.2 — Tool Management (Admin) --}}
<div class="min-h-screen bg-zinc-950">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-black text-white">{{ __('Tool Management') }}</h1>
                <p class="text-sm text-zinc-400">{{ __('Create, edit, and archive tools.') }}</p>
            </div>
            <div class="flex gap-2">
                <button wire:click="openImport" type="button"
                        class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-zinc-300 hover:bg-white/10 transition-colors">
                    {{ __('Import CSV') }}
                </button>
                <button wire:click="create" type="button"
                        class="rounded-xl bg-amber-400 px-4 py-2 text-sm font-bold text-zinc-900 hover:bg-amber-300 transition-colors">
                    {{ __('+ New Tool') }}
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <input type="search" wire:model.live.debounce.300ms="search"
                   placeholder="{{ __('Search by name, SKU, or serial…') }}"
                   class="w-full max-w-sm rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />

            <select wire:model.live="statusFilter"
                    class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                <option value="">{{ __('All statuses') }}</option>
                <option value="available">{{ __('Available') }}</option>
                <option value="reserved">{{ __('Reserved') }}</option>
                <option value="out">{{ __('Out') }}</option>
                <option value="archived">{{ __('Archived') }}</option>
            </select>

            <select wire:model.live="conditionFilter"
                    class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                <option value="">{{ __('All conditions') }}</option>
                <option value="new">{{ __('New') }}</option>
                <option value="good">{{ __('Good') }}</option>
                <option value="fair">{{ __('Fair') }}</option>
                <option value="poor">{{ __('Poor') }}</option>
            </select>
        </div>

        {{-- CSV Import Panel --}}
        @if ($showImport)
            <div class="mb-6 rounded-2xl border border-white/10 bg-zinc-900 p-6">
                <h2 class="text-lg font-bold text-white mb-3">{{ __('CSV Bulk Import') }}</h2>
                <p class="text-sm text-zinc-400 mb-3">{{ __('Paste CSV data below. Required columns: sku, name, category, daily_rate_cents. Max 500 rows.') }}</p>
                <textarea wire:model="csvData" rows="8"
                          class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-zinc-100 placeholder-zinc-500 font-mono focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                          placeholder="sku,name,category,daily_rate_cents,maintenance_fee_cents&#10;TOOL-001,Hammer Drill,Power,5000,500"></textarea>
                @if ($importError)
                    <p class="mt-2 text-sm text-red-400" role="alert">{{ $importError }}</p>
                @endif
                @if ($importSuccess)
                    <p class="mt-2 text-sm text-green-400">{{ $importSuccess }}</p>
                @endif
                <div class="mt-3 flex gap-2">
                    <button wire:click="importCsv" type="button"
                            class="rounded-xl bg-amber-400 px-4 py-2 text-sm font-bold text-zinc-900 hover:bg-amber-300 transition-colors">
                        {{ __('Import') }}
                    </button>
                    <button wire:click="$set('showImport', false)" type="button"
                            class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-zinc-300 hover:bg-white/10 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        @endif

        {{-- Create/Edit Form --}}
        @if ($showForm)
            <div class="mb-6 rounded-2xl border border-white/10 bg-zinc-900 p-6">
                <h2 class="text-lg font-bold text-white mb-4">
                    {{ $editingToolId ? __('Edit Tool') : __('New Tool') }}
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- SKU --}}
                    <div>
                        <label for="sku" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('SKU') }} *</label>
                        <input id="sku" type="text" wire:model="sku"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                               aria-invalid="{{ $errors->has('sku') ? 'true' : 'false' }}" />
                        @error('sku') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Serial Number --}}
                    <div>
                        <label for="serial_number" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Serial Number') }}</label>
                        <input id="serial_number" type="text" wire:model="serial_number"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Name') }} *</label>
                        <input id="name" type="text" wire:model="name"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                               aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" />
                        @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Category') }} *</label>
                        <select id="category" wire:model="category"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                            <option value="">{{ __('Select…') }}</option>
                            @foreach (['Power','Demo','Access','Concrete','Plumbing','Electrical','Landscape','Painting','Measuring'] as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('category') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Daily Rate (cents) --}}
                    <div>
                        <label for="daily_rate_cents" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Daily Rate (cents)') }} *</label>
                        <input id="daily_rate_cents" type="number" min="1" wire:model="daily_rate_cents"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                               aria-invalid="{{ $errors->has('daily_rate_cents') ? 'true' : 'false' }}" />
                        @error('daily_rate_cents') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Maintenance Fee (cents) --}}
                    <div>
                        <label for="maintenance_fee_cents" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Maintenance Fee (cents)') }}</label>
                        <input id="maintenance_fee_cents" type="number" min="0" wire:model="maintenance_fee_cents"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    {{-- Currency --}}
                    <div>
                        <label for="currency_code" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Currency') }}</label>
                        <input id="currency_code" type="text" wire:model="currency_code" maxlength="3"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    {{-- Condition --}}
                    <div>
                        <label for="condition" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Condition') }}</label>
                        <select id="condition" wire:model="condition"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                            <option value="new">{{ __('New') }}</option>
                            <option value="good">{{ __('Good') }}</option>
                            <option value="fair">{{ __('Fair') }}</option>
                            <option value="poor">{{ __('Poor') }}</option>
                        </select>
                    </div>

                    {{-- Last Serviced Date --}}
                    <div>
                        <label for="last_serviced_date" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Last Serviced') }}</label>
                        <input id="last_serviced_date" type="date" wire:model="last_serviced_date"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    {{-- Weight --}}
                    <div>
                        <label for="weight_kg" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Weight (kg)') }}</label>
                        <input id="weight_kg" type="number" step="0.01" min="0" wire:model="weight_kg"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    {{-- Dimensions --}}
                    <div>
                        <label for="dimensions" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Dimensions') }}</label>
                        <input id="dimensions" type="text" wire:model="dimensions" placeholder="30x20x15 cm"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    {{-- Depot --}}
                    <div>
                        <label for="depot_id" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Depot') }}</label>
                        <select id="depot_id" wire:model="depot_id"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                            <option value="">{{ __('None') }}</option>
                            @foreach ($depots as $depot)
                                <option value="{{ $depot->id }}">{{ $depot->name }} — {{ $depot->city }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Image URL --}}
                    <div class="sm:col-span-2">
                        <label for="image_url" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Image URL') }}</label>
                        <input id="image_url" type="url" wire:model="image_url"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>

                    {{-- Description --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label for="description" class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Description') }}</label>
                        <textarea id="description" rows="3" wire:model="description"
                                  class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50"></textarea>
                    </div>
                </div>

                @if ($formError)
                    <p class="mt-3 text-sm text-red-400" role="alert">{{ $formError }}</p>
                @endif

                <div class="mt-4 flex gap-2">
                    <button wire:click="save" type="button"
                            class="rounded-xl bg-amber-400 px-4 py-2 text-sm font-bold text-zinc-900 hover:bg-amber-300 transition-colors">
                        {{ $editingToolId ? __('Update') : __('Create') }}
                    </button>
                    <button wire:click="$set('showForm', false)" type="button"
                            class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-zinc-300 hover:bg-white/10 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        @endif

        {{-- Tool Table --}}
        <div class="overflow-x-auto rounded-2xl border border-white/10">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-900 text-xs uppercase text-zinc-400 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3">{{ __('SKU') }}</th>
                        <th class="px-4 py-3">{{ __('Name') }}</th>
                        <th class="px-4 py-3">{{ __('Category') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3">{{ __('Condition') }}</th>
                        <th class="px-4 py-3">{{ __('Rate') }}</th>
                        <th class="px-4 py-3">{{ __('Depot') }}</th>
                        <th class="px-4 py-3">{{ __('Service') }}</th>
                        <th class="px-4 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($tools as $tool)
                        <tr class="hover:bg-white/5 transition-colors {{ $tool->needsService() ? 'bg-red-950/20' : '' }}">
                            <td class="px-4 py-3 font-mono text-zinc-300">{{ $tool->sku }}</td>
                            <td class="px-4 py-3 font-medium text-white">{{ $tool->name }}</td>
                            <td class="px-4 py-3 text-zinc-400">{{ $tool->category }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                    {{ match($tool->status->value) {
                                        'available' => 'bg-green-500/20 text-green-400',
                                        'reserved'  => 'bg-amber-500/20 text-amber-400',
                                        'out'       => 'bg-blue-500/20 text-blue-400',
                                        'archived'  => 'bg-zinc-500/20 text-zinc-400',
                                        default     => 'bg-zinc-500/20 text-zinc-400',
                                    } }}">
                                    {{ __(ucfirst($tool->status->value)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-400 capitalize">{{ $tool->condition ?? '—' }}</td>
                            <td class="px-4 py-3 text-zinc-300">{{ number_format($tool->daily_rate_cents / 100, 2) }}</td>
                            <td class="px-4 py-3 text-zinc-400">{{ $tool->depot?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($tool->needsService())
                                    <span class="text-xs font-semibold text-red-400" title="{{ __('Last serviced more than 90 days ago') }}">⚠ {{ __('Overdue') }}</span>
                                @else
                                    <span class="text-xs text-zinc-500">{{ $tool->last_serviced_date?->format('M j') ?? '—' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <button wire:click="edit({{ $tool->id }})" type="button"
                                            class="rounded-lg bg-white/5 px-2.5 py-1 text-xs font-medium text-zinc-300 hover:bg-white/10 transition-colors">
                                        {{ __('Edit') }}
                                    </button>
                                    @if (! $tool->isArchived())
                                        <button wire:click="archive({{ $tool->id }})"
                                                wire:confirm="{{ __('Are you sure you want to archive this tool?') }}"
                                                type="button"
                                                class="rounded-lg bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-400 hover:bg-red-500/20 transition-colors">
                                            {{ __('Archive') }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-zinc-500">{{ __('No tools found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tools->links() }}
        </div>
    </div>
</div>
