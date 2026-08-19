<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 郷土料理。
     *
     * 中身は農林水産省「うちの郷土料理」から取り込む。載せるのは
     * 料理名・都道府県・主な伝承地域・主な使用食材と、農水省の該当ページへの
     * リンクだけ。由来や作り方の解説文は持ってこない。
     */
    public function up(): void
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('name', 120);
            $table->string('area', 20);
            $table->string('area_slug', 20)->index();
            $table->string('region', 160)->nullable();
            $table->string('ingredients', 200)->nullable();
            $table->string('source_url');
            $table->date('confirmed_on')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
