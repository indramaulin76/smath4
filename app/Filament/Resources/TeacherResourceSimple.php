<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeacherResourceSimple extends Resource
{
    protected static ?string $model = Teacher::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Data Guru (Simple)';
    protected static ?string $modelLabel = 'Guru';
    protected static ?string $pluralModelLabel = 'Data Guru';
    protected static ?string $navigationGroup = '👥 Data Sekolah';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('👨‍🏫 Informasi Guru')
                    ->description('Form sederhana untuk input data guru')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Dr. Ahmad Sulaiman, S.Pd, M.Pd'),
                            
                        Forms\Components\TextInput::make('title')
                            ->label('Mata Pelajaran / Jabatan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Matematika / Wali Kelas XII IPA 1'),
                            
                        Forms\Components\FileUpload::make('photo')
                            ->label('Foto Profil Guru')
                            ->image()
                            ->directory('teachers')
                            ->disk('public')
                            ->maxSize(3072)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->nullable(),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi / Bio Singkat')
                            ->rows(4)
                            ->placeholder('Ceritakan pengalaman, prestasi, atau hal menarik tentang guru ini')
                            ->nullable(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                    
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Jabatan'),
                    
                Tables\Columns\ImageColumn::make('photo')
                    ->disk('public')
                    ->square()
                    ->size(50)
                    ->label('Foto'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Dibuat'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
