<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LemonSqueezySubscriptionResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LemonSqueezySubscriptionResource extends Resource
{
    protected static ?string $model = \LemonSqueezy\Laravel\Subscription::class;

    protected static ?string $modelLabel = 'LemonSqueezy Subscriptions';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Payments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLemonSqueezySubscriptions::route('/'),
            'create' => Pages\CreateLemonSqueezySubscription::route('/create'),
            'view' => Pages\ViewLemonSqueezySubscription::route('/{record}'),
            'edit' => Pages\EditLemonSqueezySubscription::route('/{record}/edit'),
        ];
    }
}
