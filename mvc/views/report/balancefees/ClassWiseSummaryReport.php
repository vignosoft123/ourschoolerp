<?php
$siteinfos = pluck($siteinfos,'obj','siteID');
$schoolyearsessionobj = pluck($schoolyearsessionobj,'obj','schoolyearID');

// Helper function for Indian number formatting
function formatIndianCurrency($number, $decimals = 2) {
    $number = round($number, $decimals);
    $parts = explode('.', $number);
    $integer = $parts[0];
    $decimal = isset($parts[1]) ? $parts[1] : '00';
    
    // Pad decimal to required places
    $decimal = str_pad($decimal, $decimals, '0');
    
    // Indian number formatting
    $integer = (string)$integer;
    if(strlen($integer) > 3) {
        $last3digits = substr($integer, -3);
        $remaining = substr($integer, 0, -3);
        $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
        $integer = $remaining . ',' . $last3digits;
    }
    
    return $integer . '.' . $decimal;
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

<style>
.table-summary {
    margin-top: 20px;
    border-collapse: collapse;
    width: 100%;
}
.table-summary thead th {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    text-align: center;
    font-weight: bold;
    border: 1px solid #ddd;
}
.table-summary tbody td {
    padding: 8px;
    text-align: center;
    border: 1px solid #ddd;
}
.summary-header {
    background-color: #4CAF50 !important;
    color: white !important;
}
.grand-total {
    background-color: #e9ecef !important;
    font-weight: bold;
    font-size: 1.1em;
}

/* Top action buttons (Print, Export) */
.report-actions-bar {
    margin: 10px 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}

.report-actions-bar .btn {
    border-radius: 12px;
    padding: 8px 18px;
    font-weight: 600;
    font-size: 13px;
    border: none;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
}

.report-actions-bar .btn i,
.report-actions-bar .btn span.fa {
    margin-right: 6px;
}

.btn-export-excel {
    background: linear-gradient(135deg, #28a745 0%, #7ed957 100%);
    color: #fff;
}

.btn-export-excel:hover {
    background: linear-gradient(135deg, #218838 0%, #6fc44e 100%);
}
</style>

<?php
    if(customCompute($students)) { ?>
    
    <div class="row">
        <div class="col-sm-12 report-actions-bar">
            <?php
                echo btn_printReport('balancefeesreport', $this->lang->line('report_print'), 'printablediv'); 
            ?>
            <button id="exportSummaryButton" class="btn btn-export-excel">Export to Excel</button>
        </div>
    </div>

    <div id="printablediv">
        <div class="box">
            <div class="box-header bg-gray">
                <h3 class="box-title text-navy">
                    <i class="fa iniicon-balancefeesreport"></i> 
                    <?=$this->lang->line('balancefeesreport_report_for')?> 
                    <?=$this->lang->line('balancefeesreport_academicyear')?> 
                    <?=isset($schoolyearsessionobj[$schoolyearID]->schoolyear) ? $schoolyearsessionobj[$schoolyearID]->schoolyear : ''?>
                </h3>
            </div>
            <div class="box-body">
                <!-- Class Wise Summary Table -->
                <table class="table table-striped table-bordered table-hover table-summary" id="summaryTable">
                    <thead class="summary-header">
                        <tr>
                            <th>S.No</th>
                            <th>Class Name</th>
                            <th style="color:blue">Total Fee <?=!empty($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : ''?></th>
                            <th style="color:purple">Discount/Waiver <?=!empty($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : ''?></th>
                            <th style="color:green">Paid <?=!empty($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : ''?></th>
                            <th style="color:red">Balance <?=!empty($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : ''?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Group students by class and calculate totals
                        $classTotals = [];
                        
                        foreach($students as $student) {
                            if(!empty($totalAmountAndDiscount[$student->srstudentID]['amount'])) {
                                $classID = $student->srclassesID;
                                $className = isset($classes[$classID]) ? $classes[$classID] : 'Unknown Class';

                                if(!isset($classTotals[$classID])) {
                                    $classTotals[$classID] = [
                                        'className' => $className,
                                        'totalFee' => 0,
                                        'totalDiscount' => 0,
                                        'totalPaid' => 0,
                                        'totalBalance' => 0
                                    ];
                                }

                                // Calculate amounts for this student using the same logic as main report
                                $studentTotalFee = 0;
                                $studentTotalDiscount = 0;
                                $studentTotalPaid = 0;
                                
                                // Use payment_split data for accurate fee type breakdown
                                if (isset($totalPayment_split[$student->srstudentID])) {
                                    foreach($totalPayment_split[$student->srstudentID] as $feeType => $feeData) {
                                        $studentTotalFee += $feeData['total'];
                                        $studentTotalDiscount += $feeData['discount_plus_weaver'];
                                        $studentTotalPaid += $feeData['paid'];
                                    }
                                }
                                
                                $studentBalance = $studentTotalFee - $studentTotalDiscount - $studentTotalPaid;
                                $studentBalance = max(0, $studentBalance); // Ensure no negative balance

                                $classTotals[$classID]['totalFee'] += $studentTotalFee;
                                $classTotals[$classID]['totalDiscount'] += $studentTotalDiscount;
                                $classTotals[$classID]['totalPaid'] += $studentTotalPaid;
                                $classTotals[$classID]['totalBalance'] += $studentBalance;
                            }
                        }

                        // Sort by class ID
                        ksort($classTotals);

                        // Initialize grand totals
                        $grandTotalFee = 0;
                        $grandTotalDiscount = 0;
                        $grandTotalPaid = 0;
                        $grandTotalBalance = 0;
                        
                        $i = 1;
                        foreach($classTotals as $classID => $totals) {
                        ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><?=htmlspecialchars($totals['className'])?></td>
                            <td style="color:blue"><?=formatIndianCurrency($totals['totalFee'])?></td>
                            <td style="color:purple"><?=formatIndianCurrency($totals['totalDiscount'])?></td>
                            <td style="color:green"><?=formatIndianCurrency($totals['totalPaid'])?></td>
                            <td style="color:red"><?=formatIndianCurrency($totals['totalBalance'])?></td>
                        </tr>
                        <?php
                            // Add to grand totals
                            $grandTotalFee += $totals['totalFee'];
                            $grandTotalDiscount += $totals['totalDiscount'];
                            $grandTotalPaid += $totals['totalPaid'];
                            $grandTotalBalance += $totals['totalBalance'];
                        }
                        ?>
                        
                        <!-- Grand Total Row -->
                        <tr class="grand-total">
                            <td colspan="2" class="text-right">
                                <strong><?=$this->lang->line('balancefeesreport_grand_total')?> <?=!empty($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : ''?></strong>
                            </td>
                            <td style="color:blue"><strong><?=formatIndianCurrency($grandTotalFee)?></strong></td>
                            <td style="color:purple"><strong><?=formatIndianCurrency($grandTotalDiscount)?></strong></td>
                            <td style="color:green"><strong><?=formatIndianCurrency($grandTotalPaid)?></strong></td>
                            <td style="color:red"><strong><?=formatIndianCurrency($grandTotalBalance)?></strong></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Verification Info -->
                <!-- <div class="alert alert-info" style="margin-top: 20px;">
                    <h4><i class="icon fa fa-info"></i> Report Summary:</h4>
                    <p><strong>Total Classes:</strong> <?=count($classTotals)?></p>
                    <p><strong>Total Students:</strong> <?=count($students)?></p>
                    <p><strong>Academic Year:</strong> <?=isset($schoolyearsessionobj[$schoolyearID]->schoolyear) ? $schoolyearsessionobj[$schoolyearID]->schoolyear : ''?></p>
                </div> -->
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#exportSummaryButton").click(function () {
                var table = document.getElementById("summaryTable");
                var wb = XLSX.utils.table_to_book(table, { sheet: "Summary" });
                XLSX.writeFile(wb, "class_wise_summary.xlsx");
            });
        });
    </script>
    
    <?php } else { ?>
        <br/>
        <div class="col-sm-12">
            <div class="callout callout-danger">
                <p><b class="text-info"><?=$this->lang->line('report_data_not_found')?></b></p>
            </div>
        </div>
    <?php } ?>
    
    <div class="col-sm-12 text-center footerAll">
        <?=reportfooter($siteinfos, $schoolyearsessionobj)?>
    </div>