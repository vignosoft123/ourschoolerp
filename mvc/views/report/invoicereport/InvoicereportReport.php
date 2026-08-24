<style>
#invoice-report-wrap {
    overflow-x: auto;
    margin-top: 10px;
}
#invoice-pivot-table {
    border-collapse: collapse;
    min-width: 100%;
    font-size: 13px;
    white-space: nowrap;
}
#invoice-pivot-table th,
#invoice-pivot-table td {
    border: 1px solid #dee2e6;
    padding: 6px 10px;
    text-align: center;
    vertical-align: middle;
}

/* Sticky left columns — each needs an explicit left offset */
#invoice-pivot-table .col-fixed {
    position: sticky;
    background: #fff;
    z-index: 2;
    text-align: left;
}
#invoice-pivot-table thead th.col-fixed { z-index: 3; }

/* Individual sticky offsets */
#invoice-pivot-table .col-sno     { left: 0;     min-width: 36px; width: 36px; text-align: center; }
#invoice-pivot-table .col-name    { left: 36px;  min-width: 180px; }
#invoice-pivot-table .col-class   { left: 216px; min-width: 100px; }
#invoice-pivot-table .col-section { left: 316px; min-width: 70px;
    /* right border acts as visual separator from scrollable columns */
    border-right: 2px solid #adb5bd !important;
}

/* Header rows */
#invoice-pivot-table thead tr:first-child th {
    background: #007bff;
    color: #fff;
    font-weight: 700;
}
#invoice-pivot-table thead tr:nth-child(2) th {
    background: #e8f1ff;
    color: #333;
    font-weight: 600;
}

/* Grand total header */
#invoice-pivot-table th.col-grand     { background: #17a2b8; color: #fff; }
#invoice-pivot-table th.col-grand-sub { background: #d1ecf1; color: #0c5460; font-weight: 600; }

/* Zebra rows — sticky cells must repeat the background or they show through */
#invoice-pivot-table tbody tr:nth-child(even) td { background: #f9f9f9; }
#invoice-pivot-table tbody tr:nth-child(even) td.col-fixed,
#invoice-pivot-table tbody tr:nth-child(even) td.col-sno,
#invoice-pivot-table tbody tr:nth-child(even) td.col-name,
#invoice-pivot-table tbody tr:nth-child(even) td.col-class,
#invoice-pivot-table tbody tr:nth-child(even) td.col-section { background: #f9f9f9; }

#invoice-pivot-table tbody tr:hover td { background: #e9f3ff; }
#invoice-pivot-table tbody tr:hover td.col-fixed,
#invoice-pivot-table tbody tr:hover td.col-sno,
#invoice-pivot-table tbody tr:hover td.col-name,
#invoice-pivot-table tbody tr:hover td.col-class,
#invoice-pivot-table tbody tr:hover td.col-section { background: #e9f3ff; }

/* Footer */
#invoice-pivot-table tfoot td { background: #343a40; color: #fff; font-weight: 700; }
#invoice-pivot-table tfoot td.col-fixed,
#invoice-pivot-table tfoot td.col-sno { background: #343a40; color: #fff; }

