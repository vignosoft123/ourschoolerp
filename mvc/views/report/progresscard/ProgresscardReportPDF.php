<!DOCTYPE html>
<html lang="en">
<body style="font-family: DejaVu Sans, Arial, sans-serif; font-size:13px; color:#000; margin:0; padding:0;">

<?php if(customCompute($students)) { foreach($students as $student) { ?>
<div style="width:750px; margin:20px auto; padding:30px; border:1px solid #ccc; border-radius:8px;">

    <!-- ===== Header ===== -->
    <table width="100%" style="border:none; text-align:center;">
        <tr>
            <td style="width:15%; border:none;">
                <?php if($siteinfos->photo) { ?>
                    <img src="<?php echo base_url('uploads/images/'.$siteinfos->photo);?>" style="width:90px; height:90px; border-radius:50%;">
                <?php } ?>
            </td>
            <td style="width:70%; border:none;">
                <h2 style="margin:0; font-size:22px;"><?=$siteinfos->sname?></h2>
                <div style="margin-top:4px; color:#555; line-height:1.4;">
                    <span style="display:block; color:#9b00ff;"><?=$siteinfos->address?></span>
                    <span style="display:block; color:#0000ff;"><?=$siteinfos->email?></span>
                    <span style="color:#0000ff;"><?=$siteinfos->phone?></span>
                </div>
            </td>
            <td style="width:15%; border:none;">
                <img src="<?=imagelink($student->photo)?>" style="width:90px; height:90px; border-radius:50%;">
            </td>
        </tr>
    </table>

    <hr style="border:none; border-top:1px solid #aaa; margin:10px 0;">

    <!-- ===== Student Info ===== -->
    <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; margin-top:5px;">
        <tr>
            <th align="left" style="width:25%; background:#f4f4f4; border:1px solid #ddd;">Name :</th>
            <td style="border:1px solid #ddd;"><?= $student->srname ?></td>
            <th align="left" style="width:25%; background:#f4f4f4; border:1px solid #ddd;"><?=$this->lang->line('progresscardreport_reg_no')?> :</th>
            <td style="border:1px solid #ddd;"><?= $student->srregisterNO ?></td>
        </tr>
        <tr>
            <th align="left" style="background:#f4f4f4; border:1px solid #ddd;"><?=$this->lang->line('progresscardreport_class')?> :</th>
            <td style="border:1px solid #ddd;"><?= isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : '' ?></td>
            <th align="left" style="background:#f4f4f4; border:1px solid #ddd;"><?=$this->lang->line('progresscardreport_section')?> :</th>
            <td style="border:1px solid #ddd;"><?= isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : '' ?></td>
        </tr>
        <tr>
            <th align="left" style="background:#f4f4f4; border:1px solid #ddd;"><?=$this->lang->line('progresscardreport_roll_no')?> :</th>
            <td style="border:1px solid #ddd;"><?= $student->srroll ?></td>
            <th align="left" style="background:#f4f4f4; border:1px solid #ddd;">Father Name :</th>
            <td style="border:1px solid #ddd;"><?= $student->father_name ?></td>
        </tr>
    </table>

    <!-- ===== Title ===== -->
    <h3 style="text-align:center; margin:15px 0; font-size:18px; color:#444;">
        <?=isset($exams[$examID]) ? $exams[$examID] : ''?> Progress Card
    </h3>

    <!-- ===== Marks Table ===== -->
    <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; text-align:center;">
        <thead>
            <tr style="background:#ea893b; color:#fff;">
                <th style="border:1px solid #ddd;">Subject</th>
                <th style="border:1px solid #ddd;">Max Marks</th>
                <?php if(customCompute($settingExam)) { foreach($settingExam as $examID) {
                    $markpercentagesArr  = isset($markpercentagesclassArr[$examID]) ? $markpercentagesclassArr[$examID] : [];
                    reset($markpercentagesArr);
                    $firstindex = key($markpercentagesArr);
                    $uniquepercentageArr = isset($markpercentagesArr[$firstindex]) ? $markpercentagesArr[$firstindex] : [];
                    $markpercentages = $uniquepercentageArr[(($settingmarktypeID==4)||($settingmarktypeID==6)) ? 'unique' : 'own'];
                ?>
                <th colspan="<?=customCompute($markpercentages)?>" style="border:1px solid #ddd;">Obtained Marks</th>
                <?php } } ?>
            </tr>
        </thead>

        <tbody>
        <?php 
        $totalAllSubjectMark = 0; 
        $total_max_marks = 0;

        if(customCompute($mandatorySubjects)) {
            foreach($mandatorySubjects as $mandatorySubject) {
                $totalSubjectMark = 0; 
                $total_max_marks += $mandatorySubject->max_mark;
        ?>
        <tr style="background:#f9f9f9;">
            <td style="border:1px solid #ddd; text-align:left;"><?= $mandatorySubject->subject ?></td>
            <td style="border:1px solid #ddd;"><?= $mandatorySubject->max_mark ?></td>

            <?php 
            if(customCompute($settingExam)) {
                foreach($settingExam as $examID) {
                    $uniquepercentageArr = isset($markpercentagesclassArr[$examID][$mandatorySubject->subjectID]) ? $markpercentagesclassArr[$examID][$mandatorySubject->subjectID] : [];
                    $markpercentages = [];
                    if(customCompute($uniquepercentageArr)) {
                        $markpercentages = $uniquepercentageArr[(($settingmarktypeID==4)||($settingmarktypeID==6)) ? 'unique' : 'own'];
                    }

                    $percentageMark = 0;
                    if(customCompute($markpercentages)) { 
                        foreach($markpercentages as $markpercentageID) {
                            $mark = isset($markArray[$examID][$student->srstudentID]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID])
                                ? $markArray[$examID][$student->srstudentID]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID] : 0;

                            $sql = "SELECT eattendance FROM mark WHERE studentID = $student->srstudentID AND examID = $examID AND subjectID = $mandatorySubject->subjectID";
                            $exam_status = $this->db->query($sql)->row()->eattendance ?? '';

                            echo '<td style="border:1px solid #ddd;">';
                            if($exam_status == 'Absent') {
                                echo '<span style="color:#d32f2f;">Absent</span>';
                                $mark = 0;
                            } else {
                                echo $mark;
                            }
                            echo '</td>';
                            $totalSubjectMark += $mark;
                        }
                    }
                }
            }
            $totalAllSubjectMark += $totalSubjectMark;
            ?>
        </tr>
        <?php } } ?>

        <!-- Totals -->
        <tr style="background:#f4f4f4; font-weight:bold;">
            <td style="border:1px solid #ddd;">Total Marks</td>
            <td style="border:1px solid #ddd;"><?=ini_round($total_max_marks)?></td>
            <td style="border:1px solid #ddd;"><?=ini_round($totalAllSubjectMark)?></td>
        </tr>

        <tr style="background:#f9f9f9;">
            <td style="border:1px solid #ddd;">Percentage</td>
            <td style="border:1px solid #ddd;"></td>
            <td style="border:1px solid #ddd;">
                <?php 
                    $prcnt = $total_max_marks != 0 ? ini_round(($totalAllSubjectMark * 100) / $total_max_marks) : 0;
                    echo $prcnt . "%";
                ?>
            </td>
        </tr>

        <tr style="background:#f4f4f4;">
            <td style="border:1px solid #ddd;">Grade</td>
            <td style="border:1px solid #ddd;"></td>
            <td style="border:1px solid #ddd;">
                <?php
                    $percent_cal = ($total_max_marks != 0) ? ($totalAllSubjectMark / $total_max_marks) * 100 : 0;
                    if ($percent_cal >= 95) { $grade = "A+"; $bg="#c8e6c9"; $color="#2e7d32"; }
                    else if ($percent_cal >= 90) { $grade = "A"; $bg="#dcedc8"; $color="#388e3c"; }
                    else if ($percent_cal >= 80) { $grade = "B+"; $bg="#bbdefb"; $color="#0288d1"; }
                    else if ($percent_cal >= 70) { $grade = "B"; $bg="#b3e5fc"; $color="#039be5"; }
                    else if ($percent_cal >= 60) { $grade = "C+"; $bg="#fff9c4"; $color="#fbc02d"; }
                    else if ($percent_cal >= 50) { $grade = "C"; $bg="#ffe0b2"; $color="#f57c00"; }
                    else { $grade = "D"; $bg="#ffcdd2"; $color="#d32f2f"; }
                    echo "<span style='background:$bg; color:$color; padding:4px 10px; border-radius:4px; font-weight:bold;'>$grade</span>";
                ?>
            </td>
        </tr>

        <tr style="background:#f9f9f9;">
            <td style="border:1px solid #ddd;">Remarks</td>
            <td colspan="2" style="border:1px solid #ddd;">
                <?php 
                    if ($prcnt >= 90) echo "<span style='color: green; font-weight:bold;'>Excellent</span>";
                    elseif ($prcnt >= 80) echo "<span style='color: #28a745; font-weight:bold;'>Very Good</span>";
                    elseif ($prcnt >= 70) echo "<span style='color: #007bff; font-weight:bold;'>Good</span>";
                    elseif ($prcnt >= 60) echo "<span style='color: #ffc107; font-weight:bold;'>Fair</span>";
                    elseif ($prcnt >= 50) echo "<span style='color: orange; font-weight:bold;'>Average</span>";
                    else echo "<span style='color: red; font-weight:bold;'>Needs Improvement</span>";
                ?>
            </td>
        </tr>
        </tbody>
    </table>

    <!-- ===== Signatures ===== -->
    <div style="margin-top:40px; display:flex; justify-content:space-between; text-align:center;">
        <span>Parent's Signature</span>
        <span>Teacher's Signature</span>
        <span>Principal's Signature</span>
    </div>
</div>

<p style="page-break-after: always;">&nbsp;</p>
<?php } } else { ?>
<div style="text-align:center; color:red; padding:20px;">No Data Found</div>
<?php } ?>

</body>
</html>
