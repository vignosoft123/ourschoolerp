<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-teacher"></i> <?=$this->lang->line('panel_title')?></h3>

       
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("teacher/index")?>"><?=$this->lang->line('menu_teacher')?></a></li>
            <li class="active"><?=$this->lang->line('menu_edit')?> <?=$this->lang->line('menu_teacher')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <form class="form-horizontal teacher-form-info" role="form" method="post" enctype="multipart/form-data">
                   
                    <?php 
                        if(form_error('name')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="teacher_name" class="control-label">
                            <?=$this->lang->line("teacher_name")?> <span class="text-red">*</span>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="name" name="name" value="<?=set_value('name', $teacher->name)?>" >

                        </div>
                        <span class="control-label">
                            <?php echo form_error('name'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('rfid')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="rfid" class="control-label">
                            RFID
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="rfid" name="rfid" value="<?=set_value('name', $teacher->rfid)?>" >

                        </div>
                        <span class="control-label">
                            <?php echo form_error('rfid'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('designation')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="designation" class=" control-label">
                            <?=$this->lang->line("teacher_designation")?> <span class="text-red">*</span>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="designation" name="designation" value="<?=set_value('designation', $teacher->designation)?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('designation'); ?>
                        </span>
                    </div>

                     <?php 
                        if(form_error('default_login_time')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="default_login_time" class=" control-label">
                           <?=$this->lang->line("default_login_time")?>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="default_login_time" name="default_login_time" value="<?=set_value('default_login_time', $teacher->default_login_time)?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('default_login_time'); ?>
                        </span>
                    </div>

                     <?php 
                        if(form_error('default_logout_time')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="default_logout_time" class=" control-label">
                           <?=$this->lang->line("default_logout_time")?>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="default_logout_time" name="default_logout_time" value="<?=set_value('default_logout_time', $teacher->default_logout_time)?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('default_logout_time'); ?>
                        </span>
                    </div>


                      

                    <?php 
                        if(form_error('dob')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="dob" class="control-label">
                            <?=$this->lang->line("teacher_dob")?> <span class="text-red">*</span>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="dob" name="dob" value="<?=set_value('dob', date("d-m-Y", strtotime($teacher->dob)))?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('dob'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('sex')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="sex" class="control-label">
                            <?=$this->lang->line("teacher_sex")?>
                        </label>
                        <div class="input-field">
                            <?php 
                                echo form_dropdown("sex", array($this->lang->line('teacher_sex_male') => $this->lang->line('teacher_sex_male'), $this->lang->line('teacher_sex_female') => $this->lang->line('teacher_sex_female')), set_value("sex", $teacher->sex), "id='sex' class='form-control'"); 
                            ?>
                        </div>
                        <span class="control-label">
                            <?php echo form_error('sex'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('religion')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="religion" class="control-label">
                            <?=$this->lang->line("teacher_religion")?>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="religion" name="religion" value="<?=set_value('religion', $teacher->religion)?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('religion'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('email')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="email" class="control-label">
                            <?=$this->lang->line("teacher_email")?> <span class="text-red">*</span>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="email" name="email" value="<?=set_value('email', $teacher->email)?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('email'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('phone')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="phone" class="control-label">
                            <?=$this->lang->line("teacher_phone")?>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="phone" name="phone" value="<?=set_value('phone', $teacher->phone)?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('phone'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('address')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="address" class="control-label">
                            <?=$this->lang->line("teacher_address")?>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="address" name="address" value="<?=set_value('address', $teacher->address)?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('address'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('jod')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="jod" class="control-label">
                            <?=$this->lang->line("teacher_jod")?> <span class="text-red">*</span>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="jod" name="jod" value="<?=set_value('jod', date("d-m-Y", strtotime($teacher->jod)))?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('jod'); ?>
                        </span>
                    </div>

                    <!-- Photo + Signature side by side -->
                    <div class="form-group" style="grid-column: span 2;">
                        <div style="display:flex; gap:30px; align-items:flex-start; flex-wrap:wrap;">

                            <!-- Photo -->
                            <div style="flex:1; min-width:200px;">
                                <label class="control-label"><?=$this->lang->line("teacher_photo")?></label>
                                <?php if($teacher->photo && $teacher->photo != 'default.png'): ?>
                                <div style="margin-bottom:8px;" id="current-photo-wrap">
                                    <img id="current-photo-preview"
                                         src="<?=base_url('uploads/images/'.$teacher->photo)?>"
                                         style="width:90px;height:90px;object-fit:cover;border-radius:8px;border:2px solid #ddd;display:block;"
                                         onerror="document.getElementById('current-photo-wrap').style.display='none'">
                                    <div style="font-size:11px;color:#888;margin-top:4px;">Current photo</div>
                                </div>
                                <?php endif; ?>
                                <div class="input-group image-preview">
                                    <input type="text" class="form-control image-preview-filename" disabled="disabled">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                            <span class="fa fa-remove"></span> <?=$this->lang->line('teacher_clear')?>
                                        </button>
                                        <div class="btn btn-primary image-preview-input">
                                            <span class="fa fa-repeat"></span>
                                            <span class="image-preview-input-title"><?=$this->lang->line('teacher_file_browse')?></span>
                                            <input type="file" accept="image/png,image/jpeg,image/gif,.jpeg" name="photo"/>
                                        </div>
                                    </span>
                                </div>
                                <span class="control-label text-danger"><?php echo form_error('photo'); ?></span>
                            </div>

                            <!-- Signature -->
                            <div style="flex:1; min-width:200px;">
                                <label class="control-label"><?=$this->lang->line("teacher_signature")?></label>
                                <?php
                                    $sig_data_uri = null;
                                    $sig_found_path = '';
                                    if($teacher->signature && $teacher->signature != 'default.png') {
                                        $sig_paths = [
                                            FCPATH.'uploads/signatures/'.$teacher->signature,
                                            FCPATH.'uploads/images/'.$teacher->signature,
                                        ];
                                        foreach($sig_paths as $sp) {
                                            if(file_exists($sp)) {
                                                $mime = strpos($teacher->signature, '.png') !== false ? 'image/png' : 'image/jpeg';
                                                $sig_data_uri = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($sp));
                                                $sig_found_path = $sp;
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                <div style="margin-bottom:8px;" id="current-signature-wrap">
                                    <?php if($sig_data_uri): ?>
                                    <img id="current-signature-preview"
                                         src="<?=$sig_data_uri?>"
                                         style="width:180px;height:80px;object-fit:contain;border-radius:4px;border:2px solid #ddd;background:#fff;padding:4px;display:block;">
                                    <div style="font-size:11px;color:#888;margin-top:4px;">Current signature</div>
                                    <?php else: ?>
                                    <div style="width:180px;height:80px;border:1px solid #ddd;border-radius:4px;background:#fff;display:flex;align-items:center;justify-content:center;" id="sig-empty-box">
                                        <i class="fa fa-image" style="font-size:28px;color:#ddd;"></i>
                                    </div>
                                    <div style="font-size:10px;color:#bbb;margin-top:3px;">
                                        DB: <?=htmlspecialchars($teacher->signature ? substr($teacher->signature,0,30).'...' : '(empty)')?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="input-group image-preview-signature">
                                    <input type="text" class="form-control image-preview-filename-signature" disabled="disabled">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default image-preview-clear-signature" style="display:none;">
                                            <span class="fa fa-remove"></span> <?=$this->lang->line('teacher_clear')?>
                                        </button>
                                        <div class="btn btn-primary image-preview-input-signature">
                                            <span class="fa fa-repeat"></span>
                                            <span class="image-preview-input-signature-title"><?=$this->lang->line('teacher_file_browse')?></span>
                                            <input type="file" accept="image/png,image/jpeg,image/gif,.jpeg" name="signature"/>
                                        </div>
                                    </span>
                                </div>
                                <span class="control-label text-danger"><?php echo form_error('signature'); ?></span>
                            </div>

                        </div>
                    </div>

                   

                    <?php 
                        if(form_error('username')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="username" class="control-label">
                            <?=$this->lang->line("teacher_username")?> <span class="text-red">*</span>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="username" name="username" value="<?=set_value('username', $teacher->username)?>" >
                        </div>
                         <span class="control-label">
                            <?php echo form_error('username'); ?>
                        </span>
                    </div> 
                <div class="col-md-12">
                        <div class="btn-center">
                            <input type="submit" class="ose-btn" value="<?=$this->lang->line("update_teacher")?>" >
                        </div>
                    </div>
                    </form>

            </div><!-- col-sm-8 --> 
        </div>
    </div>
</div>


<style>
.image-preview-input-signature {
    position: relative;
    overflow: hidden;
    margin: 0px;
    color: #333;
    background-color: #fff;
    border-color: #ccc;
}
.image-preview-input-signature input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    padding: 0;
    font-size: 20px;
    cursor: pointer;
    opacity: 0;
    filter: alpha(opacity=0);
    overflow: hidden;
}
</style>

<script type="text/javascript">
$('#username').keyup(function() {
    $(this).val($(this).val().replace(/\s/g, ''));
});

$('#dob').datepicker({ startView: 2 });
$('#jod').datepicker();

$(document).on('click', '#close-preview', function(){ 
    $('.image-preview, .image-preview-signature').popover('hide');
    // Hover befor close the preview

    $('.image-preview, .image-preview-signature').hover(
        function () {
           $(this).popover('show');
           $('.content').css('padding-bottom', '120px');
        }, 
         function () {
           $(this).popover('hide');
           $('.content').css('padding-bottom', '20px');
        }
    );    
});

$(function() {
    // Create the close button
    var closebtn = $('<button/>', {
        type:"button",
        text: 'x',
        id: 'close-preview',
        style: 'font-size: initial;',
    });
    closebtn.attr("class","close pull-right");
    // Set the popover default content
    $('.image-preview').popover({
        trigger:'manual',
        html:true,
        title: "<strong>Preview</strong>"+$(closebtn)[0].outerHTML,
        content: "There's no image",
        placement:'bottom'
    });
    // Clear event
    $('.image-preview-clear').click(function(){
        $('.image-preview').attr("data-content","").popover('hide');
        $('.image-preview-filename').val("");
        $('.image-preview-clear').hide();
        $('.image-preview-input input:file').val("");
        $(".image-preview-input-title").text("<?=$this->lang->line('teacher_file_browse')?>");
    }); 
    // Create the preview image
    $(".image-preview-input input:file").change(function (){     
        var img = $('<img/>', {
            id: 'dynamic',
            width:250,
            height:200,
            overflow:'hidden'
        });      
        var file = this.files[0];
        var reader = new FileReader();
        // Set preview image into the popover data-content
        reader.onload = function (e) {
            $(".image-preview-input-title").text("<?=$this->lang->line('teacher_file_browse')?>");
            $(".image-preview-clear").show();
            $(".image-preview-filename").val(file.name);
            img.attr('src', e.target.result);
            $(".image-preview").attr("data-content",$(img)[0].outerHTML).popover("show");
            $('.content').css('padding-bottom', '120px');
            // update inline current-photo preview
            $('#current-photo-preview').attr('src', e.target.result).show();
        }
        reader.readAsDataURL(file);
    });

    // Signature preview
    $('.image-preview-signature').popover({
        trigger:'manual',
        html:true,
        title: "<strong>Preview</strong>"+$(closebtn)[0].outerHTML,
        content: "There's no image",
        placement:'bottom'
    });

    $('.image-preview-clear-signature').click(function(){
        $('.image-preview-signature').attr("data-content","").popover('hide');
        $('.image-preview-filename-signature').val("");
        $('.image-preview-clear-signature').hide();
        $('.image-preview-input-signature input:file').val("");
        $(".image-preview-input-signature-title").text("<?=$this->lang->line('teacher_file_browse')?>");
    }); 

    $(".image-preview-input-signature input:file").change(function (){     
        var img = $('<img/>', {
            id: 'dynamic',
            width:250,
            height:200,
            overflow:'hidden'
        });      
        var file = this.files[0];
        var reader = new FileReader();
        // Set preview image into the popover data-content
        reader.onload = function (e) {
            $(".image-preview-input-signature-title").text("<?=$this->lang->line('teacher_file_browse')?>");
            $(".image-preview-clear-signature").show();
            $(".image-preview-filename-signature").val(file.name);
            img.attr('src', e.target.result);
            $(".image-preview-signature").attr("data-content",$(img)[0].outerHTML).popover("show");
            // update inline current-signature preview
            $('#current-signature-preview').attr('src', e.target.result).show();
            $('.content').css('padding-bottom', '120px');
        }        
        reader.readAsDataURL(file);
    });
});
</script>

<script>
flatpickr("#default_login_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i", // 24-hour format
});

flatpickr("#default_logout_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i", // 24-hour format
});
</script>