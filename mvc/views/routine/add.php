
<style>
    .routine-table-container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-top: 20px;
    }
    .routine-table thead th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: 600;
        border-bottom: 2px solid #eee !important;
        white-space: nowrap;
        padding: 12px 8px !important;
    }
    .routine-table tbody td {
        padding: 10px 8px !important;
        vertical-align: middle !important;
        border-top: 1px solid #f1f1f1 !important;
    }
    .routine-table .form-control {
        border-radius: 4px;
        border: 1px solid #ddd;
        height: 38px;
        box-shadow: none;
    }
    .routine-table .select2-container--default .select2-selection--single {
        border: 1px solid #ddd;
        height: 38px;
        border-radius: 4px;
    }
    .routine-table .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
    .routine-table .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .btn-add-more {
        background-color: #27ae60;
        color: white;
        border: none;
        padding: 8px 18px;
        border-radius: 4px;
        font-weight: 600;
        transition: all 0.3s;
        margin-top: 10px;
    }
    .btn-add-more:hover {
        background-color: #219150;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(39, 174, 96, 0.2);
    }
    .btn-remove {
        background-color: #e74c3c;
        color: white;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        line-height: 32px;
        text-align: center;
        padding: 0;
        transition: all 0.3s;
    }
    .btn-remove:hover:not(:disabled) {
        background-color: #c0392b;
        transform: scale(1.05);
    }
    .btn-remove:disabled {
        background-color: #fadbd8;
        cursor: not-allowed;
    }
    .ose-btn-submit {
        background-color: #3498db;
        color: white;
        padding: 10px 30px;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        margin-top: 20px;
        transition: all 0.3s;
    }
    .ose-btn-submit:hover {
        background-color: #2980b9;
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }
</style>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-routine"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("routine/index")?>"><?=$this->lang->line('menu_routine')?></a></li>
            <li class="active"><?=$this->lang->line('menu_add')?> <?=$this->lang->line('menu_routine')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <form class="form-horizontal" role="form" method="post" id="routine_form">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group <?=form_error('schoolyearID') ? 'has-error' : ''?>">
                        <label for="schoolyearID" class="control-label">
                            <?=$this->lang->line("routine_schoolyear")?> <span class="text-red">*</span>
                        </label>
                        <div>
                            <?php
                                $arrayschoolyear = [];
                                $arrayschoolyear[0] = $this->lang->line("routine_select_schoolyear");
                                $defaultschoolyear = $siteinfos->school_year;
                                foreach ($schoolyears as $schoolyear) {
                                    $arrayschoolyear[$schoolyear->schoolyearID] = ($siteinfos->school_year == $schoolyear->schoolyearID) ? $schoolyear->schoolyear .' ('.$this->lang->line('default').')' : $schoolyear->schoolyear;
                                }
                                echo form_dropdown("schoolyearID", $arrayschoolyear, set_value("schoolyearID", $defaultschoolyear), "id='schoolyearID' class='form-control select2'");
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group <?=form_error('classesID') ? 'has-error' : ''?>">
                        <label for="classesID" class="control-label">
                            <?=$this->lang->line("routine_classes")?> <span class="text-red">*</span>
                        </label>
                        <div>
                            <?php
                                $arrayclass[0] = $this->lang->line("routine_select_classes");
                                if(customCompute($classes)) {
                                    foreach ($classes as $classa) {
                                        $arrayclass[$classa->classesID] = $classa->classes;
                                    }
                                }
                                echo form_dropdown("classesID", $arrayclass, set_value("classesID"), "id='classesID' class='form-control select2'");
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group <?=form_error('sectionID') ? 'has-error' : ''?>">
                        <label for="sectionID" class="control-label">
                            <?=$this->lang->line("routine_section")?> <span class="text-red">*</span>
                        </label>
                        <div>
                            <select name="sectionID" id="classes_sectionID" class="form-control select2">
                                <option value="0"><?=$this->lang->line("routine_select_section")?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group <?=form_error('day') ? 'has-error' : ''?>">
                        <label for="day" class="control-label">
                            <?=$this->lang->line("routine_day")?> <span class="text-red">*</span>
                        </label>
                        <div>
                            <?php
                                $weekends = $siteinfos->weekends;
                                $weekendsKeys = explode(',', $weekends);
                                $weekendsArray = array('0' => $this->lang->line('sunday'), '1' => $this->lang->line('monday'), '2' => $this->lang->line('tuesday'), '3' => $this->lang->line('wednesday'), '4' => $this->lang->line('thursday'), '5' => $this->lang->line('friday'), '6' => $this->lang->line('saturday'));
                                $newArrayDay = [100 => $this->lang->line('routine_select_day')];
                                foreach($weekendsArray as $key => $value) {
                                    if(!in_array($key, $weekendsKeys)) $newArrayDay[$key] = $value;
                                }
                                echo form_dropdown("day", $newArrayDay, set_value("day"), "id='day' class='form-control select2'");
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="routine-table-container">
                <div class="table-responsive">
                    <table class="table routine-table" id="routine_table">
                        <thead>
                            <tr>
                                <th width="25%"><?=$this->lang->line("routine_subject")?> <span class="text-red">*</span></th>
                                <th width="25%"><?=$this->lang->line("routine_teacher")?> <span class="text-red">*</span></th>
                                <th width="15%"><?=$this->lang->line("routine_start_time")?> <span class="text-red">*</span></th>
                                <th width="15%"><?=$this->lang->line("routine_end_time")?> <span class="text-red">*</span></th>
                                <th width="12%"><?=$this->lang->line("routine_room")?> <span class="text-red">*</span></th>
                                <th width="8%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="row_0">
                                <td>
                                    <select name="subjectID[0]" id="subjectID_0" class="form-control select2-field subjectID_class">
                                        <option value="0"><?=$this->lang->line("routine_subject_select")?></option>
                                    </select>
                                </td>
                                <td>
                                    <select name="teacherID[0]" id="teacherID_0" class="form-control select2-field teacherID_class">
                                        <option value="0"><?=$this->lang->line('routine_select_teacher')?></option>
                                    </select>
                                </td>
                                <td><input type="text" name="start_time[0]" id="start_time_0" class="form-control timepicker"></td>
                                <td><input type="text" name="end_time[0]" id="end_time_0" class="form-control timepicker"></td>
                                <td><input type="text" name="room[0]" id="room_0" class="form-control"></td>
                                <td><button type="button" class="btn-remove remove_row" disabled><i class="fa fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn-add-more" id="add_more_btn"><i class="fa fa-plus-circle"></i> ADD MORE</button>
            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="ose-btn-submit">
                        <i class="fa fa-save"></i> <?=$this->lang->line("add_routine")?>
                    </button>
                </div>
            </div>
        </form>

        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12">
                <?php if ($siteinfos->note==1) { ?>
                    <div class="callout callout-danger">
                        <p><b>Note:</b> Make teacher, class, subject & section before you add routine</p>
                    </div>
                <?php } ?>
                <?php if(isset($form_validation)) { ?>
                    <div class="callout callout-danger">
                        <?= $form_validation ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Utility to init plugins on a row
    function initPlugins(rowSelector) {
        $(rowSelector + ' .select2-field').select2({
            width: '100%',
            dropdownAutoWidth: true
        });
        
        $(rowSelector + ' .timepicker').timepicker({
            minuteStep: 5,
            showInputs: false,
            disableFocus: true
        });
    }

    // Initialize global dropdowns
    $('#schoolyearID, #classesID, #classes_sectionID, #day').select2({ width: '100%' });
    
    // Initialize first row
    initPlugins('#row_0');

    var row_count = 1;

    $('#add_more_btn').click(function() {
        // Clone from the hidden template
        var new_row = $('#row_template').clone();
        new_row.show();
        new_row.attr('id', 'row_' + row_count);
        new_row.removeAttr('style');

        // Update names and IDs
        new_row.find('select, input').each(function() {
            var name = $(this).attr('name');
            if(name) {
                $(this).attr('name', name.replace('[TEMPLATE]', '[' + row_count + ']'));
            }
            var id = $(this).attr('id');
            if(id) {
                $(this).attr('id', id.replace('_TEMPLATE', '_' + row_count));
            }
        });

        $('#routine_table tbody').append(new_row);
        initPlugins('#row_' + row_count);
        
        // If a class is already selected, try to sync the new row's subject dropdown
        var classesID = $('#classesID').val();
        if(classesID != 0) {
            updateRowSubjects(row_count, classesID);
        }

        row_count++;
    });

    $(document).on('click', '.remove_row', function() {
        if($('#routine_table tbody tr').length > 1) {
            if(confirm('Are you sure you want to remove this row?')) {
                $(this).closest('tr').remove();
            }
        } else {
            alert('At least one row is required.');
        }
    });

    function updateRowSubjects(index, classesID) {
        // Sync Subject for specific row
        $.ajax({
            type: 'POST',
            url: "<?=base_url('routine/subjectcall')?>",
            data: "id=" + classesID,
            success: function(data) {
               var obj = $('#subjectID_' + index);
               obj.select2('destroy');
               obj.html(data);
               obj.select2({ width: '100%' });
            }
        });
    }

    $('#classesID').change(function() {
        var classesID = $(this).val();
        if(classesID == 0) {
            $('#classes_sectionID').html('<option value="0"><?= $this->lang->line("routine_select_section") ?></option>').select2('val', 0);
            $('.subjectID_class').each(function() {
                $(this).select2('val', 0);
                $(this).html('<option value="0"><?= $this->lang->line("routine_subject_select") ?></option>');
            });
        } else {
            // Update Section dropdown once
            $.ajax({
                type: 'POST',
                url: "<?=base_url('routine/sectioncall')?>",
                data: "id=" + classesID,
                success: function(data) {
                   $('#classes_sectionID').select2('destroy');
                   $('#classes_sectionID').html(data);
                   $('#classes_sectionID').select2({ width: '100%' });
                }
            });

            // Update all existing subject dropdowns in table
            $('#routine_table tbody tr').each(function() {
                var id = $(this).attr('id');
                if(id) {
                    var index = id.split('_')[1];
                    updateRowSubjects(index, classesID);
                }
            });
        }
    });

    $(document).on('change', '.subjectID_class', function() {
        var subjectID = $(this).val();
        var id_arr = $(this).attr('id').split('_');
        var index = id_arr[id_arr.length - 1];
        var teacherSelect = $('#teacherID_' + index);
        
        if(subjectID == 0) {
            teacherSelect.select2('val', 0);
            teacherSelect.html('<option value="0"><?=$this->lang->line("routine_select_teacher")?></option>');
        } else {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('routine/teachercall')?>",
                data: { 'subjectID' : subjectID},
                success: function(data) {
                   teacherSelect.select2('destroy');
                   teacherSelect.html(data);
                   teacherSelect.select2({ width: '100%' });
                }
            });
        }
    });
});
</script>

<!-- Hidden Template Row -->
<table style="display:none;">
    <tr id="row_template">
        <td>
            <select name="subjectID[TEMPLATE]" id="subjectID_TEMPLATE" class="form-control select2-field subjectID_class">
                <option value="0"><?=$this->lang->line("routine_subject_select")?></option>
            </select>
        </td>
        <td>
            <select name="teacherID[TEMPLATE]" id="teacherID_TEMPLATE" class="form-control select2-field teacherID_class">
                <option value="0"><?=$this->lang->line('routine_select_teacher')?></option>
            </select>
        </td>
        <td><input type="text" name="start_time[TEMPLATE]" id="start_time_TEMPLATE" class="form-control timepicker"></td>
        <td><input type="text" name="end_time[TEMPLATE]" id="end_time_TEMPLATE" class="form-control timepicker"></td>
        <td><input type="text" name="room[TEMPLATE]" id="room_TEMPLATE" class="form-control"></td>
        <td><button type="button" class="btn-remove remove_row"><i class="fa fa-trash"></i></button></td>
    </tr>
</table>
