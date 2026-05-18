@extends('Admin.layouts.app')
@section('title', 'Revenue – Season Detail')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('admin-revenue-tournament', $tournament->id) }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="fas fa-arrow-left mr-1"></i> Back to {{ $tournament->name }}
        </a>
        <h1 class="h3 mb-0 text-gray-800">
            {{ $tournament->name }} — {{ $season->label ?? 'Season '.$season->season_number }}
        </h1>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Singles Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($singlesTotal, 2) }}</div>
                        <small class="text-muted">{{ $singlesPayments->count() }} registrations</small>
                    </div>
                    <div class="col-auto"><i class="fas fa-user fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Doubles Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($doublesTotal, 2) }}</div>
                        <small class="text-muted">{{ $doublesPayments->count() }} registrations</small>
                    </div>
                    <div class="col-auto"><i class="fas fa-user-friends fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Grand Total</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($grandTotal, 2) }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-rupee-sign fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Singles Payments --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user mr-2"></i>Singles Payments</h6>
    </div>
    <div class="card-body">
        @if($singlesPayments->isEmpty())
            <p class="text-center text-muted">No singles payments found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Player ID</th>
                            <th>Player Name</th>
                            <th>Season ID</th>
                            <th>Payment ID</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($singlesPayments as $i => $payment)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $payment->player1_id }}</td>
                            <td>{{ $payment->player->name ?? '—' }}</td>
                            <td>{{ $payment->season_id }}</td>
                            <td><small>{{ $payment->razorpay_payment_id }}</small></td>
                            <td class="text-success font-weight-bold">₹{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <td colspan="5" class="text-right font-weight-bold">Singles Total</td>
                            <td class="font-weight-bold text-success">₹{{ number_format($singlesTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Doubles Payments --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-user-friends mr-2"></i>Doubles Payments</h6>
    </div>
    <div class="card-body">
        @if($doublesPayments->isEmpty())
            <p class="text-center text-muted">No doubles payments found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Player 1 ID</th>
                            <th>Player 1 Name</th>
                            <th>Player 2 ID</th>
                            <th>Season ID</th>
                            <th>Payment ID</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doublesPayments as $i => $payment)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $payment->player1_id }}</td>
                            <td>{{ $payment->player->name ?? '—' }}</td>
                            <td>{{ $payment->player2_id ?? '—' }}</td>
                            <td>{{ $payment->season_id }}</td>
                            <td><small>{{ $payment->razorpay_payment_id }}</small></td>
                            <td class="text-success font-weight-bold">₹{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <td colspan="6" class="text-right font-weight-bold">Doubles Total</td>
                            <td class="font-weight-bold text-success">₹{{ number_format($doublesTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection