# OurSchoolERP — Issue Fix Log

This file is a running log of bugs found and fixed in the project. Each entry records the date, the issue, the root cause, and the exact fix applied (with file paths).

> [!IMPORTANT]
> **To the AI Assistant:** Before investigating a new bug, check this file for a similar past issue — the root cause may already be documented here, or the same class of bug may exist in another module. After fixing any issue, add a new entry here following the same format.

---

## Entry Format

```
### YYYY-MM-DD: <Short Issue Title>

**Reported as**: How the user described the symptom.

**Root Cause**: The actual underlying reason, with file:line references.

**Fix**: What was changed, with file paths. Include code snippets only if short and clarifying.

**Files Changed**:
- `path/to/file.php`
```

---

## Issues

### 2026-07-24: Student Marks Tab Showing Exams From Wrong Academic Year

**Reported as**: Currently on academic year 2026-2027, which has only 1 exam created (verified on the Exam listing page). But opening a student's profile → Marks tab showed multiple exam sections (10th TEST 1, FA-4, CP 1, etc.) — these actually belonged to the previous year, 2025-2026.

**Root Cause**: The Exam listing page (`Exam::index()`) correctly filters exams by `exam.academic_year = session('defaultschoolyearID')`. However, the student profile's Marks tab is built by a separate private `getMark()` method that:
1. Called `$this->exam_m->get_exam()` with **no filter at all**, fetching every exam row from every academic year.
2. Merged that with `Marksetting_m::get_marksetting_markpercentages()`, which **also** builds its exam list via `$this->exam_m->get_order_by_exam([], FALSE)` — again unfiltered by year — so a class's mark-setting entries for exams from any past year would still be included.

Since neither of these two paths filtered by the currently selected school year, any exam that ever had mark settings configured for a class showed up in the Marks tab regardless of which year was selected. Actual mark **values** were correctly scoped by `schoolyearID` (via `Mark_m::student_all_mark_array()`), so this was purely an "extra empty exam sections shown" bug, not a data-leak of actual marks.

**Fix**: In each `getMark()` implementation:
1. Changed the exam lookup to filter by year: `$this->exam_m->get_order_by_exam(array('academic_year' => $schoolyearID))` instead of `get_exam()`.
2. Intersected the class's marksettings against that year-filtered exam list before handing off to the view/response: `array_intersect_key($classMarksettings, $exams)`.

The shared model `Marksetting_m::get_marksetting_markpercentages()` was **deliberately left unfiltered** — it's called from ~20 other controllers (reports, promotion, mark entry, etc.) that already scope their own exam selection; changing its behavior globally would have been much higher risk than fixing the three call sites that weren't scoping their results.

