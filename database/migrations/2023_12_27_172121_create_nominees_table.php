<?php

use App\Models\Elector;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'nominees',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'membership_number');
                $table->string(column: 'title')->nullable();
                $table->string(column: 'first_name')->nullable();
                $table->string(column: 'last_name')->nullable();
                $table->string(column: 'full_name')
                    ->virtualAs(
                        expression: 'CONCAT_WS(" ", NULLIF(title, ""), NULLIF(first_name, ""), NULLIF(last_name, ""))'
                    );
                $table->string(column: 'email')->nullable();
                $table->string(column: 'phone')->nullable();

                $table->boolean(column: 'self_nomination');
                $table->string(column: 'status');
                $table->timestamp(column: 'decided_at')->nullable();
                $table->timestamp(column: 'scrutinised_at')->nullable();
                $table->timestamp(column: 'withdrawn_at')->nullable();

                $table->foreignIdFor(model: Position::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(model: Elector::class)->nullable()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->foreignIdFor(model: User::class, column: 'scrutiniser_id')->nullable()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'nominees');
    }
};
