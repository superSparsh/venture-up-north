<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TeamMember;
use App\Models\Town;
use App\Models\User;
use App\Models\HomeSection;
use App\Models\VentureMagazine;
use App\Models\Experience;
use App\Models\TourTile;

class StageOneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@venture.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // Contributors
        User::factory(3)->create();

        // Team
        TeamMember::factory(5)->create();

        // Towns
        Town::factory(6)->create();


        // Experience
        Experience::factory(6)->create();

        //TourTile
        TourTile::factory()->count(10)->create();

        //Venture Magazines
        $teamMembers = TeamMember::all();
        VentureMagazine::factory()->count(10)->make()->each(function ($mag) use ($teamMembers) {
            $mag->contributor_id = $teamMembers->random()->id;
            $mag->save();
        });


        // Home Sections (optional starter)
        HomeSection::factory()->create([
            'title' => 'Explore the Southwest',
        ]);
    }
}
