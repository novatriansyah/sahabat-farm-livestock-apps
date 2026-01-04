<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MasterPartner;
use App\Models\MasterLocation;
use App\Models\MasterCategory;
use App\Models\MasterBreed;
use App\Models\MasterPhysStatus;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Production Seeder...');

        // 1. Create Users
        $this->createUsers();

        // 2. Create Single Partner
        $this->createPartners();

        // 3. Create Master Data (Essential)
        $this->createMasterData();

        // 4. Create Specific Locations
        $this->createLocations();

        $this->command->info('Production Seeder Complete. Ready for deployment.');
    }

    private function createUsers()
    {
        // User 1: Rizki Dwianda
        User::firstOrCreate(
            ['email' => 'rizki@sahabatfarm.com'], // Adjust email if known, using placeholder based on name
            [
                'name' => 'Rizki Dwianda',
                'password' => Hash::make('password'), // Default password
                'role' => 'OWNER',
                'email_verified_at' => now(),
            ]
        );

        // User 2: Nova Triansyah Azis
        User::firstOrCreate(
            ['email' => 'nova@sahabatfarm.com'], 
            [
                'name' => 'Nova Triansyah Azis',
                'password' => Hash::make('password'),
                'role' => 'OWNER',
                'email_verified_at' => now(),
            ]
        );
        
        $this->command->info('Users Created: Rizki Dwianda, Nova Triansyah Azis');
    }

    private function createPartners()
    {
        // Only one internal partner
        MasterPartner::firstOrCreate(
            ['name' => 'Sahabat Farm Indonesia (Internal)'],
            ['contact_info' => 'Internal Management']
        );

        $this->command->info('Partner Created: Sahabat Farm Indonesia (Internal)');
    }

    private function createLocations()
    {
        $locations = [
            ['name' => 'Kandang Koloni 1', 'type' => 'Kandang Koloni'],
            ['name' => 'Kandang Koloni 2', 'type' => 'Kandang Koloni'],
            ['name' => 'Kandang Menyusui 1 (Individu)', 'type' => 'Kandang Individu'],
            ['name' => 'Kandang Penggemukan 1 (Koloni)', 'type' => 'Kandang Koloni'],
            ['name' => 'Kandang Penggemukan 2 (Koloni)', 'type' => 'Kandang Koloni'],
            ['name' => 'Kandang Penggemukan 3 (Koloni)', 'type' => 'Kandang Koloni'],
            ['name' => 'Kandang Penggemukan 4 (Koloni)', 'type' => 'Kandang Koloni'],
        ];

        foreach ($locations as $loc) {
            MasterLocation::firstOrCreate(
                ['name' => $loc['name']],
                ['type' => $loc['type']]
            );
        }

        $this->command->info('Locations Created: 7 Specific Kandang');
    }

    private function createMasterData()
    {
        // Categories
        $categories = ['Domba', 'Kambing'];
        foreach ($categories as $cat) {
            MasterCategory::firstOrCreate(['name' => $cat]);
        }

        // Breeds (Common ones)
        $breeds = ['Domba Garut', 'Dorper', 'Texel', 'Merino', 'Kambing Boer', 'Jawaando'];
        // Need to assign category_id, assume Domba for simplicity or map them
        $dombaId = MasterCategory::where('name', 'Domba')->first()->id;
        foreach ($breeds as $breed) {
            MasterBreed::firstOrCreate(
                ['name' => $breed],
                ['category_id' => $dombaId] // Default to Domba
            );
        }

        // Physical Statuses
        $statuses = [
            'Sehat',
            'Sakit',
            'Bunting',
            'Menyusui',
            'Cempe Lahir',
            'Cempe Sapih',
            'Dara',
            'Pejantan',
            'Penggemukan - Siap Jual',
            'Karantina'
        ];
        foreach ($statuses as $status) {
            MasterPhysStatus::firstOrCreate(['name' => $status]);
        }
        
        $this->command->info('Master Data Created (Categories, Breeds, Statuses)');
    }
}
