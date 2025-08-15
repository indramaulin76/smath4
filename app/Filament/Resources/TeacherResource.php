<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationLabel = 'Data Guru';
    
    protected static ?string $modelLabel = 'Guru';
    
    protected static ?string $pluralModelLabel = 'Data Guru';
    
    protected static ?string $navigationGroup = '👥 Data Sekolah';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('👨‍🏫 Informasi Guru')
                    ->description('Masukkan data lengkap guru untuk ditampilkan di website')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('📝 Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Dr. Ahmad Sulaiman, S.Pd, M.Pd')
                            ->helperText('Nama lengkap guru beserta gelar (jika ada)'),
                            
                        Forms\Components\TextInput::make('title')
                            ->label('📚 Mata Pelajaran / Jabatan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Matematika / Wali Kelas XII IPA 1')
                            ->helperText('Mata pelajaran yang diajar atau jabatan di sekolah'),
                            
                        Forms\Components\FileUpload::make('photo')
                            ->label('📸 Foto Profil Guru')
                            ->image()
                            ->directory('teachers')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(3072)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',   // Square - RECOMMENDED
                                '4:5',   // Portrait
                                '3:4',   // Standard Portrait
                            ])
                            ->helperText('📋 REKOMENDASI FOTO GURU: Rasio 1:1 (Square), Resolusi 400x400px minimum, Max 3MB, Format JPG/PNG/WEBP, Background polos/formal, Pencahayaan terang dan jelas')
                            ->columnSpanFull()
                            ->nullable(),
                            
                        Forms\Components\RichEditor::make('description')
                            ->label('📋 Deskripsi / Bio Singkat')
                            ->columnSpanFull()
                            ->placeholder('Contoh: Guru Matematika berpengalaman 10 tahun, lulusan UGM, aktif dalam penelitian pendidikan...')
                            ->helperText('Ceritakan pengalaman, prestasi, atau hal menarik tentang guru ini (opsional)')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->disk('public')
                    ->square(),
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
