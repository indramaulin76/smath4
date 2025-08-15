<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        Contact::create([
            'address' => 'Komplek BNI 46, Pesing, Wijaya Kusuma, Jakarta Barat',
            'phone' => '(021) 5660066',
            'email' => 'Smatunasharapanadm@gmail.com',
            'website' => 'https://www.smatunasharapan.sch.id',
            'map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.817702041258!2d106.76924517592815!3d-6.155164760331785!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f7005875f537%3A0x1572a91c0e6bf97d!2sSMA%20Tunas%20Harapan!5e0!3m2!1sid!2sid!4v1753807208956!5m2!1sid!2sid" width="100%" height="290" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
            'opening_hours' => "Senin - Jumat: 07:00 - 16:00\nSabtu: 07:00 - 12:00\nMinggu: Libur",
            'is_active' => true,
        ]);
    }
}Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    }
}
