<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bus;

class BusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JULILA TRANSIT - Non-aircon buses
        Bus::updateOrCreate(
            ['plate_number' => 'JLT-001'],
            [
                'bus_number' => 'JT-001',
                'model' => 'Hyundai County',
                'capacity' => 45,
                'bus_company' => 'JULILA TRANSIT',
                'accommodation_type' => 'regular',
                'status' => 'active',
                'description' => 'Regular bus for Sogod-Borbon and Tabogon via Tuburan routes',
            ]
        );

        Bus::updateOrCreate(
            ['plate_number' => 'JLT-002'],
            [
                'bus_number' => 'JT-002',
                'model' => 'Hyundai County',
                'capacity' => 45,
                'bus_company' => 'JULILA TRANSIT',
                'accommodation_type' => 'regular',
                'status' => 'active',
                'description' => 'Regular bus for north routes',
            ]
        );

        // INDAY MEMIE BUS - Non-aircon buses
        Bus::updateOrCreate(
            ['plate_number' => 'IMB-001'],
            [
                'bus_number' => 'IM-001',
                'model' => 'Hino RM2',
                'capacity' => 50,
                'bus_company' => 'INDAY MEMIE BUS',
                'accommodation_type' => 'regular',
                'status' => 'active',
                'description' => 'Regular bus for Tabuelan via Maravilla route',
            ]
        );

        // CEBU SAN SEBASTIAN LINER CORP. - Non-aircon buses
        Bus::updateOrCreate(
            ['plate_number' => 'CSS-001'],
            [
                'bus_number' => 'CS-001',
                'model' => 'Hino FB',
                'capacity' => 48,
                'bus_company' => 'CEBU SAN SEBASTIAN LINER CORP.',
                'accommodation_type' => 'regular',
                'status' => 'active',
                'description' => 'Regular bus for Sogod via Borbon and Tabogon routes',
            ]
        );

        Bus::updateOrCreate(
            ['plate_number' => 'CSS-002'],
            [
                'bus_number' => 'CS-002',
                'model' => 'Hino FB',
                'capacity' => 48,
                'bus_company' => 'CEBU SAN SEBASTIAN LINER CORP.',
                'accommodation_type' => 'regular',
                'status' => 'active',
                'description' => 'Regular bus for north routes',
            ]
        );

        // ROUGH RIDERS / WHITE STALLION - Non-aircon buses
        Bus::updateOrCreate(
            ['plate_number' => 'RR-001'],
            [
                'bus_number' => 'RR-001',
                'model' => 'Mitsubishi Rosa',
                'capacity' => 40,
                'bus_company' => 'ROUGH RIDERS / WHITE STALLION',
                'accommodation_type' => 'regular',
                'status' => 'active',
                'description' => 'Regular bus for Daan Bantayan Maya route',
            ]
        );

        // METRO CEBU AUTOBUS - Mix of regular and air-conditioned buses
        Bus::updateOrCreate(
            ['plate_number' => 'MCA-001'],
            [
                'bus_number' => 'MC-001',
                'model' => 'Mitsubishi Fuso',
                'capacity' => 46,
                'bus_company' => 'METRO CEBU AUTOBUS',
                'accommodation_type' => 'regular',
                'status' => 'active',
                'description' => 'Regular bus for Bagay via Hagnaya and Kawit routes',
            ]
        );

        Bus::updateOrCreate(
            ['plate_number' => 'MCA-002'],
            [
                'bus_number' => 'MC-002',
                'model' => 'Mitsubishi Fuso',
                'capacity' => 40,
                'bus_company' => 'METRO CEBU AUTOBUS',
                'accommodation_type' => 'air-conditioned',
                'status' => 'active',
                'description' => 'Air-conditioned bus for Vistoria via Hagnaya route',
            ]
        );

        // ISLAND AUTOBUS - Mix of regular and air-conditioned buses
        Bus::updateOrCreate(
            ['plate_number' => 'IA-001'],
            [
                'bus_number' => 'IA-001',
                'model' => 'Nissan Civilian',
                'capacity' => 42,
                'bus_company' => 'ISLAND AUTOBUS',
                'accommodation_type' => 'regular',
                'status' => 'active',
                'description' => 'Regular bus for Mainline and Hagnaya routes',
            ]
        );

        Bus::updateOrCreate(
            ['plate_number' => 'IA-002'],
            [
                'bus_number' => 'IA-002',
                'model' => 'Nissan Civilian',
                'capacity' => 38,
                'bus_company' => 'ISLAND AUTOBUS',
                'accommodation_type' => 'air-conditioned',
                'status' => 'active',
                'description' => 'Air-conditioned bus for north routes',
            ]
        );

        // CERES - Mix of regular and air-conditioned buses
        Bus::updateOrCreate(
            ['plate_number' => 'CER-001'],
            [
                'bus_number' => 'CR-001',
                'model' => 'Yutong ZK6119H',
                'capacity' => 55,
                'bus_company' => 'CERES',
                'accommodation_type' => 'regular',
                'status' => 'active',
                'description' => 'Regular bus for various north routes including Maya via Bagay/Kawit, Tabogon, Tuburan',
            ]
        );

        Bus::updateOrCreate(
            ['plate_number' => 'CER-002'],
            [
                'bus_number' => 'CR-002',
                'model' => 'Yutong ZK6119H',
                'capacity' => 50,
                'bus_company' => 'CERES',
                'accommodation_type' => 'air-conditioned',
                'status' => 'active',
                'description' => 'Air-conditioned bus for Madridejos, Bacolod via Don Salvador, and Bacood via Canlaon routes',
            ]
        );
    }
}