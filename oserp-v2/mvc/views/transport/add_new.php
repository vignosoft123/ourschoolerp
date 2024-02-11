
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
                            <input type="submit" class="btn btn-success" value="Add Pickup Points" >
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
                                <?php //if(permissionChecker('transport_edit') || permissionChecker('transport_delete')) { ?>
                                    <!-- <th class="col-sm-2"><?=$this->lang->line('action')?></th> -->
                                <?php //} ?>
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
                                        <?php echo $transport->fare; ?>
                                    </td>
                                    <td data-title="">
                                        <?php echo $transport->created_on; ?>
                                    </td>

                                    <?php //if(permissionChecker('transport_edit') || permissionChecker('transport_delete')) { ?>
                                        <!-- <td data-title="<?=$this->lang->line('action')?>">
                                            <?php echo btn_edit('transport/edit/'.$transport->transportID, $this->lang->line('edit')) ?>
                                            <?php echo btn_delete('transport/delete/'.$transport->transportID, $this->lang->line('delete')) ?>
                                        </td> -->
                                    <?php //} ?>
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
</script>