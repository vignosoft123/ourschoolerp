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
- **2026-05-23**: Documented **SMS and WhatsApp Sending System** (Section 9) — three modes: plain SMS, WhatsApp with template, WhatsApp with PDF attachment. Gateway: MSG91 (smslogin.co) for SMS, MindWhile (bwa.mindwhile.com) for WhatsApp. Active model: `Whatsapp_m`. Template table: `whatapp_templates`. Logging: `mailandsms`, `sms_error_logs`, `whatsapp_logs`. `WhatsappLibrary.php` is NOT used.
- **2026-05-24**: Documented **Bootstrap Modal AdminLTE Gotcha** (Section 8.4) — two-bug root cause: direct selector timing + `position:fixed` clipping by parent containers. Fix: `$('body').append($('#modal').detach())` on DOM ready + delegated `$(document).on('click', '#saveBtn', ...)` for all modal inner buttons. Reference: `mvc/views/student/index.php` Change Login Details modal.
- **2026-05-24**: **Student Login Credentials feature** — on student creation, sends SMS (template ID=3) and WhatsApp (`STUDENT_REGISTRATION` template) with username/password/URL. Per-row SMS, WhatsApp, and Change Login buttons added to student list. Bulk "Send Login Details" dropdown button (checkbox-gated, sends SMS and/or WhatsApp in parallel). `tagConvertor()` `{{password}}` fixed to use `$user->phone` (was hardcoded `"123456"`). New controller methods: `send_login_sms`, `send_login_whatsapp`, `send_bulk_login_sms`, `send_bulk_login_whatsapp`, `update_login_details`. 4 migration rows added to `schema_updates.json`.
- **2026-05-09**: Documented **Client-Side Excel Export via SheetJS** (Section 8.3) — clone table → strip tooltip attrs → replace em-dashes → `table_to_sheet` → `writeFile`. Reference: Invoice Report (`mvc/views/report/invoicereport/InvoicereportReport.php`). colspan/rowspan merged headers preserved automatically.

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

---

### 8.3 Client-Side Excel Export (SheetJS)

Export any HTML table to `.xlsx` entirely in the browser — no server-side PHP or backend endpoint needed.

**Library**: SheetJS — load via CDN at the **bottom of the view file** (after the table markup):
```html
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
```

**Pattern** (Invoice Report reference: `mvc/views/report/invoicereport/InvoicereportReport.php`):
```javascript
$('#your-excel-btn').on('click', function () {
    var wb = XLSX.utils.book_new();

    // 1. Clone the table so the original DOM is not mutated
    var tbl = document.getElementById('your-table-id').cloneNode(true);

    // 2. Strip Bootstrap tooltip attributes — they appear as extra columns in Excel
    $(tbl).find('[data-toggle]').removeAttr('data-toggle data-placement title');

    // 3. Replace em-dash placeholder cells with blank (cosmetic — avoids "—" in Excel)
    $(tbl).find('td, th').each(function () {
        if ($(this).html() === '&mdash;' || $(this).text().trim() === '—') {
            $(this).text('');
        }
    });

    // 4. Convert cleaned table to worksheet
    var ws = XLSX.utils.table_to_sheet(tbl, { raw: false });
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet Name');

    // 5. Build a descriptive filename and trigger download
    var label = $('#some-select option:selected').text().trim().replace(/\s+/g, '_');
    var today = new Date();
    var dateStr = today.getFullYear() + '-' +
                  String(today.getMonth() + 1).padStart(2, '0') + '-' +
                  String(today.getDate()).padStart(2, '0');
    XLSX.writeFile(wb, 'ReportName_' + label + '_' + dateStr + '.xlsx');
});
```

**Key rules:**
- Always **clone** the table before manipulating it so live tooltips and DOM state are preserved on screen.
- **Strip `data-toggle`/`title` attributes** from the clone first — SheetJS reads `title` as a cell value, producing extra garbage columns.
- Replace `&mdash;` (`—`) display placeholders with empty strings so Excel cells are truly blank, not literal dash characters.
- Filename convention used in this project: `ReportName_SelectedFilter_YYYY-MM-DD.xlsx`.
- The `{ raw: false }` option in `table_to_sheet` tells SheetJS to read cell text as-is (formatted strings) rather than trying to parse numbers — avoids mis-formatting large numbers.
- **colspan/rowspan headers** (pivot-style tables) are handled automatically by SheetJS — merged cells are preserved in the `.xlsx` output.

