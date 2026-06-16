<style>
/* ── Admission Report: Student Popup Modal ── */
#adm-student-modal .modal-dialog { width: 880px; max-width: 96%; }
#adm-student-modal .modal-content {
    border-radius: 14px;
    border: none;
    box-shadow: 0 14px 52px rgba(0,0,0,0.24);
    overflow: hidden;
}
#adm-student-modal .modal-header {
    background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%) !important;
    border-bottom: none !important;
    padding: 18px 22px 14px !important;
    border-radius: 0;
}
#adm-student-modal .modal-header .close {
    color: #fff !important;
    opacity: 0.75 !important;
    font-size: 26px !important;
    font-weight: 300 !important;
    text-shadow: none !important;
    margin-top: 0 !important;
}
#adm-student-modal .modal-header .close:hover { opacity: 1 !important; }
#adm-student-modal .adm-modal-title {
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 10px;
    letter-spacing: 0.2px;
}
#adm-student-modal .adm-modal-chips { display: flex; gap: 8px; flex-wrap: wrap; }
#adm-student-modal .adm-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 13px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
}
#adm-student-modal .adm-chip--total { background: rgba(255,255,255,0.18); color: #fff;    border: 1px solid rgba(255,255,255,0.4); }
#adm-student-modal .adm-chip--boys  { background: rgba(100,181,246,0.28); color: #e3f2fd; border: 1px solid rgba(100,181,246,0.5); }
#adm-student-modal .adm-chip--girls { background: rgba(240,98,146,0.28);  color: #fce4ec; border: 1px solid rgba(240,98,146,0.5); }
#adm-student-modal .modal-body {
    padding: 0 !important;
    background: #f4f6fb !important;
    max-height: 70vh;
    overflow-y: auto;
}
/* Section cards */
#adm-student-modal .adm-popup-section {
    margin: 14px 14px 0;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.09);
    overflow: hidden;
}
#adm-student-modal .adm-popup-section:last-child { margin-bottom: 14px; }
#adm-student-modal .adm-popup-section-head {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 11px 16px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #37474f;
}
#adm-student-modal .adm-popup-badge {
    margin-left: auto;
    background: rgba(0,0,0,0.10);
    border-radius: 12px;
    padding: 2px 10px;
    font-size: 12px;
    font-weight: 700;
}
#adm-student-modal .adm-gender-boys   .adm-popup-section-head { background: linear-gradient(90deg, #bbdefb 0%, #e3f2fd 100%); color: #0d47a1; }
#adm-student-modal .adm-gender-girls  .adm-popup-section-head { background: linear-gradient(90deg, #f8bbd0 0%, #fce4ec 100%); color: #880e4f; }
#adm-student-modal .adm-gender-others .adm-popup-section-head { background: linear-gradient(90deg, #e1bee7 0%, #f3e5f5 100%); color: #4a148c; }
/* Table */
#adm-student-modal .adm-popup-table { margin-bottom: 0; border: none !important; }
#adm-student-modal .adm-popup-table thead th {
    background: #eceff1 !important;
    color: #37474f !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 9px 12px !important;
    text-align: center;
    border-bottom: 2px solid #cfd8dc !important;
    border-top: none !important;
    white-space: nowrap;
}
#adm-student-modal .adm-popup-table tbody td {
    font-size: 13px;
    vertical-align: middle;
    text-align: center;
    padding: 8px 12px;
    border-color: #eceff1 !important;
    color: #424242;
}
#adm-student-modal .adm-popup-table tbody tr:nth-child(even) td { background: #f9fafb; }
#adm-student-modal .adm-popup-table tbody tr:hover td { background: #e8f0fe !important; }
#adm-student-modal .adm-popup-photo {
    width: 42px; height: 42px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #b0bec5;
    display: block;
    margin: 0 auto;
}
#adm-student-modal .adm-popup-name { text-align: left !important; font-weight: 600 !important; color: #1a237e !important; }
#adm-student-modal .adm-loader-wrap { text-align: center; padding: 48px 0; color: #90a4ae; }
#adm-student-modal .modal-footer { background: #f5f5f5; border-top: 1px solid #e0e0e0; padding: 10px 20px; }
</style>

