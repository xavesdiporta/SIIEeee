<?php

namespace App\Filament\Widgets;

use App\Models\ProgressNote;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestProgressNotes extends BaseWidget
{
    // Faz a tabela ocupar a largura toda
    protected int | string | array $columnSpan = 'full';

    // Define a ordem na dashboard (aparece depois das estatísticas)
    protected static ?int $sort = 2;

    // Título da tabela
    protected static ?string $heading = 'Propostas Pendentes de Aprovação';

    public function table(Table $table): Table
    {
        return $table
            ->query(
            // Mostra apenas o que está Pendente, do mais recente para o mais antigo
                ProgressNote::query()->where('status', 'pending')->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Colaborador')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'físico' => 'success',
                        'afectivo' => 'danger',
                        'carácter' => 'info',
                        'espiritual' => 'primary',
                        'intelectual' => 'warning',
                        'social' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref.')
                    ->sortable(),

                Tables\Columns\TextColumn::make('note')
                    ->label('Proposta / Nota')
                    ->limit(60)
                    ->tooltip(fn (ProgressNote $record): string => $record->note ?? ''),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                // Botão ACEITAR
                Tables\Actions\Action::make('approve')
                    ->label('Aceitar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->button()
                    ->requiresConfirmation()
                    ->action(fn (ProgressNote $record) => $record->update(['status' => 'approved'])),

                // Botão RECUSAR
                Tables\Actions\Action::make('reject')
                    ->label('Recusar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->button()
                    ->requiresConfirmation()
                    ->action(fn (ProgressNote $record) => $record->update(['status' => 'rejected'])),
            ]);
    }
}