**When to use**: Any report view that renders a `<table>` with a "Download Excel" button. This avoids a round-trip to the server and works instantly even for large tables.

---

### 8.4 Bootstrap Modal — AdminLTE Gotcha and Correct Pattern

AdminLTE 2.x places all page content inside `<aside class="right-side"> … <section class="content"> … <div class="col-xs-12">`. Any Bootstrap modal placed inside this container (i.e., in a sub-view file) **may silently fail to open** due to two compounding bugs:

1. **`$('#id').on('click', ...)` with direct selector binds before the HTML exists** — if the `<script>` block runs *above* the modal HTML in the page (common in sub-views), jQuery finds 0 elements and registers no handler.
2. **`position: fixed` clipping** — if a parent element has a CSS `transform`, `filter`, or `perspective`, `position: fixed` becomes relative to that parent, hiding the modal behind the page content.

#### The Fix — always apply both of these

**Step 1 — Move modal to `<body>` on DOM ready (eliminates clipping):**
```javascript
$(function() {
    $('body').append($('#yourModal').detach());
});
```
Place this in any `<script>` block in the view. It moves the modal out of the nested content containers and makes it a direct child of `<body>`, where Bootstrap expects it.

**Step 2 — Use delegated events for all modal handlers (eliminates timing issue):**
```javascript
// WRONG — direct selector runs before modal HTML is parsed:
$('#saveBtn').on('click', function() { ... });

// CORRECT — delegated event works regardless of DOM order:
$(document).on('click', '#saveBtn', function() { ... });
```
Use `$(document).on('click', '#saveBtn', handler)` for ANY element that is inside the modal HTML, even if the modal is placed at the bottom of the page below the `<script>` block.

**Step 3 — Place modal HTML at the very end of the view file, after all `</script>` tags and outside all tables:**
```html
</script>

<!-- Modal at the END of the view, outside all tables and divs -->
<div class="modal fade" id="yourModal" tabindex="-1" role="dialog">
    ...
</div>
```
Bootstrap DataTables moves `<tr>` / `<td>` content aggressively. Any modal left inside a `<table>` will be ejected from the DOM.

#### Full Working Template (Change Login Details style)
```html
<!-- At END of view file -->
<div class="modal fade" id="changeLoginModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width:380px;margin-top:120px;">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;">
            <div class="modal-header" style="background:#17a2b8;color:#fff;padding:14px 20px;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-key"></i> Change Login Details</h4>
            </div>
            <div class="modal-body" style="padding:20px 24px;">
                <input type="hidden" id="clStudentID">
                <!-- form fields here -->
                <div id="clError" style="display:none;color:#e53935;font-size:12px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" id="changeLoginSaveBtn" class="btn btn-info btn-sm">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>
```
```javascript
// In the <script> block (can be above the modal HTML):
$(function() { $('body').append($('#changeLoginModal').detach()); }); // fix clipping

$(document).on('click', '.btn-open-modal', function() {      // open
    /* populate fields */
    $('#changeLoginModal').modal('show');
});

$(document).on('click', '#changeLoginSaveBtn', function() {  // save (delegated)
    /* validate, AJAX, then: */
    $('#changeLoginModal').modal('hide');
});
```

**Reference implementation**: `mvc/views/student/index.php` — Change Login Details modal (added 2026-05-24).

#### Modal Visual Design Style (project standard)

| Property | Value | Notes |
|---|---|---|
| Max-width | `380px` | Compact — not full Bootstrap default (600px) |
| Margin-top | `120px` | Drops below the topbar |
| Border-radius | `10px` | On `.modal-content` + `overflow:hidden` to clip child corners |
| Header background | `#17a2b8` (Bootstrap info blue-teal) | `color:#fff` for text |
| Header padding | `14px 20px` | Tighter than Bootstrap default |
| Close button | `color:#fff; opacity:1; font-size:20px;` | Override Bootstrap's grey default |
| Title font-size | `15px; font-weight:700` | Compact, bold |
| Body padding | `20px 24px` | Slightly asymmetric for visual breathing room |
| Footer background | `#f8f9fa` | Light grey, `padding:12px 20px` |
| Error text | `color:#e53935; font-size:12px;` | Inline below last field, hidden by default |
| Save button | `btn btn-info btn-sm` with `fa-save` icon | Matches header colour |
| Cancel button | `btn btn-default btn-sm` | Plain, dismisses modal |
| Input fields | `form-control` with `border-radius:6px` | Slightly rounded inputs |
| Label style | `font-size:13px; font-weight:600; color:#333;` | Override the global `label { color:#fff }` in this view |

