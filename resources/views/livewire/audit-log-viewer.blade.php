{{-- FR-7.3 — Audit Log Viewer (Staff + Admin) --}}
<div class="min-h-screen bg-zinc-950">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-black text-white">{{ __('Audit Log') }}</h1>
            <p class="text-sm text-zinc-400">{{ __('Paginated, filterable audit trail for all state transitions.') }}</p>
        </div>

        {{-- Filters --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:flex-wrap">
            <select wire:model.live="action"
                    class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                <option value="">{{ __('All actions') }}</option>
                @foreach ($actionOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                @endforeach
            </select>

            <select wire:model.live="subjectType"
                    class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                <option value="">{{ __('All subject types') }}</option>
                @foreach ($subjectTypeOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                @endforeach
            </select>

            <input type="number" wire:model.live.debounce.300ms="userId" placeholder="{{ __('User ID') }}"
                   class="w-28 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />

            <div class="flex items-center gap-1.5">
                <label class="text-xs text-zinc-500">{{ __('From') }}</label>
                <input type="date" wire:model.live="dateFrom"
                       class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
            </div>

            <div class="flex items-center gap-1.5">
                <label class="text-xs text-zinc-500">{{ __('To') }}</label>
                <input type="date" wire:model.live="dateTo"
                       class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
            </div>

            <button wire:click="clearFilters" type="button"
                    class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-medium text-zinc-400 hover:bg-white/10 transition-colors">
                {{ __('Clear filters') }}
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-2xl border border-white/10">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-900 text-xs uppercase text-zinc-400 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3">{{ __('Timestamp') }}</th>
                        <th class="px-4 py-3">{{ __('Action') }}</th>
                        <th class="px-4 py-3">{{ __('User') }}</th>
                        <th class="px-4 py-3">{{ __('Subject') }}</th>
                        <th class="px-4 py-3">{{ __('IP Address') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-zinc-400 whitespace-nowrap">
                                {{ $log->timestamp->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-amber-500/10 px-2.5 py-0.5 text-xs font-semibold text-amber-400">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-300">
                                {{ $log->user?->name ?? __('System') }}
                                <span class="text-xs text-zinc-500">#{{ $log->user_id }}</span>
                            </td>
                            <td class="px-4 py-3 text-zinc-400">
                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-zinc-500">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-zinc-500">{{ __('No audit log entries found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
