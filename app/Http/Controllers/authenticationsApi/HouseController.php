<?php
namespace App\Http\Controllers\authenticationsApi;

use App\Models\User;
use App\Models\house;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\address;
use App\Models\houseImage;
use Illuminate\Support\Facades\Storage;

class HouseController extends Controller
{
  // public function allOwner(Request $request)
  // {
  //   $owner_id = auth()->id();
  //   $id = $request->input('id');
  //   $name = $request->input('name');
  //   $limit = $request->input('limit', 10);
  //   $price_from = $request->input('price_from');
  //   $price_to = $request->input('price_to');
  //   $description = $request->input('description');
  //   $tags = $request->input('tags');
  //   $kamar = $request->input('kamar');
  //   $wc = $request->input('wc');
  //   $available = $request->input('available');
  //   $quantity = $request->input('quantity');
  //   $user_id = $request->input('user_id', $owner_id);

  //   if ($id) {
  //     $house = house::with(['addresses' => function ($query) use ($user_id) {
  //       $query->where('user_id', $user_id);
  //   }])->where('user_id', $user_id)->find($id);
  //     if ($house) {
  //       return ResponseFormatter::success($house, 'Data kontrakan berhasil diambil');
  //     } else {
  //       return ResponseFormatter::error(null, 'Data kontrakan tidak ada', 404);
  //     }
  //   }

  //   $houseQuery = house::with(['addresses' => function ($query) use ($user_id) {
  //     $query->where('user_id', $user_id);
  // }]);

  // $houseQuery->where('user_id', $user_id); // Filter utama berdasarkan user_id

  //   if ($name) {
  //     $houseQuery->where('name', 'like', '%' . $name . '%');
  //   }
  //   if ($price_from && $price_to) {
  //     $houseQuery->whereBetween('price', [$price_from, $price_to]);
  //   }
  //   if ($description) {
  //     $houseQuery->where('description', 'like', '%' . $description . '%');
  //   }
  //   if ($tags) {
  //     $houseQuery->where('tags', 'like', '%' . $tags . '%');
  //   }
  //   if ($kamar) {
  //     $houseQuery->where('kamar', 'like', '%' . $kamar . '%');
  //   }
  //   if ($quantity) {
  //     $houseQuery->where('quantity', 'like', '%' . $quantity . '%');
  //   }
  //   if ($wc) {
  //     $houseQuery->where('wc', 'like', '%' . $wc . '%');
  //   }
  //   if ($available) {
  //     $houseQuery->where('available', $available); // Assuming available is a boolean
  //   }
  //   if ($user_id) {
  //     $houseQuery->where('user_id', $user_id);
  //   }

