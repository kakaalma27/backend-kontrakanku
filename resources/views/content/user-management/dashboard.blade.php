@extends('layouts/contentNavbarLayout')

@section('title', 'User - Management')

@section('content')
    <div class="content-wrapper">
        <div class="row g-6 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-1">
                                <p class="text-heading mb-1">User</p>
                                <div class="d-flex align-items-center">
                                    <h4 class="mb-1 me-2">{{ $totalUsers }}</h4>
                                    <p class="text-success mb-1">100%</p>
                                </div>
                                <small class="mb-6">Total User</small>
                            </div>
                            <div class="avatar">
                                <div class="avatar-initial bg-label-primary rounded-3">
                                    <i class="ri-user-line"></i>
                                    <i class="mdi mdi-account-outline mdi-24px text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-1">
                                <p class="text-heading mb-1">Verified Users</p>
                                <div class="d-flex align-items-center">
                                    <h4 class="mb-1 me-2">{{$verifiedCount}}</h4>
                                    <p class="text-success mb-1">{{ number_format($verifiedPercentage, 2) }}%</p>
                                </div>
                                <small class="mb-6">Total User</small>
                            </div>
                            <div class="avatar">
                                <div class="avatar-initial bg-label-success rounded-3">
                                    <i class="mdi mdi-account-check-outline mdi-24px text-success "></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-1">
                                <p class="text-heading mb-1">Duplicate Users</p>
                                <div class="d-flex align-items-center">
                                    <h4 class="mb-1 me-2">0</h4>
                                    <p class="text-success mb-1">0.00%</p>
                                </div>
                                <small class="mb-6">Total User</small>
                            </div>
                            <div class="avatar">
                                <div class="avatar-initial bg-label-danger rounded-3">
                                    <i class="mdi mdi-account-multiple-outline mdi-24px text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-1">
                                <p class="text-heading mb-1">Verification Pending</p>
                                <div class="d-flex align-items-center">
                                    <h4 class="mb-1 me-2">{{$notVerifiedCount}}</h4>
                                    <p class="text-success mb-1">{{ number_format($notVerifiedPercentage, 2) }}%</p>
                                </div>
                                <small class="mb-6">Total User</small>
                            </div>
                            <div class="avatar">
                                <div class="avatar-initial bg-label-warning rounded-3">
                                    <i class="mdi mdi-account-alert-outline mdi-24px text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <span class="fs-5">Search Filter</span>
            <div class="row align-items-center">
                <div class="col-md-4">
                    <form action="{{ route('user-management') }}" method="GET">
                        <input type="text" class="form-control mt-3 mb-1" id="defaultFormControlInput" name="search" placeholder="Search" value="{{ request('search') }}" aria-describedby="defaultFormControlHelp" />
                    </form>
                </div>
                <div class="col-md-8 d-flex justify-content-end">
                        <button type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBackdropAddUser" aria-controls="offcanvasBackdropAddUser" class="btn btn-primary mx-2">+ Add New User</button>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Export
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="{{ route('user.export.copy') }}">Copy</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.export.excel') }}">Excel</a></li>
                                <li><a class="dropdown-item" href ="{{ route('user.export.pdf') }}">PDF</a></li>
                            </ul>
                        </div>
                </div>
            </div>
        </div>
        <div class="table-responsive" style="overflow-y: auto; max-height: 400px;">
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th class="text-truncate">ID</th>
                        <th class="text-truncate">User </th>
                        <th class="text-truncate">Email</th>
                        <th class="text-truncate">VERIFIED</th>
                        <th class="text-truncate">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getUser  as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-truncate">{{ $user->username }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td class="text-truncate">{{ $user->email }}</td>
                            <td class="text-truncate">
                                @if($user->verifications->isNotEmpty() && $user->verifications->first()->is_verified)
                                    <i class="mdi mdi-shield-check mdi-24px text-primary"></i>
                                @else
                                    <i class="mdi mdi-alert-circle mdi-24px text-danger"></i>
                                @endif
                            </td>
                            <td class="text-truncate">
                                <button type="button" 
                                data-bs-toggle="offcanvas" 
                                data-bs-target="#offcanvasBackdropEditUser" 
                                aria-controls="offcanvasBackdropEditUser" 
                                class="btn btn-sm btn-icon edit-user btn-text-secondary rounded-pill waves-effect" 
                                data-user-id="{{ $user->id }}"
                                data-username="{{ $user->username }}"
                                data-email="{{ $user->email }}"
                                data-password="{{ $user->password }}"
                                data-role="{{ $user->role }}">
                            <i class="mdi mdi-square-edit-outline fs-4"></i>
                        </button>
                                <button class="btn btn-sm btn-icon delete-user btn-text-secondary rounded-pill waves-effect" data-user-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#deleteUser Modal">
                                    <i class="mdi mdi mdi-delete-outline fs-4"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $getUser ->links() }} 
        </div>
    </div>
