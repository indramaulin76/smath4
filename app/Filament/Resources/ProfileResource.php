<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileResource\Pages;
use App\Models\Profile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('School Name')
                            ->placeholder('SMA Tunas Harapan')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Short Description')
                            ->rows(2)
                            ->placeholder('Deskripsi singkat tentang sekolah')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('established_year')
                            ->label('Established Year')
                            ->placeholder('2010')
                            ->maxLength(4),
                        Forms\Components\TextInput::make('principal_name')
                            ->label('Principal Name')
                            ->placeholder('Nama Kepala Sekolah')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('About School')
                    ->schema([
                        Forms\Components\RichEditor::make('about')
                            ->label('About Us')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'link', 'bulletList', 'orderedList',
                                'h2', 'h3', 'paragraph', 'undo', 'redo',
                            ]),
                        Forms\Components\RichEditor::make('vision')
                            ->label('Vision')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'link', 'bulletList', 'orderedList',
                                'h2', 'h3', 'paragraph', 'undo', 'redo',
                            ]),
                        Forms\Components\RichEditor::make('mission')
                            ->label('Mission')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'link', 'bulletList', 'orderedList',
                                'h2', 'h3', 'paragraph', 'undo', 'redo',
                            ]),
                    ]),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->image()
                            ->directory('profile')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])
                            ->helperText('Gambar utama untuk profil sekolah'),
                        Forms\Components\FileUpload::make('gallery')
                            ->label('School Gallery')
                            ->image()
                            ->directory('profile/gallery')
                            ->disk('public')
                            ->visibility('public')
                            ->multiple()
                            ->maxFiles(15)
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])
                            ->helperText('Upload foto-foto sekolah (maksimal 15 foto)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Content Sections')
                    ->schema([
                        Forms\Components\Repeater::make('sections')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->placeholder('Contoh: Fasilitas Unggulan'),
                                Forms\Components\RichEditor::make('content')
                                    ->required()
                                    ->placeholder('Tulis konten untuk section ini...')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline', 'strike',
                                        'link', 'bulletList', 'orderedList',
                                        'paragraph', 'undo', 'redo',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->addActionLabel('Add Content Section')
                            ->helperText('Tambahkan section konten dengan judul dan paragraf')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Achievements & Facilities')
                    ->schema([
                        Forms\Components\Repeater::make('achievements')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->placeholder('Juara 1 Lomba...')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('year')
                                    ->required()
                                    ->placeholder('2024')
                                    ->maxLength(4),
                                Forms\Components\Textarea::make('description')
                                    ->placeholder('Deskripsi prestasi...')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Add Achievement')
                            ->helperText('Tambahkan prestasi-prestasi sekolah'),

                        Forms\Components\Repeater::make('facilities')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->placeholder('Laboratorium Komputer'),
                                Forms\Components\Textarea::make('description')
                                    ->placeholder('Deskripsi fasilitas...')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->addActionLabel('Add Facility')
                            ->helperText('Tambahkan fasilitas-fasilitas sekolah'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->disk('public')
                    ->square()
                    ->size(60),
                Tables\Columns\TextColumn::make('title')
                    ->label('School Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('established_year')
                    ->label('Est. Year')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('principal_name')
                    ->label('Principal')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProfiles::route('/'),
            'edit' => Pages\EditProfile::route('/{record}/edit'),
        ];
    }
}
