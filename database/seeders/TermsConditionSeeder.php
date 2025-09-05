<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TermsCondition;

class TermsConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TermsCondition::updateOrCreate(
            ['context' => 'contributor_submission'],
            [
                'body' => 'Terms and conditions for posting a blog or event go here.',
                'boxes' => [
                    ['label' => 'I have read and agree to T&Cs', 'enabled' => true,  'required' => true],
                    ['label' => 'This is my original work',      'enabled' => true,  'required' => true],
                    ['label' => 'I would like to pay for promotion of this event', 'enabled' => false, 'required' => false],
                ],
                'is_active' => true,
            ]
        );
    }
}
