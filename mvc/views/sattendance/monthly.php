<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-sattendance"></i> Add Attendance By Month</h3>

        <ol class="breadcrumb">
            <li><a href="<?= base_url("dashboard/index") ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
            <li><a href="<?= base_url("sattendance/index") ?>"><?= $this->lang->line('menu_sattendance') ?></a></li>
            <li class="active">Add Attendance By Month</li>
        </ol>
    </div><!-- /.box-header -->

    <div class="box-body">
        <div class="row filter-box">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?= $this->lang->line('attendance_classes') ?> <span class="text-red">*</span></label>
                            <?php
                                $classesArray = array("0" => $this->lang->line("attendance_select_classes"));
                                foreach ($classes as $classa) {
                                    $classesArray[$classa->classesID] = $classa->classes;
                                }
                                echo form_dropdown("classesID", $classesArray, "0", "id='classesID' class='form-control select2'");
                            ?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?= $this->lang->line('attendance_section') ?> <span class="text-red">*</span></label>
                            <?php
                                $sectionArray = array('0' => $this->lang->line("attendance_select_section"));
                                echo form_dropdown("sectionID", $sectionArray, "0", "id='sectionID' class='form-control select2'");
                            ?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Month <span class="text-red">*</span></label>
                            <?php
                                $monthArray = array();
                                foreach ($months as $monthNum => $monthName) {
                                    $monthArray[$monthNum] = $monthName;
                                }
                                echo form_dropdown("month", $monthArray, $selectedMonth, "id='month' class='form-control select2'");
                            ?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Year <span class="text-red">*</span></label>
                            <?php
                                $yearArray = array();
                                for ($y = $selectedYear - 1; $y <= $selectedYear + 1; $y++) {
                                    $yearArray[$y] = $y;
                                }
                                echo form_dropdown("year", $yearArray, $selectedYear, "id='year' class='form-control select2'");
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <button type="button" id="load_monthly_grid" class="btn btn-primary col-md-12" style="margin-top: 24px;">
                        <i class="fa fa-table"></i> Load Grid
                    </button>
                </div>
            </div>
        </div>

        <div id="monthly_grid_legend" style="display:none; margin:10px 0;">
            <span style="margin-right:16px;"><span class="legend-badge" style="background:#2ecc71;">P</span> Present</span>
            <span style="margin-right:16px;"><span class="legend-badge" style="background:#e74c3c;">A</span> Absent</span>
            <span style="margin-right:16px;"><span style="display:inline-block; width:12px; height:12px; background:#eee; border:1px solid #ccc; vertical-align:middle;"></span> Holiday / Weekly-off (not editable)</span>
            <span><i class="fa fa-check" style="color:#2ecc71;"></i> / <i class="fa fa-times" style="color:#e74c3c;"></i> above a date = mark the checked students Present / Absent for that date (check at least one student first)</span>
        </div>

        <div id="monthly_grid_container"></div>
    </div><!-- Body -->
</div><!-- /.box -->

