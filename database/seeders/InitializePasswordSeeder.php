<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitializePasswordSeeder extends Seeder
{
    public function run(): void
    {
        $count = User::whereNull('password')->orWhere('password', '')->update(['password' => Hash::make('password')]);
        $this->command?->info('Initialized '.$count.' accounts without passwords; existing passwords retained.');
    }
}
