# Global Payment (New) — Complete Technical Reference
## Use this file to diagnose & fix any issues with Global Payment or related reports

---

## 1. Files Involved

| File | Purpose |
|------|---------|
| `mvc/controllers/Global_payment_new.php` | Main controller — all logic |
| `mvc/views/global_payment_new/index.php` | Main page view |
| `mvc/views/global_payment_new/prev_balance_statement.php` | Previous year balance section (Card 3) |
| `mvc/views/common_views/invoice.php` | Print receipt view (shared) |
| `mvc/models/Student_carry_forward_m.php` | Model for carry-forward table |
| `mvc/config/routes.php` | Routes for `/global_payment/new` |
| `mvc/controllers/Duefeesreport.php` | Due Fees Report — has `prevBalanceMap` |
| `mvc/views/report/duefees/DueFeesReport.php` | Due Fees Report view — has Prev C/F column |
| `mvc/controllers/Balancefeesreport.php` | Balance Fees Report — has `prevBalanceMap` |
| `mvc/views/report/balancefees/BalanceFeesReport.php` | Balance Fees Report view |
| `mvc/views/report/balancefees/BalanceFeesReportRows.php` | Lazy-loaded rows for Balance Fees |

---

## 2. Routes (mvc/config/routes.php)

```php
$route['global_payment/new']                       = 'global_payment_new/index';
$route['global_payment/new/(:num)/(:num)']         = 'global_payment_new/index/$1/$2';
$route['global_payment/new/(:any)']                = 'global_payment_new/index/$1';
```

**Important:** The `(:num)/(:num)` route MUST come before `(:any)`.
CI3's `(:any)` only matches one URL segment (no slashes). Two-segment URLs like `/new/5/210` need the explicit `(:num)/(:num)` route.

---

## 3. Database Tables

### Core tables used by Global Payment:

| Table | Key Columns | Notes |
|-------|-------------|-------|
| `invoice` | `invoiceID, studentID, schoolyearID, feetypeID, maininvoiceID, amount, discount, paidstatus` | `discount` is FLAT RUPEE amount, NOT a percentage |
| `maininvoice` | `maininvoiceID, schoolyearID, studentID, paidstatus` | Group/summary invoice |
| `payment` | `paymentID, invoiceID, studentID, schoolyearID, globalpaymentID, paymentamount, paymentdate, paymenttype` | Column is `paymentdate` (NOT `created_date`), `paymenttype` (NOT `payment_type`) |
| `globalpayment` | `globalpaymentID, studentID, schoolyearID, invoicename, invoicenumber, invoicedescription, paymentyear, clearancetype` | `clearancetype` (NOT `payment_status`). No `invoicenumber` column — generated as `CONCAT('INV-G-', globalpaymentID)` |
| `weaverandfine` | `id, invoiceID, studentID, schoolyearID, paymentID, weaver, fine` | `weaver` = discount/waiver amount |
| `student_carry_forward` | `id, studentID, from_schoolyearID, to_schoolyearID, from_year_name, total_fee, total_discount, total_paid_in_year, total_waiver, carry_forward_due, status` | Cache table for prev year dues. UNIQUE KEY on `(studentID, from_schoolyearID, to_schoolyearID)` |

---

## 4. CRITICAL: Discount Formula

**Discount in `invoice.discount` is a FLAT RUPEE amount, NOT a percentage.**

```php
// CORRECT formula (used in Global_payment_new.php and all reports):
$net_payable = $invoice->amount - $invoice->discount;
$due = $net_payable - $paid - $waiver;

// WRONG formula (used in old Global_payment.php — DO NOT use):
$totalAmount = $invoice->amount - (($invoice->amount / 100) * $invoice->discount);
```

---

## 5. CRITICAL: Invoice paidstatus Values

| Value | Meaning |
|-------|---------|
| `0` | Unpaid |
| `1` | Partial |
| `2` | Fully Paid |

**Bug in old `Global_payment.php`:** `paymentSend()` hardcoded `schoolyearID` from session when updating `invoice.paidstatus`. Previous-year invoices were never found, so paidstatus never updated.

**Fix in `Global_payment_new.php`:** Uses `$post_schoolyearID` from POST if set, else falls back to session schoolyearID:
```php
$post_schoolyearID = $this->input->post('schoolyearID');
$effectiveYearID   = (!empty($post_schoolyearID)) ? $post_schoolyearID : $schoolyearID;
```

---

## 6. Controller Methods — Global_payment_new.php

