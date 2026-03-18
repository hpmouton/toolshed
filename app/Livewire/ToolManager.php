<?php

namespace App\Livewire;

use App\Enums\ToolStatus;
use App\Models\Depot;
use App\Models\Tool;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * FR-8.2 — Admin tool management: create, edit, and archive tools.
 */
#[Layout('layouts.app')]
class ToolManager extends Component
{
    use WithPagination;

    // ── Filters ──────────────────────────────────────────────────────────
    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $conditionFilter = '';

    // ── Form state ───────────────────────────────────────────────────────
    public bool $showForm = false;
    public ?int $editingToolId = null;

    public string $sku = '';
    public string $serial_number = '';
    public string $name = '';
    public string $description = '';
    public string $image_url = '';
    public string $category = '';
    public int $daily_rate_cents = 0;
    public int $maintenance_fee_cents = 0;
    public string $currency_code = 'NAD';
    public string $condition = 'new';
    public ?string $last_serviced_date = null;
    public ?float $weight_kg = null;
    public string $dimensions = '';
    public ?int $depot_id = null;

    public string $formError = '';

    // ── CSV Import ───────────────────────────────────────────────────────
    public bool $showImport = false;
    public string $csvData = '';
    public string $importError = '';
    public string $importSuccess = '';

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

    public function edit(int $toolId): void
    {
        $tool = Tool::findOrFail($toolId);
        $this->editingToolId = $tool->id;
        $this->sku = $tool->sku;
        $this->serial_number = $tool->serial_number ?? '';
        $this->name = $tool->name;
        $this->description = $tool->description ?? '';
        $this->image_url = $tool->image_url ?? '';
        $this->category = $tool->category ?? '';
        $this->daily_rate_cents = $tool->daily_rate_cents;
        $this->maintenance_fee_cents = $tool->maintenance_fee_cents;
        $this->currency_code = $tool->currency_code ?? 'NAD';
        $this->condition = $tool->condition ?? 'new';
        $this->last_serviced_date = $tool->last_serviced_date?->format('Y-m-d');
        $this->weight_kg = $tool->weight_kg;
        $this->dimensions = $tool->dimensions ?? '';
        $this->depot_id = $tool->depot_id;
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'sku'                   => 'required|string|max:50|unique:tools,sku,' . $this->editingToolId,
            'serial_number'         => 'nullable|string|max:100',
            'name'                  => 'required|string|max:255',
            'description'           => 'nullable|string|max:5000',
            'image_url'             => 'nullable|url|max:2048',
            'category'              => 'required|string|max:50',
            'daily_rate_cents'      => 'required|integer|min:1',
            'maintenance_fee_cents' => 'required|integer|min:0',
            'currency_code'         => 'required|string|size:3',
            'condition'             => 'required|in:new,good,fair,poor',
            'last_serviced_date'    => 'nullable|date',
            'weight_kg'             => 'nullable|numeric|min:0',
            'dimensions'            => 'nullable|string|max:100',
            'depot_id'              => 'nullable|exists:depots,id',
        ]);

        $this->formError = '';

        if ($this->editingToolId) {
            $tool = Tool::findOrFail($this->editingToolId);
            $tool->update($validated);
            app(AuditLogger::class)->log('tool.updated', $tool);
        } else {
            $tool = Tool::create($validated);
            app(AuditLogger::class)->log('tool.created', $tool);
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function archive(int $toolId): void
    {
        $tool = Tool::findOrFail($toolId);
        $tool->archive();
        app(AuditLogger::class)->log('tool.archived', $tool);
    }

    // ── FR-8.5 — CSV bulk import (up to 500 rows) ───────────────────────

    public function openImport(): void
    {
        $this->showImport = true;
        $this->csvData = '';
        $this->importError = '';
        $this->importSuccess = '';
    }

    public function importCsv(): void
    {
        $this->importError = '';
        $this->importSuccess = '';

        if (blank($this->csvData)) {
            $this->importError = __('Please paste CSV data.');
            return;
        }

        $lines = array_filter(explode("\n", trim($this->csvData)));
        $header = str_getcsv(array_shift($lines));

        if (count($lines) > 500) {
            $this->importError = __('CSV import is limited to 500 rows.');
            return;
        }

        $required = ['sku', 'name', 'category', 'daily_rate_cents'];
        foreach ($required as $col) {
            if (! in_array($col, $header, true)) {
                $this->importError = __("Missing required column: :col", ['col' => $col]);
                return;
            }
        }

        $imported = 0;

        DB::transaction(function () use ($lines, $header, &$imported) {
            foreach ($lines as $line) {
                $row = array_combine($header, str_getcsv($line));
                if (! $row || blank($row['sku'] ?? null)) {
                    continue;
                }

                Tool::updateOrCreate(
                    ['sku' => $row['sku']],
                    collect($row)->only([
                        'name', 'description', 'category', 'daily_rate_cents',
                        'maintenance_fee_cents', 'currency_code', 'serial_number',
                        'condition', 'weight_kg', 'dimensions', 'depot_id', 'image_url',
                    ])->filter(fn ($v) => $v !== null && $v !== '')->toArray(),
                );
                $imported++;
            }
        });

        $this->importSuccess = __(':count tools imported successfully.', ['count' => $imported]);
        $this->csvData = '';
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->editingToolId = null;
        $this->sku = '';
        $this->serial_number = '';
        $this->name = '';
        $this->description = '';
        $this->image_url = '';
        $this->category = '';
        $this->daily_rate_cents = 0;
        $this->maintenance_fee_cents = 0;
        $this->currency_code = 'NAD';
        $this->condition = 'new';
        $this->last_serviced_date = null;
        $this->weight_kg = null;
        $this->dimensions = '';
        $this->depot_id = null;
        $this->formError = '';
    }

    public function render()
    {
        $tools = Tool::query()
            ->with('depot')
            ->when($this->search !== '', fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('sku',  'like', "%{$this->search}%")
                  ->orWhere('serial_number', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->conditionFilter !== '', fn ($q) => $q->where('condition', $this->conditionFilter))
            ->orderBy('name')
            ->paginate(20);

        $depots = Depot::where('is_active', true)->orderBy('name')->get();

        return view('livewire.tool-manager', [
            'tools'  => $tools,
            'depots' => $depots,
        ]);
    }
}
