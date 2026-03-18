{{-- FR-3.10 — Staff Booking Management --}}
<div class="min-h-screen bg-zinc-950">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-2xl font-black text-white">{{ __('Booking Management') }}</h1>
            <p class="text-sm text-zinc-400">{{ __('Advance booking states, cancel bookings, and manage the rental lifecycle.') }}</p>
        </div>

        @if ($actionError)
            <div class="mb-4 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400" role="alert">
                {{ $actionError }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <input type="search" wire:model.live.debounce.300ms="search"
                   placeholder="{{ __('Search tool, SKU, or renter…') }}"
                   class="w-full max-w-sm rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />

            <select wire:model.live="statusFilter"
                    class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                <option value="">{{ __('All statuses') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="confirmed">{{ __('Confirmed') }}</option>
                <option value="active">{{ __('Active') }}</option>
                <option value="returned">{{ __('Returned') }}</option>
                <option value="cancelled">{{ __('Cancelled') }}</option>
            </select>
        </div>

        {{-- Cancel dialog --}}
        @if ($cancellingBookingId)
            <div class="mb-4 rounded-xl border border-amber-500/20 bg-amber-500/10 p-4">
                <p class="text-sm font-medium text-amber-300 mb-2">{{ __('Cancel booking #:id — please provide a reason:', ['id' => $cancellingBookingId]) }}</p>
                <textarea wire:model="cancelReason" rows="2"
                          class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
                          placeholder="{{ __('Reason for cancellation…') }}"
                          aria-invalid="{{ $errors->has('cancelReason') ? 'true' : 'false' }}"></textarea>
                @error('cancelReason') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                <div class="mt-2 flex gap-2">
                    <button wire:click="confirmCancel" type="button"
                            class="rounded-xl bg-red-500 px-4 py-2 text-sm font-bold text-white hover:bg-red-400 transition-colors">
                        {{ __('Confirm Cancel') }}
                    </button>
                    <button wire:click="cancelCancel" type="button"
                            class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-zinc-300 hover:bg-white/10 transition-colors">
                        {{ __('Dismiss') }}
                    </button>
                </div>
            </div>
        @endif

        {{-- Bookings Table --}}
        <div class="overflow-x-auto rounded-2xl border border-white/10">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-900 text-xs uppercase text-zinc-400 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">{{ __('Tool') }}</th>
                        <th class="px-4 py-3">{{ __('Renter') }}</th>
                        <th class="px-4 py-3">{{ __('Dates') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($bookings as $booking)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-zinc-500">{{ $booking->id }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-white">{{ $booking->tool->name }}</div>
                                <div class="text-xs text-zinc-500 font-mono">{{ $booking->tool->sku }}</div>
                            </td>
                            <td class="px-4 py-3 text-zinc-300">{{ $booking->user->name }}</td>
                            <td class="px-4 py-3 text-zinc-400 whitespace-nowrap">
                                {{ $booking->start_date->format('M j') }} → {{ $booking->end_date->format('M j, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                    {{ match($booking->booking_status) {
                                        'pending'   => 'bg-zinc-500/20 text-zinc-400',
                                        'confirmed' => 'bg-amber-500/20 text-amber-400',
                                        'active'    => 'bg-green-500/20 text-green-400',
                                        'returned'  => 'bg-blue-500/20 text-blue-400',
                                        'cancelled' => 'bg-red-500/20 text-red-400',
                                        default     => 'bg-zinc-500/20 text-zinc-400',
                                    } }}">
                                    {{ __(ucfirst($booking->booking_status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1 flex-wrap">
                                    @if ($booking->booking_status === 'pending')
                                        <button wire:click="confirm({{ $booking->id }})" type="button"
                                                class="rounded-lg bg-amber-400/10 px-2.5 py-1 text-xs font-medium text-amber-400 hover:bg-amber-400/20 transition-colors">
                                            {{ __('Confirm') }}
                                        </button>
                                    @endif

                                    @if ($booking->booking_status === 'confirmed')
                                        <button wire:click="dispatchBooking({{ $booking->id }})" type="button"
                                                class="rounded-lg bg-green-500/10 px-2.5 py-1 text-xs font-medium text-green-400 hover:bg-green-500/20 transition-colors">
                                            {{ __('Dispatch') }}
                                        </button>
                                    @endif

                                    @if ($booking->booking_status === 'active')
                                        <button wire:click="returnBooking({{ $booking->id }})" type="button"
                                                class="rounded-lg bg-blue-500/10 px-2.5 py-1 text-xs font-medium text-blue-400 hover:bg-blue-500/20 transition-colors">
                                            {{ __('Return') }}
                                        </button>
                                    @endif

                                    @if (in_array($booking->booking_status, ['pending', 'confirmed']))
                                        <button wire:click="startCancel({{ $booking->id }})" type="button"
                                                class="rounded-lg bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-400 hover:bg-red-500/20 transition-colors">
                                            {{ __('Cancel') }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-zinc-500">{{ __('No bookings found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
