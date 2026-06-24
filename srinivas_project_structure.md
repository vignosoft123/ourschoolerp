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
- **2026-05-14**: Implemented and documented **Firebase Push Notification System** (Section 10) — full admin UI (compose, history, setup, verify), Kreait SDK individual sends (Google removed `/batch`), cascading Role→Year→Class→Section→Users filter, only app-installed students shown in dropdown, image URL support, custom sound (`school_1`), `studentrelation` column name fix (`sr` prefix), select2 must be loaded via `headerassets`, foreground vs background notification behaviour documented.
- **2026-06-17**: Implemented **Activity Logging System** (Section 11) — common `Activity_log_m` model with `add()` method, `activity_logs` table, admin Logs UI with filters/pagination, wired into Teacher/User/Student/Exam/Delete Account Request controllers.
- **2026-06-25**: Implemented **Notification Event Config System** (Section 12) — central SMS/WhatsApp on/off toggle per event. New table `notification_event_config`, model `Notification_event_config_m`, 3rd tab at `/mailandsmstemplate/notification_config`. Global helper `notification_enabled($event_key, $type)` in `action_helper.php` guards all sends. Wired into: Fee Payment (both Global_payment controllers), Attendance (Sattendance), Student Login (Student, 4 methods), Exam Marks + Fee Reminder (Progresscardreport). WhatsApp `{{paid_amount}}` param format also fixed: now sends 3 params (`name`, `amount and Balance: X`, `date`) instead of 4.

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

---

## 10. Firebase Push Notification System

Implemented 2026-05-14. Sends FCM push notifications from the admin ERP to the school's Ionic mobile app.

---

### 10.1 Overview and Key Files

| File | Purpose |
|---|---|
| `mvc/controllers/Push_notification.php` | Admin controller — compose, send, AJAX loaders, history, setup, verify |
| `mvc/models/Push_notification_m.php` | Model — student filtering, token lookup, history logging |
| `mvc/helpers/fcm_helper.php` | `send_fcm_push_bulk()` — the only function that talks to Firebase |
| `mvc/views/push_notification/index.php` | Compose & send UI |
| `mvc/views/push_notification/history.php` | Notification log (last 100) |
| `mvc/views/push_notification/setup.php` | Service account management + verification |
| `mvc/third_party/firebase-service-account.json` | Firebase service account credentials — **not in git**, upload via Setup page |
| `mvc/controllers/api/v10/Token.php` | Mobile API — `store_token_post()` saves device token on app login |

**Admin URLs**:
- `/Push_notification` — compose & send
- `/Push_notification/history` — log of all sent notifications
- `/Push_notification/setup` — upload / verify service account JSON
- `/Push_notification/verify` — 5-step verification check

**Menu**: Administrator → Push Notification (`fa-bell`, priority 200)
**Permission name**: `push_notification`

---

### 10.2 Firebase Configuration

| Item | Value |
|---|---|
| Firebase Project ID | `our-school-erp-cbf37` |
| Mobile App Package | `io.ionic.ourschoolerp` |
| SDK (PHP) | Kreait Firebase PHP `^5.26` via Composer (`vendor/`) |
| Service Account File | `mvc/third_party/firebase-service-account.json` |

**How to update the service account**:
1. Go to Firebase Console → Project `our-school-erp-cbf37` → Settings → Service Accounts
2. Generate new private key → download JSON
3. Go to `/Push_notification/setup` in the ERP → paste full JSON → click Update
4. Click **Verify** — all 5 checks must go green

**Critical**: The `project_id` inside the service account JSON **must match** `our-school-erp-cbf37`. A mismatch causes silent auth failures.

---

### 10.3 FCM Helper — `send_fcm_push_bulk()`

**File**: `mvc/helpers/fcm_helper.php`

```php
send_fcm_push_bulk(
    array  $deviceTokens,       // array of FCM token strings
    string $title,              // notification title
    string $body,               // notification message body
    array  $data       = [],    // extra key-value data payload (e.g. ['type' => 'exam_alert'])
    string $imageUrl   = null,  // optional public HTTPS image URL (shows as banner on Android)
    string $sound      = 'school_1' // custom sound name bundled in the mobile app
): array   // ['status' => bool, 'successCount' => int, 'failureCount' => int, ...]
```