**Context line (student name)**: shown above the fields as `Student: <strong id="clStudentName"></strong>` in `font-size:13px;color:#555;`.

---

## 9. SMS and WhatsApp Sending System

This section documents the three sending modes used across multiple modules (Progress Card Report, Student Attendance, Fee Reminders, etc.).

### 9.1 Three Sending Modes

| Mode | Description | Key Method / Entry Point |
|---|---|---|
| **SMS (no template)** | Plain-text SMS built inline in the controller | `userConfigSMS()` → `allgetway_send_message()` |
| **WhatsApp (with template)** | Template fetched from DB, tags replaced, sent via MindWhile API | `userConfigWhatsapp()` → `Whatsapp_m->sendWhatsapp()` |
| **WhatsApp + PDF attachment** | Same as above but includes a generated PDF media file | `Whatsapp_m->sendWhatsapp_bulk_batch_with_media_progresscard()` |

---

### 9.2 SMS (Plain / Without Template)

**Gateway used**: MSG91 via `smslogin.co`
**Library file**: `mvc/libraries/Msg91.php`

**Flow**:
1. Controller builds the message string inline (may use `tagConvertor()` for personalization).
2. Calls `userConfigSMS($message, $user, $usertype, $gateway, $yearid)` (defined locally in each controller).
3. That calls `allgetway_send_message($to, $message, $gateway, ...)` which routes to the correct gateway.
4. For MSG91: sends via `https://smslogin.co/v3/api.php` with `username`, `apikey`, `senderid`, `mobile`, `message`, `templateid`.
5. API returns a string containing `"campid:XXXXX"` on success.
6. Logs to `mailandsms` table: `campid`, `usertypeID`, `users`, `type`, `message`, `year`, `senderusertypeID`, `senderID`.
7. On failure: logs request URL + response to `sms_error_logs` table.

**Settings source**: `smssettings` table, rows where `types='msg91'`:
- `msg91_username`, `msg91_password` (apikey), `msg91_senderID`, `msg91_PEID`, `msg91_register_school_name`

**Gateway routing** (`allgetway_send_message`): supports `clickatell`, `twilio`, `bulk`, `msg91` — value comes from `messageType` in site settings.

---

### 9.3 WhatsApp (With Template, No Attachment)

**API provider**: MindWhile
**API endpoint**: `http://bwa.mindwhile.com/api/sendmsgutil.php`
**Model file**: `mvc/models/Whatsapp_m.php` — `sendWhatsapp($to, $message, $template_name)` method

**Flow**:
1. Controller fetches the WhatsApp template row from `whatapp_templates` table using `short_name LIKE '%KEYWORD%'`.
2. Extracts `template_name` (the registered template ID) and `params` (comma-separated tag list).
3. Calls `userConfigWhatsapp($user, $message, $template_name)` (defined locally in each controller).
4. That calls `tagConvertor()` to replace tags (`[name]`, `{{student_name}}`, `[class]`, etc.) with real values.
5. Calls `Whatsapp_m->sendWhatsapp($phone, $processedMessage, $template_name)`.
6. Model builds URL: `user`, `pass`, `sender`, `phone`, `text` (template_name), `priority=wa`, `stype=normal`, `Params` (processed message).
7. Logs to `whatsapp_logs` table: `request_url`, `api_response`, `type`, `message`, `template_name`, `created_on`, `http_code`, `curl_error`.

**Template table** (`whatapp_templates` columns):
- `short_name` — internal key used for lookup (e.g. `EXAM_MARKS`, `ATTENDANCE`, `PROGRESS_CARD`, `FEE_REMINDER`, `FEE_PAID`)
- `template_name` — the registered template ID sent to the API as the `text` parameter
- `params` — comma-separated list of template variable names

**Settings source**: `smssettings` table, rows where `types='whatsapp'`:
- `whatsapp_user`, `whatsapp_pass`, `whatsapp_sender`

**Single vs Bulk**:
- Single: `Whatsapp_m->sendWhatsapp()` — one student at a time
- Bulk (no attachment): `Whatsapp_m->sendWhatsapp_bulk_batch($dataBatch, $templateName)` — chunked at 50 per batch

---

### 9.4 WhatsApp + PDF Attachment

