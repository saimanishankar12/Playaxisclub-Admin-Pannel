@extends('Admin.layouts.app')
@section('title', 'Edit Player — ' . $player->player_id)
@section('content')

{{-- ── Page heading ───────────────────────────────────────────────────────── --}}
<div class="eu-page-header">
    <div class="eu-page-header-left">
        <a href="javascript:history.back()" class="eu-back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="eu-page-title">Edit Player</h1>
            <div class="eu-page-sub">
                <span class="eu-pid-badge"><i class="fas fa-id-badge mr-1"></i>{{ $player->player_id }}</span>
                <span class="eu-sep">·</span>
                <span class="eu-season-badge"><i class="fas fa-layer-group mr-1"></i>{{ $player->season_id }}</span>
                <span class="eu-sep">·</span>
                <span class="eu-mode-badge eu-mode-badge--{{ $player->mode }}">
                    {{ ucfirst($player->mode) }}
                </span>
            </div>
        </div>
    </div>
    {{-- Current payment status pill (display only) --}}
    <span class="eu-status-pill eu-status-pill--{{ $player->payment_status }}">
        @if($player->payment_status === 'paid')
            <i class="fas fa-check-circle mr-1"></i> Paid
        @else
            <i class="fas fa-clock mr-1"></i> Pending
        @endif
    </span>
</div>

{{-- ── Flash messages ─────────────────────────────────────────────────────── --}}
@if(session('success'))
<div class="eu-alert eu-alert--success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="eu-alert-close">&times;</button>
</div>
@endif
@if($errors->any())
<div class="eu-alert eu-alert--danger">
    <i class="fas fa-exclamation-circle"></i>
    <ul class="mb-0 pl-3">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button onclick="this.parentElement.remove()" class="eu-alert-close">&times;</button>
</div>
@endif

{{-- ── Readonly info banner ────────────────────────────────────────────────── --}}
<div class="eu-readonly-banner">
    <i class="fas fa-lock"></i>
    <span><strong>Player ID</strong>, <strong>Season ID</strong>, <strong>Sport</strong>, and <strong>Mode</strong> are locked and cannot be changed.</span>
</div>

