<?php

namespace App\Livewire;

use App\Models\DamageCharge;
use App\Models\DamageReport;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * FR-13.2 — Staff shall be able to review submitted damage reports and
 * accept, reject, or escalate them.
 * FR-13.3 — An accepted damage report generates a damage charge record.
 */
#[Layout('layouts.app')]
class DamageReportManager extends Component
{
    use WithPagination;

    #[Url]
    public string $statusFilter = '';

    // ── Charge form (shown when accepting) ───────────────────────────────
    public ?int $chargingReportId = null;
    public int $chargeAmount = 0;
    public string $chargeCurrency = 'NAD';
    public string $chargeDescription = '';

    /**
     * FR-13.2 — Accept a damage report.
     * Opens the charge form so staff can specify the damage charge.
     */
    public function startAccept(int $reportId): void
    {
        $this->chargingReportId = $reportId;
        $this->chargeAmount = 0;
        $this->chargeCurrency = 'NAD';
        $this->chargeDescription = '';
    }

    /**
     * FR-13.3 — Save the charge and mark the report as accepted.
     */
    public function confirmAccept(): void
    {
        $this->validate([
            'chargeAmount'      => 'required|integer|min:0',
            'chargeCurrency'    => 'required|string|size:3',
            'chargeDescription' => 'nullable|string|max:1000',
        ]);

        $report = DamageReport::findOrFail($this->chargingReportId);
        $report->update(['status' => 'accepted']);

        if ($this->chargeAmount > 0) {
            DamageCharge::create([
                'damage_report_id' => $report->id,
                'booking_id'       => $report->booking_id,
                'amount_cents'     => $this->chargeAmount,
                'currency_code'    => $this->chargeCurrency,
                'description'      => $this->chargeDescription,
            ]);
        }

        app(AuditLogger::class)->log('damage_report.accepted', $report);
        $this->chargingReportId = null;
    }

    public function cancelCharge(): void
    {
        $this->chargingReportId = null;
    }

    public function reject(int $reportId): void
    {
        $report = DamageReport::findOrFail($reportId);
        $report->update(['status' => 'rejected']);
        app(AuditLogger::class)->log('damage_report.rejected', $report);
    }

    public function escalate(int $reportId): void
    {
        $report = DamageReport::findOrFail($reportId);
        $report->update(['status' => 'escalated']);
        app(AuditLogger::class)->log('damage_report.escalated', $report);
    }

    public function render()
    {
        $user = Auth::user();

        $reports = DamageReport::query()
            ->with(['booking.tool.depot', 'user', 'charge'])
            ->when($user->isStaff() && $user->depot_id, function ($q) use ($user) {
                $q->whereHas('booking.tool', fn ($tq) => $tq->where('depot_id', $user->depot_id));
            })
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.damage-report-manager', [
            'reports' => $reports,
        ]);
    }
}