**Model method**: `Whatsapp_m->sendWhatsapp_bulk_batch_with_media_progresscard($dataBatch, $templateName)`
**Internal API method**: `send_to_api_with_media_progresscard($payload)`

**Flow**:
1. Controller loops through student IDs, generates a PDF for each using `generateAttachment()`.
2. Builds a `$dataBatch` array where each entry has:
   - `phone` — parent mobile
   - `message` — comma-separated template params (e.g. `{student_name},{exam_name}`)
   - `url` — publicly accessible URL of the generated PDF
   - `htype` — `'document'` (or `'image'`/`'video'`)
   - `fname` — filename shown to recipient
3. Calls `sendWhatsapp_bulk_batch_with_media_progresscard($dataBatch, $templateName)`.
4. Model sends each message to MindWhile API with extra query params: `htype`, `fname`, `url`.
5. Success detection: checks API response for pattern `'S.XXXXX'`.
6. Comprehensive cURL error logging with 30-second timeout.
7. Logs to `whatsapp_logs` table same as 9.3.

**Gotcha**: The `send_to_api_with_media_progresscard()` method cleans params before sending — removes newlines (`\n`, `\r`), replaces special dashes (en/em dash → regular `-`), and trims whitespace. Apply the same cleanup when building params for any new PDF attachment sender.

---

### 9.5 Template Tag Replacement (`tagConvertor`)

Both SMS and WhatsApp use a `tagConvertor($userTags, $user, $message)` method. It exists in multiple places:
- `mvc/controllers/Sattendance.php` (attendance-specific tags)
- `mvc/models/Whatsapp_m.php` (fee/WhatsApp tags)
- `mvc/controllers/Progresscardreport.php` (marks/progress card tags)

**Supported tag formats** (both are equivalent):
- Single bracket: `[name]`, `[class]`, `[roll]`, `[absent_date]`, `[school_name]`, `[amount]`
- Double bracket: `{{student_name}}`, `{{roll_no}}`, `{{absent_date}}`, `{{school_name}}`

**Rule when implementing in a new module**: Copy the `tagConvertor()` from the closest existing module, extend it for the new module's tags, and keep both bracket formats supported.

---

### 9.6 Common Patterns and Gotchas

- **Phone number source**: Use `alternative_phone1` first if available, otherwise fall back to primary phone. Apply this consistently across all WhatsApp senders.
- **`whatapp_templates` lookup**: Always use `LIKE '%SHORT_NAME%'` (not exact match) — existing code does this and templates may have prefix/suffix variations.
- **`smssettings` lookup**: Query by `types` column (e.g. `types='msg91'`, `types='whatsapp'`) and pivot `field_names` → `field_values` into an associative array.
- **Logging is mandatory**: Every send attempt (success or failure) must be logged — SMS to `mailandsms`/`sms_error_logs`, WhatsApp to `whatsapp_logs`.
- **`WhatsappLibrary.php` is NOT used** — it contains debug `echo/die` code. The active implementation is entirely in `Whatsapp_m` model.
- **School-specific branches**: Some controllers have `if (sender_id == 'VIVEKA')` type blocks that alter template params. When adding a new module, keep it generic unless school-specific behavior is explicitly requested.
- **Batch size**: Bulk WhatsApp sending is chunked at **50 messages per batch** to avoid API timeouts.
- **cURL timeout**: Set to **30 seconds** for media/PDF sends; normal sends use default.

---

### 9.7 Reference Implementations

| Feature | Controller / File | Method |
|---|---|---|
| SMS — marks notification | `mvc/controllers/Progresscardreport.php` | `send_marks_to_sms()` |
| WhatsApp — marks notification | `mvc/controllers/Progresscardreport.php` | `send_marks_to_whatsapp()` |
| WhatsApp — PDF progress card | `mvc/controllers/Progresscardreport.php` | `send_pdf_to_whatsapp()` |
| SMS — attendance absent | `mvc/controllers/Sattendance.php` | `sendAbsentSMS()` |
| WhatsApp — attendance absent | `mvc/controllers/Sattendance.php` | `sendAbsentWhatsapp()` |
| WhatsApp — fee reminder | `mvc/controllers/Progresscardreport.php` | `send_balance_whatsapp()` |
| WhatsApp model (all sends) | `mvc/models/Whatsapp_m.php` | `sendWhatsapp()`, `sendWhatsapp_bulk_batch*()` |
| SMS gateway library | `mvc/libraries/Msg91.php` | `send()` |
