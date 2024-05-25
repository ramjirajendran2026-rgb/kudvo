<?php

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'organisations',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'code')->unique();
                $table->json(column: 'name')->nullable();
                $table->string(column: 'country')->nullable();
                $table->string(column: 'timezone')->nullable();

                $table->timestamps();
            },
        );

        Schema::create(
            table: 'organisation_user',
            callback: function (Blueprint $table): void {
                $table->string(column: 'role')->nullable();

                $table->foreignIdFor(model: Organisation::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(model: User::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'organisation_user');

        Schema::dropIfExists(table: 'organisations');
    }
};
