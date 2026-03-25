<?php
namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Rekka T-särk',
                'description' => 'Mugav ja stiilne t-särk igapäevaseks kandmiseks.',
                'image' => 'https://img13.img-bcg.eu/h130/2bc0f4/s1/5933597.jpg',
                'price' => 19.99,
                'category' => 'riided',
            ],
            [
                'name' => 'Buss',
                'description' => 'Soe ja pehme buss külmemateks päevadeks.',
                'image' => 'https://gobus.ee/meedia/2021/11/Kuressaare-buss.jpg',
                'price' => 39.99,
                'category' => 'auto',
            ],
            [
                'name' => 'Tass',
                'description' => 'Keraamiline tass, ideaalne kohvi või tee nautimiseks.',
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7-UYYVnnb-RBh8A_b2GNFHhYy9n8jwt4Xnw&s',
                'price' => 9.99,
                'category' => 'asi',
            ],
            [
                'name' => 'Kott',
                'description' => 'Vastupidav ja stiilne kott igapäevaseks kasutamiseks.',
                'image' => 'https://www.ananke.ee/wp-content/uploads/2023/01/Kuldne-SV9-scaled.jpg',
                'price' => 29.99,
                'category' => 'asi',
            ],
            [
                'name' => 'Müts',
                'description' => 'Stiilne müts, mis kaitseb päikese eest.',
                'image' => 'https://funbox.ee/media/mf_webp/png/media/catalog/product/cache/441b20b75d6d2f0ed0624ad0dc9fd0bb/1/_/1_-_copy_292.webp',
                'price' => 14.99,
                'category' => 'riided',
            ],
            [
                'name' => 'Šokolaad',
                'description' => 'Pehmed ja mugavad šokolaadid igapäevaseks nautimiseks.',
                'image' => 'https://c8.alamy.com/comp/BF3YTH/close-up-of-an-eaten-chocolate-bar-BF3YTH.jpg',
                'price' => 7.99,
                'category' => 'toit',
            ],
            [
                'name' => 'Viiamari',
                'description' => 'Tugev viinamari, mis hoiab sind elus raskel ajal.',
                'image' => 'https://pahklid.ee/wp-content/uploads/2018/02/Rosin-Sultan.jpg',
                'price' => 24.99,
                'category' => 'toit',
            ],
            [
                'name' => 'Autovõtmed',
                'description' => 'Stiilsed autovõtmed, mis hoiab su võtmed ilusana korras.',
                'image' => 'https://www.carkeyglobal.com/media/catalog/product/cache/c1049177746d198ba33470ae764da926/i/m/image-cache-2024-volkswagen-touareg-autosleutel-700x700.jpg',
                'price' => 4.99,
                'category' => 'auto',
            ],
            [
                'name' => 'Kael',
                'description' => 'Elegantne kael, mis sobib igale inimesele.',
                'image' => 'https://www.drsteinbrech.com/wp-content/uploads/2017/08/neck-lift-different-men.jpg',
                'price' => 49.99,
                'category' => 'inimene',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('9 toodet lisatud!');
    }
}