# Reports Design System — OurSchoolERP

> [!IMPORTANT]
> **To the AI Assistant:** Read this file entirely before touching any report page (filter form, table output, header, footer, or CSS). All design decisions, class names, file locations, and rules are documented here. Follow these patterns exactly — do NOT introduce new patterns without updating this file.

---

## 1. The Golden Rule

> **One CSS file. Zero inline styles. Zero per-file `<style>` blocks.**

All report UI styles live in **`assets/css/reports.css`** only.  
PDF files (`*ReportPDF.php`) are **never touched** — they have their own print-specific styles.

---

## 2. CSS File Locations

| File | Purpose | Loaded via |
|------|---------|-----------|
| `assets/css/reports.css` | All report UI styles (filter, table, header, buttons, scroll) | `mvc/views/_layout_main.php` — one `<link>` tag, available globally |
| `assets/css/report-buttons.css` | Export/action button gradient styles | Also in `_layout_main.php` — keep, do not remove |
| `assets/css/custom_table.css` | General table/form styles (app-wide) | `page_header.php` — do not touch |

**How CSS reaches report pages:**  
`mvc/views/_layout_main.php` → `<link>` tag → loaded on every page automatically.  
Report view files do **not** need their own `<link>` tags for reports.css.

---

## 3. Report Header — `reportheader()` Function

**Location:** `mvc/helpers/action_helper.php`

**Function signature (updated):**
```php
function reportheader($setting, $schoolyear, $pdf = false, $student = null)
```
- `$student` is optional (default `null`) — all existing 2–3 arg calls work unchanged
- When `$student` is passed, the student photo + name + roll is shown on the right side

**HTML structure produced (uses CSS classes, zero inline styles):**
```
┌──────────────────────────────────────────────────────────┐
│  .rpt-logo        .rpt-school-info       .rpt-student-block │
│  [80px logo]      School Name (bold)     [60px photo]    │
│                   Address                Name / Roll     │
│                   Academic Year: 2025-26 Class / Section │
│ ──────────── green border-bottom ────────────────────── │
└──────────────────────────────────────────────────────────┘
```

**CSS classes used:**
| Class | Element | Notes |
|-------|---------|-------|
| `.reportPage-header` | Outer flex wrapper | `display:flex; align-items:center; border-bottom: 2px solid #4CAF50` |
| `.rpt-logo` | Logo `<div>` | Left, fixed width 80px |
| `.rpt-school-info` | School text `<div>` | `flex:1`, centre |
| `.rpt-school-name` | School name `<h2>` | 24px, bold, dark |
| `.rpt-school-address` | Address `<p>` | 14px, #666 |
| `.rpt-school-year` | Year `<p>` | 13px, #999 |
| `.rpt-student-block` | Student info `<div>` | Right, 120px, shown only when `$student` passed |
| `.rpt-student-photo` | Student `<img>` | 60×60, rounded, border |
| `.rpt-student-meta` | Name/roll text | 12px |

**Footer — `reportfooter()` classes:**
| Class | Notes |
|-------|-------|
| `.rpt-footer` | Flex row, border-top, centred |
| `.rpt-footer-logo` | 28px img |
| `.rpt-footer-text` | 12px muted — footer text + phone |

---

## 4. Filter Form Design (All `*ReportView.php` files)

### HTML Pattern
```html
<div class="box">
  <div class="box-header">...</div>
  <div class="box-body">

    <div class="rpt-filter-card">                          <!-- GREEN CARD WRAPPER -->
      <div class="rpt-filter-title">
        <i class="fa fa-filter"></i>&nbsp; Filter Options
      </div>

      <div class="row">
        <div class="form-group col-sm-4">                  <!-- each field -->
          <label>Field Label</label>
          <!-- dropdown / input -->
        </div>
      </div>

      <div class="rpt-filter-actions">                     <!-- button row -->
        <button class="btn btn-success rpt-filter-btn">
          <i class="fa fa-search"></i> Get Report
        </button>
      </div>
    </div><!-- /.rpt-filter-card -->

  </div>
</div>
```

