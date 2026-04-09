<?php
namespace Database\Seeders;

use App\Models\Volkswagen;
use App\Models\User;
use Illuminate\Database\Seeder;

class VolkswagenSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->error('Kasutajat pole. Loo esmalt kasutaja.');
            return;
        }

        $cars = [
            [
                'title'       => 'VW Golf MK7 2.0 TDI',
                'description' => 'Suurepärases korras Golf MK7, täisvarustusega. Uued rehvid, hiljuti hooldatud. Üks omanik.',
                'year'        => 2016,
                'model'       => 'Golf',
                'engine'      => '2.0 TDI 150hp',
                'mileage'     => 145000,
                'price'       => 12500,
                'color'       => 'Must',
                'image'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/VW_Golf_GTD_%28VII%29_%E2%80%93_Frontansicht%2C_6._April_2014%2C_D%C3%BCsseldorf.jpg/250px-VW_Golf_GTD_%28VII%29_%E2%80%93_Frontansicht%2C_6._April_2014%2C_D%C3%BCsseldorf.jpg',
            ],
            [
                'title'       => 'VW Passat B8 2.0 TDI 4Motion',
                'description' => 'Passat B8 neljarataseveduga, nahkistmed, panoraamkatus, digitaalne armatuurlaud.',
                'year'        => 2018,
                'model'       => 'Passat',
                'engine'      => '2.0 TDI 190hp 4Motion',
                'mileage'     => 98000,
                'price'       => 22900,
                'color'       => 'Hõbedane',
                'image'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/df/Volkswagen_Passat_Variant_2.0_TDI_%28B8%2C_2018%29_%2852076031771%29.jpg/960px-Volkswagen_Passat_Variant_2.0_TDI_%28B8%2C_2018%29_%2852076031771%29.jpg',
            ],
            [
                'title'       => 'VW Polo 1.0 TSI Comfortline',
                'description' => 'Väike ja ökonoomne linnaauto. Kliimaseade, Apple CarPlay, parkimisandurid.',
                'year'        => 2019,
                'model'       => 'Polo',
                'engine'      => '1.0 TSI 95hp',
                'mileage'     => 62000,
                'price'       => 14800,
                'color'       => 'Valge',
                'image'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c6/2019_Volkswagen_Polo_1.6_MSi_Highline.jpg/960px-2019_Volkswagen_Polo_1.6_MSi_Highline.jpg',
            ],
            [
                'title'       => 'VW Tiguan 2.0 TSI 4Motion R-Line',
                'description' => 'Tiguan R-Line paketiga, panoraamkatus, LED tuled, 7-kohaline.',
                'year'        => 2020,
                'model'       => 'Tiguan',
                'engine'      => '2.0 TSI 190hp 4Motion',
                'mileage'     => 54000,
                'price'       => 31500,
                'color'       => 'Deep Black Pearl',
                'image'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/71/20_Volkswagen_Tiguan_%282020-2024%29_1.jpg/960px-20_Volkswagen_Tiguan_%282020-2024%29_1.jpg',
            ],
            [
                'title'       => 'VW Touareg 3.0 TDI V6',
                'description' => 'Luksuslik maastur täisvarustusega. Õhkvedrustus, massaažistmed, 360° kaamera.',
                'year'        => 2019,
                'model'       => 'Touareg',
                'engine'      => '3.0 TDI V6 286hp',
                'mileage'     => 87000,
                'price'       => 42000,
                'color'       => 'Ärihõbe',
                'image'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Volkswagen_Touareg_III_IMG_1991.jpg/960px-Volkswagen_Touareg_III_IMG_1991.jpg',
            ],
            [
                'title'       => 'VW Golf GTI MK8',
                'description' => 'Ikoonilise GTI uusim versioon. Sportlik juhtimine, DCC, Harman Kardon heli.',
                'year'        => 2022,
                'model'       => 'Golf',
                'engine'      => '2.0 TSI 245hp',
                'mileage'     => 28000,
                'price'       => 36900,
                'color'       => 'Tornado Red',
                'image'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/VOLKSWAGEN_GOLF_GTI_%28Mk8_CD1%29_China.jpg/960px-VOLKSWAGEN_GOLF_GTI_%28Mk8_CD1%29_China.jpg',
            ],
            [
                'title'       => 'VW ID.4 Pro Performance',
                'description' => 'Täiselektriline SUV. 520km sõiduulatus, kiirlaadimise tugi, augmented reality HUD.',
                'year'        => 2023,
                'model'       => 'ID.4',
                'engine'      => 'Elekter 204hp 77kWh',
                'mileage'     => 15000,
                'price'       => 44900,
                'color'       => 'Moonstone Grey',
                'image'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/38/Volkswagen_ID.4_GTX_IAA_2021_1X7A0247.jpg/960px-Volkswagen_ID.4_GTX_IAA_2021_1X7A0247.jpg',
            ],
            [
                'title'       => 'VW Caddy 2.0 TDI DSG',
                'description' => 'Praktilise pagasiruumiga kaubik, sobib nii äri kui pere tarbeks. DSG käigukast.',
                'year'        => 2021,
                'model'       => 'Caddy',
                'engine'      => '2.0 TDI 122hp DSG',
                'mileage'     => 73000,
                'price'       => 24500,
                'color'       => 'Reflex Silver',
                'image'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/2021_Volkswagen_Caddy_%2851161223218%29.jpg/960px-2021_Volkswagen_Caddy_%2851161223218%29.jpg',
            ],
            [
                'title'       => 'VW Arteon R-Line 2.0 TSI',
                'description' => 'Elegantne fastback, sportlik välimus. Nahkistmed, ambient valgustus, head-up display.',
                'year'        => 2021,
                'model'       => 'Arteon',
                'engine'      => '2.0 TSI 190hp',
                'mileage'     => 41000,
                'price'       => 33800,
                'color'       => 'Manganese Grey',
                'image'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/2021_Volkswagen_Arteon_Execline_4Motion%2C_Front_Right%2C_07-11-2021.jpg/960px-2021_Volkswagen_Arteon_Execline_4Motion%2C_Front_Right%2C_07-11-2021.jpg',
            ],
            [
                'title'       => 'VW Transporter T6.1 2.0 TDI',
                'description' => 'Töökindel kaubik ettevõtetele. Külgne liugdukse, laadimiskonksud, navigatsioon.',
                'year'        => 2020,
                'model'       => 'Transporter',
                'engine'      => '2.0 TDI 150hp',
                'mileage'     => 112000,
                'price'       => 28900,
                'color'       => 'Candy White',
                'image'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/VOLKSWAGEN_T6_TRANSPORTER_HONG_KONG.jpg/960px-VOLKSWAGEN_T6_TRANSPORTER_HONG_KONG.jpg',
            ],
        ];

        foreach ($cars as $car) {
            Volkswagen::create([...$car, 'user_id' => $user->id]);
        }

        \Illuminate\Support\Facades\Cache::forget('volkswagens-all');
        $this->command->info('10 Volkswagenit lisatud!');
    }
}