<?php

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

                $table->boolean(column: 'eul_email')->default(value: false);
                $table->boolean(column: 'eul_sms')->default(value: false);

                $table->boolean(column: 'mfa_email')->default(value: false);
                $table->boolean(column: 'mfa_sms')->default(value: false);

                $table->boolean(column: 'ballot_ack_email')->default(value: false);
                $table->boolean(column: 'ballot_ack_sms')->default(value: false);

                $table->boolean(column: 'ip_restriction')->default(value: false);
                $table->unsignedInteger(column: 'ip_restriction_limit')->nullable();

                $table->boolean(column: 'self_ballot_email')->default(value: false);
                $table->boolean(column: 'self_ballot_download')->default(value: false);

                $table->boolean(column: 'dnt_votes')->default(value: false);
                $table->boolean(column: 'ballot_edit')->default(value: false);

                $table->string(column: 'candidate_sort')->nullable();
                $table->boolean(column: 'candidate_photo')->default(value: false);
                $table->boolean(column: 'candidate_bio')->default(value: false);
                $table->boolean(column: 'candidate_attachments')->default(value: false);
                $table->boolean(column: 'candidate_from_electors')->default(value: false);

                $table->foreignIdFor(model: Event::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
                $table->softDeletes();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'election_preferences');
    }
};