**What it sends (FCM HTTP v1 payload)**:
```json
{
  "notification": { "title": "...", "body": "...", "image": "https://..." },
  "android": {
    "priority": "high",
    "notification": { "sound": "school_1" }
  },
  "apns": {
    "payload": { "aps": { "sound": "school_1" } }
  },
  "data": { "type": "general" }
}
```

**Key design decision — individual sends, not multicast**:
Google **removed the `/batch` FCM endpoint** in 2024. Kreait's `sendMulticast()` used that endpoint and returns HTTP 404. The helper now loops each token and calls `$messaging->send()` individually. This is reliable and correct.

**Sound file requirement (mobile app)**:
- Android: place `school_1.mp3` in `android/app/src/main/res/raw/`
- iOS: place `school_1.caf` (or `.mp3`) in the Xcode project main bundle
- If not found in app bundle → falls back to default system sound automatically

**Image requirement**:
- Must be a **publicly accessible HTTPS URL**
- Recommended: JPEG/PNG, 1024×512 px, under 1MB
- Android shows it as an expanded banner notification automatically
- iOS requires a **Notification Service Extension** in the Ionic app to display images

---

### 10.4 Notification `type` Data Field

The `type` key in the data payload is metadata for the **mobile app to act on** — it is not visible to the end user in the notification itself.

| Type value | Intended app action |
|---|---|
| `general` | Open home/dashboard |
| `exam_alert` | Navigate to Exam Timetable screen |
| `fee_reminder` | Navigate to Fee / Pending Dues screen |
| `holiday` | Navigate to Holiday calendar |
| `custom` | App developer defines the behaviour |

The app reads `data.type` in its FCM handler and navigates accordingly. Without app-side handling, all types show identically.

---

### 10.5 Device Token Flow (Mobile App → Server)

```
Student opens mobile app → logs in
    ↓ App calls: POST /api/v10/token/store_token
    { studentID, device_token, platform }
    ↓
Token.php::store_token_post()
    ↓ Updates student table:
    student.device_token = <FCM token>
    student.platform     = 'android' | 'ios'
```

**Important**:
- Token is refreshed **only on login** — stale tokens remain in DB if user doesn't log in again after reinstalling the app
- FCM may return "success" for stale tokens; the device won't actually receive it
- Failed sends do NOT automatically remove old tokens from DB

**Columns on `student` table**:
- `device_token` VARCHAR(255) — the FCM registration token
- `platform` — `'android'` or `'ios'`

---

### 10.6 Recipient Filtering Logic

The compose form has a cascading filter: **Role → School Year → Class → Section → Users multi-select**.

**AJAX endpoints** (GET, return JSON):
- `load_students` — returns students with device tokens matching the filter
- `load_sections` — returns sections for a class

**Model method for dropdown**: `load_students_for_filter($schoolyearID, $classesID, $sectionID)`
- Joins `student s` ↔ `studentrelation sr` (INNER JOIN)
- Filters only students where `s.device_token IS NOT NULL AND s.device_token != ''`
- Only app-installed students appear in the Users dropdown

**`studentrelation` column name gotcha** — all columns have `sr` prefix:
```
srstudentID, srschoolyearID, srclassesID, srsectionID
```
Do NOT use plain names (`studentID`, `classesID`, etc.) — the query will fail with `result() on bool`.

**Model method for actual send**: `get_students_with_tokens($classesID, $sectionID, $studentIDs)`
- When `$studentIDs` is provided (explicit user selection from dropdown), class/section filter is **skipped** — the students were already filtered at load time via `studentrelation`. Applying `student.classesID` again causes exclusions because the student table's direct column may differ from the studentrelation enrollment record.

---

### 10.7 Database Tables

**`push_notification_log`** — history of all sent notifications:

