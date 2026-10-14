<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudentLoginController extends Controller
{
    public function create(): View
    {
        return view('auth.student-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'role' => ['prohibited'],
        ]);

        $user = User::where('student_id', trim($validated['student_id']))
            ->where('is_active', true)
            ->whereIn('role', ['superadmin', 'admin', 'user'])
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password ?? '')) {
            throw ValidationException::withMessages([
                'student_id' => 'รหัสนักศึกษาหรือรหัสผ่านไม่ถูกต้อง หรือบัญชีถูกระงับ',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
