<?php

use App\Models\AuthSession;
use App\Models\Ballot;
use App\Models\CandidateGroup;
use App\Models\Election;
use App\Models\Elector;
use App\Models\Organisation;
use App\Models\Position;
use App\Models\Segment;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'election_plans',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->json(column: 'name')->nullable();
                $table->text(column: 'description')->nullable();
                $table->char(column: 'currency', length: 3);
                $table->integer(column: 'base_fee')->default(value: 0);
                $table->integer(column: 'elector_fee')->default(value: 0);

                $table->json(column: 'features')->nullable();

                $table->integer(column: 'sort')->default(value: 0);

                $table->softDeletes();
                $table->timestamps();
            },
        );

        Schema::create(
            table: 'elections',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'code')->unique();
                $table->json(column: 'name')->nullable();
                $table->json(column: 'description')->nullable();

                $table->json(column: 'preference')->nullable();

                $table->string(column: 'short_code')->collation(collation: 'utf8mb4_bin')->nullable()->unique();

                $table->string(column: 'timezone')->nullable();
                $table->timestamp(column: 'starts_at')->nullable();
                $table->timestamp(column: 'ends_at')->nullable();
                $table->timestamp(column: 'booth_starts_at')->nullable();
                $table->timestamp(column: 'booth_ends_at')->nullable();

                $table->timestamp(column: 'published_at')->nullable();
                $table->timestamp(column: 'closed_at')->nullable();
                $table->timestamp(column: 'completed_at')->nullable();
                $table->timestamp(column: 'cancelled_at')->nullable();

                $table->timestamp(column: 'paid_at')->nullable();
                $table->string(column: 'invoice_status')->nullable();

                $table->string(column: 'stripe_invoice_id')->collation(collation: 'utf8_bin')->nullable()
                    ->unique();
                $table->json(column: 'stripe_invoice_data')->nullable();

                $table->foreignId(column: 'owner_id')->nullable()
                    ->constrained(table: 'users')->cascadeOnUpdate()->nullOnDelete();
                $table->foreignIdFor(model: Organisation::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId(column: 'plan_id')->nullable()
                    ->constrained(table: 'election_plans')->cascadeOnUpdate()->nullOnDelete();

                $table->timestamps();
            },
        );

        Schema::create(
            table: 'election_user',
            callback: function (Blueprint $table): void {
                $table->string(column: 'designation')->nullable();
                $table->json(column: 'permissions')->nullable();

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(model: User::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );

        Schema::create(
            table: 'election_user_invitations',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'email');
                $table->ulid(column: 'token')->unique();

                $table->string(column: 'designation')->nullable();
                $table->json(column: 'permissions')->nullable();

                $table->timestamp(column: 'accepted_at')->nullable();

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(model: User::class)->nullable()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->foreignId(column: 'invitor_id')->nullable()
                    ->constrained(table: 'users')->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
                $table->softDeletes();
            },
        );

        Schema::create(
            table: 'electors',
            callback: function (Blueprint $table): void {
                $table->id();
                $table->uuid()->unique();

                $table->string(column: 'membership_number');
                $table->string(column: 'title')->nullable();
                $table->string(column: 'first_name')->nullable();
                $table->string(column: 'last_name')->nullable();
                $table->string(column: 'full_name')
                    ->virtualAs(
                        expression: 'CONCAT_WS(" ", NULLIF(first_name, ""), NULLIF(last_name, ""))'
                    );
                $table->string(column: 'email')->nullable();
                $table->string(column: 'phone')->nullable();
                $table->longText(column: 'groups')->nullable();

                $table->string(column: 'short_code')->collation(collation: 'utf8mb4_bin')->nullable()->unique();

                $table->string(column: 'current_session_id')->nullable();

                $table->morphs(name: 'event');

                $table->timestamps();
            },
        );

        Schema::create(
            table: 'positions',
            callback: function (Blueprint $table): void {
                $table->id();
                $table->uuid()->unique();

                $table->string(column: 'name');
                $table->unsignedInteger(column: 'quota');
                $table->unsignedInteger(column: 'threshold');
                $table->json(column: 'elector_groups')->nullable();

                $table->integer(column: 'sort')->nullable();

                $table->morphs(name: 'event');

                $table->timestamps();
            },
        );

        Schema::create(
            table: 'candidate_groups',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'name', length: 100);
                $table->string(column: 'short_name', length: 10);

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();

                $table->unique(columns: ['election_id', 'short_name']);
                $table->unique(columns: ['election_id', 'name']);
            },
        );

        Schema::create(
            table: 'candidates',
            callback: function (Blueprint $table): void {
                $table->id();
                $table->uuid()->unique();

                $table->string(column: 'membership_number')->nullable();
                $table->string(column: 'title')->nullable();
                $table->string(column: 'first_name')->nullable();
                $table->string(column: 'last_name')->nullable();
                $table->string(column: 'full_name')
                    ->virtualAs(
                        expression: 'CONCAT_WS(" ", NULLIF(first_name, ""), NULLIF(last_name, ""))'
                    );
                $table->string(column: 'email')->nullable();
                $table->string(column: 'phone')->nullable();

                $table->integer(column: 'sort')->nullable();
                $table->integer(column: 'rank')->nullable();

                $table->foreignIdFor(model: Position::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(model: CandidateGroup::class)->nullable()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->foreignIdFor(model: Elector::class)->nullable()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();

                $table->timestamps();
            },
        );

        Schema::create(
            table: 'ballots',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'type');
                $table->ipAddress()->nullable();

                $table->timestamp(column: 'voted_at')->nullable();

                $table->boolean(column: 'mock')->default(value: false);

                $table->foreignIdFor(model: Elector::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(model: AuthSession::class)->nullable()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();

                $table->timestamps();
            },
        );

        Schema::create(
            table: 'votes',
            callback: function (Blueprint $table): void {
                $table->uuid(column: 'id')->primary();

                $table->foreignUuid(column: 'key')
                    ->constrained(table: 'positions', column: 'uuid')->cascadeOnUpdate()->cascadeOnDelete();
                $table->longText(column: 'secret')->nullable();

                $table->boolean(column: 'mock')->default(value: false);

                $table->foreignIdFor(model: Ballot::class)->nullable()
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            },
        );

        Schema::create(
            table: 'election_results',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->unsignedInteger(column: 'total_votes')->default(value: 0);
                $table->unsignedInteger(column: 'processed_votes')->default(value: 0);
                $table->timestamp(column: 'completed_at')->nullable();
                $table->longText(column: 'meta')->nullable();

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );

        Schema::create(
            table: 'election_monitor_tokens',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->uuid(column: 'key')->unique();
                $table->timestamp(column: 'activated_at')->nullable();
                $table->ipAddress()->nullable();
                $table->text(column: 'user_agent')->nullable();

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
                $table->softDeletes();
            },
        );

        Schema::create(
            table: 'election_booth_tokens',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->uuid(column: 'key')->unique();
                $table->timestamp(column: 'activated_at')->nullable();
                $table->ipAddress()->nullable();
                $table->text(column: 'user_agent')->nullable();

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
                $table->softDeletes();
            },
        );

        Schema::create(
            table: 'segments',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'name');

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();

                $table->unique(columns: ['election_id', 'name']);
            },
        );

        Schema::create(
            table: 'elector_segment',
            callback: function (Blueprint $table): void {
                $table->foreignIdFor(model: Elector::class);
                $table->foreignIdFor(model: Segment::class);

                $table->timestamps();
            },
        );

        Schema::create(
            table: 'position_segment',
            callback: function (Blueprint $table): void {
                $table->foreignIdFor(model: Position::class);
                $table->foreignIdFor(model: Segment::class);

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'position_segment');

        Schema::dropIfExists(table: 'elector_segment');

        Schema::dropIfExists(table: 'segments');

        Schema::dropIfExists(table: 'candidate_groups');

        Schema::dropIfExists(table: 'election_booth_tokens');

        Schema::dropIfExists(table: 'election_monitor_tokens');

        Schema::dropIfExists(table: 'election_results');

        Schema::dropIfExists(table: 'votes');

        Schema::dropIfExists(table: 'ballots');

        Schema::dropIfExists(table: 'candidates');

        Schema::dropIfExists(table: 'positions');

        Schema::dropIfExists(table: 'electors');

        Schema::dropIfExists(table: 'election_user_invitations');

        Schema::dropIfExists(table: 'election_user');

        Schema::dropIfExists(table: 'elections');

        Schema::dropIfExists(table: 'election_plans');
    }
};