| Column | Description |
|---|---|
| `id` | Auto-increment PK |
| `title` | Notification title |
| `message` | Notification body |
| `notification_type` | `general`, `exam_alert`, `fee_reminder`, `holiday`, `custom` |
| `recipient_type` | `all`, `class`, or `section` |
| `classesID` | FK to classes |
| `sectionID` | FK to section |
| `class_name` | Denormalized name at time of send |
| `section_name` | Denormalized name at time of send |
| `total_recipients` | Count of tokens attempted |
| `success_count` | FCM-reported successes |
| `failure_count` | FCM-reported failures |
| `sent_by_userID` | Admin who sent it |
| `sent_by_name` | Admin name at time of send |
| `sent_at` | Datetime |

---

### 10.8 Multi-Tenant Consideration

**Current setup (single-app model)**:
- One Firebase project (`our-school-erp-cbf37`) shared across all tenants
- One service account JSON (`mvc/third_party/firebase-service-account.json`) on the server
- All schools use the **same mobile app** (same APK — `io.ionic.ourschoolerp`)
- `google-services.json` in the app is the same for all schools
- This is correct — the service account is server-side only; the app package is shared

**If you ever add per-school branded apps** (different APK per school with their own `google-services.json`):
- Each school would need their own Firebase project and service account
- The service account file path would need to be per-tenant (e.g., stored in the tenant DB or at a subdomain-specific path)
- The current single-file setup would break for those schools

---

### 10.9 select2 Loading (Critical)

select2 is **NOT loaded globally** in this ERP — each controller must explicitly load it via `headerassets`. The layout footer (`page_footer.php`) loads it only if the controller sets this data:

```php
// In Push_notification::index() — required for select2 to work
$this->data['headerassets'] = [
    'css' => [
        'assets/select2/css/select2.css',
        'assets/select2/css/select2-bootstrap.css',
    ],
    'js' => ['assets/select2/select2.js'],
];
```

**Version**: select2 **v3.4.2** (old API — not v4). Initialize in the view with:
```javascript
$('.select2').select2();
```

Do NOT call `.select2()` from an AJAX callback timing issue context — initialize it once on DOM ready. The `#pn_users` multi-select gets initialized by the global `$('.select2').select2()` call since it has `class="form-control select2"`.

---

### 10.10 Foreground vs Background Notification Behaviour

| App State | Android | iOS |
|---|---|---|
| **Background / Closed** | System notification shown automatically | System notification shown automatically |
| **Foreground (app open)** | Delivered silently to app's JS handler — **no visible notification** unless app code creates a local notification | Same — no visible notification |

**Implication for testing**: Always close the app completely before testing push notifications. If the app is open, the notification arrives at the app's `onMessageReceived` / FCM plugin handler but does NOT appear in the notification tray.

---

### 10.11 Troubleshooting Reference

---

## 11. Activity Logging System

Implemented 2026-06-17. A centralised audit trail that records who did what, to which record, when, and from which IP. Callable from any controller with one method call.

---

### 11.1 Key Files

| File | Purpose |
|---|---|
| `mvc/models/Activity_log_m.php` | **The common model** — `add()`, `get_logs()`, `count_logs()` |
| `mvc/controllers/Logs.php` | Admin controller — paginated viewer with filters |
| `mvc/views/logs/index.php` | Admin UI — table with Module/Action/Type/Date/Search filters |
| `mvc/language/english/logs_lang.php` | Language strings for the Logs page |
| `new domains/new db tables/tables.sql` | `CREATE TABLE activity_logs` statement |

**Admin URL**: `/logs`
**Menu**: Bottom of sidebar (`menuName = 'logs'`, `parentID = 0`, `priority = 10`)
**Permission name**: `logs`
**Icon**: `fa-history`

---

