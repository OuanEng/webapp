<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token', 'google_id', 'qr_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $attributes = [
        'role' => 'user',
        'is_active' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'dorm_id' => 'integer',
            'room_id' => 'integer',
        ];
    }

    public const STARTING_SCORE_REASON = 'คะแนนเริ่มต้นสมาชิก';

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (! $user->password) {
                $user->password = 'password';
            }
        });

        static::created(function (User $user): void {
            $user->grantStartingScore();
        });
    }

    public function grantStartingScore(): void
    {
        $year = AcademicYear::where('year', config('dorm.academic_year'))->first()
            ?? AcademicYear::where('is_active', true)->first();
        if (! $year) {
            return;
        }

        DB::transaction(function () use ($year): void {
            $member = static::lockForUpdate()->findOrFail($this->id);
            if (! $member->scoreHistories()->where('reason', self::STARTING_SCORE_REASON)->exists()) {
                $member->scoreHistories()->create([
                    'academic_year_id' => $year->id,
                    'score' => 10,
                    'reason' => self::STARTING_SCORE_REASON,
                    'created_by' => $member->id,
                ]);
            }
        }, 3);
    }

    public function dorm(): BelongsTo
    {
        return $this->belongsTo(Dorm::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function scoreHistories(): HasMany
    {
        return $this->hasMany(ScoreHistory::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function repairRequests(): HasMany
    {
        return $this->hasMany(RepairRequest::class);
    }

    public function membershipPayments(): HasMany
    {
        return $this->hasMany(MembershipPayment::class);
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }
}
