@extends('user.layouts.app')

@section('title', 'My Dashboard')

@section('content')

{{-- PAGE HEADING --}}
<div class="dash-hero">
    <div class="dash-hero-left">
        <div class="dash-avatar">
            <span>{{ strtoupper(substr(session('player_name', 'P'), 0, 1)) }}</span>
        </div>
        <div>
            <div class="dash-welcome">Welcome back</div>
            <h1 class="dash-name">{{ session('player_name') }}</h1>
            <span class="dash-pid"><i class="fas fa-id-badge"></i> {{ session('player_id') }}</span>
        </div>
    </div>
    <div class="dash-hero-right">
        <span class="dash-status-dot"></span>
        <span class="dash-status-text">Active Player</span>
    </div>
</div>

@if(session('success'))
    <div class="dash-alert" role="alert">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="dash-alert-close">&times;</button>
    </div>
@endif

{{-- STAT CARDS --}}
@php $winRate = $matchesPlayed > 0 ? round(($wins / $matchesPlayed) * 100) : 0; @endphp
<div class="stat-grid">
    <div class="stat-card stat-card--blue">
        <div class="stat-icon"><i class="fas fa-table-tennis"></i></div>
        <div class="stat-info">
            <div class="stat-label">Matches Played</div>
            <div class="stat-value">{{ $matchesPlayed }}</div>
        </div>
    </div>
    <div class="stat-card stat-card--green">
        <div class="stat-icon"><i class="fas fa-trophy"></i></div>
        <div class="stat-info">
            <div class="stat-label">Wins</div>
            <div class="stat-value">{{ $wins }}</div>
        </div>
    </div>
    <div class="stat-card stat-card--red">
        <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
        <div class="stat-info">
            <div class="stat-label">Losses</div>
            <div class="stat-value">{{ $losses }}</div>
        </div>
    </div>
    <div class="stat-card stat-card--amber">
        <div class="stat-icon"><i class="fas fa-percentage"></i></div>
        <div class="stat-info">
            <div class="stat-label">Win Rate</div>
            <div class="stat-value">{{ $winRate }}%</div>
        </div>
        <div class="stat-progress-track">
            <div class="stat-progress-fill" style="width:{{ $winRate }}%"></div>
        </div>
    </div>
</div>

{{-- LIVE MATCHES --}}
<div id="liveMatchesSection" class="section-card live-section" style="display:none;">
    <div class="section-header section-header--live">
        <div class="section-header-left">
            <span class="live-dot"></span>
            <span class="section-title" style="color:#d97706;">Live Matches</span>
            <span class="live-badge" id="liveCount"></span>
        </div>
        <small class="text-muted" id="lastUpdated"></small>
    </div>
    <div id="liveMatchesBody" class="section-body"></div>
</div>

