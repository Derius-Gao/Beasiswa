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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('address')->nullable();
            $table->decimal('gpa', 3, 2)->nullable(); // Academic performance
            $table->string('major')->nullable();
            $table->string('economic_status')->nullable(); // e.g., 'low', 'medium', 'high'
            $table->json('payment_history')->nullable(); // Store payment history as JSON
            $table->boolean('is_student')->default(true);
            $table->string('student_id')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'birth_date', 'address', 'gpa', 'major', 'economic_status', 'payment_history', 'is_student', 'student_id']);
        });
    }
};