{{-- ── Main form ───────────────────────────────────────────────────────────── --}}
<form action="{{ route('admin-users.update', $player->id) }}"
      method="POST"
      enctype="multipart/form-data"
      id="editPlayerForm">
    @csrf
    @method('PUT')

    {{-- Locked fields passed as hidden inputs --}}
    <input type="hidden" name="sport"  value="{{ $player->sport }}">
    <input type="hidden" name="mode"   value="{{ $player->mode }}">
    <input type="hidden" name="gender" value="male">
    {{-- For paid players, pass payment_status silently; pending players use the dropdown below --}}
    @if($player->payment_status === 'paid')
        <input type="hidden" name="payment_status" value="paid">
    @endif

    <div class="eu-grid">

        {{-- ════════════════ LEFT COLUMN ════════════════ --}}
        <div class="eu-col">

            {{-- Profile photo card --}}
            <div class="eu-card">
                <div class="eu-card-header">
                    <i class="fas fa-user-circle"></i> Profile Photo
                </div>
                <div class="eu-card-body eu-photo-section">
                    <div class="eu-photo-preview" id="photoPreview">
                        @if($player->profile_photo)
                            <img src="{{ asset('storage/' . $player->profile_photo) }}"
                                 alt="Profile" id="photoImg" />
                        @else
                            <div class="eu-photo-placeholder" id="photoPlaceholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <label class="eu-upload-btn" for="profile_photo">
                        <i class="fas fa-camera mr-1"></i> Change Photo
                    </label>
                    <input type="file" id="profile_photo" name="profile_photo"
                           accept="image/*" class="eu-file-input"
                           onchange="previewImage(this,'photoImg','photoPlaceholder')">
                    <p class="eu-upload-hint">JPG, PNG, WEBP · max 2 MB</p>
                </div>
            </div>

            {{-- Aadhar proof card --}}
            <div class="eu-card mt-3">
                <div class="eu-card-header">
                    <i class="fas fa-id-card"></i> Aadhar Proof
                </div>
                <div class="eu-card-body">
                    @if($player->aadhar_proof)
                    <div class="eu-existing-file">
                        <i class="fas fa-file-alt"></i>
                        <a href="{{ asset('storage/' . $player->aadhar_proof) }}"
                           target="_blank" class="eu-file-link">
                            View current document
                        </a>
                    </div>
                    @endif
                    <label class="eu-upload-btn eu-upload-btn--gray mt-2" for="aadhar_proof">
                        <i class="fas fa-upload mr-1"></i>
                        {{ $player->aadhar_proof ? 'Replace Document' : 'Upload Document' }}
                    </label>
                    <input type="file" id="aadhar_proof" name="aadhar_proof"
                           accept=".jpg,.jpeg,.png,.pdf" class="eu-file-input"
                           onchange="showFileName(this,'aadharFileName')">
                    <p class="eu-upload-hint" id="aadharFileName">JPG, PNG, PDF · max 4 MB</p>
                </div>
            </div>

            {{-- Locked IDs + Sport + Mode card --}}
            <div class="eu-card mt-3 eu-card--locked">
                <div class="eu-card-header">
                    <i class="fas fa-lock"></i> Locked Fields
                </div>
                <div class="eu-card-body">
                    <div class="eu-field">
                        <label class="eu-label">Player ID</label>
                        <div class="eu-locked-input">
                            <i class="fas fa-lock eu-lock-icon"></i>
                            {{ $player->player_id }}
                        </div>
                    </div>
                    <div class="eu-field">
                        <label class="eu-label">Season ID</label>
                        <div class="eu-locked-input">
                            <i class="fas fa-lock eu-lock-icon"></i>
                            {{ $player->season_id }}
                        </div>
                    </div>
                    <div class="eu-field">
                        <label class="eu-label">Sport</label>
                        <div class="eu-locked-input">
                            <i class="fas fa-lock eu-lock-icon"></i>
                            {{ $player->sport }}
                        </div>
                    </div>
                    <div class="eu-field">
                        <label class="eu-label">Mode</label>
                        <div class="eu-locked-input">
                            <i class="fas fa-lock eu-lock-icon"></i>
                            {{ ucfirst($player->mode) }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ════════════════ RIGHT COLUMN ════════════════ --}}
        <div class="eu-col eu-col--wide">

            {{-- Personal info --}}
            <div class="eu-card">
                <div class="eu-card-header">
                    <i class="fas fa-user"></i> Personal Information
                </div>
                <div class="eu-card-body">
                    <div class="eu-row">
                        <div class="eu-field">
                            <label class="eu-label" for="name">Full Name <span class="eu-req">*</span></label>
                            <input type="text" id="name" name="name"
                                   class="eu-input @error('name') is-invalid @enderror"
                                   value="{{ old('name', $player->name) }}"
                                   placeholder="Full name" required>
                            @error('name')<div class="eu-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="eu-field">
                            <label class="eu-label" for="email">Email <span class="eu-req">*</span></label>
                            <input type="email" id="email" name="email"
                                   class="eu-input @error('email') is-invalid @enderror"
                                   value="{{ old('email', $player->email) }}"
                                   placeholder="email@example.com" required>
                            @error('email')<div class="eu-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="eu-row">
                        <div class="eu-field">
                            <label class="eu-label" for="phone">Phone <span class="eu-req">*</span></label>
                            <input type="text" id="phone" name="phone"
                                   class="eu-input @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $player->phone) }}"
                                   placeholder="10-digit mobile" required>
                            @error('phone')<div class="eu-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="eu-field">
                            <label class="eu-label">Gender</label>
                            <div class="eu-locked-input eu-locked-input--inline">
                                <i class="fas fa-lock eu-lock-icon"></i>
                                Male
                            </div>
                        </div>
                    </div>

                    <div class="eu-field">
                        <label class="eu-label" for="address">Address</label>
                        <textarea id="address" name="address" rows="2"
                                  class="eu-input eu-textarea @error('address') is-invalid @enderror"
                                  placeholder="Full address">{{ old('address', $player->address) }}</textarea>
                        @error('address')<div class="eu-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="eu-row">
                        <div class="eu-field">
                            <label class="eu-label" for="state_id">State</label>
                            <select id="state_id" name="state_id"
                                    class="eu-select @error('state_id') is-invalid @enderror">
                                <option value="">Select state</option>
                                @php
                                    $allowedStates = $states->filter(fn($s) => in_array(trim($s->name), ['Andhra Pradesh', 'Telangana']));
                                @endphp
                                @foreach($allowedStates as $state)
                                    <option value="{{ $state->id }}"
                                        {{ old('state_id', $player->state_id) == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('state_id')<div class="eu-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tournament info --}}
            <div class="eu-card mt-3">
                <div class="eu-card-header">
                    <i class="fas fa-table-tennis"></i> Tournament Details
                </div>
                <div class="eu-card-body">
                    <div class="eu-row">
                        <div class="eu-field">
                            <label class="eu-label" for="age">Age Category <span class="eu-req">*</span></label>
                            <select id="age" name="age"
                                    class="eu-select @error('age') is-invalid @enderror" required>
                                <option value="">Select category</option>
                                @foreach(['U-11','U-13','U-15','U-19'] as $cat)
                                    <option value="{{ $cat }}"
                                        {{ old('age', $player->age) === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('age')<div class="eu-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="eu-field">
                            <label class="eu-label" for="tshirt_size">T-Shirt Size <span class="eu-req">*</span></label>
                            <select id="tshirt_size" name="tshirt_size"
                                    class="eu-select @error('tshirt_size') is-invalid @enderror" required>
                                <option value="">Select size</option>
                                @foreach(['XS','S','M','L','XL','XXL'] as $size)
                                    <option value="{{ $size }}"
                                        {{ old('tshirt_size',$player->tshirt_size)===$size?'selected':'' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tshirt_size')<div class="eu-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Payment Status — ONLY for pending players ─────────────── --}}
            @if($player->payment_status === 'pending')
            <div class="eu-card mt-3 eu-card--payment">
                <div class="eu-card-header eu-card-header--payment">
                    <i class="fas fa-credit-card"></i> Payment Status
                    <span class="eu-payment-editable-badge">Admin Override</span>
                </div>
                <div class="eu-card-body">
                    <p class="eu-payment-notice">
                        <i class="fas fa-info-circle"></i>
                        Changing this to <strong>Paid</strong> will move the player to the paid list immediately.
                    </p>
                    <div class="eu-field" style="margin-bottom:0;">
                        <label class="eu-label" for="payment_status">Payment Status <span class="eu-req">*</span></label>
                        <select id="payment_status" name="payment_status"
                                class="eu-select eu-select--payment @error('payment_status') is-invalid @enderror"
                                required onchange="onPaymentChange(this)">
                            <option value="pending" {{ old('payment_status', $player->payment_status) === 'pending' ? 'selected' : '' }}>
                                ⏳ Pending
                            </option>
                            <option value="paid" {{ old('payment_status', $player->payment_status) === 'paid' ? 'selected' : '' }}>
                                ✅ Paid
                            </option>
                        </select>
                        @error('payment_status')<div class="eu-error">{{ $message }}</div>@enderror
                    </div>
                    {{-- Warning shown only when admin selects Paid --}}
                    <div id="paymentWarning" class="eu-payment-warning" style="display:none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        You are marking this player as <strong>Paid</strong>. This will move them to the paid players list. Make sure this is correct before saving.
                    </div>
                </div>
            </div>
            @endif
            {{-- ─────────────────────────────────────────────────────────── --}}

            {{-- Action buttons --}}
            <div class="eu-actions">
                <a href="javascript:history.back()" class="eu-btn eu-btn--secondary">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="eu-btn eu-btn--primary" id="saveBtn">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
            </div>

        </div>{{-- end right col --}}
    </div>{{-- end eu-grid --}}
</form>

{{-- ════════════════════════════════════════════════════════
     STYLES
════════════════════════════════════════════════════════ --}}
<style>
:root {
    --eu-radius: 12px;
    --eu-shadow: 0 2px 16px rgba(0,0,0,0.07);
    --eu-border: #e2e8f0;
    --eu-text: #0f172a;
    --eu-muted: #64748b;
    --eu-surface: #ffffff;
    --eu-bg: #f4f6fb;
    --eu-primary: #4e73df;
    --eu-success: #1cc88a;
    --eu-danger: #e74a3b;
    --eu-warning: #f59e0b;
}

/* ── Page Header ── */
.eu-page-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px; flex-wrap: wrap; gap: 12px;
}
.eu-page-header-left { display: flex; align-items: center; gap: 14px; min-width: 0; }
.eu-back-btn {
    width: 38px; height: 38px; border-radius: 50%;
    background: var(--eu-surface); border: 1.5px solid var(--eu-border);
    display: flex; align-items: center; justify-content: center;
    color: var(--eu-muted); text-decoration: none; flex-shrink: 0;
    transition: all .18s;
}
.eu-back-btn:hover { background: var(--eu-primary); border-color: var(--eu-primary); color: #fff; }
.eu-page-title { font-size: 1.2rem; font-weight: 800; color: var(--eu-text); margin: 0 0 4px; }
.eu-page-sub { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.eu-sep { color: var(--eu-border); font-weight: 300; }
.eu-pid-badge, .eu-season-badge {
    font-size: .7rem; font-weight: 700;
    background: #eef2ff; color: var(--eu-primary);
    padding: 2px 8px; border-radius: 20px;
    display: inline-flex; align-items: center;
}
.eu-season-badge { background: #f0fdf4; color: #166534; }
.eu-mode-badge { font-size: .7rem; font-weight: 700; padding: 2px 8px; border-radius: 20px; }
.eu-mode-badge--singles { background: #dbeafe; color: #1d4ed8; }
.eu-mode-badge--doubles { background: #ede9fe; color: #6d28d9; }
.eu-status-pill {
    font-size: .78rem; font-weight: 700; padding: 5px 14px; border-radius: 20px;
    display: inline-flex; align-items: center;
}
.eu-status-pill--paid    { background: #d1fae5; color: #059669; }
.eu-status-pill--pending { background: #fef3c7; color: #d97706; }

/* ── Alerts ── */
.eu-alert {
    display: flex; align-items: flex-start; gap: 10px;
    border-radius: var(--eu-radius); padding: 12px 16px;
    margin-bottom: 16px; font-size: .875rem; font-weight: 500;
}
.eu-alert--success { background: #f0fdf4; border: 1.5px solid #bbf7d0; color: #166534; }
.eu-alert--danger  { background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b; }
.eu-alert-close { margin-left: auto; background: none; border: none; cursor: pointer; font-size: 1.1rem; line-height: 1; opacity: .6; }
.eu-alert-close:hover { opacity: 1; }

/* ── Readonly banner ── */
.eu-readonly-banner {
    display: flex; align-items: center; gap: 8px;
    background: #fefce8; border: 1.5px solid #fde68a;
    border-radius: 10px; padding: 10px 16px;
    font-size: .8rem; font-weight: 600; color: #92400e;
    margin-bottom: 20px;
}
.eu-readonly-banner i { color: var(--eu-warning); }

/* ── Grid layout ── */
.eu-grid { display: grid; grid-template-columns: 260px 1fr; gap: 20px; align-items: start; }
.eu-col { display: flex; flex-direction: column; }

/* ── Cards ── */
.eu-card {
    background: var(--eu-surface); border-radius: var(--eu-radius);
    box-shadow: var(--eu-shadow); overflow: hidden; border: 1px solid var(--eu-border);
}
.eu-card--locked  { border-color: #fde68a; }
.eu-card--payment { border-color: #93c5fd; }
.eu-card-header {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px; font-size: .8rem; font-weight: 700; color: var(--eu-text);
    background: #f8fafc; border-bottom: 1px solid var(--eu-border);
}
.eu-card-header i { color: var(--eu-primary); }
.eu-card-header--payment { background: #eff6ff; border-bottom-color: #bfdbfe; }
.eu-card-header--payment i { color: #2563eb; }
.eu-payment-editable-badge {
    margin-left: auto; font-size: .62rem; font-weight: 700;
    background: #dbeafe; color: #1d4ed8;
    padding: 2px 8px; border-radius: 20px; letter-spacing: .03em;
}
.eu-card-body { padding: 16px; }
.mt-3 { margin-top: 14px; }

/* ── Photo section ── */
.eu-photo-section { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 10px; }
.eu-photo-preview {
    width: 100px; height: 100px; border-radius: 50%; overflow: hidden;
    border: 3px solid var(--eu-border); box-shadow: 0 0 0 4px #f1f5f9;
    display: flex; align-items: center; justify-content: center; background: #f8fafc;
}
.eu-photo-preview img { width: 100%; height: 100%; object-fit: cover; object-position: top; }
.eu-photo-placeholder { font-size: 2.5rem; color: #94a3b8; }
.eu-upload-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 7px 16px; border-radius: 8px; font-size: .78rem; font-weight: 700;
    background: var(--eu-primary); color: #fff; cursor: pointer;
    border: none; transition: background .18s; width: 100%; text-align: center;
}
.eu-upload-btn:hover { background: #3a5fc8; }
.eu-upload-btn--gray { background: #64748b; }
.eu-upload-btn--gray:hover { background: #475569; }
.eu-file-input { display: none; }
.eu-upload-hint { font-size: .68rem; color: var(--eu-muted); margin: 0; }
.eu-existing-file {
    display: flex; align-items: center; gap: 8px;
    background: #f8fafc; border: 1px solid var(--eu-border);
    border-radius: 8px; padding: 8px 12px; font-size: .78rem; color: var(--eu-muted);
}
.eu-file-link { color: var(--eu-primary); font-weight: 600; text-decoration: none; }
.eu-file-link:hover { text-decoration: underline; }

/* ── Locked fields ── */
.eu-locked-input {
    display: flex; align-items: center; gap: 8px;
    background: #fefce8; border: 1.5px solid #fde68a;
    border-radius: 8px; padding: 8px 12px;
    font-size: .82rem; font-weight: 700; color: #92400e;
    font-family: 'DM Mono', monospace; letter-spacing: .04em;
}
.eu-locked-input--inline {
    padding: 9px 12px; font-family: inherit;
    letter-spacing: normal; font-weight: 600;
}
.eu-lock-icon { font-size: .7rem; color: var(--eu-warning); flex-shrink: 0; }

/* ── Form fields ── */
.eu-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
.eu-field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
.eu-field:last-child { margin-bottom: 0; }
.eu-label { font-size: .74rem; font-weight: 700; color: var(--eu-muted); text-transform: uppercase; letter-spacing: .05em; }
.eu-req { color: var(--eu-danger); }
.eu-input, .eu-select, .eu-textarea {
    width: 100%; padding: 9px 12px; border-radius: 8px;
    border: 1.5px solid var(--eu-border); font-size: .875rem; color: var(--eu-text);
    background: #fff; transition: border-color .18s, box-shadow .18s;
    outline: none; box-sizing: border-box;
}
.eu-input:focus, .eu-select:focus, .eu-textarea:focus {
    border-color: var(--eu-primary);
    box-shadow: 0 0 0 3px rgba(78,115,223,.15);
}
.eu-input.is-invalid, .eu-select.is-invalid { border-color: var(--eu-danger); }
.eu-textarea { resize: vertical; min-height: 68px; }
.eu-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px;
}
.eu-error { font-size: .72rem; color: var(--eu-danger); font-weight: 600; margin-top: 2px; }

/* ── Payment dropdown ── */
.eu-select--payment {
    border-color: #93c5fd; background-color: #f0f7ff; font-weight: 600;
}
.eu-select--payment:focus {
    border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.15);
}

/* ── Payment notice & warning ── */
.eu-payment-notice {
    font-size: .78rem; color: #1d4ed8;
    background: #eff6ff; border: 1px solid #bfdbfe;
    border-radius: 8px; padding: 9px 12px; margin-bottom: 14px;
    display: flex; align-items: flex-start; gap: 7px;
}
.eu-payment-notice i { margin-top: 1px; flex-shrink: 0; }
.eu-payment-warning {
    font-size: .78rem; color: #92400e;
    background: #fefce8; border: 1.5px solid #fde68a;
    border-radius: 8px; padding: 9px 12px; margin-top: 10px;
    display: flex; align-items: flex-start; gap: 7px;
}
.eu-payment-warning i { color: var(--eu-warning); margin-top: 1px; flex-shrink: 0; }

/* ── Action buttons ── */
.eu-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 16px; flex-wrap: wrap; }
.eu-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 10px 22px; border-radius: 8px; font-size: .85rem; font-weight: 700;
    border: none; cursor: pointer; text-decoration: none; transition: all .18s;
}
.eu-btn--primary   { background: var(--eu-primary); color: #fff; }
.eu-btn--primary:hover { background: #3a5fc8; }
.eu-btn--secondary { background: #f1f5f9; color: var(--eu-text); border: 1.5px solid var(--eu-border); }
.eu-btn--secondary:hover { background: #e2e8f0; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .eu-grid { grid-template-columns: 1fr; }
    .eu-col--wide { order: -1; }
}
@media (max-width: 575px) {
    .eu-row { grid-template-columns: 1fr; gap: 0; }
    .eu-actions { flex-direction: column; }
    .eu-btn { width: 100%; }
}
</style>

{{-- ════════════════════════════════════════════════════════
     SCRIPTS
════════════════════════════════════════════════════════ --}}
<script>
/* ── Photo preview ── */
function previewImage(input, imgId, placeholderId) {
    if (!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function (e) {
        var img = document.getElementById(imgId);
        var ph  = document.getElementById(placeholderId);
        if (!img) {
            img = document.createElement('img');
            img.id = imgId;
            var preview = document.getElementById('photoPreview');
            if (ph) ph.style.display = 'none';
            preview.appendChild(img);
        }
        img.src = e.target.result;
        img.style.display = 'block';
        if (ph) ph.style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}

/* ── Show filename after aadhar file selected ── */
function showFileName(input, labelId) {
    var el = document.getElementById(labelId);
    if (!el) return;
    el.textContent = input.files[0] ? input.files[0].name : 'JPG, PNG, PDF · max 4 MB';
}

/* ── Show/hide payment warning when admin selects Paid ── */
function onPaymentChange(select) {
    var warning = document.getElementById('paymentWarning');
    if (!warning) return;
    warning.style.display = select.value === 'paid' ? 'flex' : 'none';
}

/* ── Save button loading state ── */
document.getElementById('editPlayerForm').addEventListener('submit', function () {
    var btn = document.getElementById('saveBtn');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving…';
});
</script>

@endsection