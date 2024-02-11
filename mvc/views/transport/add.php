
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
                        if(form_error('route')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="route" class="col-sm-2 control-label">
                            <?=$this->lang->line("transport_route")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="route" name="route" value="<?=set_value('route')?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('route'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('vehicle')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="vehicle" class="col-sm-2 control-label">
                            Vehicle No. <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" placeholder="AP05FE3403" class="form-control" id="vehicle" name="vehicle" value=""   >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('vehicle'); ?>
                        </span>
                    </div>

                    <!-- <?php 
                        if(form_error('fare')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="fare" class="col-sm-2 control-label">
                            <?=$this->lang->line("transport_fare")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="fare" name="fare" value="<?=set_value('fare')?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('fare'); ?>
                        </span>
                    </div>

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
                    </div> -->

                  
                    <?php 
                        if(form_error('vehicle_type')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="vehicle_type" class="col-sm-2 control-label">
                            Vehicle Type <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            
                        <select name="vehicle_type" id="vehicle_type" class='form-control select2'>
                            <option value=""> </otion>
                            <option value="1">Mini Bus </otion>
                            <option value="2">Van </otion>
                            <option value="3">Bus </otion>
                            <option value="4">Auto </otion>
                        </select>
                           
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('vehicle_type'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('driverID')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="driverID" class="col-sm-2 control-label">
                            Driver <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            
                            <?php
                            // echo 'ddddd=<pre>';
                            // print_r($drivers);
                                $array = array();
                                $array[0] = $this->lang->line("classes_select_route_name");
                                foreach ($drivers as $driver) {
                                    $array[$driver->userID] = $driver->name;
                                }
                                echo form_dropdown("driverID", $array, set_value("userID"), "id='driverID' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('driverID'); ?>
                        </span>
                    </div>

                    
                    <?php 
                        if(form_error('attenderID')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="attenderID" class="col-sm-2 control-label">
                            Attender <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            
                            <?php
                            // echo 'ddddd=<pre>';
                            // print_r($attenders);
                                $array = array();
                                $array[0] = $this->lang->line("classes_select_route_name");
                                foreach ($attenders as $attender) {
                                    $array[$attender->userID] = $attender->name;
                                }
                                echo form_dropdown("attenderID", $array, set_value("userID"), "id='attenderID' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('attenderID'); ?>
                        </span>
                    </div>

                    <?php 
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
                    </div>

                    <?php 
                        if(form_error('note')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="note" class="col-sm-2 control-label">
                            <?=$this->lang->line("transport_note")?>
                        </label>
                        <div class="col-sm-6">
                            <textarea class="form-control" style="resize:none;" id="note" name="note"><?=set_value('note')?></textarea>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('note'); ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input type="submit" class="btn btn-success" value="<?=$this->lang->line("add_transport")?>" >
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>