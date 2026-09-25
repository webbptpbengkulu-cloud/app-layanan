<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Village;
use Illuminate\Database\Seeder;

class DistrictVillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            [
                'code' => '35.05.01',
                'name' => 'Wonotirto',
                'villages' => ['Wonotirto', 'Pasiraman', 'Gununggede', 'Tambakrejo', 'Kaligambir', 'Ngadipuro', 'Ngeni', 'Sumberboto'],
            ],
            [
                'code' => '35.05.02',
                'name' => 'Nglegok',
                'villages' => ['Nglegok', 'Modangan', 'Ngoran', 'Jiwut', 'Kedawung', 'Dayu', 'Penataran', 'Krenceng', 'Bangsri', 'Sumberasri', 'Kemloko'],
            ],
            [
                'code' => '35.05.03',
                'name' => 'Kanigoro',
                'villages' => ['Kanigoro', 'Satreyan', 'Tlogo', 'Gaprang', 'Gogodeso', 'Papungan', 'Sawentar', 'Minggirsari', 'Kuningan', 'Bangle', 'Karangsono', 'Jatinom'],
            ],
            [
                'code' => '35.05.04',
                'name' => 'Garum',
                'villages' => ['Garum', 'Bence', 'Tawangsari', 'Pojok', 'Tingal', 'Slorok', 'Sidodadi', 'Karanganyar', 'Sumberdiren'],
            ],
            [
                'code' => '35.05.05',
                'name' => 'Sutojayan',
                'villages' => ['Sutojayan', 'Kalipang', 'Kembangarum', 'Kedungbunder', 'Sukorejo', 'Pandanarum', 'Jatisari', 'Kaulon'],
            ],
            [
                'code' => '35.05.06',
                'name' => 'Panggungrejo',
                'villages' => ['Panggungrejo', 'Margomulyo', 'Panggungasri', 'Sumberagung', 'Serang', 'Kaligambir', 'Kalitengah', 'Bumiayu'],
            ],
            [
                'code' => '35.05.07',
                'name' => 'Talun',
                'villages' => ['Talun', 'Bajang', 'Bendosewu', 'Duren', 'Jeblog', 'Kamulan', 'Kaweron', 'Kendalsari', 'Pasirharjo', 'Sragi', 'Tumpang', 'Wonorejo'],
            ],
            [
                'code' => '35.05.08',
                'name' => 'Gandusari',
                'villages' => ['Gandusari', 'Butun', 'Gadang', 'Kotes', 'Krisik', 'Ngaringan', 'Semisir', 'Slumbung', 'Sukosewu', 'Sumberagung', 'Tambakan', 'Tulungrejo'],
            ],
            [
                'code' => '35.05.09',
                'name' => 'Binangun',
                'villages' => ['Binangun', 'Birowo', 'Kedungwungu', 'Ngadri', 'Ngembul', 'Rejoso', 'Sambigede', 'Sukorame', 'Tawangrejo', 'Tawangrejo'],
            ],
            [
                'code' => '35.05.10',
                'name' => 'Wlingi',
                'villages' => ['Wlingi', 'Beru', 'Babadan', 'Klemunan', 'Tangkil', 'Balerejo', 'Tegalasri', 'Tembalang', 'Ngadirenggo'],
            ],
            [
                'code' => '35.05.11',
                'name' => 'Doko',
                'villages' => ['Doko', 'Genengan', 'Jambepawon', 'Kalimanis', 'Plumbangan', 'Resapombo', 'Sidoasri', 'Sidorejo', 'Suru', 'Sumberurip'],
            ],
            [
                'code' => '35.05.12',
                'name' => 'Kesamben',
                'villages' => ['Kesamben', 'Bumiayu', 'Jajag', 'Kemorejo', 'Pagergunung', 'Pagerwojo', 'Siraman', 'Sukoanyar', 'Tapakrejo', 'Tepas'],
            ],
            [
                'code' => '35.05.13',
                'name' => 'Wates',
                'villages' => ['Wates', 'Mojorejo', 'Purworejo', 'Ringinrejo', 'Sukorejo', 'Sumberarum', 'Tugurejo', 'Tulungrejo'],
            ],
            [
                'code' => '35.05.14',
                'name' => 'Ponggok',
                'villages' => ['Ponggok', 'Bacem', 'Buni', 'Candirejo', 'Dadaplangu', 'Gembongan', 'Jatilengger', 'Karangbendo', 'Kawedusan', 'Kebonduren', 'Maliran', 'Pojok', 'Ringinanyar', 'Sidorejo'],
            ],
            [
                'code' => '35.05.15',
                'name' => 'Sanankulon',
                'villages' => ['Sanankulon', 'Bendosari', 'Bendowulung', 'Gleduk', 'Jatisari', 'Kalipucung', 'Plosoarang', 'Purworejo', 'Sumber', 'Sumberjo', 'Tuliskriyo'],
            ],
            [
                'code' => '35.05.16',
                'name' => 'Kademangan',
                'villages' => ['Kademangan', 'Bendosari', 'Darungan', 'Dawuhan', 'Jimbe', 'Kebonsari', 'Maron', 'Pakisaji', 'Panggungduwet', 'Plosorejo', 'Rejoso', 'Suruhwadang'],
            ],
            [
                'code' => '35.05.17',
                'name' => 'Bakung',
                'villages' => ['Bakung', 'Kedungbanteng', 'Loren', 'Ngrejo', 'Plandirejo', 'Pulerejo', 'Sidomulyo', 'Tumpakkepuh'],
            ],
            [
                'code' => '35.05.18',
                'name' => 'Udanawu',
                'villages' => ['Bakung', 'Besuki', 'Bendorejo', 'Karanggondang', 'Mangunan', 'Ringinanom', 'Sukamulyo', 'Sumbersari', 'Temenggungan', 'Tunjung'],
            ],
            [
                'code' => '35.05.19',
                'name' => 'Srengat',
                'villages' => ['Srengat', 'Kauman', 'Dandong', 'Togogan', 'Bagelenan', 'Karanggayam', 'Kendalsari', 'Kerjen', 'Maron', 'Ngaglik', 'Purwokerto', 'Selokajang', 'Wonorejo'],
            ],
            [
                'code' => '35.05.20',
                'name' => 'Wonodadi',
                'villages' => ['Wonodadi', 'Gandar', 'Kaliboto', 'Kebonagung', 'Kolomayan', 'Kunir', 'Pikatan', 'Rejosari', 'Salam', 'Tawangrejo'],
            ],
            [
                'code' => '35.05.21',
                'name' => 'Selorejo',
                'villages' => ['Selorejo', 'Banjarsari', 'Boro', 'Ngrendeng', 'Olakkalen', 'Pohgajih', 'Sidomulyo', 'Sumberagung'],
            ],
            [
                'code' => '35.05.22',
                'name' => 'Selopuro',
                'villages' => ['Selopuro', 'Jambewangi', 'Jatitengah', 'Mandisan', 'Mronjo', 'Plawangan', 'Popoh', 'Tegalrejo'],
            ],
        ];

        foreach ($districts as $dData) {
            $district = District::updateOrCreate(
                ['code' => $dData['code']],
                ['name' => $dData['name']]
            );

            $villageIndex = 1;
            foreach ($dData['villages'] as $villageName) {
                $vCode = sprintf('%s.%04d', $district->code, $villageIndex);
                Village::updateOrCreate(
                    ['code' => $vCode],
                    [
                        'district_id' => $district->id,
                        'name' => $villageName,
                    ]
                );
                $villageIndex++;
            }
        }
    }
}
