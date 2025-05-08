<?php

namespace App\Models;

use App\Data\Election\PlanFeatureData;
use App\Enums\ElectionFeature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\LaravelData\DataCollection;
use Spatie\Translatable\HasTranslations;

class ElectionPlan extends Model
{
    use HasTranslations;
    use LogsActivity;
    use SoftDeletes;

    public array $translatable = [
        'name',
        'description',
    ];

    protected $fillable = [
        'name',
        'description',
        'currency',
        'base_fee',
        'elector_fee',
        'features',
    ];

    protected $casts = [
        'base_fee' => 'int',
        'elector_fee' => 'int',
        'features' => DataCollection::class . ':' . PlanFeatureData::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function hasFeature(ElectionFeature $feature): bool
    {
        return $this->getFeatures()
            ->contains(fn (PlanFeatureData $planFeature) => $planFeature->feature === $feature);
    }

    public function getFeatures(): Collection
    {
        return $this->features->toCollection()->filter();
    }

    public function hasAnyFeature(array $features): bool
    {
        return $this->getFeatures()
            ->contains(fn (PlanFeatureData $planFeature) => in_array($planFeature->feature, $features));
    }

    public function selfFeatures(): Collection
    {
        return $this->getFeatures()->where('is_add_on', false);
    }

    public function hasAddOnFeature(ElectionFeature $feature): bool
    {
        return $this
            ->addOnFeatures()
            ->contains(fn (PlanFeatureData $planFeature) => $planFeature->feature === $feature);
    }

    public function addOnFeatures(): Collection
    {
        return $this->getFeatures()->where('is_add_on', true);
    }

    public function getFeatureFee(ElectionFeature $feature): int
    {
        return $this->getFeature($feature)?->feature_fee ?? 0;
    }

    public function getFeature(ElectionFeature $feature): ?PlanFeatureData
    {
        return $this->getFeatures()->firstWhere('feature', $feature);
    }

    public function getElectorFee(ElectionFeature $feature): int
    {
        return $this->getFeature($feature)?->elector_fee ?? 0;
    }
}
