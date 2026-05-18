@extends('Admin.layouts.app')
@section('title', 'Tournament Bracket')
@section('page-title', 'Tournament Bracket')

@section('styles')
<style>
    .br-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
    .br-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}
    .br-filter-bar{display:flex;gap:10px;margin-bottom:24px;flex-wrap:wrap;}
    .br-filter-btn{padding:7px 16px;border-radius:8px;font-size:.8rem;font-weight:600;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;cursor:pointer;text-decoration:none;transition:all .15s;}
    .br-filter-btn.active,.br-filter-btn:hover{border-color:#1a56db;background:#eff6ff;color:#1a56db;text-decoration:none;}
    .br-filter-sep{width:1px;background:#e2e8f0;margin:0 4px;}

    .br-bracket{display:flex;gap:0;overflow-x:auto;padding-bottom:12px;}
    .br-round{display:flex;flex-direction:column;min-width:220px;}
    .br-round-title{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;padding:0 12px 12px;text-align:center;}
    .br-round-title.qf{color:#3b82f6;}
    .br-round-title.sf{color:#8b5cf6;}
    .br-round-title.fn{color:#f59e0b;}
    .br-matches{display:flex;flex-direction:column;justify-content:space-around;flex:1;gap:16px;padding:0 8px;}

    .br-match{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.06);overflow:hidden;position:relative;}
    .br-match-band{height:3px;}
    .br-match--live   .br-match-band{background:#ef4444;animation:blink 1.2s infinite;}
    .br-match--done   .br-match-band{background:#10b981;}
    .br-match--pending .br-match-band{background:#e2e8f0;}
    @keyframes blink{0%,100%{opacity:1;}50%{opacity:.4;}}

    .br-match-body{padding:12px;}
    .br-player{display:flex;align-items:center;gap:8px;padding:5px 0;}
    .br-player + .br-player{border-top:1px solid #f1f5f9;}
    .br-player-name{font-size:.8rem;font-weight:600;color:#1e293b;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
    .br-player-score{font-family:'DM Mono',monospace;font-size:.82rem;font-weight:700;color:#1e293b;flex-shrink:0;}
    .br-player.winner .br-player-name{color:#059669;}
    .br-player.winner .br-player-score{color:#059669;}
    .br-crown{font-size:.6rem;color:#f59e0b;flex-shrink:0;}
    .br-player.loser .br-player-name{color:#94a3b8;text-decoration:line-through;}
    .br-player.loser .br-player-score{color:#94a3b8;}

    .br-match-footer{font-size:.62rem;color:#94a3b8;padding:4px 12px 8px;display:flex;justify-content:space-between;}

    .br-tbd{color:#94a3b8;font-size:.78rem;font-style:italic;padding:8px 4px;}

    .br-connector{display:flex;flex-direction:column;justify-content:space-around;width:24px;padding:0;}
    .br-connector-line{flex:1;border-right:2px solid #e2e8f0;margin:8px 0;}

    .br-empty{text-align:center;padding:48px;color:#94a3b8;font-size:.875rem;background:#fff;border-radius:14px;}
    .br-empty i{font-size:2rem;opacity:.25;display:block;margin-bottom:12px;}
</style>
@endsection

@section('content')
<div class="br-header">
    <div>
        <h2>Tournament Bracket</h2>
        <p>Knockout draw for {{ ucfirst($matchType) }} — {{ $division }}</p>
    </div>
    <a href="{{ route('admin-matches.index') }}" style="font-size:.8rem;color:#64748b;text-decoration:none;"><i class="fas fa-arrow-left mr-1"></i> All Matches</a>
</div>

{{-- Filters --}}
<div class="br-filter-bar">
    @foreach(['singles','doubles'] as $t)
        @foreach(['U-11','U-13','U-15','U-19'] as $d)
        <a href="{{ route('admin-matches.bracket', ['type'=>$t,'division'=>$d]) }}"
           class="br-filter-btn {{ $matchType===$t && $division===$d ? 'active' : '' }}">
            {{ ucfirst($t) }} {{ $d }}
        </a>
        @endforeach
        @if(!$loop->last)<div class="br-filter-sep"></div>@endif
    @endforeach
</div>

@if($matches->isEmpty())
    <div class="br-empty"><i class="fas fa-sitemap"></i>No matches found for {{ ucfirst($matchType) }} {{ $division }} yet.</div>
@else
<div class="br-bracket">

    @php
        $roundOrder = ['quarter_final', 'semi_final', 'final'];
        $roundTitleClass = ['quarter_final'=>'qf','semi_final'=>'sf','final'=>'fn'];
        $roundLabels = ['quarter_final'=>'Knock Out','semi_final'=>'Semi Finals','final'=>'Final'];
    @endphp

    @foreach($roundOrder as $round)
        @if(isset($matches[$round]) && $matches[$round]->count() > 0)
        <div class="br-round">
            <div class="br-round-title {{ $roundTitleClass[$round] }}">{{ $roundLabels[$round] }}</div>
            <div class="br-matches">
                @foreach($matches[$round] as $m)
                @php
                    $isLive    = $m->status === 'live';
                    $isDone    = $m->status === 'completed';
                    $isPending = $m->status === 'setup';
                    $statusCls = $isDone ? 'done' : ($isLive ? 'live' : 'pending');
                @endphp
                <div class="br-match br-match--{{ $statusCls }}">
                    <div class="br-match-band"></div>
                    <div class="br-match-body">
                        @if($m->player1_id)
                            <div class="br-player {{ $isDone && $m->winner_id === $m->player1_id ? 'winner' : ($isDone ? 'loser' : '') }}">
                                @if($isDone && $m->winner_id === $m->player1_id)<i class="fas fa-crown br-crown"></i>@endif
                                <span class="br-player-name">{{ $m->p1_name }}</span>
                                <span class="br-player-score">{{ $m->score_p1 }}</span>
                            </div>
                            <div class="br-player {{ $isDone && $m->winner_id === $m->player2_id ? 'winner' : ($isDone ? 'loser' : '') }}">
                                @if($isDone && $m->winner_id === $m->player2_id)<i class="fas fa-crown br-crown"></i>@endif
                                <span class="br-player-name">{{ $m->p2_name }}</span>
                                <span class="br-player-score">{{ $m->score_p2 }}</span>
                            </div>
                        @else
                            <div class="br-tbd">TBD</div>
                        @endif
                    </div>
                    <div class="br-match-footer">
                        <span>{{ $m->court_no }}</span>
                        <span>
                            @if($isLive)<span style="color:#ef4444;font-weight:700;">● LIVE</span>
                            @elseif($isDone)<span style="color:#059669;">✓ Done</span>
                            @else<span>Pending</span>@endif
                        </span>
                    </div>
                    @if($isLive)
                    <a href="{{ route('admin-matches.live', $m->id) }}" style="display:block;text-align:center;background:#fee2e2;color:#dc2626;font-size:.72rem;font-weight:700;padding:5px;text-decoration:none;">
                        <i class="fas fa-play mr-1"></i> Go Live
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Connector lines between rounds --}}
        @if(!$loop->last)
        <div class="br-connector">
            @foreach($matches[$round] as $m)
            <div class="br-connector-line"></div>
            @endforeach
        </div>
        @endif

        @endif
    @endforeach

</div>
@endif
@endsection
