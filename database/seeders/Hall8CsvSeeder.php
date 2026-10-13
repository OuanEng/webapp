<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Dorm;
use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;

class Hall8CsvSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new RuntimeException('Sample import is only available locally.');
        }

        $path = storage_path('app/demo_hall8_database_A-F.csv');
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new RuntimeException('CSV file is missing.');
        }
        $header = fgetcsv($handle, 0, ',', '"', '');
        if ($header === false) {
            throw new RuntimeException('CSV file is empty.');
        }
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        $expected = ['ห้องพัก', 'รหัสนักศึกษา', 'ชื่อ', 'นามสกุล', 'คณะ', 'KKU Mail'];
        if ($header !== $expected) {
            throw new RuntimeException('Unexpected CSV columns.');
        }
        $students = [];
        while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            if ($row === [null]) {
                continue;
            }
            if (count($row) !== 6) {
                throw new RuntimeException('Invalid CSV row.');
            }
            $student = array_combine(['room', 'student_id', 'first_name', 'last_name', 'faculty', 'email'], array_map('trim', $row));
            $student['email'] = strtolower($student['email']);
            Validator::make($student, [
                'room' => ['required', 'regex:/^[234](0[1-9]|1[0-9]|2[01])$/'],
                'student_id' => ['required', 'string', 'max:255'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'faculty' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
            ])->validate();
            $students[] = $student;
        }
        fclose($handle);
        // CSV has no room 301, so add two clearly marked sample residents.
        if (! collect($students)->contains('room', '301')) {
            foreach ([1, 2] as $index) {
                $students[] = [
                    'room' => '301', 'student_id' => '690001301-'.$index,
                    'first_name' => 'นักศึกษาจำลอง'.$index, 'last_name' => 'ห้อง301',
                    'faculty' => 'วิทยาลัยการคอมพิวเตอร์',
                    'email' => 'room301.student'.$index.'@mock.kku.test',
                ];
            }
        }
        $rows = collect($students);
        if ($rows->isEmpty() || $rows->unique('student_id')->count() !== $rows->count() || $rows->unique('email')->count() !== $rows->count()) {
            throw new RuntimeException('Empty CSV or duplicate student identity.');
        }
        foreach ($rows->groupBy('room') as $roomStudents) {
            if ($roomStudents->count() > Room::CAPACITY) {
                throw new RuntimeException('CSV exceeds room capacity.');
            }
        }

        DB::transaction(function () use ($students, $rows): void {
            if (Dorm::where('code', '!=', 'H8')->exists()) {
                throw new RuntimeException('This sample belongs to hall H8 only.');
            }
            $dorm = Dorm::firstOrCreate(['code' => 'H8'], ['name' => 'หอพัก 8']);
            $building = Building::firstOrCreate(['dorm_id' => $dorm->id, 'code' => 'H8'], ['name' => 'อาคารหอพัก 8']);
            $rooms = [];
            foreach ([2, 3, 4] as $number) {
                $floor = Floor::firstOrCreate(['building_id' => $building->id, 'number' => $number]);
                for ($index = 1; $index <= 21; $index++) {
                    $roomNumber = (string) ($number * 100 + $index);
                    $rooms[$roomNumber] = Room::firstOrCreate(['floor_id' => $floor->id, 'number' => $roomNumber], ['capacity' => Room::CAPACITY]);
                }
            }

            // Keep the original demo accounts without overfilling CSV rooms.
            foreach (config('dorm.temporary_members') as $member) {
                $user = User::where('student_id', $member['student_id'])->where('email', $member['email'])->first();
                if ($user && ! $rows->contains('student_id', $user->student_id)) {
                    $user->room_id = $user->role === 'user' ? $rooms['301']->id : null;
                    $user->save();
                }
            }
            foreach ($students as $student) {
                $byId = User::where('student_id', $student['student_id'])->first();
                $byEmail = User::where('email', $student['email'])->first();
                if (($byId && $byId->email !== $student['email']) || ($byEmail && $byEmail->student_id !== $student['student_id'])) {
                    throw new RuntimeException('Student identity conflicts with an existing account.');
                }
                $user = $byId ?? new User;
                $user->student_id = $student['student_id'];
                $user->email = $student['email'];
                $user->first_name = $student['first_name'];
                $user->last_name = $student['last_name'];
                $user->name = $student['first_name'].' '.$student['last_name'];
                $user->faculty = $student['faculty'];
                $user->dorm_id = $dorm->id;
                $user->room_id = $rooms[$student['room']]->id;
                if (! $user->exists) {
                    $user->role = 'user';
                    $user->password = Hash::make('password');
                    $user->qr_token = (string) Str::uuid();
                }
                $user->save();
            }
            foreach ($rooms as $room) {
                if ($room->users()->where('is_active', true)->count() > $room->capacity) {
                    throw new RuntimeException('Room '.$room->number.' exceeds capacity; import rolled back.');
                }
            }
        });
        $this->command?->info('Imported '.$rows->count().' students; assigned CSV rooms and preserved existing roles.');
    }
}

