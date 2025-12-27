<style>
/* Enhanced Design - Preserving Original Box Classes */

 
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

 
 
.breadcrumb {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 25px;
    padding: 10px 20px;
    margin-top: 15px;
    backdrop-filter: blur(10px);
}

.breadcrumb li a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: color 0.3s;
}

.breadcrumb li a:hover {
    color: white;
}

.breadcrumb li.active {
    color: white;
    font-weight: 500;
}

.box-body {
    padding: 0;
    border-radius: 0 0 15px 15px;
    background: white;
}

/* Modern Tab Design */
.nav-tabs-custom {
    margin-bottom: 0;
    background: transparent;
    box-shadow: none;
}

.nav-tabs-custom .nav-tabs {
    border-bottom: none;
    background: #f8f9fa;
    padding: 15px 20px 0;
    margin: 0;
    border-radius: 0;
}

.nav-tabs-custom .nav-tabs li {
    margin-bottom: 0;
    margin-right: 10px;
}

.nav-tabs-custom .nav-tabs li a {
    border: none;
    border-radius: 25px;
    color: #6c757d;
    background: #e9ecef;
    padding: 12px 30px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.nav-tabs-custom .nav-tabs li a::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: -1;
}

.nav-tabs-custom .nav-tabs li.active a,
.nav-tabs-custom .nav-tabs li a:hover {
    color: white;
    background: transparent;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.nav-tabs-custom .nav-tabs li.active a::before,
.nav-tabs-custom .nav-tabs li a:hover::before {
    left: 0;
}

.tab-content {
    background: white;
    padding: 30px;
    min-height: 400px;
    border-radius: 0 0 15px 15px;
}

/* Enhanced Form Design */
.tab-pane {
    position: relative;
}

.form-section-header {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 15px 25px;
    border-radius: 12px;
    margin-bottom: 30px;
    font-weight: 700;
    text-align: center;
    box-shadow: 0 5px 15px rgba(240, 147, 251, 0.4);
    position: relative;
    overflow: hidden;
}

.form-section-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: shimmer 2s linear infinite;
}

@keyframes shimmer {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.form-group {
    margin-bottom: 25px;
    position: relative;
}

.form-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 13px;
    display: block;
    position: relative;
}

.form-group label i {
    color: #667eea;
    margin-right: 8px;
    font-size: 14px;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #f8f9fa;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
    background: white;
    transform: translateY(-1px);
}

.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    background: #f8f9fa;
    min-height: 48px;
    transition: all 0.3s ease;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 44px;
    padding-left: 16px;
    color: #495057;
}

.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #667eea;
    background: white;
    transform: translateY(-1px);
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
}

/* Premium Button Design */
.btn {
    border-radius: 12px;
    padding: 12px 30px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.3s ease;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    box-shadow: 0 5px 15px rgba(86, 171, 47, 0.4);
}

.btn-success:hover {
    background: linear-gradient(135deg, #4a9c26 0%, #9dd9bb 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(86, 171, 47, 0.6);
}

.btn-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-info:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
}

.btn-block {
    width: 100%;
}

/* Enhanced Row Design */
.tab-pane .row {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 25px;
    margin: 0 -5px;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
    position: relative;
}

.tab-pane .row::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 15px 15px 0 0;
}

/* Loading Container Enhancement */
#load_balancefeesreport {
    margin-top: 25px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

/* Responsive Design */
@media (max-width: 768px) {
    .nav-tabs-custom .nav-tabs li a {
        padding: 10px 20px;
        font-size: 12px;
    }
    
    .form-section-header {
        padding: 12px 20px;
        font-size: 14px;
    }
    
    .tab-content {
        padding: 20px 15px;
    }
    
    .box-title {
        font-size: 1.3em;
    }
    
    .btn {
        padding: 10px 20px;
        font-size: 13px;
    }
}

/* Hover Animations */
.form-group:hover .form-control {
    border-color: #667eea;
    background: white;
    transform: translateY(-1px);
}

.form-group:hover label {
    color: #667eea;
}

.form-group:hover label i {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}

