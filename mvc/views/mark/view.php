<?php if(customCompute($profile)) { ?>
    <div class="well">
        <div class="row">
            <div class="col-sm-6">
                <?php if(!permissionChecker('mark_view') && permissionChecker('mark_add')) { echo btn_sm_add('mark/add', $this->lang->line('add_sub_mark')); } ?>

                <button class="btn-cs btn-sm-cs" onclick="javascript:printDiv('printablediv')"><span class="fa fa-print"></span> <?=$this->lang->line('print')?> </button>
                <?=btn_add_pdf('mark/print_preview/'.$profile->studentID."/".$profile->srclassesID, $this->lang->line('pdf_preview'))?>

                <button class="btn-cs btn-sm-cs" data-toggle="modal" data-target="#mail"><span class="fa fa-envelope-o"></span> <?=$this->lang->line('mail')?></button>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
                    <li><a href="<?=base_url("mark/index/$profile->srclassesID")?>"><?=$this->lang->line('menu_mark')?></a></li>
                    <li class="active"><?=$this->lang->line('view')?></li>
                </ol>
            </div>
        </div>
    </div>

    <div id="printablediv">
        <div class="row">
            <div class="col-sm-3">
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <?=profileviewimage($profile->photo)?>
                        <h3 class="profile-username text-center"><?=$profile->name?></h3>
                        <p class="text-muted text-center"><?=$usertype->usertype?></p>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item" style="background-color: #FFF">
                                <b><?=$this->lang->line('mark_registerNO')?></b> <a class="pull-right"><?=$profile->srregisterNO?></a>
                            </li>
                            <li class="list-group-item" style="background-color: #FFF">
                                <b><?=$this->lang->line('mark_roll')?></b> <a class="pull-right"><?=$profile->srroll?></a>
                            </li>
                            <li class="list-group-item" style="background-color: #FFF">
                                <b><?=$this->lang->line('mark_classes')?></b> <a class="pull-right"><?=customCompute($class) ? $class->classes : ''?></a>
                            </li>
                            <li class="list-group-item" style="background-color: #FFF">
                                <b><?=$this->lang->line('mark_section')?></b> <a class="pull-right"><?=customCompute($section) ? $section->section : ''?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-sm-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#mark" data-toggle="tab"><?=$this->lang->line('mark_mark')?></a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="active tab-pane" id="mark">
                            <?php
                                // Style/columns mirror the Mark tab on the student profile page (student/getView.php) —
                                // simple Subject/Max Marks/Mark table + Percentage/Grade/Remarks footer, instead of the
                                // old Obtained/Highest/Point/GPA layout. Exams with no examschedule rows for this
                                // class+section are skipped entirely (they have nothing to show but N/A everywhere).
                                $optionalsubjectID = $profile->sroptionalsubjectID;
                                if(customCompute($marksettings)) {
                                    foreach ($marksettings as $examID => $marksetting) {
                                        if (empty($examScheduleData[$examID])) { continue; }
                                        echo '<div style="border:1px solid #ddd" class="box" id="e'.$examID.'">';
                                            echo '<div class="box-header" style="background-color:#ddedfd;">';
                                                echo '<h3 class="box-title" style="color:#23292F;">';
                                                    echo (isset($exams[$examID]) ? $exams[$examID] : '');
                                                echo '</h3>';
                                            echo '</div>';

                                            echo '<div class="box-body mark-bodyID">';
                                                echo "<table class=\"table table-striped table-bordered\">";
                                                    echo "<thead>";
                                                        echo "<tr>";
                                                            echo "<th class='text-center' style='background-color:#016bd6;color:#fff;' data-title='".$this->lang->line("mark_subject")."'>";
                                                                echo $this->lang->line("mark_subject");
                                                            echo "</th>";
                                                            echo "<th class='text-center' style='background-color:#016bd6;color:#fff;'>Max Marks</th>";
                                                            echo "<th class='text-center' style='background-color:#016bd6;color:#fff;' data-title='".$this->lang->line("mark_mark")."'>";
                                                                echo $this->lang->line("mark_mark");
                                                            echo "</th>";
                                                        echo "</tr>";
                                                    echo "</thead>";
                                                    echo "<tbody>";
                                                    $totalMark           = 0;
                                                    $opmarkpercentageArr = [];
                                                    foreach ($marksetting as $subjectID => $markpercentageArr) {
                                                        if($subjectID == $optionalsubjectID) {
                                                            $opmarkpercentageArr = $markpercentageArr;
                                                        }
                                                        if(!in_array($subjectID, $optionalsubjectArr) && isset($examScheduleData[$examID][$subjectID])) {
                                                            echo "<tr>";
                                                                echo "<td class='text-black' data-title='".$this->lang->line('mark_subject')."'>";
                                                                     echo isset($subjects[$subjectID]) ? $subjects[$subjectID]->subject : '';
                                                                echo "</td>";
                                                                echo "<td class='text-center' data-title='Max Marks'>";
                                                                    echo $examScheduleData[$examID][$subjectID];
                                                                echo "</td>";

                                                                $totalSubjectMark = 0;
                                                                foreach ($markpercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'] as $markpercentageID) {
                                                                    if(isset($marks[$examID][$subjectID][$markpercentageID])) {
                                                                        $totalSubjectMark += $marks[$examID][$subjectID][$markpercentageID];
                                                                    }
                                                                }

                                                                $isAbsent = isset($attendanceData[$examID][$subjectID]) && ($attendanceData[$examID][$subjectID] == 'Absent');
                                                                echo "<td class='text-black' data-title='".$this->lang->line('mark_mark')."'>";
                                                                    if($isAbsent) {
                                                                        echo '<span style="color:#c62828;">Absent</span>';
                                                                        $totalSubjectMark = 0;
                                                                    } else {
                                                                        echo $totalSubjectMark;
                                                                    }
                                                                echo "</td>";
                                                                $totalMark += $totalSubjectMark;
                                                            echo "</tr>";
                                                        }
                                                    }

                                                    if(($optionalsubjectID > 0) && customCompute($opmarkpercentageArr) && isset($examScheduleData[$examID][$optionalsubjectID])) {
                                                        echo "<tr>";
                                                            echo "<td class='text-black' data-title='".$this->lang->line('mark_subject')."'>";
                                                                 echo isset($subjects[$optionalsubjectID]) ? $subjects[$optionalsubjectID]->subject : '';
                                                            echo "</td>";
                                                            echo "<td class='text-center' data-title='Max Marks'>";
                                                                echo $examScheduleData[$examID][$optionalsubjectID];
                                                            echo "</td>";

                                                            $totalSubjectMark = 0;
                                                            foreach ($opmarkpercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'] as $markpercentageID) {
                                                                if(isset($marks[$examID][$optionalsubjectID][$markpercentageID])) {
                                                                    $totalSubjectMark += $marks[$examID][$optionalsubjectID][$markpercentageID];
                                                                }
                                                            }

                                                            $isAbsent = isset($attendanceData[$examID][$optionalsubjectID]) && ($attendanceData[$examID][$optionalsubjectID] == 'Absent');
                                                            echo "<td class='text-black' data-title='".$this->lang->line('mark_mark')."'>";
                                                                if($isAbsent) {
                                                                    echo '<span style="color:#c62828;">Absent</span>';
                                                                    $totalSubjectMark = 0;
                                                                } else {
                                                                    echo $totalSubjectMark;
                                                                }
                                                            echo "</td>";
                                                            $totalMark += $totalSubjectMark;
                                                        echo "</tr>";
                                                    }
                                                    echo "</tbody>";
                                                echo "</table>";

                                                $totalOutOf = array_sum($examScheduleData[$examID]);
                                                $percent    = $totalOutOf > 0 ? round(($totalMark / $totalOutOf) * 100, 2) : 0;

                                                if ($percent >= 95)     { $grade = 'A+'; $gradeClass = 'label-success'; $remarks = 'Excellent';       $remarksClass = 'label-success'; }
                                                elseif ($percent >= 90) { $grade = 'A';  $gradeClass = 'label-success'; $remarks = 'Excellent';       $remarksClass = 'label-success'; }
                                                elseif ($percent >= 80) { $grade = 'B+'; $gradeClass = 'label-primary'; $remarks = 'Very Good';       $remarksClass = 'label-primary'; }
                                                elseif ($percent >= 70) { $grade = 'B';  $gradeClass = 'label-info';    $remarks = 'Good';            $remarksClass = 'label-info'; }
                                                elseif ($percent >= 60) { $grade = 'C+'; $gradeClass = 'label-warning'; $remarks = 'Fair';            $remarksClass = 'label-warning'; }
                                                elseif ($percent >= 50) { $grade = 'C';  $gradeClass = 'label-warning'; $remarks = 'Average';         $remarksClass = 'label-warning'; }
                                                else                    { $grade = 'D';  $gradeClass = 'label-danger';  $remarks = 'Need Improvement'; $remarksClass = 'label-danger'; }

                                                echo '<div class="box-footer st-attendance-info">';
                                                    echo '<div class="footer-item">'.$this->lang->line('mark_total_marks').' : <span class="text-red text-bold">'.ini_round($totalOutOf).'</span>,</div>';
                                                    echo '<div class="footer-item">'.$this->lang->line('mark_total_obtained_marks').' : <span class="text-red text-bold">'.ini_round($totalMark).'</span>,</div>';
                                                    echo '<div class="footer-item">Percentage : <span class="text-red text-bold">'.$percent.'%</span>,</div>';
                                                    echo '<div class="footer-item">Grade : <span class="label '.$gradeClass.'" style="font-size:13px;padding:4px 8px;">'.$grade.'</span></div>';
                                                    echo '<div class="footer-item">Remarks : <span class="label '.$remarksClass.'" style="font-size:13px;padding:4px 8px;">'.$remarks.'</span></div>';
                                                echo '</div>';

                                            echo '</div>';
                                        echo "</div>";
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form class="form-horizontal" role="form" action="<?=base_url('student/send_mail');?>" method="post">
        <div class="modal fade" id="mail">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title"><?=$this->lang->line('mail')?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="to" class="col-sm-2 control-label">
                                <?=$this->lang->line("to")?> <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-6">
                                <input type="email" class="form-control" id="to" name="to" value="<?=set_value('to')?>" >
                            </div>
                            <span class="col-sm-4 control-label" id="to_error">
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="subject" class="col-sm-2 control-label">
                                <?=$this->lang->line("subject")?> <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="subject" name="subject" value="<?=set_value('subject')?>" >
                            </div>
                            <span class="col-sm-4 control-label" id="subject_error">
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="message" class="col-sm-2 control-label">
                                <?=$this->lang->line("message")?>
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" id="message" style="resize: vertical;" name="message" value="<?=set_value('message')?>" ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                        <input type="button" id="send_pdf" class="btn btn-success" value="<?=$this->lang->line("send")?>" />
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script language="javascript" type="text/javascript">
        function printDiv(divID) {
            var divElements = document.getElementById(divID).innerHTML;
            var oldPage     = document.body.innerHTML;
            document.body.innerHTML = "<html><head><title></title></head><body>" + divElements + "</body>";
            window.print();
            document.body.innerHTML = oldPage;
            window.location.reload();
        }

        function closeWindow() {
            location.reload();
        }

        function check_email(email) {
            var status = false;
            var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
            if (email.search(emailRegEx) == -1) {
                $("#to_error").html('');
                $("#to_error").html("<?=$this->lang->line('mail_valid')?>").css("text-align", "left").css("color", 'red');
            } else {
                status = true;
            }
            return status;
        }

        $('#send_pdf').click(function() {
            var to      = $('#to').val();
            var subject = $('#subject').val();
            var message = $('#message').val();
            var id      = "<?=$profile->studentID;?>";
            var set     = "<?=$profile->srclassesID;?>";
            var error   = 0;

            $("#to_error").html("");
            if(to == "" || to == null) {
                error++;
                $("#to_error").html("");
                $("#to_error").html("<?=$this->lang->line('mail_to')?>").css("text-align", "left").css("color", 'red');
            } else {
                if(check_email(to) == false) {
                    error++
                }
            }

            if(subject == "" || subject == null) {
                error++;
                $("#subject_error").html("");
                $("#subject_error").html("<?=$this->lang->line('mail_subject')?>").css("text-align", "left").css("color", 'red');
            } else {
                $("#subject_error").html("");
            }

            if(error == 0) {
                $('#send_pdf').attr('disabled','disabled');
                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mark/send_mail')?>",
                    data: 'to='+ to + '&subject=' + subject + "&id=" + id+ "&message=" + message+ "&set=" + set,
                    dataType: "html",
                    success: function(data) {
                        var response = JSON.parse(data);
                        if (response.status == false) {
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

        $('.mark-bodyID').mCustomScrollbar({
            axis:"x"
        });

    </script>
<?php } ?>
