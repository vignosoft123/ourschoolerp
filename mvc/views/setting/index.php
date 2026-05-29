<style>
.st-page-head { display:flex; align-items:center; justify-content:space-between; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:12px 18px; margin-bottom:20px; flex-wrap:wrap; gap:8px; }
.st-page-head h3 { margin:0; font-size:16px; font-weight:700; color:#1a202c; display:flex; align-items:center; gap:8px; }
.st-page-head h3 .fa { color:#1a73e8; }
.st-page-head .breadcrumb { margin:0; background:none; padding:0; font-size:13px; }

.st-section { background:#fff; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:20px; overflow:hidden; }
.st-section-head { background:linear-gradient(90deg,#1a73e8,#1558b0); color:#fff; padding:10px 18px; font-size:12.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; display:flex; align-items:center; gap:8px; }
.st-section-head .fa { font-size:13px; opacity:.85; }
.st-section-body { padding:20px 18px; }

.st-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px 20px; }
.st-grid.cols-2 { grid-template-columns:repeat(2,1fr); }
.st-grid.cols-4 { grid-template-columns:repeat(4,1fr); }
.st-grid.cols-1 { grid-template-columns:1fr; }

.st-field { }
.st-field > label { display:block; font-size:11.5px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.45px; margin-bottom:5px; }
.st-field .form-control { border-radius:6px; border:1px solid #d1d9e0; font-size:13px; font-weight:600; color:#1a202c; height:34px; padding:6px 10px; }
.st-field textarea.form-control { height:auto; min-height:70px; resize:vertical; }
.st-field .control-label { font-size:11.5px; color:#e53e3e; display:block; margin-top:3px; }

.st-toggle-group { display:flex; align-items:center; gap:10px; padding:6px 0; }
.st-toggle-group label { font-size:13px; font-weight:500; color:#1a202c; margin:0; cursor:pointer; display:flex; align-items:center; gap:5px; }
.st-toggle-group input[type=radio], .st-toggle-group input[type=checkbox] { width:15px; height:15px; cursor:pointer; accent-color:#1a73e8; }

.st-checkbox-row { display:flex; align-items:center; gap:10px; padding:4px 0; }
.st-checkbox-row input[type=checkbox] { width:16px; height:16px; cursor:pointer; accent-color:#1a73e8; flex-shrink:0; }
.st-checkbox-row label { font-size:13px; font-weight:500; color:#1a202c; margin:0; cursor:pointer; }

.st-note { background:#fff8e1; border-left:3px solid #f59e0b; padding:8px 12px; border-radius:4px; font-size:12.5px; color:#7c5c00; margin-bottom:12px; }
.st-note b { color:#d97706; }

.st-img-preview { border:1px solid #e2e8f0; border-radius:6px; padding:8px; display:inline-block; margin-top:8px; background:#f8fafc; }
.st-img-preview img { display:block; }

.st-save-bar { background:#f8fafc; border-top:1px solid #e2e8f0; padding:14px 18px; display:flex; align-items:center; }
.st-save-bar .btn-save { background:linear-gradient(135deg,#1a73e8,#1558b0); color:#fff; border:none; padding:9px 30px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; transition:opacity .2s; }
.st-save-bar .btn-save:hover { opacity:.88; }

.st-autoinvoice-row { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.st-info-icon { color:#94a3b8; font-size:12px; }

/* file upload button */
.st-file-btn { display:inline-flex; align-items:center; gap:6px; background:#fff; color:#4a5568; border:1.5px solid #d1d9e0; padding:7px 14px; border-radius:6px; font-size:13px; cursor:pointer; position:relative; overflow:hidden; font-weight:600; }
.st-file-btn input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; }
.st-file-btn:hover { background:#f8fafc; border-color:#94a3b8; }
</style>

<div class="st-page-head">
    <h3><i class="fa fa-gears"></i> <?= $this->lang->line('panel_title') ?></h3>
    <ol class="breadcrumb">
        <li><a href="<?= base_url("dashboard/index") ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
        <li class="active"><?= $this->lang->line('menu_setting') ?></li>
    </ol>
</div>

<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">

    <!-- ── Site Configuration ── -->
    <div class="st-section">
        <div class="st-section-head"><i class="fa fa-cog"></i> <?= $this->lang->line('setting_site_configaration') ?></div>
        <div class="st-section-body">

            <div class="st-grid">
                <div class="st-field <?= form_error('sname') ? 'has-error' : '' ?>">
                    <label>School Name <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Set your school name here"></i></label>
                    <input type="text" class="form-control" id="sname" name="sname" value="<?= set_value('sname', $setting->sname) ?>">
                    <span class="control-label"><?= form_error('sname') ?></span>
                </div>

                <div class="st-field <?= form_error('phone') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_phone") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Set organization phone number here"></i></label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= set_value('phone', $setting->phone) ?>">
                    <span class="control-label"><?= form_error('phone') ?></span>
                </div>

                <div class="st-field <?= form_error('email') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_email") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Set organization email address here"></i></label>
                    <input type="text" class="form-control" id="email" name="email" value="<?= set_value('email', $setting->email) ?>">
                    <span class="control-label"><?= form_error('email') ?></span>
                </div>

                <div class="st-field <?= form_error('village_name') ? 'has-error' : '' ?>">
                    <label>Village Name <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Village name"></i></label>
                    <input type="text" class="form-control" id="village_name" name="village_name" value="<?= set_value('village_name', isset($setting->village_name) ? $setting->village_name : '') ?>">
                    <span class="control-label"><?= form_error('village_name') ?></span>
                </div>

                <div class="st-field <?= form_error('footer') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_footer") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Set site footer text here"></i></label>
                    <input type="text" class="form-control" id="footer" name="footer" value="<?= set_value('footer', $setting->footer) ?>">
                    <span class="control-label"><?= form_error('footer') ?></span>
                </div>

                <div class="st-field <?= form_error('schoolCode') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_code") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Set School Code"></i></label>
                    <input type="text" class="form-control" id="schoolCode" name="schoolCode" value="<?= set_value('schoolCode', isset($setting->schoolCode) ? $setting->schoolCode : '') ?>">
                    <span class="control-label"><?= form_error('schoolCode') ?></span>
                </div>

                <div class="st-field <?= form_error('website') ? 'has-error' : '' ?>">
                    <label>Website <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="School website URL"></i></label>
                    <input type="text" class="form-control" id="website" name="website" value="<?= set_value('website', isset($setting->website) ? $setting->website : '') ?>">
                    <span class="control-label"><?= form_error('website') ?></span>
                </div>

                <div class="st-field <?= form_error('school_telugu') ? 'has-error' : '' ?>">
                    <label>School Telugu Name <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Set school name in Telugu"></i></label>
                    <input type="text" class="form-control" id="school_telugu" name="school_telugu" value="<?= set_value('school_telugu', isset($setting->school_telugu) ? $setting->school_telugu : '') ?>">
                    <span class="control-label"><?= form_error('school_telugu') ?></span>
                </div>

                <div class="st-field <?= form_error('app_link') ? 'has-error' : '' ?>">
                    <label>App Link <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Mobile app link"></i></label>
                    <input type="text" class="form-control" id="app_link" name="app_link" value="<?= set_value('app_link', isset($setting->app_link) ? $setting->app_link : '') ?>">
                    <span class="control-label"><?= form_error('app_link') ?></span>
                </div>

                <div class="st-field <?= form_error('address') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_address") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Set organization address here"></i></label>
                    <textarea class="form-control" id="address" name="address"><?= set_value('address', $setting->address) ?></textarea>
                    <span class="control-label"><?= form_error('address') ?></span>
                </div>

                <div class="st-field <?= form_error('currency_code') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_currency_code") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Currency code like USD or GBP"></i></label>
                    <input type="text" class="form-control" id="currency_code" name="currency_code" value="<?= set_value('currency_code', $setting->currency_code) ?>">
                    <span class="control-label"><?= form_error('currency_code') ?></span>
                </div>

                <div class="st-field <?= form_error('currency_symbol') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_currency_symbol") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Currency symbol like $ or £"></i></label>
                    <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="<?= set_value('currency_symbol', $setting->currency_symbol) ?>">
                    <span class="control-label"><?= form_error('currency_symbol') ?></span>
                </div>

                <div class="st-field <?= form_error('google_analytics') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_google_analytics") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Google Analytics tracking code"></i></label>
                    <input type="text" class="form-control" id="google_analytics" name="google_analytics" value="<?= set_value('google_analytics', $setting->google_analytics) ?>">
                    <span class="control-label"><?= form_error('google_analytics') ?></span>
                </div>

                <div class="st-field <?= form_error('language_status') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_language") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Enable/Disable language selector in top section"></i></label>
                    <?php
                    $languageArray[0] = $this->lang->line('setting_enable');
                    $languageArray[1] = $this->lang->line('setting_disable');
                    echo form_dropdown("language_status", $languageArray, set_value("language_status", $setting->language_status), "id='language_status' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('language_status') ?></span>
                </div>

                <div class="st-field <?= form_error('lang') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_dafault_language") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Select default language"></i></label>
                    <?php
                    echo form_dropdown("language", array(
                        "english"    => $this->lang->line("setting_english"),
                        "bengali"    => $this->lang->line("setting_bengali"),
                        "arabic"     => $this->lang->line("setting_arabic"),
                        "chinese"    => $this->lang->line("setting_chinese"),
                        "french"     => $this->lang->line("setting_french"),
                        "german"     => $this->lang->line("setting_german"),
                        "hindi"      => $this->lang->line("setting_hindi"),
                        "indonesian" => $this->lang->line("setting_indonesian"),
                        "italian"    => $this->lang->line("setting_italian"),
                        "portuguese" => $this->lang->line("setting_portuguese"),
                        "romanian"   => $this->lang->line("setting_romanian"),
                        "russian"    => $this->lang->line("setting_russian"),
                        "spanish"    => $this->lang->line("setting_spanish"),
                        "thai"       => $this->lang->line("setting_thai"),
                        "turkish"    => $this->lang->line("setting_turkish"),
                    ), set_value("language", $setting->language), "id='language' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('lang') ?></span>
                </div>

                <div class="st-field <?= form_error('time_zone') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_time_zone") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Select your region time zone"></i></label>
                    <?php
                    $path = APPPATH . "config/timezones_class.php";
                    if (@include($path)) {
                        $timezones_cls = new Timezones();
                        $timezones = $timezones_cls->get_timezones();
                        unset($timezones['']);
                        $selectTimeZone['none'] = $this->lang->line('setting_school_select_time_zone');
                        $timeZones = array_merge($selectTimeZone, $timezones);
                        echo form_dropdown("time_zone", $timeZones, set_value("time_zone", $setting->time_zone), "id='time_zone' class='form-control select2'");
                    }
                    ?>
                    <span class="control-label"><?= form_error('time_zone') ?></span>
                </div>

                <div class="st-field <?= form_error('school_year') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_default_school_year") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Select running academic year"></i></label>
                    <?php
                    $array = array("0" => $this->lang->line("setting_school_select_school_year"));
                    if (customCompute($schoolyears)) {
                        foreach ($schoolyears as $schoolyear) {
                            if ($schoolyear->schooltype == 'semesterbase') {
                                $array[$schoolyear->schoolyearID] = $schoolyear->schoolyeartitle . ' (' . $schoolyear->schoolyear . ')';
                            } else {
                                $array[$schoolyear->schoolyearID] = $schoolyear->schoolyear;
                            }
                        }
                    }
                    echo form_dropdown("school_year", $array, set_value("school_year", $setting->school_year), "id='school_year' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('school_year') ?></span>
                </div>

                <div class="st-field <?= form_error('attendance') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_default_attendance") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Select default attendance system"></i></label>
                    <?php
                    $array = array(
                        "0"       => $this->lang->line("setting_school_select_attendance"),
                        "day"     => $this->lang->line("setting_school_select_day_attendance"),
                        "subject" => $this->lang->line("setting_school_select_subject_attendance")
                    );
                    echo form_dropdown("attendance", $array, set_value("attendance", $setting->attendance), "id='attendance' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('attendance') ?></span>
                </div>

                <div class="st-field <?= form_error('ex_class') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_graduate_class") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Graduate/former student class"></i></label>
                    <?php
                    $ex_classArray['0'] = $this->lang->line('setting_select_graduate_class');
                    if (customCompute($classes)) {
                        foreach ($classes as $class) {
                            $ex_classArray[$class->classesID] = $class->classes;
                        }
                    }
                    echo form_dropdown("ex_class", $ex_classArray, set_value("ex_class", $setting->ex_class), "id='ex_class' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('ex_class') ?></span>
                </div>

                <div class="st-field <?= form_error('note') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_note") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Enable/Disable module helper note"></i></label>
                    <?php
                    $noteArray[1] = $this->lang->line('setting_enable');
                    $noteArray[0] = $this->lang->line('setting_disable');
                    echo form_dropdown("note", $noteArray, set_value("note", $setting->note), "id='note' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('note') ?></span>
                </div>

                <div class="st-field <?= form_error('frontendorbackend') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_frontend") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Enable/Disable frontend site"></i></label>
                    <?php
                    echo form_dropdown("frontendorbackend", array(
                        "YES" => $this->lang->line("setting_school_yes"),
                        "NO"  => $this->lang->line("setting_school_no"),
                    ), set_value("frontendorbackend", $setting->frontendorbackend), "id='frontendorbackend' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('frontendorbackend') ?></span>
                </div>

                <div class="st-field <?= form_error('profile_edit') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_school_profile_edit") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Enable/Disable profile editing"></i></label>
                    <?php
                    $profileEditArray[1] = $this->lang->line('setting_enable');
                    $profileEditArray[0] = $this->lang->line('setting_disable');
                    echo form_dropdown("profile_edit", $profileEditArray, set_value("profile_edit", $setting->profile_edit), "id='profile_edit' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('profile_edit') ?></span>
                </div>

                <div class="st-field <?= form_error('weekends') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line('setting_weekends') ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Select weekend days"></i></label>
                    <?php
                    $array = array(
                        "0" => $this->lang->line("setting_sunday"),
                        "1" => $this->lang->line("setting_monday"),
                        "2" => $this->lang->line("setting_tuesday"),
                        "3" => $this->lang->line("setting_wednesday"),
                        "4" => $this->lang->line("setting_thursday"),
                        "5" => $this->lang->line("setting_friday"),
                        "6" => $this->lang->line("setting_saturday")
                    );
                    $expHoliday = isset($setting->weekends) ? explode(',', $setting->weekends) : [];
                    echo form_multiselect("weekends[]", $array, set_value('weekends', $expHoliday), "id='weekends' class='form-control'");
                    ?>
                    <span class="control-label"><?= form_error('weekends[]') ?></span>
                </div>
            </div>

            <!-- Auto Invoice row -->
            <div style="margin-top:16px; padding-top:16px; border-top:1px solid #f1f5f9;">
                <div class="st-grid cols-2">
                    <div class="st-field <?= (form_error('auto_invoice_generate') || form_error('automation')) ? 'has-error' : '' ?>">
                        <label><?= $this->lang->line("setting_school_auto_invoice_generate") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Enable/Disable auto invoice generation monthly"></i></label>
                        <div class="st-autoinvoice-row">
                            <div id="autoinvoicediv">
                                <?php
                                $array = array("0" => $this->lang->line("setting_school_no"), "1" => $this->lang->line("setting_school_yes"));
                                echo form_dropdown("auto_invoice_generate", $array, set_value("auto_invoice_generate", $setting->auto_invoice_generate), "id='auto_invoice_generate' class='form-control select2'");
                                ?>
                            </div>
                            <div id="automation_wrap">
                                <?php
                                $dayArray = array();
                                for ($i = 1; $i <= 28; $i++) { $dayArray[$i] = $i; }
                                echo form_dropdown("automation", $dayArray, set_value("automation", $setting->automation), "id='automation' class='form-control select2' style='width:100px'");
                                ?>
                            </div>
                        </div>
                        <span class="control-label"><?= form_error('auto_invoice_generate') ?: form_error('automation') ?></span>
                    </div>

                    <div class="st-field <?= form_error('photo') ? 'has-error' : '' ?>">
                        <label><?= $this->lang->line("setting_logo") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Set organization logo"></i></label>
                        <div class="input-group image-preview">
                            <input type="text" class="form-control image-preview-filename" disabled="disabled">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                    <span class="fa fa-remove"></span> <?= $this->lang->line('setting_clear') ?>
                                </button>
                                <div class="btn btn-success image-preview-input">
                                    <span class="fa fa-repeat"></span>
                                    <span class="image-preview-input-title"><?= $this->lang->line('setting_file_browse') ?></span>
                                    <input type="file" accept="image/png, image/jpeg, image/gif" name="photo">
                                </div>
                            </span>
                        </div>
                        <span class="control-label"><?= form_error('photo') ?></span>
                    </div>
                </div>
            </div>

            <!-- Toggle options -->
            <div style="margin-top:16px; padding-top:16px; border-top:1px solid #f1f5f9;">
                <div class="st-grid">
                    <div class="st-field">
                        <label><?= $this->lang->line("setting_school_random_admission_number") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Generate random admission number on student creation"></i></label>
                        <div class="st-checkbox-row">
                            <input type="checkbox" id="isRandomAdmissionNumber" name="isRandomAdmissionNumber" value="1" <?= (isset($setting->isRandomAdmissionNumber) && $setting->isRandomAdmissionNumber == 1) ? 'checked' : '' ?>>
                            <label for="isRandomAdmissionNumber">Enable random admission number</label>
                        </div>
                        <span class="control-label"><?= form_error('isRandomAdmissionNumber') ?></span>
                    </div>

                    <div class="st-field">
                        <label>Display Phone in Receipt? <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Show phone number on fee receipts"></i></label>
                        <div class="st-checkbox-row">
                            <input type="checkbox" id="isrecieptphone" name="isrecieptphone" value="1" <?= (isset($setting->isrecieptphone) && $setting->isrecieptphone == 1) ? 'checked' : '' ?>>
                            <label for="isrecieptphone">Show phone number on receipt</label>
                        </div>
                        <span class="control-label"><?= form_error('isrecieptphone') ?></span>
                    </div>

                    <div class="st-field">
                        <label>Auto Invoice on Student Creation <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Generate auto invoice when admitting a student"></i></label>
                        <div class="st-toggle-group">
                            <label><input type="radio" name="is_student_auto_invoice" value="0" <?= (isset($setting->is_student_auto_invoice) && $setting->is_student_auto_invoice == 0) ? 'checked' : '' ?>> No</label>
                            <label><input type="radio" name="is_student_auto_invoice" value="1" <?= (isset($setting->is_student_auto_invoice) && $setting->is_student_auto_invoice == 1) ? 'checked' : '' ?>> School Fee</label>
                            <label><input type="radio" id="is_student_auto_invoice1" name="is_student_auto_invoice" value="2" <?= (isset($setting->is_student_auto_invoice) && $setting->is_student_auto_invoice == 2) ? 'checked' : '' ?>> Term Fee</label>
                        </div>
                        <span class="control-label"><?= form_error('is_student_auto_invoice') ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ── Auto Update ── -->
    <div class="st-section">
        <div class="st-section-head"><i class="fa fa-refresh"></i> <?= $this->lang->line('setting_auto_update') ?></div>
        <div class="st-section-body">
            <div class="st-grid cols-2">
                <div class="st-field <?= form_error('auto_update_notification') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_auto_update_notification") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Enable/Disable auto update notification for main system admin"></i></label>
                    <?php
                    $autoupdateArray[1] = $this->lang->line('setting_enable');
                    $autoupdateArray[0] = $this->lang->line('setting_disable');
                    echo form_dropdown("auto_update_notification", $autoupdateArray, set_value("auto_update_notification", $setting->auto_update_notification), "id='auto_update_notification' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('auto_update_notification') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Captcha ── -->
    <div class="st-section">
        <div class="st-section-head"><i class="fa fa-shield"></i> <?= $this->lang->line('setting_captcha') ?></div>
        <div class="st-section-body">
            <div class="st-grid">
                <div class="st-field <?= form_error('captcha_status') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_captcha") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Enable/Disable captcha on login page"></i></label>
                    <?php
                    $captchaArray[0] = $this->lang->line('setting_enable');
                    $captchaArray[1] = $this->lang->line('setting_disable');
                    echo form_dropdown("captcha_status", $captchaArray, set_value("captcha_status", $setting->captcha_status), "id='captcha_status' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('captcha_status') ?></span>
                </div>

                <div class="st-field <?= form_error('recaptcha_site_key') ? 'has-error' : '' ?>" id="recaptcha_site_key_id">
                    <label><?= $this->lang->line("setting_school_recaptcha_site_key") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="reCAPTCHA site key — invalid key will lock login"></i></label>
                    <input type="text" class="form-control" id="recaptcha_site_key" name="recaptcha_site_key" value="<?= set_value('recaptcha_site_key', $setting->recaptcha_site_key) ?>">
                    <span class="control-label"><?= form_error('recaptcha_site_key') ?></span>
                </div>

                <div class="st-field <?= form_error('recaptcha_secret_key') ? 'has-error' : '' ?>" id="recaptcha_secret_key_id">
                    <label><?= $this->lang->line("setting_school_recaptcha_secret_key") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="reCAPTCHA secret key — invalid key will lock login"></i></label>
                    <input type="text" class="form-control" id="recaptcha_secret_key" name="recaptcha_secret_key" value="<?= set_value('recaptcha_secret_key', $setting->recaptcha_secret_key) ?>">
                    <span class="control-label"><?= form_error('recaptcha_secret_key') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Attendance Notification ── -->
    <div class="st-section">
        <div class="st-section-head"><i class="fa fa-bell"></i> <?= $this->lang->line('setting_attendance_notification') ?></div>
        <div class="st-section-body">
            <div class="st-grid">
                <div class="st-field <?= form_error('attendance_notification') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_attendance_notification") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Select attendance notification method"></i></label>
                    <?php
                    $array = array(
                        "none"  => $this->lang->line("setting_none"),
                        "email" => $this->lang->line("setting_email"),
                        "sms"   => $this->lang->line("setting_sms")
                    );
                    echo form_dropdown("attendance_notification", $array, set_value("attendance_notification", $setting->attendance_notification), "id='attendance_notification' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('attendance_notification') ?></span>
                </div>

                <div class="st-field <?= form_error('attendance_smsgateway') ? 'has-error' : '' ?>" id="mainSmsDiv">
                    <div id="attendance_smsgateway_div">
                        <label><?= $this->lang->line("setting_attendance_smsgateway") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Select SMS gateway"></i></label>
                        <?php
                        $array = array(
                            "0"     => $this->lang->line("setting_select_sms_gateway"),
                            "msg91" => $this->lang->line("setting_msg91")
                        );
                        echo form_dropdown("attendance_smsgateway", $array, set_value("attendance_smsgateway", $setting->attendance_smsgateway), "id='attendance_smsgateway' class='form-control select2'");
                        ?>
                        <span class="control-label"><?= form_error('attendance_smsgateway') ?></span>
                    </div>
                </div>

                <div class="st-field <?= form_error('attendance_notification_template') ? 'has-error' : '' ?>" id="attendance_notification_template_div">
                    <label><?= $this->lang->line("setting_attendance_notification_template") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Select attendance notification template"></i></label>
                    <?php
                    $attendanceNotificationArray = array("0" => $this->lang->line("setting_select_template"));
                    if (customCompute($attendance_notification_templates)) {
                        foreach ($attendance_notification_templates as $tpl) {
                            $attendanceNotificationArray[$tpl->mailandsmstemplateID] = $tpl->name;
                        }
                    }
                    echo form_dropdown("attendance_notification_template", $attendanceNotificationArray, set_value("attendance_notification_template", $setting->attendance_notification_template), "id='attendance_notification_template' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('attendance_notification_template') ?></span>
                </div>

                <div class="st-field <?= form_error('attendance_voice_notification') ? 'has-error' : '' ?>">
                    <label><?= $this->lang->line("setting_attendance_voice_notification") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Select voice call notification"></i></label>
                    <?php
                    $array = array("none" => $this->lang->line("setting_none"), "voicecall" => "Voice Call");
                    echo form_dropdown("attendance_voice_notification", $array, set_value("attendance_voice_notification", isset($setting->attendance_voice_notification) ? $setting->attendance_voice_notification : ''), "id='attendance_voice_notification' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('attendance_voice_notification') ?></span>
                </div>

                <div class="st-field <?= form_error('attendance_voice_notification_template') ? 'has-error' : '' ?>" id="attendance_voice_notification_template_div">
                    <label><?= $this->lang->line("setting_attendance_voice_notification_template") ?> <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Select voice notification template"></i></label>
                    <?php
                    $attendanceNotificationArray = array("0" => $this->lang->line("setting_select_template"));
                    if (customCompute($attendance_notification_templates1)) {
                        foreach ($attendance_notification_templates1 as $tpl) {
                            $attendanceNotificationArray[$tpl->mailandsmstemplateID] = $tpl->name;
                        }
                    }
                    echo form_dropdown("attendance_voice_notification_template", $attendanceNotificationArray, set_value("attendance_voice_notification_template", isset($setting->attendance_voice_notification_template) ? $setting->attendance_voice_notification_template : ''), "id='attendance_voice_notification_template' class='form-control select2'");
                    ?>
                    <span class="control-label"><?= form_error('attendance_voice_notification_template') ?></span>
                </div>

                <div class="st-field <?= form_error('teacher_present_time') ? 'has-error' : '' ?>">
                    <label>Teacher Attendance Time <i class="fa fa-question-circle st-info-icon" data-toggle="tooltip" title="Set teacher attendance cutoff time"></i></label>
                    <input type="text" class="form-control" id="teacher_present_time" name="teacher_present_time" value="<?= set_value('teacher_present_time', isset($setting->teacher_present_time) ? $setting->teacher_present_time : '') ?>">
                    <span class="control-label"><?= form_error('teacher_present_time') ?></span>
                </div>
            </div>

            <div style="margin-top:16px; padding-top:16px; border-top:1px solid #f1f5f9;">
                <div class="st-grid">
                    <div class="st-field">
                        <label>Biometric Integration</label>
                        <div class="st-toggle-group">
                            <label><input type="radio" id="yes" name="is_biometric" value="1" <?= ($setting->is_biometric == 1) ? 'checked' : '' ?>> Yes</label>
                            <label><input type="radio" id="no" name="is_biometric" value="0" <?= ($setting->is_biometric == 0) ? 'checked' : '' ?>> No</label>
                        </div>
                        <span class="control-label"><?= form_error('is_biometric') ?></span>
                    </div>

                    <div class="st-field">
                        <label>Fee SMS Notification</label>
                        <div class="st-toggle-group">
                            <label><input type="radio" name="is_fee_sms" value="1" <?= ($setting->is_fee_sms == 1) ? 'checked' : '' ?>> Enable</label>
                            <label><input type="radio" name="is_fee_sms" value="0" <?= ($setting->is_fee_sms == 0) ? 'checked' : '' ?>> Disable</label>
                        </div>
                        <span class="control-label"><?= form_error('is_fee_sms') ?></span>
                    </div>

                    <div class="st-field">
                        <label>Show Attendance on Progress Card</label>
                        <div class="st-toggle-group">
                            <label><input type="radio" name="is_display_attendance_on_progresscard" value="1" <?= ($setting->is_display_attendance_on_progresscard == 1) ? 'checked' : '' ?>> Yes</label>
                            <label><input type="radio" name="is_display_attendance_on_progresscard" value="0" <?= ($setting->is_display_attendance_on_progresscard == 0) ? 'checked' : '' ?>> No</label>
                        </div>
                        <span class="control-label"><?= form_error('is_display_attendance_on_progresscard') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Correspondence Signature ── -->
    <div class="st-section">
        <div class="st-section-head"><i class="fa fa-pencil"></i> Correspondence Signature</div>
        <div class="st-section-body">
            <div class="st-note"><b>Note:</b> Image size should be between 300×500 and 100×150 pixels and less than 2MB.</div>
            <div class="st-grid cols-2">
                <div class="st-field <?= form_error('signature') ? 'has-error' : '' ?>">
                    <label>Upload Signature</label>
                    <input type="hidden" name="correspondent_signature" value="<?= $setting->correspondent_signature ?>">
                    <label class="st-file-btn">
                        <i class="fa fa-upload"></i> Choose File
                        <input type="file" name="image" accept="image/*">
                    </label>
                    <span class="control-label"><?= form_error('signature') ?></span>
                </div>
                <?php if (!empty($setting->correspondent_signature)): ?>
                <div class="st-field">
                    <label>Current Signature</label>
                    <div class="st-img-preview">
                        <img src="<?= base_url('/uploads/signatures/') . $setting->correspondent_signature ?>" style="width:150px;height:50px;object-fit:contain;">
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── ID Card Template ── -->
    <div class="st-section">
        <div class="st-section-head"><i class="fa fa-id-card-o"></i> ID Card Template</div>
        <div class="st-section-body">
            <div class="st-note"><b>Note:</b> Template size should match ID card dimensions and be less than 2MB.</div>
            <div class="st-grid cols-2">
                <div class="st-field <?= form_error('id_card_template') ? 'has-error' : '' ?>">
                    <label>Upload Template</label>
                    <input type="hidden" name="id_card_template" value="<?= $setting->id_card_template ?>">
                    <label class="st-file-btn">
                        <i class="fa fa-upload"></i> Choose File
                        <input type="file" name="id_card_template_file" accept="image/*">
                    </label>
                    <span class="control-label"><?= form_error('id_card_template') ?></span>
                </div>
                <?php if (!empty($setting->id_card_template)): ?>
                <div class="st-field">
                    <label>Current Template</label>
                    <div class="st-img-preview">
                        <img src="<?= base_url('uploads/idcard_templates/') . $setting->id_card_template ?>" style="width:250px;height:150px;object-fit:contain;">
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Save -->
    <div class="st-save-bar">
        <input type="submit" class="st-save-bar btn-save" value="<?= $this->lang->line("update_setting") ?>">
    </div>

</form>

<!-- Backend theme box (hidden) -->
<div class="box d-none" style="margin-bottom:40px;display:none!important;">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-th-large"></i> <?= $this->lang->line('backend_theme_setting') ?></h3>
    </div>
    <div class="box-body">
        <div class="row"><div class="col-sm-12">
            <ul class="list-unstyled clearfix">
                <?php if (customCompute($themes)) { foreach ($themes as $theme) { ?>
                    <li class="backendThemeMainWidht" style="float:left;padding:5px;">
                        <a id="<?= $theme->themesID ?>" data-toggle="tooltip" data-placement="top" title="<?= $theme->themename ?>" data-skin="skin-green-light" style="display:block;box-shadow:0 0 3px rgba(0,0,0,.4);cursor:pointer;" class="clearfix full-opacity-hover backendThemeEvent">
                            <div>
                                <span class="backendThemeHeadHeight" style="display:block;width:20%;float:left;background:<?= $theme->topcolor ?>"></span>
                                <span class="backendThemeHeadHeight" style="display:block;width:80%;float:left;background:<?= $theme->topcolor ?>"></span>
                            </div>
                            <div>
                                <span class="backendThemeBodyHeight" style="display:block;width:20%;float:left;background:<?= $theme->leftcolor ?>"></span>
                                <span class="backendThemeBodyHeight" style="display:block;width:80%;float:left;background:#f4f5f7" id="themeBodyContent-<?= strtolower(str_replace(' ', '', $theme->themename)) ?>">
                                    <?php if ($setting->backend_theme == strtolower(str_replace(' ', '', $theme->themename))): ?>
                                        <center class="backendThemeBodyMargin"><button type="button" class="btn btn-danger"><i class="fa fa-check-circle"></i></button></center>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </a>
                        <p class="text-center no-margin" style="font-size:12px"><?= $theme->themename ?></p>
                    </li>
                <?php } } ?>
            </ul>
        </div></div>
    </div>
</div>

<?php if (form_error('recaptcha_site_key') || form_error('recaptcha_secret_key')): ?>
<script>$('#recaptcha_site_key_id').show();$('#recaptcha_secret_key_id').show();</script>
<?php endif; ?>

<script type="text/javascript">
    <?php if ($this->data["siteinfos"]->attendance_notification == 'sms') { ?>
        $("#mainSmsDiv").show(); $("#attendance_smsgateway_div").show(); $("#attendance_notification_template_div").show();
    <?php } elseif ($this->data["siteinfos"]->attendance_notification == 'email') { ?>
        $("#mainSmsDiv").hide(); $("#attendance_smsgateway_div").hide(); $("#attendance_notification_template_div").show();
    <?php } else { ?>
        $("#mainSmsDiv").hide(); $("#attendance_smsgateway_div").hide(); $("#attendance_notification_template_div").hide();
    <?php } ?>

    <?php if ($attendance_notification == 'sms') { ?>
        $("#mainSmsDiv").show(); $("#attendance_smsgateway_div").show(); $("#attendance_notification_template_div").show();
    <?php } elseif ($attendance_notification == 'email') { ?>
        $("#mainSmsDiv").hide(); $("#attendance_smsgateway_div").hide(); $("#attendance_notification_template_div").show();
    <?php } else { ?>
        $("#mainSmsDiv").hide('slow'); $("#attendance_smsgateway_div").hide(); $("#attendance_notification_template_div").hide();
    <?php } ?>

    <?php if (isset($this->data["siteinfos"]->attendance_voice_notification) && $this->data["siteinfos"]->attendance_voice_notification == 'voicecall') { ?>
        $("#attendance_voice_notification_template_div").show();
    <?php } else { ?>
        $("#attendance_voice_notification_template_div").hide();
    <?php } ?>

    $(document).on('change', "#attendance_notification", function() {
        var value = $(this).val();
        if (value == 'sms') {
            $("#mainSmsDiv").show('slow'); $("#attendance_smsgateway_div").show('slow'); $("#attendance_notification_template_div").show('slow');
        } else if (value == 'email') {
            $("#mainSmsDiv").hide('slow'); $("#attendance_smsgateway_div").hide('slow'); $("#attendance_notification_template_div").show('slow');
        } else {
            $("#mainSmsDiv").hide('slow'); $("#attendance_smsgateway_div").hide('slow'); $("#attendance_notification_template_div").hide('slow');
        }
        if (value == 'sms' || value == 'email') {
            $.ajax({ type:'POST', url:"<?= base_url('setting/getTemplate') ?>", data:{"value":value}, dataType:"html",
                success:function(data){ $('#attendance_notification_template').html(data); }
            });
        }
    });

    $(document).on('change', "#attendance_voice_notification", function() {
        var value = $(this).val();
        if (value == 'voicecall') { $("#attendance_voice_notification_template_div").show('slow'); }
        else { $("#attendance_voice_notification_template_div").hide('slow'); }
    });

    $(document).ready(function() {
        $('.backendThemeEvent').click(function() {
            var id = $(this).attr('id');
            if (id) {
                $.ajax({ type:'POST', url:"<?= base_url('setting/backendtheme') ?>", data:"id="+id, dataType:"html",
                    success:function(data) {
                        $('#headStyleCSSLink').attr('href', "<?= base_url('assets/inilabs/themes/') ?>" + data + "/style.css?v=1.0");
                        $('#headInilabsCSSLink').attr('href', "<?= base_url('assets/inilabs/themes/') ?>" + data + "/inilabs.css");
                        var $html = '<center class="backendThemeBodyMargin"><button type="button" class="btn btn-danger"><i class="fa fa-check-circle"></i></button></center>';
                        $('.backendThemeBodyMargin').hide();
                        $('#themeBodyContent-' + data).html($html);
                        if (data) { toastr["success"]("<?= $this->lang->line('menu_success') ?>"); }
                    }
                });
            }
        });
    });

    $('#captcha_status').change(function() {
        var v = $(this).val();
        if (v == 0) { $('#recaptcha_site_key_id').show(300); $('#recaptcha_secret_key_id').show(300); }
        else { $('#recaptcha_site_key_id').hide(300); $('#recaptcha_secret_key_id').hide(300); }
    });

    <?php if ($captcha_status == 0) { ?>
        $('#recaptcha_site_key_id').show(300); $('#recaptcha_secret_key_id').show(300);
    <?php } else { ?>
        $('#recaptcha_site_key_id').hide(300); $('#recaptcha_secret_key_id').hide(300);
    <?php } ?>

    <?php if ($setting->auto_invoice_generate) { ?>
        $('#automation').show(); $('#autoinvoicediv').addClass('col-sm-6');
    <?php } else { ?>
        $('#automation').hide(); $('#autoinvoicediv').addClass('col-sm-12');
    <?php } ?>

    $('#auto_invoice_generate').change(function() {
        var aig = $(this).val();
        if (aig == 1) {
            $('#s2id_automation').show(1000);
            $("#auto_invoice_generate").fadeIn("slow", function() { $('#autoinvoicediv').removeClass('col-sm-12').addClass('col-sm-6'); });
        } else {
            $('#s2id_automation').hide(1000);
            $("#auto_invoice_generate").fadeIn("slow", function() { $('#autoinvoicediv').removeClass('col-sm-6').addClass('col-sm-12'); });
        }
    });

    $(document).on('click', '#close-preview', function() {
        $('.image-preview').popover('hide');
        $('.image-preview').hover(
            function() { $('.image-preview').popover('show'); $('.content').css('padding-bottom','120px'); },
            function() { $('.image-preview').popover('hide'); $('.content').css('padding-bottom','20px'); }
        );
    });

    $(function() {
        var closebtn = $('<button/>', { type:"button", text:'x', id:'close-preview', style:'font-size:initial;' });
        closebtn.attr("class", "close pull-right");
        $('.image-preview').popover({ trigger:'manual', html:true, title:"<strong>Preview</strong>"+$(closebtn)[0].outerHTML, content:"There's no image", placement:'bottom' });
        $('.image-preview-clear').click(function() {
            $('.image-preview').attr("data-content","").popover('hide');
            $('.image-preview-filename').val(""); $('.image-preview-clear').hide();
            $('.image-preview-input input:file').val("");
            $(".image-preview-input-title").text("<?= $this->lang->line('setting_file_browse') ?>");
        });
        $(".image-preview-input input:file").change(function() {
            var img = $('<img/>', { id:'dynamic', width:250, height:200, overflow:'hidden' });
            var file = this.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                $(".image-preview-input-title").text("<?= $this->lang->line('setting_clear') ?>");
                $(".image-preview-clear").show(); $(".image-preview-filename").val(file.name);
                img.attr('src', e.target.result);
                $(".image-preview").attr("data-content", $(img)[0].outerHTML).popover("show");
                $('.content').css('padding-bottom','120px');
            };
            reader.readAsDataURL(file);
        });
    });

    $(".select2").select2({ placeholder:"", maximumSelectionSize:6 });
    $('#weekends').select2();
</script>
