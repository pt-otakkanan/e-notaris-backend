<?php
// app/Http/Controllers/Api/Auth/GoogleAuthController.php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /** =======================
     *  MODE 1: OAUTH Redirect
     *  ======================= */

    /**
     * FE panggil: GET /api/auth/google/redirect?role_id=2|3
     * role_id akan disimpan di state (dienkripsi) untuk dipakai saat callback.
     */
    public function redirect(Request $request)
    {
        // Validasi role_id dari FE (opsional, default 2 jika tidak valid)
        $roleId = (int) $request->query('role_id', 2);
        if (!in_array($roleId, [2, 3], true)) {
            $roleId = 2;
        }

        $statePayload = [
            'role_id' => $roleId,
            'ts'      => time(),
        ];
        $state = Crypt::encryptString(json_encode($statePayload));

        // Stateless untuk API; tambahkan scopes seperlunya
        return Socialite::driver('google')
            ->stateless()
            ->with(['state' => $state])
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Google redirect ke sini -> balas JSON + Sanctum token
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Ambil desired role dari state terenkripsi (jika ada)
            $desiredRoleId = 2;
            if ($request->filled('state')) {
                try {
                    $decoded = json_decode(Crypt::decryptString($request->input('state')), true);
                    if (isset($decoded['role_id']) && in_array((int)$decoded['role_id'], [2, 3], true)) {
                        $desiredRoleId = (int)$decoded['role_id'];
                    }
                } catch (\Throwable $e) {
                    // state rusak → fallback 2
                    $desiredRoleId = 2;
                }
            }

            [$user, $roleIgnored] = $this->findOrCreateUserFromGoogle($googleUser, $desiredRoleId);

            // login dan buat Sanctum token
            Auth::login($user);
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login Google berhasil',
                'data'    => [
                    'token'         => $token,
                    'user'          => $user,
                    'role_requested' => $desiredRoleId,
                    'role_applied'  => (int)$user->role_id,
                    'role_ignored'  => $roleIgnored, // true jika FE minta ganti role tapi user lama sudah punya role berbeda
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

    /** =======================
     *  MODE 2: Token-based (One Tap / Credential API)
     *  ======================= */
    // FE kirim: { id_token: "...", role_id: 2|3 }
    public function tokenLogin(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id_token' => 'required|string',
            'role_id'  => 'nullable|integer|in:2,3',
        ]);

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'data'    => $v->errors(),
            ], 422);
        }

        try {
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->id_token);

            $desiredRoleId = (int) ($request->input('role_id', 2));
            if (!in_array($desiredRoleId, [2, 3], true)) {
                $desiredRoleId = 2;
            }

            [$user, $roleIgnored] = $this->findOrCreateUserFromGoogle($googleUser, $desiredRoleId);

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login Google berhasil',
                'data'    => [
                    'token'         => $token,
                    'user'          => $user,
                    'role_requested' => $desiredRoleId,
                    'role_applied'  => (int)$user->role_id,
                    'role_ignored'  => $roleIgnored,
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google token tidak valid',
                'error'   => $e->getMessage(),
            ], 400);
        }
    }

    /** ==============
     *  PARTNER DRIVER (opsional, sama seperti sebelumnya)
     *  ============== */

    public function redirectPartner()
    {
        // untuk partner tidak butuh role_id; ini contoh saja
        return Socialite::driver('google_partner')->stateless()->redirect();
    }

    public function callbackPartner(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google_partner')->stateless()->user();

            $authUser = Auth::user(); // butuh bearer token dari klien jika mau "link partner ke user"
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harus login sebagai user terlebih dahulu untuk menautkan partner.',
                ], 401);
            }

            $partner = Partner::firstOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'user_id'           => $authUser->id,
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'email_verified_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Partner Google ditautkan',
                'data'    => $partner,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google partner auth gagal',
                'error'   => $e->getMessage(),
            ], 400);
        }
    }

    public function tokenLoginPartner(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);
        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'data'    => $v->errors(),
            ], 422);
        }

        try {
            $authUser = Auth::user();
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harus login sebagai user terlebih dahulu untuk menautkan partner.',
                ], 401);
            }

            $googleUser = Socialite::driver('google_partner')->stateless()->userFromToken($request->id_token);

            $partner = Partner::firstOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'user_id'           => $authUser->id,
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'email_verified_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Partner Google ditautkan (token)',
                'data'    => $partner,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google partner token tidak valid',
                'error'   => $e->getMessage(),
            ], 400);
        }
    }

    /** =======================
     *  Helper: buat/temukan user dari Google + terapkan role
     *  ======================= */
    private function findOrCreateUserFromGoogle($googleUser, int $desiredRoleId): array
    {
        // Prioritas: cari by google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        $roleIgnored = false;

        if (!$user) {
            // Kedua: link by email (kalau sudah ada)
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Link akun lama → TIDAK mengubah role_id yang sudah ada
                $user->google_id = $googleUser->getId();
                if (empty($user->email_verified_at)) {
                    $user->email_verified_at = now();
                }
                $user->save();

                // Jika FE minta role berbeda tapi user lama sudah punya role, tandai di respons
                if ((int)$user->role_id !== (int)$desiredRoleId) {
                    $roleIgnored = true;
                }
            } else {
                // Buat user baru → role_id pakai yang diminta FE (2|3), default 2 jika invalid
                if (!in_array($desiredRoleId, [2, 3], true)) {
                    $desiredRoleId = 2;
                }

                $user = User::create([
                    'role_id'           => $desiredRoleId,
                    'name'              => $googleUser->getName() ?? 'User',
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'password'          => null, // social login
                ]);
            }
        } else {
            // Sudah punya google_id; hormati role yang ada
            if ((int)$user->role_id !== (int)$desiredRoleId) {
                $roleIgnored = true;
            }
        }

        return [$user, $roleIgnored];
    }
}
