
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

<style>
    /* Modern Button Styles */
    .btn {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 13px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .btn:hover:before {
        left: 100%;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .btn:active {
        transform: translateY(0);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Success Button */
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
        color: white;
    }

    /* Warning Button */
    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        color: #212529;
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
        color: #212529;
    }

    /* Primary Button */
    .btn-primary {
        background: linear-gradient(135deg, #007bff 0%, #667eea 100%);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #667eea 0%, #007bff 100%);
        color: white;
    }

    /* Info Button */
    .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #764ba2 100%);
        color: white;
    }

    .btn-info:hover {
        background: linear-gradient(135deg, #764ba2 0%, #17a2b8 100%);
        color: white;
    }

    /* Black Button */
    .btn-black {
        background: linear-gradient(135deg, #343a40 0%, #495057 100%);
        color: white;
    }

    .btn-black:hover {
        background: linear-gradient(135deg, #495057 0%, #343a40 100%);
        color: white;
    }

    /* Default Button */
    .btn-default {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #495057;
        border: 1px solid #dee2e6;
    }

    .btn-default:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
        color: #495057;
    }

    /* File Upload Button */
    .fileUpload {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 8px;
        font-weight: 600;
    }

    .fileUpload:hover {
        background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
    }

    /* Special styling for action buttons */
    .mark_btn {
        background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }

    .mark_btn:hover {
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        background: linear-gradient(135deg, #34ce57 0%, #28a745 100%);
    }

    #generateRankBtn {
        background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
    }

    #generateRankBtn:hover {
        box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
        background: linear-gradient(135deg, #ffb300 0%, #ffc107 100%);
    }

    /* Load More Buttons */
    #loadMoreBtn, #loadAllBtn {
        border-radius: 25px;
        padding: 12px 25px;
        font-weight: 600;
        text-transform: none;
        letter-spacing: normal;
    }

    #loadMoreBtn {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
    }

    #loadAllBtn {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }

    /* Excel-only class remains unchanged */
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
                cursor: pointer;
                margin-right: 10px;
                color: #337ab7;
                font-size: 16px;
            }
            
            .icon-eattendance:hover {
                color: #23527c;
            }
            
            @media print {
                .icon-eattendance {
                    display: none !important; /* Hide only when printing */
                }
            }

            /* Enhanced Modal Styles */
            .modal-content {
                border: none;
                transition: all 0.3s ease;
                border-radius: 12px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            }
            
            .modal-header {
                border-bottom: none;
                position: relative;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 12px 12px 0 0;
                padding: 20px 25px;
            }
            
            .modal-header .close {
                color: white;
                opacity: 0.8;
                font-size: 24px;
                font-weight: 300;
            }
            
            .modal-header .close:hover {
                opacity: 1;
            }
            
            .modal-header::after {
                content: '';
                position: absolute;
                bottom: -10px;
                left: 50%;
                transform: translateX(-50%);
                width: 50px;
                height: 3px;
                background: rgba(255,255,255,0.3);
                border-radius: 3px;
            }
            
            .modal-body {
                border-radius: 0;
                padding: 25px;
            }
            
            .modal-footer {
                border-top: 1px solid #e9ecef;
                justify-content: space-between;
                padding: 20px 25px;
                border-radius: 0 0 12px 12px;
            }
            
            .modal-fade .modal-dialog {
                transform: scale(0.7);
                transition: transform 0.3s ease;
            }
            
            .modal-fade.in .modal-dialog {
                transform: scale(1);
            }
            
            /* Modal Button Styles */
            .modal-footer .btn {
                min-width: 120px;
                padding: 12px 20px;
                border-radius: 25px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-size: 12px;
            }
            
            .modal-footer .btn-default {
                background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%);
                color: white;
                border: none;
            }
            
            .modal-footer .btn-default:hover {
                background: linear-gradient(135deg, #adb5bd 0%, #6c757d 100%);
                color: white;
            }
            
            .modal-footer .btn-primary {
                background: linear-gradient(135deg, #007bff 0%, #667eea 100%);
                border: none;
            }
            
            .modal-footer .btn-primary:hover {
                background: linear-gradient(135deg, #667eea 0%, #007bff 100%);
            }
            
            /* Save/Action Button Styles in Modals */
            .save-attendance-btn, .save-subject-attendance-btn, .save-all-attendance-btn {
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                border: none;
                border-radius: 20px;
                padding: 10px 25px;
                font-weight: 600;
                color: white;
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-size: 12px;
            }
            
            .save-attendance-btn:hover, .save-subject-attendance-btn:hover, .save-all-attendance-btn:hover {
                background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
                color: white;
            }
            
            .alert {
                margin-bottom: 20px;
            }
            
            .form-control:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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


                        <input type="hidden" id="selected_exam_date" value="" class="form-control">

                        <div class="col-md-2 col-xs-12">
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <button type="button" id="getMarksBtn" class="btn btn-success col-md-12 col-xs-12 mark_btn" style="margin-top: 20px;">
                                            <span class="btn-text">Get Marks</span>
                                            <span class="btn-loading" style="display:none;"><i class="fa fa-spinner fa-spin"></i> Loading...</span>
                                        </button>
                                        <button type="button" class="btn btn-warning col-md-12 col-xs-12" id="generateRankBtn" style="margin-top: 5px; display:none;">
                                            <span class="fa fa-trophy"></span> Generate Rank
                                            <span id="rankLoading" style="display:none;"><i class="fa fa-spinner fa-spin"></i></span>
                                        </button>
                                        <!-- <input class="btn btn-black" type="reset" value="Clear"> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Important Note for Users -->
                <div class="alert alert-info" style="margin-top: 15px; border-left: 4px solid #17a2b8; background-color: #f8f9fa; border-radius: 6px; font-size: 16px; color: #dc3545;">
                    <i class="fa fa-info-circle" style="margin-right: 8px; color: #dc3545; font-size: 18px;"></i>
                    <strong style="color: #dc3545;">Note:</strong> After any marks changes, please click the <strong style="color: rgba(13, 57, 115, 1);">Generate Rank</strong> button to ensure correct data and updated rankings.
                </div>

                <?php if (customCompute($students)) { ?>
                    <!-- <form enctype="multipart/form-data" style="" action="<?= base_url('mark/mark_bulkimport'); ?>" class="form-horizontal" role="form" method="post">
                        <input  type="hidden"name="classId" value="" class="classId" />
                        <input  type="hidden"name="sectionId" value="" class="sectionId" />
                        <input  type="hidden"name="subjectId" value="" class="subjectId" />
                        <input  type="hidden" name="examId" value="" class="examId" />
                        <div class="form-group">
                            <label for="csvMark" class="col-sm-2 control-label col-xs-8 col-md-2">
                                <?= 'Add Mark Sheet' ?>
                                &nbsp;<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="Download sample mark shhet and add marks and upload"></i>
                            </label>
                            <div class="col-sm-3 col-xs-1 col-md-2">
                                <input class="form-control markImport" id="uploadFile" placeholder="Choose File" disabled />
                            </div>

                            <div class="col-sm-2 col-xs-1 col-md-1">
                                <div class="fileUpload btn btn-success form-control">
                                    <span class="fa fa-repeat"></span>
                                    <span><?= "Upload" ?></span>
                                    <input id="uploadBtn" type="file" class="upload markUpload" name="csvMark" />
                                </div>
                            </div>

                            <div class="col-md-1 rep-mar">
                                <input type="submit" class="btn btn-success" value="Import">
                            </div>
                        </div>
                    </form>
                    <form enctype="multipart/form-data" style="" action="<?= base_url('mark/add'); ?>" class="form-horizontal" role="form" method="post">
                        <input type="hidden" name="classesID" value="" class="classId" />
                        <input  type="hidden"name="sectionID" value="" class="sectionId" />
                        <input  type="hidden"name="subjectID" value="" class="subjectId" />
                        <input  type="hidden"name="examID" value="" class="examId" />
                        <input  type="hidden" name="downloadFile" value="1" id="" />
                        <div class="form-group">
                            <div class="col-md-1 rep-mar">
                                <input type="submit" class="btn btn-success" value="Download Sample File">
                            </div>
                        </div>
                    </form> -->

                <?php }  ?>


 <div class="row" style="margin-top: 20px;">
    <!-- Left: Info Box -->
    <div class="col-sm-6 box-layout-fame" style="display:none;">
        <div class="panel panel-default" style="padding: 10px; background-color: #f9f9f9;">
            <h5><center><?php echo $this->lang->line('mark_details'); ?></center></h5>
            <h5 id="examDetailsText"><center>Exam: </center></h5>
            <h5 id="classDetailsText"><center>Class: </center></h5>
            <h5 id="sectionDetailsText"><center>Section: </center></h5>   
            <br/>
            <br/>
           
            <div class="form-group">
                <button class="btn btn-black col-md-3" id="printBtn"><span class="fa fa-print"> &nbsp;</span>Print Sheet</button>

            
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
                        <input type="submit" class="btn btn-success" value="Save Marks">
                    </div>
                </form>
            </div>       
        </div>
    </div>

    <!-- Right: Grade Legend -->
    <div class="col-sm-6">
        <div class="grade-legend" style="background-color: #fdfdfd; border: 1px solid #ddd; padding: 10px; border-radius: 4px; display:none;">
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
</div>


            </div>

            
            
            <div class="col-sm-12">
                <div id="hide-table" style="display:none;">
                    <button onclick="exportTableToCSV('myTable','table_data.csv')" class="btn btn-primary">📥 Download CSV</button>
                    
                    <table class="table table-striped table-bordered table-hover dataTable no-footer" id="myTable">
                        <thead id="tableHeaders">
                            <!-- Headers will be dynamically loaded -->
                        </thead>
                        <tbody id="studentsTableBody">
                            <!-- Students will be dynamically loaded -->
                        </tbody>
                    </table>
                    
                    <!-- Loading States -->
                    <div id="loadMoreContainer" class="text-center" style="display:none; padding: 20px;">
                        <button id="loadMoreBtn" class="btn btn-primary" style="margin-right: 10px;">
                            <span class="load-text">Load More Students</span>
                            <span class="load-spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i> Loading...</span>
                        </button>
                        <button id="loadAllBtn" class="btn btn-success">
                            <span class="load-all-text">Load All Students</span>
                            <span class="load-all-spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i> Loading All...</span>
                        </button>
                    </div>
                    
                    <div id="noMoreData" class="text-center" style="display:none; padding: 20px;">
                        <p class="text-muted"><i class="fa fa-check"></i> All students loaded</p>
                    </div>
                </div>

                <!-- Initial Loading State -->
                <div id="initialLoading" class="text-center" style="display:none; padding: 50px;">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                    <p class="mt-3">Loading students data...</p>
                </div>

                <div class="text-right" id="actionButtons" style="display:none;">
                    <button class="btn btn-info sendSms" id="send_sms_marks_btn">
                        <span class="fa fa-comment"></span> Send Marks - SMS
                    </button>

                    <button class="btn btn-primary sendSms" id="send_whatsapp_marks_btn">
                        <span class="fa fa-whatsapp"></span> Send Marks - Whatsapp
                    </button>

                    <!-- <button type="button" class="btn btn-success " id="add_mark" name="add_mark" value="Save or Refresh Marks" > Save or Refresh Marks </button> -->
                </div>

                        <!-- Message Preview Modal -->
                        <div class="modal fade" id="messagePreviewModal" role="dialog">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">
                                            <span id="previewModalIcon"></span>
                                            <span id="previewModalTitle"></span>
                                        </h4>
                                    </div>
                                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                        <div id="messagePreviewContent" class="message-preview-container"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">
                                            <i class="fa fa-times"></i> Cancel
                                        </button>
                                        <button type="button" class="btn btn-primary" id="confirmSendMessage">
                                            <i class="fa fa-paper-plane"></i> Confirm & Send whatsapp
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <style>
                            .sms-preview-container {
                                padding: 10px;
                            }
                            .sms-preview-item {
                                background-color: #f8f9fa;
                                border: 1px solid #e9ecef;
                                border-radius: 8px;
                                padding: 15px;
                                margin-bottom: 15px;
                                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                                transition: all 0.3s ease;
                            }
                            .sms-preview-item:hover {
                                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                                transform: translateY(-2px);
                            }
                            .sms-preview-item .recipient {
                                color: #495057;
                                font-weight: 600;
                                margin-bottom: 10px;
                                padding-bottom: 10px;
                                border-bottom: 1px dashed #ced4da;
                            }
                            .sms-preview-item .message {
                                color: #212529;
                                line-height: 1.5;
                                white-space: pre-line;
                            }
                            .modal-header.bg-info {
                                background-color: #17a2b8;
                                color: white;
                            }
                            .modal-header .close {
                                color: white;
                                opacity: 0.8;
                            }
                            .modal-header .close:hover {
                                opacity: 1;
                            }
                        </style>

                        <!-- SMS Preview Modal -->
                        <div class="modal fade" id="smsPreviewModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Preview SMS Messages</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div id="smsPreviewContent">
                                            <!-- Messages will be populated here -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="confirmSendSMS">Confirm & Send SMS</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



          




                    <script type="text/javascript">
                        // Global variables for lazy loading
                        let currentOffset = 0;
                        let isLoading = false;
                        let hasMoreData = true;
                        let currentFilters = {};

                        jQuery(document).ready(function($) {
                            // Enhanced Toastr Configuration
                            toastr.options = {
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": true,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "300",
                                "timeOut": "4000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut",
                                "toastClass": "toast-enhanced",
                                "iconClass": "toast-icon",
                                "titleClass": "toast-title",
                                "messageClass": "toast-message"
                            };
                            
                            // Add custom CSS for enhanced toasts
                            if (!$('#toast-enhanced-styles').length) {
                                $('<style id="toast-enhanced-styles">')
                                .html(`
                                    .toast-enhanced {
                                        border-radius: 8px !important;
                                        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
                                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
                                    }
                                    .toast-success.toast-enhanced,
                                    #toast-container > .toast-success {
                                        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
                                        background-color: #28a745 !important;
                                        color: white !important;
                                    }
                                    .toast-error.toast-enhanced,
                                    #toast-container > .toast-error {
                                        background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%) !important;
                                        background-color: #dc3545 !important;
                                        color: white !important;
                                    }
                                    .toast-warning.toast-enhanced,
                                    #toast-container > .toast-warning {
                                        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
                                        background-color: #ffc107 !important;
                                        color: #212529 !important;
                                    }
                                    .toast-info.toast-enhanced,
                                    #toast-container > .toast-info {
                                        background: linear-gradient(135deg, #17a2b8 0%, #667eea 100%) !important;
                                        background-color: #17a2b8 !important;
                                        color: white !important;
                                    }
                                    .toast-title {
                                        font-weight: 600 !important;
                                        color: inherit !important;
                                    }
                                    .toast-message {
                                        font-size: 13px !important;
                                        opacity: 0.95;
                                        color: inherit !important;
                                    }
                                `)
                                .appendTo('head');
                            }

                            // Handle Get Marks button click
                            $('#getMarksBtn').on('click', function() {
                                var classesID = $('#classesID').val();
                                var examID = $('#examID').val();
                                var sectionID = $('#sectionID').val();
                                
                                if (!classesID || classesID == '0') {
                                    toastr["warning"]("Please select a class");
                                    return;
                                }
                                if (!examID || examID == '0') {
                                    toastr["warning"]("Please select an exam");
                                    return;
                                }
                                if (!sectionID || sectionID == '0') {
                                    toastr["warning"]("Please select a section");
                                    return;
                                }

                                // Reset pagination
                                currentOffset = 0;
                                hasMoreData = true;
                                currentFilters = {
                                    classesID: classesID,
                                    examID: examID,
                                    sectionID: sectionID
                                };

                                // Clear previous data
                                $('#studentsTableBody').empty();
                                $('#hide-table').hide();
                                $('#actionButtons').hide();
                                $('#loadMoreContainer').hide();
                                $('#noMoreData').hide();

                                // Show loading
                                $('#initialLoading').show();
                                
                                loadStudentsData(true);
                            });

                            // Handle Load More button click
                            $('#loadMoreBtn').on('click', function() {
                                if (!isLoading && hasMoreData) {
                                    loadStudentsData(false);
                                }
                            });

                            // Handle Load All Students button click
                            $('#loadAllBtn').on('click', function() {
                                if (!isLoading && hasMoreData) {
                                    loadAllStudents();
                                }
                            });

                            // Function to load all remaining students
                            function loadAllStudents() {
                                isLoading = true;
                                $('.load-all-text').hide();
                                $('.load-all-spinner').show();
                                $('#loadAllBtn').prop('disabled', true);
                                $('#loadMoreBtn').prop('disabled', true);
                                
                                // Load all remaining students by setting offset to current position and limit to remaining count
                                $.ajax({
                                    type: 'POST',
                                    url: '<?= base_url("mark/loadStudentsAjax") ?>',
                                    data: {
                                        classesID: currentFilters.classesID,
                                        examID: currentFilters.examID,
                                        sectionID: currentFilters.sectionID,
                                        offset: currentOffset,
                                        loadAll: true // Special flag to load all remaining
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.status && response.data) {
                                            // Append all remaining students
                                            $('#studentsTableBody').append(response.data);
                                            
                                            // Append modals
                                            if (response.modals) {
                                                $('body').append(response.modals);
                                            }
                                            
                                            // Update counters
                                            currentOffset += response.count;
                                            
                                            // Hide both buttons and show completion message
                                            $('#loadMoreContainer').hide();
                                            $('#noMoreData').show();
                                            hasMoreData = false;
                                            
                                            toastr["success"]("All " + response.totalStudents + " students loaded successfully!");
                                        } else {
                                            toastr["error"](response.message || "Failed to load all students");
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Load All Students Error:', error);
                                        toastr["error"]("Failed to load all students: " + error);
                                    },
                                    complete: function() {
                                        isLoading = false;
                                        $('.load-all-text').show();
                                        $('.load-all-spinner').hide();
                                        $('#loadAllBtn').prop('disabled', false);
                                        $('#loadMoreBtn').prop('disabled', false);
                                    }
                                });
                            }

                            // Handle Generate Rank button click
                            $('#generateRankBtn').on('click', function() {
                                var btn = $(this);
                                var btnText = btn.find('span:not(#rankLoading)').first();
                                var loading = $('#rankLoading');
                                
                                btn.prop('disabled', true);
                                btnText.hide();
                                loading.show();
                                
                                $.ajax({
                                    type: 'POST',
                                    url: "<?= base_url('mark/generateRanks') ?>",
                                    data: {
                                        classesID: currentFilters.classesID,
                                        examID: currentFilters.examID,
                                        sectionID: currentFilters.sectionID
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.status) {
                                            toastr["success"](response.message || "Ranks generated successfully!");
                                            // Reload the current data to show updated ranks
                                            currentOffset = 0;
                                            hasMoreData = true;
                                            $('#studentsTableBody').empty();
                                            loadStudentsData(true);
                                        } else {
                                            toastr["error"](response.message || "Failed to generate ranks");
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        toastr["error"]("Error generating ranks: " + error);
                                    },
                                    complete: function() {
                                        btn.prop('disabled', false);
                                        btnText.show();
                                        loading.hide();
                                    }
                                });
                            });

                            function loadStudentsData(isInitialLoad) {
                                if (isLoading) return;
                                
                                isLoading = true;
                                
                                if (isInitialLoad) {
                                    $('.btn-text').hide();
                                    $('.btn-loading').show();
                                } else {
                                    $('.load-text').hide();
                                    $('.load-spinner').show();
                                }

                                $.ajax({
                                    type: 'POST',
                                    url: "<?= base_url('mark/loadStudentsAjax') ?>",
                                    data: {
                                        classesID: currentFilters.classesID,
                                        examID: currentFilters.examID,
                                        sectionID: currentFilters.sectionID,
                                        offset: currentOffset
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.status) {
                                            if (isInitialLoad) {
                                                // Set static info
                                                if (response.staticData) {
                                                    $('#examInfo').html('Exam: ' + response.staticData.examName);
                                                    $('#classInfo').html('Class: ' + response.staticData.className);
                                                    $('#sectionInfo').html('Section: ' + response.staticData.sectionName);
                                                    $('#marksInfo').show();
                                                    
                                                    // Update the box-layout-fame panel details
                                                    $('#examDetailsText').html('<center>Exam : ' + response.staticData.examName + '</center>');
                                                    $('#classDetailsText').html('<center>Class : ' + response.staticData.className + '</center>');
                                                    $('#sectionDetailsText').html('<center>Section : ' + response.staticData.sectionName + '</center>');
                                                    
                                                    // Show the static info panels and buttons
                                                    $('.box-layout-fame').show();
                                                    $('.grade-legend').show();
                                                }
                                                
                                                // Set table headers
                                                $('#tableHeaders').html(response.headers);
                                                $('#hide-table').show();
                                                $('#actionButtons').show();
                                                $('#generateRankBtn').show();
                                                $('#initialLoading').hide();
                                            }
                                            
                                            // Append student rows
                                            $('#studentsTableBody').append(response.data);
                                            
                                            // Append modals to body (for attendance functionality)
                                            if (response.modals) {
                                                $('body').append(response.modals);
                                            }
                                            
                                            currentOffset += response.count;
                                            
                                            // Check if more data exists
                                            if (response.count < 20) {
                                                hasMoreData = false;
                                                $('#loadMoreContainer').hide();
                                                $('#noMoreData').show();
                                            } else {
                                                $('#loadMoreContainer').show();
                                            }
                                            
                                        } else {
                                            toastr["error"](response.message || "Failed to load students data");
                                            if (isInitialLoad) {
                                                $('#initialLoading').hide();
                                            }
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        toastr["error"]("Error loading students data: " + error);
                                        if (isInitialLoad) {
                                            $('#initialLoading').hide();
                                        }
                                    },
                                    complete: function() {
                                        isLoading = false;
                                        
                                        if (isInitialLoad) {
                                            $('.btn-text').show();
                                            $('.btn-loading').hide();
                                        } else {
                                            $('.load-text').show();
                                            $('.load-spinner').hide();
                                        }
                                    }
                                });
                            }

                            // Common function to build preview
                            function buildMessagePreview(messageType) {
                                var selectedCheckboxes = $('input[name="send_sms_marks"]:checked');
                                
                                if (selectedCheckboxes.length === 0) {
                                    toastr["warning"]("Please select at least one student");
                                    return false;
                                }

                                var previewHtml = '<div class="sms-preview-list">';
                                
                                selectedCheckboxes.each(function() {
                                    var $checkbox = $(this);
                                    var marks_template = $checkbox.attr('marks_template') || '';
                                    
                                    // Remove trailing comma if exists
                                    var template1 = marks_template.endsWith(',') ? 
                                        marks_template.slice(0, -1) : marks_template;
                                    
                                    var subs = template1.split(',');
                                    
                                    // Format the message
                                    var message = 'Dear parent, your children ' + $checkbox.attr('st_names') + 
                                                ' Exam name ' + $checkbox.attr('exam_name') + 
                                                ' marks are ' + subs.join(' and ') +
                                                '. Total: ' + $checkbox.attr('total_marks') + 
                                                ' Grade:' + $checkbox.attr('marks_grade');

                                    previewHtml += '<div class="sms-preview-item">';
                                    previewHtml += '<div class="recipient"><i class="fa fa-user"></i> ' + 
                                                 $checkbox.attr('st_names') + 
                                                 ' <span style="color: #6c757d">(' + 
                                                 $checkbox.attr('mobile_no') + ')</span></div>';
                                    previewHtml += '<div class="message">' + message + '</div>';
                                    previewHtml += '</div>';
                                });
                                
                                previewHtml += '</div>';
                                
                                $('#smsPreviewContent').html(previewHtml);
                                window.previewMessageType = messageType;
                                
                                // Update modal title based on message type
                                if (messageType === 'whatsapp') {
                                    $('#smsPreviewModal .modal-title').text('Preview WhatsApp Messages');
                                    $('#confirmSendSMS').html('<i class="fa fa-whatsapp"></i> Confirm & Send WhatsApp');
                                } else {
                                    $('#smsPreviewModal .modal-title').text('Preview SMS Messages');
                                    $('#confirmSendSMS').html('<i class="fa fa-paper-plane"></i> Confirm & Send SMS');
                                }
                                
                                $('#smsPreviewModal').modal('show');
                                return true;
                            }

                            // SMS Preview and Send functionality
                            $("#send_sms_marks_btn").on('click', function(e) {
                                e.preventDefault();
                                buildMessagePreview('sms');
                            });

                            // WhatsApp button functionality
                            $("#send_whatsapp_marks_btn").on('click', function(e) {
                                e.preventDefault();
                                buildMessagePreview('whatsapp');
                            });

                            // Handle confirmation and sending
                            $('#confirmSendSMS').on('click', function() {
                                var selectedData = {
                                    st_ids: [],
                                    mobile_no: [],
                                    marks_template: [],
                                    st_names: [],
                                    total_marks: [],
                                    exam_name: [],
                                    marks_grade: [],
                                    sms_rank: [],
                                    exam_date: []
                                };

                                $('input[name="send_sms_marks"]:checked').each(function() {
                                    var $checkbox = $(this);
                                    selectedData.st_ids.push($checkbox.attr('st_ids'));
                                    selectedData.mobile_no.push($checkbox.attr('mobile_no'));
                                    selectedData.marks_template.push($checkbox.attr('marks_template'));
                                    selectedData.st_names.push($checkbox.attr('st_names'));
                                    selectedData.total_marks.push($checkbox.attr('total_marks'));
                                    selectedData.exam_name.push($checkbox.attr('exam_name'));
                                    selectedData.marks_grade.push($checkbox.attr('marks_grade'));
                                    selectedData.sms_rank.push($checkbox.attr('sms_rank'));
                                    selectedData.exam_date.push($checkbox.attr('exam_date'));
                                });

                                var previewType = window.previewMessageType || 'sms';
                                var url = previewType === 'whatsapp' ?
                                    "<?=base_url('progresscardreport/send_marks_to_whatsapp')?>" :
                                    "<?=base_url('progresscardreport/send_marks_to_sms')?>";

                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: selectedData,
                                    success: function(response) {
                                        $('#smsPreviewModal').modal('hide');
                                        try {
                                            var total = JSON.parse(response);
                                            toastr["success"]((previewType === 'whatsapp' ? "WhatsApp" : "SMS") + " sent successfully");
                                        } catch (e) {
                                            toastr["success"]((previewType === 'whatsapp' ? "WhatsApp" : "SMS") + " request completed");
                                        }
                                    },
                                    error: function() {
                                        $('#smsPreviewModal').modal('hide');
                                        toastr["error"]("Failed to send " + (previewType === 'whatsapp' ? "WhatsApp" : "SMS"));
                                    }
                                });
                        });

                        // AJAX handler for save all attendance
                        $(document).on('click', '.save-all-attendance-btn', function(e) {
                            e.preventDefault();
                            
                            var studentId = $(this).data('student-id');
                            var form = $('.attendance-all-form[data-student-id="' + studentId + '"]');
                            var modalId = '#attendance-all-modal_' + studentId;
                            
                            // Get form data
                            var formData = {
                                attendance: form.find('select[name="attendance"]').val(),
                                examID: form.find('input[name="examID"]').val(),
                                classesID: form.find('input[name="classesID"]').val(),
                                sectionID: form.find('input[name="sectionID"]').val(),
                                studentID: form.find('input[name="studentID"]').val()
                            };
                            
                            // Disable button and show loading
                            var saveBtn = $(this);
                            saveBtn.prop('disabled', true).text('Saving...');
                            
                            $.ajax({
                                type: 'POST',
                                url: "<?= base_url('Mark/saveAllAttendance') ?>",
                                data: formData,
                                success: function(response) {
                                    try {
                                        // Try to parse as JSON
                                        var jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;
                                        
                                        if (jsonResponse.status) {
                                            toastr["success"](jsonResponse.message || "Attendance saved successfully");
                                            $(modalId).modal('hide');
                                            
                                            // Update the UI directly instead of page reload
                                            var studentRow = $('tr').filter(function() {
                                                return $(this).find('input[st_ids="' + studentId + '"]').length > 0;
                                            });
                                            
                                            if (formData.attendance === 'Absent') {
                                                // Show "A" for absent in all subject columns with icons
                                                studentRow.find('input.input_mark').each(function() {
                                                    var subjectId = $(this).attr('subj_id');
                                                    var iconHtml = '<i class="fa icon-eattendance pull-left" title="add exam attendance" data-toggle="modal" data-target="#attendance-subject-modal_' + studentId + '_' + subjectId + '"></i>';
                                                    var absentHtml = '<span class="attendance-circle" style="margin-left: 20px;">A</span>';
                                                    $(this).closest('td').html(iconHtml + absentHtml);
                                                });
                                                
                                                // Update total and grade
                                                var totalCell = studentRow.find('td').eq(-3); // Total column (3rd from end)
                                                var gradeCell = studentRow.find('td').eq(-2);  // Grade column (2nd from end)
                                                
                                                totalCell.text('0');
                                                gradeCell.html('<span class="grade-label grade-d">D</span>');
                                                
                                                // Update SMS checkbox attributes
                                                var smsCheckbox = studentRow.find('input[st_ids="' + studentId + '"]');
                                                if (smsCheckbox.length) {
                                                    var currentTotalMarks = smsCheckbox.attr('total_marks');
                                                    if (currentTotalMarks) {
                                                        var outOf = currentTotalMarks.split('/')[1] || '0';
                                                        smsCheckbox.attr('total_marks', '0/' + outOf);
                                                        smsCheckbox.attr('marks_grade', 'D Rank -');
                                                    }
                                                }
                                            } else {
                                                // For Present - would need to reload marks data, so show message
                                                toastr["info"]("Please reload the page to see updated marks for present status");
                                            }
                                        } else {
                                            toastr["error"](jsonResponse.message || "Failed to save attendance");
                                        }
                                    } catch (e) {
                                        // If JSON parsing fails, treat as HTML error response
                                        console.error('Response parsing error:', e);
                                        console.log('Server response:', response);
                                        
                                        if (response.includes('success') || response.includes('saved')) {
                                            toastr["success"]("Attendance saved successfully");
                                            $(modalId).modal('hide');
                                            
                                            // Update UI for absent status
                                            var studentRow = $('tr').filter(function() {
                                                return $(this).find('input[st_ids="' + studentId + '"]').length > 0;
                                            });
                                            
                                            if (formData.attendance === 'Absent') {
                                                studentRow.find('input.input_mark').each(function() {
                                                    var subjectId = $(this).attr('subj_id');
                                                    var iconHtml = '<i class="fa icon-eattendance pull-left" title="add exam attendance" data-toggle="modal" data-target="#attendance-subject-modal_' + studentId + '_' + subjectId + '"></i>';
                                                    var absentHtml = '<span class="attendance-circle" style="margin-left: 20px;">A</span>';
                                                    $(this).closest('td').html(iconHtml + absentHtml);
                                                });
                                                
                                                var totalCell = studentRow.find('td').eq(-3);
                                                var gradeCell = studentRow.find('td').eq(-2);
                                                
                                                totalCell.text('0');
                                                gradeCell.html('<span class="grade-label grade-d">D</span>');
                                                
                                                // Update SMS checkbox
                                                var smsCheckbox = studentRow.find('input[st_ids="' + studentId + '"]');
                                                if (smsCheckbox.length) {
                                                    var currentTotalMarks = smsCheckbox.attr('total_marks');
                                                    if (currentTotalMarks) {
                                                        var outOf = currentTotalMarks.split('/')[1] || '0';
                                                        smsCheckbox.attr('total_marks', '0/' + outOf);
                                                        smsCheckbox.attr('marks_grade', 'D Rank -');
                                                    }
                                                }
                                            } else {
                                                toastr["info"]("Please reload the page to see updated marks for present status");
                                            }
                                        } else {
                                            toastr["error"]("Server returned an unexpected response. Please check browser console for details.");
                                        }
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('AJAX Error Details:', {
                                        status: status,
                                        error: error,
                                        responseText: xhr.responseText,
                                        statusCode: xhr.status
                                    });
                                    
                                    if (xhr.status === 404) {
                                        toastr["error"]("Server endpoint not found. Please check the URL.");
                                    } else if (xhr.status === 500) {
                                        toastr["error"]("Server error occurred. Please check server logs.");
                                    } else {
                                        toastr["error"]("Failed to save attendance: " + error);
                                    }
                                },
                                complete: function() {
                                    // Re-enable button
                                    saveBtn.prop('disabled', false).text('Save');
                                }
                            });
                        });

                        // AJAX handler for subject-specific attendance
                        $(document).on('click', '.save-subject-attendance-btn', function(e) {
                            e.preventDefault();
                            
                            var studentId = $(this).data('student-id');
                            var subjectId = $(this).data('subject-id');
                            var form = $('.attendance-subject-form[data-student-id="' + studentId + '"][data-subject-id="' + subjectId + '"]');
                            var modalId = '#attendance-subject-modal_' + studentId + '_' + subjectId;
                            
                            // Get form data
                            var formData = {
                                attendance: form.find('select[name="attendance"]').val(),
                                examID: form.find('input[name="examID"]').val(),
                                classesID: form.find('input[name="classesID"]').val(),
                                sectionID: form.find('input[name="sectionID"]').val(),
                                studentID: form.find('input[name="studentID"]').val(),
                                subjectID: form.find('input[name="subjectID"]').val()
                            };
                            
                            // Disable button and show loading
                            var saveBtn = $(this);
                            saveBtn.prop('disabled', true).text('Saving...');
                            
                            $.ajax({
                                type: 'POST',
                                url: "<?= base_url('Mark/saveSubjectAttendance') ?>",
                                data: formData,
                                success: function(response) {
                                    try {
                                        var jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;
                                        
                                        if (jsonResponse.status) {
                                            toastr["success"](jsonResponse.message || "Subject attendance saved successfully");
                                            $(modalId).modal('hide');
                                            
                                            // Update the specific mark input based on attendance status
                                            var targetInput = $('input[subj_id="' + subjectId + '"][name*="mark-"]').filter(function() {
                                                return $(this).closest('tr').find('input[st_ids="' + studentId + '"]').length > 0;
                                            });
                                            
                                            var targetCell = null;
                                            if (targetInput.length) {
                                                targetCell = targetInput.closest('td');
                                            } else {
                                                // If no input found, look for the absent cell
                                                targetCell = $('tr').filter(function() {
                                                    return $(this).find('input[st_ids="' + studentId + '"]').length > 0;
                                                }).find('td').filter(function() {
                                                    return $(this).find('span.attendance-circle').length > 0 && 
                                                           $(this).find('i[data-target*="_' + subjectId + '"]').length > 0;
                                                });
                                            }
                                            
                                            if (targetCell && targetCell.length) {
                                                var iconHtml = '<i class="fa icon-eattendance pull-left" title="add exam attendance" data-toggle="modal" data-target="#attendance-subject-modal_' + studentId + '_' + subjectId + '"></i>';
                                                
                                                if (jsonResponse.attendance === 'Absent') {
                                                    var absentHtml = '<span class="attendance-circle" style="margin-left: 20px;">A</span>';
                                                    targetCell.html(iconHtml + absentHtml);
                                                } else if (jsonResponse.attendance === 'Present') {
                                                    // Present - create input field with server-provided values
                                                    var markValue = jsonResponse.markValue || 0;
                                                    var maxMark = jsonResponse.maxMark || 100;
                                                    var markName = jsonResponse.markName || (subjectId + 'mark-0');
                                                    var markId = jsonResponse.markID || '1';
                                                    
                                                    var inputHtml = '<input id="' + markId + '" subj_id="' + subjectId + '" class="aaa form-control mark input_mark" style="width: 80px !important; margin-left: 20px;" ' +
                                                                   'name="' + markName + '" value="' + markValue + '" min="0" max="' + maxMark + '">';
                                                    targetCell.html(iconHtml + inputHtml);
                                                }
                                            }
                                        } else {
                                            toastr["error"](jsonResponse.message || "Failed to save subject attendance");
                                        }
                                    } catch (e) {
                                        console.error('Response parsing error:', e);
                                        toastr["error"]("Server returned an unexpected response for subject attendance.");
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Subject Attendance AJAX Error:', {
                                        status: status,
                                        error: error,
                                        responseText: xhr.responseText
                                    });
                                    toastr["error"]("Failed to save subject attendance: " + error);
                                },
                                complete: function() {
                                    saveBtn.prop('disabled', false).text('Save');
                                }
                            });
                        });

                        // AJAX handler for individual attendance
                        $(document).on('click', '.save-attendance-btn', function(e) {
                            e.preventDefault();
                            
                            var studentId = $(this).data('student-id');
                            var form = $('.attendance-form[data-student-id="' + studentId + '"]');
                            var modalId = '#attendance-modal_' + studentId;
                            
                            // Get form data
                            var formData = {
                                attendance: form.find('select[name="attendance"]').val(),
                                examID: form.find('input[name="examID"]').val(),
                                classesID: form.find('input[name="classesID"]').val(),
                                sectionID: form.find('input[name="sectionID"]').val(),
                                studentID: form.find('input[name="studentID"]').val()
                            };
                            
                            // Disable button and show loading
                            var saveBtn = $(this);
                            saveBtn.prop('disabled', true).text('Saving...');
                            
                            $.ajax({
                                type: 'POST',
                                url: "<?= base_url('Mark/saveIndividualAttendance') ?>",
                                data: formData,
                                success: function(response) {
                                    try {
                                        var jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;
                                        
                                        if (jsonResponse.status) {
                                            toastr["success"](jsonResponse.message || "Individual attendance saved successfully");
                                            $(modalId).modal('hide');
                                            // Note: Individual attendance affects specific exam records
                                            toastr["info"]("Individual attendance updated. You may need to refresh to see grade changes.");
                                        } else {
                                            toastr["error"](jsonResponse.message || "Failed to save individual attendance");
                                        }
                                    } catch (e) {
                                        console.error('Response parsing error:', e);
                                        toastr["error"]("Server returned an unexpected response for individual attendance.");
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Individual Attendance AJAX Error:', {
                                        status: status,
                                        error: error,
                                        responseText: xhr.responseText
                                    });
                                    toastr["error"]("Failed to save individual attendance: " + error);
                                },
                                complete: function() {
                                    saveBtn.prop('disabled', false).text('Save');
                                }
                            });
                        });

                        // Lazy load images
                                // setTimeout(lazyLoad, 1000);
                            });                        $(document).on("keyup", ".mark", function() {
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
                                var $inputField = $(this);
                                
                                // Get student ID from the closest row
                                var studentId = $inputField.closest('tr').find('input[name*="st_ids"], input[st_ids]').attr('st_ids') || 
                                               $inputField.closest('tr').find('input[name="studentID"]').val() ||
                                               $inputField.closest('tr').data('student-id');
                               
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
                                    "examID": $("#examID").val(),
                                    "classesID": $("#classesID").val(), 
                                    "subjectID":subj_id,
                                    "studentID": studentId,
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

        // Use event delegation for better compatibility
        $(document).on('change', '#checkAll', function(){
            var isChecked = this.checked;
            console.log("Check All clicked, checked state:", isChecked);
            $('input[name="send_sms_marks"].checkbox').each(function() {
                $(this).prop('checked', isChecked);
            });
            console.log("Checkboxes updated. Count:", $('input[name="send_sms_marks"].checkbox').length);
        });
        

        
        
        $(document).on("click","#send_sms_marks_btn",function(){

            if ($(".checkbox:checked").length === 0) {
                alert("Please check at least one checkbox before proceeding.");
                return false;
            }

            var sms_rank = [];

            var marks_grade = [];

            var st_ids = [];
            st_names =[];
            mobile_no = [];
            exam_name = [];
            total_marks = [] ;
            marks_template = []; 
            i=j=k=l=m=n=p=q=0;

            $('.checkbox:checked').each(function(){        
                // var values = $(this).val();
                // var sids = $(this).attr("st_ids");
                marks_grade[p++] = $(this).attr("marks_grade");
                sms_rank[q++] = $(this).attr("sms_rank");
                
                st_ids[i++] = $(this).attr("st_ids");
                st_names[j++] = $(this).attr("st_names");
                mobile_no[k++] = $(this).attr("mobile_no");
                exam_name[l++] = $(this).attr("exam_name");
                total_marks[m++] = $(this).attr("total_marks");
                marks_template[n++] = $(this).attr("marks_template");
            });

 
            // $.ajax({
                            
            //     type: "POST",
            //     url: "<?php echo site_url('progresscardreport/send_marks_to_sms1'); ?>",
            //     // dataType: "json",
            //     data: {"st_ids":st_ids,"st_names":st_names,"mobile_no":mobile_no,"exam_name":exam_name,"total_marks":total_marks,"marks_template":marks_template,"marks_grade":marks_grade,"sms_rank" : sms_rank},
            //     success: function(result)
            //     {
                    
            //     }
            // })
        });

        // Override direct WhatsApp send: show preview modal first
        $(document).on("click","#send_whatsapp_marks_btn",function(e){
            e.preventDefault();
            if ($(".checkbox:checked").length === 0) {
                alert("Please check at least one checkbox before proceeding.");
                return false;
            }
            // The functionality is now handled in the main jQuery ready block above
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



$('#examID').on('change', function() {
    var examDate = $(this).find(':selected').data('examdate'); // YYYY-MM-DD
    if (examDate) {
        var parts = examDate.split("-"); // [YYYY, MM, DD]
        var formatted = parts[2] + "-" + parts[1] + "-" + parts[0]; // DD-MM-YYYY
        $("#selected_exam_date").val(formatted);
    }
});




</script>

  
<script>
function exportTableToCSV(tableID, filename = 'table.csv') {
    var csv = [];
    var rows = document.querySelectorAll(`#${tableID} tr`);

    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        for (var j = 0; j < cols.length; j++) {
            // Escape double quotes
            var text = cols[j].innerText.replace(/"/g, '""');
            row.push('"' + text + '"');
        }
        csv.push(row.join(","));
    }

    // Create download link
    var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";

    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>
