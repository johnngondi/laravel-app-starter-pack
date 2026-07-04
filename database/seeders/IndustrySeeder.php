<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    /**
     * Seed the reference list of industries / sectors.
     *
     * Idempotent: each industry is matched on its name so the seeder can be
     * re-run safely without creating duplicates.
     */
    public function run(): void
    {
        $industries = [
            'Government Institutions',
            'County Governments',
            'State Corporations & Parastatals',
            'Non-Governmental Organisations (NGOs)',
            'Community-Based Organisations (CBOs)',
            'Faith-Based Organisations (FBOs)',
            'Financial Institutions',
            'Banking',
            'Microfinance Institutions',
            'Insurance',
            'Pension Schemes',
            'Investment & Asset Management',
            'Capital Markets',
            'Building and Construction',
            'Real Estate & Property Management',
            'Mining & Quarrying',
            'Oil, Gas & Energy',
            'Renewable Energy',
            'Agriculture & Agribusiness',
            'Manufacturing',
            'Food & Beverage',
            'Wholesale & Retail Trade',
            'Transport & Logistics',
            'Aviation',
            'Maritime & Shipping',
            'Information Technology',
            'Telecommunications',
            'Media & Communications',
            'Hospitality & Tourism',
            'Education & Training',
            'Healthcare & Pharmaceuticals',
            'Legal Services',
            'Professional & Consulting Services',
            'Accounting & Audit',
            'Human Resources & Recruitment',
            'Marketing & Advertising',
            'Sacco Societies',
            'Cooperatives',
            'Trade Unions & Professional Bodies',
            'Sports, Arts & Entertainment',
            'Security Services',
            'Water & Sanitation',
            'Environment & Conservation',
            'Research & Development',
            'E-commerce',
            'Automotive',
            'Textile & Apparel',
            'Other',
        ];

        $now = now();

        foreach ($industries as $name) {
            Industry::updateOrCreate(
                ['name' => $name],
                ['updated_at' => $now],
            );
        }
    }
}