### Key Rules
- `rpt-filter-card` — green gradient bg (`#f8fffe → #edf7ee`), `#c8e6c9` border, 8px radius
- `rpt-filter-title` — 12px, 700, uppercase, green, border-bottom separator
- Labels — 11px, 700, uppercase, #666 (via `.rpt-filter-card label` selector)
- Date input fields — wrapped in `<div class="input-group">` with calendar icon prefix
- Buttons — `btn btn-success rpt-filter-btn` (NO inline `style="margin-top:23px"`)
- Two-button pages (e.g. Horizontal + Vertical) — both in `.rpt-filter-actions`, side by side
- `rpt-filter-actions` — `text-align:right`, `border-top: 1px solid #c8e6c9`, `margin-top:14px`

---

## 5. Report Output Design (All `*Report.php` non-PDF files)

### HTML Pattern
```html
<!-- Action / Export buttons -->
<div class="rpt-action-bar">
  <a href="..." class="btn btn-success rpt-action-btn">
    <i class="fa fa-file-excel-o"></i> Export XLSX
  </a>
  <button class="btn btn-info rpt-action-btn">
    <i class="fa fa-download"></i> Download
  </button>
</div>

<!-- Report box -->
<div class="box" style="border-top: 3px solid #3949ab;">
  <div class="rpt-box-header">
    <h3><i class="fa fa-clipboard"></i> Report Title</h3>
  </div>

  <div id="printablediv">
    <div class="box-body">

      <?= reportheader($siteinfos, $schoolyearsessionobj) ?>

      <!-- Class / Section info bar -->
      <div class="rpt-class-info">
        <span><i class="fa fa-graduation-cap"></i> Class: <strong>...</strong></span>
        <span><i class="fa fa-users"></i> Section: <strong>...</strong></span>
      </div>

      <!-- Table with horizontal scroll -->
      <div id="rpt-wrap-reportname" class="rpt-table-wrap">
        <table class="table table-bordered rpt-table">
          <thead>
            <tr>
              <th>#</th>
              <th class="rpt-sticky-left-hd">Name</th>  <!-- sticky left header -->
              <!-- other columns -->
              <th class="rpt-sticky-right-hd">Total</th> <!-- sticky right header -->
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td class="rpt-sticky-left">Student Name</td>
              <!-- other cells -->
              <td class="rpt-sticky-right">5,000.00</td>
            </tr>
          </tbody>
        </table>
      </div><!-- /.rpt-table-wrap -->

      <!-- Sticky bottom scrollbar (immediately after rpt-table-wrap) -->
      <div class="rpt-hscroll-bar" id="hbar-reportname">
        <div class="rpt-hscroll-inner"></div>
      </div>

      <?= reportfooter($siteinfos, $schoolyearsessionobj) ?>

    </div>
  </div>
</div>

<!-- Scroll-to-top button -->
<button class="rpt-scroll-top-btn" id="scroll-to-top-btn" title="Back to top">&#8679;</button>
```

---

## 6. CSS Class Reference (Quick Lookup)

### Filter Form
| Class | Used on | Effect |
|-------|---------|--------|
| `.rpt-filter-card` | wrapper div | Green gradient card |
| `.rpt-filter-title` | title div | Bold green uppercase header |
| `.rpt-filter-actions` | button row div | Right-aligned, top border |
| `.rpt-filter-btn` | submit button | Replaces `style="margin-top:23px"` |

### Report Output
| Class | Used on | Effect |
|-------|---------|--------|
| `.rpt-box-header` | box-header div | Gradient bg + left colour border |
| `.rpt-class-info` | class/section bar | Flex space-between info pill |
| `.rpt-action-bar` | export btn wrapper | Flex row, gap:10px |
| `.rpt-action-btn` | each export button | 7px 16px, hover lift |
| `.rpt-table` | `<table>` | Green header (`#4CAF50`), clean body — the project default. Add `.rpt-table-navy` too for the navy/indigo alternative (§18) |
| `.rpt-table-wrap` | scroll wrapper div | `overflow-x: auto` |
| `.rpt-hscroll-bar` | sticky scrollbar div | `position:fixed; bottom:0` mirror bar |
| `.rpt-hscroll-inner` | spacer inside bar | Width = table scrollWidth (set by JS) |

