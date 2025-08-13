<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroSectionResource\Pages;
use App\Filament\Resources\HeroSectionResource\RelationManagers;
use App\Models\HeroSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HeroSectionResource extends Resource
{
    protected static ?string $model = HeroSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationLabel = 'Halaman Utama';
    
    protected static ?string $modelLabel = 'Halaman Utama';
    
    protected static ?string $pluralModelLabel = 'Halaman Utama';
    
    protected static ?string $navigationGroup = '🎯 Kelola Konten Website';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📝 Konten Halaman Utama')
                    ->description('Atur teks yang akan muncul di halaman utama website sekolah')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('🏫 Judul Utama')
                            ->required()
                            ->maxLength(255)
                            ->default('Welcome to SMA Tunas Harapan')
                            ->placeholder('Contoh: Selamat Datang di SMA Tunas Harapan')
                            ->helperText('Judul besar yang akan muncul di halaman utama')
                            ->hint('Maksimal 255 karakter'),
                            
                        Forms\Components\TextInput::make('subtitle')
                            ->label('📖 Subtitle/Slogan')
                            ->maxLength(255)
                            ->placeholder('Contoh: Membentuk Generasi Unggul & Berakhlak')
                            ->helperText('Kalimat pendek yang menjelaskan visi sekolah')
                            ->hint('Opsional, bisa dikosongkan'),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('📄 Deskripsi Sekolah')
                            ->rows(4)
                            ->maxLength(500)
                            ->placeholder('Contoh: Bersama kita menciptakan generasi muda yang berakhlakul mulia, berprestasi, dan siap menghadapi masa depan.')
                            ->helperText('Deskripsi singkat tentang sekolah yang akan menarik minat calon siswa')
                            ->hint('Maksimal 500 karakter'),
                    ])->columns(1)->collapsible(),
                    
                Forms\Components\Section::make('🔗 Tombol Aksi')
                    ->description('Atur tombol yang akan memudahkan pengunjung untuk melakukan aksi')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('primary_button_text')
                                ->label('📍 Teks Tombol Utama')
                                ->placeholder('Contoh: Daftar Sekarang')
                                ->helperText('Tombol utama dengan warna mencolok'),
                                
                            Forms\Components\TextInput::make('primary_button_url')
                                ->label('🔗 Link Tombol Utama')
                                ->placeholder('Contoh: #contact atau https://pendaftaran.com')
                                ->helperText('Kemana tombol akan mengarahkan pengunjung')
                                ->hint('Gunakan #contact untuk ke bagian kontak'),
                        ]),
                        
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('secondary_button_text')
                                ->label('🎥 Teks Tombol Kedua')
                                ->placeholder('Contoh: Lihat Video Profil')
                                ->helperText('Tombol kedua dengan style berbeda'),
                                
                            Forms\Components\TextInput::make('secondary_button_url')
                                ->label('🔗 Link Tombol Kedua')
                                ->placeholder('Contoh: https://youtube.com/watch?v=...')
                                ->helperText('Link video YouTube atau halaman lain'),
                        ]),
                    ])->collapsible(),
                    
                Forms\Components\Section::make('🎨 Tampilan & Warna')
                    ->description('Sesuaikan warna dan gambar latar belakang halaman utama')
                    ->schema([
                        Forms\Components\FileUpload::make('background_image')
                            ->label('🖼️ Gambar Latar Belakang')
                            ->image()
                            ->directory('hero-sections')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                            ])
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->helperText('Upload gambar untuk latar belakang (opsional). Ukuran maksimal 5MB. Rasio 16:9 direkomendasikan untuk hasil terbaik.')
                            ->hint('Format: JPG, PNG, WEBP. Resolusi minimal: 1920x1080px'),
                            
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\ColorPicker::make('background_color')
                                ->label('🎨 Warna Latar Belakang')
                                ->default('#1e3a8a')
                                ->helperText('Pilih warna jika tidak menggunakan gambar')
                                ->hint('Klik kotak untuk memilih warna'),
                                
                            Forms\Components\ColorPicker::make('text_color')
                                ->label('✏️ Warna Teks')
                                ->default('#ffffff')
                                ->helperText('Pastikan teks mudah dibaca')
                                ->hint('Putih untuk latar gelap, hitam untuk latar terang'),
                        ]),
                    ])->collapsible(),
                    
                Forms\Components\Section::make('⚙️ Pengaturan')
                    ->description('Pengaturan lanjutan untuk menampilkan halaman utama')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Toggle::make('is_active')
                                ->label('✅ Aktifkan Halaman Ini')
                                ->default(true)
                                ->helperText('Matikan jika ingin menyembunyikan halaman ini')
                                ->hint('Hanya halaman aktif yang akan ditampilkan'),
                                
                            Forms\Components\TextInput::make('sort_order')
                                ->label('📊 Urutan Tampil')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->maxValue(10)
                                ->helperText('Angka 1 akan muncul pertama')
                                ->hint('1=Pertama, 2=Kedua, dst'),
                        ]),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('🏫 Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }
                        return null;
                    }),
                    
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('📖 Subtitle')
                    ->limit(30)
                    ->placeholder('Tidak ada subtitle')
                    ->searchable(),
                    
                Tables\Columns\ImageColumn::make('background_image')
                    ->label('🖼️ Gambar')
                    ->circular()
                    ->defaultImageUrl(function () {
                        return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>');
                    })
                    ->tooltip('Klik untuk melihat preview gambar'),
                    
                Tables\Columns\ColorColumn::make('background_color')
                    ->label('🎨 Warna')
                    ->tooltip('Warna latar belakang'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('📊 Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($state): string => $state ? 'Aktif - Ditampilkan di website' : 'Nonaktif - Disembunyikan'),
                    
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('📋 Urutan')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('Urutan tampil di website'),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('⏰ Terakhir Diubah')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->tooltip('Kapan terakhir kali diubah')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('📊 Status Tampil')
                    ->placeholder('🔍 Semua Status')
                    ->trueLabel('✅ Hanya yang Aktif')
                    ->falseLabel('❌ Hanya yang Nonaktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('✏️ Edit')
                    ->tooltip('Klik untuk mengubah konten'),
                Tables\Actions\ViewAction::make()
                    ->label('👁️ Lihat')
                    ->tooltip('Klik untuk melihat detail'),
                Tables\Actions\DeleteAction::make()
                    ->label('🗑️ Hapus')
                    ->tooltip('Hati-hati! Aksi ini tidak dapat dibatalkan')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Halaman Utama?')
                    ->modalDescription('Apakah Anda yakin ingin menghapus halaman utama ini? Data yang sudah dihapus tidak dapat dikembalikan.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('🗑️ Hapus yang Dipilih')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Halaman yang Dipilih?')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua halaman yang dipilih?'),
                ]),
            ])
            ->defaultSort('sort_order')
            ->emptyStateHeading('📄 Belum Ada Halaman Utama')
            ->emptyStateDescription('Buat halaman utama pertama untuk website sekolah')
            ->emptyStateIcon('heroicon-o-home')
            ->striped();
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
            'index' => Pages\ListHeroSections::route('/'),
            'create' => Pages\CreateHeroSection::route('/create'),
            'edit' => Pages\EditHeroSection::route('/{record}/edit'),
        ];
    }
}
