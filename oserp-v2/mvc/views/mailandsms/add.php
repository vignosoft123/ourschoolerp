<style>
    .common_input{
        background: #eee;
    border-radius: 5px;
    margin: 16px 0;
    width: 20%;
    height: 20px;
    padding-left: 10px;
    padding-right: 10px;
    border: 1px solid;
    text-align: left;
    line-height: 30px !important;
    }
</style>


<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-mailandsms"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("mailandsms/index")?>"> <?=$this->lang->line('menu_mailandsms')?></a></li>
            <li class="active"> <?=$this->lang->line('menu_add')?> <?=$this->lang->line('menu_mailandsms')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <!--<li class="<?php if($email == 1) echo 'active'; ?>"><a data-toggle="tab" href="#email" aria-expanded="true"><?=$this->lang->line('mailandsms_email')?></a></li>-->

                        <li class="<?php if($sms == 1) echo 'active'; ?>"><a data-toggle="tab" href="#sms" aria-expanded="true"><?=$this->lang->line('mailandsms_sms')?></a></li>

                        <!--<li class="<?php if($otheremail == 1) echo 'active'; ?>"><a data-toggle="tab" href="#otheremail" aria-expanded="true"><?=$this->lang->line('mailandsms_otheremail')?></a></li>-->

                        <li class="<?php if($othersms == 1) echo 'active'; ?>"><a data-toggle="tab" href="#othersms" aria-expanded="true"><?php echo "Voice Call";?></a></li>

                        <li class="<?php if($othersms == 1) echo 'active'; ?>"><a data-toggle="tab" href="#whatsapp" aria-expanded="true"><?php echo "Bulk Whatsapp";?></a></li>

                    </ul>

                    <div class="tab-content">
                        <div id="email" class="tab-pane <?php if($email == 1) //echo 'active';?> ">
                            <br>
                            <div class="row">
                                <div class="col-sm-10">
                                    <form class="form-horizontal" role="form" method="post">
                                        <?php echo form_hidden('type', 'email'); ?> 
                                        <?php 
                                            if(form_error('email_usertypeID')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="email_usertypeID" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_usertype")?> <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $usertypeArray = array(
                                                        'select' => $this->lang->line('mailandsms_all_role')
                                                    );

                                                    if(customCompute($usertypes)) {
                                                        foreach ($usertypes as $key => $usertype) {
                                                            $usertypeArray[$usertype->usertypeID] = $usertype->usertype;
                                                        }
                                                    }

                                                    echo form_dropdown("email_usertypeID", $usertypeArray, set_value("email_usertypeID"), "id='email_usertypeID' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('email_usertypeID'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('email_schoolyear')) 
                                                echo "<div id='divemail_schoolyear' class='form-group has-error' >";
                                            else     
                                                echo "<div id='divemail_schoolyear' class='form-group' >";
                                        ?>
                                            <label for="email_schoolyear" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_schoolyear")?>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $schoolyearArray = array(
                                                        'select' => $this->lang->line('mailandsms_all_schoolyear')
                                                    );

                                                    if(customCompute($schoolyears)) {
                                                        $setschoolyear = '';
                                                        foreach ($schoolyears as $key => $schoolyear) {
                                                            if($schoolyear->schoolyearID == $siteinfos->school_year) {
                                                                $schoolyearArray[$schoolyear->schoolyearID] = $schoolyear->schoolyear.' - ('.$this->lang->line('mailandsms_default').')';
                                                                $setschoolyear = $schoolyear->schoolyearID;
                                                            } else {
                                                                $schoolyearArray[$schoolyear->schoolyearID] = $schoolyear->schoolyear;
                                                            }
                                                        }
                                                    }

                                                    echo form_dropdown("email_schoolyear", $schoolyearArray, set_value("email_schoolyear", $setschoolyear), "id='email_schoolyear' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('email_schoolyear'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('email_class')) 
                                                echo "<div id='divemail_class' class='form-group has-error' >";
                                            else     
                                                echo "<div id='divemail_class' class='form-group' >";
                                        ?>
                                            <label for="email_class" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_class")?>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $classArray = array(
                                                        'select' => $this->lang->line('mailandsms_all_class')
                                                    );

                                                    if(customCompute($allClasses)) {
                                                        foreach ($allClasses as $allClass) {
                                                            $classArray[$allClass->classesID] = $allClass->classes;
                                                        }
                                                    }

                                                    echo form_dropdown("email_class", $classArray, set_value("email_class"), "id='email_class' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('email_class'); ?>
                                            </span>
                                        </div>

                                        <?php
                                        if(form_error('email_section'))
                                            echo "<div id='divemail_section' class='form-group has-error' >";
                                        else
                                            echo "<div id='divemail_section' class='form-group' >";
                                        ?>
                                        <label for="sectionID" class="col-sm-2 control-label">
                                            <?=$this->lang->line("mailandsms_section")?>
                                        </label>
                                        <div class="col-sm-6">
                                            <?php
                                            $arraysection['select'] = $this->lang->line("mailandsms_all_section");
                                            if(customCompute($sections)) {
                                                foreach ($sections as $section) {
                                                    $arraysection[$section->sectionID] = $section->section;
                                                }
                                            }
                                            echo form_dropdown("email_section", $arraysection, set_value("email_section"), "id='email_section' class='form-control select2'");
                                            ?>
                                        </div>
                                        <span class="col-sm-4 control-label">
                                            <?php echo form_error('email_section'); ?>
                                        </span>
                                    </div>

                                        <?php 
                                            if(form_error('email_users')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="email_users" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_users")?>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $userArray = array(
                                                        'select' => $this->lang->line('mailandsms_all_users')
                                                    );

                                                    if(customCompute($allStudents)) {
                                                        foreach ($allStudents as $allStudent) {
                                                            $userArray[$allStudent->studentID] = $allStudent->name;
                                                        }
                                                    }

                                                    echo form_dropdown("email_users", $userArray, set_value("email_users"), "id='email_users' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('email_users'); ?>
                                            </span>
                                        </div>


                                        
                                        <?php 
                                            if(form_error('email_template')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="email_template" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_template")?>
                                            </label>
                                            <div class="col-sm-6" >
                                                
                                                <?php
                                                    $array = array(
                                                        'select' => $this->lang->line('mailandsms_select_template'),
                                                    );
                                                        
                                                    echo form_dropdown("email_template", $array, set_value("email_template"), "id='email_template' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('email_template'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('email_subject')) 
                                                echo "<div class='form-group has-error' id='subject_section' >";
                                            else     
                                                echo "<div class='form-group' id='subject_section' >";
                                        ?>
                                            <label for="email_subject" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_subject")?> <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="email_subject" name="email_subject" value="<?=set_value('email_subject')?>" >
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('email_subject'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('email_message')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="email_message" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_message")?> <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" id="email_message" name="email_message" ><?=set_value('email_message')?></textarea>
                                            </div>
                                            <span class="col-xs-12 col-sm-10 col-sm-offset-2 control-label">
                                                <?php echo form_error('email_message'); ?>
                                            </span>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-8">
                                                <input type="submit" class="btn btn-success" value="<?=$this->lang->line("send")?>" >
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                        <div id="sms" class="tab-pane <?php if($sms == 1) echo 'active'; ?>">
                            <br>
                            <div class="row">
                                <div class="col-sm-10">
                                    <form class="form-horizontal" role="form" method="post">
                                        <?php echo form_hidden('type', 'sms'); ?> 
                                        <?php 
                                            if(form_error('sms_usertypeID')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="sms_usertypeID" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_usertype")?> <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $array = array(
                                                        'select' => $this->lang->line('mailandsms_all_role')
                                                    );

                                                    if(customCompute($usertypes)) {
                                                        foreach ($usertypes as $key => $usertype) {
                                                            $array[$usertype->usertypeID] = $usertype->usertype;
                                                        }
                                                    }
                                                    echo form_dropdown("sms_usertypeID", $array, set_value("sms_usertypeID"), "id='sms_usertypeID' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('sms_usertypeID'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('sms_schoolyear')) 
                                                echo "<div id='divsms_schoolyear' class='form-group has-error' >";
                                            else     
                                                echo "<div id='divsms_schoolyear' class='form-group' >";
                                        ?>
                                            <label for="sms_schoolyear" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_schoolyear")?>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $array = array(
                                                        'select' => $this->lang->line('mailandsms_all_schoolyear')
                                                    );

                                                    if(customCompute($schoolyears)) {
                                                        $setschoolyear = '';
                                                        foreach ($schoolyears as $key => $schoolyear) {
                                                            if($schoolyear->schoolyearID == $siteinfos->school_year) {
                                                                $array[$schoolyear->schoolyearID] = $schoolyear->schoolyear.' - ('.$this->lang->line('mailandsms_default').')';
                                                                $setschoolyear = $schoolyear->schoolyearID;
                                                            } else {
                                                                $array[$schoolyear->schoolyearID] = $schoolyear->schoolyear;
                                                            }
                                                        }
                                                    }

                                                    echo form_dropdown("sms_schoolyear", $array, set_value("sms_schoolyear", $setschoolyear), "id='sms_schoolyear' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('sms_schoolyear'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('sms_class')) 
                                                echo "<div id='divsms_class' class='form-group has-error' >";
                                            else     
                                                echo "<div id='divsms_class' class='form-group' >";
                                        ?>
                                            <label for="sms_class" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_class")?>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $classArray = array(
                                                        'select' => $this->lang->line('mailandsms_all_class')
                                                    );

                                                    if(customCompute($allClasses)) {
                                                        foreach ($allClasses as $allClass) {
                                                            $classArray[$allClass->classesID] = $allClass->classes;
                                                        }
                                                    }

                                                    echo form_dropdown("sms_class", $classArray, set_value("sms_class"), "id='sms_class' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('sms_class'); ?>
                                            </span>
                                        </div>
                                        <?php
                                        if(form_error('sms_section'))
                                            echo "<div id='divsms_section' class='form-group has-error' >";
                                        else
                                            echo "<div id='divsms_section' class='form-group' >";
                                        ?>
                                        <label for="sectionID" class="col-sm-2 control-label">
                                            <?=$this->lang->line("mailandsms_section")?>
                                        </label>
                                        <div class="col-sm-6">
                                            <?php
                                            $arraysection['select'] = $this->lang->line("mailandsms_all_section");
                                            if(customCompute($sections)) {
                                                foreach ($sections as $section) {
                                                    $arraysection[$section->sectionID] = $section->section;
                                                }
                                            }
                                            echo form_dropdown("sms_section", $arraysection, set_value("sms_section"), "id='sms_section' class='form-control select2'");
                                            ?>
                                        </div>
                                        <span class="col-sm-4 control-label">
                                                 <?php echo form_error('sms_section'); ?>
                                           </span>
                                        </div>


                                        <?php
                                        if(form_error('hostel_transport'))
                                            echo "<div id='hostel_transport' class='form-group has-error' >";
                                        else
                                            echo "<div id='hostel_transport' class='form-group' >";
                                        ?>
                                        <label for="hostel_transport" class="col-sm-2 control-label">
                                            Hostel/Trasport
                                        </label>
                                        <div class="col-sm-6">
                                           <select name="sms_hostel_transport" id="sms_hostel_transport" class='form-control select2'>
                                            <option value="">Select</option>
                                            <option value="1">Hostel</option>
                                            <option value="2">Transport</option>
                                        </select>
                                        </div>
                                        <span class="col-sm-4 control-label">
                                                 <?php echo form_error('hostel_transport'); ?>
                                           </span>
                                        </div>


                                        <?php 
                                            if(form_error('sms_users')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="sms_users" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_users")?>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $userArray = array(
                                                        'select' => $this->lang->line('mailandsms_all_users')
                                                    );

                                                    if(customCompute($allStudents)) {
                                                        foreach ($allStudents as $allStudent) {
                                                            $userArray[$allStudent->studentID] = $allStudent->name;
                                                        }
                                                    }

                                                    echo form_dropdown("sms_users", $userArray, set_value("sms_users"), "id='sms_users' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('sms_users'); ?>
                                            </span>
                                        </div>

                                        <div class='form-group' >
                                        <label for="sms_template" class="col-sm-2 control-label">
                                               Select Type </label>
                                            <div class="col-sm-6" >
                                            <input type="radio" name="auto_manual" id="manual" value="manual" checked>Manual
                                            <input type="radio" name="auto_manual" id="auto" value="auto">Auto

                                            </div>
                                        </div>

                                        <?php 
                                            if(form_error('sms_template')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="sms_template" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_template")?>
                                            </label>
                                            <div class="col-sm-6" >
                                                <?php
                                                    $array = array(
                                                        'select' => $this->lang->line('mailandsms_select_template'),
                                                    );

                                                    echo form_dropdown("sms_template", $array, set_value("sms_template"), "id='sms_template' class='form-control select2'");
                                                ?>
                                                
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('sms_template'); ?>
                                            </span>
                                        </div>

                                        <div class="form-group <?=(($submittype == 'sms') && form_error('sms_getway')) ? 'has-error' : '' ?>" style="display:none;">
                                            <label for="sms_getway" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_getway")?> <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <input type="hidden" name="sms_getway" value="msg91">
                                                <?php
                                                    $array = array(
                                                        'select' => $this->lang->line('mailandsms_select_send_by'),
                                                        'clickatell' => $this->lang->line('mailandsms_clickatell'),
                                                        'twilio' => $this->lang->line('mailandsms_twilio'),
                                                        'bulk' => $this->lang->line('mailandsms_bulk'),
                                                        'msg91' => $this->lang->line('mailandsms_msg91'),
                                                    );
                                                    // echo form_dropdown("sms_getway", $array, (($submittype == 'sms') ? set_value("sms_getway") : ''), "id='sms_getway' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?=(($submittype == 'sms') && form_error('sms_getway')) ? form_error('sms_getway') : '' ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('sms_message')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="sms_message" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_message")?> <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="resize:vertical" id="sms_message" name="sms_message" ><?=set_value('sms_message')?></textarea>

                                                <div id="manual_template" name="aaa" style="background-color: aliceblue; padding: 25px;">
                                            
                                                
                                            </div>

                                                
                                            </div>
                                            <span class="col-xs-12 col-sm-10 col-sm-offset-2 control-label">
                                                <?php echo form_error('sms_message'); ?>
                                            </span>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-1 col-sm-8">
                                                <input type="submit" class="btn btn-success" value="<?=$this->lang->line("send")?>" >
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                        <div id="otheremail" class="tab-pane <?php if($otheremail == 1) echo 'active';?> ">
                            <br>
                            <div class="row">
                                <div class="col-sm-10">
                                    <form class="form-horizontal" role="form" method="post">
                                        <?php echo form_hidden('type', 'otheremail'); ?> 

                                        <?php 
                                            if(form_error('otheremail_name')) 
                                                echo "<div class='form-group has-error'>";
                                            else     
                                                echo "<div class='form-group'>";
                                        ?>
                                            <label for="otheremail_name" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_name")?> <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="otheremail_name" name="otheremail_name" value="<?=set_value('otheremail_name')?>" >
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('otheremail_name'); ?>
                                            </span>
                                        </div>


                                        <?php 
                                            if(form_error('otheremail_email')) 
                                                echo "<div class='form-group has-error'>";
                                            else     
                                                echo "<div class='form-group'>";
                                        ?>
                                            <label for="otheremail_email" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_email")?> <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="otheremail_email" name="otheremail_email" value="<?=set_value('otheremail_email')?>" >
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('otheremail_email'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('otheremail_subject')) 
                                                echo "<div class='form-group has-error'>";
                                            else     
                                                echo "<div class='form-group'>";
                                        ?>
                                            <label for="otheremail_subject" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_subject")?> <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="otheremail_subject" name="otheremail_subject" value="<?=set_value('otheremail_subject')?>" >
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('otheremail_subject'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('otheremail_message')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="otheremail_message" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_message")?> <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" id="otheremail_message" name="otheremail_message" ><?=set_value('otheremail_message')?></textarea>
                                            </div>
                                            <span class="col-xs-12 col-sm-10 col-sm-offset-2 control-label">
                                                <?php echo form_error('otheremail_message'); ?>
                                            </span>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-8">
                                                <input type="submit" class="btn btn-success" value="<?=$this->lang->line("send")?>" >
                                            </div>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>

                        <div id="othersms" class="tab-pane <?php if($othersms == 1) echo 'active';?> ">
                            <br>
                            <div class="row">
                                <div class="col-sm-10">
                                    <form class="form-horizontal" role="form" method="post" id="voice_form">
                                        <div class='form-group'>
                                            <label for="from_call" class="col-sm-2 control-label">
                                                New Voice File
                                            </label>
                                            <div class="col-sm-6">
                                                <input type="file" id="csv_file" class="form-control" name="individual_attachment" accept=".wav,.mp3">
                                            </div>
                                            <div class="col-sm-2">
                                                <a href="javascript:void(0)" id="upload_btn" class="btn btn-success upload_btn" onclick="uploadFile()">Upload </a>
                                            </div>
                                        </div>
                                        <?php echo form_hidden('type', 'voice'); ?>
                                        <?php 
                                            if(form_error('from_call')) 
                                                echo "<div class='form-group has-error'>";
                                            else     
                                                echo "<div class='form-group'>";
                                        ?>
                                            <label for="from_call" class="col-sm-2 control-label">
                                                From Caller ID <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <select id="from_call" name="from_call" class="form-control select2">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                    foreach($caller_list as $key => $caller_info){
                                                ?>
                                                    <option value="<?php echo $caller_info->mobile_no;?>"><?php echo $caller_info->mobile_no." ".$caller_info->title;?></option>
                                                <?php
                                                    }
                                                ?>
                                                </select>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('from_call'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('voice_file')) 
                                                echo "<div class='form-group has-error'>";
                                            else     
                                                echo "<div class='form-group'>";
                                        ?>
                                            <label for="voice_file" class="col-sm-2 control-label">
                                                Existing Voice File <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <select id="voice_file" name="voice_file" class="form-control select2 voice_file">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                    foreach($voice_list as $key => $voice_info){
                                                ?>
                                                    <option value="<?php echo $voice_info->voice_file_url;?>"><?php echo $voice_info->title." (".$voice_info->duration." seconds)";?></option>
                                                <?php
                                                    }
                                                ?>
                                                </select>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('voice_file'); ?>
                                            </span>
                                        </div>
                                        <?php 
                                            if(form_error('voice_usertypeID')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="voice_usertypeID" class="col-sm-2 control-label">
                                                Role <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $usertypeArray = array(
                                                        'select' => $this->lang->line('mailandsms_all_role')
                                                    );
                                                    if(customCompute($usertypes)) {
                                                        foreach ($usertypes as $key => $usertype) {
                                                            if($usertype->usertypeID==2 || $usertype->usertypeID==3)
                                                            {
                                                                $usertypeArray[$usertype->usertypeID] = $usertype->usertype;
                                                            }
                                                        }
                                                    }
                                                    echo form_dropdown("voice_usertypeID", $usertypeArray, set_value("voice_usertypeID"), "id='voice_usertypeID' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('voice_usertypeID'); ?>
                                            </span>
                                        </div>
                                        
                                        <?php 
                                            if(form_error('voice_schoolyear')) 
                                                echo "<div id='divvoice_schoolyear' class='form-group has-error' >";
                                            else     
                                                echo "<div id='divvoice_schoolyear' class='form-group' >";
                                        ?>
                                            <label for="voice_schoolyear" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_schoolyear")?>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $array = array(
                                                        'select' => $this->lang->line('mailandsms_all_schoolyear')
                                                    );
                                                    if(customCompute($schoolyears)) {
                                                        $setschoolyear = '';
                                                        foreach ($schoolyears as $key => $schoolyear) {
                                                            if($schoolyear->schoolyearID == $siteinfos->school_year) {
                                                                $array[$schoolyear->schoolyearID] = $schoolyear->schoolyear.' - ('.$this->lang->line('mailandsms_default').')';
                                                                $setschoolyear = $schoolyear->schoolyearID;
                                                            } else {
                                                                $array[$schoolyear->schoolyearID] = $schoolyear->schoolyear;
                                                            }
                                                        }
                                                    }
                                                    echo form_dropdown("voice_schoolyear", $array, set_value("voice_schoolyear", $setschoolyear), "id='voice_schoolyear' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('voice_schoolyear'); ?>
                                            </span>
                                        </div>
                                        
                                        <?php 
                                            if(form_error('voice_class')) 
                                                echo "<div id='divvoice_class' class='form-group has-error' >";
                                            else     
                                                echo "<div id='divvoice_class' class='form-group' >";
                                        ?>
                                            <label for="voice_class" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandsms_class")?>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $classArray = array(
                                                        'select' => $this->lang->line('mailandsms_all_class')
                                                    );
                                                    if(customCompute($allClasses)) {
                                                        foreach ($allClasses as $allClass) {
                                                            $classArray[$allClass->classesID] = $allClass->classes;
                                                        }
                                                    }
                                                    echo form_dropdown("voice_class", $classArray, set_value("voice_class"), "id='voice_class' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('voice_class'); ?>
                                            </span>
                                        </div>
                                        
                                        <?php 
                                            if(form_error('to_numbers')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="to_numbers" class="col-sm-2 control-label">
                                                Other Numbers
                                            </label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" id="to_numbers" name="to_numbers" rows="4"><?=set_value('to_numbers')?></textarea>
                                            </div>
                                            <span class="col-xs-12 col-sm-10 col-sm-offset-2 control-label">
                                                <?php echo form_error('to_numbers'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('voice_usertypeID')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="voice_usertypeID" class="col-sm-2 control-label">
                                                Role <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $usertypeArray = array(
                                                        'select' => $this->lang->line('mailandsms_all_role')
                                                    );
                                                    if(customCompute($usertypes)) {
                                                        foreach ($usertypes as $key => $usertype) {
                                                            if($usertype->usertypeID==2 || $usertype->usertypeID==3)
                                                            {
                                                                $usertypeArray[$usertype->usertypeID] = $usertype->usertype;
                                                            }
                                                        }
                                                    }
                                                    echo form_dropdown("voice_usertypeID", $usertypeArray, set_value("voice_usertypeID"), "id='voice_usertypeID' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('voice_usertypeID'); ?>
                                            </span>
                                        </div>
                                        
                                        


                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-8">
                                                <input type="submit" class="btn btn-success" value="<?=$this->lang->line("send")?>" >
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- whatsapp start -->
                        <div id="whatsapp"  class="tab-pane <?php if($whatsapp == 1) echo 'active'; ?>">
                            <br>
                            <div class="row">
                                <div class="col-sm-10">
                                    <form class="form-horizontal" role="form" method="post" id="whatsapp_form">
                                        <?php echo form_hidden('type', 'whatsapp'); ?> 
                                        <?php 
                                            if(form_error('whatsapp_usertypeID')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="whatsapp_usertypeID" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandwhatsapp_usertype")?> <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $array = array(
                                                        'select' => $this->lang->line('mailandsms_all_role')
                                                    );

                                                    if(customCompute($usertypes)) {
                                                        foreach ($usertypes as $key => $usertype) {
                                                            $array[$usertype->usertypeID] = $usertype->usertype;
                                                        }
                                                    }
                                                    echo form_dropdown("whatsapp_usertypeID", $array, set_value("whatsapp_usertypeID"), "id='whatsapp_usertypeID' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('whatsapp_usertypeID'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('whatsapp_schoolyear')) 
                                                echo "<div id='divwhatsapp_schoolyear' class='form-group has-error' >";
                                            else     
                                                echo "<div id='divwhatsapp_schoolyear' class='form-group' >";
                                        ?>
                                            <label for="whatsapp_schoolyear" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandwhatsapp_schoolyear")?>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $array = array(
                                                        'select' => $this->lang->line('mailandwhatsapp_all_schoolyear')
                                                    );

                                                    if(customCompute($schoolyears)) {
                                                        $setschoolyear = '';
                                                        foreach ($schoolyears as $key => $schoolyear) {
                                                            if($schoolyear->schoolyearID == $siteinfos->school_year) {
                                                                $array[$schoolyear->schoolyearID] = $schoolyear->schoolyear.' - ('.$this->lang->line('mailandwhatsapp_default').')';
                                                                $setschoolyear = $schoolyear->schoolyearID;
                                                            } else {
                                                                $array[$schoolyear->schoolyearID] = $schoolyear->schoolyear;
                                                            }
                                                        }
                                                    }

                                                    echo form_dropdown("whatsapp_schoolyear", $array, set_value("whatsapp_schoolyear", $setschoolyear), "id='whatsapp_schoolyear' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('whatsapp_schoolyear'); ?>
                                            </span>
                                        </div>

                                        <?php 
                                            if(form_error('whatsapp_class')) 
                                                echo "<div id='divwhatsapp_class' class='form-group has-error' >";
                                            else     
                                                echo "<div id='divwhatsapp_class' class='form-group' >";
                                        ?>
                                            <label for="whatsapp_class" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandwhatsapp_class")?>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $classArray = array(
                                                        'select' => $this->lang->line('mailandwhatsapp_all_class')
                                                    );

                                                    if(customCompute($allClasses)) {
                                                        foreach ($allClasses as $allClass) {
                                                            $classArray[$allClass->classesID] = $allClass->classes;
                                                        }
                                                    }

                                                    echo form_dropdown("whatsapp_class", $classArray, set_value("whatsapp_class"), "id='whatsapp_class' class='form-control select2'");
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('whatsapp_class'); ?>
                                            </span>
                                        </div>
                                        <?php
                                        if(form_error('whatsapp_section'))
                                            echo "<div id='divwhatsapp_section' class='form-group has-error' >";
                                        else
                                            echo "<div id='divwhatsapp_section' class='form-group' >";
                                        ?>
                                        <label for="sectionID" class="col-sm-2 control-label">
                                            <?=$this->lang->line("mailandwhatsapp_section")?>
                                        </label>
                                        <div class="col-sm-6">
                                            <?php
                                            $arraysection['select'] = $this->lang->line("mailandwhatsapp_all_section");
                                            if(customCompute($sections)) {
                                                foreach ($sections as $section) {
                                                    $arraysection[$section->sectionID] = $section->section;
                                                }
                                            }
                                            echo form_dropdown("whatsapp_section", $arraysection, set_value("whatsapp_section"), "id='whatsapp_section' class='form-control select2'");
                                            ?>
                                        </div>
                                        <span class="col-sm-4 control-label">
                                                 <?php echo form_error('whatsapp_section'); ?>
                                           </span>
                                        </div>

                                        <?php 
                                            if(form_error('whatsapp_users')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="whatsapp_users" class="col-sm-2 control-label">
                                                <?=$this->lang->line("mailandwhatsapp_users")?>
                                            </label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $userArray = array(
                                                        'select' => $this->lang->line('mailandwhatsapp_all_users')
                                                    );

                                                    if(customCompute($allStudents)) {
                                                        foreach ($allStudents as $allStudent) {
                                                            $userArray[$allStudent->studentID] = $allStudent->name;
                                                        }
                                                    }

                                                    echo form_dropdown("whatsapp_users[]", $userArray, set_value("whatsapp_users"), "id='whatsapp_users' class='form-control select2' multiple" );
                                                ?>
                                            </div>
                                            <span class="col-sm-4 control-label">
                                                <?php echo form_error('whatsapp_users'); ?>
                                            </span>
                                        </div>

                                        

                                        <?php 
                                            if(form_error('whatsapp_numbers')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="whatsapp_numbers" class="col-sm-2 control-label">
                                              Other Whatsapp Numbers   <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="resize:vertical" id="whatsapp_numbers" name="others" ><?=set_value('whatsapp_numbers')?></textarea>
                                            </div>
                                            <span class="col-xs-12 col-sm-10 col-sm-offset-2 control-label">
                                                <?php echo form_error('whatsapp_numbers'); ?>
                                            </span>
                                        </div>


                                        <?php 
                                            if(form_error('whatsapp_message')) 
                                                echo "<div class='form-group has-error' >";
                                            else     
                                                echo "<div class='form-group' >";
                                        ?>
                                            <label for="whatsapp_message" class="col-sm-2 control-label">
                                               Whatsapp Message  <span class="text-red">*</span>
                                            </label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="resize:vertical" id="whatsapp_message" name="whatsapp_message" ><?=set_value('whatsapp_message')?></textarea>
                                            </div>
                                            <span class="col-xs-12 col-sm-10 col-sm-offset-2 control-label">
                                                <?php echo form_error('whatsapp_message'); ?>
                                            </span>
                                        </div>
                                        <input type="hidden" name="dynamic_file1_path" id="dynamic_file1_path" value="">
                                         
                                        <div class='form-group'>
                                            <label for="from_call" class="col-sm-2 control-label">
                                                New File
                                            </label>
                                            <div class="col-sm-6">
                                                <input type="file" id="csv_file1" class="form-control" name="individual_attachment1" >
                                            </div>
                                            <div class="col-sm-2">
                                                <a href="javascript:void(0)" id="upload_btn1" class="btn btn-success upload_btn1" onclick="uploadFile1()">Upload </a>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <div class="col-sm-offset-1 col-sm-8">
                                                <input type="submit" class="btn btn-success" value="<?=$this->lang->line("send")?>" >
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- whatsapp end -->
                    </div>
                </div> <!-- nav-tabs-custom -->

                <?php if ($siteinfos->note==1) { ?>
                    <div class="callout callout-danger">
                        <p><b>Note:</b> Admin can make custom template before send voice or sms, that will help you send voice/sms very quickly.</p>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>

</div><!-- /.box -->
<script type="text/javascript" src="<?php echo base_url('assets/editor/jquery-te-1.4.0.min.js'); ?>"></script>

<?php 
    $useroption = '<option value="select">'.$this->lang->line('mailandsms_all_users').'</option>';
    $classoption = '<option value="select">'.$this->lang->line('mailandsms_all_class').'</option>';
    $schoolyearoption = '<option value="select">'.$this->lang->line('mailandsms_all_schoolyear').'</option>';

    $setEmailUserTypeID = $email_usertypeID;
    $setSMSUserTypeID = $sms_usertypeID;

    $setEmailUserID = $emailUserID;
    $setEmailTemplateID = $emailTemplateID;

    $setSMSUserID = $smsUserID;
    $setSMSTemplateID = $smsTemplateID;

?>

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
        $('#divemail_class').hide();
        $('#divemail_section').hide();
        $('#divemail_schoolyear').hide();
        $('#email_message').jqte();
        $('#otheremail_message').jqte();


        var usertypeID = "<?=$setEmailUserTypeID?>";
        var userID = "<?=$setEmailUserID?>";
        var emailTemplateID = "<?=$setEmailTemplateID?>";

        var nonEmailUsertypeID = usertypeID;
        var euser = userID;
        if(usertypeID != 'select') {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('mailandsms/alltemplate')?>",
                data: "usertypeID=" + usertypeID + "&type=" + "email",
                dataType: "html",
                success: function(data) {
                   $('#email_template').html(data);
                   $('#email_template').val(emailTemplateID).trigger('change');
                }
            });

            $.ajax({
                type: 'POST',
                url: "<?=base_url('mailandsms/allusers')?>",
                data: {'usertypeID' : usertypeID, 'userID' : userID },
                dataType: "html",
                success: function(data) {
                    if(usertypeID == 3) {
                        $('#divemail_class').show();
                        $('#divemail_section').show();

                        $('#divemail_schoolyear').show();

                        $('#email_users').val(euser).trigger('change');
                    } else if(nonEmailUsertypeID == 3) {
                        $('#divemail_class').show();
                        $('#divemail_section').show();

                        $('#divemail_schoolyear').show();

                        $('#email_users').val(euser).trigger('change');
                    } else {
                        $('#divemail_schoolyear').hide();
                        $('#divemail_class').hide();
                        $('#divemail_section').hide();
                        $('#email_users').html(data);
                        $('#email_users').val(euser).trigger('change');
                    }
                }
            });

        } else {
            $('#email_users').html('<?=$useroption?>');
        }

        $("#email_usertypeID").change(function() {
            var usertypeID = $(this).val();
            var userID = "<?=$setEmailUserID?>";
            if(usertypeID != 'select') {
                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/alltemplate')?>",
                    data: "usertypeID=" + usertypeID + "&type=" + "email",
                    dataType: "html",
                    success: function(data) {
                       $('#email_template').html(data);
                       $('#email_template').val('select').trigger('change');
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allusers')?>",
                    data: {"usertypeID" : usertypeID, 'userID' : userID },
                    dataType: "html",
                    success: function(data) {
                        if(usertypeID == 3) {
                            $('#divemail_class').show();
                            $('#divemail_section').show();
                            $('#email_class').html(data);

                            $('#divemail_schoolyear').show();

                            $('#email_users').html('<?=$useroption?>');
                            $('#email_users').val('select').trigger('change');
                        } else {
                            $('#divemail_schoolyear').hide();
                            $('#divemail_class').hide();
                            $('#divemail_section').hide();
                            $('#email_users').html(data);
                            $('#email_users').val('select').trigger('change');
                        }
                    }
                });
            } else {
                $('#email_users').html('<?=$useroption?>');
            }
        });

        $('#email_schoolyear').change(function() {
            var schoolyear = $(this).val();
            if(schoolyear != 'select') {
                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allusers')?>",
                    data: "usertypeID=" + 3,
                    dataType: "html",
                    success: function(data) {
                       $('#email_class').html(data);
                    }
                });
            } else {
                $('#email_class').html('<?=$classoption?>');
                $('#email_users').html('<?=$useroption?>');
            } 
        });

        $('#email_class').change(function() {
            var schoolyear = $('#email_schoolyear').val();
            var classes = $(this).val();
            if(classes != 'select') {

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allsection')?>",
                    data: "classes=" + classes,
                    dataType: "html",
                    success: function(data) {
                        $('#email_section').html(data)
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allstudent')?>",
                    data: "schoolyear=" + schoolyear + "&classes=" + classes + "&section=",
                    dataType: "html",
                    success: function(data) {
                       $('#email_users').html(data);
                    }
                });
            } else {
                $('#email_users').html('<?=$useroption?>');
            }
        });

        $('#email_section').change(function() {
            var schoolyear = $('#email_schoolyear').val();
            var section = $(this).val();
            var classes = $('#email_class').val();
            if(section != 'select') {

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allstudent')?>",
                    data: "schoolyear=" + schoolyear + "&classes=" + classes + "&section=" + section,
                    dataType: "html",
                    success: function(data) {
                        console.log(data);
                        $('#email_users').html(data);
                    }
                });
            } else {
                $('#email_users').html('<?=$useroption?>');
            }
        });



        $('#email_template').change(function() {
            var templateID = $(this).val();
                $.ajax({
                type: 'POST',
                url: "<?=base_url('mailandsms/alltemplatedesign')?>",
                data: "templateID=" + templateID,
                dataType: "html",
                success: function(data) {
                   $('.jqte_editor').html(data);
                }
            });

        });


        /* Start For Sms */

        $('#divsms_class').hide();
        $('#divsms_section').hide();
        $('#hostel_transport').hide();
        $('#divsms_schoolyear').hide();
        $('#divvoice_schoolyear').hide();

        

        var usertypeID = "<?=$setSMSUserTypeID?>";
        var userID = "<?=$setSMSUserID?>";
        var smsTemplateID = "<?=$setSMSTemplateID?>";
        var nonSMSUsertypeID = usertypeID;

        if(usertypeID != 'select') {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('mailandsms/alltemplate')?>",
                data: "usertypeID=" + usertypeID + "&type=" + "sms",
                dataType: "html",
                success: function(data) {
                   $('#sms_template').html(data);
                   $('#sms_template').val(smsTemplateID).trigger('change');
                }
            });

            $.ajax({
                type: 'POST',
                url: "<?=base_url('mailandsms/allusers')?>",
                data: {'usertypeID' : usertypeID, 'userID' : userID },
                dataType: "html",
                success: function(data) {
                    if(usertypeID == 3) {
                        $('#divsms_class').show();
                        $('#divsms_section').show();
                        $('#hostel_transport').show();

                        $('#divsms_schoolyear').show();
                        
                        $('#sms_users').val(userID).trigger('change');
                    } else if(nonSMSUsertypeID == 3) {
                        $('#divsms_class').show();
                        $('#divsms_section').show();
                        $('#hostel_transport').show();


                        $('#divsms_schoolyear').show();

                        $('#sms_users').val(userID).trigger('change');
                    } else {
                        $('#divsms_schoolyear').hide();
                        $('#divsms_class').hide();
                        $('#divsms_section').hide();
                        $('#hostel_transport').hide();

                        $('#sms_users').html(data);
                        $('#sms_users').val(userID).trigger('change');
                    }
                }
            });
        } else {
            $('#sms_users').html('<?=$useroption?>');
        }

        $("#sms_usertypeID").change(function() {
            var usertypeID = $(this).val();
            var userID = "<?=$setSMSUserID?>";
            if(usertypeID != 'select') {
                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/alltemplate')?>",
                    data: "usertypeID=" + usertypeID + "&type=" + "sms",
                    dataType: "html",
                    success: function(data) {
                       $('#sms_template').html(data);
                       $('#sms_template').val('select').trigger('change');
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allusers')?>",
                    data: {'usertypeID' : usertypeID, 'userID' : userID },
                    dataType: "html",
                    success: function(data) {
                        if(usertypeID == 3) {
                            $('#divsms_class').show();
                            $('#divsms_section').show();
                            $('#hostel_transport').show();

                            $('#sms_class').html(data);

                            $('#divsms_schoolyear').show();

                            $('#sms_users').html('<?=$useroption?>');
                            $('#sms_users').val('select').trigger('change');
                        } else {
                            $('#divsms_schoolyear').hide();
                            $('#divsms_class').hide();
                            $('#divsms_section').hide();
                            $('#hostel_transport').hide();

                            $('#sms_users').html(data);
                            $('#sms_users').val('select').trigger('change');
                        }
                    }
                });
            } else {
                $('#sms_users').html('<?=$useroption?>');
            }
        });

        $('#sms_schoolyear').change(function() {
            var schoolyear = $(this).val();
            if(schoolyear != 'select') {
                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allusers')?>",
                    data: "usertypeID=" + 3,
                    dataType: "html",
                    success: function(data) {
                       $('#sms_class').html(data);
                    }
                });
            } else {
                $('#sms_class').html('<?=$classoption?>');
                $('#sms_users').html('<?=$useroption?>');
            } 
        });

        $('#sms_class').change(function() {
            var schoolyear = $('#sms_schoolyear').val();
            var classes = $(this).val();


            $('#sms_hostel_transport option[value=""]').prop('selected',true);
            $('#sms_hostel_transport').trigger('change');


            if(classes != 'select') {

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allsection')?>",
                    data: "classes=" + classes,
                    dataType: "html",
                    success: function(data) {
                        $('#sms_section').html(data)
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allstudent')?>",
                    data: "schoolyear=" + schoolyear + "&classes=" + classes + "&section=",
                    dataType: "html",
                    success: function(data) {
                        $('#sms_users').html(data);
                    }
                });
            } else {
                $('#sms_users').html('<?=$useroption?>');
            }
        });

        $('#sms_section').change(function() {
            var schoolyear = $('#sms_schoolyear').val();
            var section = $(this).val();
            var classes = $('#sms_class').val();


            $('#sms_hostel_transport option[value=""]').prop('selected',true);
            $('#sms_hostel_transport').trigger('change');


            if(section != 'select') {

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allstudent')?>",
                    data: "schoolyear=" + schoolyear + "&classes=" + classes + "&section=" + section,
                    dataType: "html",
                    success: function(data) {
                        console.log(data);
                        $('#sms_users').html(data);
                    }
                });
            } else {
                $('#sms_users').html('<?=$useroption?>');
            }
        });

        $('#sms_hostel_transport').change(function() {
            var schoolyear = $('#sms_schoolyear').val();
            var hostel_transport = $(this).val();
            var classes = $('#sms_class').val();
            var section = $('#sms_section').val();
            if(hostel_transport != '') {

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allstudent')?>",
                    data: "schoolyear=" + schoolyear + "&classes=" + classes + "&section=" + section+ "&hostel_transport=" + hostel_transport,
                    dataType: "html",
                    success: function(data) {
                        console.log(data);
                        $('#sms_users').html(data);
                    }
                });
            } else {
                $('#sms_users').html('<?=$useroption?>');
            }
        });

        $('#sms_template').change(function() {
            var templateID = $(this).val();
            var auto_manual = $('input[name="auto_manual"]:checked').val();

                $.ajax({
                type: 'POST',
                url: "<?=base_url('mailandsms/alltemplatedesign_sms')?>",
                data: "templateID=" + templateID + "&auto_manual=" + auto_manual ,
                dataType: "html",
                success: function(data) {
                    if(auto_manual == 'auto'){
                        $('#sms_message').html(data);
                    }else{
                        $('#manual_template').html(data);
                    }
                //    
                    
                }
            });

        }); 

            


