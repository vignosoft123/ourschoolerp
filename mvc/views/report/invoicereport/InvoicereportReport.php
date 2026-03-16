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
/* Tooltip cursor hint on Grand Total Amount */
#invoice-pivot-table td.inv-gt-amount { cursor: help; }

@media print {
    #invoice-report-wrap { overflow: visible; }
    #invoice-pivot-table .col-fixed { position: static; }
    #invoice-print-btn { display: none; }
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>

<?php if (customCompute($students) && customCompute($feetypesList)): ?>

<div class="box" style="margin-top:15px;">
    <div class="box-header" style="background:#f4f6f9;">
        <h3 class="box-title"><i class="fa fa-table"></i> Invoice Report</h3>
        <div class="pull-right">
            <button id="invoice-print-btn" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fa fa-print"></i> Print
            </button>
        </div>
    </div>
    <div class="box-body">

        <div style="text-align:center; margin-bottom:10px;">
            <strong style="font-size:16px;"><?= isset($siteinfos->sname) ? $siteinfos->sname : '' ?></strong><br/>
            <span>Invoice Report — Academic Year <?= isset($schoolyearsessionobj->schoolyear) ? $schoolyearsessionobj->schoolyear : '' ?></span>
        </div>

        <div id="invoice-report-wrap">
            <table id="invoice-pivot-table">
                <thead>
                    <!-- Row 1 -->
                    <tr>
                        <th class="col-fixed col-sno" rowspan="2">#</th>
                        <th class="col-fixed col-name" rowspan="2">Student</th>
                        <th class="col-fixed col-class" rowspan="2">Class</th>
                        <th class="col-fixed col-section" rowspan="2">Section</th>
                        <?php foreach ($feetypesList as $fid => $fname): ?>
                            <th colspan="2"><?= htmlspecialchars($fname) ?></th>
                        <?php endforeach; ?>
                        <th colspan="4" class="col-grand">Grand Total</th>
                    </tr>
                    <!-- Row 2 sub-headers -->
                    <tr>
                        <?php foreach ($feetypesList as $fid => $fname): ?>
                            <th>Amount</th>
                            <th>Discount</th>
                        <?php endforeach; ?>
                        <th class="col-grand-sub">Amount</th>
                        <th class="col-grand-sub">Discount</th>
                        <th class="col-grand-sub">Paid</th>
                        <th class="col-grand-sub">Net</th>
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

                foreach ($students as $sid => $student):
                    $rowAmount   = 0;
                    $rowDiscount = 0;
                    $rowPaid     = 0;
                    $ttAmount    = [];  // tooltip lines per grand total cell
                    $ttDiscount  = [];
                    $ttPaid      = [];
                    $ttNet       = [];
                ?>
                    <tr>
                        <td class="col-fixed col-sno"><?= $i++ ?></td>
                        <td class="col-fixed col-name"><strong><?= htmlspecialchars($student->srname) ?></strong></td>
                        <td class="col-fixed col-class"><?= isset($classes[$student->srclassesID])  ? htmlspecialchars($classes[$student->srclassesID])  : '' ?></td>
                        <td class="col-fixed col-section"><?= isset($sections[$student->srsectionID]) ? htmlspecialchars($sections[$student->srsectionID]) : '' ?></td>

                        <?php foreach ($feetypesList as $fid => $fname):
                            if (isset($pivot[$sid][$fid])) {
                                $amount   = $pivot[$sid][$fid]['amount'];
                                $discount = $pivot[$sid][$fid]['discount'];
                                $paid     = $pivot[$sid][$fid]['paid'];
                                $net      = $amount - $discount;
                                $ttAmount[]   = $fname . ': ' . number_format($amount,   2);
                                if ($discount > 0) $ttDiscount[] = $fname . ': ' . number_format($discount, 2);
                                if ($paid    > 0) $ttPaid[]     = $fname . ': ' . number_format($paid,     2);
                                $ttNet[]      = $fname . ': ' . number_format($net,      2);
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
                            <td><?= $amount !== null ? number_format($amount, 2) : '&mdash;' ?></td>
                            <td class="inv-discount"><?= ($discount !== null && $discount > 0) ? number_format($discount, 2) : '&mdash;' ?></td>
                        <?php endforeach; ?>

                        <?php
                            $rowNet        = $rowAmount - $rowDiscount;
                            $grandAmount   += $rowAmount;
                            $grandDiscount += $rowDiscount;
                            $grandPaid     += $rowPaid;
                        ?>
                        <td class="inv-gt-amount" data-toggle="tooltip" data-placement="top"
                            title="<?= implode('&#10;', $ttAmount) ?>">
                            <strong><?= number_format($rowAmount, 2) ?></strong>
                        </td>
                        <td class="inv-discount inv-gt-amount" data-toggle="tooltip" data-placement="top"
                            title="<?= count($ttDiscount) ? implode('&#10;', $ttDiscount) : 'No discount' ?>">
                            <strong><?= $rowDiscount > 0 ? number_format($rowDiscount, 2) : '&mdash;' ?></strong>
                        </td>
                        <td class="inv-paid inv-gt-amount" data-toggle="tooltip" data-placement="top"
                            title="<?= count($ttPaid) ? implode('&#10;', $ttPaid) : 'No payments' ?>">
                            <strong><?= $rowPaid > 0 ? number_format($rowPaid, 2) : '&mdash;' ?></strong>
                        </td>
                        <td class="inv-gt-amount" data-toggle="tooltip" data-placement="top"
                            title="<?= implode('&#10;', $ttNet) ?>">
                            <strong><?= number_format($rowNet, 2) ?></strong>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td class="col-fixed col-sno"></td>
                        <td class="col-fixed" colspan="3" style="position:sticky; left:36px; text-align:right; border-right:2px solid #adb5bd;">TOTAL</td>
                        <?php foreach ($feetypesList as $fid => $fname): ?>
                            <td><?= number_format($colTotals[$fid]['amount'],   2) ?></td>
                            <td><?= number_format($colTotals[$fid]['discount'], 2) ?></td>
                        <?php endforeach; ?>
                        <td><?= number_format($grandAmount,                    2) ?></td>
                        <td><?= number_format($grandDiscount,                  2) ?></td>
                        <td><?= number_format($grandPaid,                      2) ?></td>
                        <td><?= number_format($grandAmount - $grandDiscount,   2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>

<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip({ container: 'body', trigger: 'hover' });
});
</script>

<?php else: ?>
    <div class="alert alert-info" style="margin-top:15px;">
        <i class="fa fa-info-circle"></i> No invoice data found for the selected filters.
    </div>
<?php endif; ?>
