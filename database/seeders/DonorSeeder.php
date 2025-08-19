<?php

namespace Database\Seeders;

use App\Models\Donor;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DonorSeeder extends Seeder
{
    public function run(): void
    {
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $villages = ['Mogadishu', 'Hargeisa', 'Kismayo', 'Bosaso', 'Galkayo', 'Baidoa', 'Garowe', 'Jowhar'];
        $occupations = ['Teacher', 'Doctor', 'Engineer', 'Student', 'Business Owner', 'Government Employee', 'Nurse', 'Driver'];
        $statuses = ['Eligible', 'Ineligible', 'Pending'];

        for ($i = 0; $i < 50; $i++) {
            $dateOfBirth = Carbon::now()->subYears(rand(18, 60));
            $lastDonationDate = rand(0, 1) ? Carbon::now()->subMonths(rand(1, 12)) : null;
            
            Donor::create([
                'first_name' => $this->generateFirstName(),
                'last_name' => $this->generateLastName(),
                'date_of_birth' => $dateOfBirth,
                'sex' => rand(0, 1) ? 'male' : 'female',
                'age' => Carbon::now()->diffInYears($dateOfBirth),
                'occupation' => $occupations[array_rand($occupations)],
                'village' => $villages[array_rand($villages)],
                'tell' => $this->generatePhoneNumber(),
                'weight' => rand(50, 100) + (rand(0, 99) / 100),
                'bp' => rand(90, 140) . '/' . rand(60, 90),
                'hemoglobin' => rand(12, 18),
                'blood_group' => $bloodGroups[array_rand($bloodGroups)],
                'status' => $statuses[array_rand($statuses)],
                'last_donation_date' => $lastDonationDate,
            ]);
        }
    }

    private function generateFirstName(): string
    {
        $firstNames = [
            'Ahmed', 'Mohamed', 'Abdi', 'Hassan', 'Omar', 'Ali', 'Ibrahim', 'Yusuf',
            'Fatima', 'Amina', 'Hodan', 'Fadumo', 'Maryam', 'Sahra', 'Asha', 'Naima'
        ];
        return $firstNames[array_rand($firstNames)];
    }

    private function generateLastName(): string
    {
        $lastNames = [
            'Hassan', 'Mohamed', 'Ali', 'Ibrahim', 'Omar', 'Abdi', 'Yusuf', 'Ahmed',
            'Hussein', 'Farah', 'Dahir', 'Warsame', 'Guled', 'Hersi', 'Jama', 'Muse'
        ];
        return $lastNames[array_rand($lastNames)];
    }

    private function generatePhoneNumber(): string
    {
        return '252' . rand(61, 90) . rand(1000000, 9999999);
    }
} 