<style>
        /* Print specific styles */
        @media print {
            input {
                display: none; /* Hide input fields when printing */
            }
            .input_mark {
                display: none !important; /* Hide input fields when printing */
            }
            .callout {
                display: none !important; /* Hide input fields when printing */
            }
            .box-header {
                display: none !important; /* Hide input fields when printing */
            }
             #add_mark {
                display: none !important; /* Hide input fields when printing */
            }

            button {
                display: none !important; /* Hide input fields when printing */
            }

            .icon-eattendance {
                display: none !important; /* Hide input fields when printing */
            }

            .hide-in-print {
                display: none !important; /* Hide input fields when printing */
            }

            .box-layout-fame{
                padding-top:0px !important;
            }

            img {
                display: block; /* Ensure images are visible */
            }

             /* Hide the last 3 columns in the table */
             table tr th:nth-last-child(-n+3), /* Hides headers for last 3 columns */
            table tr td:nth-last-child(-n+3) /* Hides data for last 3 columns */ {
                display: none;
            }
            
            /* Optional: Make table look better for printing */
            table {
                width: 100%;
                border-collapse: collapse;
            }

            table, th, td {
                border: 1px solid black;
            }

            th, td {
                padding: 10px;
                text-align: left;
            }
        }
    </style>

<script>
        $(document).ready(function() {
            $('#printBtn').on('click', function() {
                window.print(); // Trigger the print dialog
            });
        });
    </script>




