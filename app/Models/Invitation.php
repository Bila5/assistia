<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = ['organization_id', 'token', 'role', 'used'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
