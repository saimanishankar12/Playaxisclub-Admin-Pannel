@extends('Admin.layouts.app')
@section('title', 'Match Results')

@section('content')

{{-- Page Header --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-trophy text-warning mr-2"></i> Match Results
        </h1>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0 bg-transparent p-0" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('admin-dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Match Results</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Intro Banner --}}
<div class="alert border-left-warning shadow-sm mb-4 py-3" style="background:#fffbf0; border-left: 4px solid #f6c23e !important;">
    <div class="d-flex align-items-center">
        <i class="fas fa-info-circle text-warning fa-lg mr-3"></i>
        <span class="text-gray-700">Select a tournament below to view match results by type and age category.</span>
    </div>
</div>

{{-- Tournament Cards --}}
<div class="row">
    @forelse($tournaments as $tournament)
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="{{ route('admin-match-results.match-types', $tournament->id) }}"
           class="text-decoration-none">
            <div class="card shadow h-100 tournament-card border-0"
                 style="transition: transform .18s, box-shadow .18s; cursor:pointer; border-radius: 14px; overflow:hidden;">

                {{-- Coloured top bar --}}
                <div style="height:5px; background: linear-gradient(90deg, #4e73df, #224abe);"></div>

                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-2"
                                 style="letter-spacing:1px;">Tournament</div>
                            <h5 class="font-weight-bold text-gray-800 mb-1">{{ $tournament->name }}</h5>
                            <span class="badge badge-light text-muted border">
                                {{ $tournament->seasons_count }}
                                Season{{ $tournament->seasons_count != 1 ? 's' : '' }}
                            </span>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:54px; height:54px; background: linear-gradient(135deg,#4e73df22,#4e73df11); flex-shrink:0;">
                            <i class="fas fa-shuttlecock fa-lg text-primary"></i>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-xs text-muted">
                            <i class="fas fa-layer-group mr-1"></i> Singles &amp; Doubles
                        </span>
                        <span class="text-primary font-weight-bold" style="font-size:13px;">
                            View Results <i class="fas fa-arrow-right ml-1"></i>
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12">
        <div class="card shadow border-0 text-center py-5">
            <div class="card-body">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted mb-0">No tournaments found.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

@endsection

@section('scripts')
<style>
    .tournament-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.12) !important;
    }
</style>
@endsection