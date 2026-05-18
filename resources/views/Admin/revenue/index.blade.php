@extends('Admin.layouts.app')

@section('title', 'Revenue Report')
@section('page-title', 'Revenue Report')

@section('styles')
<style>
    /* ── Page header ─────────────────────────────────── */
    .rev-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
    }
    .rev-header h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .rev-header p {
        margin: 2px 0 0;
        font-size: .8rem;
        color: #64748b;
    }

    /* ── Summary cards ───────────────────────────────── */
    .rev-summary {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }
    .rev-summary-card {
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.06);
        display: flex;
        flex-direction: column;
        gap: 10px;
        position: relative;
        overflow: hidden;
    }
    .rev-summary-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 14px 14px 0 0;
    }
    .rev-card--green::before  { background: #10b981; }
    .rev-card--blue::before   { background: #1a56db; }
    .rev-card--purple::before { background: #8b5cf6; }

    .rev-summary-card .s-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
    }
    .rev-card--green  .s-icon { background: #d1fae5; color: #059669; }
    .rev-card--blue   .s-icon { background: #e8f0fe; color: #1a56db; }
    .rev-card--purple .s-icon { background: #ede9fe; color: #7c3aed; }

    .rev-summary-card .s-value {
        font-size: 1.6rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
        font-family: 'DM Mono', monospace;
    }
    .rev-summary-card .s-label {
        font-size: .72rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    /* ── Main table card ─────────────────────────────── */
    .rev-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.06);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .rev-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .rev-card-header h6 {
        font-size: .95rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .rev-card-header small {
        font-size: .75rem;
        color: #64748b;
    }

    /* search input */
    .rev-search {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: .8rem;
        color: #1e293b;
        outline: none;
        transition: border-color .15s;
        width: 200px;
    }
    .rev-search:focus { border-color: #1a56db; }

    /* ── Table ───────────────────────────────────────── */
    .rev-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .83rem;
    }
    .rev-table thead th {
        background: #f8fafc;
        padding: 11px 16px;
        text-align: left;
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .rev-table thead th.text-right { text-align: right; }

    .rev-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background .12s;
    }
    .rev-table tbody tr:last-child { border-bottom: none; }
    .rev-table tbody tr:hover { background: #f8fafc; }
    .rev-table tbody td {
        padding: 13px 16px;
        color: #1e293b;
        vertical-align: middle;
    }
    .rev-table tbody td.text-right { text-align: right; }

    /* tfoot totals row */
    .rev-table tfoot td {
        padding: 12px 16px;
        font-weight: 700;
        font-size: .83rem;
        color: #1e293b;
        border-top: 2px solid #e2e8f0;
        background: #f8fafc;
    }
    .rev-table tfoot td.text-right { text-align: right; }

    /* season name */
    .rev-season-name {
        font-weight: 600;
        color: #1e293b;
    }
    .rev-sport-tag {
        display: inline-block;
        font-size: .65rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 20px;
        background: #e8f0fe;
        color: #1a56db;
        margin-left: 6px;
        vertical-align: middle;
    }

    /* status badges */
    .rev-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: .68rem;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 20px;
    }
    .rev-badge--active   { background: #d1fae5; color: #059669; }
    .rev-badge--upcoming { background: #fef3c7; color: #d97706; }
    .rev-badge--done     { background: #e2e8f0; color: #64748b; }

    /* revenue amount */
    .rev-amount {
        font-family: 'DM Mono', monospace;
        font-weight: 700;
        color: #059669;
        font-size: .88rem;
    }
    .rev-amount--zero { color: #94a3b8; }

    /* progress bar */
    .rev-bar-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 120px;
    }
    .rev-bar {
        flex: 1;
        height: 6px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
    }
    .rev-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #1a56db, #6366f1);
        border-radius: 99px;
        transition: width .4s ease;
    }
    .rev-bar-pct {
        font-size: .7rem;
        font-weight: 600;
        color: #64748b;
        width: 32px;
        text-align: right;
        flex-shrink: 0;
    }

    /* empty state */
    .rev-empty {
        padding: 48px;
        text-align: center;
        color: #94a3b8;
        font-size: .875rem;
    }
    .rev-empty i {
        font-size: 2rem;
        opacity: .25;
        display: block;
        margin-bottom: 12px;
    }

    @media (max-width: 576px) {
        .rev-summary { grid-template-columns: 1fr 1fr; }
        .rev-bar-wrap { display: none; }
        .rev-search { width: 140px; }
    }
</style>
@endsection

@section('content')

    {{-- ── Page Header ── --}}
    <div class="rev-header">
        <div>
            <h2>Revenue Report</h2>
            <p>Tournament-wise breakdown of paid registrations and earnings.</p>
        </div>
        <a href="{{ route('admin-dashboard') }}" style="font-size:.8rem;color:#64748b;text-decoration:none;">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="rev-summary">
       
      <div class="rev-summary-card rev-card--purple">
            <div class="s-icon"><i class="fas fa-trophy"></i></div>
            <div class="s-value">{{ $seasons->count() }}</div>
            <div class="s-label">Tournaments</div>
        </div>
     

        <div class="rev-summary-card rev-card--blue">
            <div class="s-icon"><i class="fas fa-users"></i></div>
            <div class="s-value">{{ number_format($grandPaidCount) }}</div>
            <div class="s-label">Total Paid Registrations</div>
        </div>
     

        
      

           <div class="rev-summary-card rev-card--green">
            <div class="s-icon"><i class="fas fa-rupee-sign"></i></div>
            <div class="s-value">₹{{ number_format($grandTotal, 2) }}</div>
            <div class="s-label">Total Revenue</div>
        </div>

    </div>

    {{-- ── Revenue Table ── --}}
    <div class="rev-card">
        <div class="rev-card-header">
            <div>
                <h6>Season-wise Revenue</h6>
                <small>Only <strong>paid</strong> transactions counted &mdash; failed &amp; pending excluded</small>
            </div>
            <input type="text" class="rev-search" id="revSearch" placeholder="&#xf002;  Search tournament…" oninput="filterTable()">
        </div>

        <div style="overflow-x:auto;">
            @if($seasons->isEmpty())
                <div class="rev-empty">
                    <i class="fas fa-chart-bar"></i>
                    No tournament data found. Revenue will appear automatically once payments are recorded.
                </div>
            @else
                @php
                    $maxRevenue = $seasons->max('total_revenue') ?: 1;
                @endphp
                <table class="rev-table" id="revTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tournament / Season</th>
                            <th>Sport</th>
                            <th>Status</th>
                            <th class="text-right">Singles</th>
                            <th class="text-right">Doubles</th>
                            <th class="text-right">Paid Registrations</th>
                            <th class="text-right">Revenue</th>
                            <th>Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($seasons as $i => $season)
                            @php
                                $pct = $maxRevenue > 0
                                    ? round(($season->total_revenue / $maxRevenue) * 100)
                                    : 0;
                                $isZero = !$season->total_revenue || $season->total_revenue == 0;
                            @endphp
                            <tr>
                                <td style="color:#94a3b8;font-size:.75rem;">{{ $i + 1 }}</td>

                                <td>
                                    <span class="rev-season-name">{{ $season->name }}</span>
                                </td>

                                <td>
                                    <span class="rev-sport-tag">{{ $season->sport }}</span>
                                </td>

                                <td>
                                    @if($season->status === 'active')
                                        <span class="rev-badge rev-badge--active">
                                            <i class="fas fa-circle" style="font-size:.35rem;"></i> Active
                                        </span>
                                    @elseif($season->status === 'upcoming')
                                        <span class="rev-badge rev-badge--upcoming">
                                            <i class="fas fa-clock" style="font-size:.5rem;"></i> Upcoming
                                        </span>
                                    @else
                                        <span class="rev-badge rev-badge--done">
                                            <i class="fas fa-check" style="font-size:.5rem;"></i> Completed
                                        </span>
                                    @endif
                                </td>

                                <td class="text-right">
                                    <span style="font-size:.8rem;color:#475569;">
                                        {{ number_format($season->singles_count ?? 0) }}
                                        <span style="color:#94a3b8;font-size:.7rem;"> reg</span>
                                    </span><br>
                                    <span style="font-size:.75rem;color:#64748b;">
                                        ₹{{ number_format($season->singles_revenue ?? 0, 2) }}
                                    </span>
                                </td>

                                <td class="text-right">
                                    <span style="font-size:.8rem;color:#475569;">
                                        {{ number_format($season->doubles_count ?? 0) }}
                                        <span style="color:#94a3b8;font-size:.7rem;"> reg</span>
                                    </span><br>
                                    <span style="font-size:.75rem;color:#64748b;">
                                        ₹{{ number_format($season->doubles_revenue ?? 0, 2) }}
                                    </span>
                                </td>

                                <td class="text-right">
                                    <strong>{{ number_format($season->paid_count ?? 0) }}</strong>
                                </td>

                                <td class="text-right">
                                    <span class="rev-amount {{ $isZero ? 'rev-amount--zero' : '' }}">
                                        ₹{{ number_format($season->total_revenue ?? 0, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="rev-bar-wrap">
                                        <div class="rev-bar">
                                            <div class="rev-bar-fill" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <span class="rev-bar-pct">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="font-size:.75rem;color:#64748b;">
                                {{ $seasons->count() }} tournament{{ $seasons->count() !== 1 ? 's' : '' }} total
                            </td>
                            <td class="text-right">
                                ₹{{ number_format($seasons->sum('singles_revenue'), 2) }}
                            </td>
                            <td class="text-right">
                                ₹{{ number_format($seasons->sum('doubles_revenue'), 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($grandPaidCount) }}
                            </td>
                            <td class="text-right" style="color:#059669;">
                                ₹{{ number_format($grandTotal, 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function filterTable() {
        const q     = document.getElementById('revSearch').value.toLowerCase();
        const rows  = document.querySelectorAll('#revTable tbody tr');
        rows.forEach(function(row) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    }
</script>
@endsection