@extends('Admin.layouts.app')
@section('title', 'Pending Singles — Select Age — ' . $season->name)
@section('page-title', 'Pending Singles')

@section('styles')
<style>
    .ur-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
    .ur-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}
    .ur-header p{margin:2px 0 0;font-size:.8rem;color:#64748b;}
    .ur-breadcrumb{display:flex;align-items:center;gap:6px;font-size:.78rem;color:#94a3b8;margin-bottom:28px;flex-wrap:wrap;}
    .ur-breadcrumb a{color:#64748b;text-decoration:none;}
    .ur-breadcrumb a:hover{color:#1a56db;}
    .ur-breadcrumb span{color:#1e293b;font-weight:600;}
    .ur-age-intro{margin-bottom:20px;}
    .ur-age-intro h5{font-size:1rem;font-weight:700;color:#1e293b;margin-bottom:4px;}
    .ur-age-intro p{font-size:.8rem;color:#64748b;}
    .ur-age-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;}
    .ur-age-card{background:#fff;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.08);overflow:hidden;text-decoration:none;display:block;transition:transform .15s,box-shadow .15s;position:relative;}
    .ur-age-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.13);text-decoration:none;}
    .ur-age-card-band{height:4px;}
    .ur-age-card--u11 .ur-age-card-band{background:linear-gradient(90deg,#f59e0b,#fcd34d);}
    .ur-age-card--u13 .ur-age-card-band{background:linear-gradient(90deg,#3b82f6,#93c5fd);}
    .ur-age-card--u15 .ur-age-card-band{background:linear-gradient(90deg,#10b981,#6ee7b7);}
    .ur-age-card--u19 .ur-age-card-band{background:linear-gradient(90deg,#8b5cf6,#c4b5fd);}
    .ur-age-card-body{padding:22px 20px;}
    .ur-age-label{font-size:1.8rem;font-weight:900;color:#1e293b;line-height:1;font-family:'DM Mono',monospace;margin-bottom:6px;}
    .ur-age-sub{font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;}
    .ur-age-count{display:inline-flex;align-items:center;gap:6px;font-size:.8rem;font-weight:700;padding:5px 12px;border-radius:20px;}
    .ur-age-card--u11 .ur-age-count{background:#fef3c7;color:#d97706;}
    .ur-age-card--u13 .ur-age-count{background:#dbeafe;color:#2563eb;}
    .ur-age-card--u15 .ur-age-count{background:#d1fae5;color:#059669;}
    .ur-age-card--u19 .ur-age-count{background:#ede9fe;color:#7c3aed;}
    .ur-age-card-arrow{position:absolute;bottom:20px;right:20px;color:#cbd5e1;font-size:.9rem;}
    .ur-age-card--zero{opacity:.5;pointer-events:none;}
</style>
@endsection

@section('content')
<div class="ur-header">
    <div>
        <h2>Pending Singles — Select Age Category</h2>
        <p>{{ $season->name }}</p>
    </div>
    <a href="{{ route('admin-dashboard') }}" style="font-size:.8rem;color:#64748b;text-decoration:none;"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
</div>
<div class="ur-breadcrumb">
    <a href="{{ route('admin-users.index') }}">User Reports</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <a href="{{ route('admin-users.notpaid.tournaments') }}">Not Paid Users</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <a href="{{ route('admin-users.notpaid.categories', $season->id) }}">{{ $season->name }}</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <span>Singles — Age</span>
</div>
<div class="ur-age-intro">
    <h5>Which age category?</h5>
    <p>Select an age group to view pending singles registrations.</p>
</div>
@php
    $colorMap = ['U-11'=>'u11','U-13'=>'u13','U-15'=>'u15','U-19'=>'u19'];
    $descMap  = ['U-11'=>'Under 11 years','U-13'=>'Under 13 years','U-15'=>'Under 15 years','U-19'=>'Under 19 years'];
@endphp
<div class="ur-age-grid">
    @foreach($ageCategories as $age)
    @php $cls = $colorMap[$age] ?? 'u11'; $cnt = $ageCounts[$age] ?? 0; @endphp
    <a href="{{ $cnt > 0 ? route('admin-users.notpaid.singles', [$season->id, urlencode($age)]) : '#' }}"
       class="ur-age-card ur-age-card--{{ $cls }} {{ $cnt === 0 ? 'ur-age-card--zero' : '' }}">
        <div class="ur-age-card-band"></div>
        <div class="ur-age-card-body">
            <div class="ur-age-label">{{ $age }}</div>
            <div class="ur-age-sub">{{ $descMap[$age] ?? '' }}</div>
            <span class="ur-age-count"><i class="fas fa-user" style="font-size:.65rem;"></i> {{ number_format($cnt) }} player{{ $cnt !== 1 ? 's' : '' }}</span>
        </div>
        <div class="ur-age-card-arrow"><i class="fas fa-chevron-right"></i></div>
    </a>
    @endforeach
</div>
@endsection