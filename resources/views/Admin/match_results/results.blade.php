@extends('Admin.layouts.app')
@section('title', 'Results — ' . $ageCategory . ' ' . $matchType)

@section('content')

{{-- Page Header --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-medal text-warning mr-2"></i>
            {{ $ageCategory }} {{ $matchType }} Results
        </h1>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0 bg-transparent p-0" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="{{ route('admin-dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin-match-results') }}">Match Results</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin-match-results.match-types', $tournament->id) }}">{{ $tournament->name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin-match-results.age-categories', [$tournament->id, $matchType]) }}">{{ $matchType }}</a></li>
                <li class="breadcrumb-item active">{{ $ageCategory }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin-match-results.age-categories', [$tournament->id, $matchType]) }}"
       class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>

{{-- Summary Cards --}}
<div class="row mb-4">

    {{-- Total Players --}}
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Players</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['total_players']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Matches --}}
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Matches Played</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['total_matches']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-table-tennis fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Wins --}}
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Wins</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['total_wins']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Losses --}}
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Losses</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['total_losses']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Win Rate Progress (bonus visual) --}}
@if($summary['total_matches'] > 0)
@php $winRate = round(($summary['total_wins'] / $summary['total_matches']) * 100, 1); @endphp
<div class="card shadow mb-4 border-0">
    <div class="card-body py-3 px-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-xs font-weight-bold text-success text-uppercase">Overall Win Rate</span>
            <span class="font-weight-bold text-gray-800">{{ $winRate }}%</span>
        </div>
        <div class="progress" style="height:10px; border-radius:5px;">
            <div class="progress-bar bg-success" style="width:{{ $winRate }}%; border-radius:5px;"></div>
        </div>
    </div>
</div>
@endif

{{-- Results Table --}}
<div class="card shadow mb-4 border-0">
    <div class="card-header py-3 d-flex align-items-center justify-content-between" style="background:#fff; border-bottom:2px solid #e3e6f0;">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list-alt mr-2"></i>
            Player Results —
            <span class="text-gray-600">{{ $tournament->name }}</span>
            <span class="badge badge-primary ml-2">{{ $matchType }}</span>
            <span class="badge badge-warning text-dark ml-1">{{ $ageCategory }}</span>
        </h6>
        <span class="badge badge-light border text-muted">
            {{ $summary['total_players'] }} Player{{ $summary['total_players'] != 1 ? 's' : '' }}
        </span>
    </div>
    <div class="card-body p-0">
        @if($players->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3 d-block"></i>
                <p class="text-muted mb-0">No results found for this category.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="resultsTable">
                <thead style="background: linear-gradient(90deg,#4e73df11,#4e73df05);">
                    <tr>
                        <th class="border-0 pl-4" style="font-size:11px; letter-spacing:1px; color:#5a5c69; font-weight:700;">#</th>
                        <th class="border-0" style="font-size:11px; letter-spacing:1px; color:#5a5c69; font-weight:700;">PLAYER ID</th>
                        <th class="border-0" style="font-size:11px; letter-spacing:1px; color:#5a5c69; font-weight:700;">SEASON ID</th>
                        <th class="border-0" style="font-size:11px; letter-spacing:1px; color:#5a5c69; font-weight:700;">MATCH TYPE</th>
                        <th class="border-0" style="font-size:11px; letter-spacing:1px; color:#5a5c69; font-weight:700;">AGE CATEGORY</th>
                        <th class="border-0 text-center" style="font-size:11px; letter-spacing:1px; color:#5a5c69; font-weight:700;">TOTAL MATCHES</th>
                        <th class="border-0 text-center" style="font-size:11px; letter-spacing:1px; color:#5a5c69; font-weight:700;">WON</th>
                        <th class="border-0 text-center" style="font-size:11px; letter-spacing:1px; color:#5a5c69; font-weight:700;">LOST</th>
                        <th class="border-0 text-center" style="font-size:11px; letter-spacing:1px; color:#5a5c69; font-weight:700;">WIN RATE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($players as $i => $player)
                    @php
                        $winRate = $player->total_matches > 0
                            ? round(($player->won / $player->total_matches) * 100)
                            : 0;
                        $barColor = $winRate >= 70 ? '#1cc88a' : ($winRate >= 40 ? '#f6c23e' : '#e74a3b');
                    @endphp
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td class="pl-4 align-middle text-muted" style="font-size:13px;">{{ $i + 1 }}</td>

                        <td class="align-middle">
                            <span class="font-weight-bold text-primary" style="font-size:13px;">
                                {{ $player->player_id }}
                            </span>
                        </td>

                        <td class="align-middle">
                            <span class="badge badge-light border text-gray-700">{{ $player->season_id }}</span>
                        </td>

                        <td class="align-middle">
                            <span class="badge {{ $player->match_type === 'Singles' ? 'badge-success' : 'badge-primary' }}">
                                <i class="fas fa-{{ $player->match_type === 'Singles' ? 'user' : 'user-friends' }} mr-1"></i>
                                {{ $player->match_type }}
                            </span>
                        </td>

                        <td class="align-middle">
                            <span class="badge badge-warning text-dark">{{ $player->age_category }}</span>
                        </td>

                        <td class="align-middle text-center font-weight-bold text-gray-800">
                            {{ $player->total_matches }}
                        </td>

                        <td class="align-middle text-center">
                            <span class="font-weight-bold" style="color:#1cc88a; font-size:14px;">
                                {{ $player->won }}
                            </span>
                        </td>

                        <td class="align-middle text-center">
                            <span class="font-weight-bold" style="color:#e74a3b; font-size:14px;">
                                {{ $player->lost }}
                            </span>
                        </td>

                        <td class="align-middle" style="min-width:100px;">
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 mr-2" style="height:7px; border-radius:4px; background:#f0f0f0;">
                                    <div class="progress-bar"
                                         style="width:{{ $winRate }}%; background:{{ $barColor }}; border-radius:4px;"></div>
                                </div>
                                <small class="font-weight-bold" style="color:{{ $barColor }}; font-size:11px; white-space:nowrap;">
                                    {{ $winRate }}%
                                </small>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                {{-- Totals footer --}}
                <tfoot style="background:#f8f9fc; border-top:2px solid #e3e6f0;">
                    <tr>
                        <td colspan="5" class="text-right font-weight-bold pl-4 text-gray-700" style="font-size:12px;">
                            TOTALS
                        </td>
                        <td class="text-center font-weight-bold text-gray-800">{{ $summary['total_matches'] }}</td>
                        <td class="text-center font-weight-bold" style="color:#1cc88a;">{{ $summary['total_wins'] }}</td>
                        <td class="text-center font-weight-bold" style="color:#e74a3b;">{{ $summary['total_losses'] }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection