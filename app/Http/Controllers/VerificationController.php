<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Identity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    public function verifyIdentity(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'status_verification' => 'required|in:pending,approved,rejected',
            'notes_verification'     => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari user dan update status
            $user = User::find($request->id);
            $user->status_verification = $request->status_verification;
            if ($request->notes_verification) {
                $user->notes_verification = $request->notes_verification;
            } else {
                $user->notes_verification = null; // Reset jika tidak ada catatan
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Status verifikasi berhasil diperbarui',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status_verification' => $user->status_verification,
                    'notes_verification' => $user->notes_verification,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function getPendingVerifications(Request $request)
    {
        try {
            $perPage = (int)($request->query('per_page', 10));
            $perPage = $perPage > 0 ? $perPage : 10;
            $q = $request->query('search');

            $query = Identity::with('user')
                ->whereHas('user', function ($query) use ($q) {
                    $query->where('status_verification', 'pending');

                    if ($q) {
                        $query->where(function ($subQuery) use ($q) {
                            $subQuery->where('name', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                    }
                })
                ->orderBy('created_at', 'desc');

            $identities = $query->paginate($perPage)->appends($request->query());

            $transformedData = $identities->getCollection()->map(function ($identity) {
                return [
                    'user_id' => $identity->user_id,
                    'user_name' => $identity->user->name,
                    'user_email' => $identity->user->email,
                    'ktp' => $identity->ktp,
                    'npwp' => $identity->npwp,
                    'ktp_notaris' => $identity->ktp_notaris,
                    'file_ktp' => $identity->file_ktp,
                    'file_kk' => $identity->file_kk,
                    'file_npwp' => $identity->file_npwp,
                    'file_ktp_notaris' => $identity->file_ktp_notaris,
                    'file_sign' => $identity->file_sign,
                    'verification_status' => $identity->verification_status,
                    'verification_notes' => $identity->verification_notes,
                    'updated_at' => $identity->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $transformedData,
                'meta' => [
                    'current_page' => $identities->currentPage(),
                    'per_page' => $identities->perPage(),
                    'total' => $identities->total(),
                    'last_page' => $identities->lastPage(),
                    'from' => $identities->firstItem(),
                    'to' => $identities->lastItem(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function getRejectVerifications(Request $request)
    {
        try {
            $perPage = (int)($request->query('per_page', 10));
            $perPage = $perPage > 0 ? $perPage : 10;
            $q = $request->query('search');

            $query = Identity::with('user')
                ->whereHas('user', function ($query) use ($q) {
                    $query->where('status_verification', 'rejected');

                    if ($q) {
                        $query->where(function ($subQuery) use ($q) {
                            $subQuery->where('name', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                    }
                })
                ->orderBy('created_at', 'desc');

            $identities = $query->paginate($perPage)->appends($request->query());

            $transformedData = $identities->getCollection()->map(function ($identity) {
                return [
                    'user_id' => $identity->user_id,
                    'user_name' => $identity->user->name,
                    'user_email' => $identity->user->email,
                    'ktp' => $identity->ktp,
                    'npwp' => $identity->npwp,
                    'ktp_notaris' => $identity->ktp_notaris,
                    'file_ktp' => $identity->file_ktp,
                    'file_kk' => $identity->file_kk,
                    'file_npwp' => $identity->file_npwp,
                    'file_ktp_notaris' => $identity->file_ktp_notaris,
                    'file_sign' => $identity->file_sign,
                    'verification_status' => $identity->verification_status,
                    'verification_notes' => $identity->verification_notes,
                    'updated_at' => $identity->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $transformedData,
                'meta' => [
                    'current_page' => $identities->currentPage(),
                    'per_page' => $identities->perPage(),
                    'total' => $identities->total(),
                    'last_page' => $identities->lastPage(),
                    'from' => $identities->firstItem(),
                    'to' => $identities->lastItem(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function getApprovedVerifications(Request $request)
    {
        try {
            $perPage = (int)($request->query('per_page', 10));
            $perPage = $perPage > 0 ? $perPage : 10;
            $q = $request->query('search');

            $query = Identity::with('user')
                ->whereHas('user', function ($query) use ($q) {
                    $query->where('status_verification', 'approved');

                    if ($q) {
                        $query->where(function ($subQuery) use ($q) {
                            $subQuery->where('name', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                    }
                })
                ->orderBy('created_at', 'desc');

            $identities = $query->paginate($perPage)->appends($request->query());

            $transformedData = $identities->getCollection()->map(function ($identity) {
                return [
                    'user_id' => $identity->user_id,
                    'user_name' => $identity->user->name,
                    'user_email' => $identity->user->email,
                    'ktp' => $identity->ktp,
                    'npwp' => $identity->npwp,
                    'ktp_notaris' => $identity->ktp_notaris,
                    'file_ktp' => $identity->file_ktp,
                    'file_kk' => $identity->file_kk,
                    'file_npwp' => $identity->file_npwp,
                    'file_ktp_notaris' => $identity->file_ktp_notaris,
                    'file_sign' => $identity->file_sign,
                    'verification_status' => $identity->verification_status,
                    'verification_notes' => $identity->verification_notes,
                    'updated_at' => $identity->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $transformedData,
                'meta' => [
                    'current_page' => $identities->currentPage(),
                    'per_page' => $identities->perPage(),
                    'total' => $identities->total(),
                    'last_page' => $identities->lastPage(),
                    'from' => $identities->firstItem(),
                    'to' => $identities->lastItem(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function getRejectedPendingVerifications(Request $request)
    {
        try {
            $perPage = (int)($request->query('per_page', 10));
            $perPage = $perPage > 0 ? $perPage : 10;
            $q = $request->query('search');

            $query = Identity::with('user')
                ->whereHas('user', function ($query) use ($q) {
                    $query->whereIn('status_verification', ['rejected', 'pending']);

                    if ($q) {
                        $query->where(function ($subQuery) use ($q) {
                            $subQuery->where('name', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%");
                        });
                    }
                })
                ->orderBy('created_at', 'desc');

            $identities = $query->paginate($perPage)->appends($request->query());

            $transformedData = $identities->getCollection()->map(function ($identity) {
                return [
                    'user_id' => $identity->user_id,
                    'user_name' => $identity->user->name,
                    'user_email' => $identity->user->email,
                    'ktp' => $identity->ktp,
                    'npwp' => $identity->npwp,
                    'ktp_notaris' => $identity->ktp_notaris,
                    'file_ktp' => $identity->file_ktp,
                    'file_kk' => $identity->file_kk,
                    'file_npwp' => $identity->file_npwp,
                    'file_ktp_notaris' => $identity->file_ktp_notaris,
                    'file_sign' => $identity->file_sign,
                    'verification_status' => $identity->verification_status,
                    'verification_notes' => $identity->verification_notes,
                    'updated_at' => $identity->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $transformedData,
                'meta' => [
                    'current_page' => $identities->currentPage(),
                    'per_page' => $identities->perPage(),
                    'total' => $identities->total(),
                    'last_page' => $identities->lastPage(),
                    'from' => $identities->firstItem(),
                    'to' => $identities->lastItem(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function getAllUsers(Request $request)
    {
        try {
            $perPage = (int)($request->query('per_page', 10));
            $perPage = $perPage > 0 ? $perPage : 10;
            $q = $request->query('search');

            $query = Identity::with('user')->orderBy('created_at', 'desc');

            if ($q) {
                $query->whereHas('user', function ($userQuery) use ($q) {
                    $userQuery->where(function ($subQuery) use ($q) {
                        $subQuery->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
                });
            }

            $identities = $query->paginate($perPage)->appends($request->query());

            $transformedData = $identities->getCollection()->map(function ($identity) {
                return [
                    'user_id' => $identity->user_id,
                    'user_name' => $identity->user->name,
                    'user_email' => $identity->user->email,
                    'ktp' => $identity->ktp,
                    'npwp' => $identity->npwp,
                    'ktp_notaris' => $identity->ktp_notaris,
                    'file_ktp' => $identity->file_ktp,
                    'file_kk' => $identity->file_kk,
                    'file_npwp' => $identity->file_npwp,
                    'file_ktp_notaris' => $identity->file_ktp_notaris,
                    'file_sign' => $identity->file_sign,
                    'verification_status' => $identity->verification_status,
                    'verification_notes' => $identity->verification_notes,
                    'updated_at' => $identity->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $transformedData,
                'meta' => [
                    'current_page' => $identities->currentPage(),
                    'per_page' => $identities->perPage(),
                    'total' => $identities->total(),
                    'last_page' => $identities->lastPage(),
                    'from' => $identities->firstItem(),
                    'to' => $identities->lastItem(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    public function getIdentityDetail($userId)
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $identity = Identity::where('user_id', $userId)->first();

            if (!$identity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data identitas user tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail identitas berhasil diambil',
                'data' => [
                    // Data User
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'telepon' => $user->telepon,
                        'gender' => $user->gender,
                        'address' => $user->address,
                        'file_avatar' => $user->file_avatar,
                        'status_verification' => $user->status_verification,
                        'notes_verification' => $user->notes_verification,
                        'created_at' => $user->created_at,
                    ],

                    // Data Identity
                    'identity' => [
                        'ktp' => $identity->ktp,
                        'npwp' => $identity->npwp,
                        'ktp_notaris' => $identity->ktp_notaris,
                        'file_ktp' => $identity->file_ktp,
                        'file_kk' => $identity->file_kk,
                        'file_npwp' => $identity->file_npwp,
                        'file_ktp_notaris' => $identity->file_ktp_notaris,
                        'file_sign' => $identity->file_sign,
                        'created_at' => $identity->created_at,
                        'updated_at' => $identity->updated_at,
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }
}
