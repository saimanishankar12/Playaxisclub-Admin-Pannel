@extends('Admin.layouts.app')
@section('title', 'Sponsors')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Sponsors</h1>
    <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addSponsorModal">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add Sponsor
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

{{-- Filter --}}
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Filter</h6></div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin-sponsors') }}" class="form-inline">
            <div class="form-group mr-3 mb-2">
                <label class="mr-2">Tournament</label>
                <select name="tournament_id" class="form-control form-control-sm" id="filterTournament" onchange="this.form.submit()">
                    <option value="">All Tournaments</option>
                    @foreach($tournaments as $t)
                        <option value="{{ $t->id }}" {{ $tournamentId == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            @if($tournamentId)
            <div class="form-group mr-3 mb-2">
                <label class="mr-2">Season</label>
                <select name="tournament_season_id" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">All Seasons</option>
                    @foreach($tournaments->find($tournamentId)?->seasons ?? [] as $s)
                        <option value="{{ $s->id }}" {{ $seasonId == $s->id ? 'selected' : '' }}>
                            {{ $s->label ?? 'Season '.$s->season_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            @if($tournamentId || $seasonId)
                <a href="{{ route('admin-sponsors') }}" class="btn btn-sm btn-outline-secondary mb-2">Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- Summary Card --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Sponsorship</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($totalSponsorship, 2) }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-handshake fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sponsors Table --}}
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Sponsor List</h6></div>
    <div class="card-body">
        @if($sponsors->isEmpty())
            <p class="text-center text-muted">No sponsors found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Sponsor Name</th>
                            <th>Package (₹)</th>
                            <th>Tournament</th>
                            <th>Season</th>
                            <th>Notes</th>
                            <th>Added On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sponsors as $i => $sponsor)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $sponsor->name }}</td>
                            <td class="font-weight-bold text-warning">₹{{ number_format($sponsor->package) }}</td>
                            <td>{{ $sponsor->tournament->name ?? '—' }}</td>
                            <td>{{ $sponsor->season->label ?? ($sponsor->season ? 'Season '.$sponsor->season->season_number : '—') }}</td>
                            <td>{{ $sponsor->notes ?? '—' }}</td>
                            <td>{{ $sponsor->created_at->format('d M Y') }}</td>
                            <td class="d-flex gap-1">
                                {{-- Edit Button --}}
                                <button
                                    class="btn btn-sm btn-warning mr-1"
                                    onclick="openEditModal(
                                        {{ $sponsor->id }},
                                        '{{ addslashes($sponsor->name) }}',
                                        '{{ $sponsor->package }}',
                                        '{{ $sponsor->tournament_id }}',
                                        '{{ $sponsor->tournament_season_id }}',
                                        '{{ addslashes($sponsor->notes ?? '') }}',
                                        {{ $sponsor->tournament?->seasons->toJson() ?? '[]' }}
                                    )">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- Delete Button --}}
                                <form action="{{ route('admin-sponsors-destroy', $sponsor->id) }}" method="POST"
                                    onsubmit="return confirm('Remove this sponsor?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <td colspan="1" class="text-right font-weight-bold">Total</td>
                            <td class="font-weight-bold text-warning">₹{{ number_format($totalSponsorship) }}</td>
                            <td colspan="6"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Add Sponsor Modal --}}
<div class="modal fade" id="addSponsorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Sponsor</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('admin-sponsors-store') }}" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="form-group">
                        <label>Sponsor Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Yonex India" required>
                    </div>

                    <div class="form-group">
                        <label>Package Amount <span class="text-danger">*</span></label>
                        <select name="package" class="form-control" required>
                            <option value="">Select Package</option>
                            <option value="25000">₹25,000</option>
                            <option value="50000">₹50,000</option>
                            <option value="75000">₹75,000</option>
                            <option value="100000">₹1,00,000</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tournament <span class="text-danger">*</span></label>
                        <select name="tournament_id" class="form-control" id="modalTournament" required>
                            <option value="">Select Tournament</option>
                            @foreach($tournaments as $t)
                                <option value="{{ $t->id }}" data-seasons="{{ $t->seasons->toJson() }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Season <span class="text-danger">*</span></label>
                        <select name="tournament_season_id" class="form-control" id="modalSeason" required>
                            <option value="">Select Tournament First</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Sponsor</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Sponsor Modal --}}
<div class="modal fade" id="editSponsorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Sponsor</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="editSponsorForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="form-group">
                        <label>Sponsor Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Package Amount <span class="text-danger">*</span></label>
                        <select name="package" id="editPackage" class="form-control" required>
                            <option value="">Select Package</option>
                            <option value="25000">₹25,000</option>
                            <option value="50000">₹50,000</option>
                            <option value="75000">₹75,000</option>
                            <option value="100000">₹1,00,000</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tournament <span class="text-danger">*</span></label>
                        <select name="tournament_id" class="form-control" id="editTournament" required>
                            <option value="">Select Tournament</option>
                            @foreach($tournaments as $t)
                                <option value="{{ $t->id }}" data-seasons="{{ $t->seasons->toJson() }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Season <span class="text-danger">*</span></label>
                        <select name="tournament_season_id" class="form-control" id="editSeason" required>
                            <option value="">Select Tournament First</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" id="editNotes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Sponsor</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Populate seasons when tournament changes in Add Modal
    document.getElementById('modalTournament').addEventListener('change', function () {
        const seasons = JSON.parse(this.selectedOptions[0]?.dataset.seasons || '[]');
        const sel = document.getElementById('modalSeason');
        sel.innerHTML = '<option value="">Select Season</option>';
        seasons.forEach(s => {
            sel.innerHTML += `<option value="${s.id}">${s.label || 'Season ' + s.season_number}</option>`;
        });
    });

    // Populate seasons when tournament changes in Edit Modal
    document.getElementById('editTournament').addEventListener('change', function () {
        const seasons = JSON.parse(this.selectedOptions[0]?.dataset.seasons || '[]');
        const sel = document.getElementById('editSeason');
        sel.innerHTML = '<option value="">Select Season</option>';
        seasons.forEach(s => {
            sel.innerHTML += `<option value="${s.id}">${s.label || 'Season ' + s.season_number}</option>`;
        });
    });

    // Open Edit Modal and pre-fill fields
    function openEditModal(id, name, pkg, tournamentId, seasonId, notes, seasons) {
        // Set form action to the update route
        document.getElementById('editSponsorForm').action = `/admin/sponsors/${id}`;

        // Fill basic fields
        document.getElementById('editName').value = name;
        document.getElementById('editNotes').value = notes;

        // Set package
        document.getElementById('editPackage').value = pkg;

        // Set tournament
        const tournamentSelect = document.getElementById('editTournament');
        tournamentSelect.value = tournamentId;

        // Populate seasons dropdown from passed seasons data
        const seasonSelect = document.getElementById('editSeason');
        seasonSelect.innerHTML = '<option value="">Select Season</option>';
        seasons.forEach(s => {
            seasonSelect.innerHTML += `<option value="${s.id}">${s.label || 'Season ' + s.season_number}</option>`;
        });

        // Set current season
        seasonSelect.value = seasonId;

        // Show modal
        $('#editSponsorModal').modal('show');
    }
</script>
@endsection