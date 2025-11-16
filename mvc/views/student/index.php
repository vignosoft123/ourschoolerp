<?php 
// echo "<pre>";print_r($this->session->userdata('usertypeID'));die;
$global_payment_permission = false;
if($this->session->userdata('usertypeID') == 1 || $this->session->userdata('usertypeID') == 5){ //admin & accountant
    $global_payment_permission = true;
}
?>
<style>
    label { 
    color: #ffff;
    }
    </style>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-student"></i> <?= $this->lang->line('menu_student') ?></h3>


        <ol class="breadcrumb">
            <li><a href="<?= base_url("dashboard/index") ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
            <li class="active"><?= $this->lang->line('menu_student') ?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
            <div class="col-sm-12">

                <?php if ((($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) || ($this->session->userdata('usertypeID') != 3)) { ?>
                    <div class="page-header"> 

                        
                        <?php if (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) { ?>
                            <?php if (permissionChecker('student_add')) { ?>
                                <a class="ose-btn create-btn" href="<?php echo base_url('student/add') ?>">
                                    <i class="fa fa-plus"></i>
                                    <?= $this->lang->line('add_title') ?>
                                </a>
                                <button type="button" class="ose-btn create-btn btn btn-primary" data-toggle="modal" data-target="#quickStudentModal" style="margin-left:8px;">
                                    <i class="fa fa-plus"></i> Quick Student Creation
                                </button>

                                <?php if (permissionChecker('student_delete') && customCompute($students) > 0) { ?>
                                    <form id="multiDeleteForm" method="post" action="<?= base_url('student/multi_delete') ?>" style="display:inline-block; margin-left:8px;">
                                        <input type="hidden" name="ids" id="multi_delete_ids" value="" />
                                        <input type="hidden" name="url" value="<?= isset($set) ? $set : '' ?>" />
                                        <button type="button" id="bulkDeleteBtn" class="ose-btn create-btn btn btn-danger" onclick="confirmMultiDelete()" style="background-color:#d9534f !important;border-color:#d43f3a !important;color:#ffffff !important;">
                                            <i class="fa fa-trash"></i> <?= 'Delete Selected' ?>
                                        </button>
                                    </form>
                                <?php } ?>

                                <span class="pull-right" style="display:inline-block; margin-left:auto;">
                                <?php if ($this->session->userdata('usertypeID') != 3) { ?>
                                    <?php
                                    $array = array("0" => $this->lang->line("student_select_class"));
                                    if (customCompute($classes)) {
                                        foreach ($classes as $classa) {
                                            $array[$classa->classesID] = $classa->classes;
                                        }
                                    }
                                    echo form_dropdown("classesID", $array, set_value("classesID", $set), "id='classesID' class='form-control select2' style='display:inline-block; width:260px;'");
                                    ?>
                                <?php } ?>
                                </span>

                        </div>  
                            <?php } ?> 

                        <?php } ?>






                     















                        <?php if ($this->session->userdata('usertypeID') != 3) { ?>
                            <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12 pull-right" style="display:none;">
                                <?php
                                $array = array("0" => $this->lang->line("student_select_class"));
                                if (customCompute($classes)) {
                                    foreach ($classes as $classa) {
                                        $array[$classa->classesID] = $classa->classes;
                                    }
                                }
                                echo form_dropdown("classesID", $array, set_value("classesID", $set), "id='classesID' class='form-control select2'");
                                ?>
                            </div>
                        <?php } ?>
                            </div>
                <?php } ?>


                <?php if (customCompute($students) > 0) { ?>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#all" aria-expanded="true"><?= $this->lang->line("student_all_students") ?></a></li>
                            <?php foreach ($sections as $key => $section) {
                                echo '<li class=""><a data-toggle="tab" href="#tab' . $section->classesID . $section->sectionID . '" aria-expanded="false">' . $this->lang->line("student_section") . " " . $section->section . " ( " . $section->category . " )" . '</a></li>';
                            } ?>
                        </ul>



                        <div class="tab-content">
                            <div id="all" class="tab-pane active">
                                <div id="hide-table" class="responsive">
                                   
                                    <table id="example1" class="table table-bordered tableBorder dataTable no-footer">
                                    <h3 class='err' style="margin-left:25%;color:green;"> </h3>
                                        <thead>
                                            <tr>
                                                <th style="width:30px; text-align:center"><input type="checkbox" id="select_all_students" /></th>
                                                <th class="col-sm-1"><?= $this->lang->line('slno') ?></th>
                                                <th class="col-sm-1"><?= $this->lang->line('student_photo') ?></th>
                                                <th class="col-sm-1"><?= $this->lang->line('student_registerNO') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_name') ?></th>
                                                <th class="col-sm-1"><?= $this->lang->line('student_roll') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_phone') ?></th>
                                                <th>WhatsApp</th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_village') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('studentType') ?></th>
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>RFID</th>
                                                <th>Invoice</th>
                                                <?php if (permissionChecker('student_edit')) { ?>
                                                    <th class="col-sm-1"><?= $this->lang->line('student_status') ?></th>
                                                <?php } ?>
                                                <?php if (permissionChecker('student_edit') || permissionChecker('student_delete') || permissionChecker('student_view')) { ?>
                                                    <th class="col-sm-2"><?= $this->lang->line('action') ?></th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (customCompute($students)) {
                                                $i = 1;
                                                // echo "<pre>";print_r($students);die;
                                                foreach ($students as $student) { ?>
                                                    <tr>
                                                        <td style="text-align:center"><input type="checkbox" class="student-checkbox" value="<?= $student->srstudentID ?>" /></td>
                                                        <td data-title="<?= $this->lang->line('slno') ?>">
                                                            <?php echo $i; ?>
                                                        </td>
                                                        <td class="student-photo-cell" onclick="getStudentID(<?= $student->srstudentID ?>);" data-title="<?= $this->lang->line('student_photo') ?>"  data-toggle="modal" data-target="#fileUploadModal">
                                                            <?= profileimage($student->photo); ?>
                                                            <span class="photo-zoom-icon" data-img="<?= base_url('uploads/images/') . ($student->photo ? $student->photo : 'default.png') ?>" title="Preview">
                                                                <i class="fa fa-search-plus" aria-hidden="true"></i>
                                                            </span>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_registerNO') ?>">
                                                            <?php echo $student->srregisterNO; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_name') ?>">
                                                            <?php echo $student->srname; ?>
                                                        </td>
                                                        <td id="rollNo" studentID="<?= $student->srstudentID ?>" classId="<?= $student->srclassesID ?>" sectionId="<?= $student->srsectionID ?>"   style="color:green;border:2px solid gray;" contenteditable="true" data-title="<?= $this->lang->line('student_roll') ?>"><?php echo $student->srroll; ?></td>
                                                        
                                                        <td style="color:green;border:2px solid gray;" contenteditable="true"  id="phone_update" class="phone_update"  parentID='<?php echo $student->parentID; ?>'   studentID="<?= $student->srstudentID ?>" data-title="<?= $this->lang->line('student_phone') ?>"><?php echo $student->phone; ?></td>
                                                        <td>
                                                            <?php $waPhone = preg_replace('/\D+/', '', (string)$student->phone); ?>
                                                            <a href="tel:<?= $waPhone ?>"><?= $waPhone ?></a>
                                                        </td>
                                                        
                                                        <td data-title="<?= $this->lang->line('student_village') ?>">
                                                            <?php echo $student->village_name; ?>
                                                        </td>
                                                        <?php 
                                                            $studentType = array('' => 'Select Student Type', 1 => "TRANSPORT", 2 => "HOSTEL" , 3 => "DAY SCHOLAR", 0 =>'')
                                                        ?>
                                                        <td data-title="<?= $this->lang->line('studentType') ?>">
                                                            <?php echo  ($studentType[$student->studentType]) ?  $studentType[$student->studentType] : 'DAY SCHOLAR'; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_class') ?>">
                                                            <?php echo $student->srclasses; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_section') ?>">
                                                            <?php echo $student->srsection; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_village') ?>">
                                                            <?php echo $student->rf_id; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_village') ?>">
                                                            <a href="<?php echo base_url('student/view/'). $student->srstudentID . '/' . $set.'/inv'?>"> invoice </a>
                                                             

                                                        </td>

                                                        <?php if (permissionChecker('student_edit')) { ?>
                                                            <td data-title="<?= $this->lang->line('student_status') ?>">
                                                                <div class="onoffswitch-small" id="<?= $student->srstudentID ?>">
                                                                    <input type="checkbox" id="myonoffswitch<?= $student->srstudentID ?>" class="onoffswitch-small-checkbox" name="paypal_demo" <?php if ($student->active === '1') echo "checked='checked'"; ?>>
                                                                    <label for="myonoffswitch<?= $student->srstudentID ?>" class="onoffswitch-small-label">
                                                                        <span class="onoffswitch-small-inner"></span>
                                                                        <span class="onoffswitch-small-switch"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        <?php } ?>
                                                        <?php if (permissionChecker('student_edit') || permissionChecker('student_delete') || permissionChecker('student_view')) { 
                                                            if(!empty($set)){$set = $set;}else{
                                                                $set = $student->srclassesID;
                                                            }
                                                            ?>
                                                            <td class="action-btns" data-title="<?= $this->lang->line('action') ?>">
                                                                <?php
                                                                echo btn_view('student/view/' . $student->srstudentID . "/" . $set, $this->lang->line('view'));
                                                                if (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                                                                    echo btn_edit('student/edit/' . $student->srstudentID . "/" . $set, $this->lang->line('edit'));
                                                                        echo btn_delete('student/delete/' . $student->srstudentID . "/" . $set, $this->lang->line('delete'));
                                                                        
                                                                        
                                                                        if( $global_payment_permission){
                                                                        ?>

                                                                         <a href="<?php echo base_url('Global_payment/index/').$student->classesID.'/'.$student->srstudentID;?>"  class="btn btn-primary btn-xs mrg  " data-placement="top" data-toggle="tooltip" data-original-title="Global invoice"><i class="fa fa-balance-scale"></i></a> 
                                                              <?php   }}  ?>

                                                               


                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                            <?php $i++;
                                                }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                            <?php foreach ($sections as $key => $section) { ?>
                                <div id="tab<?= $section->classesID . $section->sectionID ?>" class="tab-pane">
                                    <div id="hide-table">
                                        <table id="example1" class="table table-bordered   tableBorder dataTable no-footer" style="width:100%">
                                        
                                            <thead>
                                                <tr>
                                                    <th style="width:30px; text-align:center"><input type="checkbox" class="select_all_section" /></th>
                                                    <th class="col-sm-1"><?= $this->lang->line('slno') ?></th>
                                                    <th class="col-sm-1"><?= $this->lang->line('student_photo') ?></th>
                                                    <th class="col-sm-1"><?= $this->lang->line('student_registerNO') ?></th>
                                                    <th class="col-sm-2"><?= $this->lang->line('student_name') ?></th>
                                                    <th class="col-sm-1"><?= $this->lang->line('student_roll') ?></th>
                                                    <th class="col-sm-2"><?= $this->lang->line('student_phone') ?></th>
                                                    <th>WhatsApp</th>
                                                    <th class="col-sm-2"><?= $this->lang->line('student_village') ?></th>
                                                    <th class="col-sm-2"><?= $this->lang->line('studentType') ?></th>

                                                     <th>Class</th>
                                                    <th>Section</th>
                                                    <th>RFID</th>
                                                    <th>Invoice</th>
                                                    <?php if (permissionChecker('student_edit')) { ?>
                                                        <th class="col-sm-1"><?= $this->lang->line('student_status') ?></th>
                                                    <?php } ?>
                                                    <?php if (permissionChecker('student_edit') || permissionChecker('student_delete') || permissionChecker('student_view')) { ?>
                                                        <th class="col-sm-2"><?= $this->lang->line('action') ?></th>
                                                    <?php } ?>
                                                    </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (customCompute($allsection[$section->sectionID])) {
                                                    $i = 1;
                                                    foreach ($allsection[$section->sectionID] as $student) {
                                                        if ($section->sectionID === $student->srsectionID) { ?>
                                                            <tr>
                                                            <td style="text-align:center"><input type="checkbox" class="student-checkbox" value="<?= $student->srstudentID ?>" /></td>
                                                            <td data-title="<?= $this->lang->line('slno') ?>">
                                                            <?php echo $i; ?>
                                                        </td>
                                                       

                                                        <td class="student-photo-cell" onclick="getStudentID(<?= $student->srstudentID ?>);" data-title="<?= $this->lang->line('student_photo') ?>"  data-toggle="modal" data-target="#fileUploadModal">
                                                            <?= profileimage($student->photo); ?>
                                                            <span class="photo-zoom-icon" data-img="<?= base_url('uploads/images/') . ($student->photo ? $student->photo : 'default.png') ?>" title="Preview">
                                                                <i class="fa fa-search-plus" aria-hidden="true"></i>
                                                            </span>
                                                        </td>

                                                        <td data-title="<?= $this->lang->line('student_registerNO') ?>">
                                                            <?php echo $student->srregisterNO; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_name') ?>">
                                                            <?php echo $student->srname; ?>
                                                        </td>
                                                        <td id="rollNo" studentID="<?= $student->srstudentID ?>" classId="<?= $student->srclassesID ?>" sectionId="<?= $student->srsectionID ?>"   style="color:green;border:2px solid gray;" contenteditable="true" data-title="<?= $this->lang->line('student_roll') ?>"><?php echo $student->srroll; ?></td>
                                                        
                                                        <td style="color:green;border:2px solid gray;" contenteditable="true"  id="phone_update" studentID="<?= $student->srstudentID ?>" parentID='<?php echo $student->parentID; ?>' data-title="<?= $this->lang->line('student_phone') ?>"><?php echo $student->phone; ?></td>
                                                        <td>
                                                            <?php $waPhone = preg_replace('/\D+/', '', (string)$student->phone); ?>
                                                            <a href="tel:<?= $waPhone ?>"><?= $waPhone ?></a>
                                                        </td>
                                                        
                                                        <td data-title="<?= $this->lang->line('student_village') ?>">
                                                            <?php echo $student->village_name; ?>
                                                        </td>
                                                        <?php 
                                                            $studentType = array('' => 'Select Student Type', 1 => "TRANSPORT", 2 => "HOSTEL" , 3 => "DAY SCHOLAR", 0 =>'')
                                                        ?>
                                                        <td data-title="<?= $this->lang->line('studentType') ?>">
                                                            <?php echo  ($studentType[$student->studentType]) ?  $studentType[$student->studentType] : 'DAY SCHOLAR'; ?>
                                                        </td>
                                                             <td data-title="<?= $this->lang->line('student_class') ?>">
                                                            <?php echo $student->srclasses; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_section') ?>">
                                                            <?php echo $student->srsection; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_village') ?>">
                                                            <?php echo $student->rf_id; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_village') ?>">
                                                            <a href="<?php echo base_url('student/view/'). $student->srstudentID . '/' . $set.'/inv'?>"> invoice </a>
                                                             

                                                        </td>

                                                        <?php if (permissionChecker('student_edit')) { ?>
                                                            <td data-title="<?= $this->lang->line('student_status') ?>">
                                                                <div class="onoffswitch-small" id="<?= $student->srstudentID ?>">
                                                                    <input type="checkbox" id="myonoffswitch<?= $student->srstudentID ?>" class="onoffswitch-small-checkbox" name="paypal_demo" <?php if ($student->active === '1') echo "checked='checked'"; ?>>
                                                                    <label for="myonoffswitch<?= $student->srstudentID ?>" class="onoffswitch-small-label">
                                                                        <span class="onoffswitch-small-inner"></span>
                                                                        <span class="onoffswitch-small-switch"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        <?php } ?>
                                                        <?php if (permissionChecker('student_edit') || permissionChecker('student_delete') || permissionChecker('student_view')) { 
                                                            if(!empty($set)){$set = $set;}else{
                                                                $set = $student->srclassesID;
                                                            }
                                                            ?>
                                                            <td class="action-btns" data-title="<?= $this->lang->line('action') ?>">
                                                                <?php
                                                                echo btn_view('student/view/' . $student->srstudentID . "/" . $set, $this->lang->line('view'));
                                                                if (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                                                                    echo btn_edit('student/edit/' . $student->srstudentID . "/" . $set, $this->lang->line('edit'));
                                                                        echo btn_delete('student/delete/' . $student->srstudentID . "/" . $set, $this->lang->line('delete'));
                                                                        if( $global_payment_permission){
                                                                        
                                                                        ?>

                                                                        <a href="<?php echo base_url('Global_payment/index/').$student->classesID.'/'.$student->srstudentID;?>"  class="btn btn-primary btn-xs mrg  " data-placement="top" data-toggle="tooltip" data-original-title="Global invoice"><i class="fa fa-balance-scale"></i></a> 
                                                                        
                                                              <?php  } 
                                                              }?>

                                                               

                                                                


                                                            </td>
                                                        <?php } ?>
                                                            </tr>


                                                <?php $i++;
                                                        }
                                                    }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div> <!-- nav-tabs-custom -->
                <?php } else { ?>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#all" aria-expanded="true"><?= $this->lang->line("student_all_students") ?></a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="all" class="tab-pane active">
                                <div id="hide-table">
                                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                                        <thead>
                                            <tr>
                                            <th class="col-sm-1"><?= $this->lang->line('slno') ?></th>
                                                <th class="col-sm-1"><?= $this->lang->line('student_photo') ?></th>
                                                <th class="col-sm-1"><?= $this->lang->line('student_registerNO') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_name') ?></th>
                                                <th class="col-sm-1"><?= $this->lang->line('student_roll') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_phone') ?></th>
                                                <th>WhatsApp</th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_village') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('studentType') ?></th>
                                                 <th>Class</th>
                                                <th>Section</th>
                                                <th>RFID</th>
                                                <th>Invoice</th>
                                                <?php if (permissionChecker('student_edit')) { ?>
                                                    <th class="col-sm-1"><?= $this->lang->line('student_status') ?></th>
                                                <?php } ?>
                                                <?php if (permissionChecker('student_edit') || permissionChecker('student_delete') || permissionChecker('student_view')) { ?>
                                                    <th class="col-sm-2"><?= $this->lang->line('action') ?></th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- nav-tabs-custom -->
                <?php } ?>
            </div> <!-- col-sm-12 -->
        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->



<!-- queck student popup start -->
                         <div class="modal fade" id="quickStudentModal" tabindex="-1" role="dialog" aria-labelledby="quickStudentModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <form id="quickStudentForm" method="post" action="<?= base_url('student/add') ?>">
                                <div class="modal-content">

                                    <!-- Header -->
                                    <div class="modal-header bg-gradient-primary ">
                                    <h5 class="modal-title" id="quickStudentModalLabel">
                                        <i class="fa fa-user-plus"></i> Quick Student Creation
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>

                                    <!-- Body -->
                                    <div class="modal-body" style="background-color: #4d93fb;">
                                    <div class="container-fluid">

                                        <!-- Basic Info -->
                                        <fieldset class="border p-3 mb-4 bg-white shadow-sm rounded">
                                        <legend class="w-auto text-primary font-weight-bold">Basic Information</legend>
                                        <div class="row">
                                            
                                            <div class="col-md-3 form-group">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" id="first_name" name="first_name" class="form-control id_card" placeholder="First Name" required>
                                            </div>
                                            <div class="col-md-3 form-group">
                                            <label>Last Name</label>
                                            <input type="text" id="last_name" name="last_name" class="form-control id_card" placeholder="Last Name">
                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label>ID Card Name</label>
                                            <input type="text" id="name_id" name="name" class="form-control" placeholder="Name on ID Card">
                                            </div>

                                            <div class="col-md-3 form-group">
                                            <label>Phone <span class="text-danger">*</span></label>
                                            <input type="text" name="phone" class="form-control" required>
                                            </div>
                                        
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                            <label>Class <span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="classesID" id="classesID_popup" required>
                                                <option value="">Select Class</option>
                                                <?php foreach ($classes as $class): ?>
                                                <option value="<?= $class->classesID ?>"><?= $class->classes ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            </div>

                                            <div class="col-md-3 form-group">
                                            <label>Section <span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="sectionID" id="sectionID" required>
                                                <option value="">Select Section</option>
                                            </select>
                                            </div>

                                            <div class="col-md-3 form-group">
                                            <label>Roll No <span class="text-danger">*</span></label>
                                            <input type="text" id="roll" name="roll" class="form-control" placeholder="Roll No" required>
                                            </div>

                                            <div class="col-md-3 form-group">
                                            <label>Admission No <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="registerNO" name="registerNO" value="<?= set_value('registerNO', $randomAdmissionCode) ?>" <?= $randomAdmissionCode ? 'readonly' : '';?> >
                                            </div>

                                            

                                        </div>

                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                            <label>Date of Birth</label> 
                                            <input type="text" class="form-control" id="dob" name="dob" value="<?= set_value('dob') ?>" required>
                                            </div>
                                            <div class="col-md-3 form-group">
                                            <label>Student Type <span class="text-danger">*</span></label>
                                            <select name="studentType" id="studentType" class="form-control" required>
                                                <option value="3">Day Scholar</option>
                                                <option value="1">Transport</option>
                                                <option value="2">Hostel</option>
                                            </select>
                                            </div>
                                            <div class="col-md-3 form-group">
                                            <label>Village Name</label>
                                            
                                            <select id="village_name" name="village_name" class='form-control select2' >
                                                    <?php foreach($villages as $v){?>
                                                        <option value="<?= $v['villageID']?>"> <?= $v['villageName']?> </option>
                                                <?php  }?>
                                                </select>

                                            </div>
                                            <div class="col-md-3 form-group">
                                            <label>Father Name</label>
                                            <input type="text" name="father_name" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            
                                            <div class="col-md-3 form-group">
                                            <label>Student Type <span class="text-danger">*</span></label>
                                            <select name="sex" id="sex" class="form-control" required>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option> 
                                            </select>
                                            </div>

                                            <div class="col-md-3 form-group">
                                            <label>Refered By <span class="text-danger">*</span></label>
                                            <select id="refered_by" name="refered_by" class='form-control select2' >
                                                <option value=""> --Select-- </option>

                                                    <?php foreach($teachers as $k=>$v){?>
                                                        <option value="<?= $k?>"> <?= $v?> </option>
                                                <?php  }?>
                                                </select>
                                            </div>

                                            </div>

                                        </fieldset>

                                        <!-- Transport -->
                                        <div id="transport_div" class="border p-3 mb-4 bg-white shadow-sm rounded" style="display: none;">
                                        <legend class="w-auto text-success font-weight-bold">Transport Details</legend>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                            <label>Transport Route</label>
                                            <select name="transportID" id="transportID" class="form-control clear-dropdown">
                                                <option value="">Select Route</option>
                                                <?php foreach ($transports as $transport): ?>
                                                <option value="<?= $transport->transportID ?>"><?= $transport->route ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            </div> 



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
                                            </div>



                                        </div>
                                        </div>

                                        <!-- Hostel -->
                                        <div id="hostel_div" class="border p-3 mb-4 bg-white shadow-sm rounded" style="display: none;">
                                        <legend class="w-auto text-warning font-weight-bold">Hostel Details</legend>
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                            <label>Hostel Name</label>
                                            <select name="hostelID" id='hostelID' class="form-control clear-dropdown">
                                                <option value="">Select Hostel</option>
                                                <?php foreach ($hostels as $hostel): ?>
                                                <option value="<?= $hostel->hostelID ?>"><?= $hostel->name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            </div>
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
                                                    echo form_dropdown("categoryID", $array, set_value("categoryID"), "id='categoryID' class='clear-dropdown form-control select2'");
                                                ?>
                                            </div>
                                            <span class="control-label <?= $this->input->post('studentType')=='2' ? '' : 'show'; ?> hostel" >
                                                <?php echo form_error('categoryID'); ?>
                                            </span>
                                        </div>
                                        </div>
                                        </div>

                                    </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="modal-footer bg-white">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i> Save Student
                                    </button>
                                    </div>
                                </div>
                                </form>
                          </div>
                         
                        <!-- queck student popup end -->

                        

<!-- photo  Modal  start Structure -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />
<div class="modal fade" id="fileUploadModal" tabindex="-1" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileUploadModalLabel">Upload Photo</h5>
                <button style="margin-left: 98% !important;" type="button" class="btn-close" data-dismiss="modal" aria-label="Close"> X </button>
            </div>
            <div class="modal-body">
                <!-- Form for File Upload -->
                <form id="fileUploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Choose a file to upload</label>
                        <input class="form-control" type="file" id="formFile" name="file" accept="image/*">
                        <input class="form-control" type="hidden" id="student_id" name="studentID" value="">
                    </div>
                    <div class="mb-3" id="imagePreviewContainer" style="display:none; text-align:center;">
                        <img id="imagePreview" style="max-width:100%; max-height:300px;" />
                    </div>
                    <div class="mb-3" id="cropBtnContainer" style="display:none; text-align:center;">
                        <button type="button" class="btn btn-secondary" id="cropImageBtn">Crop & Compress</button>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
let cropper;
let croppedBlob;
let compressedBlob = null; // holds auto-compressed blob when original > limit
const MAX_FILE_SIZE_MB = 0.05; // 0.05MB (50KB) limit
const MAX_WIDTH = 400; // px
const MAX_HEIGHT = 400; // px

/**
 * Compress image file to target max bytes by resizing and reducing JPEG quality.
 * Returns a Promise<Blob>.
 */
function compressImage(file, maxBytes) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const reader = new FileReader();
        reader.onload = function(e) {
            img.onload = function() {
                // compute scale to fit MAX_WIDTH / MAX_HEIGHT
                let width = img.width;
                let height = img.height;
                const maxW = MAX_WIDTH;
                const maxH = MAX_HEIGHT;
                if (width > maxW || height > maxH) {
                    const ratio = Math.min(maxW / width, maxH / height);
                    width = Math.round(width * ratio);
                    height = Math.round(height * ratio);
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // try decreasing quality until size under limit or quality too low
                (function tryCompress(quality) {
                    canvas.toBlob(function(blob) {
                        if (!blob) {
                            reject(new Error('Compression failed')); return;
                        }
                        if (blob.size <= maxBytes || quality <= 0.1) {
                            resolve(blob);
                        } else {
                            // reduce quality and try again
                            tryCompress(quality - 0.1);
                        }
                    }, 'image/jpeg', quality);
                })(0.9);
            };
            img.onerror = function() { reject(new Error('Image load error')); };
            img.src = e.target.result;
        };
        reader.onerror = function() { reject(new Error('File read error')); };
        reader.readAsDataURL(file);
    });
}

document.getElementById('formFile').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) {
        // not an image, clear input
        event.target.value = '';
        return;
    }

    const maxBytes = Math.round(MAX_FILE_SIZE_MB * 1024 * 1024);
    const proceedWithDataURL = function(dataURL) {
        const image = document.getElementById('imagePreview');
        image.src = dataURL;
        document.getElementById('imagePreviewContainer').style.display = 'block';
        document.getElementById('cropBtnContainer').style.display = 'block';
        if (cropper) cropper.destroy();
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1,
        });
    };

    if (file.size > maxBytes) {
        // auto-compress, don't alert the user
        compressImage(file, maxBytes).then(function(blob) {
            compressedBlob = blob;
            // show preview of compressed blob
            const url = URL.createObjectURL(blob);
            proceedWithDataURL(url);
        }).catch(function(err) {
            // fallback to original file preview if compression fails
            const reader = new FileReader();
            reader.onload = function(e) { proceedWithDataURL(e.target.result); };
            reader.readAsDataURL(file);
        });
    } else {
        // keep original, clear any previous compressed blob
        compressedBlob = null;
        const reader = new FileReader();
        reader.onload = function(e) { proceedWithDataURL(e.target.result); };
        reader.readAsDataURL(file);
    }
});

