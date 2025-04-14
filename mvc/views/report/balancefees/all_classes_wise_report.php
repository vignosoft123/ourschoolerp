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
        <button onclick="exportToExcel()" style="background-color: #2196F3; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Export to Excel</button>
      </div>

                    <table border='1' cellpadding='5' class="table table-bordered" id="myTable">
                        <thead>
                            <tr>
                                <th style='background-color: #e3f2fd;'>Classname</th>
                                <th style='background-color: #c8e6c9;'>Total Fee</th>
                                <th style='background-color: #ffe0b2;'>Total Paid</th>
                                <th style='background-color: #f8bbd0;'>Total Discount</th>
                                <th style='background-color: #d1c4e9;'>Total Balance</th>
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

 

<script type="text/javascript">

 </script>


<script>
// function exportToExcel(tableID='myTable') {
//     var table = document.getElementById(tableID);
//     var html = table.outerHTML.replace(/ /g, '%20');
//     var filename = 'class_fee_report.xls';
//     var dataType = 'application/vnd.ms-excel';
//     var link = document.createElement('a');
//     link.href = 'data:' + dataType + ', ' + html;
//     link.download = filename;
//     link.click();
// }

</script>

 
<script>
// function exportTableToExcel(tableID='myTable', filename = '') {
//     const dataType = 'application/vnd.ms-excel';
//     const tableSelect = document.getElementById(tableID);
//     const tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

//     filename = filename ? filename + '.xls' : 'excel_data.xls';

//     const downloadLink = document.createElement("a");
//     document.body.appendChild(downloadLink);

//     if (navigator.msSaveOrOpenBlob) {
//         const blob = new Blob(['\ufeff', tableHTML], { type: dataType });
//         navigator.msSaveOrOpenBlob(blob, filename);
//     } else {
//         downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
//         downloadLink.download = filename;
//         downloadLink.click();
//     }
// }
</script>