<div class="box no-print">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Admission Report</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url('dashboard/index')?>"><i class="fa fa-laptop"></i> Dashboard</a></li>
            <li><a href="<?=base_url('studentreport')?>">Student Report</a></li>
            <li class="active">Admission Report</li>
        </ol>
    </div>
    <div class="box-body">
        <div class="rpt-filter-card">
            <div class="rpt-filter-title"><i class="fa fa-filter"></i>&nbsp; Filter Options</div>
            <div class="row">
                <div class="form-group col-sm-3">
                    <label>Academic Year</label>
                    <select id="schoolyearID" name="schoolyearID" class="form-control select2">
                        <option value="0">All Years</option>
                        <?php if (customCompute($schoolyears)): foreach ($schoolyears as $sy): ?>
                        <option value="<?=$sy->schoolyearID?>"><?=$sy->schoolyear?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label>Class</label>
                    <?php
                    $arr = ['0' => 'All Classes'];
                    if (customCompute($classes)) {
                        foreach ($classes as $c) $arr[$c->classesID] = $c->classes;
                    }
                    echo form_dropdown('classesID', $arr, '0', "id='classesID' class='form-control select2'");
                    ?>
                </div>
                <div class="form-group col-sm-3">
                    <label>Admission Date From</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" id="fromdate" name="fromdate" class="form-control" placeholder="DD-MM-YYYY" autocomplete="off">
                    </div>
                </div>
                <div class="form-group col-sm-3">
                    <label>Admission Date To</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" id="todate" name="todate" class="form-control" placeholder="DD-MM-YYYY" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="rpt-filter-actions">
                <button id="get_admissionreport" class="btn btn-success rpt-filter-btn">
                    <i class="fa fa-bar-chart"></i> Generate Report
                </button>
            </div>
        </div>
    </div>
</div>

<div id="load_admissionreport"></div>

<!-- ── Student detail popup ─────────────────────────────────── -->
<div class="modal fade adm-modal" id="adm-student-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title adm-modal-title" id="adm-modal-title">
                    <i class="fa fa-users"></i> Students
                </h4>
                <div class="adm-modal-chips" id="adm-modal-stats"></div>
            </div>
            <div class="modal-body" id="adm-modal-body">
                <div class="adm-loader-wrap">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$('.select2').select2();
$('#fromdate, #todate').datepicker({ autoclose: true, format: 'dd-mm-yyyy' });

/* ── Generate Report ──────────────────────────────────────── */
$(document).on('click', '#get_admissionreport', function () {
    var btn = $(this);
    var field = {
        schoolyearID : $('#schoolyearID').val(),
        classesID    : $('#classesID').val(),
        fromdate     : $('#fromdate').val(),
        todate       : $('#todate').val()
    };
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');
    $.ajax({
        type     : 'POST',
        url      : '<?=base_url('admissionreport/getAdmissionReport')?>',
        data     : field,
        dataType : 'html',
        success  : function (data) {
            btn.prop('disabled', false).html('<i class="fa fa-bar-chart"></i> Generate Report');
            var response = JSON.parse(data);
            if (response.status) {
                $('#load_admissionreport').html(response.render);
                if ($.fn.DataTable.isDataTable('#admission-stats-table')) {
                    $('#admission-stats-table').DataTable().destroy();
                }
                var exportCols = { columns: ':not(:last-child)' };
                $('#admission-stats-table').DataTable({
                    dom      : 'Bfrtip',
                    buttons  : [
                        { extend: 'copy',  className: 'btn btn-default btn-sm', text: '<i class="fa fa-copy"></i> Copy',          exportOptions: exportCols },
                        { extend: 'csv',   className: 'btn btn-default btn-sm', text: '<i class="fa fa-file-text-o"></i> CSV',    exportOptions: exportCols, filename: 'admission_report' },
                        { extend: 'excel', className: 'btn btn-default btn-sm', text: '<i class="fa fa-file-excel-o"></i> Excel', exportOptions: exportCols, filename: 'admission_report' },
                        { extend: 'pdf',   className: 'btn btn-default btn-sm', text: '<i class="fa fa-file-pdf-o"></i> PDF',     exportOptions: exportCols, filename: 'admission_report', orientation: 'landscape' },
                        { text: '<i class="fa fa-print"></i> Print', className: 'btn btn-default btn-sm', action: function () { window.print(); } }
                    ],
                    columnDefs : [{ orderable: false, targets: 7 }],
                    paging     : false,
                    ordering   : true,
                    info       : true,
                    language   : { emptyTable: 'No admission data found.' }
                });
            }
        },
        error: function () {
            btn.prop('disabled', false).html('<i class="fa fa-bar-chart"></i> Generate Report');
        }
    });
});

