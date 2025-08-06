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

function initializeDatepickers() {//alert();
    $(".date").datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy',
        startDate: '01-04-2025',
        endDate: '31-03-2026',
        daysOfWeekDisabled: "0",
        datesDisabled: ["15-08-2025", "02-10-2025"]
    });
        $('.examfrom').timepicker();
    $('.examto').timepicker();
}


$(document).ready(function() {
     initializeDatepickers();
    // $(".date").datepicker({ 
    //     autoclose: true,
    //     format: 'dd-mm-yyyy',
    //     startDate:'<?=$schoolyearsessionobj->startingdate?>',
    //     endDate:'<?=$schoolyearsessionobj->endingdate?>',
    //     daysOfWeekDisabled: "<?=$siteinfos->weekends?>",
    //     datesDisabled: ["<?=$get_all_holidays;?>"], 
    // });
    // $('.examfrom').timepicker();
    // $('.examto').timepicker();
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


               <!-- Assign Default Input Fields -->
<div class="col-md-12 filter-box" > 
    <div class="col-md-2">
        <label>Default Pass Marks</label>
        <input type="text" id="default_min_mark" class="form-control">
    </div>
    <div class="col-md-2">
        <label>Default Max Marks</label>
        <input type="text" id="default_max_mark" class="form-control">
    </div>
    <div class="col-md-2">
        <label>Default Start Time</label>
        <input type="text" id="default_examfrom" class="form-control examfrom">
    </div>
    <div class="col-md-2">
        <label>Default End Time</label>
        <input type="text" id="default_examto" class="form-control examto">
    </div>
    <div class="col-md-2" style="padding-top: 25px;">
        <button type="button" id="assign_defaults" class="btn btn-black">Assign to All</button>
    </div> 
</div>




            <div class="col-sm-12">
                <form class="form-horizontal filter-box1" role="form" method="post">

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
                            Pass Marks
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
                            Start Time <span class="text-red">*</span>
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
                            End Time <span class="text-red">*</span>
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
                   
                    <div class="row"> 
                    <div id="dynamic_div" class="col-md-12  ">
                        
                    </div>
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

$(document).on('click', ".addDetails", function() {
    var count = $('#dynamic_div .dynamic-row').length;
    var ct = count + 1;
    var $dyn_subjects = $("#subjectID").html();

    var markup = `
    <div id="row_${ct}" class="row dynamic-row" style="margin-bottom:10px; padding:15px; border-radius:7px;">
        <div class="col-md-2">
            <label>Subject <span class="text-red">*</span></label>
            <select name="subjectID[]" class="form-control select2">${$dyn_subjects}</select>
        </div>
        <div class="col-md-2">
            <label>Pass Marks</label>
            <input type="text" class="form-control min_mark" name="min_mark[]" />
        </div>
        <div class="col-md-2">
            <label>Max Marks</label>
            <input type="text" class="form-control max_mark" name="max_mark[]" />
        </div>
        <div class="col-md-2">
            <label>Date <span class="text-red">*</span></label>
            <input type="text" class="form-control date" id="date" name="date[]" />
        </div>
        <div class="col-md-2">
            <label>Time From <span class="text-red">*</span></label>
            <input type="text" class="form-control examfrom" name="examfrom[]" />
        </div>
        <div class="col-md-2">
            <label>
                Time To <span class="text-red">*</span>
                <span class="remove" myct="${ct}" style="cursor:pointer; margin-left:10px;"><i class="fa fa-trash-o"></i></span>
            </label>
            <input type="text" class="form-control examto" name="examto[]" />
        </div>
    </div>`;

    $("#dynamic_div").append(markup);

    initializeDatepickers();

    $(".select2").select2();
    
    $('.examfrom').timepicker({ timeFormat: 'h:mm p', interval: 15 });
    $('.examto').timepicker({ timeFormat: 'h:mm p', interval: 15 });

     
});

$(document).on('click', '.remove', function(){
    var ct = $(this).attr('myct');
    $('#row_' + ct).remove();
});

$(document).on('click', '#assign_defaults', function() {
    let min = $('#default_min_mark').val();
    let max = $('#default_max_mark').val();
    let from = $('#default_examfrom').val();
    let to = $('#default_examto').val();

    $('.dynamic-row').each(function() {
        $(this).find('input[name="min_mark[]"]').val(min);
        $(this).find('input[name="max_mark[]"]').val(max);
        $(this).find('input[name="examfrom[]"]').val(from);
        $(this).find('input[name="examto[]"]').val(to);
    });

    $("#min_mark").val(min);
    $("#max_mark").val(max);
    $("#examfrom").val(from);
    $("#examfto").val(to);
});
</script>
