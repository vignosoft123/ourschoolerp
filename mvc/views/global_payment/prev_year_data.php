<div>Previous Year Invoices</div>

 <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px;">
    <table class="table table-striped table-bordered" style="margin-top: 10px">

     <thead>
        <tr>
            <td style="border-bottom-width: 1px;"><?=$this->lang->line('global_invoice_name')?></td>
            <td style="border-bottom-width: 1px;"><input class="form-control" id="prev_invoicename" type="text" name="invoicename" value="<?=$inv_name?>"></td>

            <td style="border-bottom-width: 1px;"><?=$this->lang->line('global_description')?></td>
            <td style="border-bottom-width: 1px;"><input class="form-control" id="prev_invoicedescription" type="text" name="invoicedescription"></td>
        </tr>
    </thead>

        <tbody>
            <tr>
                <td><?=$this->lang->line('global_invoice_number')?></td>
                <td><input class="form-control" id="prev_invoicenumber" type="text" name="prev_invoicenumber" value="INV-G-<?=(customCompute($globalpayment_max) > 0) ? $globalpayment_max->globalpaymentID+1 : '1'?>" readonly></td>
                <td><?=$this->lang->line('global_payment_year')?></td>
                <td><input class="form-control" id="prev_paymentyear" type="text" name="prev_paymentyear" value="<?=date('Y')?>"></td>
            </tr>
        </tbody>
    </table>
</div>

<?php if(customCompute($invoices)) { 
    $usertype = $this->session->userdata("usertype");
    $prev_accountant = ($usertype == 'Accountant') ? "d-none" : "";
?>

<div class="col-sm-12" style="padding-left: 0px; padding-right: 0px;">
    <div class="table-responsive" style="margin-top:10px !important">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <td style="width:3%">
                        <input type="checkbox" id="prev_select_all"> 
                        <a style="padding: 1px 5px;" id="prev_update_selected" class="btn btn-danger btn-xs mrg"><i class="fa fa-trash-o"></i></a>
                    </td>
                    <td><?=$this->lang->line('global_fees_name')?></td>
                    <td><?=$this->lang->line('global_fees_amount')?></td>
                    <td><?=$this->lang->line('global_due')?></td>
                    <td><?=$this->lang->line('global_paid_amount')?></td>
                    <td class="<?= $prev_accountant ?>">Discount</td>
                    <td class="<?= $prev_accountant ?>"><?=$this->lang->line('global_fine')?></td>
                </tr>
            </thead>

            <tbody>
                <?php 
                $prev_total = 0;
                $prev_totalDue = 0;
                if(customCompute($invoices)) { 
                    $i=1; 
                    foreach ($invoices as $invoice) {
                        $prev_total += number_format($invoice->amount, 2, '.', '');
                        if($invoice->discount > 0) {
                            $prev_total = number_format(($prev_total - $invoice->discount), 2, '.', '');
                        }

                        $payment = 0;
                        if(isset($payments[$invoice->invoiceID])) {
                            $payment = number_format($payments[$invoice->invoiceID], 2, '.', '');
                        }

                        $due = number_format(($invoice->amount - $payment), 2, '.', '');

                        if($invoice->discount > 0) {
                            $due = number_format(($due - $invoice->discount), 2, '.', '');
                        }

                        if(isset($weavers[$invoice->invoiceID])) {
                            $due -= number_format($weavers[$invoice->invoiceID], 2, '.', '');
                        }

                        $prev_totalDue += $due;
                ?>
                <tr>
                    <td>
                        <input type="checkbox" class="prev_record_checkbox" value="<?= $invoice->invoiceID ?>" data-prev-maininvoiceid="<?= $invoice->maininvoiceID ?>">
                        <a style="padding: 1px 5px;" class="prev_update_single btn btn-danger btn-xs mrg" data-prev-id="<?= $invoice->invoiceID ?>" data-prev-mainid="<?= $invoice->maininvoiceID ?>">
                            <i class="fa fa-trash-o"></i>
                        </a>
                    </td>
                    <td><?php if(isset($feetypes[$invoice->feetypeID])) { echo $feetypes[$invoice->feetypeID]; } ?></td>
                    <td><?php echo ($invoice->discount > 0) ? ($invoice->amount - $invoice->discount) : $invoice->amount; ?></td>
                    <td id="prev_due_<?= $i - 1 ?>"><?= $due ?></td>
                    <td>
                        <?php
                            if($due <= 0) {
                                echo 'Paid';
                                echo '<input style="display:none" name="prev_paid-'.$invoice->invoiceID.'-'.$invoice->feetypeID.'" class="form-control prev_paid_amount prev_paid_'.$i.'" type="text">';
                            } else {
                                echo '<input name="prev_paid-'.$invoice->invoiceID.'-'.$invoice->feetypeID.'" class="form-control prev_paid_amount prev_paid_'.$i.'" type="text">';
                            }
                        ?>
                    </td>
                    <td class="<?= $prev_accountant ?>">
                        <?php
                            if($due > 0) {
                                echo '<input name="prev_weaver-'.$invoice->invoiceID.'-'.$invoice->feetypeID.'" class="form-control prev_weaver prev_weaver_'.$i.'" type="text">';
                            } else {
                                echo '<input style="display:none" name="prev_weaver-'.$invoice->invoiceID.'-'.$invoice->feetypeID.'" class="form-control prev_weaver prev_weaver_'.$i.'" type="text">';
                            }
                        ?>
                    </td>
                    <td class="<?= $prev_accountant ?>">
                        <?php
                            if($due > 0) {
                                echo '<input name="prev_fine-'.$invoice->invoiceID.'-'.$invoice->feetypeID.'" class="form-control prev_fine" type="text">';
                            } else {
                                echo '<input style="display:none" name="prev_fine-'.$invoice->invoiceID.'-'.$invoice->feetypeID.'" class="form-control prev_fine" type="text">';
                            }
                        ?>
                    </td>
                </tr>
                <?php $i++; } ?>
                <tr>
                    <td></td>
                    <td><b><?=$this->lang->line('global_total')?></b></td>
                    <td><?= $prev_total ?></td>
                    <td><?= $prev_totalDue ?></td>
                    <td id="prev_set_paid_amount" class="<?= $prev_accountant ?>">0</td>
                    <td id="prev_set_weaver" class="<?= $prev_accountant ?>">0</td>
                    <td id="prev_set_fine">0</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2"><?=$this->lang->line('global_total_collection').' ('.$this->lang->line('global_paid').'+'.$this->lang->line('global_fine').')'?></td>
                    <td colspan="3" id="prev_total_collection">0</td>
                </tr>
                <tr>
                    <td></td>
                    <td><?=$this->lang->line('global_payment_status')?></td>
                    <td>
                        <select class="form-control" id="prev_payment_status">
                            <option value="paid"><?=$this->lang->line('global_paid')?></option>
                            <option value="partial"><?=$this->lang->line('global_partial')?></option>
                            <option value="unpaid"><?=$this->lang->line('global_unpaid')?></option>
                        </select>
                    </td>
                    <td><?=$this->lang->line('global_payment_type')?></td>
                    <td>
                        <select class="form-control" id="prev_payment_type">
                            <option value="cash"><?=$this->lang->line('global_cash')?></option>
                            <option value="chaque"><?=$this->lang->line('global_chaque')?></option>
                            <option value="digital">Digital</option>
                        </select>
                    </td>
                    <td colspan="">Payment Date</td>
                    <td colspan=""><input type="date" name="prev_created_date" id="prev_created_date"></td>
                </tr>
            <?php } ?>
        </tbody>
        </table>
    </div>
</div>
<br/>
<button name="prev_submit" value="sub" id="prev_add_payment" type="submit" class="btn btn-success col-md-2" style="margin-top: 20px;"><?=$this->lang->line('global_submit')?></button>
<?php } ?>
 

