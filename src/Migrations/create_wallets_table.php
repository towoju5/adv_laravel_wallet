<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->string('user_id')->nullable();
            $table->string('currency')->nullable();
            $table->string('balance')->nullable();
            $table->json('meta')->nullable();
            $table->string('title')->nullable();
            $table->boolean('is_department')->default(0);
            $table->string('department_id')->nullable();
            $table->string('role')->default('general');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallets');
    }
};
