<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // Diperlukan untuk metode authenticated
use Illuminate\Support\Facades\Auth; // Diperlukan untuk Auth::user()

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME; // Baris ini akan diabaikan karena ada metode authenticated()

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a successful login attempt.
     *
     * Metode ini dipanggil setelah pengguna berhasil diautentikasi.
     * Ini akan mengarahkan pengguna ke dashboard yang sesuai berdasarkan peran mereka.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        // Memeriksa peran pengguna dan mengarahkan ke rute dashboard yang sesuai
        switch ($user->roles) {
            case 'admin':
                return redirect()->route('admin.dashboard');
                break;
            case 'guru':
                return redirect()->route('guru.dashboard');
                break;
            case 'siswa':
                return redirect()->route('siswa.dashboard');
                break;
            case 'orangtua':
                return redirect()->route('orangtua.dashboard');
                break;
            default:
                // Jika peran tidak dikenali atau tidak ada, arahkan ke rute 'home' default
                return redirect()->route('home');
                break;
        }
    }
}
