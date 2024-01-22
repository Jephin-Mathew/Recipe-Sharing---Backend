<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'bio','is_admin','blocked'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    // Additional relationships
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    public function likes()
    {
        return $this->belongsToMany(Recipe::class, 'likes', 'user_id', 'recipe_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function activities()
    {
    return $this->hasMany(Activity::class);
    }

    public function followingActivities()
    {
    return $this->hasManyThrough(Activity::class, User::class, 'follower_id', 'user_id', 'id', 'id')
        ->latest(); 
    }

    public function followerActivities()
    {
    return $this->hasManyThrough(Activity::class, User::class, 'user_id', 'follower_id', 'id', 'id')
        ->latest(); 
    } 

     public function activityFeed()
    {
    $user = auth()->user();
    $activityFeed = $user->followingActivities;

    return response()->json(['activity_feed' => $activityFeed]);
    }
    
    // Accessors
    public function getFollowersCountAttribute()
    {
        return $this->followers()->count();
    }

    public function getFollowingCountAttribute()
    {
        return $this->following()->count();
    }
}
