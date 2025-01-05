<?php

namespace App\Http\Controllers\authenticationsApi;

use App\Models\UserBookmark;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserBookmarkController extends Controller
{
    /**
     * Menambahkan Bookmark
     */
    public function addBookmark(Request $request)
    {
        $userId = Auth::id();
        $houseId = $request->house_id;

        $request->validate([
            'house_id' => 'required|integer|exists:houses,id'
        ]);

        $existingBookmark = UserBookmark::where('user_id', $userId)
                                        ->where('house_id', $houseId)
                                        ->first();

        if ($existingBookmark) {
            return ResponseFormatter::error(null, 'Bookmark already exists', 400);
        }

        $bookmark = UserBookmark::create([
            'user_id' => $userId,
            'house_id' => $houseId,
        ]);

        return ResponseFormatter::success($bookmark, 'Bookmark added successfully');
    }

    /**
     * Menghapus Bookmark (Soft Delete)
     */
    public function deleteBookmark(Request $request)
    {
        $userId = Auth::id();
        $houseId = $request->house_id;

        $request->validate([
            'house_id' => 'required|integer|exists:houses,id'
        ]);

        $bookmark = UserBookmark::where('user_id', $userId)
                                ->where('house_id', $houseId)
                                ->first();

        if (!$bookmark) {
            return ResponseFormatter::error(null, 'Bookmark not found', 404);
        }

        $bookmark->delete();

        return ResponseFormatter::success(null, 'Bookmark deleted successfully');
    }

    /**
     * Menampilkan Daftar Bookmark Pengguna
     */
    public function listBookmarks()
    {
        $userId = Auth::id();

        $bookmarks = UserBookmark::where('user_id', $userId)
                                 ->with('house')
                                 ->get();

        return ResponseFormatter::success($bookmarks, 'List of bookmarks retrieved successfully');
    }
}
