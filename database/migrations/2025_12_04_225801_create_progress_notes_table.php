<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category'); // Ex: Físico
            $table->string('reference'); // Ex: F1, A3 (O número da sub coluna)
            $table->text('proposal'); // O texto original do quadrado
            $table->text('note')->nullable(); // A nota que o user escreveu
            $table->timestamps();

            // Garante que um user só tem uma nota por referência (F1, F2, etc)
            $table->unique(['user_id', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_notes');
    }
};