  //   return ResponseFormatter::success($houseQuery->paginate($limit), 'Data kontrakan berhasil diambil');
  // }
  public function allOwner(Request $request)
  {
      $id = $request->input('id');
      $name = $request->input('name');
      $limit = $request->input('limit', 10);
      $price_from = $request->input('price_from');
      $price_to = $request->input('price_to');
      $description = $request->input('description');
      $tags = $request->input('tags');
      $kamar = $request->input('kamar');
      $wc = $request->input('wc');
      $available = $request->input('available');
      $quantity = $request->input('quantity');
      $user_id = $request->input('user_id');
  
      if ($id) {
          $house = house::with(['houseImage', 'addresses'])->find($id);
  
          if ($house) {
              return ResponseFormatter::success($house, 'Data kontrakan berhasil diambil');
          } else {
              return ResponseFormatter::error(null, 'Data kontrakan tidak ada', 404);
          }
      }
  
      $owner_id = auth()->id();
  
      $houseQuery = house::with(['houseImage', 'addresses']);
  
      $houseQuery->where('user_id', $owner_id);
  
      if ($name) {
          $houseQuery->where('name', 'like', '%' . $name . '%');
      }
      if ($price_from && $price_to) {
          $houseQuery->whereBetween('price', [$price_from, $price_to]);
      }
      if ($description) {
          $houseQuery->where('description', 'like', '%' . $description . '%');
      }
      if ($tags) {
          $houseQuery->where('tags', 'like', '%' . $tags . '%');
      }
      if ($kamar) {
          $houseQuery->where('kamar', 'like', '%' . $kamar . '%');
      }
      if ($quantity) {
          $houseQuery->where('quantity', 'like', '%' . $quantity . '%');
      }
      if ($wc) {
          $houseQuery->where('wc', 'like', '%' . $wc . '%');
      }
      if ($available) {
          $houseQuery->where('available', $available); // Assuming available is a boolean
      }
      if ($user_id) {
          $houseQuery->where('user_id', $user_id);
      }
  
      return ResponseFormatter::success($houseQuery->paginate($limit), 'Data kontrakan berhasil diambil');
  }
  public function all(Request $request)
  {
    $id = $request->input('id');
    $name = $request->input('name');
    $limit = $request->input('limit', 10);
    $price_from = $request->input('price_from');
    $price_to = $request->input('price_to');
    $description = $request->input('description');
    $tags = $request->input('tags');
    $kamar = $request->input('kamar');
    $wc = $request->input('wc');
    $available = $request->input('available');
    $quantity = $request->input('quantity');
    $user_id = $request->input('user_id');

    if ($id) {
      $house = house::with(['houseImage', 'addresses'])->find($id);

      if ($house) {
        return ResponseFormatter::success($house, 'Data kontrakan berhasil diambil');
      } else {
        return ResponseFormatter::error(null, 'Data kontrakan tidak ada', 404);
      }
    }

    $owner_id = auth()->id();
    $houseQuery = house::with(['houseImage', 'addresses']);

    if ($name) {
      $houseQuery->where('name', 'like', '%' . $name . '%');
    }
    if ($price_from && $price_to) {
      $houseQuery->whereBetween('price', [$price_from, $price_to]);
    }
    if ($description) {
      $houseQuery->where('description', 'like', '%' . $description . '%');
    }
    if ($tags) {
      $houseQuery->where('tags', 'like', '%' . $tags . '%');
    }
    if ($kamar) {
      $houseQuery->where('kamar', 'like', '%' . $kamar . '%');
    }
    if ($quantity) {
      $houseQuery->where('quantity', 'like', '%' . $quantity . '%');
    }
    if ($wc) {
      $houseQuery->where('wc', 'like', '%' . $wc . '%');
    }
    if ($available) {
      $houseQuery->where('available', $available); // Assuming available is a boolean
    }
    if ($user_id) {
      $houseQuery->where('user_id', $user_id);
    }

    return ResponseFormatter::success($houseQuery->paginate($limit), 'Data kontrakan berhasil diambil');
  }
  public function store(Request $request)
  {
      try {
          $request->validate([
              'name' => 'required|string|max:255',
              'price' => 'required|numeric',
              'description' => 'nullable|string',
              'tags' => 'nullable|string',
              'kamar' => 'nullable|integer',
              'wc' => 'nullable|integer',
              'quantity' => 'nullable|integer',
              'available' => 'required|boolean',
              'images' => 'required|array',
              'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
          ]);
  
          // Validasi User
          $user_id = auth()->id();
          $user = User::findOrFail($user_id);
          if (!$user || !in_array($user->role, [1, 2])) {
              return ResponseFormatter::error(null, 'Opps, Hanya Pemilik Kontrakan!', 403);
          }
  
          // Validasi Address
          $address = Address::where('user_id', $user_id)->first();
          if (!$address) {
              return ResponseFormatter::error(null, 'Opps, Tambah Alamat terlebih dahulu!', 403);
          }
  
          // Upload Images
          $imageUrls = [];
          if ($request->hasFile('images')) {
              foreach ($request->file('images') as $image) {
                  $path = $image->store('images', 'public'); // Simpan di storage/public/images
                  $imgUrl = Storage::disk('public')->url($path); // Ambil URL gambar
                  $imageUrls[] = $imgUrl; // Simpan URL ke array
              }
          }
  
          // Buat data House
          $house = House::create([
              'name' => $request->name,
              'price' => $request->price,
              'description' => $request->description,
              'tags' => $request->tags,
              'kamar' => $request->kamar,
              'wc' => $request->wc,
              'quantity' => $request->quantity,
              'available' => $request->available,
              'user_id' => $user_id,
              'address_id' => $address->id,
          ]);
  
          // Simpan data gambar ke houseImage
          foreach ($imageUrls as $imageUrl) {
              houseImage::create([
                  'path' => $imageUrl,
                  'house_id' => $house->id, // Hubungkan dengan house ID
              ]);
          }
  
          return ResponseFormatter::success($house->load('houseImage'), 'Data rumah berhasil disimpan');
      } catch (\Exception $e) {
          \Log::error('Error storing house: ' . $e->getMessage(), [
              'request' => $request->all(),
              'user_id' => auth()->id(),
          ]);
  
          return ResponseFormatter::error(null, [
              'message' => 'Terjadi kesalahan saat menyimpan data rumah',
              'error' => $e->getMessage(),
              'trace' => $e->getTraceAsString(),
          ], 500);
      }
  }
  
