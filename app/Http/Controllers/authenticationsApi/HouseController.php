<?php
namespace App\Http\Controllers\authenticationsApi;

use App\Models\User;
use App\Models\house;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class HouseController extends Controller
{
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
      $user = User::find($user_id);
      if (!$user || !in_array($user->role, [1, 2])) {
          return ResponseFormatter::error(null, 'Opps, kamu tidak memiliki izin', 403);
      }
  
      $imageUrls = [];
      foreach ($request->file('images') as $image) {
          $path = $image->store('images', 'public'); 
          $imageUrls[] = Storage::url($path); 
      }
  
      $house = house::create([
          'url' => $imageUrls, 
          'name' => $request->name,
          'price' => $request->price,
          'description' => $request->description,
          'tags' => $request->tags,
          'kamar' => $request->kamar,
          'wc' => $request->wc,
          'quantity' => $request->quantity,
          'available' => $request->available,
          'user_id' => $user_id,
      ]);
  
      return ResponseFormatter::success($house, 'Data rumah berhasil disimpan');
  }

  public function update(Request $request, $id)
  {
    // Validasi input
    $request->validate([
      'name' => 'required|string|max:255',
      'price' => 'required|numeric',
      'description' => 'nullable|string',
      'tags' => 'nullable|string',
      'kamar' => 'nullable|integer',
      'wc' => 'nullable|integer',
      'quantity' => 'nullable|integer',
      'available' => 'required|boolean',
      'user_id' => 'required|exists:users,id',
    ]);

    // Temukan rumah berdasarkan ID
    $house = house::findOrFail($id);
    $house->update($request->all());

    return ResponseFormatter::success($house, 'Data rumah berhasil diperbarui');
  }

  public function destroy($id)
  {
    $house = house::findOrFail($id);
    $house->delete();

    return ResponseFormatter::success(null, 'Data rumah berhasil dihapus', 204);
  }
}
