<!DOCTYPE html>
<html lang="en">
<!-- <head>
    <meta charset="UTF-8">
    <title>Progress Card</title>
    <style type="text/css">
    .grade-label { padding: 3px 8px; border-radius: 4px; font-weight: bold; display: inline-block; font-size: 13px; }
    .grade-a-plus { background-color: #e6f4ea; color: #2e7d32; }
    .grade-a      { background-color: #e8f5e9; color: #388e3c; }
    .grade-b-plus { background-color: #e3f2fd; color: #0288d1; }
    .grade-b      { background-color: #e1f5fe; color: #039be5; }
    .grade-c-plus { background-color: #fff9c4; color: #fbc02d; }
    .grade-c      { background-color: #ffe0b2; color: #f57c00; }
    .grade-d      { background-color: #ffcdd2; color: #d32f2f; }
    .error{color:red;}
    .row_background {background-color:#dce1ee;}
    .row_absent{background-color: #f8e3e3;}
    .row_head{background-color:#ea893b;}
    .mainprogresscardreport{ margin: 0px auto 10px; padding:55px; max-width:794px; border:1px solid #ddd; min-height: 443px; }
    .progresscard-contents { margin-top: 10px; }
    .progresscard-contents table { width: 100%; border-collapse: collapse; }
    .progresscard-contents table tr,.progresscard-contents table td,.progresscard-contents table th { border:1px solid #ddd; padding: 8px; font-size: 14px; text-align: center; }
    .table-container { width: 100%; margin: 20px 0; border-collapse: collapse; }
    .table-container td, .table-container th { padding: 12px; text-align: left; border: 1px solid #ddd; }
    .table-container th { background-color: #f4f4f4; }
    .text-red { color: #e74c3c; font-weight: bold; }
    .text-green { color: #2ecc71; font-weight: bold; }
    .full-width { width: 100%; }
    @media print { * { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; print-color-adjust: exact !important; } }
    </style>
</head> -->
<body>
<?php if(customCompute($students)) { foreach($students as $student) { ?>
    <div class="mainprogresscardreport">
        <table width="100%" style="text-align:center; border:none;">
            <tr>
                <td style="width:15%; border:none;">
                    <?php if($siteinfos->photo) { ?><img src="<?php echo base_url('uploads/images/'.$siteinfos->photo);?>" style="width:100px; height:auto; border-radius:50%;"><?php } ?>
                </td>
                <td style="width:70%; border:none;">
                    <h2 style="margin:0"><?=$siteinfos->sname?></h2>
                    <div style="margin-top:4px;"><span style="color:#9b00ff; display:block;"><?=$siteinfos->address?></span><span style="color:#0000ff; display:block;"><?= $siteinfos->email?></span></div>
                    <div style="color:#0000ff; margin-top:4px;"><?=$siteinfos->phone?></div>
                </td>
                <td style="width:15%; border:none;">
                    <img src="<?=imagelink($student->photo)?>" style="width:100px; height:auto; border-radius:50%;">
                </td>
            </tr>
        </table>

        <hr style="border:none; border-top:1px solid #ddd; margin:10px 0;">

        <table class="table-container full-width">
            <tr>
                <th class="text-red">Name :</th>
                <td class="text-green"><?= $student->srname ?></td>
                <th class="text-red"><?=$this->lang->line('progresscardreport_reg_no')?> :</th>
                <td class="text-green"><?= $student->srregisterNO ?></td>
            </tr>
            <tr>
                <th class="text-red"><?=$this->lang->line('progresscardreport_class')?> :</th>
                <td class="text-green"><?= isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : '' ?></td>
                <th class="text-red"><?=$this->lang->line('progresscardreport_section')?> :</th>
                <td class="text-green"><?= isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : '' ?></td>
            </tr>
            <tr>
                <th class="text-red"><?=$this->lang->line('progresscardreport_roll_no')?> :</th>
                <td class="text-green"><?= $student->srroll ?></td>
                <th class="text-red">Father Name :</th>
                <td class="text-green"><?= $student->father_name ?></td>
            </tr>
        </table>

        <div class="progresscard-contents">
            <h3 style="text-align:center; margin:10px 0;"><?=isset($exams[$examID]) ? $exams[$examID] : ''?> Progress Card</h3>

            <table>
                <thead>
                    <tr class="row_head">
                        <th><?=$this->lang->line('progresscardreport_subjects')?></th>
                        <?php if(customCompute($settingExam)) { foreach($settingExam as $examID) {
                            $markpercentagesArr  = isset($markpercentagesclassArr[$examID]) ? $markpercentagesclassArr[$examID] : [];
                            reset($markpercentagesArr);
                            $firstindex = key($markpercentagesArr);
                            $uniquepercentageArr = isset($markpercentagesArr[$firstindex]) ? $markpercentagesArr[$firstindex] : [];
                            $markpercentages = $uniquepercentageArr[(($settingmarktypeID==4)||($settingmarktypeID==6)) ? 'unique' : 'own'];
                        ?>
                        <th>Max Marks</th>
                        <th colspan="<?= customCompute($markpercentages) ?>">Obtained Marks</th>
                        <input type="hidden" name="exam_name[]" value="<?=isset($exams[$examID]) ? $exams[$examID] : ''?>">
                        <?php } } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalAllSubjectMark = 0; $marks_template = ''; $totalExMarks = 0; $total_max_marks = 0;
                    if(customCompute($mandatorySubjects)) { foreach($mandatorySubjects as $mandatorySubject) {
                        $totalExMarks += 0; // kept for compatibility
                        $totalSubjectMark = 0; $totalGradeSubjectMark = 0;
                        $total_max_marks += $mandatorySubject->max_mark;
                    ?>
                    <tr>
                        <td class="text-blue"><?=$mandatorySubject->subject?></td>
                        <td class="text-purple"><?=$mandatorySubject->max_mark?></td>
                        <?php if(customCompute($settingExam)) { foreach($settingExam as $examID) {
                            $examTotalSubjectMark = 0;
                            $uniquepercentageArr = isset($markpercentagesclassArr[$examID][$mandatorySubject->subjectID]) ? $markpercentagesclassArr[$examID][$mandatorySubject->subjectID] : [];
                            $markpercentages = [];
                            if(customCompute($uniquepercentageArr)) { $markpercentages = $uniquepercentageArr[(($settingmarktypeID==4)||($settingmarktypeID==6)) ? 'unique' : 'own']; }
                            if(customCompute($markpercentages)) { foreach($markpercentages as $markpercentageID) {
                                $mark = 0;
                                if(isset($markArray[$examID][$student->srstudentID]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID])) {
                                    $mark = $markArray[$examID][$student->srstudentID]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID];
                                }
                                $sql = "select eattendance from mark where studentID = $student->srstudentID and examID = $examID and subjectID = $mandatorySubject->subjectID";
                                $exam_status = $this->db->query($sql)->row()->eattendance;
                                if($exam_status == 'Absent') { echo '<td><span class="text-red">Absent</span></td>'; }
                                else { echo '<td>'.(($mark)?$mark:0).'</td>'; }
                                $totalSubjectMark += $mark; $examTotalSubjectMark += $mark; $marks_template .= substr($mandatorySubject->subject,0,3).'='.$mark.'/'.' ,';
                            } }
                            $totalGradeSubjectMark += markCalculationView($examTotalSubjectMark, $mandatorySubject->max_mark, 0);
                        } } ?>
                    </tr>
                    <?php } } ?>

                    <tr>
                        <td class="text-blue"><b>Total Marks </b></td>
                        <td><b><?=ini_round($total_max_marks)?></b></td>
                        <td><b><?=ini_round($totalAllSubjectMark)?></b></td>
                    </tr>

                    <tr>
                        <td class="text-blue"><b>Percentage</b></td>
                        <td></td>
                        <td><b><?php $prcnt = $total_max_marks != 0 ? ini_round(($totalAllSubjectMark * 100) / $total_max_marks) : 0; echo $prcnt . "%"; ?></b></td>
                    </tr>

                    <tr>
                        <td class="text-blue"><b>Grade</b></td>
                        <td></td>
                        <td>
                            <?php
                                $out_of = $total_max_marks != 0 ? $total_max_marks : 1;
                                $percent_cal = ($totalAllSubjectMark / $out_of) * 100;
                                if ($percent_cal >= 95) { $grade = "A+"; $gradeClass = "grade-a-plus"; }
                                else if ($percent_cal >= 90) { $grade = "A"; $gradeClass = "grade-a"; }
                                else if ($percent_cal >= 80) { $grade = "B+"; $gradeClass = "grade-b-plus"; }
                                else if ($percent_cal >= 70) { $grade = "B"; $gradeClass = "grade-b"; }
                                else if ($percent_cal >= 60) { $grade = "C+"; $gradeClass = "grade-c-plus"; }
                                else if ($percent_cal >= 50) { $grade = "C"; $gradeClass = "grade-c"; }
                                else { $grade = "D"; $gradeClass = "grade-d"; }
                                echo "<span class='grade-label {$gradeClass}'>$grade</span>";
                            ?>
                        </td>
                    </tr>

                    <?php if($is_display_attendance > 0) { ?>
                        <tr><td colspan="3">
                            <h5 class="text-blue"><b>Attendance</b></h5>
                            <?php $months = array('6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec','1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr'); ?>
                            <table style="width:100%; border-collapse:collapse;">
                                <thead><tr><th>Months</th><?php for($m=6;$m<count($months)+6;$m++){ $d_m = ($m>12)?$m-12:$m; ?><th><?= $months[$d_m]?></th><?php } ?></tr></thead>
                                <tbody>
                                    <tr class="row_background"><td class="text-green"><b>Present</b></td><?php for($m=6;$m<count($months)+6;$m++){ $d_m = ($m>12)?$m-12:$m; ?><td><?= $attendance[$d_m][$student->studentID]['present'];?></td><?php } ?></tr>
                                    <tr class="row_absent"><td class="text-red"><b>Absent</b></td><?php for($m=6;$m<count($months)+6;$m++){ $d_m = ($m>12)?$m-12:$m; ?><td><?= $attendance[$d_m][$student->studentID]['absent'];?></td><?php } ?></tr>
                                </tbody>
                            </table>
                        </td></tr>
                    <?php } ?>

                </tbody>
            </table>

            <div style="padding-top:30px; display:flex; justify-content:space-between;">
                <span style="visibility:hidden;"><img src="<?php echo base_url('/uploads/signatures/').$siteinfos->correspondent_signature ?>" style="width:150px;height:50px;"></span>
                <span style="visibility:hidden;"><img src="<?php echo base_url('/uploads/signatures/').$siteinfos->correspondent_signature ?>" style="width:150px;height:50px;"></span>
                <span class="headmaster_signature"><img src="<?php echo base_url('/uploads/signatures/').$siteinfos->correspondent_signature ?>" style="width:150px;height:50px;"></span>
            </div>

            <div style="display:flex; justify-content:space-between; padding-top:20px;">
                <span>Parent's Signature</span>
                <span>Teacher's Signature</span>
                <span>Principal's Signature</span>
            </div>

            <input type="hidden" name="marks_template[]" value="<?php echo $marks_template;?>">
        </div>
    </div>
    <p style="page-break-after: always;">&nbsp;</p>
<?php } } else { ?>
    <div class="callout callout-danger"><p><b class="text-info"><?=$this->lang->line('progresscardreport_data_not_found')?></b></p></div>
<?php } ?>
</body>
</html>