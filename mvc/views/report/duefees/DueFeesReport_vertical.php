 

<style>
/* Style the table header */
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

/* Optional: sticky headers when scrolling */
#myTable thead th {
    position: sticky;
    top: 0;
    z-index: 2;
}
</style>


<div class="row">
    <div class="col-sm-12" style="margin:10px 0px">
        <?php
            if($fromdate !='' && $todate !='') {
                $pdf_preview_uri = base_url('duefeesreport/pdf/'.$classesID.'/'.$sectionID.'/'.$studentID.'/'.$feetypeID.'/'.strtotime($fromdate).'/'.strtotime($todate));
            } else {
                $pdf_preview_uri = base_url('duefeesreport/pdf/'.$classesID.'/'.$sectionID.'/'.$studentID.'/'.$feetypeID);
            }

            if($fromdate !='' && $todate !='') {
                $xml_preview_uri = base_url('duefeesreport/xlsx/'.$classesID.'/'.$sectionID.'/'.$studentID.'/'.$feetypeID.'/'.strtotime($fromdate).'/'.strtotime($todate));
            } else {
                $xml_preview_uri = base_url('duefeesreport/xlsx/'.$classesID.'/'.$sectionID.'/'.$studentID.'/'.$feetypeID);
            }

            echo btn_printReport('duefeesreport', $this->lang->line('report_print'), 'printablediv');
            // echo btn_pdfPreviewReport('duefeesreport',$pdf_preview_uri, $this->lang->line('report_pdf_preview'));
            echo btn_xmlReport('duefeesreport',$xml_preview_uri, $this->lang->line('report_xlsx'));
            // echo btn_sentToMailReport('duefeesreport', $this->lang->line('report_send_pdf_to_mail'));
        ?>
 

        <button id="exportBtn" class="btn btn-default">Download Excel - Vertical View</button>



    </div>
</div>

