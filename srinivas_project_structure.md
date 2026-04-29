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
- **Migrations**: `mvc/migrations/schema_updates.json` — all DB schema changes live here (see Section 5).

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
- **Sidebar Menu Language Keys**: Every menu item's display label is looked up from **`mvc/language/english/topbar_menu_lang.php`** — this file is loaded globally by `Admin_Controller` for every page. The pattern is `$lang['menu_{menuName}']` where `{menuName}` matches the `menuName` column in the `menu` table.
  - **Gotcha**: If you add a new menu entry (in `schema_updates.json` or directly in DB) and the label does not appear in the sidebar, the fix is always to add the key to `topbar_menu_lang.php`. Adding it only to the controller's own language file (e.g. `youtube_lang.php`) will NOT work for the sidebar because that file is only loaded when that specific controller runs.
  - **Example**: `$lang['menu_youtube_links'] = 'YouTube Links';` added to `topbar_menu_lang.php` after the sidebar showed no label for the YouTube Links menu item.
- **Subdomain/Licensing**: The system contains logic in `Admin_Controller` (`_my_settings`, `check_aapi`) that performs site-key verification and potential remote checks.

## 5. Database Migration System

**File**: `mvc/migrations/schema_updates.json`

This is a JSON array of migration entries. The system reads each entry and runs the query **only if the safety check passes** — so it is safe to run repeatedly without duplicating changes.

### Entry Types

| `type` | Safety Check Field | When to Use |
|---|---|---|
| `alter` | `check_column: { table, column }` | Adding/modifying a column |
| `create` | `check_table: "table_name"` | Creating a new table |
| `insert` | `check_row: { table, where: {} }` | Inserting a default/seed row |
| `raw` | _(none — always runs)_ | Index creation, complex SQL with no simple check |

### Entry Skeleton Examples

```json
// ALTER — add a column
{
    "query": "ALTER TABLE `table_name` ADD `column_name` INT(11) NOT NULL DEFAULT '0'",
    "type": "alter",
    "check_column": { "table": "table_name", "column": "column_name" }
}

// CREATE — new table
{
    "query": "CREATE TABLE `new_table` ( `id` int(11) NOT NULL AUTO_INCREMENT, ... PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
    "type": "create",
    "check_table": "new_table"
}

// INSERT — seed/default row
{
    "query": "INSERT INTO `permissions` (`name`, `description`) VALUES ('my_feature', 'my_feature')",
    "type": "insert",
    "check_row": { "table": "permissions", "where": { "name": "my_feature" } }
}

// RAW — no guard, always runs (use only when a check is not possible)
{
    "query": "ALTER TABLE mark ADD INDEX idx_name (col1, col2)",
    "type": "raw"
}
```

> **Rule**: Every new DB change (new table, new column, seed data) MUST be added as an entry in this file. Never ask the user to run raw SQL manually — add it here instead.

---

## 7. Maintenance Log
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
- **2026-04-29**: Documented **Sticky Bottom Action Bar** and **Scroll-to-Top Button** as reusable UI patterns (Section 8). Reference implementation: Student Attendance page (`mvc/views/sattendance/add.php`).
- **2026-04-30**: Documented **Database Migration System** (Section 5) — `mvc/migrations/schema_updates.json` with entry types, safety checks, and skeleton examples.
- **2026-04-30**: Documented **Sidebar Menu Language Key gotcha** (Section 4) — all menu labels must go in `topbar_menu_lang.php`, not the controller's own lang file.

## 8. Reusable UI Patterns

### 8.1 Sticky Bottom Action Bar
A fixed bar that sticks to the bottom of the viewport while the user scrolls. Use this on any page that has save/submit actions so the buttons are always visible.

**When to use**: Any list/form page where the user scrolls through data before saving (e.g., attendance, bulk mark, report pages).

**HTML — place just before the closing `</div><!-- /.box -->` of the page:**
```html
<div id="action-bar" style="
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1050;
    background: rgba(255,255,255,0.88);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    border-top: 1px solid #ddd;
    padding: 10px 24px;
    text-align: right;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.08);
">
    <span class="btn btn-default your-save-class">Submit</span>&nbsp;&nbsp;
    <!-- add more buttons as needed -->
</div>
<div style="height: 60px;"></div><!-- spacer so last row isn't hidden behind bar -->
```

**Key rules:**
- `z-index: 1050` — sits above Bootstrap modals backdrop (1040) but below modals (1050+). Adjust if conflicts.
- `rgba(255,255,255,0.88)` — 88% opacity white. Change alpha for lighter/darker feel.
- The `height: 60px` spacer div must follow immediately so the page content is not clipped when scrolled to the bottom.
- **Conditional display**: Wrap the entire bar in a PHP `if` condition so it only renders when there is data on the page (e.g., `<?php if (customCompute($students)) { ?> ... <?php } ?>`). This hides buttons when no class/section is selected yet.

---

### 8.2 Scroll-to-Top Button
A circular floating button (↑) that appears after scrolling down 200px and smoothly scrolls back to the top.

**When to use**: Any page with a long scrollable table (attendance, student list, reports, etc.).

**HTML — place just before the first `<script>` block at the bottom of the view file:**
```html
<button id="scroll-to-top-btn" title="Back to top" style="
    display: none;
    position: fixed;
    bottom: 70px;
    right: 24px;
    z-index: 1100;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: rgba(51,122,183,0.80);
    color: #fff;
    font-size: 18px;
    line-height: 40px;
    text-align: center;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.20);
    transition: opacity 0.3s;
    padding: 0;
">&#8679;</button>
```
> `bottom: 70px` keeps it above the sticky action bar (60px tall). If no action bar, use `bottom: 24px`.

**JavaScript — add inside any existing `<script>` block (requires jQuery, already loaded globally):**
```javascript
// Scroll to top button
$(window).on('scroll', function() {
    if ($(this).scrollTop() > 200) {
        $('#scroll-to-top-btn').fadeIn(300);
    } else {
        $('#scroll-to-top-btn').fadeOut(300);
    }
});
$('#scroll-to-top-btn').on('click', function() {
    $('html, body').animate({ scrollTop: 0 }, 400);
});
```

**Key rules:**
- `z-index: 1100` — must be above the sticky action bar (1050).
- `&#8679;` is the ↑ (upwards double arrow) Unicode character. Can replace with a FontAwesome icon: `<i class="fa fa-chevron-up"></i>`.
- The button starts `display: none` and is shown/hidden by the scroll handler.

---

**Reference implementation**: `mvc/views/sattendance/add.php` (Student Attendance page, added 2026-04-29).
