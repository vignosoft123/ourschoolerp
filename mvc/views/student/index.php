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
        <div class="row">
            <div class="col-sm-12">

                <?php if ((($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) || ($this->session->userdata('usertypeID') != 3)) { ?>
                    <div class="page-header">


                        <!-- <div class="search-sec">
                        <form name="g_search_form" action="<?php echo base_url('Student/global_student_search');?>" method="post">
                                
                                <div class="search">
                                    <input type="text" name="global_search" class="search__input" placeholder="Global student search...">
                                    <button class="search__button">
                                    <i class="fa fa-search"></i>
                                    </button>
                                </div>                                
                            </form>                             
                        </div> -->

                        
                        <?php if (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) { ?>
                            <?php if (permissionChecker('student_add')) { ?>
                                <a class="ose-btn create-btn" href="<?php echo base_url('student/add') ?>">
                                    <i class="fa fa-plus"></i>
                                    <?= $this->lang->line('add_title') ?>
                                </a>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($this->session->userdata('usertypeID') != 3) { ?>
                            <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12 pull-right">
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
                                                <th class="col-sm-1"><?= $this->lang->line('slno') ?></th>
                                                <th class="col-sm-1"><?= $this->lang->line('student_photo') ?></th>
                                                <th class="col-sm-1"><?= $this->lang->line('student_registerNO') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_name') ?></th>
                                                <th class="col-sm-1"><?= $this->lang->line('student_roll') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_phone') ?></th>
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
                                                        <td data-title="<?= $this->lang->line('slno') ?>">
                                                            <?php echo $i; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_photo') ?>">
                                                            <?= profileimage($student->photo); ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_registerNO') ?>">
                                                            <?php echo $student->registerNO; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_name') ?>">
                                                            <?php echo $student->srname; ?>
                                                        </td>
                                                        <td id="rollNo" studentID="<?= $student->srstudentID ?>" classId="<?= $student->srclassesID ?>" sectionId="<?= $student->srsectionID ?>"   style="color:green;border:2px solid gray;" contenteditable="true" data-title="<?= $this->lang->line('student_roll') ?>"><?php echo $student->srroll; ?></td>
                                                        
                                                        <td style="color:green;border:2px solid gray;" contenteditable="true"  id="phone_update" studentID="<?= $student->srstudentID ?>" data-title="<?= $this->lang->line('student_phone') ?>"><?php echo $student->phone; ?></td>
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
                                                                }

                                                                // print_r($student);die;
                                                                ?>

                                                                <a href="<?php echo base_url('Global_payment/index/').$student->classesID.'/'.$student->srstudentID;?>"  class="btn btn-primary btn-xs mrg  " data-placement="top" data-toggle="tooltip" data-original-title="Global invoice"><i class="fa fa-balance-scale"></i></a> 


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
                                        <table class="table table-bordered table-hover dataTable no-footer">
                                            <thead>
                                                <tr>
                                                    <th class="col-sm-1"><?= $this->lang->line('slno') ?></th>
                                                    <th class="col-sm-1"><?= $this->lang->line('student_photo') ?></th>
                                                    <th class="col-sm-2"><?= $this->lang->line('student_name') ?></th>
                                                    <th class="col-sm-2"><?= $this->lang->line('student_roll') ?></th>
                                                    <th class="col-sm-2"><?= $this->lang->line('student_phone') ?></th>
                                                    <th class="col-sm-2"><?= $this->lang->line('student_village') ?></th>
                                                    <?php if (permissionChecker('student_edit') || permissionChecker('student_delete') || permissionChecker('student_view')) { ?>
                                                        <th class="col-sm-3"><?= $this->lang->line('action') ?></th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (customCompute($allsection[$section->sectionID])) {
                                                    $i = 1;
                                                    foreach ($allsection[$section->sectionID] as $student) {
                                                        if ($section->sectionID === $student->srsectionID) { ?>
                                                            <tr>
                                                                <td data-title="<?= $this->lang->line('slno') ?>">
                                                                    <?php echo $i; ?>
                                                                </td>

                                                                <td data-title="<?= $this->lang->line('student_photo') ?>">
                                                                    <?= profileimage($student->photo) ?>
                                                                </td>
                                                                <td data-title="<?= $this->lang->line('student_name') ?>">
                                                                    <?php echo $student->srname; ?>
                                                                </td>
                                                                <td data-title="<?= $this->lang->line('student_roll') ?>">
                                                                    <?php echo $student->srroll; ?>
                                                                </td>
                                                                <td data-title="<?= $this->lang->line('student_phone') ?>">
                                                                    <?php echo $student->phone; ?>
                                                                </td>
                                                                <td data-title="<?= $this->lang->line('student_village') ?>">
                                                                    <?php echo $student->village_name; ?>
                                                                </td>
                                                                <?php if (permissionChecker('student_edit') || permissionChecker('student_delete') || permissionChecker('student_view')) { ?>
                                                                    <td class="action-btns" data-title="<?= $this->lang->line('action') ?>">
                                                                        <?php
                                                                        echo btn_view('student/view/' . $student->srstudentID . "/" . $set, $this->lang->line('view'));
                                                                        if (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                                                                            echo btn_edit('student/edit/' . $student->srstudentID . "/" . $set, $this->lang->line('edit'));
                                                                            echo btn_delete('student/delete/' . $student->srstudentID . "/" . $set, $this->lang->line('delete'));
                                                                        }
                                                                        ?>
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
                                                <th class="col-sm-2"><?= $this->lang->line('slno') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_photo') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_name') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_roll') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_phone') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_village') ?></th>
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


$(document).on("focusout","#phone_update",function(){
  
    var studentID = $(this).attr('studentID');
    var phone = $(this).text(); 

    const editableDiv = document.getElementById('phone_update');
  
    if (phone.length > 10) {
      phone = phone.substring(0, 10);
    }

    $.ajax({
            type: 'POST',
            url: "<?=base_url('student/phone_update')?>",
            data: {"phone":phone,"studentID":studentID},
            dataType: "html",
            success: function(data) {
              $('.err').html(data);
            // alert(data);
            }
        });
})


</script>