@extends('Admin.layouts.app')
@section('title', 'Revenue – {{ $tournament->name }}')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('admin-revenue') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="fas fa-arrow-left mr-1"></i> Back to Revenue
        </a>
        <h1 class="h3 mb-0 text-gray-800">{{ $tournament->name }}</h1>
        <small class="text-muted">{{ $tournament->sport->name ?? '' }}</small>
    </div>
</div>

{{-- Summary Card --}}
<div class="row mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tournament Total Revenue</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">₹{{ number_format($tournamentTotal, 2) }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-rupee-sign fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Seasons Breakdown --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Season Breakdown</h6>
    </div>
    <div class="card-body">
        @if($seasons->isEmpty())
            <p class="text-center text-muted">No seasons found for this tournament.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Season</th>
                            <th>Status</th>
                            <th>Singles Registrations</th>
                            <th>Singles Revenue</th>
                            <th>Doubles Registrations</th>
                            <th>Doubles Revenue</th>
                            <th>Total Revenue</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($seasons as $i => $season)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $season->label ?? $season->name ?? 'Season '.$season->season_number }}</td>
                            <td>
                                @if($season->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @elseif($season->status === 'upcoming')
                                    <span class="badge badge-warning text-dark">Upcoming</span>
                                @else
                                    <span class="badge badge-secondary">Completed</span>
                                @endif
                            </td>
                            <td>{{ $season->singles_count }}</td>
                            <td>₹{{ number_format($season->singles_revenue, 2) }}</td>
                            <td>{{ $season->doubles_count }}</td>
                            <td>₹{{ number_format($season->doubles_revenue, 2) }}</td>
                            <td class="font-weight-bold text-success">₹{{ number_format($season->total_revenue, 2) }}</td>
                            <td>
                                <a href="{{ route('admin-revenue-season', [$tournament->id, $season->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye mr-1"></i> Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <td colspan="7" class="text-right font-weight-bold">Tournament Total</td>
                            <td class="font-weight-bold text-success">₹{{ number_format($tournamentTotal, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection