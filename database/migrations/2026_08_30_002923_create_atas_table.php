<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->date('dia');
            $table->string('ficheiro'); // caminho relativo dentro de public/, ex: "atas/reuniao-jan.pdf"
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // quem adicionou
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atas');
    }
};
