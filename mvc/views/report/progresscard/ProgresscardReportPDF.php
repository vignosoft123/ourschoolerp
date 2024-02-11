<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
<?php 
                // echo "<pre>";print_r($students);die;
                if(customCompute($students)) { foreach($students as $student) { ?>
                    <input type="hidden" name="st_ids[]" value="<?=$student->studentID?>">
                    <input type="hidden" name="st_names[]" value="<?=$student->name?>">
                    <input type="hidden" name="mobile_no[]" value="<?=$student->phone?>">
                    <div class="mainprogresscardreport">
                        <div class="progresscard-headers">
                            <div class="progresscard-logo">
                                <img src="<?=base_url("uploads/images/$siteinfos->photo")?>" alt="">
                            </div>
                            <div class="school-name">
                                <h2><?=$siteinfos->sname?></h2>
                            </div>
                        </div>
                        <div class="progresscard-infos">
                            
                            <div class="student-profile">
                                <p> <span class="text-red">Name</span> &nbsp;&nbsp;&nbsp;:  <b><?=$student->srname?></b> </p>
                                <p> <span class="text-red"><?=$this->lang->line('progresscardreport_academic_year')?></span> : <b><?=$schoolyearsessionobj->schoolyear;?></b>
                                <p> <span class="text-red"><?=$this->lang->line('progresscardreport_reg_no')?></span> : <b><?=$student->srregisterNO?></b>,<br/> <span class="text-red"> <?=$this->lang->line('progresscardreport_class')?></span> : <b><?=isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : ''?></b></p>
                                <p> <span class="text-red"><?=$this->lang->line('progresscardreport_section')?></span> : <b><?=isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : ''?></b>, <span class="text-red"> <?=$this->lang->line('progresscardreport_roll_no')?> </span>: <b><?=$student->srroll?></b></p>  
                                <p> <span class="text-red"><?=$this->lang->line('progresscardreport_group')?></span> : <b><?=isset($groups[$student->srstudentgroupID]) ? $groups[$student->srstudentgroupID] : ''?></b></p> 
                            </div>

                            <div class="school-address">
                                <!-- <h4><b><?php //echo $siteinfos->sname?></b></h4> -->
                                <p> <span class="text-green"><?=$siteinfos->address?></span></p>
                                <p> <span class="text-red"><?=$this->lang->line('progresscardreport_phone')?></span> : <?=$siteinfos->phone?></p>
                                <p> <span class="text-red"><?=$this->lang->line('progresscardreport_email')?></span> : <?=$siteinfos->email?></p>

                                <p> <span class="text-red"><?=$this->lang->line('website')?></span> : <?=$siteinfos->website?></p>

                            </div>

                            <div class="student-profile-img">
                                <img src="<?=imagelink($student->photo)?>" alt="">
                            </div>
                        </div>
                        <div class="progresscard-contents progresscardreporttable">


                            <table>
                                <thead>
                                    <tr>
                                        <th ><?=$this->lang->line('progresscardreport_subjects')?></th>
                                        <?php if(customCompute($settingExam)) { foreach($settingExam as $examID) {
                                            $markpercentagesArr  = isset($markpercentagesclassArr[$examID]) ? $markpercentagesclassArr[$examID] : [];
                                            reset($markpercentagesArr);
                                            $firstindex          = key($markpercentagesArr);
                                            $uniquepercentageArr = isset($markpercentagesArr[$firstindex]) ? $markpercentagesArr[$firstindex] : [];
                                            $markpercentages     = $uniquepercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'];
                                            ?>
                                            <th ><?=isset($exams[$examID]) ? $exams[$examID] : ''?></th>
                                            <input type="hidden" name="exam_name[]" value="<?=isset($exams[$examID]) ? $exams[$examID] : ''?>">
                                        <?php } } ?>
                                        <!-- <th rowspan="2"><?=$this->lang->line('progresscardreport_total')?></th>
                                        <th rowspan="2"><?=$this->lang->line('progresscardreport_grade')?></th>
                                        <th rowspan="2"><?=$this->lang->line('progresscardreport_point')?></th> -->
                                    </tr>
                                    <tr>
                                        <?php 
                                        $i = 0;
                                        $totalColumn = 4;
                                        $leftColumn  = 0;
                                        $subjTotal = 0;
                                        if(customCompute($settingExam)) { foreach($settingExam as $examID) { $i++;
                                            $markpercentagesArr  = isset($markpercentagesclassArr[$examID]) ? $markpercentagesclassArr[$examID] : [];
                                            reset($markpercentagesArr);
                                            $firstindex          = key($markpercentagesArr);
                                            $uniquepercentageArr = isset($markpercentagesArr[$firstindex]) ? $markpercentagesArr[$firstindex] : [];
                                            $markpercentages     = $uniquepercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'];

                                            if($i == 1) {
                                                $leftColumn  = customCompute($markpercentages) + 1;
                                            }
                                            if(customCompute($markpercentages)) { foreach($markpercentages as $markpercentageID) { $totalColumn++;
                                            $subjTotal = isset($percentageArr[$markpercentageID]) ? substr($percentageArr[$markpercentageID]->percentage, 0, 3) : 100;
                                            ?>
                                                <!-- <th>
                                                    <?=isset($percentageArr[$markpercentageID]) ? substr($percentageArr[$markpercentageID]->percentage, 0, 3) : '';?>
                                                </th> -->
                                        <?php } } } } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 

                                    $totalAllSubjectMark      = 0; 
                                    $totalAllSubjectFinalMark = 0;
                                    $total_gpa_point = 0;
                                    $marks_template = '';
                                    $totalExMarks = 0;
                                    // echo "<pre>";print_r($mandatorySubjects);die;
                                    if(customCompute($mandatorySubjects)) { foreach($mandatorySubjects  as $mandatorySubject) {
                                        $totalExMarks += $subjTotal;
                                        $totalSubjectMark = 0; $totalGradeSubjectMark=0 ?>
                                        <tr>
                                            <td><?=$mandatorySubject->subject?>  ( <?=$mandatorySubject->max_mark?> ) </td>
                                            <?php 
                                            if(customCompute($settingExam)) { foreach($settingExam as $examID) {
                                                $examTotalSubjectMark = 0;

                                                $uniquepercentageArr = isset($markpercentagesclassArr[$examID][$mandatorySubject->subjectID]) ? $markpercentagesclassArr[$examID][$mandatorySubject->subjectID] : [];
                                                $markpercentages     = [];
                                                if(customCompute($uniquepercentageArr)) {
                                                    $markpercentages = $uniquepercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'];
                                                }

                                                $percentageMark      = 0;
                                                if(customCompute($markpercentages)) { foreach($markpercentages as $markpercentageID) {
                                                    
                                                    if(isset($uniquepercentageArr['own']) && in_array($markpercentageID, $uniquepercentageArr['own'])) {
                                                        $percentageMark   += isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->percentage : 0;
                                                    }

                                                ?>
                                                <td>
                                                    <?php
                                                        $mark = 0;
                                                        if(isset($markArray[$examID][$student->srstudentID]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID])) {
                                                            $mark = $markArray[$examID][$student->srstudentID]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID];
                                                        }
                                                           $sql = "select eattendance from eattendance where studentID = $student->srstudentID and examID = $examID and sectionID = $student->srsectionID and subjectID = $mandatorySubject->subjectID"; 
                                                          $exam_status = $this->db->query($sql)->row()->eattendance;

                                                        if($exam_status == 'Absent'){
                                                            $mark = 0;
                                                            echo 'Absent';
                                                        }else{
                                                            echo ($mark) ? $mark : 0;
                                                        }

                                                        
                                                        $totalSubjectMark     += $mark;
                                                        $examTotalSubjectMark += $mark;
                                                        $marks_template.=substr($mandatorySubject->subject,0,3).'='.$mark.'/'.$subjTotal.',';
                                                    ?>
                                                </td>
                                            <?php } }
                                            
                                            // echo "<pre>";print_r($mandatorySubject);die;
                                            
                                            $totalGradeSubjectMark += markCalculationView($examTotalSubjectMark, $mandatorySubject->max_mark, $percentageMark);
                                            } } ?>
                                            <!-- <td><?php echo ($totalSubjectMark) ? $totalSubjectMark : "AB";?></td> -->
                                            <?php
                                            $totalAllSubjectMark      += $totalSubjectMark;
                                            $subjectGradeMark          = $totalGradeSubjectMark / customCompute($settingExam);

                                            if(customCompute($grades)) { foreach($grades as $grade) {
                                                if(($grade->gradefrom <= floor($subjectGradeMark)) && ($grade->gradeupto >= floor($subjectGradeMark))) { ?>
                                                    <!-- <td><?=$grade->grade?></td> -->
                                                    <!-- <td>
                                                        <?php
                                                            echo $grade->point;
                                                            $total_gpa_point += $grade->point;
                                                        ?>
                                                    </td> -->
                                            <?php } } } ?>
                                        </tr>
                                    <?php } ?>
                                    <?php if(($student->sroptionalsubjectID > 0) && isset($optionalSubjects[$student->sroptionalsubjectID])) {
                                        $totalExMarks += $subjTotal;
                                        $totalSubjectMark = 0; $totalGradeSubjectMark = 0;?>
                                        <tr>
                                            <td><?=$optionalSubjects[$student->sroptionalsubjectID]->subject?></td>
                                            <?php if(customCompute($settingExam)) { foreach($settingExam as $examID) {
                                                $examTotalSubjectMark  = 0;

                                                $opuniquepercentageArr = isset($markpercentagesclassArr[$examID][$student->sroptionalsubjectID]) ? $markpercentagesclassArr[$examID][$student->sroptionalsubjectID] : [];

                                                $markpercentages     = [];
                                                if(customCompute($opuniquepercentageArr)) {
                                                    $markpercentages = $opuniquepercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'];
                                                }

                                                $percentageMark = 0;
                                                if(customCompute($markpercentages)) { foreach($markpercentages as $markpercentageID) {
                                                    if(isset($opuniquepercentageArr['own']) && in_array($markpercentageID, $opuniquepercentageArr['own'])) {
                                                        $percentageMark   += isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->percentage : 0;
                                                    }
                                                    ?>
                                                <td>
                                                    <?php
                                                        $mark   = 0;
                                                        if(isset($markArray[$examID][$student->srstudentID]['markpercentageMark'][$student->sroptionalsubjectID][$markpercentageID])) {
                                                            $mark = $markArray[$examID][$student->srstudentID]['markpercentageMark'][$student->sroptionalsubjectID][$markpercentageID];
                                                        }
                                                        echo ($mark) ? $mark : 0;
                                                        $totalSubjectMark     += $mark;
                                                        $examTotalSubjectMark += $mark;
                                                    ?>
                                                </td>
                                            <?php } }
                                            $totalGradeSubjectMark += markCalculationView($examTotalSubjectMark, $optionalSubjects[$student->sroptionalsubjectID]->max_mark, $percentageMark);
                                            } } ?>
                                            <td><?=$totalSubjectMark?></td>
                                            <?php
                                            $totalAllSubjectMark      += $totalSubjectMark;
                                            $subjectGradeMark          = $totalGradeSubjectMark / customCompute($settingExam);

                                            if(customCompute($grades)) { foreach($grades as $grade) {
                                                if(($grade->gradefrom <= floor($subjectGradeMark)) && ($grade->gradeupto >= floor($subjectGradeMark))) { ?>
                                                    <td><?=$grade->grade?></td>
                                                    <td>
                                                        <?php
                                                            echo $grade->point;
                                                            $total_gpa_point += $grade->point;
                                                        ?>
                                                    </td>
                                            <?php } } } ?>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td  ><?=$this->lang->line('progresscardreport_total_mark')?> </td>
                                        <td  ><b><?=ini_round($totalAllSubjectMark)?></b></td>
                                        <input type="hidden" name="total_marks[]" value="<?php echo ini_round($totalAllSubjectMark)."/".$totalExMarks;?>">
                                    </tr>
                                    <tr>
                                        <td ><?=$this->lang->line('progresscardreport_average_mark')?> </td>
                                       <td> 
                                        <b>
                                                <?php
                                                    $tSubject     = $totalSubject;
                                                    if($student->sroptionalsubjectID > 0) {
                                                        $tSubject = $tSubject + 1;
                                                    }
                                                    $totalAllSubject = $tSubject * customCompute($settingExam);
                                                    echo ini_round($totalAllSubjectMark / $totalAllSubject);
                                                ?>
                                            </b>
                                    </td>
                                        
                                    </tr>
                                    <!-- <tr>
                                        <td colspan="<?=$leftColumn?>"><?=$this->lang->line('progresscardreport_gpa')?></td>
                                        <td colspan="<?=$totalColumn-$leftColumn?>">
                                            <?php 
                                                echo ini_round($total_gpa_point / $tSubject);
                                            ?>
                                        </td>
                                    </tr> -->
                                    <!-- <tr>
                                        <td colspan="<?=$leftColumn?>"><?=$this->lang->line('progresscardreport_from_teacher_remarks')?></td>
                                        <td colspan="<?=$totalColumn-$leftColumn?>"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="<?=$leftColumn?>"><?=$this->lang->line('progresscardreport_house_teacher_remarks')?></td>
                                        <td colspan="<?=$totalColumn-$leftColumn?>"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="<?=$leftColumn?>"><?=$this->lang->line('progresscardreport_principal_remarks')?></td>
                                        <td colspan="<?=$totalColumn-$leftColumn?>"></td>
                                    </tr> -->

                                    <!-- <tr>
                                        <td colspan="<?=$totalColumn?>">
                                            <?=$this->lang->line('progresscardreport_interpretation')?> :
                                            <b>
                                                <?php if(customCompute($grades)) { $i = 1; foreach($grades as $grade) {
                                                    if(customCompute($grades) == $i) {
                                                        echo $grade->gradefrom.'-'.$grade->gradeupto." = ".$grade->point." [".$grade->grade."]";
                                                    } else {
                                                        echo $grade->gradefrom.'-'.$grade->gradeupto." = ".$grade->point." [".$grade->grade."], ";
                                                    }
                                                    $i++;
                                                } } ?>
                                            </b>
                                        </td>
                                    </tr> -->
                                    <?php } ?>
                                </tbody>
                            </table>

                            <!-- code for attendance -->
                            <br/>
                                                <h5 class="text-blue"><b>Attendance</b></h5>
                                                <table>
                                                <thead>
                                                <tr>
                                                    <th><?php //echo $schoolyear;?>Months</th>
                                                 
                                                    <?php 
                                                    // print_r($months);
                                                        for($m=6;$m<count($months)+6;$m++){  

                                                            if($m > 12)
                                                                $d_m = $m - 12;
                                                            else
                                                                $d_m = $m 
                                                    ?>
                                                <th class="text-purple"><?= $months[$d_m]?></th>
                                                <?php } ?>
                                                </tr>                                               
                                                </thead>

                                                <tbody>
                                                     
                                                    <tr class="row_background">
                                                    <td class="text-green"><b>Present</b></td>
                                                    <?php  
                                                        for($m=6;$m<count($months)+6;$m++){  

                                                            if($m > 12)
                                                                $d_m = $m - 12;
                                                            else
                                                                $d_m = $m 
                                                    ?>
                                                        
                                                        <td><?php echo $attendance[$d_m][$student->studentID]['present'];?></td>
                                                        <?php }?>
                                                    </tr>


                                                    <tr class="row_absent">
                                                        <td class="text-red"><b>Absent</b></td>
                                                        <?php  
                                                        for($m=6;$m<count($months)+6;$m++){  

                                                            if($m > 12)
                                                                $d_m = $m - 12;
                                                            else
                                                                $d_m = $m 
                                                    ?>
                                                       <td> <?php echo $attendance[$d_m][$student->srstudentID]['absent'];?></td>
                                                    
                                                    <?php }?>
                                                </tr>
                                                </tbody>
                                            </table>

                                            
                            <div class="admitcardfooter" style="margin-top:500px !important">
                                <span class="">Parent Signature </span>
                                <span class="" style="margin-left:40%">Teacher Signature</span>
                                <span class="headmaster_signature" style="margin-left:80%">Principal Signature</span>
                            </div>
                            <input type="hidden" name="marks_template[]" value="<?php echo $marks_template;?>">
                        </div>
                    </div>
                    <p style="page-break-after: always;">&nbsp;</p>
                <?php } } else { ?>
                    <div class="callout callout-danger">
                        <p><b class="text-info"><?=$this->lang->line('progresscardreport_data_not_found')?></b></p>
                    </div>
                <?php } ?>
</body>
</html>