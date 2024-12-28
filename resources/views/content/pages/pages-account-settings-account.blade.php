@extends('layouts/contentNavbarLayout')

@section('title', 'Account settings - Account')

@section('page-script')
<script src="{{ asset('assets/js/pages-account-settings-account.js') }}"></script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Account Settings /</span> Account
</h4>

<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-md-row mb-4 gap-2 gap-lg-0">
      <li class="nav-item"><a class="nav-link active" href=" javascript:void(0);"><i class="mdi mdi-account-outline mdi-20px me-1"></i>Account</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ url('pages/account-settings-notifications') }}"><i class="mdi mdi-bell-outline mdi-20px me-1"></i>Notifications</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ url('pages/account-settings-connections') }}"><i class="mdi mdi-link mdi-20px me-1"></i>Connections</a></li>
    </ul>
    <div class="card mb-4">
      <h4 class="card-header">Profile Details</h4>
      <div class="card-body">
        <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
          @csrf
          <div class="d-flex align-items-start align-items-sm-center gap-4">
            @if($user->profile->image)
              <img src="{{ asset('storage/' . $user->profile->image) }}" alt="user-avatar" class="d-block w-px-120 h-px-120 rounded" id="uploadedAvatar" />
            @else
                <img src="{{ asset('assets/img/icons/misc/aviato.png') }}" alt="default-avatar" class="d-block w-px-120 h-px-120 rounded" id="uploadedAvatar" />
            @endif              <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
                <span class="d-none d-sm-block">Upload new photo</span>
                <i class="mdi mdi-tray-arrow-up d-block d-sm-none"></i>
                <input type="file" id="upload" name="image" class="account-file-input" hidden accept="image/png, image/jpeg" />
              </label>
              <button type="button" class="btn btn-outline-danger account-image-reset mb-3">
                <i class="mdi mdi-reload d-block d-sm-none"></i>
                <span class="d-none d-sm-block">Reset</span>
              </button>
              <div class="text-muted small">Allowed JPG, GIF or PNG. Max size of 800K</div>
            </div>
          </div>
          <div class="row mt-2 gy-4">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input class="form-control" type="text" id="firstName" name="firstName" value="{{ $user->profile->first_name ?? '' }}" required autofocus />
                <label for="firstName">First Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input class="form-control" type="text" name="lastName" id="lastName" value="{{ $user->profile->last_name ?? '' }}" required />
                <label for="lastName">Last Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required />
                <label for="email">E-mail</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="organization" name="organization" value="{{ $user->profile->organization ?? '' }}" />
                <label for="organization">Organization</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" value="{{ $user->profile->phone_number ?? '' }}" />
                  <label for="phoneNumber">Phone Number</label>
                </div>
                <span class="input-group-text">US (+62)</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input class="form-control" type="text" id="address" name="address" value="{{ $user->profile->address ?? '' }}" />
                <label for="address">Address</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input class="form-control" type="text" id="state" name="state" value="{{ $user->profile->state ?? '' }}" />
                <label for ="state">State</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input class="form-control" type="text" id="zipCode" name="zipCode" value="{{ $user->profile->zip_code ?? '' }}" maxlength="6" />
                <label for="zipCode">Zip Code</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select id="country" class="select2 form-select" name="country">
                  <option value="">Select</option>
                  <option value="Country1" {{ ($user->profile->country == 'Country1') ? 'selected' : '' }}>Country1</option>
                  <option value="Country2" {{ ($user->profile->country == 'Country2') ? 'selected' : '' }}>Country2</option>
                  <!-- Add more countries as needed -->
                </select>
                <label for="country">Country</label>
              </div>
            </div>
          </div>
          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2">Save changes</button>
            <button type="reset" class="btn btn-outline-secondary">Reset</button>
          </div>
        </form>
      </div>
      <div class="card-body pt-2 mt-1">
        <form id="formAccountDeactivation" method="POST" action="{{ route('profile.delete') }}">
          @csrf
          <div class="form-check mb-3 ms-3">
            <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation" required />
            <label class="form-check-label" for="accountActivation">I confirm my account deactivation</label>
          </div>
          <button type="submit" class="btn btn-danger">Deactivate Account</button>
        </form>
      </div>
    </div>
    <div class="card">
      <h5 class="card-header fw-normal">Delete Account</h5>
      <div class="card-body">
        <div class="mb-3 col-12 mb-0">
          <div class="alert alert-warning">
            <h6 class="alert-heading mb-1">Are you sure you want to delete your account?</h6>
            <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
          </div>
        </div>
        <form id="formAccountDeactivation" method="POST" action="{{ route('profile.delete') }}">
          @csrf
          <div class="form-check mb-3 ms-3">
            <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation" required />
            <label class="form-check-label" for="accountActivation">I confirm my account deactivation</label>
          </div>
          <button type="submit" class="btn btn-danger">Deactivate Account</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection