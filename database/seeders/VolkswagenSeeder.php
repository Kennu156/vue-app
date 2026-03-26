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
                'image'       => null,
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
                'image'       => null,
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
                'image'       => null,
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
                'image'       => null,
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
                'image'       => null,
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
                'image'       => null,
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
                'image'       => null,
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
                'image'       => null,
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
                'image'       => null,
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
                'image'       => null,
            ],
        ];

        foreach ($cars as $car) {
            Volkswagen::create([...$car, 'user_id' => $user->id]);
        }

        $this->command->info('10 Volkswagenit lisatud!');
    }
}