
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
        <h3 class="box-title"><i class="fa fa-sitemap"></i> <?=$this->lang->line('panel_title')?></h3>

        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_classes')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <?php
                    $usertype = $this->session->userdata("usertype");
                    if(permissionChecker('classes_add')) {
                ?>
                    <h5 class="page-header btn-center">
                        <a class="ose-btn create-btn" href="<?php echo base_url('classes/add') ?>">
                            <i class="fa fa-plus"></i>
                            <?=$this->lang->line('add_title')?>
                        </a>
                    </h5>
                <?php } ?>

                <div id="hide-table">
                    <table id="example1" class="table table-bordered tableBorder dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-lg-1"><?=$this->lang->line('slno')?></th>
                                <th class="col-lg-2"><?=$this->lang->line('classes_name')?></th>
                                <th class="col-lg-2"><?=$this->lang->line('classes_numeric')?></th>
                                <th class="col-lg-3"><?=$this->lang->line('teacher_name')?></th>
                                <th class="col-lg-2"><?=$this->lang->line('classes_note')?></th>
                                <th class="col-sm-2">Status</th>
                                 <?php if(permissionChecker('classes_edit')) { ?>
                                <th class="col-lg-2"><?=$this->lang->line('action')?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($classes)) {$i = 1; foreach($classes as $class) { ?>
                                <tr>
                                    <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('classes_name')?>">
                                        <?php echo $class->classes; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('classes_numeric')?>">
                                        <?php echo $class->classes_numeric; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('teacher_name')?>">
                                        <?=isset($teachers[$class->teacherID]) ? $teachers[$class->teacherID] : '' ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('classes_note')?>">
                                        <?php echo $class->note; ?>
                                    </td>
                                    <td data-title="Status">
                                        <span class="ft-toggle-switch <?=(isset($class->active_status) && $class->active_status == 1) ? 'ft-toggle-on' : 'ft-toggle-off'?>" data-id="<?=$class->classesID?>" title="Click to toggle status">
                                            <span class="ft-toggle-knob"></span>
                                            <span class="ft-toggle-label"><?=(isset($class->active_status) && $class->active_status == 1) ? 'ON' : 'OFF'?></span>
                                        </span>
                                    </td>
                                    <?php if(permissionChecker('classes_edit')) { ?>
                                    <td data-title="<?=$this->lang->line('action')?>">
                                        <?php echo btn_edit('classes/edit/'.$class->classesID, $this->lang->line('edit')) ?>
                                        <?php // echo btn_delete('classes/delete/'.$class->classesID, $this->lang->line('delete')) ?>
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
            url: '<?=base_url("classes/toggle_status")?>' + '/' + id,
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
