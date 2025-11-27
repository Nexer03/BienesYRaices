<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model {
  protected $guarded = [];

  public function property() { return $this->belongsTo(Property::class); }
  public function agent() { return $this->belongsTo(User::class, 'agent_id'); }
  public function client() { return $this->belongsTo(User::class, 'client_id'); }
  public function messages() { return $this->hasMany(Message::class); }
}
