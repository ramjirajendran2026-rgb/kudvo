<?php

namespace App\Models;

use Akhan619\LaravelSesEventManager\App\Models\Email;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailClick extends Model
{
    protected $fillable = [
        'ip_address',
        'user_agent',
        'link',
        'link_tags',
        'clicked_at',
        'email_id',
    ];

    protected $casts = [
        'link' => 'encrypted',
        'link_tags' => 'array',
        'clicked_at' => 'immutable_datetime',
        'email_id' => 'int',
    ];

    public function email(): BelongsTo
    {
        return $this->belongsTo(related: Email::class);
    }
}