<?php 
$prev_schoolyear = 0;

if (!empty($schoolyearID)) {
    $res = $this->db->query(
        'SELECT schoolyear FROM schoolyear WHERE schoolyearID = ?',
        [$schoolyearID]
    )->row_array();

    if (!empty($res)) {
        $prev_schoolyear = $res['schoolyear'];
    }
}

?>

<script>
    $(document).ready(function () {
    let prevGlobalPaid = 0;
    let prevGlobalFine = 0;
    let prevGlobalWeaver = 0;

    $(document).on("keyup", ".prev_paid_amount", function () {
        let sum = 0;
        $(".prev_paid_amount").each(function (i) {
            const val = parseFloat($(this).val()) || 0;
            sum += val;
        });
        prevGlobalPaid = sum;
        $("#prev_set_paid_amount").text(sum.toFixed(2));
        $("#prev_total_collection").text((prevGlobalPaid + prevGlobalFine).toFixed(2));
    });

    $(document).on("keyup", ".prev_weaver", function () {
        let sum = 0;
        $(".prev_weaver").each(function (i) {
            const val = parseFloat($(this).val()) || 0;
            sum += val;
        });
        prevGlobalWeaver = sum;
        $("#prev_set_weaver").text(sum.toFixed(2));
    });

    $(document).on("keyup", ".prev_fine", function () {
        let sum = 0;
        $(".prev_fine").each(function (i) {
            const val = parseFloat($(this).val()) || 0;
            sum += val;
        });
        prevGlobalFine = sum;
        $("#prev_set_fine").text(sum.toFixed(2));
        $("#prev_total_collection").text((prevGlobalPaid + prevGlobalFine).toFixed(2));
    });

    $(document).on("click", "#prev_select_all", function () {
        const checked = $(this).is(":checked");
        $(".prev_record_checkbox").prop("checked", checked);
    });

    $(document).on("click", "#prev_update_selected", function () {
        const selected = $(".prev_record_checkbox:checked");
        if (selected.length === 0) {
            alert("Please select at least one record to update/delete.");
            return;
        }

        const invoiceIDs = [];
        selected.each(function () {
            invoiceIDs.push($(this).val());
        });

        console.log("Selected Invoices:", invoiceIDs);
        // AJAX call can be triggered here
    });

    $(document).on("click", ".prev_update_single", function () {
        const invoiceID = $(this).data("prev-id");
        const mainID = $(this).data("prev-mainid");

        if (confirm("Are you sure you want to delete invoice " + invoiceID + "?")) {
            // Call AJAX to delete
            console.log("Deleting invoiceID:", invoiceID, "mainID:", mainID);
        }
    });
});

