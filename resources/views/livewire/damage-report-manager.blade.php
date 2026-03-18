{{-- FR-13.2 — Staff Damage Report Management --}}
<div class="min-h-screen bg-zinc-950">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-2xl font-black text-white">{{ __('Damage Reports') }}</h1>
            <p class="text-sm text-zinc-400">{{ __('Review, accept, reject, or escalate damage reports.') }}</p>
        </div>

        <div class="mb-6">
            <select wire:model.live="statusFilter"
                    class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                <option value="">{{ __('All statuses') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="accepted">{{ __('Accepted') }}</option>
                <option value="rejected">{{ __('Rejected') }}</option>
                <option value="escalated">{{ __('Escalated') }}</option>
            </select>
        </div>

        {{-- Charge form --}}
        @if ($chargingReportId)
            <div class="mb-4 rounded-xl border border-amber-500/20 bg-amber-500/10 p-4">
                <p class="text-sm font-medium text-amber-300 mb-3">{{ __('Accept damage report #:id — specify charge:', ['id' => $chargingReportId]) }}</p>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Amount (cents)') }}</label>
                        <input type="number" min="0" wire:model="chargeAmount"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Currency') }}</label>
                        <input type="text" maxlength="3" wire:model="chargeCurrency"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1">{{ __('Description') }}</label>
                        <input type="text" wire:model="chargeDescription"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />
                    </div>
                </div>
                <div class="mt-3 flex gap-2">
                    <button wire:click="confirmAccept" type="button"
                            class="rounded-xl bg-green-500 px-4 py-2 text-sm font-bold text-white hover:bg-green-400 transition-colors">
                        {{ __('Accept & Charge') }}
                    </button>
                    <button wire:click="cancelCharge" type="button"
                            class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-zinc-300 hover:bg-white/10 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        @endif

        {{-- Reports Table --}}
        <div class="overflow-x-auto rounded-2xl border border-white/10">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-900 text-xs uppercase text-zinc-400 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">{{ __('Tool') }}</th>
                        <th class="px-4 py-3">{{ __('Renter') }}</th>
                        <th class="px-4 py-3">{{ __('Condition') }}</th>
                        <th class="px-4 py-3">{{ __('Description') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3">{{ __('Charge') }}</th>
                        <th class="px-4 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-zinc-500">{{ $report->id }}</td>
                            <td class="px-4 py-3 text-white">{{ $report->booking->tool->name }}</td>
                            <td class="px-4 py-3 text-zinc-300">{{ $report->user->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                    {{ match($report->condition_declared) {
                                        'undamaged'    => 'bg-green-500/20 text-green-400',
                                        'minor_damage' => 'bg-amber-500/20 text-amber-400',
                                        'major_damage' => 'bg-red-500/20 text-red-400',
                                        default        => 'bg-zinc-500/20 text-zinc-400',
                                    } }}">
                                    {{ __(str_replace('_', ' ', ucfirst($report->condition_declared))) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-400 max-w-xs truncate">{{ $report->description ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                    {{ match($report->status) {
                                        'pending'   => 'bg-zinc-500/20 text-zinc-400',
                                        'accepted'  => 'bg-green-500/20 text-green-400',
                                        'rejected'  => 'bg-red-500/20 text-red-400',
                                        'escalated' => 'bg-purple-500/20 text-purple-400',
                                        default     => 'bg-zinc-500/20 text-zinc-400',
                                    } }}">
                                    {{ __(ucfirst($report->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-300">
                                @if ($report->charge)
                                    {{ number_format($report->charge->amount_cents / 100, 2) }} {{ $report->charge->currency_code }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($report->status === 'pending')
                                    <div class="flex gap-1">
                                        <button wire:click="startAccept({{ $report->id }})" type="button"
                                                class="rounded-lg bg-green-500/10 px-2.5 py-1 text-xs font-medium text-green-400 hover:bg-green-500/20 transition-colors">
                                            {{ __('Accept') }}
                                        </button>
                                        <button wire:click="reject({{ $report->id }})" type="button"
                                                class="rounded-lg bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-400 hover:bg-red-500/20 transition-colors">
                                            {{ __('Reject') }}
                                        </button>
                                        <button wire:click="escalate({{ $report->id }})" type="button"
                                                class="rounded-lg bg-purple-500/10 px-2.5 py-1 text-xs font-medium text-purple-400 hover:bg-purple-500/20 transition-colors">
                                            {{ __('Escalate') }}
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-zinc-500">{{ __('No damage reports found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>
</div>
