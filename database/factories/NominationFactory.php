<?php

namespace Database\Factories;

use App\Models\Nomination;
use App\Models\Organisation;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NominationFactory extends Factory
{
    protected $model = Nomination::class;

    public function definition(): array
    {
        $tz = $this->faker->timezone();
        $publishedAt = CarbonImmutable::now(tz: $tz)->addWeek()->subDays(value: $this->faker->numberBetween(int2: 31));
        $createdAt = $publishedAt->subDays(value: $this->faker->numberBetween(int2: 7));
        $startsAt = $publishedAt->addDays(value: $this->faker->numberBetween(int1: 1, int2: 7));
        $endsAt = $startsAt->addDays(value: $this->faker->numberBetween(int1: 1, int2: 7));
        $withdrawalStartsAt = $endsAt->addDays(value: $this->faker->numberBetween(int1: 1, int2: 7));
        $withdrawalEndsAt = $withdrawalStartsAt->addDays(value: $this->faker->numberBetween(int1: 1, int2: 7));
        $closedAt = $withdrawalEndsAt->addDays(value: $this->faker->numberBetween(int1: 1, int2: 7));

        $selfNomination = $this->faker->boolean();

        return [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->text(),
            'self_nomination' => $selfNomination,
            'nominator_threshold' => $this->faker->numberBetween(int1: $selfNomination ? 0 : 1, int2: 3),
            'timezone' => $tz,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'withdrawal_starts_at' => $withdrawalStartsAt,
            'withdrawal_ends_at' => $withdrawalEndsAt,
            'published_at' => $publishedAt->isFuture() ? null : $publishedAt,
            'closed_at' => $closedAt->isFuture() ? null : $closedAt,
            'cancelled_at' => null,
            'created_at' => $createdAt->isFuture() ? CarbonImmutable::now(tz: $tz) : $createdAt,
            'updated_at' => Carbon::now(tz: $tz),

            'organisation_id' => Organisation::factory(),
        ];
    }
}
