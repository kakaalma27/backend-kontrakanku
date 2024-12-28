<?php
namespace App\Http\Controllers\authenticationsApi;

use App\Models\transaction;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
  public function all(Request $request)
  {
    $id = $request->input('id');
    $name = $request->input('name');
    $limit = $request->input('limit', 10); // Default limit to 10 if not provided

    if ($id) {
      $transactions = transaction::with(['house'])->find($id);

      if ($transactions) {
        return ResponseFormatter::success($house, 'Data transaksi berhasil diambil');
      } else {
        return ResponseFormatter::error(null, 'Data transaksi tidak ada', 404);
      }
    }
    $transactions = transaction::with(['house'])->where('user_id', Auth::user());
  }
}
