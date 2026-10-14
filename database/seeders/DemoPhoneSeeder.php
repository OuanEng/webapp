<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DemoPhoneSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new RuntimeException('Demo phones can only be generated locally.');
        }

        $count = DB::transaction(function (): int {
            $usedPhones = [];
            $users = User::orderBy('id')->get();
            foreach ($users as $user) {
                do {
                    $phone = '9'.str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
                } while (isset($usedPhones[$phone]));
                $usedPhones[$phone] = true;
                $user->phone = $phone;
                $user->save();
            }

            $savedPhones = User::pluck('phone');
            if ($savedPhones->unique()->count() !== $users->count()
                || $savedPhones->contains(fn ($phone): bool => ! preg_match('/^9[0-9]{9}$/', $phone))) {
                throw new RuntimeException('Generated phone verification failed.');
            }

            return $users->count();
        });

        $this->command?->info('Generated and verified '.$count.' unique demo phones: 10 digits, starting with 9.');
    }
}
