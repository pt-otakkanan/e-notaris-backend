<?php
// app/Http/Controllers/GoogleAuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Crypt;

class GoogleAuthController extends Controller
{
    /**
     * ======== MODE 2 (Utama): Token-based via access_token) ========
     * FE kirim: { access_token: "...", role_id?: 2|3 }
     */
    public function tokenLogin(Request $request)
    {
        $v = Validator::make(
            $request->all(),
            [
                'access_token' => 'required|string',
                'role_id'      => 'nullable|integer|in:2,3',
            ],
            [
                'access_token.required' => 'Token Google wajib dikirim.',
                'access_token.string'   => 'Token Google harus berupa teks (string).',
                'role_id.integer'       => 'Peran (role_id) harus berupa angka.',
                'role_id.in'            => 'Peran yang dipilih tidak valid. Gunakan 2 untuk Klien atau 3 untuk Notaris.',
            ]
        );

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $v->errors(),
            ], 422);
        }

        try {
            // Socialite menerima access token (OAuth 2.0)
            $googleUser = Socialite::driver('google')->stateless()
                ->userFromToken($request->access_token);

            // Coba temukan user berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();
                if ($user && empty($user->google_id)) {
                    // Link-kan akun lama ke google_id
                    $user->google_id = $googleUser->getId();
                    if (empty($user->email_verified_at)) {
                        $user->email_verified_at = now();
                    }
                    $user->save();
                }
            }

            if (!$user) {
                // User belum ada → butuh role
                $roleId = (int) $request->input('role_id', 0);
                if (!in_array($roleId, [2, 3], true)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun google belum terdaftar. Silahkan beralih ke halaman daftar untuk mendaftar dan memilih peran.',
                        'code'    => 'ROLE_REQUIRED',
                    ], 409);
                }

                // Buat user baru beserta role
                $user = User::create([
                    'role_id'           => $roleId,         // 2=Klien, 3=Notaris
                    'name'              => $googleUser->getName() ?? 'User',
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'password'          => null, // social login only
                ]);
            }

            // Login & token Sanctum
            Auth::login($user);
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login Google berhasil',
                'data'    => [
                    'token'        => $token,
                    'user'         => $user,
                    'role_applied' => (int) $user->role_id,
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google auth gagal',
                'error'   => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * (Opsional) MODE 1: Redirect klasik — tetap kompatibel
     */
    public function redirect(Request $request)
    {
        // Kita tidak pakai role di sini; 1 tombol saja
        $statePayload = [
            'ts' => time(),
        ];
        $state = Crypt::encryptString(json_encode($statePayload));

        return Socialite::driver('google')
            ->stateless()
            ->with(['state' => $state])
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Cari user
            $user = User::where('google_id', $googleUser->getId())->first()
                ?: User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Redirect alur ini tidak punya UI role picker, jadi beri pesan agar FE pakai tokenLogin
                return response()->json([
                    'success' => false,
                    'message' => 'Akun belum terdaftar. Gunakan endpoint token-login dan kirim role_id.',
                    'code'    => 'ROLE_REQUIRED',
                ], 409);
            }

            Auth::login($user);
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login Google berhasil',
                'data'    => [
                    'token'        => $token,
                    'user'         => $user,
                    'role_applied' => (int) $user->role_id,
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google auth gagal',
                'error'   => $e->getMessage(),
            ], 400);
        }
    }
}
