<style>
    .admission-modal .modal-dialog.modal-lg {
        width: 1100px;
        max-width: 95%;
    }
    .admission-modal .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .admission-modal .modal-header {
        background: linear-gradient(135deg, #1a73e8 0%, #1557b0 100%);
        color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding: 20px 25px;
    }
    .admission-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.5px;
        font-size: 1.2rem;
    }
    .admission-modal .modal-header .close {
        color: white;
        opacity: 0.8;
        text-shadow: none;
    }
    .admission-modal .modal-header .close:hover {
        opacity: 1;
    }
    .admission-modal .modal-body {
        padding: 25px 30px;
        background-color: #ffffff;
    }
    .admission-modal .form-group {
        margin-bottom: 15px;
    }
    .admission-modal .form-control {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        padding: 8px 12px;
        height: 38px;
        transition: all 0.2s ease;
        background-color: #fafafa;
    }
    .admission-modal .form-control:focus {
        border-color: #1a73e8;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.15);
    }
    .admission-modal textarea.form-control {
        height: auto;
    }
    .admission-modal label {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        font-size: 13px;
        display: block;
    }
    .admission-modal .modal-footer {
        padding: 15px 30px;
        border-top: 1px solid #eee;
        background-color: #f9f9f9;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .section-title {
        font-size: 11px;
        text-transform: uppercase;
        color: #1a73e8;
        letter-spacing: 1.2px;
        margin-bottom: 15px;
        margin-top: 25px;
        padding-bottom: 5px;
        border-bottom: 2px solid #eef3f9;
        font-weight: 800;
    }
    .section-title:first-child {
        margin-top: 0;
    }
    .input-group-addon {
        background-color: #f1f3f4;
        border-color: #e0e0e0;
        color: #666;
    }
    .select2-container--bootstrap .select2-selection--single {
        height: 38px !important;
        border-color: #e0e0e0 !important;
        border-radius: 6px !important;
        background-color: #fafafa !important;
    }
    .select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        color: #333 !important;
    }
