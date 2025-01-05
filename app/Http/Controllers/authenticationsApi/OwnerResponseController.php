<?php

namespace App\Http\Controllers\authenticationsApi;

use Illuminate\Http\Request;
use App\Models\ownerResponse;
use App\Models\userComplaint;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
class OwnerResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'complaint_id' => 'required|exists:user_complaints,id',
            'response' => 'required|string',
        ]);

        try {
            $response = OwnerResponse::create([
                'user_id' => auth()->id(), // Jika menggunakan autentikasi
                'complaint_id' => $validated['complaint_id'],
                'response' => $validated['response'],
            ]);

            $complaint = UserComplaint::find($validated['complaint_id']);
            $complaint->update([
                'owner_response' => $validated['response'],
                'status' => 'resolved', // Jika ingin langsung mengubah status
            ]);

            return ResponseFormatter::success($response, 'Respons pemilik berhasil disimpan');
        } catch (\Exception $e) {
            return ResponseFormatter::error(null, 'Terjadi kesalahan saat menyimpan respons pemilik', 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(ownerResponse $ownerResponse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ownerResponse $ownerResponse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ownerResponse $ownerResponse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ownerResponse $ownerResponse)
    {
        //
    }
}
