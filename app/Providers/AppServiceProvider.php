<?php

namespace App\Providers;

use App\Models\MReservasi;
use App\Models\MPembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('layout.app', function ($view) {
            $notifications = collect();

            if (!Auth::check()) {
                $view->with('notifications', $notifications);
                return;
            }

            $user = Auth::user();
            $seenAt = session('notifications_seen_at');

            if ($user->role !== 'apoteker') {
                $query = MReservasi::with(['pasien.user', 'dokter.user'])
                    ->latest('updated_at')
                    ->limit(5);

                if ($seenAt) {
                    $query->where('updated_at', '>', $seenAt);
                }

                if ($user->role === 'dokter') {
                    $query->where('dokter_id', $user->dokter->id ?? 0);
                } elseif ($user->role === 'pasien') {
                    $query->where('pasien_id', $user->pasien->id ?? 0);
                }

                $notifications = $query->get()->map(function ($reservasi) use ($user) {
                    if ($user->role === 'admin') {
                        $title = 'Reservasi diperbarui';
                        $message = ($reservasi->pasien->user->name ?? 'Pasien') . ' - ' . ucfirst($reservasi->status);
                    } elseif ($user->role === 'dokter') {
                        $title = 'Data reservasi pasien';
                        $message = ($reservasi->pasien->user->name ?? 'Pasien') . ' - ' . ucfirst($reservasi->status);
                    } else {
                        $title = 'Status reservasi Anda';
                        $message = ucfirst($reservasi->status) . ' dengan ' . ($reservasi->dokter->user->name ?? 'dokter');
                    }

                    return [
                        'title' => $title,
                        'message' => $message,
                        'date' => $reservasi->updated_at->diffForHumans(),
                        'timestamp' => $reservasi->updated_at->timestamp,
                        'url' => route('reservasi.index'),
                    ];
                });
            }

            if ($user->role === 'admin') {
                $tagihanQuery = MPembayaran::with('reservasi.pasien.user')
                    ->where('status', 'belum_bayar')
                    ->whereHas('reservasi.rekamMedis', function ($query) {
                        $query->where('status_resep', 'siap')->whereHas('obat');
                    })
                    ->latest('updated_at')
                    ->limit(5);

                if ($seenAt) {
                    $tagihanQuery->where('updated_at', '>', $seenAt);
                }

                $notifications = $notifications->concat($tagihanQuery->get()->map(function ($pembayaran) {
                    return [
                        'title' => 'Tagihan resep siap',
                        'message' => ($pembayaran->reservasi->pasien->user->name ?? 'Pasien')
                            . ' - Rp ' . number_format($pembayaran->total_bayar, 0, ',', '.'),
                        'date' => $pembayaran->updated_at->diffForHumans(),
                        'timestamp' => $pembayaran->updated_at->timestamp,
                        'url' => route('pembayaran.invoice', $pembayaran->reservasi_id),
                    ];
                }));
            }

            $notifications = $notifications
                ->sortByDesc('timestamp')
                ->take(5)
                ->values()
                ->map(function ($notification) {
                    unset($notification['timestamp']);
                    return $notification;
                });

            $view->with('notifications', $notifications);
        });
    }
}
