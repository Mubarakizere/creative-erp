# Creative ERP REST API Endpoint Reference

**Version**: 1.1  
**Document**: 06_API_REFERENCE  
**Base URL**: `/api`  
**Authentication**: Laravel Sanctum Bearer Token  

---

# 1. Authentication Header & Response Protocol

Protected API endpoints require the `Authorization` HTTP header with a valid Sanctum Bearer token:

```http
Authorization: Bearer <your_sanctum_token>
Accept: application/json
Content-Type: application/json
```

---

# 2. Authentication Endpoints

### `POST /api/login`
Authenticates user credentials and returns a Sanctum Bearer Token.

#### Request Body
```json
{
  "email": "admin@creative-erp.com",
  "password": "Password123!"
}
```

#### Successful Response (200 OK)
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "token": "1|AbCdEf123456789...",
    "user": {
      "id": 1,
      "uuid": "8f8b3c10-91a2-4a25-b461-123456789abc",
      "name": "Super Admin",
      "email": "admin@creative-erp.com",
      "company_id": 1
    }
  }
}
```

---

### `POST /api/logout`
Revokes the authenticated Sanctum token. Requires Auth Header.

#### Response (200 OK)
```json
{
  "status": "success",
  "message": "Logged out successfully"
}
```

---

### `GET /api/me`
Retrieves authenticated user profile, company details, and assigned permissions.

---

# 3. Financial Reporting API (`/api/finance`)

### `GET /api/finance/reports/profit-and-loss`
Generates Profit & Loss Statement for a date range.

#### Query Parameters
- `start_date` (string, optional, `YYYY-MM-DD`)
- `end_date` (string, optional, `YYYY-MM-DD`)

---

### `GET /api/finance/reports/balance-sheet`
Generates Balance Sheet Statement as of specified date.

---

### `GET /api/finance/reports/cash-flow`
Generates Cash Flow Statement.

---

### `GET /api/finance/budgets`
Lists financial budgets with actual vs. estimated variances.

---

### `GET /api/finance/analytics`
Retrieves executive financial KPIs and revenue/expense series data.

---

# 4. Executive Dashboard API (`/api/dashboard`)

### `GET /api/dashboard/executive`
Retrieves enterprise KPI metrics including active projects, actual costs, total revenue, inventory valuation, and pending approvals.

---

# 5. Inventory API (`/api/inventory`)

### `GET /api/inventory/products`
Lists products with SKU, categories, variants, and stock balances.

---

### `GET /api/inventory/warehouses`
Lists warehouses with zones and bin locations.

---

### `GET /api/inventory/stock`
Returns current stock levels per warehouse and product.

---

### `POST /api/inventory/transfer`
Executes an inventory transfer between warehouses.

#### Request Body
```json
{
  "from_warehouse_id": 1,
  "to_warehouse_id": 2,
  "product_id": 15,
  "quantity": 50,
  "notes": "Transfer site materials to Project B warehouse"
}
```

---

### `POST /api/inventory/adjust`
Logs a stock adjustment for inventory reconciliation.

---

# 6. Procurement API (`/api/procurement`)

### `GET /api/procurement/suppliers`
Lists registered suppliers, ratings, and contact info.

---

### `GET /api/procurement/purchase-requisitions`
Lists purchase requisitions.

---

### `POST /api/procurement/purchase-requisitions/{id}/approve`
Approves a purchase requisition.

---

### `POST /api/procurement/purchase-orders/{id}/approve`
Approves a purchase order.

---

### `POST /api/procurement/goods-receipts`
Records physical goods received against a Purchase Order and updates warehouse inventory stock.

---

### `POST /api/procurement/purchase-invoices`
Records a purchase invoice for 3-way matching.

---

### `POST /api/procurement/supplier-payments`
Processes a payment to a supplier.
