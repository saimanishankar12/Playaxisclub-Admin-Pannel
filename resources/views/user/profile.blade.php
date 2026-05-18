@extends('user.layouts.app')

@section('title', 'My Profile')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-circle mr-2 text-primary"></i>My Profile
    </h1>
    <a href="{{ route('user-dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i>Back to Dashboard
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <strong>Please fix the errors below:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="row">

    {{-- ══ LEFT: Read-only info ══════════════════════════════════════════ --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-id-card mr-2"></i>Player Details
                </h6>
            </div>
            <div class="card-body">

                {{-- Profile Photo --}}
                <div class="text-center mb-4">
                    @if(!empty($player->profile_photo))
                        <img id="profilePreview"
                             src="{{ asset('storage/' . $player->profile_photo) }}"
                             alt="Profile Photo"
                             class="rounded-circle shadow"
                             style="width:110px;height:110px;object-fit:cover;border:4px solid #4e73df;">
                    @else
                        <div id="profilePreview"
                             class="rounded-circle d-inline-flex align-items-center justify-content-center shadow"
                             style="width:110px;height:110px;background:#eaf0fb;border:4px solid #e3e6f0;">
                            <i class="fas fa-user fa-3x" style="color:#4e73df;opacity:0.4;"></i>
                        </div>
                    @endif
                    <h5 class="font-weight-bold mt-3 mb-0 text-gray-800">{{ $player->name ?? '—' }}</h5>
                    <small class="text-muted">{{ $player->player_id ?? '' }}</small>
                </div>

                <hr>

                {{-- Read-only fields --}}
                <table class="table table-borderless table-sm" style="font-size:0.85rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted font-weight-bold" style="width:105px;">Player ID</td>
                            <td>
                                <span class="badge badge-primary px-2 py-1" style="font-size:0.8rem;">
                                    {{ $player->player_id ?? '—' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Season ID</td>
                            <td>
                                <span class="badge badge-secondary px-2 py-1" style="font-size:0.8rem;">
                                    {{ $player->season_id ?? '—' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Name</td>
                            <td class="font-weight-bold text-gray-800">{{ $player->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Sport</td>
                            <td>{{ ucfirst($player->sport ?? '—') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Mode</td>
                            <td>{{ ucfirst($player->mode ?? '—') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Gender</td>
                            <td>{{ ucfirst($player->gender ?? '—') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Age</td>
                            <td>{{ $player->age ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">T-Shirt</td>
                            <td>{{ $player->tshirt_size ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">City</td>
                             <td>{{ $player->address ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">State</td>
                            <td>{{ $player->state->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Joined</td>
                            <td class="text-muted small">
                                {{ isset($player->created_at) ? $player->created_at->format('d M Y') : '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- Aadhar proof --}}
                @if(!empty($player->aadhar_proof))
                    <hr>
                    <p class="text-muted small font-weight-bold mb-2">
                        <i class="fas fa-id-card mr-1"></i>Current Aadhar Proof
                    </p>
                    @php $ext = pathinfo($player->aadhar_proof, PATHINFO_EXTENSION); @endphp
                    @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp']))
                        <img src="{{ asset('storage/' . $player->aadhar_proof) }}"
                             alt="Aadhar" class="img-fluid rounded shadow-sm"
                             style="max-height:120px;object-fit:cover;">
                    @else
                        <a href="{{ asset('storage/' . $player->aadhar_proof) }}"
                           target="_blank" class="btn btn-sm btn-outline-secondary btn-block">
                            <i class="fas fa-file-pdf mr-1"></i>View Aadhar PDF
                        </a>
                    @endif
                @endif

            </div>
        </div>
    </div>

    {{-- ══ RIGHT: Editable form ══════════════════════════════════════════ --}}
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-edit mr-2"></i>Update Profile
                    <small class="text-muted font-weight-normal ml-2">
                        (you can update phone, email, and photos)
                    </small>
                </h6>
            </div>
            <div class="card-body">

                <form action="{{ route('user-profile.update') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        {{-- Phone --}}
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-700 small">
                                <i class="fas fa-phone mr-1 text-primary"></i>Phone Number
                            </label>
                            <input type="text"
                                   name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $player->phone) }}"
                                   placeholder="Enter phone number">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-700 small">
                                <i class="fas fa-envelope mr-1 text-primary"></i>Email Address
                            </label>
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $player->email) }}"
                                   placeholder="Enter email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Changing email will require you to use the new email at next login.
                            </small>
                        </div>

                    </div>

                    <hr>

                    <div class="row">

                        {{-- Profile Photo --}}
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-700 small">
                                <i class="fas fa-camera mr-1 text-primary"></i>Profile Photo
                            </label>
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input @error('profile_photo') is-invalid @enderror"
                                       id="profilePhotoInput"
                                       name="profile_photo"
                                       accept="image/jpeg,image/png,image/webp">
                                <label class="custom-file-label" for="profilePhotoInput">
                                    Choose photo...
                                </label>
                                @error('profile_photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">JPG, PNG or WEBP · Max 2 MB</small>

                            {{-- Live preview --}}
                            <div class="mt-2" id="photoPreviewWrap" style="display:none;">
                                <p class="text-muted small mb-1">Preview:</p>
                                <img id="photoPreviewImg"
                                     src=""
                                     alt="Preview"
                                     class="rounded shadow-sm"
                                     style="height:80px;width:80px;object-fit:cover;border:2px solid #4e73df;">
                            </div>
                        </div>

                        {{-- Aadhar Proof --}}
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-700 small">
                                <i class="fas fa-id-card mr-1 text-primary"></i>Aadhar Proof
                            </label>
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input @error('aadhar_proof') is-invalid @enderror"
                                       id="aadharInput"
                                       name="aadhar_proof"
                                       accept="image/jpeg,image/png,image/webp,application/pdf">
                                <label class="custom-file-label" for="aadharInput">
                                    Choose file...
                                </label>
                                @error('aadhar_proof')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">JPG, PNG, WEBP or PDF · Max 4 MB</small>

                            {{-- Aadhar preview --}}
                            <div class="mt-2" id="aadharPreviewWrap" style="display:none;">
                                <p class="text-muted small mb-1">Preview:</p>
                                <img id="aadharPreviewImg"
                                     src=""
                                     alt="Aadhar Preview"
                                     class="rounded shadow-sm"
                                     style="height:80px;object-fit:cover;border:2px solid #e3e6f0;">
                            </div>
                        </div>

                    </div>

                    <hr>

                    <div class="d-flex align-items-center">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                        <a href="{{ route('user-dashboard') }}" class="btn btn-outline-secondary ml-2">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div>

{{-- ══ Scripts ══════════════════════════════════════════════════════════ --}}
<script>
// Custom file label + image preview
document.getElementById('profilePhotoInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    document.querySelector('label[for="profilePhotoInput"]').textContent = file.name;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('photoPreviewImg').src = e.target.result;
        document.getElementById('photoPreviewWrap').style.display = 'block';
    };
    reader.readAsDataURL(file);
});

document.getElementById('aadharInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    document.querySelector('label[for="aadharInput"]').textContent = file.name;
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('aadharPreviewImg').src = e.target.result;
            document.getElementById('aadharPreviewWrap').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>

@endsection