{{-- TOURNAMENT WINNERS --}}
@if($winnerMatches->isNotEmpty())
<div class="section-card winners-section">
    <div class="section-header">
        <div class="section-header-left">
            <div class="tw-icon"><i class="fas fa-trophy"></i></div>
            <div>
                <div class="section-title">Tournament Winners</div>
                <div class="section-sub">Congratulations to all champions!</div>
            </div>
        </div>
        <div class="carousel-controls">
            <button class="carousel-btn" id="prevBtn" onclick="carouselPrev()"><i class="fas fa-chevron-left"></i></button>
            <span class="carousel-dots" id="carouselDots"></span>
            <button class="carousel-btn" id="nextBtn" onclick="carouselNext()"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <div class="carousel-wrapper">
        <div class="carousel-track" id="carouselTrack">
            @foreach($winnerMatches as $w)
            @php
                $isDoubles = $w->match_type !== 'singles';
                if ($isDoubles) {
                    $doublesPlayers = \App\Models\Player::where('season_id', $w->winner_id)->where('mode', 'doubles')->get();
                    $p1 = $doublesPlayers->first();
                    $p2 = $doublesPlayers->skip(1)->first();
                } else {
                    $p1 = \App\Models\Player::where('player_id', $w->winner_id)->first();
                    $p2 = null;
                }
            @endphp
            <div class="carousel-slide">
                <div class="tw-winner-card">
                    <div class="tw-winner-topbar"></div>
                    <div class="tw-winner-tournament">
                       
                        {{ $w->tournament_name ?? 'Ekalavya Badminton Tournament' }}
                        <br>
                        {{ $w->tournament_name ?? 'Season 1' }}
                    </div>

                    @if($isDoubles)
                    <div class="tw-doubles-photos">
                        <div class="tw-photo-wrap tw-photo-wrap--left">
                            <div class="tw-photo-ring tw-photo-ring--p1">
                                @if($p1?->profile_photo)
                                    <img src="{{ asset('storage/' . $p1->profile_photo) }}" alt="{{ $p1->name }}" class="tw-photo" />
                                @else
                                    <div class="tw-photo-placeholder tw-photo-placeholder--p1"><i class="fas fa-user"></i></div>
                                @endif
                            </div>
                        </div>
                        <div class="tw-photo-wrap tw-photo-wrap--right">
                            <div class="tw-photo-ring tw-photo-ring--p2">
                                @if($p2?->profile_photo)
                                    <img src="{{ asset('storage/' . $p2->profile_photo) }}" alt="{{ $p2->name }}" class="tw-photo" />
                                @else
                                    <div class="tw-photo-placeholder tw-photo-placeholder--p2"><i class="fas fa-user"></i></div>
                                @endif
                            </div>
                        </div>
                        <div class="tw-trophy-badge tw-trophy-badge--doubles"><i class="fas fa-trophy"></i></div>
                    </div>
                    @else
                    <div class="tw-photo-wrap">
                        <div class="tw-photo-ring">
                            @if($p1?->profile_photo)
                                <img src="{{ asset('storage/' . $p1->profile_photo) }}" alt="{{ $p1->name }}" class="tw-photo" />
                            @else
                                <div class="tw-photo-placeholder"><i class="fas fa-user"></i></div>
                            @endif
                        </div>
                        <div class="tw-trophy-badge"><i class="fas fa-trophy"></i></div>
                    </div>
                    @endif

                    <div class="tw-badges">
                        <span class="tw-badge tw-badge--div">{{ $w->division }}</span>
                        <span class="tw-badge tw-badge--type">{{ $isDoubles ? 'Doubles' : 'Singles' }}</span>
                    </div>

                    @if($isDoubles)
                        <div class="tw-name">{{ $p1?->name ?? '—' }}</div>
                        <div class="tw-partner-name"><i class="fas fa-handshake mr-1"></i>&amp; {{ $p2?->name ?? '—' }}</div>
                    @else
                        <div class="tw-name">{{ $p1?->name ?? $w->winner_name }}</div>
                    @endif

                    <div class="tw-id-stack">
                        @if($isDoubles)
                            @if($p1)
                            <div class="tw-id-row">
                                <span class="tw-id-label"><i class="fas fa-user" style="color:#1a56db;"></i> P1</span>
                                <span class="tw-id-value">{{ $p1->player_id }}</span>
                            </div>
                            @endif
                            @if($p2)
                            <div class="tw-id-row">
                                <span class="tw-id-label"><i class="fas fa-user" style="color:#7c3aed;"></i> P2</span>
                                <span class="tw-id-value">{{ $p2->player_id }}</span>
                            </div>
                            @endif
                        @else
                            <div class="tw-id-row">
                                <span class="tw-id-label">ID</span>
                                <span class="tw-id-value">{{ $w->winner_id }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Mobile swipe hint shown once --}}
    <div class="swipe-hint" id="swipeHint">
        <i class="fas fa-hand-point-right"></i> Swipe to see more winners
    </div>
</div>
@endif

