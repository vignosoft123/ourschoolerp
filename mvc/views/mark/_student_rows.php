<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (customCompute($students)) {
    $i = (isset($offset) && $offset) ? $offset + 1 : 1; // offset passed optionally
    foreach ($students as $student) { ?>
        <tr>
            <td class="no-export" data-title="<?= $this->lang->line('slno') ?>">
                <?php echo $i; ?>
            </td>
            <td data-title="<?= $this->lang->line('mark_photo') ?>">
                <?= profileproimage($student->photo) ?>
            </td>
            <td  class='excel-only1'>
                <?php echo $student->studentID; ?>
            </td>
            <td data-title="<?= $this->lang->line('mark_name') ?>">
                <?php echo $student->name; ?> ( <?php echo $student->roll; ?>)
            </td>
            <?php
            // Prepare helper maps for quick lookup if you prefer (pluck by student+subject)
            // But to keep exactly your behaviour we loop like original code
            $tot = 0;
            $zero_mark = 0;
            foreach ($subjects as $subject) {
                foreach ($markpercentages as $data) {
                    // find mark for this student-subject-markpercentage from $marks + $markRelations
                    // We will search $marks first (mark rows), then join with markrelation to get the mark value.
                    $found = null;
                    if (customCompute($marks)) {
                        foreach ($marks as $mark) {
                            if ($subject->subjectID == $mark->subjectID && $student->studentID == $mark->studentID) {
                                // find markrelation value for this mark and percentage
                                $all_marks = null;
                                if (customCompute($markRelations)) {
                                    foreach ($markRelations as $mr) {
                                        if ($mr->markID == $mark->markID && $mr->markpercentageID == $data->markpercentageID) {
                                            // if markrelation has 'mark' field (some implementations store it here)
                                            if (isset($mr->mark)) {
                                                $all_marks = $mr;
                                            } else {
                                                // sometimes mark value is in another column - try to query fallback
                                                $all_marks = $mr;
                                            }
                                            break;
                                        }
                                    }
                                }

                                // fallback: $mark row may also include eattendance and total mark in some setups
                                // We'll create $mrk and $exam_absent similarly to original code by attempting DB query if necessary:
                                $mrk = null;
                                $exam_absent = null;

                                // Primary try: check $all_marks->mark if exists
                                if ($all_marks && isset($all_marks->mark)) {
                                    $mrk = $all_marks->mark;
                                } else {
                                    // fallback DB query like your original code (keeps behaviour identical)
                                    $sql = "SELECT mark,eattendance FROM `mark` LEFT JOIN `markrelation` ON `markrelation`.`markID` = `mark`.`markID` WHERE `mark`.`schoolyearID` = ".$mark->schoolyearID." AND `mark`.`examID` = ".$mark->examID." AND `mark`.`classesID` = ".$mark->classesID." and studentId= ".$student->studentID." and markpercentageID =".$data->markpercentageID." and subjectID=".$subject->subjectID;
                                    $row = $this->db->query($sql)->row();
                                    if ($row) {
                                        $mrk = $row->mark;
                                        $exam_absent = isset($row->eattendance) ? $row->eattendance : null;
                                    }
                                }

                                // If exam_absent not set yet, try from $mark object
                                if (($exam_absent === null || $exam_absent === '') && isset($mark->eattendance)) {
                                    $exam_absent = $mark->eattendance;
                                }

                                $readonly = "";
                                $A = "";
                                if ($exam_absent == 'Absent') {
                                    $readonly = "readonly";
                                    $A = "a";
                                }

                                $mrk = (int)$mrk;
                                if ($mrk == 0) ++$zero_mark;
                                $tot += $mrk;

                                echo "<td data-title='$data->markpercentagetype'>";
                                echo "<a href='#' ><i class='fa icon-eattendance pull-left' title='add exam attendance' data-toggle='modal' data-target='#attendance-modal_".$mark->markID."'></i></a>";

                                if($A == 'a'){
                                    echo '<span class="attendance-circle">A</span>';
                                } else {
                                    echo  "<input subj_id = '".$subject->subjectID."'  class='form-control mark input_mark' type='' style='width: 100px !important;' name='".$subject->subjectID."mark-" . $mark->markID . "' id='" . $data->markpercentageID . "' value='" . $mrk . "' min='0' max='" . $subject->max_mark . "' $readonly  />";
                                }

                                echo "</td>";

                                // Render the same attendance modal form as original (keep identical)
                                ?>
                                <form class="form-horizontal" role="form" action="<?=base_url('Mark/saveAttendance');?>" method="post">
                                    <div class="modal fade" id="attendance-modal_<?= $mark->markID?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title">Add Attendance</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="percentage_id" value="<?= $data->markpercentageID?>">
                                                    <div class="<?php echo form_error('to') ? 'form-group has-error' : 'form-group'; ?>">
                                                        <label for="to" class="col-sm-2 control-label">Attendance</label>
                                                        <div class="col-sm-6">
                                                            <select name="attendance" id="attendance" class="form-control">
                                                                <option value="Present">Present</option>
                                                                <option value="Absent">Absent</option>
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="examID" value="<?= $set_exam?>" class="form-control">
                                                        <input type="hidden" name="classesID" value="<?= $set_classes?>" class="form-control">
                                                        <input type="hidden" name="subjectID" value="<?= $subject->subjectID?>" class="form-control">
                                                        <input type="hidden" name="sectionID" value="<?= $set_section?>" class="form-control">
                                                        <input type="hidden" name="studentID" value="<?= $student->studentID?>" class="form-control">
                                                        <input type="hidden" name="markID" value="<?= $mark->markID?>" class="form-control">
                                                        <span class="col-sm-4 control-label" id="to_error"></span>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                                                    <input type="submit" class="btn btn-success" value="Save" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <?php
                                $my_template .= $subject->subject."=".( $mrk ? ($mrk."/".$subject->max_mark) : 'Ab' ).",";
                                $found = true;
                                break; // we found the mark for this subject for this student, go next percentage
                            } // end if matching mark row
                        } // end foreach $marks
                    } // end if customCompute($marks)
                    // If not found we still need to output empty cell to keep table structure
                    if (!$found) {
                        echo "<td data-title='".$data->markpercentagetype."'><input class='form-control mark input_mark' type='' style='width: 100px !important;' value='' disabled /></td>";
                    }
                } // end foreach markpercentages
            } // end foreach subjects

            // total cell
            echo "<td>".$tot."</td>";

            // grade calculation (reuse your logic)
            $out_of = isset($out_of) && $out_of != 0 ? $out_of : 1;
            $percent_cal = ($tot / $out_of) * 100;
            if ($percent_cal >= 95 && $zero_mark == 0) { $grade = "A+"; $gradeClass = "grade-a-plus"; }
            else if ($percent_cal >= 90 && $percent_cal < 95 && $zero_mark == 0) { $grade = "A"; $gradeClass = "grade-a"; }
            else if ($percent_cal >= 80 && $percent_cal < 90 && $zero_mark == 0) { $grade = "B+"; $gradeClass = "grade-b-plus"; }
            else if ($percent_cal >= 70 && $percent_cal < 80 && $zero_mark == 0) { $grade = "B"; $gradeClass = "grade-b"; }
            else if ($percent_cal >= 60 && $percent_cal < 70 && $zero_mark == 0) { $grade = "C+"; $gradeClass = "grade-c-plus"; }
            else if ($percent_cal >= 50 && $percent_cal < 60 && $zero_mark == 0) { $grade = "C"; $gradeClass = "grade-c"; }
            else { $grade = "D"; $gradeClass = "grade-d"; }

            echo "<td><span class='grade-label {$gradeClass}'>$grade</span></td>";

            // rank cell
            $stuID = $student->studentID;
            if (isset($studentResults[$stuID])) {
                $sms_rank = $studentResults[$stuID]['isFail'] ? "Fail" : $studentResults[$stuID]['rank'];
            } else {
                $sms_rank = "-";
            }
            echo "<td>$sms_rank</td>";

            // SMS checkbox cell (keep as original)
            echo '<td><input type="checkbox" st_ids="'.$student->studentID.'" st_names="'.$student->name.'" mobile_no="'.$student->phone.'" exam_name="'.(isset($mark->exam)?$mark->exam:'').'" total_marks="'.$tot.'/'.$out_of.'" marks_template="'.$my_template.'" exam_date="'.(isset($sendExam->date)?$sendExam->date:'').'" marks_grade="'.$grade.' Rank '.$sms_rank.'" sms_rank="'.$sms_rank.'" name="send_sms_marks" id="send_sms_marks" class="checkbox"></td>';

            // All-subject absent button + modal (same as original)
            ?>
            <td>
                <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#attendance-all-modal_<?= $student->studentID?>">
                    All Subjects Absent
                </button>

                <form class="form-horizontal" role="form" action="<?=base_url('Mark/saveAllAttendance');?>" method="post">
                    <div class="modal fade" id="attendance-all-modal_<?= $student->studentID?>">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    <h4 class="modal-title">Mark All Subjects Attendance</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Attendance</label>
                                        <div class="col-sm-6">
                                            <select name="attendance" class="form-control">
                                                <option value="Present">Present</option>
                                                <option value="Absent">Absent</option>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="examID" value="<?= $set_exam?>">
                                    <input type="hidden" name="classesID" value="<?= $set_classes?>">
                                    <input type="hidden" name="sectionID" value="<?= $set_section?>">
                                    <input type="hidden" name="studentID" value="<?= $student->studentID?>">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                                    <input type="submit" class="btn btn-success" value="Save" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    <?php
    $my_template = "";
    $i++;
    } // foreach students
} // if customCompute
?>
