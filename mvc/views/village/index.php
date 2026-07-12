<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<style>
.ft-toggle-switch {
    display: inline-flex;
    align-items: center;
    width: 58px;
    height: 28px;
    border-radius: 14px;
    position: relative;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.3s;
    padding: 0 6px;
}
.ft-toggle-on  { background: #4cd964; justify-content: flex-end; }
.ft-toggle-off { background: #b0b0b0; justify-content: flex-start; }
.ft-toggle-knob {
    position: absolute;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    transition: left 0.3s;
    top: 3px;
}
.ft-toggle-on  .ft-toggle-knob { right: 3px; left: auto; }
.ft-toggle-off .ft-toggle-knob { left: 3px; }
.ft-toggle-label {
    font-size: 11px;
    font-weight: 700;
    color: #fff;
    line-height: 1;
    user-select: none;
}
.ft-toggle-on  .ft-toggle-label { margin-right: 26px; }
.ft-toggle-off .ft-toggle-label { margin-left: 26px; }
</style>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-home"></i> <?= $this->lang->line('panel_title') ?></h3>
        <ol class="breadcrumb">
            <li><a href="<?= base_url("dashboard/index") ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
            <li class="active"><?= $this->lang->line('menu_villages') ?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php
                //if (permissionChecker('village_add')) {
                ?>
                    <h5 class="page-header">
                        <a href="javascript:void(0);" class="ose-btn create-btn" data-toggle="modal" data-target="#addVillageModal">
                            <i class="fa fa-plus"></i>
                            <?= $this->lang->line('add_title') ?>
                        </a>
                    </h5>
                <?php //} ?>


                <div id="hide-table">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-1"><?= $this->lang->line('slno') ?></th>
                                <th class="col-sm-2">Villege Name</th>
                                <th class="col-sm-2">Database Id</th>
                                <th class="col-sm-1"><?= $this->lang->line('village_status') ?></th>
                                <?php //if (permissionChecker('village_edit') || permissionChecker('village_delete')) { ?>
                                    <th class="col-sm-1"><?= $this->lang->line('action') ?></th>
                                <?php //} ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (customCompute($villages)) {
                                $i = 1;
                                foreach ($villages as $village) { ?>
                                    <tr>
                                        <td data-title="<?= $this->lang->line('slno') ?>">
                                            <?php echo $i; ?>
                                        </td>

                                        <td data-title="<?= $this->lang->line('village_name') ?>">
                                            <?php echo $village->villageName; ?>
                                        </td>

                                        <td data-title="<?= $this->lang->line('village_name') ?>">
                                            <?php echo $village->villageID; ?>
                                        </td>



                                        <td data-title="<?= $this->lang->line('village_status') ?>">
                                            <span class="ft-toggle-switch <?=($village->status == 1) ? 'ft-toggle-on' : 'ft-toggle-off'?>" data-id="<?php echo $village->villageID; ?>" title="Click to toggle status">
                                                <span class="ft-toggle-knob"></span>
                                                <span class="ft-toggle-label"><?=($village->status == 1) ? 'ON' : 'OFF'?></span>
                                            </span>
                                        </td>

                                        <?php //if (permissionChecker('village_edit') || permissionChecker('village_delete')) { ?>
                                            <td data-title="<?= $this->lang->line('action') ?>">

                                                <a href="javascript:void(0);" class="edit-village-btn"
                                                    data-id="<?php echo $village->villageID; ?>"
                                                    data-name="<?php echo htmlspecialchars($village->villageName); ?>"
                                                    data-status="<?php echo $village->status; ?>"
                                                    title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>

                                            </td>
                                        <?php //} ?>
                                    </tr>
                            <?php $i++;
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addVillageModal" tabindex="-1" role="dialog" aria-labelledby="addVillageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:380px;margin-top:120px;">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;">
            <div class="modal-header" style="background:#17a2b8;color:#fff;padding:14px 20px;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;font-size:20px;">&times;</button>
                <h4 class="modal-title" id="addVillageModalLabel" style="font-size:15px;font-weight:700;">
                    <i class="fa fa-plus"></i> Add Village
                </h4>
            </div>
            <div class="modal-body" style="padding:20px 24px;">
                <div class="form-group" style="margin-bottom:6px;">
                    <label style="font-size:13px;font-weight:600;color:#333;">Village Name <span class="text-red">*</span></label>
                    <input type="text" id="newVillageName" class="form-control" style="border-radius:6px;" placeholder="Enter village name">
                    <span id="newVillageNameError" style="color:#e53935;font-size:12px;"></span>
                </div>
            </div>
            <div class="modal-footer" style="background:#f8f9fa;padding:12px 20px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" id="saveNewVillageBtn" class="btn btn-info btn-sm">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editVillageModal" tabindex="-1" role="dialog" aria-labelledby="editVillageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:380px;margin-top:120px;">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;">
            <div class="modal-header" style="background:#17a2b8;color:#fff;padding:14px 20px;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;font-size:20px;">&times;</button>
                <h4 class="modal-title" id="editVillageModalLabel" style="font-size:15px;font-weight:700;">
                    <i class="fa fa-pencil"></i> Edit Village
                </h4>
            </div>
            <div class="modal-body" style="padding:20px 24px;">
                <input type="hidden" id="editVillageID">
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="font-size:13px;font-weight:600;color:#333;">Village Name <span class="text-red">*</span></label>
                    <input type="text" id="editVillageName" class="form-control" style="border-radius:6px;" placeholder="Enter village name">
                </div>
                <div class="form-group" style="margin-bottom:6px;">
                    <label style="font-size:13px;font-weight:600;color:#333;">Status <span class="text-red">*</span></label>
                    <select id="editVillageStatus" class="form-control" style="border-radius:6px;">
                        <option value="1"><?= $this->lang->line('village_active_status') ?></option>
                        <option value="0"><?= $this->lang->line('village_in_active_status') ?></option>
                    </select>
                </div>
                <span id="editVillageError" style="color:#e53935;font-size:12px;"></span>
            </div>
            <div class="modal-footer" style="background:#f8f9fa;padding:12px 20px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" id="saveEditVillageBtn" class="btn btn-info btn-sm">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    $('body').append($('#addVillageModal').detach());
    $('body').append($('#editVillageModal').detach());
});

$(document).on('shown.bs.modal', '#addVillageModal', function () {
    $('#newVillageName').val('').focus();
    $('#newVillageNameError').text('');
});

$(document).on('click', '.edit-village-btn', function () {
    $('#editVillageID').val($(this).data('id'));
    $('#editVillageName').val($(this).data('name'));
    $('#editVillageStatus').val($(this).data('status'));
    $('#editVillageError').text('');
    $('#editVillageModal').modal('show');
});

$(document).on('click', '#saveEditVillageBtn', function () {
    var id = $('#editVillageID').val();
    var name = $.trim($('#editVillageName').val());
    var status = $('#editVillageStatus').val();
    $('#editVillageError').text('');

    if (!name) {
        $('#editVillageError').text('Village name is required.');
        return;
    }

    var $btn = $(this);
    $btn.prop('disabled', true).text('Saving...');

    $.ajax({
        url: "<?php echo base_url('Village/ajax_update'); ?>",
        type: 'POST',
        data: { villageID: id, villageName: name, status: status },
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#editVillageModal').modal('hide');
                toastr.success('Village updated successfully.');
                setTimeout(function () { location.reload(); }, 600);
            } else {
                $('#editVillageError').text(res.message || 'Failed to update village.');
            }
        },
        error: function () {
            $('#editVillageError').text('Request failed. Please try again.');
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save');
        }
    });
});

