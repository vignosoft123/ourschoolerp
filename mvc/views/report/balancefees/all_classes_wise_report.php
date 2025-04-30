<?php
// header("Content-Type: application/vnd.ms-excel");
// header("Content-Disposition: attachment; filename=report.xls");
?>

<style>
@media print {
    .no-print, #menuid {
        display: none !important;
    }
}
</style>";



<style>
    /* thead th {
        background: linear-gradient(90deg, #007bff, #3399ff);
        color: white;
        text-align: center;
        font-weight: bold;
        vertical-align: middle;
        padding: 10px;
    } */

    #myTable thead th {
    background-color: #4CAF50; /* Green background */
    
    color: white;               /* White text */
    padding: 10px;              /* Padding inside headers */
    text-align: center;         /* Center the header text */
    font-weight: bold;          /* Bold text */
    border: 1px solid #ddd;     /* Light border */
    font-size: 14px;            /* Font size */
    white-space: nowrap;        /* Prevent headers from wrapping */
}

    tbody td {
        text-align: center;
        vertical-align: middle;
        padding: 8px;
    }
    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }
    .text-bold {
        font-weight: bold;
    }
    tfoot td {
        background-color: #e9ecef;
        font-weight: bold;
    }
</style>

<div class="box">
    <div class="box-header no-print">
        <h3 class="box-title"><i class="fa iniicon-balancefeesreport"></i> <?=$this->lang->line('panel_title')?> - All Class Report</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"> Class Wise Balance Report</li>
        </ol>
    </div><!-- /.box-header -->

 


    <!-- form start -->
    <div class="box-body">
        <div class="row">

            <div class="col-sm-12">
                <div id="hide-table">

                <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="margin-right: 10px; background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Print</button>
        <button id="exportBtn" onclick="exportToExcel()" style="background-color: #2196F3; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Export to Excel</button>
      </div>

                    <table border='1' cellpadding='5' class="table table-bordered" id="myTable">
                        <thead>
                            <tr>
                                <th  >Classname</th>
                                <th >Total Fee</th>
                                <th  >Total Paid</th>
                                <th  >Total Discount</th>
                                <th >Total Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
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
                            
                                    
                                }else{
                                    echo "<tr style='font-weight:bold;'>
                                    <td>No Records Found</td></tr>";
                                } 

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->

 
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>


<script>
        $(document).ready(function () {
            $("#exportBtn").click(function () {
                var table = document.getElementById("myTable");
                var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
                XLSX.writeFile(wb, "AllClassWise Report.xlsx");
            });
        });
    </script>
