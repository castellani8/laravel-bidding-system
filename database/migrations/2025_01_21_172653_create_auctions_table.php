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
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->jsonb('images');
            $table->jsonb('files')->nullable();
            $table->decimal('start_price', 15);
            $table->string('status')->comment('INACTIVE, ACTIVE, FINISHED');
            $table->date('starts_at')->nullable();
            $table->dateTime('ends_at');
            $table->foreignIdFor(\App\Models\User::class, 'created_by')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
