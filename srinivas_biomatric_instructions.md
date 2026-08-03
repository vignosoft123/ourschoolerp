# Biometric Attendance System — Reference

**Project:** OurSchoolERP
**Created:** 2026-08-01
**Author context:** Srinivas — this file captures the full biometric attendance/reporting system (how punches are captured, how the three biometric reports work, the multi-tenancy bug that was fixed, and the RFID-length convention adopted) so a future Claude session doesn't have to re-discover it.

---

## 1. How a punch gets captured

**Endpoint:** `Biometric::index()` in `mvc/controllers/Biometric.php` — hit directly by the physical biometric/RFID terminal via query string:
```
http://{domain}/Biometric?$99999&99&1234567890&20082023123025*4
```
Format: `orgid & mecineid & rfid & datetime`, `$` and `*` stripped before parsing.

Every punch is inserted into the **`biometric`** table (columns: `id`, `sid`, `mid`, `rfid`, `date_time`, `date`, `time`, `created_date`). This table has **no notion of person type** — it's just raw device data.

### Dispatch by RFID digit-length (current design)

After inserting the raw row, `Biometric::index()` decides who the punch belongs to **purely by counting digits in the RFID string** (`strlen($RFID)`), and marks attendance accordingly:

| RFID length | Person type | Matched against | Attendance table updated |
|---|---|---|---|
| 5 digits | Student | `student.rf_id` | `attendance` (column `aN` for day N) |
| 6 digits | User / Non-teaching staff | `user.rfid` | `uattendance` (column `aN`) |
| 4 or 8 digits | Teacher | `teacher.rfid` | `tattendance` (column `aN`) |
| anything else | Unrecognized | — | nothing (punch is still logged in `biometric`, just not attributed) |

**This replaced an older letter-prefix scheme** (`s` prefix = student, `u` prefix = user, else = teacher) that never actually worked in production — a check of live data showed 100% of real punches were pure numeric with no letters at all, so the old scheme's student/user branches were dead code and every real punch silently fell into the teacher branch regardless of who it actually belonged to.

**Why 4 *and* 8 for teacher:** existing teachers already have 8-digit RFID codes (`28231701`...) in production. New teacher cards going forward should be issued as 4-digit. Both are accepted so nothing broke for existing teachers.

**Why exactly 6 for user (no legacy exception):** existing non-teaching staff had 7-digit codes (`2823101`...) under an older scheme. It was a deliberate decision **not** to grandfather 7-digit codes — existing staff RFIDs need to be reissued as 6-digit and re-enrolled on the physical device. Until that happens, old 7-digit staff punches simply won't match anyone (this is expected, not a bug).

**Why students got a clean 5-digit rule:** no student RFIDs existed yet at the time this was designed, so there was no legacy data to reconcile.

---

## 2. The three Biometric Reports

All three live under `Attendanceoverviewreport` controller (`mvc/controllers/Attendanceoverviewreport.php`) and share one page/view: `mvc/views/report/attendanceoverview/AttendanceOverviewReportView.php`. The page has a left-hand nav card (General Reports: Attendance, Teacher Late Comers; Biometric Reports: Teacher, Student, User) and a right-hand content card showing whichever report is selected — only one panel visible at a time, toggled via JS (`hideAllPanels()` / `setActiveNav()`).

| Report | Method | AJAX endpoint | Joins `biometric` to | Result view |
|---|---|---|---|---|
| Teacher Biometric Report | `get_biomatric_report()` | `/attendanceoverviewreport/get_biomatric_report` | `teacher.rfid = biometric.rfid`, filtered `WHERE t.teacherID IS NOT NULL` | `report/biomatric_report` |
| Student Biometric Report | `get_student_biomatric_report()` | `/attendanceoverviewreport/get_student_biomatric_report` | `student.rf_id = biometric.rfid`, filtered `WHERE s.studentID IS NOT NULL` | `report/student_biomatric_report` |
| User Biometric Report | `get_user_biomatric_report()` | `/attendanceoverviewreport/get_user_biomatric_report` | `user.rfid = biometric.rfid`, filtered `WHERE u.userID IS NOT NULL`, plus `usertype ut ON ut.usertypeID = u.usertypeID` for the role label | `report/user_biomatric_report` |

**Column-name gotcha:** `user` table has *both* an `rfid` column (populated, used everywhere) and a legacy `rf_id` column (always `NULL`, unused). Always join on `user.rfid`, never `user.rf_id`. `student` only has `rf_id`. `teacher` only has `rfid`. There is no consistency across tables — check before writing new queries against any of them.

**`usertype` table gotcha:** its label column is literally named `usertype`, not `name`. (`SELECT ut.usertype AS role`, not `ut.name`.)

---

## 3. The multi-tenancy bug (fixed 2026-08-01)

