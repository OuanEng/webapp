<?php

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(PreventRequestForgery::class);
});

test('student login page contains the credentials form', function () {
    $this->get('/login')->assertOk()->assertViewIs('auth.student-login')
        ->assertSee('name="student_id"', false)->assertSee('name="password"', false);
});

test('active students enter using student id and shared password with database role', function () {
    $user = User::factory()->create(['student_id' => '673380322-2', 'role' => 'user', 'password' => null]);
    $this->post('/auth/student', ['student_id' => '673380322-2', 'password' => 'password'])
        ->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
    $this->get('/dashboard')->assertOk();
    $this->get('/admin/users')->assertForbidden();
    $this->post('/logout')->assertRedirect('/login');
    $this->assertGuest();
});

test('wrong passwords unknown students and inactive accounts cannot enter', function (string $id, string $password, bool $active) {
    User::factory()->create(['student_id' => '673380322-2', 'is_active' => $active]);
    $this->from('/login')->post('/auth/student', ['student_id' => $id, 'password' => $password])
        ->assertRedirect('/login')->assertSessionHasErrors('student_id');
    $this->assertGuest();
})->with([
    ['673380322-2', 'wrong', true],
    ['unknown', 'password', true],
    ['673380322-2', 'password', false],
]);

test('student login does not accept a submitted role', function () {
    User::factory()->create(['student_id' => '673380322-2']);
    $this->post('/auth/student', ['student_id' => '673380322-2', 'password' => 'password', 'role' => 'superadmin'])
        ->assertSessionHasErrors('role');
    $this->assertGuest();
});