### 11.2 `activity_logs` Table Structure

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | — |
| `module` | VARCHAR(100) | Feature name e.g. `student`, `teacher`, `delete_account_request` |
| `action` | VARCHAR(50) | `create` / `update` / `delete` / `deactivate` / `view` |
| `record_id` | INT | ID of the affected record (studentID, teacherID, etc.) |
| `record_type` | VARCHAR(50) | `student` / `teacher` / `user` / `exam` etc. |
| `old_value` | TEXT | JSON of the state **before** the change |
| `new_value` | TEXT | JSON of the state **after** the change |
| `description` | TEXT | Human-readable sentence explaining what happened |
| `performed_by_id` | INT | `userID` from session — who triggered the action |
| `performed_by_name` | VARCHAR(255) | `name` from session |
| `performed_by_usertype` | INT | `usertypeID` from session |
| `performed_by_usertype_name` | VARCHAR(100) | `usertype` string from session |
| `ip_address` | VARCHAR(45) | Client IP (`$this->input->ip_address()`) |
| `created_at` | DATETIME | Auto-set to NOW |

**Indexes**: `idx_module_action`, `idx_record (record_id, record_type)`, `idx_performed_by`

> **Note**: The table must be created by running `tables.sql` on the tenant DB. The schema_updates.json adds only the **menu, permission, and permission_relationship** rows — it does NOT create the table itself (to keep large DDL out of the migration JSON).

---

### 11.3 How to Add Logging to Any Module (Step-by-Step)

#### Step 1 — Load the model in the controller constructor

```php
public function __construct() {
    parent::__construct();
    // ... existing model loads ...
    $this->load->model('activity_log_m');  // ← add this line
}
```

> For the **Student** controller (which is very large), load the model inline inside the specific method instead of the constructor to avoid touching the shared constructor:
> ```php
> $this->load->model('activity_log_m');
> $this->activity_log_m->add([...]);
> ```

#### Step 2 — Call `add()` after every successful DB operation

```php
$this->activity_log_m->add([
    'module'      => 'module_name',     // lowercase, underscore — matches menu/permission name
    'action'      => 'create',          // create | update | delete | deactivate | view
    'record_id'   => $id,               // the PK of the affected record (int)
    'record_type' => 'student',         // what type of entity was affected
    'old_value'   => ['active' => 1],   // array OR JSON string of BEFORE state (null if create)
    'new_value'   => ['active' => 0],   // array OR JSON string of AFTER state (null if delete)
    'description' => 'Student (ID: ' . $id . ') deactivated via Delete Account Request',
]);
```

**Rules**:
- `old_value` / `new_value` accept **arrays** (auto-encoded to JSON) or a JSON string directly.
- The model auto-fills `performed_by_id`, `performed_by_name`, `performed_by_usertype`, `performed_by_usertype_name`, `ip_address`, and `created_at` from the session — you do NOT pass these.
- If the `activity_logs` table doesn't exist yet, `add()` returns `0` silently — no crash, no side-effect.
- Always call `add()` **after** the DB insert/update/delete succeeds, never before.
- For **CREATE**: set `old_value = null`, pass the new record fields in `new_value`.
- For **DELETE**: read the record first (before deleting), store its fields in `old_value`, set `new_value = null`.
- For **active/inactive toggle**: `old_value = ['active' => 1]`, `new_value = ['active' => 0]` (or reverse).

#### Step 3 — Add the module name to the Logs admin filter dropdown (optional)

In `mvc/views/logs/index.php`, add the module to the `$modules` array:

```php
$modules = ['delete_account_request','student','teacher','user','fee','exam','attendance', 'your_new_module'];
```

---

### 11.4 Modules Already Wired (as of 2026-06-17)

| Module | Controller | Actions Logged |
|---|---|---|
| `delete_account_request` | `Delete_account_request.php` | `deactivate` (mark as processed) |
| `teacher` | `Teacher.php` | `create`, `update`, `delete`, `update` (activate), `deactivate` |
| `user` | `User.php` | `create`, `update`, `delete`, `update` (activate), `deactivate` |
| `student` | `Student.php` | `update` (activate), `deactivate` |
| `exam` | `Exam.php` | `create`, `update`, `delete` |

---

### 11.5 `Activity_log_m` Method Reference

