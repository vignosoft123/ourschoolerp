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
