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
