<?php

namespace App\Filament\Resources\AuctionResource\Pages;

use App\Filament\Resources\AuctionResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageAuction extends ManageRelatedRecords
{
    protected static string $resource = AuctionResource::class;

    protected static string $relationship = 'auctionBids';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Auction Bids';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->columnSpanFull()
                    ->prefix('$')
                    ->mask(RawJs::make('$money($input)'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->requiresConfirmation()
                    ->createAnother(false),
//                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DissociateAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DissociateBulkAction::make(),
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }
}