```php
// Save one log entry — auto-fills session/IP fields
$this->activity_log_m->add([
    'module'      => string,   // required
    'action'      => string,   // required
    'record_id'   => int,      // optional
    'record_type' => string,   // optional
    'old_value'   => array|string|null,
    'new_value'   => array|string|null,
    'description' => string,   // optional but strongly recommended
]);
// returns: int (inserted log ID), or 0 if table missing

// Fetch paginated logs with optional filters
$this->activity_log_m->get_logs($filters, $limit, $offset);
// $filters keys: module, action, record_type, performed_by_id, date_from, date_to, search

// Count logs (same filters, for pagination)
$this->activity_log_m->count_logs($filters);
```

---

### 11.6 Menu and Permission Setup

These three `raw` entries are in `schema_updates.json` (run via `/Schema_update/apply_updates`):

```sql
-- Menu entry (priority=10 = bottom of sidebar, below delete_account_request at 20)
INSERT INTO `menu` (`menuName`,`link`,`icon`,`status`,`parentID`,`priority`)
SELECT 'logs','logs','fa-history','1','0','10'
FROM dual WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `menuName`='logs');

-- Permission entry
INSERT INTO `permissions` (`name`,`description`)
SELECT 'logs','logs' FROM dual WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name`='logs');

-- Admin usertype gets the permission
INSERT INTO `permission_relationships` (`usertype_id`,`permission_id`)
SELECT 1, permissionID FROM `permissions` WHERE `name`='logs'
AND NOT EXISTS (SELECT 1 FROM `permission_relationships`
    WHERE `usertype_id`=1 AND `permission_id`=(SELECT permissionID FROM `permissions` WHERE `name`='logs'));
```

**Sidebar label**: add to `mvc/language/english/topbar_menu_lang.php`:
```php
$lang['menu_logs'] = 'Logs';
```

**Mobile app menu**: add `'logs'` to the `$hideMenu` array in `mvc/controllers/api/v10/Backendmenucall.php` — this is an admin-only web feature, not needed in the mobile API menu.

---

### 11.7 Priority Numbers for Sidebar Position Reference

| Menu Item | `priority` | Position |
|---|---|---|
| Settings | 30 | Near bottom |
| Delete Account Request | 20 | Below Settings |
| **Logs** | **10** | **Last / very bottom** |
| (All other menus) | >30 | Higher = earlier in sidebar |

> **Rule**: Higher `priority` number = appears **earlier** (higher) in the sidebar. Use low numbers (1–15) for items that should appear at the very bottom.

| Symptom | Cause | Fix |
|---|---|---|
| `result() on bool` in `Push_notification_m` | Wrong `studentrelation` column names (missing `sr` prefix) | Use `srstudentID`, `srschoolyearID`, `srclassesID`, `srsectionID` |
| `$(...).select2 is not a function` | select2 JS not loaded | Add `headerassets` in controller with select2 CSS+JS |
| `/batch` 404 from FCM | Google removed the batch endpoint; `sendMulticast()` broken | Use individual `$messaging->send()` loop — already fixed in `fcm_helper.php` |
| FCM says Delivered=1 but phone doesn't vibrate | App is open (foreground) | Close app fully and test again |
| FCM says Delivered=1 but notification never arrives | Stale device token in DB | Student must log into app again to refresh token |
| `Firebase Not Configured` banner on compose page | Service account missing or wrong project | Go to `/Push_notification/setup` and upload correct JSON for `our-school-erp-cbf37` |
| Users dropdown shows 0 students | No students have logged into the app (no device tokens) | Students must log in via mobile app to register their token |
| Sent to fewer than selected users | `get_students_with_tokens` was applying class filter on top of explicit IDs | Fixed: class/section filter is skipped when `userIDs[]` are posted |
| Image not showing in notification (iOS) | iOS requires Notification Service Extension in Ionic app | Mobile app team must add the extension |

---

## 12. Notification Event Config System

Implemented 2026-06-25. A central on/off switch for SMS and WhatsApp sending per event. Accessible as a 3rd tab on the Mail/SMS Template page.

---

### 12.1 Key Files

| File | Purpose |
|---|---|
| `mvc/models/Notification_event_config_m.php` | Model — `get_all()`, `update_by_key()` |
| `mvc/controllers/Mailandsmstemplate.php` | `notification_config()` method — GET loads view, POST saves flags |
| `mvc/views/mailandsmstemplate/notification_config.php` | Admin UI — event table with SMS + WhatsApp checkboxes |
| `mvc/helpers/action_helper.php` | `notification_enabled($event_key, $type)` — global helper, loaded on every request |

**Admin URL**: `/mailandsmstemplate/notification_config`
**Tab**: 3rd tab alongside "Mail / SMS Template" and "Whatsapp Templates"

---

### 12.2 DB Table — `notification_event_config`

| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | — |
| `event_key` | VARCHAR(50) UNIQUE | Machine key used in code e.g. `fee_payment` |
| `event_name` | VARCHAR(100) | Human-readable label shown in the UI |
| `sms_enabled` | TINYINT(1) DEFAULT 1 | 1 = send SMS, 0 = block SMS for this event |
| `whatsapp_enabled` | TINYINT(1) DEFAULT 1 | 1 = send WhatsApp, 0 = block WhatsApp for this event |

**Seeded via migration** — run `/Schema_update/apply_updates` once after deploy. All 7 rows default to enabled (1, 1).

---

### 12.3 Seeded Events

| `event_key` | `event_name` | Wired In |
|---|---|---|
| `fee_payment` | Fee Payment | `Global_payment_new.php`, `Global_payment.php` |
| `attendance` | Absent Attendance | `Sattendance.php` |
| `student_registration` | Student Registration / Login | `Student.php` (4 methods) |
| `exam_marks` | Exam Marks | `Progresscardreport.php` |
| `fee_reminder` | Fee Reminder | `Progresscardreport.php` |
| `progress_card` | Progress Card | `Progresscardreport.php` (config row only) |
| `holiday_intimation` | Holiday Intimation | Config row only — wire when needed |

---

### 12.4 The `notification_enabled()` Helper

Defined in `mvc/helpers/action_helper.php` (globally loaded on every request — no need to load it manually).

```php
notification_enabled($event_key, $type)   // $type = 'sms' or 'whatsapp'
// Returns: true (allow send) or false (block send)
// Safe default: returns true if table/row missing — existing sends never break before migration runs
```

**Usage pattern** (wrap every SMS/WhatsApp send with this guard):
```php
// SMS guard
if (notification_enabled('fee_payment', 'sms')) {
    $this->userConfigSMS($student, 'msg91');
}

