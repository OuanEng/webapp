<?php

use App\Models\Room;
use App\Models\User;
use Database\Seeders\Hall8CsvSeeder;
use Database\Seeders\TemporaryMemberSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('hall8 import fills 63 rooms without duplicates and every imported student can enter their own room', function () {
    $this->withoutMiddleware([PreventRequestForgery::class, \Illuminate\Routing\Middleware\ThrottleRequests::class]);
    $this->seed(TemporaryMemberSeeder::class);
    $this->seed(Hall8CsvSeeder::class);
    $this->seed(Hall8CsvSeeder::class);
    expect(User::count())->toBe(126)->and(Room::count())->toBe(63);
    foreach (Room::withCount(['users' => fn ($query) => $query->where('is_active', true)])->get() as $room) {
        expect($room->users_count)->toBe(2);
    }
    expect(User::where('role', 'superadmin')->count())->toBe(1)
        ->and(User::where('role', 'admin')->count())->toBe(2);
    foreach (User::with('room')->get() as $student) {
        $this->post('/auth/student', ['student_id' => $student->student_id, 'password' => 'password'])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($student);
        $response = $this->get('/dashboard')->assertOk();
        if ($student->role === 'user') {
            $response->assertSee('ห้อง '.$student->room->number);
        }
        $this->get('/me')->assertOk()->assertSee($student->student_id)->assertSee($student->room->number);
        $this->get('/me/qr/image')->assertOk()->assertHeader('Content-Type', 'image/svg+xml');
        if ($student->role === 'user') {
            $this->get('/admin/users')->assertForbidden();
        } else {
            $this->get('/admin/users')->assertOk();
        }
        $this->post('/logout')->assertRedirect('/login');
    }
});


