# Dynamic Column Selector — Playbook

> [!IMPORTANT]
> **To the AI Assistant:** This file is a self-contained implementation playbook for adding a "Columns" show/hide checkbox dropdown to any report's output table. The user will invoke this later by giving you: **this file name + the target report's name + a screenshot**. When that happens, follow this file step by step against the target report's actual files — don't re-derive the approach from scratch, and don't invent new CSS/JS patterns; reuse what's documented here verbatim.
>
> Before applying this to a new report, also read `srinivas_project_structure.md` (§8.3/8.3.1/8.5/8.6 — Excel export patterns) and `srinivas_reports_design.md` (§4/§6 — filter card + action bar classes) if you haven't already this session. This file assumes both are known.

---

## 1. What This Feature Is

A **"Columns" dropdown button** next to Print/Export to Excel on a report's output page. It shows a checkbox per table column (auto-built from whatever `<th>`s the server actually rendered). Unchecking a column hides it from the on-screen table **and** excludes it from the Excel export — live, no page reload, no server round-trip.

**Reference implementation** (built 2026-08-05): Fees Report.
- `mvc/views/report/fees/FeesReport.php` — the AJAX-rendered output partial (table + button + dropdown + JS all live here)
- `assets/css/reports.css` §17 — the reusable `.rpt-col-selector-*` CSS (already written, generic, **do not duplicate it into a per-report `<style>` block** — that violates the reports.css golden rule in `srinivas_reports_design.md`)

---

## 2. Step 0 — Identify the Target Report's Excel Export Pattern FIRST

**This is the step most likely to get skipped and cause a broken feature.** The checkbox UI and column-hiding JS are always the same, but *how the hide-state reaches the exported Excel file* depends entirely on which export pattern the target report already uses. Open the report's output view file and find its export button's JS/PHP before writing anything.

| Pattern (see `srinivas_project_structure.md` for full detail) | How to recognize it | How column-hiding reaches the export |
|---|---|---|
| **8.3.1 — `table_to_book()` one-liner** | `XLSX.utils.table_to_book(table, {...})` reading a live DOM `<table>` directly | Add `display: true` to the options object. Done — SheetJS skips any element with `display:none` automatically. **(Fees Report uses this.)** |
| **8.3 — `table_to_sheet()` cleaned clone** | Clones the table (`cloneNode(true)`), strips `data-toggle`/tooltip attrs, then `XLSX.utils.table_to_sheet(tbl, {...})` | Same fix: add `display: true` to the options object. `cloneNode(true)` copies inline styles too, so a `td` hidden via `.css('display','none')` stays hidden in the clone — the option still works. |
| **8.5 — HTML blob (`.xls`, no library)** | JS builds an HTML string by hand from a JS data object (not from a DOM table) and downloads it as a `Blob` | **No `display` option exists here** — you must skip appending the `<td>`/`<th>` string for any column whose checkbox is unchecked while building the HTML string. Wrap each column's cell-building line in `if (isColumnVisible('key')) { ... }`. |
| **8.6 — Server-side PhpSpreadsheet (direct download link)** | A plain `<a href="...">` / `btn_xmlReport()` pointing at a controller method that builds the file in PHP with `phpspreadsheet` | **Fundamentally different — the browser's checkbox state never reaches PHP unless you send it.** See §5.4 below. This is the pattern most likely to need real backend changes, not just a JS tweak. |

**If you're not sure which pattern a report uses**, grep the report's `*Report.php` view for `XLSX.utils`, `Blob(`, or `btn_xmlReport`/`phpspreadsheet` — exactly one of those will appear.

---

## 3. Step 1 — Tag Every Table Cell with `data-col`

Add `data-col="shortkey"` to every `<th>` in `<thead>` **and** the matching `<td>` in every `<tbody>` row. Use short, stable keys (`slno`, `name`, `roll`, `paid`, etc.) — these become the checkbox identities and the JS selector target.

```php
<th data-col="paid"><?=$this->lang->line('yourreport_paid')?></th>
...
<td data-col="paid" data-title="...">...</td>
```

