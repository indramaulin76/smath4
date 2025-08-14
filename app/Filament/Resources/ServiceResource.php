<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Layanan Sekolah';
    
    protected static ?string $modelLabel = 'Layanan';
    
    protected static ?string $pluralModelLabel = 'Layanan Sekolah';
    
    protected static ?string $navigationGroup = '📰 Kelola Konten Website';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('📋 Judul Program')
                    ->required()
                    ->hint('Nama program unggulan (contoh: Program Olimpiade Sains)')
                    ->maxLength(255),
                    
                Forms\Components\FileUpload::make('image')
                    ->label('🖼️ Gambar Program')
                    ->directory('services')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3', 
                        '1:1',
                    ])
                    ->maxSize(3072)
                    ->hint('Upload gambar program (max 3MB). Aspect ratio 16:9 recommended.')
                    ->helperText('Gambar akan ditampilkan di halaman utama website'),
                    
                Forms\Components\Textarea::make('description')
                    ->label('📝 Deskripsi Program')
                    ->required()
                    ->rows(4)
                    ->hint('Jelaskan detail program unggulan sekolah')
                    ->maxLength(500),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('🖼️ Gambar')
                    ->circular()
                    ->size(60)
                    ->defaultImageUrl(url('images/services.jpg')),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('📋 Judul Program')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('📝 Deskripsi')
                    ->limit(80)
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Dibuat')
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
