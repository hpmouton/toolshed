<?php

namespace App\Livewire;

use App\Models\AuditLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * FR-7.3 — Staff and admin users shall be able to view a paginated,
 * filterable audit log.
 */
#[Layout('layouts.app')]
class AuditLogViewer extends Component
{
    use WithPagination;

    #[Url]
    public string $action = '';

    #[Url]
    public string $subjectType = '';

    #[Url]
    public ?int $userId = null;

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function updatedAction(): void
    {
        $this->resetPage();
    }

    public function updatedSubjectType(): void
    {
        $this->resetPage();
    }

    public function updatedUserId(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->action = '';
        $this->subjectType = '';
        $this->userId = null;
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function render()
    {
        $logs = AuditLog::query()
            ->with('user')
            ->when($this->action !== '', fn ($q) => $q->where('action', $this->action))
            ->when($this->subjectType !== '', fn ($q) => $q->where('subject_type', $this->subjectType))
            ->when($this->userId, fn ($q) => $q->where('user_id', $this->userId))
            ->when($this->dateFrom !== '', fn ($q) => $q->where('timestamp', '>=', $this->dateFrom . ' 00:00:00'))
            ->when($this->dateTo !== '', fn ($q) => $q->where('timestamp', '<=', $this->dateTo . ' 23:59:59'))
            ->orderByDesc('timestamp')
            ->paginate(25);

        $actionOptions = AuditLog::distinct()->pluck('action')->sort()->values();
        $subjectTypeOptions = AuditLog::distinct()->pluck('subject_type')->sort()->values();

        return view('livewire.audit-log-viewer', [
            'logs'               => $logs,
            'actionOptions'      => $actionOptions,
            'subjectTypeOptions' => $subjectTypeOptions,
        ]);
    }
}
