<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model {
  protected $guarded = [];

  protected $casts = [
    'read_at' => 'datetime',
  ];

  protected $appends = ['attachment_url'];

  public function conversation() { return $this->belongsTo(Conversation::class); }
  public function sender() { return $this->belongsTo(User::class, 'sender_id'); }

  public function getAttachmentUrlAttribute(): ?string
  {
      if (!$this->attachment_path) {
          return null;
      }

      return Storage::disk('public')->url($this->attachment_path);
  }
}
