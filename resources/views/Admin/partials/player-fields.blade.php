{{-- 
    Reusable player fields partial.
    Variables:
      $prefix      — field name prefix e.g. '' / 'player1_' / 'player2_'
      $states      — collection of states from DB
      $old_prefix  — same as $prefix, used for old() helper
--}}

<div class="rp-grid">

    {{-- Name --}}
    <div class="rp-field rp-full">
        <label>Full Name <span>*</span></label>
        <input type="text" name="{{ $prefix }}name" value="{{ old($old_prefix . 'name') }}" placeholder="Enter full name"
            style="{{ $errors->has($old_prefix . 'name') ? 'border-color:#ef4444;' : '' }}">
        @error($old_prefix . 'name') <p class="rp-err">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div class="rp-field">
        <label>Email <span>*</span></label>
        <input type="email" name="{{ $prefix }}email" value="{{ old($old_prefix . 'email') }}" placeholder="Enter email"
            style="{{ $errors->has($old_prefix . 'email') ? 'border-color:#ef4444;' : '' }}">
        @error($old_prefix . 'email') <p class="rp-err">{{ $message }}</p> @enderror
    </div>

    {{-- Phone --}}
    <div class="rp-field">
        <label>Phone <span>*</span></label>
        <input type="tel" name="{{ $prefix }}phone" value="{{ old($old_prefix . 'phone') }}" placeholder="10-digit mobile number" maxlength="10"
            style="{{ $errors->has($old_prefix . 'phone') ? 'border-color:#ef4444;' : '' }}">
        @error($old_prefix . 'phone') <p class="rp-err">{{ $message }}</p> @enderror
    </div>

    {{-- State --}}
    <div class="rp-field">
        <label>State <span>*</span></label>
        <select name="{{ $prefix }}state_id" ...>
    <option value="">Select State</option>
    @foreach($states->filter(fn($s) => in_array(trim($s->name), ['Andhra Pradesh', 'Telangana'])) as $state)
        <option value="{{ $state->id }}" {{ old($old_prefix . 'state_id') == $state->id ? 'selected' : '' }}>
            {{ $state->name }}
        </option>
    @endforeach
</select>
    </div>

    {{-- Age --}}
    <div class="rp-field">
        <label>Age Category <span>*</span></label>
        <select name="{{ $prefix }}age" style="{{ $errors->has($old_prefix . 'age') ? 'border-color:#ef4444;' : '' }}">
            <option value="">Select Age</option>
            @foreach(['U-11','U-13','U-15','U-19'] as $age)
                <option value="{{ $age }}" {{ old($old_prefix . 'age') === $age ? 'selected' : '' }}>{{ $age }}</option>
            @endforeach
        </select>
        @error($old_prefix . 'age') <p class="rp-err">{{ $message }}</p> @enderror
    </div>

    {{-- Sport --}}
    <div class="rp-field">
        <label>Sport <span>*</span></label>
        <select name="{{ $prefix }}sport" style="{{ $errors->has($old_prefix . 'sport') ? 'border-color:#ef4444;' : '' }}">
            <option value="">Select Sport</option>
            <option value="badminton" {{ old($old_prefix . 'sport') === 'badminton' ? 'selected' : '' }}>Badminton</option>
        </select>
        @error($old_prefix . 'sport') <p class="rp-err">{{ $message }}</p> @enderror
    </div>

    {{-- Gender --}}
    <div class="rp-field">
        <label>Gender <span>*</span></label>
        <select name="{{ $prefix }}gender" style="{{ $errors->has($old_prefix . 'gender') ? 'border-color:#ef4444;' : '' }}">
            <option value="">Select Gender</option>
           <option value="Male" {{ old($old_prefix . 'gender', 'Male') === 'Male' ? 'selected' : '' }}>Male</option>
        </select>
        @error($old_prefix . 'gender') <p class="rp-err">{{ $message }}</p> @enderror
    </div>

    {{-- T-Shirt Size --}}
    <div class="rp-field">
        <label>T-Shirt Size <span>*</span></label>
        <select name="{{ $prefix }}tshirt_size" style="{{ $errors->has($old_prefix . 'tshirt_size') ? 'border-color:#ef4444;' : '' }}">
            <option value="">Select Size</option>
            @foreach(['S','M','L','XL','XXL'] as $size)
                <option value="{{ $size }}" {{ old($old_prefix . 'tshirt_size') === $size ? 'selected' : '' }}>{{ $size }}</option>
            @endforeach
        </select>
        @error($old_prefix . 'tshirt_size') <p class="rp-err">{{ $message }}</p> @enderror
    </div>

    {{-- Address --}}
    <div class="rp-field rp-full">
        <label>Address / City <span>*</span></label>
        <textarea name="{{ $prefix }}address" placeholder="Enter city or address"
            style="{{ $errors->has($old_prefix . 'address') ? 'border-color:#ef4444;' : '' }}">{{ old($old_prefix . 'address') }}</textarea>
        @error($old_prefix . 'address') <p class="rp-err">{{ $message }}</p> @enderror
    </div>

    {{-- Aadhaar --}}
    <div class="rp-field">
        <label>Aadhaar Proof <small style="color:#9ca3af;font-weight:400;">(JPG/PNG/PDF, max 5MB)</small></label>
        <input type="file" name="{{ $prefix }}aadhar_proof" accept=".jpg,.jpeg,.png,.pdf">
        @error($old_prefix . 'aadhar_proof') <p class="rp-err">{{ $message }}</p> @enderror
    </div>

    {{-- Photo --}}
    <div class="rp-field">
        <label>Profile Photo <small style="color:#9ca3af;font-weight:400;">(JPG/PNG, max 5MB)</small></label>
        <input type="file" name="{{ $prefix }}profile_photo" accept=".jpg,.jpeg,.png">
        @error($old_prefix . 'profile_photo') <p class="rp-err">{{ $message }}</p> @enderror
    </div>

</div>