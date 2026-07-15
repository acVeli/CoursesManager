<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Dish;
use App\Models\MealPlanEntry;
use App\Services\BabyFeedingGuide;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'has_baby', 'baby_birth_date'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'has_baby' => 'boolean',
            'baby_birth_date' => 'date',
        ];
    }

    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }

    public function mealPlanEntries()
    {
        return $this->hasMany(MealPlanEntry::class);
    }

    public function babyFeedingRecommendations(): ?array
    {
        if (! $this->has_baby) {
            return null;
        }

        return app(BabyFeedingGuide::class)->forBirthDate($this->baby_birth_date);
    }
}
