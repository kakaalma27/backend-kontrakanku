<?php
namespace App\Http\Controllers\authenticationsApi;

use App\Models\User;
use App\Models\house;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\address;
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
      $house = house::with(['addresses'])->find($id);

      if ($house) {
        return ResponseFormatter::success($house, 'Data kontrakan berhasil diambil');
      } else {
        return ResponseFormatter::error(null, 'Data kontrakan tidak ada', 404);
      }
    }

    $houseQuery = house::with(['addresses']);

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
  
          $user_id = auth()->id();
          $user = User::findOrFail($user_id);
          if (!$user || !in_array($user->role, [1, 2])) {
              return ResponseFormatter::error(null, 'Opps, Hanya Pemilik Kontrakan!', 403);
          }
          $address = Address::where('user_id', $user_id)->first();
          if (!$address) {
              return ResponseFormatter::error(null, 'Opps, Tambah Alamat terlebih dahulu!', 403);
          } 
          $imageUrls = [];
          if ($request->hasFile('images')) {
              foreach ($request->file('images') as $image) {
                  $path = $image->store('images', 'public'); 
                  $imgUrl = Storage::disk('public')->url($path); 
                  $imageUrls[] = $imgUrl; 
              }
          }
  
          $house = house::create([
              'path' => json_encode($imageUrls),
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
          return ResponseFormatter::success($house, 'Data rumah berhasil disimpan');
      } catch (\Exception $e) {
          \Log::error('Error storing house: ' . $e->getMessage(), [
              'request' => $request->all(),
              'user_id' => auth()->id(),
          ]);
  
          // Menampilkan pesan kesalahan yang lebih rinci
          return ResponseFormatter::error(null, [
              'message' => 'Terjadi kesalahan saat menyimpan data rumah',
              'error' => $e->getMessage(), // Menyertakan pesan kesalahan
              'trace' => $e->getTraceAsString(), // Menyertakan stack trace
          ], 500);
      }
  }
  public function update(Request $request, $id)
  {
      $house = house::findOrFail($id);
  
      $request->validate([
          'name' => 'required|string|max:255',
          'price' => 'required|numeric',
          'description' => 'nullable|string',
          'tags' => 'nullable|string',
          'kamar' => 'nullable|integer',
          'wc' => 'nullable|integer',
          'quantity' => 'nullable|integer',
          'available' => 'required|boolean',
          'images' => 'nullable|array',
          'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
          'remove_images' => 'nullable|array',
          'remove_images.*' => 'nullable|string', 
      ]);
  
      $existingImages = json_decode($house->path, true) ?? [];
      if ($request->filled('remove_images')) {
          foreach ($request->remove_images as $imageToRemove) {
              if (($key = array_search($imageToRemove, $existingImages)) !== false) {
                  $filePath = str_replace(Storage::disk('public')->url(''), '', $imageToRemove);
                  Storage::disk('public')->delete($filePath);
  
                  unset($existingImages[$key]);
              }
          }
      }
  
      if ($request->hasFile('images')) {
          foreach ($request->file('images') as $image) {
              $path = $image->store('images', 'public');
              $imgUrl = Storage::disk('public')->url($path);
              $existingImages[] = $imgUrl;
          }
      }
  
      $house->update([
          'name' => $request->name,
          'price' => $request->price,
          'description' => $request->description,
          'tags' => $request->tags,
          'kamar' => $request->kamar,
          'wc' => $request->wc,
          'quantity' => $request->quantity,
          'available' => $request->available,
          'path' => json_encode(array_values($existingImages)), // Simpan gambar terbaru
      ]);
  
      return ResponseFormatter::success($house, 'Data rumah berhasil diperbarui');
  }
  

  public function destroy($id)
  {
    $house = house::findOrFail($id);
    $house->delete();

    return ResponseFormatter::success(null, 'Data rumah berhasil dihapus', 204);
  }
}