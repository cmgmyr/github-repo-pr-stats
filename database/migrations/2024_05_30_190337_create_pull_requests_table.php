<?php
declare(strict_types=1);
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
        Schema::create('pull_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('number');
            $table->string('url');
            $table->string('title');
            $table->string('user');
            $table->dateTime('merged_at');
            $table->unsignedMediumInteger('merged_at_year');
            $table->unsignedInteger('total_files')->nullable();
            $table->unsignedInteger('total_additions')->nullable();
            $table->unsignedInteger('total_deletions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pull_requests');
    }
};
