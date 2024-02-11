
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-lbooks"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("village/index")?>"><?=$this->lang->line('menu_villages')?></a></li>
            <li class="active"><?=$this->lang->line('menu_add')?> <?=$this->lang->line('menu_villages')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-10">
                 <form class="form-horizontal" role="form" method="post">
                   <?php 
                        if(form_error('villageName')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="villageName" class="col-sm-2 control-label">
                            <?=$this->lang->line("village_name")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="villageName" name="villageName" value="<?=set_value('villageName')?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('villageName'); ?>
                        </span>
                    </div>
                    <?php
                        if(form_error('status'))
                            echo "<div class='form-group has-error' >";
                        else
                            echo "<div class='form-group' >";
                    ?>
                        <label for="status" class="col-sm-2 control-label">
                            <?=$this->lang->line("village_status")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                echo form_dropdown("status", array('' => $this->lang->line('village_select_status'), 1 => $this->lang->line('village_active_status'), 0 => $this->lang->line('village_in_active_status')), set_value("status", ''), "id='status' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('status'); ?>
                        </span>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input type="submit" class="btn btn-success" value="<?=$this->lang->line("add_village")?>" >
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
