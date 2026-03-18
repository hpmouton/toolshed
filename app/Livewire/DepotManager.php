<?php

namespace App\Livewire;

use App\Models\Depot;
use App\Services\AuditLogger;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * FR-9.1 — Admin depot management: create, edit, deactivate, reactivate depots.
 */
#[Layout('layouts.app')]
class DepotManager extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    // ── Form state ───────────────────────────────────────────────────────
    public bool $showForm = false;
    public ?int $editingDepotId = null;

    public string $name = '';
    public string $address_line1 = '';
    public string $address_line2 = '';
    public string $city = '';
    public string $state_province = '';
    public string $postal_code = '';
    public string $country_code = '';
    public string $country_name = '';
    public string $currency_code = 'NAD';
    public float $tax_rate = 0.15;
    public ?float $latitude = null;
    public ?float $longitude = null;
    public string $phone = '';
    public string $email = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // ── CRUD ─────────────────────────────────────────────────────────────

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $depotId): void
    {
        $depot = Depot::findOrFail($depotId);
        $this->editingDepotId = $depot->id;
        $this->name = $depot->name;
        $this->address_line1 = $depot->address_line1;
        $this->address_line2 = $depot->address_line2 ?? '';
        $this->city = $depot->city;
        $this->state_province = $depot->state_province ?? '';
        $this->postal_code = $depot->postal_code ?? '';
        $this->country_code = $depot->country_code;
        $this->country_name = $depot->country_name;
        $this->currency_code = $depot->currency_code;
        $this->tax_rate = $depot->tax_rate;
        $this->latitude = $depot->latitude;
        $this->longitude = $depot->longitude;
        $this->phone = $depot->phone ?? '';
        $this->email = $depot->email ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name'            => 'required|string|max:255',
            'address_line1'   => 'required|string|max:255',
            'address_line2'   => 'nullable|string|max:255',
            'city'            => 'required|string|max:100',
            'state_province'  => 'nullable|string|max:100',
            'postal_code'     => 'nullable|string|max:20',
            'country_code'    => 'required|string|size:2',
            'country_name'    => 'required|string|max:100',
            'currency_code'   => 'required|string|size:3',
            'tax_rate'        => 'required|numeric|min:0|max:1',
            'latitude'        => 'required|numeric|between:-90,90',
            'longitude'       => 'required|numeric|between:-180,180',
            'phone'           => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:255',
        ]);

        if ($this->editingDepotId) {
            $depot = Depot::findOrFail($this->editingDepotId);
            $depot->update($validated);
            app(AuditLogger::class)->log('depot.updated', $depot);
        } else {
            $depot = Depot::create($validated);
            app(AuditLogger::class)->log('depot.created', $depot);
        }

        $this->showForm = false;
        $this->resetForm();
    }

    /**
     * FR-9.2 — Deactivating a depot hides all of its tools from the catalogue
     * and prevents new bookings against those tools.
     */
    public function deactivate(int $depotId): void
    {
        $depot = Depot::findOrFail($depotId);
        $depot->update(['is_active' => false]);
        app(AuditLogger::class)->log('depot.deactivated', $depot);
    }

    public function reactivate(int $depotId): void
    {
        $depot = Depot::findOrFail($depotId);
        $depot->update(['is_active' => true]);
        app(AuditLogger::class)->log('depot.reactivated', $depot);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->editingDepotId = null;
        $this->name = '';
        $this->address_line1 = '';
        $this->address_line2 = '';
        $this->city = '';
        $this->state_province = '';
        $this->postal_code = '';
        $this->country_code = '';
        $this->country_name = '';
        $this->currency_code = 'NAD';
        $this->tax_rate = 0.15;
        $this->latitude = null;
        $this->longitude = null;
        $this->phone = '';
        $this->email = '';
    }

    public function render()
    {
        $depots = Depot::query()
            ->withCount('tools')
            ->when($this->search !== '', fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%");
            }))
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.depot-manager', [
            'depots' => $depots,
        ]);
    }
}
