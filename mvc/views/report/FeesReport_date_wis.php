


<style>
    .grand-total-col {
        background-color: #e6ffe6; /* light green */
        font-weight: bold;
    }
    tfoot .grand-total-col {
        background-color: #b3ffb3; /* darker green for total */
    }
</style>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <th>Cash Amount</th>
            <th>Digital Amount</th>
            <th>Cheque Amount</th>
            <th class="grand-total-col">Grand Total</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $total_cash = 0;
            $total_digital = 0;
            $total_cheque = 0;
            $grand_total = 0;

            foreach($getFeesReports as $report) { 
                $total_cash += $report->cash_amount;
                $total_digital += $report->digital_amount;
                $total_cheque += $report->cheque_amount;
                $row_total = $report->cash_amount + $report->digital_amount + $report->cheque_amount;
                $grand_total += $row_total;
        ?>
            <tr>
                <td><?= date('d M Y', strtotime($report->paymentdate)) ?></td>
                <td><?= number_format($report->cash_amount, 2) ?></td>
                <td><?= number_format($report->digital_amount, 2) ?></td>
                <td><?= number_format($report->cheque_amount, 2) ?></td>
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
            <th class="grand-total-col"><?= number_format($grand_total, 2) ?></th>
        </tr>
    </tfoot>
</table>


 

