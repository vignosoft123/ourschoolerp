<style>
/* ── Admission Report: Stat Cards ── */
.adm-stats-row { margin: 16px 0 20px; }
.adm-stat-card {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 20px 18px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    border: 1px solid rgba(0,0,0,0.05);
}
.adm-stat-card__body { position: relative; z-index: 1; }
.adm-stat-card__value { font-size: 38px; font-weight: 700; line-height: 1; letter-spacing: -1px; }
.adm-stat-card__label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-top: 6px;
}
.adm-stat-card__label i { margin-right: 5px; }
.adm-stat-card__icon {
    font-size: 52px;
    opacity: 0.13;
    line-height: 1;
    position: relative;
    z-index: 0;
}
/* Blue — Total Admissions */
.adm-stat-card--blue { background: #e8f0fe; border-left: 4px solid #1a73e8; }
.adm-stat-card--blue .adm-stat-card__value { color: #1a237e; }
.adm-stat-card--blue .adm-stat-card__label { color: #1565c0; }
.adm-stat-card--blue .adm-stat-card__icon  { color: #1a73e8; }
/* Teal — Boys */
.adm-stat-card--teal { background: #e0f7fa; border-left: 4px solid #00838f; }
.adm-stat-card--teal .adm-stat-card__value { color: #004d40; }
.adm-stat-card--teal .adm-stat-card__label { color: #00838f; }
.adm-stat-card--teal .adm-stat-card__icon  { color: #00838f; }
/* Pink — Girls */
.adm-stat-card--pink { background: #fce4ec; border-left: 4px solid #c2185b; }
.adm-stat-card--pink .adm-stat-card__value { color: #880e4f; }
.adm-stat-card--pink .adm-stat-card__label { color: #c2185b; }
.adm-stat-card--pink .adm-stat-card__icon  { color: #c2185b; }
/* Growth indicators */
.adm-growth-pos { color: #2e7d32; font-weight: 700; }
.adm-growth-neg { color: #c62828; font-weight: 700; }
.adm-growth-na  { color: #9e9e9e; }
/* View button */
.adm-view-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 13px;
    background: linear-gradient(135deg, #1565c0, #1976d2);
    color: #fff; border: none; border-radius: 4px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    transition: all 0.18s ease; white-space: nowrap;
}
.adm-view-btn:hover {
    background: linear-gradient(135deg, #0d47a1, #1565c0);
    box-shadow: 0 3px 8px rgba(21,101,192,0.4);
    transform: translateY(-1px); color: #fff;
}
/* Grand total row */
.adm-total-row td { background: #f5f5f5 !important; font-weight: 600; }
</style>

<div class="box" style="border-top:3px solid #1565c0;">
    <div class="rpt-box-header adm-box-header">
        <h3><i class="fa fa-bar-chart"></i> Admission Reports
            <small class="adm-record-count"><?=count($rows)?> record<?=count($rows)!=1?'s':''?></small>
        </h3>
    </div>

    <div class="box-body" id="printablediv">
        <?= reportheader($siteinfos, $schoolyearsessionobj) ?>

        <!-- Summary stat cards -->
        <div class="row adm-stats-row">
            <div class="col-sm-4">
                <div class="adm-stat-card adm-stat-card--blue">
                    <div class="adm-stat-card__body">
                        <div class="adm-stat-card__value"><?=$totalStudents?></div>
                        <div class="adm-stat-card__label"><i class="fa fa-users"></i> Total Admissions</div>
                    </div>
                    <div class="adm-stat-card__icon"><i class="fa fa-users"></i></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="adm-stat-card adm-stat-card--teal">
                    <div class="adm-stat-card__body">
                        <div class="adm-stat-card__value"><?=$totalBoys?></div>
                        <div class="adm-stat-card__label"><i class="fa fa-male"></i> Boys</div>
                    </div>
                    <div class="adm-stat-card__icon"><i class="fa fa-male"></i></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="adm-stat-card adm-stat-card--pink">
                    <div class="adm-stat-card__body">
                        <div class="adm-stat-card__value"><?=$totalGirls?></div>
                        <div class="adm-stat-card__label"><i class="fa fa-female"></i> Girls</div>
                    </div>
                    <div class="adm-stat-card__icon"><i class="fa fa-female"></i></div>
                </div>
            </div>
        </div>

        <!-- Store active filter dates for the popup -->
        <div id="adm-filter-meta"
             data-fromdate="<?=htmlspecialchars($fromdate ?? '')?>"
             data-todate="<?=htmlspecialchars($todate ?? '')?>">
        </div>

        <!-- DataTable -->
        <div class="rpt-table-wrap rpt-table-wrap--compact">
            <table id="admission-stats-table" class="table table-bordered adm-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Academic Year</th>
                        <th>Class</th>
                        <th>Total Admissions</th>
                        <th>Boys</th>
                        <th>Girls</th>
                        <th>Growth (%)</th>
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (customCompute($rows)): $i = 1; foreach ($rows as $row): ?>
                    <tr>
                        <td><?=$i++?></td>
                        <td><?=htmlspecialchars($row->academic_year)?></td>
                        <td><?=htmlspecialchars($row->class_name)?></td>
                        <td><?=$row->total?></td>
                        <td><?=$row->boys?></td>
                        <td><?=$row->girls?></td>
                        <td>
                            <?php if ($row->growth !== null): ?>
                                <span class="adm-growth-<?=$row->growth >= 0 ? 'pos' : 'neg'?>">
                                    <?=$row->growth >= 0 ? '+' : ''?><?=$row->growth?>%
                                </span>
                            <?php else: ?>
                                <span class="adm-growth-na">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="no-print">
                            <button class="adm-view-btn"
                                    data-yearid="<?=$row->schoolyearID?>"
                                    data-classid="<?=$row->classesID?>"
                                    data-year="<?=htmlspecialchars($row->academic_year)?>"
                                    data-class="<?=htmlspecialchars($row->class_name)?>"
                                    data-total="<?=$row->total?>"
                                    data-boys="<?=$row->boys?>"
                                    data-girls="<?=$row->girls?>">
                                <i class="fa fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="8" class="text-center text-muted">No admission data found.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="adm-total-row">
                        <td colspan="3"><strong>Grand Total</strong></td>
                        <td><strong><?=$totalStudents?></strong></td>
                        <td><strong><?=$totalBoys?></strong></td>
                        <td><strong><?=$totalGirls?></strong></td>
                        <td>—</td>
                        <td class="no-print"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<button class="rpt-scroll-top-btn" id="scroll-to-top-btn" title="Back to top">&#8679;</button>
<script>
$(window).on('scroll', function () {
    $(this).scrollTop() > 200 ? $('#scroll-to-top-btn').fadeIn(300) : $('#scroll-to-top-btn').fadeOut(300);
});
$('#scroll-to-top-btn').on('click', function () {
    $('html, body').animate({ scrollTop: 0 }, 400);
});
</script>