document.getElementById('cropImageBtn').addEventListener('click', function() {
    if (!cropper) return;
    const canvas = cropper.getCroppedCanvas({
        width: MAX_WIDTH,
        height: MAX_HEIGHT,
        imageSmoothingQuality: 'high',
    });
    canvas.toBlob(function(blob) {
        croppedBlob = blob;
        // Show preview of cropped image
        document.getElementById('imagePreview').src = URL.createObjectURL(blob);
        alert('Image cropped and compressed. Now click Submit to upload.');
    }, 'image/jpeg', 0.7); // 70% quality
});

document.getElementById('fileUploadForm').addEventListener('submit', function(event) {
    event.preventDefault();
    var formData = new FormData();
    var studentID = document.getElementById('student_id').value;
    formData.append('studentID', studentID);
    if (croppedBlob) {
        formData.append('file', croppedBlob, 'cropped.jpg');
    } else if (compressedBlob) {
        // use auto-compressed blob when original was too large
        formData.append('file', compressedBlob, 'compressed.jpg');
    } else {
        // fallback: use original file if not cropped or compressed
        const fileInput = document.getElementById('formFile');
        if (!fileInput.files[0]) {
            alert('Please select an image.');
            return;
        }
        formData.append('file', fileInput.files[0]);
    }
    fetch('<?php echo base_url('Student/uploadPhoto')?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        alert('Photo uploaded successfully!');
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>
<!-- photo upload modal end -->

<!-- Photo zoom modal -->
<div class="modal fade" id="photoZoomModal" tabindex="-1" role="dialog" aria-labelledby="photoZoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center" style="padding:10px;">
                <img id="photoZoomImg" src="" style="max-width:100%; max-height:80vh; border:1px solid #ddd;" />
            </div>
        </div>
    </div>
</div>

<style>
    .photo-zoom-icon { margin-left:6px; color:#337ab7; cursor:pointer; display:inline-block; vertical-align:middle; }
    .photo-zoom-icon i { font-size:14px; }
    .student-photo-cell img { vertical-align:middle; }
</style>

<script>
// Show preview when user clicks the zoom icon (click opens modal)
$(document).on('click', '.photo-zoom-icon', function(e){
    // stop propagation so the upload modal (parent cell) doesn't open
    e.stopPropagation();
    var imgUrl = $(this).attr('data-img');
    if (!imgUrl) return;
    $('#photoZoomImg').attr('src', imgUrl);
    $('#photoZoomModal').modal('show');
});
</script>





<script type="text/javascript">
    $(".select2").select2();

    $('.global_invoice').click(function() {
        var classesID = $(this).attr("classId");
        var studentId = $(this).attr("studentId");
            $.ajax({
                type: 'POST',
                url: "<?= base_url('Global_payment/index') ?>",
                data: {"classesID" : classesID, "studentID": studentId},
                dataType: "html",
                success: function(data) {
                  //  window.location.href = data;
                }
            }); 
    });


    $('#classesID').change(function() {
        var classesID = $(this).val();
        if (classesID == 0) {
            $('#hide-table').hide();
            $('.nav-tabs-custom').hide();
        } else {
            $.ajax({
                type: 'POST',
                url: "<?= base_url('student/student_list') ?>",
                data: "id=" + classesID,
                dataType: "html",
                success: function(data) {
                    window.location.href = data;
                }
            });
        }
    });


    var status = '';
    var id = 0;
    $('.onoffswitch-small-checkbox').click(function() {
        if ($(this).prop('checked')) {
            status = 'chacked';
            id = $(this).parent().attr("id");
        } else {
            status = 'unchacked';
            id = $(this).parent().attr("id");
        }

        if ((status != '' || status != null) && (id != '')) {
            $.ajax({
                type: 'POST',
                url: "<?= base_url('student/active') ?>",
                data: "id=" + id + "&status=" + status,
                dataType: "html",
                success: function(data) {
                    if (data == 'Success') {
                        toastr["success"]("Success")
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
                    } else {
                        toastr["error"]("Error")
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
                }
            });
        }
    });

$(document).on("focusout","#rollNo",function(){
    var classesID = $(this).attr('classId');
    var sectionID = $(this).attr('sectionId');
    var studentID = $(this).attr('studentID');
    var rollNo = $(this).text(); 
    $.ajax({
            type: 'POST',
            url: "<?=base_url('student/checkRoll_update')?>",
            data: {"classesID":classesID,"sectionID":sectionID,"rollNo":rollNo,"studentID":studentID},
            dataType: "html",
            success: function(data) {
              $('.err').html(data);
            // alert(data);
            }
        });
})


$(document).on("focusout",".phone_update",function(){
  
    var studentID = $(this).attr('studentID');
    var phone = $(this).text(); 

    const editableDiv = document.getElementById('phone_update');
    const parentID = $(this).attr('parentID');
  
    // Place the caret at the end
    // placeCaretAtEnd($(this)[0]);

    if (/\D/.test(phone)) {
        alert('Phone number should not contain characters'); 
        $(this).text('');
      return false;
    }
   
    if (phone.length > 10 || phone.length < 10) {
        alert('Phone number should be 10 characters');
        $(this).text('');
      phone = phone.substring(0, 10);
      return false;
    }

    $.ajax({
            type: 'POST',
            url: "<?=base_url('student/phone_update')?>",
            data: {"phone":phone,"studentID":studentID,"parentID":parentID},
            dataType: "html",
            success: function(data) {
              $('.err').html(data);
            // alert(data);
            }
        });

 

})


</script>




<script>
function getStudentID(studentID){
    // alert(studentID);
    $("#student_id").val(studentID);
}
// upload submit handler is defined earlier (handles croppedBlob and compressedBlob fallback)
</script>


<script type="text/javascript">
    // $(".select2").select2();
    $('#dob').datepicker({
        startView: 2
    });
    $('#admission_date').datepicker({
        startView: 2
    });

    $('#username').keyup(function() {
        $(this).val($(this).val().replace(/\s/g, ''));
    });


    $('#classesID_popup').change(function(event) {
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
        $(".clear-dropdown").val('');
        $("#categoryID").val('0').change();;
        $("#pickup_id").val('0').change();;


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



$(document).on("focusout","#roll",function(){
    var classesID = $("#classesID_popup").val();
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
 




</script>

<script>
// Bulk select / delete handlers
$(document).on('change', '#select_all_students', function() {
    var checked = $(this).prop('checked');
    $('#example1').find('.student-checkbox').prop('checked', checked);
});

$(document).on('change', '.select_all_section', function() {
    var checked = $(this).prop('checked');
    $(this).closest('table').find('.student-checkbox').prop('checked', checked);
});

function confirmMultiDelete() {
    var ids = [];
    $('.student-checkbox:checked').each(function() {
        ids.push($(this).val());
    });
    if (!ids.length) {
        alert('Please select at least one student to delete.');
        return;
    }
    if (!confirm('Are you sure you want to delete selected student(s)? This action cannot be undone.')) return;

    $('#multi_delete_ids').val(ids.join(','));
    $('#multiDeleteForm').submit();
}
</script>