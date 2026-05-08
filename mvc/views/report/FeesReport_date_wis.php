


<style>
    .grand-total-col {
        background-color: #e6ffe6;
        font-weight: bold;
    }
    tfoot .grand-total-col {
        background-color: #b3ffb3;
    }
</style>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <th>Cash Amount</th>
            <th>Digital Amount</th>
            <th>Cheque Amount</th>
            <th>Others Amount</th>
            <th class="grand-total-col">Grand Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $total_cash    = 0;
            $total_digital = 0;
            $total_cheque  = 0;
            $total_others  = 0;
            $grand_total   = 0;

            foreach ($getFeesReports as $report) {
                $others_amt = isset($report->others_amount) ? (float)$report->others_amount : 0;
                $total_cash    += $report->cash_amount;
                $total_digital += $report->digital_amount;
                $total_cheque  += $report->cheque_amount;
                $total_others  += $others_amt;
                $row_total = $report->cash_amount + $report->digital_amount + $report->cheque_amount + $others_amt;
                $grand_total += $row_total;
        ?>
            <tr>
                <td><?= date('d M Y', strtotime($report->paymentdate)) ?></td>
                <td><?= number_format($report->cash_amount, 2) ?></td>
                <td><?= number_format($report->digital_amount, 2) ?></td>
                <td><?= number_format($report->cheque_amount, 2) ?></td>
                <td>
                    <?= number_format($others_amt, 2) ?>
                    <?php if ($others_amt > 0): ?>
                    <i class="fa fa-info-circle others-detail-icon"
                       data-date="<?= $report->paymentdate ?>"
                       style="cursor:pointer; color:#e67e22; margin-left:4px;"
                       title="View breakdown"></i>
                    <?php endif; ?>
                </td>
                <td class="grand-total-col"><?= number_format($row_total, 2) ?></td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #fff8dc; font-weight: bold;">
            <th>Total</th>
            <th><?= number_format($total_cash, 2) ?></th>
            <th><?= number_format($total_digital, 2) ?></th>
            <th><?= number_format($total_cheque, 2) ?></th>
            <th><?= number_format($total_others, 2) ?></th>
            <th class="grand-total-col"><?= number_format($grand_total, 2) ?></th>
        </tr>
    </tfoot>
</table>

<!-- Others Detail Popup Modal -->
<div class="modal fade" id="othersDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:480px;">
        <div class="modal-content">
            <div class="modal-header" style="background:#e67e22; color:#fff; border-radius:4px 4px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> Others Payment Breakdown — <span id="othersDetailDate"></span></h4>
            </div>
            <div class="modal-body" id="othersDetailBody">
                <div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.others-detail-icon', function () {
    var date = $(this).data('date');
    $('#othersDetailDate').text(date ? new Date(date).toDateString() : date);
    $('#othersDetailBody').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x" style="color:#e67e22;"></i></div>');
    $('#othersDetailModal').modal('show');

    $.ajax({
        type: 'POST',
        url: '<?= base_url('feesreport/getOthersDetailByDate') ?>',
        data: { date: date },
        dataType: 'html',
        success: function (data) {
            var r = JSON.parse(data);
            if (!r.status || !r.rows || r.rows.length === 0) {
                $('#othersDetailBody').html('<p class="text-muted text-center">No data found.</p>');
                return;
            }
            var html = '<table class="table table-bordered table-condensed" style="margin-bottom:0;">';
            html += '<thead><tr style="background:#f1f5f9;"><th>Bank / Detail</th><th class="text-right">Amount (₹)</th></tr></thead><tbody>';
            var total = 0;
            $.each(r.rows, function (i, row) {
                total += parseFloat(row.total) || 0;
                html += '<tr><td>' + (row.payment_other_details || '—') + '</td><td class="text-right">' + parseFloat(row.total).toFixed(2) + '</td></tr>';
            });
            html += '</tbody><tfoot><tr style="background:#fef9e7; font-weight:700;"><td>Total</td><td class="text-right">' + total.toFixed(2) + '</td></tr></tfoot></table>';
            $('#othersDetailBody').html(html);
        },
        error: function () {
            $('#othersDetailBody').html('<p class="text-danger">Failed to load details.</p>');
        }
    });
});
</script>


