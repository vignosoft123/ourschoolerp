<?php
// echo "<pre>";print_r($this->session->userdata('usertypeID'));die;
$global_payment_permission = false;
if($this->session->userdata('usertypeID') == 1 || $this->session->userdata('usertypeID') == 5){ //admin & accountant
    $global_payment_permission = true;
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<style>
    label { 
    color: #ffff;
    }
    
    /* Modal labels with dark color for visibility */
    .modal-body label {
        color: #333 !important;
        font-weight: 500;
    }
    
    .modal-body .form-label {
        color: #333 !important;
    }
    /* ── Top action toolbar ── */
    .student-top-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        padding: 14px 18px;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .sbar-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none !important;
        transition: all 0.2s ease;
        line-height: 1.3;
        letter-spacing: 0.2px;
    }
    .sbar-btn:hover {
        text-decoration: none !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 14px rgba(0,0,0,0.18);
        color: #fff !important;
    }
    .sbar-btn i { font-size: 14px; }
    .sbar-btn-add  { background: linear-gradient(135deg, #0cc035 0%, #0a9d2b 100%); color: #fff !important; }
    .sbar-btn-quick{ background: linear-gradient(135deg, #1a73e8 0%, #1558b0 100%); color: #fff !important; }
    .sbar-btn-excel{ background: linear-gradient(135deg, #217346 0%, #155a2e 100%); color: #fff !important; }
    .sbar-btn-delete{ background: linear-gradient(135deg, #e53935 0%, #b71c1c 100%); color: #fff !important; }
    .sbar-btn-yearly{ background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); color: #fff !important; }
    .sbar-btn.sbar-disabled { background: #ccc !important; color: #888 !important; border-color: #ccc !important; cursor: not-allowed !important; transform: none !important; box-shadow: none !important; opacity: 0.7; }
    .sbar-class-wrap { margin-left: auto; display: flex; align-items: center; }
    .sbar-class-wrap select { width: 220px !important; border-radius: 8px !important; font-size: 13px; }

    /* Keep old class names working as fallback */
    .ose-btn1, .ose-btn2 {
        white-space: nowrap;
    }

    /* Enhanced Table Styling */
    #example1 {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #fff !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
        border-radius: 8px !important;
        overflow: hidden !important;
        margin: 15px 0 !important;
        width: 100% !important;
        border: 1px solid #e0e0e0 !important;
    }

    #example1 thead {
        background: linear-gradient(135deg, #1a73e8 0%, #1045a8 100%) !important;
        color: white !important;
    }

    #example1 thead th {
        /* padding: 15px 12px !important; */
        text-align: center !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        letter-spacing: 0.5px !important;
        border: none !important;
        border-right: 1px solid rgba(255,255,255,0.2) !important;
        color: white !important;
        position: relative !important;
    }

    #example1 thead th:last-child {
        border-right: none !important;
    }

    #example1 tbody tr {
        transition: all 0.3s ease !important;
        border-bottom: 1px solid #f0f0f0 !important;
    }

    #example1 tbody tr:hover {
        background: linear-gradient(90deg, #fff3e0 0%, #ffe0b2 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.2) !important;
    }

    #example1 tbody td {
        padding: 12px 10px !important;
        vertical-align: middle !important;
        border: 1px solid gray !important;
        font-size: 13px !important;
        text-align: center !important;
        position: relative !important;
    }

    #example1 tbody td:last-child {
        border: 1px solid gray !important;
    }

    /* Zebra striping for better readability */
    #example1 tbody tr:nth-child(even) {
        background: rgba(255, 235, 238, 0.3) !important;
    }

    #example1 tbody tr:nth-child(odd) {
        background: #fff !important;
    }

    /* Student photo styling */
    .student-photo-cell {
        position: relative !important;
        cursor: pointer !important;
        padding: 8px !important;
    }

    .student-photo-cell img {
        border-radius: 50% !important;
        border: 2px solid #e0e0e0 !important;
        transition: all 0.3s ease !important;
        width: 40px !important;
        height: 40px !important;
        object-fit: cover !important;
    }

    .student-photo-cell:hover img {
        border-color: #0cc035 !important;
        transform: scale(1.1) !important;
    }

    /* Action buttons styling — DO NOT change, student listing looks good */
    .action-btns { white-space: nowrap !important; text-align: center; }
    .action-btns .btn {
        margin: 2px !important;
        border-radius: 4px !important;
        font-size: 11px !important;
        padding: 4px 8px !important;
        transition: all 0.3s ease !important;
    }
    .action-btns .btn:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
    }

    /* Editable cells styling */
    td[contenteditable="true"] {
        cursor: text !important;
        position: relative !important;
        transition: all 0.3s ease !important;
    }

    td[contenteditable="true"]:hover {
        background: rgba(255, 107, 107, 0.1) !important;
        border-color: #0cc035 !important;
    }

    td[contenteditable="true"]:focus {
        outline: 2px solid #0cc035 !important;
        outline-offset: -2px !important;
        background: #fff !important;
        box-shadow: inset 0 0 5px rgba(255, 107, 107, 0.3) !important;
    }

    /* Checkbox styling */
    input[type="checkbox"] {
        width: 16px !important;
        height: 16px !important;
        accent-color: #0cc035 !important;
        cursor: pointer !important;
    }

    /* Status switch styling */
    .onoffswitch-small {
        position: relative !important;
        width: 50px !important;
        height: 24px !important;
        margin: 0 auto !important;
    }

    .onoffswitch-small-checkbox {
        display: none !important;
    }

    .onoffswitch-small-label {
        display: block !important;
        overflow: hidden !important;
        cursor: pointer !important;
        border: 2px solid #ccc !important;
        border-radius: 20px !important;
        transition: all 0.3s ease !important;
    }

    .onoffswitch-small-inner {
        display: block !important;
        width: 200% !important;
        margin-left: -100% !important;
        transition: margin 0.3s ease-in 0s !important;
    }

    .onoffswitch-small-switch {
        display: block !important;
        width: 18px !important;
        height: 18px !important;
        margin: 1px !important;
        background: #fff !important;
        position: absolute !important;
        top: 0 !important;
        bottom: 0 !important;
        right: 24px !important;
        border: 2px solid #ccc !important;
        border-radius: 20px !important;
        transition: all 0.3s ease-in 0s !important;
    }

    .onoffswitch-small-checkbox:checked + .onoffswitch-small-label .onoffswitch-small-inner {
        margin-left: 0 !important;
    }

    .onoffswitch-small-checkbox:checked + .onoffswitch-small-label .onoffswitch-small-switch {
        right: 0 !important;
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }

    /* Responsive table container */
    .responsive {
        border-radius: 8px !important;
        overflow: hidden !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
    }

    /* Download button for sections */
    .section-download-btn {
        background: linear-gradient(135deg, #217346, #155a2e) !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        color: #fff !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.12) !important;
        line-height: 1.4 !important;
    }
    .section-download-btn:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.18) !important;
        color: #fff !important;
        text-decoration: none !important;
        opacity: 0.92 !important;
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
                    <?php if (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) { ?>
                        <?php if (permissionChecker('student_add')) { ?>
                        <div class="student-top-bar">

                            <a class="sbar-btn sbar-btn-add" href="<?php echo base_url('student/add') ?>">
                                <i class="fa fa-plus"></i> Add Student
                            </a>

                            <button type="button" class="sbar-btn sbar-btn-quick" data-toggle="modal" data-target="#quickStudentModal">
                                <i class="fa fa-bolt"></i> Quick Add
                            </button>

                            <a href="<?= base_url('student/export_comprehensive_excel/' . (isset($set) ? $set : '0')) ?>" class="sbar-btn sbar-btn-excel">
                                <i class="fa fa-file-excel-o"></i> Export Excel
                            </a>

                            <?php if (permissionChecker('student_delete') && customCompute($students) > 0) { ?>
                                <form id="multiDeleteForm" method="post" action="<?= base_url('student/multi_delete') ?>" style="display:contents;">
                                    <input type="hidden" name="ids" id="multi_delete_ids" value="" />
                                    <input type="hidden" name="url" value="<?= isset($set) ? $set : '' ?>" />
                                    <button type="button" id="bulkDeleteBtn" class="sbar-btn sbar-btn-delete sbar-disabled" onclick="confirmMultiDelete()">
                                        <i class="fa fa-trash"></i> Delete Selected
                                    </button>
                                </form>
                            <?php } ?>
                            <?php if (permissionChecker('student_edit') && customCompute($students) > 0) { ?>
                                <div style="position:relative;display:inline-block;">
                                    <button type="button" id="bulkLoginDetailsBtn" class="sbar-btn sbar-disabled" style="background:#1a73e8;color:#fff;border-color:#1558b0;" onclick="toggleLoginDropdown(event)">
                                        <i class="fa fa-paper-plane"></i> Send Login Details
                                    </button>
                                    <!-- Dropdown popover -->
                                    <div id="loginDetailsDropdown" style="display:none;position:absolute;top:calc(100% + 8px);left:0;z-index:9999;background:#fff;border-radius:10px;box-shadow:0 6px 24px rgba(0,0,0,0.18);min-width:230px;padding:16px 18px;">
                                        <div style="font-weight:700;font-size:13px;color:#1a73e8;margin-bottom:12px;border-bottom:1px solid #e8eaf6;padding-bottom:8px;">
                                            <i class="fa fa-paper-plane"></i> Send Login Details
                                        </div>
                                        <p style="font-size:12px;color:#777;margin-bottom:12px;">Select channel(s) to send to <strong id="loginDetailsCount">0</strong> student(s).</p>
                                        <label style="display:flex;align-items:center;gap:10px;font-size:13px;font-weight:600;color:#333;cursor:pointer;margin-bottom:10px;">
                                            <input type="checkbox" id="chkSendSms" style="width:16px;height:16px;accent-color:#27ae60;cursor:pointer;">
                                            <i class="fa fa-comment" style="color:#27ae60;"></i> Send SMS
                                        </label>
                                        <label style="display:flex;align-items:center;gap:10px;font-size:13px;font-weight:600;color:#333;cursor:pointer;margin-bottom:14px;">
                                            <input type="checkbox" id="chkSendWa" style="width:16px;height:16px;accent-color:#25D366;cursor:pointer;">
                                            <i class="fa fa-whatsapp" style="color:#25D366;"></i> Send WhatsApp
                                        </label>
                                        <button type="button" id="loginDetailsSendBtn" class="btn btn-primary btn-sm btn-block" disabled>
                                            <i class="fa fa-paper-plane"></i> Send
                                        </button>
                                    </div>
                                </div>
                            <?php } ?>

                            <a href="<?= base_url('promotion/yearly_status') ?>" class="sbar-btn sbar-btn-yearly">
                                <i class="fa fa-bar-chart"></i> Yearly Status
                            </a>

                            <?php if ($this->session->userdata('usertypeID') != 3) { ?>
                            <div class="sbar-class-wrap">
                                <?php
                                $array = array("0" => $this->lang->line("student_select_class"));
                                if (customCompute($classes)) {
                                    foreach ($classes as $classa) {
                                        $array[$classa->classesID] = $classa->classes;
                                    }
                                }
                                echo form_dropdown("classesID", $array, set_value("classesID", $set), "id='classesID' class='form-control'");
                                ?>
                            </div>
                            <?php } ?>

                        </div><!-- /.student-top-bar -->

                        <!-- Advanced Tools (superadmin only) -->
                        <?php if ($this->session->userdata('usertypeID') == 1) { ?>
                        <div style="margin-bottom:12px;">
                            <button type="button" onclick="toggleAdvanced()" id="advancedToggleBtn"
                                style="background:none;border:1px dashed #aaa;color:#666;font-size:12px;padding:5px 14px;border-radius:6px;cursor:pointer;">
                                <i class="fa fa-cogs" id="advancedIcon"></i> Advanced <i class="fa fa-chevron-down" id="advancedChevron"></i>
                            </button>
                            <div id="advancedPanel" style="display:none;margin-top:8px;padding:12px 16px;background:#fff8e1;border:1px solid #ffe082;border-radius:8px;">
                                <span style="font-size:12px;color:#888;margin-right:12px;"><i class="fa fa-info-circle"></i> Advanced data operations</span>
                                <button type="button" onclick="fillWhatsappFromPhone()" id="fillWaBtn"
                                    style="background:#25D366;color:#fff;border:none;padding:6px 16px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                                    <i class="fa fa-whatsapp"></i> Fill WhatsApp from Phone
                                </button>
                            </div>
                        </div>
                        <script>
                        function toggleAdvanced() {
                            var panel   = document.getElementById('advancedPanel');
                            var chevron = document.getElementById('advancedChevron');
                            var open    = panel.style.display === 'block';
                            panel.style.display   = open ? 'none' : 'block';
                            chevron.className = open ? 'fa fa-chevron-down' : 'fa fa-chevron-up';
                        }
                        function fillWhatsappFromPhone() {
                            Swal.fire({
                                title: 'Fill WhatsApp Numbers?',
                                text: 'This will copy Phone to WhatsApp for all students where WhatsApp is empty.',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#25D366',
                                confirmButtonText: 'Yes, Fill Now',
                                cancelButtonText: 'Cancel'
                            }).then(function(result) {
                                if (!result.isConfirmed) return;
                                var btn = document.getElementById('fillWaBtn');
                                btn.disabled = true;
                                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Running...';
                                $.ajax({
                                    url: '<?= base_url("student/fill_whatsapp_from_phone") ?>',
                                    type: 'POST',
                                    success: function(res) {
                                        var data = typeof res === 'string' ? JSON.parse(res) : res;
                                        Swal.fire('Done!', data.message, 'success');
                                    },
                                    error: function() {
                                        Swal.fire('Error', 'Something went wrong.', 'error');
                                    },
                                    complete: function() {
                                        btn.disabled = false;
                                        btn.innerHTML = '<i class="fa fa-whatsapp"></i> Fill WhatsApp from Phone';
                                    }
                                });
                            });
                        }
                        </script>
                        <?php } ?>

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
                                echo form_dropdown("classesID", $array, set_value("classesID", $set), "id='classesID_legacy' class='form-control'");
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
                                                <th class="col-sm-1">Adm No</th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_name') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_phone') ?></th>
                                                <th>WhatsApp</th>
                                                <th class="col-sm-2">Address</th>
                                                <th class="col-sm-2"><?= $this->lang->line('studentType') ?></th>
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>RFID</th>
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
                                                        <td data-title="Adm No">
                                                            <?php echo $student->srregisterNO; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_name') ?>">
                                                            <?php echo $student->srname; ?><?php if (!empty($student->srroll)) { echo ' (' . $student->srroll . ')'; } ?>
                                                        </td>
                                                        
                                                        <td style="color:green;border:1px solid gray;" contenteditable="true"  id="phone_update" class="phone_update"  parentID='<?php echo $student->parentID; ?>'   studentID="<?= $student->srstudentID ?>" data-title="<?= $this->lang->line('student_phone') ?>"><?php echo $student->phone; ?></td>
                                                        <td>
                                                            <?php $waPhone = preg_replace('/\D+/', '', (string)($student->alternative_phone1 ?: $student->phone)); ?>
                                                            <a href="tel:<?= $waPhone ?>" style="color: green; font-weight: bold; text-decoration: underline;" title="Call this number on WhatsApp"><?= $waPhone ?></a>
                                                        </td>
                                                        
                                                        <td data-title="Address">
                                                            <?php echo htmlspecialchars($student->address ?? ''); ?>
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
                                                                        // echo btn_delete('student/delete/' . $student->srstudentID . "/" . $set, $this->lang->line('delete'));
                                                                        ?>
                                                                        <button class="btn btn-success btn-xs mrg btn-send-sms" data-id="<?= $student->srstudentID ?>" data-toggle="tooltip" data-placement="top" title="Send Login SMS"><i class="fa fa-comment"></i></button>
                                                                        <button class="btn btn-xs mrg btn-send-wa" style="background:#25D366;color:#fff;border-color:#128C7E;" data-id="<?= $student->srstudentID ?>" data-toggle="tooltip" data-placement="top" title="Send Login WhatsApp"><i class="fa fa-whatsapp"></i></button>
                                                                        <button class="btn btn-info btn-xs mrg btn-change-login" data-id="<?= $student->srstudentID ?>" data-username="<?= htmlspecialchars($student->username) ?>" data-name="<?= htmlspecialchars($student->srname) ?>" data-toggle="tooltip" data-placement="top" title="Change Login Details"><i class="fa fa-key"></i></button>
                                                                        <?php
                                                                        if( $global_payment_permission){
                                                                        ?>

                                                                         <!-- <a href="<?php echo base_url('Global_payment/index/').$student->classesID.'/'.$student->srstudentID;?>"  class="btn btn-primary btn-xs mrg  " data-placement="top" data-toggle="tooltip" data-original-title="Global invoice"><i class="fa fa-balance-scale"></i></a> -->
                                                                         <a href="<?php echo base_url('global_payment/new/').$student->classesID.'/'.$student->srstudentID;?>" class="btn btn-warning btn-xs mrg" data-placement="top" data-toggle="tooltip" data-original-title="Global Payment (New)"><i class="fa fa-money"></i></a>
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
                                    <div style="margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">
                                        <a href="<?= base_url('student/export_comprehensive_excel/' . $section->classesID . '/' . $section->sectionID) ?>" class="section-download-btn">
                                            <i class="fa fa-file-excel-o"></i> Download <?= $section->section ?> Students Excel
                                        </a>
                                    </div>
                                    <div id="hide-table">
                                        <table id="example1" class="table table-bordered   tableBorder dataTable no-footer" style="width:100%">
                                        
                                            <thead>
                                                <tr>
                                                    <th style="width:30px; text-align:center"><input type="checkbox" class="select_all_section" /></th>
                                                    <th class="col-sm-1"><?= $this->lang->line('slno') ?></th>
                                                    <th class="col-sm-1"><?= $this->lang->line('student_photo') ?></th>
                                                    <th class="col-sm-1">Adm No</th>
                                                    <th class="col-sm-2"><?= $this->lang->line('student_name') ?></th>
                                                    <th class="col-sm-2"><?= $this->lang->line('student_phone') ?></th>
                                                    <th>WhatsApp</th>
                                                    <th class="col-sm-2">Address</th>
                                                    <th class="col-sm-2"><?= $this->lang->line('studentType') ?></th>

                                                     <th>Class</th>
                                                    <th>Section</th>
                                                    <th>RFID</th>
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

                                                        <td data-title="Adm No">
                                                            <?php echo $student->srregisterNO; ?>
                                                        </td>
                                                        <td data-title="<?= $this->lang->line('student_name') ?>">
                                                            <?php echo $student->srname; ?><?php if (!empty($student->srroll)) { echo ' (' . $student->srroll . ')'; } ?>
                                                        </td>
                                                        
                                                        <td style="color:green;border:1px solid gray;" contenteditable="true"  id="phone_update" studentID="<?= $student->srstudentID ?>" parentID='<?php echo $student->parentID; ?>' data-title="<?= $this->lang->line('student_phone') ?>"><?php echo $student->phone; ?></td>
                                                        <td>
                                                            <?php $waPhone = preg_replace('/\D+/', '', (string)($student->alternative_phone1 ?: $student->phone)); ?>
                                                            <a href="tel:<?= $waPhone ?>" style="color: green; font-weight: bold; text-decoration: underline;" title="Call this number on WhatsApp"><?= $waPhone ?></a>
                                                        </td>
                                                        
                                                        <td data-title="Address">
                                                            <?php echo htmlspecialchars($student->address ?? ''); ?>
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
                                                                        // echo btn_delete('student/delete/' . $student->srstudentID . "/" . $set, $this->lang->line('delete'));
                                                                        ?>
                                                                        <button class="btn btn-success btn-xs mrg btn-send-sms" data-id="<?= $student->srstudentID ?>" data-toggle="tooltip" data-placement="top" title="Send Login SMS"><i class="fa fa-comment"></i></button>
                                                                        <button class="btn btn-xs mrg btn-send-wa" style="background:#25D366;color:#fff;border-color:#128C7E;" data-id="<?= $student->srstudentID ?>" data-toggle="tooltip" data-placement="top" title="Send Login WhatsApp"><i class="fa fa-whatsapp"></i></button>
                                                                        <button class="btn btn-info btn-xs mrg btn-change-login" data-id="<?= $student->srstudentID ?>" data-username="<?= htmlspecialchars($student->username) ?>" data-name="<?= htmlspecialchars($student->srname) ?>" data-toggle="tooltip" data-placement="top" title="Change Login Details"><i class="fa fa-key"></i></button>
                                                                        <?php
                                                                        if( $global_payment_permission){

                                                                        ?>

                                                                        <!-- <a href="<?php echo base_url('Global_payment/index/').$student->classesID.'/'.$student->srstudentID;?>"  class="btn btn-primary btn-xs mrg  " data-placement="top" data-toggle="tooltip" data-original-title="Global invoice"><i class="fa fa-balance-scale"></i></a> -->
                                                                        <a href="<?php echo base_url('global_payment/new/').$student->classesID.'/'.$student->srstudentID;?>" class="btn btn-warning btn-xs mrg" data-placement="top" data-toggle="tooltip" data-original-title="Global Payment (New)"><i class="fa fa-money"></i></a>

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
                                                <th class="col-sm-1">Adm No</th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_name') ?></th>
                                                <th class="col-sm-2"><?= $this->lang->line('student_phone') ?></th>
                                                <th>WhatsApp</th>
                                                <th class="col-sm-2">Address</th>
                                                <th class="col-sm-2"><?= $this->lang->line('studentType') ?></th>
                                                 <th>Class</th>
                                                <th>Section</th>
                                                <th>RFID</th>
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
<!-- photo  Modal  start Structure -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />
<div class="modal fade" id="fileUploadModal" tabindex="-1" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; border-bottom: none; padding: 20px 25px;">
                <h5 class="modal-title" id="fileUploadModalLabel" style="font-weight: 600; font-size: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-upload"></i> Upload Photo
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; font-size: 28px; font-weight: 300; text-shadow: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 30px 25px; background: #f8f9fa;">
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

<!-- Photo zoom modal -->
<div class="modal fade" id="photoZoomModal" tabindex="-1" role="dialog" aria-labelledby="photoZoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.3); background: transparent;">
            <div class="modal-body text-center" style="padding: 0; border-radius: 20px; background: #000; position: relative;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 10px; right: 15px; z-index: 10; color: white; opacity: 1; font-size: 32px; font-weight: 300; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                    <span aria-hidden="true">&times;</span>
                </button>
                <img id="photoZoomImg" src="" style="max-width:100%; max-height:85vh; border-radius: 20px; object-fit: contain;" />
            </div>
        </div>
    </div>
</div>

<!-- queck student popup start -->
                         <div class="modal fade" id="quickStudentModal" tabindex="-1" role="dialog" aria-labelledby="quickStudentModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <form id="quickStudentForm" method="post" action="<?= base_url('student/add') ?>">
                                <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 60px rgba(0,0,0,0.25); overflow: hidden;">

                                    <!-- Header -->
                                    <div class="modal-header" style="background: linear-gradient(135deg, #0cc035 0%, #0a9d2b 100%); color: white; border: none; padding: 25px 30px;">
                                    <h5 class="modal-title" id="quickStudentModalLabel" style="font-weight: 600; font-size: 22px; display: flex; align-items: center; gap: 12px;">
                                        <i class="fa fa-user-plus" style="font-size: 24px;"></i> Quick Student Creation
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 32px; font-weight: 300; text-shadow: none;">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>

                                    <!-- Body -->
                                    <div class="modal-body" style="background: linear-gradient(180deg, #f0f4ff 0%, #e8f5e9 100%); padding: 30px;">
                                    <div class="container-fluid">

                                        <!-- Basic Info -->
                                        <fieldset class="border p-4 mb-4 bg-white shadow-sm" style="border-radius: 15px; border: 2px solid #e0e0e0 !important;">
                                        <legend class="w-auto font-weight-bold" style="color: #0cc035; font-size: 18px; padding: 0 10px;"><i class="fa fa-info-circle"></i> Basic Information</legend>
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
                                        <div id="transport_div" class="border p-4 mb-4 bg-white shadow-sm" style="display: none; border-radius: 15px; border: 2px solid #e0e0e0 !important;">
                                        <legend class="w-auto font-weight-bold" style="color: #28a745; font-size: 18px; padding: 0 10px;"><i class="fa fa-bus"></i> Transport Details</legend>
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
                                        <div id="hostel_div" class="border p-4 mb-4 bg-white shadow-sm" style="display: none; border-radius: 15px; border: 2px solid #e0e0e0 !important;">
                                        <legend class="w-auto font-weight-bold" style="color: #ff9800; font-size: 18px; padding: 0 10px;"><i class="fa fa-building"></i> Hostel Details</legend>
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
                                    <div class="modal-footer" style="background: white; border-top: 2px solid #e0e0e0; padding: 20px 30px;">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="padding: 10px 25px; border-radius: 10px; font-weight: 500; border: none; background: #6c757d;">Cancel</button>
                                    <button type="submit" class="btn btn-success" style="padding: 10px 25px; border-radius: 10px; font-weight: 500; border: none; background: linear-gradient(135deg, #0cc035 0%, #0a9d2b 100%); box-shadow: 0 4px 15px rgba(12, 192, 53, 0.3);">
                                        <i class="fa fa-save"></i> Save Student
                                    </button>
                                    </div>
                                </div>
                                </form>
                          </div>
                         
                        <!-- queck student popup end -->

                        


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

    // Fix: prevent browser autofill in Select2 v3 search inputs.
    // Select2 v3 uses class 'select2-input' (not 'select2-search__field') and
    // injects the input dynamically on open. A MutationObserver catches it
    // the instant it appears — before the browser can autofill it.
    (function () {
        function disableAutofill($el) {
            $el.attr({ autocomplete: 'new-password', autocorrect: 'off', spellcheck: 'false' });
        }
        // Cover any already-present inputs
        disableAutofill($('input.select2-input'));
        // Cover dynamically injected inputs (triggered on every Select2 open)
        new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                var nodes = mutations[i].addedNodes;
                for (var j = 0; j < nodes.length; j++) {
                    var node = nodes[j];
                    if (node.nodeType !== 1) continue;
                    var $inp = $(node).find('input.select2-input').add($(node).filter('input.select2-input'));
                    if ($inp.length) disableAutofill($inp);
                }
            }
        }).observe(document.body, { childList: true, subtree: true });
    }());

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


    $(document).on('change', '.onoffswitch-small-checkbox', function() {
        var checkbox    = $(this);
        var isNowOn     = checkbox.prop('checked');   // state AFTER browser toggled it
        var prevState   = !isNowOn;                   // state BEFORE
        var newStatus   = isNowOn ? 'chacked' : 'unchacked';
        var actionLabel = isNowOn ? 'Activate' : 'Deactivate';
        var btnColor    = isNowOn ? '#0cc035'  : '#e53935';
        var studentID   = checkbox.closest('.onoffswitch-small').attr('id');

        // Immediately revert the visual toggle — wait for user confirmation
        // Note: .prop() does NOT re-trigger the 'change' event, so no infinite loop
        checkbox.prop('checked', prevState);

        Swal.fire({
            title: actionLabel + ' Student?',
            text: 'Are you sure you want to ' + actionLabel.toLowerCase() + ' this student?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: btnColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, ' + actionLabel + '!',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (!result.isConfirmed) return; // toggle stays reverted — no change

            checkbox.prop('checked', isNowOn); // apply the intended new state

            $.ajax({
                type: 'POST',
                url: '<?= base_url("student/active") ?>',
                data: { id: studentID, status: newStatus },
                dataType: 'html',
                success: function(data) {
                    if (data === 'Success') {
                        toastr.success('Student ' + actionLabel.toLowerCase() + 'd successfully.');
                    } else {
                        checkbox.prop('checked', prevState); // server rejected — revert
                        toastr.error('Failed to update status. Please try again.');
                    }
                },
                error: function() {
                    checkbox.prop('checked', prevState); // network error — revert
                    toastr.error('Request failed. Please try again.');
                }
            });
        });
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
    $('#dob').datepicker({ startView: 2, format: 'dd-mm-yyyy', endDate: '0d', autoclose: true });
    $('#admission_date').datepicker({ format: 'dd-mm-yyyy', endDate: '0d', autoclose: true });

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
// ── Quick modal: auto-capitalize name fields ──
$(document).on('input', '#quickStudentModal #first_name, #quickStudentModal #last_name', function() {
    var pos = this.selectionStart, v = $(this).val();
    if (v.length > 0) {
        $(this).val(v.charAt(0).toUpperCase() + v.slice(1));
        try { this.setSelectionRange(pos, pos); } catch(e) {}
    }
});
$(document).on('keypress', '#quickStudentModal #first_name, #quickStudentModal #last_name', function(e) {
    if (!/[a-zA-Z\s.\-']/.test(String.fromCharCode(e.which || e.keyCode))) e.preventDefault();
});

$(document).on("keyup", ".id_card", function(){
    var f = $("#first_name").val(), l = $("#last_name").val();
    var idcard = f.charAt(0).toUpperCase() + " " + l.charAt(0).toUpperCase() + l.slice(1);
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
            $('#error-message').text('');
        }
    });
});

// ── Quick modal phone: digits only, max 10 ──
$('#quickStudentModal').on('keypress', 'input[name="phone"]', function(e) {
    var c = e.which || e.keyCode; if (c < 48 || c > 57) e.preventDefault();
});
$('#quickStudentModal').on('input', 'input[name="phone"]', function() {
    $(this).val($(this).val().replace(/\D/g,'').slice(0,10));
});

// ── Quick modal form validation ──
$('#quickStudentForm').on('submit', function(e) {
    var ok = true, $f = $(this);
    $f.find('.qs-val-error').remove();
    $f.find('.form-group').removeClass('has-error');

    function qsErr($el, msg) {
        var $p = $el.closest('.form-group,.col-md-3,.col-md-4');
        $p.addClass('has-error');
        $p.find('.qs-val-error').remove();
        $el.after('<span class="qs-val-error" style="color:#c0392b;font-size:12px;display:block;margin-top:2px;"><i class="fa fa-exclamation-circle"></i> '+msg+'</span>');
    }

    $f.find('input[type="text"]').each(function(){ $(this).val($.trim($(this).val())); });

    [['input[name="first_name"]','First Name is required'],
     ['input[name="phone"]','Phone number is required'],
     ['input[name="registerNO"]','Admission No is required'],
     ['input[name="roll"]','Roll No is required']
    ].forEach(function(r){
        var $el=$f.find(r[0]);
        if ($el.length&&!$.trim($el.val())) { qsErr($el,r[1]); ok=false; }
    });

    [['select[name="classesID"]','Please select a Class'],
     ['select[name="sectionID"]','Please select a Section']
    ].forEach(function(r){
        var $el=$f.find(r[0]), v=$el.val();
        if ($el.length&&(!v||v=='0'||v=='')) { qsErr($el,r[1]); ok=false; }
    });

    var ph=$.trim($f.find('input[name="phone"]').val());
    if (ph&&!/^\d{10}$/.test(ph)) { qsErr($f.find('input[name="phone"]'),'Phone must be exactly 10 digits'); ok=false; }

    var dob=$.trim($f.find('input[name="dob"]').val());
    if (dob) {
        var p=dob.split('-');
        if (p.length===3&&new Date(+p[2],+p[1]-1,+p[0])>new Date()) {
            qsErr($f.find('input[name="dob"]'),'Date of birth cannot be a future date'); ok=false;
        }
    }

    if (!ok) e.preventDefault();
});
</script>

<script>
// Bulk select / delete / sms / whatsapp handlers
function updateDeleteBtn() {
    var anyChecked = $('.student-checkbox:checked').length > 0;
    $('#bulkDeleteBtn, #bulkLoginDetailsBtn').toggleClass('sbar-disabled', !anyChecked);
}

$(document).on('change', '#select_all_students', function() {
    var checked = $(this).prop('checked');
    $('#example1').find('.student-checkbox').prop('checked', checked);
    updateDeleteBtn();
});

$(document).on('change', '.select_all_section', function() {
    var checked = $(this).prop('checked');
    $(this).closest('table').find('.student-checkbox').prop('checked', checked);
    updateDeleteBtn();
});

$(document).on('change', '.student-checkbox', function() {
    updateDeleteBtn();
});

function confirmMultiDelete() {
    if ($('#bulkDeleteBtn').hasClass('sbar-disabled')) {
        Swal.fire({ icon: 'warning', title: 'No Student Selected', text: 'Please select at least one checkbox to proceed.', confirmButtonColor: '#e53935' });
        return;
    }
    var ids = [];
    $('.student-checkbox:checked').each(function() { ids.push($(this).val()); });
    if (!confirm('Are you sure you want to delete selected student(s)? This action cannot be undone.')) return;
    $('#multi_delete_ids').val(ids.join(','));
    $('#multiDeleteForm').submit();
}

// Per-row: Send Login SMS
$(document).on('click', '.btn-send-sms', function() {
    var id  = $(this).data('id');
    var btn = $(this);
    btn.prop('disabled', true);
    $.ajax({
        type: 'POST',
        url:  '<?= base_url("student/send_login_sms") ?>',
        data: { id: id },
        dataType: 'json',
        success: function(res) {
            if (res.status) { toastr.success(res.message); }
            else            { toastr.error(res.message); }
            btn.prop('disabled', false);
        },
        error: function() { toastr.error('SMS request failed.'); btn.prop('disabled', false); }
    });
});

// Per-row: Send Login WhatsApp
$(document).on('click', '.btn-send-wa', function() {
    var id  = $(this).data('id');
    var btn = $(this);
    btn.prop('disabled', true);
    $.ajax({
        type: 'POST',
        url:  '<?= base_url("student/send_login_whatsapp") ?>',
        data: { id: id },
        dataType: 'json',
        success: function(res) {
            if (res.status) { toastr.success(res.message); }
            else            { toastr.error(res.message); }
            btn.prop('disabled', false);
        },
        error: function() { toastr.error('WhatsApp request failed.'); btn.prop('disabled', false); }
    });
});

// Toggle the login details dropdown
function toggleLoginDropdown(e) {
    e.stopPropagation();
    if ($('#bulkLoginDetailsBtn').hasClass('sbar-disabled')) {
        Swal.fire({ icon: 'warning', title: 'No Student Selected', text: 'Please select at least one checkbox to proceed.', confirmButtonColor: '#1a73e8' });
        return;
    }
    var dd = $('#loginDetailsDropdown');
    if (dd.is(':visible')) {
        dd.hide();
        return;
    }
    var count = $('.student-checkbox:checked').length;
    $('#loginDetailsCount').text(count);
    $('#chkSendSms').prop('checked', false);
    $('#chkSendWa').prop('checked', false);
    $('#loginDetailsSendBtn').prop('disabled', true);
    dd.show();
}

// Close dropdown when clicking outside
$(document).on('click', function(e) {
    if (!$(e.target).closest('#loginDetailsDropdown, #bulkLoginDetailsBtn').length) {
        $('#loginDetailsDropdown').hide();
    }
});

// Enable Send button when at least one channel checked
$(document).on('change', '#chkSendSms, #chkSendWa', function() {
    var any = $('#chkSendSms').is(':checked') || $('#chkSendWa').is(':checked');
    $('#loginDetailsSendBtn').prop('disabled', !any);
});

// Send button click
$(document).on('click', '#loginDetailsSendBtn', function() {
    var ids = [];
    $('.student-checkbox:checked').each(function() { ids.push($(this).val()); });
    var sendSms = $('#chkSendSms').is(':checked');
    var sendWa  = $('#chkSendWa').is(':checked');
    if (!ids.length || (!sendSms && !sendWa)) return;

    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
    $('#loginDetailsDropdown').hide();

    var requests = [];
    if (sendSms) {
        requests.push($.ajax({ type:'POST', url:'<?= base_url("student/send_bulk_login_sms") ?>', data:{ids:ids.join(',')}, dataType:'json' }));
    }
    if (sendWa) {
        requests.push($.ajax({ type:'POST', url:'<?= base_url("student/send_bulk_login_whatsapp") ?>', data:{ids:ids.join(',')}, dataType:'json' }));
    }

    $.when.apply($, requests).done(function() {
        var results = requests.length === 1 ? [arguments] : Array.from(arguments);
        results.forEach(function(r) {
            var res = r[0];
            if (res && res.status) { toastr.success(res.message); }
            else if (res)          { toastr.error(res.message); }
        });
        btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send');
    }).fail(function() {
        toastr.error('Request failed. Please try again.');
        btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send');
    });
});

// Move modal to body to avoid overflow/z-index clipping by parent containers
$(function() { $('body').append($('#changeLoginModal').detach()); });

// Change Login Details — open modal
$(document).on('click', '.btn-change-login', function() {
    var id       = $(this).data('id');
    var username = $(this).data('username');
    var name     = $(this).data('name');
    $('#clStudentID').val(id);
    $('#clStudentName').text(name);
    $('#clUsername').val(username);
    $('#clPassword').val('');
    $('#clPasswordConfirm').val('');
    $('#clError').hide().text('');
    $('#changeLoginModal').modal('show');
});

// Save login details via AJAX
$(document).on('click', '#changeLoginSaveBtn', function() {
    var id   = $('#clStudentID').val();
    var pass = $.trim($('#clPassword').val());
    var conf = $.trim($('#clPasswordConfirm').val());

    if (!pass) { $('#clError').text('Password is required.').show(); return; }
    if (pass !== conf) { $('#clError').text('Passwords do not match.').show(); return; }

    $('#clError').hide();
    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        type: 'POST',
        url:  '<?= base_url("student/update_login_details") ?>',
        data: { studentID: id, password: pass },
        dataType: 'json',
        success: function(res) {
            if (res.status) {
                toastr.success(res.message);
                $('#changeLoginModal').modal('hide');
            } else {
                $('#clError').text(res.message).show();
            }
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save');
        },
        error: function() {
            $('#clError').text('Request failed. Please try again.').show();
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save');
        }
    });
});