**Files Changed**:
- `mvc/controllers/Student.php` — `getMark()` (admin panel student view, Marks tab)
- `mvc/controllers/api/v10/Student.php` — `getMark()` (mobile API — admin/teacher viewing a student via API)
- `mvc/controllers/api/v10/Profile.php` — `getMark()` (mobile API — student/parent's own "My Profile" Marks tab)

**Related rule documented**: See `srinivas_project_structure.md` Section 4, "Web ↔ Mobile API Parity" — this bug had to be fixed in three near-identical copy-pasted locations because the mobile API duplicates web logic instead of sharing it.

---

### 2026-08-01: Biometric Reports (Teacher/Student/User) Showing Blank Names / Wrong Data / SQL Error

**Reported as**: On the Attendance Overview Report → Biometric Report tabs, Teacher Name/Designation/Phone came back blank even though RFID/Date/Punch IN/OUT showed real data. Local (`nice.ourschoolerp.localhost`) and live (`nice.ourschoolerp.com`) showed different data for the same school. User Biometric Report eventually threw `Unknown column 'ut.name' in 'field list'`.

**Root Cause** (four separate, compounding issues, all in `Attendanceoverviewreport.php` / `Biometric.php`):
1. **Multi-tenancy bug**: `Attendanceoverviewreport::__construct()` called `$this->load->database();` with no arguments *after* `parent::__construct()` had already switched `$this->db` to the current school's own tenant database (via `MY_Controller::callSubDomainProcess()`). The no-arg call silently reloaded the raw default/master connection group instead, so **every report in this controller was reading the wrong database for every school** — not the actual school's data.
2. **Wrong column joined for User report**: `user` table has both `rfid` (populated, real data) and a legacy `rf_id` (always NULL). `get_user_biomatric_report()` and the `'u'`-prefix branch of `Biometric::index()` both joined on `rf_id` — the dead column — so the User report could never match, structurally, independent of any RFID-value mismatch.
3. **Teacher report missing entity filter**: `get_biomatric_report()` had no `WHERE t.teacherID IS NOT NULL`, unlike the student/user report methods — it returned every `biometric` row in the date range regardless of match, leaking student/user punches into the teacher report as blank-name rows.
4. **RFID value mismatch** (data issue, not code): teacher/student/user card numbers didn't follow any enforced, non-overlapping convention. The old capture-time dispatch in `Biometric::index()` used a letter-prefix scheme (`s`=student, `u`=user, else=teacher) that real device data showed was never actually used (100% of live punches were pure numeric, no letters) — so student/user attendance-marking was effectively dead code, and every punch defaulted to the teacher branch regardless of who it belonged to.
5. **Column-name typo**: `get_user_biomatric_report()` selected `ut.name AS role`, but the `usertype` table's label column is actually named `usertype`, not `name` → SQL error `Unknown column 'ut.name'`.

**Fix**:
- Removed the stray `$this->load->database();` call from `Attendanceoverviewreport::__construct()`.
- Changed `user` joins to `u.rfid = b.rfid` (both in the report query and in `Biometric::index()`).
- Added `AND t.teacherID IS NOT NULL` to the teacher report query.
- Changed `ut.name AS role` → `ut.usertype AS role`.
- Replaced the letter-prefix dispatch in `Biometric::index()` with a digit-length convention: 5 digits = student, 6 = user, 4 or 8 = teacher (8 kept alongside 4 to grandfather existing teachers' 8-digit cards). Added matching `regex_match` validation to the RFID field in `Teacher.php`/`Student.php`/`User.php` add/edit rules (appended at the end of each `rules()` array to avoid shifting existing `unset($rules[N])` index references).
- Added `teacher.rfid` and `student.rf_id` as tracked, idempotent column-adds in `mvc/migrations/schema_updates.sql` and `schema_updates.json` — they were being used in code but were never added to the shared migration files, so some tenant databases may have been missing them.

Full design writeup: see `srinivas_biomatric_instructions.md`.

**Files Changed**:
- `mvc/controllers/Attendanceoverviewreport.php` — removed stray `load->database()`, fixed teacher/user report queries
- `mvc/controllers/Biometric.php` — digit-length dispatch instead of letter-prefix
- `mvc/controllers/Teacher.php`, `mvc/controllers/Student.php`, `mvc/controllers/User.php` — RFID length validation in `rules()`
- `mvc/migrations/schema_updates.sql`, `mvc/migrations/schema_updates.json` — added `teacher.rfid` / `student.rf_id` column guards
- `srinivas_biomatric_instructions.md` — new reference doc (created)

---

### 2026-08-03: Teacher/Student/User Edit Form Rejects RFID With "Unable to access an error message... (permit_empty)"

**Reported as**: Editing a teacher and entering a valid RFID (e.g. `1234`, a valid 4-digit value) showed the error "Unable to access an error message corresponding to your field name RFID. (permit_empty)" and blocked saving.

**Root Cause**: The RFID validation rules added in the previous entry included `permit_empty` (intended to let the field stay blank while still validating it when filled in). This project runs a **customized CodeIgniter core** at `main/libraries/Form_validation.php` (not the standard `system/` library). Its `_execute()` method already auto-skips every rule when a field's value is `NULL`/`''` (except `required`/`isset`/`matches`) — see `main/libraries/Form_validation.php:699-708` — so `permit_empty` was never necessary here. Worse, this custom library **never implemented `permit_empty` as an actual rule** (no such method, no native PHP function of that name). So whenever RFID had a non-empty value, CI tried to execute `permit_empty` as a real rule, couldn't find it, treated it as a failed rule, and — with no error message registered for a rule literally named `permit_empty` — fell back to the generic "unable to access an error message" text, blocking the save entirely regardless of whether the actual RFID value was valid.

**Fix**: Removed `|permit_empty` from the `rfid`/`rf_id` rule strings in all three controllers. Blank fields still skip validation automatically (framework default in this codebase); non-blank fields are now checked by `regex_match` alone, which *is* implemented (`main/libraries/Form_validation.php:1071`).

**Files Changed**:
- `mvc/controllers/Teacher.php`, `mvc/controllers/Student.php`, `mvc/controllers/User.php` — removed `permit_empty` from the `rfid`/`rf_id` rule
- `srinivas_biomatric_instructions.md` — documented the `permit_empty` gotcha so it isn't reintroduced

---

### 2026-08-03: Teacher RFID Save Still Rejected After `permit_empty` Fix — Even Valid 4-Digit Values

**Reported as**: After the `permit_empty` fix above, saving a teacher with RFID `1234` (a valid 4-digit value per the new rule) still failed, this time with the *intended* custom message — "Teacher RFID must be exactly 4 digits (new cards) or 8 digits (existing cards)." — as if the value never matched, even though `1234` clearly does.

**Root Cause**: Teacher's rule was `regex_match[/^([0-9]{4}|[0-9]{8})$/]`. This project's customized `main/libraries/Form_validation.php` splits a rules string into individual rules via `preg_split('/\|(?![^\[]*\])/', $rules)` ([Form_validation.php:214](main/libraries/Form_validation.php#L214)) — a "bracket-aware" pipe-split intended to avoid breaking on a `|` that's inside a rule's own `[...]` parameter. It only handles a single simple bracket span; Teacher's pattern has an alternation between *two* `[0-9]` character classes, and that combination defeats the heuristic. Verified directly:
```php
preg_split('/\|(?![^\[]*\])/', "trim|xss_clean|regex_match[/^([0-9]{4}|[0-9]{8})$/]")
// => ["trim", "xss_clean", "regex_match[/^([0-9]{4}", "[0-9]{8})$/]"]
```
The rule name still resolves cleanly to `regex_match` (which is why the custom error message displayed correctly), but the parameter that actually reaches `regex_match($str, $param)` is the mangled fragment `/^([0-9` — an incomplete pattern that fails to match *any* input, valid or not. Student's and User's patterns (`^[0-9]{5}$`, `^[0-9]{6}$`) have no internal `|` and were never affected by this.

**Fix**: Replaced Teacher's `regex_match[...]` rule with a plain PHP callback (`callback_rfid_format`, defined as a public method on `Teacher.php` doing `preg_match('/^([0-9]{4}|[0-9]{8})$/', $str)` directly) — callbacks are identified by the `callback_` string prefix alone, with no `[...]` parameter, so they never go through the broken bracket-splitting logic at all. Student and User were left on `regex_match[...]` since their single-length patterns split correctly (verified with the same `preg_split` call).

**Files Changed**:
- `mvc/controllers/Teacher.php` — `rfid` rule changed to `callback_rfid_format`; added `rfid_format()` method
- `srinivas_biomatric_instructions.md` — documented why Teacher needed a callback while Student/User didn't

---

### 2026-08-03: Hostel Member Edit Shows "Page Not Found" / Un-Member Says "Not Currently a Hostel Member"

**Reported as**: On a specific student in the Hostel Member listing (shown as a member, with Edit/View/Un-Member buttons visible), clicking **Edit** rendered the app's generic "Oops! Page not found." view (not a real CI 404 — sidebar/header still present), and clicking **Un-Member** opened the confirmation modal but immediately alerted "This student is not currently a hostel member."

**Root Cause**: `student.hostel` (a flag on the `student` table) and the `hmember` table (the actual membership record) can fall out of sync, because every place that sets `hostel = 1` does it as two separate, unguarded steps with no verification in between:
1. `Hmember_m::insert_hmember()` did `parent::insert($array); return TRUE;` — **always returned `TRUE`**, even if the underlying `$this->db->insert()` call failed. Since this project's DB connections run with `db_debug = FALSE` (see the Admission Enquiry gotcha in `srinivas_project_structure.md`), an insert failure is completely silent — no exception, no error, nothing in the response.
2. Every caller (`Hmember::add()`, `Hmember::bulk_add()`, and the equivalent flows in `Student.php`) called `insert_hmember()` and then **unconditionally** called `student_m->update_student(['hostel' => 1], $id)` right after, with no check on step 1's result.

Whenever that pairing broke for any reason (a transient failure on the remote tenant DB connection, a race, etc.), the student ends up flagged `hostel = 1` with no backing `hmember` row. `Hmember::edit()` then falls into its `if ($this->data["hmember"]) { ... } else { subview = "error" }` branch, and `unmember_precheck()`/`unmember()` both explicitly reject when `get_single_hmember()` finds nothing — neither path had ever considered the flag-without-record case.

**Verified at scale**: queried the live `ggs` tenant DB directly — **142 of 327** students flagged `hostel = 1` had zero matching `hmember` rows (43%). This is a systemic data-consistency issue, not a one-off.

**Also found while reviewing (same root cause) — `Hmember::add()`/`bulk_add()` invoice generation used `$student->classesID`/`$student->sectionID` (from the joined `student` table, which per the documented `student` vs `studentrelation` gotcha can be stale after promotion/section transfer) instead of `$student->srclassesID`/`$student->srsectionID` (the current-year source of truth already present on the same `studentrelation`-joined object).**

**Fix**:
1. `Hmember_m::insert_hmember()` now returns the actual insert ID (or `FALSE`) instead of hardcoded `TRUE`.
2. `Hmember::add()` and `Hmember::bulk_add()` now check that return value before flipping `student.hostel = 1` or generating the auto-invoice — on failure they report an error / count it as a failure instead of silently proceeding.
3. `Hmember::add()`/`bulk_add()` invoice generation switched to `srclassesID`/`srsectionID`.
4. **Self-heal for already-orphaned students** (so the 142 existing cases don't need a manual DB fix): `Hmember::edit()`, `unmember_precheck()`, and `unmember()` now detect "flag is 1 but no `hmember` row" and clear the stale `hostel` flag (reporting a clear message) instead of dead-ending on a confusing error/block. This fixes each affected student automatically the next time an admin clicks Edit or Un-Member on them — no bulk SQL migration needed.

**Update**: `Student.php` had the identical unguarded `insert_hmember()` → `update_student(hostel=1)` pairing in 4 places — the two `add()` flows (~line 1065, ~1827), the `edit()` flow (~line 2641), and the `assign_hostel()` AJAX endpoint (~line 4498, the "quick assign hostel" dropdown on the Student list). All 4 now check the insert result the same way as `Hmember.php`, on top of that file's other unrelated in-progress edits.

**Known follow-up, not fixed**: `Student::assign_hostel()`'s auto-invoice generation still builds `classesID`/`sectionID` from `$student->classesID`/`$student->sectionID` (via `Student_m::get_single_student()`, which for the admin/usertype-1 path reads straight off the `student` table with no `studentrelation` join) — same staleness risk as the `Hmember.php` case, but fixing it here needs a separate `studentrelation` lookup for the current year since `Student_m`'s object doesn't carry `srclassesID`/`srsectionID`. Deferred since this endpoint is a live, frequently-used path and the fix deserves its own testing pass.

**Scope**: Purely a code-level bug in shared controllers/models — not specific to the `ggs` subdomain. Every tenant database is equally exposed since they all run the same code; other schools likely have their own orphaned students at whatever rate their admins have hit the failure path.

**Files Changed**:
- `mvc/models/Hmember_m.php` — `insert_hmember()` returns real insert ID/`FALSE`
- `mvc/controllers/Hmember.php` — `add()`, `bulk_add()` verify insert success + use `srclassesID`/`srsectionID`; `edit()`, `unmember_precheck()`, `unmember()` self-heal orphaned flags
- `mvc/views/hmember/index.php` — Un-Member modal shows a clear message for the orphan/self-heal case

---

### 2026-08-11: Invoice List "Edit Discount" Amount Missing From Global Payment's Payment History Popup

**Reported as**: A student's invoice has a discount entered via the Invoice list page's inline "edit discount" icon (Discount column). That amount is correctly reflected in the invoice list's Balance and in the Global Payment page's "Remaining Due" total, but the Global Payment page's "Payment History — Current Year" popup does not show it anywhere against that invoice's row.

**Root Cause**: The Invoice list's edit-discount icon does **not** write to `invoice.discount`. `Invoice::change_discount()` instead inserts a dummy row into `payment` (no `paymentamount`, `globalpaymentID` defaults to `0`) plus a `weaverandfine` row (`weaver = disc_amount`) tied to that dummy payment's `paymentID` — a second, separate discount mechanism from the `invoice.discount` column set at invoice-creation time (see `srinivas_global_payment_instructions.md` Section 4 for the column-based mechanism).

In `Global_payment_new::index()`, `generateAllPaymentAmountWithGlobalID()` groups payment history by `globalpaymentID` for rendering. A `globalpaymentID = 0` orphan payment never matches any real `globalpayment` row, so it's invisible to the main "Payment History" loop (`mvc/views/global_payment_new/index.php`, ~line 553). The view also has a separate "Discount-only rows" fallback block meant for invoices with a discount but no real payment — but it was gated by `if ($_dinvPaid > 0) continue;`, so as soon as the invoice ALSO had one real payment (e.g. a partial payment through the normal Global Payment form), the whole fallback block was skipped and the orphan weaver-discount silently disappeared from the popup, even though it was still correctly subtracted in the page's own Due/Remaining Balance math (which reads `$weavers[$invoiceID]`, an all-time sum across both discount mechanisms, unaffected by this bug).

**Fix**:
1. Added `Global_payment_new::generateOrphanDiscountAmount()` — sums `weaverandfine.weaver` per `invoiceID` **only** for rows where `globalpaymentID == 0` (the invoice-list-edit-discount mechanism), and wired it into `index()` as `$this->data['orphan_discounts']`.
2. In the view's "Discount-only rows" block, replaced the `$_dinvPaid > 0` skip-the-whole-block guard with a targeted one: the orphan-discount portion is now **always** rendered (regardless of other real payments on the invoice); only the `invoice.discount`-column portion is zeroed out when a real payment exists, since that portion is already correctly attributed to the real payment's row via the existing `invoice_discount` map (unaffected by this bug, kept as-is) — this avoids double-counting while no longer dropping the orphan amount.

**Files Changed**:
- `mvc/controllers/Global_payment_new.php` — added `generateOrphanDiscountAmount()`; `index()` now builds `$this->data['orphan_discounts']`
- `mvc/views/global_payment_new/index.php` — "Discount-only rows" block now always shows the orphan (`globalpaymentID=0`) weaverandfine discount, independent of whether the invoice has a real payment
- `srinivas_global_payment_instructions.md` — documented the `globalpaymentID=0` orphan-discount mechanism

---

### 2026-08-21: Invoice List "Edit Discount" Icon Does Nothing From Page 2 Onward (Load More / Load All)

**Reported as**: On the Invoice list page, the pencil/edit icon next to the Discount column value opens the "Change Amount" popup correctly for rows on the first page (initial 50 records). After clicking "Load More" or "Load All Invoices" to bring in the next batch, clicking the same edit icon on any of those newly-loaded rows does nothing — no popup appears.

**Root Cause**: Each invoice row's edit icon calls `checkDiscountValidation(invoice_id)` (`mvc/views/invoice/index.php`), which does `$("#change_discount"+invoice_id).modal('show')`. That per-invoice modal `<div>` is only ever rendered by the initial server-side page load (`Invoice::index()` → `mvc/views/invoice/index.php` lines ~436-465, one modal emitted inline per `<tr>` in the PHP loop). The AJAX row-builders backing "Load More"/"Load All" — `Invoice::load_more_invoices()` and `Invoice::load_all_invoices()` (`mvc/controllers/Invoice.php`) — build each row's `<tr>` HTML in PHP string concatenation but never emitted the matching `change_discount{id}` modal markup at all, so for any invoice loaded via those two AJAX endpoints, `#change_discount{id}` simply doesn't exist in the DOM — `.modal('show')` on an empty jQuery selection silently no-ops.

**Fix**: Extracted the modal markup into a shared `Invoice::buildChangeDiscountModal($maininvoiceID, $srstudentID)` helper. Both `load_more_invoices()` and `load_all_invoices()` now build this per-invoice modal HTML into a separate `$modalsHtml` string (kept out of the `<tr>` string on purpose — a `<div>` can't validly live inside a table row, and DataTables' `rows.add()` only accepts `<tr>` nodes) and return it as a new `modals` key in the JSON response, alongside the existing `html`/`count`. In `mvc/views/invoice/index.php`, both AJAX success handlers (`#load-more-btn`, `#load-all-btn`) now do `$('body').append(response.modals)` right after adding the new rows via `rows.add(...).draw(false)`.

**Files Changed**:
- `mvc/controllers/Invoice.php` — added `buildChangeDiscountModal()`; `load_more_invoices()` and `load_all_invoices()` now return `modals` in their JSON response
- `mvc/views/invoice/index.php` — both Load More/Load All AJAX success handlers append `response.modals` to `<body>`

---

### 2026-08-21: Exam Filter Dropdown Showed Exams With No Exam Schedule, Across All Report Pages

**Reported as**: Follow-up to the same-day Exam Schedule listing page fix (dropdown showed exams like "slip test 3" duplicated / exams with zero schedule rows). User asked for the identical fix to be applied everywhere else in the app that has a Class → Exam cascading dropdown, since several report pages share the same pattern.

**Root Cause**: `Marksetting_m::get_exam($marktypeID, $classesID)` returns every exam configured in `marksetting`/`exam` for the class/marktype, with no check that the exam actually has any rows in `examschedule`. Six report controllers' `getExam()` AJAX methods (bound to each report's Class dropdown `onchange`, populating the Exam dropdown) all called this unfiltered method directly:
- `Terminalreport::getExam()`, `Marksheetreport::getExam()` (shared by Progress Card Report, Marksheet Report, and Student Session Report views — all three point at `marksheetreport/getExam`), `Admitcardreport::getExam()`, `Tabulationsheetreport::getExam()`, `Meritstagereport::getExam()`, `Examschedulereport::getExam()`.

A schedule-aware alternative already existed and was already in use elsewhere: `Marksetting_m::get_exam_with_schedule_condition()` (used by `Mark::examcall()` for the Mark entry page) — same signature as `get_exam()`, but adds an `EXISTS (SELECT 1 FROM examschedule WHERE examschedule.examID = exam.examID AND examschedule.classesID = $classesID)` guard per exam-type branch (marktypeID 4/5-6/default).

**Fix**: Swapped `$this->marksetting_m->get_exam(...)` → `$this->marksetting_m->get_exam_with_schedule_condition(...)` in all six `getExam()` methods above — no other logic changed, since the two methods share the same call signature and return shape. (The Exam Schedule listing page itself, `Examschedule::getExam()`, was fixed earlier the same day with an equivalent but separately-written scheduled-exam-IDs intersection — left as-is since it already does the same job.)

**Files Changed**:
- `mvc/controllers/Terminalreport.php`
- `mvc/controllers/Marksheetreport.php`
- `mvc/controllers/Admitcardreport.php`
- `mvc/controllers/Tabulationsheetreport.php`
- `mvc/controllers/Meritstagereport.php`
- `mvc/controllers/Examschedulereport.php`

---

### 2026-08-21: New-Design Progress Card Report Ignored the "Show Attendance" Setting

**Reported as**: The old Progress Card Report (`progresscardreport/getProgresscardreport`) correctly hides the Attendance section when `is_display_attendance_on_progresscard` is off in Settings, and shows it when on. The New Design report (`progresscardreport/getProgresscardreportNew`) always shows the Attendance donut and the full Attendance Summary table, regardless of that setting.

**Root Cause**: The old flow (`Progresscardreport::getProgresscardreport()`) reads `$this->setting_m->get_setting_where('is_display_attendance_on_progresscard')` and only builds/renders attendance data inside `if($is_display_attendance > 0)`, also passing `$this->data['is_display_attendance']` to the view so `ProgresscardReport.php` can gate its `<h5>Attendance</h5>` block. The New flow — `getProgresscardreportNew()`, `pdf_new()` (Print/PDF button), and `send_pdf_to_whatsapp_new()` (bulk WhatsApp send) — builds `attendanceByStudent` unconditionally via `_buildAttendanceByMonth()` and never reads the setting at all, so neither `ProgresscardReportNew.php` (screen view) nor `ProgresscardReportPDFNew.php` (PDF/WhatsApp view) had any gate to check — they always render the Overall Attendance donut and Attendance Summary table.

**Fix**: In all three controller methods, added the same `$this->setting_m->get_setting_where('is_display_attendance_on_progresscard')` read, stored as `$this->data['is_display_attendance']`, and only build `attendanceByStudent` (or the per-recipient one in the WhatsApp loop) when it's `> 0`. Wrapped the corresponding display blocks in both views with `if($is_display_attendance > 0) { ... }`:
- `ProgresscardReportNew.php` — the "Overall Attendance" donut panel (sits beside the 2x2 Result Summary grid) and the whole "Attendance Summary" box (month-by-month table).
- `ProgresscardReportPDFNew.php` — the "Attendance" column in the Result Summary table's header+data row (both cells gated together to keep row column counts matched) and the whole "Attendance Summary" table block.

**Files Changed**:
- `mvc/controllers/Progresscardreport.php` — `getProgresscardreportNew()`, `pdf_new()`, `send_pdf_to_whatsapp_new()`
- `mvc/views/report/progresscard/ProgresscardReportNew.php`
- `mvc/views/report/progresscard/ProgresscardReportPDFNew.php`

---

### 2026-08-22: Progress Card Report (New Design) PDF — `MpdfException: HTML code size is larger than pcre.backtrack_limit` for a whole class/section

**Reported as**: Screenshot of an uncaught `Mpdf\MpdfException` on `sribhavanicbse.ourcollegeerp.com` — `progresscardreport/pdf_new/9/14/0/8`. Backtrace: `Progresscardreport::pdf_new()` → `Admin_Controller::reportPDF()` → `Mhtml2pdf::create()` → mPDF's `WriteHTML()` → `AdjustHTML()`.

**Root Cause**: The URL's third segment (`studentID`) is `0`, meaning "every student in the section," not one student. `Progresscardreport::pdf_new()` built `$this->data['students']` as the full list for the class/section and rendered them **all** into a single `ProgresscardReportPDFNew` view call (that view loops `foreach($students as $student)` with a `page-break-after` between each), producing one giant combined HTML string passed to a single `WriteHTML()` call. mPDF's `AdjustHTML()` runs several `preg_*` passes over the whole HTML before layout; large enough input (a full section, each student contributing a header table + subject table + grade-scale table + nested-table bar charts) makes one of those regexes exceed PHP's default `pcre.backtrack_limit` (1,000,000 steps), and PHP/mPDF throws rather than truncating or degrading gracefully.

**Fix** (two parts, per project discussion — see the "why this error comes" exchange for the full tradeoff writeup):
1. **Safety net for every report using this shared library**: `Mhtml2pdf::create()` now does `ini_set('pcre.backtrack_limit', 10000000)` and `ini_set('pcre.recursion_limit', 10000000)` before constructing `Mpdf` — raises the ceiling for any report's PDF build, not just this one.
2. **Actual fix for this report**: `pdf_new()` now branches on `customCompute($students) <= 1`. A single student keeps the old behavior (stream the PDF inline via `reportPDF(..., 'view')`) — no HTML-size risk there. For 2+ students (the `studentID=0` whole-class/section case), it now loops per student, calling `Admin_Controller::generateAttachment()` (the same 'save'-mode helper already used by `send_pdf_to_whatsapp_new()` for the same view) to render and save **one PDF per student** instead of one mega-HTML combined PDF. Each saved PDF is added to a `ZipArchive` (`uploads/report/progresscard_batches/ProgressCards_<hash>.zip`), and the response is a new results page — `mvc/views/report/progresscard/ProgresscardPdfBatchResults.php` — listing every generated student's PDF with per-row View/Download links plus one "Download All as ZIP" button, addressing the follow-up ask to "display all the generated PDFs and provision to download."

**Files Changed**:
- `mvc/libraries/Mhtml2pdf.php` — `create()`: raised `pcre.backtrack_limit`/`pcre.recursion_limit`
- `mvc/controllers/Progresscardreport.php` — `pdf_new()`: branches single-vs-bulk, bulk path generates per-student PDFs + zip
- `mvc/views/report/progresscard/ProgresscardPdfBatchResults.php` — new results/listing view

**Follow-up same day**: applied the identical `customCompute($students) <= 1` branch-and-batch fix to the **old/original design** `Progresscardreport::pdf()` too (`report/progresscard/ProgresscardReportPDF` view, `progresscardreport.css`) — that endpoint has no `studentID` URL segment at all (`pdf/<classesID>/<sectionID>/<examID>` only), so every call is a whole-class/section combined-HTML render and is exposed to exactly the same PCRE crash risk for a large enough class, just not yet reported. Reused the same `ProgresscardPdfBatchResults.php` view for both endpoints' bulk path (it's generic — only needs `generatedPdfs`, `zipUrl`, `classesID`/`sectionID`/`examID`, `classes`/`sections`/`exams` — nothing New-Design-specific), so no second results view was needed.

**Redesigned same day (superseded the pre-generate-and-save-to-disk approach above)**: user flagged (screenshot) that the bulk results page's per-student PDFs and the "Download All as ZIP" file were all being written into the project's `uploads/report/progresscard_batches/` folder and staying there — not what was wanted; the View button should behave like the existing single-student inline view (generate on demand, stream straight to the browser, nothing saved), and the ZIP should download directly to the client with nothing left on the server either.

Reworked both `pdf_new()` and `pdf()`'s bulk branch to generate **nothing** up front — the results page now just lists student name/roll/View/Download links built from student IDs already in hand (instant, no mPDF calls at all). Each row's:
- **View** link points back at the *same* `pdf_new`/`pdf` method with that one `studentID` filled in — which is exactly the existing single-student branch (`customCompute($students) <= 1`), so it's generated fresh per click and streamed inline (`mode='view'`) exactly like the pre-existing single-student PDF flow. No new code path for this — it's the same one, just reached via a different URL.
- **Download** link is the same URL plus `?dl=1`; both `pdf_new()`/`pdf()` now check `$this->input->get('dl')` to pick `mode='download'` instead of `mode='view'` (mPDF's existing `Output(...,'D')` — still no disk write, mode `'download'` already existed in `Mhtml2pdf::create()`).
- **"Download All as ZIP"** hits two new methods, `pdf_new_zip()` / `pdf_zip()`: loop every student, render each one's PDF as a raw byte string via a new `Admin_Controller::generatePdfString()` helper (mirrors `generateAttachment()` but calls a new `Mhtml2pdf::create()` mode, `'string'`, which does `$mpdf->Output('', 'S')` — no file write at all), `ZipArchive::addFromString()` each into a zip opened on a PHP temp file (`tempnam(sys_get_temp_dir(), 'pcz_')` — outside the project folder entirely), then `readfile()` + `Content-Disposition: attachment` headers to stream it, then `unlink()` the temp file immediately. Nothing is ever created under `uploads/` for this flow anymore.

Also factored the ~100-line data-prep block (students/marks/markArray/attendance/classPerf, etc.) out of both `pdf_new()` and `pdf()` into new private helpers `_prepareProgressCardNewData()` / `_prepareProgressCardOldData()`, since both the single/bulk-listing method and its new `*_zip()` sibling need the identical prep. While extracting `_prepareProgressCardOldData()`, fixed a latent bug: the old `pdf()` had `foreach($settingExam as $examID)`, silently clobbering the URL's `$examID` with the loop variable — renamed to `$seExamID` (matching the New Design's already-correct convention) so `$examID` stays the real exam id throughout, needed for the new per-student View/Download URLs. Also added the previously-absent `studentID` URL segment to `pdf()` (segment 6, optional — old 3-segment links keep working, `(int)$studentID<=0` still means "every student").

**Separately fixed**: the "Scan to Verify" QR box had already been disabled (commented out, `if(false)`) on-screen the same day, but the QR **generation** call (`generate_qrcode()`, writing a PNG per student per exam to `uploads/progresscardQRcode/`) was left outside that guard and kept running on every screen view regardless — visible in the same screenshot as a growing pile of `pc-<studentID>-<examID>.png` files. Moved the generation code inside the same `if(false)` block in `ProgresscardReportNew.php` so nothing is written while the feature is off.

**Files Changed** (this redesign):
- `mvc/libraries/Mhtml2pdf.php` — `create()`: added `'string'` output mode (`Output('', 'S')`, no disk write)
- `mvc/libraries/Admin_Controller.php` — added `generatePdfString()`
- `mvc/controllers/Progresscardreport.php` — added `_prepareProgressCardNewData()`/`_prepareProgressCardOldData()`; `pdf_new()`/`pdf()` rewritten to list-then-generate-on-demand (`?dl=1` for download mode); added `pdf_new_zip()`/`pdf_zip()` streaming endpoints
- `mvc/views/report/progresscard/ProgresscardPdfBatchResults.php` — updated to the new per-row `viewUrl`/`downloadUrl` fields, wording no longer implies PDFs are pre-generated
- `mvc/views/report/progresscard/ProgresscardReportNew.php` — QR PNG generation moved inside the existing disabled QR display block
- Deleted the leftover local artifacts from the superseded approach: `uploads/report/progresscard_batches/*.zip`, `uploads/progresscardQRcode/*.png` (dev environment only — production's `uploads/` clutter from the same superseded code needs cleaning up separately on that server)

**Follow-up (2026-08-22)**: added a **Consolidated PDF** option (View + Download) alongside "Download All as ZIP" on the results page, for a single merged multi-page PDF instead of one file per student. Confirmed `setasign/fpdi` is already installed as an `mpdf/mpdf` dependency, and mpdf's `Mpdf\Mpdf` class itself exposes the PDF-import API (`setSourceFile()`, `importPage()`, `getTemplateSize()`, `useTemplate()`, `AddPageByArray()`, bundled via `Mpdf\FpdiTrait` — confirmed working with a standalone `php -r` test before wiring it in) — this merges **already-rendered PDF files at the binary/page level**, not by concatenating HTML, so it does not reintroduce the `pcre.backtrack_limit` crash that a single combined `WriteHTML()` call caused.

New private helper `Progresscardreport::_mergeStudentPdfsAndOutput($students, $stylesheet, $viewpath, $outputName, $mode)`: for each student, renders their PDF via the same `generatePdfString()` used by the ZIP path, writes it to a temp file just long enough to `setSourceFile()`/`importPage()`/`useTemplate()` its pages into one shared `Mpdf` instance, then deletes that temp file immediately — only the final merged PDF is ever streamed (`Output(..., 'I')` for View / `'D'` for Download via the same `?dl=1` convention as the per-student links), never saved under `uploads/`. Two thin public wrappers, `pdf_new_consolidated()` / `pdf_consolidated()`, supply the New/old-design stylesheet+view pair.

**Files Changed**:
- `mvc/controllers/Progresscardreport.php` — added `_mergeStudentPdfsAndOutput()`, `pdf_new_consolidated()`, `pdf_consolidated()`; `pdf_new()`/`pdf()` bulk branches now also pass `consolidatedUrl`
- `mvc/views/report/progresscard/ProgresscardPdfBatchResults.php` — added View/Download Consolidated PDF buttons

---

### 2026-08-22: Progress Card PDF "Performance Overview" Bars Correct Locally, Full-Width Regardless of Value on Live Server

**Reported as**: Screenshots comparing the New Design PDF's "Performance Overview" section (Subject Wise Marks / Marks Distribution / Grade Distribution mini bar-charts) on the dev environment vs. `sribhavanicbse.ourcollegeerp.com`: locally the bars are proportional to each value (including near-zero-width for `C+`/`C`/`D` grade counts of 0); on the live server every bar renders full-width regardless of its value, even the ones for a count of 0.

**Root Cause**: mPDF can't run the on-screen Highcharts JS, so `ProgresscardReportPDFNew.php`'s bar-chart mini-visuals were built with a classic mPDF workaround — a `<table>` "track" cell (`background:#e0e0e0`) containing a **nested `<table width="X%">`** (the fill color) sized via the legacy HTML `width` **attribute** as a percentage. That specific technique — a percentage width resolved on a table nested inside a `height:8px; font-size:1px` cell — is exactly the kind of edge case whose behavior has changed across mPDF's 8.x releases. `composer.json` pins `"mpdf/mpdf": "^8.0"`, a caret range that allows any 8.x version, so the dev environment's `vendor/mpdf` and the live server's `vendor/mpdf` are very likely on different 8.x point releases — one resolves the nested percentage width correctly, the other appears to ignore it and renders the inner table at full width regardless of the percentage given (matching the 0%-still-full-width symptom on `C+`/`C`/`D`).

**Fix**: Replaced all three bar visuals' nested-percentage-table technique with plain block `<div>`s sized by an **explicit pixel width computed in PHP** (`style="width:{$px}px"`), never a percentage left for mPDF to resolve against an unusual nested-table context. Fixed pixel track widths chosen per column (`$barTrackPx = 120` for Subject Wise Marks, `$mdTrackPx = 150` for Marks Distribution, `$gradeTrackPx = 110` for Grade Distribution), with each bar's fill width computed as `round($trackPx * $pct / 100)` in PHP and rendered as a literal pixel value — mPDF's plain block-`<div>`-with-explicit-px-dimensions rendering is one of its most consistently-supported features across versions (also already used elsewhere in this same file for photos/logos), unlike the legacy nested-table percentage-width trick.

**Files Changed**:
- `mvc/views/report/progresscard/ProgresscardReportPDFNew.php` — Subject Wise Marks, Marks Distribution, and Grade Distribution (Class) bar visuals switched from nested `<table width="X%">` to fixed-pixel `<div>` bars

**Correction (same day)**: the plain-`<div>` version above was wrong — tested and the bars (and even their gray tracks) disappeared entirely, on the same environment that previously rendered the table-based bars fine. mPDF appears to collapse empty/content-less `<div>` blocks (no text node) to zero visible size regardless of an explicit CSS `height`/`background`, unlike table cells which reliably keep their box even with just `&nbsp;` content. **Reverted to the original nested-`<table>` structure** (proven reliable) but kept the actual fix — the inner table's `width` is now given as a **plain pixel number with no `%` sign** (`width="<?=$barPx?>"` instead of `width="<?=$barPct?>%"`), which sidesteps the percentage-resolution ambiguity that differed across mPDF versions while keeping the table-cell rendering path that's known to work. Applied to all three bars (Subject Wise Marks, Marks Distribution, Grade Distribution).
