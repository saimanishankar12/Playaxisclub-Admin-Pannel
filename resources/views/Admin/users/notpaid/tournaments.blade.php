@extends('Admin.layouts.app')
@section('title', 'Not Paid Users — Select Tournament')
@section('page-title', 'Not Paid Users')

@section('styles')
<style>
    .ur-header { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px; }
    .ur-header h2 { font-size:1.25rem;font-weight:700;color:#1e293b;margin:0; }
    .ur-header p  { margin:2px 0 0;font-size:.8rem;color:#64748b; }
    .ur-breadcrumb { display:flex;align-items:center;gap:6px;font-size:.78rem;color:#94a3b8;margin-bottom:20px; }
    .ur-breadcrumb a { color:#64748b;text-decoration:none; }
    .ur-breadcrumb a:hover { color:#1a56db; }
    .ur-breadcrumb span { color:#1e293b;font-weight:600; }
    .ur-section-title { font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:14px; }
    .ur-tournament-list { display:flex;flex-direction:column;gap:12px; }
    .ur-tournament-card { background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.06);overflow:hidden;text-decoration:none;display:block;transition:transform .15s,box-shadow .15s; }
    .ur-tournament-card:hover { transform:translateY(-2px);box-shadow:0 6px 24px rgba(0,0,0,.1);text-decoration:none; }
    .ur-tournament-card-inner { display:flex;align-items:center;gap:16px;padding:18px 20px; }
    .ur-t-icon { width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;background:#fef3c7;color:#d97706; }
    .ur-t-info { flex:1;min-width:0; }
    .ur-t-name  { font-size:.95rem;font-weight:700;color:#1e293b;margin-bottom:4px; }
    .ur-t-sport { display:inline-block;font-size:.65rem;font-weight:600;padding:2px 8px;border-radius:20px;background:#e8f0fe;color:#1a56db; }
    .ur-t-chips { display:flex;gap:8px;margin-top:8px;flex-wrap:wrap; }
    .ur-chip    { display:inline-flex;align-items:center;gap:4px;font-size:.68rem;font-weight:600;padding:3px 9px;border-radius:20px; }
    .ur-chip--s { background:#dbeafe;color:#2563eb; }
    .ur-chip--d { background:#ede9fe;color:#7c3aed; }
    .ur-chip--t { background:#f1f5f9;color:#475569; }
    .ur-t-arrow { color:#94a3b8;font-size:.9rem;flex-shrink:0; }
    .ur-status-badge { display:inline-flex;align-items:center;gap:4px;font-size:.65rem;font-weight:600;padding:3px 8px;border-radius:20px;margin-left:8px; }
    .ur-badge--active   { background:#d1fae5;color:#059669; }
    .ur-badge--upcoming { background:#fef3c7;color:#d97706; }
    .ur-badge--done     { background:#e2e8f0;color:#64748b; }
    .ur-empty { padding:48px;text-align:center;color:#94a3b8;font-size:.875rem;background:#fff;border-radius:14px; }
    .ur-empty i { font-size:2rem;opacity:.25;display:block;margin-bottom:12px; }
</style>
@endsection

@section('content')
<div class="ur-header">
    <div><h2>Not Paid Users</h2><p>Select a tournament to view pending registrations.</p></div>
    <a href="{{ route('admin-dashboard') }}" style="font-size:.8rem;color:#64748b;text-decoration:none;"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
</div>
<div class="ur-breadcrumb">
    <a href="{{ route('admin-users.index') }}"><i class="fas fa-users mr-1"></i>User Reports</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <span>Not Paid Users</span>
</div>
<div class="ur-section-title">Select Tournament</div>
<div class="ur-tournament-list">
    @forelse($seasons as $season)
        <a href="{{ route('admin-users.notpaid.categories', $season->id) }}" class="ur-tournament-card">
            <div class="ur-tournament-card-inner">
                <div class="ur-t-icon"><i class="fas fa-trophy"></i></div>
                <div class="ur-t-info">
                    <div class="ur-t-name">
                        {{ $season->name }}
                        @if($season->status === 'active')
                            <span class="ur-status-badge ur-badge--active"><i class="fas fa-circle" style="font-size:.3rem;"></i> Active</span>
                        @elseif($season->status === 'upcoming')
                            <span class="ur-status-badge ur-badge--upcoming"><i class="fas fa-clock" style="font-size:.5rem;"></i> Upcoming</span>
                        @else
                            <span class="ur-status-badge ur-badge--done"><i class="fas fa-check" style="font-size:.5rem;"></i> Completed</span>
                        @endif
                    </div>
                    <span class="ur-t-sport">{{ $season->sport }}</span>
                    <div class="ur-t-chips">
                        <span class="ur-chip ur-chip--s"><i class="fas fa-user"></i> {{ number_format($season->singles_count) }} Singles</span>
                        <span class="ur-chip ur-chip--d"><i class="fas fa-users"></i> {{ number_format($season->doubles_count) }} Doubles</span>
                        <span class="ur-chip ur-chip--t"><i class="fas fa-list"></i> {{ number_format($season->total_count) }} Total</span>
                    </div>
                </div>
                <div class="ur-t-arrow"><i class="fas fa-chevron-right"></i></div>
            </div>
        </a>
    @empty
        <div class="ur-empty"><i class="fas fa-trophy"></i>No tournaments found.</div>
    @endforelse
</div>
@endsection