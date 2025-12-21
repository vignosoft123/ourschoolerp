<link rel="stylesheet" href="/assets/css/report-buttons.css">
<div class="row">
    <div class="col-sm-12" style="margin:10px 0px">
        <?php
            $pdf_preview_uri = base_url('studentsessionreport/pdf/'.$studentID);
            echo btn_printReport('studentsessionreport', $this->lang->line('report_print'), 'printablediv');
            // echo btn_pdfPreviewReport('studentsessionreport',$pdf_preview_uri, $this->lang->line('report_pdf_preview'));
            // echo btn_sentToMailReport('studentsessionreport', $this->lang->line('report_send_pdf_to_mail'));
        ?>
    </div>
</div>

<style type="text/css">
    /* ==== Base Container ==== */
    .mainstudentsessionreport {
        margin: 0 auto 20px auto;
        overflow: hidden;
        border: 1px solid #ddd;
        /* max-width: 850px; */
        background: #f9fafc;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 30px 35px;
    }

    /* ==== Header Section ==== */
    .studentsession-headers {
        display: flex;
        align-items: center;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 12px;
        margin-bottom: 15px;
    }

    .studentsession-logo img {
        width: 70px;
        height: 70px;
        border-radius: 6px;
    }

    .school-name h2 {
        margin: 0;
        padding-left: 20px;
        font-weight: 700;
        font-size: 24px;
        color: #6b9ce2;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ==== Info Section ==== */
    .studentsession-infos {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 15px;
        border-bottom: 1px dashed #ccc;
        padding-bottom: 15px;
    }

    .school-address, .student-profile {
        /* width: 45%; */
    }

    .school-address h4, .student-profile h4 {
        color: #2d2d2d;
        font-weight: 600;
        border-bottom: 1px solid #ddd;
        padding-bottom: 4px;
        margin-bottom: 8px;
    }

    .school-address p, .student-profile p {
        margin: 3px 0;
        font-size: 14px;
        color: #333;
    }

    .student-profile-img {
        /* width: 100%; */
        text-align: right;
        margin-top: 10px;
    }

    .student-profile-img img {
        width: 120px;
        height: 120px;
        border: 2px solid #ddd;
        border-radius: 8px;
        object-fit: cover;
        background: #fff;
        padding: 4px;
    }

    /* ==== Table Section ==== */
    .studentsession-contents {
        margin-top: 20px;
    }

    .studentsession-contents table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .studentsession-contents table th {
        background: #6b9ce2;
        color: #fff;
        padding: 10px;
        font-weight: 600;
        border: 1px solid #ccc;
        text-align: center;
    }

    .studentsession-contents table td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: center;
        background-color: #fff;
    }

    .studentsession-contents table tr:nth-child(even) td {
        background: #f7f9fb;
    }

    .studentsession-contents table tr:hover td {
        background: #eef6ff;
    }

    /* ==== Attendance Section ==== */
    h5.text-blue {
        margin-top: 25px;
        padding-bottom: 5px;
        border-bottom: 2px solid #6b9ce2;
        color: #6b9ce2;
        font-weight: 600;
    }

    table.table-bordered {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #ddd;
    }

    table.table-bordered th {
        background: #457b9d;
        color: white;
        text-align: center;
    }

    .row_background td, .row_absent td, .row_workingdays td {
        text-align: center;
    }

    .text-green { color: #2a9d8f; }
    .text-red { color: #e63946; }
    .text-blue { color: #6b9ce2; }
    .text-purple { color: #6a1b9a; }

    /* Attendance badge */
    .attendance-badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        background: #e74c3c; /* red for absent */
        box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    }
    .attendance-na {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        background: #7f8c8d; /* gray for N/A */
    }

    /* ==== Print Optimization ==== */
 
</style>

<div class="box">
    <div class="box-header bg-gray">
        <h3 class="box-title text-navy"><i class="fa fa-clipboard"></i> 
        <?=$this->lang->line('studentsessionreport_report_for')?> - <?=$this->lang->line('studentsessionreport_student_session')?></h3>
    </div><!-- /.box-header -->
    <div id="printablediv">
        <style type="text/css">
            .mainstudentsessionreport{
                margin: 0px;
                overflow: hidden;
                border:1px solid #ddd;
                max-width:794px;
                margin: 0px auto;
                margin-bottom: 10px;
                padding:30px;
            }

            .studentsession-headers{
                border-bottom: 1px solid #ddd;
                overflow: hidden;
                padding-bottom: 10px;
                vertical-align: middle;
                margin-bottom: 4px;
            }

            .studentsession-logo {
                float: left;
            }

            .studentsession-headers img{
                width: 60px;
                height: 60px;
            }

            .school-name h2{
                float: left;
                padding-left: 20px;
                padding-top: 7px;
                font-weight: bold;
            }

            .studentsession-infos {
                width: 100%;
                overflow: hidden;
            }

            .studentsession-infos h3{
                padding: 2px 0px;
                margin: 0px;
            }

            .studentsession-infos p{
                margin-bottom: 3px;
                font-size: 15px;
            }

            .school-address{
                float: left;
                width: 40%;
            }

            .student-profile {
                float: left;
                width: 40%;

            }

            .student-profile-img {
                float: left;
                width: 20%;
                text-align: right;
            }

            .student-profile-img img {
                width: 120px;
                height: 120px;
                border: 1px solid #ddd;
                margin-top: 5px;
                margin-right: 2px;
            }

             @media screen and (max-width: 480px) {
                .school-name h2{
                    padding-left: 0px;
                    float: none;
                }

                .school-address {
                    width: 100%;
                }

                .student-profile {
                    width: 100%;
                } 

                .student-profile-img  {
                    margin-top: 10px;
                    width: 100%;
                }

                .student-profile-img img {
                    width: 100%;
                    height: 100%;
                    margin: 10px 0px;
                }
            }

            .studentsession-contents {
                width: 100%;
                overflow: hidden;
                margin-top: 10px;
            }

            .studentsession-contents table {
                width: 100%;
            }

            .studentsession-contents table tr,.studentsession-contents table td,.studentsession-contents table th {
                border:1px solid #ddd;
                padding: 8px 1px;
                font-size: 14px;
                text-align: center;
            }

            @media print {
                .mainstudentsessionreport{
                    border:0px solid #ddd;
                    padding: 0px 20px;
                }

                .student-profile-img img {
                    margin-right: 5px !important;
                }

                .studentsession-contents table td,.studentsession-contents table th {
                    font-size: 12px;
                }
            }
        </style>
        <div class="box-body" style="margin-bottom: 50px;">
            <div class="row">
                <div class="col-sm-12">
                <?php if(customCompute($students)) { foreach($students as $schoolyearID => $student) { ?>
                    <div class="mainstudentsessionreport">
                        <div class="studentsession-headers">
                            <div class="studentsession-logo">
                                <img src="<?=base_url("uploads/images/$siteinfos->photo")?>" alt="">
                            </div>
                            <div class="school-name">
                                <h2><?=$siteinfos->sname?></h2>
                            </div>
                        </div>
                        <div class="studentsession-infos">
                            <div class="school-address">
                                <h4><b><?=$siteinfos->sname?></b></h4>
                                <p><?=$siteinfos->address?></p>
                                <p><?=$this->lang->line('studentsessionreport_phone')?> : <?=$siteinfos->phone?></p>
                                <p><?=$this->lang->line('studentsessionreport_email')?> : <?=$siteinfos->email?></p>

                                <p><?=$this->lang->line('website')?> : <?=$siteinfos->website?></p>

                            </div>
                            <div class="student-profile">
                                <h4><b><?=$student->srname?></b></h4>
                                <p><?=$this->lang->line('studentsessionreport_academic_year')?> : <b><?=isset($schoolyears[$schoolyearID]) ? $schoolyears[$schoolyearID] : []?></b>
                                <p><?=$this->lang->line('studentsessionreport_reg_no')?> : <b><?=$student->srregisterNO?></b>, <?=$this->lang->line('studentsessionreport_class')?> : <b><?=isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : ''?></b></p>
                                <p><?=$this->lang->line('studentsessionreport_section')?> : <b><?=isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : ''?></b>, <?=$this->lang->line('studentsessionreport_roll_no')?> : <b><?=$student->srroll?></b></p>  
                                <p><?=$this->lang->line('studentsessionreport_group')?> : <b><?=isset($groups[$student->srstudentgroupID]) ? $groups[$student->srstudentgroupID] : ''?></b></p> 
                            </div>
                            <div class="student-profile-img">
                                <img src="<?=imagelink($student->photo)?>" alt="">
                            </div>
                        </div>
                        <div class="studentsession-contents studentsessionreporttable">
                        <table>
    <thead>
        <tr>
            <th rowspan="2"><?= $this->lang->line('studentsessionreport_subjects') ?></th>
            <?php
            $markpercentagesexamArr = isset($markpercentagesmainArr[$student->srclassesID]) ? $markpercentagesmainArr[$student->srclassesID] : [];

            if (customCompute($markpercentagesexamArr)) {
                foreach ($markpercentagesexamArr as $examID => $markpercentagessubjectArr) {
                    reset($markpercentagessubjectArr);
                    $firstindex = key($markpercentagessubjectArr);
                    $uniquepercentageArr = isset($markpercentagessubjectArr[$firstindex]) ? $markpercentagessubjectArr[$firstindex] : [];
                    $uniqueandown = (($settingmarktypeID == 4) || ($settingmarktypeID == 6)) ? 'unique' : 'own';
                    $markpercentages = isset($uniquepercentageArr[$uniqueandown]) ? $uniquepercentageArr[$uniqueandown] : ''; ?>
                    <th colspan="<?= customCompute($markpercentages) ?>"><?= isset($exams[$examID]) ? $exams[$examID] : '' ?></th>
                <?php }
            } ?>
            <!-- <th rowspan="2"><?= $this->lang->line('studentsessionreport_total') ?></th> -->
        </tr>
        <tr>
            <?php
            $i = 0;
            $totalColumn = 4;
            $leftColumn = 0;

            if (customCompute($markpercentagesexamArr)) {
                foreach ($markpercentagesexamArr as $examID => $markpercentagessubjectArr) {
                    reset($markpercentagessubjectArr);
                    $firstindex = key($markpercentagessubjectArr);
                    $uniquepercentageArr = isset($markpercentagessubjectArr[$firstindex]) ? $markpercentagessubjectArr[$firstindex] : [];
                    $uniqueandown = (($settingmarktypeID == 4) || ($settingmarktypeID == 6)) ? 'unique' : 'own';
                    $markpercentages = isset($uniquepercentageArr[$uniqueandown]) ? $uniquepercentageArr[$uniqueandown] : '';

                    if ($i == 1) {
                        $leftColumn = customCompute($markpercentages) + 1;
                    }

                    if (customCompute($markpercentages)) {
                        foreach ($markpercentages as $markpercentageID) {
                            $totalColumn++; ?>
                            <th>
                                <?php echo '<span style="color:green">Max:' . $exam_max_marks[$examID] . '</span>';
                                $tot_max_marks += $exam_max_marks[$examID];
                                ?>
                            </th>
                        <?php }
                    }
                }
            } ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalGpaPoint = 0;
        $totalAllSubjectMark = 0;

        // Build subject list: scheduled subjects (if any) else mandatory subjects
        $subjectList = array();
        if (isset($scheduledSubjects) && customCompute($scheduledSubjects)) {
            foreach ($scheduledSubjects as $sid => $sobj) {
                $subjectList[$sid] = $sobj;
            }
        } else {
            if (isset($mandatorySubjects[$student->srclassesID]) && customCompute($mandatorySubjects[$student->srclassesID])) {
                foreach ($mandatorySubjects[$student->srclassesID] as $sobj) {
                    $subjectList[$sobj->subjectID] = $sobj;
                }
            }
        }

        if (customCompute($subjectList)) {
            foreach ($subjectList as $subjectID => $subject) {
                // Skip optional subject if student didn't choose it
                $isOptional = isset($optionalSubjects[$student->srclassesID]) && isset($optionalSubjects[$student->srclassesID][$subjectID]);
                if ($isOptional && ($student->sroptionalsubjectID > 0) && ($student->sroptionalsubjectID != $subjectID)) {
                    continue;
                }

                $totalSubjectMark = 0;
                $totalGradeSubjectMark = 0; ?>
                <tr>
                    <td><?= $subject->subject ?></td>
                    <?php
                    if (customCompute($markpercentagesexamArr)) {
                        foreach ($markpercentagesexamArr as $examID => $markpercentagessubjectArr) {
                            $examTotalSubjectMark = 0;

                            // determine full column count for this exam (based on first subject's config)
                            reset($markpercentagessubjectArr);
                            $firstindex_all = key($markpercentagessubjectArr);
                            $uniquepercentageArr_all = isset($markpercentagessubjectArr[$firstindex_all]) ? $markpercentagessubjectArr[$firstindex_all] : [];
                            $uniqueandown_all = (($settingmarktypeID == 4) || ($settingmarktypeID == 6)) ? 'unique' : 'own';
                            $markpercentages_all = isset($uniquepercentageArr_all[$uniqueandown_all]) ? $uniquepercentageArr_all[$uniqueandown_all] : [];
                            $examColCount = customCompute($markpercentages_all) ? customCompute($markpercentages_all) : 0;

                            $uniquepercentageArr = isset($markpercentagessubjectArr[$subjectID]) ? $markpercentagessubjectArr[$subjectID] : [];
                            $markpercentages = [];
                            if (customCompute($uniquepercentageArr)) {
                                $uniqueandown = (($settingmarktypeID == 4) || ($settingmarktypeID == 6)) ? 'unique' : 'own';
                                $markpercentages = isset($uniquepercentageArr[$uniqueandown]) ? $uniquepercentageArr[$uniqueandown] : '';
                            }

                            $percentageMark = 0;
                            $isScheduled = isset($examSubjects[$examID]) && in_array($subjectID, $examSubjects[$examID]);
                            if (!$isScheduled || !customCompute($markpercentages)) {
                                for ($na = 0; $na < $examColCount; $na++) { ?>
                                    <td>N/A</td>
                                <?php }
                            } else {
                                foreach ($markpercentages as $markpercentageID) {
                                    if (isset($uniquepercentageArr['own']) && in_array($markpercentageID, $uniquepercentageArr['own'])) {
                                        $percentageMark += isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->percentage : 0;
                                    }
                                    ?>
                                    <td>
                                        <?php
                                        $mark = 0;
                                        if (isset($retMark[$schoolyearID][$student->srclassesID][$examID][$subjectID][$markpercentageID])) {
                                            $mark = $retMark[$schoolyearID][$student->srclassesID][$examID][$subjectID][$markpercentageID];
                                        }
                                        if ($mark === 'A' || $mark === 'a' || (is_string($mark) && strtolower($mark) === 'absent')) {
                                            echo '<span class="attendance-badge" title="Absent">Ab</span>';
                                        } elseif ($mark !== 0 && $mark !== '' && $mark !== null) {
                                            echo $mark;
                                        } else {
                                            echo '';
                                        }
                                        $totalSubjectMark += is_numeric($mark) ? $mark : 0;
                                        $examTotalSubjectMark += is_numeric($mark) ? $mark : 0;
                                        ?>
                                    </td>
                                <?php }
                            }
                            $totalGradeSubjectMark += markCalculationView($examTotalSubjectMark, $subject->finalmark, $percentageMark);
                        }
                    } ?>
                    <!-- <td><?= $totalSubjectMark ?></td> -->
                    <?php
                    $totalAllSubjectMark += $totalSubjectMark;
                    $subjectGradeMark = $totalGradeSubjectMark / customCompute($markpercentagesexamArr);

                    if (customCompute($grades)) {
                        foreach ($grades as $grade) {
                            if (($grade->gradefrom <= floor($subjectGradeMark)) && ($grade->gradeupto >= floor($subjectGradeMark))) { ?>
                                <td><?= $grade->grade ?></td>
                                <td>
                                    <?php
                                    echo $grade->point;
                                    $totalGpaPoint += $grade->point;
                                    ?>
                                </td>
                            <?php }
                        }
                    }
                    ?>
                </tr>
            <?php }
        }
        ?>

        <!-- Total Marks Row -->
        <tr>
            <td colspan="<?= $leftColumn ?>"><?= $this->lang->line('studentsessionreport_total_mark') ?></td>
            <?php
            if (customCompute($markpercentagesexamArr)) {
                foreach ($markpercentagesexamArr as $examID => $markpercentagessubjectArr) {
                    $totalExamMarks = 0;
                    if (customCompute($subjectList)) {
                        foreach ($subjectList as $sID => $sobj) {
                            // skip optional subjects the student didn't choose
                            $isOptional = isset($optionalSubjects[$student->srclassesID]) && isset($optionalSubjects[$student->srclassesID][$sID]);
                            if ($isOptional && ($student->sroptionalsubjectID > 0) && ($student->sroptionalsubjectID != $sID)) {
                                continue;
                            }
                            $uniquepercentageArr = isset($markpercentagessubjectArr[$sID]) ? $markpercentagessubjectArr[$sID] : [];
                            $markpercentages = [];
                            if (customCompute($uniquepercentageArr)) {
                                $uniqueandown = (($settingmarktypeID == 4) || ($settingmarktypeID == 6)) ? 'unique' : 'own';
                                $markpercentages = isset($uniquepercentageArr[$uniqueandown]) ? $uniquepercentageArr[$uniqueandown] : '';
                            }

                            if (customCompute($markpercentages)) {
                                foreach ($markpercentages as $markpercentageID) {
                                    $mark = 0;
                                    if (isset($retMark[$schoolyearID][$student->srclassesID][$examID][$sID][$markpercentageID])) {
                                        $mark = $retMark[$schoolyearID][$student->srclassesID][$examID][$sID][$markpercentageID];
                                    }
                                    $totalExamMarks += $mark;
                                }
                            }
                        }
                    }
                    ?>
                    <td><b><?= $totalExamMarks ?></b></td>
                <?php }
            } ?>
        </tr>

        <!-- Average Marks Row -->
        <tr>
            <td colspan="<?= $leftColumn ?>">Average Marks </td>
            <?php
            if (customCompute($markpercentagesexamArr)) {
                foreach ($markpercentagesexamArr as $examID => $markpercentagessubjectArr) {
                    $totalExamMarks = 0;
                    $subjectCount = 0;
                    if (customCompute($subjectList)) {
                        foreach ($subjectList as $sID => $sobj) {
                            $isOptional = isset($optionalSubjects[$student->srclassesID]) && isset($optionalSubjects[$student->srclassesID][$sID]);
                            if ($isOptional && ($student->sroptionalsubjectID > 0) && ($student->sroptionalsubjectID != $sID)) {
                                continue;
                            }
                            $subjectCount++;

                            $uniquepercentageArr = isset($markpercentagessubjectArr[$sID]) ? $markpercentagessubjectArr[$sID] : [];
                            $markpercentages = [];
                            if (customCompute($uniquepercentageArr)) {
                                $uniqueandown = (($settingmarktypeID == 4) || ($settingmarktypeID == 6)) ? 'unique' : 'own';
                                $markpercentages = isset($uniquepercentageArr[$uniqueandown]) ? $uniquepercentageArr[$uniqueandown] : '';
                            }

                            if (customCompute($markpercentages)) {
                                foreach ($markpercentages as $markpercentageID) {
                                    $mark = 0;
                                    if (isset($retMark[$schoolyearID][$student->srclassesID][$examID][$sID][$markpercentageID])) {
                                        $mark = $retMark[$schoolyearID][$student->srclassesID][$examID][$sID][$markpercentageID];
                                    }
                                    $totalExamMarks += $mark;
                                }
                            }
                        }
                    }
                    $subjectCount = $subjectCount ? $subjectCount : 1;
                    ?>
                    <td><b><?= ini_round($totalExamMarks / $subjectCount) ?></b></td>
                <?php }
            } ?>
        </tr>
<!-- Overall Exam Average Row -->
<tr>
<td colspan="<?= $leftColumn ?>">
    Overall Exam Average 
  
</td>
    <?php
    $grandTotalMarks = 0;
    $examCount = 0;

    if (customCompute($markpercentagesexamArr)) {
        foreach ($markpercentagesexamArr as $examID => $markpercentagessubjectArr) {
            $totalExamMarks = 0;
            if (customCompute($subjectList)) {
                foreach ($subjectList as $sID => $sobj) {
                    // skip optional subjects the student didn't choose
                    $isOptional = isset($optionalSubjects[$student->srclassesID]) && isset($optionalSubjects[$student->srclassesID][$sID]);
                    if ($isOptional && ($student->sroptionalsubjectID > 0) && ($student->sroptionalsubjectID != $sID)) {
                        continue;
                    }
                    $uniquepercentageArr = isset($markpercentagessubjectArr[$sID]) ? $markpercentagessubjectArr[$sID] : [];
                    $markpercentages = [];
                    if (customCompute($uniquepercentageArr)) {
                        $uniqueandown = (($settingmarktypeID == 4) || ($settingmarktypeID == 6)) ? 'unique' : 'own';
                        $markpercentages = isset($uniquepercentageArr[$uniqueandown]) ? $uniquepercentageArr[$uniqueandown] : '';
                    }

                    if (customCompute($markpercentages)) {
                        foreach ($markpercentages as $markpercentageID) {
                            $mark = 0;
                            if (isset($retMark[$schoolyearID][$student->srclassesID][$examID][$sID][$markpercentageID])) {
                                $mark = $retMark[$schoolyearID][$student->srclassesID][$examID][$sID][$markpercentageID];
                            }
                            $totalExamMarks += $mark;
                        }
                    }
                }
            }

            $grandTotalMarks += $totalExamMarks;
            $examCount++;
        }

        $overallAverage = ($examCount > 0) ? ini_round($grandTotalMarks / $examCount) : 0;
    ?>
    <td colspan="<?= $totalColumn - $leftColumn ?>"><b> 
    <span style="font-weight: bold;" class="text-success fw-bold"><?= $grandTotalMarks . ' / ' . $examCount ?>= </span>    
    <?= $overallAverage ?></b></td>
    <?php } ?>
</tr>

        <tr>
            <td colspan="<?= $leftColumn ?>">Teacher Signature</td>
            <td colspan="<?= $totalColumn - $leftColumn ?>"></td>
        </tr>
        <tr>
            <td colspan="<?= $leftColumn ?>">Correspondent Signature</td>
            <td colspan="<?= $totalColumn - $leftColumn ?>"><img src="<?= base_url('/uploads/signatures/') . $siteinfos->correspondent_signature ?>" style="width:150px;height:50px;"></td>
        </tr>
        <tr>
            <td colspan="<?= $leftColumn ?>">Parent or Guardian Signature</td>
            <td colspan="<?= $totalColumn - $leftColumn ?>"></td>
        </tr>
    </tbody>
</table>


 <!-- code for attendance table start -->

<?php 
if($is_display_attendance > 0){ ?>

<br/>
<h5 class="text-blue"><b>Attendance</b></h5>

<?php
$months = array(
    '6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec','1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr'
);

// ✅ Define function only once
if (!function_exists('countDaysByMonth')) {
    function countDaysByMonth($daysArray, $monthNum, $year) {
        $count = 0;
        foreach($daysArray as $day) {
            $parts = explode('-', $day);
            if(count($parts) == 3) {
                $d = (int)$parts[0];
                $m = (int)$parts[1];
                $y = (int)$parts[2];
                if($m == $monthNum && $y == $year) {
                    $count++;
                }
            }
        }
        return $count;
    }
}

// ✅ Get academic year dynamically
$schoolyearID = $this->session->userdata('defaultschoolyearID');
$schoolyearObj = $this->db->get_where('schoolyear', ['schoolyearID' => $schoolyearID])->row();

if ($schoolyearObj) {
    $schoolyear = $schoolyearObj->schoolyear;
    $parts = explode('-', $schoolyear);

    if (count($parts) == 2) {
        $startYear = (int)$parts[0];
        $endPart   = trim($parts[1]);
        $endYear   = (strlen($endPart) == 2) ? (int)("20" . $endPart) : (int)$endPart;
    } else {
        $startYear = date('Y');
        $endYear   = $startYear + 1;
    }
} else {
    $startYear = date('Y');
    $endYear   = $startYear + 1;
}

$this->data['startYear'] = $startYear;
$this->data['endYear']   = $endYear;
?>

<table class="table table-bordered table-striped">
    <thead>
        <tr class="row_head">
            <th>Months</th>
            <?php 
                for($m=6; $m<count($months)+6; $m++){  
                    $d_m = ($m > 12) ? $m - 12 : $m;
            ?>
                <th class="text-purple"><?= $months[$d_m]?></th>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <!-- Present Row -->
        <tr class="row_background">
            <td class="text-green"><b>Present</b></td>
            <?php  
                for($m=6; $m<count($months)+6; $m++){  
                    $d_m = ($m > 12) ? $m - 12 : $m;
            ?>
                <td><?= $attendance[$d_m][$student->studentID]['present'] ?? 0; ?></td>
            <?php } ?>
        </tr>

        <!-- Absent Row -->
        <tr class="row_absent">
            <td class="text-red"><b>Absent</b></td>
            <?php  
                for($m=6; $m<count($months)+6; $m++){  
                    $d_m = ($m > 12) ? $m - 12 : $m;
            ?>
                <td><?= $attendance[$d_m][$student->studentID]['absent'] ?? 0; ?></td>
            <?php } ?>
        </tr>

        <!-- Working Days Row -->
        <tr class="row_workingdays">
            <td class="text-blue"><b>Working Days</b></td>
            <?php  
                for($m=6; $m<count($months)+6; $m++){  
                    $d_m = ($m > 12) ? $m - 12 : $m;
                    $year = ($m > 12) ? $endYear : $startYear;

                    $totalDays    = cal_days_in_month(CAL_GREGORIAN, $d_m, $year);
                    $holidayCount = countDaysByMonth($getHolidays, $d_m, $year);
                    $weekendCount = countDaysByMonth($getWeekendDays, $d_m, $year);
                    $workingDays  = $totalDays - ($holidayCount + $weekendCount);
            ?>
                <td><?= $workingDays; ?></td>
            <?php } ?>
        </tr>
    </tbody>
</table>

<?php } ?>
<!-- code for attendance table end -->



                        </div>
                    </div>
                    <p style="page-break-after: always;">&nbsp;</p>
                <?php } } else { ?>
                    <div class="callout callout-danger">
                        <p><b class="text-info"><?=$this->lang->line('studentsessionreport_data_not_found')?></b></p>
                    </div>
                <?php } ?>
                </div>
            </div><!-- row -->
        </div><!-- Body -->
    </div>
</div>


<!-- email modal starts here -->
<form class="form-horizontal" role="form" action="<?=base_url('studentsessionreport/send_pdf_to_mail');?>" method="post">
    <div class="modal fade" id="mail">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=$this->lang->line('studentsessionreport_close')?></span></button>
                <h4 class="modal-title"><?=$this->lang->line('studentsessionreport_mail')?></h4>
            </div>
            <div class="modal-body">

                <?php
                    if(form_error('to'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                ?>
                    <label for="to" class="col-sm-2 control-label">
                        <?=$this->lang->line("studentsessionreport_to")?> <span class="text-red">*</span>
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
                        <?=$this->lang->line("studentsessionreport_subject")?> <span class="text-red">*</span>
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
                        <?=$this->lang->line("studentsessionreport_message")?>
                    </label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="message" style="resize: vertical;" name="message" value="<?=set_value('message')?>" ></textarea>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                <input type="button" id="send_pdf" class="btn btn-success" value="<?=$this->lang->line("studentsessionreport_send")?>" />
            </div>
        </div>
      </div>
    </div>
</form>
<!-- email end here -->

<script type="text/javascript">

    $('.studentsessionreporttable').mCustomScrollbar({
        axis:"x"
    });
    
    function check_email(email) {
        var status = false;
        var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        if (email.search(emailRegEx) == -1) {
            $("#to_error").html('');
            $("#to_error").html("<?=$this->lang->line('studentsessionreport_mail_valid')?>").css("text-align", "left").css("color", 'red');
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
            'studentID'  : '<?=$studentID?>',
        };

        var to = $('#to').val();
        var subject = $('#subject').val();
        var error = 0;

        $("#to_error").html("");
        $("#subject_error").html("");

        if(to == "" || to == null) {
            error++;
            $("#to_error").html("<?=$this->lang->line('studentsessionreport_mail_to')?>").css("text-align", "left").css("color", 'red');
        } else {
            if(check_email(to) == false) {
                error++
            }
        }

        if(subject == "" || subject == null) {
            error++;
            $("#subject_error").html("<?=$this->lang->line('studentsessionreport_mail_subject')?>").css("text-align", "left").css("color", 'red');
        } else {
            $("#subject_error").html("");
        }

        if(error == 0) {
            $('#send_pdf').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: "<?=base_url('studentsessionreport/send_pdf_to_mail')?>",
                data: field,
                dataType: "html",
                success: function(data) {
                    var response = JSON.parse(data);
                    if (response.status == false) {
                        $('#send_pdf').removeAttr('disabled');
                        if( response.to) {
                            $("#to_error").html("<?=$this->lang->line('studentsessionreport_mail_to')?>").css("text-align", "left").css("color", 'red');
                        }

                        if( response.subject) {
                            $("#subject_error").html("<?=$this->lang->line('studentsessionreport_mail_subject')?>").css("text-align", "left").css("color", 'red');
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