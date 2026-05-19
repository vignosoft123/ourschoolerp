<section class="content-header">
    <h1>Push Notification</h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Push Notification</li>
    </ol>
</section>

<section class="content">
<div class="row">
<div class="col-md-12">

<?php if (!$service_account_ok): ?>
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong><i class="fa fa-exclamation-triangle"></i> Firebase Not Configured!</strong>
    The service account is missing or for the wrong project.
    <a href="<?= base_url('Push_notification/setup') ?>" class="btn btn-sm btn-danger" style="margin-left:10px;">
        <i class="fa fa-cog"></i> Fix Now
    </a>
</div>
<?php endif; ?>

<div class="row">

    <!-- ── Compose Form ─────────────────────────────────── -->
    <div class="col-md-7">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-bell"></i> Compose Notification</h3>
            <div class="box-tools pull-right">
                <a href="<?= base_url('Push_notification/history') ?>" class="btn btn-sm btn-default"><i class="fa fa-history"></i> History</a>
                <a href="<?= base_url('Push_notification/setup') ?>"   class="btn btn-sm btn-default"><i class="fa fa-cog"></i> Setup</a>
            </div>
        </div>
        <div class="box-body">
        <div class="form-horizontal">

            <!-- Role -->
            <div class="form-group">
                <label class="col-sm-3 control-label">Role <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <select id="pn_role" class="form-control select2">
                        <option value="">— Select Role —</option>
                        <option value="3" selected>Student</option>
                        <option value="2">Teacher</option>
                        <option value="4">Parent</option>
                    </select>
                </div>
            </div>

            <!-- School Year — visible by default (Student is pre-selected) -->
            <div class="form-group" id="div_schoolyear">
                <label class="col-sm-3 control-label">School Year</label>
                <div class="col-sm-9">
                    <select id="pn_schoolyear" class="form-control select2">
                        <option value="">— All Years —</option>
                        <?php if (!empty($topbarschoolyears)): ?>
                        <?php foreach ($topbarschoolyears as $sy): ?>
                        <option value="<?= $sy->schoolyearID ?>"
                            <?= ($sy->schoolyearID == ($schoolyearsessionobj->schoolyearID ?? 0)) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sy->schoolyear) ?>
                        </option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <!-- Class -->
            <div class="form-group" id="div_class">
                <label class="col-sm-3 control-label">Class</label>
                <div class="col-sm-9">
                    <select id="pn_class" class="form-control select2">
                        <option value="0">— All Classes —</option>
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c->classesID ?>"><?= htmlspecialchars($c->classes) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Section -->
            <div class="form-group" id="div_section" style="display:none;">
                <label class="col-sm-3 control-label">Section</label>
                <div class="col-sm-9">
                    <select id="pn_section" class="form-control select2">
                        <option value="0">— All Sections —</option>
                    </select>
                </div>
            </div>

            <!-- Users multi-select -->
            <div class="form-group" id="div_users" style="display:none;">
                <label class="col-sm-3 control-label">
                    Users
                    <div id="user_count_badge" class="text-muted" style="font-weight:normal; font-size:11px; margin-top:3px;"></div>
                </label>
                <div class="col-sm-9">
                    <select id="pn_users" name="pn_users[]" class="form-control select2" multiple="multiple" style="width:100%;">
                    </select>
                    <span class="help-block" style="margin-top:5px;">
                        <i class="fa fa-info-circle"></i> Only students with the app installed are shown. All are pre-selected — deselect any you don't want to notify.
                    </span>
                </div>
            </div>

            <!-- Non-student role info -->
            <div class="form-group" id="div_role_info" style="display:none;">
                <div class="col-sm-9 col-sm-offset-3">
                    <div class="alert alert-info" style="margin-bottom:0; padding:8px 12px;">
                        <i class="fa fa-info-circle"></i>
                        <span id="role_info_text"></span>
                    </div>
                </div>
            </div>

            <hr style="margin: 10px 0 15px;">

            <!-- Notification Type -->
            <div class="form-group">
                <label class="col-sm-3 control-label">Type</label>
                <div class="col-sm-9">
                    <select id="pn_type" class="form-control select2">
                        <option value="general">General</option>
                        <option value="exam_alert">Exam Alert</option>
                        <option value="fee_reminder">Fee Reminder</option>
                        <option value="holiday">Holiday Notice</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
            </div>

            <!-- Title -->
            <div class="form-group">
                <label class="col-sm-3 control-label">Title <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="text" id="pn_title" class="form-control" placeholder="Notification title" maxlength="200">
                    <span class="help-block text-right" id="title_count">0 / 200</span>
                </div>
            </div>

            <!-- Message -->
            <div class="form-group">
                <label class="col-sm-3 control-label">Message <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <textarea id="pn_message" class="form-control" rows="4" placeholder="Enter your message..." maxlength="500"></textarea>
                    <span class="help-block text-right" id="msg_count">0 / 500</span>
                </div>
            </div>

            <!-- Image URL (optional) -->
            <div class="form-group">
                <label class="col-sm-3 control-label">Image <span class="text-muted" style="font-weight:normal;">(optional)</span></label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-image"></i></span>
                        <input type="url" id="pn_image_url" class="form-control" placeholder="https://example.com/image.jpg">
                    </div>
                    <span class="help-block" style="margin-top:4px;">
                        <i class="fa fa-info-circle"></i> Public HTTPS image URL. Shows as a banner image in the notification (Android). Recommended: JPEG/PNG, 1024×512 px.
                        </span>
                        <span>
                        EX: https://picsum.photos/200/100.jpg
                    </span>
                    <div id="img_preview_wrap" style="display:none; margin-top:8px;">
                        <img id="img_preview" src="" alt="Preview" style="max-width:100%; max-height:120px; border-radius:4px; border:1px solid #ddd;">
                    </div>
                </div>
            </div>

        </div><!-- /.form-horizontal -->
        </div><!-- /.box-body -->
        <div class="box-footer">
            <button id="btn_send" class="btn btn-primary btn-lg">
                <i class="fa fa-paper-plane"></i> Send Notification
            </button>
            <span id="sending_spinner" style="display:none; margin-left:12px;">
                <i class="fa fa-spinner fa-spin"></i> Sending...
            </span>
        </div>
    </div><!-- /.box -->
    </div>

    <!-- ── Result / Tips ─────────────────────────────────── -->
    <div class="col-md-5">

        <div id="result_panel" style="display:none;">
            <div class="box" id="result_box">
                <div class="box-header with-border">
                    <h3 class="box-title" id="result_title"></h3>
                </div>
                <div class="box-body" id="result_body"></div>
            </div>
        </div>

        <div class="box box-default" id="tips_panel">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-info-circle"></i> How to use</h3>
            </div>
            <div class="box-body">
                <ol style="padding-left:18px; line-height:2.4;">
                    <li>Select <strong>Role</strong> → School Year → Class → Section to filter.</li>
                    <li>All matching students are automatically loaded in the <strong>Users</strong> field.</li>
                    <li>Deselect any students you don't want to include.</li>
                    <li>Type a <strong>Title</strong> and <strong>Message</strong>, then click <strong>Send</strong>.</li>
                    <li>Only users who have logged into the mobile app receive notifications.</li>
                </ol>
            </div>
        </div>

    </div>

