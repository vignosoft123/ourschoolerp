
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-feetypes"></i> <?=$this->lang->line('panel_title')?></h3>

        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("feetypes/index")?>"><?=$this->lang->line('menu_feetypes')?></a></li>
            <li class="active"><?=$this->lang->line('menu_add')?> <?=$this->lang->line('menu_feetypes')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-10">
                <form class="form-horizontal" role="form" method="post">

                 
                    <div class="row">
                        <div class="col-md-10">
                            <div class="row">
                            <div class="col-sm-12">

                                <div class="col-md-3">
                                    <div class="<?php echo form_error('classesID') ? 'form-group has-error' : 'form-group'; ?>">
                                        <label for="classesID" class="control-label">
                                           Class <span class="text-red">*</span>
                                        </label>
                                        <?php
                                        $array = array("0" => $this->lang->line("mark_select_classes"));
                                        foreach ($classes as $classa) {
                                            $array[$classa->classesID] = $classa->classes;
                                        }
                                        echo form_dropdown("classesID", $array, set_value("classesID"), "id='classesID' class='form-control select2 classesID'");
                                        ?>
                                    </div>
                                </div> 
                                </div>
                                </div>  
                               
                            </div>
                        </div>
                    
                    
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input type="submit" class="btn btn-success" value="Get Sections" >
                        </div>
                    </div>

                </form>

            </div>
        </div>


        <div id="hide-table">
            <input type="hidden" id="class_id" value="<?= $set_classes?>">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th width="2%" class="col-sm-2"><?=$this->lang->line('slno')?></th>
                                <th class="col-sm-2">Section</th>
                                <th class="col-sm-2">School Fee</th>
                                 
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($sections)) {$i = 1; foreach($sections as $sec) { ?>
                                <tr>
                                    <td width="2%" data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td  >
                                        <?php echo $sec->section; ?>
                                    </td>
                                    <td><input vid="<?= $sec->sf_id?>" section_id="<?= $sec->sectionID?>" id="" class="school_fee" value="<?= $sec->fee_amount?>"></td>
                                
                  
                                     
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>
                



    </div>
</div>

<script>
     $(document).on('focusout','.school_fee',function() {

        var vid = $(this).attr('vid');
        var section_id = $(this).attr('section_id');
        var my_value = $(this).val();
        var class_id = $("#class_id").val();

        
        $.ajax({
        url : "<?php echo site_url('Feetypes/update_school_fee'); ?>",
        type : "POST",
        cache:false,  
        data: {vid:vid,section_id:section_id,class_id:class_id,my_value:my_value},
        dataType: "json", 
        success : function(resp) {
            location.reload();
            // var obj = JSON.parse(resp); 
            
        }  
        });
        })
</script>