<?php if ($siteinfos->note == 1) { ?>
    <div class="callout callout-danger">
        <p><b>Note:</b> Create exam, class, section & subject before add mark</p>
    </div>
<?php } ?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-flask"></i> <?= $this->lang->line('panel_title') ?></h3>
        <ol class="breadcrumb">
            <li><a href="<?= base_url("dashboard/index") ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
            <li><a href="<?= base_url("mark/index") ?>"><?= $this->lang->line('menu_mark') ?></a></li>
            <li class="active"><?= $this->lang->line('menu_add') ?> <?= $this->lang->line('menu_mark') ?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <form method="POST">
                    <div class="row hide-in-print">
                        <div class="col-md-10">
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="<?php echo form_error('classesID') ? 'form-group has-error' : 'form-group'; ?>">
                                        <label for="classesID" class="control-label">
                                            <?= $this->lang->line('mark_classes') ?> <span class="text-red">*</span>
                                        </label>
                                        <?php
                                        $array = array("0" => $this->lang->line("mark_select_classes"));
                                        foreach ($classes as $classa) {
                                            $array[$classa->classesID] = $classa->classes;
                                        }
                                        echo form_dropdown("classesID", $array, set_value("classesID"), "id='classesID' class='form-control select2 classesID'");
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="<?php echo form_error('examID') ? 'form-group has-error' : 'form-group'; ?>">
                                        <label for="examID" class="control-label">
                                            <?= $this->lang->line('mark_exam') ?> <span class="text-red">*</span>
                                        </label>
                                        <?php
                                        $array = array("0" => $this->lang->line("mark_select_exam"));
                                        foreach ($exams as $exam) {
                                            $array[$exam->examID] = $exam->exam;
                                        }
                                        echo form_dropdown("examID", $array, set_value("examID"), "id='examID' class='form-control select2 examID'");
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="<?php echo form_error('sectionID') ? 'form-group has-error' : 'form-group'; ?>">
                                        <label class="control-label"><?= $this->lang->line('mark_section') ?> <span class="text-red">*</span></label>
                                        <?php
                                        $arraysection = array('0' => $this->lang->line("mark_select_section"));
                                        if (customCompute($sections)) {
                                            foreach ($sections as $section) {
                                                $arraysection[$section->sectionID] = $section->section;
                                            }
                                        }
                                        echo form_dropdown("sectionID", $arraysection, set_value("sectionID"), "id='sectionID' class='form-control select2'");
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-3" style="display:none;">
                                    <div class="<?php echo form_error('subjectID') ? 'form-group has-error' : 'form-group'; ?>">
                                        <label for="subjectID" class="control-label">
                                            <?= $this->lang->line('mark_subject') ?> <span class="text-red">*</span>
                                        </label>
                                        <?php
                                        // $subjectArray = array("0" => $this->lang->line("mark_select_subject"));
                                        if (customCompute($subjects)) {
                                            foreach ($subjects as $subject) {
                                                $subjectArray[$subject->subjectID] = $subject->subject;
                                            }
                                        }
                                        echo form_dropdown("subjectID", $subjectArray, set_value("subjectID"), "id='subjectID' class='form-control select2'");
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12">
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success col-md-12 col-xs-12 mark_btn" style="margin-top: 20px;"><?= $this->lang->line('add_mark') ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
               


                <?php if (customCompute($sendExam) && customCompute($sendClasses) && customCompute($sendSection) && customCompute($sendSubject)) { ?>
                    <div class="col-sm-4 col-sm-offset-4 box-layout-fame">
                        <?php
                        echo '<h5><center>' . $this->lang->line('mark_details') . '</center></h5>';
                        echo '<h5><center>' . $this->lang->line('mark_exam') . ' : ' . $sendExam->exam . '</center></h5>';
                        echo '<h5><center>' . $this->lang->line('mark_classes') . ' : ' . $sendClasses->classes . '</center></h5>';
                        echo '<h5><center>' . $this->lang->line('mark_section') . ' : ' . $sendSection->section . '</center></h5>';
                      
                        ?>
                    </div>
                <?php } ?> 

            </div>

             
            <form action="<?= base_url('mark/saveAllMarks') ?>" method="POST">
    <div id="hide-table">
        <input type="hidden" name="examID" value="<?= $set_exam?>">
        <input type="hidden" name="classesID" value="<?= $set_classes?>">
        <table class="table table-striped table-bordered table-hover dataTable no-footer">
            <thead>
                <tr>
                    <th><?= $this->lang->line('slno') ?></th>
                    <th><?= $this->lang->line('mark_photo') ?></th>
                    <th><?= $this->lang->line('mark_name') ?>(<?= $this->lang->line('mark_roll') ?>)</th>
                    <?php
                        $out_of = 0;
                        foreach ($subjects as $subject) {
                            echo "<th>$subject->subject ($subject->max_mark)</th>";
                            $out_of += $subject->max_mark;
                        }
                    ?>
                    <th>Total (Out of <?= $out_of ?>)</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($students as $student): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= profileproimage($student->photo) ?></td>
                        <td><?= $student->name ?> (<?= $student->roll ?>)</td>
                        <?php
                            $studentTotal = 0;
                            $zero_mark = 0;
                            foreach ($subjects as $subject):
                                $subjectTotal = 0;
                                foreach ($markpercentages as $data):
                                    // you may need to fetch $mark->mark from your `$marks` array using studentID + subjectID + markpercentageID
                                    $mark_value = ''; // default empty

                                    // loop to find the correct mark (you can optimize this part or preload into a lookup)
                                    foreach ($marks as $mark) {
                                        if (
                                            $mark->studentID == $student->studentID &&
                                            $mark->subjectID == $subject->subjectID &&
                                            $mark->markpercentageID == $data->markpercentageID
                                        ) {
                                            $mark_value = $mark->mark;
                                            break;
                                        }
                                    }

                                    if ($mark_value === '' || $mark_value == 0) $zero_mark++;
                                    $subjectTotal += $mark_value;
                                    ?>
                                    <td>
                                        <input type="number"
                                               class="form-control"
                                               min="0"
                                               max="<?= $subject->max_mark ?>"
                                               name="marks[<?= $student->studentID ?>][<?= $subject->subjectID ?>][<?= $data->markpercentageID ?>]"
                                               value="<?= $mark_value ?>">
                                    </td>
                                <?php endforeach;
                                $studentTotal += $subjectTotal;
                            endforeach;

                            // GRADE CALCULATION
                            $percent = $studentTotal / ($out_of ?: 1) * 100;
                            $grade = 'D';
                            if ($percent >= 95 && $zero_mark == 0) $grade = 'A+';
                            elseif ($percent >= 90) $grade = 'A';
                            elseif ($percent >= 80) $grade = 'B+';
                            elseif ($percent >= 70) $grade = 'B';
                            elseif ($percent >= 60) $grade = 'C+';
                            elseif ($percent >= 50) $grade = 'C';
                        ?>
                        <td><?= $studentTotal ?></td>
                        <td><?= $grade ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center">
        <button type="submit" name="add_mark" class="btn btn-success"><?= $this->lang->line("add_sub_mark") ?></button>
    </div>
</form>



        </div>
    </div>


</div>

