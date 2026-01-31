<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\LoginService;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected LoginService $service;

    public function __construct(LoginService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        return view('auth.login');
    }

    public function submit(LoginRequest $request)
    {
        $data = $request->validated();

        try {
            $user = $this->service->attempt($data['email'], $data['password']);

            if (! $user) {
                return back()->withErrors([
                    'email' => 'The credentials provided do not match our records.',
                ])->onlyInput('email');
            }

            Auth::guard()->login($user, $remember = (bool) ($data['remember'] ?? false));

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        } catch (\Throwable $e) {
            \Log::error('LoginController@store error: ' . $e->getMessage(), ['exception' => $e]);

            return back()
                ->withInput($request->only('email'))
                ->with('flash.error', 'Internal error while creating your account. Please try again.');
        }
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->ajax()) {
            return response()->json(['redirect' => url('/')]);
        }

        return redirect('/');
    }
}
