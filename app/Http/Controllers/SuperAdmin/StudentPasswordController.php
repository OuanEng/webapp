<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentPasswordController extends Controller
{
    public function edit(User $user): View
    {
        Gate::authorize('resetPassword', $user);

        return view('superadmin.users.reset-password', ['member' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('resetPassword', $user);
        $data = $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'user_id' => ['prohibited'], 'role' => ['prohibited'],
        ], [
            'new_password.min' => 'รหัสผ่านใหม่ต้องมีอย่างน้อย 8 ตัวอักษร',
            'new_password.confirmed' => 'ยืนยันรหัสผ่านใหม่ไม่ตรงกัน',
        ]);

        DB::transaction(function () use ($request, $user, $data): void {
            $member = User::lockForUpdate()->findOrFail($user->id);
            Gate::authorize('resetPassword', $member);
            $member->forceFill(['password' => $data['new_password'], 'remember_token' => Str::random(60)])->save();
            if (config('session.driver') === 'database') {
                DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                    ->where('user_id', $member->id)->delete();
            }
            AuditLog::create([
                'actor_id' => $request->user()->id, 'action' => 'user.password_reset',
                'target_type' => User::class, 'target_id' => $member->id,
                'new_values' => ['user_id' => $member->id, 'password_reset' => true],
            ]);
        }, 3);

        return redirect()->route('admin.users.show', $user)->with('success', 'ตั้งรหัสผ่านใหม่ให้นักศึกษาเรียบร้อยแล้ว');
    }
}