### Sticky Columns
| Class | Used on | Effect |
|-------|---------|--------|
| `.rpt-sticky-left` | body `<td>` | Sticky left, white/zebra (`#fff` / `#f9f9f9`) |
| `.rpt-sticky-left-hd` | header `<th>` | Sticky left, green `#4CAF50` (matches `.rpt-table` default header) |
| `.rpt-sticky-right` | body `<td>` | Sticky right, light green `#e8f5e9` |
| `.rpt-sticky-right-hd` | header `<th>` | Sticky right, dark green `#2e7d32` |

### Scroll Utilities
| Class/ID | Effect |
|----------|--------|
| `.rpt-scroll-top-btn` | Fixed ↑ button, bottom-right, appears after 200px scroll |

### Column Selector (generic, §17 — reuse on any report)
| Class | Used on | Effect |
|-------|---------|--------|
| `.rpt-col-selector-group` | `.btn-group` wrapper | Positions the "Columns" dropdown toggle |
| `.rpt-col-selector-menu` | `.dropdown-menu` | 380px card: header + 2-col checkbox grid + footer |
| `.rpt-col-selector-header` / `-title` / `-actions` | header row | "Show Columns" title + Select All/Deselect All links |
| `.rpt-col-selector-list` | checkbox grid wrapper | 2-column grid, light-gray bg, scrolls if tall |
| `.rpt-col-selector-item` | each `<label>` checkbox row | White chip, green accent-color checkbox, hover highlight |
| `.rpt-col-selector-footer` | footer row | "`x`/`y` columns shown" live counter |

---

## 7. Sticky Column Rules

- **Always sticky-left**: The first meaningful text column — student name, employee name, subject name, etc.
- **Always sticky-right**: The last total/amount/due column
- Use **classes on elements** (not nth-child CSS selectors) — safer when column count is dynamic
- Header `<th>` gets the `-hd` variant; body `<td>` gets the base class
- Do NOT apply zebra/alternating row colors — user preference: plain white rows, sticky column highlight only

---

## 8. Sticky Bottom Horizontal Scrollbar — JS Pattern

Each report table that uses `.rpt-table-wrap` needs this JS after the table:
```javascript
(function() {
    var wrap  = document.getElementById('rpt-wrap-REPORTNAME');
    var bar   = document.getElementById('hbar-REPORTNAME');
    var inner = bar ? bar.querySelector('.rpt-hscroll-inner') : null;
    if (!wrap || !bar || !inner) return;

    function reposition() {
        var rect = wrap.getBoundingClientRect();
        bar.style.left  = rect.left + 'px';
        bar.style.width = rect.width + 'px';
        inner.style.width = wrap.scrollWidth + 'px';
    }
    function checkVisibility() {
        var rect = wrap.getBoundingClientRect();
        var wide = wrap.scrollWidth > wrap.clientWidth;
        bar.style.display = (wide && rect.top < window.innerHeight && rect.bottom > window.innerHeight)
            ? 'block' : 'none';
    }
    reposition(); checkVisibility();
    bar.addEventListener('scroll', function() { wrap.scrollLeft = bar.scrollLeft; });
    wrap.addEventListener('scroll', function() { bar.scrollLeft  = wrap.scrollLeft; });
    window.addEventListener('scroll', function() { reposition(); checkVisibility(); });
    window.addEventListener('resize', function() { reposition(); checkVisibility(); });
})();
```
**Rule:** Replace `REPORTNAME` with a short unique identifier per report (e.g. `fees`, `salary`, `attendance`).

---

## 9. Scroll-to-Top Button — JS Pattern

```javascript
$(window).on('scroll', function() {
    $(this).scrollTop() > 200
        ? $('#scroll-to-top-btn').fadeIn(300)
        : $('#scroll-to-top-btn').fadeOut(300);
});
$('#scroll-to-top-btn').on('click', function() {
    $('html, body').animate({ scrollTop: 0 }, 400);
});
```
- `bottom: 24px` when no sticky action bar is present
- `bottom: 70px` when a sticky action bar (60px tall) is also on the page

---

## 10. Due Fees Reports — Special Notes

These files were the first to be redesigned and use the same class system:

| File | Notes |
|------|-------|
| `mvc/views/report/duefees/DueFeesReportView.php` | Filter form, already uses `rpt-filter-card` pattern |
| `mvc/views/report/duefees/DueFeesReport.php` | Horizontal report — table id `#due-fees-h-table`, indigo theme |
| `mvc/views/report/duefees/DueFeesReport_vertical.php` | Vertical pivot report — table id `#myTable`, green theme |

