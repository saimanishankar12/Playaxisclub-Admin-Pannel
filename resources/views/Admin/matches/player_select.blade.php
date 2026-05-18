@extends('Admin.layouts.app')
@section('title', 'Select Players')
@section('page-title', 'Select Players')

@section('styles')
<style>
    .mm-wrap{max-width:740px;margin:0 auto;}
    .mm-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
    .mm-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}
    .mm-breadcrumb{display:flex;align-items:center;gap:6px;font-size:.78rem;color:#94a3b8;margin-bottom:20px;flex-wrap:wrap;}
    .mm-breadcrumb a{color:#64748b;text-decoration:none;}.mm-breadcrumb a:hover{color:#1a56db;}
    .mm-breadcrumb span{color:#1e293b;font-weight:600;}
    .mm-meta-bar{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;}
    .mm-meta-chip{display:inline-flex;align-items:center;gap:5px;font-size:.73rem;font-weight:600;padding:4px 11px;border-radius:20px;background:#f1f5f9;color:#475569;}
    .mm-round-chip{background:#fef3c7;color:#d97706;}
    .mm-form-card{background:#fff;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 20px rgba(0,0,0,.08);padding:26px;}

    /* Random Pick Button */
    .mm-random-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:13px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);color:#fff;border:none;border-radius:10px;font-size:.88rem;font-weight:700;cursor:pointer;margin-bottom:20px;transition:opacity .15s;}
    .mm-random-btn:hover{opacity:.9;}
    .mm-random-btn i{font-size:1rem;}

    .mm-section-label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#1a56db;margin-bottom:12px;display:flex;align-items:center;gap:8px;}
    .mm-section-label::after{content:'';flex:1;height:1px;background:#e2e8f0;}
    .mm-player-grid{display:flex;flex-direction:column;gap:7px;margin-bottom:18px;max-height:260px;overflow-y:auto;padding-right:4px;}
    .mm-player-card{position:relative;}
    .mm-player-card input{position:absolute;opacity:0;width:0;height:0;}
    .mm-player-card label{display:flex;align-items:center;gap:12px;padding:11px 14px;border:2px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:all .15s;}
    .mm-player-card label:hover{border-color:#93c5fd;background:#f0f9ff;}
    .mm-player-card input:checked + label{border-color:#1a56db;background:#eff6ff;}
    .mm-player-card .pc-name{font-weight:600;color:#1e293b;font-size:.86rem;}
    .mm-player-card .pc-id{font-size:.67rem;color:#94a3b8;font-family:'DM Mono',monospace;}
    .mm-player-card .pc-check{margin-left:auto;width:20px;height:20px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;font-size:.6rem;color:transparent;flex-shrink:0;transition:all .15s;}
    .mm-player-card input:checked + label .pc-check{background:#1a56db;color:#fff;}
    .mm-divider{border:none;border-top:1px dashed #e2e8f0;margin:18px 0;}

    /* Preview */
    .mm-preview{background:#f8fafc;border-radius:12px;padding:16px;margin-bottom:18px;display:none;}
    .mm-preview.show{display:block;}
    .mm-preview-label{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;margin-bottom:10px;}
    .mm-preview-vs{display:flex;align-items:center;justify-content:center;gap:14px;margin-bottom:16px;}
    .mm-preview-player{text-align:center;flex:1;min-width:0;}
    .mm-preview-player .pvp-name{font-size:.9rem;font-weight:700;color:#1e293b;word-break:break-word;}
    .mm-preview-player .pvp-id{font-size:.68rem;color:#94a3b8;font-family:'DM Mono',monospace;white-space:pre-line;line-height:1.6;}
    .mm-vs-badge{background:#1a56db;color:#fff;font-size:.72rem;font-weight:800;padding:5px 10px;border-radius:20px;flex-shrink:0;}

    /* Match Info Grid inside preview */
    .mm-match-info{border-top:1px solid #e2e8f0;padding-top:14px;display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .mm-match-info-item{display:flex;flex-direction:column;gap:2px;}
    .mm-match-info-item .mi-label{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;}
    .mm-match-info-item .mi-value{font-size:.8rem;font-weight:600;color:#1e293b;display:flex;align-items:center;gap:5px;}
    .mm-match-info-item .mi-value i{color:#1a56db;font-size:.7rem;}

    /* Badges inside info */
    .mi-badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:.65rem;font-weight:700;}
    .mi-badge-blue{background:#eff6ff;color:#1a56db;}
    .mi-badge-green{background:#f0fdf4;color:#059669;}
    .mi-badge-amber{background:#fef3c7;color:#d97706;}

    .mm-submit{width:100%;padding:13px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer;transition:opacity .15s;}
    .mm-submit:hover{opacity:.9;}
    .mm-submit:disabled{opacity:.4;cursor:not-allowed;}
    .mm-final-notice{background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:12px 16px;font-size:.8rem;color:#92400e;margin-bottom:18px;display:flex;gap:8px;align-items:flex-start;}
    .mm-empty-players{text-align:center;padding:24px;color:#94a3b8;font-size:.83rem;}
    .mm-empty-players i{font-size:1.5rem;opacity:.3;display:block;margin-bottom:8px;}
    @media(max-width:480px){.mm-preview-vs{flex-direction:column;}.mm-match-info{grid-template-columns:1fr;}}
</style>
@endsection

@section('content')
<div class="mm-header">
    <div>
        <h2>Select Players</h2>
        <p>{{ $match->match_type === 'singles' ? 'Choose 2 players' : 'Choose 2 doubles pairs' }} — eliminated players are excluded.</p>
    </div>
    <a href="{{ route('admin-dashboard') }}" style="font-size:.8rem;color:#64748b;text-decoration:none;"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
</div>
<div class="mm-breadcrumb">
    <a href="{{ route('admin-matches.index') }}">Match Manager</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <a href="{{ route('admin-matches.setup') }}">Setup</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <span>Select Players</span>
</div>
<div class="mm-wrap">
    <div class="mm-meta-bar">
        <span class="mm-meta-chip"><i class="fas fa-map-marker-alt"></i> {{ $match->court_no }}</span>
        <span class="mm-meta-chip"><i class="fas fa-users"></i> {{ ucfirst($match->match_type) }}</span>
        <span class="mm-meta-chip"><i class="fas fa-tag"></i> {{ $match->division }}</span>
        <span class="mm-meta-chip mm-round-chip"><i class="fas fa-trophy"></i> {{ $match->getRoundLabel() }}</span>
        <span class="mm-meta-chip"><i class="fas fa-layer-group"></i>
            {{ $match->sets_to_win === 1 ? '1 Set' : 'Best of 3 Sets' }}
        </span>
    </div>

    @if($match->round === 'final')
        <div class="mm-final-notice">
            <i class="fas fa-info-circle" style="margin-top:1px;"></i>
            <div>
                <strong>Final Match</strong> — Players are auto-assigned from Semi Final winners.
                @if(!$match->player1_id) Both Semi Finals must be completed first. @endif
            </div>
        </div>
    @endif

    <form action="{{ route('admin-matches.store-players', $match->id) }}" method="POST" id="playerForm">
        @csrf
        <div class="mm-form-card">

            {{-- Random Pick Button --}}
            @if($match->round !== 'final')
            <button type="button" class="mm-random-btn" id="randomBtn" onclick="randomPick()">
                <i class="fas fa-random"></i> 🎲 Random Pick
            </button>
            @endif

            @if($match->match_type === 'singles')

                {{-- Singles --}}
                <div class="mm-section-label"><i class="fas fa-user"></i> Player 1</div>
                <div class="mm-player-grid" id="p1Grid">
                    @forelse($players as $p)
                    <div class="mm-player-card"
                        data-name="{{ $p->name }}"
                        data-id="{{ $p->player_id }}"
                        data-season="{{ $p->season_id }}"
                        data-city="{{ $p->address }}">
                        <input type="radio" name="player1_id" id="p1_{{ $p->player_id }}" value="{{ $p->player_id }}"
                            {{ $match->player1_id === $p->player_id ? 'checked' : '' }}>
                        <label for="p1_{{ $p->player_id }}">
                            <div><div class="pc-name">{{ $p->name }}</div><div class="pc-id">{{ $p->player_id }} · {{ $p->gender }}</div></div>
                            <div class="pc-check"><i class="fas fa-check"></i></div>
                        </label>
                    </div>
                    @empty
                        <div class="mm-empty-players"><i class="fas fa-user-slash"></i>No eligible players for {{ $match->division }}.<br><small>All may be eliminated or none registered.</small></div>
                    @endforelse
                </div>
                @error('player1_id')<p style="font-size:.72rem;color:#ef4444;margin-bottom:10px;">{{ $message }}</p>@enderror

                <hr class="mm-divider">

                <div class="mm-section-label"><i class="fas fa-user"></i> Player 2</div>
                <div class="mm-player-grid" id="p2Grid">
                    @forelse($players as $p)
                    <div class="mm-player-card"
                        data-name="{{ $p->name }}"
                        data-id="{{ $p->player_id }}"
                        data-season="{{ $p->season_id }}"
                        data-city="{{ $p->address }}">
                        <input type="radio" name="player2_id" id="p2_{{ $p->player_id }}" value="{{ $p->player_id }}"
                            {{ $match->player2_id === $p->player_id ? 'checked' : '' }}>
                        <label for="p2_{{ $p->player_id }}">
                            <div><div class="pc-name">{{ $p->name }}</div><div class="pc-id">{{ $p->player_id }} · {{ $p->gender }}</div></div>
                            <div class="pc-check"><i class="fas fa-check"></i></div>
                        </label>
                    </div>
                    @empty
                        <div class="mm-empty-players"><i class="fas fa-user-slash"></i>No eligible players.</div>
                    @endforelse
                </div>
                @error('player2_id')<p style="font-size:.72rem;color:#ef4444;margin-bottom:10px;">{{ $message }}</p>@enderror

            @else

                {{-- Doubles --}}
                <div class="mm-section-label"><i class="fas fa-users"></i> Pair 1</div>
                <div class="mm-player-grid" id="p1Grid">
                    @forelse($pairs as $pair)
                    <div class="mm-player-card"
                        data-name="{{ $pair['names'] }}"
                        data-id="{{ $pair['ids'] }}"
                        data-season="{{ $pair['season_id'] }}"
                        data-city="">
                        <input type="radio" name="player1_id" id="pair1_{{ $pair['season_id'] }}" value="{{ $pair['season_id'] }}"
                            {{ $match->player1_id === $pair['season_id'] ? 'checked' : '' }}>
                        <label for="pair1_{{ $pair['season_id'] }}">
                            <div><div class="pc-name">{{ $pair['names'] }}</div><div class="pc-id">{{ $pair['season_id'] }}</div></div>
                            <div class="pc-check"><i class="fas fa-check"></i></div>
                        </label>
                    </div>
                    @empty
                        <div class="mm-empty-players"><i class="fas fa-users-slash"></i>No eligible pairs for {{ $match->division }}.</div>
                    @endforelse
                </div>
                @error('player1_id')<p style="font-size:.72rem;color:#ef4444;margin-bottom:10px;">{{ $message }}</p>@enderror

                <hr class="mm-divider">

                <div class="mm-section-label"><i class="fas fa-users"></i> Pair 2</div>
                <div class="mm-player-grid" id="p2Grid">
                    @forelse($pairs as $pair)
                    <div class="mm-player-card"
                        data-name="{{ $pair['names'] }}"
                        data-id="{{ $pair['ids'] }}"
                        data-season="{{ $pair['season_id'] }}"
                        data-city="">
                        <input type="radio" name="player2_id" id="pair2_{{ $pair['season_id'] }}" value="{{ $pair['season_id'] }}"
                            {{ $match->player2_id === $pair['season_id'] ? 'checked' : '' }}>
                        <label for="pair2_{{ $pair['season_id'] }}">
                            <div><div class="pc-name">{{ $pair['names'] }}</div><div class="pc-id">{{ $pair['season_id'] }}</div></div>
                            <div class="pc-check"><i class="fas fa-check"></i></div>
                        </label>
                    </div>
                    @empty
                        <div class="mm-empty-players"><i class="fas fa-users-slash"></i>No eligible pairs.</div>
                    @endforelse
                </div>
                @error('player2_id')<p style="font-size:.72rem;color:#ef4444;margin-bottom:10px;">{{ $message }}</p>@enderror

            @endif

            {{-- Preview --}}
            <div class="mm-preview" id="previewBox">
                <div class="mm-preview-label">Match Preview</div>

                {{-- Players VS --}}
                <div class="mm-preview-vs">
                    <div class="mm-preview-player">
                        <div class="pvp-name" id="prev_p1">—</div>
                        <div class="pvp-id" id="prev_p1_id"></div>
                    </div>
                    <div class="mm-vs-badge">VS</div>
                    <div class="mm-preview-player">
                        <div class="pvp-name" id="prev_p2">—</div>
                        <div class="pvp-id" id="prev_p2_id"></div>
                    </div>
                </div>

                {{-- Match Info --}}
                <div class="mm-match-info">
                    <div class="mm-match-info-item">
                        <span class="mi-label">Category</span>
                        <span class="mi-value">
                            <span class="mi-badge mi-badge-blue">{{ ucfirst($match->match_type) }}</span>
                        </span>
                    </div>
                    <div class="mm-match-info-item">
                        <span class="mi-label">Age</span>
                        <span class="mi-value">
                            <span class="mi-badge mi-badge-green">{{ $match->division }}</span>
                        </span>
                    </div>
                    <div class="mm-match-info-item">
                        <span class="mi-label">Umpire Name</span>
                        <span class="mi-value"><i class="fas fa-user-tie"></i> {{ $match->umpire_name }}</span>
                    </div>
                    <div class="mm-match-info-item">
                        <span class="mi-label">Court</span>
                        <span class="mi-value"><i class="fas fa-map-marker-alt"></i> {{ $match->court_no }}</span>
                    </div>
                    @if($match->scorer_name)
                    <div class="mm-match-info-item">
                        <span class="mi-label">Scorer</span>
                        <span class="mi-value"><i class="fas fa-clipboard"></i> {{ $match->scorer_name }}</span>
                    </div>
                    @endif
                    <div class="mm-match-info-item">
                        <span class="mi-label">Round</span>
                        <span class="mi-value">
                            <span class="mi-badge mi-badge-amber">{{ $match->getRoundLabel() }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <button type="submit" class="mm-submit" id="startBtn" {{ ($match->player1_id && $match->player2_id) ? '' : 'disabled' }}>
                <i class="fas fa-play mr-2"></i> Start Match
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
(function() {
    function getLabel(input) {
        const card = input.closest('.mm-player-card');
        return {
            name: card.querySelector('.pc-name').textContent,
            id:   `Player ID: ${card.dataset.id}\nTournament ID: ${card.dataset.season}\nCity: ${card.dataset.city}`
        };
    }

    function updatePreview() {
        const p1 = document.querySelector('input[name="player1_id"]:checked');
        const p2 = document.querySelector('input[name="player2_id"]:checked');
        const preview  = document.getElementById('previewBox');
        const startBtn = document.getElementById('startBtn');

        if (p1) { const l = getLabel(p1); document.getElementById('prev_p1').textContent = l.name; document.getElementById('prev_p1_id').textContent = l.id; }
        if (p2) { const l = getLabel(p2); document.getElementById('prev_p2').textContent = l.name; document.getElementById('prev_p2_id').textContent = l.id; }

        if (p1 && p2) {
            preview.classList.add('show');
            startBtn.disabled = p1.value === p2.value;
        } else {
            preview.classList.remove('show');
            startBtn.disabled = true;
        }
    }

    document.querySelectorAll('input[name="player1_id"], input[name="player2_id"]')
        .forEach(i => i.addEventListener('change', updatePreview));

    updatePreview();

    window.randomPick = function() {
        const p1Inputs = Array.from(document.querySelectorAll('input[name="player1_id"]'));
        const p2Inputs = Array.from(document.querySelectorAll('input[name="player2_id"]'));

        if (p1Inputs.length < 1 || p2Inputs.length < 1) return;

        const allIds   = p1Inputs.map(i => i.value);
        const shuffled = allIds.sort(() => Math.random() - 0.5);
        const pick1    = shuffled[0];
        const pick2    = shuffled.find(id => id !== pick1);

        if (!pick2) { alert('Need at least 2 eligible players/pairs to random pick.'); return; }

        const p1Input = document.querySelector(`input[name="player1_id"][value="${pick1}"]`);
        if (p1Input) { p1Input.checked = true; }

        const p2Input = document.querySelector(`input[name="player2_id"][value="${pick2}"]`);
        if (p2Input) { p2Input.checked = true; }

        updatePreview();

        const btn = document.getElementById('randomBtn');
        if (btn) {
            btn.style.background = 'linear-gradient(135deg,#059669,#10b981)';
            btn.innerHTML = '<i class="fas fa-check"></i> Players Selected!';
            setTimeout(() => {
                btn.style.background = 'linear-gradient(135deg,#7c3aed,#8b5cf6)';
                btn.innerHTML = '<i class="fas fa-random"></i> 🎲 Random Pick Again';
            }, 1500);
        }
    };
})();
</script>
@endsection