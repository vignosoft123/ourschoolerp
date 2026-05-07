# OurSchoolERP — UI Design System Notes
## Global Payment (New) Page — Design Reference

---

## 1. Universal Card System

All sections use a consistent `.gp-card` class:

```css
.gp-card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.07);
    margin-bottom: 22px;
    overflow: hidden;
    background: #fff;
}
```

### Card Header Rule — LIGHT backgrounds, NOT dark/solid colours
```css
/* Blue  */ background: #eaf1fb;  color: #1d4e9e;  border-bottom: 2px solid #c5d9f5;
/* Green */ background: #e8f5ed;  color: #1a6b3e;  border-bottom: 2px solid #b8dfc8;
/* Orange*/ background: #fef3e4;  color: #8a4a10;  border-bottom: 2px solid #f5d49a;
/* Slate */ background: #f1f4f8;  color: #374151;  border-bottom: 2px solid #d1d9e6;
```

**Rule:** Never use dark/saturated gradients for card headers. Always use the light tinted versions above.

---

## 2. Colour Palette (Professional)

| Use | Hex | Description |
|-----|-----|-------------|
| Primary Blue | `#3a7bd5` | Buttons, invoice numbers, links |
| Dark Blue | `#1d4e9e` | Receipt header, heading emphasis |
| Primary Green | `#2e8b57` | Success actions, paid status, submit button |
| Dark Green | `#1a6b3e` | Card header text (green cards) |
| Amber/Orange | `#c97a2a` | Previous year balance header |
| Dark Orange | `#8a4a10` | Orange card header text |
| Body Text | `#1a202c` | Primary text |
| Muted Text | `#718096` | Labels, secondary text |
| Border | `#e2e8f0` | All card/table borders |
| Light BG | `#f8fafc` | Form grids, submit bars, tfoot rows |
| Due/Alert | `#c05621` | Due amounts (orange-red) |

---

## 3. Page Layout — Global Payment (New)

### Structure (top to bottom):
```
[Page title + breadcrumb bar]
[Search Card]          ← only shown on /global_payment/new (no student selected)
[Card 1] Student Details
[Card 2] Payments & Invoice Entry   ← has Payment History button (top-right corner)
[Card 3] Previous Year Balance Statement  ← only shown if carry-forward due > 0
```

---

## 4. Card 1 — Student Details

- **Photo:** circular, 68×68px, blue ring (`border: 3px solid #3a7bd5`)
- **Fields:** horizontal pill badges (`.gp-field-pill`)
  - Each pill: `background:#f7faff; border:1px solid #dde8f8; border-radius:20px`
  - Label (`NAME`, `CLASS`, etc.) in small uppercase gray + separator `|` + bold value
- **No vertical table** — everything in a flex-wrap row

```
[Photo] [NAME | K Ishitha] [CLASS | 10TH CLASS] [SECTION | A] [ROLL | 6] [REG NO | ...] [FATHER | ...] [PHONE | ...]
```

---

## 5. Card 2 — Payments & Invoice Entry

### Header:
- Green light header (`#e8f5ed`)
- "Payment History" button aligned `margin-left:auto` (right side of header)
  - Button: `background:#1a6b3e; color:#fff; border-radius:6px`
  - Clicking opens `#payHistModal` popup

### Section Order (inside card):
1. **"New Payment Entry"** sub-label (blue)
2. **Invoice form grid** (8 fields, 2 rows of 4):
   - Row 1: Invoice Name | Manual Receipt | Invoice Number | Year
   - Row 2: Payment Status | Payment Type | Payment Date | Send WhatsApp checkbox
