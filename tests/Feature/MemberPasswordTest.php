<?php

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(PreventRequestForgery::class);
});

test('forgot password tells guests to contact admin', function () {
    $this->get('/login')->assertOk()->assertSee('ลืมรหัสผ่าน?');
    $this->get('/forgot-password')->assertOk()->assertSee('ติดต่อประธานหอพัก');
    $this->get('/me/password')->assertRedirect('/login');
});

test('all roles can change password and must then log in using the new password', function (string $role) {
    $user = User::factory()->create(['student_id' => '690000001-1', 'role' => $role]);
    $this->actingAs($user)->get('/me/password')->assertOk();
    $this->put('/me/password', [
        'current_password' => 'password', 'current_password_confirmation' => 'password', 'new_password' => 'mynewpassword123',
    ])->assertRedirect('/me/password')->assertSessionHas('success');
    expect(Hash::check('mynewpassword123', $user->fresh()->password))->toBeTrue();
    $this->post('/logout');
    $this->post('/auth/student', ['student_id' => $user->student_id, 'password' => 'password'])->assertSessionHasErrors('student_id');
    $this->assertGuest();
    $this->post('/auth/student', ['student_id' => $user->student_id, 'password' => 'mynewpassword123'])->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
})->with(['user', 'admin', 'superadmin']);

test('wrong old password or mismatched confirmation cannot change password', function (string $old, string $confirmation, string $error) {
    $user = User::factory()->create();
    $this->actingAs($user)->from('/me/password')->put('/me/password', [
        'current_password' => $old, 'current_password_confirmation' => $confirmation, 'new_password' => 'newpassword123',
    ])->assertRedirect('/me/password')->assertSessionHasErrors($error);
    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
    foreach (['current_password', 'current_password_confirmation', 'new_password'] as $field) {
        expect(session('_old_input.'.$field))->toBeNull();
    }
})->with([
    ['wrongpass', 'wrongpass', 'current_password'],
    ['password', 'different', 'current_password_confirmation'],
]);