// Toggle password visibility
$(document).on('click', '.cl-toggle-pw', function() {
    var targetID = $(this).data('target');
    var input    = $('#' + targetID);
    var icon     = $(this).find('i');
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});
</script>

<!-- Change Login Details Modal -->
<div class="modal fade" id="changeLoginModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width:380px;margin-top:120px;">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;">
            <div class="modal-header" style="background:#17a2b8;color:#fff;padding:14px 20px;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;font-size:20px;">&times;</button>
                <h4 class="modal-title" style="font-size:15px;font-weight:700;">
                    <i class="fa fa-key"></i> Change Login Details
                </h4>
            </div>
            <div class="modal-body" style="padding:20px 24px;">
                <p style="font-size:13px;color:#555;margin-bottom:16px;">
                    Student: <strong id="clStudentName"></strong>
                </p>
                <input type="hidden" id="clStudentID">
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="font-size:13px;font-weight:600;color:#333;">Username</label>
                    <input type="text" id="clUsername" class="form-control" placeholder="Enter username" style="border-radius:6px;background:#f0f0f0;" disabled>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="font-size:13px;font-weight:600;color:#333;">New Password <small style="color:#aaa;font-weight:400;">(leave blank to keep current)</small></label>
                    <div class="input-group">
                        <input type="password" id="clPassword" class="form-control" placeholder="New password" style="border-radius:6px 0 0 6px;">
                        <span class="input-group-addon cl-toggle-pw" data-target="clPassword" style="cursor:pointer;border-radius:0 6px 6px 0;background:#fff;border:1px solid #ccd0d5;border-left:0;padding:6px 10px;">
                            <i class="fa fa-eye" style="color:#888;"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:6px;">
                    <label style="font-size:13px;font-weight:600;color:#333;">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" id="clPasswordConfirm" class="form-control" placeholder="Confirm new password" style="border-radius:6px 0 0 6px;">
                        <span class="input-group-addon cl-toggle-pw" data-target="clPasswordConfirm" style="cursor:pointer;border-radius:0 6px 6px 0;background:#fff;border:1px solid #ccd0d5;border-left:0;padding:6px 10px;">
                            <i class="fa fa-eye" style="color:#888;"></i>
                        </span>
                    </div>
                </div>
                <div id="clError" style="display:none;color:#e53935;font-size:12px;margin-top:8px;"></div>
            </div>
            <div class="modal-footer" style="padding:12px 20px;background:#f8f9fa;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" id="changeLoginSaveBtn" class="btn btn-info btn-sm">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>