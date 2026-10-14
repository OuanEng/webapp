<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function edit(): View
    {
        return view('member.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string', 'max:255', 'current_password:web'],
            'current_password_confirmation' => ['required', 'same:current_password'],
            'new_password' => ['required', 'string', 'min:8', 'max:255', 'different:current_password'],
        ], [
            'current_password.current_password' => 'รหัสผ่านเดิมไม่ถูกต้อง',
            'current_password_confirmation.same' => 'ยืนยันรหัสผ่านเดิมไม่ตรงกัน',
            'new_password.min' => 'รหัสผ่านใหม่ต้องมีอย่างน้อย 8 ตัวอักษร',
            'new_password.different' => 'รหัสผ่านใหม่ต้องไม่เหมือนรหัสผ่านเดิม',
        ]);

        $request->user()->forceFill([
            'password' => $data['new_password'],
            'remember_token' => Str::random(60),
        ])->save();
        $request->session()->regenerate();

        return redirect()->route('member.password.edit')->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
    }
}