| Method | Purpose |
|--------|---------|
| `index($classesID, $studentID)` | Main page load. Loads student, invoices, payments, carry-forward |
| `computeCarryForward($studentID, $currentSchoolyearID)` | Calculates all previous-year dues. Returns `prev_balances[]` array + `total_carry_forward_due` |
| `paymentSend()` | AJAX: saves payment. Uses `$effectiveYearID` to fix prev-year paidstatus bug |
| `getPaymentDetails()` | AJAX: returns payment history rows for the popup modal |
| `print_reciept($studentID, $globalpaymentID, $prev_schoolyearID)` | Receipt page. 3rd segment = prev year schoolyearID |
| `sectioncall()` | AJAX: cascade dropdown — sections for a class |
| `studentcall()` | AJAX: cascade dropdown — students for class+section |
| `generateAllPaymentAmount($payments)` | Returns `[invoiceID => total_paid]` map |
| `generateAllPaymentAmountWithGlobalID($payments)` | Returns `[paid][gid], [weaver][gid], [fine][gid], [paiddate][gid], [invoice_id][gid][], [paid_per_type][gid][]` |
| `generateAllWeaverAmount($weaverandfines)` | Returns `[invoiceID => total_waiver]` map |

---

## 7. computeCarryForward() Logic

```
For each previous school year (schoolyearID < currentSchoolyearID):
  1. Fetch invoices for student in that year (active only, paidstatus < 2 check skipped — all fetched)
  2. Fetch payments by [studentID, schoolyearID]
  3. Fetch weavers by [studentID, schoolyearID]
  4. For each invoice:
       net = invoice.amount - invoice.discount  (flat discount)
       paid = sum of payments for this invoiceID
       waiver = sum of weavers for this invoiceID
       due = net - paid - waiver
  5. If total_due for the year > 0:
       Upsert into student_carry_forward table
       Append to $prev_balances array
  6. Return ['prev_balances' => [...], 'total_carry_forward_due' => float]
```

### prev_balances array structure:
```php
$prev_balances[$yIdx] = [
    'schoolyearID' => int,
    'year_name'    => string,       // e.g. "2024-2025"
    'invoices'     => $invoices,    // invoice objects
    'payments'     => [invoiceID => total_paid],
    'waivers'      => [invoiceID => total_waiver],
    'total_fee'    => float,
    'total_paid'   => float,
    'total_waiver' => float,
    'due'          => float,        // carry-forward due for this year
];
```

---

## 8. getPaymentDetails() SQL

```sql
SELECT
    gp.globalpaymentID,
    CONCAT('INV-G-', gp.globalpaymentID) AS invoicenumber,
    i.feetype,
    p.paymentamount,
    i.discount,
    COALESCE(wf.fine,  0) AS fine,
    COALESCE(wf.weaver,0) AS waiver,
    (p.paymentamount + COALESCE(wf.fine, 0)) AS total_collection,
    COALESCE(gp.clearancetype, 'paid') AS payment_status,
    DATE_FORMAT(p.paymentdate, '%d-%b-%Y') AS payment_date
FROM payment p
LEFT JOIN invoice       i  ON i.invoiceID       = p.invoiceID
LEFT JOIN globalpayment gp ON gp.globalpaymentID = p.globalpaymentID
LEFT JOIN weaverandfine wf ON wf.paymentID       = p.paymentID
WHERE p.studentID = ? AND p.schoolyearID = ?
ORDER BY gp.globalpaymentID ASC, p.paymentID ASC
```

**Note:** `globalpayment.invoicenumber` column does NOT exist. Invoice number is always generated as `INV-G-{globalpaymentID}`.

---

## 9. generateAllPaymentAmountWithGlobalID() Output Structure

```php
$paidpayments['paid'][$gid]          // float: total paid for this globalpaymentID
$paidpayments['weaver'][$gid]        // float: total waiver
$paidpayments['fine'][$gid]          // float: total fine
$paidpayments['paiddate'][$gid]      // string: payment date (first payment)
$paidpayments['invoice_id'][$gid]    // array: ALL fee type names for this gid (NOT just first)
$paidpayments['paid_per_type'][$gid] // array: individual payment amounts per fee type
```

**Important:** `invoice_id` and `paid_per_type` are arrays (one entry per fee type under the same globalpaymentID). The view uses `count($feeTypes)` for rowspan in the Payment History table.

---

## 10. paymentSend() — Key POST Fields

| Field | Description |
|-------|-------------|
| `classesID` | Student's class |
| `studentID` | Student ID |
| `invoicename` | Invoice/receipt name |
| `invoicedescription` | Manual receipt / description |
| `invoicenumber` | e.g. `INV-G-191` |
| `paymentyear` | 4-digit year (e.g. `2026`) |
| `payment_status` | `paid` / `partial` / `unpaid` |
| `payment_type` | `cash` / `chaque` / `digita` |
| `paid[]` | Array of `{paidFieldID: 'paid-{invoiceID}-{feetypeID}', value: amount}` |
| `weaver[]` | Array of `{weaverFieldID: 'weaver-...', value: amount}` |
| `fine[]` | Array (empty `[]` for new page — Fine column removed from UI) |
| `created_date` | Payment date |
| `send_whatsapp` | `1` or `0` |
| `schoolyearID` | **ONLY set for previous-year payments** — triggers `$effectiveYearID` fix |
| `is_previous_year_amount` | Year name string — marks as carry-forward payment |

---

## 11. Report Integration — prevBalanceMap

Both `Duefeesreport.php` and `Balancefeesreport.php` use a private `getPrevBalanceDue()` method:

