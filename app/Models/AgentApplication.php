<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentApplication extends Model
{
    protected $fillable = [
        'user_id',
        'rfc', 
        'curp'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}