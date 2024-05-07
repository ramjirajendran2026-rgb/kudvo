<?php

namespace App\Models;

use App\Data\Election\PlanFeatureData;
use App\Enums\ElectionPlanFeatureType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelData\DataCollection;

class ElectionPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
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

    public function selfFeatures()
    {
        return $this->features->filter(fn (PlanFeatureData $feature) => $feature->type === ElectionPlanFeatureType::Self);
    }

    public function baseFeatures()
    {
        return $this->features->filter(fn (PlanFeatureData $feature) => $feature->type === ElectionPlanFeatureType::Self);
    }



    public function addOnFeatures()
    {
        return $this->features->filter(fn (PlanFeatureData $feature) => $feature->type === ElectionPlanFeatureType::AddOn);
    }
}
