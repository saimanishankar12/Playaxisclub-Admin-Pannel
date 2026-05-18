@extends('Admin.layouts.app')
@section('title', 'Edit Match #' . $match->id)
@section('page-title', 'Edit Match')

@section('styles')
<style>
    .em-wrap{max-width:680px;margin:0 auto;}
    .em-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
    .em-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}
    .em-card{background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.06);padding:24px;margin-bottom:20px;}
    .em-section-label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#1a56db;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
    .em-section-label::after{content:'';flex:1;height:1px;background:#e2e8f0;}

    .em-players-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;}
    .em-player-info{background:#f8fafc;border-radius:10px;padding:14px;border:1.5px solid #e2e8f0;}
    .em-player-info.p1{border-left:3px solid #1a56db;}
    .em-player-info.p2{border-left:3px solid #ef4444;}
    .em-player-badge{display:inline-block;font-size:.62rem;font-weight:700;padding:2px 8px;border-radius:20px;margin-bottom:6px;}
    .em-player-badge.p1{background:#eff6ff;color:#1a56db;}
    .em-player-badge.p2{background:#fee2e2;color:#ef4444;}
    .em-player-name{font-size:.92rem;font-weight:800;color:#1e293b;margin-bottom:4px;}
    .em-player-meta{font-size:.72rem;color:#64748b;display:flex;flex-direction:column;gap:2px;}
    .em-player-meta span{display:flex;align-items:center;gap:4px;}
    .em-player-meta i{color:#94a3b8;font-size:.65rem;width:12px;}

    .em-set-row{display:grid;grid-template-columns:60px 1fr auto 1fr auto;align-items:center;gap:10px;margin-bottom:12px;background:#f8fafc;border-radius:10px;padding:12px;}
    .em-set-label{font-size:.75rem;font-weight:700;color:#64748b;}
    .em-set-input{width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 10px;font-size:.88rem;font-weight:700;text-align:center;color:#1e293b;outline:none;}
    .em-set-input:focus{border-color:#1a56db;}
    .em-set-sep{font-size:.8rem;color:#94a3b8;font-weight:700;}
    .em-set-winner{display:flex;gap:6px;}
    .em-set-winner label{display:flex;align-items:center;gap:4px;font-size:.72rem;font-weight:600;cursor:pointer;padding:4px 8px;border-radius:6px;border:1.5px solid #e2e8f0;color:#64748b;transition:all .12s;}
    .em-set-winner input[type=radio]{display:none;}
    .em-set-winner input[type=radio]:checked + label{border-color:#1a56db;background:#eff6ff;color:#1a56db;}

    .em-winner-section{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:8px;}
    .em-winner-card{position:relative;}
    .em-winner-card input{position:absolute;opacity:0;width:0;height:0;}
    .em-winner-card label{display:flex;align-items:center;justify-content:center;gap:8px;padding:14px;border:2px solid #e2e8f0;border-radius:12px;cursor:pointer;font-size:.85rem;font-weight:700;color:#64748b;transition:all .15s;}
    .em-winner-card input:checked + label{border-color:#059669;background:#f0fdf4;color:#059669;}

    .em-submit{width:100%;padding:13px;background:linear-gradient(135deg,#059669,#10b981);color:#fff;border:none;border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer;margin-top:8px;}
    .em-submit:hover{opacity:.9;}
    .em-cancel{display:block;text-align:center;margin-top:10px;font-size:.8rem;color:#64748b;text-decoration:none;}
    .em-cancel:hover{color:#1a56db;}

    @media(max-width:540px){
        .em-players-row{grid-template-columns:1fr;}
        .em-set-row{grid-template-columns:50px 1fr auto 1fr;}
        .em-set-winner{display:none;}
    }
</style>
@endsection

@section('content')
<div class="em-wrap">

    <div class="em-header">
        <div>
            <h2>Edit Match #{{ $match->id }}</h2>
            <p style="font-size:.8rem;color:#64748b;margin:2px 0 0;">
                {{ $match->division }} · {{ ucfirst($match->match_type) }} · {{ $match->getRoundLabel() }}
            </p>
        </div>
        <a href="{{ route('admin-matches.complete', $match->id) }}"
           style="font-size:.8rem;color:#64748b;text-decoration:none;">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>

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
            $p1Players = DB::table('players')->where('season_id', $match->player1_id)->where('mode','doubles')->get();
            $p2Players = DB::table('players')->where('season_id', $match->player2_id)->where('mode','doubles')->get();
        }
    @endphp

    <form action="{{ route('admin-matches.update-match', $match->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Set Scores --}}
        @if($completedSets->count() > 0)
        <div class="em-card">
            <div class="em-section-label"><i class="fas fa-list-ol"></i> Set Scores</div>

            <div style="display:grid;grid-template-columns:60px 1fr auto 1fr auto;gap:10px;padding:0 12px;margin-bottom:10px;">
                <span></span>
                <div style="text-align:center;">
                    <div style="font-size:.78rem;font-weight:800;color:#1a56db;">{{ $p1Name }}</div>
                    @if($match->match_type === 'singles' && isset($p1Info))
                        <div style="font-size:.65rem;color:#94a3b8;">Tournament ID: {{ $p1Info->season_id }}</div>
                        <div style="font-size:.65rem;color:#94a3b8;">City: {{ $p1Info->address }}</div>
                    @endif
                </div>
                <span></span>
                <div style="text-align:center;">
                    <div style="font-size:.78rem;font-weight:800;color:#ef4444;">{{ $p2Name }}</div>
                    @if($match->match_type === 'singles' && isset($p2Info))
                        <div style="font-size:.65rem;color:#94a3b8;">Tournament ID: {{ $p2Info->season_id }}</div>
                        <div style="font-size:.65rem;color:#94a3b8;">City: {{ $p2Info->address }}</div>
                    @endif
                </div>
                <span style="font-size:.65rem;font-weight:700;text-transform:uppercase;color:#94a3b8;">Winner</span>
            </div>

            @foreach($completedSets as $set)
            <div class="em-set-row">
                <span class="em-set-label">Set {{ $set->set_number }}</span>
                <input type="number" name="sets[{{ $set->set_number }}][score_p1]"
                    value="{{ $set->score_p1 }}" min="0" max="30" class="em-set-input">
                <span class="em-set-sep">—</span>
                <input type="number" name="sets[{{ $set->set_number }}][score_p2]"
                    value="{{ $set->score_p2 }}" min="0" max="30" class="em-set-input">
                <div class="em-set-winner">
                    <input type="radio" name="sets[{{ $set->set_number }}][winner]"
                        id="sw_p1_{{ $set->set_number }}" value="p1"
                        {{ $set->winner === 'p1' ? 'checked' : '' }}>
                    <label for="sw_p1_{{ $set->set_number }}">P1</label>

                    <input type="radio" name="sets[{{ $set->set_number }}][winner]"
                        id="sw_p2_{{ $set->set_number }}" value="p2"
                        {{ $set->winner === 'p2' ? 'checked' : '' }}>
                    <label for="sw_p2_{{ $set->set_number }}">P2</label>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Match Winner --}}
        <div class="em-card" id="em-winner-card">
            <div class="em-section-label"><i class="fas fa-crown"></i> Match Winner</div>

            {{-- Tie Warning — shown by JS when sets are tied --}}
            <div id="em-tie-warning" style="display:none;background:#fef3c7;border:1px solid #f59e0b;border-radius:10px;padding:14px 16px;font-size:.83rem;color:#92400e;font-weight:600;">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Sets are tied! Saving will create a decider set and reopen the match for live scoring.
            </div>

            {{-- Winner radios — hidden when tied --}}
            <div id="em-winner-radios" class="em-winner-section">
                <div class="em-winner-card">
                    <input type="radio" name="winner" id="w_p1" value="p1"
                        {{ $match->winner_id === $match->player1_id ? 'checked' : '' }}>
                    <label for="w_p1"><i class="fas fa-crown"></i> {{ $p1Name }}</label>
                </div>
                <div class="em-winner-card">
                    <input type="radio" name="winner" id="w_p2" value="p2"
                        {{ $match->winner_id === $match->player2_id ? 'checked' : '' }}>
                    <label for="w_p2"><i class="fas fa-crown"></i> {{ $p2Name }}</label>
                </div>
            </div>

            @error('winner')
                <p style="font-size:.72rem;color:#ef4444;margin-top:8px;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="em-submit" id="em-submit-btn">
            <i class="fas fa-save mr-2"></i> Save Changes
        </button>
        <a href="{{ route('admin-matches.complete', $match->id) }}" class="em-cancel">Cancel</a>

    </form>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const maxSets  = {{ $match->total_sets }};
    const isBestOf = maxSets > 1;

    function countSetWins() {
        let p1 = 0, p2 = 0;
        document.querySelectorAll('[name$="[winner]"]').forEach(function (radio) {
            if (radio.checked) {
                if (radio.value === 'p1') p1++;
                else p2++;
            }
        });
        return { p1, p2 };
    }

    function hasUnplayedSet() {
        let unplayed = false;
        document.querySelectorAll('.em-set-row').forEach(function (row) {
            const inputs = row.querySelectorAll('.em-set-input');
            if (inputs.length === 2) {
                const s1 = parseInt(inputs[0].value) || 0;
                const s2 = parseInt(inputs[1].value) || 0;
                if (s1 === 0 && s2 === 0) unplayed = true;
            }
        });
        return unplayed;
    }

    function checkTie() {
        if (!isBestOf) return;

        const { p1, p2 } = countSetWins();

        // Tied if sets won are equal OR any set has 0-0 scores
        const tied = ((p1 === p2) && (p1 > 0)) || hasUnplayedSet();

        document.getElementById('em-tie-warning').style.display  = tied ? 'block' : 'none';
        document.getElementById('em-winner-radios').style.display = tied ? 'none'  : 'grid';

        document.querySelectorAll('[name="winner"]').forEach(function (r) {
            r.disabled = tied;
        });

        const btn = document.getElementById('em-submit-btn');
        btn.innerHTML = tied
            ? '<i class="fas fa-play-circle mr-2"></i> Save & Start Decider Set'
            : '<i class="fas fa-save mr-2"></i> Save Changes';
    }

    // Listen to set winner radio changes
    document.querySelectorAll('[name$="[winner]"]').forEach(function (radio) {
        radio.addEventListener('change', checkTie);
    });

    // Listen to score input changes too
    document.querySelectorAll('.em-set-input').forEach(function (input) {
        input.addEventListener('input', checkTie);
    });

    // Run on load
    checkTie();
})();
</script>
@endsection
