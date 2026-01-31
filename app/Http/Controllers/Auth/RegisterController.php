<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\RegisterService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    protected RegisterService $service;

    public function __construct(RegisterService $service)
    {
        $this->service = $service;
    }

    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $user = $this->service->createUser($data);

            Auth::guard()->login($user);

            $request->session()->regenerate();

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            Log::error('RegisterController@store error: ' . $e->getMessage(), ['exception' => $e]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('flash.error', 'Internal error while creating your account. Please try again.');
        }
    }
}
