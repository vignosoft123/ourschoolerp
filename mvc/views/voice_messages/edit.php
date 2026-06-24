<?php $upload_error = isset($upload_error) ? $upload_error : ''; ?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-microphone text-red"></i>
            <?php echo $this->lang->line('voice_edit'); ?>
        </h3>
        <ol class="breadcrumb pull-right" style="margin-top:5px;">
            <li><a href="<?php echo base_url('voice_messages/index'); ?>"><?php echo $this->lang->line('voice_messages_title'); ?></a></li>
            <li class="active"><?php echo $this->lang->line('voice_edit'); ?></li>
        </ol>
    </div>
    <div class="box-body">

        <?php if ($upload_error): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $upload_error; ?>
            </div>
        <?php endif; ?>

        <form id="vmForm" method="post" action="<?php echo base_url('voice_messages/edit/' . $voice->id); ?>" enctype="multipart/form-data" class="form-horizontal">

            <!-- Voice Name -->
            <div class="form-group <?php echo form_error('voice_name') ? 'has-error' : ''; ?>">
                <label class="col-sm-2 control-label"><?php echo $this->lang->line('voice_name_label'); ?> <span class="text-danger">*</span></label>
                <div class="col-sm-6">
                    <input type="text" name="voice_name" class="form-control" value="<?php echo htmlspecialchars($voice->voice_name); ?>" placeholder="Enter voice message name">
                    <?php echo form_error('voice_name', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>

            <!-- Class -->
            <div class="form-group">
                <label class="col-sm-2 control-label">Class <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <?php
                    $classArr = ['0' => 'Please Select'];
                    if (customCompute($classes))
                        foreach ($classes as $c)
                            $classArr[$c->classesID] = $c->classes;
                    $selectedClass = isset($voice->class_id) ? $voice->class_id : 0;
                    echo form_dropdown('class_id', $classArr, $selectedClass, "id='class_id' class='form-control select2'");
                    ?>
                </div>
            </div>

            <!-- Section (AJAX) -->
            <div class="form-group" id="sectionDiv" <?php echo ($selectedClass && $selectedClass != '0') ? '' : 'style="display:none;"'; ?>>
                <label class="col-sm-2 control-label">Section</label>
                <div class="col-sm-4">
                    <?php echo form_dropdown('section_id', ['0' => 'Please Select'], isset($voice->section_id) ? $voice->section_id : 0, "id='section_id' class='form-control select2'"); ?>
                </div>
            </div>

            <!-- Current Audio -->
            <div class="form-group">
                <label class="col-sm-2 control-label">Current Audio</label>
                <div class="col-sm-6">
                    <audio controls style="width:100%;">
                        <source src="<?php echo base_url('uploads/voice_messages/' . $voice->file_name); ?>">
                        Your browser does not support audio.
                    </audio>
                    <small class="text-muted">Original file: <?php echo htmlspecialchars($voice->file_original_name); ?></small>
                </div>
            </div>

            <!-- Replace Audio -->
            <div class="form-group">
                <label class="col-sm-2 control-label">Replace Audio <small class="text-muted">(optional)</small></label>
                <div class="col-sm-8">
                    <div class="nav-tabs-custom" style="margin-bottom:0;">
                        <ul class="nav nav-tabs" id="audioTabs">
                            <li class="active"><a href="#tab-upload" data-toggle="tab"><i class="fa fa-upload"></i> <?php echo $this->lang->line('voice_tab_upload'); ?></a></li>
                            <li><a href="#tab-record" data-toggle="tab"><i class="fa fa-microphone"></i> <?php echo $this->lang->line('voice_tab_record'); ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <!-- Upload Tab -->
                            <div class="tab-pane active" id="tab-upload">
                                <input type="file" name="audio_file" id="audio_file" accept=".mp3,.wav,.ogg,.m4a,.aac" class="form-control">
                                <small class="text-muted">Accepted formats: MP3, WAV, OGG, M4A, AAC. Max 10MB. Leave blank to keep current audio.</small>
                                <div id="upload-preview" style="display:none;margin-top:10px;">
                                    <label>Preview New File:</label>
                                    <audio id="upload-audio-player" controls style="width:100%;"></audio>
                                </div>
                            </div>
                            <!-- Record Tab -->
                            <div class="tab-pane" id="tab-record">
                                <div style="text-align:center;padding:10px 0;">
                                    <button type="button" id="startRecBtn" class="btn btn-danger btn-lg" style="border-radius:50%;width:70px;height:70px;">
                                        <i class="fa fa-microphone fa-2x"></i>
                                    </button>
                                    <button type="button" id="stopRecBtn" class="btn btn-default btn-lg" style="border-radius:50%;width:70px;height:70px;display:none;">
                                        <i class="fa fa-stop fa-2x"></i>
                                    </button>
                                    <div id="rec-timer" style="font-size:22px;font-weight:bold;color:#e00;margin:10px 0;display:none;">00:00</div>
                                    <div id="rec-status" style="color:#888;margin-bottom:10px;">Press the mic button to record a new audio</div>
                                </div>
                                <div id="rec-preview" style="display:none;margin-top:10px;">
                                    <label>Preview New Recording:</label>
                                    <audio id="rec-audio-player" controls style="width:100%;"></audio>
                                    <br>
                                    <button type="button" id="reRecBtn" class="btn btn-warning btn-sm" style="margin-top:8px;">
                                        <i class="fa fa-refresh"></i> Re-record
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="audio_source" id="audio_source" value="">
            <input type="hidden" name="temp_audio_file" id="temp_audio_file" value="">

            <!-- Buttons -->
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-6">
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="fa fa-save"></i> <?php echo $this->lang->line('voice_update_btn'); ?>
                    </button>
                    <a href="<?php echo base_url('voice_messages/index'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> <?php echo $this->lang->line('voice_back_btn'); ?>
                    </a>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
$('.select2').select2();

$(document).on('change', '#class_id', function() {
    var id = $(this).val();
    if (id == '0') {
        $('#section_id').html('<option value="0">Please Select</option>');
        $('#sectionDiv').hide('fast');
    } else {
        $('#sectionDiv').show('fast');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url("voice_messages/getSection"); ?>',
            data: { classesID: id },
            dataType: 'html',
            success: function(d) { $('#section_id').html(d); $('#section_id').trigger('change.select2'); }
        });
    }
});