/* Start For whatsapp */

$('#divwhatsapp_class').hide();
        $('#divwhatsapp_section').hide();
        $('#divwhatsapp_schoolyear').hide();
        $('#divvoice_schoolyear').hide();

        $('#divwhatsapp_class').hide();
        $('#divwhatsapp_section').hide();
        $('#divwhatsapp_schoolyear').hide(); 

        var usertypeID = "<?=$setwhatsappUserTypeID?>";
        var userID = "<?=$setwhatsappUserID?>";
        var whatsappTemplateID = "<?=$setwhatsappTemplateID?>";
        var nonwhatsappUsertypeID = usertypeID;

        if(usertypeID != 'select') {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('mailandsms/alltemplate')?>",
                data: "usertypeID=" + usertypeID + "&type=" + "sms",
                dataType: "html",
                success: function(data) {
                   $('#sms_template').html(data);
                   $('#sms_template').val(smsTemplateID).trigger('change');
                }
            });

            $.ajax({
                type: 'POST',
                url: "<?=base_url('mailandsms/allusers')?>",
                data: {'usertypeID' : usertypeID, 'userID' : userID },
                dataType: "html",
                success: function(data) {
                    if(usertypeID == 3) {
                        $('#divwhatsapp_class').show();
                        $('#divwhatsapp_section').show();

                        $('#divwhatsapp_schoolyear').show();
                        
                        $('#whatsapp_users').val(userID).trigger('change');
                    } else if(nonwhatsappUsertypeID == 3) {
                        $('#divwhatsapp_class').show();
                        $('#divwhatsapp_section').show();

                        $('#divwhatsapp_schoolyear').show();

                        $('#whatsapp_users').val(userID).trigger('change');
                    } else {
                        $('#divwhatsapp_schoolyear').hide();
                        $('#divwhatsapp_class').hide();
                        $('#divwhatsapp_section').hide();
                        $('#whatsapp_users').html(data);
                        $('#whatsapp_users').val(userID).trigger('change');
                    }
                }
            });
        } else {
            $('#whatsapp_users').html('<?=$useroption?>');
        }

        $("#whatsapp_usertypeID").change(function() {
            var usertypeID = $(this).val();
            var userID = "<?=$setwhatsappUserID?>";
            if(usertypeID != 'select') {
                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/alltemplate')?>",
                    data: "usertypeID=" + usertypeID + "&type=" + "sms",
                    dataType: "html",
                    success: function(data) {
                       $('#sms_template').html(data);
                       $('#sms_template').val('select').trigger('change');
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allusers')?>",
                    data: {'usertypeID' : usertypeID, 'userID' : userID },
                    dataType: "html",
                    success: function(data) {
                        if(usertypeID == 3) {
                            $('#divwhatsapp_class').show();
                            $('#divwhatsapp_section').show();
                            $('#whatsapp_class').html(data);

                            $('#divwhatsapp_schoolyear').show();

                            $('#whatsapp_users').html('<?=$useroption?>');
                            $('#whatsapp_users').val('select').trigger('change');
                        } else {
                            $('#divwhatsapp_schoolyear').hide();
                            $('#divwhatsapp_class').hide();
                            $('#divwhatsapp_section').hide();
                            $('#whatsapp_users').html(data);
                            $('#whatsapp_users').val('select').trigger('change');
                        }
                    }
                });
            } else {
                $('#whatsapp_users').html('<?=$useroption?>');
            }
        });

        $('#whatsapp_schoolyear').change(function() {
            var schoolyear = $(this).val();
            if(schoolyear != 'select') {
                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allusers')?>",
                    data: "usertypeID=" + 3,
                    dataType: "html",
                    success: function(data) {
                       $('#whatsapp_class').html(data);
                    }
                });
            } else {
                $('#whatsapp_class').html('<?=$classoption?>');
                $('#whatsapp_users').html('<?=$useroption?>');
            } 
        });

        $('#whatsapp_class').change(function() {
            var schoolyear = $('#whatsapp_schoolyear').val();
            var classes = $(this).val();
            if(classes != 'select') {

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allsection')?>",
                    data: "classes=" + classes,
                    dataType: "html",
                    success: function(data) {
                        $('#whatsapp_section').html(data)
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allstudent')?>",
                    data: "schoolyear=" + schoolyear + "&classes=" + classes + "&section=",
                    dataType: "html",
                    success: function(data) {
                        $('#whatsapp_users').html(data);
                    }
                });
            } else {
                $('#whatsapp_users').html('<?=$useroption?>');
            }
        });

        $('#whatsapp_section').change(function() {
            var schoolyear = $('#whatsapp_schoolyear').val();
            var section = $(this).val();
            var classes = $('#whatsapp_class').val();
            if(section != 'select') {

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allstudent')?>",
                    data: "schoolyear=" + schoolyear + "&classes=" + classes + "&section=" + section,
                    dataType: "html",
                    success: function(data) {
                        console.log(data);
                        $('#whatsapp_users').html(data);
                    }
                });
            } else {
                $('#whatsapp_users').html('<?=$useroption?>');
            }
        });

        $('#whatsapp_template').change(function() {
            var templateID = $(this).val();
                $.ajax({
                type: 'POST',
                url: "<?=base_url('mailandsms/alltemplatedesign')?>",
                data: "templateID=" + templateID,
                dataType: "html",
                success: function(data) {
                   $('#whatsapp_message').html(data);
                }
            });

        }); 

        // whatsapp end




        //For Voice Call
        $("#voice_usertypeID").change(function() {
            var usertypeID = $(this).val();
            var userID = "<?=$setEmailUserID?>";
            if(usertypeID != 'select') {
                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allusers')?>",
                    data: {'usertypeID' : usertypeID, 'userID' : userID },
                    dataType: "html",
                    success: function(data) {
                        if(usertypeID == 3) {
                            $('#divvoice_class').show();
                            $('#voice_class').html(data);
                            $('#divvoice_schoolyear').show();
                        } else {
                            $('#divvoice_class').hide();
                            $('#divvoice_schoolyear').hide();
                        }
                    }
                });
            }
        });

        //for getting phone numbers

        $("#whatsapp_users").change(function() {
            // var userID = $(this).val();
            var userID = "<?=$setEmailUserID?>";
            
                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('mailandsms/allusers')?>",
                    data: {'usertypeID' : usertypeID, 'userID' : userID },
                    dataType: "html",
                    success: function(data) {
                        if(usertypeID == 3) {
                            $('#divvoice_class').show();
                            $('#voice_class').html(data);
                            $('#divvoice_schoolyear').show();
                        } else {
                            $('#divvoice_class').hide();
                            $('#divvoice_schoolyear').hide();
                        }
                    }
                });
           
        });

    });
    
