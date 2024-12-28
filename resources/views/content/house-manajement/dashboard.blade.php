@extends('layouts/contentNavbarLayout')

@section('title', 'Kontrakan - Management')

@section('content')
<div class="content-wrapper">
  <div class="row g-6 mb-4">
      <!-- Your existing cards here -->
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="row align-items-center">
      <div class="col-md-8 d-flex justify-content-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCenter" onclick="clearModal()">
          + ADD NEW HOUSE
        </button>
      </div>
    </div>

    <div class="table-responsive text-nowrap">
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Deskripsi</th>
            <th>Kamar</th>
            <th>Wc</th>
            <th>Gambar</th>
            <th>Price</th>
            <th>Available</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @foreach($houses as $house)
          <tr>
            <td>{{ $house->name }}</td>
            <td>{{ $house->description }}</td>
            <td>{{ $house->num_bedrooms }}</td>
            <td>{{ $house->num_bathrooms }}</td>
            <td>
              @foreach($house->images as $image)
                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="showImage('{{ asset('storage/' . $image->images) }}')">lihat gambar</a>
                @if (!$loop->last) | @endif <!-- Menambahkan pemisah jika ada lebih dari satu gambar -->
              @endforeach
            </td>
            <td>{{ $house->price }}</td>
            <td>{{ $house->available ? 'Yes' : 'No' }}</td>
            <td>
              <button class="btn btn-warning" onclick="editHouse({{ $house->id }})">Edit</button>
              <form action="{{ route('rent_houses.destroy', $house->id) }}" method="POST" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
              </form>
          </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modalCenterTitle">Add New House</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('rent_houses.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
          <div class="row">
            <div class="col mb-4 mt-2">
              <div class="form-floating form-floating-outline">
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Name" required>
                <label for="name">Name</label>
              </div>
            </div>
          </div>
          <div class="row g-2">
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <input type="text" id="description" name="description" class="form-control" placeholder="Description" required>
                <label for="description">Description</label>
              </div>
            </div>
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <input type="number" id="num_bedrooms" name="num_bedrooms" class="form-control" placeholder="Number of Bedrooms" required>
                <label for="num_bedrooms">Number of Bedrooms</label>
              </div>
            </div>
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <input type="number" id="num_bathrooms" name="num_bathrooms" class="form-control" placeholder="Number of Bathrooms" required>
                <label for="num_bathrooms">Number of Bathrooms</label>
              </div>
            </div>
          </div>
          <div class="row g-2">
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <input type="number" id="price" name="price" class="form-control" placeholder="Price" required>
                <label for="price">Price</label>
              </div>
            </div>
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <input type="file" id="images" name="images[]" class="form-control" placeholder="Upload Images" multiple required>
                <label for="images">Upload Images</label>
              </div>
            </div>
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <select id="available" name="available" class="form-select" required>
                  <option value="1">Yes</option>
                  <option value="0">No</option>
                </select>
                <label for="available">Available</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal untuk menampilkan gambar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">House Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" alt="House Image" class="img-fluid">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal untuk menambahkan dan mengedit rumah -->
<div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modalCenterTitle">Add New House</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="houseForm" method="POST" action="{{ route('rent_houses.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
          <div class="row">
            <div class="col mb-4 mt-2">
              <div class="form-floating form-floating-outline">
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Name" required>
                <label for="name">Name</label>
              </div>
            </div>
          </div>
          <div class="row g-2">
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <input type="text" id="description" name="description" class="form-control" placeholder="Description" required>
                <label for="description">Description</label>
              </div>
            </div>
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <input type="number" id="num_bedrooms" name="num_bedrooms" class="form-control" placeholder="Number of Bedrooms" required>
                <label for="num_bedrooms">Number of Bedrooms</label>
              </div>
            </div>
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <input type="number" id="num_bathrooms" name="num_bathrooms" class="form-control" placeholder="Number of Bathrooms" required>
                <label for="num_bathrooms">Number of Bathrooms</label>
              </div>
            </div>
          </div>
          <div class="row g-2">
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <input type="number" id="price" name="price" class="form-control" placeholder="Price" required>
                <label for="price">Price</label>
              </div>
            </div>
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <input type="file" id="images" name="images[]" class="form-control" placeholder="Upload Images" multiple>
                <label for="images">Upload Images (optional)</label>
              </div>
            </div>
            <div class="col mb-2">
              <div class="form-floating form-floating-outline">
                <select id="available" name="available" class="form-select" required>
                  <option value="1">Yes</option>
                  <option value="0">No</option>
                </select>
                <label for="available">Available</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  function clearModal() {
    document.getElementById('houseForm').reset();
    document.getElementById('modalCenterTitle').innerText = 'Add New House';
    document.getElementById('houseForm').action = '{{ route('rent_houses.store') }}';
  }

  function showImage(src) {
    document.getElementById('modalImage').src = src;
  }

  function editHouse(id) {
    fetch(`/rent_houses/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description;
            document.getElementById('num_bedrooms').value = data.num_bedrooms;
            document.getElementById('num_bathrooms').value = data.num_bathrooms;
            document.getElementById('price').value = data.price;
            document.getElementById('available').value = data.available ? '1' : '0';
            document.getElementById('modalCenterTitle').innerText = 'Edit House';
            document.getElementById('houseForm').action = `/rent_houses/${id}`;
            $('#modalCenter').modal('show'); // Tampilkan modal
        });
}
</script>
@endsection