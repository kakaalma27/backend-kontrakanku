<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\houseImage;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class HouseImageController extends Controller
{
  // 🔹 POST: Upload Gambar Rumah
  public function store(Request $request)
  {
    // Validate the request
    $request->validate([
      'house_id' => 'required|exists:houses,id',
      'images' => 'required|array',
      'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Ensures image files
    ]);

    $uploadedImages = [];
    foreach ($request->file('images') as $image) {
      // Store image in the 'houses' directory
      $imagePath = $image->store('houses', 'public');

      // Create a new house image record
      $uploadedImages[] = houseImage::create([
        'house_id' => $request->input('house_id'),
        'url' => Storage::url($imagePath), // Get URL of the stored image
      ]);
    }

    return ResponseFormatter::success($uploadedImages, 'Semua gambar berhasil diunggah');
  }

  // 🔹 GET: Detail Gambar Rumah
  public function show($id)
  {
    $image = houseImage::find($id);

    if ($image) {
      return ResponseFormatter::success($image, 'Detail gambar rumah berhasil diambil');
    } else {
      return ResponseFormatter::error(null, 'Gambar rumah tidak ditemukan', 404);
    }
  }

  // 🔹 PUT: Update Gambar Rumah
  public function update(Request $request, $id)
  {
    $request->validate([
      'house_id' => 'required|exists:houses,id',
      'url' => 'required|url', // URL validation
    ]);

    $image = houseImage::find($id);

    if ($image) {
      $image->update([
        'house_id' => $request->input('house_id'),
        'url' => $request->input('url'),
      ]);

      return ResponseFormatter::success($image, 'Gambar rumah berhasil diperbarui');
    } else {
      return ResponseFormatter::error(null, 'Gambar rumah tidak ditemukan', 404);
    }
  }

  // 🔹 DELETE: Hapus Gambar Rumah
  public function destroy($id)
  {
    $image = houseImage::find($id);

    if ($image) {
      // Delete the physical file from storage
      Storage::delete(str_replace('/storage/', '', $image->url));
      $image->delete();

      return ResponseFormatter::success(null, 'Gambar rumah berhasil dihapus');
    } else {
      return ResponseFormatter::error(null, 'Gambar rumah tidak ditemukan', 404);
    }
  }
}
