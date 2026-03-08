<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-university"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("college_group/index")?>"><?=$this->lang->line('panel_title')?></a></li>
            <li class="active"><?=$this->lang->line('edit')?> <?=$this->lang->line('panel_title')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <form class="form-horizontal" role="form" method="post">
                    
                    <div class="form-group <?=form_error('college_name') ? 'has-error' : ''?>">
                        <label for="college_name" class="col-sm-2 control-label">
                            <?=$this->lang->line("college_group_name")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="college_name" name="college_name" value="<?=set_value('college_name', $college_group->college_name)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('college_name'); ?>
                        </span>
                    </div>

                    <div class="form-group <?=form_error('college_url') ? 'has-error' : ''?>">
                        <label for="college_url" class="col-sm-2 control-label">
                            <?=$this->lang->line("college_group_url")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="college_url" name="college_url" value="<?=set_value('college_url', $college_group->college_url)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('college_url'); ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input type="submit" class="btn btn-success" value="<?=$this->lang->line("update_college_group")?>" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
