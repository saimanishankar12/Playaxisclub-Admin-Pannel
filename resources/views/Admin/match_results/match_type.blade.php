@extends('Admin.layouts.app')
@section('title', 'Match Results — ' . $tournament->name)

@section('content')

{{-- Page Header --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-trophy text-warning mr-2"></i> {{ $tournament->name }}
        </h1>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0 bg-transparent p-0" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('admin-dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin-match-results') }}">Match Results</a></li>
                <li class="breadcrumb-item active">{{ $tournament->name }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin-match-results') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>

{{-- Section label --}}
<p class="text-muted mb-4" style="font-size:14px;">
    <i class="fas fa-hand-pointer mr-1 text-primary"></i>
    Select a match type to view age categories and results.
</p>

{{-- Match Type Cards --}}
<div class="row justify-content-center">
    @php
        $config = [
            'Singles' => [
                'icon'     => 'fas fa-user',
                'gradient' => 'linear-gradient(135deg, #1cc88a, #17a673)',
                'light'    => '#e8faf4',
                'desc'     => 'One-on-one competitive matches',
            ],
            'Doubles' => [
                'icon'     => 'fas fa-user-friends',
                'gradient' => 'linear-gradient(135deg, #4e73df, #224abe)',
                'light'    => '#eef2ff',
                'desc'     => 'Two-player team matches',
            ],
        ];
    @endphp

    @foreach($matchTypes as $type)
    @php $cfg = $config[$type] ?? ['icon'=>'fas fa-gamepad','gradient'=>'linear-gradient(135deg,#f6c23e,#dda20a)','light'=>'#fffbf0','desc'=>'Match type']; @endphp
    <div class="col-xl-4 col-md-5 mb-4">
        <a href="{{ route('admin-match-results.age-categories', [$tournament->id, $type]) }}"
           class="text-decoration-none">
            <div class="card shadow-lg border-0 match-type-card h-100" style="border-radius:16px; overflow:hidden; cursor:pointer; transition: transform .18s, box-shadow .18s;">

                {{-- Top gradient band --}}
                <div style="height:6px; background: {{ $cfg['gradient'] }};"></div>

                <div class="card-body p-4 text-center">
                    {{-- Icon circle --}}
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width:72px; height:72px; background:{{ $cfg['gradient'] }}; box-shadow: 0 8px 20px rgba(0,0,0,.12);">
                        <i class="{{ $cfg['icon'] }} fa-2x text-white"></i>
                    </div>

                    <h4 class="font-weight-bold text-gray-800 mb-1">{{ $type }}</h4>
                    <p class="text-muted mb-3" style="font-size:13px;">{{ $cfg['desc'] }}</p>

                    <span class="btn btn-sm text-white font-weight-bold px-4"
                          style="background: {{ $cfg['gradient'] }}; border-radius:50px; font-size:13px;">
                        Select <i class="fas fa-chevron-right ml-1"></i>
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
    .match-type-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0,0,0,.15) !important;
    }
</style>
@endsection