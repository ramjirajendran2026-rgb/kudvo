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
            table: 'electors',
            callback: function (Blueprint $table): void {
                $table->id();
                $table->uuid()->unique();
                $table->string(column: 'short_code')->collation(collation: 'utf8mb4_bin');

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

                $table->string(column: 'current_session_id')->nullable();

                $table->morphs(name: 'event');

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'electors');
    }
};
