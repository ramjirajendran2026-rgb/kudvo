<?php

namespace App\Models;

use App\Data\Election\PlanFeatureData;
use App\Enums\ElectionFeature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\LaravelData\DataCollection;
use Spatie\Translatable\HasTranslations;

class ElectionPlan extends Model
{
    use HasTranslations;
    use SoftDeletes;

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

    public array $translatable = [
        'name',
        'description',
    ];

    public function hasFeature(ElectionFeature $feature): bool
    {
        return $this->features
            ->toCollection()
            ->contains(fn (PlanFeatureData $planFeature) => $planFeature->feature === $feature);
    }

    public function hasAnyFeature(array $features): bool
    {
        return $this->features
            ->toCollection()
            ->contains(fn (PlanFeatureData $planFeature) => in_array($planFeature->feature, $features));
    }

    public function selfFeatures(): Collection
    {
        return $this->features->toCollection()->where('is_add_on', false);
    }

    public function addOnFeatures(): Collection
    {
        return $this->features->toCollection()->where('is_add_on', true);
    }

    public function hasAddOnFeature(ElectionFeature $feature): bool
    {
        return $this
            ->addOnFeatures()
            ->contains(fn (PlanFeatureData $planFeature) => $planFeature->feature === $feature);
    }

    public function getFeatureFee(ElectionFeature $feature): int
    {
        return $this
            ->features
            ->toCollection()
            ->where('feature', $feature)
            ->first()
            ?->feature_fee ?? 0;
    }

    public function getElectorFee(ElectionFeature $feature): int
    {
        return $this
            ->features
            ->toCollection()
            ->where('feature', $feature)
            ->first()
            ?->elector_fee ?? 0;
    }
}
