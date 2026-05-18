@extends('Admin.layouts.app')
@section('title', 'Live — ' . $p1Name . ' vs ' . $p2Name)
@section('page-title', 'Live Scoreboard')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Inter:wght@400;600;700;800&display=swap');

    .live-wrap{max-width:820px;margin:0 auto;font-family:'Inter',sans-serif;}

    .live-topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;}
    .live-badge-live{display:inline-flex;align-items:center;gap:5px;background:#ef4444;color:#fff;font-size:.72rem;font-weight:700;padding:5px 12px;border-radius:20px;animation:blink 1.2s infinite;}
    @keyframes blink{0%,100%{opacity:1;}50%{opacity:.5;}}
    .live-meta-chips{display:flex;gap:8px;flex-wrap:wrap;}
    .live-meta-chip{font-size:.72rem;font-weight:600;color:#64748b;background:#f1f5f9;padding:4px 10px;border-radius:20px;}
    .live-meta-chip--round{background:#fef3c7;color:#d97706;}

    .live-timer-card{background:linear-gradient(135deg,#0f172a,#1e293b);border-radius:16px;padding:14px 20px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border:1px solid #1e3a5f;}
    .live-timer-item{display:flex;flex-direction:column;align-items:center;gap:3px;}
    .live-timer-label{font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#475569;}
    .live-timer-value{font-family:'Orbitron',monospace;font-size:.95rem;font-weight:700;color:#f1f5f9;}
    .live-timer-value--green{color:#34d399;}
    .live-timer-value--red{color:#f87171;}
    .live-timer-sep{width:1px;height:36px;background:#1e3a5f;}
    .live-elapsed{font-family:'Orbitron',monospace;font-size:1.5rem;font-weight:900;color:#fbbf24;letter-spacing:2px;}

    .live-sets-bar{display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;}
    .live-set-dot{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;border:2px solid #e2e8f0;color:#94a3b8;background:#fff;transition:all .2s;}
    .live-set-dot.won-p1{background:#1a56db;border-color:#1a56db;color:#fff;}
    .live-set-dot.won-p2{background:#ef4444;border-color:#ef4444;color:#fff;}
    .live-set-dot.current{border-color:#f59e0b;color:#d97706;background:#fef3c7;font-weight:900;animation:pulse-border 1s infinite;}
    @keyframes pulse-border{0%,100%{box-shadow:0 0 0 0 rgba(245,158,11,.4);}50%{box-shadow:0 0 0 6px rgba(245,158,11,.15);}}
    .live-sets-label{font-size:.72rem;font-weight:600;color:#94a3b8;}

    .live-board{background:linear-gradient(160deg,#0f172a 0%,#1e293b 60%,#0f2744 100%);border-radius:24px;padding:28px 24px;box-shadow:0 24px 80px rgba(0,0,0,.5);margin-bottom:16px;border:1px solid #1e3a5f;position:relative;overflow:hidden;}
    .live-board::before{content:'🏸';position:absolute;font-size:9rem;opacity:.04;right:-20px;top:-10px;transform:rotate(20deg);pointer-events:none;}

    .live-players{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:12px;margin-bottom:10px;}
    .live-player{text-align:center;}
    .live-player-name{font-size:1rem;font-weight:800;color:#f1f5f9;margin-bottom:3px;word-break:break-word;}
    .live-player-id{font-size:.63rem;color:#475569;font-family:'DM Mono',monospace;}
    .live-sets-won{font-size:.7rem;font-weight:700;margin-top:4px;}
    .live-sets-won--p1{color:#60a5fa;}
    .live-sets-won--p2{color:#f87171;}
    .live-vs{font-size:.65rem;font-weight:800;color:#334155;background:#1e293b;border:1px solid #334155;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;}

    .live-scores{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:10px;margin-bottom:10px;}
    .live-score-block{text-align:center;}
    .live-score{font-size:5.5rem;font-weight:900;color:#f1f5f9;line-height:1;font-family:'Orbitron',monospace;transition:transform .1s;}
    .live-score.bump{transform:scale(1.18);}
    .live-score--leading{color:#34d399;}
    .live-score-sep{font-size:2rem;color:#334155;font-weight:900;text-align:center;}
    .live-current-set-label{text-align:center;font-size:.75rem;font-weight:700;color:#f59e0b;margin-bottom:14px;letter-spacing:.5px;}

    .live-shuttle-divider{display:flex;align-items:center;gap:10px;margin:10px 0 16px;}
    .live-shuttle-divider::before,.live-shuttle-divider::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,transparent,#1e3a5f);}
    .live-shuttle-divider::after{background:linear-gradient(270deg,transparent,#1e3a5f);}
    .live-shuttle-icon{font-size:1rem;opacity:.5;}

    .live-sets-history{margin-bottom:16px;}
    .live-sets-history-title{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#334155;margin-bottom:8px;}
    .live-set-row{display:flex;align-items:center;gap:8px;background:#1e293b;border-radius:8px;padding:7px 12px;margin-bottom:5px;}
    .live-set-row .sr-label{font-size:.68rem;font-weight:600;color:#475569;width:50px;}
    .live-set-row .sr-score{font-family:'Orbitron',monospace;font-size:.82rem;font-weight:700;color:#f1f5f9;flex:1;text-align:center;}
    .live-set-row .sr-winner{font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px;}
    .live-set-row .sr-winner--p1{background:#1e3a8a;color:#93c5fd;}
    .live-set-row .sr-winner--p2{background:#3f0000;color:#fca5a5;}

    .live-controls{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;}
    .live-ctrl-col{display:flex;flex-direction:column;gap:8px;}
    .live-btn{display:flex;align-items:center;justify-content:center;gap:7px;padding:14px;border:none;border-radius:12px;font-size:.88rem;font-weight:700;cursor:pointer;transition:transform .1s,opacity .15s;width:100%;}
    .live-btn:active{transform:scale(.96);}
    .live-btn--add-p1{background:linear-gradient(135deg,#1a56db,#3b82f6);color:#fff;font-size:.95rem;}
    .live-btn--add-p2{background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;font-size:.95rem;}
    .live-btn--sub{background:#1e293b;color:#64748b;border:1px solid #334155;font-size:.8rem;}
    .live-btn--sub:hover{color:#94a3b8;}

    .live-declare-section{background:#0f172a;border:1px solid #1e3a5f;border-radius:14px;padding:16px;margin-bottom:12px;}
    .live-declare-title{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#475569;margin-bottom:12px;text-align:center;}
    .live-declare-btns{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .live-declare-btn{padding:13px;border:none;border-radius:10px;font-size:.88rem;font-weight:700;cursor:pointer;transition:all .15s;}
    .live-declare-btn--p1{background:linear-gradient(135deg,#1a56db,#3b82f6);color:#fff;}
    .live-declare-btn--p2{background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;}
    .live-declare-btn:hover{opacity:.9;transform:scale(.98);}

    .live-alert{display:none;align-items:center;justify-content:center;gap:10px;padding:13px 18px;border-radius:12px;font-size:.88rem;font-weight:700;margin-bottom:14px;animation:slideIn .3s ease;}
    .live-alert.show{display:flex;}
    @keyframes slideIn{from{transform:translateY(-8px);opacity:0;}to{transform:translateY(0);opacity:1;}}
    .live-alert--juice{background:#7c3aed;color:#fff;}
    .live-alert--oball{background:#d97706;color:#fff;}
    .live-alert--set{background:#059669;color:#fff;}

    .live-end-btn{width:100%;padding:11px;background:#1e293b;color:#ef4444;border:1px solid #3f2020;border-radius:10px;font-size:.83rem;font-weight:700;cursor:pointer;margin-top:10px;}
    .live-end-btn:hover{background:#3f2020;}

    .live-winner-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.87);z-index:9999;align-items:center;justify-content:center;flex-direction:column;gap:16px;}
    .live-winner-overlay.show{display:flex;}
    .live-winner-box{background:linear-gradient(135deg,#059669,#10b981);border-radius:24px;padding:36px 44px;text-align:center;box-shadow:0 0 80px rgba(16,185,129,.4);}
    .live-winner-box .trophy{font-size:3.2rem;margin-bottom:10px;}
    .live-winner-box h2{font-size:1.5rem;font-weight:900;color:#fff;margin-bottom:4px;}
    .live-winner-box p{font-size:.9rem;color:#a7f3d0;}
    .live-winner-sets{display:flex;gap:8px;justify-content:center;margin:12px 0;}
    .live-winner-set-chip{background:rgba(255,255,255,.2);color:#fff;border-radius:8px;padding:5px 12px;font-size:.8rem;font-weight:700;font-family:'Orbitron',monospace;}
    .live-winner-btn{background:#fff;color:#059669;border:none;border-radius:12px;padding:11px 28px;font-size:.88rem;font-weight:700;cursor:pointer;margin-top:8px;text-decoration:none;display:inline-block;}

    @media(max-width:480px){.live-score{font-size:3.5rem;}.live-board{padding:18px 12px;}}
</style>
@endsection

@section('content')
<div class="live-wrap">

    <div class="live-topbar">
        <span class="live-badge-live"><i class="fas fa-circle" style="font-size:.4rem;"></i> LIVE</span>
        <div class="live-meta-chips">
            <span class="live-meta-chip"><i class="fas fa-hashtag mr-1"></i>Match No: {{ $match->id }}</span>
            <span class="live-meta-chip"><i class="fas fa-map-marker-alt mr-1"></i>{{ $match->court_no }}</span>
            <span class="live-meta-chip"><i class="fas fa-users mr-1"></i>{{ ucfirst($match->match_type) }}</span>
            <span class="live-meta-chip"><i class="fas fa-tag mr-1"></i>{{ $match->division }}</span>
            <span class="live-meta-chip live-meta-chip--round"><i class="fas fa-trophy mr-1"></i>{{ $match->getRoundLabel() }}</span>
            <span class="live-meta-chip"><i class="fas fa-layer-group mr-1"></i>{{ $match->sets_to_win === 1 ? '1 Set' : 'Best of 3' }}</span>
        </div>
    </div>

    {{-- Timer --}}
    <div class="live-timer-card">
        <div class="live-timer-item">
            <span class="live-timer-label"><i class="fas fa-play-circle mr-1"></i> Started</span>
            <span class="live-timer-value live-timer-value--green" id="startTimeDisplay">
                {{ $match->started_at ? \Carbon\Carbon::parse($match->started_at)->format('g:i A') : '—' }}
            </span>
        </div>
        <div class="live-timer-sep"></div>
        <div class="live-timer-item">
            <span class="live-timer-label">⏱ Elapsed</span>
            <span class="live-elapsed" id="elapsedTimer">00:00</span>
        </div>
        <div class="live-timer-sep"></div>
        <div class="live-timer-item">
            <span class="live-timer-label"><i class="fas fa-stop-circle mr-1"></i> End Time</span>
            <span class="live-timer-value live-timer-value--red" id="endTimeDisplay">—</span>
        </div>
    </div>

    {{-- Alerts --}}
    <div class="live-alert live-alert--juice" id="alertJuice"><i class="fas fa-bolt"></i> JUICE! Both at 20+</div>
    <div class="live-alert live-alert--oball" id="alertOBall"><i class="fas fa-circle"></i> O-BALL!</div>
    <div class="live-alert live-alert--set" id="alertSet"><i class="fas fa-check-circle"></i> <span id="alertSetText">Set saved!</span> Next set starting...</div>

    {{-- Sets Bar --}}
    @if($match->sets_to_win > 1)
    <div class="live-sets-bar">
        <span class="live-sets-label">Sets:</span>
        @for($s = 1; $s <= $match->maxSets(); $s++)
        @php
            $setData = $completedSets->firstWhere('set_number', $s);
            $cls = '';
            if ($setData && $setData->winner === 'p1') $cls = 'won-p1';
            elseif ($setData && $setData->winner === 'p2') $cls = 'won-p2';
            elseif ($s == $match->current_set) $cls = 'current';
        @endphp
        <div class="live-set-dot {{ $cls }}" id="setDot{{ $s }}">{{ $s }}</div>
        @endfor
    </div>
    @endif

    <div class="live-board">
        <!-- <div class="live-players">
            <div class="live-player">
                <div class="live-player-name">{{ $p1Name }}</div>
                <div class="live-player-id">{{ $match->player1_id }}</div>
                @if($match->sets_to_win > 1)
                <div class="live-sets-won live-sets-won--p1" id="setsWonP1Label">Sets: {{ $match->sets_won_p1 }}</div>
                @endif
            </div>
            <div class="live-vs">VS</div>
            <div class="live-player">
                <div class="live-player-name">{{ $p2Name }}</div>
                <div class="live-player-id">{{ $match->player2_id }}</div>
                @if($match->sets_to_win > 1)
                <div class="live-sets-won live-sets-won--p2" id="setsWonP2Label">Sets: {{ $match->sets_won_p2 }}</div>
                @endif
            </div>
        </div> -->

        ```html
<div class="live-players">
    <div class="live-player">
        <div style="font-size:1.1rem;font-weight:800;color:#f59e0b;letter-spacing:1px;margin-bottom:3px;">
            {{ $p1->season_id ?? $match->player1_id }}
        </div>
        <div class="live-player-name">{{ $p1Name }}</div>
        @if(isset($p1))
        <div class="live-player-id">{{ $p1->state_name ?? '' }} · {{ $p1->age ?? '' }}</div>
        @endif
        @if($match->sets_to_win > 1)
        <div class="live-sets-won live-sets-won--p1" id="setsWonP1Label">Sets: {{ $match->sets_won_p1 }}</div>
        @endif
    </div>
    <div class="live-vs">VS</div>
    <div class="live-player">
        <div style="font-size:1.1rem;font-weight:800;color:#f59e0b;letter-spacing:1px;margin-bottom:3px;">
            {{ $p2->season_id ?? $match->player2_id }}
        </div>
        <div class="live-player-name">{{ $p2Name }}</div>
        @if(isset($p2))
        <div class="live-player-id">{{ $p2->state_name ?? '' }} · {{ $p2->age ?? '' }}</div>
        @endif
        @if($match->sets_to_win > 1)
        <div class="live-sets-won live-sets-won--p2" id="setsWonP2Label">Sets: {{ $match->sets_won_p2 }}</div>
        @endif
    </div>
</div>
```

        <div class="live-current-set-label" id="currentSetLabel">
            @if($match->sets_to_win > 1) Set {{ $match->current_set }} of {{ $match->maxSets() }} @endif
        </div>

        <div class="live-scores">
            <div class="live-score-block"><div class="live-score" id="score_p1">{{ $match->score_p1 }}</div></div>
            <div class="live-score-sep">—</div>
            <div class="live-score-block"><div class="live-score" id="score_p2">{{ $match->score_p2 }}</div></div>
        </div>

        <div class="live-shuttle-divider"><span class="live-shuttle-icon">🏸</span></div>

        {{-- Completed Sets History --}}
        <div class="live-sets-history" id="setsHistory">
            @if($completedSets->count() > 0)
            <div class="live-sets-history-title">Completed Sets</div>
            @foreach($completedSets as $cs)
            <div class="live-set-row">
                <span class="sr-label">Set {{ $cs->set_number }}</span>
                <span class="sr-score">{{ $cs->score_p1 }} — {{ $cs->score_p2 }}</span>
                <span class="sr-winner {{ $cs->winner === 'p1' ? 'sr-winner--p1' : 'sr-winner--p2' }}">
                    {{ $cs->winner === 'p1' ? $p1Name : $p2Name }}
                </span>
            </div>
            @endforeach
            @endif
        </div>

        {{-- Score buttons --}}
        <div class="live-controls">
            <div class="live-ctrl-col">
                <button class="live-btn live-btn--add-p1" onclick="updateScore('p1_plus')"><i class="fas fa-plus"></i> {{ $p1Name }}</button>
                <button class="live-btn live-btn--sub" onclick="updateScore('p1_minus')"><i class="fas fa-minus"></i> Undo</button>
            </div>
            <div class="live-ctrl-col">
                <button class="live-btn live-btn--add-p2" onclick="updateScore('p2_plus')"><i class="fas fa-plus"></i> {{ $p2Name }}</button>
                <button class="live-btn live-btn--sub" onclick="updateScore('p2_minus')"><i class="fas fa-minus"></i> Undo</button>
            </div>
        </div>

        {{-- Declare Winner --}}
        <div class="live-declare-section">
            <div class="live-declare-title">🏆 Declare Set / Match Winner</div>
            <div class="live-declare-btns">
                <button class="live-declare-btn live-declare-btn--p1" onclick="declareWinner('p1')">
                    <i class="fas fa-crown mr-1"></i> {{ $p1Name }} Wins
                </button>
                <button class="live-declare-btn live-declare-btn--p2" onclick="declareWinner('p2')">
                    <i class="fas fa-crown mr-1"></i> {{ $p2Name }} Wins
                </button>
            </div>
        </div>

        <button class="live-end-btn" onclick="confirmEnd()">
            <i class="fas fa-stop-circle mr-1"></i> End Match Manually (No Winner)
        </button>
    </div>
</div>

{{-- Winner Overlay --}}
<div class="live-winner-overlay" id="winnerOverlay">
    <div class="live-winner-box">
        <div class="trophy">🏆</div>
        <h2 id="winnerName">—</h2>
        <p>Wins the match!</p>
        <div class="live-winner-sets" id="winnerSets"></div>
        <a id="winnerBtn" href="#" class="live-winner-btn">View Summary</a>
    </div>
</div>
@endsection

@section('scripts')
<script>
const SCORE_URL   = "{{ route('admin-matches.update-score', $match->id) }}";
const DECLARE_URL = "{{ route('admin-matches.declare-winner', $match->id) }}";
const FORCE_URL   = "{{ route('admin-matches.force-end', $match->id) }}";
const DONE_URL    = "{{ route('admin-matches.complete', $match->id) }}";
const CSRF_TOKEN  = "{{ csrf_token() }}";
const P1_NAME     = @json($p1Name);
const P2_NAME     = @json($p2Name);
const MAX_SETS    = {{ $match->maxSets() }};
const STARTED_AT  = "{{ $match->started_at ? \Carbon\Carbon::parse($match->started_at)->toISOString() : '' }}";

// ── Timer ──────────────────────────────────────────────────────────────────
function formatTime(s) {
    const h = Math.floor(s/3600), m = Math.floor((s%3600)/60), sec = s%60;
    if (h > 0) return `${pad(h)}:${pad(m)}:${pad(sec)}`;
    return `${pad(m)}:${pad(sec)}`;
}
function pad(n) { return String(n).padStart(2,'0'); }
function formatClock(d) {
    let h = d.getHours(); const m = pad(d.getMinutes()), ap = h>=12?'PM':'AM';
    h = h%12||12; return `${h}:${m} ${ap}`;
}
if (STARTED_AT) {
    const start = new Date(STARTED_AT);
    setInterval(() => {
        document.getElementById('elapsedTimer').textContent = formatTime(Math.floor((new Date()-start)/1000));
    }, 1000);
}
function setEndTime() {
    document.getElementById('endTimeDisplay').textContent = formatClock(new Date());
}

// ── Score Update ───────────────────────────────────────────────────────────
async function updateScore(action) {
    try {
        const res  = await fetch(SCORE_URL, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
            body: JSON.stringify({action})
        });
        const data = await res.json();
        if (!res.ok) {
            if (data.error === 'Match already completed.') window.location.href = DONE_URL;
            else alert(data.error || 'Error');
            return;
        }

        setScore('score_p1', data.score_p1);
        setScore('score_p2', data.score_p2);

        document.getElementById('score_p1').classList.toggle('live-score--leading', data.score_p1 > data.score_p2);
        document.getElementById('score_p2').classList.toggle('live-score--leading', data.score_p2 > data.score_p1);

        showAlert('alertJuice', data.is_juice);
        showAlert('alertOBall', data.is_oball);

        // Update set dots
        updateSetDots(data.completed_sets, data.current_set);

    } catch(e) { console.error(e); }
}

// ── Declare Winner ─────────────────────────────────────────────────────────
async function declareWinner(player) {
    const name = player === 'p1' ? P1_NAME : P2_NAME;
    if (!confirm(`Declare ${name} as the winner of this set/match?`)) return;

    try {
        const res  = await fetch(DECLARE_URL, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
            body: JSON.stringify({winner: player})
        });
        const data = await res.json();

        if (data.match_over) {
            setEndTime();
            document.querySelectorAll('.live-btn, .live-declare-btn').forEach(b => b.disabled = true);
            document.getElementById('winnerName').textContent = data.winner_name;
            document.getElementById('winnerBtn').href = data.redirect;
            document.getElementById('winnerOverlay').classList.add('show');
        } else {
            // Show set winner alert
            const setWinName = player === 'p1' ? P1_NAME : P2_NAME;
            document.getElementById('alertSetText').textContent = setWinName + ' wins the set!';
            showAlert('alertSet', true);
            setTimeout(() => hideAlert('alertSet'), 3000);

            // Update sets won labels
            if (document.getElementById('setsWonP1Label')) {
                document.getElementById('setsWonP1Label').textContent = 'Sets: ' + data.sets_won_p1;
                document.getElementById('setsWonP2Label').textContent = 'Sets: ' + data.sets_won_p2;
            }

            // Update current set label
            if (document.getElementById('currentSetLabel') && MAX_SETS > 1) {
                document.getElementById('currentSetLabel').textContent = 'Set ' + data.current_set + ' of ' + MAX_SETS;
            }

            // Update set dots
            updateSetDots(data.completed_sets, data.current_set);

            // Reset scores to 0 — use server confirmed values
            setScore('score_p1', data.score_p1 ?? 0);
            setScore('score_p2', data.score_p2 ?? 0);

            // Remove leading highlight from both scores
            document.getElementById('score_p1').classList.remove('live-score--leading');
            document.getElementById('score_p2').classList.remove('live-score--leading');

            // Update completed sets history
            renderSetsHistory(data.completed_sets);
        }
    } catch(e) { console.error(e); }
}

// ── Force End ──────────────────────────────────────────────────────────────
function confirmEnd() {
    if (confirm('End this match manually? No winner will be declared.')) {
        fetch(FORCE_URL, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
        }).then(() => { setEndTime(); window.location.href = DONE_URL; });
    }
}

// ── Helpers ────────────────────────────────────────────────────────────────
function setScore(id, val) {
    const el = document.getElementById(id);
    el.textContent = val;
    el.classList.add('bump');
    setTimeout(() => el.classList.remove('bump'), 150);
}

function showAlert(id, show) {
    const el = document.getElementById(id);
    if (show) { el.classList.add('show'); setTimeout(() => el.classList.remove('show'), 3500); }
    else el.classList.remove('show');
}
function hideAlert(id) { document.getElementById(id).classList.remove('show'); }

// ── Update set dots ────────────────────────────────────────────────────────
function updateSetDots(sets, currentSet) {
    for (let i = 1; i <= MAX_SETS; i++) {
        const dot = document.getElementById('setDot' + i);
        if (!dot) continue;
        dot.className = 'live-set-dot';
        const s = sets.find(x => x.set_number === i);
        if (s && s.winner === 'p1')      dot.classList.add('won-p1');
        else if (s && s.winner === 'p2') dot.classList.add('won-p2');
        else if (i === currentSet)       dot.classList.add('current');
    }
}

// ── Render completed sets history ──────────────────────────────────────────
function renderSetsHistory(sets) {
    const container = document.getElementById('setsHistory');
    if (!container) return;

    if (!sets || sets.length === 0) {
        container.innerHTML = '';
        return;
    }

    let html = '<div class="live-sets-history-title">Completed Sets</div>';
    sets.forEach(function(s) {
        if (!s.winner) return; // skip pending sets
        const isP1 = s.winner === 'p1';
        const winnerName = isP1 ? P1_NAME : P2_NAME;
        const winnerClass = isP1 ? 'sr-winner--p1' : 'sr-winner--p2';
        html += `
            <div class="live-set-row">
                <span class="sr-label">Set ${s.set_number}</span>
                <span class="sr-score">${s.score_p1} — ${s.score_p2}</span>
                <span class="sr-winner ${winnerClass}">${winnerName}</span>
            </div>`;
    });

    container.innerHTML = html;
}

// ── Highlight correct dot on page load ────────────────────────────────────
updateSetDots(
    @json($completedSets->map(fn($s) => ['set_number' => $s->set_number, 'winner' => $s->winner])),
    {{ $match->current_set }}
);
</script>
@endsection