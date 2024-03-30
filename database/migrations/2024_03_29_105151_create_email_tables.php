<?php

use App\Models\Email;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            table: 'emails',
            callback: function (Blueprint $table): void {
                $table->id();
                $table->string(column: 'message_id')->unique();

                $table->string(column: 'subject')->nullable();

                $table->string(column: 'to_address')->index();
                $table->string(column: 'to_name')->nullable();

                $table->string(column: 'from_address')->index();
                $table->string(column: 'from_name')->nullable();

                $table->timestamp(column: 'bounced_at')->nullable();
                $table->json(column: 'bounce_data')->nullable();

                $table->timestamp(column: 'complained_at')->nullable();
                $table->json(column: 'complaint_data')->nullable();

                $table->timestamp(column: 'delivered_at')->nullable();

                $table->timestamp(column: 'delivery_delayed_at')->nullable();
                $table->json(column: 'delivery_delay_data')->nullable();

                $table->timestamp(column: 'rejected_at')->nullable();
                $table->json(column: 'reject_data')->nullable();

                $table->timestamp(column: 'rendering_failed_at')->nullable();
                $table->json(column: 'rendering_failure_data')->nullable();

                $table->timestamp(column: 'sent_at')->nullable();

                $table->timestamp(column: 'subscription_notified_at')->nullable();
                $table->json(column: 'subscription_data')->nullable();

                $table->string(column: 'purpose')->nullable();
                $table->nullableMorphs(name: 'notifiable');

                $table->timestamps();
            },
        );

        Schema::create(
            table: 'email_opens',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->ipAddress()->nullable();
                $table->text(column: 'user_agent')->nullable();

                $table->timestamp(column: 'opened_at')->nullable();

                $table->foreignIdFor(model: Email::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );

        Schema::create(
            table: 'email_clicks',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->ipAddress()->nullable();
                $table->text(column: 'user_agent')->nullable();
                $table->text(column: 'link')->nullable();
                $table->json(column: 'link_tags')->nullable();

                $table->timestamp(column: 'clicked_at')->nullable();

                $table->foreignIdFor(model: Email::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'email_clicks');
        Schema::dropIfExists(table: 'email_opens');
        Schema::dropIfExists(table: 'emails');
    }
};
