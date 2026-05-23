<style>
/* ── Admission Enquiry Modal ── */
.ae-modal .modal-dialog { width: 900px; max-width: 96%; }
.ae-modal .modal-content {
    border-radius: 14px;
    border: none;
    box-shadow: 0 16px 48px rgba(0,0,0,0.22);
    overflow: hidden;
}
.ae-modal .modal-header {
    background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
    color: #fff;
    padding: 16px 22px;
    border-radius: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ae-modal .modal-title {
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.4px;
    color: #fff;
}
.ae-modal .modal-title i { margin-right: 8px; opacity: 0.9; }
.ae-modal .modal-header .close {
    color: #fff;
    opacity: 0.75;
    text-shadow: none;
    font-size: 22px;
    margin-top: -2px;
}
.ae-modal .modal-header .close:hover { opacity: 1; }
.ae-modal .modal-body {
    padding: 0;
    background: #f4f6fb;
    max-height: 72vh;
    overflow-y: auto;
}
/* Section panels */
.ae-section {
    margin: 14px 16px 0;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    overflow: hidden;
}
.ae-section:last-child { margin-bottom: 14px; }
.ae-section-head {
    background: linear-gradient(90deg, #00838f 0%, #006064 100%);
    color: #fff;
    padding: 8px 16px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ae-section-head i { font-size: 13px; opacity: 0.85; }
.ae-section-body { padding: 14px 16px 6px; }
/* Form controls */
.ae-modal .form-group { margin-bottom: 12px; }
.ae-modal label {
    font-size: 12px;
    font-weight: 700;
    color: #444;
    margin-bottom: 4px;
    display: block;
}
.ae-modal .req { color: #e53935; margin-left: 2px; }
.ae-modal .form-control {
    height: 36px;
    border-radius: 7px;
    border: 1.5px solid #dde3ef;
    font-size: 13px;
    color: #2d3748;
    background: #f8faff;
    padding: 5px 11px;
    transition: border-color 0.18s, box-shadow 0.18s;
}
.ae-modal .form-control:focus {
    border-color: #1a73e8;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(26,115,232,0.13);
    outline: none;
}
.ae-modal textarea.form-control { height: auto; padding-top: 8px; }
.ae-modal .input-group { display: flex; }
.ae-modal .input-group .form-control { flex: 1; border-radius: 0 7px 7px 0 !important; border-left: none; }
.ae-modal .input-group-addon {
    background: #eef2fb;
    border: 1.5px solid #dde3ef;
    border-right: none;
    border-radius: 7px 0 0 7px;
    color: #1a73e8;
    padding: 0 10px;
    display: flex;
    align-items: center;
    font-size: 13px;
}
.ae-modal .input-group-addon.right {
    border-right: 1.5px solid #dde3ef;
    border-left: none;
    border-radius: 0 7px 7px 0;
}
.ae-modal .input-group .form-control.input-left { border-radius: 7px 0 0 7px !important; border-left: 1.5px solid #dde3ef; border-right: none; }
/* Select2 override */
.ae-modal .select2-container--bootstrap .select2-selection--single {
    height: 36px !important;
    border: 1.5px solid #dde3ef !important;
    border-radius: 7px !important;
    background: #f8faff !important;
    line-height: 34px !important;
}
.ae-modal .select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
    line-height: 34px !important;
    font-size: 13px !important;
    color: #2d3748 !important;
    padding-left: 10px !important;
}
.ae-modal .select2-container--bootstrap .select2-selection--single .select2-selection__arrow {
    height: 34px !important;
}
/* Footer */
.ae-modal .modal-footer {
    padding: 12px 20px;
    background: #f0f3fb;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    border-radius: 0;
}
.ae-btn-cancel {
    padding: 8px 20px;
    border-radius: 8px;
    border: 1.5px solid #c8d0e0;
    background: #fff;
    color: #5a6a7e;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.18s;
}
.ae-btn-cancel:hover { background: #eef2fb; border-color: #a0aec0; }
.ae-btn-save {
    padding: 8px 24px;
    border-radius: 8px;
    border: none;
    background: linear-gradient(135deg, #1a73e8 0%, #1558b0 100%);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(26,115,232,0.3);
    transition: all 0.18s;
}
.ae-btn-save:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 5px 14px rgba(26,115,232,0.35); }
.ae-modal .text-danger { font-size: 11px; display: block; margin-top: 3px; }
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
                                    <td class="action-btns" data-title="<?=$this->lang->line('admissionenquiry_action')?>">
                                        <a href="<?=base_url('admissionenquiry/print_preview/'.$enquiry->enquiryID)?>" class="btn btn-info btn-xs mrg" target="_blank" data-toggle="tooltip" title="Print"><i class="fa fa-print"></i></a>
                                        <?php //if(permissionChecker('admissionenquiry_edit')) { ?>
                                            <button class="btn btn-warning btn-xs mrg" onclick="editEnquiry(<?=$enquiry->enquiryID?>)"><i class="fa fa-edit"></i></button>
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

<!-- Admission Enquiry Modal -->
<div class="modal fade ae-modal" id="admissionModal" tabindex="-1" role="dialog" aria-labelledby="admissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="admissionModalLabel"><i class="fa fa-user-plus"></i> <?=$this->lang->line('panel_title')?></h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="admissionForm" role="form" method="post">
                <div class="modal-body">
                    <input type="hidden" name="enquiryID" id="enquiryID">

                    <!-- Section 1: Enquirer Information -->
                    <div class="ae-section">
                        <div class="ae-section-head"><i class="fa fa-user"></i> Enquirer Information</div>
                        <div class="ae-section-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group name_div">
                                        <label>Name <span class="req">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-user-o"></i></span>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Full Name">
                                        </div>
                                        <span class="text-danger" id="error_name"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group phone_div">
                                        <label>Phone <span class="req">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                            <input type="text" class="form-control" id="phone" name="phone" placeholder="10-digit number">
                                        </div>
                                        <span class="text-danger" id="error_phone"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group email_div">
                                        <label>Email</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-envelope-o"></i></span>
                                            <input type="text" class="form-control" id="email" name="email" placeholder="Email Address">
                                        </div>
                                        <span class="text-danger" id="error_email"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group address_div">
                                        <label>Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Complete Address"></textarea>
                                        <span class="text-danger" id="error_address"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Follow-up Details -->
                    <div class="ae-section">
                        <div class="ae-section-head"><i class="fa fa-calendar"></i> Follow-up Details</div>
                        <div class="ae-section-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group date_div">
                                        <label>Enquiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control datepicker" id="date" name="date" placeholder="dd-mm-yyyy">
                                        </div>
                                        <span class="text-danger" id="error_date"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group next_follow_up_date_div">
                                        <label>Next Follow-up Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i></span>
                                            <input type="text" class="form-control datepicker" id="next_follow_up_date" name="next_follow_up_date" placeholder="dd-mm-yyyy">
                                        </div>
                                        <span class="text-danger" id="error_next_follow_up_date"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group source_div">
                                        <label>Source <span class="req">*</span></label>
                                        <select class="form-control select2" id="source" name="source">
                                            <option value="">-- Select Source --</option>
                                            <option value="Online">Online</option>
                                            <option value="Advertisement">Advertisement</option>
                                            <option value="Reference">Reference</option>
                                            <option value="Walk-in">Walk-in</option>
                                        </select>
                                        <span class="text-danger" id="error_source"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Assignment & Inquiry -->
                    <div class="ae-section">
                        <div class="ae-section-head"><i class="fa fa-tasks"></i> Assignment & Inquiry</div>
                        <div class="ae-section-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group assigned_usertypeID_div">
                                        <label>Assigned Usertype</label>
                                        <select class="form-control select2" id="assigned_usertypeID" name="assigned_usertypeID">
                                            <option value="0">-- Select Usertype --</option>
                                            <?php if(customCompute($usertypes)) { foreach($usertypes as $u) { ?>
                                                <option value="<?=$u->usertypeID?>"><?=$u->usertype?></option>
                                            <?php }} ?>
                                        </select>
                                        <span class="text-danger" id="error_assigned_usertypeID"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group assigned_userID_div">
                                        <label>Assigned Person</label>
                                        <select class="form-control select2" id="assigned_userID" name="assigned_userID">
                                            <option value="0">-- Select Person --</option>
                                        </select>
                                        <span class="text-danger" id="error_assigned_userID"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group classesID_div">
                                        <label>Class</label>
                                        <select class="form-control select2" id="classesID" name="classesID">
                                            <option value="0">-- Select Class --</option>
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
                                        <label>Reference</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-link"></i></span>
                                            <input type="text" class="form-control" id="reference" name="reference" placeholder="Reference Name">
                                        </div>
                                        <span class="text-danger" id="error_reference"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group num_child_div">
                                        <label>Number of Children</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-child"></i></span>
                                            <input type="number" class="form-control" id="num_child" name="num_child" min="0" placeholder="0">
                                        </div>
                                        <span class="text-danger" id="error_num_child"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group note_div">
                                        <label>Note</label>
                                        <textarea class="form-control" id="note" name="note" rows="1" placeholder="Additional Notes..."></textarea>
                                        <span class="text-danger" id="error_note"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group description_div">
                                        <label>Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="2" placeholder="Inquiry Details..."></textarea>
                                        <span class="text-danger" id="error_description"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group fee_particulars_div">
                                        <label>Fee Particulars</label>
                                        <textarea class="form-control" id="fee_particulars" name="fee_particulars" rows="2" placeholder="Fee breakdown..."></textarea>
                                        <span class="text-danger" id="error_fee_particulars"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ae-btn-cancel" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                    <button type="submit" class="ae-btn-save"><i class="fa fa-save"></i> Save Enquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('.select2').select2({ width: '100%', dropdownParent: $('#admissionModal') });
    $('.datepicker').datepicker({ autoclose: true, format: 'dd-mm-yyyy' });

    // ── Auto-capitalize first letter ──
    $(document).on('input', '#name, #address, #reference, #note, #description, #fee_particulars', function() {
        var pos = this.selectionStart, v = $(this).val();
        if (v.length > 0) { $(this).val(v.charAt(0).toUpperCase() + v.slice(1)); try { this.setSelectionRange(pos,pos); } catch(e){} }
    });

    // ── Name: letters, spaces, hyphens only ──
    $(document).on('keypress', '#name', function(e) {
        if (!/[a-zA-Z\s.\-']/.test(String.fromCharCode(e.which || e.keyCode))) e.preventDefault();
    });

    // ── Phone: digits only, max 10 ──
    $(document).on('keypress', '#phone', function(e) {
        var c = e.which || e.keyCode; if (c < 48 || c > 57) e.preventDefault();
    });
    $(document).on('input', '#phone', function() {
        $(this).val($(this).val().replace(/\D/g,'').slice(0,10));
    });

    // ── Email: format check on blur ──
    $(document).on('blur', '#email', function() {
        var em = $.trim($(this).val());
        if (em && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) {
            $(this).closest('.form-group').addClass('has-error');
            $('#error_email').text('Enter a valid email address');
        } else {
            $(this).closest('.form-group').removeClass('has-error');
            $('#error_email').text('');
        }
    });

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

        // ── Client-side validation ──
        var ok = true;
        function fieldErr($el, errId, msg) { $el.closest('.form-group').addClass('has-error'); $('#'+errId).text(msg); ok = false; }
        function fieldOk($el, errId)       { $el.closest('.form-group').removeClass('has-error'); $('#'+errId).text(''); }

        var nm = $.trim($('#name').val());
        if (!nm) fieldErr($('#name'), 'error_name', 'Name is required'); else fieldOk($('#name'), 'error_name');

        var ph = $.trim($('#phone').val());
        if (!ph) fieldErr($('#phone'), 'error_phone', 'Phone is required');
        else if (ph.length < 10) fieldErr($('#phone'), 'error_phone', 'Phone must be 10 digits');
        else fieldOk($('#phone'), 'error_phone');

        var em = $.trim($('#email').val());
        if (em && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) fieldErr($('#email'), 'error_email', 'Enter a valid email address');
        else fieldOk($('#email'), 'error_email');

        var src = $('#source').val();
        if (!src) fieldErr($('#source'), 'error_source', 'Please select a source'); else fieldOk($('#source'), 'error_source');

        if (!ok) return;

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
