<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class StartingScoreSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AcademicYearSeeder::class);
        foreach (User::orderBy('id')->get() as $user) {
            $user->grantStartingScore();
        }
        $count = User::whereHas('scoreHistories', fn ($query) => $query->where('reason', User::STARTING_SCORE_REASON)->where('score', 10))->count();
        $this->command?->info('Starting score verified for '.$count.' members; existing scores retained.');
    }
}
