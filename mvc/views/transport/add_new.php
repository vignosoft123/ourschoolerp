
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
            <li><a href="<?=base_url("transport/index")?>"><?=$this->lang->line('menu_transport')?></a></li>
            <li class="active"><?=$this->lang->line('menu_add')?> <?=$this->lang->line('menu_transport')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-10">
                <form class="form-horizontal" role="form" method="post">

                <input type="hidden" id="pickup_id" name="pickup_id" value="">

                <?php
                        if(form_error('transportID')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="transportID" class="col-sm-2 control-label">
                            Routes <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            
                            <?php
                                $array = array();
                                $array[0] = $this->lang->line("classes_select_route_name");
                                foreach ($transports as $transport) {
                                    $array[$transport->transportID] = $transport->route;
                                }
                                echo form_dropdown("transportID", $array, set_value("transportID"), "id='transportID' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('transportID'); ?>
                        </span>
                    </div>

                     

                    <!-- <?php 
                        if(form_error('capacity')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="capacity" class="col-sm-2 control-label">
                            capacity <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="capacity" name="capacity" value="<?=set_value('capacity')?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('capacity'); ?>
                        </span>
                    </div> -->

                    

                    

                    <?php 
                        if(form_error('pickup_point')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="pickup_point" class="col-sm-2 control-label">
                            Pickup Point  <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="pickup_point" name="pickup_point" value="<?=set_value('pickup_point')?>"   >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('pickup_point'); ?>
                        </span>
                    </div>

                   
                    <?php 
                        if(form_error('pickup_time')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="pickup_time" class="col-sm-2 control-label">
                            Pickup Time <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="pickup_time" name="pickup_time" value="<?=set_value('pickup_time')?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('pickup_time'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('drop_time')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="drop_time" class="col-sm-2 control-label">
                            Droping Time <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="drop_time" name="drop_time" value="<?=set_value('drop_time')?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('drop_time'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('fare')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="fare" class="col-sm-2 control-label">
                            Pickup point fare <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="fare" name="fare" value="<?=set_value('fare')?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('fare'); ?>
                        </span>
                    </div>
                    
                    <!-- <?php 
                        if(form_error('route')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="route" class="col-sm-2 control-label">
                            Driver Name <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="route" name="route" value="<?=set_value('route')?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('route'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('route')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="route" class="col-sm-2 control-label">
                            Attender Name <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="route" name="route" value="<?=set_value('route')?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('route'); ?>
                        </span>
                    </div>
                    
 -->

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input type="submit" id="submit-btn" class="btn btn-success" value="Add Pickup Points" >
                            <button type="button" id="cancel-edit-btn" class="btn btn-default" style="display:none;">Cancel</button>
                        </div>
                    </div>
                </form>


                
            </div>

            <div id="hide-table">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-1"><?=$this->lang->line('slno')?></th>
                                <th class="col-sm-3"><?=$this->lang->line('transport_route')?></th>
                                <th class="col-sm-2">Pickup Point</th>
                                <th class="col-sm-1">Pickup Time</th>
                                <th class="col-sm-2">Drop Time</th>
                                <th class="col-sm-2">Fare</th>
                                <th class="col-sm-2">Crated Date</th>
                                <th class="col-sm-1">Status</th>
                                <th class="col-sm-1"><?=$this->lang->line('action')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($pickup_points)) {$i = 1; foreach($pickup_points as $transport) { ?>
                                <tr>
                                    <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('transport_route')?>">
                                        <?php echo $transport->route; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('transport_route')?>">
                                        <?php echo $transport->pickupPoint; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('transport_vehicle')?>">
                                        <?php echo $transport->pickup_time; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('transport_vehicle')?>">
                                        <?php echo $transport->droping_time; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('transport_fare')?>">
                                        <input value="<?php echo $transport->fare; ?>" name="fare" id="fare"  data-id="<?php echo $transport->id; ?>" class="fare-input">

                                    </td>
                                    <td data-title="">
                                        <?php echo $transport->created_on; ?>
                                    </td>

                                    <td data-title="Status">
                                        <span class="ft-toggle-switch <?=(isset($transport->active_status) && $transport->active_status == 1) ? 'ft-toggle-on' : 'ft-toggle-off'?>" data-id="<?php echo $transport->id; ?>" title="Click to toggle status">
                                            <span class="ft-toggle-knob"></span>
                                            <span class="ft-toggle-label"><?=(isset($transport->active_status) && $transport->active_status == 1) ? 'ON' : 'OFF'?></span>
                                        </span>
                                    </td>

                                    <td data-title="<?=$this->lang->line('action')?>">
                                        <a href="javascript:void(0);" class="btn btn-warning btn-xs mrg edit-pickup-btn"
                                            data-id="<?php echo $transport->id; ?>"
                                            data-route="<?php echo $transport->route_id; ?>"
                                            data-pickup="<?php echo htmlspecialchars($transport->pickupPoint); ?>"
                                            data-pickuptime="<?php echo $transport->pickup_time; ?>"
                                            data-droptime="<?php echo $transport->droping_time; ?>"
                                            data-fare="<?php echo $transport->fare; ?>"
                                            data-placement="top" data-toggle="tooltip" data-original-title="<?=$this->lang->line('edit')?>">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    $('#pickup_time').timepicker();
    $('#drop_time').timepicker();



});

$(document).ready(function () {
    $('.edit-pickup-btn').on('click', function () {
        $('#pickup_id').val($(this).data('id'));
        $('#transportID').val($(this).data('route')).trigger('change');
        $('#pickup_point').val($(this).data('pickup'));
        $('#pickup_time').val($(this).data('pickuptime'));
        $('#drop_time').val($(this).data('droptime'));
        $('#fare').val($(this).data('fare'));

        $('#submit-btn').val('Update Pickup Point');
        $('#cancel-edit-btn').show();

        $('html, body').animate({ scrollTop: $('#transportID').offset().top - 100 }, 300);
    });

    $('#cancel-edit-btn').on('click', function () {
        $('#pickup_id').val('');
        $('#transportID').val('0').trigger('change');
        $('#pickup_point').val('');
        $('#pickup_time').val('');
        $('#drop_time').val('');
        $('#fare').val('');

        $('#submit-btn').val('Add Pickup Points');
        $(this).hide();
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
            url: "<?php echo base_url('transport/toggle_status_pickup_point'); ?>" + '/' + id,
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

$(document).ready(function () {
    $('.fare-input').on('focusout', function () {
        var fare = $(this).val();
        var id = $(this).data('id');

        $.ajax({
            url: "<?php echo base_url('Transport/update_fare'); ?>",
            type: "POST",
            data: { id: id, fare: fare },
            dataType: "json",
            success: function (response) {
                if (response.status === 'success') {
                    alert("Fare updated successfully.");
                } else {
                    alert("Update failed.");
                }
            },
            error: function () {
                alert("AJAX error.");
            }
        });
    });
});

</script>