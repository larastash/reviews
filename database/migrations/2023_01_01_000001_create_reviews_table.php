<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(config('reviews.table'), function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // $table->foreignUuid('user_id')->constrained()->cascadeOnDelete(); // uuid id
            $table->tinyInteger('value');
            $table->text('title')->nullable();
            $table->text('body')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(config('reviews.table'));
    }
}