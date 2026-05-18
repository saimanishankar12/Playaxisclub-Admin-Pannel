@extends('Admin.layouts.app')
@section('title', 'Pending Doubles — ' . $season->name)
@section('page-title', 'Pending Doubles')

@section('styles')
<style>
    .ur-header { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px; }
    .ur-header h2 { font-size:1.25rem;font-weight:700;color:#1e293b;margin:0; }
    .ur-header p  { margin:2px 0 0;font-size:.8rem;color:#64748b; }
    .ur-breadcrumb { display:flex;align-items:center;gap:6px;font-size:.78rem;color:#94a3b8;margin-bottom:20px;flex-wrap:wrap; }
    .ur-breadcrumb a { color:#64748b;text-decoration:none; } .ur-breadcrumb a:hover { color:#1a56db; }
    .ur-breadcrumb span { color:#1e293b;font-weight:600; }
    .ur-toolbar { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:14px 20px;border-bottom:1px solid #e2e8f0; }
    .ur-toolbar-left h6 { font-size:.95rem;font-weight:700;color:#1e293b;margin:0; }
    .ur-toolbar-left small { font-size:.73rem;color:#64748b; }
    .ur-search { border:1px solid #e2e8f0;border-radius:8px;padding:6px 12px;font-size:.8rem;color:#1e293b;outline:none;transition:border-color .15s;width:200px; }
    .ur-search:focus { border-color:#1a56db; }
    .ur-card { background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.06);overflow:hidden;margin-bottom:24px; }
    .ur-table { width:100%;border-collapse:collapse;font-size:.83rem; }
    .ur-table thead th { background:#f8fafc;padding:10px 14px;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;border-bottom:1px solid #e2e8f0;white-space:nowrap; }
    .ur-table tbody tr { border-bottom:1px solid #f1f5f9;transition:background .12s; }
    .ur-table tbody tr:last-child { border-bottom:none; }
    .ur-table tbody tr:hover { background:#f8fafc; }
    .ur-table tbody td { padding:12px 14px;color:#1e293b;vertical-align:top; }
    .ur-player-name { font-weight:600;color:#1e293b; }
    .ur-player-id { font-size:.68rem;color:#94a3b8;font-family:'DM Mono',monospace; }
    .ur-email { font-size:.75rem;color:#475569; }
    .ur-phone { font-size:.75rem;color:#475569;font-family:'DM Mono',monospace; }
    .ur-badge { display:inline-flex;align-items:center;gap:4px;font-size:.65rem;font-weight:600;padding:3px 8px;border-radius:20px; }
    .ur-badge--pending { background:#fef3c7;color:#d97706; }
    .ur-badge--doubles { background:#ede9fe;color:#7c3aed; }
    .ur-pair-id { font-size:.68rem;font-family:'DM Mono',monospace;color:#7c3aed;font-weight:600; }
    .ur-empty { padding:48px;text-align:center;color:#94a3b8;font-size:.875rem; }
    .ur-empty i { font-size:2rem;opacity:.25;display:block;margin-bottom:12px; }
    .ur-count-chip { display:inline-flex;align-items:center;gap:5px;background:#f1f5f9;border-radius:8px;padding:4px 10px;font-size:.75rem;font-weight:600;color:#475569; }
    .photo-label { font-size:.6rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px; }
    .ur-photo { width:80px;height:auto;border-radius:6px;border:1px solid #e2e8f0;display:block;transition:transform .15s; }
    .ur-photo:hover { transform:scale(1.05); }
    .photo-block { margin-bottom:10px; }

    /* Action buttons */
    .ur-actions { display:flex;flex-direction:column;gap:6px; }
    .ur-btn-edit {
        display:inline-flex;align-items:center;gap:4px;
        padding:5px 11px;border-radius:7px;font-size:.72rem;font-weight:700;
        background:#4e73df;color:#fff;text-decoration:none;white-space:nowrap;
        border:none;cursor:pointer;transition:background .15s;
    }
    .ur-btn-edit:hover { background:#3a5fc8;color:#fff; }

    @media(max-width:576px){ .ur-search{width:140px;} }
</style>
@endsection

@section('content')
<div class="ur-header">
    <div><h2>Pending Doubles</h2><p>{{ $season->name }} &mdash; pair registrations awaiting payment.</p></div>
    <a href="{{ route('admin-dashboard') }}" style="font-size:.8rem;color:#64748b;text-decoration:none;"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
</div>
<div class="ur-breadcrumb">
    <a href="{{ route('admin-users.index') }}">User Reports</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <a href="{{ route('admin-users.notpaid.tournaments') }}">Not Paid Users</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <a href="{{ route('admin-users.notpaid.categories', $season->id) }}">{{ $season->name }}</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <span>Doubles</span>
</div>
<div class="ur-card">
    <div class="ur-toolbar">
        <div class="ur-toolbar-left">
            <h6>Pending Doubles &nbsp;<span class="ur-count-chip"><i class="fas fa-users"></i> {{ number_format($count) }} pairs</span></h6>
            <small>Registered but <strong>payment not completed</strong></small>
        </div>
        <input type="text" class="ur-search" id="urSearch" placeholder="&#xf002;  Search player…" oninput="filterTable()">
    </div>
    <div style="overflow-x:auto;">
        @if($pairs->isEmpty())
            <div class="ur-empty"><i class="fas fa-users"></i>No pending doubles registrations found.</div>
        @else
            <table class="ur-table" id="urTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pair ID</th>
                        <th>Player 1</th>
                        <th>Player 2</th>
                        <th>Sport / Age</th>
                        <th>Gender</th>
                        <th>Profile Photo</th>
                        <th>Aadhar Proof</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pairs as $i => $pair)
                    @php $p1 = $pair['player1']; $p2 = $pair['player2']; @endphp
                    <tr>
                        <td style="color:#94a3b8;font-size:.72rem;">{{ $i + 1 }}</td>

                        <td><span class="ur-pair-id">{{ $pair['season_id'] }}</span></td>

                        {{-- Player 1 --}}
                        <td>
                            <div class="ur-player-name">{{ $p1->name }}</div>
                            <div class="ur-player-id">{{ $p1->player_id }}</div>
                            <div class="ur-email">{{ $p1->email }}</div>
                            <div class="ur-phone">{{ $p1->phone }}</div>
                            <div style="font-size:.72rem;color:#94a3b8;">{{ $p1->state_name ?? '' }}</div>
                        </td>

                        {{-- Player 2 --}}
                        <td>
                            @if($p2)
                                <div class="ur-player-name">{{ $p2->name }}</div>
                                <div class="ur-player-id">{{ $p2->player_id }}</div>
                                <div class="ur-email">{{ $p2->email }}</div>
                                <div class="ur-phone">{{ $p2->phone }}</div>
                                <div style="font-size:.72rem;color:#94a3b8;">{{ $p2->state_name ?? '' }}</div>
                            @else
                                <span style="font-size:.75rem;color:#94a3b8;">Partner not found</span>
                            @endif
                        </td>

                        {{-- Sport / Age --}}
                        <td>
                            <div style="font-size:.8rem;">{{ $p1->sport }}</div>
                            <div style="font-size:.75rem;color:#64748b;">{{ $p1->age }}</div>
                        </td>

                        {{-- Gender --}}
                        <td style="font-size:.8rem;">{{ $p1->gender }}</td>

                        {{-- Profile Photo (P1 + P2) --}}
                        <td>
                            <div class="photo-block">
                                <div class="photo-label">P1</div>
                                @if($p1->profile_photo)
                                    <a href="{{ asset('storage/' . $p1->profile_photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $p1->profile_photo) }}" class="ur-photo" alt="P1 Profile">
                                    </a>
                                @else
                                    <span style="color:#94a3b8;font-size:.75rem;">—</span>
                                @endif
                            </div>
                            @if($p2)
                                <div class="photo-block">
                                    <div class="photo-label">P2</div>
                                    @if($p2->profile_photo)
                                        <a href="{{ asset('storage/' . $p2->profile_photo) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $p2->profile_photo) }}" class="ur-photo" alt="P2 Profile">
                                        </a>
                                    @else
                                        <span style="color:#94a3b8;font-size:.75rem;">—</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- Aadhar Proof (P1 + P2) --}}
                        <td>
                            <div class="photo-block">
                                <div class="photo-label">P1</div>
                                @if($p1->aadhar_proof)
                                    <a href="{{ asset('storage/' . $p1->aadhar_proof) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $p1->aadhar_proof) }}" class="ur-photo" alt="P1 Aadhar">
                                    </a>
                                @else
                                    <span style="color:#94a3b8;font-size:.75rem;">—</span>
                                @endif
                            </div>
                            @if($p2)
                                <div class="photo-block">
                                    <div class="photo-label">P2</div>
                                    @if($p2->aadhar_proof)
                                        <a href="{{ asset('storage/' . $p2->aadhar_proof) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $p2->aadhar_proof) }}" class="ur-photo" alt="P2 Aadhar">
                                        </a>
                                    @else
                                        <span style="color:#94a3b8;font-size:.75rem;">—</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- Mode --}}
                        <td><span class="ur-badge ur-badge--doubles"><i class="fas fa-users" style="font-size:.5rem;"></i> Doubles</span></td>

                        {{-- Status --}}
                        <td><span class="ur-badge ur-badge--pending"><i class="fas fa-clock" style="font-size:.5rem;"></i> Pending</span></td>

                        {{-- Registered --}}
                        <td style="font-size:.75rem;color:#64748b;white-space:nowrap;">{{ \Carbon\Carbon::parse($p1->created_at)->format('d M Y, g:i A') }}</td>

                        {{-- Action --}}
                        <td>
                            <div class="ur-actions">
                                <a href="{{ route('admin-users.edit', $p1->id) }}" class="ur-btn-edit">
                                    <i class="fas fa-edit"></i> Edit P1
                                </a>
                                @if($p2)
                                    <a href="{{ route('admin-users.edit', $p2->id) }}" class="ur-btn-edit">
                                        <i class="fas fa-edit"></i> Edit P2
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function filterTable() {
    const q = document.getElementById('urSearch').value.toLowerCase();
    document.querySelectorAll('#urTable tbody tr').forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
@endsection