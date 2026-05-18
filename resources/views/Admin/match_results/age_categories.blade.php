@extends('Admin.layouts.app')
@section('title', 'Age Categories — ' . $matchType)

@section('content')

{{-- Page Header --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-{{ $matchType === 'Singles' ? 'user' : 'user-friends' }} text-success mr-2"></i>
            {{ $tournament->name }} — {{ $matchType }}
        </h1>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0 bg-transparent p-0" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('admin-dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin-match-results') }}">Match Results</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin-match-results.match-types', $tournament->id) }}">{{ $tournament->name }}</a></li>
                <li class="breadcrumb-item active">{{ $matchType }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin-match-results.match-types', $tournament->id) }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>

<p class="text-muted mb-4" style="font-size:14px;">
    <i class="fas fa-hand-pointer mr-1 text-success"></i>
    Select an age category to view player results.
</p>

{{-- Age Category Cards --}}
@php
    $colors = [
        'U11' => ['bg' => '#fd7f6f', 'light' => '#fff5f4', 'label' => 'Under 11'],
        'U13' => ['bg' => '#f9a03f', 'light' => '#fffbf0', 'label' => 'Under 13'],
        'U15' => ['bg' => '#4e73df', 'light' => '#eef2ff', 'label' => 'Under 15'],
        'U19' => ['bg' => '#1cc88a', 'light' => '#e8faf4', 'label' => 'Under 19'],
    ];
@endphp

<div class="row">
    @foreach($ageCategories as $index => $age)
    @php
        $color = $colors[$age] ?? ['bg' => '#858796', 'light' => '#f8f9fc', 'label' => $age];
    @endphp
    <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
        <a href="{{ route('admin-match-results.results', [$tournament->id, $matchType, $age]) }}"
           class="text-decoration-none">
            <div class="card border-0 shadow age-card h-100"
                 style="border-radius:14px; overflow:hidden; cursor:pointer; transition: transform .18s, box-shadow .18s;">

                <div style="height:5px; background: {{ $color['bg'] }};"></div>

                <div class="card-body p-4 text-center" style="background:{{ $color['light'] }};">
                    {{-- Big age badge --}}
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle font-weight-bold"
                         style="width:70px; height:70px; background:{{ $color['bg'] }}; color:#fff; font-size:22px; letter-spacing:1px; box-shadow: 0 6px 16px {{ $color['bg'] }}55;">
                        {{ $age }}
                    </div>

                    <h5 class="font-weight-bold text-gray-800 mb-1">{{ $color['label'] }}</h5>
                    <p class="text-muted mb-3" style="font-size:12px;">{{ $matchType }} Category</p>

                    <span class="btn btn-sm text-white font-weight-bold px-3"
                          style="background:{{ $color['bg'] }}; border-radius:50px; font-size:12px;">
                        View Results <i class="fas fa-arrow-right ml-1"></i>
                    </span>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

@endsection

@section('scripts')
<style>
    .age-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.13) !important;
    }
</style>
@endsection