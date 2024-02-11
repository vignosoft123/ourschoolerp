<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa iniicon-productpurchasereport"></i> Expense Report</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("Dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"> Expense Report</li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group col-sm-4" id="expensetype">
                    <label>Expense Category</label>
                    <?php
                    $expensetypeArray = array(
                        "0" => "Select Category",
                    );
                    if(customCompute($expensetypes)) {
                        foreach($expensetypes as $expensetype) {
                            $expensetypeArray[$expensetype->expensetypesID] = $expensetype->expensetypes;
                        }
                    }
                    echo form_dropdown("expensetypesID", $expensetypeArray, set_value("expensetypesID"), "id='expensetypesID' class='form-control select2'");
                    ?>
                </div>

                

                <div class="form-group col-sm-4" id="referenceNoDiv">
                    <label><?=$this->lang->line("productpurchasereport_referenceNo")?></label>
                    <input class="form-control" type="text" name="reference_no" id="reference_no">
                </div>

                <!-- <div class="form-group col-sm-4" id="statusDiv">
                    <label><?=$this->lang->line("productpurchasereport_status")?></label>
                    <?php
                    // $array = array(
                    //     "0" => $this->lang->line("productpurchasereport_please_select"),
                    //     "1" => $this->lang->line("productpurchasereport_pending"),
                    //     "2" =>$this->lang->line("productpurchasereport_partial"),
                    //     "3" => $this->lang->line("productpurchasereport_fully_paid"),
                    //     "4" => $this->lang->line("productpurchasereport_refund")
                    // );
                    // echo form_dropdown("statusID", $array, set_value("statusID"), "id='statusID' class='form-control select2'");
                    ?>
                </div> -->

                <div class="form-group col-sm-4" id="fromdateDiv">
                    <label><?=$this->lang->line("productpurchasereport_fromdate")?></label>
                   <input class="form-control" type="text" name="fromdate" id="fromdate">
                </div>

                <div class="form-group col-sm-4" id="todateDiv">
                    <label><?=$this->lang->line("productpurchasereport_todate")?></label>
                    <input class="form-control" type="text" name="todate" id="todate">
                </div>

                <div class="col-sm-4">
                    <button id="get_expensereport" class="btn btn-success" style="margin-top:23px;"> <?=$this->lang->line("productpurchasereport_submit")?></button>
                </div>

            </div>

        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->

<div id="load_productpurchasereport"></div>


<script type="text/javascript">

    function printDiv(divID) {
        var oldPage = document.body.innerHTML;
        $('#headerImage').remove();
        $('.footerAll').remove();
        var divElements = document.getElementById(divID).innerHTML;
        var footer = "<center><img src='<?=base_url('uploads/images/'.$siteinfos->photo)?>' style='width:30px;' /></center>";
        var copyright = "<center><?=$siteinfos->footer?> | <?=$this->lang->line('productpurchasereport_hotline')?> : <?=$siteinfos->phone?></center>";
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

    $(document).bind('click', '#fromdate, #todate', function() {
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
    });


    $('#get_expensereport').click(function() {

        var expensetypesID = $('#expensetypesID').val(); 
        var reference_no = $('#reference_no').val(); 
        var fromdate = $('#fromdate').val();
        var todate   = $('#todate').val();

        var field = {
            'expensetypesID': expensetypesID, 
            'reference_no': reference_no, 
            'fromdate': fromdate,
            'todate': todate
        };

        makingPostDataPreviousofAjaxCall(field);
    });

    function makingPostDataPreviousofAjaxCall(field) {
        passData = field;
        ajaxCall(passData);
    }

    function ajaxCall(passData) {
        $.ajax({
            type: 'POST',
            url: "<?=base_url('expensereport/getExpensereport')?>",
            data: passData,
            dataType: "html",
            success: function(data) {
                var response = JSON.parse(data);
                renderLoder(response, passData);
            }
        });
    }

    function renderLoder(response, passData) {
        if(response.status) {
            $('#load_productpurchasereport').html(response.render);
            for (var key in passData) {
                if (passData.hasOwnProperty(key)) {
                    $('#'+key).parent().removeClass('has-error');
                }
            }
        } else {
            for (var key in passData) {
                if (passData.hasOwnProperty(key)) {
                    $('#'+key).parent().removeClass('has-error');
                }
            }

            for (var key in response) {
                if (response.hasOwnProperty(key)) {
                    $('#'+key).parent().addClass('has-error');
                }
            }
        }
    }

</script>
