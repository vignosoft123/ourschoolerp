<?php
$totalNew      = count($newStudents);
$totalPromoted = count($promotedStudents);
$totalAll      = $totalNew + $totalPromoted;
$yearLabel     = (isset($currentSchoolYear) && $currentSchoolYear) ? $currentSchoolYear->schoolyear : '';
$newPct        = $totalAll ? round($totalNew * 100 / $totalAll) : 0;
$promPct       = $totalAll ? round($totalPromoted * 100 / $totalAll) : 0;

// Build unique classes (sorted by ID) and sections for filter dropdowns
$allClasses  = []; // classesID => className
$allSections = []; // sectionName => sectionName
foreach (array_merge($newStudents, $promotedStudents) as $s) {
    if (!empty($s->srclasses))  $allClasses[(int)$s->srclassesID] = $s->srclasses;
    if (!empty($s->srsection))  $allSections[$s->srsection]       = $s->srsection;
}
ksort($allClasses);
ksort($allSections);
?>

<style>
/* ── Page wrapper ── */
.ys-wrap { padding: 0 4px; }

/* ── Top banner ── */
.ys-banner {
    background: linear-gradient(135deg, #1a3c5e 0%, #2563a8 100%);
    border-radius: 12px;
    padding: 22px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 24px;
    box-shadow: 0 4px 18px rgba(37,99,168,.3);
}
.ys-banner-title { color:#fff; font-size:20px; font-weight:700; letter-spacing:.3px; margin:0; }
.ys-banner-title i { margin-right:10px; opacity:.85; }
.ys-banner-year {
    background:rgba(255,255,255,.15); color:#fff;
    font-size:13px; font-weight:600;
    padding:6px 16px; border-radius:20px; border:1px solid rgba(255,255,255,.3);
}

/* ── Stat cards ── */
.ys-cards { display:flex; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
.ys-card {
    flex:1; min-width:180px; border-radius:12px; padding:20px 22px;
    display:flex; align-items:center; gap:18px;
    box-shadow:0 3px 12px rgba(0,0,0,.10);
    transition:transform .2s, box-shadow .2s;
}
.ys-card:hover { transform:translateY(-3px); box-shadow:0 7px 22px rgba(0,0,0,.15); }
.ys-card-new      { background:linear-gradient(135deg,#0891b2,#0e7490); }
.ys-card-promoted { background:linear-gradient(135deg,#16a34a,#15803d); }
.ys-card-total    { background:linear-gradient(135deg,#7c3aed,#6d28d9); }
.ys-card-icon {
    width:52px; height:52px; border-radius:50%;
    background:rgba(255,255,255,.2);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.ys-card-icon i { font-size:22px; color:#fff; }
.ys-card-info { color:#fff; }
.ys-card-num  { font-size:34px; font-weight:800; line-height:1; }
.ys-card-lbl  { font-size:13px; font-weight:600; opacity:.9; margin-top:3px; }
.ys-card-pct  {
    display:inline-block; margin-top:6px;
    background:rgba(255,255,255,.2); font-size:11px; font-weight:700;
    padding:2px 9px; border-radius:20px; color:#fff;
}

/* ── Filter bar ── */
.ys-filter-bar {
    background:#fff; border-radius:12px;
    padding:16px 22px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
    display:flex; align-items:flex-end; flex-wrap:wrap; gap:14px;
    margin-bottom:22px;
}
.ys-filter-bar-title {
    width:100%; font-size:12px; font-weight:700;
    text-transform:uppercase; letter-spacing:.6px; color:#64748b;
    margin-bottom:4px;
}
.ys-filter-group { display:flex; flex-direction:column; gap:5px; min-width:160px; }
.ys-filter-group label { font-size:12px; font-weight:600; color:#475569; margin:0; }
.ys-filter-select {
    padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;
    font-size:13px; color:#334155; background:#f8fafc;
    cursor:pointer; outline:none; transition:border-color .15s;
    min-width:160px;
}
.ys-filter-select:focus { border-color:#2563a8; background:#fff; }
.ys-filter-actions { display:flex; align-items:flex-end; gap:8px; margin-left:auto; }
.ys-filter-reset {
    padding:8px 18px; border-radius:8px; border:1px solid #e2e8f0;
    background:#f1f5f9; color:#64748b; font-size:13px; font-weight:600;
    cursor:pointer; transition:all .15s;
}
.ys-filter-reset:hover { background:#e2e8f0; color:#334155; }
.ys-filter-status {
    font-size:12px; color:#64748b; display:flex; align-items:center; gap:6px;
    padding:8px 14px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;
    white-space:nowrap;
}
.ys-filter-status .ys-active-dot {
    width:8px; height:8px; border-radius:50%; background:#e2e8f0;
}
.ys-filter-status.has-filter .ys-active-dot { background:#16a34a; }

/* ── Section panels ── */
.ys-panel {
    background:#fff; border-radius:12px;
    box-shadow:0 2px 12px rgba(0,0,0,.08);
    margin-bottom:28px; overflow:hidden;
}
.ys-panel-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:16px 22px; flex-wrap:wrap; gap:8px;
}
.ys-panel-header-new      { background:linear-gradient(90deg,#ecfeff,#cffafe); border-bottom:2px solid #0891b2; }
.ys-panel-header-promoted { background:linear-gradient(90deg,#f0fdf4,#dcfce7); border-bottom:2px solid #16a34a; }
.ys-panel-title { font-size:15px; font-weight:700; display:flex; align-items:center; gap:10px; }
.ys-panel-title-new i      { color:#0891b2; }
.ys-panel-title-promoted i { color:#16a34a; }
.ys-count-badge { font-size:12px; font-weight:700; padding:3px 12px; border-radius:20px; color:#fff; }
.ys-badge-new      { background:#0891b2; }
.ys-badge-promoted { background:#16a34a; }
.ys-panel-sub { font-size:12px; color:#64748b; }
.ys-panel-body { padding:18px 22px; }

/* ── Tables ── */
.ys-table { width:100%; border-collapse:collapse; }
.ys-table thead tr,
.ys-table thead tr th,
.ys-table thead th.sorting,
.ys-table thead th.sorting_asc,
.ys-table thead th.sorting_desc {
    background:#1a3c5e !important;
    color:#fff !important;
}
.ys-table thead th {
    padding:11px 14px !important; font-size:12px !important; font-weight:700 !important;
    text-transform:uppercase !important; letter-spacing:.5px !important;
    border-bottom:none !important; border-right:1px solid rgba(255,255,255,.1) !important;
    white-space:nowrap !important;
}
.ys-table thead th:last-child { border-right:none !important; }
/* DataTables sort arrow colours */
.ys-table thead .sorting:after,
.ys-table thead .sorting_asc:after,
.ys-table thead .sorting_desc:after { color:rgba(255,255,255,.5) !important; }
.ys-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .15s; }
.ys-table tbody tr:last-child { border-bottom:none; }
.ys-table tbody tr:hover { background:#f0f7ff; }
.ys-table td { padding:10px 14px; font-size:13px; color:#334155; vertical-align:middle; }
.ys-table td:first-child { color:#94a3b8; font-weight:600; width:42px; text-align:center; }
.ys-table img { width:32px; height:32px; border-radius:50%; object-fit:cover; }

/* ── Pills ── */
.cls-pill {
    display:inline-block; padding:3px 11px; border-radius:20px;
    font-size:11px; font-weight:700;
}
.cls-pill-prev { background:#fef3c7; color:#92400e; border:1px solid #fcd34d; }
.cls-pill-curr { background:#dcfce7; color:#166534; border:1px solid #86efac; }
.sex-pill {
    display:inline-block; padding:2px 10px; border-radius:20px;
    font-size:11px; font-weight:700;
}
.sex-pill-m { background:#dbeafe; color:#1e40af; border:1px solid #93c5fd; }
.sex-pill-f { background:#fce7f3; color:#9d174d; border:1px solid #f9a8d4; }
.sex-pill-o { background:#f3f4f6; color:#4b5563; border:1px solid #d1d5db; }
.promo-arrow { color:#94a3b8; font-size:12px; margin:0 4px; }

/* ── Empty state ── */
.ys-empty { text-align:center; padding:38px 20px; color:#94a3b8; font-size:14px; }
.ys-empty i { font-size:36px; display:block; margin-bottom:10px; opacity:.4; }

/* DataTables overrides */
.dataTables_wrapper .dataTables_filter input {
    border-radius:8px; border:1px solid #e2e8f0;
    padding:5px 12px; font-size:13px; margin-left:6px;
}
.dataTables_wrapper .dataTables_length select {
    border-radius:8px; border:1px solid #e2e8f0; padding:4px 8px; font-size:13px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button { border-radius:6px !important; }
.dataTables_wrapper .dataTables_info { font-size:12px; color:#94a3b8; }
</style>

<div class="ys-wrap">

    <!-- ── Banner ── -->
    <div class="ys-banner">
        <h1 class="ys-banner-title">
            <i class="fa fa-bar-chart"></i> Yearly Student Status
        </h1>
        <?php if ($yearLabel): ?>
        <span class="ys-banner-year"><i class="fa fa-calendar"></i> &nbsp;<?= htmlspecialchars($yearLabel) ?></span>
        <?php endif; ?>
    </div>

    <!-- ── Stat cards ── -->
    <div class="ys-cards">
        <div class="ys-card ys-card-new">
            <div class="ys-card-icon"><i class="fa fa-user-plus"></i></div>
            <div class="ys-card-info">
                <div class="ys-card-num"><?= $totalNew ?></div>
                <div class="ys-card-lbl">New Admissions</div>
                <span class="ys-card-pct"><?= $newPct ?>% of total</span>
            </div>
        </div>
        <div class="ys-card ys-card-promoted">
            <div class="ys-card-icon"><i class="fa fa-level-up"></i></div>
            <div class="ys-card-info">
                <div class="ys-card-num"><?= $totalPromoted ?></div>
                <div class="ys-card-lbl">Promoted Students</div>
                <span class="ys-card-pct"><?= $promPct ?>% of total</span>
            </div>
        </div>
        <div class="ys-card ys-card-total">
            <div class="ys-card-icon"><i class="fa fa-users"></i></div>
            <div class="ys-card-info">
                <div class="ys-card-num"><?= $totalAll ?></div>
                <div class="ys-card-lbl">Total Students</div>
                <span class="ys-card-pct"><?= $yearLabel ?></span>
            </div>
        </div>
    </div>

    <!-- ── Filter bar ── -->
    <div class="ys-filter-bar">
        <div class="ys-filter-bar-title"><i class="fa fa-filter"></i> &nbsp;Filter Students</div>

        <div class="ys-filter-group">
            <label for="filter-class"><i class="fa fa-graduation-cap"></i> Class</label>
            <select id="filter-class" class="ys-filter-select">
                <option value="">All Classes</option>
                <?php foreach ($allClasses as $cid => $cname): ?>
                <option value="<?= htmlspecialchars($cname) ?>"><?= htmlspecialchars($cname) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ys-filter-group">
            <label for="filter-section"><i class="fa fa-list-ul"></i> Section</label>
            <select id="filter-section" class="ys-filter-select">
                <option value="">All Sections</option>
                <?php foreach ($allSections as $sec): ?>
                <option value="<?= htmlspecialchars($sec) ?>"><?= htmlspecialchars($sec) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ys-filter-group">
            <label for="filter-sex"><i class="fa fa-venus-mars"></i> Gender</label>
            <select id="filter-sex" class="ys-filter-select">
                <option value="">All Genders</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>

        <div class="ys-filter-actions">
            <span class="ys-filter-status" id="filter-status">
                <span class="ys-active-dot"></span>
                <span id="filter-status-text">Showing all <?= $totalAll ?> students</span>
            </span>
            <button class="ys-filter-reset" id="filter-reset" onclick="resetFilters()">
                <i class="fa fa-times"></i> Reset
            </button>
        </div>
    </div>

    <!-- ── New Admissions panel ── -->
    <div class="ys-panel">
        <div class="ys-panel-header ys-panel-header-new">
            <div class="ys-panel-title ys-panel-title-new">
                <i class="fa fa-user-plus fa-lg"></i>
                New Admissions
                <span class="ys-count-badge ys-badge-new" id="badge-new"><?= $totalNew ?></span>
            </div>
            <span class="ys-panel-sub">Students enrolled for the first time this year</span>
        </div>
        <div class="ys-panel-body">
            <?php if (!empty($newStudents)): ?>
            <div class="table-responsive">
                <table id="tbl-new" class="ys-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Register No.</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Roll</th>
                            <th>Gender</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($newStudents as $s):
                            $sexRaw = strtolower(trim($s->sex ?? ''));
                            $sexLabel = $sexRaw === 'male' ? 'Male' : ($sexRaw === 'female' ? 'Female' : ucfirst($sexRaw ?: '—'));
                            $sexClass = $sexRaw === 'male' ? 'sex-pill-m' : ($sexRaw === 'female' ? 'sex-pill-f' : 'sex-pill-o');
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= profileimage($s->photo) ?></td>
                            <td><a href="<?=base_url('student/view/'.$s->srstudentID)?>" style="font-weight:600;color:#1e40af;"><?= htmlspecialchars($s->srname) ?></a></td>
                            <td style="color:#64748b;"><?= htmlspecialchars($s->srregisterNO) ?></td>
                            <td><span class="cls-pill cls-pill-curr"><?= htmlspecialchars($s->srclasses) ?></span></td>
                            <td><?= htmlspecialchars($s->srsection) ?></td>
                            <td style="font-weight:600;"><?= htmlspecialchars($s->srroll) ?></td>
                            <td><span class="sex-pill <?= $sexClass ?>"><?= $sexLabel ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="ys-empty"><i class="fa fa-user-plus"></i>No new admissions found for this academic year.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Promoted Students panel ── -->
    <div class="ys-panel">
        <div class="ys-panel-header ys-panel-header-promoted">
            <div class="ys-panel-title ys-panel-title-promoted">
                <i class="fa fa-level-up fa-lg"></i>
                Promoted Students
                <span class="ys-count-badge ys-badge-promoted" id="badge-promoted"><?= $totalPromoted ?></span>
            </div>
            <span class="ys-panel-sub">Students promoted from a previous academic year</span>
        </div>
        <div class="ys-panel-body">
            <?php if (!empty($promotedStudents)): ?>
            <div class="table-responsive">
                <table id="tbl-promoted" class="ys-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Register No.</th>
                            <th>Promotion</th>
                            <th>Section</th>
                            <th>Roll</th>
                            <th>Gender</th>
                            <th style="display:none;">CurrClass</th><!-- hidden: used for class filter -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($promotedStudents as $s):
                            $sexRaw = strtolower(trim($s->sex ?? ''));
                            $sexLabel = $sexRaw === 'male' ? 'Male' : ($sexRaw === 'female' ? 'Female' : ucfirst($sexRaw ?: '—'));
                            $sexClass = $sexRaw === 'male' ? 'sex-pill-m' : ($sexRaw === 'female' ? 'sex-pill-f' : 'sex-pill-o');
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= profileimage($s->photo) ?></td>
                            <td><a href="<?=base_url('student/view/'.$s->srstudentID)?>" style="font-weight:600;color:#1e40af;"><?= htmlspecialchars($s->srname) ?></a></td>
                            <td style="color:#64748b;"><?= htmlspecialchars($s->srregisterNO) ?></td>
                            <td>
                                <span class="cls-pill cls-pill-prev"><?= htmlspecialchars($s->prevClass) ?></span>
                                <span class="promo-arrow"><i class="fa fa-long-arrow-right"></i></span>
                                <span class="cls-pill cls-pill-curr"><?= htmlspecialchars($s->srclasses) ?></span>
                            </td>
                            <td><?= htmlspecialchars($s->srsection) ?></td>
                            <td style="font-weight:600;"><?= htmlspecialchars($s->srroll) ?></td>
                            <td><span class="sex-pill <?= $sexClass ?>"><?= $sexLabel ?></span></td>
                            <td style="display:none;"><?= htmlspecialchars($s->srclasses) ?></td><!-- hidden current class -->
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="ys-empty"><i class="fa fa-level-up"></i>No promoted students found for this academic year.</div>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /.ys-wrap -->

<script>
var dtNew, dtPromoted;
var TOTAL_NEW      = <?= $totalNew ?>;
var TOTAL_PROMOTED = <?= $totalPromoted ?>;

// New table: cols — 0:#, 1:photo, 2:name, 3:reg, 4:class, 5:section, 6:roll, 7:gender
// Promoted:  cols — 0:#, 1:photo, 2:name, 3:reg, 4:promo, 5:section, 6:roll, 7:gender, 8:currClass(hidden)

$(document).ready(function() {
    var dtOpts = {
        responsive: true,
        pageLength: 25,
        order: [],
        columnDefs: [{ orderable: false, targets: [1] }],
        language: {
            search: '',
            searchPlaceholder: 'Search...',
            lengthMenu: 'Show _MENU_ per page',
            info: 'Showing _START_–_END_ of _TOTAL_',
            infoEmpty: 'No entries to show',
            infoFiltered: '(filtered from _MAX_)',
            paginate: {
                previous: '<i class="fa fa-chevron-left"></i>',
                next:     '<i class="fa fa-chevron-right"></i>'
            }
        },
        drawCallback: updateBadges
    };

    <?php if (!empty($newStudents)): ?>
    dtNew = $('#tbl-new').DataTable($.extend(true, {}, dtOpts, {
        columnDefs: [
            { orderable: false, targets: [1] },
            { visible: true,    targets: [4, 5, 7] }  // class, section, gender visible
        ]
    }));
    <?php endif; ?>

    <?php if (!empty($promotedStudents)): ?>
    dtPromoted = $('#tbl-promoted').DataTable($.extend(true, {}, dtOpts, {
        columnDefs: [
            { orderable: false, targets: [1] },
            { visible: false,   targets: [8] }  // hidden current-class column
        ]
    }));
    <?php endif; ?>

    // ── Filter handlers ──
    $('#filter-class, #filter-section, #filter-sex').on('change', applyFilters);
});

function applyFilters() {
    var cls  = $('#filter-class').val();
    var sec  = $('#filter-section').val();
    var sex  = $('#filter-sex').val().toLowerCase();
    var esc  = $.fn.dataTable.util.escapeRegex;

    // New table: class=col4, section=col5, gender=col7
    if (dtNew) {
        dtNew
            .column(4).search(cls  ? '^' + esc(cls)  + '$' : '', true, false, true)
            .column(5).search(sec  ? '^' + esc(sec)  + '$' : '', true, false, true)
            .column(7).search(sex  ? '^' + esc(sex)  + '$' : '', true, false, true)
            .draw();
    }

    // Promoted table: currClass=col8(hidden), section=col5, gender=col7
    if (dtPromoted) {
        dtPromoted
            .column(8).search(cls  ? '^' + esc(cls)  + '$' : '', true, false, true)
            .column(5).search(sec  ? '^' + esc(sec)  + '$' : '', true, false, true)
            .column(7).search(sex  ? '^' + esc(sex)  + '$' : '', true, false, true)
            .draw();
    }

    // Update filter status indicator
    var hasFilter = cls || sec || sex;
    var $status = $('#filter-status');
    $status.toggleClass('has-filter', !!hasFilter);
}

function updateBadges() {
    var nNew  = dtNew      ? dtNew.rows({ search: 'applied' }).count()      : TOTAL_NEW;
    var nProm = dtPromoted ? dtPromoted.rows({ search: 'applied' }).count() : TOTAL_PROMOTED;
    var total = nNew + nProm;

    $('#badge-new').text(nNew);
    $('#badge-promoted').text(nProm);

    var hasFilter = $('#filter-class').val() || $('#filter-section').val() || $('#filter-sex').val();
    if (hasFilter) {
        $('#filter-status-text').text('Showing ' + total + ' of <?= $totalAll ?> students');
    } else {
        $('#filter-status-text').text('Showing all <?= $totalAll ?> students');
    }
}

function resetFilters() {
    $('#filter-class, #filter-section, #filter-sex').val('');
    applyFilters();
}
</script>
