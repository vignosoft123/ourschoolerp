# Project Structure and Technical Overview: OurSchoolERP

This document serves as a technical blueprint for the **OurSchoolERP** project. It outlines the architecture, environment, and core logic to ensure consistent and efficient development in future sessions.

> [!IMPORTANT]
> **To the AI Assistant:** Read this file entirely before starting any maintenance or new feature development to understand the project's specific patterns and constraints.

## 1. Project Environment
- **Product Base**: Inilabs School Management System (Customized).
- **Technology Stack**: PHP (CodeIgniter Framework), MySQL, jQuery, Bootstrap.
- **Local Development Path**: `c:\xampp\htdocs\ourschoolerp`.
- **Operating System**: Windows.

## 2. Directory Structure (MVC)
- **Controllers**: `mvc/controllers/`
- **Models**: `mvc/models/`
- **Views**: `mvc/views/`
- **Configuration**: `mvc/config/` (Environment-specific DB settings are in `mvc/config/development/database.php`).
- **Helpers**: `mvc/helpers/` (Contains CLI/UI utilities).
- **Libraries**: `mvc/libraries/` (Core system logic).
- **Core Extensions**: `mvc/core/` (Custom base classes like `MY_Controller`).
- **Languages**: `mvc/language/english/` (Translation files).

## 3. Core Architecture and Patterns
### Base Controllers
- **`Admin_Controller` (`mvc/libraries/Admin_Controller.php`)**: The most critical file. It handles:
    - Session initialization and login checks.
    - Global data loading (e.g., academic years, site info, and the recent **College Group** addition).
    - Permission management (`_permissionManager`).
    - Menu tree generation (`menuTree`).
- **`MY_Controller` (`mvc/core/MY_Controller.php`)**: Extends `CI_Controller`. This is the **entry point for every request**. It does the following in order:
    1. Loads `iniconfig` config and the session library.
    2. Calls `callSubDomainProcess()` — the **multi-tenant DB switching logic**:
        - Detects the current subdomain via `get_subdomain()` helper.
        - Stores the subdomain in the session (`subdomain` key) if not already set.
        - Connects to the **master/default DB** and queries `subdomain_settings` table for a row matching `[subdomain, status='active']`.
        - If not found → shows error: *"Subdomain settings not found or License Expired."*
        - If found → builds a dynamic DB config from the row's `db_host`, `db_user`, `db_pass`, `db_name` fields.
        - **Overrides `$this->db` globally** with this tenant-specific connection for the rest of the request.
    3. Checks `config_install()` — redirects to `/install` if system is not installed.
    - **Key table**: `subdomain_settings` (in master DB) — columns: `subdomain`, `status`, `db_host`, `db_user`, `db_pass`, `db_name`.
    - **Gotcha**: Every controller inherits this, so `$this->db` always points to the **tenant DB**, not the master DB, by the time any controller method runs.

### UI and Permissions
- **Permission System**:
    - **Tables**: `permissions`, `permission_relationships`, `usertype`.
    - **Logic**: Permissions are stored in the session under the key `master_permission_set`. 
    - **Checker**: `permissionChecker($permission_name)` (defined in `action_helper.php`).
- **Action Buttons**: Standardized buttons like `btn_edit($url, $label)` and `btn_delete($url, $label)` are generated in `mvc/helpers/action_helper.php`.

### Sidebar Menu
- **Table**: `menu`.
- **Structure**: Uses `parentID` (e.g., `20` for Administrator) and `priority` to control ordering.
- **Dynamic Loading**: Handled in `Admin_Controller->_permissionManager`.

## 4. Specific Knowledge and "Gotchas"
- **Session Refreshes**: When adding new permissions, they won't automatically appear for logged-in users unless the `master_permission_set` is updated in the session. A "hot-reload" logic was added to `Admin_Controller` to force a reload if specific new permissions (like `college_group`) are missing.
- **Language Keys**: Menu names in the sidebar mapping to language keys (e.g., `menu_college_group`).
- **Subdomain/Licensing**: The system contains logic in `Admin_Controller` (`_my_settings`, `check_aapi`) that performs site-key verification and potential remote checks.

## 5. Maintenance Log
*If you find new patterns, core files, or unique project traits, add them below for future reference.*

- **2026-03-09**: Initialized `project_structure.md`. Documented College Group integration and Permission Hot-Reload logic.
- **2026-03-09**: Verified Standard Admin usertypeID is `1` and `superadmin` is the primary account.
- **2026-03-09**: Implemented "Safe Loading" in `Admin_Controller` to prevent site crashes if the `college_groups` table is missing before migration.
- **2026-03-17**: Documented `MY_Controller` in full — multi-tenant subdomain-based DB switching via `subdomain_settings` master table. Every request dynamically overrides `$this->db` with a tenant-specific connection before any controller logic runs.
- **2026-03-27**: **Admission Enquiry Module**:
    - Implemented with a **premium modal UI** (1100px horizontal layout).
    - **Session Key Gotcha**: The correct session key for the user ID is **`loginuserID`** (lowercase 'u'). Using `loginUserID` will return `NULL`.
    - **AJAX Pattern**: Controller index/edit methods return JSON for modal updates. Always use `header('Content-Type: application/json')` and check `$this->db->error()` manually as `db_debug` is often `FALSE`.
    - **Topbar**: Navigation shortcuts are located in `mvc/views/components/page_topbar.php`.
