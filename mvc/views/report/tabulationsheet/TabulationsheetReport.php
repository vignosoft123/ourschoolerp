    <style>
    .tabulation-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 13px;
    }

    .tabulation-table th,
    .tabulation-table td {
        border: 1px solid #ddd;
        padding: 6px;
        text-align: center;
        vertical-align: middle;
    }

    .tabulation-table th {
        background: #3f51b5;   /* blue header */
        color: #fff;
        font-weight: bold;
    }

    .attendance-circle {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #f44336; /* red background */
        color: #fff;
        text-align: center;
        font-size: 12px;
        line-height: 20px;
        font-weight: bold;
    }

    .subject-header {
        background: #e8eaf6;
        font-weight: bold;
    }
</style>
<div class="row">
    <div class="col-sm-12" style="margin:10px 0px">
        <?php
            $pdf_preview_uri = base_url('tabulationsheetreport/pdf/'.$examID.'/'.$classesID.'/'.$sectionID.'/'.$studentID);
            echo btn_printReport('tabulationsheetreport', $this->lang->line('tabulationsheetreport_print'), 'printablediv');
            // echo btn_pdfPreviewReport('tabulationsheetreport',$pdf_preview_uri, $this->lang->line('tabulationsheetreport_pdf_preview'));
            // echo btn_sentToMailReport('tabulationsheetreport', $this->lang->line('tabulationsheetreport_send_pdf_to_mail'));
        ?>
    </div>
</div>
<div class="box">
    <div class="box-header bg-gray">
        <h3 class="box-title text-navy"><i class="fa fa-clipboard"></i> 
        <?=$this->lang->line('tabulationsheetreport_report_for')?> - <?=$this->lang->line('tabulationsheetreport_tabulationsheet')?>
        </h3>
    </div><!-- /.box-header -->
    <div id="printablediv">

        <style type="text/css">
            .maintabulationsheetreport table { 
                text-align: center;
                width: 100%;
                padding: 10px; 
            }

            .maintabulationsheetreport table th {
                padding: 2px;
                border:1px solid #ddd;
                text-align: center;
                font-size: 10px;
                min-height: 40px;
                line-height: 15px;
            }

            .maintabulationsheetreport table td{
                padding: 2px;
                border:1px solid #ddd;
                font-size: 10px;
            }
        </style>
        
        <div class="box-body" style="margin-bottom: 50px;">
            <div class="row">
                <div class="col-sm-12">
                    <?=reportheader($siteinfos, $schoolyearsessionobj)?>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12">
                            

                        <h5 class="pull-left">
                                <?php 
                                    echo $this->lang->line('tabulationsheetreport_class')." : ";
                                    echo isset($classes[$classesID]) ? $classes[$classesID] : $this->lang->line('tabulationsheetreport_all_class');
                                ?>

                                <?php 
                                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exam : ";
                                    echo isset($exams[$examID]->exam) ? $exams[$examID]->exam : 'All Exams'; 
                                ?>
                            </h5>  
                                                
                            <h5 class="pull-right">
                                <?php
                                   echo $this->lang->line('tabulationsheetreport_section')." : ";
                                   echo isset($sections[$sectionID]) ? $sections[$sectionID] : $this->lang->line('tabulationsheetreport_all_section');
                                ?>
                            </h5>                        
                        </div>
                    </div>
                </div>

                <?php if(customCompute($marks)) { ?>
                    <div class="col-sm-12">
                        <div class="maintabulationsheetreport">
                        

<table class="tabulation-table">
    <thead>
        <tr>
            <th>Roll</th>
            <th>Name</th>

            <?php foreach($mandatorysubjects as $mandatorysubject): ?>
                <th colspan="<?=count($markpercentages)?>">
                    <?=$mandatorysubject->subject?>
                </th>
            <?php endforeach; ?>

            <?php if(customCompute($optionalsubjects)): ?>
                <?php foreach($optionalsubjects as $optionalsubject): ?>
                    <th colspan="<?=count($markpercentages)?>">
                        <?=$optionalsubject->subject?>
                    </th>
                <?php endforeach; ?>
            <?php endif; ?>

            <th>Total</th>
        </tr>

        <!-- <tr>
            <th></th>
            <th></th>

            <?php /*foreach($mandatorysubjects as $mandatorysubject): ?>
                <?php foreach($markpercentages as $markpercentageID => $markpercentage): ?>
                    <th><?=$markpercentage?></th>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <?php if(customCompute($optionalsubjects)): ?>
                <?php foreach($optionalsubjects as $optionalsubject): ?>
                    <?php foreach($markpercentages as $markpercentageID => $markpercentage): ?>
                        <th><?=$markpercentage?></th>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; */?>

            <th></th>
        </tr> -->
    </thead>
    <tbody>
        <?php foreach($students as $student): ?>
            <tr>
                <td><?=$student->srroll?></td>
                <td style="text-align: left;"><?=$student->srname?></td>

                <?php 
                    $totl = 0;
                    // --- Mandatory subjects ---
                    foreach($mandatorysubjects as $mandatorysubject) {
                        $subjectTotal = 0;

                        foreach($markpercentages as $markpercentageID => $markpercentage) {
                            echo '<td>';
                            if(isset($marks[$student->srstudentID][$mandatorysubject->subjectID][$markpercentageID])) {
                                $markValue = $marks[$student->srstudentID][$mandatorysubject->subjectID][$markpercentageID];

                                if(is_string($markValue) && strpos($markValue, 'attendance-circle') !== false) {
                                    echo $markValue; // show A
                                    $numericMark = 0;
                                } else {
                                    echo $markValue;
                                    $numericMark = (float)$markValue;
                                }

                                $subjectTotal += $numericMark;
                                $totl += $numericMark;
                            } else {
                                echo 0;
                            }
                            echo '</td>';
                        }
                    }

                    // --- Optional subjects ---
                    if(customCompute($optionalsubjects)) {
                        foreach($optionalsubjects as $optionalsubject) {
                            $optionalSubjectTotal = 0;

                            foreach($markpercentages as $markpercentageID => $markpercentage) {
                                echo '<td>';
                                if(isset($marks[$student->srstudentID][$optionalsubject->subjectID][$markpercentageID])) {
                                    $markValue = $marks[$student->srstudentID][$optionalsubject->subjectID][$markpercentageID];

                                    if(is_string($markValue) && strpos($markValue, 'attendance-circle') !== false) {
                                        echo $markValue;
                                        $numericMark = 0;
                                    } else {
                                        echo $markValue;
                                        $numericMark = (float)$markValue;
                                    }

                                    $optionalSubjectTotal += $numericMark;
                                    $totl += $numericMark;
                                } else {
                                    echo 0;
                                }
                                echo '</td>';
                            }
                        }
                    }
                ?>

                <td style="font-weight: bold;"><?=$totl?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-sm-12">
                        <br>
                        <div class="callout callout-danger">
                            <p><b class="text-info"><?=$this->lang->line('tabulationsheetreport_data_not_found')?></b></p>
                        </div>
                    </div>
                <?php } ?>

                <div class="col-sm-12 text-center footerAll">
                    <?=reportfooter($siteinfos, $schoolyearsessionobj)?>
                </div>
            </div><!-- row -->
        </div>

        <!-- email modal starts here -->
        <form class="form-horizontal" role="form" action="<?=base_url('admitcardreport/send_pdf_to_mail');?>" method="post">
            <div class="modal fade" id="mail">
              <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=$this->lang->line('tabulationsheetreport_close')?></span></button>
                        <h4 class="modal-title"><?=$this->lang->line('tabulationsheetreport_send_pdf_to_mail')?></h4>
                    </div>
                    <div class="modal-body">

                        <?php
                            if(form_error('to'))
                                echo "<div class='form-group has-error' >";
                            else
                                echo "<div class='form-group' >";
                        ?>
                            <label for="to" class="col-sm-2 control-label">
                                <?=$this->lang->line("tabulationsheetreport_to")?> <span class="text-red">*</span>
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
                                <?=$this->lang->line("tabulationsheetreport_subject")?> <span class="text-red">*</span>
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
                                <?=$this->lang->line("tabulationsheetreport_message")?>
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" id="message" style="resize: vertical;" name="message" value="<?=set_value('message')?>" ></textarea>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                        <input type="button" id="send_pdf" class="btn btn-success" value="<?=$this->lang->line("tabulationsheetreport_send")?>" />
                    </div>
                </div>
              </div>
            </div>
        </form>
        <!-- email end here -->
    </div>
