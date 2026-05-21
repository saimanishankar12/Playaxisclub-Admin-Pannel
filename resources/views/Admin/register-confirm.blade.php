@extends('Admin.layouts.app')

@section('title', 'Registration Confirmation')

@section('content')

<style>
    .rc-wrap { max-width: 700px; margin: 0 auto; padding: 28px 20px; }
    .rc-success-icon { text-align:center; font-size:3rem; margin-bottom:8px; }
    .rc-title { text-align:center; font-size:1.3rem; font-weight:800; color:#1f3fae; margin:0 0 4px; }
    .rc-subtitle { text-align:center; font-size:.82rem; color:#64748b; margin:0 0 24px; }

    .rc-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; margin-bottom:16px; box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .rc-card-title { font-size:.7rem; font-weight:700; color:#1f3fae; text-transform:uppercase; letter-spacing:.08em; margin:0 0 16px; }

    .rc-row { display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid #f1f5f9; }
    .rc-row:last-child { border-bottom:none; }
    .rc-label { font-size:.78rem; color:#64748b; }
    .rc-value { font-size:.82rem; font-weight:700; color:#1e293b; text-align:right; }
    .rc-value--id { color:#1f3fae; font-family:'DM Mono',monospace; letter-spacing:.04em; }
    .rc-value--paid { color:#16a34a; }
    .rc-value--pending { color:#d97706; }

    .rc-divider { display:flex; align-items:center; gap:12px; margin:20px 0; }
    .rc-divider::before,.rc-divider::after { content:''; flex:1; height:1px; background:#e2e8f0; }
    .rc-divider span { font-size:.7rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.08em; }

    .rc-actions { display:flex; gap:10px; margin-top:20px; }
    .rc-btn { flex:1; padding:11px; border-radius:10px; font-size:.85rem; font-weight:700; border:none; cursor:pointer; text-align:center; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:6px; transition:all .18s; }
    .rc-btn--primary { background:#1f3fae; color:#fff; }
    .rc-btn--primary:hover { background:#1a35a0; color:#fff; }
    .rc-btn--secondary { background:#f1f5f9; color:#1e293b; border:1.5px solid #e2e8f0; }
    .rc-btn--secondary:hover { background:#e2e8f0; }

    .rc-pending-notice { background:#fefce8; border:1.5px solid #fde68a; border-radius:10px; padding:12px 16px; font-size:.78rem; color:#92400e; display:flex; align-items:center; gap:8px; margin-bottom:16px; }
</style>

<div class="rc-wrap">

    
    <h1 class="rc-title">Player Registered Successfully!</h1>
    <p class="rc-subtitle">Registration saved. Payment status is <strong>Pending</strong>.</p>

    <div class="rc-pending-notice">
        <i class="fas fa-info-circle"></i>
        Payment is <strong>pending</strong>. Player must complete payment to confirm tournament spot.
    </div>

    {{-- ── SINGLES ── --}}
    @if($mode === 'singles')
    <div class="rc-card">
        <p class="rc-card-title">Registration Details</p>
        <div class="rc-row">
            <span class="rc-label">PlayAxisClub ID</span>
            <span class="rc-value rc-value--id">{{ $player->player_id }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Season ID</span>
            <span class="rc-value rc-value--id">{{ $player->season_id }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Full Name</span>
            <span class="rc-value">{{ $player->name }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Email</span>
            <span class="rc-value">{{ $player->email }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Phone</span>
            <span class="rc-value">{{ $player->phone }}</span>
        </div>
     
        <div class="rc-row">
            <span class="rc-label">Age Category</span>
            <span class="rc-value">{{ $player->age }}</span>
        </div>

        <div class="rc-row">
            <span class="rc-label">Mode</span>
            <span class="rc-value">Singles</span>
        </div>
    </div>

    <div class="rc-card">
        <p class="rc-card-title">Payment Details</p>
        <div class="rc-row">
            <span class="rc-label">Status</span>
            <span class="rc-value rc-value--pending">⏳ Pending</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Amount</span>
            <span class="rc-value">₹650</span>
        </div>
    </div>
    @endif

    {{-- ── DOUBLES ── --}}
    @if($mode === 'doubles')
    <div class="rc-card">
        <p class="rc-card-title">Player 1 — Registration Details</p>
        <div class="rc-row">
            <span class="rc-label">PlayAxisClub ID</span>
            <span class="rc-value rc-value--id">{{ $p1->player_id }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Season / Team ID</span>
            <span class="rc-value rc-value--id">{{ $p1->season_id }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Full Name</span>
            <span class="rc-value">{{ $p1->name }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Email</span>
            <span class="rc-value">{{ $p1->email }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Phone</span>
            <span class="rc-value">{{ $p1->phone }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Age Category</span>
            <span class="rc-value">{{ $p1->age }}</span>
        </div>
       
    </div>

    <div class="rc-divider"><span>Player 2</span></div>

    <div class="rc-card">
        <p class="rc-card-title">Player 2 — Registration Details</p>
        <div class="rc-row">
            <span class="rc-label">PlayAxisClub ID</span>
            <span class="rc-value rc-value--id">{{ $p2->player_id }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Season / Team ID</span>
            <span class="rc-value rc-value--id">{{ $p2->season_id }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Full Name</span>
            <span class="rc-value">{{ $p2->name }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Email</span>
            <span class="rc-value">{{ $p2->email }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Phone</span>
            <span class="rc-value">{{ $p2->phone }}</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Age Category</span>
            <span class="rc-value">{{ $p2->age }}</span>
        </div>
       
    </div>

    <div class="rc-card">
        <p class="rc-card-title">Payment Details</p>
        <div class="rc-row">
            <span class="rc-label">Status</span>
            <span class="rc-value rc-value--pending">⏳ Pending</span>
        </div>
        <div class="rc-row">
            <span class="rc-label">Amount</span>
            <span class="rc-value">₹1300</span>
        </div>
    </div>
    @endif

    <div class="rc-actions">
        <a href="{{ route('admin-register-player') }}" class="rc-btn rc-btn--secondary">
            <i class="fas fa-plus"></i> Register Another
        </a>
        <a href="{{ route('admin-dashboard') }}" class="rc-btn rc-btn--primary">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
    </div>

</div>

@endsection