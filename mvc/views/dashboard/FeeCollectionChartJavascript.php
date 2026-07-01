<script type="application/javascript">
$(function () {

    /* ── 1. Fee Collection Status Donut ─────────────────────────── */
    var feeStatusData = [
        { name: 'Collected',       y: <?= (float)($feeStatus['collected'] ?? 0) ?>, color: '#2ecc71' },
        { name: 'Discount/Waiver', y: <?= (float)($feeStatus['discount']  ?? 0) ?>, color: '#f39c12' },
        { name: 'Outstanding',     y: <?= (float)($feeStatus['due']       ?? 0) ?>, color: '#e74c3c' }
    ];
    var hasStatusData = feeStatusData.some(function(d){ return d.y > 0; });
    if (hasStatusData) {
        $('#feeStatusDonut').highcharts({
            chart: { type: 'pie', margin: [10, 10, 10, 10] },
            title: { text: null },
            tooltip: {
                pointFormat: '<b>{point.name}</b><br/>&#8377;{point.y:,.0f} ({point.percentage:.1f}%)'
            },
            plotOptions: {
                pie: {
                    innerSize: '55%',
                    dataLabels: { enabled: false },
                    showInLegend: true
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                itemStyle: { fontSize: '11px' }
            },
            credits: { enabled: false },
            exporting: { enabled: false },
            series: [{ name: 'Fee', data: feeStatusData }]
        });
    } else {
        $('#feeStatusDonut').html(
            '<div style="text-align:center; color:#bbb; padding:60px 0; font-size:13px;">' +
            '<i class="fa fa-info-circle"></i> No fee invoices yet</div>'
        );
    }

    /* ── 2. Class-wise Fee Collection Bar Chart ──────────────────── */
    var classCategories = [<?php
        if (customCompute($feeClassStats)) {
            foreach ($feeClassStats as $r) {
                echo "'" . addslashes($r->classes ?? 'Unknown') . "',";
            }
        }
    ?>];
    var classInvoiced   = [<?php
        if (customCompute($feeClassStats)) {
            foreach ($feeClassStats as $r) echo (float)$r->total_invoiced . ',';
        }
    ?>];
    var classCollected  = [<?php
        if (customCompute($feeClassStats)) {
            foreach ($feeClassStats as $r) echo (float)$r->total_collected . ',';
        }
    ?>];

    if (classCategories.length > 0) {
        $('#feeClassBarChart').highcharts({
            chart: { type: 'column' },
            title: { text: null },
            xAxis: { categories: classCategories, crosshair: true },
            yAxis: {
                min: 0,
                title: { text: 'Amount (&#8377;)' },
                labels: {
                    formatter: function () {
                        if (this.value >= 100000) return '&#8377;' + (this.value / 100000).toFixed(1) + 'L';
                        if (this.value >= 1000)   return '&#8377;' + (this.value / 1000).toFixed(0) + 'K';
                        return '&#8377;' + this.value;
                    }
                }
            },
            tooltip: {
                shared: true,
                formatter: function () {
                    var s = '<b>' + this.x + '</b><br/>';
                    $.each(this.points, function () {
                        s += this.series.name + ': <b>&#8377;' + Highcharts.numberFormat(this.y, 0) + '</b><br/>';
                    });
                    return s;
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.1,
                    borderWidth: 0,
                    borderRadius: 3,
                    dataLabels: { enabled: false }
                }
            },
            legend: { enabled: true },
            credits: { enabled: false },
            exporting: { enabled: false },
            series: [
                { name: 'Invoiced',   data: classInvoiced,   color: '#7eb5f5' },
                { name: 'Collected',  data: classCollected,  color: '#2ecc71' }
            ]
        });
    } else {
        $('#feeClassBarChart').html(
            '<div style="text-align:center; color:#bbb; padding:90px 0; font-size:13px;">' +
            '<i class="fa fa-info-circle"></i> No class fee data yet</div>'
        );
    }

    /* ── 3. Fee by Type Donut ────────────────────────────────────── */
    var feeTypeData = [<?php
        if (customCompute($feeTypeStats)) {
            foreach ($feeTypeStats as $r) {
                if ((float)$r->total > 0) {
                    echo "{ name: '" . addslashes($r->feetype ?? 'Other') . "', y: " . (float)$r->total . " },";
                }
            }
        }
    ?>];

    if (feeTypeData.length > 0) {
        $('#feeTypeDonut').highcharts({
            chart: { type: 'pie', margin: [10, 10, 30, 10] },
            title: { text: null },
            tooltip: {
                pointFormat: '<b>{point.name}</b><br/>&#8377;{point.y:,.0f} ({point.percentage:.1f}%)'
            },
            plotOptions: {
                pie: {
                    innerSize: '50%',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b><br/>{point.percentage:.0f}%',
                        style: { fontSize: '10px', fontWeight: 'normal' },
                        distance: 15
                    }
                }
            },
            credits: { enabled: false },
            exporting: { enabled: false },
            series: [{ name: 'Amount', data: feeTypeData }]
        });
    } else {
        $('#feeTypeDonut').html(
            '<div style="text-align:center; color:#bbb; padding:90px 0; font-size:13px;">' +
            '<i class="fa fa-info-circle"></i> No fee type data yet</div>'
        );
    }

});
</script>