<div class="box">
    <div class="box-header bg-gray">
        <h3 class="box-title text-navy"><i class="fa fa-clipboard"></i>
            <?=$this->lang->line('duefeesreport_report_for')?> - 
            <?=$this->lang->line('duefeesreport_duefees');?>
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
                                        echo $this->lang->line('duefeesreport_class')." : ";
                                        echo isset($classes[$classesID]) ? $classes[$classesID] : $this->lang->line('duefeesreport_all_class');
                                    ?>
                                </h5>                         
                                <h5 class="pull-right">
                                    <?php
                                       echo $this->lang->line('duefeesreport_section')." : ";
                                       echo isset($sections[$sectionID]) ? $sections[$sectionID] : $this->lang->line('duefeesreport_all_section');
                                    ?>
                                </h5>
                            </div>
                        </div>
                    </div>
                <?php } 
                if(customCompute($getDueFeesReports)) { ?>
                    <div class="col-sm-12">
                        <div id="hide-table">
                        <?php
// Step 1: Collect all unique fee types
$allFeeTypes = [];
foreach($getDueFeesReports as $report) {
    $allFeeTypes[$report->feetypeID] = $feetypes[$report->feetypeID];
}

// Step 2: Pivot data by student and invoice date
$pivotData = [];

// echo "<pre>";print_r($students);die;
foreach($getDueFeesReports as $report) {
    $studentID = $report->studentID;
    $invoiceDate = date('Y-m-d', strtotime($report->create_date)); // Group by invoice date
    $key = $studentID . '-' . $invoiceDate;

    // Initialize if not already set
    if (!isset($pivotData[$key])) {
        $pivotData[$key] = [
            'student' => isset($students[$studentID]) ? $students[$studentID] : null,
            'invoice_date' => $invoiceDate,
            'fees' => [],
        ];
    }

    // $discount = ($report->amount / 100) * $report->discount;
    $discount =  $report->discount;
    $paidAmount = isset($getFeesReports[$report->invoiceID]) ? $getFeesReports[$report->invoiceID] : 0;
    $due = ($report->amount - $paidAmount - $discount);

    // Sum the due amount under the corresponding fee type
    $pivotData[$key]['fees'][$report->feetypeID] = isset($pivotData[$key]['fees'][$report->feetypeID]) ? $pivotData[$key]['fees'][$report->feetypeID] + $due : $due;
}
?>

<table class="table table-bordered table-responsive" id="myTable">
    <thead>
        <tr>
            <th><?=$this->lang->line('slno')?></th>
            <th><?=$this->lang->line('duefeesreport_invoice_date')?></th>
            <th><?=$this->lang->line('duefeesreport_name')?></th>
            <th>Father</th>
            <th>Phone</th>
            <th><?=$this->lang->line('duefeesreport_registerNO')?></th>
            <th><?=$this->lang->line('duefeesreport_roll')?></th>

            <?php if($classesID == 0) echo '<th>'.$this->lang->line('duefeesreport_class').'</th>'; ?>
            <?php if($sectionID == 0) echo '<th>'.$this->lang->line('duefeesreport_section').'</th>'; ?>

            <?php foreach($allFeeTypes as $feetypeName): ?>
                <th><?=$feetypeName?></th>
            <?php endforeach; ?>
            <th><?=$this->lang->line('duefeesreport_total_due')?></th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; $grandTotal = 0; ?>
        <?php foreach($pivotData as $key => $entry): ?>
            <tr>
                <td><?=$i++?></td>
                <td><?=date('d M Y', strtotime($entry['invoice_date']))?></td>
                <td><?=$entry['student'] ? $entry['student']->srname : ''?></td>
                <td><?=$entry['student'] ? $entry['student']->father_name : ''?></td>
                <td><?=$entry['student'] ? $entry['student']->phone : ''?></td>

                <td><?=$entry['student'] ? $entry['student']->srregisterNO : ''?></td>
                <td><?=$entry['student'] ? $entry['student']->srroll : ''?></td>

                <?php if($classesID == 0): ?>
                    <td>
                        <?php 
                            $cid = $entry['student'] ? $entry['student']->srclassesID : 0;
                            echo isset($classes[$cid]) ? $classes[$cid] : '';
                        ?>
                    </td>
                <?php endif; ?>

                <?php if($sectionID == 0): ?>
                    <td>
                        <?php 
                            $sid = $entry['student'] ? $entry['student']->srsectionID : 0;
                            echo isset($sections[$sid]) ? $sections[$sid] : '';
                        ?>
                    </td>
                <?php endif; ?>

                <?php 
                    $totalDue = 0;
                    // Display all fee types in separate columns
                    foreach($allFeeTypes as $feetypeID => $feetypeName) {
                        $due = isset($entry['fees'][$feetypeID]) ? $entry['fees'][$feetypeID] : 0;
                        echo "<td>".number_format($due,2)."</td>";
                        $totalDue += $due;
                    }
                    $grandTotal += $totalDue;
                ?>
                <td><strong><?=number_format($totalDue, 2)?></strong></td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="<?=5 + ($classesID == 0 ? 1 : 0) + ($sectionID == 0 ? 1 : 0) + count($allFeeTypes)?>" class="text-right text-bold">
                <?=$this->lang->line('duefeesreport_grand_total')?> <?=!empty($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : '' ?>
            </td>
            <td class="text-bold"><?=number_format($grandTotal,2)?></td>
        </tr>
    </tbody>
</table>

                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-sm-12">
                        <br/>
                        <div class="callout callout-danger">
                            <p><b class="text-info"><?=$this->lang->line('duefeesreport_data_not_found')?></b></p>
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
<form class="form-horizontal" role="form" action="<?=base_url('feesreport/send_pdf_to_mail');?>" method="post">
    <div class="modal fade" id="mail">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=$this->lang->line('duefeesreport_close')?></span></button>
                <h4 class="modal-title"><?=$this->lang->line('duefeesreport_mail')?></h4>
            </div>
            <div class="modal-body">

                <?php
                    if(form_error('to'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                ?>
                    <label for="to" class="col-sm-2 control-label">
                        <?=$this->lang->line("duefeesreport_to")?> <span class="text-red">*</span>
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
                        <?=$this->lang->line("duefeesreport_subject")?> <span class="text-red">*</span>
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
                        <?=$this->lang->line("duefeesreport_message")?>
                    </label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="message" style="resize: vertical;" name="message" value="<?=set_value('message')?>" ></textarea>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                <input type="button" id="send_pdf" class="btn btn-success" value="<?=$this->lang->line("duefeesreport_send")?>" />
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
            $("#to_error").html("<?=$this->lang->line('duefeesreport_mail_valid')?>").css("text-align", "left").css("color", 'red');
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
            'schoolyearID': '<?=$schoolyearID?>',
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
            $("#to_error").html("<?=$this->lang->line('duefeesreport_mail_to')?>").css("text-align", "left").css("color", 'red');
        } else {
            if(check_email(to) == false) {
                error++
            }
        }

        if(subject == "" || subject == null) {
            error++;
            $("#subject_error").html("<?=$this->lang->line('duefeesreport_mail_subject')?>").css("text-align", "left").css("color", 'red');
        } else {
            $("#subject_error").html("");
        }

        if(error == 0) {
            $('#send_pdf').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: "<?=base_url('duefeesreport/send_pdf_to_mail')?>",
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

 
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>


<script>
        $(document).ready(function () {
            $("#exportBtn").click(function () {
                var table = document.getElementById("myTable");
                var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
                XLSX.writeFile(wb, "DueFeeReport.xlsx");
            });
        });
    </script>

