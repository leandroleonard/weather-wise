<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\RegisterService;
use Inertia\Inertia;
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
        return Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $user = $this->service->createUser($data);

            // auth()->login($user);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            \Log::error('Register error: '.$e->getMessage(), ['exception' => $e]);

            return redirect()
                ->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('flash', ['error' => 'An error occurred while creating your account. Please try again.']);
        }
    }
}
