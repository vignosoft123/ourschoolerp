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
                <li><a href="#tab_3" data-toggle="tab">Fee Due slip</a></li>
            </ul>
            
            <div class="tab-content">
                <!-- TAB 1: Balance Fee Report -->
                <div class="tab-pane active" id="tab_1">
                    <div class="rpt-filter-card">
                        <div class="rpt-filter-title"><i class="fa fa-user-circle"></i> Student-wise Balance Fee Report</div>
                        <form id="balanceFeeForm">
                            <div class="row">
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

                            </div>
                        </form>
                        <div class="rpt-filter-actions">
                            <button id="get_duefeesreport" type="button" class="btn btn-success rpt-filter-btn"><i class="fa fa-search"></i> Generate Report</button>
                        </div>
                    </div>
                </div><!-- /.tab-pane -->
                
                <!-- TAB 2: Class-wise Summary Report -->
                <div class="tab-pane" id="tab_2">
                    <div class="rpt-filter-card">
                        <div class="rpt-filter-title"><i class="fa fa-bar-chart"></i> Class-wise Summary Report</div>
                        <form id="classWiseForm">
                            <div class="row">
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

                            </div>
                        </form>
                        <div class="rpt-filter-actions">
                            <button id="getClassSummaryBtn" type="button" class="btn btn-info rpt-filter-btn"><i class="fa fa-pie-chart"></i> Generate Summary</button>
                        </div>
                    </div>
                </div><!-- /.tab-pane -->
                <!-- TAB 3: Fee Due slip -->
                <div class="tab-pane" id="tab_3">
                    <div class="rpt-filter-card">
                        <div class="rpt-filter-title"><i class="fa fa-envelope"></i> Fee Due Slip Report</div>
                        <form id="feeDueSlipForm">
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label><i class="fa fa-graduation-cap"></i> <?=$this->lang->line("balancefeesreport_class")?></label>
                                    <?php
                                        echo form_dropdown("classesID_slip", $classesArray, set_value("classesID_slip"), "id='classesID_slip' class='form-control select2'");
                                     ?>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><i class="fa fa-users"></i> <?=$this->lang->line("balancefeesreport_section")?></label>
                                    <?php
                                        echo form_dropdown("sectionID_slip", $sectionArray, set_value("sectionID_slip"), "id='sectionID_slip' class='form-control select2'");
                                     ?>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><i class="fa fa-user"></i> <?=$this->lang->line("balancefeesreport_student")?></label>
                                    <?php
                                        echo form_dropdown("studentID_slip[]", $studentArray, set_value("studentID_slip[]"), "id='studentID_slip' class='form-control select2' multiple='multiple'");
                                     ?>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><i class="fa fa-money"></i> Fee Types</label>
                                    <?php
                                        echo form_dropdown("feetypeID_slip[]", $feetypeArray, set_value("feetypeID_slip[]"), "id='feetypeID_slip' class='form-control select2' multiple='multiple'");
                                    ?>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><i class="fa fa-calendar"></i> Slip Date</label>
                                    <input type="text" id="slip_date" class="form-control datepicker" value="<?=date('d-m-Y')?>">
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><i class="fa fa-calendar"></i> Due Date</label>
                                    <input type="text" id="due_date" class="form-control datepicker" value="<?=date('d-m-Y', strtotime('+7 days'))?>">
                                </div>

                            </div>
                        </form>
                        <div class="rpt-filter-actions">
                            <button id="getFeeDueSlipBtn" type="button" class="btn btn-success rpt-filter-btn"><i class="fa fa-search"></i> Generate Slips</button>
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
            },
            complete: function() {
                $('#loading').hide();
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
                        $('#loadAllBalanceFees').hide();
                    }

                    updateGrandTotals();
                    if (typeof applyStickyColumns === 'function') applyStickyColumns();
                }

                $btn.prop('disabled', false).text('Load More');
            },
            error: function() {
                $btn.prop('disabled', false).text('Load More');
            }
        });
    });

    // Load All: Load all remaining balance fees rows at once
    $(document).on('click', '#loadAllBalanceFees', function() {
        var $btn = $(this);
        var $loadMoreBtn = $('#loadMoreBalanceFees');
        var offset   = parseInt($loadMoreBtn.data('offset')) || 0;
        var total    = parseInt($loadMoreBtn.data('total')) || 0;

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
            $loadMoreBtn.hide();
            return;
        }

        $btn.prop('disabled', true).text('Loading All Records...');
        $loadMoreBtn.prop('disabled', true);

        // Load all remaining records recursively
        function loadAllRecords(currentOffset) {
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
                    offset: currentOffset
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

                        var nextOffset = parseInt(response.nextOffset) || currentOffset + 25;

                        // Continue loading if there's more data
                        if (response.hasMore && nextOffset < total) {
                            loadAllRecords(nextOffset);
                        } else {
                            // All records loaded
                            $btn.hide();
                            $loadMoreBtn.hide();
                            updateGrandTotals();
                            if (typeof applyStickyColumns === 'function') applyStickyColumns();
                        }
                    } else {
                        $btn.prop('disabled', false).text('Load All');
                        $loadMoreBtn.prop('disabled', false);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Load All');
                    $loadMoreBtn.prop('disabled', false);
                    alert('Error loading records. Please try again.');
                }
            });
        }

        loadAllRecords(offset);
    });
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy',
        todayHighlight: true
    });

    $(document).on('change', "#classesID_slip", function() {
        var classesID = $(this).val();
        $('#sectionID_slip').html("<option value='0'>" + "<?=$this->lang->line("balancefeesreport_please_select")?>" +"</option>");
        $('#studentID_slip').html("<option value='0'>" + "<?=$this->lang->line("balancefeesreport_please_select")?>" +"</option>");
        
        if(classesID != 0) {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('balancefeesreport/getSection')?>",
                data: {"classesID" : classesID},
                dataType: "html",
                success: function(data) {
                   $('#sectionID_slip').html(data);
                }
            });
        }
    });

    $(document).on('change', "#sectionID_slip", function() {
        var sectionID = $(this).val();
        var classesID = $('#classesID_slip').val();
        $('#studentID_slip').html("<option value='0'>" + "<?=$this->lang->line("balancefeesreport_please_select")?>" +"</option>");

        if(sectionID != 0 && classesID != 0) {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('balancefeesreport/getStudent')?>",
                data: {"classesID":classesID, "sectionID" : sectionID},
                dataType: "html",
                success: function(data) {
                   $('#studentID_slip').html(data);
                }
            });
        }
    });

    $('#getFeeDueSlipBtn').click(function() {
        $('#load_balancefeesreport').html("");
        var classesID = $('#classesID_slip').val();
        var sectionID = $('#sectionID_slip').val();
        var studentID = $('#studentID_slip').val(); // array since multiple
        var feetypeID = $('#feetypeID_slip').val();
        var slip_date = $('#slip_date').val();
        var due_date  = $('#due_date').val();

        if (classesID == 0) {
            alert("Please select a class.");
            return;
        }

        var field = {
            "classesID" : classesID,
            "sectionID" : sectionID,
            "studentID" : studentID,
            "feetypeID" : feetypeID,
            "slip_date" : slip_date,
            "due_date"  : due_date
        };

        $.ajax({
            type: 'POST',
            url: "<?=base_url('balancefeesreport/getFeeDueSlipReport')?>",
            data: field,
            dataType: "html",
            success: function(data) {
                var response = JSON.parse(data);
                if(response.status) {
                    $('#load_balancefeesreport').html(response.render);
                } else {
                    alert("Failed to generate slips.");
                }
            }
        });
    });
</script>


