<?php

namespace Database\Seeders;

use App\Enums\ToolStatus;
use App\Models\Tool;
use Illuminate\Database\Seeder;

class ToolSeeder extends Seeder
{
    private array $categoryImages = [
        'Power'      => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?w=800&h=600&fit=crop&auto=format',
        'Demo'       => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=600&fit=crop&auto=format',
        'Access'     => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=800&h=600&fit=crop&auto=format',
        'Concrete'   => 'https://images.unsplash.com/photo-1590274853856-f22d5ee3d228?w=800&h=600&fit=crop&auto=format',
        'Plumbing'   => 'https://images.unsplash.com/photo-1607472586893-edb57bdc0e39?w=800&h=600&fit=crop&auto=format',
        'Electrical' => 'https://images.unsplash.com/photo-1621905251918-48416bd8575a?w=800&h=600&fit=crop&auto=format',
        'Landscape'  => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&h=600&fit=crop&auto=format',
        'Painting'   => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=800&h=600&fit=crop&auto=format',
        'Measuring'  => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=800&h=600&fit=crop&auto=format',
    ];

    private array $skuImages = [
        'PWR-DRILL-001'   => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?w=800&h=600&fit=crop&auto=format',
        'PWR-DRILL-002'   => 'https://images.unsplash.com/photo-1572981779307-38b8cabb2407?w=800&h=600&fit=crop&auto=format',
        'PWR-CIRC-001'    => 'https://images.unsplash.com/photo-1530124566582-a618bc2615dc?w=800&h=600&fit=crop&auto=format',
        'PWR-MITER-001'   => 'https://images.unsplash.com/photo-1547447134-cd3f5c716030?w=800&h=600&fit=crop&auto=format',
        'PWR-TABLE-001'   => 'https://images.unsplash.com/photo-1622837571804-2ca9cc7fdb17?w=800&h=600&fit=crop&auto=format',
        'PWR-ANGLE-001'   => 'https://images.unsplash.com/photo-1609205807107-2d621c7f8cf1?w=800&h=600&fit=crop&auto=format',
        'DEM-JACK-001'    => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800&h=600&fit=crop&auto=format',
        'LFT-SCFLD-001'   => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&h=600&fit=crop&auto=format',
        'CON-MIX-001'     => 'https://images.unsplash.com/photo-1590274853856-f22d5ee3d228?w=800&h=600&fit=crop&auto=format',
        'LND-STUMP-001'   => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&h=600&fit=crop&auto=format',
        'LND-CHIPPER-001' => 'https://images.unsplash.com/photo-1500651230702-0e2d8a49d4ad?w=800&h=600&fit=crop&auto=format',
        'MSR-LASER-001'   => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=800&h=600&fit=crop&auto=format',
        'PNT-AIRLESS-001' => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=800&h=600&fit=crop&auto=format',
    ];

