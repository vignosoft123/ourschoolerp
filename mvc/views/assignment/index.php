<style>
    .filter-box {
    background-color:hsl(133, 46.30%, 75.90%);
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
}

    </style>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-assignment"></i> Home Work</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i><?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_assignment')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row ">
            <div class="col-sm-12 ">
                <?php if((($siteinfos->school_year == $this->session->userdata('defaultschoolyearID') || $this->session->userdata('usertypeID') == 1)) || ($this->session->userdata('usertypeID') != 3)) { ?>
                    <div class="filter-box">
                    <h5 class="page-header">
                        <?php if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID') || $this->session->userdata('usertypeID') == 1)) { ?>
                            <?php if(permissionChecker('assignment_add')) { ?>
                                <a class="ose-btn create-btn" href="<?php echo base_url('assignment/add') ?>">
                                    <i class="fa fa-plus"></i> 
                                    Add Home Work
                                </a>
                            <?php } ?>
                        <?php } ?>
                        <?php if($this->session->userdata('usertypeID') != 3) { ?>
                            <div class="col-lg-2 col-sm-2 col-md-2 col-xs-4 drop-marg">
                                <?php
                                    $array = array("0" => $this->lang->line("assignment_select_classes"));
                                    if(customCompute($classes)) {
                                        foreach ($classes as $classa) {
                                            $array[$classa->classesID] = $classa->classes;
                                        }
                                    }
                                    echo form_dropdown("classesID", $array, set_value("classesID", $set), "id='classesID' class='form-control select2'");
                                ?>
                            </div>

                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-4 drop-marg">
                            <?php
                                // Debug: Let's see what sections contains
                                // echo "<pre>Sections data: "; var_dump($sections); echo "</pre>";
                                // echo "<pre>Set section ID: "; var_dump($setsectionID); echo "</pre>";
                                
                                 $array = array('0' => 'Select Section');
                                if(isset($sections) && $sections && $sections != "empty") {
                                    // Check if sections is already an associative array (from pluck function)
                                    if(is_array($sections) && !empty($sections)) {
                                        $firstElement = reset($sections);
                                        if(!is_object($firstElement)) {
                                            // It's an associative array from pluck
                                            foreach ($sections as $sectionId => $sectionName) {
                                                $array[$sectionId] = $sectionName;
                                            }
                                        } else {
                                            // Handle object format
                                            foreach ($sections as $section) {
                                                if(is_object($section)) {
                                                    $array[$section->sectionID] = $section->section;
                                                }
                                            }
                                        }
                                    }
                                }
                                
                                echo form_dropdown("sectionID", $array, set_value("sectionID",$setsectionID), "id='sectionID' class='form-control select2'");
                            ?>
                        </div>

                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-4 drop-marg">
                            <?php
                                $array = array('0' => $this->lang->line("assignment_select_subject"));
                                if(isset($subjects) && $subjects && $subjects != "empty") {
                                    foreach ($subjects as $subject) {
                                        $array[$subject->subjectID] = $subject->subject;
                                    }
                                }
                                
                                echo form_dropdown("subjectID", $array, set_value("subjectID",$setsubjectID), "id='subjectID' class='form-control select2'");
                            ?>   
                        </div>

                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-4 drop-marg">
                            <input type="text" class="form-control" id="deadlinedate" name="deadlinedate" value="<?=set_value('deadlinedate',$date)?>" >
                        </div>

                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-4 drop-marg">
                            <button class="btn btn-success" id="get_data" > Get Data</button> 
                        </div>

                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-4 drop-marg">
                            <button class="btn btn-primary" id="send_whatsapp" >Send Homework on WhatsApp</button> 
                        </div>

                        <?php } ?>
                    </h5>
                </div>
                <?php } ?>

                <div id="hide-table">
                    <table id="example1" class="table table-bordered tableBorder dataTable no-footer">
                        <thead>
                            <tr>
                                <th><?=$this->lang->line('slno')?></th>
                                <th><?=$this->lang->line('assignment_section')?></th>
                                <th>Subject</th>

                                <th><?=$this->lang->line('assignment_title')?></th>
                                <th class="col-lg-3"><?=$this->lang->line('assignment_description')?></th>
                                <th><?=$this->lang->line('assignment_deadlinedate')?></th>
                                <th><?=$this->lang->line('assignment_uploder')?></th>
                                <th><?=$this->lang->line('assignment_file')?></th>
                                <?php if(permissionChecker('assignment_edit') || permissionChecker('assignment_delete') || permissionChecker('assignment_view')) { ?>
                                <th><?=$this->lang->line('action')?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($assignments)) {$i = 1; foreach($assignments as $assignment) {
                                if(($this->session->userdata('usertypeID') == 3) && customCompute($student) && in_array($assignment->subjectID, $opsubjects) && ($student->sroptionalsubjectID != $assignment->subjectID)) {
                                    continue;
                                } ?>
                                <tr>
                                    <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>

                                     <td data-title="<?=$this->lang->line('assignment_section')?>">
                                        <?php  
                                        // print_r($assignment);die;
                                        if($assignment->sectionID == 'false') {
                                            if(customCompute($sections)) {
                                                foreach ($sections as $sectionId => $sectionName) {
                                                    echo $this->lang->line('assignment_section').' '.$sectionName.'<br>';
                                                }
                                            }
                                        } else {
                                            $dbSections = json_decode($assignment->sectionID);
                                            if(customCompute($dbSections)) foreach ($dbSections as $dbSectionID) {
                                                if(isset($sections[$dbSectionID])) {
                                                    echo $this->lang->line('assignment_section').' '. $sections[$dbSectionID].'<br>';
                                                }
                                            } 
                                        }
                                        ?>
                                    </td>
                                    
                                    <td data-title="<?=$this->lang->line('subject')?>">
                                        <?php echo $assignment->subject; ?>
                                    </td>
                                    
                                    <td data-title="<?=$this->lang->line('assignment_title')?>">
                                        <?php echo $assignment->title; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('assignment_description')?>">
                                        <?php echo namesorting($assignment->description, 130); ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('assignment_deadlinedate')?>">
                                        <?php echo date('d M Y', strtotime($assignment->deadlinedate)); ?>
                                    </td>
                                   
                                    <td data-title="<?=$this->lang->line('assignment_uploder')?>">
                                        <?php echo getNameByUsertypeIDAndUserID($assignment->usertypeID, $assignment->userID); ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('assignment_file')?>">
                                        <?php 
                                            if($assignment->originalfile) { 
                                                echo btn_download_file('assignment/download/'.$assignment->assignmentID, namesorting($assignment->originalfile), $this->lang->line('download')); 
                                            }
                                        ?>
                                    </td>
                                    <?php if(permissionChecker('assignment_edit') || permissionChecker('assignment_delete') || permissionChecker('assignment_view')) { ?>
                                    <td data-title="<?=$this->lang->line('action')?>">
                                        <?php if($this->session->userdata('usertypeID') == 3) {
                                            if($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) { 
                                                echo btn_upload('assignment/assignmentanswer/'.$assignment->assignmentID.'/'.$set, $this->lang->line('upload'));
                                            } }
                                            echo btn_view('assignment/view/'.$assignment->assignmentID.'/'.$set, $this->lang->line('view'));

                                            if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) { 
                                                echo btn_edit('assignment/edit/'.$assignment->assignmentID.'/'.$set, $this->lang->line('edit'));
                                                echo btn_delete('assignment/delete/'.$assignment->assignmentID.'/'.$set, $this->lang->line('delete'));
                                            } ?>
                                    </td>
                                    <?php } ?>
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Add Modal for File Upload -->
<div class="modal fade" id="uploadHomeworkModal" tabindex="-1" role="dialog" aria-labelledby="uploadHomeworkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="uploadHomeworkModalLabel">
                    <i class="fa fa-whatsapp"></i> Send Homework via WhatsApp
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> 
                    <strong>Note: File is Optional</strong> 
                </div>
                
                <form id="homeworkUploadForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="homeworkFile" class="control-label">
                            <i class="fa fa-file-pdf-o"></i> Choose File 
                            <small class="text-muted">(Optional)</small>
                        </label>
                        <div class="input-group">
                            <input type="file" class="form-control" id="homeworkFile" name="homework_file" 
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="padding: 6px 12px;">
                            <span class="input-group-addon">
                                <i class="fa fa-paperclip"></i>
                            </span>
                        </div>
                        <small class="help-block text-muted">
                            <i class="fa fa-lightbulb-o"></i> 
                            Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB)
                        </small>
                    </div>
                    
                  <!--  <div class="form-group">
                        <label class="control-label">
                            <i class="fa fa-users"></i> Recipients
                        </label>
                         <div class="well well-sm">
                            <p class="margin-bottom-5">
                                <strong>Class:</strong> <span id="selectedClass">-</span> | 
                                <strong>Section:</strong> <span id="selectedSection">-</span>
                            </p>
                            <p class="margin-bottom-0 text-muted">
                                <i class="fa fa-clock-o"></i> 
                                Messages will be sent to all students and parents in the selected class & section.
                            </p>
                        </div>
                    </div> -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="submitHomeworkFile">
                    <i class="fa fa-paper-plane"></i> Send WhatsApp Messages
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(".select2").select2();
    $("#deadlinedate").datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    //startDate:'<?=$schoolyearobj->startingdate?>',
    //endDate:'<?=$schoolyearobj->endingdate?>',
});

    // $('#classesID').change(function() {
    //     var classesID = $(this).val();
    //     if(classesID == 0) {
    //         $('#hide-table').hide();
    //         $('.nav-tabs-custom').hide();
    //     } else {
    //         $.ajax({
    //             type: 'POST',
    //             url: "<?=base_url('assignment/student_list')?>",
    //             data: "id=" + classesID,
    //             dataType: "html",
    //             success: function(data) {
    //                 window.location.href = data;
    //             }
    //         });
    //     }
    // });

        $('#get_data').click(function() {
        // var classesID = $(this).val();
        var classesID = $('#classesID').val();
        var subjectID = $('#subjectID').val();
        var sectionID = $('#sectionID').val();
        var deadlinedate = $('#deadlinedate').val();
        if(classesID == 0) {
            alert("Please select Class!");
            $('#hide-table').hide();
            $('.nav-tabs-custom').hide();
        } else {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('assignment/student_list')?>",
                 data: {
                id: classesID,
                sectionID: sectionID,
                subjectID: subjectID,
                deadlinedate:deadlinedate
            },
                dataType: "html",
                success: function(data) {
                    window.location.href = data;
                }
            });
        }
    });


    $('#send_whatsapp').click(function() {
        var classesID = $('#classesID').val();
        var sectionID = $('#sectionID').val();
        var deadlinedate = $('#deadlinedate').val();

        if(classesID == 0 || sectionID == 0) {
            alert("Please select Class and Section!");
        } else {
            // Update modal with selected class and section info
            var selectedClassName = $('#classesID option:selected').text();
            var selectedSectionName = $('#sectionID option:selected').text();
            
            $('#selectedClass').text(selectedClassName);
            $('#selectedSection').text(selectedSectionName);
            
            $('#uploadHomeworkModal').modal('show');
        }
    });


    $('#classesID').change(function(event) {
    var classesID = $(this).val();
    if(classesID === '0') {
        $('#subjectID').val(0);
        $('#sectionID').val('');
    } else {
        $('#sectionID').val('');
        $.ajax({
            type: 'POST',
            url: "<?=base_url('assignment/subjectcall')?>",
            data: "id=" + classesID,
            dataType: "html",
            success: function(data) {
               $('#subjectID').html(data);
            }
        });

        $.ajax({
            type: 'POST',
            url: "<?=base_url('assignment/sectioncall')?>",
            data: "id=" + classesID,
            dataType: "html",
            success: function(data) {
               $('#sectionID').html(data);
            }
        });
    }
});

    $('#submitHomeworkFile').click(function() {
        var formData = new FormData($('#homeworkUploadForm')[0]);
        formData.append('classesID', $('#classesID').val());
        formData.append('sectionID', $('#sectionID').val());
        formData.append('deadlinedate', $('#deadlinedate').val());

        $.ajax({
            url: "<?=base_url('assignment/send_homework_whatsapp')?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                $('#uploadHomeworkModal').modal('hide');
                if(response.status) {
                    alert("WhatsApp messages sent successfully!");
                } else {
                    alert("Failed to send WhatsApp messages. Please try again.");
                }
            },
            error: function() {
                alert("An error occurred while sending the WhatsApp messages.");
            }
        });
    });

    $(document).ready(function() {
    var classesID = "<?= $set ?>";
    var sectionID = "<?= $setsectionID ?>";
    var subjectID = "<?= $setsubjectID ?>";
    
    // If we have URL parameters, set the dropdown values
    if (classesID && classesID != '0') {
        // Set the class dropdown
        $('#classesID').val(classesID).trigger('change.select2');
        
        // Check if sections and subjects are already loaded from server
        var sectionOptions = $('#sectionID option').length;
        var subjectOptions = $('#subjectID option').length;
        
        if (sectionOptions <= 1) {
            // Load sections if not already loaded
            $.ajax({
                type: 'POST',
                url: "<?=base_url('assignment/sectioncall')?>",
                data: "id=" + classesID,
                dataType: "html",
                success: function(data) {
                   $('#sectionID').html(data);
                   // Set the section value after options are loaded
                   if (sectionID && sectionID != '0') {
                       $('#sectionID').val(sectionID).trigger('change.select2');
                   }
                }
            });
        } else {
            // Sections already loaded, just set the value
            if (sectionID && sectionID != '0') {
                $('#sectionID').val(sectionID).trigger('change.select2');
            }
        }

        if (subjectOptions <= 1) {
            // Load subjects if not already loaded
            $.ajax({
                type: 'POST',
                url: "<?=base_url('assignment/subjectcall')?>",
                data: "id=" + classesID,
                dataType: "html",
                success: function(data) {
                   $('#subjectID').html(data);
                   // Set the subject value after options are loaded
                   if (subjectID && subjectID != '0') {
                       $('#subjectID').val(subjectID).trigger('change.select2');
                   }
                }
            });
        } else {
            // Subjects already loaded, just set the value
            if (subjectID && subjectID != '0') {
                $('#subjectID').val(subjectID).trigger('change.select2');
            }
        }
    }
});
</script>
