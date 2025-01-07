<?php

namespace App\Http\Controllers\authenticationsApi;

use Illuminate\Http\Request;
use App\Models\addressCategory;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class AddressCategoryController extends Controller
{
    // Store new address category
    public function store(Request $request)
    {
        $request->validate([
            'utama' => 'required|string',
            'kontrakan' => 'required|string',
        ]);

        $addressCategory = addressCategory::create([
            'user_id' => auth()->id(),
            'utama' => $request->utama,
            'phone' => $request->kontrakan,
        ]);
    
        return ResponseFormatter::success($addressCategory, 'Alamat kategori berhasil ditambahkan');
    }

    // Get all address categories for the authenticated user
    public function index()
    {
        $addressCategories = addressCategory::where('user_id', auth()->id())->get();

        return ResponseFormatter::success($addressCategories, 'Data kategori alamat berhasil diambil');
    }

    // Show a single address category by id
    public function show($id)
    {
        $addressCategory = addressCategory::where('user_id', auth()->id())->find($id);

        if (!$addressCategory) {
            return ResponseFormatter::error('Kategori alamat tidak ditemukan', 404);
        }

        return ResponseFormatter::success($addressCategory, 'Data kategori alamat berhasil diambil');
    }

    // Update address category
    public function update(Request $request, $id)
    {
        $request->validate([
            'utama' => 'required|string',
            'kontrakan' => 'required|string',
        ]);

        $addressCategory = addressCategory::where('user_id', auth()->id())->find($id);

        if (!$addressCategory) {
            return ResponseFormatter::error('Kategori alamat tidak ditemukan', 404);
        }

        $addressCategory->update([
            'user_id' => auth()->id(), 
            'utama' => $request->utama,
            'phone' => $request->kontrakan,
        ]);

        return ResponseFormatter::success($addressCategory, 'Kategori alamat berhasil diperbarui');
    }

    // Delete address category
    public function destroy($id)
    {
        $addressCategory = addressCategory::where('user_id', auth()->id())->find($id);

        if (!$addressCategory) {
            return ResponseFormatter::error('Kategori alamat tidak ditemukan', 404);
        }

        $addressCategory->delete();

        return ResponseFormatter::success(null, 'Kategori alamat berhasil dihapus');
    }
}