<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Deed;
use App\Models\Activity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getData(Request $request)
    {
        $user = $request->user();
        $roleName = strtolower(optional($user->roles)->name); // relasi roles() → Role::name

        switch ($roleName) {
            case 'admin':
                return response()->json([
                    'role'            => 'admin',
                    'metrics'         => [
                        'total_notaris'   => User::whereHas('roles', fn($q) => $q->where('name', 'notaris'))->count(),
                        'total_penghadap' => User::whereHas('roles', fn($q) => $q->where('name', 'penghadap'))->count(),
                        'total_akta'      => Deed::count(),
                        'total_aktivitas' => Activity::count(),
                    ],
                    // ambil dari kolom users.status_verification
                    'verifikasi'      => [
                        'approved' => User::where('status_verification', 'approved')->count(),
                        'pending'  => User::where('status_verification', 'pending')->count(),
                        'rejected' => User::where('status_verification', 'rejected')->count(),
                    ],
                    'recent_activities' => Activity::with(['deed', 'notaris', 'clients'])
                        ->orderByDesc('id')->limit(5)->get(),
                ]);

            case 'notaris':
                return response()->json([
                    'role'              => 'notaris',
                    'status_verification' => $user->status_verification ?? 'pending',
                    'metrics'           => [
                        'total_akta'      => Deed::where('user_notaris_id', $user->id)->count(),
                        'total_aktivitas' => Activity::where('user_notaris_id', $user->id)->count(),
                    ],
                    'recent_activities' => Activity::where('user_notaris_id', $user->id)
                        ->with(['deed', 'clients'])
                        ->orderByDesc('id')->limit(5)->get(),
                ]);

            case 'penghadap':
                $query = $user->clientActivities()->with(['deed', 'notaris'])->orderByDesc('activity.id');
                return response()->json([
                    'role'            => 'penghadap',
                    'metrics'         => [
                        // distinct deeds dari aktivitas yang diikuti user
                        'total_akta'      => (clone $query)->pluck('deed_id')->unique()->count(),
                        'total_aktivitas' => $user->clientActivities()->count(),
                    ],
                    'recent_activities' => $query->limit(5)->get(),
                ]);

            default:
                return response()->json(['message' => 'Role tidak dikenali'], 403);
        }
    }
}
