<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-calendar-check-o"></i> Day Sheet Report</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url('dashboard/index')?>"><i class="fa fa-laptop"></i> Dashboard</a></li>
            <li class="active">Day Sheet</li>
        </ol>
    </div>
    <div class="box-body">

        <div class="rpt-filter-card">
            <div class="rpt-filter-title"><i class="fa fa-filter"></i>&nbsp; Select Date</div>
            <div class="row">
                <div class="form-group col-sm-4" id="dateDiv">
                    <label>Date</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" id="daysheet_date" name="date" class="form-control"
                               placeholder="dd-mm-yyyy" autocomplete="off"
                               value="<?=date('d-m-Y')?>">
                    </div>
                </div>
            </div>
            <div class="rpt-filter-actions">
                <button class="btn btn-success rpt-filter-btn" id="get_daysheetreport">
                    <i class="fa fa-calendar-check-o"></i> Load Day Sheet
                </button>
            </div>
        </div><!-- /.rpt-filter-card -->

    </div>
</div>

<div id="load_daysheetreport"></div>

<script>
$(function() {
    $('#daysheet_date').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy',
        startDate: '<?=$schoolyearsessionobj->startingdate?>',
        endDate: '<?=$schoolyearsessionobj->endingdate?>',
    });

    $('#get_daysheetreport').on('click', function() {
        var date = $('#daysheet_date').val();
        if (!date) { $('#dateDiv').addClass('has-error'); return; }
        $('#dateDiv').removeClass('has-error');
        loadDaySheet(date);
    });

    function loadDaySheet(date) {
        $('#load_daysheetreport').html('<div style="text-align:center;padding:30px;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        $.ajax({
            type: 'POST',
            url: '<?=base_url('daysheetreport/getDaysheetReport')?>',
            data: { date: date },
            dataType: 'json',
            success: function(resp) {
                if (!resp.status) {
                    $('#load_daysheetreport').html('<div class="alert alert-danger">Error loading day sheet.</div>');
                    return;
                }
                $('#load_daysheetreport').html(resp.render);
            },
            error: function() {
                $('#load_daysheetreport').html('<div class="alert alert-danger">Server error. Please try again.</div>');
            }
        });
    }

    // Print
    $(document).on('click', '#daysheet-print-btn', function() {
        window.print();
    });
});
</script>
