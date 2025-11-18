<div class="box">
    <div class="box-header">
        <h3 class="box-title">Study Certificate Report</h3>
    </div>
    <div class="box-body">
        <form class="form-horizontal" role="form" method="post" id="studyCertificateReportForm">
            <div class="form-group">
                <label for="classesID" class="col-sm-2 control-label">Class</label>
                <div class="col-sm-6">
                    <select name="classesID" id="classesID" class="form-control select2">
                        <option value="0">Please Select</option>
                        <?php if(customCompute($classes)) { foreach($classes as $class) { ?>
                            <option value="<?= $class->classesID ?>"><?= $class->classes ?></option>
                        <?php } } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="sectionID" class="col-sm-2 control-label">Section</label>
                <div class="col-sm-6">
                    <select name="sectionID" id="sectionID" class="form-control select2">
                        <option value="0">Please Select</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="studentID" class="col-sm-2 control-label">Student(s)</label>
                <div class="col-sm-6">
                    <select name="studentID[]" id="studentID" class="form-control select2" multiple>
                        <option value="0">Please Select</option>
                    </select>
                    <p class="help-block">Select one or more students</p>
                </div>
            </div>

            <div class="form-group">
                <label for="years_text" class="col-sm-2 control-label">During the years</label>
                <div class="col-sm-6">
                    <input type="text" name="years_text" id="years_text" class="form-control" placeholder="e.g. 2022-23 to 2024-25" />
                </div>
            </div>

            <div class="form-group">
                <label for="conduct_text" class="col-sm-2 control-label">Conduct is</label>
                <div class="col-sm-6">
                    <input type="text" name="conduct_text" id="conduct_text" class="form-control" placeholder="e.g. Good / Excellent / Satisfactory" />
                </div>
            </div>

            <div class="form-group">
                <label for="date_text" class="col-sm-2 control-label">Date</label>
                <div class="col-sm-6">
                    <input type="date" name="date_text" id="date_text" class="form-control" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-6">
                    <button type="button" id="getStudyCertificate" class="btn btn-success">Generate</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="loadstudycertificateResult"></div>

<script type="text/javascript">
(function() {
    function initSelect2() {
        if (typeof $.fn.select2 === 'function') {
            $('.select2').select2();
        }
    }
    initSelect2();

    $('#classesID').on('change', function() {
        var classesID = $(this).val();
        $('#sectionID').html('<option value="0">Please Select</option>');
        $('#studentID').html('<option value="0">Please Select</option>');
        if (parseInt(classesID, 10) > 0) {
            $.ajax({
                type: 'POST',
                url: '<?= base_url('studycertificatereport/getSection') ?>',
                data: { classesID: classesID },
                success: function(data) {
                    $('#sectionID').html(data);
                }
            });
        }
    });

    $('#sectionID').on('change', function() {
        var classesID = $('#classesID').val();
        var sectionID = $(this).val();
        $('#studentID').html('<option value="0">Please Select</option>');
        if (parseInt(classesID, 10) > 0 && parseInt(sectionID, 10) > 0) {
            $.ajax({
                type: 'POST',
                url: '<?= base_url('studycertificatereport/getStudent') ?>',
                data: { classesID: classesID, sectionID: sectionID },
                success: function(data) {
                    $('#studentID').html(data);
                }
            });
        }
    });

    $('#getStudyCertificate').on('click', function() {
        var form = $('#studyCertificateReportForm');
        var payload = {
            classesID: $('#classesID').val() || 0,
            sectionID: $('#sectionID').val() || 0,
            // Send as array
            'studentID[]': $('#studentID').val() || [],
            years_text: $('#years_text').val() || '',
            conduct_text: $('#conduct_text').val() || '',
            date_text: $('#date_text').val() || ''
        };

        $.ajax({
            type: 'POST',
            url: '<?= base_url('studycertificatereport/getStudyCertificateReport') ?>',
            data: payload,
            dataType: 'json',
            success: function(response) {
                if(response.status) {
                    $('#loadstudycertificateResult').html(response.render);
                } else {
                    // Show simple validation feedback
                    var messages = [];
                    $.each(response, function(k, v){ if(k !== 'status') messages.push(v); });
                    alert(messages.join('\n'));
                }
            },
            error: function() {
                alert('Failed to load study certificates.');
            }
        });
    });
})();
</script>
