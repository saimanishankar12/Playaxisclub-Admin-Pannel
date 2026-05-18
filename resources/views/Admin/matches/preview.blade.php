@extends('Admin.layouts.app')
@section('title', 'Match Preview')
@section('page-title', 'Match Preview')

@section('styles')
<style>
    .mp-wrap{max-width:820px;margin:0 auto;}
    .mp-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
    .mp-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}
    .mp-header p{margin:2px 0 0;font-size:.8rem;color:#64748b;}
    .mp-breadcrumb{display:flex;align-items:center;gap:6px;font-size:.78rem;color:#94a3b8;margin-bottom:24px;}
    .mp-breadcrumb a{color:#64748b;text-decoration:none;}
    .mp-breadcrumb a:hover{color:#1a56db;}
    .mp-breadcrumb span{color:#1e293b;font-weight:600;}

    /* Match meta bar */
    .mp-meta{background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 20px rgba(0,0,0,.07);padding:18px 24px;margin-bottom:24px;display:flex;flex-wrap:wrap;gap:16px;align-items:center;}
    .mp-meta-item{display:flex;flex-direction:column;gap:2px;}
    .mp-meta-item span:first-child{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#94a3b8;}
    .mp-meta-item span:last-child{font-size:.85rem;font-weight:700;color:#1e293b;}
    .mp-badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:700;}
    .mp-badge.singles{background:#eff6ff;color:#1a56db;}
    .mp-badge.doubles{background:#f0fdf4;color:#16a34a;}
    .mp-badge.round{background:#fef3c7;color:#d97706;}
    .mp-badge.sets{background:#f3e8ff;color:#7c3aed;}

    /* ── VS layout ── */
    .mp-matchup{display:grid;grid-template-columns:1fr 56px 1fr;gap:0;align-items:stretch;margin-bottom:24px;}

    .mp-vs-col{display:flex;align-items:center;justify-content:center;position:relative;z-index:2;}
    .mp-vs-circle{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#1e293b,#334155);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:900;color:#fff;letter-spacing:.5px;box-shadow:0 4px 16px rgba(0,0,0,.18);flex-shrink:0;}

    /* ── Team card ── */
    .mp-team-card{background:#fff;border-radius:18px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 6px 24px rgba(0,0,0,.08);overflow:hidden;display:flex;flex-direction:column;}
    .mp-team-card.t1{border-top:4px solid #1a56db;border-radius:18px 0 0 18px;}
    .mp-team-card.t2{border-top:4px solid #ef4444;border-radius:0 18px 18px 0;}

    .mp-team-header{padding:14px 20px 12px;display:flex;align-items:center;gap:10px;border-bottom:1px solid #f1f5f9;}
    .mp-team-label{font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;padding:3px 10px;border-radius:20px;}
    .t1 .mp-team-label{background:#eff6ff;color:#1a56db;}
    .t2 .mp-team-label{background:#fef2f2;color:#ef4444;}
    .mp-team-title{font-size:.82rem;font-weight:700;color:#64748b;}

    /* ── Player rows inside card ── */
    .mp-player-row{display:flex;align-items:center;gap:14px;padding:16px 20px;border-bottom:1px solid #f8fafc;}
    .mp-player-row:last-child{border-bottom:none;}

    .mp-avatar{width:54px;height:54px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:800;color:#94a3b8;border:2px solid #e2e8f0;overflow:hidden;flex-shrink:0;}
    .mp-avatar img{width:54px;height:54px;border-radius:50%;object-fit:cover;}
    .t1 .mp-avatar{border-color:#bfdbfe;}
    .t2 .mp-avatar{border-color:#fecaca;}

    .mp-player-info{flex:1;min-width:0;}
    .mp-player-name{font-size:.95rem;font-weight:800;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .mp-player-id{font-size:.7rem;color:#94a3b8;font-weight:600;font-family:monospace;margin-top:2px;}

    .mp-player-meta{display:flex;flex-direction:column;gap:3px;align-items:flex-end;flex-shrink:0;}
    .mp-player-meta-item{font-size:.72rem;color:#64748b;font-weight:600;text-align:right;white-space:nowrap;}
    .mp-player-meta-item strong{color:#1e293b;font-weight:700;}

    /* ── Divider between players in a team ── */
    .mp-player-divider{margin:0 20px;height:1px;background:linear-gradient(to right,transparent,#e2e8f0,transparent);}

    /* ── Singles layout (original) ── */
    .mp-singles-matchup{display:grid;grid-template-columns:1fr auto 1fr;gap:16px;align-items:center;margin-bottom:24px;}
    .mp-singles-vs{font-size:1.4rem;font-weight:900;color:#e2e8f0;text-align:center;}
    .mp-singles-card{background:#fff;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 20px rgba(0,0,0,.08);padding:24px;display:flex;flex-direction:column;align-items:center;gap:12px;}
    .mp-singles-card.p1{border-top:4px solid #1a56db;}
    .mp-singles-card.p2{border-top:4px solid #ef4444;}
    .mp-singles-avatar{width:72px;height:72px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:800;color:#94a3b8;border:3px solid #e2e8f0;overflow:hidden;}
    .mp-singles-avatar img{width:72px;height:72px;border-radius:50%;object-fit:cover;}
    .mp-singles-name{font-size:1rem;font-weight:800;color:#1e293b;text-align:center;}
    .mp-singles-pid{font-size:.72rem;color:#94a3b8;font-weight:600;font-family:monospace;}
    .mp-detail-row{display:flex;justify-content:space-between;align-items:center;font-size:.78rem;border-bottom:1px solid #f1f5f9;padding-bottom:5px;width:100%;}
    .mp-detail-row:last-child{border-bottom:none;padding-bottom:0;}
    .mp-detail-row span:first-child{color:#94a3b8;font-weight:600;}
    .mp-detail-row span:last-child{color:#1e293b;font-weight:700;text-align:right;}
    .mp-singles-details{width:100%;display:flex;flex-direction:column;gap:6px;margin-top:4px;}

    /* Actions */
    .mp-actions{display:flex;gap:12px;}
    .mp-btn-start{flex:1;padding:13px;background:linear-gradient(135deg,#1a56db,#6366f1);color:#fff;border:none;border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer;transition:opacity .15s;}
    .mp-btn-start:hover{opacity:.9;}
    .mp-btn-back{padding:13px 20px;background:#f1f5f9;color:#64748b;border:none;border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer;text-decoration:none;display:flex;align-items:center;gap:6px;transition:background .15s;}
    .mp-btn-back:hover{background:#e2e8f0;color:#1e293b;text-decoration:none;}

    @media(max-width:620px){
        .mp-matchup{grid-template-columns:1fr;gap:12px;}
        .mp-team-card.t1,.mp-team-card.t2{border-radius:14px;}
        .mp-vs-col{padding:6px 0;}
        .mp-singles-matchup{grid-template-columns:1fr;}
        .mp-singles-vs{display:none;}
    }
</style>
@endsection

@section('content')

<div class="mp-header">
    <div>
        <h2>Match Preview</h2>
        <p>Review the auto-paired players before starting.</p>
    </div>
</div>

<div class="mp-breadcrumb">
    <a href="{{ route('admin-matches.index') }}">Match Manager</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <a href="{{ route('admin-matches.setup') }}">Setup</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <span>Preview</span>
</div>

<div class="mp-wrap">

    {{-- ── Match Meta Bar ─────────────────────────────────────────── --}}
    <div class="mp-meta">
        <div class="mp-meta-item">
            <span>Court</span>
            <span>{{ $setup['court_no'] }}</span>
        </div>
        <div class="mp-meta-item">
            <span>Umpire</span>
            <span>{{ $setup['umpire_name'] }}</span>
        </div>
        @if(!empty($setup['scorer_name']))
        <div class="mp-meta-item">
            <span>Scorer</span>
            <span>{{ $setup['scorer_name'] }}</span>
        </div>
        @endif
        <div class="mp-meta-item">
            <span>Type</span>
            <span class="mp-badge {{ $setup['match_type'] }}">
                {{ ucfirst($setup['match_type']) }}
            </span>
        </div>
        <div class="mp-meta-item">
            <span>Division</span>
            <span>{{ $setup['division'] }}</span>
        </div>
        <div class="mp-meta-item">
            <span>Round</span>
            <span class="mp-badge round">
                @php
                    echo match($setup['round']) {
                        'quarter_final' => 'Knock Out',
                        'semi_final'    => 'Semi Final',
                        'final'         => 'Final',
                        default         => ucwords(str_replace('_', ' ', $setup['round']))
                    };
                @endphp
            </span>
        </div>
        <div class="mp-meta-item">
            <span>Sets</span>
            @php $totalSets = $setup['sets'] ?? 1; @endphp
            <span class="mp-badge sets">
                {{ $totalSets }} {{ $totalSets == 1 ? 'Set' : 'Sets' }}
            </span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         DOUBLES layout — full team cards
    ══════════════════════════════════════════════════════════════ --}}
    @if($setup['match_type'] === 'doubles')

    <div class="mp-matchup">

        {{-- Team 1 --}}
        <div class="mp-team-card t1">
            <div class="mp-team-header">
                <span class="mp-team-label">Team 1</span>
                <span class="mp-team-title">{{ $player1->name }} &amp; {{ $partner1->name ?? '—' }}</span>
            </div>

            {{-- Player 1 --}}
            <div class="mp-player-row">
                <div class="mp-avatar">
                    @if(!empty($player1->profile_photo))
                        <img src="{{ $player1->profile_photo_url ?? asset('img/undraw_profile.svg') }}" alt="{{ $player1->name }}">
                    @else
                        {{ strtoupper(substr($player1->name ?? 'P', 0, 1)) }}
                    @endif
                </div>
                <div class="mp-player-info">
                    <div class="mp-player-name">{{ $player1->name }}</div>
                    <div class="mp-player-id"># {{ $player1->player_id }}</div>
                </div>
                <div class="mp-player-meta">
                    <div class="mp-player-meta-item"><strong>{{ $player1->season_id ?? '—' }}</strong></div>
                    <div class="mp-player-meta-item">{{ $player1->address ?? '—' }}</div>
                    <div class="mp-player-meta-item">{{ $player1->age ?? '—' }}</div>
                </div>
            </div>

            {{-- Partner 1 --}}
            @if(!empty($partner1))
            <div class="mp-player-row">
                <div class="mp-avatar">
                    @if(!empty($partner1->profile_photo))
                        <img src="{{ $partner1->profile_photo_url ?? asset('img/undraw_profile.svg') }}" alt="{{ $partner1->name }}">
                    @else
                        {{ strtoupper(substr($partner1->name ?? 'P', 0, 1)) }}
                    @endif
                </div>
                <div class="mp-player-info">
                    <div class="mp-player-name">{{ $partner1->name }}</div>
                    <div class="mp-player-id"># {{ $partner1->player_id }}</div>
                </div>
                <div class="mp-player-meta">
                    <div class="mp-player-meta-item"><strong>{{ $partner1->season_id ?? '—' }}</strong></div>
                    <div class="mp-player-meta-item">{{ $partner1->address ?? '—' }}</div>
                    <div class="mp-player-meta-item">{{ $partner1->age ?? '—' }}</div>
                </div>
            </div>
            @endif
        </div>

        {{-- VS --}}
        <div class="mp-vs-col">
            <div class="mp-vs-circle">VS</div>
        </div>

        {{-- Team 2 --}}
        <div class="mp-team-card t2">
            <div class="mp-team-header">
                <span class="mp-team-label">Team 2</span>
                <span class="mp-team-title">{{ $player2->name }} &amp; {{ $partner2->name ?? '—' }}</span>
            </div>

            {{-- Player 2 --}}
            <div class="mp-player-row">
                <div class="mp-avatar">
                    @if(!empty($player2->profile_photo))
                        <img src="{{ $player2->profile_photo_url ?? asset('img/undraw_profile.svg') }}" alt="{{ $player2->name }}">
                    @else
                        {{ strtoupper(substr($player2->name ?? 'P', 0, 1)) }}
                    @endif
                </div>
                <div class="mp-player-info">
                    <div class="mp-player-name">{{ $player2->name }}</div>
                    <div class="mp-player-id"># {{ $player2->player_id }}</div>
                </div>
                <div class="mp-player-meta">
                    <div class="mp-player-meta-item"><strong>{{ $player2->season_id ?? '—' }}</strong></div>
                    <div class="mp-player-meta-item">{{ $player2->address ?? '—' }}</div>
                    <div class="mp-player-meta-item">{{ $player2->age ?? '—' }}</div>
                </div>
            </div>

            {{-- Partner 2 --}}
            @if(!empty($partner2))
            <div class="mp-player-row">
                <div class="mp-avatar">
                    @if(!empty($partner2->profile_photo))
                        <img src="{{ $partner2->profile_photo_url ?? asset('img/undraw_profile.svg') }}" alt="{{ $partner2->name }}">
                    @else
                        {{ strtoupper(substr($partner2->name ?? 'P', 0, 1)) }}
                    @endif
                </div>
                <div class="mp-player-info">
                    <div class="mp-player-name">{{ $partner2->name }}</div>
                    <div class="mp-player-id"># {{ $partner2->player_id }}</div>
                </div>
                <div class="mp-player-meta">
                    <div class="mp-player-meta-item"><strong>{{ $partner2->season_id ?? '—' }}</strong></div>
                    <div class="mp-player-meta-item">{{ $partner2->address ?? '—' }}</div>
                    <div class="mp-player-meta-item">{{ $partner2->age ?? '—' }}</div>
                </div>
            </div>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         SINGLES layout — original design
    ══════════════════════════════════════════════════════════════ --}}
    @else

    <div class="mp-singles-matchup">

        <div class="mp-singles-card p1">
            <div class="mp-singles-avatar">
                @if(!empty($player1->profile_photo))
                    <img src="{{ $player1->profile_photo_url ?? asset('img/undraw_profile.svg') }}" alt="{{ $player1->name }}">
                @else
                    {{ strtoupper(substr($player1->name ?? 'P', 0, 1)) }}
                @endif
            </div>
            <div class="mp-singles-name">{{ $player1->name }}</div>
            <div class="mp-singles-pid"># {{ $player1->player_id }}</div>
            <div class="mp-singles-details">
                <div class="mp-detail-row"><span>Tournament ID</span><span>{{ $player1->season_id ?? '—' }}</span></div>
                <div class="mp-detail-row"><span>City</span><span>{{ $player1->address ?? '—' }}</span></div>
                <div class="mp-detail-row"><span>Age Group</span><span>{{ $player1->age ?? '—' }}</span></div>
            </div>
        </div>

        <div class="mp-singles-vs">VS</div>

        <div class="mp-singles-card p2">
            <div class="mp-singles-avatar">
                @if(!empty($player2->profile_photo))
                    <img src="{{ $player2->profile_photo_url ?? asset('img/undraw_profile.svg') }}" alt="{{ $player2->name }}">
                @else
                    {{ strtoupper(substr($player2->name ?? 'P', 0, 1)) }}
                @endif
            </div>
            <div class="mp-singles-name">{{ $player2->name }}</div>
            <div class="mp-singles-pid"># {{ $player2->player_id }}</div>
            <div class="mp-singles-details">
                <div class="mp-detail-row"><span>Tournament ID</span><span>{{ $player2->season_id ?? '—' }}</span></div>
                <div class="mp-detail-row"><span>City</span><span>{{ $player2->address ?? '—' }}</span></div>
                <div class="mp-detail-row"><span>Age Group</span><span>{{ $player2->age ?? '—' }}</span></div>
            </div>
        </div>

    </div>

    @endif

    {{-- ── Actions ──────────────────────────────────────────────────── --}}
    <div class="mp-actions">
        <a href="{{ route('admin-matches.setup') }}" class="mp-btn-back">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <form action="{{ route('admin-matches.confirm') }}" method="POST" style="flex:1;">
            @csrf
            <button type="submit" class="mp-btn-start">
                <i class="fas fa-play mr-2"></i> Start Match
            </button>
        </form>
    </div>

</div>

@endsection