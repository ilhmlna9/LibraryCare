<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Complaint Categories
        Schema::create('complaint_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->default('folder');
            $table->string('color')->default('blue');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Complaints
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique()->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('visitor_name');
            $table->string('visitor_email');
            $table->string('visitor_phone')->nullable();
            $table->foreignId('category_id')->constrained('complaint_categories')->restrictOnDelete();
            $table->string('title');
            $table->longText('description');
            $table->string('attachment')->nullable();
            $table->enum('status', ['pending', 'processing', 'resolved', 'rejected'])->default('pending')->index();
            $table->enum('source', ['website', 'whatsapp', 'telegram', 'google_forms'])->default('website')->index();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Complaint Replies
        Schema::create('complaint_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('message');
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
        });

        // 4. Complaint Status Histories
        Schema::create('complaint_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Customer Satisfaction
        Schema::create('customer_satisfaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->unique()->constrained('complaints')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->tinyInteger('rating')->unsigned(); // 1 to 5
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // 6. Notification Logs
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->nullable()->constrained('complaints')->cascadeOnDelete();
            $table->enum('type', ['whatsapp', 'telegram', 'google_forms', 'email']);
            $table->string('recipient');
            $table->text('message');
            $table->enum('status', ['sent', 'failed', 'simulated'])->default('simulated');
            $table->text('response')->nullable();
            $table->timestamps();
        });

        // 7. Integration Settings
        Schema::create('integration_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_settings');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('customer_satisfaction');
        Schema::dropIfExists('complaint_status_histories');
        Schema::dropIfExists('complaint_replies');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('complaint_categories');
    }
};

