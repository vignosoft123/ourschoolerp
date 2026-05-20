<style>
/* ── Required field indicator ── */
.student-form-info .has-required .form-control {
    border-left: 3px solid #27ae60 !important;
    background-color: #fff !important;
}
.student-form-info .has-required .select2-container .select2-choice,
.student-form-info .has-required .select2-container .select2-choices {
    border-left: 3px solid #27ae60 !important;
}
.student-form-info .has-required > label { font-weight: 600; color: #2c3e50; }
.bg-lpurple { background-color: #fff !important; border-left: none !important; }
.val-error { color: #c0392b; font-size: 12px; display: block; margin-top: 3px; }
.val-error i { margin-right: 3px; }
.photo-upload-wrap .photo-preview-img {
    max-width: 100px; max-height: 100px;
    border: 2px solid #ddd; border-radius: 6px;
    padding: 2px; margin-top: 6px; display: none;
}

/* ── Accordion Panels ── */
.student-accordion { margin-bottom: 10px; }
.sap {
    border: 1px solid #dde0e6;
    border-radius: 10px;
    margin-bottom: 14px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}
.sap-header {
    background: linear-gradient(135deg, #0cc035 0%, #0a9d2b 100%);
    color: #fff;
    padding: 13px 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    user-select: none;
    transition: background 0.2s;
}
.sap-header:hover { background: linear-gradient(135deg, #0aa82e 0%, #088a25 100%); }
.sap-header .sap-icon { font-size: 17px; width: 22px; text-align: center; }
.sap-header .sap-title { flex: 1; font-size: 15px; font-weight: 600; letter-spacing: 0.3px; }
.sap-header .sap-badge {
    background: rgba(255,255,255,0.25);
    font-size: 11px;
    border-radius: 10px;
    padding: 2px 8px;
}
.sap-chevron { transition: transform 0.3s ease; font-size: 13px; }
.sap-header.collapsed .sap-chevron { transform: rotate(180deg); }
.sap-body { background: #fff; padding: 22px 20px 10px; }
.sap-body .col-md-4,
.sap-body .col-md-3,
.sap-body .col-md-9 { margin-bottom: 14px; }
/* Required-fields legend note */
.req-legend { font-size: 11px; color: #888; margin-bottom: 12px; }
.req-legend .text-red { color: #e74c3c; font-weight: bold; }
/* Per-section background tints */
.sap-bg-1 { background: #f0f8ff !important; }
.sap-bg-2 { background: #f0fff4 !important; }
.sap-bg-3 { background: #fffbf0 !important; }
.sap-bg-4 { background: #fff8f0 !important; }
.sap-bg-5 { background: #f8f0ff !important; }
.sap-bg-6 { background: #fff0f8 !important; }
</style>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-student"></i> Student </h3>

        <ol class="breadcrumb">
            <li><a href="<?= base_url("dashboard/index") ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
            <li><a href="<?= base_url("student/index") ?>"><?= $this->lang->line('menu_student') ?></a></li>
            <li class="active"><?= $this->lang->line('menu_add') ?> <?= $this->lang->line('menu_student') ?></li>
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
                if (form_error('registerNO'))
                    echo "<div class='col-md-4 has-error' >";
                else
                    echo "<div class='col-md-4' >";
                ?>
                <label for="registerNO" class="  control-label">
                    <?= $this->lang->line("student_registerNO") ?> <span class="text-red">*</span>
                </label>
                    <input type="text" class="form-control" id="registerNO" name="registerNO" value="<?= set_value('registerNO', $randomAdmissionCode) ?>" <?= $randomAdmissionCode ? 'readonly' : '';?> >
                <span class="  control-label">
                    <?php echo form_error('registerNO'); ?>
                </span>
                </div> <!-------end admission ------->

                <?php
                    if (form_error('admission_date'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="admission_date" class="  control-label">
                        <?= $this->lang->line("student_admission_date") ?>
                    </label>
                    
                        <input type="text" class="form-control" id="admission_date" name="admission_date" value="<?= set_value('admission_date') ? set_value('admission_date') : date("d-m-Y") ?>">
                    
                    <span class="  control-label">
                        <?php echo form_error('admission_date'); ?>
                    </span>
                </div> <!------ Admission date ----->

                <?php
                            if (form_error('pen_number'))
                                echo "<div class='col-md-4 has-error' >";
                            else
                                echo "<div class='col-md-4' >";
                            ?>
                            <label for="pen_number" class=" control-label">
                            PEN Number
                            </label>
                            
                                <input type="text" class="form-control" id="pen_number" name="pen_number" value="<?= set_value('pen_number') ?>">
                            
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
                            
                                <input type="text" class="form-control" id="child_id" name="child_id" value="<?= set_value('child_id') ?>">
                            
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
                            
                                <input type="text" class="form-control" id="medium" name="medium" value="<?= set_value('medium') ?>">
                            
                            <span class="  control-label">
                                <?php echo form_error('medium'); ?>
                            </span>
                        </div>


                         <?php
                            if (form_error('rf_id'))
                                echo "<div class='col-md-4 has-error' >";
                            else
                                echo "<div class='col-md-4' >";
                            ?>
                            <label for="name_id" class=" control-label">
                            RF ID
                            </label>
                            
                                <input type="text" class="form-control" id="rf_id" name="rf_id" value="<?= set_value('rf_id') ?>">
                            
                            <span class="  control-label">
                                <?php echo form_error('rf_id'); ?>
                            </span>
                        </div><!------End RF ID ----->

                <?php
                    if (form_error('classesID'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="classesID" class="control-label">
                        <?= $this->lang->line("student_classes") ?> <span class="text-red">*</span>
                    </label>
                        <?php
                        $classArray = array(0 => $this->lang->line("student_select_class"));
                        foreach ($classes as $classa) {
                            $classArray[$classa->classesID] = $classa->classes;
                        }
                        echo form_dropdown("classesID", $classArray, set_value("classesID"), "id='classesID' class='form-control select2'");
                        ?>
                    <span class=" control-label">
                        <?php echo form_error('classesID'); ?>
                    </span>
                    </div><!------ Class----->

                    <?php
                        if (form_error('sectionID'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="sectionID" class="control-label">
                            <?= $this->lang->line("student_section") ?> <span class="text-red">*</span>
                            <a title="Add Section" target="_blank" href="<?= base_url('section/add');?>"> <i class="fa fa-plus" ></i></a>
                        </label>

                            <?php
                            $sectionArray = array(0 => $this->lang->line("student_select_section"));
                            if ($sections != "empty") {
                                foreach ($sections as $section) {
                                    $sectionArray[$section->sectionID] = $section->section;
                                }
                            }

                            $sID = 0;
                            if ($sectionID == 0) {
                                $sID = 0;
                            } else {
                                $sID = $sectionID;
                            }

                            echo form_dropdown("sectionID", $sectionArray, set_value("sectionID", $sID), "id='sectionID' class='form-control select2'");
                            ?>
                        <span class="  control-label">
                            <?php echo form_error('sectionID'); ?>
                        </span>
                        </div> <!------End Section ----->
                       
                        <?php
                            if (form_error('roll')) 
                                echo "<div class='col-md-4 has-error' >";
                            else
                                echo "<div class='col-md-4' >";
                            ?>
                            <label for="roll" class="  control-label">
                                <?= $this->lang->line("student_roll") ?> <span class="text-red">*</span>
                            </label>
                                <input type="text" class="form-control" id="roll" name="roll" value="<?= set_value('roll') ?>">
                            <span class=" err control-label">
                                <?php echo form_error('roll'); ?>
                            </span>
                        </div><!------End roll ----->
                        <?php
                            if (form_error('joined_class'))
                                echo "<div class='col-md-4 has-error' >";
                            else
                                echo "<div class='col-md-4' >";
                            ?>
                            <label for="classesID" class="  control-label">
                                Joined Class <span class="text-red">*</span>
                            </label>
                                <?php
                                $classArray = array(0 => $this->lang->line("student_select_class"));
                                foreach ($classes as $classa) {
                                    $classArray[$classa->classesID] = $classa->classes;
                                }
                                echo form_dropdown("joined_class", $classArray, set_value("joined_class"), "id='joined_class' class='form-control select2'");
                                ?>
                            <!-- <span class="  control-label">-->
                            <!--    <?php //echo form_error('classesID'); ?>-->
                            <!--</span>-->
                        </div>  <!------end Joined class----->

                        <div class="col-md-4 <?= form_error('studentGroupID') ? ' has-error' : ''  ?>">
                            <label for="studentGroupID" class="  control-label">
                                <?= $this->lang->line("student_studentgroup") ?>
                            </label>
                            
                                <?php
                                $groupArray = array(0 => $this->lang->line("student_select_studentgroup"));
                                if (customCompute($studentgroups)) {
                                    foreach ($studentgroups as $studentgroup) {
                                        $groupArray[$studentgroup->studentgroupID] = $studentgroup->group;
                                    }
                                }
                                echo form_dropdown("studentGroupID", $groupArray, set_value("studentGroupID"), "id='studentGroupID' class='form-control  select2'");
                                ?>
                            <span class="  control-label">
                                <?php echo form_error('studentGroupID'); ?>
                            </span>
                        </div><!------ End group ----->

                        <div class="col-md-4 <?= form_error('remarks') ? ' has-error' : ''  ?>">
                        <label for="remarks" class="  control-label">
                            <?= $this->lang->line("student_remarks") ?>
                        </label>
                            <input type="text" class="form-control" id="remarks" name="remarks" value="<?= set_value('remarks') ?>">
                        <span class="  control-label">
                            <?php echo form_error('remarks'); ?>
                        </span>
                    </div><!------ End remarks ----->
                    <div class="col-md-4 <?= form_error('optionalSubjectID') ? ' has-error' : ''  ?>">
                        <label for="optionalSubjectID" class="  control-label">
                            <?= $this->lang->line("student_optionalsubject") ?>
                        </label>
                            <?php
                            $optionalSubjectArray = array(0 => $this->lang->line("student_select_optionalsubject"));
                            if ($optionalSubjects != "empty") {
                                foreach ($optionalSubjects as $optionalSubject) {
                                    $optionalSubjectArray[$optionalSubject->subjectID] = $optionalSubject->subject;
                                }
                            }

                            echo form_dropdown("optionalSubjectID", $optionalSubjectArray, set_value("optionalSubjectID", $optionalSubjectID), "id='optionalSubjectID' class='form-control select2'");
                            ?>
                        </div>
                    <span class="control-label">
                        <?php echo form_error('optionalSubjectID'); ?>
                    </span> <!------ end optional subject ----->

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
                                <option value="1">Telugu</option>
                                <option value="2">English</option>
                                <option value="3">Hindi</option>
                                <option value="4">Kannada</option>
                                <option value="5">Malayalam</option> 
                                <option value="6">Urdhu</option> 
                            </select>
                        <span class="  control-label">
                            <?php echo form_error('mother_toungue'); ?>
                        </span>
                    </div>


                    <?php
                        if (form_error('add_admission_fee_invoice'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="add_admission_fee_invoice" class="  control-label"> Add Admission Fee to Invoice
                        </label>                        
                            <input type="checkbox" class="form-control" id="add_admission_fee_invoice" name="add_admission_fee_invoice" value="1">
                           
                        <span class="  control-label">
                            <?php echo form_error('add_admission_fee_invoice'); ?>
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
                        if (form_error('first_name'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="first_name" class=" control-label ">
                           First Name <span class="text-red">*</span>
                        </label>
                        
                            <input type="text" class="form-control id_card" id="first_name" name="first_name" value="<?= set_value('first_name') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('first_name'); ?>
                        </span>
                </div><!------ end First Name ----->
 
                <?php
                        if (form_error('last_name'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="last_name" class=" control-label">
                           Last Name <span class="text-red">*</span>
                        </label>
                        
                            <input type="text" class="form-control id_card" id="last_name" name="last_name" value="<?= set_value('last_name') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('last_name'); ?>
                        </span>
                </div><!------ end last Name ----->


                <?php
                        if (form_error('name'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="name_id" class=" control-label">
                            ID Card Name <span class="text-red">*</span>
                        </label>
                        
                            <input type="text" class="form-control" id="name_id" name="name" value="<?= set_value('name') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('name'); ?>
                        </span>
                </div><!------ end idcard Name ----->

                <?php
                    if (form_error('sex'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="sex" class="  control-label">
                        <?= $this->lang->line("student_sex") ?>
                    </label>
                    
                        <?php
                        echo form_dropdown("sex", array($this->lang->line('student_sex_male') => $this->lang->line('student_sex_male'), $this->lang->line('student_sex_female') => $this->lang->line('student_sex_female')), set_value("sex"), "id='sex' class='form-control'");
                        ?>
                    
                    <span class="  control-label">
                        <?php echo form_error('sex'); ?>
                    </span>
                    </div><!------ end Gender ----->
                    <?php
                        if (form_error('bloodgroup'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="bloodgroup" class="  control-label">
                            <?= $this->lang->line("student_bloodgroup") ?>
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
                            echo form_dropdown("bloodgroup", $bloodArray, set_value("bloodgroup"), "id='bloodgroup' class='form-control select2'");
                            ?>
                        
                        <span class="  control-label">
                            <?php echo form_error('bloodgroup'); ?>
                        </span>
                    </div><!------ end Blood Group  ----->
                    <?php
                        if (form_error('dob'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="dob" class="  control-label">
                            <?= $this->lang->line("student_dob") ?>
                        </label>
                        
                            <input type="text" class="form-control" id="dob" name="dob" value="<?= set_value('dob') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('dob'); ?>
                        </span>
                    </div><!------ end DOB  ----->
                    <?php
                        if (form_error('religion'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="religion" class="  control-label">
                            <?= $this->lang->line("student_religion") ?>
                        </label>
                        
                            <input type="text" class="form-control" id="religion" name="religion" value="<?= set_value('religion') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('religion'); ?>
                        </span>
                    </div><!------ end Religion  ----->
                    <?php
                        if (form_error('cast'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="cast" class="  control-label">
                            Caste
                        </label>                        
                            <!-- <input type="text" class="form-control" id="cast" name="cast" value="<?= set_value('cast') ?>"> -->
                            <select class="form-control select2" id="cast" name="cast">
                                <option value="">Select Caste</option>
                                <option value="OC">OC</option>
                                <option value="BC - A">BC - A</option>
                                <option value="BC - B">BC - B</option>
                                <option value="BC - C">BC - C</option>
                                <option value="BC - D">BC - D</option>
                                <option value="SC">SC</option>
                                <option value="ST">ST</option>
                                <option value="Minority">Minority</option>
                            </select>
                        <span class="  control-label">
                            <?php echo form_error('cast'); ?>
                        </span>
                    </div><!------ end Caste  ----->

                    <?php
                        if (form_error('sub_caste'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="sub_caste" class=" control-label">
                            Sub Caste<span class="text-red">*</span>
                        </label>
                        
                            <input type="text" class="form-control" id="sub_caste" name="sub_caste" value="<?= set_value('sub_caste') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('sub_caste'); ?>
                        </span>
                    </div> 


                    <?php
                        if (form_error('father_name'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="name_id" class=" control-label">
                            Father Name <span class="text-red">*</span>
                        </label>
                        
                            <input type="text" class="form-control" id="father_name_id" name="father_name" value="<?= set_value('father_name') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('father_name'); ?>
                        </span>
                    </div> <!------ end Father Name  ----->

                    <?php
                        if (form_error('father_name'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="father_aadhar" class=" control-label">
                            Father Aadhar
                        </label>
                        
                            <input type="text" class="form-control" id="father_aadhar" name="father_aadhar" value="<?= set_value('father_aadhar') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('father_aadhar'); ?>
                        </span>
                    </div> <!------ end Father Name  ----->

                    <?php
                    if (form_error('mother_name'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="name_id" class=" control-label">
                        Mother Name
                    </label>
                    
                        <input type="text" class="form-control" id="mother_name_id" name="mother_name" value="<?= set_value('mother_name') ?>">
                    
                    <span class="  control-label">
                        <?php echo form_error('mother_name'); ?>
                    </span>
                </div><!------ End Mother Name  ----->

                
                <?php
                        if (form_error('mother_aadhar'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="mother_aadhar" class=" control-label">
                            Mother Aadhar <span class="text-red">*</span>
                        </label>
                        
                            <input type="text" class="form-control" id="mother_aadhar" name="mother_aadhar" value="<?= set_value('mother_aadhar') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('mother_aadhar'); ?>
                        </span>
                    </div> <!------ end Father Name  ----->

                <?php
                    if (form_error('phone'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="phone" class="  control-label">
                        <?= $this->lang->line("student_phone") ?> <span class="text-red">*</span>
                    </label>
                    
                        <input type="text" class="form-control" id="phone" name="phone" maxlength="10" value="<?= set_value('phone') ?>">
                    
                        <span id="error-message" class="control-label" style="color:red;">
                                    <?php echo form_error('phone'); ?>
                                </span>
                    </div><!------ End Phone Number  ----->

                    <?php
                    if (form_error('alternative_phone1'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="alternative_phone1" class="  control-label">
                        Whatsapp
                    </label>
                    
                        <input type="text" class="form-control" id="alternative_phone1" name="alternative_phone1" value="<?= set_value('alternative_phone1') ?>">
                    
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
                    
                        <input type="text" class="form-control" id="alternative_phone2" name="alternative_phone2" value="<?= set_value('alternative_phone2') ?>">
                    
                    <span class="  control-label">
                        <?php echo form_error('alternative_phone2'); ?>
                    </span>
                </div><!------ End Alternative Phone Number2  ----->

                <?php
                    if (form_error('email'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="email" class="  control-label">
                        <?= $this->lang->line("student_email") ?>
                    </label>
                    
                        <input type="text" class="form-control" id="email" name="email" value="<?= set_value('email') ?>">
                    
                    <span class="  control-label">
                        <?php echo form_error('email'); ?>
                    </span>
                </div><!------ End Email  ----->

                <?php
                    if (form_error('photo'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="photo_input" class="control-label">
                        <?= $this->lang->line("student_photo") ?>
                    </label>
                    <div class="photo-upload-wrap">
                        <label class="btn btn-primary btn-sm" style="cursor:pointer; margin-bottom:0;">
                            <i class="fa fa-upload"></i> Choose Photo
                            <input type="file" id="photo_input" name="photo" accept="image/png,image/jpeg,image/gif" style="display:none;">
                        </label>
                        <span id="photo_filename" style="margin-left:8px; color:#666; font-size:12px;"></span>
                        <button type="button" id="photo_clear_btn" class="btn btn-xs btn-default" style="display:none; margin-left:6px;">
                            <i class="fa fa-times"></i>
                        </button>
                        <br>
                        <img id="photo_preview_img" class="photo-preview-img" src="" alt="Preview">
                        <span id="photo_error" class="val-error" style="display:none;"></span>
                        <div style="font-size:11px; color:#999; margin-top:4px;">JPG, PNG, GIF &mdash; max 1 MB</div>
                    </div>
                    <span><?php echo form_error('photo'); ?></span>
                </div><!-------- End student Photo------>
                <?php
                    if (form_error('mole1'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="mole1" class="  control-label">
                        <?= $this->lang->line("mole1") ?>
                    </label>
                
                        <textarea type="text" class="form-control" id="mole1" name="mole1"> <?= set_value('mole1') ?>   </textarea>
                
                    <span class="  control-label">
                        <?php echo form_error('mole1'); ?>
                    </span>
                </div><!-------- End Mole 1------>

                <?php
                    if (form_error('mole2'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="mole2" class="  control-label">
                        <?= $this->lang->line("mole2") ?>
                    </label>
                    
                        <textarea type="text" class="form-control" id="mole2" name="mole2"> <?= set_value('mole2') ?>   </textarea>
                    
                    <span class="  control-label">
                        <?php echo form_error('mole2'); ?>
                    </span>
                </div><!-------- End Mole 2------>

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
                if (form_error('address'))
                    echo "<div class='col-md-4 has-error' >";
                else
                    echo "<div class='col-md-4' >";
                ?>
                <label for="address" class="  control-label">
                    <?= $this->lang->line("student_address") ?>
                </label>
                
                    <input type="text" class="form-control" id="address" name="address" value="<?= set_value('address') ?>">
                
                <span class="  control-label">
                    <?php echo form_error('address'); ?>
                </span>
            </div><!-------- End address ------>

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
                        <?php foreach($villages as $v){?>
                            <option value="<?= $v['villageID']?>"> <?= $v['villageName']?> </option>
                       <?php  }?>
                    </select>
                
                <span class="  control-label">
                    <?php echo form_error('student_village'); ?>
                </span>
                </div><!-------- End village  ------>
                <?php
                    if (form_error('state'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="state" class="  control-label">
                        <?= $this->lang->line("student_state") ?>
                    </label>
                    
                        <input type="text" class="form-control" id="state" name="state" value="<?= set_value('state') ?>">
                    <span class="  control-label">
                        <?php echo form_error('state'); ?>
                    </span>
                    </div><!-------- End state  ------>

                    <?php
                    if (form_error('country'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="country" class="  control-label">
                        <?= $this->lang->line("student_country") ?>
                    </label>
                        <?php
                        // $country['0'] = $this->lang->line('student_select_country');
                        $country['IN'] = "India";
                        foreach ($allcountry as $allcountryKey => $allcountryit) {
                            $country[$allcountryKey] = $allcountryit;
                        }
                        ?>
                        <?php
                        echo form_dropdown("country", $country, set_value("country"), "id='country' class='form-control select2'");
                        ?>
                    <span class="  control-label">
                        <?php echo form_error('country'); ?>
                    </span>
                </div><!-------- End countery  ------>
                <?php
                    if (form_error('aadharCardNumber'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="aadharCardNumber" class="  control-label">
                        <?= $this->lang->line("aadharCardNumber") ?>
                    </label>
                    
                        <input type="text" class="form-control" id="aadharCardNumber" name="aadharCardNumber" value="<?= set_value('aadharCardNumber') ?>">
                    
                    <span class="  control-label">
                        <?php echo form_error('aadharCardNumber'); ?>
                    </span>
                </div><!-------- End Aadhar card  ------>

                <?php
                    if (form_error('ration_card'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="name_id" class=" control-label">
                        Ration Card No  
                    </label>
                    
                        <input type="text" class="form-control" id="ration_card" name="ration_card" value="<?= set_value('ration_card') ?>">
                    
                    <span class="  control-label">
                        <?php echo form_error('ration_card'); ?>
                    </span>
                </div><!-------- End Ration card  ------>

                <?php
                        if (form_error('account_no'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="name_id" class=" control-label">
                            Account No 
                        </label>
                        
                            <input type="text" class="form-control" id="account_no" name="account_no" value="<?= set_value('account_no') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('account_no'); ?>
                        </span>
                    </div><!-------- End Account number  ------>


                    <?php
                        if (form_error('bank_name'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="name_id" class=" control-label">
                            Bank Name  
                        </label>
                        
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?= set_value('bank_name') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('bank_name'); ?>
                        </span>
                     </div><!-------- End Bank name  ------>

                    <?php
                        if (form_error('ifsc_code'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                        ?>
                        <label for="name_id" class=" control-label">
                            IFSC Code  
                        </label>
                        
                            <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="<?= set_value('ifsc_code') ?>">
                        
                        <span class="  control-label">
                            <?php echo form_error('ifsc_code'); ?>
                        </span>
                    </div><!-------- End IFSC number  ------>

                <?php
                    if (form_error('branch_name'))
                        echo "<div class='col-md-4 has-error' >";
                    else
                        echo "<div class='col-md-4' >";
                    ?>
                    <label for="name_id" class=" control-label">
                        Branch Name  
                    </label>
                    
                        <input type="text" class="form-control" id="branch_name" name="branch_name" value="<?= set_value('branch_name') ?>">
                    
                    <span class="  control-label">
                        <?php echo form_error('branch_name'); ?>
                    </span>
                </div><!-------- End Branch name  ------>
              


                <div class="row"> 
 <?php
    if (form_error('studentType'))
        echo "<div class='col-md-4 has-error' >";
    else
        echo "<div class='col-md-4' >";
    ?>
    <label for="studentType" class="  control-label">
        <?= $this->lang->line("studentType") ?></span>
    </label>
       <?php
            $studentType = array(3 => "DAY SCHOLAR",1 => "TRANSPORT", 2 => "HOSTEL" );
        ?> 
        <?php
            echo form_dropdown("studentType", $studentType, set_value("studentType"), "id='studentType' class='form-control select2'");
        ?>
     <span class="  control-label">
        <?php echo form_error('studentType'); ?>
    </span>
</div> 
</div>

            </div>
            </div><!-- /.sap-body -->
        </div><!-- /.sap -->

        <!-- Section 4: Transport Details -->
        <div class="sap" id="transport_div">
            <div class="sap-header collapsed" data-toggle="collapse" data-target="#sap-transport">
                <span class="sap-icon"><i class="fa fa-bus"></i></span>
                <span class="sap-title">Transport Details</span>
                <i class="fa fa-chevron-up sap-chevron"></i>
            </div>
            <div id="sap-transport" class="collapse sap-body sap-bg-4">
            <div class="row">
            <?php 
                if(form_error('transportID')) 
                    echo "<div class='col-md-4 has-error' >";
                else     
                    echo "<div class='col-md-4' >";
            ?>
                <label for="transportID" class="control-label  <?= $this->input->post('studentType')=='1' ? '' : 'show'; ?> transport" >
                    <?=$this->lang->line("tmember_route_name")?> <span class="text-red">*</span>
                </label>
                <div class="col1-sm-4 <?= $this->input->post('studentType')=='1' ? '' : 'show'; ?> transport" >                    
                    <?php
                        $array = array();
                        $array[0] = $this->lang->line("classes_select_route_name");
                        foreach ($transports as $transport) {
                            $array[$transport->transportID] = $transport->route;
                        }
                        echo form_dropdown("transportID", $array, set_value("transportID"), "id='transportID' class='form-control clear-dropdown select2'");
                    ?>
                </div>
                <span class="  control-label <?= $this->input->post('studentType')=='1' ? '' : 'show'; ?> transport">
                    <?php echo form_error('transportID'); ?>
                </span>
            </div><!-------- End transport id  ------>

             <?php 
                   
                    echo "<div class='col-md-4' >";
            ?>
                <label for="transportID" class="control-label  <?= $this->input->post('studentType')=='1' ? '' : 'show'; ?> transport" >
                    Pickup Point <span class="text-red">*</span>
                </label>
                <div class="col1-sm-4 " >                    
                    <select id="pickup_id" name ="pickup_id" class='form-control clear-dropdown select2'>
                        <option>Select Pickup point</option>
                         
                    </select>
                </div>
                 
            </div> 

            <?php 
                    if(form_error('tbalance')) 
                        echo "<div class='col-md-4 has-error'>";
                    else     
                        echo "<div class='col-md-4'>";
                ?>
                    <label for="tbalance" class="  control-label <?= $this->input->post('studentType')=='1' ? '' : 'show'; ?> transport">
                        <?=$this->lang->line("tmember_tfee")?> <span class="text-red">*</span>
                    </label>
                    <div class="col1-sm-4 <?= $this->input->post('studentType')=='1' ? '' : 'show'; ?> transport">
                        <input type="text" class="form-control clear-dropdown" id="tbalance" name="tbalance" value="<?=set_value('tbalance', "0.00")?>" readonly>
                    </div>
                    <span class="  control-label <?= $this->input->post('studentType')=='1' ? '' : 'show'; ?> transport">
                        <?php echo form_error('tbalance'); ?>
                    </span>
                </div> <!-------- End transport fee  ------>


               
           


            </div>
            </div><!-- /.sap-body -->
        </div><!-- /.sap -->

        <!-- Section 5: Hostel Details -->
        <div class="sap" id="hostel_div">
            <div class="sap-header collapsed" data-toggle="collapse" data-target="#sap-hostel">
                <span class="sap-icon"><i class="fa fa-home"></i></span>
                <span class="sap-title">Hostel Details</span>
                <i class="fa fa-chevron-up sap-chevron"></i>
            </div>
            <div id="sap-hostel" class="collapse sap-body sap-bg-5">
            <div class="row">
            <?php 
                if(form_error('hostelID')) 
                    echo "<div class='col-md-4 has-error'>";
                else     
                    echo "<div class='col-md-4'>";
            ?>
                <label for="hostelID" class="  control-label  <?= $this->input->post('studentType')=='2' ? '' : 'show'; ?> hostel">
                    <?=$this->lang->line("hmember_hname")?> <span class="text-red">*</span>
                </label>
                <div class="coll-sm-6  <?= $this->input->post('studentType')=='2' ? '' : 'show'; ?> hostel">
                    <?php
                        $array[0] = $this->lang->line("hmember_select_hostel_name");
                        foreach ($hostels as $hostel) {
                            $array[$hostel->hostelID] = $hostel->name;
                        }
                        echo form_dropdown("hostelID", $array, set_value("hostelID"), "id='hostelID' class='form-control clear-dropdown select2'");
                    ?>
                </div>
                <span class="  control-label <?= $this->input->post('studentType')=='2' ? '' : 'show'; ?> hostel">
                    <?php echo form_error('hostelID'); ?>
                </span>
        </div> <!----------End Hostel id ----------->

            <?php 
                if(form_error('categoryID')) 
                    echo "<div class='col-md-4 has-error' >";
                else     
                    echo "<div class='col-md-4' >";
            ?>
                <label for="categoryID" class="control-label hostel  <?= $this->input->post('studentType')=='2' ? '' : 'show'; ?>">
                    <?=$this->lang->line("hmember_class_type")?> <span class="text-red">*</span>
                </label>
                <div class="coll-sm-6  <?= $this->input->post('studentType')=='2'  ? '' : 'show'; ?> hostel">
                    <?php
                        $array = array(0 => $this->lang->line("hmember_select_class_type"));
                        if(customCompute($categorys)) {
                            foreach ($categorys as $key => $category) {
                                $array[$category->categoryID] = $category->class_type;
                            }
                        }
                        echo form_dropdown("categoryID", $array, set_value("categoryID"), "id='categoryID' class='form-control clear-dropdown select2'");
                    ?>
                </div>
                <span class="control-label <?= $this->input->post('studentType')=='2' ? '' : 'show'; ?> hostel" >
                    <?php echo form_error('categoryID'); ?>
                </span>
            </div>
        </div><!-- row close -->
        </div><!-- /.sap-body -->
        </div><!-- /.sap panel #hostel_div -->

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
            <div class="col-md-3 <?= form_error('refered_by') ? 'has-error' : '' ?>">
                <label for="refered_by" class="control-label">
                    Refered By
                    <a title="Add Teacher" target="_blank" href="<?= base_url('teacher/add') ?>"><i class="fa fa-plus"></i></a>
                </label>
                <select id="refered_by" name="refered_by" class="form-control select2">
                    <option value="">--Select--</option>
                    <option value="others" <?= set_select('refered_by', 'others') ?>>Others</option>
                    <?php foreach($teachers as $k => $v): ?>
                        <option value="<?= $k ?>" <?= set_select('refered_by', $k) ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
                <div id="refered_by_other_div" style="display:none; margin-top:6px;">
                    <input type="text" class="form-control" id="refered_by_other" name="refered_by_other" placeholder="Enter referral name" value="<?= set_value('refered_by_other') ?>">
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

</div><!-- /.student-accordion -->
<div class="student-btn-info form-group">
    <div class="add-student-btn">
        <input type="submit" class="ose-btn" value="<?= $this->lang->line("add_student") ?>">
    </div>
</div>
</form>

<?php if ($siteinfos->note == 1) { ?>
    <div class="callout callout-danger">
        <p><b>Note:</b> Create teacher, class, section before create a new student.</p>
    </div>
<?php } ?>

</div><!-- /.box-body -->
</div><!-- /.box -->

<script type="text/javascript">
$(".select2").select2();

// ── Datepickers (no future dates) ──
$('#dob').datepicker({ startView: 2, format: 'dd-mm-yyyy', endDate: '0d', autoclose: true });
$('#admission_date').datepicker({ format: 'dd-mm-yyyy', endDate: '0d', autoclose: true });

// ── Mark required field groups with left-border indicator ──
$(function() {
    $('.student-form-info .col-md-4, .student-form-info .col-md-3').each(function() {
        if ($(this).find('label .text-red, label .text-danger').length > 0)
            $(this).addClass('has-required');
    });
});

// ── Auto-capitalize first letter in name fields ──
$(document).on('input', '#first_name, #last_name, #father_name_id, #mother_name_id', function() {
    var pos = this.selectionStart, v = $(this).val();
    if (v.length > 0) {
        $(this).val(v.charAt(0).toUpperCase() + v.slice(1));
        try { this.setSelectionRange(pos, pos); } catch(e) {}
    }
});

// ── Name fields: letters, spaces, hyphens, periods only ──
$(document).on('keypress', '#first_name, #last_name, #father_name_id, #mother_name_id, #religion, #state', function(e) {
    if (!/[a-zA-Z\s.\-']/.test(String.fromCharCode(e.which || e.keyCode))) e.preventDefault();
});

// ── Trim spaces on blur ──
$(document).on('blur', '.student-form-info input[type="text"], .student-form-info textarea', function() {
    $(this).val($.trim($(this).val()));
});

// ── Phone: digits only, max 10 digits ──
$(document).on('keypress', '#phone, #alternative_phone1, #alternative_phone2', function(e) {
    var c = e.which || e.keyCode;
    if (c < 48 || c > 57) e.preventDefault();
});
$(document).on('input', '#phone, #alternative_phone1, #alternative_phone2', function() {
    $(this).val($(this).val().replace(/\D/g, '').slice(0, 10));
});

// ── Photo upload: inline preview ──
$('#photo_input').on('change', function() {
    var file = this.files[0];
    $('#photo_error').hide();
    if (!file) return;
    if (!file.type.match(/^image\/(jpeg|png|gif)$/)) {
        $('#photo_error').text('Only JPG, PNG or GIF files allowed.').show();
        $(this).val(''); return;
    }
    if (file.size > 1048576) {
        $('#photo_error').text('File size must not exceed 1 MB.').show();
        $(this).val(''); return;
    }
    $('#photo_filename').text(file.name);
    $('#photo_clear_btn').show();
    var reader = new FileReader();
    reader.onload = function(e) {
        $('#photo_preview_img').attr('src', e.target.result).show();
    };
    reader.readAsDataURL(file);
});
$('#photo_clear_btn').on('click', function() {
    $('#photo_input').val('');
    $('#photo_filename').text('');
    $('#photo_preview_img').hide().attr('src', '');
    $(this).hide();
    $('#photo_error').hide();
});

// ── ID card name auto-fill ──
$(document).on("keyup", ".id_card", function() {
    var f = $("#first_name").val(), l = $("#last_name").val();
    $("#name_id").val(f.charAt(0).toUpperCase() + " " + l.charAt(0).toUpperCase() + l.slice(1));
});

$('#username').keyup(function() { $(this).val($(this).val().replace(/\s/g, '')); });

// ── Class change: load sections + optional subjects ──
$('#classesID').change(function() {
    var id = $(this).val();
    if (id === '0') { $('#sectionID').val(0); return; }
    $.ajax({ async: false, type: 'POST', url: "<?= base_url('student/sectioncall') ?>", data: "id=" + id, dataType: "html",
        success: function(d) { $('#sectionID').html(d); } });
    $.ajax({ async: false, type: 'POST', url: "<?= base_url('student/optionalsubjectcall') ?>", data: "id=" + id, dataType: "html",
        success: function(d) { $('#optionalSubjectID').html(d); } });
});

// ── Section change: auto roll ──
$('#sectionID').change(function() {
    var sID = $(this).val(), cID = $('select[name="classesID"] option:selected').val();
    $.ajax({ async: false, type: 'POST', url: "<?= base_url('student/get_auto_roll_no') ?>",
        data: {sectionID: sID, classesID: cID}, dataType: "json",
        success: function(d) { $('#roll').val(d); } });
});

// ── Roll duplicate check ──
$(document).on("focusout", "#roll", function() {
    $.ajax({ type: 'POST', url: "<?= base_url('student/checkRoll') ?>",
        data: {classesID: $("#classesID").val(), sectionID: $("#sectionID").val(), rollNo: $(this).val()},
        dataType: "html", success: function(d) { $('.err').html(d); } });
});

// ── Student type show/hide ──
$("#transport_div").hide(); $("#hostel_div").hide();
$('#studentType').change(function() {
    $(".clear-dropdown").val('');
    $("#categoryID,#pickup_id,#hostelID,#transportID").val('0').change();
    var t = $(this).val();
    $("#transport_div").toggle(t == 1);
    $("#hostel_div").toggle(t == 2);
});

// ── Transport / Pickup / Hostel ──
(function() {
    var tid = $('#transportID').val();
    if (!tid || tid == 0) { $('#tbalance').val("0.00"); }
    else { $.ajax({ type:'POST', url:"<?= base_url('tmember/transport_fare') ?>", data:"id="+tid, dataType:"html", success:function(d){$('#tbalance').val(d);} }); }
})();
$('#transportID').change(function() {
    var id = $(this).val();
    if (!id || id == 0) { $('#tbalance').val("0.00"); return; }
    $.ajax({ type:'POST', url:"<?= base_url('Student/get_pickup_points') ?>", data:"id="+id, dataType:"html", success:function(d){$('#pickup_id').html(d);} });
});
$('#pickup_id').change(function() {
    var id = $(this).val();
    if (!id || id == 0) { $('#tbalance').val("0.00"); return; }
    $.ajax({ type:'POST', url:"<?= base_url('Student/transport_fare') ?>", data:"id="+id, dataType:"html", success:function(d){$('#tbalance').val(d);} });
});
$('#hostelID').change(function() {
    $('#categoryID').val(0).select2();
    var id = $(this).val();
    if (!id || id == 0) return;
    $.ajax({ type:'POST', url:"<?= base_url('hmember/categorycall') ?>", data:"id="+id, dataType:"html", success:function(d){$('#categoryID').html(d);} });
});

// ── Refered By Others toggle ──
$('#refered_by').on('change', function() {
    if ($(this).val() === 'others') $('#refered_by_other_div').show();
    else $('#refered_by_other_div').hide().find('input').val('');
});

// ── Siblings ──
var siblingRowCount = 0;
function initSiblingRow(row) { row.find('.sibling-class-select').select2({width:'100%'}); }
$('#add_sibling_btn').on('click', function() {
    var t = $('#sibling_row_template').clone().attr('id','sibling_row_'+siblingRowCount).removeAttr('style');
    $('#sibling_tbody').append(t); initSiblingRow(t); siblingRowCount++;
});
$(document).on('click', '.remove-sibling-btn', function() { $(this).closest('tr').remove(); });
$(document).on('change', '.sibling-class-select', function() {
    var cid = $(this).val(), row = $(this).closest('tr');
    var ss = row.find('.sibling-section-select'), stu = row.find('.sibling-student-select');
    if ($.fn.select2 && ss.hasClass('select2-hidden-accessible')) ss.select2('destroy');
    if ($.fn.select2 && stu.hasClass('select2-hidden-accessible')) stu.select2('destroy');
    ss.html('<option value="">--Select Section--</option>');
    stu.html('<option value="">--Select Student--</option>');
    if (cid > 0) {
        $.ajax({ type:'POST', url:"<?= base_url('student/sectioncall') ?>", data:"id="+cid, dataType:"html", success:function(d){
            ss.html(d);
            ss.off('change.sibLoad').on('change.sibLoad', function() {
                var sid = $(this).val();
                if ($.fn.select2 && stu.hasClass('select2-hidden-accessible')) stu.select2('destroy');
                stu.html('<option value="">--Select Student--</option>');
                if (cid > 0 && sid > 0) {
                    $.ajax({ type:'GET', url:"<?= base_url('student/get_students_by_class_section') ?>",
                        data:{classesID:cid,sectionID:sid}, dataType:"json", success:function(studs){
                            $.each(studs,function(i,s){ stu.append('<option value="'+s.studentID+'">'+s.name+(s.roll?' ('+s.roll+')':'')+'</option>'); });
                            stu.select2({width:'100%',placeholder:'--Select Student--'});
                        }});
                }
            });
            ss.select2({width:'100%'});
        }});
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

    // Required text fields
    [['#registerNO','Admission No is required'],
     ['#first_name','First Name is required'],
     ['#last_name','Last Name is required'],
     ['#name_id','ID Card Name is required'],
     ['#father_name_id','Father Name is required'],
     ['#phone','Phone number is required'],
     ['#roll','Roll No is required']
    ].forEach(function(r) {
        var $el = $f.find(r[0]);
        if ($el.length && !$.trim($el.val())) { svErr($el, r[1]); ok = false; }
    });

    // Required selects
    [['#classesID','Please select a Class'],['#sectionID','Please select a Section']].forEach(function(r) {
        var $el = $f.find(r[0]), v = $el.val();
        if ($el.length && (!v || v == '0')) { svErr($el, r[1]); ok = false; }
    });

    // Name fields: letters only
    ['#first_name','#last_name','#father_name_id','#mother_name_id'].forEach(function(id) {
        var $el = $f.find(id), v = $.trim($el.val());
        if ($el.length && v && !/^[a-zA-Z\s.\-']+$/.test(v)) {
            svErr($el, 'Only letters, spaces and hyphens are allowed'); ok = false;
        }
    });

    // Phone
    var ph = $.trim($f.find('#phone').val());
    if (ph && !/^\d{10}$/.test(ph)) { svErr($f.find('#phone'), 'Phone must be exactly 10 digits'); ok = false; }

    // Email
    var em = $.trim($f.find('#email').val());
    if (em && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { svErr($f.find('#email'), 'Enter a valid email address'); ok = false; }

    // DOB: no future date
    var dob = $.trim($f.find('#dob').val());
    if (dob) {
        var p = dob.split('-');
        if (p.length === 3 && new Date(+p[2], +p[1]-1, +p[0]) > new Date()) {
            svErr($f.find('#dob'), 'Date of birth cannot be a future date'); ok = false;
        }
    }

    if (!ok) {
        e.preventDefault();
        var $first = $f.find('.has-error').first();
        if ($first.length) $('html,body').animate({scrollTop: $first.offset().top - 120}, 400);
    }
});
</script>