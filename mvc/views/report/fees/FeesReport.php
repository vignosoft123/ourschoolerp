<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

<style>
    .pull-left{padding: top 45px!important;}
</style>

<style>
/* Style the table header */
 .table-container {
      max-height: 400px; /* Set scroll height */
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

     position: sticky;
      top: 0; 
      z-index: 2; /* Keep above scrolling content */

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
            if($fromdate !='' && $todate !='') {
                $pdf_preview_uri = base_url('feesreport/pdf/'.$classesID.'/'.$sectionID.'/'.$studentID.'/'.$feetypeID.'/'.strtotime($fromdate).'/'.strtotime($todate));
            } else {
                $pdf_preview_uri = base_url('feesreport/pdf/'.$classesID.'/'.$sectionID.'/'.$studentID.'/'.$feetypeID);
            }

            if($fromdate !='' && $todate !='') {
                $xml_preview_uri = base_url('feesreport/xlsx/'.$classesID.'/'.$sectionID.'/'.$studentID.'/'.$feetypeID.'/'.strtotime($fromdate).'/'.strtotime($todate));
            } else {
                $xml_preview_uri = base_url('feesreport/xlsx/'.$classesID.'/'.$sectionID.'/'.$studentID.'/'.$feetypeID);
            }

            echo btn_printReport('feesreport', $this->lang->line('report_print'), 'printablediv');
            // echo btn_pdfPreviewReport('feesreport',$pdf_preview_uri, $this->lang->line('report_pdf_preview'));
            // echo btn_xmlReport('feesreport',$xml_preview_uri, $this->lang->line('report_xlsx'));
            // echo btn_sentToMailReport('feesreport', $this->lang->line('report_send_pdf_to_mail'));
        ?>
        <button id="exportButton" class="btn btn-default">Export to Excel</button>
    </div>
</div>

<div class="box">
    <div class="box-header bg-gray">
        <h3 class="box-title text-navy"><i class="fa fa-clipboard"></i>
            <?=$this->lang->line('feesreport_report_for')?> - 
            <?=$this->lang->line('feesreport_fees');?>
        </h3>
    </div><!-- /.box-header -->
    <div id="printablediv">
    <!-- form start -->
        <div class="box-body" style="margin-bottom: 50px;">
            <div class="row">
                <div class="col-sm-12">
                    <?=reportheader($siteinfos, $schoolyearsessionobj)?>
                </div><hr>
                <?php if($classesID >= 0 && $sectionID >= 0) { ?>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-12">
                                     <h5 class="pull-left">
                                    <?php 
                                        echo $this->lang->line('feesreport_class')." : ";
                                        echo isset($classes[$classesID]) ? $classes[$classesID] : $this->lang->line('feesreport_all_class');
                                    ?>
                                </h5>                         
                                <h5 class="pull-right">
                                    <?php
                                       echo $this->lang->line('feesreport_section')." : ";
                                       echo isset($sections[$sectionID]) ? $sections[$sectionID] : $this->lang->line('feesreport_all_section');
                                    ?>
                                </h5>
                            </div>
                        </div>          
                    </div>
                <?php } ?>
                <div class="col-sm-12">
                    <?php 
                        $cash_amount = 0;
                        $cheque_amount = 0;
                        $digital_amount = 0;
                    if(customCompute($getFeesReports)) {
                           
                        ?>
                    <div class="table-container" style="overflow-x: auto; margin-top: 20px; background: #fff; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    <table class="table table-bordered" id="myTable">
    <thead>
        <tr>
            <th><?=$this->lang->line('slno')?></th>
            <th><?= 'Invoice Number'?></th>
            <th><?=$this->lang->line('feesreport_payment_date')?></th>
            <th><?='Cashier Name'; ?></th>
            <th><?=$this->lang->line('feesreport_name')?></th>
            <th>Roll No</th>
            <?php if(!($classesID > 0)) { ?>
                <th><?=$this->lang->line('feesreport_class')?></th>
            <?php } ?>
            <?php if(!($sectionID > 0)) { ?> 
                <th><?=$this->lang->line('feesreport_section')?></th>
            <?php } ?>
            <th><?=$this->lang->line('feesreport_feetype')?></th>
            <th><?=$this->lang->line('feesreport_paid')?></th>
            <th>Payment Type</th>
            <th><?=$this->lang->line('feesreport_weaver')?></th>
            <th><?=$this->lang->line('feesreport_fine')?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $totalPaid = 0;
            $totalWeaver = 0;
            $totalFine = 0;
            $cash_amount = 0;
            $cheque_amount = 0;
            $digital_amount = 0;
            $others_amount = 0;
            $others_detail = [];
            $i = 0;

            foreach($getFeesReports as $getFeesReport) {

                if($getFeesReport->paymenttype == 'Cash'){
                    $cash_amount += $getFeesReport->paymentamount;
                } else if($getFeesReport->paymenttype == 'Cheque' || $getFeesReport->paymenttype == 'Chaque'){
                    $cheque_amount += $getFeesReport->paymentamount;
                } else if($getFeesReport->paymenttype == 'Digital' || $getFeesReport->paymenttype == 'Digita'){
                    $digital_amount += $getFeesReport->paymentamount;
                } else if($getFeesReport->paymenttype == 'Others'){
                    $others_amount += $getFeesReport->paymentamount;
                    $bank = !empty($getFeesReport->payment_other_details) ? $getFeesReport->payment_other_details : 'Unknown';
                    $others_detail[$bank] = ($others_detail[$bank] ?? 0) + $getFeesReport->paymentamount;
                }

                if(isset($weaverandfine[$getFeesReport->paymentID]) &&
                  (($weaverandfine[$getFeesReport->paymentID]->fine !='') || ($weaverandfine[$getFeesReport->paymentID]->weaver !='')) ||
                  $getFeesReport->paymentamount != '') {
                    $i++;
        ?>
        <tr>
            <td data-title="<?=$this->lang->line('slno')?>"><?=$i?></td>
            <td data-title=""><?= '<b>INV-S-'.$getFeesReport->globalpaymentID.'</b>'?></td>
            <td data-title="<?=$this->lang->line('feesreport_payment_date')?>"><?=date('d M Y',strtotime($getFeesReport->paymentdate))?></td>
            <td data-title=""><?=$getFeesReport->uname;?></td>
            <td data-title="<?=$this->lang->line('feesreport_name')?>"><?=isset($students[$getFeesReport->studentID]) ? $students[$getFeesReport->studentID]->srname : '' ?></td>
            <td data-title="Roll No">
                <?=isset($students[$getFeesReport->studentID]) ? $students[$getFeesReport->studentID]->srroll : '' ?>
            </td>

            <?php if(!($classesID > 0)) {
                echo "<td data-title='".$this->lang->line('feesreport_class')."'>";
                if(isset($students[$getFeesReport->studentID])) {
                    $stclassID = $students[$getFeesReport->studentID]->srclassesID;
                    echo isset($classes[$stclassID]) ? $classes[$stclassID] : '';
                } 
                echo "</td>";
            } ?>

            <?php if(!($sectionID > 0)) {
                echo "<td data-title='".$this->lang->line('feesreport_section')."'>";
                if(isset($students[$getFeesReport->studentID])) {
                    $stsectionID = $students[$getFeesReport->studentID]->srsectionID;
                    echo isset($sections[$stsectionID]) ? $sections[$stsectionID] : '';
                } 
                echo "</td>";
            } ?>

            <td data-title="<?=$this->lang->line('feesreport_feetype')?>">
                <?php 
                    if(isset($invoices[$getFeesReport->invoiceID])) {
                        $feetypeIDD = $invoices[$getFeesReport->invoiceID];
                        if(isset($feetypes[$feetypeIDD])) {
                            echo $feetypes[$feetypeIDD];
                        }
                    }
                ?>
            </td>

            <td data-title="<?=$this->lang->line('feesreport_paid')?>">
                <?php 
                    echo number_format($getFeesReport->paymentamount,2);
                    $totalPaid += $getFeesReport->paymentamount;
                    if(!empty($getFeesReport->is_previous_year_amount)){
                ?>
                        <br/> <h6 class="text-purple" > Previous Year(<?= $getFeesReport->is_previous_year_amount?>)</h6>
                <?php } ?>
            </td>
            <td>
                <?php
                $pt = $getFeesReport->paymenttype;
                if ($pt === 'Cash') {
                    $p_class = 'text-green'; $p_label = 'Cash';
                } elseif ($pt === 'Cheque' || $pt === 'Chaque') {
                    $p_class = 'text-navy';  $p_label = 'Cheque';
                } elseif ($pt === 'Digital' || $pt === 'Digita') {
                    $p_class = 'text-blue';  $p_label = 'Digital';
                } elseif ($pt === 'Others') {
                    $p_class = 'text-orange'; $p_label = 'Others';
                    if (!empty($getFeesReport->payment_other_details)) {
                        $p_label .= ' (' . htmlspecialchars($getFeesReport->payment_other_details) . ')';
                    }
                } else {
                    $p_class = ''; $p_label = htmlspecialchars($pt);
                }
                ?>
                <span class="<?= $p_class ?>"><?= $p_label ?></span>
            </td>

            <td data-title="<?=$this->lang->line('feesreport_weaver')?>">
                <?php 
                    if(isset($weaverandfine[$getFeesReport->paymentID])) {
                        echo number_format($weaverandfine[$getFeesReport->paymentID]->weaver,2);
                        $totalWeaver += $weaverandfine[$getFeesReport->paymentID]->weaver; 
                    } else {
                        echo number_format(0,2);
                    }
                ?>
            </td>

            <td data-title="<?=$this->lang->line('feesreport_fine')?>">
                <?php 
                    if(isset($weaverandfine[$getFeesReport->paymentID])) {
                        echo  number_format($weaverandfine[$getFeesReport->paymentID]->fine,2);
                        $totalFine += $weaverandfine[$getFeesReport->paymentID]->fine;
                    } else {
                        echo number_format(0,2);
                    }
                ?>
            </td>
        </tr>
        <?php } } ?>

        <?php
            // Adjust colspan logic based on class/section columns
            $colspan = 7; // base columns now includes Roll No
            if($classesID == 0) $colspan++;
            if($sectionID == 0) $colspan++;
        ?>

        <tr style="font-weight: bold; background:#f8f9fa;">
            <td colspan="<?=$colspan?>"></td>
            <td colspan="4" style="padding:8px 10px; white-space:nowrap;">
                <span class="text-green">Cash:&nbsp;<?=number_format($cash_amount,2)?></span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <span class="text-blue">Digital:&nbsp;<?=number_format($digital_amount,2)?></span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <span class="text-navy">Cheque:&nbsp;<?=number_format($cheque_amount,2)?></span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <span class="text-orange">Others:&nbsp;<?=number_format($others_amount,2)?>
                    <?php if($others_amount > 0): ?>
                    <i class="fa fa-info-circle" id="othersBreakdownIcon"
                       style="cursor:pointer; color:#e67e22; margin-left:4px;"
                       title="View bank breakdown"></i>
                    <?php endif; ?>
                </span>
            </td>
        </tr>

        <tr style="font-weight: bold">
            <td data-title="<?=$this->lang->line('feesreport_grand_total')?>" align="right" colspan="<?=$colspan?>">
                <?=$this->lang->line('feesreport_grand_total')?> <?=isset($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : ''?>
            </td>
            <td data-title="<?=$this->lang->line('feesreport_total_paid')?>"><?=number_format($totalPaid,2)?></td>
            <td data-title="<?=$this->lang->line('feesreport_total_weaver')?>"><?=number_format($totalWeaver,2)?></td>
            <td data-title="<?=$this->lang->line('feesreport_total_fine')?>"><?=number_format($totalFine,2)?></td>
        </tr>
    </tbody>
</table>

                    </div>
                    <?php } else { ?>
                        <br/>
                        <div class="callout callout-danger">
                            <p><b class="text-info"><?=$this->lang->line('feesreport_data_not_found')?></b></p>
                        </div>
                    <?php } ?>
                </div>

<!-- Others Breakdown Modal -->
<div class="modal fade" id="othersBreakdownModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header" style="background:#e67e22; color:#fff; border-radius:4px 4px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-university"></i> Others — Bank Breakdown</h4>
            </div>
            <div class="modal-body" style="padding:0;">
                <table class="table table-bordered table-condensed" style="margin-bottom:0;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            <th>Bank / Detail</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $othersDetailSorted = $others_detail ?? [];
                        arsort($othersDetailSorted);
                        foreach ($othersDetailSorted as $bankName => $bankAmt): ?>
                        <tr>
                            <td><?= htmlspecialchars($bankName) ?></td>
                            <td class="text-right"><?= number_format($bankAmt, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:#fef9e7; font-weight:700;">
                            <td>Total</td>
                            <td class="text-right"><?= number_format($others_amount ?? 0, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).on('click', '#othersBreakdownIcon', function () {
    $('#othersBreakdownModal').modal('show');
});
</script>
                <div class="col-sm-12 text-center footerAll">
                    <?=reportfooter($siteinfos, $schoolyearsessionobj)?>
                </div>
            </div><!-- row -->
        </div><!-- Body -->
    </div>
</div>


<!-- email modal starts here -->
<form class="form-horizontal" role="form" action="<?=base_url('feesreport/send_pdf_to_mail');?>" method="post">
    <div class="modal fade" id="mail">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=$this->lang->line('feesreport_close')?></span></button>
                <h4 class="modal-title"><?=$this->lang->line('feesreport_mail')?></h4>
            </div>
            <div class="modal-body">

                <?php
                    if(form_error('to'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                ?>
                    <label for="to" class="col-sm-2 control-label">
                        <?=$this->lang->line("feesreport_to")?> <span class="text-red">*</span>
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
                        <?=$this->lang->line("feesreport_subject")?> <span class="text-red">*</span>
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
                        <?=$this->lang->line("feesreport_message")?>
                    </label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="message" style="resize: vertical;" name="message" value="<?=set_value('message')?>" ></textarea>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                <input type="button" id="send_pdf" class="btn btn-success" value="<?=$this->lang->line("feesreport_send")?>" />
            </div>
        </div>
      </div>
    </div>
</form>

<script type="text/javascript">
    function printDiv(divID) {
        var oldPage = document.body.innerHTML;
        $('#headerImage').remove();
        $('.footerAll').remove();
        var divElements = document.getElementById(divID).innerHTML;
        var footer = "<center><img src='<?=base_url('uploads/images/'.$siteinfos->photo)?>' style='width:30px;' /></center>";
        var copyright = "<center><?=$siteinfos->footer?> | <?=$this->lang->line('feesreport_hotline')?> : <?=$siteinfos->phone?></center>";
        document.body.innerHTML =
          "<html><head><title></title></head><body>" +
          "<center><img src='<?=base_url('uploads/images/'.$siteinfos->photo)?>' style='width:50px;' /></center>"
          + divElements + footer + copyright + "</body>";

        window.print();
        document.body.innerHTML = oldPage;
        window.location.reload();

    }

    function check_email(email) {
        var status = false;
        var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        if (email.search(emailRegEx) == -1) {
            $("#to_error").html('');
            $("#to_error").html("<?=$this->lang->line('feesreport_mail_valid')?>").css("text-align", "left").css("color", 'red');
        } else {
            status = true;
        }
        return status;
    }

    $('#send_pdf').click(function() {
        var field = {
            'to'         : $('#to').val(), 
            'subject'    : $('#subject').val(), 
            'message'    : $('#message').val(),
            'classesID'  : '<?=$classesID?>',
            'sectionID'  : '<?=$sectionID?>',
            'studentID'  : '<?=$studentID?>',
            'feetypeID'  : '<?=$feetypeID?>',
            'fromdate'   : '<?=$fromdate?>',
            'todate'     : '<?=$todate?>'
        };

        var to = $('#to').val();
        var subject = $('#subject').val();
        var error = 0;

        $("#to_error").html("");
        $("#subject_error").html("");

        if(to == "" || to == null) {
            error++;
            $("#to_error").html("<?=$this->lang->line('feesreport_mail_to')?>").css("text-align", "left").css("color", 'red');
        } else {
            if(check_email(to) == false) {
                error++
            }
        }

        if(subject == "" || subject == null) {
            error++;
            $("#subject_error").html("<?=$this->lang->line('feesreport_mail_subject')?>").css("text-align", "left").css("color", 'red');
        } else {
            $("#subject_error").html("");
        }

        if(error == 0) {
            $('#send_pdf').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: "<?=base_url('feesreport/send_pdf_to_mail')?>",
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
</script>
<script>
    $(document).ready(function () {
        $("#exportButton").click(function () {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
            const dd = String(today.getDate()).padStart(2, '0');

            const todate = `${dd}-${mm}-${yyyy}`;
            const filename = `fee_report_${todate}.xlsx`;

            var table = document.getElementById("myTable");
            var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
            XLSX.writeFile(wb, filename );
        });
    });
</script>
