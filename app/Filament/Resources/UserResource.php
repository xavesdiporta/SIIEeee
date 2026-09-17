<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\PermissionRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\RoleRelationManager;
use App\Filament\Resources\UserResource\Widgets\UsersStats;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informação Pessoal')
                    ->description('Dados de identificação do utilizador.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText('Deixa em branco para não alterar (edição).'),
                        Forms\Components\TextInput::make('cargo')
                            ->label('Cargo')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cne')
                            ->label('CNE Number')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('cp')
                            ->label('CP Number')
                            ->maxLength(13),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Secção & Permissões')
                    ->description('Define a secção do escuteiro e permissões de administrador.')
                    ->schema([
                        Forms\Components\Select::make('seccao')
                            ->label('Secção')
                            ->options([
                                'lobitos'      => '🟡 Lobitos (Alcateia)',
                                'exploradores' => '🟢 Exploradores (Expedição)',
                                'pioneiros'    => '🔵 Pioneiros (Comunidade)',
                                'cla'          => '🔴 Clã (Caminheiros)',
                            ])
                            ->default('cla')
                            ->required()
                            ->native(false),
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Administrador')
                            ->helperText('Admins têm acesso a todas as secções.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seccao')
                    ->label('Secção')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'lobitos'      => 'Lobitos',
                        'exploradores' => 'Exploradores',
                        'pioneiros'    => 'Pioneiros',
                        'cla'          => 'Clã',
                        default        => $state ?? 'Clã',
                    })
                    ->color(fn ($state) => match ($state) {
                        'lobitos'      => 'warning',
                        'exploradores' => 'success',
                        'pioneiros'    => 'info',
                        'cla'          => 'danger',
                        default        => 'gray',
                    }),
                Tables\Columns\IconColumn::make('trial_is_used')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->label('Admin')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->sortable()
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('stripe_id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('seccao')
                    ->label('Secção')
                    ->options([
                        'lobitos'      => 'Lobitos (Alcateia)',
                        'exploradores' => 'Exploradores (Expedição)',
                        'pioneiros'    => 'Pioneiros (Comunidade)',
                        'cla'          => 'Clã (Caminheiros)',
                    ])
                    ->placeholder('Todas as secções'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('seccao', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RoleRelationManager::class,
            PermissionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            UsersStats::class,
        ];
    }
}
