<?php

namespace App\Livewire;

use App\Models\ProgressNote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Importante para debug
use Livewire\Component;
use Livewire\Attributes\On;

class ContentModal extends Component
{
    public bool $show = false;
    public string $category = '';
    public string $content = '';
    public string $reference = '';
    public string $color = '#3E2D1B';
    public string $note = '';

    #[On('open-content-modal')]
    public function open($category, $content, $reference = null, $color = '#3E2D1B')
    {
        Log::info("A tentar abrir modal: Cat: $category, Ref: " . ($reference ?? 'NULL'));

        $this->category = $category;
        $this->content = $content;
        // Se a referência vier nula (esqueceu-se no blade), usamos um fallback ou string vazia
        $this->reference = $reference ?? 'GERAL';
        $this->color = $color;

        if (Auth::check()) {
            $existingNote = ProgressNote::where('user_id', Auth::id())
                ->where('reference', $this->reference)
                ->first();

            $this->note = $existingNote ? $existingNote->note : '';
            Log::info("Nota carregada: " . $this->note);
        }

        $this->show = true;
    }

    public function saveNote()
    {
        Log::info("A tentar guardar nota. Ref: {$this->reference}, Texto: {$this->note}");

        if (!Auth::check()) {
            Log::warning("Utilizador não autenticado a tentar guardar.");
            return;
        }

        if (empty($this->reference)) {
            Log::error("Referência vazia ao guardar.");
            return;
        }

        try {
            ProgressNote::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'reference' => $this->reference,
                ],
                [
                    'category' => $this->category,
                    'proposal' => $this->content,
                    'note' => $this->note,
                ]
            );
            Log::info("Nota guardada com sucesso na BD.");
        } catch (\Exception $e) {
            Log::error("Erro ao guardar nota: " . $e->getMessage());
        }

        // Mantemos o show = false aqui também para garantir o estado do servidor
        $this->show = false;
    }

    public function close()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.content-modal');
    }
}
