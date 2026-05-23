<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
.taf-wrap { padding: 4px 0 20px; }
.taf-section { margin-bottom: 18px; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #dde0e6; }
.taf-section-header { background: linear-gradient(135deg, #1a73e8 0%, #1558b0 100%); color: #fff; padding: 11px 20px; display: flex; align-items: center; gap: 9px; font-weight: 600; font-size: 14px; }
.taf-section-body { background: #f7f9ff; padding: 18px 18px 6px; }
.taf-section-body .form-group { margin-bottom: 16px; }
.taf-section-body label.control-label { font-weight: 600; font-size: 12.5px; color: #3d4050; margin-bottom: 5px; display: block; }
.taf-section-body .form-control { border-radius: 7px; border: 1.5px solid #d0d5e0; font-size: 13.5px; height: 36px; padding: 6px 11px; background: #fff; transition: border-color 0.2s; }
.taf-section-body select.form-control { height: 36px; }
.taf-section-body .form-control:focus { border-color: #1a73e8; box-shadow: 0 0 0 3px rgba(26,115,232,0.1); outline: none; }
.taf-section-body .has-error .form-control { border-color: #e53935 !important; }
.taf-err { font-size: 11.5px; color: #e53935; margin-top: 3px; display: block; min-height: 16px; }
.taf-file-btn { display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #1a73e8, #1558b0); color: #fff; border: none; border-radius: 7px; padding: 7px 14px; font-size: 12.5px; font-weight: 600; cursor: pointer; white-space: nowrap; margin-bottom: 0; }
.taf-file-btn:hover { opacity: 0.88; }
.taf-file-name { font-size: 12px; color: #666; }
.taf-file-clear { background: #e53935; color: #fff; border: none; border-radius: 6px; padding: 4px 10px; font-size: 11.5px; cursor: pointer; display: none; }
.taf-preview-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #ddd; margin-bottom: 6px; display: block; }
.taf-sig-img  { width: 160px; height: 70px; object-fit: contain; border-radius: 6px; border: 2px solid #ddd; background: #fff; margin-bottom: 6px; display: block; }
.taf-file-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 4px; }
.taf-current-label { font-size: 11px; color: #888; margin-top: 3px; }
.taf-btn-row { padding: 10px 0 20px; text-align: center; }
.taf-submit-btn { background: linear-gradient(135deg, #1a73e8 0%, #1558b0 100%); color: #fff; border: none; border-radius: 8px; padding: 10px 44px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 3px 10px rgba(26,115,232,0.25); }
.taf-submit-btn:hover { opacity: 0.9; transform: translateY(-1px); }
</style>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa icon-teacher"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("teacher/index")?>"><?=$this->lang->line('menu_teacher')?></a></li>
            <li class="active"><?=$this->lang->line('menu_edit')?> <?=$this->lang->line('menu_teacher')?></li>
        </ol>
    </div>
    <div class="box-body">
        <form class="teacher-form-info taf-wrap" role="form" method="post" enctype="multipart/form-data">

            <!-- Section 1: Basic Information -->
            <div class="taf-section">
                <div class="taf-section-header">
                    <i class="fa fa-user"></i> Basic Information
                </div>
                <div class="taf-section-body">
                    <div class="row">
                        <div class="col-md-4 form-group <?= form_error('name') ? 'has-error' : '' ?>">
                            <label for="name" class="control-label"><?=$this->lang->line("teacher_name")?> <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?=set_value('name', $teacher->name)?>">
                            <span class="taf-err"><?= form_error('name') ?></span>
                        </div>
                        <div class="col-md-4 form-group <?= form_error('rfid') ? 'has-error' : '' ?>">
                            <label for="rfid" class="control-label">RFID</label>
                            <input type="text" class="form-control" id="rfid" name="rfid" value="<?=set_value('rfid', $teacher->rfid)?>">
                            <span class="taf-err"><?= form_error('rfid') ?></span>
                        </div>
                        <div class="col-md-4 form-group <?= form_error('designation') ? 'has-error' : '' ?>">
                            <label for="designation" class="control-label"><?=$this->lang->line("teacher_designation")?> <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="designation" name="designation" value="<?=set_value('designation', $teacher->designation)?>">
                            <span class="taf-err"><?= form_error('designation') ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group <?= form_error('dob') ? 'has-error' : '' ?>">
                            <label for="dob" class="control-label"><?=$this->lang->line("teacher_dob")?> <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="dob" name="dob" value="<?=set_value('dob', date("d-m-Y", strtotime($teacher->dob)))?>">
                            <span class="taf-err"><?= form_error('dob') ?></span>
                        </div>
                        <div class="col-md-4 form-group <?= form_error('jod') ? 'has-error' : '' ?>">
                            <label for="jod" class="control-label"><?=$this->lang->line("teacher_jod")?> <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="jod" name="jod" value="<?=set_value('jod', date("d-m-Y", strtotime($teacher->jod)))?>">
                            <span class="taf-err"><?= form_error('jod') ?></span>
                        </div>
                        <div class="col-md-4 form-group <?= form_error('sex') ? 'has-error' : '' ?>">
                            <label for="sex" class="control-label"><?=$this->lang->line("teacher_sex")?></label>
                            <?= form_dropdown("sex", array($this->lang->line('teacher_sex_male') => $this->lang->line('teacher_sex_male'), $this->lang->line('teacher_sex_female') => $this->lang->line('teacher_sex_female')), set_value("sex", $teacher->sex), "id='sex' class='form-control'") ?>
                            <span class="taf-err"><?= form_error('sex') ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group <?= form_error('religion') ? 'has-error' : '' ?>">
                            <label for="religion" class="control-label"><?=$this->lang->line("teacher_religion")?></label>
                            <input type="text" class="form-control" id="religion" name="religion" value="<?=set_value('religion', $teacher->religion)?>">
                            <span class="taf-err"><?= form_error('religion') ?></span>
                        </div>
                        <div class="col-md-4 form-group <?= form_error('phone') ? 'has-error' : '' ?>">
                            <label for="phone" class="control-label"><?=$this->lang->line("teacher_phone")?> <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?=set_value('phone', $teacher->phone)?>" maxlength="10">
                            <span class="taf-err"><?= form_error('phone') ?></span>
                        </div>
                        <div class="col-md-4 form-group <?= form_error('address') ? 'has-error' : '' ?>">
                            <label for="address" class="control-label"><?=$this->lang->line("teacher_address")?></label>
                            <input type="text" class="form-control" id="address" name="address" value="<?=set_value('address', $teacher->address)?>">
                            <span class="taf-err"><?= form_error('address') ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group <?= form_error('email') ? 'has-error' : '' ?>">
                            <label for="email" class="control-label"><?=$this->lang->line("teacher_email")?> <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="email" name="email" value="<?=set_value('email', $teacher->email)?>">
                            <span class="taf-err"><?= form_error('email') ?></span>
                        </div>
                        <div class="col-md-4 form-group <?= form_error('default_login_time') ? 'has-error' : '' ?>">
                            <label for="default_login_time" class="control-label"><?=$this->lang->line("default_login_time")?></label>
                            <input placeholder="Select Time" type="text" class="form-control" id="default_login_time" name="default_login_time" value="<?=set_value('default_login_time', $teacher->default_login_time)?>">
                            <span class="taf-err"><?= form_error('default_login_time') ?></span>
                        </div>
                        <div class="col-md-4 form-group <?= form_error('default_logout_time') ? 'has-error' : '' ?>">
                            <label for="default_logout_time" class="control-label"><?=$this->lang->line("default_logout_time")?></label>
                            <input placeholder="Select Time" type="text" class="form-control" id="default_logout_time" name="default_logout_time" value="<?=set_value('default_logout_time', $teacher->default_logout_time)?>">
                            <span class="taf-err"><?= form_error('default_logout_time') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Photo & Signature -->
            <div class="taf-section">
                <div class="taf-section-header">
                    <i class="fa fa-image"></i> Photo &amp; Signature
                </div>
                <div class="taf-section-body">
                    <div class="row">
                        <!-- Photo -->
                        <div class="col-md-4 form-group <?= form_error('photo') ? 'has-error' : '' ?>">
                            <label class="control-label"><?=$this->lang->line("teacher_photo")?></label>
                            <?php if ($teacher->photo && $teacher->photo != 'default.png'): ?>
                            <img id="taf-photo-preview" class="taf-preview-img"
                                 src="<?=base_url('uploads/images/'.$teacher->photo)?>"
                                 onerror="this.style.display='none'"
                                 alt="Current photo">
                            <div class="taf-current-label">Current photo</div>
                            <?php else: ?>
                            <img id="taf-photo-preview" class="taf-preview-img" src="" alt="Preview">
                            <?php endif; ?>
                            <div class="taf-file-row">
                                <label class="taf-file-btn">
                                    <i class="fa fa-repeat"></i> <?=$this->lang->line('teacher_file_browse')?>
                                    <input type="file" name="photo" id="taf-photo-input" accept="image/png,image/jpeg,image/gif" style="display:none;">
                                </label>
                                <span id="taf-photo-name" class="taf-file-name">No file chosen</span>
                                <button type="button" id="taf-photo-clear" class="taf-file-clear"><i class="fa fa-times"></i></button>
                            </div>
                            <span class="taf-err"><?= form_error('photo') ?></span>
                        </div>
                        <!-- Signature -->
                        <div class="col-md-5 form-group <?= form_error('signature') ? 'has-error' : '' ?>">
                            <label class="control-label"><?=$this->lang->line("teacher_signature")?></label>
                            <?php
                                $sig_src = '';
                                if ($teacher->signature && $teacher->signature != 'default.png') {
                                    foreach ([FCPATH.'uploads/signatures/'.$teacher->signature, FCPATH.'uploads/images/'.$teacher->signature] as $sp) {
                                        if (file_exists($sp)) {
                                            $mime = (strpos($teacher->signature, '.png') !== false) ? 'image/png' : 'image/jpeg';
                                            $sig_src = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($sp));
                                            break;
                                        }
                                    }
                                }
                            ?>
                            <?php if ($sig_src): ?>
                            <img id="taf-sig-preview" class="taf-sig-img" src="<?=$sig_src?>" alt="Current signature">
                            <div class="taf-current-label">Current signature</div>
                            <?php else: ?>
                            <img id="taf-sig-preview" class="taf-sig-img" src="" alt="Preview">
                            <?php endif; ?>
                            <div class="taf-file-row">
                                <label class="taf-file-btn">
                                    <i class="fa fa-repeat"></i> <?=$this->lang->line('teacher_file_browse')?>
                                    <input type="file" name="signature" id="taf-sig-input" accept="image/png,image/jpeg,image/gif" style="display:none;">
                                </label>
                                <span id="taf-sig-name" class="taf-file-name">No file chosen</span>
                                <button type="button" id="taf-sig-clear" class="taf-file-clear"><i class="fa fa-times"></i></button>
                            </div>
                            <span class="taf-err"><?= form_error('signature') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Login Credentials -->
            <div class="taf-section">
                <div class="taf-section-header">
                    <i class="fa fa-lock"></i> Login Credentials
                </div>
                <div class="taf-section-body">
                    <div class="row">
                        <div class="col-md-4 form-group <?= form_error('username') ? 'has-error' : '' ?>">
                            <label for="username" class="control-label"><?=$this->lang->line("teacher_username")?> <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" value="<?=set_value('username', $teacher->username)?>">
                            <span class="taf-err"><?= form_error('username') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="taf-btn-row">
                <button type="submit" class="taf-submit-btn"><i class="fa fa-save"></i> <?=$this->lang->line("update_teacher")?></button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
// Username: no spaces
$('#username').on('input', function() { $(this).val($(this).val().replace(/\s/g, '')); });

// Date pickers
$('#dob').datepicker({ startView: 2 });
$('#jod').datepicker();

// Flatpickr time
flatpickr("#default_login_time",  { enableTime: true, noCalendar: true, dateFormat: "H:i" });
flatpickr("#default_logout_time", { enableTime: true, noCalendar: true, dateFormat: "H:i" });

// Auto-capitalize first letter
$(document).on('input', '#name, #designation, #religion, #address', function() {
    var pos = this.selectionStart, v = $(this).val();
    if (v.length > 0) { $(this).val(v.charAt(0).toUpperCase() + v.slice(1)); try { this.setSelectionRange(pos,pos); } catch(e){} }
});

// Name: letters, spaces, hyphens only
$(document).on('keypress', '#name', function(e) {
    if (!/[a-zA-Z\s.\-']/.test(String.fromCharCode(e.which || e.keyCode))) e.preventDefault();
});

// Phone: digits only, max 10
$(document).on('keypress', '#phone', function(e) {
    var c = e.which || e.keyCode; if (c < 48 || c > 57) e.preventDefault();
});
$(document).on('input', '#phone', function() {
    $(this).val($(this).val().replace(/\D/g,'').slice(0,10));
});

// Photo preview
$('#taf-photo-input').on('change', function() {
    var file = this.files[0]; if (!file) return;
    $('#taf-photo-name').text(file.name); $('#taf-photo-clear').show();
    var r = new FileReader();
    r.onload = function(e) { $('#taf-photo-preview').attr('src', e.target.result).show(); };
    r.readAsDataURL(file);
});
$('#taf-photo-clear').on('click', function() {
    $('#taf-photo-input').val(''); $('#taf-photo-name').text('No file chosen');
    $('#taf-photo-preview').hide().attr('src',''); $(this).hide();
});

// Signature preview
$('#taf-sig-input').on('change', function() {
    var file = this.files[0]; if (!file) return;
    $('#taf-sig-name').text(file.name); $('#taf-sig-clear').show();
    var r = new FileReader();
    r.onload = function(e) { $('#taf-sig-preview').attr('src', e.target.result).show(); };
    r.readAsDataURL(file);
});
$('#taf-sig-clear').on('click', function() {
    $('#taf-sig-input').val(''); $('#taf-sig-name').text('No file chosen');
    $('#taf-sig-preview').hide().attr('src',''); $(this).hide();
});

// Form validation
$('.teacher-form-info').on('submit', function(e) {
    var ok = true, $f = $(this);
    function svErr($el, msg) { $el.closest('.form-group').addClass('has-error'); $el.closest('.form-group').find('.taf-err').text(msg); }
    function svOk($el)       { $el.closest('.form-group').removeClass('has-error'); $el.closest('.form-group').find('.taf-err').text(''); }

    var nm = $.trim($f.find('#name').val());
    if (!nm) { svErr($f.find('#name'), 'Name is required'); ok = false; } else { svOk($f.find('#name')); }

    var des = $.trim($f.find('#designation').val());
    if (!des) { svErr($f.find('#designation'), 'Designation is required'); ok = false; } else { svOk($f.find('#designation')); }

    var dob = $.trim($f.find('#dob').val());
    if (!dob) { svErr($f.find('#dob'), 'Date of birth is required'); ok = false; } else { svOk($f.find('#dob')); }

    var jod = $.trim($f.find('#jod').val());
    if (!jod) { svErr($f.find('#jod'), 'Joining date is required'); ok = false; } else { svOk($f.find('#jod')); }

    var em = $.trim($f.find('#email').val());
    if (!em) { svErr($f.find('#email'), 'Email is required'); ok = false; }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { svErr($f.find('#email'), 'Enter a valid email address'); ok = false; }
    else { svOk($f.find('#email')); }

    var ph = $.trim($f.find('#phone').val());
    if (!ph) { svErr($f.find('#phone'), 'Phone is required'); ok = false; }
    else if (ph.length < 10) { svErr($f.find('#phone'), 'Phone must be 10 digits'); ok = false; }
    else { svOk($f.find('#phone')); }

    var un = $.trim($f.find('#username').val());
    if (!un) { svErr($f.find('#username'), 'Username is required'); ok = false; } else { svOk($f.find('#username')); }

    if (!ok) e.preventDefault();
});
</script>
