
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
        <h3 class="box-title"><i class="fa icon-feetypes"></i> <?=$this->lang->line('panel_title')?></h3>

       
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_feetypes')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php if(permissionChecker('feetypes_add')) { ?>
                    <h5 class="page-header">
                        <a href="<?php echo base_url('feetypes/add') ?>">
                            <i class="fa fa-plus"></i> 
                            <?=$this->lang->line('add_title')?>
                        </a>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="<?php echo base_url('feetypes/fee_setup') ?>">
                            <i class="fa fa-plus"></i> 
                          School Fee Setup
                        </a>

                        <a href="<?php echo base_url('feetypes/term_fee_setup') ?>">
                            <i class="fa fa-plus"></i> 
                           Term Fee Setup
                        </a>

                         <a  data-toggle="modal" data-target="#admission_fee_popup" > <i title="addmission fee" class="fa fa-edit"></i>  Admission Fee</a>

                    </h5>

                    


                <?php } ?>


<!-- Admission fee setup   Modal  start Structure -->
<div class="modal fade" id="admission_fee_popup" tabindex="-1" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileUploadModalLabel">Change Amount</h5>
                    <button style="margin-left: 98% !important;" type="button" class="btn-close" data-dismiss="modal" aria-label="Close"> X </button>
                </div>
                <div class="modal-body">
                    <!-- Form for File Upload -->
                    <div class="row">
            <div class="col-sm-10">
                
                <form class="form-horizontal" role="form" method="post" action="<?php echo base_url('Feetypes/saveAdmissionfee');?>">

                    <?php 
                        if(form_error('feetypes')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="feetypes" class="col-sm-6 control-label">
                            Admission Fee Amount <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="a_fee_amount" name="a_fee_amount" value="<?php echo $adminssion_fee_data['fee_amount']?>" >
                            <input type="hidden" name="fee_type_id" value="<?php echo $adminssion_fee_data['feetypesID']?>">
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('feetypes'); ?>
                        </span>
                    </div>

                     

  

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input type="submit" class="btn btn-success" value="Add Admission Fee" >
                        </div>
                    </div>

                </form>

            </div>
        </div>

                </div>
            </div>
        </div>
    </div> 
<!-- Admission fee modal end -->


                <div id="hide-table">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-2"><?=$this->lang->line('slno')?></th>
                                <th class="col-sm-2"><?=$this->lang->line('feetypes_name')?></th>
                                <th class="col-sm-2"><?=$this->lang->line('feetypes_note')?></th>
                                <th class="col-sm-2">Status</th>
                                <?php if(permissionChecker('feetypes_edit')) { ?>
                                <th class="col-sm-2"><?=$this->lang->line('action')?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($feetypes)) {$i = 1; foreach($feetypes as $feetype) { ?>
                                <tr>
                                    <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('feetypes_name')?>">
                                        <?php echo $feetype->feetypes; ?>
                                    </td>

                                    <td data-title="<?=$this->lang->line('feetypes_note')?>">
                                        <?php echo $feetype->note; ?>
                                    </td>
                                    <td data-title="Status">
                                        <span class="ft-toggle-switch <?=(isset($feetype->active_status) && $feetype->active_status == 1) ? 'ft-toggle-on' : 'ft-toggle-off'?>" data-id="<?=$feetype->feetypesID?>" title="Click to toggle status">
                                            <span class="ft-toggle-knob"></span>
                                            <span class="ft-toggle-label"><?=(isset($feetype->active_status) && $feetype->active_status == 1) ? 'ON' : 'OFF'?></span>
                                        </span>
                                    </td>
                                    <?php if(permissionChecker('feetypes_edit')) { ?>
                                        <td data-title="<?=$this->lang->line('action')?>">
                                            <?php echo btn_edit('feetypes/edit/'.$feetype->feetypesID, $this->lang->line('edit')) ?>
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
    var $toggle = $(this);
    var id = $toggle.data('id');
    $toggle.css('opacity', '0.6').css('pointer-events', 'none');
    $.ajax({
        url: '<?=base_url("feetypes/toggle_status")?>' + '/' + id,
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
            }
        },
        complete: function () {
            $toggle.css('opacity', '1').css('pointer-events', 'auto');
        }
    });
});
</script>