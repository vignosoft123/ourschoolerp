# OurSchoolERP – Fixed Issues Reference

A running log of root causes and fixes. Share this file at the start of any debugging session.

---

## Issue #1 – Student missing from Invoice List but visible in Invoice Report

**Date:** 2026-04-22
**School:** Pragna High School (applies to all schools)
**Symptom:** A student (e.g., S MOHAMMAD SIDDIQ, studentID=1282) appears correctly in the Invoice Report (`/invoicereport`) but is completely missing from the Invoice List page (`/invoice/index/{classID}`).

### Root Cause
`Maininvoice_m.php` → `get_maininvoice_with_studentrelation()` and `count_maininvoice_with_studentrelation()` both had these extra WHERE conditions:
```php
$this->db->where('maininvoice.maininvoiceuname IS NOT NULL', NULL, FALSE);
$this->db->where('maininvoice.maininvoiceuname !=', '');
```
`maininvoiceuname` stores the **name of the admin who created the invoice**. Invoices created via the **Dashboard** always set this field to `null`, so they were silently hidden from the list. The Invoice Report never had this filter, so it always showed all invoices correctly.

### Fix
Removed the two `maininvoiceuname` WHERE conditions from both methods in:
- `mvc/models/Maininvoice_m.php` — `get_maininvoice_with_studentrelation()` (approx line 95)
- `mvc/models/Maininvoice_m.php` — `count_maininvoice_with_studentrelation()` (approx line 337)

### Key Principle
> When a record appears in a **Report** but not a **List**, compare the WHERE conditions of both queries. The report query is usually the correct one.

---

## Issue #2 – Marks not saved for current academic year / Mark Settings overwrite across years

**Date:** 2026-05-08
**School:** All schools (multi-year tenants)
**Symptom 1:** Entering marks for the current year (e.g., 2026-2027) appears to succeed (no error), but marks vanish on page reload. Previous year marks save correctly.
**Symptom 2:** Saving "Exam Wise" (or any Mark Setting type) for one academic year silently overwrites settings saved for other years.

### Root Cause – Symptom 1 (Marks not saving)

`Mark.php` → `mark_send()` (line ~2002) used:
```php
$schoolyearID = $this->data['siteinfos']->school_year;  // global site default — WRONG
```
while `loadStudentsAjax()` (line 2793) correctly uses:
```php
$schoolyearID = $this->session->userdata('defaultschoolyearID');  // session year — CORRECT
```
For previous years, mark records already exist so `markID != 0` and `schoolyearID` is never needed in the save path. For the current year there are no records yet, so `markID = 0` and `mark_send()` creates a new mark record using the wrong year ID. The record saves but is never found on reload because `loadStudentsAjax()` fetches by the correct session year.

### Root Cause – Symptom 2 (Mark Settings overwrite)

The `marksetting` table had **no `schoolyear_id` column**. The save logic for every mark type (0–6) did:
```php
$this->marksetting_m->delete_marksetting_by_array(['marktypeID' => X]);           // deletes ALL years
$this->marksettingrelation_m->delete_marksettingrelation_by_array(['marktypeID' => X]); // same
```
This wiped every year's settings whenever any year saved, then inserted only the current selection.

### Fix – Symptom 1

**`mvc/controllers/Mark.php`** — `mark_send()`:
```php
// Before:
$schoolyearID = $this->data['siteinfos']->school_year;
// After:
$schoolyearID = $this->session->userdata('defaultschoolyearID');
```

### Fix – Symptom 2

1. **`mvc/migrations/schema_updates.json`** — Added:
   - `ALTER TABLE marksetting ADD schoolyear_id INT(11) NOT NULL DEFAULT '0'` (guarded by `check_column`)
   - `UPDATE marksetting ms JOIN exam e ON ms.examID = e.examID SET ms.schoolyear_id = e.academic_year WHERE ms.schoolyear_id = 0 AND ms.examID > 0` (`raw` type — idempotent backfill, safe to run every page load)

2. **`mvc/models/Marksetting_m.php`** — New helper method `get_marksetting_ids_by_array($array)` returns `marksettingID`s matching a filter.

