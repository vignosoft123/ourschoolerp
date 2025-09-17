
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

<style>
    /* thead th {
        background: linear-gradient(90deg, #007bff, #3399ff);
        color: white;
        text-align: center;
        font-weight: bold;
        vertical-align: middle;
        padding: 10px;
    } */

    .excel-only {
    display: none;
}


    .grade-label {
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: bold;
    display: inline-block;
    font-size: 13px;
}

.grade-a-plus { background-color: #e6f4ea; color: #2e7d32; }
.grade-a      { background-color: #e8f5e9; color: #388e3c; }
.grade-b-plus { background-color: #e3f2fd; color: #0288d1; }
.grade-b      { background-color: #e1f5fe; color: #039be5; }
.grade-c-plus { background-color: #fff9c4; color: #fbc02d; }
.grade-c      { background-color: #ffe0b2; color: #f57c00; }
.grade-d      { background-color: #ffcdd2; color: #d32f2f; }




   .grade-label {
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: bold;
    display: inline-block;
    font-size: 13px;
}

/* Light backgrounds with readable text */
.grade-a-plus {
    background-color: #e6f4ea; /* Light green */
    color: #2e7d32;
}
.grade-a {
    background-color: #e8f5e9;
    color: #388e3c;
}
.grade-b-plus {
    background-color: #e3f2fd; /* Light blue */
    color: #0288d1;
}
.grade-b {
    background-color: #e1f5fe;
    color: #039be5;
}
.grade-c-plus {
    background-color: #fff9c4; /* Light yellow */
    color: #fbc02d;
}
.grade-c {
    background-color: #ffe0b2; /* Light orange */
    color: #f57c00;
}
.grade-d {
    background-color: #ffcdd2; /* Light red */
    color: #d32f2f;
}

   .attendance-circle {
    display: inline-block;
    width: 20px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 50%;
    background-color: #f8d7da; /* light red */
    color: #a94442;             /* darker red text */
    font-weight: bold;
    font-size: 13px;
    font-family: Arial, sans-serif;
    margin: 2px;
}


    #myTable thead th {
    background-color: #4CAF50; /* Green background */
    
    color: white;               /* White text */
    padding: 10px;              /* Padding inside headers */
    text-align: center;         /* Center the header text */
    font-weight: bold;          /* Bold text */
    border: 1px solid #ddd;     /* Light border */
    font-size: 14px;            /* Font size */
    /* white-space: nowrap;        Prevent headers from wrapping */
}

    tbody td {
        text-align: center;
        vertical-align: middle;
        padding: 8px;
    }
    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }
    .text-bold {
        font-weight: bold;
    }
    tfoot td {
        background-color: #e9ecef;
        font-weight: bold;
    }
</style>

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
                            <div class="row filter-box">

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
                                        <button type="submit" class="btn btn-success col-md-12 col-xs-12 mark_btn" style="margin-top: 20px;">Get Marks</button>

                                        <!-- <input class="btn btn-black" type="reset" value="Clear"> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
               


 <div class="row" style="margin-top: 20px;">
    <!-- Left: Info Box -->
    <?php if (customCompute($sendExam) && customCompute($sendClasses) && customCompute($sendSection) ) { ?>
        <div class="col-sm-6 box-layout-fame">
            <div class="panel panel-default" style="padding: 10px; background-color: #f9f9f9;">
                <h5><center><?php echo $this->lang->line('mark_details'); ?></center></h5>
                <h5><center><?php echo $this->lang->line('mark_exam') . ' : ' . $sendExam->exam; ?></center></h5>
                <h5><center><?php echo $this->lang->line('mark_classes') . ' : ' . $sendClasses->classes; ?></center></h5>
                <h5><center><?php echo $this->lang->line('mark_section') . ' : ' . $sendSection->section; ?></center></h5>   
                <br/>
                <br/>
               
                <div class="form-group">
 <button class="btn btn-black col-md-3 "   id="printBtn"><span class="fa fa-print"> &nbsp;</span >Print Sheet</button>

                
                <button id="exportButton" class="btn btn-info col-md-3"><i class="fa fa-download"></i>  Download Sample</button>

<form enctype="multipart/form-data" style="padding:1%" action="<?=base_url('Mark/marks_bulkimport');?>" class="form-horizontal" role="form" method="post">
                    
                        

                        <div class="col-sm-4 col-xs-6 col-md-3">
                            <div class="fileUpload btn btn-success form-control">
                                <span class="fa fa-repeat"></span>
                                <span>Upload Excel Marks</span>
                                <input id="uploadBtn" type="file" class="upload questionUpload" name="csvMarks" />
                            </div>
                        </div>

                        <div class="col-md-1 rep-mar">
                            <input type="submit" class="btn btn-success" value="Save Marks" >
                        </div>
 
                 
                </form>

            </div>       
                </div>
        </div>
    

    <!-- Right: Grade Legend -->
    <div class="col-sm-6">
        <div class="grade-legend" style="background-color: #fdfdfd; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
            <strong>Note:</strong>
            <ul>
                <li><span class="grade-label grade-a-plus">A+</span> – 95% and above</li>
                <li><span class="grade-label grade-a">A</span> – 90% to 94%</li>
                <li><span class="grade-label grade-b-plus">B+</span> – 80% to 89%</li>
                <li><span class="grade-label grade-b">B</span> – 70% to 79%</li>
                <li><span class="grade-label grade-c-plus">C+</span> – 60% to 69%</li>
                <li><span class="grade-label grade-c">C</span> – 50% to 59%</li>
                <li><span class="grade-label grade-d">D</span> – Below 50% or zero marks</li>
            </ul>
        </div>
    </div>
    <?php } ?>

</div>


            </div>

            
            
            <div class="col-sm-12">
                <?php if (customCompute($students)) { ?>
                    <div id="hide-table">
                        <table class="table table-striped table-bordered table-hover dataTable no-footer" id="myTable">
                            <thead>
                                <tr>
                                    <th class="no-export"><?= $this->lang->line('slno') ?></th>
                                    
                                    <th class="no-export"><?= $this->lang->line('mark_photo') ?></th>
                                    <th class="excel-only1">studentID</th>
                                    <th><?= $this->lang->line('mark_name') ?>(<?= $this->lang->line('mark_roll') ?>)</th>

                                      
                                    
                                   <?php
                                        $out_of = 0;
                                        foreach ($subjects as $subject) {
                                            // Visible column
                                            echo "<th class='no-export'>{$subject->subject} ({$subject->max_mark})</th>";

                                            // Hidden column for Excel
                                            echo "<th class='excel-only'>{$subject->subject}^{$subject->subjectID}</th>";

                                            $out_of += $subject->max_mark;
                                        }
                                        ?>
                                   <th class="no-export"> Total (Out of <?php echo $out_of;?>)</th>
                                   <th class="no-export"> Grade </th>
                                   <th class="no-export"> Rank </th>
                                   <th class="no-export"> Send SMS <input type="checkbox" class="" id="checkAll" name="send_sms_marks"> </th>



                                </tr>
                            </thead>
                           <tbody id="marksBody">
                                <!-- Student rows will be loaded here by AJAX -->
                            </tbody>
                        </table>

                        <div class="text-center">
<button id="loadMore" class="btn btn-primary">Load More</button>
</div>

                    </div>
                   

                    <div class="text-right">

                       
                    
                        <button class="btn btn-info sendSms" id="send_sms_marks_btn">
                            <span class="fa fa-comment"></span> Send Marks - SMS
                        </button>

                        <button class="btn btn-primary sendSms" id="send_whatsapp_marks_btn">
                            <span class="fa fa-whatsapp"></span> Send Marks - Whatsapp
                        </button>

                        <button type="button" class="btn btn-success " id="add_mark" name="add_mark" value="Save or Refresh Marks" > Save or Refresh Marks </button>
                    </div>

                    <script type="text/javascript">
                        window.addEventListener('load', function() {
                            setTimeout(lazyLoad, 1000);
                        });

                        function lazyLoad() {
                            var card_images = document.querySelectorAll('.card-image');
                            card_images.forEach(function(card_image) {
                                var image_url = card_image.getAttribute('data-image-full');
                                var content_image = card_image.querySelector('img');
                                content_image.src = image_url;
                                content_image.addEventListener('load', function() {
                                    card_image.style.backgroundImage = 'url(' + image_url + ')';
                                    card_image.className = card_image.className + ' is-loaded';
                                });
                            });
                        }

                        $(document).on("keyup", ".mark", function() {
                            if (parseInt($(this).val())) {
                                var val = parseInt($(this).val());
                                var minMark = parseInt($(this).attr('min'));
                                var maxMark = parseInt($(this).attr('max'));
                                if (minMark > val || val > maxMark) {
                                    $(this).val('');
                                }
                            } else {
                                if ($(this).val() == '0') {} else {
                                    $(this).val('');
                                }
                            }
                        });

                        // $("#add_mark").click(function() {
                            // $(document).on('keyup','.input_mark',function() {
                            $(document).on('focusout','.input_mark',function() {
                                var subj_id = $(this).attr('subj_id');
                               
                            var inputs = "";
                            var inputs_value = "";
                            // var mark = $('input[name^='+subj_id+'mark]').map(function() {
                            var mark = $(this).map(function() {
                                return {
                                    markpercentageid: this.id,
                                    mark: this.name,
                                    value: this.value
                                };
                            }).get();

                            $.ajax({
                                type: 'POST',
                                url: "<?= base_url('mark/mark_send') ?>",
                                data: {
                                    "examID": "<?= $set_exam ?>",
                                    "classesID": "<?= $set_classes ?>", 
                                    "subjectID":subj_id ,
                                    "inputs": mark
                                },
                                dataType: "html",
                                success: function(data) {
                                    var response = jQuery.parseJSON(data);
                                    if (response.status) {
                                         toastr.clear(); // remove old toast
                                        toastr["success"](response.message)
                                        // toastr.options = {
                                        //     "closeButton": true,
                                        //     "debug": false,
                                        //     "newestOnTop": false,
                                        //     "progressBar": false,
                                        //     "positionClass": "toast-top-right",
                                        //     "preventDuplicates": false,
                                        //     "onclick": null,
                                        //     "showDuration": "500",
                                        //     "hideDuration": "500",
                                        //     "timeOut": "5000",
                                        //     "extendedTimeOut": "1000",
                                        //     "showEasing": "swing",
                                        //     "hideEasing": "linear",
                                        //     "showMethod": "fadeIn",
                                        //     "hideMethod": "fadeOut"
                                        // }

                                         // Set toastr global options once
                    toastr.options = {
                        "closeButton": false,
                        "debug": false,
                        "newestOnTop": true,
                        "progressBar": false,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": true, // avoid duplicate messages
                        "onclick": null,
                        "showDuration": "200",
                        "hideDuration": "200",
                        "timeOut": "1500",
                        "extendedTimeOut": "500",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };

 
                                    } else {
                                        if (response.inputs) {
                                            toastr["error"](response.inputs)
                                            toastr.options = {
                                                "closeButton": true,
                                                "debug": false,
                                                "newestOnTop": false,
                                                "progressBar": false,
                                                "positionClass": "toast-top-right",
                                                "preventDuplicates": false,
                                                "onclick": null,
                                                "showDuration": "500",
                                                "hideDuration": "500",
                                                "timeOut": "5000",
                                                "extendedTimeOut": "1000",
                                                "showEasing": "swing",
                                                "hideEasing": "linear",
                                                "showMethod": "fadeIn",
                                                "hideMethod": "fadeOut"
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                <?php } ?>
            </div>
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

            var marks_grade = [];

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
                marks_grade[i++] = $(this).attr("marks_grade");
                
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
                data: {"st_ids":st_ids,"st_names":st_names,"mobile_no":mobile_no,"exam_name":exam_name,"total_marks":total_marks,"marks_template":marks_template,"marks_grade":marks_grade},
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

            
            var marks_grade = [];
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
                
                marks_grade[i++] = $(this).attr("marks_grade");
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
                data: {"st_ids":st_ids,"st_names":st_names,"mobile_no":mobile_no,"exam_name":exam_name,"total_marks":total_marks,"marks_template":marks_template,"exam_date":exam_date,"marks_grade" :marks_grade},
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

<!--  
<script>
    $(document).ready(function () {
        $("#exportButton").click(function () {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todate = `${dd}-${mm}-${yyyy}`;
            const filename = `fee_report_${todate}.xlsx`;

            // Custom values (you can set these dynamically in PHP and inject via JS)
            const classID = 9;
            const examID = 4;
            const subjectID = 2;

            // 1. Create a metadata row (as array of arrays)
            const metadata = [
                [`classID: ${classID}`, `examID: ${examID}`, `subjectID: ${subjectID}`]
            ];

            // 2. Create worksheet from metadata
            const ws = XLSX.utils.aoa_to_sheet(metadata);

            // 3. Append the HTML table to the worksheet starting from row 3 (index 2)
            const table = document.getElementById("myTable");
            XLSX.utils.sheet_add_dom(ws, table, { origin: -1 }); // -1 appends below existing content

            // 4. Create workbook and export
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
            XLSX.writeFile(wb, filename);
        });
    });
</script> -->
 <script>
$(document).ready(function () {
    $("#exportButton").click(function () {
        // const classID = <?= $set_classes ?? 0 ?>;
        // const examID = <?= $set_exam ?? 0 ?>;
        // const sectionID = <?= $set_section ?? 0 ?>;

         const classID = $("#classesID").val();
        const examID = $("#examID").val();
        const sectionID = $("#sectionID").val();

        const metadata = [
            [`classID: ${classID}`, `examID: ${examID}`, `sectionID: ${sectionID}`],
            [`studentID`]
        ];

        const table = document.getElementById("myTable");
        const clone = table.cloneNode(true);

        // Remove unwanted <th> and matching <td> columns
        const removeColsByClass = (className) => {
            const ths = clone.querySelectorAll("thead th");
            let indexesToRemove = [];

            ths.forEach((th, idx) => {
                if (th.classList.contains(className)) {
                    indexesToRemove.push(idx);
                    th.remove();
                }
            });

            // Remove matching <td> from each row
            const rows = clone.querySelectorAll("tbody tr");
            rows.forEach(row => {
                const cells = Array.from(row.children);
                indexesToRemove.forEach(i => {
                    if (cells[i]) cells[i].remove();
                });
            });
        };

        // Remove .no-export columns (photo, total, grade, checkbox, etc.)
        removeColsByClass("no-export");

        // Show excel-only hidden columns
        clone.querySelectorAll('.excel-only').forEach(el => el.style.display = '');

        // Convert clean table to sheet
        const sheet = XLSX.utils.table_to_sheet(clone, { origin: "A2" }); // <-- Start from A2

        // Add metadata at top
        XLSX.utils.sheet_add_aoa(sheet, metadata, { origin: "A1" }); // <-- Put metadata in A1

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, sheet, "Sheet1");

        const today = new Date();
        // const filename = `marks_${today.getFullYear()}-${(today.getMonth()+1).toString().padStart(2,'0')}-${today.getDate().toString().padStart(2,'0')}.xlsx`;
        const filename = `marks_${today.getFullYear()}-${(today.getMonth()+1).toString().padStart(2,'0')}-${today.getDate().toString().padStart(2,'0')}.csv`;


        XLSX.writeFile(wb, filename);
    });
});
</script>


<script> 

var offset = 0;
var limit = 20;
var loading = false;

function loadStudents(reset=false){
    if(loading) return;
    loading = true;

    if(reset) offset=0;
    var classesID = $('#classesID').val();
    var sectionID = $('#sectionID').val();
    var examID    = $('#examID').val();

    $.post('<?=site_url("mark/get_students_page")?>', {
        classesID: classesID,
        sectionID: sectionID,
        examID: examID,
        offset: offset
    }, function(response){
        if(reset) $('#marksBody').html(response);
        else $('#marksBody').append(response);

        var rows = $(response).filter('tr').length;
        if(rows > 0) offset += rows;
        loading = false;
    });
}

// Initial load
$('.mark_btn').on('click', function(e){
    e.preventDefault();
    loadStudents(true);
});

// Optional: Load more button
$('#loadMore').on('click', function(){
    loadStudents(false);
});


</script>
