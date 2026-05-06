<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = ['name', 'slug', 'email', 'phone', 'logo', 'plan'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
    public function invitations()
{
    return $this->hasMany(Invitation::class);
}
}
