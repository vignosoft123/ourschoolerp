# Subdomains Management — Key Reference

**URL**: `http://staging.ourschoolerp.localhost/Subdomains`
**Access**: Admin only, accessible ONLY from the `staging` subdomain.

---

## 1. Architecture Overview

This is a **multi-tenant SaaS ERP** system. Each subdomain represents a completely independent school/institution deployment with its own isolated database.

- **Central DB** (`mindw2ft_dummy` on HostGator `119.18.54.141`): Stores the `subdomain_settings` table — the master registry of all tenants.
- **Tenant DBs**: Each subdomain has its own database, potentially on a different hosting server.
- **Dynamic DB Switching**: When a user hits `abc.ourschoolerp.com`, the app reads `subdomain_settings` for `abc`, then connects to that tenant's database for all subsequent queries.

---

## 2. Central Table: `subdomain_settings`

| Column | Type | Purpose |
|--------|------|---------|
| `id` | INT PK | Primary key |
| `server` | VARCHAR | Hosting provider key (hostgator, godaddy, etc.) |
| `subdomain` | VARCHAR(100) | Subdomain prefix (e.g., `staging`, `lakshus`) |
| `db_host` | VARCHAR(255) | IP/hostname of that tenant's DB server |
| `db_name` | VARCHAR(150) | Tenant's database name |
| `db_user` | VARCHAR(150) | DB username for tenant |
| `db_pass` | VARCHAR(150) | DB password for tenant |
| `site_name` | VARCHAR(255) | Human-readable label (school/institution name) |
| `logo_url` | VARCHAR(255) | Branding logo URL |
| `theme_color` | VARCHAR(20) | Hex color for UI theming |
| `main_domain` | VARCHAR | Full domain (e.g., `ourschoolerp.com`) |
| `status` | ENUM | `active` or `inactive` — controls accessibility |

---

## 3. Key Files

| File | Role |
|------|------|
| `mvc/controllers/Subdomains.php` | Main CRUD controller; also has `get_db_host()` for server→IP AJAX |
| `mvc/models/Subdomains_m.php` | All DB operations: list, paginate, insert, update, delete, duplicate check |
| `mvc/views/subdomains/index.php` | DataTable list view; server filter dropdown; Bulk Migration button; JS for Python API calls |
| `mvc/views/subdomains/add.php` | Add new subdomain form |
| `mvc/views/subdomains/edit.php` | Edit existing subdomain form |
| `mvc/hooks/Subdomain_loader.php` | Pre-controller CodeIgniter hook that switches DB based on subdomain in URL (currently commented out) |
| `mvc/core/MY_Controller.php` | Contains `callSubDomainProcess()` — alternative DB switching method (also available but commented) |
| `mvc/helpers/subdomain_helper.php` | `get_subdomain()` — extracts subdomain string from `$_SERVER['HTTP_HOST']` |
| `mvc/config/autoload.php` | Auto-loads `subdomain_helper` |
| `mvc/config/hooks.php` | Hook config — Subdomain_loader hook registered here |
| `python/main.py` | FastAPI server on port 8000; handles table creation in tenant DBs |
| `python/db_connection.py` | Python DB connection manager |
| `python/.env` | Python API config (central DB credentials, allowed origins) |
| `new domains/new db tables/tables.sql` | SQL schema template executed when creating tables for a new tenant |

---

## 4. Server → IP Mapping

Defined in `Subdomains.php` → `get_db_host()` method (called via AJAX to auto-fill `db_host` in forms):

| Server Key | IP Address | Used By |
|------------|-----------|---------|
| `hostgator` | 119.18.54.141 | ourschoolerp.com main |
| `godaddy` | 118.139.183.79 | alphaerp domains |
| `myschools` | 119.18.54.166 | myschools domains |
| `schoolhour` | 162.241.123.136 | schoolhour domains |
| `collegehour` | 103.76.231.69 | collegehour domains |

---

## 5. Dynamic DB Connection Flow

