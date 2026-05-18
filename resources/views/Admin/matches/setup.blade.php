@extends('Admin.layouts.app')
@section('title', 'New Match — Setup')
@section('page-title', 'New Match')

@section('styles')
<style>
    .mm-wrap{max-width:620px;margin:0 auto;}
    .mm-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
    .mm-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}
    .mm-header p{margin:2px 0 0;font-size:.8rem;color:#64748b;}
    .mm-breadcrumb{display:flex;align-items:center;gap:6px;font-size:.78rem;color:#94a3b8;margin-bottom:24px;}
    .mm-breadcrumb a{color:#64748b;text-decoration:none;}
    .mm-breadcrumb a:hover{color:#1a56db;}
    .mm-breadcrumb span{color:#1e293b;font-weight:600;}
    .mm-form-card{background:#fff;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 20px rgba(0,0,0,.08);padding:32px;}
    .mm-step-label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#1a56db;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
    .mm-step-label::after{content:'';flex:1;height:1px;background:#e2e8f0;}
    .mm-field{margin-bottom:18px;}
    .mm-field label{display:block;font-size:.78rem;font-weight:600;color:#374151;margin-bottom:6px;}
    .mm-field input,
    .mm-field select{width:100%;border:1.5px solid #e2e8f0;border-radius:9px;padding:10px 14px;font-size:.85rem;color:#1e293b;outline:none;transition:border-color .15s;background:#fff;}
    .mm-field input:focus,
    .mm-field select:focus{border-color:#1a56db;box-shadow:0 0 0 3px rgba(26,86,219,.08);}
    .mm-field select option:disabled{color:#94a3b8;background:#f8fafc;}
    /* .mm-field select:disabled{background:#f1f5f9;color:#64748b;cursor:not-allowed;border-color:#e2e8f0;} */
    .mm-field-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .mm-optional-tag{font-size:.65rem;font-weight:500;color:#94a3b8;margin-left:5px;font-style:italic;}
    .mm-radio-group{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .mm-radio-card{position:relative;}
    .mm-radio-card input{position:absolute;opacity:0;width:0;height:0;}
    .mm-radio-card label{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;padding:14px;border:2px solid #e2e8f0;border-radius:12px;cursor:pointer;transition:all .15s;font-size:.82rem;font-weight:600;color:#64748b;}
    .mm-radio-card label i{font-size:1.2rem;}
    .mm-radio-card input:checked + label{border-color:#1a56db;background:#eff6ff;color:#1a56db;}
    .mm-age-group{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;}
    .mm-age-card{position:relative;}
    .mm-age-card input{position:absolute;opacity:0;width:0;height:0;}
    .mm-age-card label{display:flex;align-items:center;justify-content:center;padding:11px 4px;border:2px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:all .15s;font-size:.82rem;font-weight:700;color:#64748b;}
    .mm-age-card input:checked + label{border-color:#8b5cf6;background:#ede9fe;color:#7c3aed;}
    .mm-submit{width:100%;padding:13px;background:linear-gradient(135deg,#1a56db,#6366f1);color:#fff;border:none;border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer;transition:opacity .15s;margin-top:8px;}
    .mm-submit:hover{opacity:.9;}
    .mm-busy-note{font-size:.68rem;color:#94a3b8;margin-top:5px;display:flex;align-items:center;gap:4px;}
    .mm-sets-animate{animation:mm-fadein .2s ease;}
    @keyframes mm-fadein{from{opacity:0;transform:translateY(-4px);}to{opacity:1;transform:translateY(0);}}
    @media(max-width:480px){
        .mm-field-row{grid-template-columns:1fr;}
        .mm-age-group{grid-template-columns:repeat(2,1fr);}
    }


    @keyframes spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
</style>
@endsection

@section('content')
   {{-- Live match warning (flashed from redirect) --}}
    @if(session('warning'))
    <div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:.85rem;font-weight:600;color:#92400e;">
        <i class="fas fa-exclamation-triangle" style="color:#f59e0b;"></i>
        {{ session('warning') }}
    </div>
    @endif
<div class="mm-header">
    <div>
        <h2>New Match Setup</h2>
        <p>Configure the match before selecting players.</p>
    </div>
    <a href="{{ route('admin-dashboard') }}" style="font-size:.8rem;color:#64748b;text-decoration:none;">
        <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
    </a>
</div>

<div class="mm-breadcrumb">
    <a href="{{ route('admin-matches.index') }}">Match Manager</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <span>Setup</span>
</div>

<div class="mm-wrap">
    <form action="{{ route('admin-matches.store-setup') }}" method="POST" id="mm-setup-form">
        @csrf
        <div class="mm-form-card">

            {{-- ── Court & Officials ─────────────────────────────────── --}}
            <div class="mm-step-label"><i class="fas fa-cog"></i> Court & Officials</div>

            <div class="mm-field-row">

                {{-- Court --}}
                <div class="mm-field">
                    <label>Court No.</label>
                    <select name="court_no" required>
                        <option value="">— Select Court —</option>
                        @foreach($courts as $court)
                            @php $busy = in_array($court->court_no, $busyCourts); @endphp
                            <option value="{{ $court->court_no }}"
                                {{ old('court_no') === $court->court_no ? 'selected' : '' }}
                                {{ $busy ? 'disabled' : '' }}>
                                {{ $court->court_no }}{{ $busy ? ' (In Use)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('court_no')
                        <span style="font-size:.72rem;color:#ef4444;">{{ $message }}</span>
                    @enderror
                    @if(count($busyCourts) > 0)
                        <div class="mm-busy-note">
                            <i class="fas fa-circle" style="color:#ef4444;font-size:.4rem;"></i>
                            Courts marked "In Use" are in an active match
                        </div>
                    @endif
                </div>

                {{-- Umpire --}}
                <div class="mm-field">
                    <label>Umpire Name</label>
                    <select name="umpire_name" required>
                        <option value="">— Select Umpire —</option>
                        @foreach($umpires as $umpire)
                            @php $busy = in_array($umpire->name, $busyUmpires); @endphp
                            <option value="{{ $umpire->name }}"
                                {{ old('umpire_name') === $umpire->name ? 'selected' : '' }}
                                {{ $busy ? 'disabled' : '' }}>
                                {{ $umpire->name }}{{ $busy ? ' (Busy)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('umpire_name')
                        <span style="font-size:.72rem;color:#ef4444;">{{ $message }}</span>
                    @enderror
                    @if(count($busyUmpires) > 0)
                        <div class="mm-busy-note">
                            <i class="fas fa-circle" style="color:#ef4444;font-size:.4rem;"></i>
                            Umpires marked "Busy" are in an active match
                        </div>
                    @endif
                </div>

            </div>

            {{-- Scorer — optional --}}
            <div class="mm-field">
                <label>Scorer Name <span class="mm-optional-tag">optional</span></label>
                <select name="scorer_name">
                    <option value="">— No Scorer —</option>
                    @foreach($scorers as $scorer)
                        @php $busy = in_array($scorer->name, $busyScorers); @endphp
                        <option value="{{ $scorer->name }}"
                            {{ old('scorer_name') === $scorer->name ? 'selected' : '' }}
                            {{ $busy ? 'disabled' : '' }}>
                            {{ $scorer->name }}{{ $busy ? ' (Busy)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('scorer_name')
                    <span style="font-size:.72rem;color:#ef4444;">{{ $message }}</span>
                @enderror
                @if(count($busyScorers) > 0)
                    <div class="mm-busy-note">
                        <i class="fas fa-circle" style="color:#ef4444;font-size:.4rem;"></i>
                        Scorers marked "Busy" are in an active match
                    </div>
                @endif
            </div>

            {{-- ── Type & Division ───────────────────────────────────── --}}
            <div class="mm-step-label" style="margin-top:4px;">
                <i class="fas fa-users"></i> Type & Division
            </div>

            <div class="mm-field">
                <label>Singles or Doubles?</label>
                <div class="mm-radio-group">
                    <div class="mm-radio-card">
                        <input type="radio" name="match_type" id="type_s" value="singles"
                            {{ old('match_type') === 'singles' ? 'checked' : '' }} required>
                        <label for="type_s"><i class="fas fa-user"></i> Singles</label>
                    </div>
                    <div class="mm-radio-card">
                        <input type="radio" name="match_type" id="type_d" value="doubles"
                            {{ old('match_type') === 'doubles' ? 'checked' : '' }}>
                        <label for="type_d"><i class="fas fa-users"></i> Doubles</label>
                    </div>
                </div>
                @error('match_type')
                    <span style="font-size:.72rem;color:#ef4444;">{{ $message }}</span>
                @enderror
            </div>

            <div class="mm-field">
                <label>Age Division</label>
                <div class="mm-age-group">
                    @foreach($divisions as $age)
                    <div class="mm-age-card">
                        <input type="radio" name="division" id="div_{{ $age }}" value="{{ $age }}"
                            {{ old('division') === $age ? 'checked' : '' }} required>
                        <label for="div_{{ $age }}">{{ $age }}</label>
                    </div>
                    @endforeach
                </div>
                @error('division')
                    <span style="font-size:.72rem;color:#ef4444;">{{ $message }}</span>
                @enderror
            </div>
                        <div id="mm-eligible-wrap" style="display:none;margin-top:-8px;margin-bottom:16px;">
    <div id="mm-eligible-box" style="display:flex;align-items:center;justify-content:space-between;gap:8px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:10px 14px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class="fas fa-users" style="color:#16a34a;font-size:.85rem;"></i>
            <span id="mm-eligible-text" style="font-size:.82rem;font-weight:600;color:#15803d;"></span>
        </div>
        <button type="button" onclick="fetchEligibleCount()" 
            style="background:none;border:none;cursor:pointer;color:#16a34a;font-size:.75rem;font-weight:600;display:flex;align-items:center;gap:4px;padding:0;">
            <i class="fas fa-sync-alt" id="mm-refresh-icon"></i> Refresh
        </button>
    </div>
</div>

            {{-- ── Round & Sets ──────────────────────────────────────── --}}
            <div class="mm-step-label" style="margin-top:4px;">
                <i class="fas fa-trophy"></i> Round & Sets
            </div>

            {{-- Round --}}
            <div class="mm-field">
                <label>Round</label>
                <select name="round" id="mm-round" required>
                    <option value="">— Select Round —</option>
                    <option value="quarter_final" {{ old('round') === 'quarter_final' ? 'selected' : '' }}>Knock Out</option>
                    <option value="semi_final"    {{ old('round') === 'semi_final'    ? 'selected' : '' }}>Semi Final</option>
                    <option value="final"         {{ old('round') === 'final'         ? 'selected' : '' }}>Final</option>
                </select>
                @error('round')
                    <span style="font-size:.72rem;color:#ef4444;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Number of Sets — shown & populated dynamically by JS --}}
            <div class="mm-field mm-sets-animate" id="mm-sets-field" style="display:none;">
                <label>Number of Sets</label>
                <select name="sets" id="mm-sets">
                    <option value="">— Select Sets —</option>
                </select>
                @error('sets')
                    <span style="font-size:.72rem;color:#ef4444;">{{ $message }}</span>
                @enderror
                <div id="mm-sets-hint" class="mm-busy-note" style="margin-top:6px;display:none;">
                    <i class="fas fa-info-circle" style="color:#1a56db;font-size:.75rem;"></i>
                    <span id="mm-sets-hint-text"></span>
                </div>
            </div>

            <button type="submit" class="mm-submit">
                <i class="fas fa-arrow-right mr-2"></i> Continue to Player Selection
            </button>

        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
(function () {

    // ── Per-round configuration ──────────────────────────────────────────────
    const ROUND_CONFIG = {
        quarter_final: {
            options : [{ value: 1, label: '1 Set (Best of 1) — Fixed' }],
            fixed   : false,
            hint    : 'Knockout rounds are always 1 set.',
        },
        // REPLACE WITH
semi_final: {
    options : [
        { value: 1, label: '1 Set'  },
        { value: 2, label: '2 Sets' },
        { value: 3, label: '3 Sets' },
        { value: 4, label: '4 Sets' },
    ],
    fixed : false,
    hint  : 'Choose how many sets this Semi Final will be played.',
},
final: {
    options : [
        { value: 1, label: '1 Set'  },
        { value: 2, label: '2 Sets' },
        { value: 3, label: '3 Sets' },
        { value: 4, label: '4 Sets' },
    ],
    fixed : false,
    hint  : 'Choose how many sets this Final will be played.',
},
    };

    // Elements
    const roundEl  = document.getElementById('mm-round');
    const setsWrap = document.getElementById('mm-sets-field');
    const setsEl   = document.getElementById('mm-sets');
    const hintWrap = document.getElementById('mm-sets-hint');
    const hintText = document.getElementById('mm-sets-hint-text');
    const form     = document.getElementById('mm-setup-form');

    // old() values injected by Laravel — restore state after failed validation
    const oldRound = "{{ old('round', '') }}";
    const oldSets  = "{{ old('sets', '') }}";

    // ── Build the sets dropdown for a given round ────────────────────────────
    function renderSets(round) {
        // Reset
        setsEl.innerHTML = '';
        setsEl.disabled  = false;
        setsEl.required  = false;
        setsEl.style.borderColor = '';
        hintWrap.style.display   = 'none';

        if (!round || !ROUND_CONFIG[round]) {
            setsWrap.style.display = 'none';
            return;
        }

        const cfg = ROUND_CONFIG[round];

        // Placeholder only when admin must actively choose
        if (!cfg.fixed) {
            const ph = document.createElement('option');
            ph.value = '';
            ph.textContent = '— Select Sets —';
            setsEl.appendChild(ph);
        }

        cfg.options.forEach(function (opt) {
            const o = document.createElement('option');
            o.value = opt.value;
            o.textContent = opt.label;
            // Auto-select if there's only one choice (fixed) OR matches old() after redirect
            if (cfg.fixed || String(opt.value) === String(oldSets)) {
                o.selected = true;
            }
            setsEl.appendChild(o);
        });

        setsEl.disabled = cfg.fixed;   // lock knockout — can't change 1 set
        setsEl.required = true;

        hintText.textContent     = cfg.hint;
        hintWrap.style.display   = 'flex';
        setsWrap.style.display   = 'block';

        // Trigger re-animation
        setsWrap.classList.remove('mm-sets-animate');
        void setsWrap.offsetWidth;
        setsWrap.classList.add('mm-sets-animate');
    }

    // ── Restore state on page load after a validation redirect ──────────────
    if (oldRound) {
        renderSets(oldRound);
    }

    // ── Re-render whenever the admin picks a different round ─────────────────
    roundEl.addEventListener('change', function () {
        renderSets(this.value);
    });

    // ── Client-side guard on submit ──────────────────────────────────────────
    form.addEventListener('submit', function (e) {
        const round = roundEl.value;
        if (!round || !ROUND_CONFIG[round]) return;  // let backend handle required

        const cfg = ROUND_CONFIG[round];
        if (!cfg.fixed && !setsEl.value) {
            e.preventDefault();
            setsEl.style.borderColor = '#ef4444';
            setsEl.focus();

            // Show inline JS error (only if no server error already rendered)
            let err = document.getElementById('mm-sets-js-err');
            if (!err) {
                err = document.createElement('span');
                err.id = 'mm-sets-js-err';
                err.style.cssText = 'font-size:.72rem;color:#ef4444;display:block;margin-top:3px;';
                setsEl.insertAdjacentElement('afterend', err);
            }
            err.textContent = 'Please select the number of sets.';
        }
    });

    // Reset error styling when admin picks a set
    setsEl.addEventListener('change', function () {
        this.style.borderColor = '';
        const err = document.getElementById('mm-sets-js-err');
        if (err) err.textContent = '';
    });

})();




// ── Eligible player count fetch ──────────────────────────────────────────
const ELIGIBLE_URL = "{{ route('admin-matches.eligible-count') }}";




let eligibleInterval = null;

function fetchEligibleCount() {
    const matchType = document.querySelector('[name="match_type"]:checked')?.value;
    const division  = document.querySelector('[name="division"]:checked')?.value;

    if (!matchType || !division) return;

    const wrap        = document.getElementById('mm-eligible-wrap');
    const box         = document.getElementById('mm-eligible-box');
    const text        = document.getElementById('mm-eligible-text');
    const refreshIcon = document.getElementById('mm-refresh-icon');

    // Spin refresh icon
    if (refreshIcon) refreshIcon.style.animation = 'spin 1s linear infinite';

   const round = document.getElementById('mm-round')?.value || 'quarter_final';
fetch(`${ELIGIBLE_URL}?match_type=${matchType}&division=${division}&round=${round}`)
        .then(r => r.json())
        .then(data => {
            wrap.style.display = 'block';
            if (refreshIcon) refreshIcon.style.animation = '';

            if (data.count === 0) {
                box.style.background  = '#fef2f2';
                box.style.borderColor = '#fca5a5';
                text.style.color      = '#dc2626';
                text.innerHTML = `<i class="fas fa-times-circle mr-1"></i> No eligible players remaining in this category`;

            } else if (data.count === 1) {
                box.style.background  = '#fef3c7';
                box.style.borderColor = '#f59e0b';
                text.style.color      = '#d97706';
                text.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i> Only <strong>1</strong> player remaining — need at least 2 to start a match`;

            } else {
                const pairs = Math.floor(data.count / 2);
                const isOdd = data.count % 2 !== 0;
                box.style.background  = '#f0fdf4';
                box.style.borderColor = '#86efac';
                text.style.color      = '#15803d';
                text.innerHTML = 
                    `<i class="fas fa-check-circle mr-1"></i> ` +
                    `<strong>${data.count}</strong> players remaining — ` +
                    `can pair <strong>${pairs}</strong> match${pairs > 1 ? 'es' : ''}` +
                    (isOdd ? ` <span style="font-size:.7rem;color:#d97706;margin-left:4px;">(+1 will be round robin)</span>` : '');
            }
        })
        .catch(() => {
            if (refreshIcon) refreshIcon.style.animation = '';
            wrap.style.display = 'none';
        });
}

function startEligibleAutoRefresh() {
    if (eligibleInterval) clearInterval(eligibleInterval);
    eligibleInterval = setInterval(fetchEligibleCount, 30000);
}

function resetAndFetch() {
    if (eligibleInterval) clearInterval(eligibleInterval);
    fetchEligibleCount();
    startEligibleAutoRefresh();
}

// Trigger on match type or division change
document.querySelectorAll('[name="match_type"]').forEach(r =>
    r.addEventListener('change', resetAndFetch)
);
document.querySelectorAll('[name="division"]').forEach(r =>
    r.addEventListener('change', resetAndFetch)
);
document.getElementById('mm-round')?.addEventListener('change', resetAndFetch);
// Run on load if old() values are already selected
if (document.querySelector('[name="match_type"]:checked') &&
    document.querySelector('[name="division"]:checked')) {
    fetchEligibleCount();
    startEligibleAutoRefresh();
}
</script>
@endsection