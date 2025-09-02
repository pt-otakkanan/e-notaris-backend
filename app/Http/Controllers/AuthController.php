<?php

namespace App\Http\Controllers;

use App\Mail\AuthMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function checkUser(Request $request)
    {
        $user = $request->user(); // otomatis dari token

        return response()->json([
            'success' => true,
            'message' => 'User ditemukan dari token',
            'data'    => [
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'role_id' => $user->role_id,
            ]
        ], 200);
    }

    public function checkToken()
    {
        return response()->json(['success' => true, 'message' => 'Token Masih Aktif'], 200);
    }

    public function registerUser(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'name'             => 'required',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:6',
            'confirmPassword'  => 'required|same:password',
            'role_id'          => 'required|in:2,3',
        ], [
            'name.required'            => 'Nama wajib diisi.',
            'email.required'           => 'Email wajib diisi.',
            'email.email'              => 'Format email tidak valid.',
            'email.unique'             => 'Email sudah terdaftar.',
            'password.required'        => 'Password wajib diisi.',
            'password.min'             => 'Password minimal 6 karakter.',
            'confirmPassword.required' => 'Konfirmasi password wajib diisi.',
            'confirmPassword.same'     => 'Konfirmasi password harus sama dengan password.',
            'role_id.required'         => 'Role wajib dipilih.',
            'role_id.in'               => 'Role hanya boleh 2 (penghadap) atau 3 (notaris).',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors()
            ], 422);
        }

        // generate kode & expiry SEKALI, pakai di DB & email
        $code    = Str::upper(Str::random(7));
        $expires = now()->addHour();

        $user = User::create([
            'role_id'     => $request->role_id,
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'verify_key'  => $code,
            'expired_key' => $expires,
        ]);

        // kirim email verifikasi (tanpa mengubah verify_key/expired_key di DB lagi)
        $this->sendVerificationMail($user, $code, $expires);

        return response()->json([
            'success' => true,
            'message' => 'Register berhasil. Kode verifikasi telah dikirim ke email.',
            'data'    => ['email' => $user->email]
        ], 201);
    }

    public function verifyEmail(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'email' => 'required|email',
            'kode'  => 'required|string'
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'kode.required'  => 'Kode verifikasi wajib diisi.',
            'kode.string'    => 'Kode verifikasi harus berupa teks.'
        ]);


        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)
            ->where('verify_key', strtoupper($request->kode))
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi tidak valid'
            ], 400);
        }

        if ($user->expired_key && Carbon::parse($user->expired_key)->isPast()) {
            // kirim kode baru otomatis
            $this->regenerateAndSendVerificationCode($user);
            return response()->json([
                'success' => false,
                'message' => 'Kode kadaluarsa. Kode baru telah dikirim ke email.'
            ], 400);
        }

        $user->email_verified_at = now();
        $user->verify_key = null;
        $user->expired_key = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi'
        ], 200);
    }

    public function resendCode(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.'
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email tidak ditemukan'], 404);
        }
        if ($user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Email sudah terverifikasi'], 400);
        }

        // throttle 60 detik
        $key = "resend_code:{$user->id}";
        if (Cache::has($key)) {
            $wait = Cache::get($key) - time();
            return response()->json([
                'success' => false,
                'message' => "Tunggu {$wait} detik untuk kirim ulang."
            ], 429);
        }

        $this->regenerateAndSendVerificationCode($user);
        Cache::put($key, time() + 60, 60);

        return response()->json(['success' => true, 'message' => 'Kode verifikasi dikirim ulang'], 200);
    }

    public function loginUser(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required'
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.'
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses login gagal',
                'data'    => $validasi->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password anda salah'
            ], 401);
        }

        $user = User::with('roles')->where('email', $request->email)->first();

        // blokir login kalau belum verifikasi
        if (is_null($user->email_verified_at)) {
            if ($user->expired_key && Carbon::parse($user->expired_key)->isPast()) {
                $this->regenerateAndSendVerificationCode($user);
                return response()->json([
                    'success' => false,
                    'message' => 'Akun belum terverifikasi. Kode baru telah dikirim ke email.'
                ], 403);
            }
            return response()->json([
                'success' => false,
                'message' => 'Akun belum terverifikasi. Silakan cek email untuk kode verifikasi.'
            ], 403);
        }

        $abilities = [$user->roles->name ?? 'user'];

        // $user->tokens()->delete(); // aktifkan jika mau single-active-token

        $newToken = $user->createToken('user-token', $abilities, now()->addMinutes(30))
            ->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Anda berhasil login',
            'data'    => [
                'role_id' => $user->role_id,
                'name'    => $user->name,
                'email'   => $user->email,
                'token'   => $newToken
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Tidak ada pengguna yang sedang login.'], 401);
        }
        $user->currentAccessToken()?->delete();
        return response()->json(['success' => true, 'message' => 'Anda berhasil logout.'], 200);
    }

    /** Kirim email verifikasi dengan data yang SUDAH ada di DB */
    private function sendVerificationMail(User $user, string $code, \Carbon\Carbon $expires): void
    {
        $frontend = rtrim(config('app.frontend_url'), '/');
        $verifyUrl = $frontend . '/verify-code?' . http_build_query([
            'email' => $user->email,
            'code'  => $code, // pakai kode yang baru dibuat
        ]);

        $details = [
            'name'       => $user->name,
            'role_label' => $user->role_id == 3 ? 'Notaris' : 'Penghadap',
            'website'    => config('app.name'),
            'kode'       => $code,
            'url'        => $verifyUrl,
            'expires_at' => $expires->toIso8601String(),
        ];

        Mail::to($user->email)->send(new AuthMail($details));
    }


    /** Regenerate (buat ulang) kode + kirim email – dipakai di verify/login/resend */
    private function regenerateAndSendVerificationCode(User $user): void
    {
        $code    = Str::upper(Str::random(7));
        $expires = now()->addHour();

        $user->update([
            'verify_key'  => $code,
            'expired_key' => $expires,
        ]);

        $this->sendVerificationMail($user, $code, $expires);
    }
}