{{-- MATCH HISTORY --}}
<div class="section-card" id="match-history">
    <div class="section-header">
        <div class="section-header-left">
            <div class="tw-icon tw-icon--blue"><i class="fas fa-history"></i></div>
            <div class="section-title">Match History</div>
            @if($matchesPlayed > 0)
                <span class="live-badge" style="background:#dbeafe;color:#1d4ed8;">{{ $matchesPlayed }} played</span>
            @endif
        </div>
    </div>
    <div class="section-body p-0">
        @forelse($matches as $index => $match)
        @php $isLatest = $index === 0; @endphp
        <div class="mh-card {{ $isLatest ? 'mh-card--latest' : '' }}">
            <div class="mh-header" onclick="toggleMatch({{ $index }})">
                <div class="mh-header-left">
                    @if($match->result === 'win')
                        <span class="mh-result mh-result--win"><i class="fas fa-trophy"></i> Win</span>
                    @elseif($match->result === 'loss')
                        <span class="mh-result mh-result--loss"><i class="fas fa-times"></i> Loss</span>
                    @elseif($match->result === 'live')
                        <span class="mh-result mh-result--live"><span class="live-dot-sm"></span> Live</span>
                    @else
                        <span class="mh-result mh-result--pending">Pending</span>
                    @endif
                    <div class="mh-match-info">
                        <span class="mh-opponent">vs {{ $match->opponent }}</span>
                        <span class="mh-round-chip">{{ $match->round }}</span>
                    </div>
                </div>
                <div class="mh-header-right">
                    <span class="mh-score-inline">{{ $match->score }}</span>
                    <span class="mh-date">{{ $match->played_at ? $match->played_at->format('d M Y') : '—' }}</span>
                    <i class="fas fa-chevron-{{ $isLatest ? 'up' : 'down' }} mh-toggle-icon" id="icon-{{ $index }}"></i>
                </div>
            </div>

            <div class="mh-body" id="mh-body-{{ $index }}" style="{{ $isLatest ? '' : 'display:none;' }}">
                <div class="mh-chips">
                    <span class="mh-chip mh-chip--blue"><i class="fas fa-users"></i> {{ ucfirst($match->match_type) }}</span>
                    <span class="mh-chip mh-chip--green"><i class="fas fa-tag"></i> {{ $match->division }}</span>
                    <span class="mh-chip mh-chip--gray"><i class="fas fa-map-marker-alt"></i> Court {{ $match->court_no ?? '—' }}</span>
                </div>

                <div class="mh-scoreboard">
                    <div class="mh-sb-player {{ $match->result === 'win' ? 'mh-sb-player--winner' : '' }}">
                        <div class="mh-sb-label">You</div>
                        <div class="mh-sb-score">{{ explode(' – ', $match->score)[0] ?? '—' }}</div>
                    </div>
                    <div class="mh-sb-sep">—</div>
                    <div class="mh-sb-player {{ $match->result === 'loss' ? 'mh-sb-player--winner' : '' }}">
                        <div class="mh-sb-label">{{ $match->opponent }}</div>
                        <div class="mh-sb-score">{{ explode(' – ', $match->score)[1] ?? '—' }}</div>
                    </div>
                </div>

                @if($match->sets !== '—' && !empty($match->sets))
                <div class="mh-sets-row">
                    <span class="mh-sets-label">Sets:</span>
                    <span class="mh-sets-val">{{ $match->sets }}</span>
                </div>
                @endif

                <div class="mh-time-row">
                    <span class="mh-time-pill mh-time-pill--date">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $match->played_at ? $match->played_at->format('d M Y') : '—' }}
                    </span>
                    @if($match->started_at)
                        <span class="mh-time-pill mh-time-pill--start">
                            <i class="fas fa-play-circle"></i> {{ $match->started_at->format('g:i A') }}
                        </span>
                        <span class="mh-time-arrow"><i class="fas fa-arrow-right"></i></span>
                        <span class="mh-time-pill mh-time-pill--end">
                            <i class="fas fa-stop-circle"></i>
                            {{ $match->completed_at ? $match->completed_at->format('g:i A') : '—' }}
                        </span>
                        @if($match->completed_at)
                            @php
                                $diff = $match->started_at->diff($match->completed_at);
                                $totalMins = ($diff->h * 60) + $diff->i;
                                $secs = $diff->s;
                            @endphp
                            <span class="mh-time-pill mh-time-pill--duration">
                                <i class="fas fa-stopwatch"></i> {{ $totalMins }}m {{ $secs }}s
                            </span>
                        @else
                            <span class="mh-time-pill mh-time-pill--duration">
                                <i class="fas fa-stopwatch"></i> In Progress
                            </span>
                        @endif
                    @else
                        <span class="mh-time-pill mh-time-pill--duration"><i class="fas fa-stopwatch"></i> —</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-table-tennis"></i>
            <p>No matches played yet.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ════════════ STYLES ════════════ --}}
<style>
/* ── Google Font ── */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap');

:root {
    --ff: 'Plus Jakarta Sans', sans-serif;
    --ff-mono: 'DM Mono', monospace;
    --radius: 16px;
    --radius-sm: 10px;
    --shadow: 0 2px 16px rgba(0,0,0,0.07);
    --shadow-md: 0 6px 32px rgba(0,0,0,0.10);
    --gold: #f59e0b;
    --gold-light: #fef3c7;
    --gold-border: #fde68a;
    --surface: #ffffff;
    --bg: #f4f6fb;
    --text: #0f172a;
    --text-muted: #64748b;
    --border: #e2e8f0;
}

