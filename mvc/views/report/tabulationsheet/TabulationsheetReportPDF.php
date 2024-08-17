<!DOCTYPE html>
<html lang="en">
    <head>
    </head>
<body>
    
    <div class="col-sm-12">
        <?=reportheader($siteinfos, $schoolyearsessionobj, true)?>
    </div>
    <div class="box-header bg-gray">
        <h3 class="box-title text-navy"><i class="fa fa-clipboard"></i>
            <?=$this->lang->line('tabulationsheetreport_report_for')?> - 
            <?=$this->lang->line('tabulationsheetreport_tabulationsheet');?>
        </h3>
    </div><!-- /.box-header -->
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
    <?php if(customCompute($marks)) { ?>
    <div class="maintabulationsheetreport">
        <table>
            <!-- <thead>
                <tr>
                    <th rowspan="2"><?=$this->lang->line('tabulationsheetreport_name')?></th>
                    <th rowspan="2"><?=$this->lang->line('tabulationsheetreport_roll')?></th>
                    <?php if(customCompute($mandatorysubjects)) { foreach ($mandatorysubjects as $mandatorysubject) { 
                        $out_of += $mandatorysubject->max_mark;
                        ?>
                        <th colspan="<?=(customCompute($markpercentages) +1)?>"><?=$mandatorysubject->subject?></th>
                    <?php } } ?>

                    <?php if(customCompute($optionalsubjects)) { ?>
                        <th colspan="<?=(customCompute($markpercentages) +1) ?>">
                        <?php 
                        $i = 1; 
                        if(customCompute($optionalsubjects)) {
                            foreach ($optionalsubjects as $optionalsubject) {
                                $expSub = explode(' ', $optionalsubject->subject);
                                if(customCompute($optionalsubjects) == $i) {
                                    echo $expSub[0]; 
                                } else { 
                                    echo $expSub[0].'/';
                                }
                                $i++; 
                        } } ?>
                        </th>
                    <?php } ?>
                    <th rowspan="2"><?=$this->lang->line('tabulationsheetreport_gpa')?></th>
                </tr>

                <tr>
                <?php if(customCompute($mandatorysubjects)) { foreach($mandatorysubjects as $mandatorysubject) {
                    if(customCompute($markpercentages)) { foreach ($markpercentages as $markpercentageID) { ?>
                        <th><?=isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->markpercentagetype[0] : ''?></th>
                    <?php } } ?>
                    <th><?=$this->lang->line('tabulationsheetreport_total')?></th>
                <?php } } ?>

                <?php if(customCompute($optionalsubjects)) { foreach ($optionalsubjects as $optionalsubject) {
                     if(customCompute($markpercentages)) { foreach ($markpercentages as $markpercentageID) { ?>
                        <th><?=isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->markpercentagetype[0] : ''?></th>
                    <?php } } break; } ?> 
                    <th><?=$this->lang->line('tabulationsheetreport_total')?></th>
                <?php } ?>
                </tr>
            </thead> -->


            <thead>
                                    <tr>
                                        <th rowspan=""><?=$this->lang->line('tabulationsheetreport_name')?></th>
                                        <th rowspan=""><?=$this->lang->line('tabulationsheetreport_roll')?></th>
                                        <?php 
                                        //echo "<pre>";print_r($mandatorysubjects);
                                        if(customCompute($mandatorysubjects)) { foreach ($mandatorysubjects as $mandatorysubject) {
                                            $out_of += $mandatorysubject->max_mark;
                                            ?>
                                            <th colspan="<?php echo '1';//(customCompute($markpercentages) +1)?>"><?=$mandatorysubject->subject?></th>
                                        <?php } } ?>

                                        <?php if(customCompute($optionalsubjects)) { ?>
                                            <th colspan="<?php echo '1';//(customCompute($markpercentages) +1) ?>">
                                                <?php 
                                                    $i = 1; 
                                                    if(customCompute($optionalsubjects)) {
                                                        foreach ($optionalsubjects as $optionalsubject) {
                                                            $expSub = explode(' ', $optionalsubject->subject);
                                                            if(customCompute($optionalsubjects) == $i) {
                                                                echo $expSub[0]; 
                                                            } else { 
                                                                echo $expSub[0].'/';
                                                            }
                                                            $i++; 
                                                    } } ?>
                                            </th>
                                        <?php } ?>
                                        <th rowspan="">Total</th>
                                        <th rowspan="">Grade</th>
                                    </tr>

                                    <tr>
                                        <?php if(customCompute($mandatorysubjects)) { foreach($mandatorysubjects as $mandatorysubject) {
                                            if(customCompute($markpercentages)) { foreach ($markpercentages as $markpercentageID) { ?>
                                                <!--<th><?=isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->markpercentagetype[0] : ''?></th>-->
                                            <?php } } ?>
                                            <!--<th><?=$this->lang->line('tabulationsheetreport_total')?></th>-->
                                        <?php } } ?>

                                        <?php if(customCompute($optionalsubjects)) { foreach ($optionalsubjects as $optionalsubject) {
                                             if(customCompute($markpercentages)) { foreach ($markpercentages as $markpercentageID) { ?>
                                                <!--<th><?=isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->markpercentagetype[0] : ''?></th>-->
                                            <?php } } ?>
                                        <?php break; } ?> 
                                            <!--<th><?=$this->lang->line('tabulationsheetreport_total')?></th>-->
                                        <?php } ?>
                                    </tr>
                                </thead>

            <tbody>
                                    <?php $studentCount = []; 
                                        if(customCompute($students)) { foreach($students as $student) { $totalGrade = 0; ?>
                                        <tr>
                                            <td><?=$student->srname?></td>
                                            <td><?=$student->srroll?></td>
                                            <?php if(customCompute($mandatorysubjects)) {
                                                foreach ($mandatorysubjects as $mandatorysubject) { 
                                                    $subjectTotal         = 0; 
                                                    $optionalSubjectTotal = 0;
                                                    $uniquepercentageArr  = isset($markpercentagesArr[$mandatorysubject->subjectID]) ? $markpercentagesArr[$mandatorysubject->subjectID] : [];
                                                    $markpercentages      = $uniquepercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'];
                                                    $percentageMark       = 0;
                                                    if(customCompute($markpercentages)) {
                                                        foreach ($markpercentages as $markpercentageID) { 
                                                            $f = false;
                                                            if(isset($uniquepercentageArr['own']) && in_array($markpercentageID, $uniquepercentageArr['own'])) {
                                                                $f = true;
                                                                $percentageMark   += isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->percentage : 0;
                                                            } ?>
                                                    <td>
                                                        <?php
                                                            if(isset($marks[$student->srstudentID][$mandatorysubject->subjectID][$markpercentageID]) && $f) {
                                                                if($marks[$student->srstudentID][$mandatorysubject->subjectID][$markpercentageID] > 0) {
                                                                    echo $mrk = $marks[$student->srstudentID][$mandatorysubject->subjectID][$markpercentageID];
                                                                    $subjectTotal += $marks[$student->srstudentID][$mandatorysubject->subjectID][$markpercentageID];
                                                                    $totl +=$mrk; 
                                                                } else {
                                                                    echo 0;
                                                                }
                                                            } else {
                                                                echo 0;
                                                            }
                                                        ?>
                                                    </td>
                                                <?php } } ?>
                                                
                                                <!--<td>
                                                    <?php 
                                                        // echo $subjectTotal;
                                                        $subjectTotal = markCalculationView($subjectTotal, $mandatorysubject->finalmark, $percentageMark);
                                                        if(customCompute($grades)) {
                                                            foreach ($grades as $grade) {
                                                                if($grade->gradefrom <= $subjectTotal && $grade->gradeupto >= $subjectTotal) {
                                                                    $totalGrade += $grade->point;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                </td>-->
                                            <?php } } ?>

                                            <?php if(customCompute($optionalsubjects)) { foreach ($optionalsubjects as $optionalsubject) {
                                                if((int)$student->sroptionalsubjectID) {
                                                    if($student->sroptionalsubjectID == $optionalsubject->subjectID) {
                                                        $opuniquepercentageArr = [];
                                                        $opuniquepercentageArr = isset($markpercentagesArr[$student->sroptionalsubjectID]) ? $markpercentagesArr[$student->sroptionalsubjectID] : [];

                                                        $percentageMark  = 0;
                                                        if(customCompute($markpercentages)) { foreach ($markpercentages as $markpercentageID) {
                                                            $f = false;
                                                            if(isset($opuniquepercentageArr['own']) && in_array($markpercentageID, $opuniquepercentageArr['own'])) {
                                                                $f = true;
                                                                $percentageMark   += isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->percentage : 0;
                                                            } ?>
                                                            <td>
                                                                <?php
                                                                    if(isset($marks[$student->srstudentID][$optionalsubject->subjectID][$markpercentageID]) && $f) {
                                                                        if($marks[$student->srstudentID][$optionalsubject->subjectID][$markpercentageID] > 0) {
                                                                            echo $marks[$student->srstudentID][$optionalsubject->subjectID][$markpercentageID];
                                                                            $optionalSubjectTotal += $marks[$student->srstudentID][$optionalsubject->subjectID][$markpercentageID];
                                                                        } else {
                                                                            echo 0;
                                                                        }
                                                                    } else {
                                                                        echo 0;
                                                                    }
                                                                ?>
                                                            </td>
                                                            <?php $studentCount[$student->srstudentID] = TRUE; 
                                                        } } ?>
                                                        <!--<td>
                                                            <?php
                                                                // echo $optionalSubjectTotal;
                                                                $optionalSubjectTotal = markCalculationView($optionalSubjectTotal, $optionalsubject->finalmark, $percentageMark);
                                                                if(customCompute($grades)) {
                                                                    foreach ($grades as $grade) {
                                                                        if($grade->gradefrom <= $optionalSubjectTotal && $grade->gradeupto >= $optionalSubjectTotal) {
                                                                            $totalGrade += $grade->point;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                        </td>-->
                                                <?php } } else { 
                                                    if(!isset($studentCount[$student->srstudentID])) { 
                                                        $studentCount[$student->srstudentID] = TRUE; 
                                                        if(customCompute($markpercentages)) { foreach ($markpercentages as $markpercentageID) { ?>
                                                            <td><?php echo 0; ?></td>
                                                        <?php } } ?>
                                                        <td><?=0?></td>
                                                <?php } } } } ?>

                                            <!-- <td>
                                                <?php
                                                    $optSub = 0;
                                                    $manSub = customCompute($mandatorysubjects);
                                                    if($student->sroptionalsubjectID != 0) {
                                                        $optSub = 1;
                                                    }

                                                    $avg      = 0;
                                                    $totalSub = $manSub+$optSub;
                                                    if($totalSub > 0) {
                                                        $avg = ($totalGrade/$totalSub);
                                                    }
                                                    echo ini_round($avg);
                                                ?>
                                            </td> -->

                                            <td><?php echo $totl;?></td>
                                            <td><?php 
                                            
                                            if(!empty($out_of)){
                                                $percent_cal = ($totl / $out_of) * 100;
                                            }else{
                                                $percent_cal =0;
                                            }
                                            $zero_mark = 0;
                                            if($percent_cal >= 95 && $zero_mark == 0){
                                                $grade = "A+";
                                            }else if($percent_cal >= 90 && $percent_cal < 95 && $zero_mark == 0){
                                                $grade = "A";
                                            }else if($percent_cal >= 80 && $percent_cal < 90 && $zero_mark == 0){
                                                $grade = "B+";
                                            }else if($percent_cal >= 70 && $percent_cal < 80 && $zero_mark == 0){
                                                $grade = "B";
                                            }else if($percent_cal >= 60 && $percent_cal < 70 && $zero_mark == 0){
                                                $grade =  "C+";
                                            }else if($percent_cal >= 50 && $percent_cal < 60 && $zero_mark == 0){
                                                $grade = "C";
                                            }else{
                                                $grade = "D";
                                            }
                                            echo  $grade;
                                            ?>
                                            
                                        </td>
                                           
                                        </tr>
                                    <?php  $totl = 0;$out_of=0; } } ?>
                                </tbody>

                                
            <!-- <tbody>
            <?php $studentCount = []; 
                if(customCompute($students)) { foreach($students as $student) { $totalGrade = 0; ?>
                <tr>
                    <td><?=$student->srname?></td>
                    <td><?=$student->srroll?></td>
                    <?php if(customCompute($mandatorysubjects)) {
                        foreach ($mandatorysubjects as $mandatorysubject) { 
                            $subjectTotal         = 0; 
                            $optionalSubjectTotal = 0;
                            $uniquepercentageArr  = isset($markpercentagesArr[$mandatorysubject->subjectID]) ? $markpercentagesArr[$mandatorysubject->subjectID] : [];
                            $markpercentages      = $uniquepercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'];
                            $percentageMark       = 0;
                            if(customCompute($markpercentages)) {
                                foreach ($markpercentages as $markpercentageID) { 
                                    $f = false;
                                    if(isset($uniquepercentageArr['own']) && in_array($markpercentageID, $uniquepercentageArr['own'])) {
                                        $f = true;
                                        $percentageMark   += isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->percentage : 0;
                                    } ?>
                            <td>
                                <?php
                                    if(isset($marks[$student->srstudentID][$mandatorysubject->subjectID][$markpercentageID]) && $f) {
                                        if($marks[$student->srstudentID][$mandatorysubject->subjectID][$markpercentageID] > 0) {
                                            echo $marks[$student->srstudentID][$mandatorysubject->subjectID][$markpercentageID];
                                            $subjectTotal += $marks[$student->srstudentID][$mandatorysubject->subjectID][$markpercentageID];
                                            $totl +=$mrk; 
                                        } else {
                                            echo 0;
                                        }
                                    } else {
                                        echo 0;
                                    }
                                ?>
                            </td>
                        <?php } } ?>
                        <td>
                            <?php 
                                echo $subjectTotal;
                                $subjectTotal = markCalculationView($subjectTotal, $mandatorysubject->finalmark, $percentageMark);
                                if(customCompute($grades)) {
                                    foreach ($grades as $grade) {
                                        if($grade->gradefrom <= $subjectTotal && $grade->gradeupto >= $subjectTotal) {
                                            $totalGrade += $grade->point;
                                            break;
                                        }
                                    }
                                }
                            ?>
                        </td>
                    <?php } } ?>

                    <?php if(customCompute($optionalsubjects)) { foreach ($optionalsubjects as $optionalsubject) {
                        if((int)$student->sroptionalsubjectID) {
                            if($student->sroptionalsubjectID == $optionalsubject->subjectID) {
                                $opuniquepercentageArr = [];
                                $opuniquepercentageArr = isset($markpercentagesArr[$student->sroptionalsubjectID]) ? $markpercentagesArr[$student->sroptionalsubjectID] : [];

                                $percentageMark  = 0;
                                if(customCompute($markpercentages)) { foreach ($markpercentages as $markpercentageID) {
                                    $f = false;
                                    if(isset($opuniquepercentageArr['own']) && in_array($markpercentageID, $opuniquepercentageArr['own'])) {
                                        $f = true;
                                        $percentageMark   += isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->percentage : 0;
                                    } ?>
                                    <td>
                                        <?php
                                            if(isset($marks[$student->srstudentID][$optionalsubject->subjectID][$markpercentageID]) && $f) {
                                                if($marks[$student->srstudentID][$optionalsubject->subjectID][$markpercentageID] > 0) {
                                                    echo $marks[$student->srstudentID][$optionalsubject->subjectID][$markpercentageID];
                                                    $optionalSubjectTotal += $marks[$student->srstudentID][$optionalsubject->subjectID][$markpercentageID];
                                                    $totl +=$mrk; 
                                                } else {
                                                    echo 0;
                                                }
                                            } else {
                                                echo 0;
                                            }
                                        ?>
                                    </td>
                                    <?php $studentCount[$student->srstudentID] = TRUE; 
                                } } ?>
                                <td>
                                    <?php
                                        echo $optionalSubjectTotal;
                                        $optionalSubjectTotal = markCalculationView($optionalSubjectTotal, $optionalsubject->finalmark, $percentageMark);
                                        if(customCompute($grades)) {
                                            foreach ($grades as $grade) {
                                                if($grade->gradefrom <= $optionalSubjectTotal && $grade->gradeupto >= $optionalSubjectTotal) {
                                                    $totalGrade += $grade->point;
                                                    break;
                                                }
                                            }
                                        }
                                    ?>
                                </td>
                        <?php } } else { 
                            if(!isset($studentCount[$student->srstudentID])) { 
                                $studentCount[$student->srstudentID] = TRUE; 
                                if(customCompute($markpercentages)) { foreach ($markpercentages as $markpercentageID) { ?>
                                    <td><?php echo 0; ?></td>
                                <?php } } ?>
                                <td><?=0?></td>
                        <?php } } } } ?>

                        <td><?php echo $totl;?></td>

                    <td>
                        <?php
                            // $optSub = 0;
                            // $manSub = customCompute($mandatorysubjects);
                            // if($student->sroptionalsubjectID != 0) {
                            //     $optSub = 1;
                            // }

                            // $avg      = 0;
                            // $totalSub = $manSub+$optSub;
                            // if($totalSub > 0) {
                            //     $avg = ($totalGrade/$totalSub);
                            // }
                            // echo ini_round($avg);
                        ?> 
                        <?php 
                                            
                                            if(!empty($out_of)){
                                                $percent_cal = ($totl / $out_of) * 100;
                                            }else{
                                                $percent_cal =0;
                                            }
                                            $zero_mark = 0;
                                            if($percent_cal >= 95 && $zero_mark == 0){
                                                $grade = "A+";
                                            }else if($percent_cal >= 90 && $percent_cal < 95 && $zero_mark == 0){
                                                $grade = "A";
                                            }else if($percent_cal >= 80 && $percent_cal < 90 && $zero_mark == 0){
                                                $grade = "B+";
                                            }else if($percent_cal >= 70 && $percent_cal < 80 && $zero_mark == 0){
                                                $grade = "B";
                                            }else if($percent_cal >= 60 && $percent_cal < 70 && $zero_mark == 0){
                                                $grade =  "C+";
                                            }else if($percent_cal >= 50 && $percent_cal < 60 && $zero_mark == 0){
                                                $grade = "C";
                                            }else{
                                                $grade = "D";
                                            }
                                            echo  $grade;
                                            ?> 
                    </td>
                </tr>
            <?php } } ?>
            </tbody> -->
        </table>
    </div>
    <?php } else { ?>
        <div class="notfound">
            <?php echo $this->lang->line('tabulationsheetreport_data_not_found'); ?>
        </div>
    <?php } ?>
    <div class="col-sm-12 text-center footerAll">
        <?=reportfooter($siteinfos, $schoolyearsessionobj, true)?>
    </div>
    
</body>
</html>