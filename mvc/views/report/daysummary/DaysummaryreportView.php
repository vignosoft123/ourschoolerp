<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-table"></i> Daily Summary Report</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url('dashboard/index')?>"><i class="fa fa-laptop"></i> Dashboard</a></li>
            <li class="active">Daily Summary</li>
        </ol>
    </div>
    <div class="box-body">

        <div class="rpt-filter-card">
            <div class="rpt-filter-title"><i class="fa fa-filter"></i>&nbsp; Select Date</div>
            <div class="row">
                <div class="form-group col-sm-4" id="dsumDateDiv">
                    <label>Date</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" id="dsummary_date" name="date" class="form-control"
                               placeholder="dd-mm-yyyy" autocomplete="off"
                               value="<?=date('d-m-Y')?>">
                    </div>
                </div>
            </div>
            <div class="rpt-filter-actions">
                <button class="btn btn-success rpt-filter-btn" id="get_daysummaryreport">
                    <i class="fa fa-table"></i> Load Summary
                </button>
            </div>
        </div><!-- /.rpt-filter-card -->

    </div>
</div>

<div id="load_daysummaryreport"></div>

<script>
$(function() {
    $('#dsummary_date').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy',
        startDate: '<?=$schoolyearsessionobj->startingdate?>',
        endDate: '<?=$schoolyearsessionobj->endingdate?>',
    });

    $('#get_daysummaryreport').on('click', function() {
        var date = $('#dsummary_date').val();
        if (!date) { $('#dsumDateDiv').addClass('has-error'); return; }
        $('#dsumDateDiv').removeClass('has-error');
        loadSummary(date);
    });

    function loadSummary(date) {
        $('#load_daysummaryreport').html('<div style="text-align:center;padding:40px;"><i class="fa fa-spinner fa-spin fa-2x" style="color:#27ae60;"></i><br><small style="color:#888;margin-top:8px;display:block;">Loading...</small></div>');
        $.ajax({
            type: 'POST',
            url: '<?=base_url('daysummaryreport/getReport')?>',
            data: { date: date },
            dataType: 'json',
            success: function(resp) {
                if (!resp.status) {
                    $('#load_daysummaryreport').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Error loading report.</div>');
                    return;
                }
                $('#load_daysummaryreport').html(resp.render);
            },
            error: function() {
                $('#load_daysummaryreport').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Server error. Please try again.</div>');
            }
        });
    }

    $(document).on('click', '#dsummary-print-btn', function() {
        window.print();
    });

    $(document).on('click', '#dsummary-export-btn', function() {
        if (typeof dsmData === 'undefined') { alert('Please load the report first.'); return; }

        var date = $('#dsummary_date').val();
        function fa(n) { return parseFloat(n || 0).toFixed(2); }
        function td(v, s) { return '<td style="border:1px solid #bdc3c7;padding:5px 8px;font-size:10pt;font-family:Calibri,Arial;' + (s||'') + '">' + v + '</td>'; }
        function th(v, s) { return '<th style="border:1px solid #bdc3c7;padding:5px 8px;font-size:10pt;font-family:Calibri,Arial;font-weight:bold;' + (s||'') + '">' + v + '</th>'; }

        var DARK  = 'background:#2c3e50;color:#fff;font-weight:bold;';
        var BLUE  = 'background:#1a5276;color:#fff;font-weight:bold;';
        var LBLUE = 'background:#d6eaf8;color:#1a5276;font-weight:bold;';
        var TR    = 'text-align:right;';
        var TC    = 'text-align:center;';

        var h = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">'
              + '<head><meta charset="UTF-8"></head><body>';

        // Title
        h += '<table style="border-collapse:collapse;margin-bottom:10px;"><tr>'
           + '<td colspan="8" style="background:#2c3e50;color:#fff;font-size:14pt;font-weight:bold;'
           + 'text-align:center;padding:10px;font-family:Calibri,Arial;border:none;">'
           + 'Daily Summary Report &mdash; ' + date + '</td></tr></table>';

        // SUMMARY
        h += '<table style="border-collapse:collapse;margin-bottom:14px;">';
        h += '<tr><td colspan="2" style="' + BLUE + 'font-size:12pt;padding:7px 10px;border:1px solid #aaa;font-family:Calibri,Arial;">SUMMARY</td></tr>';
        h += '<tr>' + td('Opening Balance', LBLUE) + td(fa(dsmData.totalOpening),     TR + 'background:#eaf2ff;') + '</tr>';
        h += '<tr>' + td('Fee Collection',  LBLUE) + td(fa(dsmData.totalFeeReceipts), TR + 'background:#eafaf1;color:#1e8449;') + '</tr>';
        if (dsmData.totalOtherIncome > 0) {
        h += '<tr>' + td('Other Income',    LBLUE) + td(fa(dsmData.totalOtherIncome), TR + 'background:#f5eef8;color:#6c3483;') + '</tr>';
        }
        h += '<tr>' + td('Total Expenses',  LBLUE) + td(fa(dsmData.totalPayments),   TR + 'background:#fdedec;color:#c0392b;') + '</tr>';
        h += '<tr>' + td('Closing Balance', DARK)  + td(fa(dsmData.closingBalance),  TR + DARK) + '</tr>';
        h += '</table>';

        // TRANSACTION LEDGER
        h += '<table style="border-collapse:collapse;margin-bottom:14px;">';
        h += '<tr><td colspan="8" style="' + BLUE + 'font-size:12pt;padding:7px 10px;border:1px solid #aaa;font-family:Calibri,Arial;">TRANSACTION LEDGER</td></tr>';
        h += '<tr>' + th('Time',TC+LBLUE) + th('Type',LBLUE) + th('Student / Expense',LBLUE) + th('Category',LBLUE)
           + th('Payment Mode',TC+LBLUE) + th('Receipt (Rs)',TR+LBLUE) + th('Payment (Rs)',TR+LBLUE) + th('Balance (Rs)',TR+LBLUE) + '</tr>';

        // Opening row
        var OB = 'background:#d5d8dc;color:#333;';
        h += '<tr>' + td('&mdash;',TC+OB) + td('<b>Opening Balance</b>',OB) + td('&mdash;',TC+OB)
           + td('&mdash;',TC+OB) + td('&mdash;',TC+OB) + td('&mdash;',TC+OB) + td('&mdash;',TC+OB)
           + td('<b>' + fa(dsmData.totalOpening) + '</b>', TR + OB) + '</tr>';

        var tClr = { fee:{bg:'#d5f5e3',fg:'#1e8449'}, income:{bg:'#f5eef8',fg:'#6c3483'},
                     expense:{bg:'#fdedec',fg:'#c0392b'}, salary:{bg:'#fef9e7',fg:'#b7950b'} };

        var allTxn = dsmData.receipts.concat(dsmData.payments);
        allTxn.sort(function(a, b) { return a.sort_key - b.sort_key; });
        var bal = dsmData.totalOpening;
        allTxn.forEach(function(r) {
            bal += (r.receipt || 0) - (r.payment || 0);
            var c   = tClr[r.type] || { bg:'#fff', fg:'#333' };
            var rbg = 'background:' + c.bg + ';';
            var rfg = 'color:' + c.fg + ';';
            var bclr= bal >= 0 ? 'color:#1a5276;' : 'color:#c0392b;';
            h += '<tr>'
               + td(r.time,        TC + rbg)
               + td('<b>' + r.label + '</b>', rbg + rfg)
               + td(r.particular,  rbg)
               + td(r.category,    rbg)
               + td(r.mode,        TC + rbg)
               + td(r.receipt  > 0 ? fa(r.receipt)  : '', TR + rbg + rfg)
               + td(r.payment  > 0 ? fa(r.payment)  : '', TR + rbg + rfg)
               + td('<b>' + fa(bal) + '</b>', TR + rbg + bclr)
               + '</tr>';
        });

        // Total row
        h += '<tr>' + td('',DARK) + td('',DARK) + td('',DARK) + td('',DARK)
           + td('<b>TOTAL</b>', TC+DARK) + td('<b>'+fa(dsmData.totalReceipts)+'</b>',TR+DARK)
           + td('<b>'+fa(dsmData.totalPayments)+'</b>',TR+DARK)
           + td('<b>'+fa(dsmData.closingBalance)+'</b>',TR+DARK) + '</tr>';
        h += '</table>';

        // PAYMENT MODE SUMMARY
        var mBg = { Cash:'#fff9e6', Digital:'#f5eef8', Cheque:'#eaf2ff', Others:'#fdf2e9' };
        h += '<table style="border-collapse:collapse;">';
        h += '<tr><td colspan="4" style="' + BLUE + 'font-size:12pt;padding:7px 10px;border:1px solid #aaa;font-family:Calibri,Arial;">PAYMENT MODE SUMMARY</td></tr>';
        h += '<tr>' + th('Payment Mode',LBLUE) + th('Collection (Rs)',TR+LBLUE) + th('Expense (Rs)',TR+LBLUE) + th('Net Balance (Rs)',TR+LBLUE) + '</tr>';
        dsmData.modeSummary.forEach(function(m, i) {
            var bg  = 'background:' + (mBg[m.mode] || (i%2===0 ? '#fff' : '#f4f6f8')) + ';';
            var net = m.collection - m.payment;
            h += '<tr>'
               + td('<b>'+m.mode+'</b>', bg)
               + td(fa(m.collection), TR + bg + 'color:#1e8449;')
               + td(fa(m.payment),    TR + bg + 'color:#c0392b;')
               + td('<b>'+fa(net)+'</b>', TR + bg + (net >= 0 ? 'color:#1a5276;' : 'color:#c0392b;'))
               + '</tr>';
        });
        h += '</table></body></html>';

        var blob = new Blob([h], { type: 'application/vnd.ms-excel;charset=utf-8;' });
        var url  = URL.createObjectURL(blob);
        var a    = document.createElement('a');
        a.href   = url;
        a.download = 'DailySummary_' + date.replace(/\//g, '-') + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
});
</script>
