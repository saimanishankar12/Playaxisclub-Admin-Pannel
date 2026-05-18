@extends('Admin.layouts.app')
@section('title', 'User Reports')
@section('page-title', 'User Reports')

@section('styles')
<style>
    .ur-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px; }
    .ur-header h2 { font-size:1.25rem; font-weight:700; color:#1e293b; margin:0; }
    .ur-header p  { margin:2px 0 0; font-size:.8rem; color:#64748b; }

    .ur-stats { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:16px; margin-bottom:32px; }
    .ur-stat  { background:#fff; border-radius:14px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.06); position:relative; overflow:hidden; }
    .ur-stat::before { content:''; position:absolute; top:0;left:0;right:0; height:3px; border-radius:14px 14px 0 0; }
    .ur-stat--green::before  { background:#10b981; }
    .ur-stat--red::before    { background:#ef4444; }
    .ur-stat--blue::before   { background:#3b82f6; }
    .ur-stat--purple::before { background:#8b5cf6; }
    .ur-stat--amber::before  { background:#f59e0b; }
    .ur-stat--pink::before   { background:#ec4899; }
    .ur-stat .st-icon { width:36px;height:36px; border-radius:9px; display:flex;align-items:center;justify-content:center; font-size:.9rem; margin-bottom:10px; }
    .ur-stat--green  .st-icon { background:#d1fae5; color:#059669; }
    .ur-stat--red    .st-icon { background:#fee2e2; color:#dc2626; }
    .ur-stat--blue   .st-icon { background:#dbeafe; color:#2563eb; }
    .ur-stat--purple .st-icon { background:#ede9fe; color:#7c3aed; }
    .ur-stat--amber  .st-icon { background:#fef3c7; color:#d97706; }
    .ur-stat--pink   .st-icon { background:#fce7f3; color:#db2777; }
    .ur-stat .st-val   { font-size:1.7rem; font-weight:800; color:#1e293b; line-height:1; font-family:'DM Mono',monospace; }
    .ur-stat .st-label { font-size:.68rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-top:4px; }

    .ur-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:20px; }
    .ur-card  { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.08); overflow:hidden; transition:transform .18s,box-shadow .18s; text-decoration:none; display:block; }
    .ur-card:hover { transform:translateY(-3px); box-shadow:0 8px 32px rgba(0,0,0,.12); text-decoration:none; }
    .ur-card-band { height:5px; }
    .ur-card--paid    .ur-card-band { background:linear-gradient(90deg,#10b981,#34d399); }
    .ur-card--notpaid .ur-card-band { background:linear-gradient(90deg,#f59e0b,#fbbf24); }
    .ur-card-body { padding:24px; }
    .ur-card-icon { width:52px;height:52px; border-radius:14px; display:flex;align-items:center;justify-content:center; font-size:1.3rem; margin-bottom:16px; }
    .ur-card--paid    .ur-card-icon { background:#d1fae5; color:#059669; }
    .ur-card--notpaid .ur-card-icon { background:#fef3c7; color:#d97706; }
    .ur-card-title { font-size:1.1rem; font-weight:700; color:#1e293b; margin-bottom:6px; }
    .ur-card-desc  { font-size:.8rem; color:#64748b; margin-bottom:16px; line-height:1.5; }
    .ur-card-meta  { display:flex; gap:12px; }
    .ur-card-chip  { display:inline-flex;align-items:center;gap:5px; font-size:.72rem; font-weight:600; padding:4px 10px; border-radius:20px; }
    .ur-card--paid    .ur-card-chip { background:#d1fae5; color:#059669; }
    .ur-card--notpaid .ur-card-chip { background:#fef3c7; color:#d97706; }
    .ur-card-arrow { margin-left:auto; font-size:.9rem; color:#94a3b8; align-self:center; }
</style>
@endsection

@section('content')

<div class="ur-header">
    <div>
        <h2>User Reports</h2>
        <p>Browse paid and pending registrations across all tournaments.</p>
    </div>
    <a href="{{ route('admin-dashboard') }}" style="font-size:.8rem;color:#64748b;text-decoration:none;">
        <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
    </a>
</div>

{{-- Stats Row --}}
<div class="ur-stats">
    <div class="ur-stat ur-stat--green">
        <div class="st-icon"><i class="fas fa-user-check"></i></div>
        <div class="st-val">{{ number_format($totalPaid) }}</div>
        <div class="st-label">Total Paid</div>
    </div>
    <div class="ur-stat ur-stat--red">
        <div class="st-icon"><i class="fas fa-user-clock"></i></div>
        <div class="st-val">{{ number_format($totalPending) }}</div>
        <div class="st-label">Total Pending</div>
    </div>
    <div class="ur-stat ur-stat--blue">
        <div class="st-icon"><i class="fas fa-user"></i></div>
        <div class="st-val">{{ number_format($paidSingles) }}</div>
        <div class="st-label">Paid Singles</div>
    </div>
  
    <div class="ur-stat ur-stat--amber">
        <div class="st-icon"><i class="fas fa-hourglass-half"></i></div>
        <div class="st-val">{{ number_format($pendingSingles) }}</div>
        <div class="st-label">Pending Singles</div>
    </div>
      <div class="ur-stat ur-stat--purple">
        <div class="st-icon"><i class="fas fa-users"></i></div>
        <div class="st-val">{{ number_format($paidDoubles) }}</div>
        <div class="st-label">Paid Doubles</div>
    </div>
    <div class="ur-stat ur-stat--pink">
        <div class="st-icon"><i class="fas fa-hourglass-half"></i></div>
        <div class="st-val">{{ number_format($pendingDoubles) }}</div>
        <div class="st-label">Pending Doubles</div>
    </div>

    <div class="ur-stat ur-stat--blue">
    <div class="st-icon"><i class="fas fa-users"></i></div>
    <div class="st-val">{{ number_format($totalPlayers) }}</div>
    <div class="st-label">Total Players</div>
</div>

</div>

{{-- Main Cards --}}
<div class="ur-cards">

    <a href="{{ route('admin-users.paid.tournaments') }}" class="ur-card ur-card--paid">
        <div class="ur-card-band"></div>
        <div class="ur-card-body">
            <div class="ur-card-icon"><i class="fas fa-user-check"></i></div>
            <div class="ur-card-title">Paid Users</div>
            <div class="ur-card-desc">View all registered users who have successfully completed payment in PlayAxisClub. Browse participants by tournament and manage registrations efficiently.</div>
            <div class="ur-card-meta">
                <!-- <span class="ur-card-chip"><i class="fas fa-user"></i> {{ number_format($paidSingles) }} Singles</span>
                <span class="ur-card-chip"><i class="fas fa-users"></i> {{ number_format($paidDoubles) }} Doubles</span> -->
                <span class="ur-card-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
        </div>
    </a>

    <a href="{{ route('admin-users.notpaid.tournaments') }}" class="ur-card ur-card--notpaid">
        <div class="ur-card-band"></div>
        <div class="ur-card-body">
            <div class="ur-card-icon"><i class="fas fa-user-clock"></i></div>
            <div class="ur-card-title">Not Paid Users</div>
            <div class="ur-card-desc">View all registered users who have not yet completed payment in PlayAxisClub. Track pending registrations and take necessary follow-up actions efficiently.</div>
            <div class="ur-card-meta">
                <!-- <span class="ur-card-chip"><i class="fas fa-user"></i> {{ number_format($pendingSingles) }} Singles</span>
                <span class="ur-card-chip"><i class="fas fa-users"></i> {{ number_format($pendingDoubles) }} Doubles</span> -->
                <span class="ur-card-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
        </div>
    </a>

</div>

@endsection