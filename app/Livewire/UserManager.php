<?php

namespace App\Livewire;

use App\Models\Depot;
use App\Models\User;
use App\Services\AuditLogger;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * FR-0.5 — Admin user/role management console.
 * FR-0.6 — Every role change is audit-logged.
 */
#[Layout('layouts.app')]
class UserManager extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $roleFilter = '';

    // ── Edit role form ───────────────────────────────────────────────────
    public ?int $editingUserId = null;
    public string $newRole = '';
    public ?int $newDepotId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * FR-0.5 — Open the role-edit form for a user.
     */
    public function editRole(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->newRole = $user->role;
        $this->newDepotId = $user->depot_id;
    }

    /**
     * FR-0.5/0.6 — Save the role change and audit it.
     */
    public function saveRole(): void
    {
        $this->validate([
            'newRole'    => 'required|in:renter,staff,admin',
            'newDepotId' => 'nullable|exists:depots,id',
        ]);

        $user = User::findOrFail($this->editingUserId);
        $previousRole = $user->role;
        $previousDepotId = $user->depot_id;

        // FR-0.5 — A user shall not modify their own role.
        if ($user->id === auth()->id()) {
            $this->addError('newRole', __('You cannot change your own role.'));
            return;
        }

        $user->update([
            'role'     => $this->newRole,
            'depot_id' => $this->newRole === 'staff' ? $this->newDepotId : null,
        ]);

        // FR-0.6 — Audit the role change with previous and new values.
        app(AuditLogger::class)->log('user.role_changed', $user);

        $this->editingUserId = null;
        $this->newRole = '';
        $this->newDepotId = null;
    }

    public function cancelEdit(): void
    {
        $this->editingUserId = null;
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search !== '', fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter !== '', fn ($q) => $q->where('role', $this->roleFilter))
            ->orderBy('name')
            ->paginate(20);

        $depots = Depot::where('is_active', true)->orderBy('name')->get();

        return view('livewire.user-manager', [
            'users'  => $users,
            'depots' => $depots,
        ]);
    }
}
