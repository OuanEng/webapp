<?php

use App\Models\AcademicYear;
use App\Models\User;
use Database\Seeders\StartingScoreSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('new members start at ten and later credits accumulate without duplicate starting points', function () {
    $year = AcademicYear::create(['year' => 2569, 'is_active' => true]);
    $member = User::factory()->create();
    expect((int) $member->scoreHistories()->sum('score'))->toBe(10);
    $member->scoreHistories()->create(['academic_year_id' => $year->id, 'score' => 5, 'reason' => 'คะแนนกิจกรรม', 'created_by' => $member->id]);
    $member->grantStartingScore();
    $this->seed(StartingScoreSeeder::class);
    $this->seed(StartingScoreSeeder::class);
    expect((int) $member->scoreHistories()->sum('score'))->toBe(15)
        ->and($member->scoreHistories()->where('reason', User::STARTING_SCORE_REASON)->count())->toBe(1);
});

test('existing members receive ten without losing their old score', function () {
    $member = User::factory()->create();
    $year = AcademicYear::create(['year' => 2569, 'is_active' => true]);
    $member->scoreHistories()->create(['academic_year_id' => $year->id, 'score' => 7, 'reason' => 'คะแนนเดิม', 'created_by' => $member->id]);
    $this->seed(StartingScoreSeeder::class);
    expect((int) $member->scoreHistories()->sum('score'))->toBe(17);
});
