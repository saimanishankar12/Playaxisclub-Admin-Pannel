
@extends('Admin.layouts.app')

@section('title', 'Register Player')

@section('content')

<style>
    .rp-wrap        { max-width: 860px; margin: 0 auto; padding: 28px 20px; }
    .rp-header h1   { font-size: 1.6rem; font-weight: 700; color: #1a1a2e; margin: 0 0 4px; }
    .rp-header p    { font-size: 0.85rem; color: #6b7280; margin: 0 0 24px; }
    .rp-header p span { color: #d97706; font-weight: 600; }

    /* Success */
    .rp-success     { display:flex; align-items:flex-start; gap:12px; background:#f0fdf4; border:1px solid #86efac; border-radius:12px; padding:16px 20px; margin-bottom:20px; }
    .rp-success-icon{ font-size:1.2rem; margin-top:2px; }
    .rp-success h4  { margin:0 0 2px; font-size:0.9rem; color:#15803d; }
    .rp-success p   { margin:0; font-size:0.85rem; color:#16a34a; }

    /* Toggle buttons */
    .rp-toggle      { display:flex; gap:10px; margin-bottom:24px; }
    .rp-toggle button { padding:9px 24px; border-radius:50px; font-size:0.85rem; font-weight:600; border:none; cursor:pointer; transition:all .2s; }
    .rp-btn-active  { background:#1f3fae; color:#fff; box-shadow:0 2px 8px rgba(31,63,174,.3); }
    .rp-btn-inactive{ background:#e5e7eb; color:#374151; }
    .rp-btn-inactive:hover { background:#d1d5db; }

    /* Card */
    .rp-card        { background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:24px; margin-bottom:16px; }
    .rp-card-title  { font-size:0.75rem; font-weight:700; color:#1f3fae; text-transform:uppercase; letter-spacing:.08em; margin:0 0 20px; }

    /* Error box */
    .rp-errors      { background:#fef2f2; border:1px solid #fca5a5; border-radius:12px; padding:14px 18px; margin-bottom:18px; }
    .rp-errors p    { margin:0 0 6px; font-size:0.85rem; font-weight:600; color:#dc2626; }
    .rp-errors ul   { margin:0; padding-left:18px; }
    .rp-errors li   { font-size:0.82rem; color:#ef4444; margin-bottom:2px; }

    /* Grid */
    .rp-grid        { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .rp-full        { grid-column: 1 / -1; }

    /* Labels & inputs */
    .rp-field label { display:block; font-size:0.75rem; font-weight:500; color:#4b5563; margin-bottom:5px; }
    .rp-field label span { color:#ef4444; }
    .rp-field input,
    .rp-field select,
    .rp-field textarea {
        width:100%; box-sizing:border-box;
        border:1px solid #d1d5db; background:#f9fafb;
        border-radius:8px; padding:10px 14px;
        font-size:0.85rem; color:#111827;
        outline:none; transition:border .2s;
        font-family:inherit;
    }
    .rp-field input:focus,
    .rp-field select:focus,
    .rp-field textarea:focus { border-color:#1f3fae; background:#fff; }
    .rp-field textarea { resize:vertical; min-height:70px; }
    .rp-field .rp-err { font-size:0.75rem; color:#ef4444; margin:4px 0 0; }

    /* File input */
    .rp-field input[type="file"] {
        padding:7px 10px; cursor:pointer;
    }

    /* Submit button */
    .rp-submit      { width:100%; background:#1f3fae; color:#fff; border:none; border-radius:10px; padding:13px; font-size:0.9rem; font-weight:600; cursor:pointer; margin-top:6px; transition:background .2s; }
    .rp-submit:hover{ background:#1a35a0; }

    /* Hidden */
    .rp-hidden      { display:none !important; }

    @media(max-width:600px){
        .rp-grid { grid-template-columns:1fr; }
        .rp-full { grid-column:1; }
    }
</style>

<div class="rp-wrap">

    {{-- Header --}}
    <div class="rp-header">
        <h1>Register Player</h1>
        <p>Manually register a player. Payment status will be set to <span>Pending</span>.</p>
    </div>

    {{-- Success --}}
    @if(session('success'))
        <div class="rp-success">
            <span class="rp-success-icon">✅</span>
            <div>
                <h4>Registration Successful</h4>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Toggle --}}
    <div class="rp-toggle">
        <button type="button" id="btn-singles" onclick="switchMode('singles')" class="rp-btn-active">Singles</button>
        <button type="button" id="btn-doubles" onclick="switchMode('doubles')" class="rp-btn-inactive">Doubles</button>
    </div>

    {{-- ── SINGLES FORM ── --}}
    <div id="form-singles">
        <form action="{{ route('admin-admin-register-player.singles') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_form_type" value="singles">

            @if($errors->any() && old('_form_type') === 'singles')
                <div class="rp-errors">
                    <p>Please fix the following errors:</p>
                    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="rp-card">
                <p class="rp-card-title">Player Details</p>
                @include('Admin.partials.player-fields', ['prefix' => '', 'states' => $states, 'old_prefix' => ''])
            </div>

            <button type="submit" class="rp-submit">Register Singles Player</button>
        </form>
    </div>

    {{-- ── DOUBLES FORM ── --}}
    <div id="form-doubles" class="rp-hidden">
        <form action="{{ route('admin-admin-register-player.doubles') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_form_type" value="doubles">

            @if($errors->any() && old('_form_type') === 'doubles')
                <div class="rp-errors">
                    <p>Please fix the following errors:</p>
                    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="rp-card">
                <p class="rp-card-title">Player 1</p>
                @include('Admin.partials.player-fields', ['prefix' => 'player1_', 'states' => $states, 'old_prefix' => 'player1_'])
            </div>

            <div class="rp-card">
                <p class="rp-card-title">Player 2</p>
                @include('Admin.partials.player-fields', ['prefix' => 'player2_', 'states' => $states, 'old_prefix' => 'player2_'])
            </div>

            <button type="submit" class="rp-submit">Register Doubles Pair</button>
        </form>
    </div>

</div>

<script>
    function switchMode(mode) {
        const singles = document.getElementById('form-singles');
        const doubles = document.getElementById('form-doubles');
        const btnS    = document.getElementById('btn-singles');
        const btnD    = document.getElementById('btn-doubles');

        if (mode === 'singles') {
            singles.classList.remove('rp-hidden');
            doubles.classList.add('rp-hidden');
            btnS.className = 'rp-btn-active';
            btnD.className = 'rp-btn-inactive';
        } else {
            doubles.classList.remove('rp-hidden');
            singles.classList.add('rp-hidden');
            btnD.className = 'rp-btn-active';
            btnS.className = 'rp-btn-inactive';
        }
    }

    @if(old('_form_type') === 'doubles')
        switchMode('doubles');
    @endif
</script>

@endsection