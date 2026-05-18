@extends('Admin.layouts.app')
@section('title', 'Attendance — ' . ucfirst($mode) . ' ' . $age)
@section('page-title', 'Attendance')

@section('styles')
<style>
    .ur-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;}
    .ur-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}
    .ur-header p{margin:2px 0 0;font-size:.8rem;color:#64748b;}
    .ur-breadcrumb{display:flex;align-items:center;gap:6px;font-size:.78rem;color:#94a3b8;margin-bottom:20px;flex-wrap:wrap;}
    .ur-breadcrumb a{color:#64748b;text-decoration:none;}
    .ur-breadcrumb a:hover{color:#1a56db;}
    .ur-breadcrumb span{color:#1e293b;font-weight:600;}
    .ur-toolbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:14px 20px;border-bottom:1px solid #e2e8f0;}
    .ur-toolbar-left h6{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;}
    .ur-toolbar-left small{font-size:.73rem;color:#64748b;}
    .ur-search{border:1px solid #e2e8f0;border-radius:8px;padding:6px 12px;font-size:.8rem;color:#1e293b;outline:none;transition:border-color .15s;width:200px;}
    .ur-search:focus{border-color:#1a56db;}
    .ur-card{background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.06);overflow:hidden;margin-bottom:24px;}
    .ur-table{width:100%;border-collapse:collapse;font-size:.83rem;}
    .ur-table thead th{background:#f8fafc;padding:10px 14px;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;border-bottom:1px solid #e2e8f0;white-space:nowrap;}
    .ur-table tbody tr{border-bottom:1px solid #f1f5f9;transition:background .12s;}
    .ur-table tbody tr:last-child{border-bottom:none;}
    .ur-table tbody tr:hover{background:#f8fafc;}
    .ur-table tbody tr.is-present{background:#f0fdf4;}
    .ur-table tbody td{padding:12px 14px;color:#1e293b;vertical-align:middle;}
    .ur-player-name{font-weight:600;color:#1e293b;}
    .ur-player-id{font-size:.68rem;color:#94a3b8;font-family:'DM Mono',monospace;}
    .ur-count-chip{display:inline-flex;align-items:center;gap:5px;background:#f1f5f9;border-radius:8px;padding:4px 10px;font-size:.75rem;font-weight:600;color:#475569;}
    .ur-badge{display:inline-flex;align-items:center;gap:4px;font-size:.65rem;font-weight:600;padding:3px 8px;border-radius:20px;}
    .ur-badge--present{background:#d1fae5;color:#059669;}
    .ur-badge--absent{background:#f1f5f9;color:#94a3b8;}

    /* toggle switch */
    .att-toggle{position:relative;display:inline-block;width:40px;height:22px;}
    .att-toggle input{opacity:0;width:0;height:0;}
    .att-slider{position:absolute;inset:0;background:#e2e8f0;border-radius:22px;cursor:pointer;transition:background .2s;}
    .att-slider:before{content:'';position:absolute;width:16px;height:16px;left:3px;top:3px;background:#fff;border-radius:50%;transition:transform .2s;}
    .att-toggle input:checked + .att-slider{background:#10b981;}
    .att-toggle input:checked + .att-slider:before{transform:translateX(18px);}

    /* date picker */
    .att-date-input{border:1px solid #e2e8f0;border-radius:8px;padding:6px 12px;font-size:.8rem;color:#1e293b;outline:none;transition:border-color .15s;}
    .att-date-input:focus{border-color:#1a56db;}
</style>
@endsection

@section('content')
<div class="ur-header">
    <div>
        <h2>{{ ucfirst($mode) }} Attendance — {{ $age }}</h2>
        <p>Ekalavya Badminton Tournament — toggle to mark players present at venue.</p>
    </div>
    <a href="{{ route('admin-dashboard') }}" style="font-size:.8rem;color:#64748b;text-decoration:none;">
        <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
    </a>
</div>

<div class="ur-breadcrumb">
    <a href="{{ route('admin-attendance.index') }}">Attendance</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <a href="{{ route('admin-attendance.ages', $mode) }}">{{ ucfirst($mode) }}</a>
    <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
    <span>{{ $age }}</span>
</div>

<div class="ur-card">
    {{-- ── TOOLBAR ────────────────────────────────────────────────────── --}}
    <div class="ur-toolbar">
        <div class="ur-toolbar-left">
            <h6>{{ ucfirst($mode) }} — {{ $age }} &nbsp;
                <span class="ur-count-chip">
                    <i class="fas fa-user"></i> {{ $players->count() }} players
                </span>
            </h6>
            <small>
                Marking attendance for &nbsp;
                <strong>{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</strong>
            </small>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            {{-- Date picker — changing it reloads the page for that date --}}
            <input type="date"
                   id="att-date"
                   class="att-date-input"
                   value="{{ $date }}"
                   max="{{ now()->toDateString() }}"
                   onchange="changeDate(this.value)">
            <input type="text" class="ur-search" id="urSearch" placeholder="Search player…" oninput="filterTable()">
        </div>
    </div>
    {{-- ────────────────────────────────────────────────────────────────── --}}

    <div style="overflow-x:auto;">
        @if($players->isEmpty())
            <div style="padding:48px;text-align:center;color:#94a3b8;font-size:.875rem;">
                <i class="fas fa-users" style="font-size:2rem;opacity:.25;display:block;margin-bottom:12px;"></i>
                No players found for this category.
            </div>
        @else
            <table class="ur-table" id="urTable">
                <thead>
                    <tr>
                        
                        <th>Player</th>
                        <th>Team ID</th>
                        <th>Status</th>
                        <th>Present at venue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($players as $player)
                    <tr class="{{ $player->is_present ? 'is-present' : '' }}" id="row-{{ $player->player_id }}">
                        
                        <td>
                            <div class="ur-player-name">{{ $player->name }}</div>
                            <div class="ur-player-id">{{ $player->player_id }}</div>
                        </td>
                        <td style="font-size:.78rem;font-family:'DM Mono',monospace;color:#64748b;">
                            {{ $player->season_id ?? '—' }}
                        </td>
                        <td>
                            <span class="ur-badge {{ $player->is_present ? 'ur-badge--present' : 'ur-badge--absent' }}"
                                  id="badge-{{ $player->player_id }}">
                                <i class="fas {{ $player->is_present ? 'fa-check' : 'fa-times' }}" style="font-size:.5rem;"></i>
                                {{ $player->is_present ? 'Present' : 'Absent' }}
                            </span>
                        </td>
                        <td>
                            <label class="att-toggle">
                                <input type="checkbox"
                                       class="attendance-toggle"
                                       data-player="{{ $player->player_id }}"
                                       {{ $player->is_present ? 'checked' : '' }}>
                                <span class="att-slider"></span>
                            </label>
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
// Reload page with selected date in URL
function changeDate(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('date', val);
    window.location.href = url.toString();
}

function filterTable() {
    const q = document.getElementById('urSearch').value.toLowerCase();
    document.querySelectorAll('#urTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

document.querySelectorAll('.attendance-toggle').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const playerId = this.dataset.player;
        const present  = this.checked;
        const date     = document.getElementById('att-date').value;  // ← get selected date
        const row      = document.getElementById('row-' + playerId);
        const badge    = document.getElementById('badge-' + playerId);

        // Update row highlight
        row.classList.toggle('is-present', present);

        // Update badge
        badge.className = 'ur-badge ' + (present ? 'ur-badge--present' : 'ur-badge--absent');
        badge.innerHTML = `<i class="fas ${present ? 'fa-check' : 'fa-times'}" style="font-size:.5rem;"></i> ${present ? 'Present' : 'Absent'}`;

        // Save to server — now includes date
        fetch('{{ route("admin-attendance.mark") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ player_id: playerId, is_present: present ? 1 : 0, date: date })
        }).then(r => r.json())
.then(data => console.log('Response:', data))
.catch(err => console.error('Error:', err));
    });
});
</script>
@endsection