<style>
    .monthly-grid-wrap { overflow-x: auto; width: 100%; border: 1px solid #ddd; }
    #monthly_attendance_table { border-collapse: collapse; font-size: 11px; margin: 0; }
    #monthly_attendance_table th, #monthly_attendance_table td {
        border: 1px solid #eee; text-align: center; padding: 2px; white-space: nowrap;
    }
    #monthly_attendance_table th.checkbox-col, #monthly_attendance_table td.checkbox-col {
        position: sticky; left: 0; background: #fff; z-index: 2; width: 30px;
    }
    #monthly_attendance_table thead th.checkbox-col { z-index: 3; background: #f9f9f9; }
    #monthly_attendance_table th.student-col, #monthly_attendance_table td.student-col {
        position: sticky; left: 30px; background: #fff; z-index: 2; text-align: left;
        padding: 4px 8px; min-width: 170px; max-width: 220px; overflow: hidden; text-overflow: ellipsis;
        border-right: 2px solid #ccc;
    }
    #monthly_attendance_table thead th.student-col { z-index: 3; background: #f9f9f9; }
    #monthly_attendance_table td.day-col { width: 34px; }
    #monthly_attendance_table th.non-school-day, #monthly_attendance_table td.non-school-day {
        background: #f0f0f0; color: #999;
    }
    .attendance-toggle-cell { display: inline-flex; gap: 3px; align-items: center; justify-content: center; }
    .attendance-toggle-btn {
        cursor: pointer; font-size: 11px; font-weight: 700; padding: 1px 5px; border-radius: 3px;
        border: 1px solid #ddd; color: #bbb; background: #fafafa; line-height: 1.4; user-select: none;
    }
    .attendance-toggle-btn.present.active { color: #fff; background: #2ecc71; border-color: #27ae60; }
    .attendance-toggle-btn.absent.active { color: #fff; background: #e74c3c; border-color: #c0392b; }
    .attendance-toggle-btn.present:not(.active):hover { border-color: #2ecc71; color: #2ecc71; }
    .attendance-toggle-btn.absent:not(.active):hover { border-color: #e74c3c; color: #e74c3c; }
    .attendance-cell-saving { opacity: 0.4; }
    .legend-badge {
        display: inline-block; color: #fff; font-size: 11px; font-weight: 700; border-radius: 3px;
        padding: 1px 5px; margin-right: 3px;
    }
    .bulk-mark-cell { display: inline-flex; gap: 2px; }
    .mark-all-present-btn, .mark-all-absent-btn {
        display: inline-block; border-radius: 3px; padding: 1px 4px;
    }
    .mark-all-present-btn.disabled, .mark-all-absent-btn.disabled {
        cursor: not-allowed; color: #ccc; background: transparent;
    }
    .mark-all-present-btn.enabled {
        cursor: pointer; color: #1a7a1a; background: #e3f7e8;
    }
    .mark-all-present-btn.enabled:hover { background: #c9f0d1; }
    .mark-all-absent-btn.enabled {
        cursor: pointer; color: #a00; background: #fbe3e3;
    }
    .mark-all-absent-btn.enabled:hover { background: #f5c6c6; }
</style>

<script type="text/javascript">
    $('.select2').select2();

    $('#classesID').on('change', function() {
        var classesID = $(this).val();
        $('#sectionID').html("<option value='0'><?= $this->lang->line("attendance_select_section") ?></option>");
        if (parseInt(classesID)) {
            $.ajax({
                type: 'POST',
                url: "<?= base_url('sattendance/sectionall') ?>",
                data: { "id": classesID },
                dataType: "html",
                success: function(data) {
                    $('#sectionID').html(data);
                }
            });
        }
    });

    $('#load_monthly_grid').on('click', function() {
        var classesID = $('#classesID').val();
        var sectionID = $('#sectionID').val();
        var month = $('#month').val();
        var year = $('#year').val();

        if (!parseInt(classesID) || !parseInt(sectionID)) {
            toastr["error"]("Please select class and section");
            return;
        }

        $('#monthly_grid_container').html('<div style="padding:20px; text-align:center; color:#999;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');

        $.ajax({
            type: 'POST',
            url: "<?= base_url('sattendance/getMonthlyGrid') ?>",
            data: { classesID: classesID, sectionID: sectionID, month: month, year: year },
            dataType: "html",
            success: function(data) {
                var response = JSON.parse(data);
                if (response.status) {
                    $('#monthly_grid_container').html(response.render);
                    $('#monthly_grid_legend').show();
                    updateBulkMarkButtonsState();
                } else {
                    $('#monthly_grid_container').html('<div class="callout callout-danger">' + response.message + '</div>');
                    $('#monthly_grid_legend').hide();
                }
            }
        });
    });

    // Delegated - the grid itself is re-rendered on every "Load Grid" click, so the
    // handler has to live on a container that survives that swap, not on the buttons.
    $(document).on('click', '.attendance-toggle-btn', function() {
        var $btn = $(this);
        if ($btn.hasClass('active')) {
            return; // already saved as this value - nothing to do
        }

        var $cell = $btn.closest('.attendance-toggle-cell');
        var attendanceID = $cell.data('attendanceid');
        var day = $cell.data('day');
        var value = $btn.data('value');

        $cell.addClass('attendance-cell-saving');

        $.ajax({
            type: 'POST',
            url: "<?= base_url('sattendance/saveMonthlyCell') ?>",
            data: { attendanceID: attendanceID, day: day, value: value },
            dataType: "html",
            success: function(data) {
                $cell.removeClass('attendance-cell-saving');
                var response = JSON.parse(data);
                if (response.status) {
                    $cell.find('.attendance-toggle-btn').removeClass('active');
                    $btn.addClass('active');
                } else {
                    toastr["error"](response.message || "Could not save");
                }
            },
            error: function() {
                $cell.removeClass('attendance-cell-saving');
                toastr["error"]("Could not save - please try again");
            }
        });
    });

    // The header tick/cross only ever act on checked students - they start disabled,
    // and flip on the moment at least one student-row-check is ticked below them.
    function updateBulkMarkButtonsState() {
        var anyChecked = $('.student-row-check:checked').length > 0;
        $('.mark-all-present-btn')
            .toggleClass('enabled', anyChecked)
            .toggleClass('disabled', !anyChecked)
            .attr('title', anyChecked ? 'Mark checked students Present for this date' : 'Select at least one student below to enable');
        $('.mark-all-absent-btn')
            .toggleClass('enabled', anyChecked)
            .toggleClass('disabled', !anyChecked)
            .attr('title', anyChecked ? 'Mark checked students Absent for this date' : 'Select at least one student below to enable');
    }

    $(document).on('change', '.student-row-check', function() {
        var total   = $('.student-row-check').length;
        var checked = $('.student-row-check:checked').length;
        $('#select-all-students')
            .prop('checked', checked === total)
            .prop('indeterminate', checked > 0 && checked < total);
        updateBulkMarkButtonsState();
    });

    $(document).on('change', '#select-all-students', function() {
        $('.student-row-check').prop('checked', $(this).prop('checked'));
        updateBulkMarkButtonsState();
    });

    // Shared by the header tick and cross - marks only the checked students
    // Present/Absent for one date, one batch save covering just their
    // attendanceIDs, not the whole column.
    function bulkMarkSelectedStudents(day, label, url) {
        var $checkedRows = $('.student-row-check:checked').closest('tr');
        var attendanceIDs = [];
        $checkedRows.each(function() {
            var id = $(this).data('attendanceid');
            if (id) { attendanceIDs.push(id); }
        });

        if (!attendanceIDs.length) {
            return;
        }

        if (!confirm('Mark ' + attendanceIDs.length + ' selected student(s) ' + label + ' for day ' + day + '?')) {
            return;
        }

        var $targetCells = $checkedRows.find('.attendance-toggle-cell[data-day="' + day + '"]');
        $targetCells.addClass('attendance-cell-saving');

        $.ajax({
            type: 'POST',
            url: url,
            data: { attendanceIDs: attendanceIDs, day: day },
            dataType: "html",
            success: function(data) {
                $targetCells.removeClass('attendance-cell-saving');
                var response = JSON.parse(data);
                if (response.status) {
                    var activeClass = (label === 'Present') ? 'present' : 'absent';
                    $targetCells.each(function() {
                        $(this).find('.attendance-toggle-btn').removeClass('active');
                        $(this).find('.attendance-toggle-btn.' + activeClass).addClass('active');
                    });
                    toastr["success"]("Marked " + attendanceIDs.length + " student(s) " + label + " for day " + day);
                } else {
                    toastr["error"](response.message || "Could not save");
                }
            },
            error: function() {
                $targetCells.removeClass('attendance-cell-saving');
                toastr["error"]("Could not save - please try again");
            }
        });
    }

    $(document).on('click', '.mark-all-present-btn', function() {
        if (!$(this).hasClass('enabled')) { return; }
        bulkMarkSelectedStudents($(this).data('day'), 'Present', "<?= base_url('sattendance/saveMonthlySelectedPresent') ?>");
    });

    $(document).on('click', '.mark-all-absent-btn', function() {
        if (!$(this).hasClass('enabled')) { return; }
        bulkMarkSelectedStudents($(this).data('day'), 'Absent', "<?= base_url('sattendance/saveMonthlySelectedAbsent') ?>");
    });
</script>
