<!DOCTYPE html>
<html lang="en">
<body style="font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; color:#000; margin:0; padding:0;">

<?php if(customCompute($students)) { $pcnCount = count($students); $pcnSeen = 0; foreach($students as $student) { $pcnSeen++; ?>
<div style="width:750px; margin:6px auto; padding:10px; border:1px solid #1a237e; border-radius:8px;">

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

    <table width="100%" style="border:none; background:#fff3e0; margin-top:4px;">
        <tr><td style="border:none; text-align:center; color:#e65100; font-weight:bold; padding:4px;">
            <?=isset($exams[$examID]) ? strtoupper($exams[$examID]) : ''?> PROGRESS REPORT
        </td></tr>
    </table>

    <!-- ===== Student Info + Result Summary ===== -->
    <table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse; margin-top:5px;">
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
        $hasZeroMark = false;
        foreach($subjectRows as $row) {
            if ($row['absent'] || $row['obtained'] == 0) { $hasZeroMark = true; break; }
        }
        $gradeInfo = progresscard_resolve_grade($percent, $hasZeroMark);
        $attInfo   = isset($attendanceByStudent[$student->srstudentID]) ? $attendanceByStudent[$student->srstudentID] : array('months'=>[], 'totalWorkingDays'=>0,'totalPresent'=>0,'totalAbsent'=>0,'yearlyPercentage'=>0,'schoolyear'=>'');

        if ($percent >= 90) { $remarkText = 'Excellent'; $remarkColor = '#2e7d32'; }
        elseif ($percent >= 80) { $remarkText = 'Very Good'; $remarkColor = '#28a745'; }
        elseif ($percent >= 70) { $remarkText = 'Good'; $remarkColor = '#007bff'; }
        elseif ($percent >= 60) { $remarkText = 'Fair'; $remarkColor = '#ffc107'; }
        elseif ($percent >= 50) { $remarkText = 'Average'; $remarkColor = '#fb8c00'; }
        else { $remarkText = 'Need Improvement'; $remarkColor = '#e53935'; }

    ?>

    <!-- ===== Result Summary ===== -->
    <table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse; margin-top:5px; text-align:center;">
        <tr style="background:#eceff1; color:#1a237e;">
            <th style="border:1px solid #ddd;">Total Marks</th>
            <th style="border:1px solid #ddd;">Percentage</th>
            <th style="border:1px solid #ddd;">Grade</th>
            <th style="border:1px solid #ddd;">Rank (Class)</th>
            <?php if($is_display_attendance > 0) { ?><th style="border:1px solid #ddd;">Attendance</th><?php } ?>
        </tr>
        <tr>
            <td style="border:1px solid #ddd;"><?=ini_round($totalObtained)?>/<?=ini_round($totalMaxMarks)?></td>
            <td style="border:1px solid #ddd;"><?=$percent?>%</td>
            <td style="border:1px solid #ddd;"><span style="background:<?=$gradeInfo['bg']?>;color:<?=$gradeInfo['color']?>;padding:2px 8px;border-radius:4px;font-weight:bold;"><?=$gradeInfo['grade']?></span></td>
            <td style="border:1px solid #ddd;"><?=($student->rank !== null && $student->rank !== '') ? $student->rank : '-'?></td>
            <?php if($is_display_attendance > 0) { ?><td style="border:1px solid #ddd;"><?=$attInfo['yearlyPercentage']?>%</td><?php } ?>
        </tr>
    </table>

    <!-- ===== Subject Wise Performance ===== -->
    <h4 style="margin:6px 0 3px; color:#1a237e;">Subject Wise Performance</h4>
    <table width="100%" cellpadding="3" cellspacing="0" style="border-collapse:collapse; text-align:center;">
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
    <table width="100%" style="border:none; margin-top:5px;">
        <tr>
            <td style="width:55%; vertical-align:top; border:none;">
                <h4 style="margin:0 0 3px; color:#1a237e;">Grade Scale</h4>
                <table width="100%" cellpadding="2" cellspacing="0" style="border-collapse:collapse; text-align:center; font-size:10px;">
                    <tr style="background:#eceff1;"><th style="border:1px solid #ddd;">Range</th><th style="border:1px solid #ddd;">Grade</th><th style="border:1px solid #ddd;">Remark</th></tr>
                    <?php foreach(progresscard_grade_scale() as $gs) { ?>
                    <tr><td style="border:1px solid #ddd;"><?=$gs['range']?></td><td style="border:1px solid #ddd;"><?=$gs['grade']?></td><td style="border:1px solid #ddd;"><?=$gs['label']?></td></tr>
                    <?php } ?>
                </table>
            </td>
            <td style="width:45%; vertical-align:top; border:none; padding-left:10px;">
                <h4 style="margin:0 0 3px; color:#1a237e;">Class Performance</h4>
                <table width="100%" cellpadding="3" cellspacing="0" style="border-collapse:collapse; text-align:center;">
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

    <?php if($is_display_attendance > 0) { ?>
    <!-- ===== Attendance Summary ===== -->
    <h4 style="margin:6px 0 3px; color:#1a237e;">Attendance Summary (Academic Year: <?=isset($attInfo['schoolyear']) ? $attInfo['schoolyear'] : ''?>)</h4>
    <table width="100%" cellpadding="2" cellspacing="0" style="border-collapse:collapse; text-align:center; font-size:9px;">
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
    <p style="text-align:right; font-weight:bold; color:#1a237e; margin:2px 0; font-size:10px;">Yearly Attendance : <?=$attInfo['yearlyPercentage']?>%</p>
    <?php } ?>

    <!-- ===== Performance Overview (plain HTML/CSS bars — mPDF can't run the on-screen Highcharts JS) ===== -->
    <h4 style="margin:6px 0 3px; color:#1a237e;">Performance Overview</h4>
    <table width="100%" style="border:none;">
        <tr>
            <!-- Subject Wise Marks -->
            <td style="width:38%; vertical-align:top; border:none; padding-right:6px;">
                <p style="margin:0 0 3px; font-size:9px; font-weight:bold; color:#37474f;">Subject Wise Marks</p>
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:9px;">
                <?php
                    // Fixed-pixel track, nested table — same table-cell structure that already
                    // rendered reliably (it's what the on-screen/PDF designs both used), but the
                    // inner table's width is now a plain PIXEL number (no "%"), not a percentage.
                    // A bare pixel HTML width attribute is unambiguous; the previous percentage
                    // was the part that resolved differently across mPDF versions (identical code
                    // showed proportional bars on one server, full-width regardless of value on
                    // another). Plain <div>s with no text content were tried and mPDF collapsed
                    // them to invisible — table cells are the reliable primitive here, not divs.
                    $barTrackPx = 120;
                    foreach($subjectRows as $row):
                    $barPct = $row['max'] > 0 ? min(100, round(($row['obtained'] / $row['max']) * 100)) : 0;
                    $barPx  = round($barTrackPx * $barPct / 100);
                ?>
                    <tr>
                        <td style="width:28px; border:none; padding:2px 3px 2px 0;"><?=$row['subject']?></td>
                        <td style="border:none; padding:2px 0;">
                            <table width="<?=$barTrackPx?>" cellpadding="0" cellspacing="0" style="border-collapse:collapse;"><tr>
                                <td style="background:#e0e0e0; height:8px; font-size:1px;">
                                    <table width="<?=$barPx?>" cellpadding="0" cellspacing="0" style="border-collapse:collapse;"><tr>
                                        <td style="background:#3949ab; height:8px; font-size:1px;">&nbsp;</td>
                                    </tr></table>
                                </td>
                            </tr></table>
                        </td>
                        <td style="width:22px; text-align:right; border:none; padding:2px 0 2px 3px;"><?=$row['absent'] ? '-' : ini_round($row['obtained'])?></td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </td>
            <!-- Marks Distribution -->
            <td style="width:26%; vertical-align:top; border:none; padding:0 6px;">
                <p style="margin:0 0 3px; font-size:9px; font-weight:bold; color:#37474f;">Marks Distribution</p>
                <?php
                    $mdTrackPx = 150;
                    $mdPct     = $totalMaxMarks > 0 ? min(100, round(($totalObtained / $totalMaxMarks) * 100)) : 0;
                    $mdPx      = round($mdTrackPx * $mdPct / 100);
                ?>
                <table width="<?=$mdTrackPx?>" cellpadding="0" cellspacing="0" style="border-collapse:collapse;"><tr>
                    <td style="background:#e0e0e0; height:12px; font-size:1px;">
                        <table width="<?=$mdPx?>" cellpadding="0" cellspacing="0" style="border-collapse:collapse;"><tr>
                            <td style="background:#1a73e8; height:12px; font-size:1px;">&nbsp;</td>
                        </tr></table>
                    </td>
                </tr></table>
                <p style="margin:3px 0 0; font-size:8px; color:#555;">Obtained <?=ini_round($totalObtained)?> / <?=ini_round($totalMaxMarks)?></p>
            </td>
            <!-- Grade Distribution (Class) -->
            <td style="width:36%; vertical-align:top; border:none; padding-left:6px;">
                <p style="margin:0 0 3px; font-size:9px; font-weight:bold; color:#37474f;">Grade Distribution (Class)</p>
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:9px;">
                <?php
                    $gradeOrder = array('A+','A','B+','B','C+','C','D');
                    $maxCount   = !empty($classPerf['gradeHistogram']) ? max($classPerf['gradeHistogram']) : 0;
                    $maxCount   = $maxCount > 0 ? $maxCount : 1;
                    $gradeTrackPx = 110;
                    foreach($gradeOrder as $g):
                        $cnt = isset($classPerf['gradeHistogram'][$g]) ? $classPerf['gradeHistogram'][$g] : 0;
                        $gp  = round(($cnt / $maxCount) * 100);
                        $gpx = round($gradeTrackPx * $gp / 100);
                ?>
                    <tr>
                        <td style="width:18px; border:none; padding:1px 3px 1px 0;"><?=$g?></td>
                        <td style="border:none; padding:1px 0;">
                            <table width="<?=$gradeTrackPx?>" cellpadding="0" cellspacing="0" style="border-collapse:collapse;"><tr>
                                <td style="background:#e0e0e0; height:7px; font-size:1px;">
                                    <table width="<?=$gpx?>" cellpadding="0" cellspacing="0" style="border-collapse:collapse;"><tr>
                                        <td style="background:#00897b; height:7px; font-size:1px;">&nbsp;</td>
                                    </tr></table>
                                </td>
                            </tr></table>
                        </td>
                        <td style="width:14px; text-align:right; border:none; padding:1px 0 1px 3px;"><?=$cnt?></td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </td>
        </tr>
    </table>

    <!-- ===== Teacher's Remarks ===== -->
    <table width="100%" style="border:none; margin-top:4px;">
        <tr>
            <td style="border:none;">
                <h4 style="margin:0 0 2px; color:#1a237e;">Teacher's Remarks</h4>
                <p style="font-weight:bold; color:<?=$remarkColor?>; font-size:12px; margin:0;"><?=$remarkText?></p>
            </td>
        </tr>
    </table>

    <!-- ===== Signatures =====
         mPDF does not support CSS flexbox (display:flex is silently dropped), so the three
         labels must use a table with explicit column alignment instead — left/center/right.
         Principal's Signature column also carries the correspondent_signature image, same as
         the old-design PDF — width/height are forced (not object-fit/max-width) since mPDF
         stretches to the given box rather than preserving aspect ratio. -->
    <table width="100%" style="border:none; margin-top:8px; text-align:center; font-size:10px;">
        <tr>
            <td style="border:none; width:33%;">&nbsp;</td>
            <td style="border:none; width:33%;">&nbsp;</td>
            <td style="border:none; width:34%;"><img src="<?=base_url('/uploads/signatures/').$siteinfos->correspondent_signature?>" style="width:150px; height:50px;"></td>
        </tr>
        <tr>
            <td style="border:none;">Parent's Signature</td>
            <td style="border:none;">Class Teacher's Signature</td>
            <td style="border:none;">Principal's Signature</td>
        </tr>
    </table>
</div>

<?php if($pcnSeen < $pcnCount) { ?><p style="page-break-after: always;">&nbsp;</p><?php } ?>
<?php } } else { ?>
<div style="text-align:center; color:red; padding:20px;">No Data Found</div>
<?php } ?>

</body>
</html>
