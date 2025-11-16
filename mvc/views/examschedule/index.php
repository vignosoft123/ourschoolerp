<?php 
    
    $exam_Id = htmlentities(escapeString($this->uri->segment(4)));
    $section_ID = htmlentities(escapeString($this->uri->segment(5)));
?>


<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-puzzle-piece"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_examschedule')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <?php if((($siteinfos->school_year == $this->session->userdata('defaultschoolyearID') || $this->session->userdata('usertypeID') == 1)) || ($this->session->userdata('usertypeID') != 3)) { ?>
                    <div class="filter-box">

                    <h5 class="page-header">
                        <?php if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID') || $this->session->userdata('usertypeID') == 1)) { ?>
                            <?php if(permissionChecker('examschedule_add')) { ?>
                                <a class="ose-btn create-btn" href="<?php echo base_url('examschedule/add') ?>">
                                    <i class="fa fa-plus"></i> 
                                    <?=$this->lang->line('add_title')?>
                                </a>

                                <a class="ose-btn create-btn" href="<?php echo base_url('marksetting/index') ?>">
                                    Mark Setting
                                </a>


                            <?php } ?>
                        <?php } ?>


                            
                

                
                <?php if($this->session->userdata('usertypeID') != 3) { ?>
                            <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4 pull-right drop-marg">
                                Class : 
                                <?php
                                    $array = array("0" => $this->lang->line("examschedule_select_classes"));
                                    if(customCompute($classes)) {
                                        foreach ($classes as $classa) {
                                            $array[$classa->classesID] = $classa->classes;
                                        }
                                    }
                                    echo form_dropdown("classesID", $array, set_value("classesID", $set), "id='classesID' class='form-control select2'");
                                ?>
                            </div>


                            <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4 pull-right drop-marg">
                                Exam : 
                                <?php
                                    $examArray['0'] = $this->lang->line("examschedulereport_please_select");
                                    echo form_dropdown("examID", $examArray, set_value("examID",$examId), "id='examID' class='form-control select2'"); 
                                ?>

                            </div>

                        <?php }else{ ?> 

                            <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4 pull-right drop-marg">
                                <input type="hidden" value="<?= $set?>" name="classesID" id='classesID'> 
                                Exam :  

                                <?php
                                $examArray['0'] = $this->lang->line("examschedulereport_please_select");
                                echo form_dropdown("examID", $examArray, set_value("examID",$examId), "id='examID' class='form-control select2'"); 
                                ?>
 
                            </div>
                        <?php }?>
                    </h5>

                            </div>
                  

                <?php } ?>

                

                <?php if(customCompute($examschedules) > 0 ) { ?>
                    <?php if(permissionChecker('examschedule_delete')) { ?>
                        <form id="bulkDeleteForm" method="post" action="<?=base_url('examschedule/multi_delete')?>">
                            <input type="hidden" name="classesID" value="<?= $set ?>">
                            <input type="hidden" name="examID" value="<?= $exam_Id ?>">
                            <input type="hidden" name="sectionID" value="<?= $section_ID ?>">
                            <button type="submit" id="bulkDeleteBtn" class="ose-btn delete-btn btn btn-danger" style="margin-bottom:10px;color:#fff;border-color:#d43f3a;">Delete Selected</button>
                    <?php } ?>

                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="<?= $sec_id == 0 ? 'active' : ''?>"><a class="section_wise" sectionid='0' data-toggle="tab" href="#all" aria-expanded="true"><?=$this->lang->line("examschedule_all_examschedule")?></a></li>
                            
                            <?php foreach ($sections as $key => $section) {
                                echo '<li class="'.($section->sectionID == $sec_id ? 'active' : '').'"><a sectionid='.$section->sectionID.'  class="section_wise" data-toggle="tab" href="#'. $section->sectionID .'" aria-expanded="false">'. $this->lang->line("examschedule_section")." ".$section->section. " ( ". $section->category." )".'</a></li>';
                            } ?>
                        </ul>

                        <div class="tab-content">
                            <div id="all" class="tab-pane active">
                                <div id="hide-table">
                                    <table id="example1" class="table table-bordered tableBorder dataTable no-footer">
                                        <thead>
                                            <tr>
                                                <?php if(permissionChecker('examschedule_delete')) { ?>
                                                    <th><input type="checkbox" id="select_all"></th>
                                                <?php } ?>
                                                <th><?=$this->lang->line('slno')?></th>
                                                <th><?=$this->lang->line('examschedule_name')?></th>
                                                <th><?=$this->lang->line('examschedule_classes')?></th>
                                                <th><?=$this->lang->line('examschedule_section')?></th>
                                                <th><?=$this->lang->line('examschedule_subject')?></th>
                                                <th><?=$this->lang->line('examschedule_date')?></th>
                                                <th><?=$this->lang->line('examschedule_time')?></th>
                                                <th><?=$this->lang->line('examschedule_room')?></th>
                                                <?php if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID') || $this->session->userdata('usertypeID') == 1)) { ?>
                                                    <?php if(permissionChecker('examschedule_edit') || permissionChecker('examschedule_delete')) { ?>
                                                        <th><?=$this->lang->line('action')?></th>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(customCompute($examschedules)) {$i = 1; foreach($examschedules as $examschedule) { ?>
                                                <tr>
                                                    <?php if(permissionChecker('examschedule_delete')) { ?>
                                                        <td><input type="checkbox" class="select_item" name="selected[]" value="<?= $examschedule->examscheduleID ?>"></td>
                                                    <?php } ?>
                                                    <td data-title="<?=$this->lang->line('slno')?>">
                                                        <?php echo $i; ?>
                                                    </td>
                                                    <td data-title="<?=$this->lang->line('examschedule_name')?>">
                                                        <?php echo $examschedule->exam; ?>
                                                    </td>
                                                    <td data-title="<?=$this->lang->line('examschedule_classes')?>">
                                                        <?php echo $examschedule->classes; ?>
                                                    </td>
                                                    <td data-title="<?=$this->lang->line('examschedule_section')?>">
                                                        <?php echo $examschedule->section; ?>
                                                    </td>
                                                    <td data-title="<?=$this->lang->line('examschedule_subject')?>">
                                                        <?php echo $examschedule->subject; ?>
                                                    </td>

                                                    <td data-title="<?=$this->lang->line('examschedule_date')?>">
                                                        <?php echo date("d M Y", strtotime($examschedule->edate)); ?>
                                                    </td>

                                                    <td data-title="<?=$this->lang->line('examschedule_time')?>">
                                                        <?php echo $examschedule->examfrom, " - ", $examschedule->examto ; ?>
                                                    </td>

                                                    <td data-title="<?=$this->lang->line('examschedule_room')?>">
                                                        <?php echo $examschedule->room; ?>
                                                    </td>

                                                    <?php if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID') || $this->session->userdata('usertypeID') == 1)) { ?>
                                                        <?php if(permissionChecker('examschedule_edit') || permissionChecker('examschedule_delete')) { ?>
                                                        <td data-title="<?=$this->lang->line('action')?>">
                                                            <?php echo btn_edit('examschedule/edit/'.$examschedule->examscheduleID."/".$set, $this->lang->line('edit')) ?>
                                                            <?php echo btn_delete('examschedule/delete/'.$examschedule->examscheduleID."/".$set.'/'.$exam_Id.'/'.$section_ID, $this->lang->line('delete')) ?>

                                                            <a href="<?php echo base_url('examschedule/copy/'.$examschedule->examscheduleID."/".$set ) ?>" >  Copy </a>

                                                        </td>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </tr>
                                            <?php $i++; }} ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <?php foreach ($sections as $key => $section) { ?>
                                    <div id="<?=$section->sectionID?>" class="tab-pane">
                                        
                                        <div id="hide-table">
                                            <table class="table table-bordered tableBorder dataTable no-footer">
                                                        <thead>
                                                            <tr>
                                                                <?php if(permissionChecker('examschedule_delete')) { ?>
                                                                    <th><input type="checkbox" id="select_all_2"></th>
                                                                <?php } ?>
                                                                <th><?=$this->lang->line('slno')?></th>
                                                        <th><?=$this->lang->line('examschedule_name')?></th>
                                                        <th><?=$this->lang->line('examschedule_classes')?></th>
                                                        <th><?=$this->lang->line('examschedule_subject')?></th>
                                                        <th><?=$this->lang->line('examschedule_date')?></th>
                                                        <th><?=$this->lang->line('examschedule_time')?></th>
                                                        <th><?=$this->lang->line('examschedule_room')?></th>
                                                        <?php if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID') || $this->session->userdata('usertypeID') == 1)) { ?>
                                                            <?php if(permissionChecker('examschedule_edit') || permissionChecker('examschedule_delete')) { ?>
                                                                <th><?=$this->lang->line('action')?></th>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(customCompute($allsection[$section->section])) {$i = 1; foreach($allsection[$section->section] as $examschedule) { ?>
                                                        <tr>
                                                            <?php if(permissionChecker('examschedule_delete')) { ?>
                                                                <td><input type="checkbox" class="select_item" name="selected[]" value="<?= $examschedule->examscheduleID ?>"></td>
                                                            <?php } ?>
                                                            <td data-title="<?=$this->lang->line('slno')?>">
                                                                <?php echo $i; ?>
                                                            </td>
                                                            <td data-title="<?=$this->lang->line('examschedule_name')?>">
                                                                <?php echo $examschedule->exam; ?>
                                                            </td>
                                                            <td data-title="<?=$this->lang->line('examschedule_classes')?>">
                                                                <?php echo $examschedule->classes; ?>
                                                            </td>
        
                                                            <td data-title="<?=$this->lang->line('examschedule_subject')?>">
                                                                <?php echo $examschedule->subject; ?>
                                                            </td>

                                                            <td data-title="<?=$this->lang->line('examschedule_date')?>">
                                                                <?php echo date("d M Y", strtotime($examschedule->edate)); ?>
                                                            </td>

                                                            <td data-title="<?=$this->lang->line('examschedule_time')?>">
                                                                <?php echo $examschedule->examfrom, " - ", $examschedule->examto ; ?>
                                                            </td>

                                                            <td data-title="<?=$this->lang->line('examschedule_room')?>">
                                                                <?php echo $examschedule->room; ?>
                                                            </td>

                                                            <?php if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID') || $this->session->userdata('usertypeID') == 1)) { ?>
                                                                <?php if(permissionChecker('examschedule_edit') || permissionChecker('examschedule_delete')) { ?>

                                                                    <td data-title="<?=$this->lang->line('action')?>">
                                                                        <?php echo btn_edit('examschedule/edit/'.$examschedule->examscheduleID."/".$set, $this->lang->line('edit')) ?>
                                                                        
                                                                        <?php echo btn_delete('examschedule/delete/'.$examschedule->examscheduleID."/".$set.'/'.$exam_Id.'/'.$section_ID, $this->lang->line('delete')) ?>

                                                                       <a href="<?php echo base_url('examschedule/copy/'.$examschedule->examscheduleID."/".$set ) ?>" >  Copy </a>


                                                                    </td>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php $i++; }} ?>
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
                            <li class="active"><a data-toggle="tab" href="#all" aria-expanded="true"><?=$this->lang->line("examschedule_all_examschedule")?></a></li>
                        </ul>


                        <div class="tab-content">
                            <div id="all" class="tab-pane active">
                                <div id="hide-table">
                                    <table id="example1" class="table table-bordered tableBorder dataTable no-footer">
                                        <thead>
                                            <tr>
                                                <?php if(permissionChecker('examschedule_delete')) { ?>
                                                    <th><input type="checkbox" id="select_all_3"></th>
                                                <?php } ?>
                                                <th><?=$this->lang->line('slno')?></th>
                                                <th><?=$this->lang->line('examschedule_name')?></th>
                                                <th><?=$this->lang->line('examschedule_classes')?></th>
                                                <th><?=$this->lang->line('examschedule_section')?></th>
                                                <th><?=$this->lang->line('examschedule_subject')?></th>
                                                <th><?=$this->lang->line('examschedule_date')?></th>
                                                <th><?=$this->lang->line('examschedule_time')?></th>
                                                <th><?=$this->lang->line('examschedule_room')?></th>
                                                <?php if(permissionChecker('examschedule_edit') || permissionChecker('examschedule_delete')) { ?>
                                                <th><?=$this->lang->line('action')?></th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(customCompute($examschedules)) {$i = 1; foreach($examschedules as $examschedule) { ?>
                                                <tr>
                                                    <?php if(permissionChecker('examschedule_delete')) { ?>
                                                        <td><input type="checkbox" class="select_item" name="selected[]" value="<?= $examschedule->examscheduleID ?>"></td>
                                                    <?php } ?>
                                                    <td data-title="<?=$this->lang->line('slno')?>">
                                                        <?php echo $i; ?>
                                                    </td>
                                                    <td data-title="<?=$this->lang->line('examschedule_name')?>">
                                                        <?php echo $examschedule->exam; ?>
                                                    </td>
                                                    <td data-title="<?=$this->lang->line('examschedule_classes')?>">
                                                        <?php echo $examschedule->classes; ?>
                                                    </td>
                                                    <td data-title="<?=$this->lang->line('examschedule_section')?>">
                                                        <?php echo $examschedule->section; ?>
                                                    </td>
                                                    <td data-title="<?=$this->lang->line('examschedule_subject')?>">
                                                        <?php echo $examschedule->subject; ?>
                                                    </td>

                                                    <td data-title="<?=$this->lang->line('examschedule_date')?>">
                                                        <?php echo date("d M Y", strtotime($examschedule->edate)); ?>
                                                    </td>

                                                    <td data-title="<?=$this->lang->line('examschedule_time')?>">
                                                        <?php echo $examschedule->examfrom, " - ", $examschedule->examto ; ?>
                                                    </td>

                                                    <td data-title="<?=$this->lang->line('examschedule_room')?>">
                                                        <?php echo $examschedule->room; ?>
                                                    </td>

                                                    <?php if(permissionChecker('examschedule_edit') || permissionChecker('examschedule_delete')) { ?>
                                                    <td data-title="<?=$this->lang->line('action')?>">
                                                        <?php echo btn_edit('examschedule/edit/'.$examschedule->examscheduleID."/".$set, $this->lang->line('edit')) ?>
                                                        <?php echo btn_delete('examschedule/delete/'.$examschedule->examscheduleID."/".$set.'/'.$exam_Id.'/'.$section_ID, $this->lang->line('delete')) ?>

                                                        <a href="<?php echo base_url('examschedule/copy/'.$examschedule->examscheduleID."/".$set ) ?>" >  Copy </a>

                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php $i++; }} ?>
                                        </tbody>
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
    $('#classesID').change(function() {



        var classesID = $(this).val();
        if(classesID == 0) {
            $('#hide-table').hide();
            $('.nav-tabs-custom').hide();
        } else {
            // $.ajax({
            //     type: 'POST',
            //     url: "<?=base_url('examschedule/examschedule_list')?>",
            //     data: "id=" + classesID,
            //     dataType: "html",
            //     success: function(data) {
            //         //window.location.href = data;
            //     }
            // });

            $.ajax({
                type: 'POST',
                url: "<?=base_url('examschedule/getExam')?>",
                data: {"classesID" : classesID},
                dataType: "html",
                success: function(data) {
                   $('#examID').html(data);
                }
            });

        }
    });

    $(document).on('change', '#examID', function() {
    	$('#load_examschedulereport').html('');
        var examID = $(this).val();
        var classesID = $('#classesID').val();

        if(classesID == 0) {
            $('#hide-table').hide();
            $('.nav-tabs-custom').hide();
        } else {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('examschedule/examschedule_list')?>",
                data: {id:classesID,examID:examID}  ,
                // data: "id=" + classesID ,
                dataType: "html",
                success: function(data) {
                    window.location.href = data;
                }
            });
        }
           });

    $(document).on('click', '.section_wise', function() {
    	$('#load_examschedulereport').html('');
        var examID = $("#examID").val();
        var classesID = $('#classesID').val();
        var sectionID = $(this).attr('sectionid');
 
        if(classesID == 0) {
            $('#hide-table').hide();
            $('.nav-tabs-custom').hide();
        } else {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('examschedule/examschedule_list')?>",
                data: {id:classesID,examID:examID,sectionID:sectionID}  ,
                // data: "id=" + classesID ,
                dataType: "html",
                success: function(data) {
                    window.location.href = data;
                }
            });
        }
    });

</script>

<script type="text/javascript">
    // Select all handling for the various tables
    $(document).on('change', '#select_all, #select_all_2, #select_all_3', function() {
        var checked = $(this).prop('checked');
        $('.select_item').prop('checked', checked);
    });

    // Ensure at least one item selected and confirm before submitting
    $(document).on('submit', '#bulkDeleteForm', function(e) {
        if(!$('[name="selected[]"]:checked').length) {
            e.preventDefault();
            alert('Please select at least one exam schedule to delete.');
            return false;
        }
        if(!confirm('Are you sure you want to delete selected exam schedules?')) {
            e.preventDefault();
            return false;
        }
        return true;
    });
</script>
