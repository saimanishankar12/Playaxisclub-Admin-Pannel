@extends('Admin.layouts.app')
@section('title', 'Club Members')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Club Members</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">All Members</h6>
        <form method="GET" action="{{ route('admin-pac-users') }}" class="form-inline">
            <input type="text" name="search" class="form-control form-control-sm mr-2"
                placeholder="Search name / ID / email" value="{{ $search }}">
            <button class="btn btn-sm btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin-pac-users') }}" class="btn btn-sm btn-outline-secondary ml-2">Clear</a>
            @endif
        </form>
    </div>
    <div class="card-body">
        @if($players->isEmpty())
            <p class="text-center text-muted">No members found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Player ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Age Category</th>
                            <th>Sport</th>
                            <th>Gender</th>
                            <th>Payment</th>
                            <th>Registered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($players as $i => $player)
                        <tr>
                            <td>{{ $players->firstItem() + $i }}</td>
                            <td><span class="badge badge-primary">{{ $player->player_id }}</span></td>
                            <td>{{ $player->name }}</td>
                            <td>{{ $player->email }}</td>
                            <td>{{ $player->phone }}</td>
                            <td>{{ $player->age ?? '—' }}</td>
                            <td>{{ $player->sport ?? '—' }}</td>
                            <td>{{ $player->gender ?? '—' }}</td>
                            <td>
                                @if($player->payment_status === 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @else
                                    <span class="badge badge-danger">Unpaid</span>
                                @endif
                            </td>
                            <td>{{ $player->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $players->appends(['search' => $search])->links() }}
            </div>
        @endif
    </div>
</div>

@endsection