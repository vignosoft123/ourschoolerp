
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
        <h3 class="box-title"><i class="fa icon-sbus"></i> <?=$this->lang->line('panel_title')?></h3>

       
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_transport')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php if(permissionChecker('transport_add')) { ?>
                    <h5 class="page-header">
                        <a href="<?php echo base_url('transport/add') ?>">
                            <i class="fa fa-plus"></i> 
                            <?php //echo $this->lang->line('add_title')?>
                            Add Route/Vehicle
                        </a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="<?php echo base_url('transport/add_new') ?>">
                            <i class="fa fa-plus"></i> 
                            Add Capacity/Pickups
                        </a>

                    </h5>
 

                <?php } ?>

                <div id="hide-table">
                    <?php $vehicle_types = array(1 => 'Mini Bus', 2 => 'Van', 3 => 'Bus', 4 => 'Auto'); ?>
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-1"><?=$this->lang->line('slno')?></th>
                                <th class="col-sm-2"><?=$this->lang->line('transport_route')?></th>
                                <th class="col-sm-1"><?=$this->lang->line('transport_vehicle')?></th>
                                <th class="col-sm-1">Vehicle Type</th>
                                <th class="col-sm-1">Driver</th>
                                <th class="col-sm-1">Attender</th>
                                <th class="col-sm-1">Capacity</th>
                                <th class="col-sm-2"><?=$this->lang->line('transport_note')?></th>
                                <th class="col-sm-1">Status</th>
                                <?php if(permissionChecker('transport_edit')) { ?>
                                    <th class="col-sm-1"><?=$this->lang->line('action')?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($transports)) {$i = 1; foreach($transports as $transport) { ?>
                                <tr>
                                    <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('transport_route')?>">
                                        <?php echo $transport->route; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('transport_vehicle')?>">
                                        <?php echo $transport->vehicle; ?>
                                    </td>
                                    <td data-title="Vehicle Type">
                                        <?php echo isset($vehicle_types[$transport->vehicle_type]) ? $vehicle_types[$transport->vehicle_type] : '-'; ?>
                                    </td>
                                    <td data-title="Driver">
                                        <?php echo $transport->driver_name ? $transport->driver_name : '-'; ?>
                                    </td>
                                    <td data-title="Attender">
                                        <?php echo $transport->attender_name ? $transport->attender_name : '-'; ?>
                                    </td>
                                    <td data-title="Capacity">
                                        <?php echo $transport->capacity; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('transport_note')?>">
                                        <?php echo $transport->note; ?>
                                    </td>

                                    <td data-title="Status">
                                        <span class="ft-toggle-switch <?=(isset($transport->active_status) && $transport->active_status == 1) ? 'ft-toggle-on' : 'ft-toggle-off'?>" data-id="<?php echo $transport->transportID; ?>" title="Click to toggle status">
                                            <span class="ft-toggle-knob"></span>
                                            <span class="ft-toggle-label"><?=(isset($transport->active_status) && $transport->active_status == 1) ? 'ON' : 'OFF'?></span>
                                        </span>
                                    </td>

                                    <?php if(permissionChecker('transport_edit')) { ?>
                                        <td data-title="<?=$this->lang->line('action')?>">
                                            <?php echo btn_edit('transport/edit/'.$transport->transportID, $this->lang->line('edit')) ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
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
            url: "<?php echo base_url('transport/toggle_status'); ?>" + '/' + id,
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    if (res.active_status == 1) {
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