  public function update(Request $request, $id)
  {
      try {
          $request->validate([
              'name' => 'sometimes|string|max:255',
              'price' => 'sometimes|numeric',
              'description' => 'nullable|string',
              'tags' => 'nullable|string',
              'kamar' => 'nullable|integer',
              'wc' => 'nullable|integer',
              'quantity' => 'nullable|integer',
              'available' => 'sometimes|boolean',
              'images' => 'nullable|array',
              'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
          ]);
  
          $user_id = auth()->id();
          $user = User::findOrFail($user_id);
          if (!$user || !in_array($user->role, [1, 2])) {
              return ResponseFormatter::error(null, 'Opps, Hanya Pemilik Kontrakan!', 403);
          }
  
          $house = House::findOrFail($id);
  
          $address = Address::where('user_id', $user_id)->first();
          if (!$address) {
              return ResponseFormatter::error(null, 'Opps, Tambah Alamat terlebih dahulu!', 403);
          }
  
          $house->update([
              'name' => $request->has('name') ? $request->name : $house->name,
              'price' => $request->has('price') ? $request->price : $house->price,
              'description' => $request->has('description') ? $request->description : $house->description,
              'tags' => $request->has('tags') ? $request->tags : $house->tags,
              'kamar' => $request->has('kamar') ? $request->kamar : $house->kamar,
              'wc' => $request->has('wc') ? $request->wc : $house->wc,
              'quantity' => $request->has('quantity') ? $request->quantity : $house->quantity,
              'available' => $request->has('available') ? $request->available : $house->available,
              'address_id' => $address->id,
          ]);
  
          // Handle image uploads
          if ($request->hasFile('images')) {
              // Get existing images
              $existingImages = houseImage::where('house_id', $house->id)->get();
  
              // Store new images
              $imageUrls = [];
              foreach ($request->file('images') as $image) {
                  $path = $image->store('images', 'public');
                  $imgUrl = Storage::disk('public')->url($path);
                  $imageUrls[] = $imgUrl;
              }
  
              // Save new images to the database
              foreach ($imageUrls as $imageUrl) {
                  houseImage::create([
                      'path' => $imageUrl,
                      'house_id' => $house->id, // Link to the house ID
                  ]);
              }
          }
  
          return ResponseFormatter::success($house->load('houseImage'), 'Data rumah berhasil diupdate');
      } catch (\Exception $e) {
          \Log::error('Error updating house: ' . $e->getMessage(), [
              'request' => $request->all(),
              'user_id' => auth()->id(),
          ]);
  
          return ResponseFormatter::error(null, [
              'message' => 'Terjadi kesalahan saat mengupdate data rumah',
              'error' => $e->getMessage(),
              'trace' => $e->getTraceAsString(),
          ], 500);
      }
  }
  public function deleteImage($house_id, $image_id)
  {
      try {
          $user = auth('sanctum')->user();
          $house = House::findOrFail($house_id);
  
          if ($house->user_id !== $user->id) {
              return ResponseFormatter::error(null, 'Anda tidak memiliki izin untuk menghapus gambar dari rumah ini', 403);
          }
  
          $image = $house->houseImage()->findOrFail($image_id);
  
          Storage::disk('public')->delete($image->path);
  
          $image->delete();
  
          return ResponseFormatter::success(null, 'Gambar berhasil dihapus');
      } catch (\Exception $e) {
          return ResponseFormatter::error(null, 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage(), 500);
      }
  }


  public function destroy($id)
  {
    $house = house::findOrFail($id);
    $house->delete();

    return ResponseFormatter::success(null, 'Data rumah berhasil dihapus', 204);
  }
}