The due fees vertical report has **unique** per-table sticky column CSS (nth-child selectors) because the number of fee-type columns is dynamic. These rules live in `reports.css` under the `#myTable` and `#due-fees-h-table` selectors and should not be removed.

---

## 11. Table Header Theme — Navy + Callout Column + Sticky Header (optional alt to the default green header)

An alternative table-header color scheme to the default green `.rpt-table` header, for when a report needs a distinct look and/or a highlighted "important amount" column (e.g. a carry-forward/overdue/balance column that should visually stand out from the rest of the row). Codifies the visual theme already used ad-hoc (inline styles + ID selectors) in `mvc/views/report/duefees/DueFeesReport.php` — that file was **not** migrated to these classes, this is net-new shared CSS in `reports.css` §18 for applying to *other* reports going forward.

**Classes** (all in `reports.css` §18):

| Class | Used on | Effect |
|-------|---------|--------|
| `.rpt-table-navy` | `<table>`, **in addition to** `.rpt-table` (`class="rpt-table rpt-table-navy"`) | Overrides the header background to navy/indigo (`#1a237e`, second header row `#283593` if the table has two header rows) |
| `.rpt-col-callout-hd` | one specific `<th>` | Orange header (`#e65100`) for a standout column — class-based, so it works on any column position, not tied to `nth-child`/`last-child` |
| `.rpt-col-callout` | the matching `<td>`s in that column | Light-orange fill (`#fff3e0`) + bold orange text (`#e65100`) |

**Sticky header comes for free — no extra CSS needed.** `.rpt-table-navy` is a color-only modifier; it must be combined with the base `.rpt-table` class (never used standalone), and `.rpt-table` already makes `thead` sticky (`position: sticky; top: 0;` on the first header row, `top: 40px` on a second header row if present — see §7/reports.css). Combining both classes gets you the navy/orange color scheme **and** a header that stays pinned to the top of the scroll container while the body scrolls — exactly like the Due Fees horizontal report already does with its own bespoke CSS.

**Example**:
```html
<table class="rpt-table rpt-table-navy rpt-table-wrap ...">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <!-- ... -->
            <th class="rpt-col-callout-hd">Prev C/F</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>K Ishitha</td>
            <!-- ... -->
            <td class="rpt-col-callout">450.00</td>
        </tr>
    </tbody>
</table>
```

**When to use**: only when explicitly asked to apply this look to a report — it's an opt-in alternative, not a replacement for the project-default green `.rpt-table` header used everywhere else.

---

## 12. Files Never to Touch

- All `*ReportPDF.php` files — print-specific, separate concern
- All controllers (`mvc/controllers/`) — no design changes there
- All models (`mvc/models/`) — no design changes there
- Any `@media print` CSS rules — leave untouched wherever found
- `report-buttons.css` and `custom_table.css` — do not modify

---

## 13. Execution Order (When Applying to a New Report)

1. Open the `*ReportView.php` (filter form) — wrap content in `.rpt-filter-card`, update button class
2. Open the `*Report.php` (output) — add `.rpt-table` + `.rpt-table-wrap`, sticky columns, scrollbar, scroll-to-top, update box-header and action-bar
3. Verify `reports.css` is already linked via `_layout_main.php` (no per-file link needed)
4. Test in browser: filter → generate → scroll horizontally → confirm sticky columns and scrollbar → scroll down → confirm ↑ button

---

## 14. Maintenance Log

