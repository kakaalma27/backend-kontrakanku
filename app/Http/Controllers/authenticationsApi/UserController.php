<?php

namespace App\Http\Controllers\authenticationsApi;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  public function fetch(Request $request)
  {
    return ResponseFormatter::success($request->user(), 'Data profile user berhasil diambil');
  }
  public function editProfile(Request $request)
  {
      $user = Auth::user();
  
      $request->validate([
          'name' => 'required|string|max:255',
          'username' => 'required|string|max:255',
          'email' => 'required|email|max:255',
          'profile_photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      ]);
  
      $data = $request->only(['name', 'username', 'email']);
  
      if ($request->hasFile('profile_photo_path')) {
          // Hapus foto lama jika ada
          if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
              Storage::disk('public')->delete($user->profile_photo_path);
          }
  
          // Simpan foto baru
          $image = $request->file('profile_photo_path');
          $profilePhotoPath = $image->store('profile_photos', 'public');
          
          // Generate URL lengkap
          $data['profile_photo_path'] = Storage::disk('public')->url($profilePhotoPath);

      }
  
      try {
          $user->update($data);
  
          // Generate URL lengkap untuk response
          if (isset($data['profile_photo_path'])) {
              $data['profile_photo_url'] = url($data['profile_photo_path']);
          } else {
              $data['profile_photo_url'] = $user->profile_photo_path 
                  ? url($user->profile_photo_path) 
                  : null;
          }
  
      } catch (\Exception $e) {
          return ResponseFormatter::error($e->getMessage(), 'Update Profile Failed', 500);
      }
  
      return ResponseFormatter::success([
          'user' => $user->fresh(),
          'profile_photo_url' => $data['profile_photo_url']
      ], 'Profile Berhasil diUpdate');
  }
    
  public function login(Request $request)
  {
    try {
      $request->validate([
        'email' => 'required|email',
        'password' => 'required',
      ]);

      $credentials = request(['email', 'password']);
      if (!Auth::attempt($credentials)) {
        return ResponseFormatter::error(['message' => 'Unauthorized'], 'Authentication Failed', 401);
      }

      $user = Auth::user();
      $tokenResult = $user->createToken('authToken')->plainTextToken;

      return ResponseFormatter::success(
        [
          'access_token' => $tokenResult,
          'token_type' => 'Bearer',
          'user' => $user,
        ],
        'Authenticated'
      );
    } catch (Exception $error) {
      Log::error($error->getMessage());
      return ResponseFormatter::error(
        [
          'message' => 'Something went wrong',
          'error' => $error->getMessage(),
        ],
        'Authentication Failed',
        500
      );
    }
  }

  public function register(Request $request)
  {
    try {
      $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'role' => 'required|string|max:50',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
      ]);

      $user = User::create([
        'name' => $request->name,
        'username' => $request->username,
        'role' => $request->role,
        'email' => $request->email,
        'password' => Hash::make($request->password),
      ]);
      $user = User::where('email', $request->email)->first();
      $tokenResult = $user->createToken('authToken')->plainTextToken;

      return ResponseFormatter::success(
        [
          'access_token' => $tokenResult,
          'token_type' => 'Bearer',
          'user' => $user,
        ],
        'User Registered'
      );
    } catch (Exception $error) {
      return ResponseFormatter::error(
        [
          'message' => 'Something went wrong',
          'error' => $error,
        ],
        'Authentication Failed',
        500
      );
    }
  }

  public function logout(Request $request)
  {
    $token = $request
      ->user()
      ->currentAccessToken()
      ->delete();

    return ResponseFormatter::success($token, 'Token Revoked');
  }

  public function checkEmail(Request $request)
  {
      $request->validate([
          'email' => 'required|email|exists:users,email',
      ]);
  
      $token = rand(10000, 99999); // Token berupa kode 5 digit
      $createdAt = now('Asia/Jakarta'); // Gunakan waktu dengan timezone eksplisit
  
      // Update atau buat token baru, pastikan 'created_at' diperbarui
      $passwordReset = PasswordReset::updateOrCreate(
          ['email' => $request->email],
          [
              'token' => $token,
              'is_verified' => false,
              'attempts' => 0,
          ]
      );
  
      // Pastikan 'created_at' diperbarui secara manual jika data sudah ada
      $passwordReset->created_at = $createdAt;
      $passwordReset->save();
  

  
      Mail::raw("Kode verifikasi Anda adalah: $token", function ($message) use ($request) {
          $message->to($request->email)->subject('Kode Verifikasi Reset Password');
      });
  
      return ResponseFormatter::success($token, 'Kode verifikasi telah dikirim ke email Anda.');
  }
  
  
  public function verifyToken(Request $request)
  {
      $request->validate([
          'email' => 'required|email|exists:users,email',
          'token' => 'required|numeric',
      ]);
  
      $passwordReset = PasswordReset::where('email', $request->email)->first();
  
      if (!$passwordReset) {
          return ResponseFormatter::error(null, 'Email tidak ditemukan.', 404);
      }
  
      // Ambil created_at dalam UTC dan konversi ke Asia/Jakarta
      $createdAtUTC = Carbon::createFromFormat('Y-m-d H:i:s', $passwordReset->created_at, 'UTC');
      $createdAtJakarta = $createdAtUTC->copy()->setTimezone('Asia/Jakarta');
      $expiryTime = $createdAtJakarta->copy()->addMinutes(10);
      $currentTime = now('Asia/Jakarta');
  

  
      // Validasi token
      if ($passwordReset->token != $request->token) {
          $passwordReset->increment('attempts');
          return ResponseFormatter::error(null, 'Kode verifikasi salah.', 400);
      }
  
      // Validasi kedaluwarsa
      if ($expiryTime->isPast()) {
          return ResponseFormatter::error(null, 'Token kedaluwarsa.', 400);
      }
  
      $passwordReset->is_verified = true;
      $passwordReset->save();
  
      return ResponseFormatter::success(null, 'Kode verifikasi benar. Anda dapat melanjutkan reset password.');
  }
  

  /**
   * Tahap 3: Reset Password
   */
  public function resetPassword(Request $request)
  {
    $request->validate([
      'email' => 'required|email|exists:users,email',
      'password' => 'required|string|min:8|confirmed',
    ]);

    $passwordReset = PasswordReset::where('email', $request->email)
      ->where('is_verified', true)
      ->first();

    if (!$passwordReset) {
      return ResponseFormatter::error(null, 'Token belum diverifikasi.', 400);
    }

    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();

    // Hapus token reset password
    $passwordReset->delete();

    return ResponseFormatter::success(null, 'Password berhasil diperbarui.');
  }
}
