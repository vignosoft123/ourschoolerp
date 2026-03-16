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
- **`MY_Controller` (`mvc/core/MY_Controller.php`)**: Extends `CI_Controller` and sets up basic config/database connections.

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
