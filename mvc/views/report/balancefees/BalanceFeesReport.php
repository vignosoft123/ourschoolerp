<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

<?php
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






<div class="rpt-action-bar">
    <?php
        $pdf_preview_uri = base_url('balancefeesreport/pdf/'.$classesID.'/'.$sectionID.'/'.$studentID);
        $xml_preview_uri = base_url('balancefeesreport/xlsx/'.$classesID.'/'.$sectionID.'/'.$studentID);
        echo btn_printReport('balancefeesreport', $this->lang->line('report_print'), 'printablediv');
    ?>
    <button id="exportButton" class="btn btn-success rpt-action-btn"><i class="fa fa-file-excel-o"></i> Export to Excel</button>
    <button class="btn btn-info rpt-action-btn" id="send_sms_balance_btn"><i class="fa fa-send"></i> Send SMS</button>
    <button class="btn btn-success rpt-action-btn" id="send_whatsapp_balance_btn"><i class="fa fa-whatsapp"></i> Send Whatsapp</button>
</div>

<div class="box">
    <div class="box-header rpt-box-header">
        <h3 class="box-title"><i class="fa fa-clipboard"></i>
            <?=$this->lang->line('balancefeesreport_report_for')?> - 
            <?=$this->lang->line('balancefeesreport_balancefees');?>
        </h3>
    </div><!-- /.box-header -->
    <div id="printablediv">
    <!-- form start -->
        <div class="box-body" style="margin-bottom: 50px;">
            <div class="row">
                <div class="col-sm-12">
                    <?=reportheader($siteinfos, $schoolyearsessionobj)?>
                </div>
                <?php if($classesID >= 0 || $sectionID >= 0 ) { ?>
                    <div class="col-sm-12">
                        <div class="rpt-class-info">
                            <span><?=$this->lang->line('balancefeesreport_class')?> : <strong><?=isset($classes[$classesID]) ? $classes[$classesID] : $this->lang->line('balancefeesreport_all_class')?></strong></span>
                            <span>Fees Type : <strong><?=isset($feetypes->feetypes) ? $feetypes->feetypes : "All"?></strong></span>
                            <span><?=$this->lang->line('balancefeesreport_section')?> : <strong><?=isset($sections[$sectionID]) ? $sections[$sectionID] : $this->lang->line('balancefeesreport_all_section')?></strong></span>
                        </div>
                    </div>
                <?php }  else { ?>
                    <div class="col-sm-12" style="margin-top: 15px;"></div>
                <?php } 
                if(customCompute($students)) { ?>
                    <div class="col-sm-12">
                        <div id="hide-table">
                        <?php
    // Use controller-provided allFeeTypes (filtered to current students, non-zero only)
    if (empty($allFeeTypes)) {
        $allFeeTypes = [];
        foreach ($totalPayment_split as $sId => $feeTypes) {
            foreach ($feeTypes as $feeType => $values) {
                if (!empty($values['total'])) {
                    $allFeeTypes[$feeType] = $feeType;
                }
            }
        }
        $allFeeTypes = array_values($allFeeTypes);
    }
?>
            <div class="rpt-table-wrap" id="rpt-wrap-fees">

                <table class="rpt-table" id="myTable">
                    <thead>
                        <tr>
                            <th rowspan="2" class="rpt-sticky-left-hd"><?=$this->lang->line('slno')?></th>
                            <th rowspan="2" class="rpt-sticky-left-hd"><?=$this->lang->line('balancefeesreport_name')?></th>
                            <th rowspan="2" class="rpt-sticky-left-hd">Father Name</th>
                            <th rowspan="2" class="rpt-sticky-left-hd"><?=$this->lang->line('balancefeesreport_registerNO')?></th>
                            <th rowspan="2">Village</th>
                            <?php if($classesID == 0) { ?>
                                <th rowspan="2"><?=$this->lang->line('balancefeesreport_class')?></th>
                            <?php } ?>
                            <?php if($sectionID == 0) { ?>
                                <th rowspan="2"><?=$this->lang->line('balancefeesreport_section')?></th>
                            <?php } ?>
                            <th rowspan="2">Phone</th>

                            <!-- Fee Type main headings -->
                            <?php foreach($allFeeTypes as $feeType) { ?>
                                <th colspan="4"><?=htmlspecialchars($feeType)?></th>
                            <?php } ?>

                            <th rowspan="2"><?=$this->lang->line('balancefeesreport_fees_amount')?></th>
                            <th rowspan="2">Discount/Weaver</th>
                            <th rowspan="2"><?=$this->lang->line('balancefeesreport_paid')?></th>
                            <th rowspan="2"><?=$this->lang->line('balancefeesreport_balance')?></th>
                            <th rowspan="2" style="background:#e65100;">Prev C/F</th>
                            <th rowspan="2">
                                Send SMS <input type="checkbox" id="checkAll" name="send_sms_balance"><br/>
                                <input type="date" name="date" id="date">
                            </th>
                        </tr>

                        <tr>
                            <!-- Subheadings for each Fee Type -->
                            <?php foreach($allFeeTypes as $feeType) { ?>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Discount</th>
                                <th>Balance</th>
                            <?php } ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php 
                        $totalAmount = 0;
                        $totalDiscount = 0;
                        $totalPayments = 0;
                        $totalWeaver = 0;
                        $totalBalance = 0;
                        $total_disc = 0; // Initialize total_disc variable
                        $i = isset($startIndex) ? (int)$startIndex : 0;

                        foreach($students as $student) {
                            if(!empty($totalAmountAndDiscount[$student->srstudentID]['amount'])) {
                                $i++;
                        ?>
                        <tr>
                            <td class="rpt-sticky-left"><?=$i?></td>
                            <td class="rpt-sticky-left"><?=$student->srname?></td>
                            <td class="rpt-sticky-left"><?=$student->father_name?></td>
                            <td class="rpt-sticky-left"><?=$student->srregisterNO?></td>
                            <td><?=$student->village_name?></td>

                            <?php if($classesID == 0) { ?>
                                <td><?=isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : ''?></td>
                            <?php } ?>

                            <?php if($sectionID == 0) { ?>
                                <td><?=isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : ''?></td>
                            <?php } ?>

                            <td><?=$student->phone?></td>

                            <!-- Fee Type Amounts -->
                            <?php 
                             $all_total = $all_paid = $all_discount = $all_remaining = 0;
                            foreach($allFeeTypes as $feeType) {
                                $total = $paid = $discount = $remaining = 0;

                                if (isset($totalPayment_split[$student->srstudentID][$feeType])) {
                                    $feeData = $totalPayment_split[$student->srstudentID][$feeType];
                                    $total = $feeData['total'];
                                    $paid = $feeData['paid'];
                                    $discount = $feeData['discount_plus_weaver'];
                                    $remaining = isset($feeData['remaining']) ? max(0, $feeData['remaining']) : 0;
                                }
                            ?>
                                <td>
                                    <?php echo formatIndianCurrency($total); 
                                        $all_total += $total;
                                ?>
                                </td>
                                <td><?php echo formatIndianCurrency($paid);
                                    $all_paid += $paid;?></td>
                                <td><?=formatIndianCurrency($discount);
                                    $all_discount += $discount;
                                ?></td>
                                <td><?=formatIndianCurrency($remaining);
                                 $all_remaining += $remaining;
                                ?></td>
                            <?php } ?>

                            <!-- Overall -->
                            <td>
                                <?= //number_format($feeamount = $totalAmountAndDiscount[$student->srstudentID]['amount'], 2);
                                $feeamount = formatIndianCurrency($all_total);
                                ?>
                            </td>

                            <td>
                                <?php 
                                    // $discount_plus_waver = $totalAmountAndDiscount[$student->srstudentID]['discount'] + $totalweavar[$student->srstudentID]['weaver'];
                                    // echo number_format($discount_plus_waver,2);
                                    echo $discount_plus_waver = formatIndianCurrency($all_discount);
                                ?>
                            </td>

                            <td>
                                <?= //number_format($paid = $totalPayment[$student->srstudentID]['payment'], 2);
                                   $paid = formatIndianCurrency($all_paid);
                                ?>
                            </td>

                            <td>
                                <?php
                                    // $Amount = $totalAmountAndDiscount[$student->srstudentID]['amount'];
                                    // $Discount = $totalAmountAndDiscount[$student->srstudentID]['discount'];
                                    // $Payment = $totalPayment[$student->srstudentID]['payment'];
                                    // $Weaver = $totalweavar[$student->srstudentID]['weaver'];

                                    // $Balance = ($Amount - $Discount) - ($Payment+$Weaver);


                                    // echo number_format($Balance,2);
                                    echo $Balance = formatIndianCurrency($all_remaining);

                                     $Amount = $all_total;
                                    $Discount =$all_discount;
                                    $Payment = $all_paid;
                                    $Weaver = $all_discount;

                                    $totalAmount += $Amount;
                                    $totalDiscount += $Discount;
                                    $totalPayments += $Payment;
                                    $totalWeaver += $Weaver;
                                    $totalBalance += $all_remaining;
                                    $total_disc += $all_discount;
                                    
                                ?>
                            </td>

                            <td style="background:#fff3e0; color:#e65100; font-weight:700;">
                                <?php
                                    $cfPrev = isset($prevBalanceMap[$student->srstudentID]) ? $prevBalanceMap[$student->srstudentID] : 0;
                                    $totalPrevCFBalance = ($totalPrevCFBalance ?? 0) + $cfPrev;
                                    echo $cfPrev > 0 ? formatIndianCurrency($cfPrev) : '';
                                ?>
                            </td>
                            <td>
                                <?php
                                $fee_paid_balance = $feeamount."^".$paid."^".$Balance;
                                $fee_paid_balance = encrypt_data($fee_paid_balance);
                                // $fee_paid_balance = $Balance;

                                ?>
                                <input type="checkbox" st_ids="<?=$student->studentID?>" st_names="<?=$student->name?>" mobile_no="<?=$student->phone?>" balance="<?=$fee_paid_balance?>" name="send_sms_balance" id="send_sms_balance" class="checkbox">
                            </td>
                        </tr>
                        <?php 
                            }
                        }
                        ?>

                        <!-- Grand Total Row -->
                        <tr class="grand-total-row">
                            <?php 
                                $colspan = 6;
                                if($classesID == 0) {
                                    $colspan++;
                                }
                                if($sectionID == 0) {
                                    $colspan++;
                                }
                                $colspan += count($allFeeTypes) * 4;
                            ?>
                            <td colspan="<?=$colspan?>" class="text-right text-bold">
                                <?=$this->lang->line('balancefeesreport_grand_total')?> <?=!empty($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : ''?>
                            </td>

                            <td style="color:blue" class="text-bold"><?=formatIndianCurrency($totalAmount)?></td>

                            <td class="text-bold" style="color:purple">
                                <?php 
                                    $d_w = $total_disc ;//$totalDiscount + $totalWeaver;
                                    echo formatIndianCurrency($d_w);
                                ?> 
                            </td>

                            <td style="color:green" class="text-bold"><?=formatIndianCurrency($totalPayments)?></td>

                            <td style="color:red" class="text-bold"><?=formatIndianCurrency($totalBalance)?></td>

                            <td style="color:#e65100; background:#fff3e0;" class="text-bold">
                                <?php echo isset($totalPrevCFBalance) && $totalPrevCFBalance > 0 ? formatIndianCurrency($totalPrevCFBalance) : ''; ?>
                            </td>

                            <td></td>
                        </tr>   
                    </tbody>
                </table>

            </div>
            <div class="rpt-hscroll-bar" id="hbar-fees"><div class="rpt-hscroll-inner" id="hbar-inner-fees"></div></div>

            <?php
                // Show Load More button only if there are more than one page of results
                $totalStudents = isset($totalStudents) ? $totalStudents : (isset($students) ? customCompute($students) : 0);
                $perPage       = isset($perPage) ? $perPage : 25;
                if($totalStudents > $perPage) {
                    $nextOffset = $perPage;
            ?>
                <div class="text-center" style="margin-top:15px;">
                    <button id="loadMoreBalanceFees" class="btn btn-success" data-offset="<?=$nextOffset?>" data-perpage="<?=$perPage?>" data-total="<?=$totalStudents?>" style="margin-right: 10px;"><i class="fa fa-plus-circle"></i> Load More</button>
                    <button id="loadAllBalanceFees" class="btn btn-info" data-offset="<?=$nextOffset?>" data-perpage="<?=$perPage?>" data-total="<?=$totalStudents?>"><i class="fa fa-download"></i> Load All</button>
                </div>
            <?php } ?>
                        </div>
                    </div>
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
            </div><!-- row -->
        </div><!-- Body -->
    </div>
</div>
<button class="rpt-scroll-top-btn" id="scroll-to-top-fees">&#8679;</button>


<!-- email modal starts here -->
<form class="form-horizontal" role="form" action="<?=base_url('balancefeesreport/send_pdf_to_mail');?>" method="post">
    <div class="modal fade" id="mail">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=$this->lang->line('balancefeesreport_close')?></span></button>
                <h4 class="modal-title"><?=$this->lang->line('balancefeesreport_mail')?></h4>
            </div>
            <div class="modal-body">

                <?php
                    if(form_error('to'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                ?>
                    <label for="to" class="col-sm-2 control-label">
                        <?=$this->lang->line("balancefeesreport_to")?> <span class="text-red">*</span>
                    </label>
                    <div class="col-sm-6">
                        <input type="email" class="form-control" id="to" name="to" value="<?=set_value('to')?>" >
                    </div>
                    <span class="col-sm-4 control-label" id="to_error">
                    </span>
                </div>

                <?php
                    if(form_error('subject'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                ?>
                    <label for="subject" class="col-sm-2 control-label">
                        <?=$this->lang->line("balancefeesreport_subject")?> <span class="text-red">*</span>
                    </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="subject" name="subject" value="<?=set_value('subject')?>" >
                    </div>
                    <span class="col-sm-4 control-label" id="subject_error">
                    </span>

                </div>

                <?php
                    if(form_error('message'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                ?>
                    <label for="message" class="col-sm-2 control-label">
                        <?=$this->lang->line("balancefeesreport_message")?>
                    </label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="message" style="resize: vertical;" name="message" value="<?=set_value('message')?>" ></textarea>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                <input type="button" id="send_pdf" class="btn btn-success" value="<?=$this->lang->line("balancefeesreport_send")?>" />
            </div>
        </div>
      </div>
    </div>
</form>

<script type="text/javascript">
    
    function check_email(email) {
        var status = false;
        var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        if (email.search(emailRegEx) == -1) {
            $("#to_error").html('');
            $("#to_error").html("<?=$this->lang->line('balancefeesreport_mail_valid')?>").css("text-align", "left").css("color", 'red');
        } else {
            status = true;
        }
        return status;
    }

    $("#send_pdf").click(function() {
        var field = {
            'to'         : $('#to').val(), 
            'subject'    : $('#subject').val(), 
            'message'    : $('#message').val(),
            'classesID'  : '<?=$classesID?>',
            'sectionID'  : '<?=$sectionID?>',
            'studentID'  : '<?=$studentID?>',
        };

        var to = $('#to').val();
        var subject = $('#subject').val();
        var error = 0;

        $("#to_error").html("");
        $("#subject_error").html("");

        if(to == "" || to == null) {
            error++;
            $("#to_error").html("<?=$this->lang->line('balancefeesreport_mail_to')?>").css("text-align", "left").css("color", 'red');
        } else {
            if(check_email(to) == false) {
                error++
            }
        }

        if(subject == "" || subject == null) {
            error++;
            $("#subject_error").html("<?=$this->lang->line('balancefeesreport_mail_subject')?>").css("text-align", "left").css("color", 'red');
        } else {
            $("#subject_error").html("");
        }

        if(error == 0) {
            $('#send_pdf').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: "<?=base_url('balancefeesreport/send_pdf_to_mail')?>",
                data: field,
                dataType: "html",
                success: function(data) {
                    var response = JSON.parse(data);
                    if(response.status == false) {
                        $('#send_pdf').removeAttr('disabled');
                        $.each(response, function(index, value) {
                            if(index != 'status') {
                                toastr["error"](value)
                                toastr.options = {
                                  "closeButton": true,
                                  "debug": false,
                                  "newestOnTop": false,
                                  "progressBar": false,
                                  "positionClass": "toast-top-right",
                                  "preventDuplicates": false,
                                  "onclick": null,
                                  "showDuration": "500",
                                  "hideDuration": "500",
                                  "timeOut": "5000",
                                  "extendedTimeOut": "1000",
                                  "showEasing": "swing",
                                  "hideEasing": "linear",
                                  "showMethod": "fadeIn",
                                  "hideMethod": "fadeOut"
                                }
                            }
                        });
                    } else {
                        location.reload();
                    }
                }
            });
        }
    });


$(document).ready(function() {

    $("#checkAll").click(function(){
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    if (typeof applyStickyColumns === 'function') applyStickyColumns();
});

       
// $(document).on("click","#send_sms_balance_btn",function(){
$(document).off("click", "#send_sms_balance_btn").on("click", "#send_sms_balance_btn", function() {


var st_ids = [];
st_names =[];
mobile_no = [];
balance = [];
// total_marks = [] ;
// marks_template = []; 
i=j=k=l=m=n=0;
var date = $("#date").val();
var dynamic_term = $("#feetypeID option:selected").text(); 
if(date == null || date == ''){
    alert('Please select date');
    return false;
}


$('.checkbox:checked').each(function(){        
    // var values = $(this).val();
    // var sids = $(this).attr("st_ids");
    
    st_ids[i++] = $(this).attr("st_ids");
    st_names[j++] = $(this).attr("st_names");
    mobile_no[k++] = $(this).attr("mobile_no");
    balance[l++] = $(this).attr("balance");
    // total_marks[m++] = $(this).attr("total_marks");
    // marks_template[n++] = $(this).attr("marks_template");
}); 

$.ajax({
                
    type: "POST",
    url: "<?php echo site_url('progresscardreport/send_balance_sms'); ?>",
    // dataType: "json",
    data: {"st_ids":st_ids,"st_names":st_names,"mobile_no":mobile_no,"balance":balance,"date":date,"dynamic_term":dynamic_term},
    success: function(result)
    {
        console.log("SMS sent successfully:", result);

    }
})
});


$(document).off("click", "#send_whatsapp_balance_btn").on("click", "#send_whatsapp_balance_btn", function() {


var st_ids = [];
st_names =[];
mobile_no = [];
balance = [];
// total_marks = [] ;
// marks_template = []; 
i=j=k=l=m=n=0;
var date = $("#date").val();
var dynamic_term = $("#feetypeID option:selected").text(); 
var class_name = "<?php echo isset($classes[$classesID]) ? $classes[$classesID] : 'All Classes'; ?>";
if(date == null || date == ''){
    alert('Please select date');
    return false;
}


$('.checkbox:checked').each(function(){        
    // var values = $(this).val();
    // var sids = $(this).attr("st_ids");
    
    st_ids[i++] = $(this).attr("st_ids");
    st_names[j++] = $(this).attr("st_names");
    mobile_no[k++] = $(this).attr("mobile_no");
    balance[l++] = $(this).attr("balance");
    // total_marks[m++] = $(this).attr("total_marks");
    // marks_template[n++] = $(this).attr("marks_template");
}); 

$.ajax({
                
    type: "POST",
    url: "<?php echo site_url('progresscardreport/send_balance_whatsapp'); ?>",
    // dataType: "json",
    data: {"st_ids":st_ids,"st_names":st_names,"mobile_no":mobile_no,"balance":balance,"date":date,"dynamic_term":dynamic_term,"class_name":class_name},
    success: function(result)
    {
        var msg = "";
        try {
            if (typeof result === "string") {
                var parsed = JSON.parse(result);
                msg = parsed.message || JSON.stringify(parsed);
            } else if (typeof result === "object" && result !== null) {
                msg = result.message || JSON.stringify(result);
            } else {
                msg = "Message sent successfully.";
            }
        } catch (e) {
            msg = "Message sent successfully.";
        }
        alert(msg);
        console.log("whatsapp sent successfully:", result);

    }
})
});


</script>

<script>
        // $(document).ready(function () {
        //     $("#exportButton").click(function () {
        //         var table = document.getElementById("myTable");
        //         var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
        //         XLSX.writeFile(wb, "table_data.xlsx");
        //     });
        // });
    </script>

     <script>
    $(document).ready(function () {
        $("#exportButton").click(function () {
            var table = document.getElementById("myTable");

            // Clone the table so we can modify without affecting the original
            var clonedTable = table.cloneNode(true);

            // Remove last 5 columns from each row
            for (var i = 0; i < clonedTable.rows.length; i++) {
                // for (var j = 0; j < 5; j++) {
                //     clonedTable.rows[i].deleteCell(clonedTable.rows[i].cells.length - 1);
                // }
                 for (var j = 0; j < 1; j++) {
                    clonedTable.rows[i].deleteCell(clonedTable.rows[i].cells.length - 1);
                }
            }

            // Convert modified table to Excel
            var wb = XLSX.utils.table_to_book(clonedTable, { sheet: "Sheet1" });
            XLSX.writeFile(wb, "table_data.xlsx");
        });
    });
</script>

<script>
    // $(document).ready(function () {
    //     $("#exportButton").click(function () {
    //         var table = document.getElementById("myTable");
    //         var ws = XLSX.utils.table_to_sheet(table);

    //         // Convert sheet to array
    //         var data = XLSX.utils.sheet_to_json(ws, { header: 1 });

    //         // Remove last 5 columns from each row (without touching the HTML table)
    //         var filteredData = data.map(row => row.slice(0, row.length - 5));

    //         // Create workbook from filtered data
    //         var newWs = XLSX.utils.aoa_to_sheet(filteredData);
    //         var wb = XLSX.utils.book_new();
    //         XLSX.utils.book_append_sheet(wb, newWs, "Sheet1");

    //         XLSX.writeFile(wb, "table_data.xlsx");
    //     });
    // });
</script>

<script>
function applyStickyColumns() {
    var table = document.getElementById('myTable');
    if (!table) return;

    var firstRow = table.querySelector('thead tr:first-child');
    if (!firstRow) return;

    var stickyCells = firstRow.querySelectorAll('th.rpt-sticky-left-hd');
    if (!stickyCells.length) return;

    var offsets = [];
    var cumLeft = 0;
    stickyCells.forEach(function(th, idx) {
        offsets[idx] = cumLeft;
        th.style.left = cumLeft + 'px';
        cumLeft += th.offsetWidth;
        th.classList.toggle('rpt-sticky-left-shadow', idx === stickyCells.length - 1);
    });

    table.querySelectorAll('tbody tr').forEach(function(tr) {
        var tds = tr.querySelectorAll('td.rpt-sticky-left');
        tds.forEach(function(td, idx) {
            if (idx < offsets.length) {
                td.style.left = offsets[idx] + 'px';
                td.classList.toggle('rpt-sticky-left-shadow', idx === tds.length - 1);
            }
        });
    });
}

// Sticky horizontal scrollbar
(function() {
    var wrap  = document.getElementById('rpt-wrap-fees');
    var hbar  = document.getElementById('hbar-fees');
    var inner = document.getElementById('hbar-inner-fees');
    if (!wrap || !hbar || !inner) return;

    function syncBarWidth() {
        var table = wrap.querySelector('.rpt-table');
        inner.style.width = (table ? table.scrollWidth : wrap.scrollWidth) + 'px';
        hbar.style.left   = wrap.getBoundingClientRect().left + 'px';
        hbar.style.width  = wrap.clientWidth + 'px';
        hbar.style.display = (wrap.scrollWidth > wrap.clientWidth) ? 'block' : 'none';
    }

    var syncing = false;
    wrap.addEventListener('scroll', function() {
        if (!syncing) { syncing = true; hbar.scrollLeft = wrap.scrollLeft; syncing = false; }
    });
    hbar.addEventListener('scroll', function() {
        if (!syncing) { syncing = true; wrap.scrollLeft = hbar.scrollLeft; syncing = false; }
    });

    window.addEventListener('resize', syncBarWidth);
    syncBarWidth();
    setTimeout(syncBarWidth, 400);
})();

// Scroll to top
(function() {
    var btn = document.getElementById('scroll-to-top-fees');
    if (!btn) return;
    window.addEventListener('scroll', function() {
        btn.style.display = window.scrollY > 300 ? 'block' : 'none';
    });
    btn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();
</script>

