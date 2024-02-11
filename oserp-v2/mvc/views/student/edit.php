
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
        <div class="row">
            <div class="col-sm-10">

                <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
                   
                    <?php 
                        if(form_error('registerNO')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="registerNO" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_registerNO")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="registerNO" name="registerNO" value="<?=set_value('registerNO', $student->srregisterNO)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('registerNO'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('joined_class')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="classesID" class="col-sm-2 control-label">
                            Joining Class <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $classArray = array(0 => $this->lang->line("student_select_class"));
                                foreach ($classes as $classa) {
                                    $classArray[$classa->classesID] = $classa->classes;
                                }
                                echo form_dropdown("joined_class", $classArray, set_value("joined_class", $student->srclassesID), "id='joined_class' class='form-control select2'");
                            ?>
                        </div>
                        <!--<span class="col-sm-4 control-label">-->
                            <?php //echo form_error('classesID'); ?>
                        <!--</span>-->
                    </div>

                    <?php 
                        if(form_error('name')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="name_id" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_name")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="name_id" name="name" value="<?=set_value('name', $student->name)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('name'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('fathername')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";

                            $this->db->where('parentsID',$student->parentID);
                            $father_name = $this->db->get('parents')->row()->father_name;
                    ?>
                        <label for="name_id" class="col-sm-2 control-label">
                           Father Name <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="father_name" name="father_name" value="<?= $father_name?$father_name:''?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('name'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('guargianID')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="guargianID" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_guargian")?>
                        </label>
                        <div class="col-sm-6">
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
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('guargianID'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('admission_date')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="admission_date" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_admission_date")?>
                        </label>
                        <div class="col-sm-6">
                            <?php $admission_date = ''; if($student->admission_date) { $admission_date = date("d-m-Y", strtotime($student->admission_date)); }  ?>
                            <input type="text" class="form-control" id="admission_date" name="admission_date" value="<?=set_value('admission_date', $admission_date)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('admission_date'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('dob')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="dob" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_dob")?>
                        </label>
                        <div class="col-sm-6">
                            <?php $dob = ''; if($student->dob) { $dob = date("d-m-Y", strtotime($student->dob)); }  ?>
                            <input type="text" class="form-control" id="dob" name="dob" value="<?=set_value('dob', $dob)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('dob'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('sex')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="sex" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_sex")?>
                        </label>
                        <div class="col-sm-6">
                            <?php 
                                echo form_dropdown("sex", array($this->lang->line('student_sex_male') => $this->lang->line('student_sex_male'), $this->lang->line('student_sex_female') => $this->lang->line('student_sex_female')), set_value("sex", $student->sex), "id='sex' class='form-control'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('sex'); ?>
                        </span>

                    </div>

                    <?php 
                        if(form_error('bloodgroup')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="bloodgroup" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_bloodgroup")?>
                        </label>
                        <div class="col-sm-6">
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
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('bloodgroup'); ?>
                        </span>
                    </div>

                   
                    <?php 
                        if(form_error('religion')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="religion" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_religion")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="religion" name="religion" value="<?=set_value('religion', $student->religion)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('religion'); ?>
                        </span>
                    </div>
                    <?php 
                        if(form_error('aadharCardNumber')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="aadharCardNumber" class="col-sm-2 control-label">
                            <?=$this->lang->line("aadharCardNumber")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="aadharCardNumber" name="aadharCardNumber" value="<?=set_value('aadharCardNumber', $student->aadharCardNumber)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('aadharCardNumber'); ?>
                        </span>
                    </div>
                    
                    <?php 
                        if(form_error('rf_id')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="aadharCardNumber" class="col-sm-2 control-label">
                           RFID
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="rf_id" name="rf_id" value="<?=set_value('rf_id', $student->rf_id)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('rf_id'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('mole1')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="mole1" class="col-sm-2 control-label">
                            <?=$this->lang->line("mole1")?>
                        </label>
                        <div class="col-sm-6">
                            <textarea type="text" class="form-control" id="mole1" name="mole1"> <?=set_value('mole1', $student->mole1)?>   </textarea>   
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('mole1'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('mole2')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="mole2" class="col-sm-2 control-label">
                            <?=$this->lang->line("mole2")?>
                        </label>
                        <div class="col-sm-6">
                            <textarea type="text" class="form-control" id="mole2" name="mole2"> <?=set_value('mole2', $student->mole2)?>   </textarea>   
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('mole2'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('email')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="email" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_email")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="email" name="email" value="<?=set_value('email', $student->email)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('email'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('phone')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="phone" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_phone")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="phone" name="phone" value="<?=set_value('phone', $student->phone)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('phone'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('address')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="address" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_address")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="address" name="address" value="<?=set_value('address', $student->address)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('address'); ?>
                        </span>
                    </div>
                    

                    <?php 
                        if(form_error('student_village')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="phone" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_village")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="village_name" name="village_name" value="<?=set_value('village_name', $student->village_name)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('student_village'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('state')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="state" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_state")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="state" name="state" value="<?=set_value('state', $student->state)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('state'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('country')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="country" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_country")?>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $country['0'] = $this->lang->line('student_select_country');  
                                foreach ($allcountry as $allcountryKey => $allcountryit) {
                                    $country[$allcountryKey] = $allcountryit;
                                }
                            ?>
                            <?php 
                                echo form_dropdown("country", $country, set_value("country", $student->country), "id='country' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('country'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('classesID')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="classesID" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_classes")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $classArray = array(0 => $this->lang->line("student_select_class"));
                                foreach ($classes as $classa) {
                                    $classArray[$classa->classesID] = $classa->classes;
                                }
                                echo form_dropdown("classesID", $classArray, set_value("classesID", $student->srclassesID), "id='classesID' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('classesID'); ?>
                        </span>
                        <input type="hidden" name="old_class" value="<?= $student->srclassesID?>">
                    </div>

                    <?php 
                        if(form_error('sectionID')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="sectionID" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_section")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $array = array(0 => $this->lang->line("student_select_section"));
                                foreach ($sections as $section) {
                                    $array[$section->sectionID] = $section->section;
                                }
                                echo form_dropdown("sectionID", $array, set_value("sectionID", $student->srsectionID), "id='sectionID' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('sectionID'); ?>
                        </span>

                        <input type="hidden" name="old_section" value="<?= $student->srsectionID?>">
                    </div>

                    <div class="form-group <?=form_error('studentGroupID') ? ' has-error' : ''  ?>">
                        <label for="studentGroupID" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_studentgroup")?>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $groupArray = array(0 => $this->lang->line("student_select_studentgroup"));
                            if(customCompute($studentgroups)) {
                                foreach ($studentgroups as $studentgroup) {
                                    $groupArray[$studentgroup->studentgroupID] = $studentgroup->group;
                                }
                            }
                            echo form_dropdown("studentGroupID", $groupArray, set_value("studentGroupID", $student->srstudentgroupID), "id='studentGroupID' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('studentGroupID'); ?>
                        </span>
                    </div>

                    <div class="form-group <?=form_error('optionalSubjectID') ? ' has-error' : ''  ?>">
                        <label for="optionalSubjectID" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_optionalsubject")?>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $optionalSubjectArray = array(0 => $this->lang->line("student_select_optionalsubject"));
                            if($optionalSubjects != "empty") {
                                foreach ($optionalSubjects as $optionalSubject) {
                                    $optionalSubjectArray[$optionalSubject->subjectID] = $optionalSubject->subject;
                                }
                            }

                            echo form_dropdown("optionalSubjectID", $optionalSubjectArray, set_value("optionalSubjectID", $student->sroptionalsubjectID), "id='optionalSubjectID' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('optionalSubjectID'); ?>
                        </span>
                    </div>

                   
                    <?php 
                        if(form_error('roll')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="roll" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_roll")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="roll" name="roll" value="<?=set_value('roll', $student->srroll)?>" readonly>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('roll'); ?>
                        </span>
                    </div>




                    <?php
                        if(form_error('photo'))
                            echo "<div class='form-group has-error' >";
                        else
                            echo "<div class='form-group' >";
                    ?>
                        <label for="photo" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_photo")?>
                        </label>
                        <div class="col-sm-6">
                            <div class="input-group image-preview">
                                <input type="text" class="form-control image-preview-filename" disabled="disabled">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                        <span class="fa fa-remove"></span>
                                        <?=$this->lang->line('student_clear')?>
                                    </button>
                                    <div class="btn btn-success image-preview-input">
                                        <span class="fa fa-repeat"></span>
                                        <span class="image-preview-input-title">
                                        <?=$this->lang->line('student_file_browse')?></span>
                                        <input type="file" accept="image/png, image/jpeg, image/gif" name="photo"/>
                                    </div>
                                </span>
                            </div>
                        </div>

                        <span class="col-sm-4">
                            <?php echo form_error('photo'); ?>
                        </span>
                    </div>

                    <div class="form-group <?=form_error('extraCurricularActivities') ? ' has-error' : ''  ?>">
                        <label for="extraCurricularActivities" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_extracurricularactivities")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="extraCurricularActivities" name="extraCurricularActivities" value="<?=set_value('extraCurricularActivities', $student->extracurricularactivities)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('extraCurricularActivities'); ?>
                        </span>
                    </div>

                    <div class="form-group <?=form_error('remarks') ? ' has-error' : ''  ?>">
                        <label for="remarks" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_remarks")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="remarks" name="remarks" value="<?=set_value('remarks', $student->remarks)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('remarks'); ?>
                        </span>
                    </div>
                    <?php
    if (form_error('studentType'))
        echo "<div class='form-group has-error' >";
    else
        echo "<div class='form-group' >";
    ?>
    <label for="studentType" class="col-sm-2 control-label">
        <?= $this->lang->line("studentType") ?></span>
    </label>
    <div class="col-sm-6">
        <?php
            $studentType = array('' => 'Select Student Type', 1 => "TRANSPORT", 2 => "HOSTEL" , 3 => "DAY SCHOLAR");
        ?>
        <?php
            echo form_dropdown("studentType", $studentType, set_value("studentType",$student->studentType ), "id='studentType' class='form-control select2'");
        ?>
    </div>
    <span class="col-sm-4 control-label">
        <?php echo form_error('studentType'); ?>
    </span>
</div>

    <?php 
        if(form_error('hostelID')) 
            echo "<div class='form-group has-error' >";
        else     
            echo "<div class='form-group' >";
    ?>
        <label for="hostelID" class="col-sm-2 control-label  <?= $this->input->post('studentType')=='2' || $student->studentType == '2' ? '' : 'hide'; ?> hostel">
            <?=$this->lang->line("hmember_hname")?> <span class="text-red">*</span>
        </label>
        <div class="col-sm-6  <?= $this->input->post('studentType')=='2' || $student->studentType == '2' ? '' : 'hide'; ?> hostel">
            <?php
                $array[0] = $this->lang->line("hmember_select_hostel_name");
                foreach ($hostels as $hostel) {
                    $array[$hostel->hostelID] = $hostel->name;
                }
                echo form_dropdown("hostelID", $array, set_value("hostelID", isset($studntHostelDetails->hostelID) ?$studntHostelDetails->hostelID:'' ), "id='hostelID' class='form-control select2'");
            ?>
        </div>
        <span class="col-sm-4 control-label <?= $this->input->post('studentType')=='2' || $student->studentType == '2' ? '' : 'hide'; ?> hostel">
            <?php echo form_error('hostelID'); ?>
        </span>
</div>

<?php 
    if(form_error('categoryID')) 
        echo "<div class='form-group has-error' >";
    else     
        echo "<div class='form-group' >";
?>
    <label for="categoryID" class="col-sm-2 control-label hostel  <?= $this->input->post('studentType')=='2' || $student->studentType == '2' ? '' : 'hide'; ?>">
        <?=$this->lang->line("hmember_class_type")?> <span class="text-red">*</span>
    </label>
    <div class="col-sm-6  <?= $this->input->post('studentType')=='2' || $student->studentType == '2' ? '' : 'hide'; ?> hostel">
        <?php
            $array = array(0 => $this->lang->line("hmember_select_class_type"));
            if(customCompute($categorys)) {
                foreach ($categorys as $key => $category) {
                    $array[$category->categoryID] = $category->class_type;
                }
            }
            echo form_dropdown("categoryID", $array, set_value("categoryID", isset($studntHostelDetails->categoryID) ?$studntHostelDetails->categoryID : '' ), "id='categoryID' class='form-control select2'");
        ?>
    </div>
    <span class="col-sm-4 control-label <?= $this->input->post('studentType')=='2' || $student->studentType == '2' ? '' : 'hide'; ?> hostel" >
        <?php echo form_error('categoryID'); ?>
    </span>
</div>

<?php 
    if(form_error('transportID')) 
        echo "<div class='form-group has-error' >";
    else     
        echo "<div class='form-group' >";
?>
    <label for="transportID" class="col-sm-2 control-label  <?= $this->input->post('studentType')=='1' || $student->studentType == '1' ? '' : 'hide'; ?> transport" >
        <?=$this->lang->line("tmember_route_name")?> <span class="text-red">*</span>
    </label>
    <div class="col-sm-6 <?= $this->input->post('studentType')=='1' || $student->studentType == '1' ? '' : 'hide'; ?> transport" >
        
        <?php
            $array = array();
            $array[0] = $this->lang->line("classes_select_route_name");
            foreach ($transports as $transport) {
                $array[$transport->transportID] = $transport->route;
            }
            echo form_dropdown("transportID", $array, set_value("transportID", isset($studntTransportDetails->transportID) ? ($studntTransportDetails->transportID) : '' ), "id='transportID' class='form-control select2'");
        ?>
    </div>
    <span class="col-sm-4 control-label <?= $this->input->post('studentType')=='1' || $student->studentType == '1'? '' : 'hide'; ?> transport">
        <?php echo form_error('transportID'); ?>
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
 
<span class="  control-label">
    <?php echo form_error('ration_card'); ?>
</span>
</div>
</div>

<div class="row">

    
<?php
if (form_error('account_no'))
    echo "<div class='col-md-4 has-error' >";
else
    echo "<div class='col-md-4' >";
?>
<label for="name_id" class=" control-label">
    Account No <span class="text-red">*</span>
</label>
 
    <input type="text" class="form-control" id="account_no" name="account_no" value="<?= set_value('account_no', $student->account_no) ?>">
 
<span class="  control-label">
    <?php echo form_error('account_no'); ?>
</span>
</div>


    <?php
    if (form_error('bank_name'))
        echo "<div class='col-md-4 has-error' >";
    else
        echo "<div class='col-md-4' >";
    ?>
    <label for="name_id" class=" control-label">
        Bank Name <span class="text-red">*</span>
    </label>
    
        <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?= set_value('bank_name', $student->bank_name) ?>">
    
    <span class="  control-label">
        <?php echo form_error('bank_name'); ?>
    </span>
    </div>

    <?php
    if (form_error('ifsc_code'))
        echo "<div class='col-md-4 has-error' >";
    else
        echo "<div class='col-md-4' >";
    ?>
    <label for="name_id" class=" control-label">
        IFSC Code <span class="text-red">*</span>
    </label>
     
        <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="<?= set_value('ifsc_code', $student->ifsc_code) ?>">
     
    <span class="  control-label">
        <?php echo form_error('ifsc_code'); ?>
    </span>
</div>

<?php
if (form_error('branch_name'))
    echo "<div class='col-md-4 has-error' >";
else
    echo "<div class='col-md-4' >";
?>
<label for="name_id" class=" control-label">
    Branch Name <span class="text-red">*</span>
</label>
 
    <input type="text" class="form-control" id="branch_name" name="branch_name" value="<?= set_value('branch_name', $student->branch_name) ?>">
 
<span class="  control-label">
    <?php echo form_error('branch_name'); ?>
</span>
</div>

    <?php 
        if(form_error('tbalance')) 
            echo "<div class='form-group has-error' >";
        else     
            echo "<div class='form-group' >";
    ?>
        <label for="tbalance" class="col-sm-2 control-label <?= $this->input->post('studentType')=='1' || $student->studentType == '1' ? '' : 'hide'; ?> transport">
            <?=$this->lang->line("tmember_tfee")?> <span class="text-red">*</span>
        </label>
        <div class="col-sm-6 <?= $this->input->post('studentType')=='1' || $student->studentType == '1' ? '' : 'hide'; ?> transport">
            <input type="text" class="form-control" id="tbalance" name="tbalance" value="<?=set_value('tbalance', isset($studntTransportDetails->tbalance) ? $studntTransportDetails->tbalance : '' ); ?>" readonly>
        </div>
        <span class="col-sm-4 control-label <?= $this->input->post('studentType')=='1' || $student->studentType == '1' ? '' : 'hide'; ?> transport">
            <?php echo form_error('tbalance'); ?>
        </span>
    </div>
    
    
<!--
                    <?php 
                        if(form_error('username')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="username" class="col-sm-2 control-label">
                            <?=$this->lang->line("student_username")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="username" name="username" value="<?=set_value('username', $student->username)?>" >
                        </div>
                         <span class="col-sm-4 control-label">
                            <?php echo form_error('username'); ?>
                        </span>
                    </div>-->

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input type="submit" class="btn btn-success" value="<?=$this->lang->line("update_student")?>" >
                        </div>
                    </div>

                </form>

            </div> <!-- col-sm-8 -->
        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->

<script type="text/javascript">
$( ".select2" ).select2();
$('#dob').datepicker({ startView: 2 });
$('#admission_date').datepicker({ startView: 2 });

$('#username').keyup(function() {
    $(this).val($(this).val().replace(/\s/g, ''));
});

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
$('#studentType').change(function() {
        var studentType = $('#studentType').val();
        if(studentType == 1)
        {   
            $('.transport').removeClass('hide');
            $('.hostel').addClass('hide');
        }
        else if(studentType==2){
            $('.transport').addClass('hide');
            $('.hostel').removeClass('hide');
        }
        else{
            $('.transport').addClass('hide');
            $('.hostel').addClass('hide');
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
                 url: "<?=base_url('tmember/transport_fare')?>",
                 data: "id=" + transportID,
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

</script>
