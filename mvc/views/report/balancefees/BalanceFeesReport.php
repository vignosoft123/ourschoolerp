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

    <div class="btn-group rpt-col-selector-group" id="columnSelectorGroup">
        <button type="button" class="btn btn-info rpt-action-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                            <th rowspan="2" class="rpt-sticky-left-hd" data-col="slno"><?=$this->lang->line('slno')?></th>
                            <th rowspan="2" class="rpt-sticky-left-hd" data-col="name"><?=$this->lang->line('balancefeesreport_name')?></th>
                            <th rowspan="2" class="rpt-sticky-left-hd" data-col="father">Father Name</th>
                            <th rowspan="2" class="rpt-sticky-left-hd" data-col="regno"><?=$this->lang->line('balancefeesreport_registerNO')?></th>
                            <th rowspan="2" data-col="village">Village</th>
                            <?php if($classesID == 0) { ?>
                                <th rowspan="2" data-col="class"><?=$this->lang->line('balancefeesreport_class')?></th>
                            <?php } ?>
                            <?php if($sectionID == 0) { ?>
                                <th rowspan="2" data-col="section"><?=$this->lang->line('balancefeesreport_section')?></th>
                            <?php } ?>
                            <th rowspan="2" data-col="phone">Phone</th>

                            <!-- Fee Type main headings: one "Columns" checkbox per fee type,
                                 shared (via the same data-col="ftN" value) across this merged
                                 heading + its 4 sub-heading cells below + the 4 matching <td>s
                                 in every body row, so unchecking it hides the whole 4-col group. -->
                            <?php foreach($allFeeTypes as $ftIndex => $feeType) { ?>
                                <th colspan="4" data-col="ft<?=$ftIndex?>"><?=htmlspecialchars($feeType)?></th>
                            <?php } ?>

                            <th rowspan="2" data-col="amount"><?=$this->lang->line('balancefeesreport_fees_amount')?></th>
                            <th rowspan="2" data-col="discountweaver">Discount/Weaver</th>
                            <th rowspan="2" data-col="paid"><?=$this->lang->line('balancefeesreport_paid')?></th>
                            <th rowspan="2" data-col="balance"><?=$this->lang->line('balancefeesreport_balance')?></th>
                            <th rowspan="2" style="background:#e65100;" data-col="prevcf">Prev C/F</th>
                            <th rowspan="2" data-col="sendsms">
                                Send SMS <input type="checkbox" id="checkAll" name="send_sms_balance"><br/>
                                <input type="date" name="date" id="date">
                            </th>
                        </tr>

                        <tr>
                            <!-- Subheadings for each Fee Type (same data-col="ftN" as the merged heading above,
                                 not a separate checkbox entry -- the JS de-dupes by data-col) -->
                            <?php foreach($allFeeTypes as $ftIndex => $feeType) { ?>
                                <th data-col="ft<?=$ftIndex?>">Total</th>
                                <th data-col="ft<?=$ftIndex?>">Paid</th>
                                <th data-col="ft<?=$ftIndex?>">Discount</th>
                                <th data-col="ft<?=$ftIndex?>">Balance</th>
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
                            <td class="rpt-sticky-left" data-col="slno"><?=$i?></td>
                            <td class="rpt-sticky-left" data-col="name"><?=$student->srname?></td>
                            <td class="rpt-sticky-left" data-col="father"><?=$student->father_name?></td>
                            <td class="rpt-sticky-left" data-col="regno"><?=$student->srregisterNO?></td>
                            <td data-col="village"><?=$student->village_name?></td>

                            <?php if($classesID == 0) { ?>
                                <td data-col="class"><?=isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : ''?></td>
                            <?php } ?>

                            <?php if($sectionID == 0) { ?>
                                <td data-col="section"><?=isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : ''?></td>
                            <?php } ?>

                            <td data-col="phone"><?=$student->phone?></td>

                            <!-- Fee Type Amounts -->
                            <?php
                             $all_total = $all_paid = $all_discount = $all_remaining = 0;
                            foreach($allFeeTypes as $ftIndex => $feeType) {
                                $total = $paid = $discount = $remaining = 0;

                                if (isset($totalPayment_split[$student->srstudentID][$feeType])) {
                                    $feeData = $totalPayment_split[$student->srstudentID][$feeType];
                                    $total = $feeData['total'];
                                    $paid = $feeData['paid'];
                                    $discount = $feeData['discount_plus_weaver'];
                                    $remaining = isset($feeData['remaining']) ? max(0, $feeData['remaining']) : 0;
                                }
                            ?>
                                <td data-col="ft<?=$ftIndex?>">
                                    <?php echo formatIndianCurrency($total);
                                        $all_total += $total;
                                ?>
                                </td>
                                <td data-col="ft<?=$ftIndex?>"><?php echo formatIndianCurrency($paid);
                                    $all_paid += $paid;?></td>
                                <td data-col="ft<?=$ftIndex?>"><?=formatIndianCurrency($discount);
                                    $all_discount += $discount;
                                ?></td>
                                <td data-col="ft<?=$ftIndex?>"><?=formatIndianCurrency($remaining);
                                 $all_remaining += $remaining;
                                ?></td>
                            <?php } ?>

                            <!-- Overall -->
                            <td data-col="amount">
                                <?= //number_format($feeamount = $totalAmountAndDiscount[$student->srstudentID]['amount'], 2);
                                $feeamount = formatIndianCurrency($all_total);
                                ?>
                            </td>

                            <td data-col="discountweaver">
                                <?php
                                    // $discount_plus_waver = $totalAmountAndDiscount[$student->srstudentID]['discount'] + $totalweavar[$student->srstudentID]['weaver'];
                                    // echo number_format($discount_plus_waver,2);
                                    echo $discount_plus_waver = formatIndianCurrency($all_discount);
                                ?>
                            </td>

                            <td data-col="paid">
                                <?= //number_format($paid = $totalPayment[$student->srstudentID]['payment'], 2);
                                   $paid = formatIndianCurrency($all_paid);
                                ?>
                            </td>

                            <td data-col="balance">
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

                            <td style="background:#fff3e0; color:#e65100; font-weight:700;" data-col="prevcf">
                                <?php
                                    $cfPrev = isset($prevBalanceMap[$student->srstudentID]) ? $prevBalanceMap[$student->srstudentID] : 0;
                                    $totalPrevCFBalance = ($totalPrevCFBalance ?? 0) + $cfPrev;
                                    echo $cfPrev > 0 ? formatIndianCurrency($cfPrev) : '';
                                ?>
                            </td>
                            <td data-col="sendsms">
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
                            <td colspan="<?=$colspan?>" class="text-right text-bold" data-col-group="grandtotal-label">
                                <?=$this->lang->line('balancefeesreport_grand_total')?> <?=!empty($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : ''?>
                            </td>

                            <td style="color:blue" class="text-bold" data-col="amount"><?=formatIndianCurrency($totalAmount)?></td>

                            <td class="text-bold" style="color:purple" data-col="discountweaver">
                                <?php
                                    $d_w = $total_disc ;//$totalDiscount + $totalWeaver;
                                    echo formatIndianCurrency($d_w);
                                ?>
                            </td>

                            <td style="color:green" class="text-bold" data-col="paid"><?=formatIndianCurrency($totalPayments)?></td>

                            <td style="color:red" class="text-bold" data-col="balance"><?=formatIndianCurrency($totalBalance)?></td>

                            <td style="color:#e65100; background:#fff3e0;" class="text-bold" data-col="prevcf">
                                <?php echo isset($totalPrevCFBalance) && $totalPrevCFBalance > 0 ? formatIndianCurrency($totalPrevCFBalance) : ''; ?>
                            </td>

                            <td data-col="sendsms"></td>
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
            // display: true makes SheetJS skip any cell hidden via the Columns selector
            // (display:none is copied onto the clone by cloneNode(true) above).
            var wb = XLSX.utils.table_to_book(clonedTable, { sheet: "Sheet1", display: true });
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
    $(document).ready(function () {
        var $table = $('#myTable');
        if (!$table.length) { return; }

        // Base (non fee-type) columns that the Grand Total row's label cell spans across.
        // Class/Section may not exist in the DOM at all depending on the filter -- the
        // $th.length check below skips those automatically, same as the conditional
        // columns already handled by the plain data-col lookups.
        var BASE_LABEL_COLS = ['slno', 'name', 'father', 'regno', 'village', 'class', 'section', 'phone'];

        var $menu = $('#columnSelectorMenu');
        var $list = $('#columnSelectorList');

        // Each fee type's 4 sub-columns (Total/Paid/Discount/Balance) share one data-col
        // value ("ft0", "ft1", ...) with their merged heading cell, so unchecking one
        // checkbox hides the whole 4-column breakdown for that fee type at once. Because
        // of that sharing, the same data-col value appears on 5 different <th> elements
        // (1 merged heading + 4 sub-headings) -- de-dupe so only one checkbox is built per
        // distinct data-col, keeping the merged heading's text (the fee type name) as the
        // label since it appears first in DOM order.
        var seenCols = {};
        $table.find('thead th[data-col]').each(function () {
            var col = $(this).attr('data-col');
            if (seenCols[col]) { return; }
            seenCols[col] = true;

            var label = $.trim($(this).text());
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

        // Distinct "ftN" fee-type group keys currently in the header (varies per search).
        function getFeetypeGroupCols() {
            var cols = [];
            $table.find('thead th[data-col^="ft"]').each(function () {
                var col = $(this).attr('data-col');
                if (cols.indexOf(col) === -1) { cols.push(col); }
            });
            return cols;
        }

        function recomputeGroupColspans() {
            var baseVisible = BASE_LABEL_COLS.filter(function (col) {
                var $th = $table.find('thead th[data-col="' + col + '"]');
                return $th.length && $th.css('display') !== 'none';
            }).length;

            // Each visible fee-type group contributes its 4 columns (Total/Paid/Discount/
            // Balance) to the Grand Total label's colspan; a hidden group contributes 0.
            var visibleFeetypeGroups = getFeetypeGroupCols().filter(function (col) {
                var $th = $table.find('thead th[data-col="' + col + '"]').first();
                return $th.length && $th.css('display') !== 'none';
            }).length;

            $table.find('td[data-col-group="grandtotal-label"]').attr('colspan', Math.max(baseVisible + (visibleFeetypeGroups * 4), 1));
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

        // Exposed so the lazy "Load More"/"Load All" handlers (BalanceFeesReportView.php)
        // can re-apply the currently hidden columns to rows appended after the fact --
        // those rows are rendered fresh from BalanceFeesReportRows.php and start fully visible.
        window.balanceFeesReapplyColumns = function () {
            $list.find('input[type="checkbox"][data-col]').each(function () {
                toggleColumn($(this).attr('data-col'), $(this).is(':checked'));
            });
        };
    });
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

