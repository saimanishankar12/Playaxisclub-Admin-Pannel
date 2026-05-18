@extends('Admin.layouts.app')
@section('title', 'Audience')

@section('styles')
<style>
    /* ── Lucky Draw Panel ── */
    .ld-panel { background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.07);overflow:hidden;margin-bottom:28px; }
    .ld-panel-header { background:linear-gradient(135deg,#7c3aed,#4f46e5);padding:18px 24px;display:flex;align-items:center;gap:12px; }
    .ld-panel-header h5 { color:#fff;font-size:1rem;font-weight:700;margin:0; }
    .ld-panel-header p  { color:rgba(255,255,255,.75);font-size:.75rem;margin:2px 0 0; }
    .ld-panel-body { padding:20px 24px; }

    /* ── Day Tabs ── */
    .ld-days { display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap; }
    .ld-day-tab { display:flex;flex-direction:column;align-items:center;justify-content:center;width:110px;padding:12px 8px;border-radius:12px;border:2px solid #e2e8f0;background:#f8fafc;cursor:pointer;transition:all .15s;text-decoration:none; }
    .ld-day-tab:hover { border-color:#7c3aed;background:#faf5ff; }
    .ld-day-tab.active { border-color:#7c3aed;background:#ede9fe; }
    .ld-day-tab.won { border-color:#059669;background:#d1fae5;cursor:default; }
    .ld-day-tab .day-label { font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;margin-bottom:3px; }
    .ld-day-tab .day-num { font-size:1.3rem;font-weight:800;color:#1e293b; }
    .ld-day-tab .day-date { font-size:.65rem;color:#64748b;margin-top:2px; }
    .ld-day-tab.won .day-label { color:#059669; }
    .ld-day-tab.won .day-num  { color:#059669; }

    /* ── Winner Card ── */
    .ld-winner-card { display:flex;align-items:center;gap:14px;background:#f0fdf4;border:1.5px solid #86efac;border-radius:12px;padding:14px 18px;margin-bottom:6px; }
    .ld-winner-card .trophy { font-size:1.6rem; }
    .ld-winner-card .winner-name { font-weight:700;font-size:.95rem;color:#166534; }
    .ld-winner-card .winner-meta { font-size:.75rem;color:#4ade80; }
    .ld-winner-card .winner-id { font-size:.7rem;font-family:'DM Mono',monospace;color:#15803d;background:#bbf7d0;padding:2px 7px;border-radius:6px; }

    /* ── Declare Form ── */
    .ld-form { display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end; }
    .ld-form .form-group { margin:0; }
    .ld-form label { font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:5px; }
    .ld-input { border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 14px;font-size:.85rem;color:#1e293b;outline:none;transition:border-color .15s;min-width:180px; }
    .ld-input:focus { border-color:#7c3aed; }
    .ld-select { border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 14px;font-size:.85rem;color:#1e293b;outline:none;background:#fff;min-width:130px; }
    .ld-btn { background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;border:none;border-radius:8px;padding:9px 20px;font-size:.82rem;font-weight:700;cursor:pointer;transition:opacity .15s;white-space:nowrap; }
    .ld-btn:hover { opacity:.88; }
    .ld-btn:disabled { opacity:.45;cursor:not-allowed; }
    .ld-no-winner { font-size:.8rem;color:#94a3b8;font-style:italic;padding:10px 0; }

    /* ── Audience Table ── */
    .ur-card { background:#fff;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.06);overflow:hidden;margin-bottom:24px; }
    .ur-toolbar { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:14px 20px;border-bottom:1px solid #e2e8f0; }
    .ur-toolbar h6 { font-size:.95rem;font-weight:700;color:#1e293b;margin:0; }
    .ur-search { border:1px solid #e2e8f0;border-radius:8px;padding:6px 12px;font-size:.8rem;color:#1e293b;outline:none;transition:border-color .15s;width:200px; }
    .ur-search:focus { border-color:#7c3aed; }
    .ur-table { width:100%;border-collapse:collapse;font-size:.83rem; }
    .ur-table thead th { background:#f8fafc;padding:10px 14px;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;border-bottom:1px solid #e2e8f0;white-space:nowrap; }
    .ur-table tbody tr { border-bottom:1px solid #f1f5f9;transition:background .12s; }
    .ur-table tbody tr:last-child { border-bottom:none; }
    .ur-table tbody tr:hover { background:#f8fafc; }
    .ur-table tbody tr.is-winner { background:#f0fdf4; }
    .ur-table tbody td { padding:11px 14px;color:#1e293b;vertical-align:middle; }
    .ur-badge { display:inline-flex;align-items:center;gap:4px;font-size:.65rem;font-weight:600;padding:3px 8px;border-radius:20px; }
    .ur-badge--winner  { background:#d1fae5;color:#059669; }
    .ur-badge--no      { background:#f1f5f9;color:#94a3b8; }
    .ur-count-chip { display:inline-flex;align-items:center;gap:5px;background:#f1f5f9;border-radius:8px;padding:4px 10px;font-size:.75rem;font-weight:600;color:#475569; }
    .ur-empty { padding:48px;text-align:center;color:#94a3b8;font-size:.875rem; }

    /* ── Day filter tabs for audience list ── */
    .day-filter { display:flex;gap:8px;flex-wrap:wrap;padding:12px 20px;border-bottom:1px solid #e2e8f0;background:#fafafa; }
    .day-filter a { font-size:.75rem;font-weight:600;padding:5px 14px;border-radius:20px;border:1.5px solid #e2e8f0;color:#475569;text-decoration:none;transition:all .12s; }
    .day-filter a:hover { border-color:#7c3aed;color:#7c3aed; }
    .day-filter a.active { background:#7c3aed;border-color:#7c3aed;color:#fff; }
</style>
@endsection

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Audience</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

{{-- ══════════════════════════════════════════════════════
     LUCKY DRAW PANEL — Ekalavya Badminton Tournament
     29 May – 31 May (3 days, 3 winners)
════════════════════════════════════════════════════════ --}}
<div class="ld-panel">
    <div class="ld-panel-header">
        <div>
            <h5><i class="fas fa-trophy mr-2"></i> Lucky Draw — Ekalavya Badminton Tournament</h5>
            <p>29 May – 31 May &nbsp;|&nbsp; 3 days &nbsp;|&nbsp; 1 winner per day &nbsp;|&nbsp; Each audience member eligible only once</p>
        </div>
    </div>
    <div class="ld-panel-body">

        {{-- Day Tabs --}}
        @php
            $days = [
                1 => ['label' => 'Day 1', 'date' => '29 May 2026'],
                2 => ['label' => 'Day 2', 'date' => '30 May 2026'],
                3 => ['label' => 'Day 3', 'date' => '31 May 2026'],
            ];
            $activeDay = request('day', 1);
        @endphp

        <div class="ld-days">
            @foreach($days as $num => $info)
                @php $hasWon = in_array($num, $wonDays ?? []); @endphp
                <a href="{{ route('admin-audience', array_merge(request()->query(), ['day' => $num])) }}"
                   class="ld-day-tab {{ $activeDay == $num ? 'active' : '' }} {{ $hasWon ? 'won' : '' }}">
                    <span class="day-label">{{ $hasWon ? '✓ Done' : 'Draw' }}</span>
                    <span class="day-num">{{ $num }}</span>
                    <span class="day-date">{{ $info['date'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Winner display for active day --}}
        @php
            $dayWinner = $winners->firstWhere('won_day', $activeDay);
        @endphp

        @if($dayWinner)
            <div class="ld-winner-card">
                <div class="trophy">🏆</div>
                <div>
                    <div class="winner-name">{{ $dayWinner->name }}</div>
                    <div class="d-flex align-items-center gap-2 mt-1" style="gap:8px;">
                        <span class="winner-id">{{ $dayWinner->audience_id }}</span>
                        <span class="winner-meta">{{ $dayWinner->phone }} &nbsp;·&nbsp; {{ $dayWinner->city }}</span>
                    </div>
                    <div style="font-size:.7rem;color:#166534;margin-top:4px;">
                        Winner of Day {{ $dayWinner->won_day }} — {{ $days[$dayWinner->won_day]['date'] }}
                    </div>
                </div>
            </div>
        @else
            <div class="ld-no-winner"><i class="fas fa-clock mr-1"></i> No winner declared yet for Day {{ $activeDay }} ({{ $days[$activeDay]['date'] }})</div>

            {{-- Declare Winner Form --}}
        <form method="POST" action="{{ route('admin-audience.declare-winner') }}"
                  onsubmit="return confirm('Are you sure you want to declare this person as the Day {{ $activeDay }} Lucky Draw Winner? This cannot be undone.')">
                @csrf
                <input type="hidden" name="won_day" value="{{ $activeDay }}">
                <input type="hidden" name="tournament_season_id" value="{{ $seasonId ?? '' }}">

                <div class="ld-form">
                    <div class="form-group">
                        <label>Audience ID</label>
                        <input type="text" name="audience_id" class="ld-input"
                               placeholder="e.g. AUD0001" required
                               style="text-transform:uppercase;">
                    </div>
                    <div class="form-group">
                        <label>Day</label>
                        <select name="won_day" class="ld-select">
                            @foreach($days as $num => $info)
                                @if(!in_array($num, $wonDays ?? []))
                                    <option value="{{ $num }}" {{ $activeDay == $num ? 'selected' : '' }}>
                                        Day {{ $num }} — {{ $info['date'] }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @if(!$seasonId)
                        <div class="form-group">
                            <label>Season</label>
                            <select name="tournament_season_id" class="ld-select" required>
                                <option value="">Select Season</option>
                                @foreach($tournaments as $tournament)
                                    @foreach($tournament->seasons as $season)
                                        <option value="{{ $season->id }}">
                                            {{ $tournament->name }} — {{ $season->label ?? 'Season '.$season->season_number }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="tournament_season_id" value="{{ $seasonId }}">
                    @endif
                    <div class="form-group">
                        <button type="submit" class="ld-btn">
                            <i class="fas fa-trophy mr-1"></i> Declare Winner
                        </button>
                    </div>
                </div>
            </form>
        @endif

        {{-- All 3 days summary --}}
        <!-- @if($winners->count() > 0)
            <hr style="border-color:#e2e8f0;margin:20px 0 14px;">
            <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;margin-bottom:10px;">All Declared Winners</div>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                @foreach($winners as $w)
                    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:10px 14px;min-width:180px;">
                        <div style="font-size:.6rem;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:.4px;">Day {{ $w->won_day }} — {{ $days[$w->won_day]['date'] ?? '' }}</div>
                        <div style="font-weight:700;font-size:.88rem;color:#166534;margin-top:3px;">{{ $w->name }}</div>
                        <div style="font-size:.7rem;font-family:'DM Mono',monospace;color:#15803d;">{{ $w->audience_id }}</div>
                        <div style="font-size:.7rem;color:#4ade80;">{{ $w->phone }}</div>
                    </div>
                @endforeach
            </div>
        @endif -->

    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     AUDIENCE LIST
════════════════════════════════════════════════════════ --}}
<div class="ur-card">
    <div class="ur-toolbar">
        <div>
            <h6>
                Audience List &nbsp;
                <span class="ur-count-chip"><i class="fas fa-users"></i> {{ $audiences->total() }} members</span>
            </h6>
        </div>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            {{-- Filter by season --}}
            <select class="ld-select" style="min-width:180px;font-size:.78rem;"
                    onchange="window.location='{{ route('admin-audience') }}?tournament_season_id='+this.value+'&day={{ $activeDay }}'">
                <option value="">All Seasons</option>
                @foreach($tournaments as $tournament)
                    @foreach($tournament->seasons as $season)
                        <option value="{{ $season->id }}" {{ $seasonId == $season->id ? 'selected' : '' }}>
                            {{ $tournament->name }} — {{ $season->label ?? 'Season '.$season->season_number }}
                        </option>
                    @endforeach
                @endforeach
            </select>
            <input type="text" class="ur-search" id="urSearch" placeholder="&#xf002;  Search…" oninput="filterTable()">
        </div>
    </div>

    {{-- Day filter tabs for audience list --}}
    <div class="day-filter">
        <a href="{{ route('admin-audience', array_merge(request()->except('day'), [])) }}"
           class="{{ !request('day') ? 'active' : '' }}">All Days</a>
        @foreach($days as $num => $info)
            <a href="{{ route('admin-audience', array_merge(request()->query(), ['day' => $num])) }}"
               class="{{ request('day') == $num ? 'active' : '' }}">
               {{ $info['label'] }} <span style="font-size:.65rem;opacity:.7;">{{ $info['date'] }}</span>
            </a>
        @endforeach
        <a href="{{ route('admin-audience', array_merge(request()->query(), ['winner' => 1])) }}"
           class="{{ request('winner') ? 'active' : '' }}" style="{{ request('winner') ? '' : 'border-color:#d1fae5;color:#059669;' }}">
           🏆 Winners Only
        </a>
    </div>

    <div style="overflow-x:auto;">
        @if($audiences->isEmpty())
            <div class="ur-empty"><i class="fas fa-users" style="font-size:2rem;opacity:.2;display:block;margin-bottom:12px;"></i>No audience members found.</div>
        @else
            <table class="ur-table" id="urTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Audience ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Age</th>
                        <th>Tournament</th>
                        <th>Season</th>
                        <th>Lucky Draw</th>
                        <th>Registered On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audiences as $i => $member)
                    <tr class="{{ $member->is_winner ? 'is-winner' : '' }}">
                        <td style="color:#94a3b8;font-size:.72rem;">{{ $audiences->firstItem() + $i }}</td>
                        <td>
                            <span style="font-weight:700;color:#7c3aed;font-family:'DM Mono',monospace;font-size:.8rem;">
                                {{ $member->audience_id }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#1e293b;">{{ $member->name }}</div>
                            @if($member->is_winner)
                                <span class="ur-badge ur-badge--winner" style="margin-top:3px;">
                                    <i class="fas fa-trophy" style="font-size:.5rem;"></i> Day {{ $member->won_day }} Winner
                                </span>
                            @endif
                        </td>
                        <td style="font-size:.8rem;">{{ $member->phone }}</td>
                        <td style="font-size:.78rem;color:#475569;">{{ $member->email ?? '—' }}</td>
                        <td style="font-size:.8rem;">{{ $member->city }}</td>
                        <td style="font-size:.8rem;">{{ $member->age }}</td>
                        <td style="font-size:.8rem;">{{ $member->tournament_name ?? '—' }}</td>
                        <td style="font-size:.8rem;">
                            @if($member->season)
                                {{ $member->season->label ?? 'Season '.$member->season->season_number }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($member->is_winner)
                                <span class="ur-badge ur-badge--winner">
                                    <i class="fas fa-check" style="font-size:.5rem;"></i> Day {{ $member->won_day }}
                                </span>
                            @else
                                <span class="ur-badge ur-badge--no">No</span>
                            @endif
                        </td>
                        <td style="font-size:.75rem;color:#64748b;white-space:nowrap;">
                            {{ $member->created_at->format('d M Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:14px 20px;">
                {{ $audiences->appends(request()->query())->links() }}
            </div>
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
// Auto uppercase audience ID input
document.addEventListener('DOMContentLoaded', function() {
    const input = document.querySelector('input[name="audience_id"]');
    if (input) {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
});
</script>
@endsection