</style>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-user-plus"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_admissionenquiry')?></li>
        </ol>
    </div><!-- /.box-header -->

    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="page-header">
                    <?php //if(permissionChecker('admissionenquiry_add')) { ?>
                        <button class="btn btn-success" data-toggle="modal" data-target="#admissionModal" onclick="resetForm()">
                            <i class="fa fa-plus"></i> <?=$this->lang->line('add_title')?>
                        </button>
                    <?php //} ?>
                </h5>

                <div id="hide-table">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th><?=$this->lang->line('slno')?></th>
                                <th><?=$this->lang->line('admissionenquiry_name')?></th>
                                <th><?=$this->lang->line('admissionenquiry_phone')?></th>
                                <th><?=$this->lang->line('admissionenquiry_date')?></th>
                                <th><?=$this->lang->line('admissionenquiry_assigned')?></th>
                                <th><?=$this->lang->line('admissionenquiry_source')?></th>
                                <th><?=$this->lang->line('admissionenquiry_action')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($admission_enquiries)) {$i = 1; foreach($admission_enquiries as $enquiry) { ?>
                                <tr>
                                    <td data-title="<?=$this->lang->line('slno')?>"><?=$i?></td>
                                    <td data-title="<?=$this->lang->line('admissionenquiry_name')?>"><?=$enquiry->name?></td>
                                    <td data-title="<?=$this->lang->line('admissionenquiry_phone')?>"><?=$enquiry->phone?></td>
                                    <td data-title="<?=$this->lang->line('admissionenquiry_date')?>"><?=($enquiry->date != '0000-00-00') ? date("d-m-Y", strtotime($enquiry->date)) : ''?></td>
                                    <td data-title="<?=$this->lang->line('admissionenquiry_assigned')?>">
                                        <?=isset($all_users[$enquiry->assigned_usertypeID][$enquiry->assigned_userID]) ? $all_users[$enquiry->assigned_usertypeID][$enquiry->assigned_userID] : ''?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('admissionenquiry_source')?>"><?=$enquiry->source?></td>
                                    <td data-title="<?=$this->lang->line('admissionenquiry_action')?>">
                                        <a href="<?=base_url('admissionenquiry/print_preview/'.$enquiry->enquiryID)?>" class="btn btn-info btn-xs" target="_blank" data-toggle="tooltip" title="Print"><i class="fa fa-print"></i></a>
                                        <?php //if(permissionChecker('admissionenquiry_edit')) { ?>
                                            <button class="btn btn-warning btn-xs" onclick="editEnquiry(<?=$enquiry->enquiryID?>)"><i class="fa fa-edit"></i></button>
                                        <?php //} ?>
                                        <?php //if(permissionChecker('admissionenquiry_delete')) { ?>
                                            <?=btn_delete('admissionenquiry/delete/'.$enquiry->enquiryID, $this->lang->line('delete'))?>
                                        <?php //} ?>
                                    </td>
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade admission-modal" id="admissionModal" role="dialog" aria-labelledby="admissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="admissionModalLabel"><i class="fa fa-user-plus"></i> <?=$this->lang->line('panel_title')?></h4>
            </div>
            <form id="admissionForm" class="form-horizontal" role="form" method="post">
                <div class="modal-body">
                    <input type="hidden" name="enquiryID" id="enquiryID">
                    
                    <div class="section-title">Enquirer Information</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group name_div">
                                <label for="name"><?=$this->lang->line('admissionenquiry_name')?> <span class="text-red">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Full Name">
                                <span class="text-danger" id="error_name"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group phone_div">
                                <label for="phone"><?=$this->lang->line('admissionenquiry_phone')?> <span class="text-red">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number">
                                <span class="text-danger" id="error_phone"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group email_div">
                                <label for="email"><?=$this->lang->line('admissionenquiry_email')?></label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email Address">
                                <span class="text-danger" id="error_email"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group address_div">
                                <label for="address"><?=$this->lang->line('admissionenquiry_address')?></label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter Complete Address"></textarea>
                                <span class="text-danger" id="error_address"></span>
                            </div>
                        </div>
                    </div>

                    <div class="section-title">Follow-up Details</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group date_div">
                                <label for="date"><?=$this->lang->line('admissionenquiry_date')?></label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" class="form-control datepicker" id="date" name="date">
                                </div>
                                <span class="text-danger" id="error_date"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group next_follow_up_date_div">
                                <label for="next_follow_up_date"><?=$this->lang->line('admissionenquiry_next_follow_up_date')?></label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar-check-o"></i></div>
                                    <input type="text" class="form-control datepicker" id="next_follow_up_date" name="next_follow_up_date">
                                </div>
                                <span class="text-danger" id="error_next_follow_up_date"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group source_div">
                                <label for="source"><?=$this->lang->line('admissionenquiry_source')?> <span class="text-red">*</span></label>
                                <select class="form-control select2" id="source" name="source">
                                    <option value=""><?=$this->lang->line('admissionenquiry_select_user')?></option>
                                    <option value="Online">Online</option>
                                    <option value="Advertisement">Advertisement</option>
                                    <option value="Reference">Reference</option>
                                    <option value="Walk-in">Walk-in</option>
                                </select>
                                <span class="text-danger" id="error_source"></span>
                            </div>
                        </div>
                    </div>

                    <div class="section-title">Assignment & Inquiry</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group assigned_usertypeID_div">
                                <label for="assigned_usertypeID">Assigned Usertype</label>
                                <select class="form-control select2" id="assigned_usertypeID" name="assigned_usertypeID">
                                    <option value="0"><?=$this->lang->line('admissionenquiry_select_usertype')?></option>
                                    <?php if(customCompute($usertypes)) { foreach($usertypes as $u) { ?>
                                        <option value="<?=$u->usertypeID?>"><?=$u->usertype?></option>
                                    <?php }} ?>
                                </select>
                                <span class="text-danger" id="error_assigned_usertypeID"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group assigned_userID_div">
                                <label for="assigned_userID">Assigned Person</label>
                                <select class="form-control select2" id="assigned_userID" name="assigned_userID">
                                    <option value="0"><?=$this->lang->line('admissionenquiry_select_user')?></option>
                                </select>
                                <span class="text-danger" id="error_assigned_userID"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group classesID_div">
                                <label for="classesID"><?=$this->lang->line('admissionenquiry_class')?></label>
                                <select class="form-control select2" id="classesID" name="classesID">
                                    <option value="0"><?=$this->lang->line('admissionenquiry_select_class')?></option>
                                    <?php if(customCompute($classes)) { foreach($classes as $c) { ?>
                                        <option value="<?=$c->classesID?>"><?=$c->classes?></option>
                                    <?php }} ?>
                                </select>
                                <span class="text-danger" id="error_classesID"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group reference_div">
                                <label for="reference"><?=$this->lang->line('admissionenquiry_reference')?></label>
                                <input type="text" class="form-control" id="reference" name="reference" placeholder="Enter Reference Name">
                                <span class="text-danger" id="error_reference"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group num_child_div">
                                <label for="num_child"><?=$this->lang->line('admissionenquiry_num_child')?></label>
                                <input type="number" class="form-control" id="num_child" name="num_child" min="0">
                                <span class="text-danger" id="error_num_child"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group note_div">
                                <label for="note"><?=$this->lang->line('admissionenquiry_note')?></label>
                                <textarea class="form-control" id="note" name="note" rows="1" placeholder="Additional Notes..."></textarea>
                                <span class="text-danger" id="error_note"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group description_div">
                                <label for="description"><?=$this->lang->line('admissionenquiry_description')?></label>
                                <textarea class="form-control" id="description" name="description" rows="2" placeholder="Inquiry Details..."></textarea>
                                <span class="text-danger" id="error_description"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group fee_particulars_div">
                                <label for="fee_particulars"><?=$this->lang->line('admissionenquiry_fee_particulars')?></label>
                                <textarea class="form-control" id="fee_particulars" name="fee_particulars" rows="2" placeholder="Fee breakdown..."></textarea>
                                <span class="text-danger" id="error_fee_particulars"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->lang->line('admissionenquiry_cancel')?></button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> <?=$this->lang->line('admissionenquiry_submit')?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('.select2').select2({ width: '100%', dropdownParent: $('#admissionModal') });
    $('.datepicker').datepicker({ autoclose: true, format: 'dd-mm-yyyy' });

    $('#assigned_usertypeID').change(function() {
        var id = $(this).val();
        if(id == 0 || id == "") {
            $('#assigned_userID').html('<option value="0">Select User</option>').trigger('change');
            return;
        }
        $.ajax({
            type: 'POST',
            url: "<?=base_url('admissionenquiry/usercall')?>",
            data: {id: id},
            dataType: "html",
            success: function(data) {
               $('#assigned_userID').html(data).trigger('change');
            }
        });
    });

    $('#admissionForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "<?=base_url('admissionenquiry/index')?>",
            data: $(this).serialize(),
            dataType: "json",
            success: function(data) {
                if(data.status) {
                    window.location.reload();
                } else {
                    if(data.error) {
                        alert(data.error + "\n\nQuery: " + data.query);
                    }
                    $('.form-group').removeClass('has-error');
                    $('.text-danger').html('');
                    $.each(data, function(key, val) {
                        if(key != 'status' && key != 'error' && key != 'query') {
                            $('.' + key + '_div').addClass('has-error');
                            $('#error_' + key).html(val);
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + error);
                alert("An error occurred while saving. Status: " + status + ", Error: " + error);
            }
        });
    });
});

