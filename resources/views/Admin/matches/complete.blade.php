@extends('Admin.layouts.app')
@section('title', 'Match Complete')
@section('page-title', 'Match Complete')

@section('styles')
<style>
    .cm-wrap{max-width:720px;margin:0 auto;}

    /* ── Match Header Banner ─────────────────────────────────────────────── */
    .cm-match-banner{background:linear-gradient(135deg,#0f172a,#1e293b);border-radius:20px;padding:28px 24px;margin-bottom:24px;box-shadow:0 8px 32px rgba(0,0,0,.3);border:1px solid #1e3a5f;}

    .cm-banner-meta{display:flex;align-items:center;justify-content:center;gap:8px;flex-wrap:wrap;margin-bottom:20px;}
    .cm-banner-chip{font-size:.68rem;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(255,255,255,.08);color:#94a3b8;}
    .cm-banner-chip.match-no{background:rgba(26,86,219,.25);color:#60a5fa;}
    .cm-banner-chip.round{background:rgba(245,158,11,.2);color:#fbbf24;}

    .cm-banner-players{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:16px;}
    .cm-banner-player{display:flex;flex-direction:column;align-items:center;gap:8px;}
    .cm-banner-player.winner .cm-bp-name{color:#34d399;}
    .cm-banner-player.loser .cm-bp-name{color:#64748b;}

    .cm-bp-photo{width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #334155;}
    .cm-bp-photo-placeholder{width:72px;height:72px;border-radius:50%;background:#334155;display:flex;align-items:center;justify-content:center;font-size:1.6rem;border:3px solid #475569;}
    .cm-banner-player.winner .cm-bp-photo{border-color:#34d399;box-shadow:0 0 16px rgba(52,211,153,.3);}
    .cm-banner-player.loser .cm-bp-photo{opacity:.6;}
    .cm-banner-player.loser .cm-bp-photo-placeholder{opacity:.6;}

    .cm-bp-name{font-size:.92rem;font-weight:800;color:#f1f5f9;text-align:center;word-break:break-word;}
    .cm-bp-score{font-size:2.8rem;font-weight:900;font-family:'DM Mono',monospace;line-height:1;}
    .cm-banner-player.winner .cm-bp-score{color:#34d399;}
    .cm-banner-player.loser .cm-bp-score{color:#334155;}
    .cm-bp-label{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#475569;margin-top:2px;}

    .cm-banner-vs{display:flex;flex-direction:column;align-items:center;gap:6px;}
    .cm-banner-vs-badge{background:#1e293b;border:1px solid #334155;color:#475569;font-size:.7rem;font-weight:800;padding:6px 10px;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;}
    .cm-banner-winner-label{font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#34d399;text-align:center;}

    .cm-no-winner-banner{background:linear-gradient(135deg,#64748b,#94a3b8);border-radius:16px;padding:32px;text-align:center;color:#fff;margin-bottom:24px;}

    .cm-card{background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.06);margin-bottom:20px;overflow:hidden;}
    .cm-card-header{padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:8px;}
    .cm-card-header span{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#64748b;}
    .cm-card-body{padding:20px;}

    .cm-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
    .cm-info-item label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;display:block;margin-bottom:2px;}
    .cm-info-item span{font-size:.88rem;font-weight:600;color:#1e293b;}

    .cm-time-row{display:flex;flex-wrap:wrap;gap:20px;align-items:center;}
    .cm-time-item{display:flex;align-items:center;gap:6px;font-size:.85rem;font-weight:600;color:#1e293b;}
    .cm-time-item i{font-size:.8rem;}

    .cm-scoreboard{width:100%;border-collapse:collapse;}
    .cm-scoreboard thead tr th{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;padding:8px 12px;text-align:left;border-bottom:2px solid #f1f5f9;}
    .cm-scoreboard tbody tr td{padding:10px 12px;font-size:.84rem;font-weight:600;color:#1e293b;border-bottom:1px solid #f8fafc;}
    .cm-scoreboard tbody tr:last-child td{border-bottom:none;}
    .cm-scoreboard tbody tr td.set-num{color:#94a3b8;font-weight:500;}
    .cm-scoreboard tbody tr td.score-win{color:#059669;}
    .cm-scoreboard tbody tr td.score-lose{color:#94a3b8;}
    .cm-scoreboard tbody tr td .badge-winner{background:#d1fae5;color:#059669;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px;}

    .cm-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:24px;}
    .cm-btn{padding:10px 22px;border-radius:9px;font-size:.82rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:all .15s;}
    .cm-btn-primary{background:#1a56db;color:#fff;}
    .cm-btn-primary:hover{background:#1648c0;color:#fff;text-decoration:none;}
    .cm-btn-secondary{background:#f1f5f9;color:#475569;}
    .cm-btn-secondary:hover{background:#e2e8f0;color:#475569;text-decoration:none;}
    .cm-btn-ghost{background:#fff;color:#64748b;border:1.5px solid #e2e8f0;}
    .cm-btn-ghost:hover{border-color:#1a56db;color:#1a56db;text-decoration:none;}
    .cm-btn-warning{background:#fff7ed;color:#c2410c;border:1.5px solid #fed7aa;}
    .cm-btn-warning:hover{background:#ffedd5;color:#c2410c;text-decoration:none;}

    @media(max-width:480px){
        .cm-bp-score{font-size:2rem;}
        .cm-bp-photo,.cm-bp-photo-placeholder{width:56px;height:56px;}
        .cm-match-banner{padding:18px 14px;}
    }
</style>
@endsection

@section('content')
<div class="cm-wrap">

    {{-- Back link --}}
    <a href="{{ route('admin-matches.index') }}" style="font-size:.8rem;color:#64748b;text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:20px;">
        <i class="fas fa-arrow-left"></i> All Matches
    </a>

    @if(session('success'))
    <div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:.83rem;color:#059669;font-weight:600;">
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
    @endif

    @php
        if ($match->match_type === 'singles') {
            $p1Info = DB::table('players')->where('player_id', $match->player1_id)->first();
            $p2Info = DB::table('players')->where('player_id', $match->player2_id)->first();
        } else {
            $p1Info = DB::table('players')->where('season_id', $match->player1_id)->where('mode','doubles')->first();
            $p2Info = DB::table('players')->where('season_id', $match->player2_id)->where('mode','doubles')->first();
        }
        $p1IsWinner   = $match->winner_id === $match->player1_id;
        $p2IsWinner   = $match->winner_id === $match->player2_id;

        // Only count scores from played sets (winner not null)
        $p1TotalScore = $completedSets->sum('score_p1');
        $p2TotalScore = $completedSets->sum('score_p2');
    @endphp

    {{-- Winner Banner --}}
    @if($winnerName)
    <div class="cm-match-banner">

        {{-- Meta chips --}}
        <div class="cm-banner-meta">
            <span class="cm-banner-chip match-no">Match #{{ $match->id }}</span>
            <span class="cm-banner-chip round">{{ $match->getRoundLabel() }}</span>
            <span class="cm-banner-chip">{{ $match->division }}</span>
            <span class="cm-banner-chip">{{ ucfirst($match->match_type) }}</span>
        </div>

        {{-- Players --}}
        <div class="cm-banner-players">

            {{-- Player 1 --}}
            <div class="cm-banner-player {{ $p1IsWinner ? 'winner' : 'loser' }}">
                @if($p1Info && $p1Info->profile_photo)
                    <img src="{{ asset('storage/' . $p1Info->profile_photo) }}"
                         alt="{{ $p1Name }}" class="cm-bp-photo">
                @else
                    <div class="cm-bp-photo-placeholder">🏸</div>
                @endif
                <div class="cm-bp-name">{{ $p1Name }}</div>
                @if($p1Info)
                <div style="font-size:.65rem;color:#475569;font-family:'DM Mono',monospace;">Tournament ID: {{ $p1Info->season_id }}</div>
                <div style="font-size:.65rem;color:#475569;">City: {{ $p1Info->address }}</div>
                @endif
                <div class="cm-bp-score">{{ $p1TotalScore }}</div>
                <div class="cm-bp-label">Total Score</div>
            </div>

            {{-- VS --}}
            <div class="cm-banner-vs">
                <div class="cm-banner-vs-badge">VS</div>
                <div class="cm-banner-winner-label">🏆 {{ $winnerName }} wins!</div>
            </div>

            {{-- Player 2 --}}
            <div class="cm-banner-player {{ $p2IsWinner ? 'winner' : 'loser' }}">
                @if($p2Info && $p2Info->profile_photo)
                    <img src="{{ asset('storage/' . $p2Info->profile_photo) }}"
                         alt="{{ $p2Name }}" class="cm-bp-photo">
                @else
                    <div class="cm-bp-photo-placeholder">🏸</div>
                @endif
                <div class="cm-bp-name">{{ $p2Name }}</div>
                @if($p2Info)
                <div style="font-size:.65rem;color:#475569;font-family:'DM Mono',monospace;">Tournament ID: {{ $p2Info->season_id }}</div>
                <div style="font-size:.65rem;color:#475569;">City: {{ $p2Info->address }}</div>
                @endif
                <div class="cm-bp-score">{{ $p2TotalScore }}</div>
                <div class="cm-bp-label">Total Score</div>
            </div>

        </div>
    </div>

    @else
    <div class="cm-no-winner-banner">
        <div style="font-size:2.5rem;margin-bottom:8px;">🛑</div>
        <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;opacity:.8;margin-bottom:4px;">Match Ended Manually</div>
        <div style="font-size:1.6rem;font-weight:800;">No Winner Declared</div>
        <div style="font-size:.8rem;opacity:.75;margin-top:8px;">
            Match #{{ $match->id }} &nbsp;·&nbsp; {{ $match->getRoundLabel() }} &nbsp;·&nbsp; {{ $match->division }} &nbsp;·&nbsp; {{ ucfirst($match->match_type) }}
        </div>
    </div>
    @endif

    {{-- Set by Set Breakdown — only played sets --}}
    @if($completedSets->count())
    <div class="cm-card">
        <div class="cm-card-header">
            <i class="fas fa-list-ol" style="color:#1a56db;font-size:.85rem;"></i>
            <span>Set by Set Breakdown</span>
        </div>
        <div class="cm-card-body" style="padding:0;">
            <table class="cm-scoreboard">
                <thead>
                    <tr>
                        <th>Set</th>
                        <th>{{ $p1Name }}</th>
                        <th>{{ $p2Name }}</th>
                        <th>Winner</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Only loop sets that have a winner (played sets) --}}
                    @foreach($completedSets->whereNotNull('winner') as $set)
                    @php $p1Win = $set->winner === 'p1'; @endphp
                    <tr>
                        <td class="set-num">Set {{ $set->set_number }}</td>
                        <td class="{{ $p1Win ? 'score-win' : 'score-lose' }}">{{ $set->score_p1 }}</td>
                        <td class="{{ !$p1Win ? 'score-win' : 'score-lose' }}">{{ $set->score_p2 }}</td>
                        <td><span class="badge-winner">{{ $p1Win ? $p1Name : $p2Name }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Sets Summary --}}
    @if($completedSets->count())
    <div class="cm-card">
        <div class="cm-card-header">
            <i class="fas fa-trophy" style="color:#f59e0b;font-size:.85rem;"></i>
            <span>Sets Summary</span>
        </div>
        <div class="cm-card-body">
            <div style="display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:16px;text-align:center;">
                <div>
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">{{ $p1Name }}</div>
                    <div style="font-size:2.4rem;font-weight:900;color:{{ $p1IsWinner ? '#059669' : '#94a3b8' }};">
                        {{ $completedSets->where('winner','p1')->count() }}
                    </div>
                    <div style="font-size:.68rem;color:#94a3b8;">sets won</div>
                </div>
                <div style="font-size:.75rem;font-weight:800;color:#cbd5e1;">VS</div>
                <div>
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">{{ $p2Name }}</div>
                    <div style="font-size:2.4rem;font-weight:900;color:{{ $p2IsWinner ? '#059669' : '#94a3b8' }};">
                        {{ $completedSets->where('winner','p2')->count() }}
                    </div>
                    <div style="font-size:.68rem;color:#94a3b8;">sets won</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Match Info --}}
    <div class="cm-card">
        <div class="cm-card-header">
            <i class="fas fa-info-circle" style="color:#1a56db;font-size:.85rem;"></i>
            <span>Match Info</span>
        </div>
        <div class="cm-card-body">
            <div class="cm-info-grid">
                <div class="cm-info-item">
                    <label>Match No</label>
                    <span>#{{ $match->id }}</span>
                </div>
                <div class="cm-info-item">
                    <label>Court</label>
                    <span>{{ $match->court_no }}</span>
                </div>
                <div class="cm-info-item">
                    <label>Round</label>
                    <span>{{ $match->getRoundLabel() }}</span>
                </div>
                <div class="cm-info-item">
                    <label>Division</label>
                    <span>{{ $match->division }}</span>
                </div>
                <div class="cm-info-item">
                    <label>Match Type</label>
                    <span>{{ ucfirst($match->match_type) }}</span>
                </div>
                <div class="cm-info-item">
                    <label>Umpire</label>
                    <span>{{ $match->umpire_name }}</span>
                </div>
                @if($match->scorer_name)
                <div class="cm-info-item">
                    <label>Scorer</label>
                    <span>{{ $match->scorer_name }}</span>
                </div>
                @endif
                @if($match->started_at)
                <div class="cm-info-item" style="grid-column:span 2;">
                    <label>Match Time</label>
                    <div class="cm-time-row">
                        <div class="cm-time-item">
                            <i class="fas fa-play-circle" style="color:#059669;"></i>
                            Start: {{ $match->started_at->format('g:i A') }}
                        </div>
                        @if($match->completed_at)
                        <div class="cm-time-item">
                            <i class="fas fa-stop-circle" style="color:#ef4444;"></i>
                            End: {{ $match->completed_at->format('g:i A') }}
                        </div>
                        <div class="cm-time-item">
                            @php
                                $diff = $match->started_at->diff($match->completed_at);
                                $mins = ($diff->h * 60) + $diff->i;
                                $secs = $diff->s;
                            @endphp
                            <i class="fas fa-clock" style="color:#1a56db;"></i>
                            Duration: {{ $mins }} minutes {{ $secs }} sec
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="cm-actions">
        <a href="{{ route('admin-matches.setup') }}" class="cm-btn cm-btn-primary">
            <i class="fas fa-plus"></i> New Match
        </a>
        <a href="{{ route('admin-matches.edit', $match->id) }}" class="cm-btn cm-btn-warning">
            <i class="fas fa-edit"></i> Edit Score & Winner
        </a>
        <a href="{{ route('admin-matches.bracket', ['type' => $match->match_type, 'division' => $match->division]) }}" class="cm-btn cm-btn-secondary">
            <i class="fas fa-sitemap"></i> View Bracket
        </a>
        <a href="{{ route('admin-matches.results') }}" class="cm-btn cm-btn-ghost">
            <i class="fas fa-list"></i> All Results
        </a>
    </div>

</div>
@endsection