<script type="text/javascript">
    $('.select2').select2();
    $("#classesID").change(function() {
        var classesID = $(this).val();
        if (parseInt(classesID)) {
            $.ajax({
                type: 'POST',
                url: "<?= base_url('mark/examcall') ?>",
                data: {
                    "classesID": classesID
                },
                dataType: "html",
                success: function(data) {
                    $('#examID').html(data);
                }
            });

            $.ajax({
                type: 'POST',
                url: "<?= base_url('mark/subjectcall') ?>",
                data: {
                    "id": classesID
                },
                dataType: "html",
                success: function(data) {
                    $('#subjectID').html(data);
                }
            });

            $.ajax({
                type: 'POST',
                url: "<?= base_url('mark/sectioncall') ?>",
                data: {
                    "id": classesID
                },
                dataType: "html",
                success: function(data) {
                    $('#sectionID').html(data);
                }
            });
        }
    });


    $('.markUpload').on('change', function() {
        $('.markImport').val($(this).val());
    });

    $(document).ready(function() {

        $("#checkAll").click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        

        
        
        $(document).on("click","#send_sms_marks_btn",function(){

            if ($(".checkbox:checked").length === 0) {
                alert("Please check at least one checkbox before proceeding.");
                return false;
            }

            var st_ids = [];
            st_names =[];
            mobile_no = [];
            exam_name = [];
            total_marks = [] ;
            marks_template = []; 
            i=j=k=l=m=n=0;

            $('.checkbox:checked').each(function(){        
                // var values = $(this).val();
                // var sids = $(this).attr("st_ids");
                
                st_ids[i++] = $(this).attr("st_ids");
                st_names[j++] = $(this).attr("st_names");
                mobile_no[k++] = $(this).attr("mobile_no");
                exam_name[l++] = $(this).attr("exam_name");
                total_marks[m++] = $(this).attr("total_marks");
                marks_template[n++] = $(this).attr("marks_template");
            });

            $.ajax({
                            
                type: "POST",
                url: "<?php echo site_url('progresscardreport/send_marks_to_sms'); ?>",
                // dataType: "json",
                data: {"st_ids":st_ids,"st_names":st_names,"mobile_no":mobile_no,"exam_name":exam_name,"total_marks":total_marks,"marks_template":marks_template},
                success: function(result)
                {
                    
                }
            })
        });

        $(document).on("click","#send_whatsapp_marks_btn",function(){

            if ($(".checkbox:checked").length === 0) {
                alert("Please check at least one checkbox before proceeding.");
                return false;
            }

            var st_ids = [];
            st_names =[];
            mobile_no = [];
            exam_name = [];
            total_marks = [] ;
            marks_template = [];  
            exam_date = [];  
            i=j=k=l=m=n=o=0;

            $('.checkbox:checked').each(function(){        
                // var values = $(this).val();
                // var sids = $(this).attr("st_ids");
                
                st_ids[i++] = $(this).attr("st_ids");
                st_names[j++] = $(this).attr("st_names");
                mobile_no[k++] = $(this).attr("mobile_no");
                exam_name[l++] = $(this).attr("exam_name");
                total_marks[m++] = $(this).attr("total_marks");
                marks_template[n++] = $(this).attr("marks_template");
                exam_date[o++] = $(this).attr("exam_date");
            });
                 

            $.ajax({
                            
                type: "POST",
                url: "<?php echo site_url('progresscardreport/send_marks_to_whatsapp'); ?>",
                // dataType: "json",
                data: {"st_ids":st_ids,"st_names":st_names,"mobile_no":mobile_no,"exam_name":exam_name,"total_marks":total_marks,"marks_template":marks_template,"exam_date":exam_date},
                success: function(result)
                {
                    
                }
            })
            });

            

        // if( sessionStorage.getItem("click") == 'yes')){}else{
        //     sessionStorage.setItem("click", "no");
        // }
        // if(sessionStorage.getItem("click") == 'no'){
        //     $(".mark_btn").click();
        //     sessionStorage.setItem("click", "yes");
        // }
        // $(".mark_btn").click();

        var cID = $('#classesID').val();
        var sID = $('#sectionID').val();
        var eID = $('#examID').val();
        var subID = $('#subjectID').val();

        $('.classId').val(cID);
        $('.sectionId').val(sID);
        $('.examId').val(eID);
        $('.subjectId').val(subID);
    });
$(document).on("click","#add_mark",function(){
    location.reload();
});

</script>