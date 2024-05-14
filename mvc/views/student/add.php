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
        <div class="row">
            <!-- <div class="col-sm-10"> -->
        <div class="student-firm">
            <h2 class="h2-title">Add Student Form</h2>
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
                        <label for="sectionID" class="  control-label">
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
                            if (form_error('roll')) 
                                echo "<div class='col-md-4 has-error' >";
                            else
                                echo "<div class='col-md-4' >";
                            ?>
                            <label for="roll" class="  control-label">
                                <?= $this->lang->line("student_roll") ?> <span class="text-red">*</span>
                            </label>
                                <input type="text" class="form-control" id="roll" name="roll" value="<?= set_value('roll') ?>">
                            <span class="  control-label">
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
                                Joined Class
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
                                echo form_dropdown("studentGroupID", $groupArray, set_value("studentGroupID"), "id='studentGroupID' class='form-control select2'");
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
                                <option value="1">Telug</option>
                                <option value="2">English</option>
                                <option value="3">Hindi</option>
                                <option value="4">Kannada</option>
                                <option value="5">Malayalam</option> 
                            </select>
                        <span class="  control-label">
                            <?php echo form_error('mother_toungue'); ?>
                        </span>
                    </div>

              

            </div> <!------ end row----->
        </div> <!-------- End Add student firm------>
        <div class="student-details-sec">
            <h2 class="h2-title">Student Details</h2>
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
                            <?php echo form_error('father_name'); ?>
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
                            Father Aadhar <span class="text-red">*</span>
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
                    
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= set_value('phone') ?>">
                    
                    <span class="  control-label">
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
                        Alternative Phone1
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
                    <label for="photo" class="  control-label">
                        <?= $this->lang->line("student_photo") ?>
                    </label>
                        <div class="input-group image-preview">
                            <input type="text" class="form-control image-preview-filename" disabled="disabled">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                    <span class="fa fa-remove"></span>
                                    <?= $this->lang->line('student_clear') ?>
                                </button>
                                <div class="btn btn-primary image-preview-input">
                                    <span class="fa fa-repeat"></span>
                                    <span class="image-preview-input-title">
                                        <?= $this->lang->line('student_file_browse') ?></span>
                                    <input type="file" accept="image/png, image/jpeg, image/gif" name="photo" />
                                </div>
                            </span>
                        </div>
                    
                    <span class=" ">
                        <?php echo form_error('photo'); ?>
                    </span>
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
        </div><!-------- End student details sec------>

        <div class="student-address-sec">
            <h2 class="h2-title">Student Address Details</h2>
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
        </div> <!-------- End student address details sec------>
        <div class="transport-details-sec" id="transport_div">
            <h2 class="h2-title">Transport Details</h2>
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
                        echo form_dropdown("transportID", $array, set_value("transportID"), "id='transportID' class='form-control select2'");
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
                    <select id="pickup_id" name ="pickup_id" class='form-control select2'>
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
                        <input type="text" class="form-control" id="tbalance" name="tbalance" value="<?=set_value('tbalance', "0.00")?>" readonly>
                    </div>
                    <span class="  control-label <?= $this->input->post('studentType')=='1' ? '' : 'show'; ?> transport">
                        <?php echo form_error('tbalance'); ?>
                    </span>
                </div> <!-------- End transport fee  ------>


               
           


            </div>
       </div>

       <div class="hostel-details-sec" id="hostel_div">
            <h2 class="h2-title">Hostel Details</h2>
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
                        echo form_dropdown("hostelID", $array, set_value("hostelID"), "id='hostelID' class='form-control select2'");
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
                        echo form_dropdown("categoryID", $array, set_value("categoryID"), "id='categoryID' class='form-control select2'");
                    ?>
                </div>
                <span class="control-label <?= $this->input->post('studentType')=='2' ? '' : 'show'; ?> hostel" >
                    <?php echo form_error('categoryID'); ?>
                </span>
            </div><!----------End Hostel type ----------->
        </div> 
    </div> <!----------End Hostel details ----------->

</div>






<!--<div class="row">
 <div class="col-md-4 <?= form_error('extraCurricularActivities') ? ' has-error' : ''  ?>">
    <label for="extraCurricularActivities" class="  control-label">
        <?= $this->lang->line("student_extracurricularactivities") ?>
    </label>
         <input type="text" class="form-control" id="extraCurricularActivities" name="extraCurricularActivities" value="<?= set_value('extraCurricularActivities') ?>">
     <span class="  control-label">
        <?php echo form_error('extraCurricularActivities'); ?>
    </span>