<div class="row">
    <div class="col-md-4">
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBackdropAddUser" aria-labelledby="offcanvasBackdropLabelAddUser">
            <div class="offcanvas-header">
                <h5 id="offcanvasBackdropLabelAddUser " class="offcanvas-title">Add User</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form action="{{ route('user.add') }}" method="POST">
                    @csrf
                    <div class="form-floating form-floating-outline mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" autofocus>
                        <label for="username">Username</label>
                      </div>
                    <div class="form-floating form-floating-outline mb-3">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" autofocus>
                        <label for="email">Email</label>
                    </div>
                    <div class="form-floating form-floating-outline mb-3">
                        <input type="text" class="form-control" id="password" name="password" placeholder="Password" autofocus>
                        <label for="password">Password</label>
                    </div>
                    <div class="mb-3">
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="0">User </option>
                            <option value="1">Owner</option>
                            <option value="2">Super Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBackdropEditUser" aria-labelledby="offcanvasBackdropLabelEditUser">
            <div class="offcanvas-header">
                <h5 id="offcanvasBackdropLabelEditUser" class="offcanvas-title">Edit User</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form id="editUser Form" action="{{ route('user.edit', ['id' => $user->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-floating form-floating-outline mb-3">
                        <input type="text" class="form-control" id="editUsername" name="username" placeholder="Enter your username" autofocus>
                        <label for="editUsername">Username</label>
                    </div>
                    <div class="form-floating form-floating-outline mb-3">
                        <input type="text" class="form-control" id="editEmail" name="email" placeholder="Enter your email" required>
                        <label for="editEmail">Email</label>
                    </div>
                    <div class="form-floating form-floating-outline mb-3">
                        <input type="text" class="form-control" id="editPassword" name="password" placeholder="Enter new password (leave blank to keep current)" autofocus>
                        <label for="editPassword">Password</label>
                    </div>
                    <div class="mb-3">
                        <select class="form-select" name="role" required>
                            <option id="editRole" value="">Select Role</option>
                            <option value="0">User </option>
                            <option value="1">Owner</option>
                            <option value="2">Super Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteUser Modal" tabindex="-1" aria-labelledby="deleteUser ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUser ModalLabel">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form id="deleteUser Form" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let deleteButtons = document.querySelectorAll('.delete-user');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                let userId = this.getAttribute('data-user-id');
                let deleteForm = document.getElementById('deleteUser Form');
                deleteForm.action = `/user-management/delete/${userId}`;
                let modal = new bootstrap.Modal(document.getElementById('deleteUser Modal'));
                modal.show();
            });
        });

        let editButtons = document.querySelectorAll('.edit-user');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                let userId = this.getAttribute('data-user-id');
                let username = this.getAttribute('data-username');
                let email = this.getAttribute('data-email');
                let role = this.getAttribute('data-role');

                document.getElementById('editUsername').value = username;
                document.getElementById('editEmail').value = email;
                document.getElementById('editPassword').value;
                document.getElementById('editRole').value = role;

                let editForm = document.getElementById('editUser Form');
                editForm.action = `/user-management/edit/${userId}`; 
                let editModal = new bootstrap.Offcanvas(document.getElementById('offcanvasBackdropEditUser')); // Pastikan ID tidak memiliki spasi
                editModal.show();
            });
        });
    });
</script>
@endsection
