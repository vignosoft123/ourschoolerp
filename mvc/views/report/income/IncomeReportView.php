<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-clipboard"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("Dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"> <a href="<?=base_url("income/index")?>"> <?=$this->lang->line('incomereport_income')?> </a> </li>
            <li class="active"> <?=$this->lang->line('panel_title')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group col-sm-4" id="fromdateDiv">
                    <label><?=$this->lang->line("incomereport_fromdate")?></label>
                   <input class="form-control" type="text" name="fromdate" id="fromdate">
                </div>

                <div class="form-group col-sm-3" id="todateDiv">
                    <label><?=$this->lang->line("incomereport_todate")?></label>
                    <input class="form-control" type="text" name="todate" id="todate">
                </div>

                <div class="form-group col-sm-3" id="incomecategoriesDiv">
                    <label>Income Category</label>
                    <?php
                        $array = array(0 => 'Select Category');
                        if(customCompute($income_categories)) {
                            foreach ($income_categories as $key => $category) {
                                $array[$category->incomecategoriesID] = $category->name;
                            }
                        }
                        echo form_dropdown("incomecategoriesID", $array, set_value("incomecategoriesID"), "id='incomecategoriesID' class='form-control select2'");
                    ?>
                </div>

                <div class="col-sm-3">
                    <button id="get_incomereport" class="btn btn-success" style="margin-top:23px;"> <?=$this->lang->line("incomereport_submit")?></button>
                </div>

            </div>

        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->

<div id="load_incomereport"></div>


<script type="text/javascript">

    function printDiv(divID) {
        var oldPage = document.body.innerHTML;
        $('#headerImage').remove();
        $('.footerAll').remove();
        var divElements = document.getElementById(divID).innerHTML;
        var footer = "<center><img src='<?=base_url('uploads/images/'.$siteinfos->photo)?>' style='width:30px;' /></center>";
        var copyright = "<center><?=$siteinfos->footer?> | <?=$this->lang->line('incomereport_hotline')?> : <?=$siteinfos->phone?></center>";
        document.body.innerHTML =
          "<html><head><title></title></head><body>" +
          "<center><img src='<?=base_url('uploads/images/'.$siteinfos->photo)?>' style='width:50px;' /></center>"
          + divElements + footer + copyright + "</body>";

        window.print();
        document.body.innerHTML = oldPage;
        window.location.reload();
    }

    $('.select2').select2();

    $('#fromdate').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy',
        startDate:'<?=$schoolyearsessionobj->startingdate?>',
        endDate:'<?=$schoolyearsessionobj->endingdate?>',
    });

    $('#todate').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy',
        startDate:'<?=$schoolyearsessionobj->startingdate?>',
        endDate:'<?=$schoolyearsessionobj->endingdate?>',
    });

    $('#get_incomereport').click(function() {
        var fromdate = $('#fromdate').val();
        var todate   = $('#todate').val();
        var incomecategoriesID = $('#incomecategoriesID').val();

        var field = {
            'fromdate': fromdate,
            'todate': todate,
            'incomecategoriesID': incomecategoriesID
        };

        $.ajax({
            type: 'POST',
            url: "<?=base_url('incomereport/getIncomereport')?>",
            data: field,
            dataType: "html",
            success: function(data) {
                var response = JSON.parse(data);
                if(response.status) {
                    $('#load_incomereport').html(response.render);
                    $('#fromdateDiv').removeClass('has-error');
                    $('#todateDiv').removeClass('has-error');
                } else {
                    if(response.fromdate) {
                        $('#fromdateDiv').addClass('has-error');
                    } else {
                        $('#fromdateDiv').removeClass('has-error');
                    }
                    if(response.todate) {
                        $('#todateDiv').addClass('has-error');
                    } else {
                        $('#todateDiv').removeClass('has-error');
                    }
                }
            }
        });
    });

</script>
