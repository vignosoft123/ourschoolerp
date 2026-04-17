<?php
    $days = [
        0 => $this->lang->line('sunday'),
        1 => $this->lang->line('monday'),
        2 => $this->lang->line('tuesday'),
        3 => $this->lang->line('wednesday'),
        4 => $this->lang->line('thursday'),
        5 => $this->lang->line('friday'),
        6 => $this->lang->line('saturday'),
    ];
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-routine"></i> <?php //echo $this->lang->line('panel_title');?>Timetable</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_routine')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="page-header">
                    <?php if(permissionChecker('routine_add')) { ?>
                        <a class="ose-btn create-btn" href="<?php echo base_url('routine/add') ?>">
                            <i class="fa fa-plus"></i> Timetable
                            <?php //echo $this->lang->line('add_title')?>
                        </a>
                    <?php } ?>
                    <?php if(isset($sections) && count($sections) > 0) { ?>
                        <button type="button" class="btn btn-warning btn-sm" id="btn_copy_timetable" style="margin-left:8px;" data-toggle="modal" data-target="#copyTimetableModal">
                            <i class="fa fa-clone"></i> Copy Timetable
                        </button>
                    <?php } ?>

                    <?php if($this->session->userdata('usertypeID') != 3) { ?>
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 pull-right drop-marg">
                            <?php
                                $array = array("0" => $this->lang->line("routine_select_classes"));
                                foreach ($classes as $classa) {
                                    $array[$classa->classesID] = $classa->classes;
                                }
                                echo form_dropdown("classesID", $array, set_value("classesID", $set), "id='classesID' class='form-control select2'");
                            ?>
                        </div>
                    <?php } ?>
                </h5>

                                <input type="hidden" value="<?= $this->uri->segment(3)?>" id="class_id">
                                <input type="hidden" value="<?= $this->uri->segment(3)?>" id="classid">


                <?php 
                //echo "<pre>";print_r($routines);die;
                if(customCompute($routines) > 0 ) { ?>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <?php /* All Routines tab hidden – causes confusion when mixed sections shown
                            <li class="active"><a data-toggle="tab" href="#all" aria-expanded="true"><?=$this->lang->line("routine_all_routine")?></a></li>
                            */ ?>
                            <?php foreach ($sections as $key => $section) {
                                $activeClass = ($key === 0) ? 'active' : '';
                                echo '<li class="'.$activeClass.'"><a data-toggle="tab" href="#'. $section->sectionID .'" aria-expanded="'.($key===0?'true':'false').'">'. $this->lang->line("routine_section")." ".$section->section. " ( ". $section->category." )".'</a></li>';
                            } ?>
                        </ul>

                        <div class="tab-content" id="scrolling">
                            <?php /* All Routines pane hidden – see tab comment above
                            <div id="all" class="tab-pane active">
                                ...all routines table...
                            </div>
                            */ ?>

                            <?php foreach ($sections as $key => $section) { ?>
                                <div id="<?=$section->sectionID?>" class="tab-pane <?= ($key === 0) ? 'active' : '' ?>">
                                    <div id="hide-table-2">
                                        <table id="table" class="table table-bordered tableBorder">
                                            <tbody>
                                                <?php
                                                    if(isset($allsection[$section->section]) && customCompute($allsection[$section->section])) {
                                                        $map = function($r) {return $r->day;};
                                                        $count = array_count_values(array_map($map, $allsection[$section->section]));
                                                        $max = max($count);

                                                        $flag = 0;
                                                        foreach ($days as $dayKey => $day) {
                                                            $row_count = 0;
                                                            foreach($allsection[$section->section] as $routine) {
                                                                if($routine->day == $dayKey) {
                                                                    if(!in_array($dayKey, $weekends)) {
                                                                       if($flag == 0) {
                                                                            echo '<tr>';
                                                                            echo '<td>'.$day.'</td>';
                                                                            $flag = 1;
                                                                       }
                                                                        echo '<td class="text-center">';
                                                                            echo $routine->start_time.'-'.$routine->end_time.'<br/>';
                                                                            echo $routine->subject.'<br/>';
                                                                            echo $routine->room.'<br/>';
                                                                            echo $routine->name.'<br/>';
                                                                            if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                                                                                if(permissionChecker('routine_edit')) {
                                                                                    echo btn_edit('routine/edit/'.$routine->routineID.'/'.$set, $this->lang->line('edit'));
                                                                                }

                                                                                if(permissionChecker('routine_delete')) {
                                                                                    echo btn_delete('routine/delete/'.$routine->routineID.'/'.$set, $this->lang->line('delete'));
                                                                                }
                                                                            }
                                                                        echo '</td>';
                                                                        $row_count++;
                                                                    }
                                                                }
                                                            }

                                                            if($flag == 1) {
                                                                while($row_count<$max) {
                                                                    echo "<td class='text-center'>N/A</td>";
                                                                    $row_count++;
                                                                }
                                                                echo '</tr>';
                                                                $flag = 0;
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#all" aria-expanded="true"><?=$this->lang->line("routine_all_routine")?></a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="all" class="tab-pane active">
                                <div id="hide-table-2">
                                    <table id="table" class="table table-bordered tableBorder">
                                        <tbody>
                                            <?php
                                                $flag = 0;
                                                foreach ($days as $dayKey => $day) {
                                                    if(!in_array($dayKey, $weekends)) {
                                                        echo '<tr>';
                                                            echo '<td>'.$day.'</td>';
                                                        echo '</tr>';
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Copy Timetable Modal -->
<div class="modal fade" id="copyTimetableModal" tabindex="-1" role="dialog" aria-labelledby="copyTimetableModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#f0f4ff; border-bottom:2px solid #d0d9f0;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="copyTimetableModalLabel">
                    <i class="fa fa-clone" style="color:#337ab7;"></i> &nbsp;Copy Timetable
                </h4>
            </div>
            <div class="modal-body">
                <!-- Tab navigation -->
                <ul class="nav nav-tabs" id="copyModalTabs">
                    <li class="active">
                        <a href="#tab-copy-day" data-toggle="tab">
                            <i class="fa fa-calendar"></i> &nbsp;Copy Day(s)
                        </a>
                    </li>
                    <li>
                        <a href="#tab-copy-section" data-toggle="tab">
                            <i class="fa fa-users"></i> &nbsp;Copy Section(s)
                        </a>
                    </li>
                </ul>

                <div class="tab-content" style="padding-top:20px;">

                    <!-- TAB 1: Copy Day(s) -->
                    <div class="tab-pane active" id="tab-copy-day">
                        <div class="row">
                            <!-- FROM -->
                            <div class="col-md-5">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="background:#e8f4e8;">
                                        <strong><i class="fa fa-arrow-right text-success"></i> &nbsp;Copy From</strong>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label>Section</label>
                                            <select id="day_from_section" class="form-control">
                                                <option value="">All Sections</option>
                                                <?php if(isset($sections)) foreach($sections as $sec): ?>
                                                <option value="<?= $sec->sectionID ?>">
                                                    Section <?= $sec->section ?> (<?= $sec->category ?>)
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="help-block" style="font-size:11px;">Leave as "All Sections" to copy all sections' data for this day.</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Day</label>
                                            <?php foreach($days as $dk => $dy): ?>
                                                <?php if(!in_array($dk, $weekends)): ?>
                                                <div>
                                                    <label style="font-weight:normal;">
                                                        <input type="radio" name="day_from_day" class="day_from_day_radio" value="<?= $dk ?>">
                                                        &nbsp;<?= $dy ?>
                                                    </label>
                                                </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Arrow -->
                            <div class="col-md-2 text-center" style="padding-top:80px;">
                                <i class="fa fa-arrow-right fa-3x" style="color:#337ab7; opacity:0.5;"></i>
                            </div>

                            <!-- TO -->
                            <div class="col-md-5">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="background:#e8eef8;">
                                        <strong><i class="fa fa-arrow-left text-primary"></i> &nbsp;Copy To</strong>
                                    </div>
                                    <div class="panel-body">
                                        <label>Day(s) &nbsp;<small class="text-muted">(select one or more)</small></label>
                                        <?php foreach($days as $dk => $dy): ?>
                                            <?php if(!in_array($dk, $weekends)): ?>
                                            <div>
                                                <label style="font-weight:normal;" class="to_day_label_<?= $dk ?>">
                                                    <input type="checkbox" class="to_day_cb" value="<?= $dk ?>">
                                                    &nbsp;<?= $dy ?>
                                                </label>
                                            </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right" style="margin-top:5px;">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            &nbsp;
                            <button type="button" class="btn btn-success" id="do_copy_day">
                                <i class="fa fa-clone"></i> &nbsp;Copy Day(s)
                            </button>
                        </div>
                    </div>

                    <!-- TAB 2: Copy Section(s) -->
                    <div class="tab-pane" id="tab-copy-section">
                        <div class="row">
                            <!-- FROM -->
                            <div class="col-md-5">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="background:#e8f4e8;">
                                        <strong><i class="fa fa-arrow-right text-success"></i> &nbsp;Copy From</strong>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label>Section</label>
                                            <select id="sec_from_section" class="form-control">
                                                <option value="">-- Select Source Section --</option>
                                                <?php if(isset($sections)) foreach($sections as $sec): ?>
                                                <option value="<?= $sec->sectionID ?>">
                                                    Section <?= $sec->section ?> (<?= $sec->category ?>)
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="help-block" style="font-size:11px;">All days from this section will be copied.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Arrow -->
                            <div class="col-md-2 text-center" style="padding-top:60px;">
                                <i class="fa fa-arrow-right fa-3x" style="color:#337ab7; opacity:0.5;"></i>
                            </div>

                            <!-- TO -->
                            <div class="col-md-5">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="background:#e8eef8;">
                                        <strong><i class="fa fa-arrow-left text-primary"></i> &nbsp;Copy To</strong>
                                    </div>
                                    <div class="panel-body">
                                        <label>Section(s) &nbsp;<small class="text-muted">(select one or more)</small></label>
                                        <?php if(isset($sections)) foreach($sections as $sec): ?>
                                        <div>
                                            <label class="to_sec_label" style="font-weight:normal;" data-sec-id="<?= $sec->sectionID ?>">
                                                <input type="checkbox" class="to_sec_cb" value="<?= $sec->sectionID ?>">
                                                &nbsp;Section <?= $sec->section ?> (<?= $sec->category ?>)
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right" style="margin-top:5px;">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            &nbsp;
                            <button type="button" class="btn btn-primary" id="do_copy_section">
                                <i class="fa fa-clone"></i> &nbsp;Copy Section(s)
                            </button>
                        </div>
                    </div>

                </div><!-- /.tab-content -->
            </div><!-- /.modal-body -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /#copyTimetableModal -->

<script type="text/javascript">
    $('#classesID').change(function() {
        var classesID = $(this).val();
        if(classesID == 0) {
            $('#table').hide();
            $('.nav-tabs-custom').hide();
        } else {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('routine/routine_list')?>",
                data: "id=" + classesID,
                dataType: "html",
                success: function(data) {
                    window.location.href = data;
                }
            });
        }
    });

    $('.select2').select2();
    var mainWidth = $('html').width();
    if(mainWidth >= 980) {
        $('.tab-pane').mCustomScrollbar({
            axis:"x"
        });
    }

    // ── Helper: disable a section in the "To" list ──────────────────────────
    function refreshSectionToList() {
        var fromVal = $('#sec_from_section').val();
        $('.to_sec_cb').each(function(){
            var isSrc = ($(this).val() == fromVal && fromVal !== '');
            $(this).prop('checked', false).prop('disabled', isSrc);
            $(this).closest('label').css('opacity', isSrc ? '0.4' : '1');
        });
    }

    // Run on every modal open so initial state is always correct
    $('#copyTimetableModal').on('show.bs.modal', function(){
        refreshSectionToList();
    });

    // Re-run when the "From Section" dropdown changes (Tab 2)
    $('#sec_from_section').on('change', function(){
        refreshSectionToList();
    });

    // ── Tab 1: Auto-uncheck "To Day" that matches the selected "From Day" ───
    $(document).on('change', '.day_from_day_radio', function(){
        var fromDay = $(this).val();
        $('.to_day_cb').each(function(){
            if($(this).val() == fromDay) {
                $(this).prop('checked', false);
            }
        });
    });

    // ── Tab 1: Copy Day(s) ───────────────────────────────────────────────────
    $('#do_copy_day').on('click', function(){
        var from_day = $('input[name="day_from_day_radio"]:checked').val();
        // support both name variants
        if(!from_day) {
            from_day = $('.day_from_day_radio:checked').val();
        }
        if(!from_day) {
            alert('Please select a source day.');
            return;
        }

        var to_days = [];
        $('.to_day_cb:checked').each(function(){ to_days.push($(this).val()); });
        if(to_days.length === 0) {
            alert('Please select at least one destination day.');
            return;
        }

        var section_id  = $('#day_from_section').val();
        var class_id    = $('#classid').val();

        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Copying...');
        var btn = $(this);

        $.ajax({
            type: 'post',
            url: "<?php echo site_url('Routine/copy_timetable'); ?>",
            data: { from: from_day, days: to_days, class_id: class_id, section_id: section_id },
            success: function(response) {
                $('#copyTimetableModal').modal('hide');
                alert('Day(s) copied successfully.');
                window.location.reload();
            },
            error: function() {
                alert('An error occurred. Please try again.');
                btn.prop('disabled', false).html('<i class="fa fa-clone"></i> &nbsp;Copy Day(s)');
            }
        });
    });

    // ── Tab 2: Copy Section(s) ───────────────────────────────────────────────
    $('#do_copy_section').on('click', function(){
        var from_section = $('#sec_from_section').val();
        if(!from_section) {
            alert('Please select a source section.');
            return;
        }

        var to_sections = [];
        $('.to_sec_cb:checked').each(function(){ to_sections.push($(this).val()); });
        if(to_sections.length === 0) {
            alert('Please select at least one destination section.');
            return;
        }

        var class_id = $('#classid').val();

        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Copying...');
        var btn = $(this);

        $.ajax({
            type: 'post',
            url: "<?php echo site_url('Routine/copy_section_timetable'); ?>",
            data: { from_section: from_section, to_sections: to_sections, class_id: class_id },
            success: function(response) {
                $('#copyTimetableModal').modal('hide');
                alert('Section(s) copied successfully.');
                window.location.reload();
            },
            error: function() {
                alert('An error occurred. Please try again.');
                btn.prop('disabled', false).html('<i class="fa fa-clone"></i> &nbsp;Copy Section(s)');
            }
        });
    });
</script>
