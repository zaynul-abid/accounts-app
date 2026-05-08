<?php

namespace Database\Seeders;

use App\Models\HouseCreation;
use App\Models\Place;
use App\Models\HouseType;
use Illuminate\Database\Seeder;

class HouseCreationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create house types
        $apartmentType = HouseType::firstOrCreate(
            ['name' => 'Apartment'],
            ['description' => 'Residential apartment']
        );

        $villaType = HouseType::firstOrCreate(
            ['name' => 'Villa'],
            ['description' => 'Standalone villa']
        );

        $cottageType = HouseType::firstOrCreate(
            ['name' => 'Cottage'],
            ['description' => 'Cottage type residence']
        );

        $farmhouseType = HouseType::firstOrCreate(
            ['name' => 'Farmhouse'],
            ['description' => 'Agricultural farmhouse']
        );

        // Get or create places
        $place1 = Place::firstOrCreate(
            ['name' => 'Downtown'],
            ['description' => 'City center area']
        );

        $place2 = Place::firstOrCreate(
            ['name' => 'Uptown'],
            ['description' => 'Northern residential area']
        );

        $place3 = Place::firstOrCreate(
            ['name' => 'Suburbia'],
            ['description' => 'Suburban area']
        );

        $place4 = Place::firstOrCreate(
            ['name' => 'Countryside'],
            ['description' => 'Rural area']
        );

        // Create houses
        HouseCreation::firstOrCreate(
            ['house_no' => 'H-001'],
            [
                'sl_number' => '001',
                'registration_date' => now()->format('Y-m-d'),
                'place_id' => $place1->id,
                'house_type_id' => $apartmentType->id,
                'jamath_house_no' => 'JH-001',
                'house_name' => 'Rose Garden Apartments',
                'house_owner' => 'Ahmed Khan',
                'floors' => 4,
                'ward_no' => 'W-01',
                'mobile' => '+971501234567',
                'reg_fee' => 500,
                'house_sub' => true,
                'default_amount' => 1000,
                'due_amount' => 0,
                'active' => true,
            ]
        );

        HouseCreation::firstOrCreate(
            ['house_no' => 'H-002'],
            [
                'sl_number' => '002',
                'registration_date' => now()->format('Y-m-d'),
                'place_id' => $place1->id,
                'house_type_id' => $villaType->id,
                'jamath_house_no' => 'JH-002',
                'house_name' => 'Sunset Villa',
                'house_owner' => 'Fatima Al-Mansouri',
                'floors' => 3,
                'ward_no' => 'W-01',
                'mobile' => '+971505555555',
                'reg_fee' => 600,
                'house_sub' => true,
                'default_amount' => 1200,
                'due_amount' => 0,
                'active' => true,
            ]
        );

        HouseCreation::firstOrCreate(
            ['house_no' => 'H-003'],
            [
                'sl_number' => '003',
                'registration_date' => now()->format('Y-m-d'),
                'place_id' => $place2->id,
                'house_type_id' => $villaType->id,
                'jamath_house_no' => 'JH-003',
                'house_name' => 'Green Valley Estate',
                'house_owner' => 'Mohammad Hassan',
                'floors' => 2,
                'ward_no' => 'W-02',
                'mobile' => '+971506666666',
                'reg_fee' => 550,
                'house_sub' => true,
                'default_amount' => 1100,
                'due_amount' => 0,
                'active' => true,
            ]
        );

        HouseCreation::firstOrCreate(
            ['house_no' => 'H-004'],
            [
                'sl_number' => '004',
                'registration_date' => now()->format('Y-m-d'),
                'place_id' => $place2->id,
                'house_type_id' => $cottageType->id,
                'jamath_house_no' => 'JH-004',
                'house_name' => 'Peaceful Cottage',
                'house_owner' => 'Aisha Ibrahim',
                'floors' => 2,
                'ward_no' => 'W-02',
                'mobile' => '+971507777777',
                'reg_fee' => 450,
                'house_sub' => true,
                'default_amount' => 900,
                'due_amount' => 0,
                'active' => true,
            ]
        );

        HouseCreation::firstOrCreate(
            ['house_no' => 'H-005'],
            [
                'sl_number' => '005',
                'registration_date' => now()->format('Y-m-d'),
                'place_id' => $place3->id,
                'house_type_id' => $apartmentType->id,
                'jamath_house_no' => 'JH-005',
                'house_name' => 'Modern Towers',
                'house_owner' => 'Omar Ali',
                'floors' => 5,
                'ward_no' => 'W-03',
                'mobile' => '+971508888888',
                'reg_fee' => 700,
                'house_sub' => true,
                'default_amount' => 1400,
                'due_amount' => 0,
                'active' => true,
            ]
        );

        HouseCreation::firstOrCreate(
            ['house_no' => 'H-006'],
            [
                'sl_number' => '006',
                'registration_date' => now()->format('Y-m-d'),
                'place_id' => $place3->id,
                'house_type_id' => $villaType->id,
                'jamath_house_no' => 'JH-006',
                'house_name' => 'Lakeside Manor',
                'house_owner' => 'Zahra Mohammed',
                'floors' => 3,
                'ward_no' => 'W-03',
                'mobile' => '+971509999999',
                'reg_fee' => 650,
                'house_sub' => true,
                'default_amount' => 1300,
                'due_amount' => 0,
                'active' => true,
            ]
        );

        HouseCreation::firstOrCreate(
            ['house_no' => 'H-007'],
            [
                'sl_number' => '007',
                'registration_date' => now()->format('Y-m-d'),
                'place_id' => $place4->id,
                'house_type_id' => $farmhouseType->id,
                'jamath_house_no' => 'JH-007',
                'house_name' => 'Heritage Farm',
                'house_owner' => 'Abdullah Rashid',
                'floors' => 2,
                'ward_no' => 'W-04',
                'mobile' => '+971501111111',
                'reg_fee' => 400,
                'house_sub' => true,
                'default_amount' => 800,
                'due_amount' => 0,
                'active' => true,
            ]
        );

        HouseCreation::firstOrCreate(
            ['house_no' => 'H-008'],
            [
                'sl_number' => '008',
                'registration_date' => now()->format('Y-m-d'),
                'place_id' => $place4->id,
                'house_type_id' => $farmhouseType->id,
                'jamath_house_no' => 'JH-008',
                'house_name' => 'Golden Fields',
                'house_owner' => 'Noor Hassan',
                'floors' => 1,
                'ward_no' => 'W-04',
                'mobile' => '+971502222222',
                'reg_fee' => 350,
                'house_sub' => true,
                'default_amount' => 700,
                'due_amount' => 0,
                'active' => true,
            ]
        );

        HouseCreation::firstOrCreate(
            ['house_no' => 'H-009'],
            [
                'sl_number' => '009',
                'registration_date' => now()->format('Y-m-d'),
                'place_id' => $place1->id,
                'house_type_id' => $apartmentType->id,
                'jamath_house_no' => 'JH-009',
                'house_name' => 'Pearl Heights',
                'house_owner' => 'Karim Saeed',
                'floors' => 6,
                'ward_no' => 'W-01',
                'mobile' => '+971503333333',
                'reg_fee' => 750,
                'house_sub' => true,
                'default_amount' => 1500,
                'due_amount' => 0,
                'active' => true,
            ]
        );

        HouseCreation::firstOrCreate(
            ['house_no' => 'H-010'],
            [
                'sl_number' => '010',
                'registration_date' => now()->format('Y-m-d'),
                'place_id' => $place2->id,
                'house_type_id' => $villaType->id,
                'jamath_house_no' => 'JH-010',
                'house_name' => 'Crown Jewel Villa',
                'house_owner' => 'Layla Ahmad',
                'floors' => 3,
                'ward_no' => 'W-02',
                'mobile' => '+971504444444',
                'reg_fee' => 600,
                'house_sub' => true,
                'default_amount' => 1200,
                'due_amount' => 0,
                'active' => true,
            ]
        );
    }
}
