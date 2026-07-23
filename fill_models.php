<?php

$dir = __DIR__ . '/app/Models';

$modelsData = [
    'ProductCategory' => "
    public function parent() { return \$this->belongsTo(ProductCategory::class, 'parent_id'); }
    public function children() { return \$this->hasMany(ProductCategory::class, 'parent_id'); }
    public function products() { return \$this->hasMany(Product::class); }",
    
    'Brand' => "
    public function products() { return \$this->hasMany(Product::class); }",
    
    'UnitOfMeasure' => "
    public function products() { return \$this->hasMany(Product::class); }",
    
    'Product' => "
    protected \$casts = [
        'track_inventory' => 'boolean',
        'allow_negative_stock' => 'boolean',
        'serial_numbers' => 'boolean',
        'batch_numbers' => 'boolean',
        'expiration_date' => 'boolean',
    ];
    public function category() { return \$this->belongsTo(ProductCategory::class, 'product_category_id'); }
    public function brand() { return \$this->belongsTo(Brand::class); }
    public function unit() { return \$this->belongsTo(UnitOfMeasure::class, 'unit_of_measure_id'); }
    public function tax() { return \$this->belongsTo(Tax::class); }
    public function variants() { return \$this->hasMany(ProductVariant::class); }
    public function barcodes() { return \$this->hasMany(Barcode::class); }
    public function supplierProducts() { return \$this->hasMany(SupplierProduct::class); }
    public function inventory() { return \$this->hasMany(Inventory::class); }",
    
    'ProductVariant' => "
    protected \$casts = [
        'attributes' => 'array',
        'track_inventory' => 'boolean',
    ];
    public function product() { return \$this->belongsTo(Product::class); }
    public function barcodes() { return \$this->hasMany(Barcode::class); }",
    
    'Barcode' => "
    public function product() { return \$this->belongsTo(Product::class); }
    public function variant() { return \$this->belongsTo(ProductVariant::class, 'product_variant_id'); }",
    
    'SupplierProduct' => "
    public function product() { return \$this->belongsTo(Product::class); }
    public function contact() { return \$this->belongsTo(Contact::class); }",
    
    'Warehouse' => "
    protected \$casts = [
        'is_default' => 'boolean',
    ];
    public function manager() { return \$this->belongsTo(User::class, 'manager_id'); }
    public function zones() { return \$this->hasMany(WarehouseZone::class); }
    public function inventories() { return \$this->hasMany(Inventory::class); }",
    
    'WarehouseZone' => "
    public function warehouse() { return \$this->belongsTo(Warehouse::class); }
    public function inventories() { return \$this->hasMany(Inventory::class); }",
    
    'Inventory' => "
    public function product() { return \$this->belongsTo(Product::class); }
    public function variant() { return \$this->belongsTo(ProductVariant::class, 'product_variant_id'); }
    public function warehouse() { return \$this->belongsTo(Warehouse::class); }
    public function zone() { return \$this->belongsTo(WarehouseZone::class, 'warehouse_zone_id'); }
    public function transactions() { return \$this->hasMany(InventoryTransaction::class); }",
    
    'InventoryTransaction' => "
    protected \$casts = [
        'date' => 'datetime',
    ];
    public function inventory() { return \$this->belongsTo(Inventory::class); }
    public function user() { return \$this->belongsTo(User::class); }
    public function reference() { return \$this->morphTo(); }",
    
    'InventoryAdjustment' => "
    protected \$casts = [
        'comments' => 'array',
        'attachments' => 'array',
    ];
    public function warehouse() { return \$this->belongsTo(Warehouse::class); }
    public function approvedBy() { return \$this->belongsTo(User::class, 'approved_by'); }
    public function transactions() { return \$this->morphMany(InventoryTransaction::class, 'reference'); }",
    
    'InventoryTransfer' => "
    public function fromWarehouse() { return \$this->belongsTo(Warehouse::class, 'from_warehouse_id'); }
    public function toWarehouse() { return \$this->belongsTo(Warehouse::class, 'to_warehouse_id'); }
    public function fromZone() { return \$this->belongsTo(WarehouseZone::class, 'from_zone_id'); }
    public function toZone() { return \$this->belongsTo(WarehouseZone::class, 'to_zone_id'); }
    public function transactions() { return \$this->morphMany(InventoryTransaction::class, 'reference'); }",
    
    'InventoryReservation' => "
    protected \$casts = [
        'expires_at' => 'datetime',
    ];
    public function product() { return \$this->belongsTo(Product::class); }
    public function reference() { return \$this->morphTo(); }",
    
    'StockCount' => "
    protected \$casts = [
        'variance_detected' => 'boolean',
    ];
    public function warehouse() { return \$this->belongsTo(Warehouse::class); }
    public function approvedBy() { return \$this->belongsTo(User::class, 'approved_by'); }",
    
    'InventoryValuation' => "
    public function product() { return \$this->belongsTo(Product::class); }
    public function warehouse() { return \$this->belongsTo(Warehouse::class); }"
];

foreach ($modelsData as $model => $relations) {
    $file = "$dir/$model.php";
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Insert relations before the closing brace
        $content = preg_replace('/}\s*$/', $relations . "\n}", $content);
        file_put_contents($file, $content);
        echo "Updated Model: $model\n";
    }
}
echo "Done replacing models.\n";
