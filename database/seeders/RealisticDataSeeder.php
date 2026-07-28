<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Client;
use App\Models\Branch;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\UnitOfMeasure;
use App\Models\Product;
use App\Models\Project;
use App\Models\Warehouse;
use App\Services\Inventory\InventoryEngine;
use App\Services\Project\ProjectMaterialIssueService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RealisticDataSeeder extends Seeder
{
    public function run()
    {
        // ─── COMPANY & BRANCH SETUP ──────────────────────────────────────
        $company = Company::find(1); // Creative Engineering
        if (!$company) {
            $this->command->error("No company found. Aborting.");
            return;
        }

        $branchHQ    = Branch::where('company_id', $company->id)->where('name', 'Head Office')->first();
        $branchAD    = Branch::where('company_id', $company->id)->where('name', 'Abu Dhabi Branch')->first();
        $branchSH    = Branch::where('company_id', $company->id)->where('name', 'Sharjah Branch')->first();

        $user = User::where('company_id', $company->id)->first();
        if (!$user) {
            $this->command->error("No user found for company. Aborting.");
            return;
        }

        $warehouse = Warehouse::where('company_id', $company->id)->where('name', 'Main Warehouse')->first();
        if (!$warehouse) {
            $this->command->error("No warehouse found. Aborting.");
            return;
        }

        // ─── PRODUCT CATEGORIES ──────────────────────────────────────────
        $this->command->info("Setting up Product Categories...");
        $catConstruction = ProductCategory::firstOrCreate(['name' => 'Construction Materials', 'company_id' => $company->id]);
        $catElectrical   = ProductCategory::firstOrCreate(['name' => 'Electrical', 'company_id' => $company->id]);
        $catPlumbing     = ProductCategory::firstOrCreate(['name' => 'Plumbing', 'company_id' => $company->id]);
        $catOffice       = ProductCategory::firstOrCreate(['name' => 'Office / General', 'company_id' => $company->id]);

        // ─── UNITS OF MEASURE (reuse existing) ──────────────────────────
        $this->command->info("Setting up Units of Measure...");
        $unitKg  = UnitOfMeasure::where('company_id', $company->id)->where('name', 'Kilogram')->first();
        $unitPcs = UnitOfMeasure::where('company_id', $company->id)->where('name', 'Piece')->first();
        $unitBox = UnitOfMeasure::where('company_id', $company->id)->where('name', 'Box')->first();
        $unitM   = UnitOfMeasure::where('company_id', $company->id)->where('name', 'Meter')->first();
        $unitL   = UnitOfMeasure::where('company_id', $company->id)->where('name', 'Liter')->first();

        // Fallback: use Piece for any missing unit
        $fallbackUnit = $unitPcs ?? UnitOfMeasure::where('company_id', $company->id)->first();

        // ─── PRODUCTS ────────────────────────────────────────────────────
        $this->command->info("Creating Realistic Product Catalog...");

        // [sku, name, category, unit, cost_price]
        $productsData = [
            // Construction Materials
            ['MAT-CEMENT-50KG',  'Portland Cement 50kg',           $catConstruction, $unitKg  ?? $fallbackUnit, 8000],
            ['MAT-STEEL-12MM',   'Reinforcement Steel Bar 12mm',   $catConstruction, $unitPcs ?? $fallbackUnit, 12000],
            ['MAT-SAND-FINE',    'Fine Aggregate Sand (ton)',       $catConstruction, $unitKg  ?? $fallbackUnit, 50],
            ['MAT-STONE-20MM',   'Crushed Stone 20mm (ton)',       $catConstruction, $unitKg  ?? $fallbackUnit, 60],
            ['MAT-TILE-60X60',   'Ceramic Floor Tile 60x60cm',     $catConstruction, $unitBox ?? $fallbackUnit, 25000],
            ['MAT-PAINT-20L',    'Interior Wall Paint 20L',        $catConstruction, $unitL   ?? $fallbackUnit, 45000],

            // Electrical
            ['ELEC-LED-60X60',      'LED Panel Light 60x60cm',     $catElectrical, $unitPcs ?? $fallbackUnit, 15000],
            ['ELEC-CABLE-2.5MM',    'Electrical Cable 2.5mm (m)',   $catElectrical, $unitM   ?? $fallbackUnit, 1500],
            ['ELEC-SOCKET-DOUBLE',  'Electrical Socket Double',     $catElectrical, $unitPcs ?? $fallbackUnit, 3500],

            // Plumbing
            ['PLUMB-PVC-50MM',    'PVC Pipe 50mm',       $catPlumbing, $unitPcs ?? $fallbackUnit, 8000],
            ['PLUMB-ELBOW-50MM',  'PVC Elbow 50mm',      $catPlumbing, $unitPcs ?? $fallbackUnit, 1200],
            ['PLUMB-TAP-CHROME',  'Water Tap Chrome',    $catPlumbing, $unitPcs ?? $fallbackUnit, 22000],

            // Office / General
            ['OFF-DESK-001',     'Office Desk',         $catOffice, $unitPcs ?? $fallbackUnit, 150000],
            ['OFF-CHAIR-001',    'Office Chair',        $catOffice, $unitPcs ?? $fallbackUnit, 85000],
            ['OFF-PRINTER-A4',   'Printer A4 Laser',    $catOffice, $unitPcs ?? $fallbackUnit, 350000],
        ];

        $products = [];
        foreach ($productsData as $pData) {
            $products[$pData[0]] = Product::create([
                'company_id'          => $company->id,
                'name'                => $pData[1],
                'sku'                 => $pData[0],
                'product_category_id' => $pData[2]->id,
                'unit_of_measure_id'  => $pData[3]->id,
                'valuation_method'    => 'FIFO',
                'cost_price'          => $pData[4],
                'selling_price'       => round($pData[4] * 1.3, 2),
                'allow_negative_stock' => false,
                'status'              => 'Active',
                'track_inventory'     => true,
            ]);
            $this->command->line("  ✓ Product: {$pData[0]} — {$pData[1]}");
        }

        // ─── PROJECTS ────────────────────────────────────────────────────
        $this->command->info("Creating Realistic Projects...");

        $clients = Client::where('company_id', $company->id)->take(6)->get();
        if ($clients->isEmpty()) {
            $this->command->error('No clients found. Aborting project creation.');
            return;
        }

        // [code, name, status, branch, description, budget, clientIndex]
        $projectsData = [
            ['PRJ-KCC-001',   'Kigali Convention Center Renovation',          'Active',   $branchHQ, 'Full interior renovation of the Kigali Convention Center including structural reinforcement, flooring, wall finishing, and electrical upgrades.', 35000000, 0],
            ['PRJ-NBO-002',   'Nairobi Corporate Office Fit-Out',             'Active',   $branchAD, 'Complete corporate office fit-out for a 3-storey commercial building in Nairobi CBD, including partitioning, MEP, and furniture installation.', 28000000, 1],
            ['PRJ-KRC-003',   'Kigali Residential Complex Development',       'Active',   $branchHQ, 'Phase 1 development of a 120-unit residential complex in Kigali featuring modern apartments, underground parking, and landscaped gardens.', 50000000, 2],
            ['PRJ-COB-004',   'Commercial Office Building Electrical Upgrade', 'Active',  $branchSH, 'Comprehensive electrical system upgrade for a 10-storey commercial building, including new distribution boards, LED lighting, and generator backup.', 18000000, 3],
            ['PRJ-HOTEL-005', 'Hotel Interior Renovation',                    'Planning', $branchHQ, 'Luxury hotel lobby and room renovation project covering 80 guest rooms, lobby area, restaurant, and conference facilities.', 42000000, 4],
            ['PRJ-WH-006',    'Warehouse Expansion Project',                  'Planning', $branchAD, 'Expansion of existing warehouse facility by 2,500 sq meters including new loading bays, racking systems, and climate control.', 15000000, 5],
        ];

        $projects = [];
        foreach ($projectsData as $pData) {
            $clientForProject = $clients[$pData[6] % $clients->count()];
            $projects[$pData[0]] = Project::create([
                'company_id'          => $company->id,
                'branch_id'           => $pData[3]?->id,
                'client_id'           => $clientForProject->id,
                'project_code'        => $pData[0],
                'name'                => $pData[1],
                'description'         => $pData[4],
                'status'              => $pData[2],
                'priority'            => 'High',
                'start_date'          => Carbon::today()->subDays(rand(10, 60)),
                'planned_end_date'    => Carbon::today()->addMonths(rand(3, 12)),
                'estimated_budget'    => $pData[5],
                'project_manager_id'  => $user->id,
                'created_by'          => $user->id,
            ]);
            $this->command->line("  ✓ Project: {$pData[0]} — {$pData[1]}");
        }

        // ─── INITIAL INVENTORY (Opening Stock) ──────────────────────────
        $this->command->info("Seeding Initial Inventory via InventoryEngine...");
        $inventoryEngine = app(InventoryEngine::class);
        auth()->login($user);

        $initialStock = [
            'MAT-CEMENT-50KG'      => 500,
            'MAT-STEEL-12MM'       => 300,
            'MAT-SAND-FINE'        => 200,
            'MAT-STONE-20MM'       => 250,
            'MAT-TILE-60X60'       => 300,
            'MAT-PAINT-20L'        => 100,
            'ELEC-LED-60X60'       => 150,
            'ELEC-CABLE-2.5MM'     => 1000,
            'ELEC-SOCKET-DOUBLE'   => 200,
            'PLUMB-PVC-50MM'       => 400,
            'PLUMB-ELBOW-50MM'     => 250,
            'PLUMB-TAP-CHROME'     => 80,
            'OFF-DESK-001'         => 10,
            'OFF-CHAIR-001'        => 15,
            'OFF-PRINTER-A4'       => 5,
        ];

        foreach ($initialStock as $sku => $qty) {
            $product = $products[$sku];
            $inventoryEngine->stockIn(
                $product,
                $warehouse,
                $qty,
                'initial_stock',
                null,   // reference
                null,   // variantId
                null,   // zoneId
                $user->id,
                $product->cost_price
            );
            $this->command->line("  ✓ Stocked {$qty} x {$sku} @ {$product->cost_price}/unit");
        }

        // ─── PROJECT MATERIAL CONSUMPTION ────────────────────────────────
        $this->command->info("Simulating Project Material Consumption...");
        $issueService = app(ProjectMaterialIssueService::class);

        // Issue 1: Kigali Convention Center — foundation & flooring
        $this->command->line("  → Issuing materials to PRJ-KCC-001...");
        $issueService->createIssue([
            'company_id'   => $company->id,
            'branch_id'    => $branchHQ?->id,
            'project_id'   => $projects['PRJ-KCC-001']->id,
            'warehouse_id' => $warehouse->id,
            'issue_number' => 'PMI-KCC-001',
            'issue_date'   => Carbon::today()->subDays(5),
            'notes'        => 'Foundation phase: cement, tiles, and paint for ground floor',
        ], [
            ['product_id' => $products['MAT-CEMENT-50KG']->id, 'quantity' => 50],
            ['product_id' => $products['MAT-TILE-60X60']->id,  'quantity' => 30],
            ['product_id' => $products['MAT-PAINT-20L']->id,   'quantity' => 10],
        ]);

        // Issue 2: Nairobi Corporate Office — electrical fit-out
        $this->command->line("  → Issuing materials to PRJ-NBO-002...");
        $issueService->createIssue([
            'company_id'   => $company->id,
            'branch_id'    => $branchAD?->id,
            'project_id'   => $projects['PRJ-NBO-002']->id,
            'warehouse_id' => $warehouse->id,
            'issue_number' => 'PMI-NBO-001',
            'issue_date'   => Carbon::today()->subDays(3),
            'notes'        => 'First floor electrical installation and LED panel mounting',
        ], [
            ['product_id' => $products['MAT-CEMENT-50KG']->id,    'quantity' => 20],
            ['product_id' => $products['ELEC-CABLE-2.5MM']->id,   'quantity' => 40],
            ['product_id' => $products['ELEC-LED-60X60']->id,     'quantity' => 15],
        ]);

        // Issue 3: Hotel Interior — lobby renovation
        $this->command->line("  → Issuing materials to PRJ-HOTEL-005...");
        $issueService->createIssue([
            'company_id'   => $company->id,
            'branch_id'    => $branchHQ?->id,
            'project_id'   => $projects['PRJ-HOTEL-005']->id,
            'warehouse_id' => $warehouse->id,
            'issue_number' => 'PMI-HTL-001',
            'issue_date'   => Carbon::today()->subDays(1),
            'notes'        => 'Lobby area tile installation and wall painting',
        ], [
            ['product_id' => $products['MAT-TILE-60X60']->id,  'quantity' => 40],
            ['product_id' => $products['MAT-PAINT-20L']->id,   'quantity' => 15],
        ]);

        // ─── SUMMARY ────────────────────────────────────────────────────
        $this->command->newLine();
        $this->command->info("═══════════════════════════════════════════");
        $this->command->info("  REALISTIC DATA SEEDING COMPLETE");
        $this->command->info("═══════════════════════════════════════════");
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Companies',          Company::count()],
                ['Branches',           Branch::count()],
                ['Users',              User::count()],
                ['Products',           Product::count()],
                ['Product Categories', ProductCategory::count()],
                ['Projects',           Project::count()],
                ['Warehouses',         Warehouse::count()],
                ['Inventory Records',  \App\Models\Inventory::count()],
                ['Material Issues',    \App\Models\ProjectMaterialIssue::count()],
            ]
        );
    }
}