**Conditionally-rendered columns** (e.g. a "Class" column only shown when no specific class filter is applied) keep their existing PHP `if` — just add `data-col` inside the `if` block. The JS builds its checkbox list by reading whatever `<th data-col>` elements actually exist in the DOM, so conditional columns are handled automatically — no special-casing needed.

**Merged/summary rows (grand total, sub-total, breakdown bars) are the tricky part.** If the table has a row where one `<td colspan="N">` spans several data columns (e.g. a "Grand Total" label spanning columns 1–9), you cannot give it a single `data-col` — instead:
- Give it `data-col-group="group1"` (or `group2`, etc. — one group per distinct colspan region).
- The JS (§4) recomputes that cell's `colspan` live, counting how many of the columns in that group are still visible.
- If a summary row has **individual** total cells that map 1:1 to a real column (e.g. a "Total Paid" cell under the "Paid" column), just give it the same `data-col="paid"` as the header — no group needed, it hides/shows like any other cell.

Reference: `FeesReport.php` lines ~296–322 — `data-col-group="group1"` covers 7–9 columns (Sl.No through Fee Type depending on filter), `group2` covers the 4-column Cash/Digital/Cheque/Others breakdown bar, and the grand-total row's Paid/Weaver/Fine cells use plain `data-col`.

**If the target report's table has no merged/summary row at all** (a plain flat table), skip `data-col-group` entirely and drop the `recomputeGroupColspans()` function from the JS in §4 — nothing to recompute.

---

## 4. Step 2 — The Button + Dropdown HTML (copy verbatim, change only IDs if the page already has an `#exportButton` etc.)

Place this next to the existing Print/Export buttons, inside a `.rpt-action-bar` wrapper (see `srinivas_reports_design.md` §6):

```php
<div class="rpt-action-bar">
    <?php /* existing Print button, etc. */ ?>
    <button id="exportButton" class="btn btn-success rpt-action-btn"><i class="fa fa-file-excel-o"></i> Export to Excel</button>

    <div class="btn-group rpt-col-selector-group" id="columnSelectorGroup">
        <button type="button" class="btn btn-info rpt-action-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-columns"></i> Columns <span class="caret"></span>
        </button>
        <div class="dropdown-menu rpt-col-selector-menu" id="columnSelectorMenu">
            <div class="rpt-col-selector-header">
                <span class="rpt-col-selector-title"><i class="fa fa-eye"></i> Show Columns</span>
                <span class="rpt-col-selector-actions">
                    <a href="javascript:void(0)" id="columnSelectAll">Select All</a>
                    <a href="javascript:void(0)" id="columnDeselectAll">Deselect All</a>
                </span>
            </div>
            <div class="rpt-col-selector-list" id="columnSelectorList">
                <!-- checkboxes injected by JS -->
            </div>
            <div class="rpt-col-selector-footer">
                <span id="columnSelectedCount">0</span>/<span id="columnTotalCount">0</span> columns shown
            </div>
        </div>
    </div>
</div>
```

**Do not** write a new `<style>` block for this. All of `.rpt-col-selector-*` already exists in `assets/css/reports.css` §17 and is loaded globally via `_layout_main.php` — it's generic (not Fees-Report-specific) and works as-is on any report.