</div><!-- /.row -->
</div>
</div>
</section>

<script>
$(function () {
    // Initialize select2 on all .select2 elements (same pattern as other pages)
    $('.select2').select2();

    var URL_SECTIONS = '<?= base_url('Push_notification/load_sections') ?>';
    var URL_STUDENTS = '<?= base_url('Push_notification/load_students') ?>';
    var URL_SEND     = '<?= base_url('Push_notification/send') ?>';
    var URL_HISTORY  = '<?= base_url('Push_notification/history') ?>';

    // ── Role visibility helper ───────────────────────────────
    function applyRoleChange(role) {
        if (role == 3) {
            // Student — show class/year filters
            $('#div_schoolyear, #div_class').show();
            $('#div_section, #div_users, #div_role_info').hide();
            $('#pn_section').html('<option value="0">— All Sections —</option>');
            clearUsers();
        } else if (role == 2) {
            // Teacher
            $('#div_schoolyear, #div_class, #div_section, #div_users').hide();
            $('#div_role_info').show();
            $('#role_info_text').text('Notification will be sent to all Teachers who have logged into the mobile app.');
            clearUsers();
        } else if (role == 4) {
            // Parent
            $('#div_schoolyear, #div_class, #div_section, #div_users').hide();
            $('#div_role_info').show();
            $('#role_info_text').text('Notification will be sent to all Parents who have logged into the mobile app.');
            clearUsers();
        } else {
            $('#div_schoolyear, #div_class, #div_section, #div_users, #div_role_info').hide();
            clearUsers();
        }
    }

    // ── Role change ─────────────────────────────────────────
    $('#pn_role').on('change', function () {
        applyRoleChange($(this).val());
    });

    // Apply on load, then again after 250ms to override any spurious events
    // fired by the global select2 initializer in the layout footer
    applyRoleChange($('#pn_role').val());
    setTimeout(function () { applyRoleChange($('#pn_role').val()); }, 250);

    // ── School Year change ──────────────────────────────────
    $('#pn_schoolyear').on('change', function () {
        var classesID = $('#pn_class').val();
        if (classesID && classesID != 0) {
            loadStudents();
        }
    });

    // ── Class change ────────────────────────────────────────
    $('#pn_class').on('change', function () {
        var classesID = $(this).val();
        if (!classesID || classesID == 0) {
            $('#div_section, #div_users').hide();
            $('#pn_section').html('<option value="0">— All Sections —</option>');
            clearUsers();
            return;
        }
        // Load sections
        $.getJSON(URL_SECTIONS, { classesID: classesID }, function (data) {
            var opts = '<option value="0">— All Sections —</option>';
            $.each(data, function (i, s) {
                opts += '<option value="' + s.sectionID + '">' + s.section + '</option>';
            });
            $('#pn_section').html(opts).trigger('change.select2');
            if (data.length > 0) $('#div_section').show();
        });
        // Show users panel immediately, then populate via AJAX
        $('#div_users').show();
        loadStudents();
    });

    // ── Section change ──────────────────────────────────────
    $('#pn_section').on('change', function () {
        var classesID = $('#pn_class').val();
        if (classesID && classesID != 0) {
            $('#div_users').show();
            loadStudents();
        }
    });

    // ── Load students (reusable) ────────────────────────────
    function loadStudents() {
        var params = {
            schoolyearID: $('#pn_schoolyear').val() || 0,
            classesID:    $('#pn_class').val()      || 0,
            sectionID:    $('#pn_section').val()    || 0,
        };
        $('#user_count_badge').text('Loading...');
        $.ajax({
            url:      URL_STUDENTS,
            data:     params,
            dataType: 'json',
            success: function (data) {
                var opts = '';
                $.each(data, function (i, s) {
                    opts += '<option value="' + s.id + '" selected>' + $('<span>').text(s.text).html() + '</option>';
                });
                $('#pn_users').html(opts).trigger('change');
                $('#div_users').show();
                updateUserBadge();
            },
            error: function () {
                $('#pn_users').html('').trigger('change');
                $('#user_count_badge').html('<span class="text-red"><i class="fa fa-exclamation-triangle"></i> Failed to load students</span>');
                $('#div_users').show();
            }
        });
    }

    function clearUsers() {
        $('#pn_users').html('').trigger('change');
        $('#user_count_badge').text('');
    }

    function updateUserBadge() {
        var total    = $('#pn_users option').length;
        var selected = $('#pn_users').val() ? $('#pn_users').val().length : 0;
        $('#user_count_badge').text(selected + ' / ' + total + ' selected');
    }

    $('#pn_users').on('change', function () { updateUserBadge(); });

    // ── Character counters ──────────────────────────────────
    $('#pn_title').on('input',   function () { $('#title_count').text($(this).val().length + ' / 200'); });
    $('#pn_message').on('input', function () { $('#msg_count').text($(this).val().length + ' / 500'); });

    // ── Image URL live preview ──────────────────────────────
    $('#pn_image_url').on('input', function () {
        var url = $.trim($(this).val());
        if (url) {
            $('#img_preview').attr('src', url);
            $('#img_preview_wrap').show();
        } else {
            $('#img_preview_wrap').hide();
        }
    });

    // ── Send ────────────────────────────────────────────────
    $('#btn_send').on('click', function () {
        var role    = $('#pn_role').val();
        var title   = $.trim($('#pn_title').val());
        var message = $.trim($('#pn_message').val());

        if (!role)    { alert('Please select a Role.');    return; }
        if (!title)   { alert('Please enter a Title.');    return; }
        if (!message) { alert('Please enter a Message.');  return; }

        var selectedUsers = $('#pn_users').val();
        if ($('#div_users').is(':visible') && (!selectedUsers || selectedUsers.length === 0)) {
            alert('No users selected. Please select at least one user.'); return;
        }

        $('#btn_send').prop('disabled', true);
        $('#sending_spinner').show();
        $('#result_panel').hide();

        var postData = {
            role:              role,
            schoolyearID:      $('#pn_schoolyear').val() || 0,
            classesID:         $('#pn_class').val()      || 0,
            sectionID:         $('#pn_section').val()    || 0,
            title:             title,
            message:           message,
            notification_type: $('#pn_type').val(),
            image_url:         $.trim($('#pn_image_url').val()),
        };

        if (selectedUsers && selectedUsers.length > 0) {
            postData['userIDs[]'] = selectedUsers;
        }

        $.ajax({
            url:         URL_SEND,
            type:        'POST',
            data:        postData,
            dataType:    'json',
            traditional: true,
            success:     function (res) { showResult(res); },
            error:       function ()    { showResult({ status: false, message: 'Server error. Please try again.' }); },
            complete:    function ()    { $('#btn_send').prop('disabled', false); $('#sending_spinner').hide(); }
        });
    });

    // ── Result panel ────────────────────────────────────────
    function showResult(res) {
        $('#result_box').removeClass('box-success box-danger');
        $('#tips_panel').hide();

        if (res.status) {
            $('#result_box').addClass('box-success');
            $('#result_title').html('<i class="fa fa-check-circle text-green"></i> Sent Successfully');
            $('#result_body').html(
                '<table class="table table-condensed" style="margin-bottom:0;">' +
                '<tr><td>Total Recipients</td><td><strong>' + res.totalRecipients + '</strong></td></tr>' +
                '<tr><td>Delivered</td><td><strong class="text-green">' + res.successCount + '</strong></td></tr>' +
                '<tr><td>Failed</td><td><strong class="' + (res.failureCount > 0 ? 'text-red' : '') + '">' + res.failureCount + '</strong></td></tr>' +
                '</table>' +
                '<div style="margin-top:10px;"><a href="' + URL_HISTORY + '" class="btn btn-sm btn-default"><i class="fa fa-history"></i> View History</a></div>'
            );
        } else {
            $('#result_box').addClass('box-danger');
            $('#result_title').html('<i class="fa fa-times-circle text-red"></i> Send Failed');
            $('#result_body').html('<p class="text-red">' + (res.message || 'Unknown error') + '</p>');
        }
        $('#result_panel').show();
    }
});
</script>