```
HTTP Request to abc.ourschoolerp.com
  → Subdomain_loader hook fires (pre_controller)
  → get_subdomain() extracts "abc" from HTTP_HOST
  → SELECT * FROM subdomain_settings WHERE subdomain = 'abc' AND status = 'active'
  → Build $db_config with db_host, db_name, db_user, db_pass from row
  → $this->db = $this->load->database($db_config, TRUE)
  → All subsequent CI queries hit tenant's database
```

---

## 6. CRUD Operations & Validation Rules

**Controller methods**: `index()`, `ajax_list()`, `add()`, `edit($id)`, `delete($id)`, `get_db_host()`

**DataTable** (`ajax_list()`): Supports server filter (`?server=hostgator`) and global search. Returns JSON for DataTables.

**Validation** (defined in `rules()` method):

| Field | Rule |
|-------|------|
| `server` | required, xss_clean |
| `subdomain` | required, unique (excludes self on edit), max 100 chars |
| `db_host` | required, max 255 chars |
| `db_name` | required, max 150 chars |
| `db_user` | required, max 150 chars |
| `db_pass` | required, max 150 chars |
| `site_name` | optional, max 255 chars |
| `logo_url` | optional, max 255 chars |
| `theme_color` | optional, max 20 chars |
| `status` | required, must be `active` or `inactive` |

---

## 7. Python API Endpoints (FastAPI — Port 8000)

| Endpoint | Method | Action |
|----------|--------|--------|
| `/create-tables/{subdomain_id}` | POST | Connects to that tenant's DB, runs `tables.sql` to create full schema |
| `/create-tables-bulk?server={name}` | POST | Runs above for ALL active subdomains on a given server |

Called from JS in `index.php` (the database icon button per row, and the "Bulk Migration" button).

---

## 8. UI Features (index.php)

- **Server Filter Dropdown**: Filters the DataTable to show only subdomains for the selected server. Populated from distinct `server` values in the table.
- **Add SubDomain button**: Opens `subdomains/add` form.
- **Bulk Migration button**: Enabled when a server is selected; calls `/create-tables-bulk?server=X` on the Python API.
- **DataTable columns**: #, Server, SubDomain, DB Host, DB Name, Site Name, Main Domain, Status, Actions
- **Status Badge**: Green = Active, Red = Inactive.
- **Row Action buttons**:
  1. **Create Tables** (DB icon) — calls Python API `/create-tables/{id}`
  2. **Edit** (pencil icon) — goes to `subdomains/edit/{id}`
  3. **Delete** (trash icon) — calls `subdomains/delete/{id}` with JS confirm

---

## 9. Model Methods (`Subdomains_m.php`)

| Method | Purpose |
|--------|---------|
| `get_subdomains($array)` | Fetch all / filtered subdomains |
| `get_single_subdomain($array)` | Fetch one subdomain by conditions |
| `get_subdomains_with_pagination()` | Paginated + searchable list for DataTable |
| `get_subdomains_count()` | Total count for DataTable pagination |
| `insert_subdomain($array)` | Insert new row |
| `update_subdomain($data, $id)` | Update row by ID |
| `delete_subdomain($id)` | Delete row by ID |
| `subdomain_exists($subdomain, $exclude_id)` | Check for duplicate subdomain (unique validation) |

---

## 10. Important Notes for Future Development

1. **Subdomain ≠ School record**: The subdomain entry is the tenant container. Inside each tenant DB there are separate school/college records.
2. **`tables.sql` is the schema template**: Any new DB table needed across all tenants must be added to this file.
3. **The hook is currently commented out**: `Subdomain_loader.php` may be disabled on local; the `callSubDomainProcess()` in `MY_Controller.php` is the fallback.
4. **Central DB config**: Stored in `mvc/config/development/database.php` (or `mvc/config/database.php`) — this is the connection used to read `subdomain_settings`.
5. **Python server must be running**: Table creation feature requires the FastAPI server on port 8000 to be active (there's a "START PYTHON SERVER" button in the UI).
6. **Only `staging` subdomain can manage this**: Access control in the controller checks the current subdomain before allowing access.
