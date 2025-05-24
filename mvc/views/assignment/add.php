 <div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-assignment"></i> Homework</h3>

        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("assignment/index")?>"></i> <?=$this->lang->line('menu_assignment')?></a></li>
            <li class="active"><?=$this->lang->line('menu_add')?> <?=$this->lang->line('menu_assignment')?></li>
        </ol>
    </div><!-- /.box-header -->

    <div class="box-body">
        <div class="row filter-box1">
            <div class="col-sm-12">
                <form class="form-horizontal ose-vertical-form-list1" enctype="multipart/form-data" role="form" method="post">
<div id="assignmentContainer">

    <!-- Initial Assignment Block -->
    <div class="assignment-block" style="border: 2px solid #4287f5; padding: 20px; margin-bottom: 20px; border-radius: 10px; background: #e8f0fe; position: relative;">
        <div class="row">

         <?php 
                $classClass = form_error('classesID') ? 'form-group col-md-3 has-error' : 'form-group col-md-3';
            ?>
            <div class="<?=$classClass?>">
                <label><?=$this->lang->line("assignment_classes")?> <span class="text-red">*</span></label>
                <?php
                    $array = array(0 => $this->lang->line("assignment_select_classes"));
                    foreach ($classes as $classa) {
                        $array[$classa->classesID] = $classa->classes;
                    }
                    echo form_dropdown("classesID[]", $array, set_value("classesID[0]"), "class='form-control select2 classesID'");
                ?>
                <span class="control-label"><?php echo form_error('classesID'); ?></span>
            </div>

            <?php 
                $sectionClass = form_error('sectionID') ? 'form-group col-md-3 has-error' : 'form-group col-md-3';
            ?>
            <div class="<?=$sectionClass?>">
                <label><?=$this->lang->line("assignment_section")?></label>
                <?php
                    $array = array();
                    if($sections != "empty") {
                        foreach ($sections as $section) {
                            $array[$section->sectionID] = $section->section;
                        }
                    }
                    echo form_dropdown("sectionID[]", $array, set_value("sectionID[0]"), "class='form-control select2 sectionID'");
                ?>
                <span class="control-label"><?php echo form_error('sectionID'); ?></span>
            </div>

             <?php 
                $deadlineClass = form_error('deadlinedate') ? 'form-group col-md-3 has-error' : 'form-group col-md-3';
            ?>
            <div class="<?=$deadlineClass?>">
                <label>Deadline Date <span class="text-red">*</span></label>
                <input type="text" class="form-control deadlinedate" name="deadlinedate[]" value="<?=set_value('deadlinedate[0]')?>">
                <span class="control-label"><?php echo form_error('deadlinedate'); ?></span>
            </div>


            
        
        </div>

      

        <div class="row">
           

            <?php 
                $subjectClass = form_error('subjectID') ? 'form-group col-md-3 has-error' : 'form-group col-md-3';
            ?>
            <div class="<?=$subjectClass?>">
                <label><?=$this->lang->line("assignment_subject")?> <span class="text-red">*</span></label>
                <?php
                    $array = array('0' => $this->lang->line("assignment_select_subject"));
                    if($subjects != "empty") {
                        foreach ($subjects as $subject) {
                            $array[$subject->subjectID] = $subject->subject;
                        }
                    }
                    echo form_dropdown("subjectID[]", $array, set_value("subjectID[0]"), "class='form-control sticky-subjects select2 subjectID'");
                ?>
                <span class="control-label"><?php echo form_error('subjectID'); ?></span>
            </div>

            <?php 
                $titleClass = form_error('title') ? 'form-group col-md-3 has-error' : 'form-group col-md-3';
            ?>
            <div class="<?=$titleClass?>">
                <label>Homework <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="title[]" value="<?=set_value('title[0]')?>">
                <span class="control-label"><?php echo form_error('title'); ?></span>
            </div>

           

              <?php 
            $descClass = form_error('description') ? 'form-group col-md-4 has-error' : 'form-group col-md-4';
        ?>
        <div class="<?=$descClass?>">
            <label><?=$this->lang->line("assignment_description")?> <span class="text-red">*</span></label>
            <textarea class="form-control" name="description[]" rows="3" style="resize: none;"><?=set_value('description[0]')?></textarea>
            <span class="control-label"><?php echo form_error('description'); ?></span>
        </div>
      

            <div class=" col-md-2 form-group <?php if(form_error('file')) echo 'has-error'; ?>">
                <label><?=$this->lang->line("assignment_file")?></label>
                <div class="input-group image-preview">
                    <input type="text" class="form-control image-preview-filename" disabled="disabled">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                            <span class="fa fa-remove"></span>
                            <?=$this->lang->line('assignment_clear')?>
                        </button>
                        <div class="btn btn-primary image-preview-input">
                            <span class="fa fa-repeat"></span>
                            <span class="image-preview-input-title"><?=$this->lang->line('assignment_file_browse')?></span>
                            <input type="file" name="file[]" />
                        </div>
                    </span>
                </div>
                <span class="control-label"><?php echo form_error('file'); ?></span>
            </div>
        </div>
    </div>