- **2026-04-30**: Created this document. Designed and planned the full reports.css design system. Due fees reports (DueFeesReportView, DueFeesReport, DueFeesReport_vertical) fully implemented as reference. All other reports pending batch implementation.
- **2026-07-19**: ID Card report migrated. `IdcardReportView_new.php` wrapped in `.rpt-filter-card`/`.rpt-filter-title`/`.rpt-filter-actions`/`.rpt-filter-btn`. `IdcardReport_new.php` (non-tabular — a grid of visual ID cards, not a data table) adapted the output pattern: `.rpt-box-header` + `.rpt-action-bar`/`.rpt-action-btn` + `.rpt-scroll-top-btn` applied, but `.rpt-table`/sticky-columns/hscroll-bar were **not** used since there is no table. The per-file `.idcard-*` `<style>` block was moved into `reports.css` (§15); the school's uploaded ID card template image stays as a small dynamic inline `style="background-image:url(...)"` on `.idcard-box` since that value is per-record data, not a static design property. `reportheader()`/`reportfooter()` were intentionally not added — each card is a self-contained printable artifact, not part of a school-header-style document.
- **2026-08-05**: Fees Report — added a "Columns" show/hide dropdown next to Export to Excel (`FeesReport.php`), backed by `data-col` attributes on every `<th>`/`<td>` and a jQuery toggle that also recomputes the `colspan` on the two merged summary/grand-total cells (`data-col-group="group1"/"group2"`). The Excel export (`XLSX.utils.table_to_book`) was given `{ display: true }` so hidden columns are excluded from the downloaded file automatically — no server-side change needed. New **generic, reusable** pattern documented in `reports.css` §17: `.rpt-col-selector-*` (group/menu/header/title/actions/list/item/footer). Reuse this section as-is on any other `*Report.php` that needs the same column show/hide control — do not re-invent a per-file version. `FeesReportView.php` filter form was migrated to the `.rpt-filter-card` pattern (§4) in the same pass. The pre-existing `#myTable`/`.table-container`/`.table-responsive` inline `<style>` block in `FeesReport.php` (green header, sticky thead, min-width scroll) was **left as-is** — migrating the table output itself to `.rpt-table`/sticky-columns/hscroll-bar (§5) is a separate, larger task that wasn't in scope here, and `#myTable` is also used un-migrated by `DueFeesReport_vertical.php` with different styling, so moving it into the shared `reports.css` without doing that full migration would risk a cross-report collision. Also created a companion playbook file, `srinivas_reports_dynamic_columns.md`, documenting how to apply this same column-selector feature to any other report (including the 4-way Excel-export-pattern branch it needs).
- **2026-08-06**: Added **§11 Table Header Theme — Navy + Callout Column + Sticky Header** and its CSS in `reports.css` §18 (`.rpt-table-navy`, `.rpt-col-callout-hd`, `.rpt-col-callout`) — an opt-in alternative to the default green `.rpt-table` header, codifying the navy/indigo header + orange "Prev C/F" callout column already used ad-hoc/inline in `DueFeesReport.php`, as class-based (not `nth-child`) CSS so it transfers cleanly to a different report's column layout. `.rpt-table-navy` is a color-only modifier meant to be combined with the base `.rpt-table` class, which already makes the header sticky (`position: sticky`) — so this theme gets a pinned header for free with no extra CSS. `DueFeesReport.php` itself was **not** migrated to these new classes; they're net-new shared CSS for applying to other reports going forward. Also corrected two stale color descriptions in the §6 CSS Class Reference table (`.rpt-table`/`.rpt-sticky-left-hd` were documented as "dark navy" but the actual `reports.css` values are green `#4CAF50`/`#2e7d32` — likely drift from an earlier design iteration).
- **2026-08-06**: First real-world use of the §11/§18 navy theme — applied to **Fees Report** (`FeesReport.php`) at the user's request, right after the theme was documented. This also finally migrated the table that the 2026-08-05 entry above had explicitly left as-is: removed the old per-file `<style>` block (`#myTable`/`.table-container`/`.table-responsive`, green header) entirely, and replaced `<table class="table table-bordered" id="myTable">` with `<table class="rpt-table rpt-table-navy" id="myTable">` plus `<div class="rpt-table-wrap" style="max-height:400px; overflow-y:auto;">` for the wrapper (kept the deliberate 400px vertical-scroll box as a small inline override on top of the shared class, since `.rpt-table-wrap` itself only handles horizontal overflow and this report intentionally caps height rather than letting the page grow — that specific behavior isn't in the shared class and wasn't worth inventing a whole new CSS variant for one report). No `.rpt-col-callout` column was added — Fees Report has no carry-forward/overdue-style column that fits that treatment; skipped rather than forcing one on. Verified the `data-col`/`data-col-group` column-selector feature (§17, added 2026-08-05) and the Excel export still work unaffected by the header/wrapper class swap.
