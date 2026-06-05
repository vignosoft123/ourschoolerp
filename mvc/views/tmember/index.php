
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-member"></i> <?=$this->lang->line('panel_title')?></h3>

        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('panel_title')?></li>
        </ol>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <?php if($this->session->userdata('usertypeID') != 3) { ?>
                    <h5 class="page-header">
                        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 pull-right drop-marg">
                            <?php
                                $array = array("0" => $this->lang->line("tmember_select_class"));
                                foreach ($classes as $classa) {
                                    $array[$classa->classesID] = $classa->classes;
                                }
                                echo form_dropdown("classesID", $array, set_value("classesID", $set), "id='classesID' class='form-control select2'");
                            ?>
                        </div>
                    </h5>
                <?php } ?>

                <?php if(permissionChecker('tmember_add') && (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1))) { ?>
                <div id="bulk-action-bar" style="display:none; margin-bottom:10px; clear:both; padding-top:8px;">
                    <button type="button" id="bulk-add-btn" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> Bulk Add Members &nbsp;<span class="badge" id="selected-count">0</span>
                    </button>
                </div>
                <?php } ?>

                <?php if(customCompute($students) > 0 ) { ?>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#all" aria-expanded="true"><?=$this->lang->line("tmember_all_students")?></a></li>
                            <?php if(customCompute($sections)) { foreach ($sections as $key => $section) {
                                echo '<li class=""><a data-toggle="tab" href="#tab'.$section->classesID.$section->sectionID .'" aria-expanded="false">'. $this->lang->line("tmember_section")." ".$section->section. " ( ". $section->category." )".'</a></li>';
                            } } ?>
                        </ul>
                        <div style="padding:10px 15px; border-bottom:1px solid #ddd; background:#f9f9f9; text-align:center;">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-default transport-filter active" data-filter=""><i class="fa fa-users"></i> All</button>
                                <button type="button" class="btn btn-success transport-filter" data-filter="1"><i class="fa fa-check-circle"></i> Members</button>
                                <button type="button" class="btn btn-warning transport-filter" data-filter="0"><i class="fa fa-plus-circle"></i> Not Added</button>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div id="all" class="tab-pane active">
                                <div id="hide-table">
                                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                                        <thead>
                                            <tr>
                                                <?php if(permissionChecker('tmember_add') && (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1))) { ?>
                                                <th style="width:35px;"><input type="checkbox" class="select-all-cb" title="Select All"></th>
                                                <?php } ?>
                                                <th class="col-sm-2"><?=$this->lang->line('slno')?></th>
                                                <th class="col-sm-2"><?=$this->lang->line('tmember_photo')?></th>
                                                <th class="col-sm-2"><?=$this->lang->line('tmember_name')?></th>
                                                <th class="col-sm-2"><?=$this->lang->line('tmember_roll')?></th>
                                                <th class="col-sm-2"><?=$this->lang->line('tmember_phone')?></th>
                                                <?php if(permissionChecker('tmember_add') || permissionChecker('tmember_edit') || permissionChecker('tmember_delete') || permissionChecker('tmember_view')) { ?>
                                                    <th class="col-sm-2"><?=$this->lang->line('action')?></th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(customCompute($students)) {$i = 1; foreach($students as $student) { ?>
                                                <tr data-transport="<?=$student->transport?>">
                                                    <?php if(permissionChecker('tmember_add') && (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1))) { ?>
                                                    <td>
                                                        <?php if($student->transport == 0) { ?>
                                                        <input type="checkbox" class="student-cb" data-student-id="<?=$student->studentID?>">
                                                        <?php } ?>
                                                    </td>
                                                    <?php } ?>
                                                    <td data-title="<?=$this->lang->line('slno')?>"><?php echo $i; ?></td>
                                                    <td data-title="<?=$this->lang->line('tmember_photo')?>"><?=profileimage($student->photo)?></td>
                                                    <td data-title="<?=$this->lang->line('tmember_name')?>"><?php echo $student->srname; ?></td>
                                                    <td data-title="<?=$this->lang->line('tmember_roll')?>"><?php echo $student->srroll; ?></td>
                                                    <td data-title="<?=$this->lang->line('tmember_phone')?>"><?php echo $student->phone; ?></td>
                                                    <?php if(permissionChecker('tmember_add') || permissionChecker('tmember_edit') || permissionChecker('tmember_delete') || permissionChecker('tmember_view')) { ?>
                                                    <td data-title="<?=$this->lang->line('action')?>">
                                                        <?php
                                                            if($student->transport == 0) {
                                                                if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                                                                    echo btn_add('tmember/add/'.$student->studentID."/".$set, $this->lang->line('tmember'));
                                                                }
                                                            } else {
                                                                echo btn_view('tmember/view/'.$student->studentID."/".$set, $this->lang->line('view')). " ";
                                                                if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                                                                    echo btn_edit('tmember/edit/'.$student->studentID."/".$set, $this->lang->line('edit')). " ";
                                                                    echo btn_delete('tmember/delete/'.$student->studentID."/".$set, $this->lang->line('delete'));
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php $i++; }} ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <?php if(customCompute($sections)) { foreach ($sections as $key => $section) { ?>
                                <div id="tab<?=$section->classesID.$section->sectionID?>" class="tab-pane">
                                    <div id="hide-table">
                                        <table class="section-tab-table table table-striped table-bordered table-hover dataTable no-footer" data-section-id="<?=$section->sectionID?>" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <?php if(permissionChecker('tmember_add') && (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1))) { ?>
                                                    <th style="width:35px;"><input type="checkbox" class="select-all-cb" title="Select All"></th>
                                                    <?php } ?>
                                                    <th><?=$this->lang->line('slno')?></th>
                                                    <th><?=$this->lang->line('tmember_photo')?></th>
                                                    <th><?=$this->lang->line('tmember_name')?></th>
                                                    <th><?=$this->lang->line('tmember_roll')?></th>
                                                    <th><?=$this->lang->line('tmember_phone')?></th>
                                                    <?php if(permissionChecker('tmember_add') || permissionChecker('tmember_edit') || permissionChecker('tmember_delete') || permissionChecker('tmember_view')) { ?>
                                                        <th><?=$this->lang->line('action')?></th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(isset($allsection[$section->sectionID])) { $i = 1; foreach($allsection[$section->sectionID] as $student) { if($section->sectionID === $student->srsectionID) { ?>
                                                    <tr data-transport="<?=$student->transport?>">
                                                        <?php if(permissionChecker('tmember_add') && (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1))) { ?>
                                                        <td>
                                                            <?php if($student->transport == 0) { ?>
                                                            <input type="checkbox" class="student-cb" data-student-id="<?=$student->studentID?>">
                                                            <?php } ?>
                                                        </td>
                                                        <?php } ?>
                                                        <td data-title="<?=$this->lang->line('slno')?>"><?php echo $i; ?></td>
                                                        <td data-title="<?=$this->lang->line('tmember_photo')?>"><?=profileimage($student->photo)?></td>
                                                        <td data-title="<?=$this->lang->line('tmember_name')?>"><?php echo $student->srname; ?></td>
                                                        <td data-title="<?=$this->lang->line('tmember_roll')?>"><?php echo $student->srroll; ?></td>
                                                        <td data-title="<?=$this->lang->line('tmember_phone')?>"><?php echo $student->phone; ?></td>
                                                        <?php if(permissionChecker('tmember_add') || permissionChecker('tmember_edit') || permissionChecker('tmember_delete') || permissionChecker('tmember_view')) { ?>
                                                        <td data-title="<?=$this->lang->line('action')?>">
                                                            <?php
                                                                if($student->transport == 0) {
                                                                    if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                                                                        echo btn_add('tmember/add/'.$student->studentID."/".$set, $this->lang->line('tmember'));
                                                                    }
                                                                } else {
                                                                    echo btn_view('tmember/view/'.$student->studentID."/".$set, $this->lang->line('view')). " ";
                                                                    if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                                                                        echo btn_edit('tmember/edit/'.$student->studentID."/".$set, $this->lang->line('edit')). " ";
                                                                        echo btn_delete('tmember/delete/'.$student->studentID."/".$set, $this->lang->line('delete'));
                                                                    }
                                                                }
                                                            ?>
                                                        </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php $i++; }}} ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php } } ?>
                        </div>
                    </div> <!-- nav-tabs-custom -->
                <?php } else { ?>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#all" aria-expanded="true"><?=$this->lang->line("tmember_all_students")?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="all" class="tab-pane active">
                                <div id="hide-table">
                                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                                        <thead>
                                            <tr>
                                                <th class="col-sm-2"><?=$this->lang->line('slno')?></th>
                                                <th class="col-sm-2"><?=$this->lang->line('tmember_photo')?></th>
                                                <th class="col-sm-2"><?=$this->lang->line('tmember_name')?></th>
                                                <th class="col-sm-2"><?=$this->lang->line('tmember_roll')?></th>
                                                <th class="col-sm-2"><?=$this->lang->line('tmember_phone')?></th>
                                                <?php if(permissionChecker('tmember_add') || permissionChecker('tmember_edit') || permissionChecker('tmember_delete') || permissionChecker('tmember_view')) { ?>
                                                    <th class="col-sm-2"><?=$this->lang->line('action')?></th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(customCompute($students)) {$i = 1; foreach($students as $student) { ?>
                                                <tr>
                                                    <td data-title="<?=$this->lang->line('slno')?>"><?php echo $i; ?></td>
                                                    <td data-title="<?=$this->lang->line('tmember_photo')?>">
                                                        <?php $array = array(
                                                                "src" => base_url('uploads/images/'.$student->photo),
                                                                'width' => '35px',
                                                                'height' => '35px',
                                                                'class' => 'img-rounded'
                                                            );
                                                            echo img($array);
                                                        ?>
                                                    </td>
                                                    <td data-title="<?=$this->lang->line('tmember_name')?>"><?php echo $student->name; ?></td>
                                                    <td data-title="<?=$this->lang->line('tmember_roll')?>"><?php echo $student->roll; ?></td>
                                                    <td data-title="<?=$this->lang->line('tmember_email')?>"><?php echo $student->email; ?></td>
                                                    <?php if(permissionChecker('tmember_add') || permissionChecker('tmember_edit') || permissionChecker('tmember_delete') || permissionChecker('tmember_view')) { ?>
                                                    <td data-title="<?=$this->lang->line('action')?>">
                                                        <?php
                                                            if($student->transport == 0) {
                                                                echo btn_add('tmember/add/'.$student->studentID."/".$set, $this->lang->line('tmember'));
                                                            } else {
                                                                echo btn_view('tmember/view/'.$student->studentID."/".$set, $this->lang->line('view')). " ";
                                                                echo btn_edit('tmember/edit/'.$student->studentID."/".$set, $this->lang->line('edit')). " ";
                                                                echo btn_delete('tmember/delete/'.$student->studentID."/".$set, $this->lang->line('delete'));
                                                            }
                                                        ?>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php $i++; }} ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- nav-tabs-custom -->
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Add Members Modal -->
<?php if(permissionChecker('tmember_add') && (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1))) { ?>
<div class="modal fade" id="bulkAddModal" tabindex="-1" role="dialog" aria-labelledby="bulkAddModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:#fff; border-radius:3px 3px 0 0;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:1;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="bulkAddModalLabel">
                    <i class="fa fa-plus-circle"></i> Bulk Add Transport Members
                    &nbsp;<span class="badge" id="modal-selected-count">0</span> students selected
                </h4>
            </div>
            <form id="bulk-add-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Route Name <span class="text-red">*</span></label>
                        <select name="transportID" id="bulk_transportID" class="form-control">
                            <option value="0"><?=$this->lang->line("classes_select_route_name")?></option>
                            <?php if(isset($transports)) { foreach ($transports as $transport) { ?>
                            <option value="<?=$transport->transportID?>"><?=$transport->route?></option>
                            <?php } } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pickup Point <span class="text-red">*</span></label>
                        <select name="pickup_id" id="bulk_pickup_id" class="form-control">
                            <option value="">Select</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Transport Fee <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="bulk_tbalance" name="tbalance" value="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="bulk-submit-btn">
                        <i class="fa fa-check"></i> Add Members
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>


<script type="text/javascript">
    $('.select2').select2();
    $('#classesID').change(function() {
        var classesID = $(this).val();
        if(classesID == 0) {
            $('#hide-table').hide();
        } else {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('tmember/student_list')?>",
                data: "id=" + classesID,
                dataType: "html",
                success: function(data) {
                    window.location.href = data;
                }
            });
        }
    });

    // --- Bulk selection ---
    var selectedStudents = {};

    function updateBulkButton() {
        var count = Object.keys(selectedStudents).length;
        if (count > 0) {
            $('#bulk-action-bar').show();
            $('#selected-count').text(count);
        } else {
            $('#bulk-action-bar').hide();
        }
    }

    $(document).on('change', '.student-cb', function() {
        var sid = $(this).data('student-id');
        if ($(this).is(':checked')) {
            selectedStudents[sid] = true;
        } else {
            delete selectedStudents[sid];
            $(this).closest('table').find('.select-all-cb').prop('checked', false);
        }
        updateBulkButton();
    });

    $(document).on('change', '.select-all-cb', function() {
        var table  = $(this).closest('table');
        var isChecked = $(this).is(':checked');
        table.find('.student-cb').each(function() {
            $(this).prop('checked', isChecked);
            var sid = $(this).data('student-id');
            if (isChecked) {
                selectedStudents[sid] = true;
            } else {
                delete selectedStudents[sid];
            }
        });
        updateBulkButton();
    });

    // Open modal
    $('#bulk-add-btn').on('click', function() {
        $('#modal-selected-count').text(Object.keys(selectedStudents).length);
        $('#bulkAddModal').modal('show');
    });

    // Reset modal when shown
    $('#bulkAddModal').on('show.bs.modal', function() {
        $('#modal-selected-count').text(Object.keys(selectedStudents).length);
        $('#bulk_transportID').val('0');
        $('#bulk_pickup_id').html('<option value="">Select</option>');
        $('#bulk_tbalance').val('0.00');
        $('#bulk-submit-btn').prop('disabled', false).html('<i class="fa fa-check"></i> Add Members');
    });

    // Pickup points for modal
    $('#bulk_transportID').on('change', function() {
        var transportID = $(this).val();
        $('#bulk_pickup_id').html('<option value="">Select</option>');
        $('#bulk_tbalance').val('0.00');
        if (transportID && transportID != 0) {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('tmember/pickup_points')?>",
                data: "id=" + transportID,
                dataType: "html",
                success: function(data) {
                    $('#bulk_pickup_id').html(data);
                }
            });
        }
    });

    // Transport fare for modal
    $('#bulk_pickup_id').on('change', function() {
        var pickup_id = $(this).val();
        if (pickup_id && pickup_id != '' && pickup_id != 0) {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('tmember/transport_fare')?>",
                data: "id=" + pickup_id,
                dataType: "html",
                success: function(data) {
                    $('#bulk_tbalance').val(data);
                }
            });
        } else {
            $('#bulk_tbalance').val('0.00');
        }
    });

    // Bulk add form submit
    $('#bulk-add-form').on('submit', function(e) {
        e.preventDefault();
        var studentIDsArray = Object.keys(selectedStudents);
        if (studentIDsArray.length === 0) {
            alert('No students selected.');
            return;
        }
        var transportID = $('#bulk_transportID').val();
        if (!transportID || transportID == 0) {
            alert('Please select a route.');
            return;
        }
        var tbalance = $('#bulk_tbalance').val();
        if (tbalance === '' || parseFloat(tbalance) < 0) {
            alert('Please enter a valid transport fee.');
            return;
        }

        var postData = {
            'transportID': transportID,
            'tbalance':    tbalance,
            'classesID':   '<?=$set?>',
            'studentIDs':  studentIDsArray
        };

        $('#bulk-submit-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            type: 'POST',
            url: "<?=base_url('tmember/bulk_add')?>",
            data: postData,
            dataType: 'json',
            success: function(response) {
                $('#bulk-submit-btn').prop('disabled', false).html('<i class="fa fa-check"></i> Add Members');
                if (response.status) {
                    $('#bulkAddModal').modal('hide');
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'An error occurred. Please try again.');
                }
            },
            error: function() {
                $('#bulk-submit-btn').prop('disabled', false).html('<i class="fa fa-check"></i> Add Members');
                alert('An error occurred. Please try again.');
            }
        });
    });

    // --- Transport status filter (AJAX) ---
    var activeTransportFilter = '';

    function applyTransportFilter(tableEl, sectionID) {
        var $table   = $(tableEl);
        var colCount = $table.find('thead tr th').length || 7;
        $table.find('tbody').html(
            '<tr><td colspan="' + colCount + '" class="text-center" style="padding:15px;">' +
            '<i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>'
        );
        $.ajax({
            type: 'POST',
            url: '<?=base_url('tmember/ajax_filter')?>',
            data: { classesID: '<?=$set?>', sectionID: sectionID || 0, filter: activeTransportFilter },
            dataType: 'json',
            success: function(response) {
                var emptyMsg = '<tr><td colspan="' + colCount + '" class="text-center">No students found.</td></tr>';
                var newHtml  = (response.status && response.html) ? response.html : emptyMsg;
                if ($.fn.dataTable && $.fn.dataTable.isDataTable(tableEl)) {
                    var dt = $(tableEl).DataTable();
                    dt.clear();
                    if (response.status && response.html) {
                        var $rows = $('<table><tbody>' + response.html + '</tbody></table>').find('tr');
                        $rows.each(function() { dt.row.add(this); });
                    }
                    dt.draw();
                } else {
                    $table.find('tbody').html(newHtml);
                }
                selectedStudents = {};
                updateBulkButton();
            },
            error: function() {
                $table.find('tbody').html(
                    '<tr><td colspan="' + colCount + '" class="text-center text-danger">Error loading data.</td></tr>'
                );
            }
        });
    }

    $(document).on('click', '.transport-filter', function() {
        $('.transport-filter').removeClass('active');
        $(this).addClass('active');
        var filterVal = $(this).data('filter');
        activeTransportFilter = (filterVal === undefined || filterVal === '') ? '' : String(filterVal);
        applyTransportFilter('#example1', 0);
        $('.section-tab-table').each(function() {
            applyTransportFilter(this, $(this).data('section-id'));
        });
    });

    // Fix column widths + re-apply filter when switching to a section tab
    $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
        var $pane  = $($(this).attr('href'));
        var $table = $pane.find('.section-tab-table');
        if ($table.length) {
            // Force the table and its DataTables wrapper to full width
            $table.css('width', '100%');
            $table.closest('.dataTables_wrapper').css('width', '100%');
            if ($.fn.dataTable && $.fn.dataTable.isDataTable($table[0])) {
                $table.DataTable().columns.adjust().draw(false);
            }
        }
        if (activeTransportFilter === '' || !$table.length) return;
        applyTransportFilter($table[0], $table.data('section-id'));
    });
</script>
