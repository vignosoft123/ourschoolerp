<!DOCTYPE html>
<html lang="en">
<body style="font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#000; margin:0; padding:0;">

<?php if(customCompute($students)) { foreach($students as $student) { ?>
<div style="width:750px; margin:15px auto; padding:20px; border:1px solid #1a237e; border-radius:8px;">

    <!-- ===== Header ===== -->
    <table width="100%" style="border:none;">
        <tr>
            <td style="width:15%; border:none; text-align:center;">
                <?php if($siteinfos->photo) { ?>
                    <img src="<?php echo base_url('uploads/images/'.$siteinfos->photo);?>" style="width:70px; height:70px; border-radius:50%;">
                <?php } ?>
            </td>
            <td style="width:55%; border:none;">
                <h2 style="margin:0; font-size:18px; color:#1a237e;"><?=$siteinfos->sname?></h2>
                <div style="margin-top:2px; color:#555; font-size:11px; line-height:1.4;">
                    <span style="display:block;"><?=$siteinfos->address?></span>
                    <span style="display:block;"><?=$siteinfos->email?> | <?=$siteinfos->phone?></span>
                </div>
            </td>
            <td style="width:15%; border:none; text-align:center;">
                <img src="<?=imagelink($student->photo)?>" style="width:70px; height:70px; border-radius:50%;">
            </td>
            <td style="width:15%; border:none;">
                <table width="100%" style="background:#1a237e; color:#fff; border-radius:4px;" cellpadding="4">
                    <tr><td style="border:none; font-size:9px; color:#fff;">ACADEMIC YEAR</td></tr>
                    <tr><td style="border:none; font-size:12px; font-weight:bold; color:#fff;"><?=isset($schoolyearsessionobj->schoolyear) ? $schoolyearsessionobj->schoolyear : ''?></td></tr>
                    <tr><td style="border:none; font-size:9px; color:#fff;">Exam: <?=isset($exams[$examID]) ? $exams[$examID] : ''?></td></tr>
                    <tr><td style="border:none; font-size:9px; color:#fff;">Teacher: <?=$classTeacher ? $classTeacher : '-'?></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" style="border:none; background:#fff3e0; margin-top:6px;">
        <tr><td style="border:none; text-align:center; color:#e65100; font-weight:bold; padding:6px;">
            <?=isset($exams[$examID]) ? strtoupper($exams[$examID]) : ''?> PROGRESS CARD REPORT
        </td></tr>
    </table>

    <!-- ===== Student Info + Result Summary ===== -->
    <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse:collapse; margin-top:8px;">
        <tr>
            <th align="left" style="width:20%; background:#f4f4f4; border:1px solid #ddd;">Name :</th>
            <td style="width:30%; border:1px solid #ddd;"><?= $student->srname ?></td>
            <th align="left" style="width:20%; background:#f4f4f4; border:1px solid #ddd;">Reg No :</th>
            <td style="width:30%; border:1px solid #ddd;"><?= $student->srregisterNO ?></td>
        </tr>
        <tr>
            <th align="left" style="background:#f4f4f4; border:1px solid #ddd;">Class :</th>
            <td style="border:1px solid #ddd;"><?= isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : '' ?></td>
            <th align="left" style="background:#f4f4f4; border:1px solid #ddd;">Section :</th>
            <td style="border:1px solid #ddd;"><?= isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : '' ?></td>
        </tr>
        <tr>
            <th align="left" style="background:#f4f4f4; border:1px solid #ddd;">Roll No :</th>
            <td style="border:1px solid #ddd;"><?= $student->srroll ?></td>
            <th align="left" style="background:#f4f4f4; border:1px solid #ddd;">Father Name :</th>
            <td style="border:1px solid #ddd;"><?= $student->father_name ?></td>
        </tr>
    </table>

    <?php
        $totalMaxMarks = 0;
        $totalObtained = 0;
        $subjectRows   = [];

        if(customCompute($mandatorySubjects)) {
            foreach($mandatorySubjects as $subj) {
                $totalMaxMarks += $subj->max_mark;
                $subjTotal = 0;
                $isAbsent  = false;

                if(customCompute($settingExam)) {
                    foreach($settingExam as $seExamID) {
                        $uniquepercentageArr = isset($markpercentagesclassArr[$seExamID][$subj->subjectID]) ? $markpercentagesclassArr[$seExamID][$subj->subjectID] : [];
                        $markpercentages = [];
                        if(customCompute($uniquepercentageArr)) {
                            $markpercentages = $uniquepercentageArr[(($settingmarktypeID==4)||($settingmarktypeID==6)) ? 'unique' : 'own'];
                        }
                        if(customCompute($markpercentages)) {
                            foreach($markpercentages as $markpercentageID) {
                                $subjTotal += isset($markArray[$seExamID][$student->srstudentID]['markpercentageMark'][$subj->subjectID][$markpercentageID])
                                    ? $markArray[$seExamID][$student->srstudentID]['markpercentageMark'][$subj->subjectID][$markpercentageID] : 0;
                            }
                        }
                        if(isset($eattendanceArray[$seExamID][$student->srstudentID][$subj->subjectID]) && $eattendanceArray[$seExamID][$student->srstudentID][$subj->subjectID] == 'Absent') {
                            $isAbsent  = true;
                            $subjTotal = 0;
                        }
                    }
                }

                $subjectRows[] = array('subject' => $subj->subject, 'max' => $subj->max_mark, 'obtained' => $subjTotal, 'absent' => $isAbsent);
                $totalObtained += $subjTotal;
            }
        }

        $percent   = $totalMaxMarks > 0 ? round(($totalObtained / $totalMaxMarks) * 100, 2) : 0;
        $gradeInfo = progresscard_resolve_grade($percent);
        $attInfo   = isset($attendanceByStudent[$student->srstudentID]) ? $attendanceByStudent[$student->srstudentID] : array('months'=>[], 'totalWorkingDays'=>0,'totalPresent'=>0,'totalAbsent'=>0,'yearlyPercentage'=>0,'schoolyear'=>'');

        if ($percent >= 90) { $remarkText = 'Excellent'; $remarkColor = '#2e7d32'; }
        elseif ($percent >= 80) { $remarkText = 'Very Good'; $remarkColor = '#28a745'; }
        elseif ($percent >= 70) { $remarkText = 'Good'; $remarkColor = '#007bff'; }
        elseif ($percent >= 60) { $remarkText = 'Fair'; $remarkColor = '#ffc107'; }
        elseif ($percent >= 50) { $remarkText = 'Average'; $remarkColor = '#fb8c00'; }
        else { $remarkText = 'Need Improvement'; $remarkColor = '#e53935'; }

        $qrFilename = 'pc-'.$student->srstudentID.'-'.$examID;
        $qrText     = base_url('progresscardreport/verify/'.$student->srregisterNO);
        $qrFilepath = FCPATH.'uploads/progresscardQRcode/'.$qrFilename.'.png';
        if(!file_exists($qrFilepath)) {
            generate_qrcode($qrText, $qrFilename, 'progresscardQRcode');
        }
        $qrUrl = base_url('uploads/progresscardQRcode/'.$qrFilename.'.png');
    ?>

    <!-- ===== Result Summary ===== -->
    <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; margin-top:8px; text-align:center;">
        <tr style="background:#1a237e; color:#fff;">
            <th style="border:1px solid #ddd;">Total Marks</th>
            <th style="border:1px solid #ddd;">Percentage</th>
            <th style="border:1px solid #ddd;">Grade</th>
            <th style="border:1px solid #ddd;">Rank (Class)</th>
            <th style="border:1px solid #ddd;">Attendance</th>
        </tr>
        <tr>
            <td style="border:1px solid #ddd;"><?=ini_round($totalObtained)?>/<?=ini_round($totalMaxMarks)?></td>
            <td style="border:1px solid #ddd;"><?=$percent?>%</td>
            <td style="border:1px solid #ddd;"><span style="background:<?=$gradeInfo['bg']?>;color:<?=$gradeInfo['color']?>;padding:2px 8px;border-radius:4px;font-weight:bold;"><?=$gradeInfo['grade']?></span></td>
            <td style="border:1px solid #ddd;"><?=($student->rank !== null && $student->rank !== '') ? $student->rank : '-'?></td>
            <td style="border:1px solid #ddd;"><?=$attInfo['yearlyPercentage']?>%</td>
        </tr>
    </table>

    <!-- ===== Subject Wise Performance ===== -->
    <h4 style="margin:12px 0 4px; color:#1a237e;">Subject Wise Performance</h4>
    <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; text-align:center;">
        <tr style="background:#ea893b; color:#fff;">
            <th style="border:1px solid #ddd;">Subject</th>
            <th style="border:1px solid #ddd;">Max Marks</th>
            <th style="border:1px solid #ddd;">Obtained</th>
            <th style="border:1px solid #ddd;">Grade</th>
        </tr>
        <?php foreach($subjectRows as $row) {
            $subjPct   = $row['max'] > 0 ? ($row['obtained'] / $row['max']) * 100 : 0;
            $subjGrade = progresscard_resolve_grade($subjPct);
        ?>
        <tr style="background:#f9f9f9;">
            <td style="border:1px solid #ddd; text-align:left;"><?=$row['subject']?></td>
            <td style="border:1px solid #ddd;"><?=$row['max']?></td>
            <td style="border:1px solid #ddd;"><?=$row['absent'] ? '<span style="color:#c62828;">Absent</span>' : ini_round($row['obtained'])?></td>
            <td style="border:1px solid #ddd;"><?=$row['absent'] ? '-' : $subjGrade['grade']?></td>
        </tr>
        <?php } ?>
    </table>

    <!-- ===== Grade Scale + Class Performance ===== -->
    <table width="100%" style="border:none; margin-top:10px;">
        <tr>
            <td style="width:55%; vertical-align:top; border:none;">
                <h4 style="margin:0 0 4px; color:#1a237e;">Grade Scale</h4>
                <table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse; text-align:center;">
                    <tr style="background:#eceff1;"><th style="border:1px solid #ddd;">Range</th><th style="border:1px solid #ddd;">Grade</th><th style="border:1px solid #ddd;">Remark</th></tr>
                    <?php foreach(progresscard_grade_scale() as $gs) { ?>
                    <tr><td style="border:1px solid #ddd;"><?=$gs['range']?></td><td style="border:1px solid #ddd;"><?=$gs['grade']?></td><td style="border:1px solid #ddd;"><?=$gs['label']?></td></tr>
                    <?php } ?>
                </table>
            </td>
            <td style="width:45%; vertical-align:top; border:none; padding-left:10px;">
                <h4 style="margin:0 0 4px; color:#1a237e;">Class Performance</h4>
                <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; text-align:center;">
                    <tr style="background:#eceff1;"><th style="border:1px solid #ddd;">Your %</th><th style="border:1px solid #ddd;">Class Avg</th><th style="border:1px solid #ddd;">Strength</th></tr>
                    <tr>
                        <td style="border:1px solid #ddd;"><?=$percent?>%</td>
                        <td style="border:1px solid #ddd;"><?=round($classPerf['classAveragePct'], 2)?>%</td>
                        <td style="border:1px solid #ddd;"><?=$classPerf['totalStudents']?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ===== Attendance Summary ===== -->
    <h4 style="margin:12px 0 4px; color:#1a237e;">Attendance Summary (Academic Year: <?=isset($attInfo['schoolyear']) ? $attInfo['schoolyear'] : ''?>)</h4>
    <table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse; text-align:center; font-size:10px;">
        <tr style="background:#eceff1;">
            <th style="border:1px solid #ddd; text-align:left;">Month</th>
            <?php foreach($attInfo['months'] as $m) { ?><th style="border:1px solid #ddd;"><?=$m['label']?></th><?php } ?>
            <th style="border:1px solid #ddd;">Total</th>
        </tr>
        <tr>
            <td style="border:1px solid #ddd; text-align:left;">Working Days</td>
            <?php foreach($attInfo['months'] as $m) { ?><td style="border:1px solid #ddd;"><?=$m['workingDays']?></td><?php } ?>
            <td style="border:1px solid #ddd;"><b><?=$attInfo['totalWorkingDays']?></b></td>
        </tr>
        <tr>
            <td style="border:1px solid #ddd; text-align:left;">Present</td>
            <?php foreach($attInfo['months'] as $m) { ?><td style="border:1px solid #ddd;"><?=$m['present']?></td><?php } ?>
            <td style="border:1px solid #ddd;"><b><?=$attInfo['totalPresent']?></b></td>
        </tr>
        <tr>
            <td style="border:1px solid #ddd; text-align:left;">Absent</td>
            <?php foreach($attInfo['months'] as $m) { ?><td style="border:1px solid #ddd;"><?=$m['absent']?></td><?php } ?>
            <td style="border:1px solid #ddd;"><b><?=$attInfo['totalAbsent']?></b></td>
        </tr>
    </table>
    <p style="text-align:right; font-weight:bold; color:#1a237e; margin:4px 0;">Yearly Attendance : <?=$attInfo['yearlyPercentage']?>%</p>

    <!-- ===== Remarks + QR ===== -->
    <table width="100%" style="border:none; margin-top:10px;">
        <tr>
            <td style="width:70%; vertical-align:top; border:none;">
                <h4 style="margin:0 0 4px; color:#1a237e;">Teacher's Remarks</h4>
                <p style="font-weight:bold; color:<?=$remarkColor?>; font-size:14px;"><?=$remarkText?></p>
            </td>
            <td style="width:30%; text-align:center; vertical-align:top; border:none;">
                <h4 style="margin:0 0 4px; color:#1a237e;">Scan to Verify</h4>
                <img src="<?=$qrUrl?>" style="width:80px; height:80px;">
            </td>
        </tr>
    </table>

    <!-- ===== Signatures ===== -->
    <div style="margin-top:30px; display:flex; justify-content:space-between; text-align:center; font-size:11px;">
        <span>Parent's Signature</span>
        <span>Class Teacher's Signature</span>
        <span>Principal's Signature</span>
    </div>
</div>

<p style="page-break-after: always;">&nbsp;</p>
<?php } } else { ?>
<div style="text-align:center; color:red; padding:20px;">No Data Found</div>
<?php } ?>

</body>
</html>
