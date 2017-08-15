<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class MarkersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        DB::table('markers')->truncate();

        for ($i = 0; $i < 200; $i++) {

            DB::table('markers')->insert(
                [
                    'title' => $faker->paragraph,
                    'latitude' => $faker->latitude,
                    'longitude' => $faker->longitude
                ]
            );
        }
    }
}
