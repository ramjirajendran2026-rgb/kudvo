<?php

use App\Models\Nomination;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'nomination_preferences',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->boolean(column: 'mfa_mail')->default(value: false);
                $table->boolean(column: 'mfa_sms')->default(value: false);

                $table->foreignIdFor(model: Nomination::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'nomination_preferences');
    }
};