/* ── View button → student popup ──────────────────────────── */
$(document).on('click', '.adm-view-btn', function (e) {
    e.stopPropagation();
    var $btn       = $(this);
    var yearID     = $btn.data('yearid');
    var classID    = $btn.data('classid');
    var yearLabel  = $btn.data('year');
    var classLabel = $btn.data('class');
    var total      = $btn.data('total');
    var boys       = $btn.data('boys');
    var girls      = $btn.data('girls');
    var fromdate   = $('#adm-filter-meta').data('fromdate') || '';
    var todate     = $('#adm-filter-meta').data('todate') || '';

    $('#adm-modal-title').html('<i class="fa fa-users"></i> ' + classLabel + ' &mdash; ' + yearLabel);
    $('#adm-modal-stats').html(
        '<span class="adm-chip adm-chip--total"><i class="fa fa-users"></i>&nbsp;' + total + '&nbsp;Total</span>' +
        '<span class="adm-chip adm-chip--boys"><i class="fa fa-male"></i>&nbsp;' + boys + '&nbsp;Boys</span>' +
        '<span class="adm-chip adm-chip--girls"><i class="fa fa-female"></i>&nbsp;' + girls + '&nbsp;Girls</span>'
    );
    $('#adm-modal-body').html(
        '<div class="adm-loader-wrap"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i>' +
        '<p class="text-muted" style="margin-top:8px;">Loading students...</p></div>'
    );
    $('#adm-student-modal').modal('show');

    $.ajax({
        type     : 'POST',
        url      : '<?=base_url('admissionreport/getStudentsForRow')?>',
        data     : { schoolyearID: yearID, classesID: classID, fromdate: fromdate, todate: todate },
        dataType : 'json',
        success  : function (resp) {
            if (!resp.status) {
                $('#adm-modal-body').html('<p class="text-center text-danger" style="padding:20px;">Failed to load students.</p>');
                return;
            }
            var html = '';
            if (resp.boys.length)   html += admBuildGenderBlock(resp.boys,   'Boys',   'adm-gender-boys');
            if (resp.girls.length)  html += admBuildGenderBlock(resp.girls,  'Girls',  'adm-gender-girls');
            if (resp.others.length) html += admBuildGenderBlock(resp.others, 'Others', 'adm-gender-others');
            if (!html) html = '<p class="text-center text-muted" style="padding:24px;">No students found.</p>';
            $('#adm-modal-body').html(html);
        }
    });
});

function admBuildGenderBlock(students, label, cls) {
    var iconCls = label === 'Boys' ? 'fa-male' : (label === 'Girls' ? 'fa-female' : 'fa-user');
    var html  = '<div class="adm-popup-section ' + cls + '">';
    html     += '<div class="adm-popup-section-head">';
    html     += '<i class="fa ' + iconCls + '"></i>&nbsp;' + label;
    html     += '<span class="adm-popup-badge">' + students.length + '</span>';
    html     += '</div>';
    html     += '<div class="table-responsive">';
    html     += '<table class="table adm-popup-table">';
    html     += '<thead><tr>';
    html     += '<th style="width:40px">#</th>';
    html     += '<th style="width:56px">Photo</th>';
    html     += '<th>Name</th>';
    html     += '<th>Reg. No.</th>';
    html     += '<th style="width:56px">Roll</th>';
    html     += '<th>Admission Date</th>';
    html     += '</tr></thead><tbody>';
    $.each(students, function (i, s) {
        html += '<tr>';
        html += '<td>' + (i + 1) + '</td>';
        html += '<td><img src="' + s.photo_url + '" class="adm-popup-photo" alt=""></td>';
        html += '<td class="adm-popup-name">' + s.srname + '</td>';
        html += '<td>' + (s.srregisterNO || '—') + '</td>';
        html += '<td>' + (s.srroll || '—') + '</td>';
        html += '<td>' + (s.admission_date || '—') + '</td>';
        html += '</tr>';
    });
    html += '</tbody></table></div></div>';
    return html;
}
</script>
