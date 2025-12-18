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


<style>
/* Style the table header */
 .table-container {
      max-height: 500px; /* Set scroll height */
      overflow-y: auto;
      border: 1px solid #ccc;
    }

#myTable thead th {
    background-color: #4CAF50; Green background
    color: white;               /* White text */
    padding: 10px;              /* Padding inside headers */
    text-align: center;         /* Center the header text */
    font-weight: bold;          /* Bold text */
    border: 1px solid #ddd;     /* Light border */
    font-size: 14px;            /* Font size */
    white-space: nowrap;        /* Prevent headers from wrapping */

     

}

#myTable thead tr:first-child th {
    position: sticky;
    top: 0;
    z-index: 3;
    background-color: #4CAF50;
    color: white;
}

#myTable thead tr:nth-child(2) th {
    position: sticky;
    top: 40px; /* Adjust this to match your first row height */
    z-index: 2;
    background-color: #66BB6A; /* Slightly different green for clarity */
    color: white;
}

/* Table rows */
#myTable tbody td {
    padding: 8px;
    text-align: center;
    border: 1px solid #ddd;
    font-size: 13px;
}

/* Table overall */
#myTable {
    border-collapse: collapse;
    width: 100%;
    min-width: 1200px; /* make sure table scrolls */
}

/* Scrollbar wrapping div */
.table-responsive {
    width: 100%;
    overflow-x: auto;
}

 
</style>



<div class="row">
    <div class="col-sm-12" style="margin:10px 0px">
        <?php
            
            $pdf_preview_uri = base_url('balancefeesreport/pdf/'.$classesID.'/'.$sectionID.'/'.$studentID);
            $xml_preview_uri = base_url('balancefeesreport/xlsx/'.$classesID.'/'.$sectionID.'/'.$studentID);

            echo btn_printReport('balancefeesreport', $this->lang->line('report_print'), 'printablediv'); 

            // echo btn_pdfPreviewReport('balancefeesreport',$pdf_preview_uri, $this->lang->line('report_pdf_preview'));
            // echo btn_xmlReport('balancefeesreport',$xml_preview_uri, $this->lang->line('report_xlsx'));
            // echo btn_sentToMailReport('balancefeesreport', $this->lang->line('report_send_pdf_to_mail'));
        ?>

        <button id="exportButton" class="btn btn-default">Export to Excel</button>
        <button class="btn btn-default " id="send_sms_balance_btn"><span class="fa fa-send"></span> Send SMS</button>
        <button class="btn btn-default " id="send_whatsapp_balance_btn"><span class="fa fa-send"></span> Send Whatsapp</button>

       


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
                        <?php
    // Prepare Fee Type List (dynamic headers)
    $allFeeTypes = [];
    foreach ($totalPayment_split as $studentID => $feeTypes) {
        foreach ($feeTypes as $feeType => $values) {
            $allFeeTypes[$feeType] = $feeType;
        }
    }
    $allFeeTypes = array_values($allFeeTypes);
?>
            <div class="table-container" style="overflow-x: auto; margin-top: 20px; background: #fff; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">

                <table class="table table-striped table-bordered" id="myTable" style="min-width: 1200px;">
                    <thead>
                        <tr>
                            <th rowspan="2"><?=$this->lang->line('slno')?></th>
                            <th rowspan="2"><?=$this->lang->line('balancefeesreport_name')?></th>
                            <th rowspan="2">Father Name </th>
                            <th rowspan="2"><?=$this->lang->line('balancefeesreport_registerNO')?></th>
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
                        $i = 0;

                        foreach($students as $student) {
                            if(!empty($totalAmountAndDiscount[$student->srstudentID]['amount'])) {
                                $i++;
                        ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$student->srname?></td>
                            <td><?=$student->father_name?></td>
                            <td><?=$student->srregisterNO?></td>
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
                        <tr>
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

                            <td></td>
                        </tr>   
                    </tbody>
                </table>

            </div>
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

