@extends('Admin.layouts.app')
@section('title', 'Match Results')
@section('page-title', 'Match Results')

@section('styles')
<style>
    .rs-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
    .rs-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}

    .rs-card{background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.06);overflow:hidden;margin-bottom:20px;}

    .rs-table{width:100%;border-collapse:collapse;}
    .rs-table thead tr th{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;padding:12px 16px;text-align:left;border-bottom:2px solid #f1f5f9;background:#fafafa;}
    .rs-table tbody tr{transition:background .12s;}
    .rs-table tbody tr:hover{background:#f8fafc;}
    .rs-table tbody tr td{padding:12px 16px;font-size:.83rem;color:#1e293b;border-bottom:1px solid #f1f5f9;}
    .rs-table tbody tr:last-child td{border-bottom:none;}

    .badge-round{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
    .badge-round.qf{background:#eff6ff;color:#1a56db;}
    .badge-round.sf{background:#f5f3ff;color:#7c3aed;}
    .badge-round.fn{background:#fffbeb;color:#d97706;}

    .badge-type{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.65rem;font-weight:700;background:#f1f5f9;color:#475569;}

    .badge-division{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.65rem;font-weight:700;background:#f0fdf4;color:#059669;}

    .rs-winner{display:flex;align-items:center;gap:6px;font-weight:700;color:#059669;}
    .rs-winner i{font-size:.7rem;color:#f59e0b;}

    .rs-players{font-size:.8rem;color:#64748b;}
    .rs-players .vs{color:#cbd5e1;margin:0 4px;font-size:.7rem;}

    .rs-sets{font-family:'DM Mono',monospace;font-size:.8rem;font-weight:600;color:#475569;}

    .rs-empty{text-align:center;padding:48px;color:#94a3b8;font-size:.875rem;}
    .rs-empty i{font-size:2rem;opacity:.25;display:block;margin-bottom:12px;}

    .rs-action-btn{font-size:.75rem;font-weight:600;color:#1a56db;text-decoration:none;padding:4px 10px;border-radius:6px;background:#eff6ff;transition:background .12s;}
    .rs-action-btn:hover{background:#dbeafe;text-decoration:none;}
</style>
@endsection

@section('content')

<div class="rs-header">
    <div>
        <h2>Match Results</h2>
        <p style="font-size:.82rem;color:#64748b;margin:2px 0 0;">All completed matches</p>
    </div>
    <a href="{{ route('admin-matches.setup') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#1a56db;color:#fff;border-radius:9px;font-size:.82rem;font-weight:700;text-decoration:none;">
        <i class="fas fa-plus"></i> New Match
    </a>
</div>

<div class="rs-card">
    @if($matches->isEmpty())
        <div class="rs-empty">
            <i class="fas fa-trophy"></i>
            No completed matches yet.
        </div>
    @else
    <table class="rs-table">
        <thead>
            <tr>
                <th>Round</th>
                <th>Division</th>
                <th>Type</th>
                <th>Players</th>
                <th>Sets</th>
                <th>Winner</th>
                <th>Court</th>
                <th>Completed</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($matches as $m)
            @php
                $roundClass = match($m->round) {
                    'quarter_final' => 'qf',
                    'semi_final'    => 'sf',
                    'final'         => 'fn',
                    default         => 'qf'
                };
                $roundLabel = match($m->round) {
                    'quarter_final' => 'Quarter Final',
                    'semi_final'    => 'Semi Final',
                    'final'         => 'Final',
                    default         => $m->round
                };
            @endphp
            <tr>
                <td>
                    <span class="badge-round {{ $roundClass }}">{{ $roundLabel }}</span>
                </td>
                <td>
                    <span class="badge-division">{{ $m->division }}</span>
                </td>
                <td>
                    <span class="badge-type">{{ ucfirst($m->match_type) }}</span>
                </td>
                <td>
                    <div class="rs-players">
                        {{ $m->p1_name }}
                        <span class="vs">vs</span>
                        {{ $m->p2_name }}
                    </div>
                </td>
                <td>
                    <span class="rs-sets">{{ $m->sets_won_p1 }} – {{ $m->sets_won_p2 }}</span>
                </td>
                <td>
                    <div class="rs-winner">
                        <i class="fas fa-crown"></i>
                        {{ $m->winner_name }}
                    </div>
                </td>
                <td style="color:#64748b;font-size:.8rem;">{{ $m->court_no }}</td>
                <td style="color:#94a3b8;font-size:.78rem;">
                    {{ $m->completed_at ? $m->completed_at->format('d M, h:i A') : '—' }}
                </td>
                <td>
                    <a href="{{ route('admin-matches.complete', $m->id) }}" class="rs-action-btn">
                        View
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- Bottom nav --}}
<div style="display:flex;gap:10px;flex-wrap:wrap;">
    <a href="{{ route('admin-matches.index') }}"
       style="font-size:.8rem;color:#64748b;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
        <i class="fas fa-arrow-left"></i> All Matches
    </a>
    <span style="color:#e2e8f0;">|</span>
    <a href="{{ route('admin-matches.bracket') }}"
       style="font-size:.8rem;color:#64748b;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
        <i class="fas fa-sitemap"></i> Bracket View
    </a>
</div>

@endsection