    public function run(): void
    {
        $catalogue = [
            ['sku' => 'PWR-DRILL-001',     'name' => '18V Cordless Drill/Driver',          'category' => 'Power',      'rate' =>  2500, 'maint' =>  500],
            ['sku' => 'PWR-DRILL-002',     'name' => '20V Hammer Drill',                   'category' => 'Power',      'rate' =>  3500, 'maint' =>  600],
            ['sku' => 'PWR-CIRC-001',      'name' => '7" Circular Saw',                   'category' => 'Power',      'rate' =>  4000, 'maint' =>  800],
            ['sku' => 'PWR-JIGSW-001',     'name' => 'Variable-Speed Jigsaw',              'category' => 'Power',      'rate' =>  3000, 'maint' =>  500],
            ['sku' => 'PWR-RECIP-001',     'name' => 'Reciprocating Saw (Sawzall)',        'category' => 'Power',      'rate' =>  3500, 'maint' =>  700],
            ['sku' => 'PWR-MITER-001',     'name' => '10" Compound Miter Saw',             'category' => 'Power',      'rate' =>  6500, 'maint' => 1200],
            ['sku' => 'PWR-TABLE-001',     'name' => '10" Table Saw (contractor)',         'category' => 'Power',      'rate' =>  8500, 'maint' => 1500],
            ['sku' => 'PWR-BNDSAW-001',    'name' => '14" Band Saw',                       'category' => 'Power',      'rate' =>  7000, 'maint' => 1200],
            ['sku' => 'PWR-ROUT-001',      'name' => '2HP Plunge Router',                'category' => 'Power',      'rate' =>  4500, 'maint' =>  800],
            ['sku' => 'PWR-BELT-001',      'name' => '3x21" Belt Sander',                 'category' => 'Power',      'rate' =>  2800, 'maint' =>  500],
            ['sku' => 'PWR-ORBIT-001',     'name' => '5" Random Orbit Sander',            'category' => 'Power',      'rate' =>  1800, 'maint' =>  300],
            ['sku' => 'PWR-NAILGUN-001',   'name' => 'Framing Nailer 21deg',              'category' => 'Power',      'rate' =>  5000, 'maint' =>  900],
            ['sku' => 'PWR-FINNAILER-001', 'name' => '18GA Finish Nailer',               'category' => 'Power',      'rate' =>  3500, 'maint' =>  600],
            ['sku' => 'PWR-IMPACT-001',    'name' => '1/2" Impact Wrench',                'category' => 'Power',      'rate' =>  3000, 'maint' =>  500],
            ['sku' => 'PWR-ANGLE-001',     'name' => '4.5" Angle Grinder',                'category' => 'Power',      'rate' =>  2500, 'maint' =>  600],
            ['sku' => 'PWR-PLANER-001',    'name' => '3.25" Electric Hand Planer',        'category' => 'Power',      'rate' =>  3200, 'maint' =>  600],
            ['sku' => 'DEM-ROTO-001',      'name' => 'SDS-Plus Rotary Hammer',            'category' => 'Demo',       'rate' =>  5500, 'maint' => 1000],
            ['sku' => 'DEM-DEMO-001',      'name' => 'Electric Demolition Hammer 15A',    'category' => 'Demo',       'rate' =>  7500, 'maint' => 1500],
            ['sku' => 'DEM-JACK-001',      'name' => '75 lb Electric Jackhammer',         'category' => 'Demo',       'rate' => 12000, 'maint' => 2000],
            ['sku' => 'DEM-CUTOFF-001',    'name' => '14" Electric Cut-Off Saw',          'category' => 'Demo',       'rate' =>  6000, 'maint' => 1200],
            ['sku' => 'LFT-LADDER-001',    'name' => '6 ft Fibreglass Step Ladder',       'category' => 'Access',     'rate' =>  1500, 'maint' =>  200],
            ['sku' => 'LFT-LADDER-002',    'name' => '8 ft Aluminium Step Ladder',        'category' => 'Access',     'rate' =>  2000, 'maint' =>  250],
            ['sku' => 'LFT-EXT-001',       'name' => '24 ft Extension Ladder',            'category' => 'Access',     'rate' =>  3500, 'maint' =>  400],
            ['sku' => 'LFT-SCFLD-001',     'name' => 'Aluminium Scaffolding Set 5x2m',    'category' => 'Access',     'rate' => 15000, 'maint' => 2000],
            ['sku' => 'LFT-PLAT-001',      'name' => 'Rolling Work Platform 6 ft',        'category' => 'Access',     'rate' =>  8000, 'maint' =>  800],
            ['sku' => 'LFT-HOIST-001',     'name' => 'Half Ton Electric Chain Hoist',     'category' => 'Access',     'rate' => 10000, 'maint' => 1500],
            ['sku' => 'CON-MIX-001',       'name' => '3.5 Cu Ft Electric Cement Mixer',  'category' => 'Concrete',   'rate' => 10000, 'maint' => 1800],
            ['sku' => 'CON-MIX-002',       'name' => '6 Cu Ft Drum Cement Mixer',        'category' => 'Concrete',   'rate' => 15000, 'maint' => 2000],
            ['sku' => 'CON-VIBR-001',      'name' => 'Concrete Vibrator 2in Head',       'category' => 'Concrete',   'rate' =>  5000, 'maint' =>  800],
            ['sku' => 'CON-FLOAT-001',     'name' => 'Walk-Behind Power Float 24in',     'category' => 'Concrete',   'rate' => 12000, 'maint' => 2000],
            ['sku' => 'CON-GRIND-001',     'name' => 'Concrete Floor Grinder 7in',       'category' => 'Concrete',   'rate' =>  9000, 'maint' => 1500],
            ['sku' => 'CON-SAW-001',       'name' => 'Concrete Tile Saw 10in',           'category' => 'Concrete',   'rate' =>  8000, 'maint' => 1500],
            ['sku' => 'PLB-SNAKE-001',     'name' => '50 ft Drain Snake Manual',         'category' => 'Plumbing',   'rate' =>  2000, 'maint' =>  300],
            ['sku' => 'PLB-SNAKE-002',     'name' => '100 ft Electric Drain Auger',       'category' => 'Plumbing',   'rate' =>  6000, 'maint' => 1000],
            ['sku' => 'PLB-PRESS-001',     'name' => 'PEX Press Tool Kit',               'category' => 'Plumbing',   'rate' =>  8500, 'maint' =>  800],
            ['sku' => 'PLB-PIPE-001',      'name' => 'Pipe Threading Machine 0.5-2in',   'category' => 'Plumbing',   'rate' => 12000, 'maint' => 2000],
            ['sku' => 'PLB-TORCH-001',     'name' => 'Propane Torch Kit MAPP',           'category' => 'Plumbing',   'rate' =>  2500, 'maint' =>  400],
            ['sku' => 'ELC-FISH-001',      'name' => '240 ft Fibre Glow Fish Tape',      'category' => 'Electrical', 'rate' =>  1500, 'maint' =>  200],
            ['sku' => 'ELC-CABLE-001',     'name' => 'Cable Puller Kit 1200 lbs',        'category' => 'Electrical', 'rate' =>  7000, 'maint' =>  800],
            ['sku' => 'ELC-KNOCK-001',     'name' => 'Electric Knockout Punch Set',      'category' => 'Electrical', 'rate' =>  5500, 'maint' =>  600],
            ['sku' => 'ELC-CONDUIT-001',   'name' => 'Electric Conduit Bender 0.5-1.25in','category' => 'Electrical','rate' =>  4000, 'maint' =>  500],
            ['sku' => 'LND-TILLER-001',    'name' => 'Front-Tine Tiller 16in',           'category' => 'Landscape',  'rate' =>  8000, 'maint' => 1200],
            ['sku' => 'LND-AERAT-001',     'name' => 'Lawn Aerator Push',                'category' => 'Landscape',  'rate' =>  4000, 'maint' =>  500],
            ['sku' => 'LND-DTHATCH-001',   'name' => 'Electric Dethatcher 14in',         'category' => 'Landscape',  'rate' =>  3500, 'maint' =>  500],
            ['sku' => 'LND-STUMP-001',     'name' => 'Stump Grinder 14HP',               'category' => 'Landscape',  'rate' => 25000, 'maint' => 3000],
            ['sku' => 'LND-CHIPPER-001',   'name' => 'Wood Chipper 3in capacity',        'category' => 'Landscape',  'rate' => 18000, 'maint' => 2500],
            ['sku' => 'LND-TRENCHER-001',  'name' => 'Walk-Behind Trencher 4in wide',    'category' => 'Landscape',  'rate' => 20000, 'maint' => 3000],
            ['sku' => 'LND-PPLATE-001',    'name' => 'Plate Compactor 5000 lbs',         'category' => 'Landscape',  'rate' => 12000, 'maint' => 1800],
            ['sku' => 'LND-TAMPER-001',    'name' => 'Jumping Jack Tamper',              'category' => 'Landscape',  'rate' =>  8000, 'maint' => 1500],
            ['sku' => 'PNT-SPRAY-001',     'name' => 'HVLP Paint Sprayer 3 qt',         'category' => 'Painting',   'rate' =>  3500, 'maint' =>  600],
            ['sku' => 'PNT-AIRLESS-001',   'name' => 'Airless Paint Sprayer 0.27 GPM',  'category' => 'Painting',   'rate' =>  8000, 'maint' => 1200],
            ['sku' => 'PNT-SANDER-001',    'name' => 'Drywall Pole Sander Electric',     'category' => 'Painting',   'rate' =>  4000, 'maint' =>  500],
            ['sku' => 'PNT-SCRAPER-001',   'name' => 'Heat Gun and Scraper Kit',         'category' => 'Painting',   'rate' =>  2000, 'maint' =>  300],
            ['sku' => 'MSR-LASER-001',     'name' => 'Self-Levelling Cross-Line Laser',  'category' => 'Measuring',  'rate' =>  5000, 'maint' =>  500],
            ['sku' => 'MSR-THDR-001',      'name' => 'Laser Level Rotary 360',           'category' => 'Measuring',  'rate' =>  9000, 'maint' =>  800],
            ['sku' => 'MSR-DIST-001',      'name' => 'Laser Distance Measurer 200m',     'category' => 'Measuring',  'rate' =>  2000, 'maint' =>  200],
            ['sku' => 'MSR-STUD-001',      'name' => 'Deep Scan Stud Finder Pro',        'category' => 'Measuring',  'rate' =>   800, 'maint' =>  100],
        ];

        $descriptions = [
            'Power'      => 'Professional-grade power tool, fully serviced and ready for site use.',
            'Demo'       => 'Heavy-duty demolition equipment  ear and eye protection recommended.',
            'Access'     => 'Safe access equipment, inspected and load-tested before each rental.',
            'Concrete'   => 'Concrete and masonry equipment for residential and light commercial work.',
            'Plumbing'   => 'Plumbing tool kit, cleaned and calibrated after every return.',
            'Electrical' => 'Electrical installation tool  verify local code requirements before use.',
            'Landscape'  => 'Outdoor and landscaping equipment  fuel/battery not included.',
            'Painting'   => 'Painting and finishing equipment, thoroughly cleaned after each use.',
            'Measuring'  => 'Precision measuring tool  calibrated to +/-1 mm / +/-0.1 deg.',
        ];

        foreach ($catalogue as $item) {
            $imageUrl = $this->skuImages[$item['sku']] ?? $this->categoryImages[$item['category']] ?? null;
            Tool::firstOrCreate(
                ['sku' => $item['sku']],
                [
                    'name'                  => $item['name'],
                    'description'           => $descriptions[$item['category']],
                    'image_url'             => $imageUrl,
                    'category'              => $item['category'],
                    'daily_rate_cents'      => $item['rate'],
                    'maintenance_fee_cents' => $item['maint'],
                    'status'                => ToolStatus::Available,
                    'user_id'               => null,
                ],
            );
        }

        Tool::firstOrCreate(['sku' => 'PWR-DRILL-003'], ['name' => '20V Brushless Drill (out on loan)', 'description' => $descriptions['Power'], 'image_url' => $this->categoryImages['Power'], 'category' => 'Power', 'daily_rate_cents' => 3000, 'maintenance_fee_cents' => 500, 'status' => ToolStatus::Out]);
        Tool::firstOrCreate(['sku' => 'LFT-LADDER-003'], ['name' => '32 ft Extension Ladder (reserved)', 'description' => $descriptions['Access'], 'image_url' => $this->categoryImages['Access'], 'category' => 'Access', 'daily_rate_cents' => 4000, 'maintenance_fee_cents' => 400, 'status' => ToolStatus::Reserved]);
        Tool::firstOrCreate(['sku' => 'DEM-JACK-002'], ['name' => '90 lb Pneumatic Jackhammer (archived)', 'description' => $descriptions['Demo'], 'image_url' => $this->categoryImages['Demo'], 'category' => 'Demo', 'daily_rate_cents' => 14000, 'maintenance_fee_cents' => 2500, 'status' => ToolStatus::Archived]);
    }
}
