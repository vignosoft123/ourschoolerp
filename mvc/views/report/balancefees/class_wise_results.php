<table border='1' cellpadding='5' class="table table-bordered" id="myTable">
    <thead>
        <tr>
            <th>Classname</th>
            <th>Total Fee</th>
            <th>Total Paid</th>
            <th>Total Discount</th>
            <th>Total Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $grandTotalFee = 0;
        $grandTotalPaid = 0;
        $grandTotalDiscount = 0;
        $grandTotalBalance = 0;
        
        if(!empty($result)){
            foreach($result as $row) { 
                echo "<tr>
                    <td style='background-color: #e3f2fd;'><b><i>{$row->Classname}</i></b></td>
                    <td style='background-color: #c8e6c9;'>" . number_format($row->TotalFee, 2) . "</td>
                    <td style='background-color: #ffe0b2;'>" . number_format($row->TotalPaid, 2) . "</td>
                    <td style='background-color: #f8bbd0;'>" . number_format($row->TotalDiscount, 2) . "</td>
                    <td style='background-color: #d1c4e9;'>" . number_format($row->TotalBalance, 2) . "</td>
                </tr>";

                // Accumulate grand totals
                $grandTotalFee += $row->TotalFee;
                $grandTotalPaid += $row->TotalPaid;
                $grandTotalDiscount += $row->TotalDiscount;
                $grandTotalBalance += $row->TotalBalance;
            }

            // Display grand total row
            echo "<tr style='font-weight:bold;'>
                <td style='background-color: #bbdefb;'><b><i>Grand Total</i></b></td>
                <td style='background-color: #a5d6a7;'>" . number_format($grandTotalFee, 2) . "</td>
                <td style='background-color: #ffcc80;'>" . number_format($grandTotalPaid, 2) . "</td>
                <td style='background-color: #f48fb1;'>" . number_format($grandTotalDiscount, 2) . "</td>
                <td style='background-color: #b39ddb;'>" . number_format($grandTotalBalance, 2) . "</td>
            </tr>";
        } else {
            echo "<tr style='font-weight:bold;'>
                <td colspan='5'>No Records Found</td>
            </tr>";
        } 
        ?>
    </tbody>
</table>