```php
private function getPrevBalanceDue($studentID, $currentSchoolyearID) {
    // Loops all previous years, fetches invoices+payments+weavers
    // Uses flat discount formula
    // Returns total due across all previous years (float, min 0)
}
```

The map is built and passed to the view:
```php
$this->data['prevBalanceMap'][$studentID] = $this->getPrevBalanceDue($studentID, $schoolyearID);
```

**In Balance Fees Report:** `prevBalanceMap` is computed in BOTH `getBalanceFeesReport()` (full load) AND `getBalanceFeesReportLazy()` (lazy-loaded batches). If amounts are missing in lazy-loaded rows, check `getBalanceFeesReportLazy()`.

---

## 12. Previous Year Payment — Receipt URL

```php
// Normal current-year receipt:
/Global_payment_new/print_reciept/{studentID}/{globalpaymentID}

// Previous-year receipt (3rd segment = prev schoolyearID):
/Global_payment_new/print_reciept/{studentID}/{globalpaymentID}/{prev_schoolyearID}
```

The `print_reciept()` method uses the 3rd segment to set `$schoolyearID` for fetching invoices/payments of the correct year. It also sets `$is_prev_year_receipt = true` and `$receipt_year_name` so the receipt view shows "(2024-2025)" labels next to fee names.

---

## 13. Known Bugs in OLD Global_payment.php (NOT in new page)

### Bug 1 — paidstatus never updates for prev-year payments
```php
// Line 581 in old controller — uses session schoolyearID, ignores prev year:
$invoices = $this->invoice_m->get_order_by_invoice(array('schoolyearID' => $schoolyearID, ...));
// Fix: use POST schoolyearID if set
```

### Bug 2 — Discount treated as percentage
```php
// Old code:
$totalAmount = $invoice->amount - (($invoice->amount / 100) * $invoice->discount);
// Correct (flat rupee):
$totalAmount = $invoice->amount - $invoice->discount;
```

Both bugs are FIXED in `Global_payment_new.php`. The old `Global_payment.php` is untouched.

---

## 14. student_carry_forward Table

```sql
CREATE TABLE `student_carry_forward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `studentID` int(11) NOT NULL,
  `from_schoolyearID` int(11) NOT NULL,
  `to_schoolyearID` int(11) NOT NULL,
  `from_year_name` varchar(128) NOT NULL DEFAULT '',
  `total_fee` double NOT NULL DEFAULT 0,
  `total_discount` double NOT NULL DEFAULT 0,
  `total_paid_in_year` double NOT NULL DEFAULT 0,
  `total_waiver` double NOT NULL DEFAULT 0,
  `carry_forward_due` double NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cf` (`studentID`,`from_schoolyearID`,`to_schoolyearID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

- Used as a **cache** — `computeCarryForward()` upserts on every page load
- `status`: `pending` / `partial` / `cleared`
- `from_schoolyearID` = year the dues are from; `to_schoolyearID` = current active year

---

## 15. Common Issues & Quick Fixes

| Issue | Cause | Fix |
|-------|-------|-----|
| 404 on `/global_payment/new/5/210` | CI3 `(:any)` doesn't match slashes | Ensure `(:num)/(:num)` route is before `(:any)` in routes.php |
| Previous year paidstatus stays 0 | schoolyearID mismatch in paymentSend | Pass `schoolyearID` in POST for prev-year payments; controller uses `$effectiveYearID` |
| Prev balance not showing in reports | `getPrevBalanceDue()` missing or prevBalanceMap not built for lazy rows | Check both `getBalanceFeesReport()` AND `getBalanceFeesReportLazy()` in Balancefeesreport.php |
| Receipt shows wrong year fees | prev_schoolyearID not passed as 3rd URL segment | Ensure receipt URL includes `/{prev_schoolyearID}` as 3rd segment for prev-year payments |
| `result_array() on bool` in getPaymentDetails | Wrong column names in SQL | Correct names: `gp.clearancetype`, `p.paymentdate`, no `gp.invoicenumber` |
| Amount totals don't match between page and reports | Discount treated as % somewhere | Ensure all calculations use flat: `amount - discount` (not `amount * discount / 100`) |
| Payment History shows only 1 fee type per invoice | Old `generateAllPaymentAmountWithGlobalID` only stored first fee type | Method now appends all fee types as array in `invoice_id[$gid][]` and `paid_per_type[$gid][]` |
| Carry-forward amount wrong | Waiver not subtracted, or wrong schoolyearID used | Verify: `due = (amount - discount) - paid - waiver` for each invoice in each prev year |

---

## 16. Language Keys Added (global_payment_new_lang.php)

Key new language keys used in prev_balance_statement.php:
- `global_prev_balance_title` — "Previous Year Balance Statement"
- `global_prev_year` — "Year"
- `global_prev_total_fees` — "Total Fees"
- `global_prev_paid` — "Paid"
- `global_prev_waiver` — "Waiver"
- `global_prev_balance_due` — "Balance Due"
- `global_carry_forward_total` — "Total Carry Forward Due"
- `global_pay_now` — "Pay Now"
