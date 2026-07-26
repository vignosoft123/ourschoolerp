<?php
    $pct = function($n, $total) {
        return $total > 0 ? round(($n / $total) * 100, 2) : 0;
    };
    $classExportUrl = base_url('schoolwisestrengthreport/export_excel').'?classesID='.$classesID.'&sectionID='.$sectionID;
    $classPdfUrl    = base_url('schoolwisestrengthreport/pdf/'.$classesID.'/'.$sectionID);
?>
<div class="rpt-action-bar">
    <a href="<?=$classPdfUrl?>" class="btn btn-primary rpt-action-btn" target="_blank">
        <i class="fa fa-print"></i> Print
    </a>
    <a href="<?=$classExportUrl?>" class="btn btn-success rpt-action-btn" target="_blank">
        <i class="fa fa-file-excel-o"></i> <?=$this->lang->line('schoolwisestrengthreport_export_excel')?>
    </a>
</div>

<div class="box" style="border-top: 3px solid #1565c0;">
    <div class="rpt-box-header">
        <h3><i class="fa fa-bar-chart"></i> <?=$this->lang->line('panel_title')?></h3>
    </div>

    <div id="printablediv" class="box-body">

        <?=reportheader($siteinfos, $schoolyear)?>

        <div class="row adm-stats-row">
            <div class="col-sm-3 col-xs-6">
                <div class="adm-stat-card adm-stat-card--blue">
                    <div class="adm-stat-card__value"><?=$totalStudents?></div>
                    <div class="adm-stat-card__label"><i class="fa fa-users"></i> Total Students</div>
                </div>
            </div>
            <div class="col-sm-3 col-xs-6">
                <div class="adm-stat-card adm-stat-card--green">
                    <div class="adm-stat-card__value"><?=$totalBoys?></div>
                    <div class="adm-stat-card__label"><i class="fa fa-male"></i> Total Boys (<?=$pct($totalBoys, $totalStudents)?>%)</div>
                </div>
            </div>
            <div class="col-sm-3 col-xs-6">
                <div class="adm-stat-card adm-stat-card--pink">
                    <div class="adm-stat-card__value"><?=$totalGirls?></div>
                    <div class="adm-stat-card__label"><i class="fa fa-female"></i> Total Girls (<?=$pct($totalGirls, $totalStudents)?>%)</div>
                </div>
            </div>
            <div class="col-sm-3 col-xs-6">
                <div class="adm-stat-card adm-stat-card--orange">
                    <div class="adm-stat-card__value"><?=$totalClasses?></div>
                    <div class="adm-stat-card__label"><i class="fa fa-graduation-cap"></i> Total Classes</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-7">
                <div class="sws-panel">
                    <div class="sws-panel-title"><i class="fa fa-users"></i> Class &amp; Section Wise Strength</div>
                    <?php if (customCompute($classAgg)) { ?>
                    <div class="rpt-table-wrap" id="rpt-wrap-sws-matrix">
                        <table class="sws-matrix-table">
                            <thead>
                                <tr>
                                    <th class="sws-hd-class" rowspan="2">Class</th>
                                    <th class="sws-hd-class" rowspan="2">Sections</th>
                                    <th class="sws-hd-boys" colspan="<?=count($sectionNameList) + 1?>">Boys</th>
                                    <th class="sws-hd-girls" colspan="<?=count($sectionNameList) + 1?>">Girls</th>
                                    <th class="sws-hd-total" colspan="<?=count($sectionNameList) + 1?>">Total Strength</th>
                                </tr>
                                <tr>
                                    <?php foreach ($sectionNameList as $secName) { ?><th class="sws-hd-boys"><?=$secName?></th><?php } ?>
                                    <th class="sws-hd-boys">Total</th>
                                    <?php foreach ($sectionNameList as $secName) { ?><th class="sws-hd-girls"><?=$secName?></th><?php } ?>
                                    <th class="sws-hd-girls">Total</th>
                                    <?php foreach ($sectionNameList as $secName) { ?><th class="sws-hd-total"><?=$secName?></th><?php } ?>
                                    <th class="sws-hd-total">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $grandBySection = [];
                                    $grandBoys = 0; $grandGirls = 0;
                                    foreach ($classAgg as $c) {
                                        $sectionCount = count($c['sectionIDs']);
                                ?>
                                <tr>
                                    <td><?=$c['classes']?></td>
                                    <td><?=$sectionCount?></td>
                                    <?php foreach ($sectionNameList as $secName) {
                                        $b = $c['sections'][$secName]['M'] ?? 0;
                                        $grandBySection[$secName]['M'] = ($grandBySection[$secName]['M'] ?? 0) + $b;
                                    ?><td><?=$b ?: '-'?></td><?php } ?>
                                    <td><?=$c['totalM']?></td>
                                    <?php foreach ($sectionNameList as $secName) {
                                        $g = $c['sections'][$secName]['F'] ?? 0;
                                        $grandBySection[$secName]['F'] = ($grandBySection[$secName]['F'] ?? 0) + $g;
                                    ?><td><?=$g ?: '-'?></td><?php } ?>
                                    <td><?=$c['totalF']?></td>
                                    <?php foreach ($sectionNameList as $secName) {
                                        $t = ($c['sections'][$secName]['M'] ?? 0) + ($c['sections'][$secName]['F'] ?? 0);
                                    ?><td><?=$t ?: '-'?></td><?php } ?>
                                    <td><?=$c['totalM'] + $c['totalF']?></td>
                                </tr>
                                <?php
                                        $grandBoys  += $c['totalM'];
                                        $grandGirls += $c['totalF'];
                                    }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>Grand Total</td>
                                    <td><?=$totalSections?></td>
                                    <?php foreach ($sectionNameList as $secName) { ?><td><?=$grandBySection[$secName]['M'] ?? 0?></td><?php } ?>
                                    <td><?=$grandBoys?></td>
                                    <?php foreach ($sectionNameList as $secName) { ?><td><?=$grandBySection[$secName]['F'] ?? 0?></td><?php } ?>
                                    <td><?=$grandGirls?></td>
                                    <?php foreach ($sectionNameList as $secName) { ?><td><?=($grandBySection[$secName]['M'] ?? 0) + ($grandBySection[$secName]['F'] ?? 0)?></td><?php } ?>
                                    <td><?=$grandBoys + $grandGirls?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php } else { ?>
                        <p class="text-muted">No students found for the selected filter.</p>
                    <?php } ?>
                </div>
            </div>

            <div class="col-sm-5">
                <div class="sws-panel">
                    <div class="sws-panel-title"><i class="fa fa-pie-chart"></i> Caste Wise Strength</div>
                    <?php if (customCompute($casteAgg)) { ?>
                    <div class="rpt-table-wrap">
                        <table class="sws-matrix-table">
                            <thead>
                                <tr>
                                    <th class="sws-hd-class">Caste Category</th>
                                    <th class="sws-hd-boys">Boys</th>
                                    <th class="sws-hd-girls">Girls</th>
                                    <th class="sws-hd-total">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($casteAgg as $label => $counts) {
                                    $ctotal = $counts['M'] + $counts['F'];
                                ?>
                                <tr>
                                    <td><?=$label?></td>
                                    <td><?=$counts['M']?></td>
                                    <td><?=$counts['F']?></td>
                                    <td><?=$ctotal?> (<?=$pct($ctotal, $totalStudents)?>%)</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>Total</td>
                                    <td><?=$totalBoys?></td>
                                    <td><?=$totalGirls?></td>
                                    <td><?=$totalStudents?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php } else { ?>
                        <p class="text-muted">No caste data found for the selected filter.</p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="sws-panel">
                    <div class="sws-panel-title"><i class="fa fa-tachometer"></i> Strength Summary</div>
                    <div id="sws_summary_chart" style="position:relative; height:220px;"></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="sws-panel">
                    <div class="sws-panel-title"><i class="fa fa-bar-chart"></i> Class Wise Total Strength</div>
                    <div id="sws_class_chart" style="height:220px;"></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="sws-panel">
                    <div class="sws-panel-title"><i class="fa fa-pie-chart"></i> Caste Wise Strength</div>
                    <div id="sws_caste_chart" style="height:220px;"></div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 text-center footerAll">
            <?=reportfooter($siteinfos, $schoolyear)?>
        </div>
    </div>
</div>

<button class="rpt-scroll-top-btn" id="sws-scroll-top-btn" title="Back to top">&#8679;</button>

<script>
(function() {
    var classNames = <?=json_encode(array_values(array_map(function($c){ return $c['classes']; }, $classAgg)))?>;
    var classTotals = <?=json_encode(array_values(array_map(function($c){ return $c['totalM'] + $c['totalF']; }, $classAgg)))?>;
    var casteLabels = <?=json_encode(array_values(array_keys($casteAgg)))?>;
    var casteTotals = <?=json_encode(array_values(array_map(function($c){ return $c['M'] + $c['F']; }, $casteAgg)))?>;
    var totalBoys  = <?=(int)$totalBoys?>;
    var totalGirls = <?=(int)$totalGirls?>;
    var totalStudents = <?=(int)$totalStudents?>;

    Highcharts.chart('sws_summary_chart', {
        chart: { type: 'pie' },
        title: { text: '' },
        credits: { enabled: false },
        tooltip: { pointFormat: '{series.name}: <b>{point.y}</b> ({point.percentage:.1f}%)' },
        plotOptions: {
            pie: {
                innerSize: '65%',
                dataLabels: { enabled: false },
                showInLegend: true
            }
        },
        series: [{
            name: 'Students',
            colorByPoint: true,
            colors: ['#1976d2', '#c2185b'],
            data: [
                { name: 'Total Boys', y: totalBoys },
                { name: 'Total Girls', y: totalGirls }
            ]
        }]
    });
    $('#sws_summary_chart').append('<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">'
        + '<div style="font-size:22px;font-weight:700;color:#263238;">' + totalStudents + '</div>'
        + '<div style="font-size:11px;color:#888;">Total</div></div>');

    Highcharts.chart('sws_class_chart', {
        chart: { type: 'column' },
        title: { text: '' },
        credits: { enabled: false },
        xAxis: { categories: classNames },
        yAxis: { title: { text: 'No. of Students' } },
        legend: { enabled: false },
        series: [{ name: 'Students', data: classTotals, color: '#7e57c2' }]
    });

    Highcharts.chart('sws_caste_chart', {
        chart: { type: 'pie' },
        title: { text: '' },
        credits: { enabled: false },
        tooltip: { pointFormat: '{series.name}: <b>{point.y}</b> ({point.percentage:.1f}%)' },
        plotOptions: {
            pie: { dataLabels: { enabled: true, format: '{point.percentage:.1f}%' } }
        },
        series: [{
            name: 'Students',
            colorByPoint: true,
            data: casteLabels.map(function(label, i) { return { name: label, y: casteTotals[i] }; })
        }]
    });
})();

$(window).on('scroll', function() {
    $(this).scrollTop() > 200
        ? $('#sws-scroll-top-btn').fadeIn(300)
        : $('#sws-scroll-top-btn').fadeOut(300);
});
$('#sws-scroll-top-btn').on('click', function() {
    $('html, body').animate({ scrollTop: 0 }, 400);
});
</script>