</div>

<script type="text/javascript">
    $('.maintabulationsheetreport').mCustomScrollbar({
        axis:"x"
    });

    function check_email(email) {
        var status = false;
        var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        if (email.search(emailRegEx) == -1) {
            $("#to_error").html('');
            $("#to_error").html("<?=$this->lang->line('tabulationsheetreport_mail_valid')?>").css("text-align", "left").css("color", 'red');
        } else {
            status = true;
        }
        return status;
    }

    $('#send_pdf').click(function() {
        var field = {
            'to'          : $('#to').val(), 
            'subject'     : $('#subject').val(), 
            'message'     : $('#message').val(),
            'examID'      : '<?=$examID?>',
            'classesID'   : '<?=$classesID?>',
            'sectionID'   : '<?=$sectionID?>',
            'studentID'   : '<?=$studentID?>'
        };

        var to = $('#to').val();
        var subject = $('#subject').val();
        var error = 0;

        $("#to_error").html("");
        $("#subject_error").html("");

        if(to == "" || to == null) {
            error++;
            $("#to_error").html("<?=$this->lang->line('tabulationsheetreport_mail_to')?>").css("text-align", "left").css("color", 'red');
        } else {
            if(check_email(to) == false) {
                error++
            }
        }

        if(subject == "" || subject == null) {
            error++;
            $("#subject_error").html("<?=$this->lang->line('tabulationsheetreport_mail_subject')?>").css("text-align", "left").css("color", 'red');
        } else {
            $("#subject_error").html("");
        }

        if(error == 0) {
            $('#send_pdf').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: "<?=base_url('tabulationsheetreport/send_pdf_to_mail')?>",
                data: field,
                dataType: "html",
                success: function(data) {
                    var response = JSON.parse(data);
                    if (response.status == false) {
                        $('#send_pdf').removeAttr('disabled');
                        if( response.to) {
                            $("#to_error").html("<?=$this->lang->line('tabulationsheetreport_mail_to')?>").css("text-align", "left").css("color", 'red');
                        } 
                        if( response.subject) {
                            $("#subject_error").html("<?=$this->lang->line('tabulationsheetreport_mail_subject')?>").css("text-align", "left").css("color", 'red');
                        }
                        if(response.message) {
                            toastr["error"](response.message)
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
                    } else {
                        location.reload();
                    }
                }
            });
        }
    });
</script>