<link rel="stylesheet" href="<?=base_url('assets/css/custom_table.css')?>"/>
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
                            <button class="btn" id="get_data" style="background: linear-gradient(45deg, #007bff, #6610f2); color: white; border: none; padding: 8px 20px; border-radius: 20px; font-weight: 500; box-shadow: 0 3px 10px rgba(0,123,255,0.3);"> 
                                <i class="fa fa-search" style="margin-right: 6px;"></i>Get Data
                            </button> 
                        </div>

                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-4 drop-marg">
                            <button class="btn" id="send_whatsapp" style="background: linear-gradient(45deg, #25D366, #128C7E); color: white; border: none; padding: 8px 18px; border-radius: 20px; font-weight: 500; box-shadow: 0 3px 10px rgba(37,211,102,0.4); transition: all 0.3s ease;" 
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(37,211,102,0.5)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 10px rgba(37,211,102,0.4)';">
                                <i class="fa fa-whatsapp" style="margin-right: 8px; font-size: 16px;"></i>Send Homework
                            </button> 
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
                                                    echo '<span class="section-badge">'.$sectionName.'</span>';
                                                }
                                            }
                                        } else {
                                            $dbSections = json_decode($assignment->sectionID);
                                            if(customCompute($dbSections)) foreach ($dbSections as $dbSectionID) {
                                                if(isset($sections[$dbSectionID])) {
                                                    echo '<span class="section-badge">'. $sections[$dbSectionID].'</span>';
                                                }
                                            } 
                                        }
                                        ?>
                                    </td>
                                    
                                    <td data-title="<?=$this->lang->line('subject')?>" class="subject-column">
                                        <?php echo $assignment->subject; ?>
                                    </td>
                                    
                                    <td data-title="<?=$this->lang->line('assignment_title')?>" class="title-column">
                                        <?php echo $assignment->title; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('assignment_description')?>" class="description-column">
                                        <?php echo namesorting($assignment->description, 130); ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('assignment_deadlinedate')?>" class="date-column">
                                        <?php echo date('d M Y', strtotime($assignment->deadlinedate)); ?>
                                    </td>
                                   
                                    <td data-title="<?=$this->lang->line('assignment_uploder')?>" class="uploader-column">
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
                                        <div class="action-btns">
                                        <?php if($this->session->userdata('usertypeID') == 3) {
                                            if($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) { 
                                                echo btn_upload('assignment/assignmentanswer/'.$assignment->assignmentID.'/'.$set, $this->lang->line('upload'));
                                            } }
                                            echo btn_view('assignment/view/'.$assignment->assignmentID.'/'.$set, $this->lang->line('view'));

                                            if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) { 
                                                echo btn_edit('assignment/edit/'.$assignment->assignmentID.'/'.$set, $this->lang->line('edit'));
                                                echo btn_delete('assignment/delete/'.$assignment->assignmentID.'/'.$set, $this->lang->line('delete'));
                                            } ?>
                                        </div>
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
        <div class="modal-content" style="border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); border: none;">
            <!-- Gradient Header -->
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; padding: 15px 20px; border-bottom: none;">
                <h4 class="modal-title" id="uploadHomeworkModalLabel" style="font-weight: 600; margin: 0; display: flex; align-items: center;">
                    <i class="fa fa-whatsapp" style="font-size: 24px; margin-right: 10px; color: #25D366;"></i>
                    Send Homework via WhatsApp
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1; text-shadow: none; font-size: 24px; font-weight: 300;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body" style="padding: 20px; background: #f8f9fa;">
                <!-- Info Card -->
                <div style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #17a2b8;">
                    <div style="display: flex; align-items: center;">
                        <i class="fa fa-info-circle" style="font-size: 20px; color: #0c5460; margin-right: 12px;"></i>
                        <div>
                            <strong style="color: #0c5460; font-size: 16px;">Optional File Attachment</strong>
                            <p style="margin: 5px 0 0; color: #0c5460; font-size: 14px;">You can attach a document to send along with the homework details.</p>
                        </div>
                    </div>
                </div>
                
                <form id="homeworkUploadForm" enctype="multipart/form-data">
                    <!-- File Upload Section -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="homeworkFile" class="control-label" style="font-weight: 600; color: #495057; margin-bottom: 12px; display: block;">
                            <i class="fa fa-cloud-upload" style="margin-right: 8px; color: #6c757d;"></i> 
                            Choose File 
                            <span style="color: #6c757d; font-weight: normal; font-size: 13px;">(Optional)</span>
                        </label>
                        
                        <!-- Custom File Upload Area -->
                        <div style="border: 2px dashed #e0e6ed; border-radius: 10px; padding: 20px; text-align: center; background: white; transition: all 0.3s ease; position: relative;" 
                             onmouseover="this.style.borderColor='#667eea'; this.style.backgroundColor='#f8f9ff';" 
                             onmouseout="this.style.borderColor='#e0e6ed'; this.style.backgroundColor='white';">
                            
                            <div id="uploadArea">
                                <i class="fa fa-cloud-upload" style="font-size: 36px; color: #667eea; margin-bottom: 10px;"></i>
                                <h6 style="color: #495057; margin-bottom: 8px; font-weight: 500;">Drag & Drop or Click to Browse</h6>
                                <p style="color: #6c757d; margin-bottom: 12px; font-size: 13px;">Select a file to attach with the homework</p>
                                
                                <input type="file" class="form-control" id="homeworkFile" name="homework_file" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" 
                                       style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer;">
                                
                                <button type="button" class="btn" style="background: linear-gradient(45deg, #667eea, #764ba2); color: white; border: none; padding: 10px 25px; border-radius: 25px; font-weight: 500;">
                                    <i class="fa fa-folder-open" style="margin-right: 8px;"></i>Browse Files
                                </button>
                            </div>
                            
                            <div id="fileSelected" style="display: none;">
                                <i class="fa fa-file-text-o" style="font-size: 36px; color: #28a745; margin-bottom: 10px;"></i>
                                <h6 style="color: #28a745; margin-bottom: 5px; font-weight: 600;">File Selected</h6>
                                <p id="fileName" style="color: #495057; margin-bottom: 10px; word-break: break-all;"></p>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()" style="border-radius: 20px;">
                                    <i class="fa fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                        
                        <!-- File Info -->
                        <div style="display: flex; align-items: center; margin-top: 12px; padding: 12px; background: #e3f2fd; border-radius: 8px;">
                            <i class="fa fa-lightbulb-o" style="color: #1976d2; margin-right: 10px; font-size: 16px;"></i>
                            <small style="color: #1976d2; font-size: 13px; line-height: 1.4;">
                                <strong>Supported formats:</strong> PDF, Word Documents, Images (JPG, PNG) • <strong>Max size:</strong> 10MB
                            </small>
                        </div>
                    </div>
                    
                    <!-- Recipients Info -->
                    <div style="background: white; padding: 15px; border-radius: 10px; border: 1px solid #e9ecef;">
                        <h6 style="color: #495057; margin-bottom: 15px; display: flex; align-items: center; font-weight: 600;">
                            <i class="fa fa-users" style="margin-right: 10px; color: #6c757d;"></i>Message Recipients
                        </h6>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                            <div style="padding: 10px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #007bff;">
                                <small style="color: #6c757d; font-weight: 500; display: block; margin-bottom: 3px;">CLASS</small>
                                <span id="selectedClass" style="color: #495057; font-weight: 600; font-size: 14px;">-</span>
                            </div>
                            <div style="padding: 10px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #28a745;">
                                <small style="color: #6c757d; font-weight: 500; display: block; margin-bottom: 3px;">SECTION</small>
                                <span id="selectedSection" style="color: #495057; font-weight: 600; font-size: 14px;">-</span>
                            </div>
                        </div>
                        <!-- <div style="display: flex; align-items: center; padding: 12px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                            <i class="fa fa-clock-o" style="color: #856404; margin-right: 10px;"></i>
                            <small style="color: #856404; line-height: 1.4;">
                                WhatsApp messages will be sent to all students and parents in the selected class & section.
                            </small>
                        </div> -->
                    </div>
                </form>
            </div>
            
            <!-- Enhanced Footer -->
            <div class="modal-footer" style="background: #f8f9fa; border-radius: 0 0 15px 15px; padding: 15px 20px; border-top: 1px solid #e9ecef;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background: #6c757d; border: none; padding: 12px 24px; border-radius: 25px; font-weight: 500; margin-right: 10px;">
                    <i class="fa fa-times" style="margin-right: 8px;"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="submitHomeworkFile" style="background: linear-gradient(45deg, #28a745, #20c997); border: none; padding: 12px 30px; border-radius: 25px; font-weight: 500; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                    <i class="fa fa-paper-plane" style="margin-right: 8px;"></i>Send WhatsApp Messages
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
                    // Enhanced success notification
                    showNotification("WhatsApp messages sent successfully!", "success");
                } else {
                    showNotification("Failed to send WhatsApp messages. Please try again.", "error");
                }
            },
            error: function() {
                showNotification("An error occurred while sending the WhatsApp messages.", "error");
            }
        });
    });

    // Enhanced notification function
    function showNotification(message, type) {
        var bgColor = type === 'success' ? '#28a745' : '#dc3545';
        var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        var notification = $('<div style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px 20px; border-radius: 8px; color: white; font-weight: 500; box-shadow: 0 4px 15px rgba(0,0,0,0.2); min-width: 300px; display: flex; align-items: center; background: ' + bgColor + ';">' +
            '<i class="fa ' + icon + '" style="margin-right: 10px; font-size: 18px;"></i>' +
            '<span>' + message + '</span>' +
            '<button onclick="$(this).parent().remove()" style="background: none; border: none; color: white; margin-left: 15px; font-size: 18px; cursor: pointer;">&times;</button>' +
            '</div>');
        
        $('body').append(notification);
        setTimeout(function() { notification.fadeOut(); }, 5000);
    }

    // File upload handling
    $('#homeworkFile').change(function() {
        var file = this.files[0];
        if (file) {
            $('#uploadArea').hide();
            $('#fileSelected').show();
            $('#fileName').text(file.name + ' (' + formatFileSize(file.size) + ')');
        }
    });

    function clearFile() {
        $('#homeworkFile').val('');
        $('#fileSelected').hide();
        $('#uploadArea').show();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

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
