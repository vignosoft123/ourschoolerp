<?php 
//print_r($attendance);die;
?>

<style>
    .error{color:red;}
    .row_background {background-color:#dce1ee;}
    .row_absent{    background-color: #f8e3e3;}
    .row_head{background-color:#ea893b;}
</style>
<div class="row">
    <div class="col-sm-12" style="margin:10px 0px">
        <?php
            $pdf_preview_uri = base_url('progresscardreport/pdf/'.$classesID.'/'.$sectionID.'/'.$studentID);
            echo btn_printReport('progresscardreport', $this->lang->line('report_print'), 'printablediv');
            // echo btn_pdfPreviewReport('progresscardreport',$pdf_preview_uri, $this->lang->line('report_pdf_preview'));
            // echo btn_sentToMailReport('progresscardreport', $this->lang->line('report_send_pdf_to_mail'));
        ?>
        <button class="btn btn-default sendSms"><span class="fa fa-send"></span> Send SMS</button>

        <button class="btn btn-default sendWhatsapp_btn"></span> Send Whatsapp</button>
    </div>
    

    <div class="form-group col-sm-12" id="students_div">
                    <label><?=$this->lang->line("progresscardreport_student")?></label>

                    <!-- <select name="stud_id[]" id="stud_id" class="form-control select2" multiple>
                       <?php 
                       $stds = (array) $students;
                       
                        foreach($stds as $stud){
                       ?>
                       <option value="<?php echo $stud->srstudentID?>"><?php echo $stud->srname;?></option>
                       <?php }?>
                    </select> -->

                    <?php  foreach($stds as $stud){
                         $stds = (array) $students;?>

                        <input value="<?php echo $stud->srstudentID?>" type="checkbox" id="stud_id" class="stud_id">
                        <?php echo $stud->srname;?>

                    <?php }?>


                    <p><b>Note :</b> For multi student selection please hold cntl + click</p>
                    <span class="error" id="stud_error"></span>
                    <span><button class="btn btn-success sendWhatsapp"><span class="fa fa-send"></span> Send Report to Whatsapp</button></span>


                   

    </div>


</div>
<div class="box">
    <div class="box-header bg-gray">
        <h3 class="box-title text-navy"><i class="fa fa-clipboard"></i> 
        <?=$this->lang->line('progresscardreport_report_for')?> - <?=$this->lang->line('progresscardreport_progresscard')?></h3>
    </div><!-- /.box-header -->
    <div id="printablediv">
        <style type="text/css">
        * {
            -webkit-print-color-adjust: exact !important;   /* Chrome, Safari 6 – 15.3, Edge */
            color-adjust: exact !important;                 /* Firefox 48 – 96 */
            print-color-adjust: exact !important;           /* Firefox 97+, Safari 15.4+ */
        }
            .mainprogresscardreport{
                margin: 0px;
                overflow: hidden;
                border:1px solid #ddd;
                max-width:794px;
                margin: 0px auto;
                margin-bottom: 10px;
                padding:55px;
                min-height: 443px;
                background:url("<?=base_url('uploads/default/admitcard-border.png')?>")!important;
                background-size: 100% 100% !important;
            }

            .progresscard-headers{
                border-bottom: 1px solid #ddd;
                overflow: hidden;
                padding-bottom: 10px;
                vertical-align: middle;
                margin-bottom: 4px;
            }

            .progresscard-logo {
                float: left;
            }

            .progresscard-headers img{
                width: 110px;
                height: 110px;
                padding-top :3px;
            }

            .school-name h2{
                float: left;
                padding-left: 20px;
                padding-top: 7px;
                font-weight: bold;
            }

            .progresscard-infos {
                width: 100%;
                overflow: hidden;
            }

            .progresscard-infos h3{
                padding: 2px 0px;
                margin: 0px;
            }

            .progresscard-infos p{
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
                line-height:24px;

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

            .progresscard-contents {
                width: 100%;
                overflow: hidden;
                margin-top: 10px;
            }

            .progresscard-contents table {
                width: 100%;
            }

            .progresscard-contents table tr,.progresscard-contents table td,.progresscard-contents table th {
                border:1px solid #ddd;
                padding: 8px 1px;
                font-size: 14px;
                text-align: center;
            }

            @media print {
                .mainprogresscardreport{
                    border:0px solid #ddd;
                    padding: 0px 20px;
                    padding:55px;
                    min-height: 443px;
                    background:url("uploads/default/admitcard-border.png") !important;
                    background-size: 100% 100% !important;
                }

                .student-profile-img img {
                    margin-right: 5px !important;
                }

                .progresscard-contents table td,.progresscard-contents table th {
                    font-size: 12px;
                }
            }
        </style>
        <div class="box-body" style="margin-bottom: 50px;">
            <div class="row">
                <div class="col-sm-12">
                <form id="marksForm">
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
                                <p> <span class="text-red">Website</span> : <?=$siteinfos->website?></p>
                            </div>

                            <div class="student-profile-img">
                                <img src="<?=imagelink($student->photo)?>" alt="">
                            </div>
                        </div>
                        <div class="progresscard-contents progresscardreporttable">


                            <table>
                                <thead>
                                    <tr class="row_head">
                                        <th rowspan="2"><?=$this->lang->line('progresscardreport_subjects')?></th>
                                        <?php if(customCompute($settingExam)) { foreach($settingExam as $examID) {
                                            $markpercentagesArr  = isset($markpercentagesclassArr[$examID]) ? $markpercentagesclassArr[$examID] : [];
                                            reset($markpercentagesArr);
                                            $firstindex          = key($markpercentagesArr);
                                            $uniquepercentageArr = isset($markpercentagesArr[$firstindex]) ? $markpercentagesArr[$firstindex] : [];
                                            $markpercentages     = $uniquepercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'];
                                            ?>
                                            <th colspan="<?=customCompute($markpercentages)?>"><?=isset($exams[$examID]) ? $exams[$examID] : ''?></th>
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
                                            <td class="text-blue"><?=$mandatorySubject->subject?>  ( <?=$mandatorySubject->max_mark?> ) </td>
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
                                                            echo '<span class="text-red">Absent</span>';
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
                                        <td class="text-blue" ><b><?=$this->lang->line('progresscardreport_total_mark')?> </b></td>
                                        <td  ><blink><b><?=ini_round($totalAllSubjectMark)?></b></blink></td>
                                        <input type="hidden" name="total_marks[]" value="<?php echo ini_round($totalAllSubjectMark)."/".$totalExMarks;?>">
                                    </tr>
                                    <tr>
                                        <td class="text-blue"><b><?=$this->lang->line('progresscardreport_average_mark')?></b> </td>
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
                                                <tr class="row_head">
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
                                                       <td> <?php echo $attendance[$d_m][$student->studentID]['absent'];?></td>
                                                    
                                                    <?php }?>
                                                </tr>
                                                </tbody>
                                            </table>


                            <div class="admitcardfooter" style="margin-top:100px !important">
                                <span class="">Parent Signature </span>
                                <span class="" style="margin-left:25%">Teacher Signature</span>
                                <span class="headmaster_signature" style="margin-left:25%">Principal Signature</span>
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
                </form>
                </div>
            </div><!-- row -->
        </div><!-- Body -->
    </div>
</div>


<!-- email modal starts here -->
<form class="form-horizontal" role="form" action="<?=base_url('progresscardreport/send_pdf_to_mail');?>" method="post">
    <div class="modal fade" id="mail">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=$this->lang->line('progresscardreport_close')?></span></button>
                <h4 class="modal-title"><?=$this->lang->line('progresscardreport_mail')?></h4>
            </div>
            <div class="modal-body">

                <?php
                    if(form_error('to'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                ?>
                    <label for="to" class="col-sm-2 control-label">
                        <?=$this->lang->line("progresscardreport_to")?> <span class="text-red">*</span>
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
                        <?=$this->lang->line("progresscardreport_subject")?> <span class="text-red">*</span>
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
                        <?=$this->lang->line("progresscardreport_message")?>
                    </label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="message" style="resize: vertical;" name="message" value="<?=set_value('message')?>" ></textarea>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                <input type="button" id="send_pdf" class="btn btn-success" value="<?=$this->lang->line("progresscardreport_send")?>" />
            </div>
        </div>
      </div>
    </div>
</form>
<!-- email end here -->

<script type="text/javascript">

$("#students_div").hide();
    $('.progresscardreporttable').mCustomScrollbar({
        axis:"x"
    });
    
    function check_email(email) {
        var status = false;
        var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        if (email.search(emailRegEx) == -1) {
            $("#to_error").html('');
            $("#to_error").html("<?=$this->lang->line('progresscardreport_mail_valid')?>").css("text-align", "left").css("color", 'red');
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
            'classesID'  : '<?=$classesID?>',
            'sectionID'  : '<?=$sectionID?>',
            'studentID'  : '<?=$studentID?>',
        };

        var to = $('#to').val();
        var subject = $('#subject').val();
        var error = 0;

        $("#to_error").html("");
        $("#subject_error").html("");

        if(to == "" || to == null) {
            error++;
            $("#to_error").html("<?=$this->lang->line('progresscardreport_mail_to')?>").css("text-align", "left").css("color", 'red');
        } else {
            if(check_email(to) == false) {
                error++
            }
        }

        if(subject == "" || subject == null) {
            error++;
            $("#subject_error").html("<?=$this->lang->line('progresscardreport_mail_subject')?>").css("text-align", "left").css("color", 'red');
        } else {
            $("#subject_error").html("");
        }

        if(error == 0) {
            $('#send_pdf').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: "<?=base_url('progresscardreport/send_pdf_to_mail')?>",
                data: field,
                dataType: "html",
                success: function(data) {
                    var response = JSON.parse(data);
                    if (response.status == false) {
                        $('#send_pdf').removeAttr('disabled');
                        if( response.to) {
                            $("#to_error").html("<?=$this->lang->line('progresscardreport_mail_to')?>").css("text-align", "left").css("color", 'red');
                        }

                        if( response.subject) {
                            $("#subject_error").html("<?=$this->lang->line('progresscardreport_mail_subject')?>").css("text-align", "left").css("color", 'red');
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
    
$('.sendSms').click(function() {
    var formDt = $('#marksForm').serialize();
    $.ajax({
        type: 'POST',
        url: "<?=base_url('progresscardreport/send_marks_to_sms')?>",
        data: formDt,
        success: function(data) {
            if(data>0)
            {
                window.location.reload();
            }
        }
    });
});

$('.sendWhatsapp').click(function() {
    // var formDt = $('#marksForm').serialize();

    // if($("#stud_id").val() == null){
    //     $("#stud_error").text("Please select atleast one student from above list");
    //     return false;
    // }

       // var val1=[]; 
    //     $('select[name="stud_id[]"] option:selected').each(function() {
    //     val1.push($(this).val());
    //     });

    var checkBox1 = [];  
    var i=0; 
           $('.stud_id:checked').each(function(){        
               var values = $(this).val();
               checkBox1[i++] = values; 
           });
        //alert(checkBox1.length);
           if(checkBox1.length <= 0){
        $("#stud_error").text("Please select atleast one student from above list");
        return false;
    }

 

    var formDt = {
            'classesID'  : '<?=$classesID?>',
            'sectionID'  : '<?=$sectionID?>',
            'examID'  : '<?=$examID?>',
            // 'studentID'  : '<?=$studentID?>',
            // 'studentID[]'  : val1,
            'studentID[]'  : checkBox1,
        };
    $.ajax({
        type: 'POST',
        url: "<?=base_url('progresscardreport/send_pdf_to_whatsapp')?>",
        data: formDt,
        success: function(data) {
            if(data>0)
            {
               // window.location.reload();
            }
        }
    });
});

$('.sendWhatsapp_btn').click(function() {
    // $("#students_div").show('slow');
    $("#students_div").toggle('slow');
})
</script>