$(document).on('click', '#saveNewVillageBtn', function () {
    var name = $.trim($('#newVillageName').val());
    $('#newVillageNameError').text('');
    if (!name) {
        $('#newVillageNameError').text('Village name is required.');
        return;
    }

    var $btn = $(this);
    $btn.prop('disabled', true).text('Saving...');

    $.ajax({
        url: "<?php echo base_url('Village/ajax_add'); ?>",
        type: 'POST',
        data: { villageName: name },
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#addVillageModal').modal('hide');
                toastr.success('Village added successfully.');
                setTimeout(function () { location.reload(); }, 600);
            } else {
                $('#newVillageNameError').text(res.message || 'Failed to add village.');
            }
        },
        error: function () {
            $('#newVillageNameError').text('Request failed. Please try again.');
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save');
        }
    });
});

$(document).on('click', '.ft-toggle-switch', function () {
    var $toggle   = $(this);
    var id        = $toggle.data('id');
    var isOn      = $toggle.hasClass('ft-toggle-on');
    var actionLabel = isOn ? 'Deactivate' : 'Activate';
    var btnColor    = isOn ? '#e53935'    : '#0cc035';

    Swal.fire({
        title: actionLabel + '?',
        text: 'Are you sure you want to ' + actionLabel.toLowerCase() + ' this record?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, ' + actionLabel + '!',
        cancelButtonText: 'Cancel'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $toggle.css('opacity', '0.6').css('pointer-events', 'none');

        $.ajax({
            url: "<?php echo base_url('Village/toggle_status'); ?>" + '/' + id,
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    if (res.status == 1) {
                        $toggle.removeClass('ft-toggle-off').addClass('ft-toggle-on');
                        $toggle.find('.ft-toggle-label').text('ON');
                    } else {
                        $toggle.removeClass('ft-toggle-on').addClass('ft-toggle-off');
                        $toggle.find('.ft-toggle-label').text('OFF');
                    }
                    toastr.success('Status updated successfully.');
                } else {
                    toastr.error('Failed to update status. Please try again.');
                }
            },
            error: function () {
                toastr.error('Request failed. Please try again.');
            },
            complete: function () {
                $toggle.css('opacity', '1').css('pointer-events', 'auto');
            }
        });
    });
});
</script>