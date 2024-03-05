<script>
    $(document).on("click","#exam_schedule_btn",function(e){
    
    if($("#sectionID").val() == null){
        $("#custom_error").html("Please select section");
          e.preventDefault();
        return false;
    }else{
        return true;
    }
});


$(document).ready(function() {
    $(".date").datepicker({ 
        autoclose: true,
        format: 'dd-mm-yyyy',
        startDate:'<?=$schoolyearsessionobj->startingdate?>',
        endDate:'<?=$schoolyearsessionobj->endingdate?>',
        daysOfWeekDisabled: "<?=$siteinfos->weekends?>",
        datesDisabled: ["<?=$get_all_holidays;?>"], 
    });
    $('.examfrom').timepicker();
    $('.examto').timepicker();
})

</script>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-puzzle-piece"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("examschedule/index")?>"><?=$this->lang->line('menu_examschedule')?></a></li>
            <li class="active"><?=$this->lang->line('menu_add')?> <?=$this->lang->line('menu_examschedule')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <form class="form-horizontal" role="form" method="post">

                    <?php
                        if(form_error('classesID'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                    ?>
                        <label for="classesID" class="control-label">
                            <?=$this->lang->line('examschedule_classes')?> <span class="text-red">*</span>
                        </label>
                        <div class="inpput-field">
                            <?php
                                $classArray[0] = $this->lang->line("examschedule_select_classes");
                                if(customCompute($classes)) {
                                    foreach ($classes as $classa) {
                                        $classArray[$classa->classesID] = $classa->classes;
                                    }
                                }
                                echo form_dropdown("classesID", $classArray, set_value("classesID"), "id='classesID' class='form-control select2'");
                            ?>
                        </div>
                        <span class="control-label">
                            <?php echo form_error('classesID'); ?>
                        </span>
                    </div>
                    <?php
                        if(form_error('sectionID'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                    ?>
                        <label for="sectionID" class="control-label">
                            <?=$this->lang->line("examschedule_section")?> <span class="text-red">*</span>
                        </label>
                        <div class="inpput-field">
                            <?php
                                $sectionArray[0] = $this->lang->line("examschedule_select_section");
                                if(customCompute($sections)) {
                                    foreach ($sections as $section) {
                                        $sectionArray[$section->sectionID] = $section->section;
                                    }
                                }

                                echo form_multiselect("sectionID[]", $sectionArray, set_value("sectionID"), "id='sectionID' class='form-control select2'");
                            ?>
                        </div>
                        <span class="control-label" id="custom_error" >
                            <?php echo form_error('sectionID'); ?>
                        </span>
                    </div>

                     <?php
                        if(form_error('examID'))
                            echo "<div class='col-md-4 has-error' >";
                        else
                            echo "<div class='col-md-4' >";
                    ?>
                        <label for="examID" class="control-label">
                            <?=$this->lang->line("examschedule_name")?> <span class="text-red">*</span>
                        </label>
                        <div class="inpput-field">
                            <?php
                                $examArray[0] = $this->lang->line("examschedule_select_exam");
                                if(customCompute($exams)) {
                                    foreach ($exams as $exam) {
                                        $examArray[$exam->examID] = $exam->exam;
                                    }
                                }
                                echo form_dropdown("examID", $examArray, set_value("examID"), "id='examID' class='form-control select2'");
                            ?>
                        </div>
                        <span class="control-label">
                            <?php echo form_error('examID'); ?>
                        </span>
                    </div>

                    

                    <?php
                        if(form_error('subjectID'))
                            echo "<div class='col-md-2 has-error' >";
                        else
                            echo "<div class='col-md-2' >";
                    ?>
                        <label for="subjectID" class=" control-label">
                            <?=$this->lang->line("examschedule_subject")?> <span class="text-red">*</span>
                        </label>
                       <div class="inpt-field"> 
                            <?php
                                $subjectArray[0] = $this->lang->line("examschedule_select_subject");
                                if(customCompute($subjects)) {
                                    foreach ($subjects as $subject) {
                                        $subjectArray[$subject->subjectID] = $subject->subject;
                                    }
                                }
                                echo form_dropdown("subjectID[]", $subjectArray, set_value("subjectID"), "id='subjectID' class='form-control select2'");
                            ?>
                         </div> 
                        <span class="control-label">
                            <?php echo form_error('subjectID'); ?>
                        </span>
                    </div>


                    <?php
                        if(form_error('min_mark'))
                            echo "<div class='col-md-2 has-error'>";
                        else
                            echo "<div class='col-md-2'>";
                    ?>
                        <label for="min_mark" class=" control-label">
                            Min Marks
                        </label>
                      <div class="input-field">
                            <input type="text" class="form-control" id="min_mark" name="min_mark[]" value="<?=set_value('min_mark')?>" >
                         </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('min_mark'); ?>
                        </span>
                    </div>

                    <?php
                        if(form_error('max_mark'))
                            echo "<div class='col-md-2 has-error'>";
                        else
                            echo "<div class='col-md-2'>";
                    ?>
                        <label for="max_mark" class="control-label">
                            Max Marks
                        </label>
                      <div class="input-field"> 
                            <input type="text" class="form-control" id="max_mark" name="max_mark[]" value="<?=set_value('max_mark')?>" >
                       </div>
                        <span class="control-label">
                            <?php echo form_error('max_mark'); ?>
                        </span>
                    </div>



                    <?php
                        if(form_error('date'))
                            echo "<div class='col-md-2 has-error' >";
                        else
                            echo "<div class='col-md-2' >";
                    ?>
                        <label for="date" class="control-label">
                            <?=$this->lang->line("examschedule_date")?> <span class="text-red">*</span>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control date" id="date" name="date[]" value="<?=set_value('date')?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('date'); ?>
                        </span>
                    </div>

                    <?php
                        if(form_error('examfrom'))
                            echo "<div class='col-md-2 has-error' >";
                        else
                            echo "<div class='col-md-2' >";
                    ?>
                        <label for="examfrom" class="control-label">
                            <?=$this->lang->line("examschedule_examfrom")?> <span class="text-red">*</span>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control examfrom" id="examfrom" name="examfrom[]" value="<?=set_value('examfrom')?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('examfrom'); ?>
                        </span>
                    </div>

                    <?php
                        if(form_error('examto'))
                            echo "<div class='col-md-2 has-error'>";
                        else
                            echo "<div class='col-md-2'>";
                    ?>
                        <label for="examto" class="control-label">
                            <?=$this->lang->line("examschedule_examto")?> <span class="text-red">*</span>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control examto" id="examto" name="examto[]" value="<?=set_value('examto')?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('examto'); ?>
                        </span>
                    </div>

                    <!-- <?php
                        if(form_error('room'))
                            echo "<div class='col-md-2 has-error'>";
                        else
                            echo "<div class='col-md-2'>";
                    ?>
                        <label for="room" class="control-label">
                            <?=$this->lang->line("examschedule_room")?>
                        </label>
                        <div class="input-field">
                            <input type="text" class="form-control" id="room" name="room" value="<?=set_value('room')?>" >
                        </div>
                        <span class="control-label">
                            <?php echo form_error('room'); ?>
                        </span>
                    </div> -->
                   

                    <div id="dynamic_div" class="col-md-12">
                        
                    </div>
               
                    <div class="col-md-12">
                        
                        <div class="btn-center">
                             <a   class="ose-btn addDetails"><i class="fa fa-plus"></i> Add Row</a>
                            <input type="submit" id="exam_schedule_btn" class="ose-btn" value="<?=$this->lang->line("add_examschedule")?>" >
                        </div>
                    </div>

                    </form>

                <div class="col-md-12">
                <?php if ($siteinfos->note==1) { ?>
                    <div class="callout callout-danger">
                        <p><b>Note:</b> Create exam, class, section & subject before you create a new exam schedule</p>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$('.select2').select2();
$('#classesID').change(function(event) {
    var classesID = $(this).val();
    if(classesID === '0') {
        $('#subjectID').val(0);
        $('#sectionID').val(0);
    } else {
        $.ajax({
            type: 'POST',
            url: "<?=base_url('examschedule/examcall')?>",
            data: "classesID=" + classesID,
            dataType: "html",
            success: function(data) {
               $('#examID').html(data);
            }
        });

        $.ajax({
            type: 'POST',
            url: "<?=base_url('examschedule/subjectcall')?>",
            data: "id=" + classesID,
            dataType: "html",
            success: function(data) {
               $('#subjectID').html(data);
            }
        });

        $.ajax({
            type: 'POST',
            url: "<?=base_url('examschedule/sectioncall')?>",
            data: "id=" + classesID,
            dataType: "html",
            success: function(data) {
               $('#sectionID').html(data);
            }
        });
    }
});

/*$("#date").datepicker({
    autoclose: true,
    format: 'dd-mm-yyyy',
    startDate:'<?=$schoolyearsessionobj->startingdate?>',
    endDate:'<?=$schoolyearsessionobj->endingdate?>',
    daysOfWeekDisabled: "<?=$siteinfos->weekends?>",
    datesDisabled: ["<?=$get_all_holidays;?>"], 
});
$('#examfrom').timepicker();
$('#examto').timepicker();*/



$(document).on('click', ".addDetails", function(){ 

        // alert("hi");
        var count = $('#dynamic_div div').length;;
        $dyn_subjects = $("#subjectID").html();
        var ct = count+1;
        var markup = '<div class="col-md-2"><label for="s2id_autogen4" class=" control-label"> Subject <span class="text-red">*</span> </label> <div class="inpt-field">  <select name="subjectID[]" id="" class="form-control select2 "> <option value="0">Select Subject</option>'+$dyn_subjects+ '</select> </div>  <span class="control-label"> </span> </div><div class="col-md-2"> <label for="min_mark" class=" control-label"> Min Marks </label> <div class="input-field"> <input type="text" class="form-control" id="min_mark" name="min_mark[]" value="">  </div> <span class="col-sm-4 control-label">  </div><div class="col-md-2"> <label for="max_mark" class="control-label">  Max Marks  </label> <div class="input-field">  <input type="text" class="form-control" id="max_mark" name="max_mark[]" value=""> </div> <span class="control-label"> </span> </div></div> <div class="col-md-2">   <label for="date" class="control-label">  Date <span class="text-red">*</span> </label> <div class="input-field"> <input type="text" class="form-control date" id="date" name="date[]" value=""> </div> <span class="control-label">  </span> </div> <div class="col-md-2"> <label for="examfrom" class="control-label"> Time From <span class="text-red">*</span>  </label> <div class="input-field"> <input type="text" class="form-control examfrom" id="examfrom" name="examfrom[]" value=""> </div> <span class="control-label"> </span>  </div> <div class="col-md-2">  <label for="examto" class="control-label"> Time To <span class="text-red">*</span> </label>  <div class="input-field"> <input type="text" class="form-control examto" id="examto" name="examto[]" value="">  <span class="control-label"> </span> </div> '; 
         
        $("#dynamic_div").append(markup);

                $(".date").datepicker({ 
                autoclose: true,
                format: 'dd-mm-yyyy',
                startDate:'<?=$schoolyearsessionobj->startingdate?>',
                endDate:'<?=$schoolyearsessionobj->endingdate?>',
                daysOfWeekDisabled: "<?=$siteinfos->weekends?>",
                datesDisabled: ["<?=$get_all_holidays;?>"], 
            });
            $('.examfrom').timepicker();
            $('.examto').timepicker();

    });

</script>