`Attendanceoverviewreport::__construct()` used to call `$this->load->database();` with no arguments, **after** `parent::__construct()` had already set `$this->db` to the correct per-school tenant database (via `MY_Controller::callSubDomainProcess()`, which looks up the current subdomain in `subdomain_settings` and reconnects `$this->db` to that school's own database).

Calling `$this->load->database()` with no params reloads the **default/master** connection group from `mvc/config/{env}/database.php`, silently undoing the tenant override. This meant **every report in this controller** — Attendance Report, Teacher Late Comers, and all three Biometric reports — was reading the wrong database for every school, not just one. Removed; the line is gone entirely, so the tenant-scoped `$this->db` from `parent::__construct()` is what's actually used now.

---

## 4. Report queries that were fixed

- **Teacher report** was missing `WHERE t.teacherID IS NOT NULL` — it used to show *every* `biometric` row in the date range, matched or not (leaking student/user punches into the teacher report as blank-name rows). Fixed.
- **User report** joined on `user.rf_id` (always NULL) instead of `user.rfid` (the real, populated column). Fixed — this alone made real staff punches start matching.
- **User report** selected `ut.name AS role`, but `usertype`'s label column is `usertype`, not `name` → `Unknown column 'ut.name'` SQL error. Fixed to `ut.usertype AS role`.

---

## 5. RFID validation (data entry)

Blank RFID is allowed — a field left empty is skipped by the framework automatically (see the `permit_empty` gotcha below), but if present it must match the length rule:

| Controller | Field | Pattern | Implemented as |
|---|---|---|---|
| `mvc/controllers/Teacher.php` | `rfid` | `^([0-9]{4}\|[0-9]{8})$` | `callback_rfid_format` (PHP callback method) |
| `mvc/controllers/Student.php` | `rf_id` | `^[0-9]{5}$` | `regex_match[/^[0-9]{5}$/]` (built-in rule) |
| `mvc/controllers/User.php` | `rfid` | `^[0-9]{6}$` | `regex_match[/^[0-9]{6}$/]` (built-in rule) |

> **Why Teacher uses a callback instead of `regex_match[...]` directly:** this project's customized `main/libraries/Form_validation.php` splits a `field => 'rules'` string into individual rules with `preg_split('/\|(?![^\[]*\])/', $rules)` ([Form_validation.php:214](main/libraries/Form_validation.php#L214)) — a "bracket-aware" pipe split meant to avoid breaking on the `|` inside a rule's own `[...]` parameter (e.g. `in_list[a|b|c]`). It only works when the parameter has **one simple bracket span**. Teacher's pattern needs an alternation between *two* `[0-9]` character classes (`[0-9]{4}|[0-9]{8}`), and that combination breaks the heuristic: it silently chops the rule into garbage fragments (`regex_match[/^([0-9]{4}` + `[0-9]{8})$/]`), and the parameter that actually reaches `regex_match()` ends up as the broken partial pattern `/^([0-9` — which fails to match *any* input, valid or not. (Verified directly: `preg_split('/\|(?![^\[]*\])/', "trim|xss_clean|regex_match[/^([0-9]{4}|[0-9]{8})$/]")` produces exactly that 4-element garbled array.) Student's and User's single-length patterns have no internal `|`, so they don't trigger this and can safely use `regex_match[...]` as-is. **Lesson: never put a literal `|` inside a `regex_match[...]`/`in_list[...]`-style bracketed rule parameter in this codebase — use a `callback_` method instead, which never goes through the bracket-splitting logic at all.**

Rules were appended at the **end** of each `rules()` array (not inserted in the middle) specifically to avoid shifting the numeric indices that each controller's `edit()` method uses to `unset()` certain fields (e.g. username/password aren't required on edit forms).

> **`permit_empty` gotcha (do not use it in this codebase):** this project runs a customized CodeIgniter core at `main/libraries/Form_validation.php`, not the standard `system/` library. Its `_execute()` method ([Form_validation.php:699-708](main/libraries/Form_validation.php#L699-L708)) already auto-skips **every** rule when a field's value is `NULL`/`''` — except `required`, `isset`, `matches`. So blank fields never needed an explicit `permit_empty` instruction here. Worse, this custom library never implemented `permit_empty` as an actual rule (no method, no native PHP function of that name) — so the moment a field *has* a value, CI tries to execute `permit_empty` as a real rule, can't find it, treats it as a failed rule, and — since there's no error message registered for a rule literally named `permit_empty` — falls back to the generic `Unable to access an error message corresponding to your field name {X}. (permit_empty)` message, blocking the save entirely. This is exactly what happened when the RFID rules above were first added with `|permit_empty|` in the rule string; fixed by removing it. **Lesson: never add `permit_empty` to a rules string in this codebase — it isn't needed and it isn't implemented.**

---

## 6. Schema

`teacher.rfid` and `student.rf_id` were being used in code but were **not** tracked in the shared migration files (only `user.rfid`/`rf_id` were). Since this is multi-tenant (one database per school), that meant some schools' databases might be missing these columns entirely. Added idempotent column-adds to both migration files:

`mvc/migrations/schema_updates.sql`:
```sql
ALTER TABLE `student` ADD COLUMN IF NOT EXISTS `rf_id` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `teacher` ADD COLUMN IF NOT EXISTS `rfid`  VARCHAR(255) DEFAULT NULL;
```

`mvc/migrations/schema_updates.json` (auto-run by the PHP migration runner) — matching entries with `check_column` guards on `teacher.rfid` and `student.rf_id`.

No `person_type`/`person_id` column was added to `biometric` itself — that "belt and suspenders" approach (tagging each punch's person type permanently at capture time, immune to any future RFID range collision) was discussed but not implemented, since the digit-length convention was judged sufficient for now. Revisit if RFID ranges ever get reused/renumbered in a way that could collide.

---

## 7. Known residual state (not code bugs — real-world data/hardware gaps)

- Existing non-teaching staff have 7-digit RFIDs that no longer validate under the new 6-digit-only rule. Their punches won't be attributed to anyone until each person is reissued a 6-digit code **and** re-enrolled on the physical biometric device with that new code (changing the database value alone does nothing if the device still transmits the old number).
- No student has an RFID assigned yet at all (5-digit scheme is brand new).
- The physical biometric device's own capabilities were never fully confirmed — real captured data showed only numeric IDs, suggesting the device may not support alphabetic characters at all (this is why the length-based convention was chosen over a letter-prefix one).
- Every RFID-matching query in this system does an **exact string match** against `biometric.rfid`. If a device ever transmits values with inconsistent leading zeros (stripped or not), a code could silently shift categories (e.g. a 6-digit code losing a leading zero becomes 5 digits → misread as a student). Avoid assigning RFID codes that start with `0`.
