<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'subject',
        'body',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function emailStatuses(): HasMany
    {
        return $this->hasMany(EmailStatus::class);
    }

    public function recipients()
    {
        return $this->belongsToMany(Contact::class, 'email_statuses')
            ->withPivot('status', 'sent_at', 'error_message')
            ->withTimestamps();
    }
}