3. **Fee entry table** (# | checkbox | Fees Name | Fees Amount | Due | Paid Amount | Discount)
   - NO Fine column in this table
   - Due amount: red `#c05621` if positive, green `#276749` if paid
4. **Table footer:** Total row + Remaining Due row + Total Collection row
5. **Submit button** — right-aligned, `background:#2e8b57`

### Payment History Modal (`#payHistModal`):
- Popup width: 92%, max-width: 1000px
- Light green header (`#e8f5ed`)
- Columns: Invoice Number | Fee Type | Total Pay | Discount | Total Collection | Clearance | Payment Date | Action
- **NO Fine column**
- Same invoice grouped with `rowspan` — one Print button per `globalpaymentID`
- Multiple fee types for same invoice shown as separate rows, Invoice Number cell spans them all

### Invoice form grid style:
```css
.gp-inv-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 10px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px 16px;
}
/* Labels: font-size:10px; font-weight:700; color:#718096; text-transform:uppercase */
```

---

## 6. Card 3 — Previous Year Balance Statement

- Orange light header (`#fef3e4`)
- Summary table columns: Year | Total Fees | Paid (+ ⓘ info button) | Waiver | Balance Due | Action
- "Pay Now" button (amber) per year → opens **Pay Now Modal** popup
- ⓘ button on Paid amount → opens **Payment History Modal** (per year)

### Pay Now Modal (`#cfPayNowModal`):
- Width: 90%, max-width: 820px
- Header: `background:#c97a2a; color:#fff`
- Form grid (above the table):
  - Row 1: Invoice Name | Invoice Number | Year | Payment Status
  - Row 2: Payment Type | Payment Date | (empty)
- Fee table: Fees Name | Amount | Due | Paid Amount | Waiver — **NO Fine column**
- Submit button: amber (`#c97a2a`), right-aligned

### Payment History Modal (`#cfPaidDetailModal`):
- Width: 92%, max-width: 1050px
- Columns: Invoice No. | Fee Type | Total Pay | Discount | Total Collection | Clearance | Payment Date | Receipt
- Grouped by `globalpaymentID` with rowspan — one Print Receipt button per group
- **NO Fine column**

---

## 7. Table Design Rules

```css
/* Table headers — light gray, never dark */
.gp-table thead th {
    background: #f1f5f9;
    color: #374151;
    font-size: 12px;
    font-weight: 700;
    padding: 9px 10px;
    border: 1px solid #e2e8f0 !important;
}
/* Row hover */
.gp-table tbody tr:hover { background: #fafbff; }
/* Tfoot */
.gp-table tfoot td { background: #f8fafc; font-weight: 700; }
/* Body cells */
.gp-table tbody td { border: 1px solid #e9eef5 !important; vertical-align: middle !important; }
```

---

## 8. Status Badge Styles

```css
.label-paid    { background:#c6f6d5; color:#276749; border:1px solid #9ae6b4; border-radius:4px; padding:2px 8px; font-size:11px; font-weight:700; }
.label-partial { background:#fef3c7; color:#92400e; border:1px solid #fcd34d; border-radius:4px; padding:2px 8px; font-size:11px; font-weight:700; }
.label-unpaid  { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; border-radius:4px; padding:2px 8px; font-size:11px; font-weight:700; }
```

---

## 9. Print Receipt Design (invoice.php)

**Current state:** Uses original design (NOT redesigned).
- The redesigned version was rejected by the user — keep original `common_views/invoice.php`.
- Only the `back-to-payment-page` button and `receipt_back_url` / `receipt_year_name` / `is_prev_year_receipt` variables were added to the original design.

---

## 10. Key UX Decisions

| Decision | Rule |
|----------|------|
| Card headers | Light tinted backgrounds only — no dark/gradient |
| Fine column | Removed from New Payment Entry table and all modals |
| Payment controls position | Above the fee table (inside form grid), NOT below |
| Payment History | Popup modal (button in card header top-right), NOT inline |
| Multiple fee types per invoice | Grouped with rowspan in all tables |
| Previous year pay | Popup modal only, no collapsible inline rows |
| Section order in Card 2 | New Payment Entry first, then Payment History (modal) |
