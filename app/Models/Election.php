<?php

namespace App\Models;

use App\Data\Election\CollaboratorPermissionsData;
use App\Data\Election\PlanFeatureData;
use App\Data\Election\PreferenceData;
use App\Data\Election\ResultMetaData;
use App\Data\Election\VoteSecretData;
use App\Enums\ElectionSetupStep;
use App\Enums\ElectionStatus;
use App\Enums\InvoiceStatus;
use App\Models\Concerns\HasShortCode;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Cashier\Checkout;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Election extends Model
{
    use HasShortCode;
    use HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'ballot_locales',
        'preference',
        'timezone',
        'starts_at',
        'ends_at',
        'booth_starts_at',
        'booth_ends_at',
        'published_at',
        'closed_at',
        'completed_at',
        'cancelled_at',
        'paid_at',
        'invoice_status',
        'stripe_invoice_id',
        'stripe_invoice_data',
        'owner_id',
        'plan_id',
        'organisation_id',
    ];

    protected $casts = [
        'preference' => PreferenceData::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'booth_starts_at' => 'datetime',
        'booth_ends_at' => 'datetime',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'paid_at' => 'datetime',
        'invoice_status' => InvoiceStatus::class,
        'stripe_invoice_data' => 'array',
        'owner_id' => 'int',
        'plan_id' => 'int',
        'organisation_id' => 'int',
    ];

    public array $translatable = [
        'name',
        'description',
    ];

    protected function ballotLinkVia(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => [
                ...Arr::wrap(value: $this->preference?->ballot_link_mail ? 'mail' : null),
                ...Arr::wrap(value: $this->preference?->ballot_link_sms ? 'sms' : null),
            ],
        );
    }

    protected function votedConfirmationVia(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => [
                ...Arr::wrap(value: $this->preference?->voted_confirmation_mail ? 'mail' : null),
                ...Arr::wrap(value: $this->preference?->voted_confirmation_sms ? 'sms' : null),
            ],
        );
    }

    protected function votedBallotCopyShareVia(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => [
                ...Arr::wrap(value: $this->preference?->voted_ballot_mail ? 'mail' : null),
            ],
        );
    }

    protected function startsAtLocal(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->starts_at?->tz(value: $this->timezone),
        );
    }

    protected function endsAtLocal(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->ends_at?->tz(value: $this->timezone),
        );
    }

    protected function boothStartsAtLocal(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->booth_starts_at?->tz(value: $this->timezone),
        );
    }

    protected function boothEndsAtLocal(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->booth_ends_at?->tz(value: $this->timezone),
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => match (true) {
                filled(value: $this->cancelled_at) => ElectionStatus::CANCELLED,
                filled(value: $this->completed_at) => ElectionStatus::COMPLETED,
                filled(value: $this->closed_at) => ElectionStatus::CLOSED,
                filled(value: $this->published_at) => ElectionStatus::PUBLISHED,
                default => ElectionStatus::DRAFT,
            },
        );
    }

    protected function isDraft(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->status === ElectionStatus::DRAFT,
        );
    }

    protected function isPublished(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->status === ElectionStatus::PUBLISHED,
        );
    }

    protected function isUpcoming(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->is_published && $this->starts_at->isFuture(),
        );
    }

    protected function isOpen(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->is_published && $this->starts_at->isPast() && $this->ends_at->isFuture(),
        );
    }

    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->is_published && $this->ends_at->isPast(),
        );
    }

    protected function isClosed(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->status === ElectionStatus::CLOSED,
        );
    }

    protected function isBoothUpcoming(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->is_published && $this->booth_starts_at?->isFuture(),
        );
    }

    protected function isBoothOpen(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->is_published && $this->booth_starts_at?->isPast() && $this->booth_ends_at?->isFuture(),
        );
    }

    protected function isBoothExpired(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->is_published && $this->booth_ends_at?->isPast(),
        );
    }

    protected function isCompleted(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->status === ElectionStatus::COMPLETED,
        );
    }

    protected function isCancelled(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->status === ElectionStatus::CANCELLED,
        );
    }

    protected function isPaid(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => filled($this->paid_at),
        );
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(related: Organisation::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(related: ElectionPlan::class);
    }

    public function collaborationInvitations(): HasMany
    {
        return $this->hasMany(related: ElectionUserInvitation::class);
    }

    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(related: User::class)
            ->as(accessor: 'collaboration')
            ->using(class: ElectionUser::class)
            ->withPivot(columns: ['designation', 'permissions'])
            ->withTimestamps();
    }

    public function segments(): HasMany
    {
        return $this->hasMany(related: Segment::class);
    }

    public function electors(): MorphMany
    {
        return $this->morphMany(
            related: Elector::class,
            name: 'event',
        );
    }

    public function positions(): MorphMany
    {
        return $this
            ->morphMany(
                related: Position::class,
                name: 'event',
            )
            ->oldest(column: 'sort');
    }

    public function candidateGroups(): HasMany
    {
        return $this->hasMany(related: CandidateGroup::class);
    }

    public function monitorTokens(): HasMany
    {
        return $this->hasMany(related: ElectionMonitorToken::class);
    }

    public function boothTokens(): HasMany
    {
        return $this->hasMany(related: ElectionBoothToken::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(related: ElectionResult::class)
            ->latestOfMany();
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'cancelled_at');
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'closed_at')
            ->whereNull(columns: 'completed_at')
            ->whereNull(columns: 'cancelled_at');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->whereNull(columns: 'published_at')
            ->whereNull(columns: 'cancelled_at');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'published_at')
            ->whereNull(columns: 'closed_at')
            ->whereNull(columns: 'cancelled_at');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'completed_at')
            ->whereNull(columns: 'cancelled_at');
    }

    public function scopeWhereUser(Builder $query, User $user): Builder
    {
        return $query->where(
            column: fn (Builder $query) => $query->where('owner_id', $user->getKey())
                ->orWhereHas(
                    relation: 'collaborators',
                    callback: fn (Builder $query) => $query->whereKey($user->getKey())
                )
        );
    }

    protected static function booted(): void
    {
        static::creating(callback: function (Election $election) {
            if (blank($election->code)) {
                $election->code = static::generateCode();
            }
        });

        static::created(callback: function (Election $election) {
            $election->collaborators()->attach(
                $election->owner,
                [
                    'designation' => 'Admin',
                    'permissions' => CollaboratorPermissionsData::empty(),
                ]
            );
        });

        static::deleting(callback: function (Election $election) {
            $election->electors()->cursor()->each->delete();
            $election->positions()->cursor()->each->delete();
            $election->candidateGroups()->cursor()->each->delete();
            $election->monitorTokens()->cursor()->each->delete();
            $election->boothTokens()->cursor()->each->delete();
            $election->result()->cursor()->each->delete();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function getFallbackLocale()
    {
        return $this->locales()[0] ?? config('app.locale');
    }

    public static function generateCode(): string
    {
        return config(key: 'app.election.code.prefix').
            Str::upper(value: Str::random(length: config(key: 'app.election.code.length')));
    }

    public function isTimingConfigured(): bool
    {
        return filled(value: $this->starts_at) &&
            filled(value: $this->ends_at) &&
            filled(value: $this->timezone) &&
            (
                ! $this->isBoothVotingEnabled() ||
                $this->isBoothTimingConfigured()
            );
    }

    public function isBoothTimingConfigured(): bool
    {
        return filled(value: $this->booth_starts_at) &&
            filled(value: $this->booth_ends_at) &&
            filled(value: $this->timezone);
    }

    public function isMfaRequired(): bool
    {
        return $this->preference->mfa_sms || $this->preference->mfa_mail;
    }

    public function isMfaSmsAutoFillOnly(): bool
    {
        return $this->preference->mfa_sms_auto_fill_only;
    }

    public function isPwaEnabled(): bool
    {
        return filled($this->preference?->web_app_manifest);
    }

    public function isBoothVotingEnabled(): bool
    {
        return $this->preference?->booth_voting ?? false;
    }

    public function isCheckoutRequired(): bool
    {
        return blank($this->paid_at);
    }

    public function isOwner(User|Authenticatable $user): bool
    {
        return $this->owner_id === $user->getKey();
    }

    public function getCollaboratorPermissions(User $user): CollaboratorPermissionsData
    {
        return $this->collaborators()
            ->wherePivot('user_id', $user->getKey())
            ->first()?->collaboration->permissions ?? CollaboratorPermissionsData::from();
    }

    public function getPendingStep(): ?ElectionSetupStep
    {
        return match (true) {
            ! $this->is_draft => null,
            blank($this->preference) || blank($this->plan_id) => ElectionSetupStep::Preference,
            $this->electors()->count() === 0 => ElectionSetupStep::Electors,
            empty($positionsCount = $this->positions()->count()) ||
            $this->positions()
                ->whereHas(
                    relation: 'candidates',
                    count: DB::raw(value: 'positions.quota')
                )
                ->count() < $positionsCount => ElectionSetupStep::Ballot,
            ! $this->isTimingConfigured() => ElectionSetupStep::Timing,
            $this->isCheckoutRequired() => ElectionSetupStep::Payment,
            ! $this->isPublished() => ElectionSetupStep::Publish,
            default => null,
        };
    }

    public function getElectorGroups(): array
    {
        return $this
            ->electors()
            ->select(columns: ['groups'])
            ->whereNotNull(columns: 'groups')
            ->distinct()
            ->pluck(column: 'groups')
            ->map(callback: fn (string $item): array => explode(separator: ',', string: $item))
            ->flatten()
            ->unique()
            ->toArray();
    }

    public function cancel(): bool
    {
        return $this->touch(attribute: 'cancelled_at');
    }

    public function close(): bool
    {
        return $this->touch(attribute: 'closed_at');
    }

    public function publish(): bool
    {
        return $this->touch(attribute: 'published_at');
    }

    public function generateResult(): void
    {
        $result = $this->result()->create([
            'total_votes' => Vote::query()
                ->whereHas(
                    relation: 'position',
                    callback: fn (Builder $query) => $query->where('event_id', $this->getKey())
                )
                ->count(),
        ]);

        $data = Candidate::query()
            ->whereHas(
                relation: 'position',
                callback: fn (Builder $query) => $query->where('event_id', $this->getKey())
            )
            ->pluck(column: 'uuid', key: 'uuid')
            ->map(callback: fn ($item) => 0)
            ->toArray();

        Vote::query()
            ->whereHas(
                relation: 'position',
                callback: fn (Builder $query) => $query->where('event_id', $this->getKey())
            )
            ->chunkById(
                count: 300,
                callback: function (Collection $votes) use (&$data, $result) {
                    $votes->each(callback: function (Vote $vote) use (&$data) {
                        $vote->secret->each(callback: function (VoteSecretData $secret) use (&$data) {
                            $data[$secret->key] ??= 0;
                            $data[$secret->key] += $secret->value;
                        });
                    });

                    $result->increment(column: 'processed_votes', amount: $votes->count());
                }
            );

        $result->meta = [];
        foreach ($data as $key => $value) {
            $result->meta[] = new ResultMetaData(key: $key, value: $value);
        }
        $result->completed_at = now();
        $result->save();

        $this->touch(attribute: 'completed_at');

        $data = Arr::sort(array: $data);
        $data = array_keys(array: $data);
        $data = array_reverse(array: $data);
        Candidate::query()
            ->whereHas(
                relation: 'position',
                callback: fn (Builder $query) => $query->where('event_id', $this->getKey())
            )
            ->get()
            ->each(callback: fn (Candidate $candidate) => $candidate->update(attributes: ['rank' => array_search(needle: $candidate->uuid, haystack: $data) + 1]));
    }

    public function replicateElectors(Election $from): void
    {
        $from->electors()
            ->chunkById(
                count: 300,
                callback: function (Collection $electors) {
                    $electors->each(
                        callback: fn (Elector $elector) => $this->electors()
                            ->create(
                                attributes: $elector
                                    ->replicate(except: [
                                        'current_session_id',
                                        'event_id',
                                        'event_type',
                                        'full_name',
                                        'short_code',
                                    ])
                                    ->toArray()
                            )
                    );
                }
            );
    }

    public function replicateBallotSetup(Election $from): void
    {
        $from->positions()
            ->cursor()
            ->each(
                callback: function (Position $position) {
                    $replicaPosition = $this->positions()
                        ->create(
                            attributes: $position
                                ->replicate(except: [
                                    'event_id',
                                    'event_type',
                                ])
                                ->toArray()
                        );

                    $position->candidates()
                        ->cursor()
                        ->each(
                            callback: function (Candidate $candidate) use ($replicaPosition) {
                                $replicaCandidate = $replicaPosition->candidates()
                                    ->create(
                                        attributes: $candidate
                                            ->replicate(except: [
                                                'elector_id',
                                                'full_name',
                                                'position_id',
                                                'candidate_group_id',
                                            ])
                                            ->toArray()
                                    );

                                if ($this->preference->candidate_group && filled($candidate->candidate_group_id)) {
                                    $candidateGroup = $this->candidateGroups()
                                        ->where('name', $candidate->candidateGroup->name)
                                        ->where('short_name', $candidate->candidateGroup->short_name)
                                        ->firstOr(fn () => $this->candidateGroups()->create(
                                            attributes: $candidate->candidateGroup
                                                ->replicate(except: [
                                                    'election_id',
                                                ])
                                                ->toArray()
                                        ));

                                    $replicaCandidate->update(attributes: ['candidate_group_id' => $candidateGroup->getKey()]);
                                }

                                if ($this->preference->candidate_photo) {
                                    $candidate->getMedia(collectionName: Candidate::MEDIA_COLLECTION_PHOTO)
                                        ->each(
                                            callback: function (Media $media) use ($replicaCandidate) {
                                                $replicaCandidate->addMedia(file: $media->getPath())
                                                    ->preservingOriginal()
                                                    ->toMediaCollection(
                                                        collectionName: Candidate::MEDIA_COLLECTION_PHOTO,
                                                        diskName: config('filament.default_filesystem_disk'),
                                                    );
                                            }
                                        );
                                }

                                if ($this->preference->candidate_symbol) {
                                    $candidate->getMedia(collectionName: Candidate::MEDIA_COLLECTION_SYMBOL)
                                        ->each(
                                            callback: function (Media $media) use ($replicaCandidate) {
                                                $replicaCandidate->addMedia(file: $media->getPath())
                                                    ->preservingOriginal()
                                                    ->toMediaCollection(
                                                        collectionName: Candidate::MEDIA_COLLECTION_SYMBOL,
                                                        diskName: config('filament.default_filesystem_disk'),
                                                    );
                                            }
                                        );
                                }
                            }
                        );
                }
            );
    }

    public function checkout(User $user): Checkout
    {
        $electorsCount = $this->electors()->count();

        $plan = $this->plan;

        $items = collect(value: [
            [
                'quantity' => 1,
                'price_data' => [
                    'currency' => $plan->currency,
                    'unit_amount' => $plan->base_fee,
                    'product_data' => [
                        'name' => "$plan->name - Base fee",
                    ],
                ],
            ],
            [
                'quantity' => $electorsCount,
                'price_data' => [
                    'currency' => $plan->currency,
                    'unit_amount' => $plan->elector_fee,
                    'product_data' => [
                        'name' => "$plan->name - Elector fee",
                    ],
                ],
            ],
        ]);

        $preference = $this->preference->all();
        $addOnFeatureFee = $plan->addOnFeatures()
            ->filter(fn (PlanFeatureData $feature) => $preference[$feature->feature->getPreferenceKey()] ?? false)
            ->sum(fn (PlanFeatureData $feature) => $feature->feature_fee);
        $addOnElectorFee = $plan->addOnFeatures()
            ->filter(fn (PlanFeatureData $feature) => $preference[$feature->feature->getPreferenceKey()] ?? false)
            ->sum(fn (PlanFeatureData $feature) => $feature->elector_fee);

        if ($addOnFeatureFee > 0) {
            $items->push([
                'quantity' => 1,
                'price_data' => [
                    'currency' => $plan->currency,
                    'unit_amount' => $addOnFeatureFee,
                    'product_data' => [
                        'name' => 'Add-ons - Feature fee',
                    ],
                ],
            ]);
        }
        if ($addOnElectorFee > 0) {
            $items->push([
                'quantity' => $electorsCount,
                'price_data' => [
                    'currency' => $plan->currency,
                    'unit_amount' => $addOnElectorFee,
                    'product_data' => [
                        'name' => 'Add-ons - Elector fee',
                    ],
                ],
            ]);
        }

        return $user
            ->allowPromotionCodes()
            ->collectTaxIds()
            ->checkout(
                items: $items->toArray(),
                sessionOptions: [
                    'success_url' => route(name: 'checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route(name: 'checkout.cancel').'?session_id={CHECKOUT_SESSION_ID}',
                    'automatic_tax' => [
                        'enabled' => true,
                    ],
                    'billing_address_collection' => 'required',
                    'customer_update' => [
                        'address' => 'auto',
                    ],
                    'invoice_creation' => [
                        'enabled' => true,
                        'invoice_data' => [
                            'metadata' => [
                                'related_type' => 'election',
                                'related_id' => $this->getKey(),
                            ],
                        ],
                    ],
                    'metadata' => [
                        'related_type' => 'election',
                        'related_id' => $this->getKey(),
                    ],
                ],
            );
    }
}