/* ── Hero ── */
.dash-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--surface);
    border-radius: var(--radius);
    padding: 20px 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow);
    gap: 12px;
    flex-wrap: wrap;
}
.dash-hero-left { display: flex; align-items: center; gap: 14px; min-width: 0; }
.dash-avatar {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4e73df, #1a56db);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; font-weight: 800; color: #fff;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(78,115,223,0.35);
}
.dash-welcome { font-size: .72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
.dash-name { font-family: var(--ff); font-size: 1.25rem; font-weight: 800; color: var(--text); margin: 2px 0 4px; line-height: 1.2; word-break: break-word; }
.dash-pid { font-size: .72rem; font-weight: 600; color: #4e73df; background: #eef2ff; padding: 2px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px; }
.dash-hero-right { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.dash-status-dot { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; animation: pulse-green 2s infinite; flex-shrink: 0; }
@keyframes pulse-green { 0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,.5);} 50%{box-shadow:0 0 0 6px rgba(34,197,94,0);} }
.dash-status-text { font-size: .72rem; font-weight: 700; color: #166534; }

/* ── Alert ── */
.dash-alert {
    display: flex; align-items: center; gap: 10px;
    background: #f0fdf4; border: 1.5px solid #bbf7d0;
    color: #166534; border-radius: var(--radius-sm);
    padding: 12px 16px; margin-bottom: 20px;
    font-size: .85rem; font-weight: 600;
}
.dash-alert-close { margin-left: auto; background: none; border: none; cursor: pointer; font-size: 1.1rem; color: #166534; line-height: 1; padding: 0 4px; }

/* ── Stat Grid ── */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
.stat-card {
    background: var(--surface);
    border-radius: var(--radius);
    padding: 18px 16px 14px;
    box-shadow: var(--shadow);
    display: flex;
    align-items: flex-start;
    gap: 12px;
    position: relative;
    overflow: hidden;
    transition: transform .2s;
}
.stat-card:hover { transform: translateY(-2px); }
.stat-card::before {
    content: ''; position: absolute;
    left: 0; top: 0; bottom: 0; width: 4px;
    border-radius: 2px 0 0 2px;
}
.stat-card--blue::before  { background: #4e73df; }
.stat-card--green::before { background: #1cc88a; }
.stat-card--red::before   { background: #e74a3b; }
.stat-card--amber::before { background: var(--gold); }
.stat-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.stat-card--blue  .stat-icon { background: #eef2ff; color: #4e73df; }
.stat-card--green .stat-icon { background: #f0fdf4; color: #1cc88a; }
.stat-card--red   .stat-icon { background: #fef2f2; color: #e74a3b; }
.stat-card--amber .stat-icon { background: #fffbeb; color: var(--gold); }
.stat-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); margin-bottom: 4px; }
.stat-value { font-size: 1.6rem; font-weight: 800; color: var(--text); line-height: 1; }
.stat-progress-track { position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: #fef3c7; }
.stat-progress-fill { height: 100%; background: linear-gradient(90deg, #fbbf24, #f59e0b); border-radius: 0 2px 2px 0; transition: width 1s ease; }

/* ── Section Cards ── */
.section-card {
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 20px;
    overflow: hidden;
    padding: 0; /* padding lives inside section-header / section-body */
}
.winners-section .carousel-wrapper { margin: 0; }
.section-header {
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1.5px solid var(--border);
    flex-wrap: wrap;
    gap: 10px;
}
.section-header-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; min-width: 0; }
.section-title { font-size: .95rem; font-weight: 800; color: var(--text); }
.section-sub { font-size: .72rem; color: var(--text-muted); margin-top: 1px; }
.section-body { padding: 16px 20px; }
.section-body.p-0 { padding: 0; }
.tw-icon {
    width: 36px; height: 36px; border-radius: 9px;
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; color: #fff;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(245,158,11,.3);
}
.tw-icon--blue { background: linear-gradient(135deg, #4e73df, #1a56db); box-shadow: 0 4px 10px rgba(78,115,223,.3); }

/* ── Live Section ── */
.live-section { border: 2px solid #fde68a; }
.section-header--live { background: #fffbf0; }
.live-dot {
    display: inline-block; width: 10px; height: 10px;
    background: #f59e0b; border-radius: 50%;
    animation: livepulse 1.4s infinite; flex-shrink: 0;
}
.live-dot-sm {
    display: inline-block; width: 7px; height: 7px;
    background: currentColor; border-radius: 50%;
    animation: livepulse 1.4s infinite; vertical-align: middle;
}
@keyframes livepulse {
    0%   { box-shadow: 0 0 0 0 rgba(245,158,11,.7); }
    70%  { box-shadow: 0 0 0 7px rgba(245,158,11,0); }
    100% { box-shadow: 0 0 0 0 rgba(245,158,11,0); }
}
.live-badge {
    font-size: .68rem; font-weight: 700;
    background: #fef3c7; color: #92400e;
    padding: 2px 10px; border-radius: 20px;
}

/* ── Live Match Grid ── */
.lm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; }
.lm-card { background: #fff; border-radius: 12px; border: 1.5px solid var(--border); overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.lm-card.my-match { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.25); }
.lm-tags { display: flex; align-items: center; flex-wrap: wrap; gap: 5px; padding: 10px 12px; border-bottom: 1px solid #f0f0f0; }
.lm-tag { font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; white-space: nowrap; }
.lm-tag.court  { background: #4e73df; color: #fff; }
.lm-tag.type   { background: #e8f4fd; color: #1d4ed8; border: 1px solid #bfdbfe; }
.lm-tag.round  { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.lm-tag.age    { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.lm-tag.mymatch{ background: #f59e0b; color: #fff; margin-left: auto; }
.lm-mid { display: flex; align-items: center; padding: 12px 10px; gap: 6px; }
.lm-player-block { flex: 1; min-width: 0; }
.lm-player-block .lm-season { font-size: .7rem; font-weight: 700; color: #4e73df; background: #eaf0fb; display: block; padding: 2px 6px; border-radius: 4px; margin-bottom: 3px; word-break: break-all; }
.lm-player-block .lm-name { font-size: .88rem; font-weight: 700; color: #1a1a2e; word-break: break-word; }
.lm-player-block .lm-city { font-size: .7rem; color: #6c757d; margin-top: 2px; word-break: break-word; }
.lm-player-block.right { text-align: right; }
.lm-vs { font-size: .68rem; font-weight: 700; color: #adb5bd; flex-shrink: 0; }
.lm-score-box { flex-shrink: 0; text-align: center; }
.lm-score-num { background: #2e3a4e; color: #fff; font-size: 1rem; font-weight: 900; letter-spacing: 2px; padding: 6px 10px; border-radius: 8px; display: inline-block; min-width: 70px; white-space: nowrap; }
.lm-sets { font-size: .66rem; color: #888; margin-top: 3px; }
.lm-footer { border-top: 1px solid #f0f0f0; padding: 7px 14px; font-size: .74rem; color: #888; }

/* ── Carousel ── */
.carousel-controls { display: flex; align-items: center; gap: 8px; }
.carousel-btn {
    width: 30px; height: 30px; border-radius: 50%;
    background: #f1f5f9; border: 1.5px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: .72rem; color: var(--text-muted);
    cursor: pointer; transition: all .18s;
    flex-shrink: 0;
}
.carousel-btn:hover { background: #4e73df; border-color: #4e73df; color: #fff; }
.carousel-dots { display: flex; gap: 5px; align-items: center; }
.carousel-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: #cbd5e1; cursor: pointer; transition: all .2s;
}
.carousel-dot.active { background: #4e73df; width: 18px; border-radius: 3px; }
.carousel-wrapper { overflow: hidden; padding: 0; }
.carousel-track {
    display: flex;
    transition: transform .4s cubic-bezier(.25,.46,.45,.94);
    will-change: transform;
}
.carousel-slide {
    flex: 0 0 auto;
    padding: 14px 10px;
    box-sizing: border-box;
}
.swipe-hint {
    text-align: center; font-size: .72rem; color: var(--text-muted);
    padding: 0 0 12px;
    animation: fadeSwipe 3s ease forwards;
}
@keyframes fadeSwipe { 0%{opacity:1;} 70%{opacity:1;} 100%{opacity:0;} }

/* ── Winner Card ── */
.tw-winner-card {
    position: relative;
    background: #fff;
    border: 1.5px solid var(--gold-border);
    border-radius: 16px;
    overflow: hidden;
    padding: 0 0 16px;
    display: flex; flex-direction: column; align-items: center;
    text-align: center;
    box-shadow: 0 2px 16px rgba(245,158,11,.12);
    transition: transform .22s, box-shadow .22s;
    width: 100%;
    box-sizing: border-box;
}
.tw-winner-card:hover { transform: translateY(-4px); box-shadow: 0 10px 32px rgba(245,158,11,.22); }
.tw-winner-topbar {
    width: 100%; height: 4px;
    background: linear-gradient(90deg, #fbbf24, #fde68a, #f59e0b, #fde68a, #fbbf24);
    background-size: 200% 100%;
    animation: tw-shimmer 2.2s infinite linear;
    margin-bottom: 12px; flex-shrink: 0;
}
@keyframes tw-shimmer { 0%{background-position:-200% 0;} 100%{background-position:200% 0;} }
.tw-winner-tournament {
    font-size: 0.5rem; font-weight: 700; color: #92400e;
    letter-spacing: .06em; text-transform: uppercase;
    background: var(--gold-light); border: 1px solid var(--gold-border);
    border-radius: 999px; padding: 3px 10px;
    margin: 0 10px 12px;
    max-width: calc(100% - 20px);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    display: flex; align-items: center; gap: 4px; justify-content: center;
}
.tw-winner-tournament i { font-size: .52rem; }
.tw-photo-wrap { position: relative; margin-bottom: 10px; flex-shrink: 0; }
.tw-photo-ring {
    width: 80px; height: 80px; border-radius: 50%;
    padding: 3px;
    background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
    box-shadow: 0 0 0 3px #fff, 0 4px 16px rgba(245,158,11,.28);
    overflow: hidden; display: flex; align-items: center; justify-content: center;
}
.tw-photo { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; object-position: top center; display: block; }
.tw-photo-placeholder {
    width: 100%; height: 100%; border-radius: 50%;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #d97706;
}
.tw-trophy-badge {
    position: absolute; bottom: -3px; right: -3px;
    width: 26px; height: 26px;
    background: linear-gradient(135deg, #fbbf24, #d97706);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: .62rem; color: #fff;
    border: 2.5px solid #fff; box-shadow: 0 2px 6px rgba(245,158,11,.5);
}
.tw-doubles-photos { position: relative; width: 130px; height: 76px; margin: 0 auto 10px; flex-shrink: 0; }
.tw-photo-wrap--left  { position: absolute; left: 0; top: 0; z-index: 2; }
.tw-photo-wrap--right { position: absolute; right: 0; top: 0; z-index: 1; }
.tw-photo-ring--p1 {
    width: 70px; height: 70px; border-radius: 50%; padding: 3px;
    background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
    box-shadow: 0 0 0 3px #fff, 0 4px 14px rgba(245,158,11,.28);
    overflow: hidden; display: flex; align-items: center; justify-content: center;
}
.tw-photo-ring--p2 {
    width: 70px; height: 70px; border-radius: 50%; padding: 3px;
    background: linear-gradient(135deg, #a78bfa, #7c3aed, #c4b5fd);
    box-shadow: 0 0 0 3px #fff, 0 4px 14px rgba(124,58,237,.22);
    overflow: hidden; display: flex; align-items: center; justify-content: center;
}
.tw-photo-placeholder--p1 { width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg, #fef3c7, #fde68a); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #d97706; }
.tw-photo-placeholder--p2 { width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg, #ede9fe, #ddd6fe); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #7c3aed; }
.tw-trophy-badge--doubles {
    position: absolute; bottom: -4px; left: 50%; transform: translateX(-50%); z-index: 3;
    width: 26px; height: 26px;
    background: linear-gradient(135deg, #fbbf24, #d97706);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: .62rem; color: #fff; border: 2.5px solid #fff; box-shadow: 0 2px 8px rgba(245,158,11,.5);
}
.tw-badges { display: flex; gap: 5px; flex-wrap: wrap; justify-content: center; margin-bottom: 8px; padding: 0 10px; }
.tw-badge { font-size: .62rem; font-weight: 700; padding: 3px 9px; border-radius: 999px; white-space: nowrap; }
.tw-badge--div  { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
.tw-badge--type { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.tw-name { font-size: .92rem; font-weight: 800; color: var(--text); padding: 0 12px; line-height: 1.3; margin-bottom: 3px; }
.tw-partner-name { font-size: .74rem; color: #64748b; padding: 0 12px; margin-bottom: 8px; display: flex; align-items: center; justify-content: center; gap: 4px; }
.tw-id-stack { display: flex; flex-direction: column; gap: 5px; align-items: center; width: calc(100% - 20px); margin: 4px 10px 0; }
.tw-id-row { display: flex; align-items: center; justify-content: space-between; width: 100%; background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; padding: 4px 10px; gap: 8px; }
.tw-id-label { font-size: .6rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; display: flex; align-items: center; gap: 4px; white-space: nowrap; }
.tw-id-label i { font-size: .52rem; }
.tw-id-value { font-size: .7rem; font-weight: 800; font-family: var(--ff-mono); color: var(--text); background: #e2e8f0; padding: 2px 7px; border-radius: 5px; letter-spacing: .03em; white-space: nowrap; }

/* ── Match History ── */
.mh-card { border-bottom: 1px solid var(--border); }
.mh-card:last-child { border-bottom: none; }
.mh-card--latest { background: #fffef7; }
.mh-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; cursor: pointer; gap: 8px; flex-wrap: wrap; transition: background .15s; }
.mh-header:hover { background: #f8fafc; }
.mh-header-left { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; min-width: 0; flex: 1; }
.mh-header-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.mh-match-info { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; min-width: 0; }
.mh-result { font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
.mh-result--win    { background: #d1fae5; color: #059669; }
.mh-result--loss   { background: #fee2e2; color: #dc2626; }
.mh-result--live   { background: #fef3c7; color: #d97706; }
.mh-result--pending{ background: #f1f5f9; color: #64748b; }
.mh-opponent   { font-size: .86rem; font-weight: 700; color: var(--text); word-break: break-word; }
.mh-round-chip { font-size: .66rem; font-weight: 600; background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 20px; white-space: nowrap; }
.mh-score-inline { font-size: .82rem; font-weight: 700; font-family: var(--ff-mono); color: var(--text); letter-spacing: 1px; white-space: nowrap; }
.mh-date       { font-size: .7rem; color: #94a3b8; white-space: nowrap; }
.mh-toggle-icon { font-size: .68rem; color: #94a3b8; }
.mh-body  { padding: 0 20px 18px; }
.mh-chips { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; padding-top: 6px; }
.mh-chip  { font-size: .68rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px; }
.mh-chip--blue  { background: #dbeafe; color: #1d4ed8; }
.mh-chip--green { background: #dcfce7; color: #166534; }
.mh-chip--gray  { background: #f1f5f9; color: #475569; }
.mh-scoreboard { display: flex; align-items: center; justify-content: center; gap: 16px; background: #0f172a; border-radius: 14px; padding: 18px; margin-bottom: 12px; }
.mh-sb-player  { text-align: center; }
.mh-sb-label   { font-size: .7rem; font-weight: 600; color: #64748b; margin-bottom: 4px; word-break: break-word; max-width: 90px; }
.mh-sb-score   { font-size: 2.2rem; font-weight: 900; font-family: var(--ff-mono); color: #475569; line-height: 1; }
.mh-sb-player--winner .mh-sb-score { color: #34d399; }
.mh-sb-sep     { font-size: 1.4rem; color: #334155; font-weight: 900; }
.mh-sets-row   { font-size: .78rem; color: #475569; margin-bottom: 8px; }
.mh-sets-label { font-weight: 700; margin-right: 6px; }
.mh-sets-val   { font-family: var(--ff-mono); }
.mh-time-row { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; padding: 10px 12px; background: #f8fafc; border-radius: 10px; border: 1px solid var(--border); margin-top: 10px; }
.mh-time-pill { display: inline-flex; align-items: center; gap: 5px; font-size: .7rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap; }
.mh-time-pill i { font-size: .62rem; }
.mh-time-pill--date     { background: #e0e7ff; color: #3730a3; }
.mh-time-pill--start    { background: #dcfce7; color: #166534; }
.mh-time-pill--end      { background: #fee2e2; color: #991b1b; }
.mh-time-pill--duration { background: #fef3c7; color: #92400e; font-weight: 700; }
.mh-time-arrow { color: #94a3b8; font-size: .72rem; flex-shrink: 0; }
.empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); }
.empty-state i { font-size: 2rem; display: block; margin-bottom: 8px; }
.empty-state p { font-size: .88rem; margin: 0; }

/* ════ RESPONSIVE ════ */

/* Tablet (2-col stat grid) */
@media (max-width: 900px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
}

/* Mobile (≤575px) */
@media (max-width: 575px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .stat-value { font-size: 1.3rem; }
    .stat-icon  { width: 36px; height: 36px; font-size: .9rem; }

    .dash-hero { padding: 14px 16px; }
    .dash-avatar { width: 44px; height: 44px; font-size: 1.1rem; }
    .dash-name  { font-size: 1.05rem; }

    .section-header { padding: 12px 14px; }
    .section-body   { padding: 12px 14px; }

    .tw-winner-card { width: 100%; }
    .tw-photo-ring  { width: 68px; height: 68px; }
    .tw-doubles-photos { width: 112px; height: 62px; }
    .tw-photo-ring--p1, .tw-photo-ring--p2 { width: 60px; height: 60px; }
    .tw-name { font-size: .82rem; }
    .tw-id-label { font-size: .58rem; }
    .tw-id-value { font-size: .66rem; }

    .mh-header { padding: 12px 14px; }
    .mh-body   { padding: 0 14px 14px; }
    .mh-scoreboard { padding: 14px 10px; gap: 10px; }
    .mh-sb-score   { font-size: 1.7rem; }
    .mh-sb-label   { font-size: .65rem; max-width: 72px; }

    .mh-time-row  { gap: 4px; padding: 8px 10px; }
    .mh-time-pill { font-size: .66rem; padding: 3px 8px; }

    .lm-grid { grid-template-columns: 1fr; }
    .carousel-controls { gap: 6px; }
    .carousel-btn { width: 26px; height: 26px; }

    .swipe-hint { display: block; }
}

/* Very small phones */
@media (max-width: 360px) {
    .stat-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
    .stat-card { padding: 14px 12px 10px; }
    .tw-winner-card { width: 100%; }
    .mh-sb-score { font-size: 1.5rem; }
}

/* Hide swipe hint on wider screens */
@media (min-width: 576px) {
    .swipe-hint { display: none; }
}
</style>

{{-- ════════════ SCRIPTS ════════════ --}}
<script>
/* ──────────── Live Matches ──────────── */
(function () {
    var mySlotId  = "{{ ($player && $player->mode === 'doubles') ? $player->season_id : session('player_id') }}";
    var refreshMs = 10000;

    function pad(n) { return n < 10 ? '0' + n : n; }
    function esc(s) {
        return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function updateTimestamp() {
        var now = new Date();
        var t   = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        var el  = document.getElementById('lastUpdated');
        if (el) el.innerHTML = '<i class="fas fa-sync-alt mr-1"></i>Updated ' + t;
    }

    function buildCards(matches) {
        if (!matches.length) return '<p class="empty-state"><i class="fas fa-table-tennis"></i><span>No live matches right now.</span></p>';
        var html = '<div class="lm-grid">';
        matches.forEach(function(lm) {
            var isMyMatch = (lm.player1_id === mySlotId || lm.player2_id === mySlotId);
            var s1style = lm.score_p1 > lm.score_p2 ? 'color:#f59e0b;' : '';
            var s2style = lm.score_p2 > lm.score_p1 ? 'color:#f59e0b;' : '';
            var scoreHtml = '<span style="'+s1style+'">'+lm.score_p1+'</span> &ndash; <span style="'+s2style+'">'+lm.score_p2+'</span>';
            html += '<div class="lm-card'+(isMyMatch?' my-match':'')+'">';
            html += '<div class="lm-tags"><span class="lm-tag court">Court '+esc(lm.court_no)+'</span><span class="lm-tag type">'+esc(lm.match_type)+'</span><span class="lm-tag round">'+esc(lm.round_label)+'</span><span class="lm-tag age">'+esc(lm.division)+'</span>'+(isMyMatch?'<span class="lm-tag mymatch">Your Match</span>':'')+'</div>';
            html += '<div class="lm-mid"><div class="lm-player-block"><div class="lm-season">'+esc(lm.p1_season_id)+'</div><div class="lm-name">'+esc(lm.p1_name)+'</div>'+(lm.p1_city?'<div class="lm-city"><i class="fas fa-map-marker-alt"></i> '+esc(lm.p1_city)+'</div>':'')+'</div>';
            html += '<div class="lm-vs">VS</div><div class="lm-score-box"><div class="lm-score-num">'+scoreHtml+'</div>'+(lm.sets_to_win==2?'<div class="lm-sets">'+lm.sets_won_p1+' – '+lm.sets_won_p2+'</div>':'')+'</div>';
            html += '<div class="lm-vs">VS</div><div class="lm-player-block right"><div class="lm-season">'+esc(lm.p2_season_id)+'</div><div class="lm-name">'+esc(lm.p2_name)+'</div>'+(lm.p2_city?'<div class="lm-city"><i class="fas fa-map-marker-alt"></i> '+esc(lm.p2_city)+'</div>':'')+'</div></div>';
            html += '<div class="lm-footer"><i class="fas fa-user-tie"></i> Umpire: '+esc(lm.umpire_name||'—')+'</div></div>';
        });
        html += '</div>';
        return html;
    }

    function refreshLive() {
        fetch('{{ route("user-live-scores") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var section = document.getElementById('liveMatchesSection');
            var body    = document.getElementById('liveMatchesBody');
            var badge   = document.getElementById('liveCount');
            if (!data.count) { section.style.display = 'none'; return; }
            section.style.display = '';
            badge.textContent = data.count + ' ongoing';
            body.innerHTML = buildCards(data.matches);
            updateTimestamp();
        })
        .catch(function(err) { console.warn('Live refresh failed:', err); });
    }

    refreshLive();
    setInterval(refreshLive, refreshMs);
})();

/* ──────────── Match History Toggle ──────────── */
function toggleMatch(index) {
    var body = document.getElementById('mh-body-' + index);
    var icon = document.getElementById('icon-' + index);
    if (body.style.display === 'none') {
        body.style.display = '';
        icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
    } else {
        body.style.display = 'none';
        icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
    }
}

/* ──────────── Carousel ──────────── */
(function () {
    var track  = document.getElementById('carouselTrack');
    if (!track) return;

    var slides      = track.querySelectorAll('.carousel-slide');
    var dotsEl      = document.getElementById('carouselDots');
    var total       = slides.length;
    var current     = 0;
    var slideWidth  = 0;
    var perView     = 1;

    function getPerView() {
        var w = window.innerWidth;
        if (w >= 992) return Math.min(total, 4);
        if (w >= 768) return Math.min(total, 3);
        if (w >= 480) return Math.min(total, 2);
        return 1;
    }

    function calcSlideWidth() {
        var wrapper = track.parentElement; // .carousel-wrapper, no padding
        perView = getPerView();
        slideWidth = Math.floor(wrapper.clientWidth / perView);
        slides.forEach(function (s) {
            s.style.width = slideWidth + 'px';
        });
    }

    function buildDots() {
        if (!dotsEl) return;
        dotsEl.innerHTML = '';
        var pages = Math.ceil(total / perView);
        for (var i = 0; i < pages; i++) {
            var d = document.createElement('span');
            d.className = 'carousel-dot' + (i === 0 ? ' active' : '');
            d.setAttribute('data-page', i);
            d.addEventListener('click', function () { goToPage(+this.getAttribute('data-page')); });
            dotsEl.appendChild(d);
        }
    }

    function updateDots() {
        if (!dotsEl) return;
        var page = Math.round(current / perView);
        dotsEl.querySelectorAll('.carousel-dot').forEach(function (d, i) {
            d.classList.toggle('active', i === page);
        });
    }

    function goTo(index) {
        var max = Math.max(0, total - perView);
        current = Math.max(0, Math.min(index, max));
        track.style.transform = 'translateX(-' + (current * slideWidth) + 'px)';
        updateDots();
    }

    function goToPage(page) { goTo(page * perView); }

    window.carouselNext = function () { goTo(current + perView); };
    window.carouselPrev = function () { goTo(current - perView); };

    function init() {
        calcSlideWidth();
        buildDots();
        goTo(0);
        // Hide swipe hint after 3s
        var hint = document.getElementById('swipeHint');
        if (hint) setTimeout(function () { hint.style.display = 'none'; }, 3500);
    }

    // Touch / swipe
    var touchStartX = 0;
    track.addEventListener('touchstart', function (e) { touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', function (e) {
        var delta = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(delta) > 40) { delta > 0 ? carouselNext() : carouselPrev(); }
    });

    var resizeTimer;
    function onWrapperResize() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            calcSlideWidth();
            buildDots();
            goTo(current);
        }, 80);
    }
    if (window.ResizeObserver) {
        new ResizeObserver(onWrapperResize).observe(track.parentElement);
    }
    window.addEventListener('resize', onWrapperResize);

    init();
})();
</script>

@endsection