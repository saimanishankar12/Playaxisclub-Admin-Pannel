<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Audience — Tournament</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0a0c10;
            --surface:   #111318;
            --border:    #1e2230;
            --accent:    #f5a623;
            --accent2:   #e8403a;
            --text:      #e8eaf0;
            --muted:     #6b7280;
            --input-bg:  #161923;
            --radius:    10px;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Decorative background grid */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(245,166,35,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(245,166,35,0.03) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        /* Glow blob */
        body::after {
            content: '';
            position: fixed;
            top: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(245,166,35,0.08) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 560px;
        }

        /* Header badge */
        .badge-live {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(232,64,58,0.15);
            border: 1px solid rgba(232,64,58,0.4);
            color: var(--accent2);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 100px;
            margin-bottom: 18px;
        }

        .badge-live span {
            width: 6px;
            height: 6px;
            background: var(--accent2);
            border-radius: 50%;
            animation: pulse 1.4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.4; transform: scale(0.7); }
        }

        h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(40px, 10vw, 62px);
            letter-spacing: 2px;
            line-height: 1;
            margin-bottom: 6px;
            color: #fff;
        }

        h1 em {
            font-style: normal;
            color: var(--accent);
        }

        .subtitle {
            color: var(--muted);
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 36px;
            line-height: 1.6;
        }

        /* Card */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 36px 32px;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent2), var(--accent));
        }

        /* Stagger animation */
        .field-group {
            animation: slideUp 0.4s ease both;
        }
        .field-group:nth-child(1) { animation-delay: 0.05s; }
        .field-group:nth-child(2) { animation-delay: 0.10s; }
        .field-group:nth-child(3) { animation-delay: 0.15s; }
        .field-group:nth-child(4) { animation-delay: 0.20s; }
        .field-group:nth-child(5) { animation-delay: 0.25s; }
        .field-group:nth-child(6) { animation-delay: 0.30s; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }

        label .req { color: var(--accent2); margin-left: 2px; }

        input, select, textarea {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            padding: 12px 16px;
            border-radius: var(--radius);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            -webkit-appearance: none;
        }

        input::placeholder { color: var(--muted); }

        input:focus, select:focus, textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(245,166,35,0.12);
        }

        select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
        }

        select option { background: #1a1e2e; }

        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .field-group {
            margin-bottom: 20px;
        }

        /* Error states */
        .is-error input, .is-error select { border-color: var(--accent2); }

        .error-msg {
            font-size: 12px;
            color: var(--accent2);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Alert boxes */
        .alert {
            padding: 14px 16px;
            border-radius: var(--radius);
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .alert-success { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #4ade80; }
        .alert-danger  { background: rgba(232,64,58,0.1);  border: 1px solid rgba(232,64,58,0.3);  color: #f87171; }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--accent) 0%, #e8940a 100%);
            color: #0a0c10;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 20px;
            letter-spacing: 2px;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            margin-top: 8px;
            transition: transform 0.15s, box-shadow 0.15s;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(245,166,35,0.35);
        }

        .btn-submit:hover::after { background: rgba(255,255,255,0.08); }
        .btn-submit:active { transform: translateY(0); }

        /* Divider */
        .divider {
            height: 1px;
            background: var(--border);
            margin: 24px 0;
        }

        /* Footer note */
        .footer-note {
            text-align: center;
            font-size: 12px;
            color: var(--muted);
            margin-top: 24px;
            line-height: 1.6;
        }

        /* Lucky draw teaser */
        .lucky-draw-teaser {
            background: linear-gradient(135deg, rgba(245,166,35,0.08), rgba(232,64,58,0.05));
            border: 1px solid rgba(245,166,35,0.2);
            border-radius: var(--radius);
            padding: 14px 16px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: var(--text);
        }

        .lucky-draw-teaser .icon {
            font-size: 24px;
            flex-shrink: 0;
        }

        .lucky-draw-teaser strong { color: var(--accent); }

        /* Success screen */
        .success-screen {
            display: none;
            text-align: center;
            padding: 16px 0;
        }

        .success-screen .trophy {
            font-size: 64px;
            display: block;
            margin-bottom: 16px;
            animation: bounceIn 0.6s ease both;
        }

        @keyframes bounceIn {
            0%   { transform: scale(0.3); opacity: 0; }
            60%  { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        .ticket-id {
            display: inline-block;
            background: var(--input-bg);
            border: 1px dashed var(--accent);
            color: var(--accent);
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            letter-spacing: 4px;
            padding: 10px 24px;
            border-radius: var(--radius);
            margin: 16px 0;
        }

        @media (max-width: 480px) {
            .card { padding: 24px 20px; }
            .row-2 { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="badge-live"><span></span> Registration Open</div>

    <h1>Join the <em>Action</em></h1>
    <p class="subtitle">Register as audience for the tournament and get a chance to win in the Lucky Draw!</p>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            <span>✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            <span>✕</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="card">

        <div class="lucky-draw-teaser">
            <span class="icon">🏆</span>
            <span>Every registered audience member gets a <strong>unique ID</strong> and is eligible for the <strong>Lucky Draw</strong> each match day!</span>
        </div>

        <form action="{{ route('audience.register.store') }}" method="POST" id="regForm" novalidate>
            @csrf

            <div class="field-group {{ $errors->has('name') ? 'is-error' : '' }}">
                <label>Full Name <span class="req">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="e.g. Rahul Sharma" required autocomplete="name">
                @error('name') <div class="error-msg">⚠ {{ $message }}</div> @enderror
            </div>

            <div class="row-2">
                <div class="field-group {{ $errors->has('phone') ? 'is-error' : '' }}">
                    <label>Phone <span class="req">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           placeholder="10-digit number" maxlength="15" required autocomplete="tel">
                    @error('phone') <div class="error-msg">⚠ {{ $message }}</div> @enderror
                </div>

                <div class="field-group {{ $errors->has('age') ? 'is-error' : '' }}">
                    <label>Age <span class="req">*</span></label>
                    <input type="number" name="age" value="{{ old('age') }}"
                           placeholder="e.g. 28" min="1" max="120" required>
                    @error('age') <div class="error-msg">⚠ {{ $message }}</div> @enderror
                </div>
            </div>

            <div class="field-group {{ $errors->has('email') ? 'is-error' : '' }}">
                <label>Email <span style="color:var(--muted); font-weight:400; text-transform:none; letter-spacing:0;">(optional)</span></label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="you@example.com" autocomplete="email">
                @error('email') <div class="error-msg">⚠ {{ $message }}</div> @enderror
            </div>

            <div class="field-group {{ $errors->has('city') ? 'is-error' : '' }}">
                <label>City <span class="req">*</span></label>
                <input type="text" name="city" value="{{ old('city') }}"
                       placeholder="e.g. Hyderabad" required autocomplete="address-level2">
                @error('city') <div class="error-msg">⚠ {{ $message }}</div> @enderror
            </div>

            <div class="field-group {{ $errors->has('tournament_season_id') ? 'is-error' : '' }}">
                <label>Tournament Season <span class="req">*</span></label>
                <select name="tournament_season_id" required>
                    <option value="">Select a Season</option>
                    @foreach($tournaments as $t)
                        @foreach($t->seasons as $s)
                            <option value="{{ $s->id }}"
                                {{ old('tournament_season_id', $seasonId ?? '') == $s->id ? 'selected' : '' }}>
                                {{ $t->name }} — {{ $s->label ?? 'Season '.$s->season_number }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
                @error('tournament_season_id') <div class="error-msg">⚠ {{ $message }}</div> @enderror
            </div>

            <div class="divider"></div>

            <button type="submit" class="btn-submit">Register Now →</button>

        </form>
    </div>

    <p class="footer-note">
        Your information is only used for tournament participation and lucky draw purposes.<br>
        By registering you agree to the event terms and conditions.
    </p>

</div>

</body>
</html>