function uploadFile() {
    var filename = $('#csv_file').val();
    var realname = filename.substr((filename.lastIndexOf('\\') + 1));
    var data = new FormData($('#voice_form')[0]);
    data.append("realname", realname);
    $('.upload_btn').addClass('disabled');
    $.ajax({
        url: '<?php echo base_url();?>mailandsms/upload_voice',
        type: 'POST',
        data: data,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        async: true,
        success: function ($resp) {
            alert($resp.succes);
            if ($resp.success && $resp.data) {
                
                $(".voice_file").append("<option value=\"" + $resp.data.voice_file + "\">" + $resp.data.title +"</option>");
                $(".voice_file").val($resp.data.voice_file);
                alert('File Successfully uploaded.');
            } else {
                alert($resp.msg);
            }
           $('.upload_btn').removeClass('disabled');
        },
        error: function () {
            console.log('Server Error, Please try again');
            $('.upload_btn').removeClass('disabled');
        },
        complete: function () {

        }
    });
}

function uploadFile1() {
    var filename = $('#csv_file1').val();
    var realname = filename.substr((filename.lastIndexOf('\\') + 1));
    var data = new FormData($('#whatsapp_form')[0]);
    data.append("realname", realname);
    $('.upload_btn1').addClass('disabled');
    $.ajax({
        url: '<?php echo base_url();?>mailandsms/upload_file',
        type: 'POST',
        data: data,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        async: true,
        success: function ($resp) {
            // console.log($resp);
            
            if ($resp.success) {                
                $("#dynamic_file1_path").val($resp.new_file_path);
                alert('File Successfully uploaded.');
            } else {
                alert($resp.msg);
            }
           $('.upload_btn').removeClass('disabled');
        },
        error: function () {
            console.log('Server Error, Please try again');
            $('.upload_btn').removeClass('disabled');
        },
        complete: function () {

        }
    });
}

$(document).on("click","#generate",function(){
    var text = $("#manual_template").text()
    var a = text.replace('Preview',"");
    a.trim();
    $("#sms_message").val(a);
    // $("#sms_message").attr('disabled',true);

})


$(document).on("keydown",".common_input",function(e){
var l =$(this).text().length;
    if(l > 29){
         e.preventDefault();
    }
});

</script>
