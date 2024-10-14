<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    use AuthenticatesUsers, ThrottlesLogins;

    // AuthenticatesUsers: Menyediakan berbagai metode untuk menangani autentikasi pengguna, seperti login() dan logout().
    // ThrottlesLogins: Mengelola pembatasan percobaan login (rate limiting), misalnya jika pengguna mencoba login berkali-kali dalam waktu singkat.

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    // properti yang menentukan ke mana pengguna akan diarahkan setelah berhasil login. Dalam hal ini, diarahkan ke halaman home yang ditentukan di RouteServiceProvider.

    public function redirectTo()
    {
        return $this->redirectTo;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        //  Pada saat kelas ini diinisialisasi, middleware guest akan memastikan bahwa hanya pengguna yang belum login (guest) yang bisa mengakses metode login, kecuali metode logout.
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        session(['remember_username' => $request->remember ? $request->username : '']);
        // dd(
        //     $request->remember,
        //     $request->username,
        //     session('remember')
        // );
        if (method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }
            $request->merge(['module' => 'auth_login']);
            auth()->user()->addLog('Login berhasil');

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);


        // $this->validateLogin($request);: Memvalidasi input pengguna, seperti username dan password.
        // Throttling: Jika terlalu banyak percobaan login gagal, fungsi hasTooManyLoginAttempts() akan mengunci pengguna untuk sementara waktu.
        // attemptLogin(): Jika autentikasi berhasil, sesi login akan dimulai dan pengguna akan di-redirect. Juga mencatat log "Login berhasil".
        // sendFailedLoginResponse(): Jika login gagal, metode ini akan memberikan respons bahwa autentikasi gagal.
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->merge(['module' => 'auth_logout']);
        auth()->user()->addLog('Logout berhasil');
        
        $this->guard()->logout();

        $remember_username = session('remember_username');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session(['remember_username' => $remember_username]);

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    // Pengguna dicatat dengan log "Logout berhasil".
    // Metode guard()->logout() akan mengakhiri sesi login pengguna.
    // Sesi dihapus dan token di-refresh untuk mencegah serangan CSRF.
    // Pengguna di-redirect ke halaman utama atau JSON respons tergantung permintaan.

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view(
            'auth.login',
            [
                'remember_username' => session('remember_username')
            ]
        );
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username($value = '')
    {
        $login = request()->input('username');
        // check username apakah === email, jika iya maka nilai fieild === 'email'
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    protected function validateLogin(Request $request)
    {
        $request->validate(
            [
                $this->username()   => 'required',
                'password'          => 'required',
                //'captcha'           => 'required|simple_captcha',
            ],
            // [
            //     'captcha' => 'Captcha',
            // ],
            // [
            //     $this->username() => 'Username / Email',
            //     'captcha' => 'Captcha',
            // ]
        );
    }
}