3. **`mvc/controllers/Marksetting.php`** — `index()`:
   - Load: `get_marksetting_with_marksettingrelation(['schoolyear_id' => $schoolyearID])` so UI shows only current year's settings.
   - All 7 type branches (0–6): replaced global delete pattern with year-scoped pattern:
     ```php
     // 1. Get old IDs for current year
     $_oldIDsX = array_column($this->marksetting_m->get_marksetting_ids_by_array(['marktypeID' => X, 'schoolyear_id' => $schoolyearID]), 'marksettingID');
     // 2. Delete their relations
     if (!empty($_oldIDsX)) { $this->db->where_in('marksettingID', $_oldIDsX)->delete('marksettingrelation'); }
     // 3. Delete their marksettings
     $this->marksetting_m->delete_marksetting_by_array(['marktypeID' => X, 'schoolyear_id' => $schoolyearID]);
     ```
   - Added `$marksettingArr['schoolyear_id'] = $schoolyearID` to every insert loop.

### Data Impact on Existing Schools
- Marks: no impact — the fix only affects new mark records (`markID = 0` path).
- Mark Settings types 0–3, 5, 6 (exam-linked): backfill migration auto-runs on first dashboard load and sets `schoolyear_id` from `exam.academic_year`. Existing settings preserved.
- Mark Settings type 4 (`examID = 0`, class+subject): cannot be backfilled (no exam link). Existing records keep `schoolyear_id = 0` and become invisible in the UI until re-saved once. Type 4 is rarely the active mode.

### Key Principle
> Any query that creates or deletes records scoped to an academic year must use `$this->session->userdata('defaultschoolyearID')`, not `$this->data['siteinfos']->school_year`. The `siteinfos` value is the global DB default and does not change when the user switches year via the dropdown.

---

## General Debugging Patterns

### 1. Record visible in Report but not in List
- Compare the model method used by the List vs the Report.
- Look for extra WHERE conditions in the List query that the Report doesn't have.
- Common culprits: `IS NOT NULL`, `!= ''`, `active = 1`, `status = 1` filters applied incorrectly.

### 2. Record visible in List but not in Report
- The Report likely has a stricter JOIN (e.g., INNER JOIN instead of LEFT JOIN).
- Check if the report filters by `student.active = 1` — inactive students are excluded.

### 3. Pagination count mismatch (total says X but fewer records shown)
- The `count_*` method and the `get_*` method must have **identical WHERE conditions**.
- If you fix a filter in one, always fix it in the other.

### 4. LEFT JOIN behaves like INNER JOIN
- A `LEFT JOIN` becomes an effective `INNER JOIN` when you add a `WHERE` on the joined table's column.
- Example: `LEFT JOIN studentrelation ... WHERE studentrelation.srschoolyearID = X` — rows with no match are NULL and fail the WHERE, hiding the parent row.
- Fix: move the condition into the `ON` clause of the JOIN, not the WHERE.

### 5. Invoice created from Dashboard vs Invoice Controller
- `Invoice.php` sets `maininvoiceuname = session->userdata('name')` (admin name).
- `Dashboard.php` sets `maininvoiceuname = null` (bulk/auto creation).
- Never use `maininvoiceuname` as a filter for visibility — it is only a record of who created the invoice.

---

## Key Files for Invoice Module

| File | Purpose |
|------|---------|
| `mvc/controllers/Invoice.php` | Invoice CRUD, list page (`/invoice/index/{classID}`) |
| `mvc/models/Maininvoice_m.php` | Main invoice queries (list, count, pagination) |
| `mvc/models/Invoice_m.php` | Individual invoice line items |
| `mvc/controllers/Invoicereport.php` | Invoice Report AJAX (`/invoicereport/getInvoiceReport`) |
| `mvc/controllers/Dashboard.php` | Bulk/auto invoice creation (sets `maininvoiceuname = null`) |

---

## Database Tables – Invoice Module

| Table | Key Columns |
|-------|------------|
| `maininvoice` | `maininvoiceID`, `maininvoicestudentID`, `maininvoiceschoolyearID`, `maininvoiceclassesID`, `maininvoicedeleted_at` (1=active), `maininvoiceuname` (creator name) |
| `invoice` | `invoiceID`, `maininvoiceID`, `studentID`, `schoolyearID`, `feetypeID`, `feetype`, `amount`, `discount` |
| `payment` | `invoiceID`, `paymentamount` |
| `studentrelation` | `srstudentID`, `srschoolyearID`, `srclassesID`, `srsectionID`, `srname` |
| `student` | `studentID`, `schoolyearID`, `active` (1=active, 0=inactive) |
