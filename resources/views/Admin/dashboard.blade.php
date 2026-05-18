@extends('Admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<style>
    /* ── Player detail panel inside live card ──────────── */
.ls-player-panel {
    border-top: 1px solid #f1f5f9;
    padding: 12px 16px;
    background: #fafbff;
}
.ls-panel-row {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.ls-panel-team {
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px dashed #e2e8f0;
}
.ls-panel-team-id {
    font-size: .72rem;
    font-weight: 700;
    background: #ede9fe;
    color: #7c3aed;
    padding: 3px 10px;
    border-radius: 20px;
}
.ls-panel-player {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.ls-panel-label {
    font-size: .6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    margin-bottom: 2px;
}
.ls-panel-player--p1 .ls-panel-label { color: #1a56db; }
.ls-panel-player--p2 .ls-panel-label { color: #ef4444; }
.ls-panel-name {
    font-size: .82rem;
    font-weight: 800;
    color: #1e293b;
}
.ls-panel-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 3px;
}
.ls-panel-meta span {
    font-size: .68rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 2px 7px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ls-panel-meta span i { font-size: .55rem; color: #94a3b8; }
.ls-panel-divider {
    width: 1px;
    background: #e2e8f0;
    align-self: stretch;
    flex-shrink: 0;
}
.ls-panel-partner {
    font-size: .68rem;
    color: #059669;
    font-weight: 600;
    background: #f0fdf4;
    border-radius: 6px;
    padding: 4px 8px;
    margin-top: 4px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px;
}
.ls-panel-partner-meta {
    color: #94a3b8;
    font-weight: 500;
}
    /* ── Page header ──────────────────────────────────── */
    .pac-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .pac-page-header h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .pac-page-header p {
        margin: 2px 0 0;
        font-size: .8rem;
        color: #64748b;
    }

    /* ── Stat cards ───────────────────────────────────── */
    .pac-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }
    .pac-stat-card {
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.06);
        display: flex;
        flex-direction: column;
        gap: 14px;
        position: relative;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }
    .pac-stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 14px 14px 0 0;
    }
    .pac-stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 28px rgba(0,0,0,.1); }
    .pac-card--blue::before   { background: #1a56db; }
    .pac-card--green::before  { background: #10b981; }
    .pac-card--purple::before { background: #8b5cf6; }
    .pac-card--amber::before  { background: #f59e0b; }
    .pac-card--rose::before   { background: #f43f5e; }
    .pac-card--cyan::before   { background: #06b6d4; }
    .pac-card--indigo::before { background: #6366f1; }
    .pac-card--teal::before   { background: #14b8a6; }
    .pac-stat-top { display: flex; align-items: flex-start; justify-content: space-between; }
    .pac-stat-icon {
        width: 44px; height: 44px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; flex-shrink: 0;
    }
    .pac-card--blue   .pac-stat-icon { background: #e8f0fe; color: #1a56db; }
    .pac-card--green  .pac-stat-icon { background: #d1fae5; color: #059669; }
    .pac-card--purple .pac-stat-icon { background: #ede9fe; color: #7c3aed; }
    .pac-card--amber  .pac-stat-icon { background: #fef3c7; color: #d97706; }
    .pac-card--rose   .pac-stat-icon { background: #ffe4e6; color: #e11d48; }
    .pac-card--cyan   .pac-stat-icon { background: #cffafe; color: #0891b2; }
    .pac-card--indigo .pac-stat-icon { background: #e0e7ff; color: #4f46e5; }
    .pac-card--teal   .pac-stat-icon { background: #ccfbf1; color: #0d9488; }
    .pac-stat-value { font-size: 1.75rem; font-weight: 800; color: #1e293b; line-height: 1; font-family: 'DM Mono', monospace; }
    .pac-stat-label { font-size: .75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .5px; margin-top: 4px; }

    /* ── Section / Card ───────────────────────────────── */
    .pac-section-title { font-size: .95rem; font-weight: 700; color: #1e293b; margin: 0; }
    .pac-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.06);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .pac-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap;
    }
    .pac-card-body { padding: 0; }

    /* ── Table ────────────────────────────────────────── */
    .pac-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
    .pac-table thead th {
        background: #f8fafc; padding: 11px 16px; text-align: left;
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #64748b; border-bottom: 1px solid #e2e8f0; white-space: nowrap;
    }
    .pac-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .12s; }
    .pac-table tbody tr:last-child { border-bottom: none; }
    .pac-table tbody tr:hover { background: #f8fafc; }
    .pac-table tbody td { padding: 12px 16px; color: #1e293b; vertical-align: middle; }

    /* ── Badges ───────────────────────────────────────── */
    .pac-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: .7rem; font-weight: 600; padding: 3px 10px; border-radius: 20px;
    }
    .pac-badge--active   { background: #d1fae5; color: #059669; }
    .pac-badge--upcoming { background: #fef3c7; color: #d97706; }
    .pac-badge--done     { background: #ffe4e6; color: #e11d48; }
    .pac-badge--season   { background: #e8f0fe; color: #1a56db; font-weight: 700; font-size: .7rem; padding: 2px 8px; border-radius: 20px; }

    .pac-status-select {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 4px 8px;
        font-size: .78rem; color: #1e293b; background: #f8fafc;
        cursor: pointer; outline: none; transition: border-color .15s;
    }
    .pac-status-select:focus { border-color: #1a56db; }

    /* ── Quick links ──────────────────────────────────── */
    .pac-quick-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px; margin-bottom: 28px;
    }
    .pac-quick-link {
        background: #fff; border: 1.5px solid #e2e8f0; border-radius: 12px;
        padding: 14px 16px; display: flex; align-items: center; gap: 10px;
        text-decoration: none; color: #1e293b; font-size: .82rem; font-weight: 600;
        transition: all .18s;
    }
    .pac-quick-link i { font-size: .9rem; color: #1a56db; }
    .pac-quick-link:hover {
        border-color: #1a56db; background: #e8f0fe; color: #1a56db;
        text-decoration: none; transform: translateY(-2px);
    }

    /* ── Buttons ──────────────────────────────────────── */
    .pac-btn-primary {
        background: #1a56db; color: #fff; border: none; border-radius: 8px;
        padding: 8px 16px; font-size: .8rem; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        transition: background .15s; text-decoration: none;
    }
    .pac-btn-primary:hover { background: #1040a8; color: #fff; text-decoration: none; }

    /* ── Alert ────────────────────────────────────────── */
    .pac-alert-success {
        background: #d1fae5; color: #065f46; border-radius: 10px;
        padding: 12px 16px; font-size: .85rem; font-weight: 500; margin-bottom: 20px;
        display: flex; align-items: center; justify-content: space-between;
    }
    .pac-alert-close { background: none; border: none; font-size: 1rem; cursor: pointer; color: #065f46; opacity: .6; }
    .pac-alert-close:hover { opacity: 1; }

    /* ── Modal ────────────────────────────────────────── */
    .modal-content { border-radius: 14px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
    .modal-header { border-bottom: 1px solid #e2e8f0; padding: 20px 24px; }
    .modal-title { font-weight: 700; font-size: .95rem; }
    .modal-body { padding: 20px 24px; }
    .modal-footer { border-top: 1px solid #e2e8f0; padding: 16px 24px; }
    .form-control { border-radius: 8px; border-color: #e2e8f0; font-size: .85rem; }
    .form-control:focus { border-color: #1a56db; box-shadow: 0 0 0 3px rgba(26,86,219,.12); }
    label { font-size: .8rem; font-weight: 600; color: #374151; }

    .pac-tournament-cell { display: flex; flex-direction: column; gap: 3px; }
    .pac-tournament-name { font-weight: 600; font-size: .83rem; color: #1e293b; }
    .pac-tournament-sport { font-size: .72rem; color: #94a3b8; }

    /* ════════════════════════════════════════════════════
       LIVE SCORE SECTION
    ════════════════════════════════════════════════════ */

    .ls-section-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px; gap: 12px; flex-wrap: wrap;
    }
    .ls-section-title {
        display: flex; align-items: center; gap: 10px;
        font-size: .95rem; font-weight: 700; color: #1e293b; margin: 0;
    }
    .ls-live-dot {
        display: inline-flex; align-items: center; gap: 5px;
        background: #ef4444; color: #fff; font-size: .65rem; font-weight: 800;
        padding: 3px 9px; border-radius: 20px; letter-spacing: .5px;
        animation: ls-blink 1.4s infinite;
    }
    .ls-live-dot i { font-size: .35rem; }
    @keyframes ls-blink { 0%,100%{opacity:1;} 50%{opacity:.55;} }

    .ls-refresh-btn {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;
        border-radius: 8px; padding: 6px 12px; font-size: .75rem; font-weight: 600;
        cursor: pointer; transition: all .15s; text-decoration: none;
    }
    .ls-refresh-btn:hover { background: #e2e8f0; color: #1e293b; text-decoration: none; }
    .ls-refresh-btn.spinning i { animation: spin .6s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Match Cards Grid */
    .ls-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .ls-match-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 20px rgba(0,0,0,.08);
        overflow: hidden;
        border: 1.5px solid #f1f5f9;
        transition: box-shadow .2s, transform .2s;
        position: relative;
    }
    .ls-match-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,.12); transform: translateY(-2px); }
    .ls-match-card--live { border-color: #fecaca; }
    .ls-match-card--completed { border-color: #d1fae5; opacity: .85; }

    /* Pulsing left bar for live matches */
    .ls-match-card--live::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: #ef4444;
        animation: ls-bar-pulse 1.4s infinite;
    }
    @keyframes ls-bar-pulse { 0%,100%{opacity:1;} 50%{opacity:.3;} }
    .ls-match-card--completed::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: #10b981;
    }

    /* Card top bar */
    .ls-card-top {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 16px 10px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }
    .ls-card-chips { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
    .ls-chip {
        font-size: .65rem; font-weight: 700; padding: 2px 8px;
        border-radius: 20px; white-space: nowrap;
    }
    .ls-chip--court  { background: #e0e7ff; color: #4f46e5; }
    .ls-chip--type   { background: #f0fdf4; color: #16a34a; }
    .ls-chip--div    { background: #fef3c7; color: #d97706; }
    .ls-chip--round  { background: #ffe4e6; color: #e11d48; }

    .ls-status-badge {
        font-size: .65rem; font-weight: 800; padding: 3px 9px;
        border-radius: 20px; white-space: nowrap;
        display: inline-flex; align-items: center; gap: 4px; letter-spacing: .3px;
    }
    .ls-status-badge--live      { background: #ef4444; color: #fff; }
    .ls-status-badge--completed { background: #d1fae5; color: #059669; }

    /* Score row */
    .ls-score-row {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 8px;
        padding: 18px 20px 14px;
    }
    .ls-player-block { display: flex; flex-direction: column; }
    .ls-player-block--right { align-items: flex-end; }

    .ls-player-name {
        font-size: .88rem; font-weight: 800; color: #1e293b;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 130px;
    }
    .ls-player-type { font-size: .65rem; color: #94a3b8; font-weight: 600; margin-top: 1px; }

    .ls-score-center { display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .ls-scores { display: flex; align-items: center; gap: 6px; }
    .ls-score {
        font-size: 2rem; font-weight: 900; color: #1e293b;
        font-family: 'DM Mono', 'Courier New', monospace;
        line-height: 1;
        transition: transform .15s, color .2s;
    }
    .ls-score--leading { color: #1a56db; }
    .ls-score--right.ls-score--leading { color: #ef4444; }
    .ls-score-dash { font-size: 1rem; font-weight: 700; color: #cbd5e1; }
    .ls-set-label {
        font-size: .6rem; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .5px;
    }

    /* Sets won indicators */
    .ls-sets-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 20px 14px;
    }
    .ls-sets-dots { display: flex; gap: 5px; }
    .ls-set-dot {
        width: 20px; height: 20px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .55rem; font-weight: 700; border: 1.5px solid #e2e8f0; color: #94a3b8;
    }
    .ls-set-dot--p1 { background: #1a56db; border-color: #1a56db; color: #fff; }
    .ls-set-dot--p2 { background: #ef4444; border-color: #ef4444; color: #fff; }
    .ls-sets-label { font-size: .65rem; font-weight: 600; color: #94a3b8; }

    /* Winner banner */
    .ls-winner-banner {
        display: flex; align-items: center; justify-content: center; gap: 6px;
        background: linear-gradient(135deg, #059669, #10b981);
        color: #fff; font-size: .75rem; font-weight: 700;
        padding: 8px 16px;
    }

    /* View button */
    .ls-view-btn {
        display: flex; align-items: center; justify-content: center; gap: 6px;
        padding: 10px 16px; font-size: .78rem; font-weight: 700;
        border-top: 1px solid #f1f5f9;
        text-decoration: none; color: #1a56db; background: #f8fafc;
        transition: background .15s;
    }
    .ls-view-btn:hover { background: #e8f0fe; color: #1040a8; text-decoration: none; }

    /* Empty state */
    .ls-empty {
        text-align: center; padding: 44px 20px;
        color: #94a3b8; font-size: .85rem;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.06);
        margin-bottom: 24px;
        border: 1.5px dashed #e2e8f0;
    }
    .ls-empty-icon { font-size: 2.5rem; margin-bottom: 10px; display: block; opacity: .35; }
    .ls-empty-title { font-size: .9rem; font-weight: 700; color: #64748b; margin-bottom: 4px; }

    /* Recent completed matches mini table */
    .ls-recent-title {
        font-size: .78rem; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: .5px;
        margin-bottom: 10px; display: flex; align-items: center; gap: 7px;
    }
    .ls-recent-title::before {
        content: ''; display: inline-block; width: 3px; height: 14px;
        background: #10b981; border-radius: 2px;
    }

    /* Bump animation on score update */
    @keyframes ls-bump {
        0%   { transform: scale(1); }
        40%  { transform: scale(1.25); color: #1a56db; }
        100% { transform: scale(1); }
    }
    .ls-score.bumping { animation: ls-bump .25s ease; }

    /* ── Responsive ───────────────────────────────────── */
    @media (max-width: 576px) {
        .pac-stat-grid  { grid-template-columns: 1fr 1fr; }
        .pac-quick-grid { grid-template-columns: 1fr 1fr; }
        .ls-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 400px) {
        .pac-stat-grid { grid-template-columns: 1fr; }
    }

    /* =============================================
       PAC WINNERS — REDESIGNED
    ============================================= */

    .pac-winners-section {
        margin: 2rem 0;
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    /* Header */
    .pac-winners-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .pac-winners-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .pac-winners-count {
        font-size: 0.8rem;
        color: #64748b;
        background: #f1f5f9;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
    }

    /* Grid */
    .pac-winners-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.25rem;
    }

    /* Card */
    .pac-winner-card {
        position: relative;
        background: #ffffff;
        border: 1.5px solid #fde68a;
        border-radius: 16px;
        padding: 0 1rem 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        box-shadow: 0 4px 24px rgba(245,158,11,0.10), 0 1px 4px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .pac-winner-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 32px rgba(245,158,11,0.18), 0 2px 8px rgba(0,0,0,0.08);
    }

    /* Top shimmer bar */
    .pac-winner-topbar {
        width: 100%;
        height: 5px;
        margin-bottom: 0.75rem;
        background: linear-gradient(90deg, #fbbf24, #f59e0b, #fcd34d, #f59e0b, #fbbf24);
        background-size: 200% 100%;
        animation: shimmer 2.5s infinite linear;
        border-radius: 0 0 4px 4px;
    }

    @keyframes shimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Tournament name */
    .pac-winner-tournament-name {
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #92400e;
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 999px;
        padding: 0.2rem 0.7rem;
        margin-bottom: 0.9rem;
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Photo */
    .pac-winner-photo-wrap {
        position: relative;
        margin-bottom: 0.85rem;
    }

    .pac-winner-photo-ring {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        padding: 3px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
        box-shadow: 0 0 0 3px #fff, 0 4px 16px rgba(245,158,11,0.25);
    }

    .pac-winner-photo {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        object-position: top center;
        display: block;
        background: #f8fafc;
    }

    .pac-winner-photo-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #d97706;
    }

    /* Trophy badge */
    .pac-winner-trophy-badge {
        position: absolute;
        bottom: -4px;
        right: -4px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fbbf24, #d97706);
        border: 2.5px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        color: #fff;
        box-shadow: 0 2px 8px rgba(245,158,11,0.4);
    }

    /* Badges */
    .pac-winner-badges {
        display: flex;
        gap: 0.4rem;
        margin-bottom: 0.6rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pac-winner-badge {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.22rem 0.65rem;
        border-radius: 999px;
        letter-spacing: 0.02em;
    }

    .pac-winner-badge--div {
        background: #ede9fe;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }

    .pac-winner-badge--type {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    /* Name */
    .pac-winner-name {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.2rem;
        line-height: 1.2;
    }

    /* Partner */
    .pac-winner-partner {
        font-size: 0.78rem;
        color: #64748b;
        margin-bottom: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        justify-content: center;
    }

    /* IDs section */
    .pac-winner-ids {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.55rem 0.75rem;
        margin-top: 0.65rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .pac-winner-id-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
    }

    .pac-winner-id-label {
        font-size: 0.65rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .pac-winner-id-value {
        font-size: 0.72rem;
        font-weight: 700;
        color: #334155;
        background: #e2e8f0;
        padding: 0.15rem 0.5rem;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        letter-spacing: 0.03em;
    }

    /* ── Doubles photo pair ──────────────────────────── */
    .pac-winner-doubles-photos {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 10px;
        position: relative;
    }

    .pac-winner-doubles-player {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }

    .pac-winner-doubles-player .pac-winner-photo-ring {
        width: 72px;
        height: 72px;
    }

    .pac-winner-doubles-player--p2 .pac-winner-photo-ring {
        background: linear-gradient(135deg, #a78bfa, #7c3aed, #c4b5fd);
        box-shadow: 0 0 0 3px #fff, 0 4px 16px rgba(124,58,237,0.25);
    }

    .pac-winner-doubles-player--p2 .pac-winner-photo-placeholder {
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        color: #7c3aed;
        font-size: 1.5rem;
    }

    .pac-winner-doubles-player--p1 .pac-winner-photo-placeholder {
        font-size: 1.5rem;
    }

    .pac-winner-doubles-pid {
        font-size: 0.6rem;
        font-weight: 700;
        background: #e0e7ff;
        color: #4f46e5;
        padding: 2px 7px;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }

    .pac-winner-doubles-trophy {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -70%);
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fbbf24, #d97706);
        border: 2.5px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        color: #fff;
        box-shadow: 0 2px 8px rgba(245,158,11,0.5);
        z-index: 2;
    }

    .pac-winner-doubles-name {
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e293b;
        margin-top: 2px;
    }

    .pac-winner-doubles-label {
        font-size: 0.6rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>
@endsection

@section('content')

    {{-- ── Page Header ── --}}
    <div class="pac-page-header">
        <div>
            <h2>Overview</h2>
            <p>Welcome back, Admin &mdash; here's what's happening today.</p>
        </div>
        <button class="pac-btn-primary" data-toggle="modal" data-target="#addTournamentModal">
            <i class="fas fa-plus"></i> Add Season
        </button>
    </div>

    {{-- ── Success Alert ── --}}
    @if(session('success'))
        <div class="pac-alert-success" id="pac-success-alert">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button class="pac-alert-close" onclick="document.getElementById('pac-success-alert').style.display='none'">&times;</button>
        </div>
    @endif

    {{-- ── Quick Navigation Links ── --}}
    <div class="pac-quick-grid">
        <a href="{{ route('admin-revenue') }}" class="pac-quick-link">
            <i class="fas fa-chart-line"></i> Revenue Report
        </a>
        <a href="{{ route('admin-users.index') }}" class="pac-quick-link">
            <i class="fas fa-users"></i> All Users
        </a>
        <a href="{{ route('admin-match-results') }}" class="pac-quick-link">
            <i class="fas fa-fist-raised"></i> Match Results
        </a>
        <a href="{{ route('admin-sponsors') }}" class="pac-quick-link">
            <i class="fas fa-handshake"></i> Sponsors
        </a>
        <a href="{{ route('admin-audience') }}" class="pac-quick-link">
            <i class="fas fa-bullhorn"></i> Audience
        </a>
        <a href="{{ route('admin-matches.setup') }}" class="pac-quick-link">
            <i class="fas fa-gamepad"></i> Game Play
        </a>
    </div>


    {{-- ══════════════════════════════════════════
         TOURNAMENT WINNERS
    ══════════════════════════════════════════ --}}
    @if($winnerMatches->isNotEmpty())
    <div class="pac-winners-section">

        <div class="pac-winners-header">
            <span class="pac-winners-title">
                <i class="fas fa-trophy" style="color:#f59e0b;"></i>
                Tournament Winners
            </span>
            <span class="pac-winners-count">
                {{ $winnerMatches->count() }} category winner{{ $winnerMatches->count() > 1 ? 's' : '' }} declared
            </span>
        </div>

        <div class="pac-winners-grid">
            @foreach($winnerMatches as $w)
            <div class="pac-winner-card">

                {{-- Shimmer top bar --}}
                <div class="pac-winner-topbar"></div>

                {{-- Tournament name banner --}}
                <div class="pac-winner-tournament-name">
                    <i class="fas fa-shuttlecock" style="font-size:0.7rem;"></i>
                    {{ $w->tournament_name ?? 'Ekalavya' }}
                    <br>
                  
                    {{ $w->tournament_name ?? 'Badminton Tournament' }}
                </div>

                @php
                    $winnerPhoto  = \App\Models\Player::where('player_id', $w->winner_id)->value('profile_photo');
                    $isDoubles    = $w->match_type !== 'singles' && $w->partner;
                    $partnerPhoto = $isDoubles
                        ? \App\Models\Player::where('player_id', $w->partner->player_id ?? null)->value('profile_photo')
                        : null;
                @endphp

                @if($isDoubles)
                {{-- ══ DOUBLES: two photos side-by-side ══ --}}
                <div class="pac-winner-photo-wrap">
                    <div class="pac-winner-doubles-photos">

                        {{-- Player 1 (winner) --}}
                        <div class="pac-winner-doubles-player pac-winner-doubles-player--p1">
                            <div class="pac-winner-photo-ring">
                                @if($winnerPhoto)
                                    <img src="{{ asset('storage/' . $winnerPhoto) }}"
                                         alt="{{ $w->winner_name }}"
                                         class="pac-winner-photo" />
                                @else
                                    <div class="pac-winner-photo-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </div>
                            <span class="pac-winner-doubles-pid">{{ $w->winner_id }}</span>
                        </div>

                        {{-- Trophy in middle --}}
                        <div class="pac-winner-doubles-trophy">
                            <i class="fas fa-trophy"></i>
                        </div>

                        {{-- Player 2 (partner) --}}
                        <div class="pac-winner-doubles-player pac-winner-doubles-player--p2">
                            <div class="pac-winner-photo-ring">
                                @if($partnerPhoto)
                                    <img src="{{ asset('storage/' . $partnerPhoto) }}"
                                         alt="{{ $w->partner->name }}"
                                         class="pac-winner-photo" />
                                @else
                                    <div class="pac-winner-photo-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </div>
                            <span class="pac-winner-doubles-pid">{{ $w->partner->player_id ?? '—' }}</span>
                        </div>

                    </div>
                </div>

                @else
                {{-- ══ SINGLES: original single photo ══ --}}
                <div class="pac-winner-photo-wrap">
                    <div class="pac-winner-photo-ring">
                        @if($winnerPhoto)
                            <img src="{{ asset('storage/' . $winnerPhoto) }}"
                                 alt="{{ $w->winner_name }}"
                                 class="pac-winner-photo" />
                        @else
                            <div class="pac-winner-photo-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <div class="pac-winner-trophy-badge">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
                @endif

                {{-- Category badges --}}
                <div class="pac-winner-badges">
                    <span class="pac-winner-badge pac-winner-badge--div">{{ $w->division }}</span>
                    <span class="pac-winner-badge pac-winner-badge--type">
                        {{ $w->match_type === 'singles' ? 'Singles' : 'Doubles' }}
                    </span>
                </div>

                @if($isDoubles)
                {{-- Doubles: show both names --}}
                <div class="pac-winner-name">{{ $w->winner_name }}</div>
                <div class="pac-winner-partner">
                    <i class="fas fa-handshake"></i>
                    &amp; {{ $w->partner->name }}
                </div>
                @else
                {{-- Singles: just winner name --}}
                <div class="pac-winner-name">{{ $w->winner_name }}</div>
                @endif

                {{-- IDs section --}}
                <div class="pac-winner-ids">
                    @if($isDoubles)
                        {{-- Doubles: both player IDs --}}
                        <div class="pac-winner-id-row">
                            <span class="pac-winner-id-label">
                                <i class="fas fa-user" style="color:#1a56db;margin-right:3px;"></i>Player 1 ID
                            </span>
                            <span class="pac-winner-id-value">{{ $w->winner_id }}</span>
                        </div>
                        <div class="pac-winner-id-row" style="border-top:1px dashed #e2e8f0;padding-top:.35rem;margin-top:.1rem;">
                            <span class="pac-winner-id-label">
                                <i class="fas fa-user" style="color:#7c3aed;margin-right:3px;"></i>Player 2 ID
                            </span>
                            <span class="pac-winner-id-value">{{ $w->partner->player_id ?? '—' }}</span>
                        </div>
                    @else
                        {{-- Singles: single player ID --}}
                        <div class="pac-winner-id-row">
                            <span class="pac-winner-id-label">Player ID</span>
                            <span class="pac-winner-id-value">{{ $w->winner_id }}</span>
                        </div>
                    @endif
                </div>

            </div>
            @endforeach
        </div>

    </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════
         LIVE SCORE SECTION
    ══════════════════════════════════════════════════════════════ --}}
    <div class="ls-section-header">
        <div class="ls-section-title">
            <i class="fas fa-bolt" style="color:#f59e0b;"></i>
            Live Scores
            <span class="ls-live-dot" id="lsLiveDot" style="display:none;">
                <i class="fas fa-circle"></i> LIVE
            </span>
        </div>
        <button class="ls-refresh-btn" id="lsRefreshBtn" onclick="fetchLiveScores(true)">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>

    {{-- Live match cards — rendered by JS --}}
    <div id="lsContainer">
        {{-- Initial server-side render for zero-flash load --}}
        @if($liveMatches->isEmpty())
            <div class="ls-empty" id="lsEmpty">
                <span class="ls-empty-icon">🏸</span>
                <div class="ls-empty-title">No live matches right now</div>
                <div>Start a match from <a href="{{ route('admin-matches.setup') }}" style="color:#1a56db;font-weight:600;">Game Play</a> to see live scores here.</div>
            </div>
        @else
            <div class="ls-grid" id="lsGrid">
                @foreach($liveMatches as $m)
                @php
                    $isLeadP1 = $m->score_p1 > $m->score_p2;
                    $isLeadP2 = $m->score_p2 > $m->score_p1;
                    $p1 = (object) $m->p1_info;
                    $p2 = (object) $m->p2_info;
                @endphp
                <div class="ls-match-card ls-match-card--live" id="lsCard{{ $m->id }}">

                    {{-- Top chips --}}
                    <div class="ls-card-top">
                        <div class="ls-card-chips">
                            <span class="ls-chip ls-chip--court">
                                <i class="fas fa-map-marker-alt mr-1"></i>Court {{ $m->court_no }}
                            </span>
                            <span class="ls-chip ls-chip--type">{{ ucfirst($m->match_type) }}</span>
                            <span class="ls-chip ls-chip--div">{{ $m->division }}</span>
                            <span class="ls-chip ls-chip--round">{{ $m->getRoundLabel() }}</span>
                        </div>
                        <span class="ls-status-badge ls-status-badge--live">
                            <i class="fas fa-circle" style="font-size:.35rem;"></i> LIVE
                        </span>
                    </div>

                    {{-- Score row --}}
                    <div class="ls-score-row">
                        <div class="ls-player-block">
                            <div class="ls-player-name">{{ $p1->season_id }}</div>
                            <div class="ls-player-type">
                                {{ $m->match_type === 'doubles' ? 'Team 1' : 'Player 1' }}
                            </div>
                        </div>
                        <div class="ls-score-center">
                            <div class="ls-scores">
                                <span class="ls-score {{ $isLeadP1 ? 'ls-score--leading' : '' }}"
                                      id="lsS1_{{ $m->id }}">{{ $m->score_p1 }}</span>
                                <span class="ls-score-dash">—</span>
                                <span class="ls-score ls-score--right {{ $isLeadP2 ? 'ls-score--leading ls-score--right' : '' }}"
                                      id="lsS2_{{ $m->id }}">{{ $m->score_p2 }}</span>
                            </div>
                            <span class="ls-set-label" id="lsSetLabel_{{ $m->id }}">
                                @if($m->total_sets > 1)
                                    Set {{ $m->current_set }} of {{ $m->total_sets }}
                                @else
                                    Current Score
                                @endif
                            </span>
                        </div>
                        <div class="ls-player-block ls-player-block--right">
                            <div class="ls-player-name" style="text-align:right;">{{ $p2->season_id }}</div>
                            <div class="ls-player-type" style="text-align:right;">
                                {{ $m->match_type === 'doubles' ? 'Team 2' : 'Player 2' }}
                            </div>
                        </div>
                    </div>

                    {{-- Sets won dots --}}
                    @if($m->total_sets > 1)
                    <div class="ls-sets-row">
                        <div class="ls-sets-dots" id="lsDots1_{{ $m->id }}">
                            @for($s = 0; $s < $m->sets_won_p1; $s++)
                                <div class="ls-set-dot ls-set-dot--p1">✓</div>
                            @endfor
                            @for($s = $m->sets_won_p1; $s < ceil($m->total_sets / 2); $s++)
                                <div class="ls-set-dot"></div>
                            @endfor
                        </div>
                        <span class="ls-sets-label">Sets Won</span>
                        <div class="ls-sets-dots" id="lsDots2_{{ $m->id }}">
                            @for($s = 0; $s < $m->sets_won_p2; $s++)
                                <div class="ls-set-dot ls-set-dot--p2">✓</div>
                            @endfor
                            @for($s = $m->sets_won_p2; $s < ceil($m->total_sets / 2); $s++)
                                <div class="ls-set-dot"></div>
                            @endfor
                        </div>
                    </div>
                    @endif

                    {{-- ── Player detail highlight panel ── --}}
                    <div class="ls-player-panel">

                        {{-- Singles --}}
                        @if($m->match_type === 'singles')
                        <div class="ls-panel-row">
                            <div class="ls-panel-player ls-panel-player--p1">
                                <div class="ls-panel-label">Player 1</div>
                                <div class="ls-panel-name">ID:{{ $p1->id }}</div>
                                <div class="ls-panel-meta">
                                    <span>Name: {{ $p1->name }}</span>
                                    <span>City: {{ $p1->city }}</span>
                                </div>
                            </div>

                            <div class="ls-panel-divider"></div>

                            <div class="ls-panel-player ls-panel-player--p2">
                                <div class="ls-panel-label">Player 2</div>
                                <div class="ls-panel-name">ID:{{ $p2->id }}</div>
                                <div class="ls-panel-meta">
                                    <span>Name: {{ $p2->name }}</span>
                                    <span>City: {{ $p2->city }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Doubles --}}
                        @if($m->match_type === 'doubles')

                        {{-- Team ID row --}}
                        <div class="ls-panel-row ls-panel-team">
                            <span class="ls-panel-team-id">
                                <i class="fas fa-users"></i> Team ID: {{ $p1->team_id }}
                            </span>
                            <span style="color:#cbd5e1;font-size:.7rem;">vs</span>
                            <span class="ls-panel-team-id">
                                <i class="fas fa-users"></i> Team ID: {{ $p2->team_id }}
                            </span>
                        </div>

                        <div class="ls-panel-row">

                            {{-- Team 1 --}}
                            <div class="ls-panel-player ls-panel-player--p1">

                                {{-- Player 1 of Team 1 --}}
                                <div class="ls-panel-label">Player 1</div>
                                <div class="ls-panel-name">ID:{{ $p1->id }}</div>
                                <div class="ls-panel-meta">
                                    <span>Name: {{ $p1->name }}</span>
                                    <span>City: {{ $p1->city }}</span>
                                </div>

                                {{-- Player 2 of Team 1 (partner) --}}
                                @if(!empty($p1->partner))
                                <div style="margin-top:10px;">
                                    <div class="ls-panel-label">Player 2</div>
                                    <div class="ls-panel-name">ID:{{ $p1->partner['id'] }}</div>
                                    <div class="ls-panel-meta">
                                        <span>Name: {{ $p1->partner['name'] }}</span>
                                        <span>City: {{ $p1->partner['city'] }}</span>
                                    </div>
                                </div>
                                @endif

                            </div>

                            <div class="ls-panel-divider"></div>

                            {{-- Team 2 --}}
                            <div class="ls-panel-player ls-panel-player--p2">

                                {{-- Player 1 of Team 2 --}}
                                <div class="ls-panel-label">Player 1</div>
                                <div class="ls-panel-name">ID:{{ $p2->id }}</div>
                                <div class="ls-panel-meta">
                                    <span>Name: {{ $p2->name }}</span>
                                    <span>City: {{ $p2->city }}</span>
                                </div>

                                {{-- Player 2 of Team 2 (partner) --}}
                                @if(!empty($p2->partner))
                                <div style="margin-top:10px;">
                                    <div class="ls-panel-label">Player 2</div>
                                    <div class="ls-panel-name">ID:{{ $p2->partner['id'] }}</div>
                                    <div class="ls-panel-meta">
                                        <span>Name: {{ $p2->partner['name'] }}</span>
                                        <span>City: {{ $p2->partner['city'] }}</span>
                                    </div>
                                </div>
                                @endif

                            </div>

                        </div>
                        @endif

                    </div>
                    {{-- ── End player panel ── --}}

                    <a href="{{ route('admin-matches.live', $m->id) }}" class="ls-view-btn">
                        <i class="fas fa-external-link-alt"></i> Open Scoreboard
                    </a>

                </div>
                @endforeach
            </div>
        @endif

        {{-- Recently completed matches --}}
        @if($recentMatches->isNotEmpty())
        <div style="margin-bottom:24px;">
            <div class="ls-recent-title">Recently Completed</div>
            <div class="pac-card">
                <div style="overflow-x:auto;">
                    <table class="pac-table">
                        <thead>
                            <tr>
                                <th>Players</th>
                                <th>Type</th>
                                <th>Division</th>
                                <th>Round</th>
                                <th>Winner</th>
                                <th>Court</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentMatches as $m)
                            <tr>
                                <td>
                                    <div style="font-weight:700;font-size:.83rem;color:#1e293b;">{{ $m->p1_name }}</div>
                                    <div style="font-size:.72rem;color:#94a3b8;margin-top:2px;">vs {{ $m->p2_name }}</div>
                                </td>
                                <td>
                                    <span class="pac-badge" style="background:#f0fdf4;color:#16a34a;">{{ ucfirst($m->match_type) }}</span>
                                </td>
                                <td style="font-size:.78rem;font-weight:600;color:#64748b;">{{ $m->division }}</td>
                                <td style="font-size:.78rem;font-weight:600;color:#64748b;">{{ $m->getRoundLabel() }}</td>
                                <td>
                                    @if($m->winner_name)
                                        <span style="font-size:.78rem;font-weight:700;color:#059669;">
                                            <i class="fas fa-trophy mr-1" style="font-size:.6rem;"></i>{{ $m->winner_name }}
                                        </span>
                                    @else
                                        <span style="color:#94a3b8;font-size:.75rem;">—</span>
                                    @endif
                                </td>
                                <td style="font-size:.78rem;color:#64748b;">{{ $m->court_no }}</td>
                                <td>
                                    <a href="{{ route('admin-matches.complete', $m->id) }}"
                                       style="font-size:.75rem;color:#1a56db;font-weight:600;text-decoration:none;">
                                        View <i class="fas fa-arrow-right ml-1" style="font-size:.6rem;"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Tournament Seasons Table ── --}}
    <div class="pac-card">
        <div class="pac-card-header">
            <div>
                <div class="pac-section-title">Tournament Seasons</div>
                <div style="font-size:.75rem;color:#64748b;margin-top:2px;">
                    {{ $seasons->count() }} total &mdash;
                    {{ $seasons->where('status','active')->count() }} active,
                    {{ $seasons->where('status','upcoming')->count() }} upcoming,
                    {{ $seasons->where('status','completed')->count() }} completed
                </div>
            </div>
            <button class="pac-btn-primary" data-toggle="modal" data-target="#addTournamentModal">
                <i class="fas fa-plus"></i> Add
            </button>
        </div>

        <div class="pac-card-body">
            @if($seasons->isEmpty())
                <div style="padding:40px;text-align:center;color:#94a3b8;font-size:.875rem;">
                    <i class="fas fa-trophy fa-2x mb-3" style="opacity:.3;display:block;"></i>
                    No tournament seasons yet. Click <strong>Add</strong> to get started.
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table class="pac-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tournament</th>
                                <th>Season</th>
                                <th>Venue</th>
                                <th>Status</th>
                                <th>Change Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seasons as $index => $season)
                                <tr>
                                    <td style="color:#94a3b8;font-size:.75rem;">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="pac-tournament-cell">
                                            <span class="pac-tournament-name">{{ $season->tournament_name ?? '—' }}</span>
                                            <span class="pac-tournament-sport">{{ $season->tournament_sport ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td style="font-weight:600;">{{ $season->label }}</td>
                                    <td style="color:#64748b;">{{ $season->venue ?? '—' }}</td>
                                    <td>
                                        @if($season->status === 'active')
                                            <span class="pac-badge pac-badge--active">
                                                <i class="fas fa-circle" style="font-size:.4rem;"></i> Active
                                            </span>
                                        @elseif($season->status === 'upcoming')
                                            <span class="pac-badge pac-badge--upcoming">
                                                <i class="fas fa-clock" style="font-size:.55rem;"></i> Upcoming
                                            </span>
                                        @else
                                            <span class="pac-badge pac-badge--done">
                                                <i class="fas fa-check" style="font-size:.55rem;"></i> Completed
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin-seasons-updateStatus', $season->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="pac-status-select" onchange="this.form.submit()">
                                                <option value="upcoming"  {{ $season->status === 'upcoming'  ? 'selected' : '' }}>Upcoming</option>
                                                <option value="active"    {{ $season->status === 'active'    ? 'selected' : '' }}>Active</option>
                                                <option value="completed" {{ $season->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Add Tournament Season Modal ── --}}
    <div class="modal fade" id="addTournamentModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle mr-2 text-primary"></i>
                        Add New Tournament Season
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('admin-seasons-store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tournament <span class="text-danger">*</span></label>
                            <select name="tournament_id" class="form-control" required>
                                <option value="">— Select Tournament —</option>
                                @foreach($tournaments as $tournament)
                                    <option value="{{ $tournament->id }}">{{ $tournament->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label>Season Label <span class="text-danger">*</span></label>
                                <input type="text" name="label" class="form-control" placeholder="e.g. Ekalavya S1 2026" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Season No. <span class="text-danger">*</span></label>
                                <input type="number" name="season_number" class="form-control" placeholder="e.g. 1" min="1" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Venue</label>
                            <input type="text" name="venue" class="form-control" placeholder="e.g. Indoor Stadium, Hyderabad">
                        </div>
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="upcoming">Upcoming</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Optional description..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="pac-btn-primary">
                            <i class="fas fa-plus"></i> Add Season
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
// ── Live Score Polling ──────────────────────────────────────────────────────
const LS_API_URL  = "{{ route('admin-dashboard.live-scores') }}";
const CSRF_TOKEN  = "{{ csrf_token() }}";

// Track previous scores to detect changes and animate
const prevScores = {};

async function fetchLiveScores(manual = false) {
    const btn = document.getElementById('lsRefreshBtn');
    btn.classList.add('spinning');

    try {
        const res  = await fetch(LS_API_URL, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await res.json();
        renderLiveScores(data.matches);
    } catch (e) {
        console.error('Live score fetch failed:', e);
    } finally {
        btn.classList.remove('spinning');
    }
}

function renderLiveScores(matches) {
    const container = document.getElementById('lsContainer');

    // Update LIVE dot visibility
    const liveDot = document.getElementById('lsLiveDot');
    liveDot.style.display = matches.length > 0 ? 'inline-flex' : 'none';

    // Find existing grid or empty state
    let grid = document.getElementById('lsGrid');
    const empty = document.getElementById('lsEmpty');

    if (matches.length === 0) {
        // No live matches — show empty, hide grid
        if (grid) grid.style.display = 'none';
        if (empty) empty.style.display = 'block';
        return;
    }

    // Hide empty state
    if (empty) empty.style.display = 'none';

    // Build / refresh grid
    if (!grid) {
        grid = document.createElement('div');
        grid.id = 'lsGrid';
        grid.className = 'ls-grid';
        // Insert before the recently completed section (or at top of container)
        const recentEl = container.querySelector('.ls-recent-section');
        container.insertBefore(grid, recentEl || container.firstChild);
    }
    grid.style.display = '';

    // Track which match IDs are currently live
    const currentIds = new Set(matches.map(m => m.id));

    // Remove cards for matches no longer live
    grid.querySelectorAll('[id^="lsCard"]').forEach(card => {
        const id = parseInt(card.id.replace('lsCard', ''));
        if (!currentIds.has(id)) card.remove();
    });

    // Add / update each live match
    matches.forEach(m => {
        const existing = document.getElementById('lsCard' + m.id);
        if (existing) {
            updateCard(m, existing);
        } else {
            grid.insertAdjacentHTML('beforeend', buildCard(m));
        }
    });
}

function updateCard(m, card) {
    // Scores
    const s1El = document.getElementById('lsS1_' + m.id);
    const s2El = document.getElementById('lsS2_' + m.id);
    const prev = prevScores[m.id] || {};

    if (s1El && prev.s1 !== m.score_p1) {
        s1El.textContent = m.score_p1;
        bump(s1El);
    }
    if (s2El && prev.s2 !== m.score_p2) {
        s2El.textContent = m.score_p2;
        bump(s2El);
    }

    // Leading highlight
    if (s1El) {
        s1El.classList.toggle('ls-score--leading', m.score_p1 > m.score_p2);
        s1El.classList.remove('ls-score--right');
    }
    if (s2El) {
        s2El.classList.toggle('ls-score--leading', m.score_p2 > m.score_p1);
        if (m.score_p2 > m.score_p1) s2El.classList.add('ls-score--right');
        else s2El.classList.remove('ls-score--right');
    }

    // Set label
    const setLabel = document.getElementById('lsSetLabel_' + m.id);
    if (setLabel) {
        setLabel.textContent = m.total_sets > 1
            ? 'Set ' + m.current_set + ' of ' + m.total_sets
            : 'Current Score';
    }

    // Sets won dots
    updateSetDots(m);

    prevScores[m.id] = { s1: m.score_p1, s2: m.score_p2 };
}

function updateSetDots(m) {
    const needed = Math.ceil(m.total_sets / 2);
    ['p1', 'p2'].forEach((p, pi) => {
        const container = document.getElementById(`lsDots${pi+1}_${m.id}`);
        if (!container) return;
        const won = pi === 0 ? m.sets_won_p1 : m.sets_won_p2;
        container.innerHTML = '';
        for (let i = 0; i < needed; i++) {
            const dot = document.createElement('div');
            dot.className = 'ls-set-dot' + (i < won ? (pi === 0 ? ' ls-set-dot--p1' : ' ls-set-dot--p2') : '');
            dot.textContent = i < won ? '✓' : '';
            container.appendChild(dot);
        }
    });
}

function buildCard(m) {
    const isLeadP1 = m.score_p1 > m.score_p2;
    const isLeadP2 = m.score_p2 > m.score_p1;
    const needed   = Math.ceil(m.total_sets / 2);

    const dotsP1 = Array.from({length: needed}, (_, i) =>
        `<div class="ls-set-dot ${i < m.sets_won_p1 ? 'ls-set-dot--p1' : ''}">
            ${i < m.sets_won_p1 ? '✓' : ''}
        </div>`).join('');
    const dotsP2 = Array.from({length: needed}, (_, i) =>
        `<div class="ls-set-dot ${i < m.sets_won_p2 ? 'ls-set-dot--p2' : ''}">
            ${i < m.sets_won_p2 ? '✓' : ''}
        </div>`).join('');

    const setsRow = m.total_sets > 1 ? `
        <div class="ls-sets-row">
            <div class="ls-sets-dots" id="lsDots1_${m.id}">${dotsP1}</div>
            <span class="ls-sets-label">Sets Won</span>
            <div class="ls-sets-dots" id="lsDots2_${m.id}">${dotsP2}</div>
        </div>` : '';

    const setLabelText = m.total_sets > 1
        ? `Set ${m.current_set} of ${m.total_sets}`
        : 'Current Score';

    const teamLabel1 = m.match_type === 'doubles' ? 'Team 1' : 'Player 1';
    const teamLabel2 = m.match_type === 'doubles' ? 'Team 2' : 'Player 2';

    prevScores[m.id] = { s1: m.score_p1, s2: m.score_p2 };

    return `
    <div class="ls-match-card ls-match-card--live" id="lsCard${m.id}">
        <div class="ls-card-top">
            <div class="ls-card-chips">
                <span class="ls-chip ls-chip--court"><i class="fas fa-map-marker-alt" style="margin-right:3px;"></i>Court ${m.court_no}</span>
                <span class="ls-chip ls-chip--type">${capitalise(m.match_type)}</span>
                <span class="ls-chip ls-chip--div">${m.division}</span>
                <span class="ls-chip ls-chip--round">${m.round_label}</span>
            </div>
            <span class="ls-status-badge ls-status-badge--live">
                <i class="fas fa-circle" style="font-size:.35rem;"></i> LIVE
            </span>
        </div>
        <div class="ls-score-row">
            <div class="ls-player-block">
                <div class="ls-player-name">${escHtml(m.p1_name)}</div>
                <div class="ls-player-type">${teamLabel1}</div>
            </div>
            <div class="ls-score-center">
                <div class="ls-scores">
                    <span class="ls-score ${isLeadP1 ? 'ls-score--leading' : ''}" id="lsS1_${m.id}">${m.score_p1}</span>
                    <span class="ls-score-dash">—</span>
                    <span class="ls-score ls-score--right ${isLeadP2 ? 'ls-score--leading ls-score--right' : ''}" id="lsS2_${m.id}">${m.score_p2}</span>
                </div>
                <span class="ls-set-label" id="lsSetLabel_${m.id}">${setLabelText}</span>
            </div>
            <div class="ls-player-block ls-player-block--right">
                <div class="ls-player-name" style="text-align:right;">${escHtml(m.p2_name)}</div>
                <div class="ls-player-type" style="text-align:right;">${teamLabel2}</div>
            </div>
        </div>
        ${setsRow}
        <a href="/matches/${m.id}/live" class="ls-view-btn">
            <i class="fas fa-external-link-alt"></i> Open Scoreboard
        </a>
    </div>`;
}

function bump(el) {
    el.classList.remove('bumping');
    void el.offsetWidth; // reflow
    el.classList.add('bumping');
    setTimeout(() => el.classList.remove('bumping'), 300);
}

function capitalise(s) {
    return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

// ── Auto-poll every 8 seconds ───────────────────────────────────────────────
setInterval(fetchLiveScores, 8000);

// ── Alert auto-dismiss ──────────────────────────────────────────────────────
const alert = document.getElementById('pac-success-alert');
if (alert) { setTimeout(function(){ alert.style.display = 'none'; }, 4000); }
</script>
@endsection