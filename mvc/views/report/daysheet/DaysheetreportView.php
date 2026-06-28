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

    // Export Excel
    $(document).on('click', '#daysheet-export-btn', function() {
        if (typeof dsCards === 'undefined') { alert('Please load the report first.'); return; }

        var date = $('#daysheet_date').val();
        function fa(n) { return parseFloat(n || 0).toFixed(2); }
        function td(v, s) { return '<td style="border:1px solid #bdc3c7;padding:5px 8px;font-size:10pt;font-family:Calibri,Arial;' + (s||'') + '">' + v + '</td>'; }
        function th(v, s) { return '<th style="border:1px solid #bdc3c7;padding:5px 8px;font-size:10pt;font-family:Calibri,Arial;font-weight:bold;' + (s||'') + '">' + v + '</th>'; }
        function sec(label, color, cols) {
            return '<tr><td colspan="' + cols + '" style="background:' + color + ';color:#fff;font-size:12pt;font-weight:bold;padding:8px 10px;border:1px solid #aaa;font-family:Calibri,Arial;">' + label + '</td></tr>';
        }
        function ts() { return '<table style="border-collapse:collapse;margin-bottom:14px;">'; }
        function te() { return '</table>'; }

        function acctData(name) {
            if (name === 'Cash')    return dsCards.cash;
            if (name === 'Digital') return dsCards.digital;
            if (name === 'Cheque')  return dsCards.cheque;
            if (name === 'Others')  return dsCards.others;
            var found = null;
            (dsCards.banks||[]).forEach(function(b) { if (b.name === name) found = b; });
            return found || {opening:0, received:0, spent:0, closing:0};
        }

        var DARK = 'background:#2c3e50;color:#fff;font-weight:bold;';
        var TR = 'text-align:right;';
        var TC = 'text-align:center;';

        var h = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">'
              + '<head><meta charset="UTF-8"></head><body>';

        // Title
        h += ts() + '<tr><td colspan="5" style="' + DARK + 'font-size:14pt;text-align:center;padding:10px;border:none;">'
           + 'Day Sheet Report &mdash; ' + date + '</td></tr>' + te();

        // Section 1: Opening Balance
        h += ts() + sec('1. Opening Balance', '#1b5e20', 2);
        h += '<tr>' + th('Account','background:#c8e6c9;color:#1b5e20;') + th('Opening Balance',TR+'background:#c8e6c9;color:#1b5e20;') + '</tr>';
        var accts = dsCards.accounts || ['Cash','Digital','Cheque','Others'];
        accts.forEach(function(a) {
            var d = acctData(a);
            h += '<tr>' + td(a,'background:#f1f8e9;') + td(fa(d.opening),TR+'background:#f1f8e9;') + '</tr>';
        });
        h += '<tr>' + td('<b>Total</b>','background:#c8e6c9;color:#1b5e20;font-weight:bold;') + td('<b>'+fa(dsCards.totalOpening)+'</b>',TR+'background:#c8e6c9;color:#1b5e20;font-weight:bold;') + '</tr>';
        h += te();

        // Section 2: Fee Collection
        h += ts() + sec('2. Today\'s Fee Collection (Payment Mode Wise)', '#0d47a1', 2);
        h += '<tr>' + th('Payment Mode','background:#bbdefb;color:#0d47a1;') + th('Amount',TR+'background:#bbdefb;color:#0d47a1;') + '</tr>';
        if (dsCards.feeByType.length) {
            dsCards.feeByType.forEach(function(r) {
                h += '<tr>' + td(r.label,'background:#e3f2fd;') + td(fa(r.total),TR+'background:#e3f2fd;color:#1565c0;') + '</tr>';
            });
        } else {
            h += '<tr>' + td('No fee collections.',TC+'background:#e3f2fd;color:#888;') + td('','background:#e3f2fd;') + '</tr>';
        }
        h += '<tr>' + td('<b>Total Collection</b>','background:#bbdefb;color:#0d47a1;font-weight:bold;') + td('<b>'+fa(dsCards.totalFeeCollection)+'</b>',TR+'background:#bbdefb;color:#0d47a1;font-weight:bold;') + '</tr>';
        h += te();

        // Section 3: Other Income
        h += ts() + sec('3. Other Income (Category Wise)', '#4a148c', 2);
        h += '<tr>' + th('Category','background:#e1bee7;color:#4a148c;') + th('Amount',TR+'background:#e1bee7;color:#4a148c;') + '</tr>';
        if (dsCards.incomeBycat.length) {
            dsCards.incomeBycat.forEach(function(r) {
                h += '<tr>' + td(r.category,'background:#f3e5f5;') + td(fa(r.total),TR+'background:#f3e5f5;color:#6a1b9a;') + '</tr>';
            });
        } else {
            h += '<tr>' + td('No other income recorded.',TC+'background:#f3e5f5;color:#888;') + td('','background:#f3e5f5;') + '</tr>';
        }
        h += '<tr>' + td('<b>Total Other Income</b>','background:#e1bee7;color:#4a148c;font-weight:bold;') + td('<b>'+fa(dsCards.totalOtherIncome)+'</b>',TR+'background:#e1bee7;color:#4a148c;font-weight:bold;') + '</tr>';
        h += te();

        // Section 4: Expenses Account Wise
        h += ts() + sec('4. Expenses (Account Wise)', '#b71c1c', 3);
        h += '<tr>' + th('Account','background:#ffcdd2;color:#b71c1c;') + th('Category / Item','background:#ffcdd2;color:#b71c1c;') + th('Amount',TR+'background:#ffcdd2;color:#b71c1c;') + '</tr>';
        var eByAcct = dsCards.expItemsByAcct || {};
        Object.keys(eByAcct).forEach(function(acct) {
            var items = eByAcct[acct];
            var acctTot = items.reduce(function(s,it){ return s+it.amount; }, 0);
            h += '<tr><td colspan="3" style="background:#ffebee;color:#c62828;font-weight:bold;border:1px solid #bdc3c7;padding:4px 8px;font-family:Calibri,Arial;font-size:10pt;">' + acct + ' Expenses</td></tr>';
            items.forEach(function(it) {
                h += '<tr>' + td(it.category||it.expense,'background:#fff5f5;') + td(it.expense,'background:#fff5f5;') + td(fa(it.amount),TR+'background:#fff5f5;color:#c62828;') + '</tr>';
            });
            h += '<tr>' + td('<b>Subtotal</b>','background:#ffcdd2;color:#c62828;font-weight:bold;') + td('','background:#ffcdd2;') + td('<b>'+fa(acctTot)+'</b>',TR+'background:#ffcdd2;color:#c62828;font-weight:bold;') + '</tr>';
        });
        if (dsCards.salaryTotal > 0) {
            h += '<tr><td colspan="3" style="background:#fff3cd;color:#856404;font-weight:bold;border:1px solid #bdc3c7;padding:4px 8px;font-family:Calibri,Arial;font-size:10pt;">Salary Paid (Payroll)</td></tr>';
            dsCards.salaryDetail.forEach(function(s) {
                h += '<tr>' + td(s.name,'background:#fffde7;') + td(s.method,TC+'background:#fffde7;') + td(fa(s.amount),TR+'background:#fffde7;color:#856404;') + '</tr>';
            });
            h += '<tr>' + td('<b>Total Salary</b>','background:#fff3cd;color:#856404;font-weight:bold;') + td('','background:#fff3cd;') + td('<b>'+fa(dsCards.salaryTotal)+'</b>',TR+'background:#fff3cd;color:#856404;font-weight:bold;') + '</tr>';
        }
        h += '<tr>' + td('<b>Grand Total Expenses</b>',DARK) + td('',DARK) + td('<b>'+fa(dsCards.totalExpenses)+'</b>',TR+DARK) + '</tr>';
        h += te();

        // Section 5: Today's Summary
        var totalIncome = dsCards.totalFeeCollection + dsCards.totalOtherIncome;
        var nc = dsCards.netCashFlow;
        h += ts() + sec('5. Today\'s Summary', '#e65100', 2);
        h += '<tr>' + td('Fee Collection','background:#fff8e1;') + td(fa(dsCards.totalFeeCollection),TR+'background:#fff8e1;color:#27ae60;') + '</tr>';
        h += '<tr>' + td('Other Income','background:#fff8e1;') + td(fa(dsCards.totalOtherIncome),TR+'background:#fff8e1;color:#8e44ad;') + '</tr>';
        h += '<tr>' + td('<b>Total Income</b>','background:#fff8e1;font-weight:bold;') + td('<b>'+fa(totalIncome)+'</b>',TR+'background:#fff8e1;color:#27ae60;font-weight:bold;') + '</tr>';
        h += '<tr>' + td('Total Expenses','background:#fff8e1;') + td(fa(dsCards.totalExpenses),TR+'background:#fff8e1;color:#e74c3c;') + '</tr>';
        h += '<tr>' + td('<b>Net Balance Today</b>','background:#ffe0b2;color:#e65100;font-weight:bold;') + td('<b>'+fa(Math.abs(nc))+(nc<0?' (Deficit)':'')+'</b>',TR+'background:#ffe0b2;font-weight:bold;color:'+(nc>=0?'#27ae60':'#e74c3c')+';') + '</tr>';
        h += te();

        // Section 6: Closing Balance
        h += ts() + sec('6. Closing Balance (Tomorrow\'s Opening)', '#004d40', 5);
        h += '<tr>' + th('Account','background:#b2dfdb;color:#004d40;') + th('Opening',TR+'background:#b2dfdb;color:#004d40;') + th('Received',TR+'background:#b2dfdb;color:#27ae60;') + th('Spent',TR+'background:#b2dfdb;color:#e74c3c;') + th('Closing Balance',TR+'background:#b2dfdb;color:#004d40;') + '</tr>';
        accts.forEach(function(a) {
            var d = acctData(a);
            h += '<tr>' + td(a,'background:#e0f2f1;') + td(fa(d.opening),TR+'background:#e0f2f1;') + td(fa(d.received),TR+'background:#e0f2f1;color:#27ae60;') + td(fa(d.spent),TR+'background:#e0f2f1;color:#e74c3c;') + td('<b>'+fa(d.closing)+'</b>',TR+'background:#e0f2f1;color:'+(d.closing>=0?'#1b5e20':'#c62828')+';font-weight:bold;') + '</tr>';
        });
        h += '<tr>' + td('<b>Total</b>',DARK) + td('<b>'+fa(dsCards.totalOpening)+'</b>',TR+DARK) + td('<b>'+fa(dsCards.totalReceived)+'</b>',TR+DARK) + td('<b>'+fa(dsCards.totalSpent)+'</b>',TR+DARK) + td('<b>'+fa(dsCards.totalClosing)+'</b>',TR+DARK) + '</tr>';
        h += te();

        // Section 7: Expense Category Wise
        h += ts() + sec('7. Expense Category Wise', '#bf360c', 2);
        h += '<tr>' + th('Category','background:#ffe0b2;color:#bf360c;') + th('Amount',TR+'background:#ffe0b2;color:#bf360c;') + '</tr>';
        var tot7 = 0;
        dsCards.expenseByCat.forEach(function(r) { tot7 += r.total; h += '<tr>' + td(r.category,'background:#fff3e0;') + td(fa(r.total),TR+'background:#fff3e0;color:#e67e22;') + '</tr>'; });
        if (dsCards.salaryTotal > 0) { tot7 += dsCards.salaryTotal; h += '<tr>' + td('Salary (Payroll)','background:#fffde7;') + td(fa(dsCards.salaryTotal),TR+'background:#fffde7;color:#e67e22;') + '</tr>'; }
        h += '<tr>' + td('<b>Total</b>','background:#ffe0b2;color:#bf360c;font-weight:bold;') + td('<b>'+fa(tot7)+'</b>',TR+'background:#ffe0b2;color:#bf360c;font-weight:bold;') + '</tr>';
        h += te();

        // Section 8: Collection Category Wise
        h += ts() + sec('8. Collection Category Wise (By Fee Type)', '#0d47a1', 2);
        h += '<tr>' + th('Fee Type','background:#bbdefb;color:#0d47a1;') + th('Amount',TR+'background:#bbdefb;color:#0d47a1;') + '</tr>';
        var tot8 = 0;
        if (dsCards.feeByFeetype && dsCards.feeByFeetype.length) {
            dsCards.feeByFeetype.forEach(function(r) { tot8 += r.total; h += '<tr>' + td(r.feetype,'background:#e3f2fd;') + td(fa(r.total),TR+'background:#e3f2fd;color:#1565c0;') + '</tr>'; });
        } else {
            h += '<tr>' + td('No fee collections recorded.',TC+'background:#e3f2fd;color:#888;') + td('','background:#e3f2fd;') + '</tr>';
        }
        h += '<tr>' + td('<b>Total</b>','background:#bbdefb;color:#0d47a1;font-weight:bold;') + td('<b>'+fa(tot8)+'</b>',TR+'background:#bbdefb;color:#0d47a1;font-weight:bold;') + '</tr>';
        h += te();

        h += '</body></html>';

        var blob = new Blob([h], { type: 'application/vnd.ms-excel;charset=utf-8;' });
        var url  = URL.createObjectURL(blob);
        var a    = document.createElement('a');
        a.href   = url;
        a.download = 'DaySheet_' + date.replace(/\//g, '-') + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
});
</script>
