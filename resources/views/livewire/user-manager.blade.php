{{-- FR-0.5 — User / Role Management (Admin) --}}
<div class="min-h-screen bg-zinc-950">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-2xl font-black text-white">{{ __('User Management') }}</h1>
            <p class="text-sm text-zinc-400">{{ __('Manage user roles and depot assignments.') }}</p>
        </div>

        {{-- Filters --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <input type="search" wire:model.live.debounce.300ms="search"
                   placeholder="{{ __('Search by name or email…') }}"
                   class="w-full max-w-sm rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-400/50" />

            <select wire:model.live="roleFilter"
                    class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                <option value="">{{ __('All roles') }}</option>
                <option value="renter">{{ __('Renter') }}</option>
                <option value="staff">{{ __('Staff') }}</option>
                <option value="admin">{{ __('Admin') }}</option>
            </select>
        </div>

        {{-- User Table --}}
        <div class="overflow-x-auto rounded-2xl border border-white/10">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-900 text-xs uppercase text-zinc-400 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3">{{ __('Name') }}</th>
                        <th class="px-4 py-3">{{ __('Email') }}</th>
                        <th class="px-4 py-3">{{ __('Role') }}</th>
                        <th class="px-4 py-3">{{ __('Depot') }}</th>
                        <th class="px-4 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($users as $user)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 font-medium text-white">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-zinc-400">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @if ($editingUserId === $user->id)
                                    <select wire:model="newRole"
                                            class="rounded-lg border border-white/10 bg-white/5 px-2 py-1 text-xs text-zinc-300 focus:outline-none focus:ring-1 focus:ring-amber-400/50">
                                        <option value="renter">{{ __('Renter') }}</option>
                                        <option value="staff">{{ __('Staff') }}</option>
                                        <option value="admin">{{ __('Admin') }}</option>
                                    </select>
                                    @error('newRole') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                                @else
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                        {{ match($user->role) {
                                            'admin' => 'bg-purple-500/20 text-purple-400',
                                            'staff' => 'bg-blue-500/20 text-blue-400',
                                            default => 'bg-zinc-500/20 text-zinc-400',
                                        } }}">
                                        {{ __(ucfirst($user->role)) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-zinc-400">
                                @if ($editingUserId === $user->id && $newRole === 'staff')
                                    <select wire:model="newDepotId"
                                            class="rounded-lg border border-white/10 bg-white/5 px-2 py-1 text-xs text-zinc-300 focus:outline-none focus:ring-1 focus:ring-amber-400/50">
                                        <option value="">{{ __('None') }}</option>
                                        @foreach ($depots as $depot)
                                            <option value="{{ $depot->id }}">{{ $depot->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    {{ $user->depot?->name ?? '—' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($editingUserId === $user->id)
                                    <div class="flex gap-1">
                                        <button wire:click="saveRole" type="button"
                                                class="rounded-lg bg-amber-400 px-2.5 py-1 text-xs font-bold text-zinc-900 hover:bg-amber-300 transition-colors">
                                            {{ __('Save') }}
                                        </button>
                                        <button wire:click="cancelEdit" type="button"
                                                class="rounded-lg bg-white/5 px-2.5 py-1 text-xs font-medium text-zinc-300 hover:bg-white/10 transition-colors">
                                            {{ __('Cancel') }}
                                        </button>
                                    </div>
                                @else
                                    <button wire:click="editRole({{ $user->id }})" type="button"
                                            class="rounded-lg bg-white/5 px-2.5 py-1 text-xs font-medium text-zinc-300 hover:bg-white/10 transition-colors">
                                        {{ __('Change Role') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-zinc-500">{{ __('No users found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
