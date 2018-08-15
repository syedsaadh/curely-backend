<?php

use Illuminate\Database\Seeder;

class DrugCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            [
                'name' => 'Drug 1',
                'drug_type' => 'drops',
                'updated_by_user' => 1,
                'created_by_user' => 1,
            ],
            [
                'name' => 'Drug 2',
                'drug_type' => 'foam',
                'updated_by_user' => 1,
                'created_by_user' => 1,
            ]
        ];
        foreach ($departments as $key => $value) {
            App\Models\DrugCatalog::create($value);
        }
        //factory(App\Models\DrugCatalog::class, 10)->create();
    }
}