// WhatsApp guard (combined with form checkbox if applicable)
if ($this->input->post('send_whatsapp') && notification_enabled('fee_payment', 'whatsapp')) {
    $this->Whatsapp_m->whatsapp_config_send($student);
}
```

---

### 12.5 How to Add a New Event

When building a new feature that sends SMS or WhatsApp:

1. **Add seed row** to `schema_updates.json` (insert type) and `schema_updates.sql` (WHERE NOT EXISTS):
   ```sql
   INSERT INTO `notification_event_config` (`event_key`, `event_name`, `sms_enabled`, `whatsapp_enabled`)
       SELECT 'my_new_event', 'My New Event', 1, 1
       FROM dual WHERE NOT EXISTS (SELECT 1 FROM `notification_event_config` WHERE `event_key` = 'my_new_event');
   ```

2. **Wrap the send call** in the controller with `notification_enabled('my_new_event', 'sms')` / `notification_enabled('my_new_event', 'whatsapp')`.

3. Run `/Schema_update/apply_updates` — the new row appears automatically in the Notification Config tab.

---

### 12.6 Important Rules

- **Templates are separate from config** — adding a new SMS/WhatsApp template from the frontend does NOT require a new `notification_event_config` row. The config table controls code-level events, not template content.
- **Config is the master switch** — if `sms_enabled = 0`, SMS is blocked even if other settings (like `setting.is_fee_sms`) are enabled.
- **Default is allow** — if a row is missing or the table doesn't exist yet, `notification_enabled()` returns `true` so existing functionality never breaks.
- **Maintenance log** entry: add to Section 7 whenever a new event is wired.
