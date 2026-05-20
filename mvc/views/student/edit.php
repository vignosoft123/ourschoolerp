<style>
    .err { color: red; font-weight: bold; }
    /* Required field indicator: left green accent */
    .student-form-info .has-required .form-control {
        border-left: 3px solid #27ae60 !important;
        background-color: #fff !important;
    }
    .student-form-info .has-required .select2-container .select2-choice,
    .student-form-info .has-required .select2-container .select2-choices {
        border-left: 3px solid #27ae60 !important;
    }
    .student-form-info .has-required > label { font-weight: 600; color: #2c3e50; }
    .bg-lpurple { background-color: #fff !important; }
    .val-error { color: #c0392b; font-size: 12px; display: block; margin-top: 3px; }
    .val-error i { margin-right: 3px; }
    .photo-upload-wrap .photo-preview-img {
        max-width: 100px; max-height: 100px;
        border: 2px solid #ddd; border-radius: 6px;
        padding: 2px; margin-top: 6px; display: none;
    }
    /* ── Accordion Panels ── */
    .student-accordion { margin-bottom: 10px; }
    .sap { border: 1px solid #dde0e6; border-radius: 10px; margin-bottom: 14px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
    .sap-header { background: linear-gradient(135deg, #0cc035 0%, #0a9d2b 100%); color: #fff; padding: 13px 20px; cursor: pointer; display: flex; align-items: center; gap: 10px; user-select: none; transition: background 0.2s; }
    .sap-header:hover { background: linear-gradient(135deg, #0aa82e 0%, #088a25 100%); }
    .sap-header .sap-icon { font-size: 17px; width: 22px; text-align: center; }
    .sap-header .sap-title { flex: 1; font-size: 15px; font-weight: 600; letter-spacing: 0.3px; }
    .sap-chevron { transition: transform 0.3s ease; font-size: 13px; }
    .sap-header.collapsed .sap-chevron { transform: rotate(180deg); }
    .sap-body { padding: 22px 20px 10px; }
    .sap-body .col-md-4, .sap-body .col-md-3, .sap-body .col-md-9 { margin-bottom: 14px; }
    .req-legend { font-size: 11px; color: #888; margin-bottom: 12px; }
    .req-legend .text-red { color: #e74c3c; font-weight: bold; }
    .sap-bg-1 { background: #f0f8ff !important; }
    .sap-bg-2 { background: #f0fff4 !important; }
    .sap-bg-3 { background: #fffbf0 !important; }
    .sap-bg-4 { background: #fff8f0 !important; }
    .sap-bg-5 { background: #f8f0ff !important; }
    .sap-bg-6 { background: #fff0f8 !important; }
</style>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-student"></i> <?=$this->lang->line('panel_title')?></h3>

       
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("student/index/$set")?>"><?=$this->lang->line('menu_student')?></a></li>
            <li class="active"><?=$this->lang->line('menu_edit')?> <?=$this->lang->line('menu_student')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
            <form class="form-horizontal student-form-info" role="form" method="post" enctype="multipart/form-data">
                <div class="student-accordion">
                <!-- Section 1: Basic Information -->
                <div class="sap">
                    <div class="sap-header" data-toggle="collapse" data-target="#sap-basic">
                        <span class="sap-icon"><i class="fa fa-id-card-o"></i></span>
                        <span class="sap-title">Basic Information</span>
                        <i class="fa fa-chevron-up sap-chevron"></i>
                    </div>
                    <div id="sap-basic" class="collapse in sap-body sap-bg-1">
                    <p class="req-legend"><span class="text-red">*</span> Required fields</p>
                        <div class="row">
                        <?php 
                            if(form_error('registerNO')) 
                                echo "<div class='col-md-4 has-error' >";
                            else     
                                echo "<div class='col-md-4' >";
                        ?>
                            <label for="registerNO" class="control-label">
                                <?=$this->lang->line("student_registerNO")?> <span class="text-red">*</span>
                            </label>                            
                            <input type="text" class="form-control" id="registerNO" name="registerNO" value="<?=set_value('registerNO', $student->srregisterNO)?>" >
                            <span class="control-label">
                                <?php echo form_error('registerNO'); ?>
                            </span>
                        </div>

                      
                        <?php 
                        if(form_error('admission_date')) 
                            echo "<div class='col-md-4 has-error' >";
                        else     
                            echo "<div class='col-md-4' >";
                            ?>
                                <label for="admission_date" class="control-label">
                                    <?=$this->lang->line("student_admission_date")?>
                                </label>                                
                                    <?php $admission_date = ''; if($student->admission_date) { $admission_date = date("d-m-Y", strtotime($student->admission_date)); }  ?>
                                    <input type="text" class="form-control" id="admission_date" name="admission_date" value="<?=set_value('admission_date', $admission_date)?>" >
                                
                                <span class="control-label">
                                    <?php echo form_error('admission_date'); ?>
                                </span>
                            </div>

                            <?php
                            if (form_error('pen_number'))
                                echo "<div class='col-md-4 has-error' >";
                            else
                                echo "<div class='col-md-4' >";
                            ?>
                            <label for="pen_number" class=" control-label">
                            PEN Number
                            </label>
                            
                                <input type="text" class="form-control" id="pen_number" name="pen_number"  value="<?=set_value('pen_number', $student->pen_number)?>">
                            
                            <span class="  control-label">
                                <?php echo form_error('pen_number'); ?>
                            </span>
                        </div>

                        <?php
                            if (form_error('child_id'))
                                echo "<div class='col-md-4 has-error' >";
                            else
                                echo "<div class='col-md-4' >";
                            ?>
                            <label for="child_id" class=" control-label">
                            Child ID
                            </label>
                            
                                <input type="text" class="form-control" id="child_id" name="child_id"  value="<?=set_value('child_id', $student->child_id)?>">
                            
                            <span class="  control-label">
                                <?php echo form_error('child_id'); ?>
                            </span>
                        </div>

                         <?php
                            if (form_error('medium'))
                                echo "<div class='col-md-4 has-error' >";
                            else
                                echo "<div class='col-md-4' >";
                            ?>
                            <label for="medium" class=" control-label">
                            Medium
                            </label>
                            
                                <input type="text" class="form-control" id="medium" name="medium"  value="<?=set_value('medium', $student->medium)?>">
                            
                            <span class="  control-label">
                                <?php echo form_error('medium'); ?>
                            </span>
                        </div>

                        
                            <?php 
                            if(form_error('classesID')) 
                                echo "<div class='col-md-4 has-error' >";
                            else     
                                echo "<div class='col-md-4' >";
                                ?>
                                <label for="classesID" class="control-label">
                                    <?=$this->lang->line("student_classes")?> <span class="text-red">*</span>
                                </label>
                                
                                    <?php
                                        $classArray = array(0 => $this->lang->line("student_select_class"));
                                        foreach ($classes as $classa) {
                                            $classArray[$classa->classesID] = $classa->classes;
                                        }
                                        echo form_dropdown("classesID", $classArray, set_value("classesID", $student->srclassesID), "id='classesID' class='form-control select2'");
                                    ?>
                               
                                <span class="control-label">
                                    <?php echo form_error('classesID'); ?>
                                </span>
                                <input type="hidden" name="old_class" value="<?= $student->srclassesID?>">
                            </div>
                            <?php 
                                if(form_error('sectionID')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="sectionID" class="control-label">
                                    <?=$this->lang->line("student_section")?> <span class="text-red">*</span>
                                </label>
                                
                                    <?php
                                        $array = array(0 => $this->lang->line("student_select_section"));
                                        foreach ($sections as $section) {
                                            $array[$section->sectionID] = $section->section;
                                        }
                                        echo form_dropdown("sectionID", $array, set_value("sectionID", $student->srsectionID), "id='sectionID' class='form-control select2'");
                                    ?>
                                
                                <span class="control-label">
                                    <?php echo form_error('sectionID'); ?>
                                </span>

                                <input type="hidden" name="old_section" value="<?= $student->srsectionID?>">
                            </div>
                            <?php 
                                if(form_error('rf_id')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="aadharCardNumber" class="control-label">
                                RFID
                                </label> 

                                <input type="text" class="form-control" id="rf_id" name="rf_id" value="<?=set_value('rf_id', $student->rf_id)?>" >
                               
                                <span class="control-label">
                                    <?php echo form_error('rf_id'); ?>
                                </span>
                            </div>
                            <?php 
                                if(form_error('roll')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="roll" class="control-label">
                                    <?=$this->lang->line("student_roll")?> <span class="text-red">*</span>
                                </label>
                                
                                    <input type="text" class="form-control" id="roll" name="roll" value="<?=set_value('roll', $student->srroll)?>" >
                               
                                <span class="err control-label">
                                    <?php echo form_error('roll'); ?>
                                </span>
                            </div>
                            <?php 
                                if(form_error('joined_class')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="classesID" class=" control-label">
                                    Joining Class <span class="text-red">*</span>
                                </label>
                                
                                    <?php
                                        $classArray = array(0 => $this->lang->line("student_select_class"));
                                        foreach ($classes as $classa) {
                                            $classArray[$classa->classesID] = $classa->classes;
                                        }
                                        echo form_dropdown("joined_class", $classArray, set_value("joined_class", $student->srclassesID), "id='joined_class' class='form-control select2'");
                                    ?>
                                
                                <!--<span class="col-sm-4 control-label">-->
                                    <?php //echo form_error('classesID'); ?>
                                <!--</span>-->
                            </div>

                            <div class="col-md-4 <?=form_error('studentGroupID') ? ' has-error' : ''  ?>">
                                <label for="studentGroupID" class=" control-label">
                                    <?=$this->lang->line("student_studentgroup")?>
                                </label>
                               
                                    <?php
                                    $groupArray = array(0 => $this->lang->line("student_select_studentgroup"));
                                    if(customCompute($studentgroups)) {
                                        foreach ($studentgroups as $studentgroup) {
                                            $groupArray[$studentgroup->studentgroupID] = $studentgroup->group;
                                        }
                                    }
                                    echo form_dropdown("studentGroupID", $groupArray, set_value("studentGroupID", $student->srstudentgroupID), "id='studentGroupID' class='form-control select2'");
                                    ?>
                               
                                <span class="control-label">
                                    <?php echo form_error('studentGroupID'); ?>
                                </span>
                            </div>

                            <div class="col-md-4 <?=form_error('remarks') ? ' has-error' : ''  ?>">
                                <label for="remarks" class="control-label">
                                    <?=$this->lang->line("student_remarks")?>
                                </label>
                                
                                    <input type="text" class="form-control" id="remarks" name="remarks" value="<?=set_value('remarks', $student->remarks)?>" >
                                
                                <span class="control-label">
                                    <?php echo form_error('remarks'); ?>
                                </span>
                            </div>


                        <div class="col-md-4 <?=form_error('optionalSubjectID') ? ' has-error' : ''  ?>">
                            <label for="optionalSubjectID" class="control-label">
                                <?=$this->lang->line("student_optionalsubject")?>
                            </label>
                           
                                <?php
                                $optionalSubjectArray = array(0 => $this->lang->line("student_select_optionalsubject"));
                                if($optionalSubjects != "empty") {
                                    foreach ($optionalSubjects as $optionalSubject) {
                                        $optionalSubjectArray[$optionalSubject->subjectID] = $optionalSubject->subject;
                                    }
                                }

                                echo form_dropdown("optionalSubjectID", $optionalSubjectArray, set_value("optionalSubjectID", $student->sroptionalsubjectID), "id='optionalSubjectID' class='form-control select2'");
                                ?>
                           
                            <span class="control-label">
                                <?php echo form_error('optionalSubjectID'); ?>
                            </span>
                        </div>

                        <?php
                        if (form_error('mother_toungue'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="mother_toungue" class="  control-label">
                            Mother Toungue
                        </label>                        
                            <!-- <input type="text" class="form-control" id="cast" name="cast" value="<?= set_value('cast') ?>"> -->
                            <select class="form-control select2" id="mother_toungue" name="mother_toungue">
                                <option value="">Select Mother Toungue</option>
                                <option value="1" <?php if($student->mother_toungue == 1){echo "selected";}?> >Telugu</option>
                                <option value="2" <?php if($student->mother_toungue == 2){echo "selected";}?>  >English</option>
                                <option value="3" <?php if($student->mother_toungue == 3){echo "selected";}?> >Hindi</option>
                                <option value="4" <?php if($student->mother_toungue == 4){echo "selected";}?> >Kannada</option>
                                <option value="5" <?php if($student->mother_toungue == 5){echo "selected";}?> >Malayalam</option> 
                                <option value="6" <?php if($student->mother_toungue == 6){echo "selected";}?> >Urdhu</option> 
                            </select>
                        <span class="  control-label">
                            <?php echo form_error('mother_toungue'); ?>
                        </span>
                    </div>


                        <div class="col-md-4 <?=form_error('extraCurricularActivities') ? ' has-error' : ''  ?>">
                            <label for="extraCurricularActivities" class=" control-label">
                                <?=$this->lang->line("student_extracurricularactivities")?>
                            </label>
                            
                                <input type="text" class="form-control" id="extraCurricularActivities" name="extraCurricularActivities" value="<?=set_value('extraCurricularActivities', $student->extracurricularactivities)?>" >
                          
                            <span class=" control-label">
                                <?php echo form_error('extraCurricularActivities'); ?>
                            </span>
                        </div>


                        </div>
                    </div><!-- /.sap-body -->
                </div><!-- /.sap -->

                <!-- Section 2: Student Details -->
                <div class="sap">
                    <div class="sap-header collapsed" data-toggle="collapse" data-target="#sap-details">
                        <span class="sap-icon"><i class="fa fa-user-circle-o"></i></span>
                        <span class="sap-title">Student Details</span>
                        <i class="fa fa-chevron-up sap-chevron"></i>
                    </div>
                    <div id="sap-details" class="collapse sap-body sap-bg-2">
                        <div class="row">

                        <?php 
                                if(form_error('first_name')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="first_name" class=" control-label">
                                    First Name<span class="text-red">*</span>
                                </label>
                                
                                    <input type="text" class="form-control id_card" id="first_name" name="first_name" value="<?=set_value('first_name', $student->first_name)?>" >
                               
                                <span class="control-label">
                                    <?php echo form_error('first_name'); ?>
                                </span>
                            </div>

                            <?php 
                                if(form_error('last_name')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="last_name" class=" control-label">
                                    Last Name <span class="text-red">*</span>
                                </label>
                                
                                    <input type="text" class="form-control id_card" id="last_name" name="last_name" value="<?=set_value('last_name', $student->last_name)?>" >
                               
                                <span class="control-label">
                                    <?php echo form_error('last_name'); ?>
                                </span>
                            </div>

                            <?php 
                                if(form_error('name')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="name_id" class=" control-label">
                                    ID Card name <span class="text-red">*</span>
                                </label>
                                
                                    <input type="text" class="form-control" id="name_id" name="name" value="<?=set_value('name', $student->name)?>" >
                               
                                <span class="control-label">
                                    <?php echo form_error('name'); ?>
                                </span>
                            </div>
                            <?php 
                                if(form_error('sex')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4'>";
                            ?>
                                <label for="sex" class="control-label">
                                    <?=$this->lang->line("student_sex")?>
                                </label>
                                
                                    <?php 
                                        echo form_dropdown("sex", array($this->lang->line('student_sex_male') => $this->lang->line('student_sex_male'), $this->lang->line('student_sex_female') => $this->lang->line('student_sex_female')), set_value("sex", $student->sex), "id='sex' class='form-control'");
                                    ?>
                               
                                <span class="control-label">
                                    <?php echo form_error('sex'); ?>
                                </span>

                            </div>

                            <?php 
                                if(form_error('bloodgroup')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="bloodgroup" class="control-label">
                                    <?=$this->lang->line("student_bloodgroup")?>
                                </label>
                                
                                    <?php 
                                        $bloodArray = array(
                                            '0' => $this->lang->line('student_select_bloodgroup'),
                                            'A+' => 'A+',
                                            'A-' => 'A-',
                                            'B+' => 'B+',
                                            'B-' => 'B-',
                                            'O+' => 'O+',
                                            'O-' => 'O-',
                                            'AB+' => 'AB+',
                                            'AB-' => 'AB-'
                                        );
                                        echo form_dropdown("bloodgroup", $bloodArray, set_value("bloodgroup", $student->bloodgroup), "id='bloodgroup' class='form-control select2'"); 
                                    ?>
                               
                                <span class="control-label">
                                    <?php echo form_error('bloodgroup'); ?>
                                </span>
                            </div>
                            <?php 
                                if(form_error('dob')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="dob" class="control-label">
                                    <?=$this->lang->line("student_dob")?>
                                </label>
                               
                                    <?php $dob = ''; if($student->dob) { $dob = date("d-m-Y", strtotime($student->dob)); }  ?>
                                    <input type="text" class="form-control" id="dob" name="dob" value="<?=set_value('dob', $dob)?>" >
                               
                                <span class="control-label">
                                    <?php echo form_error('dob'); ?>
                                </span>
                            </div>
                            <?php 
                                if(form_error('religion')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="religion" class="control-label">
                                    <?=$this->lang->line("student_religion")?>
                                </label>
                                
                                    <input type="text" class="form-control" id="religion" name="religion" value="<?=set_value('religion', $student->religion)?>" >
                               
                                <span class="control-label">
                                    <?php echo form_error('religion'); ?>
                                </span>
                            </div>

                            <?php
                                if (form_error('cast'))
                                    echo "<div class='col-md-4 has-error' >";
                                else
                                    echo "<div class='col-md-4' >";
                                ?>
                                <label for="cast" class="  control-label">
                                    Caste
                                </label>                        
                                    <select class="form-control select2" id="cast" name="cast">
                                        <option value="">Select Caste</option>
                                        <option value="OC" <?=set_select('cast', 'OC', $student->caste == 'OC' ? TRUE : FALSE)?>>OC</option>
                                        <option value="BC - A" <?=set_select('cast', 'BC - A', $student->caste == 'BC - A' ? TRUE : FALSE)?>>BC - A</option>
                                        <option value="BC - B" <?=set_select('cast', 'BC - B', $student->caste == 'BC - B' ? TRUE : FALSE)?>>BC - B</option>
                                        <option value="BC - C" <?=set_select('cast', 'BC - C', $student->caste == 'BC - C' ? TRUE : FALSE)?>>BC - C</option>
                                        <option value="BC - D" <?=set_select('cast', 'BC - D', $student->caste == 'BC - D' ? TRUE : FALSE)?>>BC - D</option>
                                        <option value="SC" <?=set_select('cast', 'SC', $student->caste == 'SC' ? TRUE : FALSE)?>>SC</option>
                                        <option value="ST" <?=set_select('cast', 'ST', $student->caste == 'ST' ? TRUE : FALSE)?>>ST</option>
                                        <option value="Minority" <?=set_select('cast', 'Minority', $student->caste == 'Minority' ? TRUE : FALSE)?>>Minority</option>
                                    </select>
                                <span class="  control-label">
                                    <?php echo form_error('cast'); ?>
                                </span>
                            </div>

                            <?php
                                if (form_error('sub_caste'))
                                    echo "<div class='col-md-4 has-error' >";
                                else
                                    echo "<div class='col-md-4' >";
                                ?>
                                <label for="sub_caste" class=" control-label">
                                    Sub Caste
                                </label>
                                    <input type="text" class="form-control" id="sub_caste" name="sub_caste" value="<?= set_value('sub_caste', $student->sub_caste) ?>">
                                <span class="  control-label">
                                    <?php echo form_error('sub_caste'); ?>
                                </span>
                            </div>

                            <?php 
                                if(form_error('fathername')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";

                                    $this->db->where('parentsID',$student->parentID);
                                    $father_name = $this->db->get('parents')->row()->father_name;
                            ?>
                                <label for="name_id" class="control-label">
                                Father Name <span class="text-red">*</span>
                                </label>
                               
                                    <input type="text" class="form-control" id="father_name" name="father_name" value="<?= $father_name?$father_name:''?>" >
                               
                                <span class="control-label">
                                    <?php echo form_error('name'); ?>
                                </span>
                            </div>

                            <?php 
                                if(form_error('father_aadhar')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";

                                    $this->db->where('parentsID',$student->parentID);
                                    $father_aadhar = $this->db->get('parents')->row()->father_aadhar; 
                            ?>
                                <label for="father_aadhar" class="control-label">
                                Father Aadhar
                                </label>
                               
                                    <input type="text" class="form-control" id="father_aadhar" name="father_aadhar" value="<?= $father_aadhar?$father_aadhar:''?>" >
                               
                                <span class="control-label">
                                    <?php echo form_error('father_aadhar'); ?>
                                </span>
                            </div>

                            <?php 
                                if(form_error('mothername')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";

                                    $this->db->where('parentsID',$student->parentID);
                                    $mother_name = $this->db->get('parents')->row()->mother_name;
                            ?>
                                <label for="name_id" class="control-label">
                                Mother Name <span class="text-red">*</span>
                                </label>
                               
                                    <input type="text" class="form-control" id="mother_name" name="mother_name" value="<?= $mother_name?$mother_name:''?>" >
                               
                                <span class="control-label">
                                    <?php echo form_error('name'); ?>
                                </span>
                            </div>

                            <?php 
                                if(form_error('mother_aadhar')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";

                                    $this->db->where('parentsID',$student->parentID);
                                    $mother_aadhar = $this->db->get('parents')->row()->mother_aadhar; 
                            ?>
                                <label for="mother_aadhar" class="control-label">
                                Mother Aadhar <span class="text-red">*</span>
                                </label>
                               
                                    <input type="text" class="form-control" id="mother_aadhar" name="mother_aadhar" value="<?= $mother_aadhar?$mother_aadhar:''?>" >
                               
                                <span class="control-label">
                                    <?php echo form_error('mother_aadhar'); ?>
                                </span>
                            </div>


                            
                            <?php 
                                if(form_error('phone')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="phone" class=" control-label">
                                    <?=$this->lang->line("student_phone")?>
                                </label>
                                
                                    <input type="text" class="form-control" id="phone"  maxlength="10" name="phone" value="<?=set_value('phone', $student->phone)?>" >
                               
                                <span id="error-message" class="control-label" style="color:red;">
                                    <?php echo form_error('phone'); ?>
                                </span>
                            </div>
  

                            <?php
                    if (form_error('alternative_phone1'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="alternative_phone1" class="  control-label">
                        Whatsapp
                    </label>
                    
                        <input type="text" class="form-control" id="alternative_phone1" name="alternative_phone1" value="<?= set_value('alternative_phone1', $student->alternative_phone1) ?>">
                    
                    <span class="  control-label">
                        <?php echo form_error('alternative_phone1'); ?>
                    </span>
                    </div><!------ End Alternative Phone Number  ----->

                    <?php
                    if (form_error('alternative_phone2'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="alternative_phone2" class="  control-label">
                    Alternative Phone2
                    </label>
                    
                        <input type="text" class="form-control" id="alternative_phone2" name="alternative_phone2" value="<?= set_value('alternative_phone2', $student->alternative_phone2) ?>">
                    
                    <span class="  control-label">
                        <?php echo form_error('alternative_phone2'); ?>
                    </span>
                </div><!------ End Alternative Phone Number2  ----->


                            <?php 
                                if(form_error('email')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="email" class="control-label">
                                    <?=$this->lang->line("student_email")?>
                                </label>
                               
                                    <input type="text" class="form-control" id="email" name="email" value="<?=set_value('email', $student->email)?>" >
                               
                                <span class=" control-label">
                                    <?php echo form_error('email'); ?>
                                </span>
                            </div>

                            <?php 
                                if(form_error('guargianID')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="guargianID" class="control-label">
                                    <?=$this->lang->line("student_guargian")?>
                                </label>
                              
                                    <?php
                                        $array = array('0' => $this->lang->line('student_select_guargian'));
                                        foreach ($parents as $parent) {
                                            $parentsemail = '';
                                            if($parent->email) {
                                                $parentsemail = " (" . $parent->email ." )";
                                            }
                                            $array[$parent->parentsID] = $parent->name.$parentsemail;
                                        }
                                        echo form_dropdown("guargianID", $array, set_value("guargianID", $student->parentID), "id='guargianID' class='form-control guargianID select2'");
                                    ?>
                               
                                <span class="control-label">
                                    <?php echo form_error('guargianID'); ?>
                                </span>
                            </div>

                           
                            <?php
                                if(form_error('photo'))
                                    echo "<div class='col-md-4 has-error' >";
                                else
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="photo_input" class="control-label">
                                    <?=$this->lang->line("student_photo")?>
                                </label>
                                <div class="photo-upload-wrap">
                                    <?php if (!empty($student->photo) && $student->photo != 'default.png' && $student->photo != 'defualt.png'): ?>
                                    <img src="<?= base_url('uploads/images/'.$student->photo) ?>" alt="Current Photo" style="max-width:80px;max-height:80px;border:2px solid #ddd;border-radius:4px;padding:2px;margin-bottom:6px;display:block;">
                                    <?php endif; ?>
                                    <label class="btn btn-primary btn-sm" style="cursor:pointer; margin-bottom:0;">
                                        <i class="fa fa-upload"></i> Change Photo
                                        <input type="file" id="photo_input" name="photo" accept="image/png,image/jpeg,image/gif" style="display:none;">
                                    </label>
                                    <span id="photo_filename" style="margin-left:8px; color:#666; font-size:12px;"></span>
                                    <button type="button" id="photo_clear_btn" class="btn btn-xs btn-default" style="display:none; margin-left:6px;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <br>
                                    <img id="photo_preview_img" class="photo-preview-img" src="" alt="New Preview">
                                    <span id="photo_error" class="val-error" style="display:none;"></span>
                                    <div style="font-size:11px;color:#999;margin-top:4px;">JPG, PNG, GIF &mdash; max 1 MB</div>
                                </div>
                                <span><?php echo form_error('photo'); ?></span>
                            </div>
                           

                            <?php 
                                if(form_error('mole1')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="mole1" class=" control-label">
                                    <?=$this->lang->line("mole1")?>
                                </label>
                               
                                    <textarea type="text" class="form-control" id="mole1" name="mole1"> <?=set_value('mole1', $student->mole1)?>   </textarea>   
                               
                                <span class=" control-label">
                                    <?php echo form_error('mole1'); ?>
                                </span>
                            </div>

                            <?php 
                                if(form_error('mole2')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="mole2" class="control-label">
                                    <?=$this->lang->line("mole2")?>
                                </label>
                               
                                    <textarea type="text" class="form-control" id="mole2" name="mole2"> <?=set_value('mole2', $student->mole2)?>   </textarea>   
                                
                                <span class="control-label">
                                    <?php echo form_error('mole2'); ?>
                                </span>
                            </div>


                        </div>
                    </div><!-- /.sap-body -->
                </div><!-- /.sap -->

                <!-- Section 3: Address Details -->
                <div class="sap">
                    <div class="sap-header collapsed" data-toggle="collapse" data-target="#sap-address">
                        <span class="sap-icon"><i class="fa fa-map-marker"></i></span>
                        <span class="sap-title">Student Address Details</span>
                        <i class="fa fa-chevron-up sap-chevron"></i>
                    </div>
                    <div id="sap-address" class="collapse sap-body sap-bg-3">
                        <div class="row">
                            <?php 
                                    if(form_error('address')) 
                                        echo "<div class='col-md-4 has-error' >";
                                    else     
                                        echo "<div class='col-md-4' >";
                                ?>
                                    <label for="address" class="control-label">
                                        <?=$this->lang->line("student_address")?>
                                    </label>
                                    
                                        <input type="text" class="form-control" id="address" name="address" value="<?=set_value('address', $student->address)?>" >
                                
                                    <span class="control-label">
                                        <?php echo form_error('address'); ?>
                                    </span>
                                </div>
                                <!-- <?php 
                            if(form_error('student_village')) 
                                echo "<div class='col-md-4 has-error' >";
                            else     
                                echo "<div class='col-md-4' >";
                        ?>
                            <label for="phone" class="control-label">
                                <?=$this->lang->line("student_village")?>
                            </label>
                            
                                <input type="text" class="form-control" id="village_name" name="village_name" value="<?=set_value('village_name', $student->village_name)?>" >
                        
                            <span class="control-label">
                                <?php echo form_error('student_village'); ?>
                            </span>
                        </div> -->

                        <?php
                if (form_error('student_village'))
                    echo "<div class='col-md-4 has-error' >";
                else
                    echo "<div class='col-md-4' >";
                ?>
                <label for="student_village" class="  control-label">
                    <?= $this->lang->line("student_village") ?> <span class="text-red">*</span>
                    <a title="Add Villege" target="_blank" href="<?= base_url('Village');?>" taret="_blank"> <i class="fa fa-plus" ></i></a>
                </label>
                
                    <!-- <input type="text" class="form-control" id="village_name" name="village_name" value="<?= set_value('village_name') ?>"> -->
                    <select id="village_name" name="village_name" class='form-control select2' >
                        <option value="0">--Select--</option>
                        <?php foreach($villages as $v){?>
                            <option value="<?= $v['villageID']?>" <?php if($student->villageID == $v['villageID']){echo 'selected';}?>> <?= $v['villageName']?> </option>
                       <?php  }?>
                    </select>
                
                <span class="  control-label">
                    <?php echo form_error('student_village'); ?>
                </span>
                </div><!-------- End village  ------>

                            <?php 
                                if(form_error('state')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="state" class="control-label">
                                    <?=$this->lang->line("student_state")?>
                                </label>
                                
                                    <input type="text" class="form-control" id="state" name="state" value="<?=set_value('state', $student->state)?>" >
                            
                                <span class="control-label">
                                    <?php echo form_error('state'); ?>
                                </span>
                            </div>  

                            <?php 
                                if(form_error('country')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="country" class="control-label">
                                    <?=$this->lang->line("student_country")?>
                                </label>
                                
                                    <?php
                                        $country['0'] = $this->lang->line('student_select_country');  
                                        foreach ($allcountry as $allcountryKey => $allcountryit) {
                                            $country[$allcountryKey] = $allcountryit;
                                        }
                                    ?>
                                    <?php 
                                        echo form_dropdown("country", $country, set_value("country", $student->country), "id='country' class='form-control select2'");
                                    ?>
                                
                                <span class="control-label">
                                    <?php echo form_error('country'); ?>
                                </span>
                            </div>

                            <?php 
                                if(form_error('aadharCardNumber')) 
                                    echo "<div class='col-md-4 has-error' >";
                                else     
                                    echo "<div class='col-md-4' >";
                            ?>
                                <label for="aadharCardNumber" class="control-label">
                                    <?=$this->lang->line("aadharCardNumber")?>
                                </label>
                                
                                    <input type="text" class="form-control" id="aadharCardNumber" name="aadharCardNumber" value="<?=set_value('aadharCardNumber', $student->aadharCardNumber)?>" >
                               
                                <span class="control-label">
                                    <?php echo form_error('aadharCardNumber'); ?>
                                </span>
                            </div>


                            <?php
                                if (form_error('ration_card'))
                                    echo "<div class='col-md-4 has-error' >";
                                else
                                    echo "<div class='col-md-4' >";
                                ?>
                                <label for="name_id" class=" control-label">
                                    Ration Card No <span class="text-red">*</span>
                                </label>
                                
                                    <input type="text" class="form-control" id="ration_card" name="ration_card" value="<?= set_value('ration_card', $student->ration_card) ?>">
                                
                                <span class=" control-label">
                                    <?php echo form_error('ration_card'); ?>
                                </span>
                                </div>
                                </div>

                                <div class="row">
                                    <?php
                                        if (form_error('studentType'))
                                            echo "<div class='col-md-4 has-error'>";
                                        else
                                            echo "<div class='col-md-4'>";
                                    ?>
                                        <label for="studentType" class="control-label">
                                            <?= $this->lang->line("studentType") ?>
                                        </label>
                                        <?php
                                            $studentTypeArr = array(3 => "DAY SCHOLAR", 1 => "TRANSPORT", 2 => "HOSTEL");
                                            echo form_dropdown("studentType", $studentTypeArr, set_value("studentType", $student->studentType), "id='studentType' class='form-control select2'");
                                        ?>
                                        <span class="control-label">
                                            <?php echo form_error('studentType'); ?>
                                        </span>
                                    </div>
                                </div>

                    </div><!-- /.sap-body -->
                </div><!-- /.sap -->

                <!-- Section 4: Transport Details -->
                <div class="sap" id="transport_div" style="display:none;">
                    <div class="sap-header collapsed" data-toggle="collapse" data-target="#sap-transport">
                        <span class="sap-icon"><i class="fa fa-bus"></i></span>
                        <span class="sap-title">Transport Details</span>
                        <i class="fa fa-chevron-up sap-chevron"></i>
                    </div>
                    <div id="sap-transport" class="collapse sap-body sap-bg-4">
                        <div class="row">
                            <?php
                                if (form_error('transportID'))
                                    echo "<div class='col-md-4 has-error'>";
                                else
                                    echo "<div class='col-md-4'>";
                            ?>
                                <label for="transportID" class="control-label">
                                    <?= $this->lang->line("tmember_route_name") ?> <span class="text-red">*</span>
                                </label>
                                <?php
                                    $tarray = array(0 => $this->lang->line("classes_select_route_name"));
                                    foreach ($transports as $transport) {
                                        $tarray[$transport->transportID] = $transport->route;
                                    }
                                    $selectedTransport = $studntTransportDetails ? $studntTransportDetails->transportID : 0;
                                    echo form_dropdown("transportID", $tarray, set_value("transportID", $selectedTransport), "id='transportID' class='form-control clear-dropdown select2'");
                                ?>
                                <span class="control-label">
                                    <?php echo form_error('transportID'); ?>
                                </span>
                            </div>

                            <div class="col-md-4">
                                <label for="pickup_id" class="control-label">
                                    Pickup Point <span class="text-red">*</span>
                                </label>
                                <select id="pickup_id" name="pickup_id" class="form-control clear-dropdown select2">
                                    <option value="0">Select Pickup point</option>
                                    <?php if (!empty($pickup_points)): ?>
                                        <?php $selectedPickup = $student->pickup_id ?? 0; ?>
                                        <?php foreach ($pickup_points as $pp): ?>
                                            <option value="<?= $pp['id'] ?>" <?= ($pp['id'] == $selectedPickup) ? 'selected' : '' ?>><?= $pp['pickup_point'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <?php
                                if (form_error('tbalance'))
                                    echo "<div class='col-md-4 has-error'>";
                                else
                                    echo "<div class='col-md-4'>";
                            ?>
                                <label for="tbalance" class="control-label">
                                    <?= $this->lang->line("tmember_tfee") ?> <span class="text-red">*</span>
                                </label>
                                <?php $selectedTBalance = $studntTransportDetails ? $studntTransportDetails->tbalance : '0.00'; ?>
                                <input type="text" class="form-control clear-dropdown" id="tbalance" name="tbalance" value="<?= set_value('tbalance', $selectedTBalance) ?>" readonly>
                                <span class="control-label">
                                    <?php echo form_error('tbalance'); ?>
                                </span>
                            </div>
                        </div>
                    </div><!-- /.sap-body -->
                </div><!-- /.sap -->

                <!-- Section 5: Hostel Details -->
                <div class="sap" id="hostel_div" style="display:none;">
                    <div class="sap-header collapsed" data-toggle="collapse" data-target="#sap-hostel">
                        <span class="sap-icon"><i class="fa fa-home"></i></span>
                        <span class="sap-title">Hostel Details</span>
                        <i class="fa fa-chevron-up sap-chevron"></i>
                    </div>
                    <div id="sap-hostel" class="collapse sap-body sap-bg-5">
                        <div class="row">
                            <?php
                                if (form_error('hostelID'))
                                    echo "<div class='col-md-4 has-error'>";
                                else
                                    echo "<div class='col-md-4'>";
                            ?>
                                <label for="hostelID" class="control-label">
                                    <?= $this->lang->line("hmember_hname") ?> <span class="text-red">*</span>
                                </label>
                                <?php
                                    $harray = array(0 => $this->lang->line("hmember_select_hostel_name"));
                                    foreach ($hostels as $hostel) {
                                        $harray[$hostel->hostelID] = $hostel->name;
                                    }
                                    $selectedHostel = $studntHostelDetails ? $studntHostelDetails->hostelID : 0;
                                    echo form_dropdown("hostelID", $harray, set_value("hostelID", $selectedHostel), "id='hostelID' class='form-control clear-dropdown select2'");
                                ?>
                                <span class="control-label">
                                    <?php echo form_error('hostelID'); ?>
                                </span>
                            </div>

                            <?php
                                if (form_error('categoryID'))
                                    echo "<div class='col-md-4 has-error'>";
                                else
                                    echo "<div class='col-md-4'>";
                            ?>
                                <label for="categoryID" class="control-label">
                                    <?= $this->lang->line("hmember_class_type") ?> <span class="text-red">*</span>
                                </label>
                                <?php
                                    $carray = array(0 => $this->lang->line("hmember_select_class_type"));
                                    if (customCompute($categorys)) {
                                        foreach ($categorys as $category) {
                                            $carray[$category->categoryID] = $category->class_type;
                                        }
                                    }
                                    $selectedCategory = $studntHostelDetails ? $studntHostelDetails->categoryID : 0;
                                    echo form_dropdown("categoryID", $carray, set_value("categoryID", $selectedCategory), "id='categoryID' class='form-control clear-dropdown select2'");
                                ?>
                                <span class="control-label">
                                    <?php echo form_error('categoryID'); ?>
                                </span>
                            </div>
                        </div>
                    </div><!-- /.sap-body -->
                </div><!-- /.sap -->

                <!-- Section 6: Reference & Sibling Details -->
                <div class="sap">
                    <div class="sap-header" data-toggle="collapse" data-target="#sap-reference">
                        <span class="sap-icon"><i class="fa fa-users"></i></span>
                        <span class="sap-title">Reference &amp; Sibling Details</span>
                        <i class="fa fa-chevron-up sap-chevron"></i>
                    </div>
                    <div id="sap-reference" class="collapse in sap-body sap-bg-6">
                    <div class="row">

                        <!-- Refered By -->
                        <?php
                        $refered_by_stored = $student->refered_by;
                        $is_others_selected = (strpos($refered_by_stored, 'others-') === 0);
                        $others_text_value = $is_others_selected ? substr($refered_by_stored, 7) : '';
                        ?>
                        <div class="col-md-3 <?= form_error('refered_by') ? 'has-error' : '' ?>">
                            <label for="refered_by" class="control-label">
                                Refered By
                                <a title="Add Teacher" target="_blank" href="<?= base_url('teacher/add'); ?>"><i class="fa fa-plus"></i></a>
                            </label>
                            <select id="refered_by" name="refered_by" class="form-control select2">
                                <option value="">--Select--</option>
                                <option value="others" <?= $is_others_selected ? 'selected' : '' ?>>Others</option>
                                <?php foreach ($teachers as $k => $v) { ?>
                                    <option value="<?= $k ?>" <?php if (!$is_others_selected && $student->refered_by == $k) { echo 'selected'; } ?>><?= $v ?></option>
                                <?php } ?>
                            </select>
                            <div id="refered_by_other_div" style="<?= $is_others_selected ? '' : 'display:none;' ?>margin-top:6px;">
                                <input type="text" class="form-control" id="refered_by_other" name="refered_by_other" placeholder="Enter referral name" value="<?= htmlspecialchars($others_text_value) ?>">
                            </div>
                            <span class="control-label text-danger"><?= form_error('refered_by') ?></span>
                        </div>

                        <!-- Siblings -->
                        <div class="col-md-9">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                                <label class="control-label" style="margin:0; font-weight:600;">Siblings</label>
                                <button type="button" class="btn btn-success btn-sm" id="add_sibling_btn">
                                    <i class="fa fa-plus"></i> Add Sibling
                                </button>
                            </div>
                            <table class="table table-bordered table-condensed" id="sibling_table" style="margin-bottom:0;">
                                <thead>
                                    <tr style="background:#f5f5f5;">
                                        <th style="width:30%">Class</th>
                                        <th style="width:25%">Section</th>
                                        <th>Student</th>
                                        <th style="width:46px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="sibling_tbody">
                                    <?php if (!empty($existing_siblings)): ?>
                                    <?php foreach ($existing_siblings as $sib): ?>
                                    <tr class="sibling-existing-row"
                                        data-classesid="<?= $sib->classesID ?>"
                                        data-sectionid="<?= $sib->sectionID ?>"
                                        data-studentid="<?= $sib->sibling_studentID ?>"
                                        data-studentname="<?= htmlspecialchars($sib->name) ?>">
                                        <td>
                                            <select name="sibling_classesID[]" class="form-control sibling-class-select">
                                                <option value="">--Select Class--</option>
                                                <?php foreach($classes as $cls): ?>
                                                <option value="<?= $cls->classesID ?>" <?= ($cls->classesID == $sib->classesID) ? 'selected' : '' ?>><?= $cls->classes ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="sibling_sectionID[]" class="form-control sibling-section-select">
                                                <option value="">--Select Section--</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="sibling_studentID[]" class="form-control sibling-student-select">
                                                <option value="">--Select Student--</option>
                                            </select>
                                        </td>
                                        <td class="text-center" style="vertical-align:middle;">
                                            <button type="button" class="btn btn-danger btn-sm remove-sibling-btn"><i class="fa fa-minus"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                    <!-- template row (hidden) -->
                                    <tr id="sibling_row_template" style="display:none;">
                                        <td>
                                            <select name="sibling_classesID[]" class="form-control sibling-class-select">
                                                <option value="">--Select Class--</option>
                                                <?php foreach($classes as $cls): ?>
                                                <option value="<?= $cls->classesID ?>"><?= $cls->classes ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="sibling_sectionID[]" class="form-control sibling-section-select">
                                                <option value="">--Select Section--</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="sibling_studentID[]" class="form-control sibling-student-select">
                                                <option value="">--Select Student--</option>
                                            </select>
                                        </td>
                                        <td class="text-center" style="vertical-align:middle;">
                                            <button type="button" class="btn btn-danger btn-sm remove-sibling-btn"><i class="fa fa-minus"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    </div><!-- /.sap-body -->
                </div><!-- /.sap -->

                        </div><!-- /.student-accordion wrapper -->
                    <div class="student-btn-info form-group">
                        <div class="add-student-btn">
                        <input type="submit" class="primary-btn" value="<?=$this->lang->line("update_student")?>" >
                        </div>
                    </div>
                </form>

            
    </div><!-- Body -->
</div><!-- /.box -->

<script type="text/javascript">
$(".select2").select2();

// ── Datepickers (no future dates) ──
$('#dob').datepicker({ startView: 2, format: 'dd-mm-yyyy', endDate: '0d', autoclose: true });
$('#admission_date').datepicker({ format: 'dd-mm-yyyy', endDate: '0d', autoclose: true });

// ── Mark required field groups ──
$(function() {
    $('.student-form-info .col-md-4, .student-form-info .col-md-3').each(function() {
        if ($(this).find('label .text-red, label .text-danger').length > 0)
            $(this).addClass('has-required');
    });
});

// ── Auto-capitalize first letter in name fields ──
$(document).on('input', '#first_name, #last_name, #father_name, #mother_name', function() {
    var pos = this.selectionStart, v = $(this).val();
    if (v.length > 0) {
        $(this).val(v.charAt(0).toUpperCase() + v.slice(1));
        try { this.setSelectionRange(pos, pos); } catch(e) {}
    }
});

// ── Name fields: letters only ──
$(document).on('keypress', '#first_name, #last_name, #father_name, #mother_name, #religion, #state', function(e) {
    if (!/[a-zA-Z\s.\-']/.test(String.fromCharCode(e.which || e.keyCode))) e.preventDefault();
});

// ── Trim spaces on blur ──
$(document).on('blur', '.student-form-info input[type="text"], .student-form-info textarea', function() {
    $(this).val($.trim($(this).val()));
});

// ── Phone: digits only, max 10 ──
$(document).on('keypress', '#phone, #alternative_phone1, #alternative_phone2', function(e) {
    var c = e.which || e.keyCode; if (c < 48 || c > 57) e.preventDefault();
});
$(document).on('input', '#phone, #alternative_phone1, #alternative_phone2', function() {
    $(this).val($(this).val().replace(/\D/g, '').slice(0, 10));
});

// ── Photo upload: inline preview ──
$('#photo_input').on('change', function() {
    var file = this.files[0]; $('#photo_error').hide();
    if (!file) return;
    if (!file.type.match(/^image\/(jpeg|png|gif)$/)) { $('#photo_error').text('Only JPG, PNG or GIF files allowed.').show(); $(this).val(''); return; }
    if (file.size > 1048576) { $('#photo_error').text('File size must not exceed 1 MB.').show(); $(this).val(''); return; }
    $('#photo_filename').text(file.name); $('#photo_clear_btn').show();
    var reader = new FileReader();
    reader.onload = function(e) { $('#photo_preview_img').attr('src', e.target.result).show(); };
    reader.readAsDataURL(file);
});
$('#photo_clear_btn').on('click', function() {
    $('#photo_input').val(''); $('#photo_filename').text('');
    $('#photo_preview_img').hide().attr('src', ''); $(this).hide(); $('#photo_error').hide();
});

// (replaced original block — new lines start below)
var _dummy_placeholder_edit = true;

$('#username').keyup(function() {
    $(this).val($(this).val().replace(/\s/g, ''));
});

$(document).on("focusout","#roll",function(){
    var classesID = $("#classesID").val();
    var sectionID = $("#sectionID").val();
    var rollNo = $("#roll").val();

    $.ajax({
            type: 'POST',
            url: "<?=base_url('student/checkRoll')?>",
            data: {"classesID":classesID,"sectionID":sectionID,"rollNo":rollNo},
            dataType: "html",
            success: function(data) {
              $('.err').html(data);
            }
        });
})
$('#classesID').change(function(event) {
    var classesID = $(this).val();
    if(classesID === '0') {
        $('#classesID').val(0);
    } else {
        $.ajax({
            type: 'POST',
            url: "<?=base_url('student/sectioncall')?>",
            data: "id=" + classesID,
            dataType: "html",
            success: function(data) {
               $('#sectionID').html(data);
            }
        });

        $.ajax({
            type: 'POST',
            url: "<?=base_url('student/optionalsubjectcall')?>",
            data: "id=" + classesID,
            dataType: "html",
            success: function(data2) {
                $('#optionalSubjectID').html(data2);
            }
        });
    }
});

$(document).on('click', '#close-preview', function(){ 
    $('.image-preview').popover('hide');
    // Hover befor close the preview
    $('.image-preview').hover(
        function () {
           $('.image-preview').popover('show');
           $('.content').css('padding-bottom', '130px');
        }, 
         function () {
           $('.image-preview').popover('hide');
           $('.content').css('padding-bottom', '20px');
        }
    );    
});

$(function() {
    // Create the close button
    var closebtn = $('<button/>', {
        type:"button",
        text: 'x',
        id: 'close-preview',
        style: 'font-size: initial;',
    });
    closebtn.attr("class","close pull-right");
    // Set the popover default content
    $('.image-preview').popover({
        trigger:'manual',
        html:true,
        title: "<strong>Preview</strong>"+$(closebtn)[0].outerHTML,
        content: "There's no image",
        placement:'bottom'
    });
    // Clear event
    $('.image-preview-clear').click(function(){
        $('.image-preview').attr("data-content","").popover('hide');
        $('.image-preview-filename').val("");
        $('.image-preview-clear').hide();
        $('.image-preview-input input:file').val("");
        $(".image-preview-input-title").text("<?=$this->lang->line('student_file_browse')?>"); 
    }); 
    // Create the preview image
    $(".image-preview-input input:file").change(function (){     
        var img = $('<img/>', {
            id: 'dynamic',
            width:250,
            height:200,
            overflow:'hidden'
        });      
        var file = this.files[0];
        var reader = new FileReader();
        // Set preview image into the popover data-content
        reader.onload = function (e) {
            $(".image-preview-input-title").text("<?=$this->lang->line('student_file_browse')?>");
            $(".image-preview-clear").show();
            $(".image-preview-filename").val(file.name);            
            img.attr('src', e.target.result);
            $(".image-preview").attr("data-content",$(img)[0].outerHTML).popover("show");
            $('.content').css('padding-bottom', '130px');
        }        
        reader.readAsDataURL(file);
    });  
});


    var studentType = '<?= $student->studentType?>';
        if(studentType == 1)
        {   
            // $('.transport').removeClass('hide');
            // $('.hostel').addClass('hide');
            $("#transport_div").show();
            $("#hostel_div").hide();
        }
        else if(studentType==2){
            // $('.transport').addClass('hide');
            // $('.hostel').removeClass('hide');
            $("#transport_div").hide();
            $("#hostel_div").show();
        }
        else{
            // $('.transport').addClass('hide');
            // $('.hostel').addClass('hide');
            $("#transport_div").hide();
            $("#hostel_div").hide();
        }


 $('#studentType').change(function() {
    $(".clear-dropdown").val('');
        $("#categoryID").val('0').change();;
        $("#pickup_id").val('0').change();;
        $("#hostelID").val('0').change();;
        $("#transportID").val('0').change();;
        
        var studentType = $('#studentType').val();
        if(studentType == 1)
        {   
            // $('.transport').removeClass('hide');
            // $('.hostel').addClass('hide');
            $("#transport_div").show();
            $("#hostel_div").hide();
        }
        else if(studentType==2){
            // $('.transport').addClass('hide');
            // $('.hostel').removeClass('hide');
            $("#transport_div").hide();
            $("#hostel_div").show();
        }
        else{
            // $('.transport').addClass('hide');
            // $('.hostel').addClass('hide');
            $("#transport_div").hide();
            $("#hostel_div").hide();
        }
    });

    var transportID = $('#transportID').val();
   
    if(transportID == 0 || transportID == "" || transportID == null) {
        $('#tbalance').val("0.00");
    } else {
        $.ajax({
            type: 'POST',
            url: "<?=base_url('tmember/transport_fare')?>",
            data: "id=" + transportID,
            dataType: "html",
            success: function(data) {
               $('#tbalance').val(data)
            }
        });
    }


    $('#transportID').change(function() {
        
        var transportID = $(this).val();
         if(transportID == 0 || transportID == "" || transportID == null) {
             $('#tbalance').val("0.00");
         } else {
             $.ajax({
                 type: 'POST',
                 // url: "<?=base_url('tmember/transport_fare')?>",
                 url: "<?=base_url('Student/get_pickup_points')?>",
                 data: "id=" + transportID,
                 dataType: "html",
                 success: function(data) {
                 //    $('#tbalance').val(data)
                    $('#pickup_id').html(data)
                 }
             });
         }
     });
 
     $('#hostelID').change(function(event) {
         $('#categoryID').val(0).select2()
         var hostelID = $(this).val();
         if(hostelID == 0 || hostelID == "" || hostelID == null) {
             $('#categoryID').val(0).select2()
         } else {
             $.ajax({
                 type: 'POST',
                 url: "<?=base_url('hmember/categorycall')?>",
                 data: "id=" + hostelID,
                 dataType: "html",
                 success: function(data) {
                 $('#categoryID').html(data)
                 }
             });
         }
 });

 $('#pickup_id').change(function() {
        
        var pickup_id = $(this).val();
         if(pickup_id == 0 || pickup_id == "" || pickup_id == null) {
             $('#tbalance').val("0.00");
         } else {
             $.ajax({
                 type: 'POST',
                 // url: "<?=base_url('tmember/transport_fare')?>",
                 url: "<?=base_url('Student/transport_fare')?>",
                 data: "id=" + pickup_id,
                 dataType: "html",
                 success: function(data) {
                    $('#tbalance').val(data) 
                 }
             });
         }
     });


     $(document).on("keyup",".id_card",function(){
    var f_name= $("#first_name").val();
    var l_name= $("#last_name").val();
    var idcard = f_name + " " + l_name;
    // alert (idcart);
    $("#name_id").val(idcard);
});


$(document).ready(function(){
    // Allow only numbers
    $('#phone').on('keypress', function (e) {
        var charCode = e.which ? e.which : e.keyCode;
        if (charCode < 48 || charCode > 57) {
            e.preventDefault();
        }
    });

    // Limit to max 10 characters
    $('#phone').on('input', function () {
        var maxLength = 10;
        if ($(this).val().length > maxLength) {
            $(this).val($(this).val().slice(0, maxLength));
        }
    });

    // Validate when the user leaves the input field
    $('#phone').on('blur', function () {
        var phoneNumber = $(this).val();
        if (phoneNumber.length < 10) {
            $('#error-message').text('Phone number must be exactly 10 digits.');
        } else {
            $('#error-message').text(''); // Clear error message if valid
        }
    });
});


// Refered By "Others" toggle
$('#refered_by').on('change', function() {
    if ($(this).val() === 'others') {
        $('#refered_by_other_div').show();
    } else {
        $('#refered_by_other_div').hide().find('input').val('');
    }
});

// Siblings section
var siblingRowCount = 0;

function loadSiblingSections(classSelect, targetSectionSelect, targetStudentSelect, preSelectedSectionID, preSelectedStudentID, preSelectedStudentName) {
    var classesID = classSelect.val();
    if ($.fn.select2 && targetSectionSelect.hasClass('select2-hidden-accessible')) {
        targetSectionSelect.select2('destroy');
    }
    targetSectionSelect.html('<option value="0">Select Section</option>');
    targetStudentSelect.html('<option value="">--Select Student--</option>');
    if (classesID > 0) {
        $.ajax({
            type: 'POST',
            url: "<?= base_url('student/sectioncall') ?>",
            data: "id=" + classesID,
            dataType: "html",
            success: function(data) {
                targetSectionSelect.html(data);
                targetSectionSelect.select2({width: '100%'});
                if (preSelectedSectionID) {
                    targetSectionSelect.val(preSelectedSectionID).trigger('change.select2');
                    loadSiblingStudents(classesID, preSelectedSectionID, targetStudentSelect, preSelectedStudentID);
                }
            }
        });
    }
}

function initSiblingRow(row) {
    row.find('.sibling-class-select').select2({width: '100%'});
}

function loadSiblingStudents(classesID, sectionID, targetStudentSelect, preSelectedStudentID) {
    if ($.fn.select2 && targetStudentSelect.hasClass('select2-hidden-accessible')) {
        targetStudentSelect.select2('destroy');
    }
    $.ajax({
        type: 'GET',
        url: "<?= base_url('student/get_students_by_class_section') ?>",
        data: {classesID: classesID, sectionID: sectionID},
        dataType: "json",
        success: function(data) {
            targetStudentSelect.html('<option value="">--Select Student--</option>');
            $.each(data, function(i, s) {
                var selected = (preSelectedStudentID && s.studentID == preSelectedStudentID) ? ' selected' : '';
                var label = s.name + (s.roll ? ' (' + s.roll + ')' : '');
                targetStudentSelect.append('<option value="' + s.studentID + '"' + selected + '>' + label + '</option>');
            });
            targetStudentSelect.select2({width: '100%', placeholder: '--Select Student--'});
        }
    });
}

// Pre-populate existing sibling rows
$('#sibling_tbody .sibling-existing-row').each(function() {
    var row = $(this);
    var classesID = row.data('classesid');
    var sectionID = row.data('sectionid');
    var studentID = row.data('studentid');
    var classSelect = row.find('.sibling-class-select');
    var sectionSelect = row.find('.sibling-section-select');
    var studentSelect = row.find('.sibling-student-select');
    initSiblingRow(row);
    if (classesID) {
        loadSiblingSections(classSelect, sectionSelect, studentSelect, sectionID, studentID, null);
    }
});

$('#add_sibling_btn').on('click', function() {
    var template = $('#sibling_row_template').clone();
    template.attr('id', 'sibling_row_' + siblingRowCount);
    template.removeAttr('style');
    $('#sibling_tbody').append(template);
    initSiblingRow(template);
    siblingRowCount++;
});

$(document).on('click', '.remove-sibling-btn', function() {
    $(this).closest('tr').remove();
});

$(document).on('change', '.sibling-class-select', function() {
    var classesID = $(this).val();
    var row = $(this).closest('tr');
    var sectionSelect = row.find('.sibling-section-select');
    var studentSelect = row.find('.sibling-student-select');
    if ($.fn.select2 && sectionSelect.hasClass('select2-hidden-accessible')) {
        sectionSelect.select2('destroy');
    }
    if ($.fn.select2 && studentSelect.hasClass('select2-hidden-accessible')) {
        studentSelect.select2('destroy');
    }
    sectionSelect.html('<option value="">--Select Section--</option>');
    studentSelect.html('<option value="">--Select Student--</option>');
    if (classesID > 0) {
        $.ajax({
            type: 'POST',
            url: "<?= base_url('student/sectioncall') ?>",
            data: "id=" + classesID,
            dataType: "html",
            success: function(data) {
                sectionSelect.html(data);
                sectionSelect.off('change.sibLoad').on('change.sibLoad', function() {
                    var sectionID = $(this).val();
                    if (sectionID > 0) {
                        loadSiblingStudents(classesID, sectionID, studentSelect, null);
                    }
                });
                sectionSelect.select2({width: '100%'});
            }
        });
    }
});


// ── Form Validation ──
function svErr($el, msg) {
    var $p = $el.closest('.col-md-4,.col-md-3,.form-group');
    $p.addClass('has-error');
    $p.find('.val-error').remove();
    $el.after('<span class="val-error"><i class="fa fa-exclamation-circle"></i> '+msg+'</span>');
}
function svClear($form) {
    $form.find('.col-md-4,.col-md-3,.form-group').removeClass('has-error');
    $form.find('.val-error').remove();
    $('#error-message').text('');
}

$('.student-form-info').on('submit', function(e) {
    svClear($(this));
    var ok = true, $f = $(this);
    $f.find('input[type="text"]').each(function() { $(this).val($.trim($(this).val())); });

    [['#registerNO','Admission No is required'],
     ['#first_name','First Name is required'],
     ['#last_name','Last Name is required'],
     ['#name_id','ID Card Name is required'],
     ['#father_name','Father Name is required'],
     ['#roll','Roll No is required']
    ].forEach(function(r) {
        var $el=$f.find(r[0]);
        if ($el.length&&!$.trim($el.val())) { svErr($el,r[1]); ok=false; }
    });

    [['#classesID','Please select a Class'],['#sectionID','Please select a Section']].forEach(function(r) {
        var $el=$f.find(r[0]), v=$el.val();
        if ($el.length&&(!v||v=='0')) { svErr($el,r[1]); ok=false; }
    });

    ['#first_name','#last_name','#father_name','#mother_name'].forEach(function(id) {
        var $el=$f.find(id), v=$.trim($el.val());
        if ($el.length&&v&&!/^[a-zA-Z\s.\-']+$/.test(v)) { svErr($el,'Only letters, spaces and hyphens are allowed'); ok=false; }
    });

    var ph=$.trim($f.find('#phone').val());
    if (ph&&!/^\d{10}$/.test(ph)) { svErr($f.find('#phone'),'Phone must be exactly 10 digits'); ok=false; }

    var em=$.trim($f.find('#email').val());
    if (em&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { svErr($f.find('#email'),'Enter a valid email address'); ok=false; }

    var dob=$.trim($f.find('#dob').val());
    if (dob) {
        var p=dob.split('-');
        if (p.length===3&&new Date(+p[2],+p[1]-1,+p[0])>new Date()) { svErr($f.find('#dob'),'Date of birth cannot be a future date'); ok=false; }
    }

    if (!ok) {
        e.preventDefault();
        var $first=$f.find('.has-error').first();
        if ($first.length) $('html,body').animate({scrollTop:$first.offset().top-120},400);
    }
});
</script>
