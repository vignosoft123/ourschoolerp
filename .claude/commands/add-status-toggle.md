---
description: Add active/inactive status toggle column to any module. Usage: /add-status-toggle <module_url> (e.g. /add-status-toggle banks)
---

Add an active/inactive status toggle to the module with URL path: **$ARGUMENTS**

Follow every step below in order. Do not skip any step.

## Step 1 — Locate files

From the module URL `$ARGUMENTS`, find these files:
- Controller: `mvc/controllers/` — filename is the URL path with first letter uppercased (e.g. `banks` → `Banks.php`)
- Model: `mvc/models/` — typically `$ARGUMENTS_m.php`
- Index view: `mvc/views/$ARGUMENTS/index.php`

Read all three files fully before making any changes.

## Step 2 — Identify the DB table

From the model file, read the `$_table_name` property and the `$_primary_key` property. These are required for the migration and toggle logic.

## Step 3 — Add migration to BOTH schema files

Every DB change must be added to **two files** — always update both together.

### 3a — Add to `mvc/migrations/schema_updates.json` (before the closing `]`):

```json
{
    "query": "ALTER TABLE `{table_name}` ADD `active_status` TINYINT(1) NOT NULL DEFAULT '1'",
    "type": "alter",
    "check_column": { "table": "{table_name}", "column": "active_status" }
}
```

### 3b — Add to `mvc/migrations/schema_updates.sql` (at the end of the ALTER TABLE columns section):

```sql
ALTER TABLE `{table_name}` ADD COLUMN IF NOT EXISTS `active_status` TINYINT(1) NOT NULL DEFAULT '1';
```

Replace `{table_name}` with the actual table name from Step 2.

> **Why two files?** `schema_updates.json` is run automatically by the app's migration runner on every page load. `schema_updates.sql` is a standalone SQL file for manual import via phpMyAdmin on production/staging servers. Both must stay in sync.

## Step 4 — Update the Controller

### 4a — Remove the `delete()` method entirely from the controller.

### 4b — Add the `toggle_status()` method. Insert it where `delete()` was:

```php
public function toggle_status() {
    header('Content-Type: application/json');
    $id = htmlentities(escapeString($this->uri->segment(3)));
    if ((int)$id) {
        $row = $this->{model_name}->get_single_{module}(array('{primary_key}' => $id));
        if ($row) {
            $new_status = ($row->active_status == 1) ? 0 : 1;
            $this->{model_name}->update_{module}(array('active_status' => $new_status), $id);
            echo json_encode(array('success' => true, 'active_status' => $new_status));
            return;
        }
    }
    echo json_encode(array('success' => false));
}
```

Substitute the correct model property name, get_single method name, update method name, and primary key from reading the actual controller and model. If the model does not have a `get_single_*` method, use a direct `$this->db` query to fetch the row by primary key.

## Step 5 — Update the Index View

### 5a — Add the CSS toggle styles block

Add this `<style>` block at the very top of the view file, before the first `<div>`:

```html
<style>
.ft-toggle-switch {
    display: inline-flex;
    align-items: center;
    width: 58px;
    height: 28px;
    border-radius: 14px;
    position: relative;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.3s;
    padding: 0 6px;
}
.ft-toggle-on  { background: #4cd964; justify-content: flex-end; }
.ft-toggle-off { background: #b0b0b0; justify-content: flex-start; }
.ft-toggle-knob {
    position: absolute;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    transition: left 0.3s;
    top: 3px;
}
.ft-toggle-on  .ft-toggle-knob { right: 3px; left: auto; }
.ft-toggle-off .ft-toggle-knob { left: 3px; }
.ft-toggle-label {
    font-size: 11px;
    font-weight: 700;
    color: #fff;
    line-height: 1;
    user-select: none;
}
.ft-toggle-on  .ft-toggle-label { margin-right: 26px; }
.ft-toggle-off .ft-toggle-label { margin-left: 26px; }
</style>
```

### 5b — Add "Status" `<th>` header

In the `<thead>` row, add `<th class="col-sm-2">Status</th>` after the last data column header and before the Action `<th>`.

### 5c — Add the toggle `<td>` in each row

