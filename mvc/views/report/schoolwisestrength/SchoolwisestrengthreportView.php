<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-bar-chart"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_schoolwisestrengthreport')?></li>
        </ol>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="rpt-filter-card">
            <div class="rpt-filter-title"><i class="fa fa-filter"></i>&nbsp; Filter Options</div>
            <div class="row">
                <div class="form-group col-sm-4" id="classesDiv">
                    <label for="classesID"><?=$this->lang->line("schoolwisestrengthreport_class")?></label>
                    <?php
                        $classesArray['0'] = $this->lang->line("schoolwisestrengthreport_all_classes");
                        if(customCompute($classes)) {
                            foreach ($classes as $classa) {
                                $classesArray[$classa->classesID] = $classa->classes;
                            }
                        }
                        echo form_dropdown("classesID", $classesArray, set_value("classesID"), "id='classesID' class='form-control select2'");
                     ?>
                </div>

                <div class="form-group col-sm-4" id="sectionDiv">
                    <label for="sectionID"><?=$this->lang->line("schoolwisestrengthreport_section")?></label>
                    <?php
                        $sectionArray = array("0" => $this->lang->line("schoolwisestrengthreport_all_sections"));
                        echo form_dropdown("sectionID", $sectionArray, set_value("sectionID"), "id='sectionID' class='form-control select2'");
                     ?>
                </div>
            </div><!-- row -->

            <div class="rpt-filter-actions">
                <a href="javascript:void(0)" id="export_excel_btn" class="btn btn-success rpt-filter-btn" target="_blank">
                    <i class="fa fa-file-excel-o"></i> <?=$this->lang->line("schoolwisestrengthreport_export_excel")?>
                </a>
                <button id="get_schoolwisestrengthreport" class="btn btn-primary rpt-filter-btn">
                    <i class="fa fa-search"></i> <?=$this->lang->line("schoolwisestrengthreport_view_report")?>
                </button>
            </div>
        </div><!-- /.rpt-filter-card -->
    </div><!-- Body -->
</div><!-- /.box -->

<div id="load_schoolwisestrengthreport"></div>

<script type="text/javascript">
    $(function(){
        $(".select2").select2();
    });

    function updateExportLink() {
        var classesID = $('#classesID').val() || 0;
        var sectionID = $('#sectionID').val() || 0;
        $('#export_excel_btn').attr('href', '<?=base_url('schoolwisestrengthreport/export_excel')?>?classesID=' + classesID + '&sectionID=' + sectionID);
    }

    $(document).on('change', '#classesID', function() {
        $('#load_schoolwisestrengthreport').html('');
        var classesID = $(this).val();
        if (classesID == 0) {
            $('#sectionID').html('<option value="0"><?=$this->lang->line("schoolwisestrengthreport_all_sections")?></option>');
            updateExportLink();
        } else {
            $.ajax({
                type: 'POST',
                url: '<?=base_url('schoolwisestrengthreport/getSection')?>',
                data: {'classesID': classesID},
                success: function(data) {
                    $('#sectionID').html(data);
                    updateExportLink();
                }
            });
        }
    });

    $(document).on('change', '#sectionID', function() {
        $('#load_schoolwisestrengthreport').html('');
        updateExportLink();
    });

    $(document).on('click', '#get_schoolwisestrengthreport', function() {
        var passData = {
            'classesID': $('#classesID').val() || 0,
            'sectionID': $('#sectionID').val() || 0,
        };
        $.ajax({
            type: 'POST',
            url: '<?=base_url('schoolwisestrengthreport/getReport')?>',
            data: passData,
            dataType: 'html',
            success: function(data) {
                var response = JSON.parse(data);
                if (response.status) {
                    $('#load_schoolwisestrengthreport').html(response.render);
                }
            }
        });
    });

    updateExportLink();
    $('#get_schoolwisestrengthreport').trigger('click');
</script>
