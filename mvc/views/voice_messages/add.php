<?php $upload_error = isset($upload_error) ? $upload_error : ''; ?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-microphone"></i> <?php echo $this->lang->line('voice_add'); ?></h3>
    </div>
    <div class="box-body">
        <form id="vmForm" method="post" action="<?php echo base_url('voice_messages/add'); ?>" enctype="multipart/form-data">
            <?php echo form_error('voice_name', '<div class="alert alert-danger">', '</div>'); ?>
            <?php if ($upload_error): ?>
                <div class="alert alert-danger"><?php echo $upload_error; ?></div>
            <?php endif; ?>

            <!-- Voice Name -->
            <div class="form-group">
                <label><?php echo $this->lang->line('voice_name_label'); ?> <span class="text-danger">*</span></label>
                <input type="text" name="voice_name" class="form-control" value="<?php echo set_value('voice_name'); ?>" placeholder="Enter voice message name" required>
            </div>

            <!-- Audio Input Tabs -->
            <div class="form-group">
                <label><?php echo $this->lang->line('voice_audio_label'); ?> <span class="text-danger">*</span></label>
                <ul class="nav nav-tabs" id="audioTabs">
                    <li class="active"><a href="#tab-upload" data-toggle="tab"><i class="fa fa-upload"></i> <?php echo $this->lang->line('voice_tab_upload'); ?></a></li>
                    <li><a href="#tab-record" data-toggle="tab"><i class="fa fa-microphone"></i> <?php echo $this->lang->line('voice_tab_record'); ?></a></li>
                </ul>
                <div class="tab-content" style="border:1px solid #ddd;border-top:0;padding:16px;">
                    <!-- Upload Tab -->
                    <div class="tab-pane active" id="tab-upload">
                        <input type="file" name="audio_file" id="audio_file" accept=".mp3,.wav,.ogg,.m4a,.aac" class="form-control">
                        <small class="text-muted">Accepted formats: MP3, WAV, OGG, M4A, AAC. Max 10MB.</small>
                        <div id="upload-preview" style="display:none;margin-top:10px;">
                            <label>Preview:</label>
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
                            <div id="rec-status" style="color:#888;margin-bottom:10px;">Press the mic button to start recording</div>
                        </div>
                        <div id="rec-preview" style="display:none;margin-top:10px;">
                            <label>Preview Recording:</label>
                            <audio id="rec-audio-player" controls style="width:100%;"></audio>
                            <br>
                            <button type="button" id="reRecBtn" class="btn btn-warning btn-sm" style="margin-top:8px;">
                                <i class="fa fa-refresh"></i> Re-record
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="audio_source" id="audio_source" value="">
            <input type="hidden" name="temp_audio_file" id="temp_audio_file" value="">

            <a href="<?php echo base_url('voice_messages/index'); ?>" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fa fa-save"></i> Save Voice Message
            </button>
        </form>
    </div>
</div>

<script>
$(function() {
    // Tab switch sets audio_source
    $('#audioTabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var tab = $(e.target).attr('href');
        if (tab === '#tab-upload') {
            if ($('#audio_file')[0].files.length > 0) {
                $('#audio_source').val('upload');
            } else {
                $('#audio_source').val('');
            }
        } else {
            if ($('#recorded_audio_data').val()) {
                $('#audio_source').val('record');
            } else {
                $('#audio_source').val('');
            }
        }
    });

    // File selected -> set source, show preview
    $('#audio_file').on('change', function() {
        var file = this.files[0];
        if (file) {
            $('#audio_source').val('upload');
            var url = URL.createObjectURL(file);
            $('#upload-audio-player').attr('src', url);
            $('#upload-preview').show();
        }
    });

    // Recording
    var mediaRecorder, audioChunks = [], timerInterval, seconds = 0;

    function formatTime(s) {
        return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
    }

    var uploadTempUrl = '<?php echo base_url("voice_messages/upload_temp"); ?>';

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
                    url: uploadTempUrl,
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
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
        }).catch(function(err) {
            alert('Microphone access denied: ' + err.message);
        });
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
        $('#rec-status').text('Press the mic button to start recording');
    });

    // Form submit validation
    $('#vmForm').on('submit', function(e) {
        var src = $('#audio_source').val();
        if (!src) {
            e.preventDefault();
            alert('Please upload an audio file or record your voice before saving.');
            return false;
        }
    });
});
</script>
