<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
    ];

    public function emailStatuses(): HasMany
    {
        return $this->hasMany(EmailStatus::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'email_statuses')
            ->withPivot('status', 'sent_at', 'error_message')
            ->withTimestamps();
    }
}