**If two reports render this dropdown on the same page at the same time** (shouldn't normally happen, but if a report has two tables), give the second instance's IDs a suffix (`columnSelectorMenu2`, etc.) and adjust the JS selectors in §5 to match — the CSS classes are safe to reuse, only the IDs need to stay unique per table.

---

## 5. Step 3 — The JS (copy verbatim, then adapt only the two marked spots)

```javascript
<script>
    $(document).ready(function () {
        var $table = $('#myTable'); // ← change to the target report's actual table ID
        if (!$table.length) { return; }

        // ← ADAPT: list every data-col key that sits inside each colspan'd summary group,
        //   in left-to-right order. Delete both arrays + recomputeGroupColspans() entirely
        //   if the table has no merged summary row (see §3).
        var GROUP1_COLS = ['slno', 'invoice', 'paymentdate', 'cashier', 'name', 'roll', 'class', 'section', 'feetype'];
        var GROUP2_COLS = ['paid', 'paymenttype', 'weaver', 'fine'];

        var $menu = $('#columnSelectorMenu');
        var $list = $('#columnSelectorList');
        $table.find('thead th[data-col]').each(function () {
            var col = $(this).attr('data-col');
            var label = $.trim($(this).text());
            var $item = $('<label>', { class: 'rpt-col-selector-item' });
            var $checkbox = $('<input>', { type: 'checkbox', 'data-col': col, checked: true });
            var $text = $('<span>', { text: label, title: label });
            $item.append($checkbox).append($text);
            $list.append($item);
        });

        function updateSelectedCount() {
            var total = $list.find('input[type="checkbox"][data-col]').length;
            var selected = $list.find('input[type="checkbox"][data-col]:checked').length;
            $('#columnSelectedCount').text(selected);
            $('#columnTotalCount').text(total);
        }
        updateSelectedCount();

        function recomputeGroupColspans() {
            var group1Visible = GROUP1_COLS.filter(function (col) {
                var $th = $table.find('thead th[data-col="' + col + '"]');
                return $th.length && $th.css('display') !== 'none';
            }).length;
            var group2Visible = GROUP2_COLS.filter(function (col) {
                var $th = $table.find('thead th[data-col="' + col + '"]');
                return $th.length && $th.css('display') !== 'none';
            }).length;
            $table.find('td[data-col-group="group1"]').attr('colspan', Math.max(group1Visible, 1));
            $table.find('td[data-col-group="group2"]').attr('colspan', Math.max(group2Visible, 1));
        }

        function toggleColumn(col, visible) {
            $table.find('[data-col="' + col + '"]').css('display', visible ? '' : 'none');
            recomputeGroupColspans(); // ← delete this line too if you deleted the function above
            updateSelectedCount();
        }

        $list.on('change', 'input[type="checkbox"][data-col]', function () {
            toggleColumn($(this).attr('data-col'), $(this).is(':checked'));
        });
        $('#columnSelectAll').on('click', function () {
            $list.find('input[type="checkbox"][data-col]').prop('checked', true).each(function () {
                toggleColumn($(this).attr('data-col'), true);
            });
        });
        $('#columnDeselectAll').on('click', function () {
            $list.find('input[type="checkbox"][data-col]').prop('checked', false).each(function () {
                toggleColumn($(this).attr('data-col'), false);
            });
        });
        $menu.on('click', function (e) { e.stopPropagation(); }); // keeps dropdown open while clicking checkboxes
    });
</script>
```

**AJAX-rendered reports (the common case — table only exists after clicking "Get Report"):** this `<script>` block must live **inside the same AJAX-returned partial as the table itself** (i.e. in `*Report.php`, not `*ReportView.php`). jQuery executes `<script>` tags injected via `.html()`, and `$(document).ready()` fires immediately once the document is already loaded — exactly like `FeesReport.php` does it. Do not try to put this script in the static filter view; it will run before the table exists and find nothing.

---

## 5.4 — Wiring the Export: the branch that matters (from Step 0)

**If the report uses 8.3 or 8.3.1 (client-side SheetJS):** just add `display: true` to the existing call:
```javascript
var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1", display: true });
// or: var ws = XLSX.utils.table_to_sheet(tbl, { raw: false, display: true });
```
That's the entire change. Verify by unchecking a column, exporting, and opening the file — the header row should be missing that column.

**If the report uses 8.5 (HTML blob):** there's no automatic option. Add a small helper and guard every cell-building line:
```javascript
function isColumnVisible(col) {
    var cb = document.querySelector('#columnSelectorList input[data-col="' + col + '"]');
    return !cb || cb.checked;
}
// then, wherever the HTML string is built:
if (isColumnVisible('paid')) { h += '<td style="...">' + row.paid + '</td>'; }
```
Also guard the corresponding `<th>` the same way, and (if the table has merged header cells with `colspan`) recompute those colspans in the string-builder the same way `recomputeGroupColspans()` does for the live table.

**If the report uses 8.6 (server-side PhpSpreadsheet) — do this instead of a pure JS tweak:**
The PHP controller method has no idea what's checked in the browser, so you must submit that state. Two sub-cases:

- **If Export is a plain `<a href="...">` (most common for 8.6):** convert it to a small JS handler that builds the URL with the visible column keys appended, e.g. `feesreport/xlsx/...?cols=slno,name,paid`, then have the controller's export method read `$this->input->get('cols')`, explode on comma, and wrap every `$sheet->setCellValue(...)` call for header + body in `if (in_array('paid', $visibleCols)) { ... }` — using the **same fixed-column-letter approach already documented in `srinivas_project_structure.md` §8.6** (do not go back to the old conditional-column-counting style that section explicitly warns against; instead, keep the column *positions* fixed and skip writing to a column's letter entirely when it's excluded, or shift subsequent letters — pick whichever the existing method already does for its class/section conditional columns and stay consistent with it).
- **If Export is already an AJAX POST** (rarer): just add the checked column keys array to the existing POST payload, same idea.

This is real backend work, not a copy-paste — budget more time for this branch than the other three.

---

## 6. Step 4 — Action Bar Button Styling

Wrap Print / Export / Columns in `.rpt-action-bar` (§6 of `srinivas_reports_design.md`) if not already done. Suggested color convention (matches Fees Report):
- Export to Excel → `btn btn-success rpt-action-btn` + `fa-file-excel-o`
- Columns → `btn btn-info rpt-action-btn` + `fa-columns`
- Print → leave on whatever `btn_printReport()` / `btn_pdfPreviewReport()` already returns (`btn btn-default`) — **do not edit `action_helper.php`**, it's shared by every report.

---

## 7. Step 5 — If You Touch `reports.css`, Bump the Cache-Buster

`assets/css/reports.css` is linked with `?v=<?=CSSVERSION?>` in `_layout_main.php`. If this feature ever needs a **new** CSS rule beyond the existing `.rpt-col-selector-*` (§17) — e.g. a report-specific tweak — bump the constant so browsers actually fetch the change:

```php
// mvc/config/constants.php
defined('CSSVERSION') OR define('CSSVERSION', '2.007'); // increment the last segment
```
Forgetting this step is the #1 cause of "I changed the CSS but nothing looks different" — the browser is serving its cached copy of the old file under the old `?v=` URL.

---

## 8. Verification Checklist

1. Open the report, run a search that returns rows.
2. Click **Columns** — dropdown opens, one checkbox per rendered `<th>`, all checked, footer shows `N/N columns shown`.
3. Uncheck 2–3 columns — they disappear from the table immediately; any merged summary/total row's `colspan` visibly shrinks instead of leaving a gap; footer counter updates.
4. Click **Deselect All** / **Select All** — all columns hide/show together.
5. Click **Export to Excel** with some columns unchecked — open the downloaded file and confirm the header row (and every data row) is missing exactly the unchecked columns, nothing else changed.
6. Check the browser console for errors — there should be none introduced by this feature (pre-existing unrelated 404s for missing images/assets are fine, don't chase those).

---

## 9. Reference Files

| What | File |
|---|---|
| Reference implementation (all steps) | `mvc/views/report/fees/FeesReport.php` |
| Shared CSS (§17) | `assets/css/reports.css` |
| Design system rules this depends on | `srinivas_reports_design.md` (§4 filter card, §6 action bar) |
| Excel export pattern catalogue (Step 0) | `srinivas_project_structure.md` §8.3 / §8.3.1 / §8.5 / §8.6 |
| Report architecture / AJAX skeleton | `srinivas_report_instructions.md` |

---

## 10. Maintenance Log

- **2026-08-05**: Created this playbook after implementing the first instance on Fees Report. Documented the 4-way Excel-export branch (8.3 / 8.3.1 / 8.5 / 8.6) since that's the part that differs per report — the checkbox UI/JS/CSS itself is fully generic and reusable as-is.