</div> 
</div>-->


</div>

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
<!--</div>  col-sm-8 -->

</div><!-- row -->
</div><!-- Body -->
<!--</div> /.box -->

<script type="text/javascript">
    $(".select2").select2();
    $('#dob').datepicker({
        startView: 2
    });
    $('#admission_date').datepicker({
        startView: 2
    });

    $('#username').keyup(function() {
        $(this).val($(this).val().replace(/\s/g, ''));
    });


    $('#classesID').change(function(event) {
        var classesID = $(this).val();
        if (classesID === '0') {
            $('#sectionID').val(0);
        } else {
            $.ajax({
                async: false,
                type: 'POST',
                url: "<?= base_url('student/sectioncall') ?>",
                data: "id=" + classesID,
                dataType: "html",
                success: function(data) {
                    $('#sectionID').html(data);
                }
            });

            $.ajax({
                async: false,
                type: 'POST',
                url: "<?= base_url('student/optionalsubjectcall') ?>",
                data: "id=" + classesID,
                dataType: "html",
                success: function(data2) {
                    $('#optionalSubjectID').html(data2);
                }
            });
        }
    });

    $('#sectionID').change(function(event) {
        var sectionID = $(this).val();
        var classesID = $('select[name="classesID"] option:selected').val();

            $.ajax({
                async: false,
                type: 'POST',
                url: "<?= base_url('student/get_auto_roll_no') ?>",
                data: {"sectionID":sectionID,"classesID":classesID},
                dataType: "json",
                success: function(data) {
                    $('#roll').val(data);
                }
            }); 
    });

    $(document).on('click', '#close-preview', function() {
        $('.image-preview').popover('hide');
        // Hover befor close the preview
        $('.image-preview').hover(
            function() {
                $('.image-preview').popover('show');
                $('.content').css('padding-bottom', '100px');
            },
            function() {
                $('.image-preview').popover('hide');
                $('.content').css('padding-bottom', '20px');
            }
        );
    });

    $(function() {
        // Create the close button
        var closebtn = $('<button/>', {
            type: "button",
            text: 'x',
            id: 'close-preview',
            style: 'font-size: initial;',
        });
        closebtn.attr("class", "close pull-right");
        // Set the popover default content
        $('.image-preview').popover({
            trigger: 'manual',
            html: true,
            title: "<strong>Preview</strong>" + $(closebtn)[0].outerHTML,
            content: "There's no image",
            placement: 'bottom'
        });
        // Clear event
        $('.image-preview-clear').click(function() {
            $('.image-preview').attr("data-content", "").popover('hide');
            $('.image-preview-filename').val("");
            $('.image-preview-clear').hide();
            $('.image-preview-input input:file').val("");
            $(".image-preview-input-title").text("<?= $this->lang->line('student_file_browse') ?>");
        });
        // Create the preview image
        $(".image-preview-input input:file").change(function() {
            var img = $('<img/>', {
                id: 'dynamic',
                width: 250,
                height: 200,
                overflow: 'hidden'
            });
            var file = this.files[0];
            var reader = new FileReader();
            // Set preview image into the popover data-content
            reader.onload = function(e) {
                $(".image-preview-input-title").text("<?= $this->lang->line('student_file_browse') ?>");
                $(".image-preview-clear").show();
                $(".image-preview-filename").val(file.name);
                img.attr('src', e.target.result);
                $(".image-preview").attr("data-content", $(img)[0].outerHTML).popover("show");
                $('.content').css('padding-bottom', '100px');
            }
            reader.readAsDataURL(file);
        });
    });


    $("#transport_div").hide();
    $("#hostel_div").hide();

    $('#studentType').change(function() {
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
$(document).on("keyup",".id_card",function(){
    var f_name= $("#first_name").val();
   
    let letter = f_name.charAt(0).toUpperCase(); 

    var string= $("#last_name").val();
    var l_name = string.charAt(0).toUpperCase() + string.slice(1);
    var idcard = letter + " " + l_name;
    // alert (idcart);
    $("#name_id").val(idcard);
});

</script>