// Pre-populate section on load
var existingClassId   = '<?php echo isset($voice->class_id)   ? (int)$voice->class_id   : 0; ?>';
var existingSectionId = '<?php echo isset($voice->section_id) ? (int)$voice->section_id : 0; ?>';
if (existingClassId && existingClassId != '0') {
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url("voice_messages/getSection"); ?>',
        data: { classesID: existingClassId },
        dataType: 'html',
        success: function(d) {
            $('#section_id').html(d);
            if (existingSectionId) { $('#section_id').val(existingSectionId); }
            $('#section_id').trigger('change.select2');
        }
    });
}

$(function() {
    $('#audioTabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var tab = $(e.target).attr('href');
        if (tab === '#tab-upload') {
            $('#audio_source').val($('#audio_file')[0].files.length > 0 ? 'upload' : '');
        } else {
            $('#audio_source').val($('#recorded_audio_data').val() ? 'record' : '');
        }
    });

    $('#audio_file').on('change', function() {
        var file = this.files[0];
        if (file) {
            $('#audio_source').val('upload');
            $('#upload-audio-player').attr('src', URL.createObjectURL(file));
            $('#upload-preview').show();
        } else {
            $('#audio_source').val('');
            $('#upload-preview').hide();
        }
    });

    var uploadTempUrl = '<?php echo base_url("voice_messages/upload_temp"); ?>';
    var mediaRecorder, audioChunks = [], timerInterval, seconds = 0;

    function formatTime(s) {
        return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
    }

    $('#startRecBtn').on('click', function() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Your browser does not support audio recording. Please use the Upload tab instead.');
            return;
        }
        navigator.mediaDevices.getUserMedia({audio: true}).then(function(stream) {
            audioChunks = [];
            mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.ondataavailable = function(e) { audioChunks.push(e.data); };
            mediaRecorder.onstop = function() {
                stream.getTracks().forEach(function(t) { t.stop(); });
                var blob = new Blob(audioChunks, {type: 'audio/webm'});
                var previewUrl = URL.createObjectURL(blob);
                $('#rec-status').text('Uploading recording...');
                var fd = new FormData();
                fd.append('audio_file', blob, 'recording.webm');
                $.ajax({
                    url: uploadTempUrl, type: 'POST', data: fd, processData: false, contentType: false,
                    success: function(res) {
                        if (res.success) {
                            $('#temp_audio_file').val(res.file_name);
                            $('#audio_source').val('record');
                            $('#rec-audio-player').attr('src', previewUrl);
                            $('#rec-preview').show();
                            $('#rec-status').text('Recording saved. You can preview or re-record.');
                        } else {
                            $('#rec-status').text('Upload failed: ' + res.error);
                        }
                    },
                    error: function() { $('#rec-status').text('Upload failed. Please try again.'); }
                });
            };
            mediaRecorder.start();
            seconds = 0;
            $('#rec-timer').text(formatTime(0)).show();
            timerInterval = setInterval(function() { seconds++; $('#rec-timer').text(formatTime(seconds)); }, 1000);
            $('#startRecBtn').hide();
            $('#stopRecBtn').show();
            $('#rec-status').text('Recording... Press stop when done.');
            $('#rec-preview').hide();
        }).catch(function(err) { alert('Microphone access denied: ' + err.message); });
    });

    $('#stopRecBtn').on('click', function() {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
            clearInterval(timerInterval);
            $('#stopRecBtn').hide();
            $('#startRecBtn').show();
            $('#rec-timer').hide();
        }
    });

    $('#reRecBtn').on('click', function() {
        $('#temp_audio_file').val('');
        $('#audio_source').val('');
        $('#rec-preview').hide();
        $('#rec-audio-player').attr('src', '');
        $('#rec-status').text('Press the mic button to record a new audio');
    });

    // No audio validation on edit — empty audio_source means keep existing
});
</script>