function resetForm() {
    $('#admissionForm')[0].reset();
    $('#enquiryID').val('');
    $('.form-group').removeClass('has-error');
    $('.text-danger').html('');
    $('.select2').val('0').trigger('change');
    $('#source').val('').trigger('change');
}

function editEnquiry(id) {
    $.ajax({
        type: 'POST',
        url: "<?=base_url('admissionenquiry/edit')?>",
        data: {id: id},
        dataType: "json",
        success: function(data) {
            if(data.status) {
                resetForm();
                var e = data.enquiry;
                $('#enquiryID').val(e.enquiryID);
                $('#name').val(e.name);
                $('#phone').val(e.phone);
                $('#email').val(e.email);
                $('#address').val(e.address);
                $('#description').val(e.description);
                $('#note').val(e.note);
                $('#date').val(e.date);
                $('#next_follow_up_date').val(e.next_follow_up_date);
                $('#source').val(e.source).trigger('change');
                $('#assigned_usertypeID').val(e.assigned_usertypeID).trigger('change');
                $('#reference').val(e.reference);
                $('#num_child').val(e.num_child);
                $('#fee_particulars').val(e.fee_particulars);
                $('#classesID').val(e.classesID).trigger('change');
                
                // Set timeout to wait for usercall AJAX
                setTimeout(function() {
                    $('#assigned_userID').val(e.assigned_userID).trigger('change');
                }, 500);

                $('#admissionModal').modal('show');
            }
        }
    });
}
</script>