$(document).ready(function () {
      $('#prev_add_payment').on('click', function () {
        $('#prev_add_payment').prop('disabled', true).text('Submitting...');
      });
    });

$('#prev_add_payment').on('click', function(e){
    var error = 0;
    var invoicename        = $('#prev_invoicename'); 
    var invoicedescription = $('#prev_invoicedescription'); 
    var invoicenumber      = $('#prev_invoicenumber'); 
    var paymentyear        = $('#prev_paymentyear'); 
    var payment_status     = $('#prev_payment_status'); 
    var payment_type       = $('#prev_payment_type'); 

    // if(invoicename.val() == '') {
    //     invoicename.addClass('errorClass');
    //     error++;
    // } else if(invoicename.val().length > 127) {
    //     invoicename.addClass('errorClass');
    //     error++;
    // } else {
    //     invoicename.removeClass('errorClass');
    // }

    // if(invoicedescription.val().length > 127) {
    //     invoicedescription.addClass('errorClass');
    //     error++;
    // } else {
    //     invoicedescription.removeClass('errorClass');
    // }

    // if(invoicenumber.val() == '') {
    //     invoicenumber.addClass('errorClass');
    //     error++;
    // } else {
    //     invoicenumber.removeClass('errorClass');
    // }

    if(paymentyear.val() == '') {
        paymentyear.addClass('errorClass');
        error++;
    } else if(paymentyear.val().length > 4 || paymentyear.val().length <= 3) {
        paymentyear.addClass('errorClass');
        error++;
    } else {
        paymentyear.removeClass('errorClass');
    }

    var classesID  = <?=$set_classesID?>;
    var studentID  = <?=$set_studentID?>;

    invoicename        = invoicename.val(); 
    invoicedescription = invoicedescription.val(); 
    invoicenumber      = invoicenumber.val(); 
    paymentyear        = paymentyear.val(); 
    payment_status     = payment_status.val(); 
    payment_type       = payment_type.val();

    var isChecked_send_whatsapp = $("#send_whatsapp").is(":checked") ? 1 : 0;

    var paid = $('input[name^=prev_paid]').map(function(){
        return { paidFieldID: this.name , value: this.value };
    }).get();

    var weaver = $('input[name^=prev_weaver]').map(function(){
        return { weaverFieldID: this.name , value: this.value };
    }).get();

    var fine = $('input[name^=prev_fine]').map(function(){
        return { fineFieldID: this.name , value: this.value };
    }).get();

    var created_date = $('#prev_created_date').val();

    // Disable validation for now (enable if needed)
    error = 0;
    var schoolyearID = '<?= $schoolyearID ?? 0?>';

    if(error == 0) {
        $.ajax({
            type: 'POST',
            url: "<?=base_url('global_payment/paymentSend')?>", 
            data: {
                "classesID"        : classesID, 
                "studentID"        : studentID, 
                'invoicename'      : invoicename,
                'invoicedescription': invoicedescription,
                'invoicenumber'    : invoicenumber,
                'paymentyear'      : paymentyear,
                'payment_status'   : payment_status,
                'payment_type'     : payment_type,
                "paid"             : paid,
                "weaver"           : weaver,
                "fine"             : fine,
                "created_date"     : created_date,
                "send_whatsapp"    : isChecked_send_whatsapp,
                "schoolyearID"     : '<?= $schoolyearID ?? 0?>',
                "is_previous_year_amount" : '<?= $prev_schoolyear ?? 0?>'
            },
            dataType: "html",
            success: function(data) {
                var response = jQuery.parseJSON(data);
                if(response.status) {
                    if(response.message){
                        alert(response.message);
                    }
                   location.href = "<?=base_url();?>Global_payment/print_reciept/"+response.studentID+'/'+response.globalLastID + '/' + schoolyearID;
                } else {
                    $('input[name^=prev_paid], input[name^=prev_weaver], input[name^=prev_fine]').toggleClass('errorClass', response.paid);
                    $('#prev_invoicename').toggleClass('errorClass', !!response.invoicename);
                    $('#prev_invoicenumber').toggleClass('errorClass', !!response.invoicenumber);
                    $('#prev_paymentyear').toggleClass('errorClass', !!response.paymentyear);
                    $('#prev_invoicedescription').toggleClass('errorClass', !!response.invoicedescription);
                }
            }
        });  
    }
});

    </script>