@extends('Admin.layouts.app')
@section('title', 'Paid Users — ' . $season->name)
@section('page-title', 'Paid Users')

@section('styles')
<style>
    .ur-header { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px; }
    .ur-header h2 { font-size:1.25rem;font-weight:700;color:#1e293b;margin:0; }
    .ur-header p  { margin:2px 0 0;font-size:.8rem;color:#64748b; }
    .ur-breadcrumb { display:flex;align-items:center;gap:6px;font-size:.78rem;color:#94a3b8;margin-bottom:24px; }
    .ur-breadcrumb a { color:#64748b;text-decoration:none; }
    .ur-breadcrumb a:hover { color:#1a56db; }
    .ur-breadcrumb span { color:#1e293b;font-weight:600; }
    .ur-cat-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px; }
    .ur-cat-card { background:#fff;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.08);overflow:hidden;text-decoration:none;display:block;transition:transform .15s,box-shadow .15s; }
    .ur-cat-card:hover { transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.12);text-decoration:none; }
    .ur-cat-band { height:4px; }
    .ur-cat-card--singles .ur-cat-band { background:linear-gradient(90deg,#3b82f6,#60a5fa); }
    .ur-cat-card--doubles .ur-cat-band { background:linear-gradient(90deg,#8b5cf6,#a78bfa); }
    .ur-cat-body { padding:24px; }
    .ur-cat-icon { width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin-bottom:16px; }
    .ur-cat-card--singles .ur-cat-icon { background:#dbeafe;color:#2563eb; }
    .ur-cat-card--doubles .ur-cat-icon { background:#ede9fe;color:#7c3aed; }
    .ur-cat-title { font-size:1.1rem;font-weight:700;color:#1e293b;margin-bottom:6px; }
    .ur-cat-desc  { font-size:.8rem;color:#64748b;margin-bottom:16px;line-height:1.5; }
    .ur-cat-count { display:inline-flex;align-items:center;gap:6px;font-size:1.4rem;font-weight:800;color:#1e293b;font-family:'DM Mono',monospace; }
    .ur-cat-count small { font-size:.7rem;font-weight:600;color:#64748b;font-family:inherit; }
    .ur-cat-arrow { float:right;color:#94a3b8;margin-top:4px; }
</style>
@endsection

@section('content')
<div class="ur-header">
    <div>
        <h2>{{ $season->name }}</h2>
        <p>Paid registrations — choose a category.</p>
    </div>
    <a href="{{ route('admin-dashboard') }}" style="font-size:.8rem;color:#64748b;text-decoration:none;"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
</div>
<div class="ur-breadcrumb">
    <a href="{{ route('admin-users.index') }}">User Reports</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <a href="{{ route('admin-users.paid.tournaments') }}">Paid Users</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <span>{{ $season->name }}</span>
</div>
<div class="ur-cat-grid">
    <a href="{{ route('admin-users.paid.singles.age', $season->id) }}" class="ur-cat-card ur-cat-card--singles">
        <div class="ur-cat-band"></div>
        <div class="ur-cat-body">
            <div class="ur-cat-icon"><i class="fas fa-user"></i></div>
            <div class="ur-cat-title">Singles</div>
            <div class="ur-cat-desc">Individual paid registrations for this tournament.</div>
            <div class="ur-cat-count">{{ number_format($singlesCount) }} <small>players</small></div>
            <span class="ur-cat-arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
    </a>
    <a href="{{ route('admin-users.paid.doubles.age', $season->id) }}" class="ur-cat-card ur-cat-card--doubles">
        <div class="ur-cat-band"></div>
        <div class="ur-cat-body">
            <div class="ur-cat-icon"><i class="fas fa-users"></i></div>
            <div class="ur-cat-title">Doubles</div>
            <div class="ur-cat-desc">Pair registrations who have completed payment.</div>
            <div class="ur-cat-count">{{ number_format($doublesCount) }} <small>players</small></div>
            <span class="ur-cat-arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
    </a>
</div>
@endsection