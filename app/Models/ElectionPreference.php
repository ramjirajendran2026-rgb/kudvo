<?php

namespace App\Models;

use App\Enums\CandidateSort;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectionPreference extends Model
{
    protected $fillable = [
        'eul_mail',
        'eul_sms',
        'mfa_mail',
        'mfa_sms',
        'voted_confirmation_mail',
        'voted_confirmation_sms',
        'voted_ballot_download',
        'voted_ballot_mail',
        'dnt_votes',
        'voted_ballot_update',
        'ip_restriction_threshold',
        'candidate_sort',
        'candidate_photo',
        'candidate_bio',
        'candidate_attachment',
        'election_id',
    ];

    protected $casts = [
        'eul_mail' => 'bool',
        'eul_sms' => 'bool',
        'mfa_mail' => 'bool',
        'mfa_sms' => 'bool',
        'voted_confirmation_mail' => 'bool',
        'voted_confirmation_sms' => 'bool',
        'voted_ballot_download' => 'bool',
        'voted_ballot_mail' => 'bool',
        'dnt_votes' => 'bool',
        'voted_ballot_update' => 'bool',
        'ip_restriction_threshold' => 'int',
        'candidate_sort' => CandidateSort::class,
        'candidate_photo' => 'bool',
        'candidate_bio' => 'bool',
        'candidate_attachment' => 'bool',
        'election_id' => 'int',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(related: Election::class);
    }
}
