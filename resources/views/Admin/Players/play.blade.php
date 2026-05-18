@extends('Admin.layouts.app')

@section('title', 'Play - Match Manager')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Barlow+Condensed:wght@400;600;700;800&family=Share+Tech+Mono&display=swap" rel="stylesheet">
<style>
    :root {
        --court-green:  #1a6b3c;
        --court-light:  #24a85e;
        --shuttle:      #f5c842;
        --net-white:    #e8f4ff;
        --score-red:    #e63946;
        --score-blue:   #2563eb;
        --dark-bg:      #0d1117;
        --panel-bg:     #161b22;
        --border:       #30363d;
        --text-main:    #e6edf3;
        --text-muted:   #8b949e;
    }

    body { background: var(--dark-bg) !important; color: var(--text-main) !important; }

    /* ── Page Shell ─────────────────────────────────────────── */
    .play-page { font-family: 'Barlow Condensed', sans-serif; min-height: 100vh; padding: 24px; }

    .play-header {
        display: flex; align-items: center; gap: 14px;
        margin-bottom: 28px; border-bottom: 2px solid var(--court-green); padding-bottom: 16px;
    }
    .play-header .shuttle-icon { font-size: 2rem; filter: drop-shadow(0 0 8px var(--shuttle)); }
    .play-header h1 { font-size: 2rem; font-weight: 800; letter-spacing: 2px; color: var(--shuttle); margin: 0; }
    .play-header span { font-size: 1rem; color: var(--text-muted); letter-spacing: 1px; }

    /* ── Setup Card ─────────────────────────────────────────── */
    .setup-card {
        background: var(--panel-bg);
        border: 1px solid var(--border);
        border-top: 3px solid var(--court-green);
        border-radius: 10px;
        padding: 28px;
        margin-bottom: 24px;
    }
    .setup-card h4 {
        font-family: 'Rajdhani', sans-serif;
        font-size: 1.1rem; font-weight: 700;
        color: var(--court-light); letter-spacing: 2px;
        text-transform: uppercase; margin-bottom: 20px;
        display: flex; align-items: center; gap: 8px;
    }

    /* ── Form Controls ──────────────────────────────────────── */
    .form-label { font-size: 0.78rem; font-weight: 600; letter-spacing: 1px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px; }
    .form-select, .form-control {
        background: #0d1117 !important; border: 1px solid var(--border) !important;
        color: var(--text-main) !important; border-radius: 6px;
        font-family: 'Barlow Condensed', sans-serif; font-size: 1rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .form-select:focus, .form-control:focus {
        border-color: var(--court-green) !important;
        box-shadow: 0 0 0 3px rgba(26,107,60,.25) !important;
    }
    .form-select option { background: #161b22; }

    /* ── Buttons ────────────────────────────────────────────── */
    .btn-court {
        background: var(--court-green); color: #fff; border: none;
        font-family: 'Barlow Condensed', sans-serif; font-weight: 700;
        letter-spacing: 1.5px; text-transform: uppercase; font-size: 1rem;
        padding: 10px 24px; border-radius: 6px; transition: background .2s, transform .1s;
    }
    .btn-court:hover { background: var(--court-light); color: #fff; transform: translateY(-1px); }
    .btn-shuttle { background: var(--shuttle); color: #1a1a1a; }
    .btn-shuttle:hover { background: #f0b800; color: #1a1a1a; }

    /* ── Player Preview ─────────────────────────────────────── */
    .preview-card {
        background: var(--panel-bg); border: 1px solid var(--border);
        border-radius: 10px; padding: 20px; margin-bottom: 24px;
        display: none;
    }
    .vs-row {
        display: grid; grid-template-columns: 1fr auto 1fr; gap: 16px;
        align-items: center; margin: 16px 0;
    }
    .team-box {
        background: #0d1117; border-radius: 8px; padding: 16px;
        border: 1px solid var(--border); text-align: center;
    }
    .team-box.team-a { border-top: 3px solid var(--score-red); }
    .team-box.team-b { border-top: 3px solid var(--score-blue); }
    .team-id { font-family: 'Share Tech Mono', monospace; font-size: 1rem; color: var(--shuttle); margin-bottom: 6px; }
    .team-name { font-size: 1.1rem; font-weight: 700; color: var(--text-main); line-height: 1.3; }
    .vs-badge { font-size: 1.8rem; font-weight: 800; color: var(--text-muted); text-align: center; }
    .meta-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
    .meta-pill {
        background: #0d1117; border: 1px solid var(--border); border-radius: 20px;
        padding: 4px 14px; font-size: 0.82rem; color: var(--text-muted);
        font-family: 'Rajdhani', sans-serif; font-weight: 600; letter-spacing: .5px;
    }
    .meta-pill span { color: var(--text-main); }

    /* ── Scoreboard ─────────────────────────────────────────── */
    #scoreboard { display: none; }

    .scoreboard-wrap {
        background: #0a1628;
        border: 2px solid var(--court-green);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 0 40px rgba(26,107,60,.3);
    }

    .sb-header {
        background: linear-gradient(135deg, var(--court-green), #0f4023);
        padding: 14px 24px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .sb-title { font-size: 1.3rem; font-weight: 800; letter-spacing: 2px; color: #fff; text-transform: uppercase; }
    .sb-meta { font-size: 0.85rem; color: rgba(255,255,255,.7); font-family: 'Rajdhani', sans-serif; }
    .sb-time { font-family: 'Share Tech Mono', monospace; font-size: 0.9rem; color: var(--shuttle); }

    .sb-court {
        display: grid; grid-template-columns: 1fr 80px 1fr;
        gap: 0; min-height: 360px;
    }

    /* Team side panels */
    .sb-team {
        padding: 24px 20px; display: flex; flex-direction: column;
        justify-content: space-between; align-items: center; text-align: center;
    }
    .sb-team-a { background: linear-gradient(180deg, rgba(230,57,70,.12) 0%, transparent 100%); border-right: 1px solid var(--border); }
    .sb-team-b { background: linear-gradient(180deg, rgba(37,99,235,.12) 0%, transparent 100%); border-left: 1px solid var(--border); }

    .sb-team-id { font-family: 'Share Tech Mono', monospace; font-size: 0.85rem; color: var(--shuttle); margin-bottom: 6px; }
    .sb-team-name { font-size: 1.15rem; font-weight: 700; color: var(--text-main); line-height: 1.4; margin-bottom: 20px; }

    .sb-score-display {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 7rem; font-weight: 800; line-height: 1;
        text-shadow: 0 0 30px currentColor;
        transition: all .2s;
    }
    .sb-team-a .sb-score-display { color: var(--score-red); }
    .sb-team-b .sb-score-display { color: var(--score-blue); }

    .sb-score-btn {
        width: 60px; height: 60px; border-radius: 50%; border: none; cursor: pointer;
        font-size: 1.8rem; font-weight: 700; margin-top: 20px;
        transition: transform .1s, box-shadow .2s;
        display: flex; align-items: center; justify-content: center;
    }
    .sb-score-btn:hover { transform: scale(1.1); }
    .sb-score-btn:active { transform: scale(0.95); }
    .btn-score-a { background: var(--score-red); color: #fff; box-shadow: 0 4px 20px rgba(230,57,70,.4); }
    .btn-score-b { background: var(--score-blue); color: #fff; box-shadow: 0 4px 20px rgba(37,99,235,.4); }

    /* Center column */
    .sb-center {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; padding: 10px 0; gap: 12px;
    }
    .sb-divider { width: 2px; flex: 1; background: repeating-linear-gradient(to bottom, var(--border) 0px, var(--border) 6px, transparent 6px, transparent 12px); }
    .sb-net { font-size: 1.5rem; }
    .sb-court-label {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.75rem; color: var(--text-muted);
        writing-mode: vertical-rl; text-orientation: mixed;
        letter-spacing: 2px;
    }

    /* Alert banner */
    .alert-banner {
        display: none; padding: 10px 24px; text-align: center;
        font-size: 1.2rem; font-weight: 800; letter-spacing: 3px;
        text-transform: uppercase; font-family: 'Barlow Condensed', sans-serif;
        animation: pulse-banner 1s ease-in-out infinite;
    }
    .alert-oball { background: linear-gradient(90deg, #7c3aed, #4f46e5); color: #fff; }
    .alert-juice  { background: linear-gradient(90deg, #d97706, #b45309); color: #fff; }
    @keyframes pulse-banner { 0%,100% { opacity:1; } 50% { opacity:.7; } }

    /* Winner overlay */
    .winner-overlay {
        display: none; padding: 20px 24px; text-align: center;
        background: linear-gradient(135deg, #065f46, #0f4023);
    }
    .winner-crown { font-size: 2.5rem; }
    .winner-text { font-size: 2rem; font-weight: 800; color: var(--shuttle); letter-spacing: 3px; text-transform: uppercase; }
    .winner-sub  { font-size: 1rem; color: rgba(255,255,255,.7); margin-top: 4px; }

    /* Undo button */
    .sb-actions { padding: 14px 24px; display: flex; gap: 10px; justify-content: flex-end; background: #0d1117; border-top: 1px solid var(--border); }

    /* ── Alert toast ────────────────────────────────────────── */
    .toast-wrap { position: fixed; top: 20px; right: 20px; z-index: 9999; }
    .toast-msg {
        background: var(--panel-bg); border: 1px solid var(--border);
        border-radius: 8px; padding: 12px 20px; margin-bottom: 8px;
        font-family: 'Rajdhani', sans-serif; font-size: 1rem;
        font-weight: 600; color: var(--text-main);
        box-shadow: 0 4px 20px rgba(0,0,0,.4);
        animation: slideIn .3s ease;
    }
    .toast-msg.error { border-left: 3px solid var(--score-red); }
    .toast-msg.success { border-left: 3px solid var(--court-green); }
    @keyframes slideIn { from { opacity:0; transform: translateX(30px); } to { opacity:1; transform: translateX(0); } }

    /* Score pop animation */
    @keyframes scorePop { 0%{transform:scale(1)} 40%{transform:scale(1.25)} 100%{transform:scale(1)} }
    .score-pop { animation: scorePop .25s ease; }

    /* ── Responsive ─────────────────────────────────────────── */
    @media(max-width:600px) {
        .sb-score-display { font-size: 4.5rem; }
        .sb-court { grid-template-columns: 1fr 40px 1fr; }
        .sb-team { padding: 14px 8px; }
    }
</style>
@endpush

@section('content')
<div class="play-page">

    <!-- Toast container -->
    <div class="toast-wrap" id="toastWrap"></div>

    <!-- Header -->
    <div class="play-header">
        <span class="shuttle-icon">🏸</span>
        <div>
            <h1>MATCH MANAGER</h1>
            <span>Ekalavya Badminton Tournament</span>
        </div>
    </div>

    <!-- ── SETUP FORM ──────────────────────────────────────── -->
    <div class="setup-card" id="setupForm">
        <h4><i class="fas fa-sliders-h"></i> Match Setup</h4>

        <div class="row g-3">
            <!-- Court No -->
            <div class="col-md-3 col-6">
                <label class="form-label">Court No</label>
                <select class="form-select" id="courtId">
                    <option value="">Select Court</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}">Court {{ $court->court_no }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Empire -->
            <div class="col-md-3 col-6">
                <label class="form-label">Empire Name</label>
                <select class="form-select" id="empireId">
                    <option value="">Select Empire</option>
                    @foreach($empires as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Scorer (optional) -->
            <div class="col-md-3 col-6">
                <label class="form-label">Scorer <small style="color:var(--text-muted)">(optional)</small></label>
                <select class="form-select" id="scorerId">
                    <option value="">— None —</option>
                    @foreach($scorers as $sc)
                        <option value="{{ $sc->id }}">{{ $sc->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category / Mode -->
            <div class="col-md-3 col-6">
                <label class="form-label">Category</label>
                <select class="form-select" id="mode">
                    <option value="">Select Category</option>
                    <option value="singles">Singles</option>
                    <option value="doubles">Doubles</option>
                </select>
            </div>

            <!-- Division / Age Group -->
            <div class="col-md-3 col-6">
                <label class="form-label">Division</label>
                <select class="form-select" id="age">
                    <option value="">Select Division</option>
                    <option value="U-13">U-13</option>
                    <option value="U-15">U-15</option>
                    <option value="U-19">U-19</option>
                </select>
            </div>

            <!-- Select Players Button -->
            <div class="col-md-3 col-6 d-flex align-items-end">
                <button class="btn btn-court w-100" id="btnSelectPlayers">
                    <i class="fas fa-random me-1"></i> Select Players
                </button>
            </div>
        </div>
    </div>

    <!-- ── PLAYER PREVIEW ─────────────────────────────────── -->
    <div class="preview-card" id="previewCard">
        <h4 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:var(--court-light);letter-spacing:2px;text-transform:uppercase;margin-bottom:16px;">
            <i class="fas fa-users me-2"></i> Match Preview
        </h4>

        <div class="vs-row">
            <div class="team-box team-a">
                <div class="team-id" id="previewTeamAId">—</div>
                <div class="team-name" id="previewTeamAName">—</div>
            </div>
            <div class="vs-badge">VS</div>
            <div class="team-box team-b">
                <div class="team-id" id="previewTeamBId">—</div>
                <div class="team-name" id="previewTeamBName">—</div>
            </div>
        </div>

        <div class="meta-pills" id="metaPills"></div>

        <div class="mt-4 d-flex gap-3">
            <button class="btn btn-court btn-shuttle" id="btnLetsPlay" style="font-size:1.1rem;padding:12px 32px;">
                🏸 LET'S PLAY
            </button>
            <button class="btn btn-court" id="btnReshuffle" style="background:#21262d;">
                <i class="fas fa-sync-alt me-1"></i> Reshuffle
            </button>
        </div>
    </div>

    <!-- ── SCOREBOARD ─────────────────────────────────────── -->
    <div id="scoreboard">
        <div class="scoreboard-wrap">

            <!-- Header -->
            <div class="sb-header">
                <div>
                    <div class="sb-title" id="sbTitle">U-13 · SINGLES</div>
                    <div class="sb-meta" id="sbEmpire">Empire: — &nbsp;|&nbsp; Court: —</div>
                </div>
                <div class="sb-time" id="sbTime">—</div>
            </div>

            <!-- Alert banner (O-Ball / Juice) -->
            <div class="alert-banner alert-oball" id="alertOball">⚡ O-BALL — Win by 2!</div>
            <div class="alert-banner alert-juice"  id="alertJuice">🍋 JUICE — Equal Points!</div>

            <!-- Main court -->
            <div class="sb-court">

                <!-- Team A -->
                <div class="sb-team sb-team-a">
                    <div>
                        <div class="sb-team-id" id="sbTeamAId">ALK001S</div>
                        <div class="sb-team-name" id="sbTeamAName">Player A</div>
                    </div>
                    <div class="sb-score-display" id="sbScoreA">0</div>
                    <button class="sb-score-btn btn-score-a" id="btnScoreA" onclick="addScore('A')">+</button>
                </div>

                <!-- Center net -->
                <div class="sb-center">
                    <div class="sb-divider"></div>
                    <span class="sb-net">🏸</span>
                    <div class="sb-court-label" id="sbCourtLabel">COURT 1</div>
                    <div class="sb-divider"></div>
                </div>

                <!-- Team B -->
                <div class="sb-team sb-team-b">
                    <div>
                        <div class="sb-team-id" id="sbTeamBId">ALK002S</div>
                        <div class="sb-team-name" id="sbTeamBName">Player B</div>
                    </div>
                    <div class="sb-score-display" id="sbScoreB">0</div>
                    <button class="sb-score-btn btn-score-b" id="btnScoreB" onclick="addScore('B')">+</button>
                </div>

            </div>

            <!-- Winner overlay -->
            <div class="winner-overlay" id="winnerOverlay">
                <div class="winner-crown">🏆</div>
                <div class="winner-text" id="winnerText">WINNER!</div>
                <div class="winner-sub" id="winnerSub">—</div>
            </div>

            <!-- Actions -->
            <div class="sb-actions">
                <button class="btn btn-court" style="background:#21262d;font-size:.85rem;" onclick="saveMatch()">
                    <i class="fas fa-save me-1"></i> Save & End Match
                </button>
                <button class="btn btn-court" style="background:#21262d;font-size:.85rem;" onclick="resetMatch()">
                    <i class="fas fa-redo me-1"></i> New Match
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── State ────────────────────────────────────────────────────────────────────
let matchData = null;

// ── CSRF ─────────────────────────────────────────────────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ── Toast ─────────────────────────────────────────────────────────────────────
function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast-msg ${type}`;
    el.textContent = msg;
    document.getElementById('toastWrap').prepend(el);
    setTimeout(() => el.remove(), 3500);
}

// ── Select Players ────────────────────────────────────────────────────────────
document.getElementById('btnSelectPlayers').addEventListener('click', fetchPlayers);
document.getElementById('btnReshuffle').addEventListener('click', fetchPlayers);

function fetchPlayers() {
    const mode     = document.getElementById('mode').value;
    const age      = document.getElementById('age').value;
    const courtId  = document.getElementById('courtId').value;
    const empireId = document.getElementById('empireId').value;

    if (!courtId)  { toast('Please select a court.',    'error'); return; }
    if (!empireId) { toast('Please select an empire.',  'error'); return; }
    if (!mode)     { toast('Please select a category.', 'error'); return; }
    if (!age)      { toast('Please select a division.', 'error'); return; }

    fetch('{{ route("admin-play-get-players") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ mode, age }),
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) { toast(data.message, 'error'); return; }
        matchData = { ...data.teams, mode, age,
            courtId, empireId,
            scorerId: document.getElementById('scorerId').value };
        renderPreview(data.teams);
    })
    .catch(() => toast('Server error. Try again.', 'error'));
}

function renderPreview(teams) {
    const toNames = (players) => players.map(p => p.name).join(' / ');

    document.getElementById('previewTeamAId').textContent   = teams.teamA.id;
    document.getElementById('previewTeamAName').textContent = toNames(teams.teamA.players);
    document.getElementById('previewTeamBId').textContent   = teams.teamB.id;
    document.getElementById('previewTeamBName').textContent = toNames(teams.teamB.players);

    // Meta pills
    const empireName  = document.getElementById('empireId').selectedOptions[0]?.text  ?? '—';
    const scorerName  = document.getElementById('scorerId').selectedOptions[0]?.text  ?? 'None';
    const courtText   = document.getElementById('courtId').selectedOptions[0]?.text   ?? '—';
    const modeText    = matchData.mode.charAt(0).toUpperCase() + matchData.mode.slice(1);
    const ageText     = matchData.age;

    document.getElementById('metaPills').innerHTML = `
        <div class="meta-pill">🏟 Court <span>${courtText}</span></div>
        <div class="meta-pill">👤 Empire <span>${empireName}</span></div>
        <div class="meta-pill">📋 Scorer <span>${scorerName}</span></div>
        <div class="meta-pill">🏸 <span>${modeText}</span></div>
        <div class="meta-pill">🎖 <span>${ageText}</span></div>
    `;

    document.getElementById('previewCard').style.display = 'block';
    document.getElementById('scoreboard').style.display  = 'none';
}

// ── Let's Play ────────────────────────────────────────────────────────────────
document.getElementById('btnLetsPlay').addEventListener('click', function() {
    if (!matchData) return;

    const teamAPlayers = matchData.teamA.players.map(p => p.name);
    const teamBPlayers = matchData.teamB.players.map(p => p.name);

    fetch('{{ route("admin-play-start-match") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({
            court_id:       matchData.courtId,
            empire_id:      matchData.empireId,
            scorer_id:      matchData.scorerId || null,
            mode:           matchData.mode,
            age:            matchData.age,
            teamA_id:       matchData.teamA.id,
            teamB_id:       matchData.teamB.id,
            teamA_players:  teamAPlayers,
            teamB_players:  teamBPlayers,
        }),
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) { toast('Could not start match.', 'error'); return; }
        renderScoreboard(data.match);
    })
    .catch(() => toast('Server error.', 'error'));
});

function renderScoreboard(match) {
    document.getElementById('sbTitle').textContent    = `${match.age} · ${match.mode.toUpperCase()}`;
    document.getElementById('sbEmpire').textContent   = `Empire: ${match.empire_name}  |  Court: ${match.court_no}${match.scorer_name ? '  |  Scorer: ' + match.scorer_name : ''}`;
    document.getElementById('sbTime').textContent     = '▶ ' + match.started_at;
    document.getElementById('sbTeamAId').textContent  = match.teamA_id;
    document.getElementById('sbTeamBId').textContent  = match.teamB_id;
    document.getElementById('sbTeamAName').textContent = match.teamA_players.join(' / ');
    document.getElementById('sbTeamBName').textContent = match.teamB_players.join(' / ');
    document.getElementById('sbCourtLabel').textContent = 'COURT ' + match.court_no;
    document.getElementById('sbScoreA').textContent   = match.scoreA;
    document.getElementById('sbScoreB').textContent   = match.scoreB;

    // Reset alerts
    document.getElementById('alertOball').style.display  = 'none';
    document.getElementById('alertJuice').style.display  = 'none';
    document.getElementById('winnerOverlay').style.display = 'none';
    document.getElementById('btnScoreA').disabled = false;
    document.getElementById('btnScoreB').disabled = false;

    document.getElementById('previewCard').style.display = 'none';
    document.getElementById('scoreboard').style.display  = 'block';
    document.getElementById('scoreboard').scrollIntoView({ behavior: 'smooth' });
}

// ── Add Score ─────────────────────────────────────────────────────────────────
function addScore(team) {
    fetch('{{ route("admin-play-update-score") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ team }),
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) { toast(data.message, 'error'); return; }

        // Update scores with pop animation
        const elA = document.getElementById('sbScoreA');
        const elB = document.getElementById('sbScoreB');
        elA.textContent = data.scoreA;
        elB.textContent = data.scoreB;

        const updated = team === 'A' ? elA : elB;
        updated.classList.remove('score-pop');
        void updated.offsetWidth; // reflow
        updated.classList.add('score-pop');

        // Alerts
        document.getElementById('alertOball').style.display = data.alert === 'oball' ? 'block' : 'none';
        document.getElementById('alertJuice').style.display  = data.alert === 'juice'  ? 'block' : 'none';

        // Winner
        if (data.winner) {
            document.getElementById('btnScoreA').disabled = true;
            document.getElementById('btnScoreB').disabled = true;
            document.getElementById('winnerOverlay').style.display = 'block';
            document.getElementById('winnerText').textContent = '🏆 ' + data.winnerTeamId + ' WINS!';
            document.getElementById('winnerSub').textContent  = data.scoreA + ' – ' + data.scoreB;
            document.getElementById('alertOball').style.display = 'none';
            document.getElementById('alertJuice').style.display  = 'none';
        }
    })
    .catch(() => toast('Could not update score.', 'error'));
}

// ── Save Match ────────────────────────────────────────────────────────────────
function saveMatch() {
    if (!confirm('Save and end this match?')) return;
    fetch('{{ route("admin-play-end-match") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toast('Match saved successfully!', 'success');
            setTimeout(resetMatch, 1500);
        } else {
            toast(data.message ?? 'Error saving match.', 'error');
        }
    })
    .catch(() => toast('Server error.', 'error'));
}

// ── Reset ─────────────────────────────────────────────────────────────────────
function resetMatch() {
    matchData = null;
    document.getElementById('previewCard').style.display = 'none';
    document.getElementById('scoreboard').style.display  = 'none';
    document.getElementById('setupForm').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endpush