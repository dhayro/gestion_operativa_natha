<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // Reemplaza la línea anterior por esta:
    protected $redirectTo = '/dashboard/analytics';

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(\Illuminate\Http\Request $request)
    {
        $token = $request->route()->parameter('token');

        // Aquí usaremos una nueva vista que crearemos a continuación
        return view('admin.auth.boxed.password-reset', [
            'token' => $token,
            'email' => $request->email,
            'catName' => 'auth',
            'title' => 'Password Reset Boxed',
            "breadcrumbs" => ["Authentication", "Password Reset"],
            'scrollspy' => 0,
            'simplePage' => 1
        ]);
    }
}