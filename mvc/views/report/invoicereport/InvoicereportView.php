<div class="box">
    <div class="box-header">
        <h3 class="box-title">
            <i class="fa fa-bar-chart"></i>
            <?= $this->lang->line('invoicereport_panel_title') ?>
        </h3>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('dashboard/index') ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
            <li><a href="<?= base_url('invoice/index') ?>"><?= $this->lang->line('menu_invoice') ?></a></li>
            <li class="active"><?= $this->lang->line('invoicereport_panel_title') ?></li>
        </ol>
    </div>
    <div class="box-body">
        <div class="row">

            <!-- Class -->
            <div class="form-group col-sm-3" id="classesDiv">
                <label><?= $this->lang->line('invoicereport_class') ?></label>
                <?php
                $classesArray = ["0" => $this->lang->line('invoicereport_please_select')];
                if (customCompute($classes)) {
                    foreach ($classes as $c) {
                        $classesArray[$c->classesID] = $c->classes;
                    }
                }
                echo form_dropdown("classesID", $classesArray, set_value("classesID"), "id='classesID' class='form-control select2'");
                ?>
            </div>

            <!-- Section -->
            <div class="form-group col-sm-3" id="sectionDiv" style="display:none;">
                <label><?= $this->lang->line('invoicereport_section') ?></label>
                <?php
                echo form_dropdown("sectionID", ["0" => $this->lang->line('invoicereport_please_select')], set_value("sectionID"), "id='sectionID' class='form-control select2'");
                ?>
            </div>

            <!-- Student -->
            <div class="form-group col-sm-3" id="studentDiv" style="display:none;">
                <label><?= $this->lang->line('invoicereport_student') ?></label>
                <?php
                echo form_dropdown("studentID", ["0" => $this->lang->line('invoicereport_please_select')], set_value("studentID"), "id='studentID' class='form-control select2'");
                ?>
            </div>

            <!-- Fee Type -->
            <div class="form-group col-sm-3" id="feetypeDiv">
                <label><?= $this->lang->line('invoicereport_feetype') ?></label>
                <?php
                $feetypeArray = ["0" => $this->lang->line('invoicereport_please_select')];
                if (customCompute($feetypes)) {
                    foreach ($feetypes as $ft) {
                        $feetypeArray[$ft->feetypesID] = $ft->feetypes;
                    }
                }
                echo form_dropdown("feetypeID", $feetypeArray, set_value("feetypeID"), "id='feetypeID' class='form-control select2'");
                ?>
            </div>

            <!-- Submit -->
            <div class="col-sm-12" style="margin-top:10px;">
                <button id="get_invoicereport" class="btn btn-success">
                    <i class="fa fa-search"></i> <?= $this->lang->line('invoicereport_submit') ?>
                </button>
            </div>

        </div>
    </div>
</div>

<div id="load_invoicereport"></div>

<script type="text/javascript">
    $('.select2').select2();

    // Class change → load sections (and reset student)
    $(document).on('change', '#classesID', function () {
        var id = $(this).val();
        $('#studentID').html('<option value="0"><?= $this->lang->line('invoicereport_please_select') ?></option>');
        if (id == '0') {
            $('#sectionID').html('<option value="0"><?= $this->lang->line('invoicereport_please_select') ?></option>');
            $('#sectionDiv').hide('slow');
            $('#studentDiv').hide('slow');
        } else {
            $('#sectionDiv').show('slow');
            $.ajax({
                type: 'POST',
                url: "<?= base_url('invoicereport/getSection') ?>",
                data: { classesID: id },
                dataType: 'html',
                success: function (data) { $('#sectionID').html(data); }
            });
        }
    });

    // Section change → load students
    $(document).on('change', '#sectionID', function () {
        var sid = $(this).val();
        var cid = $('#classesID').val();
        $('#studentID').html('<option value="0"><?= $this->lang->line('invoicereport_please_select') ?></option>');
        if (sid != '0') {
            $('#studentDiv').show('slow');
            $.ajax({
                type: 'POST',
                url: "<?= base_url('invoicereport/getStudent') ?>",
                data: { classesID: cid, sectionID: sid },
                dataType: 'html',
                success: function (data) { $('#studentID').html(data); }
            });
        } else {
            $('#studentDiv').hide('slow');
        }
    });

    // Get report button
    $(document).on('click', '#get_invoicereport', function () {
        var passData = {
            classesID: $('#classesID').val(),
            sectionID: $('#classesID').val() != '0' ? $('#sectionID').val() : 0,
            studentID: $('#classesID').val() != '0' ? $('#studentID').val() : 0,
            feetypeID: $('#feetypeID').val()
        };

        $('#load_invoicereport').html(
            '<div style="text-align:center;padding:20px;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>'
        );

        $.ajax({
            type: 'POST',
            url: "<?= base_url('invoicereport/getInvoiceReport') ?>",
            data: passData,
            dataType: 'html',
            success: function (data) {
                var response = JSON.parse(data);
                if (response.status) {
                    $('#load_invoicereport').html(response.render);
                } else {
                    $('#load_invoicereport').html(
                        '<div class="alert alert-danger">No data found for the selected filters.</div>'
                    );
                }
            },
            error: function () {
                $('#load_invoicereport').html(
                    '<div class="alert alert-danger">An error occurred. Please try again.</div>'
                );
            }
        });
    });
</script>
