<?php

namespace App\Providers;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // Penting: Tambahkan ini
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk DB::listen
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk Log::debug

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Pastikan tabel 'pengaturans' sudah ada sebelum mencoba mengaksesnya
        if (Schema::hasTable('pengaturans')) {
            $pengaturan = Pengaturan::first();
            View::share('pengaturan', $pengaturan);
        }

        // Hanya aktifkan DB::listen di lingkungan lokal untuk debugging
        if (config('app.env') === 'local') {
            DB::listen(function ($query) {
                // Konversi binding ke format yang dapat dibaca
                $bindings = array_map(function($binding) {
                    if (is_object($binding)) {
                        return (string) $binding;
                    }
                    return json_encode($binding);
                }, $query->bindings);

                // Ganti placeholder (?) dengan nilai binding
                $sql = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
                $fullSql = vsprintf($sql, $bindings);

                Log::debug(
                    'DB Query:',
                    [
                        'SQL' => $fullSql,
                        'Time' => $query->time . 'ms',
                        // 'Connection' => $query->connectionName // Opsional: jika ingin tahu nama koneksi
                    ]
                );
            });
        }
    }
}