</div>


                    <div class="form-group float-right">
                        <button type="button" id="addMoreAssignment" class="ose-btn" style="margin-bottom: 15px;">
                           Add More
                        </button>
                    </div>

                    <div class="col-md-12">
                        <div class="btn-center">
                            <input type="submit" class="ose-btn" value="<?=$this->lang->line("add_assignment")?>" >
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Pass PHP language lines to JS variables
    var lang_assignment_select_section = "<?= addslashes($this->lang->line('assignment_select_section')) ?>";
    var lang_assignment_select_subject = "<?= addslashes($this->lang->line('assignment_select_subject')) ?>";
    var lang_assignment_select_classes = "<?= addslashes($this->lang->line('assignment_select_classes')) ?>";
    var lang_assignment_clear = "<?= addslashes($this->lang->line('assignment_clear')) ?>";
    var lang_assignment_file_browse = "<?= addslashes($this->lang->line('assignment_file_browse')) ?>";
    var lang_add_more = "<?= addslashes($this->lang->line('add_more') ?? 'Add More') ?>";

    // $(".select2").select2();

    $(".deadlinedate").datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy',
        //startDate:'<?=$schoolyearobj->startingdate?>',
        //endDate:'<?=$schoolyearobj->endingdate?>',
    });

    // Utility to show/hide loader on buttons
    function updateLoaderVisibility(show) {
        if(show) {
            $("#addMoreAssignment").prop('disabled', true).text('Loading...');
        } else {
            $("#addMoreAssignment").prop('disabled', false).text(lang_add_more);
        }
    }

    // Event: Class change inside any assignment block
    $(document).on('change', '.classesID', function() {
        var $block = $(this).closest('.assignment-block');
        var classesID = $(this).val();
        var sectionSelect = $block.find('.sectionID');
        var subjectSelect = $block.find('.subjectID');

        if(classesID === '0' || classesID === '') {
            sectionSelect.select2('destroy').html('<option value="">' + lang_assignment_select_section + '</option>').select2({width:'100%'});
            subjectSelect.select2('destroy').html('<option value="0">' + lang_assignment_select_subject + '</option>').select2({width:'100%'});
            return;
        }

        // Load subjects for the selected class
        $.ajax({
            type: 'POST',
            url: "<?=base_url('assignment/subjectcall')?>",
            data: {id: classesID},
            dataType: "html",
            success: function(data) {
                subjectSelect.select2('destroy').html(data).select2({width:'100%'});
                cachedSubjects = data;
            }
        });

        // Load sections for the selected class
        $.ajax({
            type: 'POST',
            url: "<?=base_url('assignment/sectioncall')?>",
            data: {id: classesID},
            dataType: "html",
            success: function(data) {
                sectionSelect.select2('destroy').html(data).select2({width:'100%'});
            }
        });
    });

    // Add More button click - append a new assignment block
    $("#addMoreAssignment").click(function() {
        updateLoaderVisibility(true);

        var index = $("#assignmentContainer .assignment-block").length;
        

        // Build new assignment block HTML with unique index for input names
       var newBlock = `<div class="assignment-block" style="border: 2px solid #28a745; padding: 20px; margin-bottom: 20px; border-radius: 10px; background: #eaf8ee; position: relative;">
    <div style="position: absolute; top: 10px; right: 10px;">
        <button type="button" class="btn btn-sm btn-danger remove-assignment"><i class="fa fa-trash"></i></button>
    </div>

    <div class="row">

    <div class="form-group col-md-3">
            <label><?=$this->lang->line("assignment_subject")?> <span class="text-red">*</span></label>
           <select class="form-control select2 subjectID" name="subjectID[]">
                ${cachedSubjects || '<option value="0"><?= $this->lang->line("assignment_select_subject") ?></option>'}
            </select>
        </div>

        <div class="form-group col-md-3">
            <label>Homework <span class="text-red">*</span></label>
            <input type="text" class="form-control" name="title[]" />
        </div>

       

        <div class="form-group col-md-4">
        <label><?=$this->lang->line("assignment_description")?> <span class="text-red">*</span></label>
        <textarea class="form-control" style="resize: none;" name="description[]" rows="3"></textarea>
    </div>

    <div class="form-group col-md-2">
        <label><?=$this->lang->line("assignment_file")?></label>
        <div class="input-group image-preview">
            <input type="text" class="form-control image-preview-filename" disabled="disabled">
            <span class="input-group-btn">
                <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                    <span class="fa fa-remove"></span> ${lang_assignment_clear}
                </button>
                <div class="btn btn-primary image-preview-input">
                    <span class="fa fa-upload"></span>
                    <span class="image-preview-input-title">${lang_assignment_file_browse}</span>
                    <input type="file" name="file[]" />
                </div>
            </span>
        </div>
    </div>

    </div>

    

    <div class="row">

    <!-- <div class="form-group col-md-3">
            <label><?=$this->lang->line("assignment_deadlinedate")?> <span class="text-red">*</span></label>
            <input type="text" class="form-control deadlinedate" name="deadlinedate[]" />
        </div>-->

       <!-- <div class="form-group col-md-3">
            <label><?=$this->lang->line("assignment_classes")?> <span class="text-red">*</span></label>
            <select class="form-control select2 classesID" name="classesID[]">
                <option value="0">${lang_assignment_select_classes}</option>
                <?php foreach ($classes as $classa): ?>
                    <option value="<?=$classa->classesID?>"><?=htmlspecialchars($classa->classes)?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label><?=$this->lang->line("assignment_section")?></label>
            <select class="form-control select2 sectionID" name="sectionID[]">
                <option value=""><?=$this->lang->line("assignment_select_section")?></option>
            </select>
        </div>-->

        

            

    </div>


</div>`;

        $("#assignmentContainer").append(newBlock);

        // Initialize select2 and datepicker for new elements
        // $("#assignmentContainer .assignment-block").last().find(".select2").select2();
        // $("#assignmentContainer .assignment-block").last().find(".deadlinedate").datepicker({
        //     autoclose: true,
        //     format: 'dd-mm-yyyy',
        // });

        updateLoaderVisibility(false);
    });

    // Image preview logic for dynamically added file inputs (optional, if you had before)
    $(document).on('click', '.image-preview-clear', function() {
        var $inputGroup = $(this).closest('.image-preview');
        $inputGroup.find('.image-preview-filename').val('');
        $inputGroup.find('input[type=file]').val('');
        $(this).hide();
    });

    $(document).on('change', '.image-preview-input input[type=file]', function() {
        var $inputGroup = $(this).closest('.image-preview');
        var filename = $(this).val().split('\\').pop();
        $inputGroup.find('.image-preview-filename').val(filename);
        $inputGroup.find('.image-preview-clear').show();
    });

    $(document).on('click', '.remove-assignment', function () {
    $(this).closest('.assignment-block').remove();
});

</script>
