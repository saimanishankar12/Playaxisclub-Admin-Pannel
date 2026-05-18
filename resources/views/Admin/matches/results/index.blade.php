@extends('Admin.layouts.app')
@section('title', 'Match Manager')
@section('page-title', 'Match Manager')

@section('styles')
<style>
    .mm-index-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
    .mm-index-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}

    /* Resume Banner */
    .mm-resume-banner{background:#fef3c7;border:1.5px solid #fde68a;border-radius:14px;padding:16px 20px;margin-bottom:24px;}
    .mm-resume-title{display:flex;align-items:center;gap:8px;font-size:.75rem;font-weight:700;color:#d97706;margin-bottom:12px;}
    .mm-resume-dot{width:8px;height:8px;border-radius:50%;background:#ef4444;animation:blink 1s infinite;}
    @keyframes blink{0%,100%{opacity:1;}50%{opacity:.3;}}
    .mm-resume-row{display:flex;align-items:center;justify-content:space-between;background:#fff;border-radius:10px;padding:12px 16px;margin-bottom:8px;box-shadow:0 1px 3px rgba(0,0,0,.05);}
    .mm-resume-row:last-child{margin-bottom:0;}
    .mm-resume-info .rr-names{font-size:.88rem;font-weight:700;color:#1e293b;}
    .mm-resume-info .rr-meta{font-size:.72rem;color:#64748b;margin-top:2px;}
    .mm-resume-info .rr-meta span{margin-right:10px;}
    .mm-resume-btn{display:inline-flex;align-items:center;gap:6px;background:#ef4444;color:#fff;padding:7px 16px;border-radius:8px;font-size:.78rem;font-weight:700;text-decoration:none;flex-shrink:0;}
    .mm-resume-btn:hover{background:#dc2626;color:#fff;text-decoration:none;}

    /* Stats row */
    .mm-stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;}
    .mm-stat-card{background:#fff;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.06);text-align:center;}
    .mm-stat-card .sc-num{font-size:1.8rem;font-weight:800;margin-bottom:2px;}
    .mm-stat-card .sc-label{font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;}
    .mm-stat-card.total .sc-num{color:#1a56db;}
    .mm-stat-card.live .sc-num{color:#ef4444;}
    .mm-stat-card.done .sc-num{color:#059669;}
    .mm-stat-card.setup .sc-num{color:#f59e0b;}

    /* Table */
    .mm-card{background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.06);overflow:hidden;margin-bottom:20px;}
    .mm-table{width:100%;border-collapse:collapse;}
    .mm-table thead tr th{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;padding:12px 16px;text-align:left;border-bottom:2px solid #f1f5f9;background:#fafafa;}
    .mm-table tbody tr{transition:background .12s;}
    .mm-table tbody tr:hover{background:#f8fafc;}
    .mm-table tbody tr td{padding:12px 16px;font-size:.83rem;color:#1e293b;border-bottom:1px solid #f1f5f9;}
    .mm-table tbody tr:last-child td{border-bottom:none;}

    .badge-status{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.65rem;font-weight:700;}
    .badge-status.live{background:#fee2e2;color:#ef4444;animation:blink 1.2s infinite;}
    .badge-status.completed{background:#d1fae5;color:#059669;}
    .badge-status.setup{background:#fef3c7;color:#d97706;}

    .badge-round{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.65rem;font-weight:700;text-transform:uppercase;}
    .badge-round.qf{background:#eff6ff;color:#1a56db;}
    .badge-round.sf{background:#f5f3ff;color:#7c3aed;}
    .badge-round.fn{background:#fffbeb;color:#d97706;}

    .badge-type{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.65rem;font-weight:700;background:#f1f5f9;color:#475569;}
    .badge-division{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.65rem;font-weight:700;background:#f0fdf4;color:#059669;}

    .mm-action-btn{font-size:.75rem;font-weight:600;text-decoration:none;padding:4px 10px;border-radius:6px;transition:background .12s;}
    .mm-action-btn.live-btn{color:#ef4444;background:#fee2e2;}
    .mm-action-btn.live-btn:hover{background:#fecaca;text-decoration:none;}
    .mm-action-btn.view-btn{color:#1a56db;background:#eff6ff;}
    .mm-action-btn.view-btn:hover{background:#dbeafe;text-decoration:none;}
    .mm-action-btn.setup-btn{color:#d97706;background:#fef3c7;}
    .mm-action-btn.setup-btn:hover{background:#fde68a;text-decoration:none;}

    .mm-players{font-size:.8rem;color:#64748b;}
    .mm-players .vs{color:#cbd5e1;margin:0 4px;font-size:.7rem;}

    .mm-empty{text-align:center;padding:48px;color:#94a3b8;font-size:.875rem;}
    .mm-empty i{font-size:2rem;opacity:.25;display:block;margin-bottom:12px;}

    @media(max-width:640px){.mm-stats-row{grid-template-columns:1fr 1fr;}}
</style>
@endsection

@section('content')

<div class="mm-index-header">
    <div>
        <h2>Match Manager</h2>
        <p style="font-size:.82rem;color:#64748b;margin:2px 0 0;">Manage all tournament matches</p>
    </div>
    <a href="{{ route('admin-matches.setup') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#1a56db;color:#fff;border-radius:9px;font-size:.82rem;font-weight:700;text-decoration:none;">
        <i class="fas fa-plus"></i> New Match
    </a>
</div>

{{-- Resume Banner --}}
@if($liveMatches->count() > 0)
<div class="mm-resume-banner">
    <div class="mm-resume-title">
        <div class="mm-resume-dot"></div>
        {{ $liveMatches->count() }} MATCH{{ $liveMatches->count() > 1 ? 'ES' : '' }} IN PROGRESS — Resume where you left off
    </div>
    @foreach($liveMatches as $lm)
    <div class="mm-resume-row">
        <div class="mm-resume-info">
            <div class="rr-names">{{ $lm->p1_name }} vs {{ $lm->p2_name }}</div>
            <div class="rr-meta">
                <span><i class="fas fa-map-marker-alt"></i> {{ $lm->court_no }}</span>
                <span><i class="fas fa-tag"></i> {{ $lm->division }}</span>
                <span><i class="fas fa-users"></i> {{ ucfirst($lm->match_type) }}</span>
                <span><i class="fas fa-trophy"></i> {{ $lm->getRoundLabel() }}</span>
            </div>
        </div>
        <a href="{{ route('admin-matches.live', $lm->id) }}" class="mm-resume-btn">
            <i class="fas fa-play"></i> Resume
        </a>
    </div>
    @endforeach
</div>
@endif

{{-- Stats --}}
@php
    $totalCount     = $matches->count();
    $liveCount      = $matches->where('status', 'live')->count();
    $completedCount = $matches->where('status', 'completed')->count();
    $setupCount     = $matches->where('status', 'setup')->count();
@endphp
<div class="mm-stats-row">
    <div class="mm-stat-card total"><div class="sc-num">{{ $totalCount }}</div><div class="sc-label">Total Matches</div></div>
    <div class="mm-stat-card live"><div class="sc-num">{{ $liveCount }}</div><div class="sc-label">Live Now</div></div>
    <div class="mm-stat-card done"><div class="sc-num">{{ $completedCount }}</div><div class="sc-label">Completed</div></div>
    <div class="mm-stat-card setup"><div class="sc-num">{{ $setupCount }}</div><div class="sc-label">Pending</div></div>
</div>

{{-- All Matches Table --}}
<div class="mm-card">
    @if($matches->isEmpty())
        <div class="mm-empty">
            <i class="fas fa-table-tennis"></i>
            No matches yet. Create your first match.
        </div>
    @else
    <table class="mm-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Round</th>
                <th>Division</th>
                <th>Type</th>
                <th>Players</th>
                <th>Score</th>
                <th>Court</th>
                <th>Created</th>
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
            @endphp
            <tr>
                <td>
                    <span class="badge-status {{ $m->status }}">
                        @if($m->status === 'live') ● LIVE
                        @elseif($m->status === 'completed') ✓ Done
                        @else ⏳ Setup
                        @endif
                    </span>
                </td>
                <td><span class="badge-round {{ $roundClass }}">{{ $m->getRoundLabel() }}</span></td>
                <td><span class="badge-division">{{ $m->division }}</span></td>
                <td><span class="badge-type">{{ ucfirst($m->match_type) }}</span></td>
                <td>
                    <div class="mm-players">
                        {{ $m->p1_name ?: '—' }}
                        <span class="vs">vs</span>
                        {{ $m->p2_name ?: '—' }}
                    </div>
                </td>
                <td style="font-family:'DM Mono',monospace;font-size:.8rem;font-weight:600;color:#475569;">
                    {{ $m->sets_won_p1 }} – {{ $m->sets_won_p2 }}
                </td>
                <td style="color:#64748b;font-size:.8rem;">{{ $m->court_no }}</td>
                <td style="color:#94a3b8;font-size:.78rem;">
                    {{ $m->created_at->format('d M, h:i A') }}
                </td>
                <td>
                    @if($m->status === 'live')
                        <a href="{{ route('admin-matches.live', $m->id) }}" class="mm-action-btn live-btn">
                            <i class="fas fa-play mr-1"></i> Resume
                        </a>
                    @elseif($m->status === 'completed')
                        <a href="{{ route('admin-matches.complete', $m->id) }}" class="mm-action-btn view-btn">
                            View
                        </a>
                    @else
                        <a href="{{ route('admin-matches.players', $m->id) }}" class="mm-action-btn setup-btn">
                            Continue
                        </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

<div style="display:flex;gap:10px;flex-wrap:wrap;">
    <a href="{{ route('admin-matches.results') }}"
       style="font-size:.8rem;color:#64748b;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
        <i class="fas fa-list"></i> Results
    </a>
    <span style="color:#e2e8f0;">|</span>
    <a href="{{ route('admin-matches.bracket') }}"
       style="font-size:.8rem;color:#64748b;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
        <i class="fas fa-sitemap"></i> Bracket
    </a>
</div>

@endsection