.inv-discount  { color: #6c757d; }
.inv-paid      { color: #28a745; font-weight: 600; }
.inv-balance   { color: #dc3545; font-weight: 600; }
/* Tooltip cursor hint on Grand Total Amount */
#invoice-pivot-table td.inv-gt-amount { cursor: help; }

@media print {
    #invoice-report-wrap { overflow: visible; }
    #invoice-pivot-table .col-fixed { position: static; }
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>

<?php if (customCompute($students) && customCompute($feetypesList)): ?>

<div class="box" style="margin-top:15px;">
    <div class="box-body">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
            <div style="width:160px;"></div>
            <div style="text-align:center; flex:1;">
                <strong style="font-size:16px;"><?= isset($siteinfos->sname) ? $siteinfos->sname : '' ?></strong><br/>
                <span style="font-size:13px; color:#555;">Invoice Report — Academic Year <?= isset($schoolyearsessionobj->schoolyear) ? $schoolyearsessionobj->schoolyear : '' ?></span>
            </div>
            <div class="rpt-action-bar" style="width:auto; text-align:right;">
                <button id="invoice-excel-btn" class="btn btn-success rpt-action-btn btn-sm">
                    <i class="fa fa-file-excel-o"></i> Download Excel
                </button>

                <div class="btn-group rpt-col-selector-group" id="columnSelectorGroup">
                    <button type="button" class="btn btn-info rpt-action-btn btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
        </div>

        <div id="invoice-report-wrap">
            <table id="invoice-pivot-table">
                <thead>
                    <!-- Row 1 -->
                    <tr>
                        <th class="col-fixed col-sno" data-col="sno" rowspan="2">#</th>
                        <th class="col-fixed col-name" data-col="name" rowspan="2">Student</th>
                        <th class="col-fixed col-class" data-col="class" rowspan="2">Class</th>
                        <th class="col-fixed col-section" data-col="section" rowspan="2">Section</th>
                        <th class="col-fixed" data-col="phone" rowspan="2">Phone</th>
                        <?php foreach ($feetypesList as $fid => $fname): ?>
                            <th colspan="2" data-col-group="ft_<?= $fid ?>" data-group-cols="amt_<?= $fid ?>,disc_<?= $fid ?>"><?= htmlspecialchars($fname) ?></th>
                        <?php endforeach; ?>
                        <th colspan="5" class="col-grand" data-col-group="grand" data-group-cols="gt_amount,gt_discount,gt_paid,gt_net,gt_balance">Grand Total</th>
                    </tr>
                    <!-- Row 2 sub-headers -->
                    <tr>
                        <?php foreach ($feetypesList as $fid => $fname): ?>
                            <th data-col="amt_<?= $fid ?>" data-label="<?= htmlspecialchars($fname) ?> Amount">Amount</th>
                            <th data-col="disc_<?= $fid ?>" data-label="<?= htmlspecialchars($fname) ?> Discount">Discount</th>
                        <?php endforeach; ?>
                        <th class="col-grand-sub" data-col="gt_amount">Amount</th>
                        <th class="col-grand-sub" data-col="gt_discount">Discount</th>
                        <th class="col-grand-sub" data-col="gt_paid">Paid</th>
                        <th class="col-grand-sub" data-col="gt_net">Net</th>
                        <th class="col-grand-sub" data-col="gt_balance">Balance</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                $colTotals   = [];
                foreach ($feetypesList as $fid => $fname) {
                    $colTotals[$fid] = ['amount' => 0, 'discount' => 0, 'paid' => 0];
                }
                $grandAmount   = 0;
                $grandDiscount = 0;
                $grandPaid     = 0;
                $grandBalance  = 0;

                foreach ($students as $sid => $student):
                    $rowAmount   = 0;
                    $rowDiscount = 0;
                    $rowPaid     = 0;
                    $ttAmount    = [];  // tooltip lines per grand total cell
                    $ttDiscount  = [];
                    $ttPaid      = [];
                    $ttNet       = [];
                    $ttBalance   = [];
                ?>
                    <tr>
                        <td class="col-fixed col-sno" data-col="sno"><?= $i++ ?></td>
                        <td class="col-fixed col-name" data-col="name"><strong><?= htmlspecialchars($student->srname) ?></strong></td>
                        <td class="col-fixed col-class" data-col="class"><?= isset($classes[$student->srclassesID])  ? htmlspecialchars($classes[$student->srclassesID])  : '' ?></td>
                        <td class="col-fixed col-section" data-col="section"><?= isset($sections[$student->srsectionID]) ? htmlspecialchars($sections[$student->srsectionID]) : '' ?></td>
                        <td class="col-fixed" data-col="phone"><?= htmlspecialchars($student->phone ?? '') ?></td>

                        <?php foreach ($feetypesList as $fid => $fname):
                            if (isset($pivot[$sid][$fid])) {
                                $amount   = $pivot[$sid][$fid]['amount'];
                                $discount = $pivot[$sid][$fid]['discount'];
                                $paid     = $pivot[$sid][$fid]['paid'];
                                $net      = $amount - $discount;
                                $balance  = $net - $paid;
                                $ttAmount[]   = $fname . ': ' . number_format($amount,   2);
                                if ($discount > 0) $ttDiscount[] = $fname . ': ' . number_format($discount, 2);
                                if ($paid    > 0) $ttPaid[]     = $fname . ': ' . number_format($paid,     2);
                                $ttNet[]      = $fname . ': ' . number_format($net,      2);
                                $ttBalance[]  = $fname . ': ' . number_format($balance,  2);
                            } else {
                                $amount = $discount = $paid = null;
                            }
                            $rowAmount   += (float)$amount;
                            $rowDiscount += (float)$discount;
                            $rowPaid     += (float)$paid;
                            $colTotals[$fid]['amount']   += (float)$amount;
                            $colTotals[$fid]['discount'] += (float)$discount;
                            $colTotals[$fid]['paid']     += (float)$paid;
                        ?>
                            <td data-col="amt_<?= $fid ?>"><?= $amount !== null ? number_format($amount, 2) : '&mdash;' ?></td>
                            <td class="inv-discount" data-col="disc_<?= $fid ?>"><?= ($discount !== null && $discount > 0) ? number_format($discount, 2) : '&mdash;' ?></td>
                        <?php endforeach; ?>

                        <?php
                            $rowNet        = $rowAmount - $rowDiscount;
                            $rowBalance    = $rowNet - $rowPaid;
                            $grandAmount   += $rowAmount;
                            $grandDiscount += $rowDiscount;
                            $grandPaid     += $rowPaid;
                            $grandBalance  += $rowBalance;
                        ?>
                        <td class="inv-gt-amount" data-col="gt_amount" data-toggle="tooltip" data-placement="top"
                            title="<?= implode('&#10;', $ttAmount) ?>">
                            <strong><?= number_format($rowAmount, 2) ?></strong>
                        </td>
                        <td class="inv-discount inv-gt-amount" data-col="gt_discount" data-toggle="tooltip" data-placement="top"
                            title="<?= count($ttDiscount) ? implode('&#10;', $ttDiscount) : 'No discount' ?>">
                            <strong><?= $rowDiscount > 0 ? number_format($rowDiscount, 2) : '&mdash;' ?></strong>
                        </td>
                        <td class="inv-paid inv-gt-amount" data-col="gt_paid" data-toggle="tooltip" data-placement="top"
                            title="<?= count($ttPaid) ? implode('&#10;', $ttPaid) : 'No payments' ?>">
                            <strong><?= $rowPaid > 0 ? number_format($rowPaid, 2) : '&mdash;' ?></strong>
                        </td>
                        <td class="inv-gt-amount" data-col="gt_net" data-toggle="tooltip" data-placement="top"
                            title="<?= implode('&#10;', $ttNet) ?>">
                            <strong><?= number_format($rowNet, 2) ?></strong>
                        </td>
                        <td class="inv-balance inv-gt-amount" data-col="gt_balance" data-toggle="tooltip" data-placement="top"
                            title="<?= count($ttBalance) ? implode('&#10;', $ttBalance) : 'No balance' ?>">
                            <strong><?= number_format($rowBalance, 2) ?></strong>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td class="col-fixed col-sno" data-col="sno"></td>
                        <td class="col-fixed" data-col-group="footer_label" data-group-cols="name,class,section,phone" colspan="4" style="position:sticky; left:36px; text-align:right; border-right:2px solid #adb5bd;">TOTAL</td>
                        <?php foreach ($feetypesList as $fid => $fname): ?>
                            <td data-col="amt_<?= $fid ?>"><?= number_format($colTotals[$fid]['amount'],   2) ?></td>
                            <td data-col="disc_<?= $fid ?>"><?= number_format($colTotals[$fid]['discount'], 2) ?></td>
                        <?php endforeach; ?>
                        <td data-col="gt_amount"><?= number_format($grandAmount,                    2) ?></td>
                        <td data-col="gt_discount"><?= number_format($grandDiscount,                  2) ?></td>
                        <td data-col="gt_paid"><?= number_format($grandPaid,                      2) ?></td>
                        <td data-col="gt_net"><?= number_format($grandAmount - $grandDiscount,   2) ?></td>
                        <td data-col="gt_balance"><?= number_format($grandBalance,                   2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>

<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip({ container: 'body', trigger: 'hover' });

    $('#invoice-excel-btn').on('click', function () {
        var wb = XLSX.utils.book_new();

        // Clone table, strip tooltip attributes so they don't appear in Excel
        var tbl = document.getElementById('invoice-pivot-table').cloneNode(true);
        $(tbl).find('[data-toggle]').removeAttr('data-toggle data-placement title');
        // Replace &mdash; cells with empty string
        $(tbl).find('td, th').each(function () {
            if ($(this).html() === '&mdash;' || $(this).text().trim() === '—') {
                $(this).text('');
            }
        });

        var ws = XLSX.utils.table_to_sheet(tbl, { raw: false, display: true });
        XLSX.utils.book_append_sheet(wb, ws, 'Invoice Report');

        var className   = $('#classesID option:selected').text().trim();
        var sectionName = $('#sectionID option:selected').text().trim();
        var today   = new Date();
        var dateStr = today.getFullYear() + '-' +
                      String(today.getMonth() + 1).padStart(2, '0') + '-' +
                      String(today.getDate()).padStart(2, '0');
        var parts = ['Invoice Report'];
        if (className && className !== 'Select Class')   parts.push(className);
        if (sectionName && sectionName !== 'Select Section') parts.push(sectionName);
        parts.push(dateStr);
        XLSX.writeFile(wb, parts.join(' - ') + '.xlsx');
    });
});
</script>

<script>
$(document).ready(function () {
    var $table = $('#invoice-pivot-table');
    if (!$table.length) { return; }

    // Build the "Columns" checkbox list from whatever headers the server actually rendered.
    // (This table's fee-type columns are dynamic per query, so — unlike the static
    // GROUP1_COLS/GROUP2_COLS arrays in the Fees Report reference — group membership is
    // read from each group cell's own data-group-cols attribute instead of a hardcoded list.)
    var $menu = $('#columnSelectorMenu');
    var $list = $('#columnSelectorList');
    $table.find('thead th[data-col]').each(function () {
        var col = $(this).attr('data-col');
        var label = $(this).attr('data-label') || $.trim($(this).text());
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
        $table.find('[data-col-group]').each(function () {
            var $cell = $(this);
            var cols = ($cell.attr('data-group-cols') || '').split(',').filter(Boolean);
            if (!cols.length) { return; }
            var visible = cols.filter(function (col) {
                var $th = $table.find('thead th[data-col="' + col + '"]');
                return $th.length && $th.css('display') !== 'none';
            }).length;
            $cell.css('display', visible ? '' : 'none');
            $cell.attr('colspan', Math.max(visible, 1));
        });
    }

    function toggleColumn(col, visible) {
        $table.find('[data-col="' + col + '"]').css('display', visible ? '' : 'none');
        recomputeGroupColspans();
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

<?php else: ?>
    <div class="alert alert-info" style="margin-top:15px;">
        <i class="fa fa-info-circle"></i> No invoice data found for the selected filters.
    </div>
<?php endif; ?>
