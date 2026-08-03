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
