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

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    
    protected static ?string $navigationLabel = 'Profil Sekolah';
    
    protected static ?string $modelLabel = 'Profil Sekolah';
    
    protected static ?string $pluralModelLabel = 'Profil Sekolah';
    
    protected static ?string $navigationGroup = '🏫 Tentang Sekolah';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                // Basic Information Section
                Forms\Components\Section::make('Basic Information')
                    ->description('Informasi dasar tentang sekolah')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('school_name')
                                    ->label('School Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->default('SMA TUNAS HARAPAN'),
                                    
                                Forms\Components\TextInput::make('established_year')
                                    ->label('Established Year')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y'))
                                    ->default(1999),
                                    
                                Forms\Components\TextInput::make('principal_name')
                                    ->label('Principal Name')
                                    ->maxLength(255)
                                    ->placeholder('Nama Kepala Sekolah'),
                            ]),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Short Description')
                            ->rows(3)
                            ->maxLength(500)
                            ->hint('Deskripsi singkat tentang sekolah (maksimal 500 karakter)'),
                    ]),

                // About School Section
                Forms\Components\Section::make('About School')
                    ->description('Informasi lengkap tentang sekolah')
                    ->schema([
                        Forms\Components\RichEditor::make('about')
                            ->label('About Us')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'undo',
                                'redo',
                            ])
                            ->hint('Ceritakan sejarah dan profil lengkap sekolah'),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\RichEditor::make('vision')
                                    ->label('Vision')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'bulletList',
                                        'undo',
                                        'redo',
                                    ])
                                    ->hint('Visi sekolah'),
                                    
                                Forms\Components\RichEditor::make('mission')
                                    ->label('Mission')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'bulletList',
                                        'orderedList',
                                        'undo',
                                        'redo',
                                    ])
                                    ->hint('Misi sekolah'),
                            ]),
                    ]),

                // Media Section
                Forms\Components\Section::make('Media')
                    ->description('Gambar dan galeri sekolah')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->directory('profile')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(3072)
                            ->hint('Gambar utama untuk profil sekolah')
                            ->helperText('📐 Rekomendasi rasio: 16:9 (landscape) untuk tampilan terbaik. Ukuran maksimal 3MB. Resolusi ideal: 1920x1080px'),
                            
                        Forms\Components\FileUpload::make('gallery')
                            ->label('School Gallery')
                            ->directory('profile/gallery')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '3:2',
                                '1:1',
                            ])
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(15)
                            ->maxSize(3072)
                            ->hint('Upload foto-foto sekolah (maksimal 15 foto)')
                            ->helperText('📐 Rekomendasi rasio: 4:3 atau 16:9 untuk galeri. Resolusi ideal: 1200x900px (4:3) atau 1200x675px (16:9). Max 3MB per foto'),
                    ]),

                // Contact Information
                Forms\Components\Section::make('Contact Information')
                    ->description('Informasi kontak sekolah')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Textarea::make('address')
                                    ->label('Alamat')
                                    ->rows(3)
                                    ->maxLength(500),
                                    
                                Forms\Components\TextInput::make('phone')
                                    ->label('Telepon')
                                    ->tel()
                                    ->maxLength(20),
                                    
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('website')
                                    ->label('Website')
                                    ->url()
                                    ->maxLength(255),
                            ]),
                    ]),

                // Achievements & Facilities
                Forms\Components\Section::make('Achievements & Facilities')
                    ->description('Prestasi dan fasilitas sekolah')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Repeater::make('achievements')
                                    ->label('Achievements')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Prestasi')
                                            ->required(),
                                        Forms\Components\TextInput::make('year')
                                            ->label('Tahun')
                                            ->numeric(),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Deskripsi')
                                            ->rows(2),
                                    ])
                                    ->addActionLabel('Add Achievement')
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                                    
                                Forms\Components\Repeater::make('facilities')
                                    ->label('Facilities')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Fasilitas')
                                            ->required(),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Deskripsi')
                                            ->rows(2),
                                    ])
                                    ->addActionLabel('Add Facility')
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                            ]),
                    ]),
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Featured Image')
                    ->circular()
                    ->size(60),
                    
                Tables\Columns\TextColumn::make('school_name')
                    ->label('School Name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('established_year')
                    ->label('Established')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('principal_name')
                    ->label('Principal')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListProfiles::route('/'),
            'create' => Pages\CreateProfile::route('/create'),
            'edit' => Pages\EditProfile::route('/{record}/edit'),
        ];
    }
}
