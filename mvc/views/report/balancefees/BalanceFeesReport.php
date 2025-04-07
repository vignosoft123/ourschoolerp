<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>


<style>
    /* Fee Type Section */
.fee-type {
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

/* Fee Type Name Styling */
.fee-type-name {
    font-size: 1.1em;
    font-weight: bold;
    color: #333;
}

/* Total Amount Styling */
.total-amount {
    font-size: 0.9em;
    color: #777;
    display: block;
    margin-top: 5px;
    color : blue;
    font-weight: bold;
}

/* Payment Details Styling */
.payment-details {
    margin: 5px 0;
    font-size: 0.95em;
}

/* Paid Amount Styling */
.paid-amount {
    color: green;
    font-weight: bold;
}

/* Remaining Balance Styling */
.remaining-balance {
    color: red;
    font-weight: bold;
}

/* Separator Styling */
.separator {
    border: none;
    height: 1px;
    background-color: #ccc;
    margin-top: 10px;
}

</style>

<div class="row">
    <div class="col-sm-12" style="margin:10px 0px">
        <?php
            
            $pdf_preview_uri = base_url('balancefeesreport/pdf/'.$classesID.'/'.$sectionID.'/'.$studentID);
            $xml_preview_uri = base_url('balancefeesreport/xlsx/'.$classesID.'/'.$sectionID.'/'.$studentID);

            echo btn_printReport('balancefeesreport', $this->lang->line('report_print'), 'printablediv');
            echo btn_pdfPreviewReport('balancefeesreport',$pdf_preview_uri, $this->lang->line('report_pdf_preview'));
            echo btn_xmlReport('balancefeesreport',$xml_preview_uri, $this->lang->line('report_xlsx'));
            echo btn_sentToMailReport('balancefeesreport', $this->lang->line('report_send_pdf_to_mail'));
        ?>

        <button class="btn btn-default " id="send_sms_balance_btn"><span class="fa fa-send"></span> Send SMS</button>

        <button id="exportButton">Export to Excel</button>


    </div>
</div>

<div class="box">
    <div class="box-header bg-gray">
        <h3 class="box-title text-navy"><i class="fa fa-clipboard"></i>
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
                        <div class="row">
                            <div class="col-sm-12">
                                <h5 class="pull-left">
                                    <?php 
                                        echo $this->lang->line('balancefeesreport_class')." : ";
                                        echo isset($classes[$classesID]) ? $classes[$classesID] : $this->lang->line('balancefeesreport_all_class');
                                    ?>
                                </h5>      
                                
                                <h5 class="" style="margin-left:20%">
                                    <?php 
                                    // echo "<pre>";print_r($feetypes);die;
                                        echo "Fees Type : ";
                                        echo isset($feetypes->feetypes) ? $feetypes->feetypes : "All";
                                    ?>
                                </h5> 

                                <h5 class="pull-right">
                                    <?php
                                       echo $this->lang->line('balancefeesreport_section')." : ";
                                       echo isset($sections[$sectionID]) ? $sections[$sectionID] : $this->lang->line('balancefeesreport_all_section');
                                    ?>
                                </h5>                        
                            </div>
                        </div>
                    </div>
                <?php }  else { ?>
                    <div class="col-sm-12" style="margin-top: 15px;"></div>
                <?php } 
                if(customCompute($students)) { ?>
                    <div class="col-sm-12">
                        <div id="hide-table">
                            <table class="table table-bordered" id="myTable">
                                <thead>
                                    <tr>
                                        <th><?=$this->lang->line('slno')?></th>
                                        <th><?=$this->lang->line('balancefeesreport_name')?></th>
                                        <th><?=$this->lang->line('balancefeesreport_registerNO')?></th>
                                        <th>Villege</th>
                                        <?php if($classesID == 0) { ?>
                                          <th><?=$this->lang->line('balancefeesreport_class')?></th>
                                        <?php } ?>
                                        <?php if($sectionID == 0) { ?>
                                          <th><?=$this->lang->line('balancefeesreport_section')?></th>
                                        <?php } ?>
                                        <th>Phone</th>
                                        <th> In Details</th>
                                        <th><?=$this->lang->line('balancefeesreport_fees_amount')?></th>
                                        <!-- <th>Amount By Type</th> -->
                                        <th>Discount/Weaver </th>
                                        <th><?=$this->lang->line('balancefeesreport_paid')?> </th>
                                       
                                        <!-- <th><?=$this->lang->line('balancefeesreport_weaver')?> </th> -->
                                        <th><?=$this->lang->line('balancefeesreport_balance') ?></th> 
                                        
                                   <th> Send SMS <input type="checkbox" class="" id="checkAll" name="send_sms_balance">
                                            <br/>
                                            <input type="date" name="date" id="date">
                                </th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $totalAmount = 0;
                                        $totalDiscount = 0;
                                        $totalPayments = 0;
                                        $totalWeaver = 0;
                                        $totalBalance = 0;
                                        $i=0;
                                        //  echo "<pre>";print_r($totalAmountAndDiscount);
                                        foreach($students as $student) { 

                                           $feeamount =  $paid = $bal_amnt = 0;
                                        
                                        if(!empty($totalAmountAndDiscount[$student->srstudentID]['amount'])){
                                            $i++; 
                                        ?>
                                            <tr>
                                                <td data-title="<?=$this->lang->line('slno')?>"><?=$i?></td>
                                                <td data-title="<?=$this->lang->line('balancefeesreport_name')?>">
                                                    <?=$student->srname?>
                                                </td>
                                                <td data-title="<?=$this->lang->line('balancefeesreport_registerNO')?>">
                                                    <?=$student->srregisterNO?>
                                                </td>
                                                <td> 
                                                <?=$student->village_name?>


                                                </td>
                                                <?php if($classesID == 0) { ?>
                                                    <td data-title="<?=$this->lang->line('balancefeesreport_class')?>">
                                                        <?=isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : ''?>
                                                    </td>
                                                <?php } ?>

                                                <?php if($sectionID == 0) { ?>
                                                    <td data-title="<?=$this->lang->line('balancefeesreport_section')?>">
                                                        <?=isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : ''?>
                                                    </td>
                                                <?php } ?>

                                                <td data-title="<?=$this->lang->line('balancefeesreport_roll')?>">
                                                    <?=$student->phone?>
                                                </td>

                                                <td>
    <?php  
    // Ensure that the 'totalPayment_split' and 'srstudentID' are properly set
    if (isset($totalPayment_split[$student->srstudentID])) {
        foreach ($totalPayment_split[$student->srstudentID] as $ksplit => $split) {
            // Wrap each fee type in a div or paragraph for cleaner styling
            echo '<div class="fee-type">';
            
            // Fee type with its total amount
            echo '<strong class="fee-type-name">' . htmlspecialchars($ksplit) . '</strong>';
            echo '<span class="total-amount">(' . number_format($split['total'], 2) . ')</span>';

            // Paid amount
            echo '<div class="payment-details"><strong>Paid: </strong><span class="paid-amount">' . number_format($split['paid'], 2) . '</span></div>';

            // Remaining balance (ensure it's not negative)
            $remaining = isset($split['remaining']) ? max(0, $split['remaining']) : 0;
            echo '<div class="payment-details"><strong>Balance: </strong><span class="remaining-balance">' . number_format($remaining, 2) . '</span></div>';
            
            // Optional separator for each fee type
            // echo '<hr class="separator">';
            
            echo '</div>';
        }
    } else {
        echo '<p>No payments found for this student.</p>';
    }
    ?>
</td>




                                               

                                                <td data-title="<?=$this->lang->line('balancefeesreport_fees_amount')?>">
                                                    <?= $feeamount = isset($totalAmountAndDiscount[$student->srstudentID]['amount']) ? number_format($totalAmountAndDiscount[$student->srstudentID]['amount'],2) : number_format(0, 2)?>
                                                   
                                                </td>

                                                <!-- <td>
                                                    <div>
                                                        <?php foreach($totalAmountAndDiscount[$student->srstudentID]['type'] as $f_types ){?>
                                                            <p style="color:green"><b> <?= $f_types?> </b></p>
                                                        <?php }?>
                                                    </div>
                                                </td> -->


                                                <td data-title="<?=$this->lang->line('balancefeesreport_discount')?>">

                                                <?php //echo "<pre>"; print_r($totalweavar);die;?>
                                                   <?php 
                                                    $discount_plus_waver = $totalAmountAndDiscount[$student->srstudentID]['discount'] + $totalweavar[$student->srstudentID]['weaver'];
                                                    ?>
                                                    <?php 
                                                        //echo isset($totalAmountAndDiscount[$student->srstudentID]['discount']) ? number_format($totalAmountAndDiscount[$student->srstudentID]['discount'],2) : number_format(0, 2)
                                                        echo isset($discount_plus_waver) ? number_format($discount_plus_waver,2) : number_format(0, 2)
                                                    ?>

                                                </td>

                                                <td data-title="<?=$this->lang->line('balancefeesreport_paid')?>">
                                                    <?= $paid = isset($totalPayment[$student->srstudentID]['payment']) ? number_format($totalPayment[$student->srstudentID]['payment'],2) : number_format(0, 2)?>
                                                </td>
                                             

                                        


                                                <td data-title="<?=$this->lang->line('balancefeesreport_balance')?>">
                                                    <?php 

                                                    // echo "<pre>";print_r($totalweavar);
                                                        $Amount = 0;
                                                        $Discount = 0;
                                                        $Payment = 0;
                                                        $Weaver = 0;

                                                        if(isset($totalAmountAndDiscount[$student->srstudentID]['amount'])) {
                                                            $Amount = $totalAmountAndDiscount[$student->srstudentID]['amount'];
                                                            $totalAmount += $Amount;
                                                        }

                                                        if(isset($totalAmountAndDiscount[$student->srstudentID]['discount'])) {
                                                            $Discount = $totalAmountAndDiscount[$student->srstudentID]['discount'];
                                                            $totalDiscount += $Discount;
                                                        }

                                                        //  echo "<pre>";print_r($totalPayment[$student->srstudentID]);die;
                                                        if(isset($totalPayment[$student->srstudentID]['payment'])) {
                                                            $Payment = $totalPayment[$student->srstudentID]['payment'];
                                                            $totalPayments += $Payment;
                                                        }

                                                        if(isset($totalweavar[$student->srstudentID]['weaver'])) {
                                                            $Weaver = $totalweavar[$student->srstudentID]['weaver'];
                                                            $totalWeaver += $Weaver;
                                                        }

                                                        $Balance = ($Amount - $Discount) - ($Payment+$Weaver);

                                                        $totalBalance += $Balance;

                                                        echo number_format($Balance,2);
                                                    ?>
                                                </td>
 

                                                 <!-- //CONSTRUCT SEND MARKS SMS -->
                                                 <td>
                                                    <?php 
                                                       $fee_paid_balance = $feeamount."^".$paid."^".$Balance;
                                                       $fee_paid_balance = encrypt_data($fee_paid_balance); 
                                                    ?>
                                                

                                                 <input type="checkbox" st_ids="<?php echo $student->studentID;?>" st_names="<?php echo $student->name;?>" mobile_no="<?php echo $student->phone;?>"  balance1="<?php //echo $Balance;?>" balance="<?php echo $fee_paid_balance;?>"    name="send_sms_balance" id="send_sms_balance" class="checkbox"></td>
                                               
                                            </tr>
                                            <?php
                                        }
                                        }
                                    ?>       
                                    <tr>
                                        <?php 
                                            $colspan = 3;
                                            if($classesID == 0) {
                                                $colspan = 4;
                                            }

                                            if($sectionID == 0) {
                                                $colspan = 4;
                                            }

                                            if($classesID == 0 && $sectionID == 0) {
                                                $colspan = 5;
                                            }
                                        ?>
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td></td>

                                        <td  data-title="<?=$this->lang->line('balancefeesreport_grand_total')?>" class="text-right text-bold" colspan="<?=$colspan?>">
                                            <?=$this->lang->line('balancefeesreport_grand_total')?> <?=!empty($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : ''?> </td>

                                        <td style="color:blue" data-title="<?=$this->lang->line('balancefeesreport_total_fees_amount')?>" class="text-bold"><?=number_format($totalAmount,2)?></td>

                                        <td data-title="<?=$this->lang->line('balancefeesreport_total_discount')?>" class="text-bold">
                                            <?php 
                                                $d_w = $totalDiscount + $totalWeaver;
                                            echo number_format($d_w,2);?> 
                                        </td>
                                        
                                        <td style="color:green" data-title="<?=$this->lang->line('balancefeesreport_total_paid')?>" class="text-bold"><?=number_format($totalPayments,2)?></td>
                                        <!-- <td data-title="<?=$this->lang->line('balancefeesreport_total_weaver')?>" class="text-bold"><?=number_format($totalWeaver,2)?></td> -->
                                        <td style="color:red" data-title="<?=$this->lang->line('balancefeesreport_total_balance')?>" class="text-bold"><?=number_format($totalBalance,2)?></td>
                                    </tr>                             
                                </tbody>
                            </table>
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


</script>

<script>
        $(document).ready(function () {
            $("#exportButton").click(function () {
                var table = document.getElementById("myTable");
                var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
                XLSX.writeFile(wb, "table_data.xlsx");
            });
        });
    </script>