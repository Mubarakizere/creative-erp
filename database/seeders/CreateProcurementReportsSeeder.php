<?php

namespace Database\Seeders;

use App\Models\ReportTemplate;
use Illuminate\Database\Seeder;

class CreateProcurementReportsSeeder extends Seeder
{
    public function run(): void
    {
        $reports = [
            [
                'name' => 'Purchase Orders Report',
                'description' => 'Comprehensive list of purchase orders with supplier and status information.',
                'type' => 'purchase_orders',
                'filters' => [],
                'layout' => [
                    'columns' => ['id', 'order_number', 'supplier.name', 'order_date', 'status', 'grand_total'],
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Supplier Spend Report',
                'description' => 'Total amount spent per supplier based on completed payments.',
                'type' => 'supplier_spend',
                'filters' => [],
                'layout' => [
                    'columns' => ['id', 'name', 'code', 'total_spend'],
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Supplier Performance Report',
                'description' => 'Analysis of supplier fulfillment rates and performance metrics.',
                'type' => 'supplier_performance',
                'filters' => [],
                'layout' => [
                    'columns' => ['id', 'name', 'total_orders', 'completed_orders', 'fulfillment_rate'],
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Goods Receipts Report',
                'description' => 'Detailed view of received goods and related purchase orders.',
                'type' => 'goods_receipts',
                'filters' => [],
                'layout' => [
                    'columns' => ['id', 'receipt_number', 'purchaseOrder.order_number', 'purchaseOrder.supplier.name', 'receipt_date', 'status'],
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Purchase Invoices Report',
                'description' => 'List of purchase invoices with payment statuses.',
                'type' => 'purchase_invoices',
                'filters' => [],
                'layout' => [
                    'columns' => ['id', 'invoice_number', 'supplier.name', 'purchaseOrder.order_number', 'invoice_date', 'due_date', 'status', 'grand_total', 'paid_amount'],
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Outstanding Supplier Payments',
                'description' => 'Aging report of unpaid and partially paid supplier invoices.',
                'type' => 'outstanding_supplier_payments',
                'filters' => [],
                'layout' => [
                    'columns' => ['id', 'invoice_number', 'supplier.name', 'due_date', 'aging_days', 'aging_bucket', 'grand_total', 'outstanding_amount'],
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Lead Time Report',
                'description' => 'Average days between purchase order creation and goods receipt per supplier.',
                'type' => 'lead_time_report',
                'filters' => [],
                'layout' => [
                    'columns' => ['supplier.name', 'average_lead_time', 'order_count'],
                ],
                'is_system' => true,
            ],
        ];

        foreach ($reports as $report) {
            ReportTemplate::firstOrCreate(
                ['type' => $report['type'], 'is_system' => true],
                $report
            );
        }
    }
}
