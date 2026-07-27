<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\WarehousePacking;
use App\Models\WarehouseShipment;
use App\Models\Warehouse;
use App\Services\Warehouse\ShippingService;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    protected ShippingService $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function index()
    {
        $this->authorize('viewAny', WarehouseShipment::class);
        $companyId = session('company_id') ?? 1;

        $items = WarehouseShipment::where('company_id', $companyId)
            ->latest()
            ->paginate(15);

        return view('admin.warehouse.shipments.index', compact('items'));
    }

    public function create()
    {
        $this->authorize('create', WarehouseShipment::class);
        $companyId = session('company_id') ?? 1;

        $warehouses = Warehouse::where('company_id', $companyId)->get();

        // Completed packings that are not yet assigned to a shipment
        $packings = WarehousePacking::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereNull('warehouse_shipment_id')
            ->get();

        return view('admin.warehouse.shipments.create', compact('warehouses', 'packings'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', WarehouseShipment::class);

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'packing_ids' => 'nullable|array',
            'packing_ids.*' => 'exists:warehouse_packings,id',
            'carrier' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'shipping_notes' => 'nullable|string|max:1000',
        ]);

        $companyId = session('company_id') ?? 1;

        $shipment = $this->shippingService->createShipment(
            $companyId,
            $request->warehouse_id,
            $request->packing_ids ?? [],
            auth()->id()
        );

        // Save carrier and tracking if provided at creation
        if ($request->filled('carrier') || $request->filled('tracking_number')) {
            $shipment->update([
                'carrier' => $request->carrier,
                'tracking_number' => $request->tracking_number,
                'shipping_notes' => $request->shipping_notes,
            ]);
        }

        return redirect()->route('admin.warehouse.shipments.show', $shipment)
            ->with('success', 'Shipment created successfully.');
    }

    public function show(WarehouseShipment $shipment)
    {
        $this->authorize('view', $shipment);

        $packings = WarehousePacking::where('warehouse_shipment_id', $shipment->id)->get();

        return view('admin.warehouse.shipments.show', compact('shipment', 'packings'));
    }

    public function edit(WarehouseShipment $shipment)
    {
        $this->authorize('update', $shipment);

        return view('admin.warehouse.shipments.edit', compact('shipment'));
    }

    public function update(Request $request, WarehouseShipment $shipment)
    {
        $this->authorize('update', $shipment);

        $request->validate([
            'action' => 'required|in:prepare,dispatch,deliver,cancel',
            'carrier' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'shipping_notes' => 'nullable|string|max:1000',
        ]);

        $action = $request->input('action');

        switch ($action) {
            case 'prepare':
                $shipment->update([
                    'status' => 'prepared',
                    'carrier' => $request->carrier,
                    'tracking_number' => $request->tracking_number,
                    'shipping_notes' => $request->shipping_notes,
                ]);
                $message = 'Shipment prepared and ready for dispatch.';
                break;

            case 'dispatch':
                $this->shippingService->dispatchShipment($shipment, $request->all(), auth()->id());
                $message = 'Shipment dispatched successfully.';
                break;

            case 'deliver':
                $this->shippingService->markAsDelivered($shipment, auth()->id());
                $message = 'Shipment marked as delivered.';
                break;

            case 'cancel':
                $shipment->update(['status' => 'cancelled']);
                $message = 'Shipment cancelled.';
                break;

            default:
                $message = 'No action taken.';
        }

        return redirect()->route('admin.warehouse.shipments.show', $shipment)
            ->with('success', $message);
    }
}
