<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Program;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('code')
                ->readOnly()
                ->required()
                ->default(fn() => 'TRX' . mt_rand(10000, 99999)),
            Forms\Components\Select::make('user_id')
                ->required()
                ->relationship('users', 'name'),
            // Forms\Components\TextInput::make('payment_method')
            //     ->maxLength(100),
            Forms\Components\TextInput::make('payment_status')
                ->readOnly()
                ->default('pending'),
            // Forms\Components\TextInput::make('payment_proof')
            //     ->maxLength(100),
            // Forms\Components\TextInput::make('user_id')
            //     ->required()
            //     ->maxLength(100),
            Forms\Components\Fieldset::make('Program')
                ->schema([
                    Forms\Components\Select::make('program_id')
                        ->required()
                        ->label('Program')
                        ->options(
                            Program::query()
                                ->get()
                                ->mapWithKeys(function ($program) {
                                    return [
                                        $program->id => $program->name . ' - Tingkat Sabuk(Geup): ' . $program->belt_levels
                                    ];
                                })
                                ->toArray())
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set){
                                    if($program = Program::find($state)) {
                                        $set('program_cost', $program->cost);
                                    }else{
                                        $set('program_cost', null);
                                    }
                                }),
                                Forms\Components\TextInput::make('program_cost')
                                    ->label('Cost')
                                    ->disabled(),
                ]),
        ]);
}


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label('Nama Anggota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state){
                        'pending' => 'warning',
                        'success' => 'success',
                        'failed' => 'danger',
                        'default' => 'secondary',
                    }),
                Tables\Columns\ImageColumn::make('payment_proof')
                    ->label('Bukti Pembayaran')
                    ->width(450)
                    ->height(225),
                Tables\Columns\TextColumn::make('programs.name')
                    ->label('Program')
                    ->searchable(),
                Tables\Columns\TextColumn::make('programs.belt_levels')
                    ->label('Level')
                    ->searchable(),
                Tables\Columns\TextColumn::make('programs.cost')
                    ->label('Iuran Bulanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
