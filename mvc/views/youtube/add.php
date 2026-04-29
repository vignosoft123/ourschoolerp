<div class="box">
    <div class="box-header">
        <h3 class="box-title">
            <i class="fa fa-youtube-play text-danger"></i>
            <?php echo $this->lang->line('youtube_add'); ?>
        </h3>
        <ol class="breadcrumb pull-right" style="margin-top:5px;">
            <li><a href="<?php echo base_url('youtube/index'); ?>"><?php echo $this->lang->line('youtube_title'); ?></a></li>
            <li class="active"><?php echo $this->lang->line('youtube_add'); ?></li>
        </ol>
    </div>
    <div class="box-body">

        <?php echo form_open(base_url('youtube/add'), ['class' => 'form-horizontal']); ?>

            <!-- Title -->
            <div class="form-group <?php echo form_error('title') ? 'has-error' : ''; ?>">
                <label class="col-sm-2 control-label"><?php echo $this->lang->line('youtube_title_label'); ?> <span class="text-danger">*</span></label>
                <div class="col-sm-6">
                    <input type="text" name="title" id="title" class="form-control"
                           value="<?php echo set_value('title'); ?>"
                           placeholder="Enter video title">
                    <?php echo form_error('title', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>

            <!-- YouTube Link -->
            <div class="form-group <?php echo form_error('link') ? 'has-error' : ''; ?>">
                <label class="col-sm-2 control-label"><?php echo $this->lang->line('youtube_link'); ?> <span class="text-danger">*</span></label>
                <div class="col-sm-6">
                    <input type="text" name="link" id="link" class="form-control"
                           value="<?php echo set_value('link'); ?>"
                           placeholder="https://www.youtube.com/watch?v=...">
                    <?php echo form_error('link', '<span class="help-block">', '</span>'); ?>
                </div>
                <div class="col-sm-4">
                    <div id="link_preview" style="display:none;">
                        <img id="yt_thumbnail" src="" alt="thumbnail"
                             style="width:120px;height:68px;object-fit:cover;border-radius:4px;margin-top:4px;">
                    </div>
                </div>
            </div>

            <!-- Class -->
            <div class="form-group <?php echo form_error('class_id') ? 'has-error' : ''; ?>" id="classDiv">
                <label class="col-sm-2 control-label"><?php echo $this->lang->line('youtube_class'); ?> <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <?php
                    $classArr = ['0' => 'Please Select'];
                    if (customCompute($classes))
                        foreach ($classes as $c)
                            $classArr[$c->classesID] = $c->classes;
                    echo form_dropdown('class_id', $classArr, set_value('class_id'), "id='class_id' class='form-control select2'");
                    ?>
                    <?php echo form_error('class_id', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>

            <!-- Section (AJAX) -->
            <div class="form-group" id="sectionDiv" style="display:none;">
                <label class="col-sm-2 control-label"><?php echo $this->lang->line('youtube_section'); ?></label>
                <div class="col-sm-4">
                    <?php echo form_dropdown('section_id', ['0' => 'Please Select'], set_value('section_id'), "id='section_id' class='form-control select2'"); ?>
                </div>
            </div>

            <!-- Subject (AJAX) -->
            <div class="form-group" id="subjectDiv" style="display:none;">
                <label class="col-sm-2 control-label"><?php echo $this->lang->line('youtube_subject'); ?></label>
                <div class="col-sm-4">
                    <?php echo form_dropdown('subject_id', ['0' => 'Please Select'], set_value('subject_id'), "id='subject_id' class='form-control select2'"); ?>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $this->lang->line('youtube_description'); ?></label>
                <div class="col-sm-6">
                    <textarea name="description" id="description" class="form-control" rows="3"
                              placeholder="Optional description"><?php echo set_value('description'); ?></textarea>
                </div>
            </div>

            <!-- Sort Order -->
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $this->lang->line('youtube_sort_order'); ?></label>
                <div class="col-sm-2">
                    <input type="number" name="sort_order" id="sort_order" class="form-control"
                           value="<?php echo set_value('sort_order', '0'); ?>" min="0">
                </div>
            </div>

            <!-- Buttons -->
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-6">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> <?php echo $this->lang->line('youtube_save_btn'); ?>
                    </button>
                    <a href="<?php echo base_url('youtube/index'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> <?php echo $this->lang->line('youtube_back_btn'); ?>
                    </a>
                </div>
            </div>

        <?php echo form_close(); ?>

    </div>
</div>

<script>
$('.select2').select2();

// Live thumbnail preview on URL input
$('#link').on('input', function() {
    var url = $(this).val();
    var match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|v\/|shorts\/))([a-zA-Z0-9_-]{11})/);
    if (match) {
        $('#yt_thumbnail').attr('src', 'https://img.youtube.com/vi/' + match[1] + '/mqdefault.jpg');
        $('#link_preview').show();
    } else {
        $('#link_preview').hide();
    }
});

// Class change → load Section + Subject
$(document).on('change', '#class_id', function() {
    var id = $(this).val();
    if (id == '0') {
        $('#section_id').html('<option value="0">Please Select</option>');
        $('#subject_id').html('<option value="0">Please Select</option>');
        $('#sectionDiv').hide('fast');
        $('#subjectDiv').hide('fast');
    } else {
        $('#sectionDiv').show('fast');
        $('#subjectDiv').show('fast');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url("youtube/getSection"); ?>',
            data: { classesID: id },
            dataType: 'html',
            success: function(d) { $('#section_id').html(d); $('#section_id').trigger('change.select2'); }
        });
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url("youtube/getSubject"); ?>',
            data: { classesID: id },
            dataType: 'html',
            success: function(d) { $('#subject_id').html(d); $('#subject_id').trigger('change.select2'); }
        });
    }
});
</script>
