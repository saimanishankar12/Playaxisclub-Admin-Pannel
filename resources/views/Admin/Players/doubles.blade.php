@extends('Admin.layouts.app')

@section('title', 'Doubles Players')

@section('styles')
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Doubles Players</h1>
        <span class="badge badge-purple p-2" style="background:#6f42c1;color:#fff;">Total: {{ $players->count() }}</span>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user-friends mr-1"></i>Ekalavya Doubles
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            
                            <th>Player ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Age</th>
                            <th>Season ID</th>
                          
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                           
                            <th>Player ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Age</th>
                            <th>Season ID</th>
                           
                        </tr>
                    </tfoot>
                    <tbody>
                        @forelse($players as $player)
                        <tr>
                         
                            <td>{{ $player->player_id }}</td>
                            <td>{{ $player->name }}</td>
                            <td>{{ $player->email }}</td>
                            <td>{{ $player->phone }}</td>
                            <td>{{ $player->age ?? 'N/A' }}</td>
                            <td>{{ $player->season_id ?? 'N/A' }}</td>
                          
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-user-friends fa-3x mb-3 d-block"></i>
                                No doubles players registered yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "columnDefs": [
                    { "orderable": false, "targets": [7] }
                ]
            });
        });
    </script>
@endsection