In the `<tbody>` foreach loop, add this `<td>` after the last data column cell and before the action `<td>`:

```php
<td data-title="Status">
    <span class="ft-toggle-switch <?=(isset($row->active_status) && $row->active_status == 1) ? 'ft-toggle-on' : 'ft-toggle-off'?>" data-id="<?=$row->{primary_key}?>" title="Click to toggle status">
        <span class="ft-toggle-knob"></span>
        <span class="ft-toggle-label"><?=(isset($row->active_status) && $row->active_status == 1) ? 'ON' : 'OFF'?></span>
    </span>
</td>
```

Replace `$row` with the actual foreach loop variable and `{primary_key}` with the actual primary key column name.

### 5d — Remove the delete button

Remove the `btn_delete(...)` call from the action `<td>`. Update the permission check on the action column from `permissionChecker('{module}_edit') || permissionChecker('{module}_delete')` to just `permissionChecker('{module}_edit')`.

### 5e — Load SweetAlert 2 (if not already loaded globally)

Check `mvc/views/components/page_footer.php` and `page_header.php` for an existing SweetAlert 2 script tag. If not found, add these two lines at the **top of the view file** (after the opening `<?php` block):

```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
```

### 5f — Add the AJAX script with SweetAlert confirmation

Append this block at the very bottom of the view file, after the closing `</div>` of the box:

```html
<script>
$(document).on('click', '.ft-toggle-switch', function () {
    var $toggle   = $(this);
    var id        = $toggle.data('id');
    var isOn      = $toggle.hasClass('ft-toggle-on');
    var actionLabel = isOn ? 'Deactivate' : 'Activate';
    var btnColor    = isOn ? '#e53935'    : '#0cc035';

    Swal.fire({
        title: actionLabel + '?',
        text: 'Are you sure you want to ' + actionLabel.toLowerCase() + ' this record?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, ' + actionLabel + '!',
        cancelButtonText: 'Cancel'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $toggle.css('opacity', '0.6').css('pointer-events', 'none');

        $.ajax({
            url: '<?=base_url("$ARGUMENTS/toggle_status")?>' + '/' + id,
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    if (res.active_status == 1) {
                        $toggle.removeClass('ft-toggle-off').addClass('ft-toggle-on');
                        $toggle.find('.ft-toggle-label').text('ON');
                    } else {
                        $toggle.removeClass('ft-toggle-on').addClass('ft-toggle-off');
                        $toggle.find('.ft-toggle-label').text('OFF');
                    }
                    toastr.success('Status updated successfully.');
                } else {
                    toastr.error('Failed to update status. Please try again.');
                }
            },
            error: function () {
                toastr.error('Request failed. Please try again.');
            },
            complete: function () {
                $toggle.css('opacity', '1').css('pointer-events', 'auto');
            }
        });
    });
});
</script>
```

> **Key rule for modules that use a real checkbox toggle (like the Student module's `onoffswitch-small`):**
> Use the `change` event — NOT `click` — because the user clicks the visible **label**, not the checkbox itself.
> Read the new state after toggle, immediately revert with `.prop('checked', prevState)` (`.prop()` does NOT re-fire `change`, so no infinite loop), show SweetAlert, then re-apply on confirm.
>
> ```javascript
> $(document).on('change', '.onoffswitch-small-checkbox', function () {
>     var checkbox  = $(this);
>     var isNowOn   = checkbox.prop('checked');   // state AFTER browser toggled
>     var prevState = !isNowOn;
>     checkbox.prop('checked', prevState);         // revert immediately — awaiting confirm
>
>     Swal.fire({ ... }).then(function (result) {
>         if (!result.isConfirmed) return;
>         checkbox.prop('checked', isNowOn);       // apply on confirm
>         $.ajax({ ... });                         // fire to student/active
>     });
> });
> ```
> Reference implementation: `mvc/views/student/index.php` (added 2026-05-24).

## Step 6 — Confirm

After all edits are complete, report:
- The DB table name and column added
- The controller method added / removed
- The view file updated

Do not ask the user to run any SQL manually. The migration in `schema_updates.json` handles it automatically.
