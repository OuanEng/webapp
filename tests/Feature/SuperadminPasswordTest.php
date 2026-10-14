<?php

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);
beforeEach(function () { $this->withoutMiddleware(PreventRequestForgery::class); });

test('superadmin sets a student password without the old password and logs no secret', function () {
    $actor = User::factory()->create(['role' => 'superadmin']);
    $student = User::factory()->create(['student_id' => '690000001-1']);
    $this->actingAs($actor)->get('/admin/users/'.$student->id)->assertSee('ตั้งรหัสผ่านใหม่');
    $this->get('/superadmin/users/'.$student->id.'/password')->assertOk();
    $this->put('/superadmin/users/'.$student->id.'/password', ['new_password' => 'resetpassword123', 'new_password_confirmation' => 'resetpassword123'])
        ->assertRedirect('/admin/users/'.$student->id)->assertSessionHas('success');
    expect(Hash::check('resetpassword123', $student->fresh()->password))->toBeTrue();
    $log = AuditLog::where('action', 'user.password_reset')->firstOrFail();
    expect($log->actor_id)->toBe($actor->id)->and($log->target_id)->toBe($student->id)
        ->and(json_encode($log->toArray()))->not->toContain('resetpassword123');
    $this->post('/logout');
    $this->post('/auth/student', ['student_id' => $student->student_id, 'password' => 'password'])->assertSessionHasErrors('student_id');
    $this->post('/auth/student', ['student_id' => $student->student_id, 'password' => 'resetpassword123'])->assertRedirect('/dashboard');
});

test('other roles cannot reset a student password even by direct request', function (string $role) {
    $actor = User::factory()->create(['role' => $role]);
    $student = User::factory()->create();
    $this->actingAs($actor)->get('/superadmin/users/'.$student->id.'/password')->assertForbidden();
    $this->put('/superadmin/users/'.$student->id.'/password', ['new_password' => 'resetpassword123', 'new_password_confirmation' => 'resetpassword123'])->assertForbidden();
    expect(Hash::check('password', $student->fresh()->password))->toBeTrue();
})->with(['user', 'admin']);

test('reset confirmation must match and superadmin cannot reset other privileged accounts', function () {
    $actor = User::factory()->create(['role' => 'superadmin']);
    $student = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($actor)->put('/superadmin/users/'.$student->id.'/password', ['new_password' => 'resetpassword123', 'new_password_confirmation' => 'mismatch'])->assertSessionHasErrors('new_password');
    expect(Hash::check('password', $student->fresh()->password))->toBeTrue();
    $this->get('/superadmin/users/'.$admin->id.'/password')->assertForbidden();
    expect(AuditLog::where('action', 'user.password_reset')->count())->toBe(0);
});