/* Tab Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translate3d(0, 30px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

.tab-pane.active {
    animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa iniicon-balancefeesreport"></i> 
        <?=$this->lang->line('panel_title')?>

        <!-- <a style="margin-left: 20px;; background-color:rgb(2, 97, 95); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;" target="_blank" href="<?php echo base_url('balancefeesreport/all_class_wise');?>"> <i class="fa iniicon-balancefeesreport"></i> All Class Wise Balance Report </a> -->
    </h3>

        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"> <?=$this->lang->line('menu_balancefeesreport')?></li>
        </ol>
    </div><!-- /.box-header -->
    
    <!-- Tab Navigation -->
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">Balance Fee Report</a></li>
                <li><a href="#tab_2" data-toggle="tab">Class-wise Summary Report</a></li>
            </ul>
            
            <div class="tab-content">
                <!-- TAB 1: Balance Fee Report -->
                <div class="tab-pane active" id="tab_1">
                    <div class="form-section-header">
                        <i class="fa fa-user-circle"></i> Student-wise Balance Fee Report
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <form id="balanceFeeForm">
                                <div class="form-group col-sm-4" id="classesDiv">
                                    <label><i class="fa fa-graduation-cap"></i> <?=$this->lang->line("balancefeesreport_class")?></label>
                                    <?php
                                        $classesArray = array(
                                            "0" => $this->lang->line("balancefeesreport_please_select"),
                                        );
                                        foreach ($classes as $classaKey => $classa) {
                                            $classesArray[$classa->classesID] = $classa->classes;
                                        }
                                        echo form_dropdown("classesID", $classesArray, set_value("classesID"), "id='classesID' class='form-control select2'");
                                     ?>
                                </div>

                                <div class="form-group col-sm-4" id="villegesDiv">
                                    <label><i class="fa fa-map-marker"></i> Villages</label>
                                    <?php
                                        $villegesArray = array(
                                            "0" => $this->lang->line("balancefeesreport_please_select"),
                                        );
                                        foreach ($villeges as $classaKey => $v) {
                                            $villegesArray[$v['villageID']] = $v['villageName'];
                                        }
                                        echo form_dropdown("villageID", $villegesArray, set_value("villageID"), "id='villageID' class='form-control select2'");
                                     ?>
                                </div>

                                <div class="form-group col-sm-4" id="feetypeDiv">
                                    <label><i class="fa fa-money"></i> Fee Types</label>
                                    <?php
                                        $feetypeArray = [];
                                        if (customCompute($feetypes)) {
                                            foreach ($feetypes as $feetype) {
                                                $feetypeArray[$feetype->feetypesID] = $feetype->feetypes;
                                            }
                                        }

                                        echo form_dropdown(
                                            "feetypeID[]",
                                            $feetypeArray,
                                            set_value("feetypeID[]"),
                                            "id='feetypeID' class='form-control select2' multiple='multiple' placeholder='Select Fee Types'"
                                        );
                                    ?>
                                </div>

                                <div class="form-group col-sm-4" id="sectionDiv">
                                    <label><i class="fa fa-users"></i> <?=$this->lang->line("balancefeesreport_section")?></label>
                                    <?php
                                        $sectionArray = array(
                                            "0" => $this->lang->line("balancefeesreport_please_select"),
                                        );
                                        echo form_dropdown("sectionID", $sectionArray, set_value("sectionID"), "id='sectionID' class='form-control select2'");
                                     ?>
                                </div>

                                <div class="form-group col-sm-4" id="studentDiv">
                                    <label><i class="fa fa-user"></i> <?=$this->lang->line("balancefeesreport_student")?></label>
                                    <?php
                                        $studentArray = array(
                                            "0" => $this->lang->line("balancefeesreport_please_select"),
                                        );
                                        echo form_dropdown("studentID", $studentArray, set_value("studentID"), "id='studentID' class='form-control select2'");
                                     ?>
                                </div>

                                <div class="col-sm-4">
                                    <label>&nbsp;</label>
                                    <button id="get_duefeesreport" type="button" class="btn btn-success btn-block" style="margin-top:8px;">
                                        <i class="fa fa-search"></i> Generate Report
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- /.tab-pane -->
                
                <!-- TAB 2: Class-wise Summary Report -->
                <div class="tab-pane" id="tab_2">
                    <div class="form-section-header">
                        <i class="fa fa-bar-chart"></i> Class-wise Summary Report
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <form id="classWiseForm">
                                <div class="form-group col-sm-6" id="classesMultiDiv">
                                    <label><i class="fa fa-graduation-cap"></i> Select Classes (Multi-selection)</label>
                                    <?php
                                        $classesMultiArray = [];
                                        foreach ($classes as $classaKey => $classa) {
                                            $classesMultiArray[$classa->classesID] = $classa->classes;
                                        }
                                        echo form_dropdown("classesID_multi[]", $classesMultiArray, set_value("classesID_multi[]"), "id='classesID_multi' class='form-control select2' multiple='multiple' placeholder='Select Classes'");
                                     ?>
                                </div>

                                <div class="form-group col-sm-6" id="villegesMultiDiv">
                                    <label><i class="fa fa-map-marker"></i> Villages</label>
                                    <?php
                                        $villegesMultiArray = array(
                                            "0" => $this->lang->line("balancefeesreport_please_select"),
                                        );
                                        foreach ($villeges as $classaKey => $v) {
                                            $villegesMultiArray[$v['villageID']] = $v['villageName'];
                                        }
                                        echo form_dropdown("villageID_multi", $villegesMultiArray, set_value("villageID_multi"), "id='villageID_multi' class='form-control select2'");
                                     ?>
                                </div>

                                <div class="form-group col-sm-6" id="feetypeMultiDiv">
                                    <label><i class="fa fa-money"></i> Fee Types</label>
                                    <?php
                                        $feetypeMultiArray = [];
                                        if (customCompute($feetypes)) {
                                            foreach ($feetypes as $feetype) {
                                                $feetypeMultiArray[$feetype->feetypesID] = $feetype->feetypes;
                                            }
                                        }

                                        echo form_dropdown(
                                            "feetypeID_multi[]",
                                            $feetypeMultiArray,
                                            set_value("feetypeID_multi[]"),
                                            "id='feetypeID_multi' class='form-control select2' multiple='multiple' placeholder='Select Fee Types'"
                                        );
                                    ?>
                                </div>

                                <div class="col-sm-6">
                                    <label>&nbsp;</label>
                                    <button id="getClassSummaryBtn" type="button" class="btn btn-info btn-block" style="margin-top:8px;">
                                        <i class="fa fa-pie-chart"></i> Generate Summary
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- /.tab-pane -->
            </div><!-- /.tab-content -->
        </div><!-- /.nav-tabs-custom -->
    </div><!-- /.box-body -->
</div><!-- /.box -->

<div id="load_balancefeesreport"></div>

<script type="text/javascript">

    function printDiv(divID) {
        var oldPage = document.body.innerHTML;
        $('#headerImage').remove();
        $('.footerAll').remove();
        var divElements = document.getElementById(divID).innerHTML;
        var footer = "<center><img src='<?=base_url('uploads/images/'.$siteinfos->photo)?>' style='width:30px;' /></center>";
        var copyright = "<center><?=$siteinfos->footer?> | <?=$this->lang->line('balancefeesreport_hotline')?> : <?=$siteinfos->phone?></center>";
        document.body.innerHTML =
          "<html><head><title></title></head><body>" +
          "<center><img src='<?=base_url('uploads/images/'.$siteinfos->photo)?>' style='width:50px;' /></center>"
          + divElements + footer + copyright + "</body>";

        window.print();
        document.body.innerHTML = oldPage;
        window.location.reload();
    }

    $('.select2').select2();
    $(function(){
        $('#sectionDiv').hide('slow');
        $('#studentDiv').hide('slow');
    });

    $(document).on('change', "#classesID", function() {
        $('#load_balancefeesreport').html("");
        var classesID = $(this).val();
        
        $('#sectionID').val(0);
        $('#studentID').html("<option value='0'>" + "<?=$this->lang->line("balancefeesreport_please_select")?>" +"</option>");
        $('#studentID').val(0);
        
        if(classesID == '0'){
            $("#sectionDiv").hide('slow');
            $("#studentDiv").hide('slow');
        } else {
            $("#sectionDiv").show('slow');
            $("#studentDiv").show('slow');
        }

        if(classesID !=0) {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('balancefeesreport/getSection')?>",
                data: {"classesID" : classesID},
                dataType: "html",
                success: function(data) {
                   $('#sectionID').html(data);
                }
            });
        }
    });

    $(document).on('change', "#sectionID", function() {
        $('#load_balancefeesreport').html("");
        var sectionID = $(this).val();
        
        $('#studentID').html("<option value='0'>" + "<?=$this->lang->line("balancefeesreport_please_select")?>" +"</option>");
        $('#studentID').val(0);

        var classesID = $('#classesID').val();
        if(sectionID != 0 && classesID != 0) {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('balancefeesreport/getStudent')?>",
                data: {"classesID":classesID, "sectionID" : sectionID},
                dataType: "html",
                success: function(data) {
                   $('#studentID').html(data);
                }
            });
        }
    });

    $(document).on('click','#get_duefeesreport', function() {
        $('#load_balancefeesreport').html("");
        var classesID = $('#classesID').val();
        var sectionID = $('#sectionID').val();
        var studentID = $('#studentID').val();
        var feetypeID = $('#feetypeID').val();
        var villageID = $('#villageID').val();
        var sectionName = $('#sectionID option:selected').text(); // 👈 get selected section name
        if (sectionName === '' || sectionName.toLowerCase() === 'please select') {
            sectionName = '';
        }
        var error = 0;

        var field = {
            "classesID" : classesID,
            "sectionID" : sectionID,
            "studentID" : studentID,
            "feetypeID" : feetypeID,
            "villageID" : villageID,
            "sectionName" :sectionName
        };

        if(error == 0 ) {
            makingPostDataPreviousofAjaxCall(field);
        }
    });

    function makingPostDataPreviousofAjaxCall(field) {
        passData = field;
        ajaxCall(passData);
    }

    function ajaxCall(passData) {
        $.ajax({
            type: 'POST',
            url: "<?=base_url('balancefeesreport/getBalanceFeesReport')?>",
            data: passData,
            dataType: "html",
            success: function(data) {
                var response = JSON.parse(data);
                renderLoder(response, passData);
                // After initial load, ensure grand totals are correct
                updateGrandTotals();
            }
        });
    }

    function renderLoder(response, passData) {
        if(response.status) {
            $('#load_balancefeesreport').html(response.render);
            for (var key in passData) {
                if (passData.hasOwnProperty(key)) {
                    $('#'+key).parent().removeClass('has-error');
                }
            }
            // Recalculate totals in case of any dynamic changes
            updateGrandTotals();
        } else {
            for (var key in passData) {
                if (passData.hasOwnProperty(key)) {
                    $('#'+key).parent().removeClass('has-error');
                }
            }

            for (var key in response) {
                if (response.hasOwnProperty(key)) {
                    $('#'+key).parent().addClass('has-error');
                }
            }
        }
    }

    function parseIndianCurrency(value) {
        if (!value) return 0;
        // Remove commas and spaces
        value = value.toString().replace(/,/g, '').trim();
        var num = parseFloat(value);
        return isNaN(num) ? 0 : num;
    }

    function formatIndianCurrencyJS(number) {
        number = Math.round(number * 100) / 100;
        var parts = number.toFixed(2).split('.');
        var integer = parts[0];
        var decimal = parts[1];

        var last3 = integer.slice(-3);
        var rest = integer.slice(0, -3);
        if (rest.length > 0) {
            rest = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',');
            integer = rest + ',' + last3;
        }
        return integer + '.' + decimal;
    }

    function updateGrandTotals() {
        var $table = $('#myTable');
        if ($table.length === 0) return;

        var $rows = $table.find('tbody tr').not('.grand-total-row');
        if ($rows.length === 0) return;

        var totalAmount = 0,
            totalDiscount = 0,
            totalPaid = 0,
            totalBalance = 0;

        $rows.each(function() {
            var $cells = $(this).find('td');
            var len = $cells.length;
            if (len < 5) return;

            var amount   = parseIndianCurrency($cells.eq(len - 5).text());
            var discount = parseIndianCurrency($cells.eq(len - 4).text());
            var paid     = parseIndianCurrency($cells.eq(len - 3).text());
            var balance  = parseIndianCurrency($cells.eq(len - 2).text());

            totalAmount   += amount;
            totalDiscount += discount;
            totalPaid     += paid;
            totalBalance  += balance;
        });

        var $grand = $table.find('tbody tr.grand-total-row');
        if ($grand.length === 0) return;

        var $gCells = $grand.find('td');
        var gLen = $gCells.length;
        if (gLen < 5) return;

        $gCells.eq(gLen - 5).text(formatIndianCurrencyJS(totalAmount));
        $gCells.eq(gLen - 4).text(formatIndianCurrencyJS(totalDiscount));
        $gCells.eq(gLen - 3).text(formatIndianCurrencyJS(totalPaid));
        $gCells.eq(gLen - 2).text(formatIndianCurrencyJS(totalBalance));
    }

    // Class-wise Summary Report (Tab 2)
    $('#getClassSummaryBtn').click(function() {
        var classesID_multi = $('#classesID_multi').val();
        var feetypeID_multi = $('#feetypeID_multi').val();
        var villageID_multi = $('#villageID_multi').val();

        var field = {
            "classesID_multi" : classesID_multi,
            "feetypeID_multi" : feetypeID_multi,
            "villageID_multi" : villageID_multi
        };

        $.ajax({
            type: 'POST',
            url: "<?=base_url('balancefeesreport/getClassWiseSummaryReport')?>",
            data: field,
            dataType: "html",
            success: function(data) {
                var response = JSON.parse(data);
                if(response.status) {
                    $('#load_balancefeesreport').html(response.render);
                }
            }
        });
    });

    // Lazy load: Load more balance fees rows
    $(document).on('click', '#loadMoreBalanceFees', function() {
        var $btn = $(this);
        var offset   = parseInt($btn.data('offset')) || 0;
        var perPage  = parseInt($btn.data('perpage')) || 25;
        var total    = parseInt($btn.data('total')) || 0;

        var classesID = $('#classesID').val();
        var sectionID = $('#sectionID').val();
        var studentID = $('#studentID').val();
        var feetypeID = $('#feetypeID').val();
        var villageID = $('#villageID').val();
        var sectionName = $('#sectionID option:selected').text();
        if (sectionName === '' || sectionName.toLowerCase() === 'please select') {
            sectionName = '';
        }

        if (offset >= total) {
            $btn.hide();
            return;
        }

        $btn.prop('disabled', true).text('Loading...');

        $.ajax({
            type: 'POST',
            url: "<?=base_url('balancefeesreport/getBalanceFeesReportLazy')?>",
            data: {
                classesID: classesID,
                sectionID: sectionID,
                studentID: studentID,
                feetypeID: feetypeID,
                villageID: villageID,
                sectionName: sectionName,
                offset: offset
            },
            dataType: 'html',
            success: function(data) {
                var response = {};
                try {
                    response = JSON.parse(data);
                } catch (e) {
                    response = { status: false };
                }

                if(response.status) {
                    var $tbody = $('#myTable').find('tbody');
                    var $grand = $tbody.find('tr.grand-total-row');
                    if ($grand.length) {
                        $(response.rows).insertBefore($grand);
                    } else {
                        $tbody.append(response.rows);
                    }

                    var nextOffset = parseInt(response.nextOffset) || offset + perPage;
                    $btn.data('offset', nextOffset);

                    if (!response.hasMore || nextOffset >= total) {
                        $btn.hide();
                    }

                    updateGrandTotals();
                }

                $btn.prop('disabled', false).text('Load More');
            },
            error: function() {
                $btn.prop('disabled', false).text('Load More');
            }
        });
    });
</script>


