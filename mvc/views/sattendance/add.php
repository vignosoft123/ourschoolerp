
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-sattendance"></i> <?= $this->lang->line('panel_title') ?></h3>


        <ol class="breadcrumb">
            <li><a href="<?= base_url("dashboard/index") ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
            <li><a href="<?= base_url("sattendance/index") ?>"><?= $this->lang->line('menu_sattendance') ?></a></li>
            <li class="active"><?= $this->lang->line('menu_add') ?> <?= $this->lang->line('menu_sattendance') ?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php if ($setting->attendance == "subject") { ?>
                    <form method="POST">
                        <div class="row filter-box">
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="<?php echo form_error('classesID') ? 'form-group has-error' : 'form-group'; ?>">
                                            <label class="control-label"><?= $this->lang->line('attendance_classes') ?> <span class="text-red">*</span></label>
                                            <?php
                                            $classArray = array("0" => $this->lang->line("attendance_select_classes"));
                                            if (customCompute($classes)) {
                                                foreach ($classes as $classa) {
                                                    $classArray[$classa->classesID] = $classa->classes;
                                                }
                                            }
                                            echo form_dropdown("classesID", $classArray, set_value("classesID", $set), "id='classesID' class='form-control select2'");
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="<?php echo form_error('sectionID') ? 'form-group has-error' : 'form-group'; ?>">
                                            <label class="control-label"><?= $this->lang->line('attendance_section') ?> <span class="text-red">*</span></label>
                                            <?php
                                            $sectionArray = array('0' => $this->lang->line("attendance_select_section"));
                                            if (customCompute($sections)) {
                                                foreach ($sections as $section) {
                                                    $sectionArray[$section->sectionID] = $section->section;
                                                }
                                            }
                                            echo form_dropdown("sectionID", $sectionArray, set_value("sectionID", $sectionID), "id='sectionID' class='form-control select2'");
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="<?php echo form_error('subjectID') ? 'form-group has-error' : 'form-group'; ?>">
                                            <label class="control-label"><?= $this->lang->line('attendance_subject') ?> <span class="text-red">*</span></label>
                                            <?php
                                            $subjectArray = array('0' => $this->lang->line("attendance_select_subject"));
                                            if (customCompute($subjects)) {
                                                foreach ($subjects as $subject) {
                                                    $subjectArray[$subject->subjectID] = $subject->subject;
                                                }
                                            }
                                            echo form_dropdown("subjectID", $subjectArray, set_value("subjectID", $subjectID), "id='subjectID' class='form-control select2'");
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="<?php echo form_error('date') ? 'form-group has-error' : 'form-group'; ?>">
                                            <label class="control-label"><?= $this->lang->line('attendance_date') ?> <span class="text-red">*</span></label>
                                            <input type="text" class="form-control" name="date" id="date" value="<?= set_value("date", $date) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary col-md-12" style="margin-top: 24px;"><?= $this->lang->line('add_attendance') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    

                <?php } else { ?>
                    <form method="POST">
                        <div class="row filter-box">
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="<?php echo form_error('classesID') ? 'form-group has-error' : 'form-group'; ?>">
                                            <label class="control-label"><?= $this->lang->line('attendance_classes') ?> <span class="text-red">*</span></label>

                                            <?php
                                            $classesArray = array("0" => $this->lang->line("attendance_select_classes"));
                                            foreach ($classes as $classa) {
                                                $classesArray[$classa->classesID] = $classa->classes;
                                            }
                                            echo form_dropdown("classesID", $classesArray, set_value("classesID", $set), "id='classesID' class='form-control select2'");
                                            ?>

                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="<?php echo form_error('sectionID') ? 'form-group has-error' : 'form-group'; ?>">
                                            <label class="control-label"><?= $this->lang->line('attendance_section') ?> <span class="text-red">*</span></label>
                                            <?php
                                            $sectionArray = array('0' => $this->lang->line("attendance_select_section"));
                                            if (customCompute($sections)) {
                                                foreach ($sections as $section) {
                                                    $sectionArray[$section->sectionID] = $section->section;
                                                }
                                            }
                                            echo form_dropdown("sectionID", $sectionArray, set_value("sectionID", $sectionID), "id='sectionID' class='form-control select2'");
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="<?php echo form_error('date') ? 'form-group has-error' : 'form-group'; ?>">
                                            <label class="control-label"><?= $this->lang->line('attendance_date') ?> <span class="text-red">*</span></label>
                                            <input type="text" class="form-control" name="date" id="date" value="<?= set_value("date", $date) ?>">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary col-md-12" style="margin-top: 24px;"><?= $this->lang->line('add_attendance') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php } ?>

            </div>
            <div class="col-sm-12">
                <?php if (customCompute($students)) { ?>

                    <div id="bulk-action-bar" class="well well-sm" style="margin-bottom: 10px;">
                        <div style="display: inline-block; margin-right: 12px;">
                            <strong>Mark All As:</strong>&nbsp;
                            <button type="button" class="btn btn-success btn-sm bulk-mark-all" data-val="P"><i class="fa fa-check"></i> Present</button>&nbsp;
                            <button type="button" class="btn btn-danger btn-sm bulk-mark-all" data-val="A"><i class="fa fa-times"></i> Absent</button>&nbsp;
                            <button type="button" class="btn btn-warning btn-sm bulk-mark-all" data-val="M">Morning - Present</button>&nbsp;
                            <button type="button" class="btn btn-info btn-sm bulk-mark-all" data-val="AF">Afternoon - Present</button>
                        </div>
                        <div id="selected-bulk-panel" style="display: none; margin-top: 8px; padding-top: 8px; border-top: 1px solid #ddd;">
                            <span id="selected-count" style="font-weight: 600; margin-right: 10px; color: #337ab7;"></span>
                            <strong>Mark Selected As:</strong>&nbsp;
                            <button type="button" class="btn btn-success btn-sm bulk-mark-selected" data-val="P"><i class="fa fa-check"></i> Present</button>&nbsp;
                            <button type="button" class="btn btn-danger btn-sm bulk-mark-selected" data-val="A"><i class="fa fa-times"></i> Absent</button>&nbsp;
                            <button type="button" class="btn btn-warning btn-sm bulk-mark-selected" data-val="M">Morning - Present</button>&nbsp;
                            <button type="button" class="btn btn-info btn-sm bulk-mark-selected" data-val="AF">Afternoon - Present</button>&nbsp;
                            <button type="button" class="btn btn-default btn-sm" id="clear-selection"><i class="fa fa-remove"></i> Clear Selection</button>
                        </div>
                    </div>

                    <div id="hide-table">
                        <table class="table tableBorder table-bordered table-hover" id="attendance_table">
                            <thead>
                                <tr>
                                    <th class="col-sm-1" style="width: 40px; text-align: center;">
                                        <input type="checkbox" id="select-all-check" title="Select All">
                                    </th>
                                    <th class="col-sm-1"><?= $this->lang->line('slno') ?></th>
                                    <th class="col-sm-1"><?= $this->lang->line('attendance_photo') ?></th>
                                    <th class="col-sm-2"><?= $this->lang->line('attendance_name') ?></th>
                                    <th class="col-sm-1"><?= $this->lang->line('attendance_phone') ?></th>
                                    <th class="col-sm-1">Whatsapp No</th>
                                    <th class="col-sm-1">Village</th>
                                    <th class="col-sm-1">Student Type</th>
                                    <th class="col-sm-1"><?= $this->lang->line('attendance_roll') ?></th>
                                    <th class="col-sm-5">
                                        <?= $this->lang->line('attendance_attendance') ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="list">
                                <?php if (customCompute($students)) {
                                    $i = 1;
                                    foreach ($students as $student) {
                                        if (isset($attendances[$student->studentID])) { ?>
                                            <tr data-studenttype="<?= $student->studentType ?>" data-attendanceid="<?= $attendances[$student->studentID]->attendanceID ?>">
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <input type="checkbox" class="student-row-check">
                                                </td>
                                                <td data-title="<?= $this->lang->line('slno') ?>">
                                                    <?php echo $i; ?>
                                                </td>
                                                <td data-title="<?= $this->lang->line('attendance_photo') ?>">
                                                    <?= profileproimage($student->photo) ?>
                                                </td>
                                                <td data-title="<?= $this->lang->line('attendance_name') ?>">
                                                    <?php echo $student->name; ?>  (<?= $student->roll ?>)
                                                </td>
                                                <td data-title="<?= $this->lang->line('attendance_phone') ?>">
                                                    <?php echo $student->phone; ?>
                                                </td>
                                                <td data-title="Whatsapp No">
                                                    <?php echo $student->alternative_phone1; ?>
                                                </td>
                                                <td data-title="Village">
                                                    <?php echo $student->villageName; ?>
                                                </td>
                                                <?php
                                                $studentType = array('' => 'Select Student Type', 1 => "TRANSPORT", 2 => "HOSTEL", 3 => "DAY SCHOLAR", 0 => '');
                                                ?>
                                                <td data-title="<?= $this->lang->line('studentType') ?>">
                                                    <?php echo  $studentType[$student->studentType]; ?>
                                                </td>
                                                <td data-title="<?= $this->lang->line('attendance_roll') ?>">
                                                    <?php echo $student->srroll; ?>
                                                </td>
                                                <td class="studentID" data-studentid="<?= $student->studentID ?>" data-title="<?= $this->lang->line('attendance_attendance') ?>">
                                                    <?php
                                                    $aday = "a" . abs($day);
                                                    if (isset($attendances[$student->studentID])) {
                                                        if ($setting->attendance == "subject") {
                                                            if ($monthyear == $attendances[$student->studentID]->monthyear && $attendances[$student->studentID]->studentID == $student->srstudentID && $attendances[$student->studentID]->classesID == $student->srclassesID && $attendances[$student->studentID]->subjectID == $subjectID) {
                                                                $pmethod = '';
                                                                $lemethod = '';
                                                                $lmethod = '';
                                                                $amethod = '';

                                                                if ($attendances[$student->studentID]->$aday == "P") {
                                                                    $pmethod = "checked";
                                                                } elseif ($attendances[$student->studentID]->$aday == "LE") {
                                                                    $lemethod = "checked";
                                                                } elseif ($attendances[$student->studentID]->$aday == "L") {
                                                                    $lmethod = "checked";
                                                                } elseif ($attendances[$student->studentID]->$aday == "A") {
                                                                    $amethod = "checked";
                                                                } else {
                                                                    $pmethod = "checked";
                                                                }

                                                                echo  btn_attendance_radio($attendances[$student->studentID]->attendanceID . '-1', $pmethod, "attendance btn btn-warning present", "attendance" . $attendances[$student->studentID]->attendanceID, $this->lang->line('sattendance_present'), 'P');

                                                                // echo  btn_attendance_radio($attendances[$student->studentID]->attendanceID . '-2', $lemethod, "attendance btn btn-warning lateexcuse", "attendance" . $attendances[$student->studentID]->attendanceID, $this->lang->line('sattendance_late_excuse'), 'LE');

                                                                // echo  btn_attendance_radio($attendances[$student->studentID]->attendanceID . '-3', $lmethod, "attendance btn btn-warning late", "attendance" . $attendances[$student->studentID]->attendanceID, $this->lang->line('sattendance_late_present'), 'L');

                                                                echo  btn_attendance_radio($attendances[$student->studentID]->attendanceID . '-4', $amethod, "attendance btn btn-warning absent", "attendance" . $attendances[$student->studentID]->attendanceID, $this->lang->line('sattendance_absent'), 'A');
                                                            }
                                                        } else {
                                                            if ($monthyear == $attendances[$student->studentID]->monthyear && $attendances[$student->studentID]->studentID == $student->srstudentID && $attendances[$student->studentID]->classesID == $student->srclassesID) {
                                                                $pmethod = '';
                                                                $lemethod = '';
                                                                $lmethod = '';
                                                                $amethod = '';
                                                                $mmethod = '';
                                                                $nmethod = '';

                                                                if ($attendances[$student->studentID]->$aday == "P") {
                                                                    $pmethod = "checked";
                                                                } elseif ($attendances[$student->studentID]->$aday == "LE") {
                                                                    $lemethod = "checked";
                                                                } elseif ($attendances[$student->studentID]->$aday == "L") {
                                                                    $lmethod = "checked";
                                                                } elseif ($attendances[$student->studentID]->$aday == "A") {
                                                                    $amethod = "checked";
                                                                }elseif ($attendances[$student->studentID]->$aday == "M") {
                                                                    $mmethod = "checked";
                                                                }elseif ($attendances[$student->studentID]->$aday == "N") {
                                                                    $nmethod = "checked";
                                                                } else {
                                                                    $pmethod = "checked";
                                                                }
                                                                
                                                                  // echo  btn_attendance_radio($attendances[$student->studentID]->attendanceID . '-1', $pmethod, "attendance btn btn-warning present", "attendance" . $attendances[$student->studentID]->attendanceID, $this->lang->line('sattendance_present'), 'P');

                                                                // echo  btn_attendance_radio($attendances[$student->studentID]->attendanceID . '-4', $amethod, "attendance btn btn-warning absent", "attendance" . $attendances[$student->studentID]->attendanceID, $this->lang->line('sattendance_absent'), 'A');

                                                                // HALF DAYS
                                                                // echo  btn_attendance_radio($attendances[$student->studentID]->attendanceID . '-5', $mmethod, "attendance btn btn-warning morning", "attendance" . $attendances[$student->studentID]->attendanceID, "Morning", 'M');

                                                                // echo  btn_attendance_radio($attendances[$student->studentID]->attendanceID . '-6', $nmethod, "attendance btn btn-warning evening", "attendance" . $attendances[$student->studentID]->attendanceID, "Afternoon", 'AF');

                                                                $attendanceValue = $attendances[$student->studentID]->$aday ?? 'P'; // default to 'P'

                                                                ?>

                                                                <div class='filter-box1'>
                                                              

                                                                <div class="card mb-3 shadow-sm p-3">
    <!-- <h5><?= $student->name ?> (<?= $student->roll ?>)</h5> -->

    <div class="form-group d-flex align-items-center gap-3 mb-2">
        <!-- Full Day Radio -->
        <label class="btn btn-outline-success mb-0">
            <input type="radio" name="attendance[<?= $student->studentID ?>]" value="P"
    class="attendance-radio attendance" data-studentid="<?= $attendances[$student->studentID]->attendanceID ?>"
    <?= $attendanceValue == 'P' ? 'checked' : '' ?>> Present
        </label>

        <!-- Absent Radio -->
        <label class="btn btn-outline-danger mb-0">
           <input type="radio" name="attendance[<?= $student->studentID ?>]" value="A"
    class="attendance-radio attendance" data-studentid="<?= $attendances[$student->studentID]->attendanceID ?>"
    <?= $attendanceValue == 'A' ? 'checked' : '' ?>> Absent
        </label>

        <!-- Morning/Afternoon Dropdown -->
      <select name="halfday[<?= $student->studentID ?>]" class="form-control halfday-dropdown attendance"
    data-studentid="<?= $attendances[$student->studentID]->attendanceID ?>">
    <option value="">-- Select --</option>
    <option value="M" <?= $attendanceValue == 'M' ? 'selected' : '' ?>>Morning - Present</option>
    <option value="AF" <?= $attendanceValue == 'AF' ? 'selected' : '' ?>>Afternoon - Present</option>
</select>

    </div>
</div>


                                                            <?php  echo "</div>";
                                                                   // echo  btn_attendance_radio($attendances[$student->studentID]->attendanceID . '-2', $lemethod, "attendance btn btn-warning lateexcuse", "attendance" . $attendances[$student->studentID]->attendanceID, $this->lang->line('sattendance_late_excuse'), 'LE');

                                                                // echo  btn_attendance_radio($attendances[$student->studentID]->attendanceID . '-3', $lmethod, "attendance btn btn-warning late", "attendance" . $attendances[$student->studentID]->attendanceID, $this->lang->line('sattendance_late_present'), 'L');
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                <?php $i++;
                                        }
                                    }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="attendance-action-bar" style="
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        z-index: 1050;
                        background: rgba(255,255,255,0.88);
                        backdrop-filter: blur(4px);
                        -webkit-backdrop-filter: blur(4px);
                        border-top: 1px solid #ddd;
                        padding: 10px 24px;
                        text-align: right;
                        box-shadow: 0 -2px 8px rgba(0,0,0,0.08);
                    ">
                        <span class="btn btn-default submit_attendance_only">Submit</span>&nbsp;&nbsp;
                        <span class="btn btn-primary save_attendance">Submit &amp; Send SMS</span>&nbsp;&nbsp;
                        <span class="btn btn-success save_attendance_send_whatsapp">Submit &amp; Send Whatsapp</span>
                    </div>
                    <div style="height: 60px;"></div>
                    <?php } ?>

                    <script type="text/javascript">
                        window.addEventListener('load', function() {
                            setTimeout(lazyLoad, 1000);
                        });

                        function lazyLoad() {
                            var card_images = document.querySelectorAll('.card-image');
                            card_images.forEach(function(card_image) {
                                var image_url = card_image.getAttribute('data-image-full');
                                var content_image = card_image.querySelector('img');
                                content_image.src = image_url;
                                content_image.addEventListener('load', function() {
                                    card_image.style.backgroundImage = 'url(' + image_url + ')';
                                    card_image.className = card_image.className + ' is-loaded';
                                });
                            });
                        }

                        $('.save_attendance').click(function() {
                            var attendance = {};
                            // $('.attendance').each(function(i) {
                            //     var name = $(this).attr('name');
                            //     if ($("input:radio[name=" + name + "]").is(":checked")) {
                            //         var val = $('input:radio[name=' + name + ']:checked').val();
                            //     } else {
                            //         var val = 'A';
                            //     }
                            //     attendance[name] = val;
                            // });
 var attendance = {};

$('.attendance').each(function() {
    var $this = $(this);
    var studentID = $this.data('studentid');
    var key = 'attendance' + studentID;

    if ($this.is(':radio')) {
        if ($this.is(':checked')) {
            attendance[ key ] = $this.val();
        }
    } else if ($this.is('select')) {
        var selectedVal = $this.val();
        if (selectedVal === 'M' || selectedVal === 'AF') {
            attendance[key] = selectedVal;
        }
    }
});

// Optional fallback: mark as 'A' if nothing selected
$('.attendance').each(function() {
    var studentID = $(this).data('studentid');
    var key = 'attendance' + studentID;
    if (attendance[key] === undefined) {
        attendance[ key ] = 'A';
    }
});



                            var day = "<?= $day ?>";
                            var classes = "<?= $set ?>";
                            var section = "<?= $sectionID ?>";
                            var monthyear = "<?= $monthyear ?>";
                            <?php if ($setting->attendance == "subject") { ?>
                                var subjectID = "<?= $subjectID ?>";
                            <?php } else { ?>
                                var subjectID = 0;
                            <?php } ?>

                            if (parseInt(classes) && parseInt(day)) {
                                $.ajax({
                                    type: 'POST',
                                    url: "<?= base_url('sattendance/save_attendace') ?>",
                                    data: {
                                        "day": day,
                                        "classes": classes,
                                        "section": section,
                                        "subject": subjectID,
                                        "monthyear": monthyear,
                                        "attendance": attendance
                                    },
                                    dataType: "html",
                                    success: function(data) {
                                        var response = JSON.parse(data);
                                        if (response.status == true) {
                                            toastr["success"](response.message)
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
                                            $.each(response, function(index, value) {
                                                if (index != 'status') {
                                                    toastr["error"](value)
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
                                            })
                                        }
                                    }
                                });
                            }
                        });

                        $('.submit_attendance_only').click(function() {
                            var attendance = {};
$('.attendance').each(function() {
    var $this = $(this);
    var studentID = $this.data('studentid');
    var key = 'attendance' + studentID;
    if ($this.is(':radio')) {
        if ($this.is(':checked')) { attendance[key] = $this.val(); }
    } else if ($this.is('select')) {
        var selectedVal = $this.val();
        if (selectedVal === 'M' || selectedVal === 'AF') { attendance[key] = selectedVal; }
    }
});
$('.attendance').each(function() {
    var studentID = $(this).data('studentid');
    var key = 'attendance' + studentID;
    if (attendance[key] === undefined) { attendance[key] = 'A'; }
});
                            var day = "<?= $day ?>";
                            var classes = "<?= $set ?>";
                            var section = "<?= $sectionID ?>";
                            var monthyear = "<?= $monthyear ?>";
                            <?php if ($setting->attendance == "subject") { ?>
                                var subjectID = "<?= $subjectID ?>";
                            <?php } else { ?>
                                var subjectID = 0;
                            <?php } ?>
                            if (parseInt(classes) && parseInt(day)) {
                                $.ajax({
                                    type: 'POST',
                                    url: "<?= base_url('sattendance/save_attendace') ?>",
                                    data: {
                                        "day": day,
                                        "classes": classes,
                                        "section": section,
                                        "subject": subjectID,
                                        "monthyear": monthyear,
                                        "attendance": attendance,
                                        "send_whatsapp": 2
                                    },
                                    dataType: "html",
                                    success: function(data) {
                                        var response = JSON.parse(data);
                                        if (response.status == true) {
                                            toastr["success"](response.message);
                                            toastr.options = {"closeButton":true,"positionClass":"toast-top-right","timeOut":"5000"};
                                        } else {
                                            $.each(response, function(index, value) {
                                                if (index != 'status') { toastr["error"](value); }
                                            });
                                        }
                                    }
                                });
                            }
                        });

                        $('.save_attendance_send_whatsapp').click(function() {
                            // var attendance = {};
                            // $('.attendance').each(function(i) {
                            //     var name = $(this).attr('name');
                            //     if ($("input:radio[name=" + name + "]").is(":checked")) {
                            //         var val = $('input:radio[name=' + name + ']:checked').val();
                            //     } else {
                            //         var val = 'A';
                            //     }
                            //     attendance[name] = val;
                            // });

                            var attendance = {};

$('.attendance').each(function() {
    var $this = $(this);
    var studentID = $this.data('studentid');
    var key = 'attendance' + studentID;

    if ($this.is(':radio')) {
        if ($this.is(':checked')) {
            attendance[ key ] = $this.val();
        }
    } else if ($this.is('select')) {
        var selectedVal = $this.val();
        if (selectedVal === 'M' || selectedVal === 'AF') {
            attendance[key] = selectedVal;
        }
    }
});

// Optional fallback: mark as 'A' if nothing selected
$('.attendance').each(function() {
    var studentID = $(this).data('studentid');
    var key = 'attendance' + studentID;
    if (attendance[key] === undefined) {
        attendance[ key ] = 'A';
    }
});


                            var day = "<?= $day ?>";
                            var classes = "<?= $set ?>";
                            var section = "<?= $sectionID ?>";
                            var monthyear = "<?= $monthyear ?>";
                            <?php if ($setting->attendance == "subject") { ?>
                                var subjectID = "<?= $subjectID ?>";
                            <?php } else { ?>
                                var subjectID = 0;
                            <?php } ?>

                            if (parseInt(classes) && parseInt(day)) {
                                $.ajax({
                                    type: 'POST',
                                    url: "<?= base_url('sattendance/save_attendace') ?>",
                                    data: {
                                        "day": day,
                                        "classes": classes,
                                        "section": section,
                                        "subject": subjectID,
                                        "monthyear": monthyear,
                                        "attendance": attendance,
                                        "send_whatsapp" : 1
                                    },
                                    dataType: "html",
                                    success: function(data) {
                                        var response = JSON.parse(data);
                                        if (response.status == true) {
                                            toastr["success"](response.message)
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
                                            $.each(response, function(index, value) {
                                                if (index != 'status') {
                                                    toastr["error"](value)
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
                                            })
                                        }
                                    }
                                });
                            }
                        });
                    </script>
            </div> <!-- col-sm-12 -->
        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->

<button id="scroll-to-top-btn" title="Back to top" style="
    display: none;
    position: fixed;
    bottom: 70px;
    right: 24px;
    z-index: 1100;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: rgba(51,122,183,0.80);
    color: #fff;
    font-size: 18px;
    line-height: 40px;
    text-align: center;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.20);
    transition: opacity 0.3s;
    padding: 0;
">&#8679;</button>

<script type="text/javascript">
    $('.select2').select2();


    $('#date').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy',
        startDate: '<?= $schoolyearsessionobj->startingdate ?>',
        endDate: '<?= $schoolyearsessionobj->endingdate ?>',
        daysOfWeekDisabled: "<?= $siteinfos->weekends ?>",
        datesDisabled: ["<?= $get_all_holidays; ?>"],
    });

    $("#classesID").change(function() {
        var id = $(this).val();
        if (parseInt(id)) {

            <?php if ($setting->attendance == "subject") { ?>
                if (id === '0') {
                    $('#subjectID').val(0);
                } else {
                    $.ajax({
                        type: 'POST',
                        url: "<?= base_url('sattendance/subjectall') ?>",
                        data: {
                            "id": id
                        },
                        dataType: "html",
                        success: function(data) {
                            $('#subjectID').html(data);
                        }
                    });
                }
            <?php } ?>

            if (id === '0') {
                $('#sectionID').val(0);
            } else {
                $.ajax({
                    type: 'POST',
                    url: "<?= base_url('sattendance/sectionall') ?>",
                    data: {
                        "id": id
                    },
                    dataType: "html",
                    success: function(data) {
                        $('#sectionID').html(data);
                    }
                });
            }
        }
    });
    
$(document).ready( function () {
    $('#attendance_table').DataTable(
    {
        "paging": false,       // Disable pagination
        "pageLength": -1       // Show all records
    }
);
} )
    

$(document).ready(function() {
    $('.halfday-dropdown').show(); // shown by default since Full Day is selected

    // When radio button changes
    $(document).on('change', '.attendance-radio', function() {
        var studentID = $(this).data('studentid');
        var selectedVal = $(this).val();

        if (selectedVal === 'A') {
            $('select.halfday-dropdown[data-studentid="' + studentID + '"]').hide().val('');
        } else if (selectedVal === 'P') {
            $('select.halfday-dropdown[data-studentid="' + studentID + '"]').show().val('');
        }
    });

    // When dropdown is changed
    $(document).on('change', '.halfday-dropdown', function() {
        var studentID = $(this).data('studentid');
        var selected = $(this).val();

        if (selected === 'M' || selected === 'AF') {
            $('input.attendance-radio[data-studentid="' + studentID + '"]').prop('checked', false);
        }
    });
});


$(document).ready(function() {
    $('.halfday-dropdown').each(function() {
        var selected = $(this).val();
        var studentID = $(this).data('studentid');

        if (selected === 'M' || selected === 'AF') {
            $('input.attendance-radio[data-studentid="' + studentID + '"]').prop('checked', false);
        }

        if (selected === '') {
            // Show if Full Day is selected
            var radioVal = $('input.attendance-radio[data-studentid="' + studentID + '"]:checked').val();
            if (radioVal === 'P') {
                // $(this).show();
            } else {
                // $(this).hide();
            }
        }
    });
});

// ============ BULK ATTENDANCE ACTIONS ============
function applyBulkAttendanceValue(attendanceID, val) {
    var $radios   = $('input.attendance-radio[data-studentid="' + attendanceID + '"]');
    var $dropdown = $('select.halfday-dropdown[data-studentid="' + attendanceID + '"]');
    if (val === 'M' || val === 'AF') {
        $radios.prop('checked', false);
        $dropdown.show().val(val);
    } else if (val === 'P') {
        $radios.filter('[value="P"]').prop('checked', true);
        $radios.filter('[value="A"]').prop('checked', false);
        $dropdown.show().val('');
    } else if (val === 'A') {
        $radios.filter('[value="A"]').prop('checked', true);
        $radios.filter('[value="P"]').prop('checked', false);
        $dropdown.hide().val('');
    }
}

function updateSelectedPanel() {
    var count = $('.student-row-check:checked').length;
    if (count > 0) {
        $('#selected-count').text(count + (count === 1 ? ' student' : ' students') + ' selected —');
        $('#selected-bulk-panel').show();
    } else {
        $('#selected-bulk-panel').hide();
    }
}

// Mark All buttons
$('.bulk-mark-all').on('click', function() {
    var val = $(this).data('val');
    var processed = {};
    $('input.attendance-radio').each(function() {
        var id = $(this).data('studentid');
        if (!processed[id]) {
            processed[id] = true;
            applyBulkAttendanceValue(id, val);
        }
    });
});

// Mark Selected buttons
$('.bulk-mark-selected').on('click', function() {
    var val = $(this).data('val');
    $('.student-row-check:checked').each(function() {
        var attendanceID = $(this).closest('tr').data('attendanceid');
        applyBulkAttendanceValue(attendanceID, val);
    });
});

// Select All checkbox in header
$('#select-all-check').on('change', function() {
    var checked = $(this).prop('checked');
    $('.student-row-check').prop('checked', checked);
    updateSelectedPanel();
});

// Individual row checkbox
$(document).on('change', '.student-row-check', function() {
    var total      = $('.student-row-check').length;
    var checkedCnt = $('.student-row-check:checked').length;
    $('#select-all-check')
        .prop('indeterminate', checkedCnt > 0 && checkedCnt < total)
        .prop('checked', checkedCnt === total);
    updateSelectedPanel();
});

// Clear selection
$('#clear-selection').on('click', function() {
    $('.student-row-check').prop('checked', false);
    $('#select-all-check').prop('checked', false).prop('indeterminate', false);
    updateSelectedPanel();
});

// Scroll to top button
$(window).on('scroll', function() {
    if ($(this).scrollTop() > 200) {
        $('#scroll-to-top-btn').fadeIn(300);
    } else {
        $('#scroll-to-top-btn').fadeOut(300);
    }
});
$('#scroll-to-top-btn').on('click', function() {
    $('html, body').animate({ scrollTop: 0 }, 400);
});

</script>