<?php

use App\Models\Election;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'election_preferences',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->boolean(column: 'eul_mail')->default(value: false);
                $table->boolean(column: 'eul_sms')->default(value: false);

                $table->boolean(column: 'mfa_mail')->default(value: false);
                $table->boolean(column: 'mfa_sms')->default(value: false);

                $table->boolean(column: 'voted_confirmation_mail')->default(value: false);
                $table->boolean(column: 'voted_confirmation_sms')->default(value: false);

                $table->boolean(column: 'voted_ballot_download')->default(value: false);
                $table->boolean(column: 'voted_ballot_mail')->default(value: false);

                $table->boolean(column: 'dnt_votes')->default(value: false);
                $table->boolean(column: 'voted_ballot_update')->default(value: false);

                $table->unsignedInteger(column: 'ip_restriction_threshold')->nullable();

                $table->string(column: 'candidate_sort')->nullable();
                $table->boolean(column: 'candidate_photo')->default(value: false);
                $table->boolean(column: 'candidate_bio')->default(value: false);
                $table->boolean(column: 'candidate_attachment')->default(value: false);

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'election_preferences');
    }
};
