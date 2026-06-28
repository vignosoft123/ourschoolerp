<?php
function dsum_fmt($n) { return number_format((float)$n, 2); }

// Determine badge color per payment mode
function dsum_mode_color($mode) {
    $m = strtolower(trim($mode));
    if ($m === 'cash')    return '#27ae60';
    if ($m === 'digital') return '#8e44ad';
    if ($m === 'cheque')  return '#7f8c8d';
    if ($m === 'others')  return '#f39c12';
    return '#2980b9'; // bank names
}

// All unique modes that actually appear in this day's transactions
$allModes = [];
foreach ($transactions as $txn) {
    $allModes[$txn['mode']] = true;
}

$nextDate = date('d-m-Y', strtotime($date . ' +1 day'));
$displayDate = date('d F Y, l', strtotime($date));
?>

<style>
.dsm-card        { border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 12px rgba(0,0,0,0.07);transition:transform .15s,box-shadow .15s; }
.dsm-clickcard:hover { transform:translateY(-3px);box-shadow:0 6px 18px rgba(0,0,0,0.13); }
.dsm-card-icon   { width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.dsm-card-val    { font-size:20px;font-weight:800;line-height:1.1; }
.dsm-card-lbl    { font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.5px;margin-top:3px; }
.dsm-tabs        { margin:16px 0 0;border-bottom:2px solid #eee;padding-bottom:0; }
.dsm-tab         { display:inline-block;padding:7px 14px;border-radius:6px 6px 0 0;cursor:pointer;font-size:12px;font-weight:600;color:#666;border:1px solid transparent;border-bottom:none;margin-bottom:-2px;transition:all .15s; }
.dsm-tab:hover   { background:#f5f5f5;color:#333; }
.dsm-tab.active  { background:#fff;border-color:#ddd;border-bottom-color:#fff;color:#27ae60; }
.txn-badge       { display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:700;color:#fff; }
.txn-fee         { background:#27ae60; }
.txn-expense     { background:#e74c3c; }
.txn-income      { background:#8e44ad; }
.txn-salary      { background:#e67e22; }
.mode-pill       { display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;color:#fff; }
.dsm-tbl td,.dsm-tbl th { padding:7px 10px;font-size:12px;vertical-align:middle; }
.dsm-tbl thead th { background:#dde8f5 !important;color:#000000 !important;border-bottom:2px solid #a0b8d8 !important;font-weight:700 !important;opacity:1 !important; }
#dsummary-printable table thead th,
#dsummary-printable table thead td { color:#000000 !important;font-weight:700 !important;opacity:1 !important; }
.bal-cell        { font-weight:600; }
.dsm-sidebar-box { background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.07);margin-bottom:16px;overflow:hidden; }
.dsm-sidebar-hd  { padding:10px 14px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.8px; }
.dsm-sidebar-row { display:flex;justify-content:space-between;padding:8px 14px;border-top:1px solid #f0f0f0;font-size:13px; }
.dsm-closing-row { padding:10px 14px;border-top:2px solid #eee;display:flex;justify-content:space-between;align-items:center; }
@media print {
    .rpt-filter-card, .rpt-filter-actions, .dsm-tabs, #dsummary-print-btn { display:none !important; }
    .dsm-card { box-shadow:none; }
}
</style>

<div id="dsummary-printable">

<?= reportheader($siteinfos, $schoolyearsessionobj) ?>

<!-- ===== TOP BAR ===== -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
    <div>
        <h4 style="margin:0;font-size:15px;font-weight:600;color:#333;">Daily Summary</h4>
        <div style="font-size:13px;color:#888;margin-top:2px;"><?=$displayDate?></div>
    </div>
    <div style="display:flex;gap:8px;">
        <button id="dsummary-export-btn" class="btn btn-success btn-sm">
            <i class="fa fa-file-excel-o"></i> Export Excel
        </button>
        <button id="dsummary-print-btn" class="btn btn-default btn-sm">
            <i class="fa fa-print"></i> Print
        </button>
    </div>
</div>

<!-- ===== 4 SUMMARY CARDS ===== -->
<div class="row" style="margin-bottom:16px;">

    <div class="col-xs-6 col-sm-3" style="margin-bottom:12px;">
        <div class="dsm-card dsm-clickcard" data-card="opening" style="background:#eaf4ff;cursor:pointer;" title="Click for breakdown">
            <div class="dsm-card-icon" style="background:#c5e0ff;">
                <i class="fa fa-wallet" style="color:#2980b9;font-size:18px;"></i>
            </div>
            <div>
                <div class="dsm-card-val" style="color:#1a5276;">&#8377;<?=dsum_fmt($totalOpening)?></div>
                <div class="dsm-card-lbl">Opening Balance</div>
            </div>
        </div>
    </div>

    <div class="col-xs-6 col-sm-3" style="margin-bottom:12px;">
        <div class="dsm-card dsm-clickcard" data-card="receipts" style="background:#eafaf1;cursor:pointer;" title="Click for breakdown">
            <div class="dsm-card-icon" style="background:#c6efce;">
                <i class="fa fa-arrow-down" style="color:#27ae60;font-size:18px;"></i>
            </div>
            <div>
                <div class="dsm-card-val" style="color:#1e8449;">&#8377;<?=dsum_fmt($totalFeeReceipts)?></div>
                <div class="dsm-card-lbl">Fee Collection</div>
                <?php if ($totalOtherIncome > 0): ?>
                <div style="font-size:10px;color:#27ae60;margin-top:3px;">
                    + &#8377;<?=dsum_fmt($totalOtherIncome)?> Other Income
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-xs-6 col-sm-3" style="margin-bottom:12px;">
        <div class="dsm-card dsm-clickcard" data-card="payments" style="background:#fef9f9;cursor:pointer;" title="Click for breakdown">
            <div class="dsm-card-icon" style="background:#f9d4d4;">
                <i class="fa fa-arrow-up" style="color:#e74c3c;font-size:18px;"></i>
            </div>
            <div>
                <div class="dsm-card-val" style="color:#c0392b;">&#8377;<?=dsum_fmt($totalPayments)?></div>
                <div class="dsm-card-lbl">Total Expenses</div>
            </div>
        </div>
    </div>

    <div class="col-xs-6 col-sm-3" style="margin-bottom:12px;">
        <div class="dsm-card dsm-clickcard" data-card="closing" style="background:<?=($closingBalance>=0?'#eaf4ff':'#fff5f5')?>;cursor:pointer;" title="Click for breakdown">
            <div class="dsm-card-icon" style="background:<?=($closingBalance>=0?'#c5e0ff':'#f9d4d4')?>;">
                <i class="fa fa-university" style="color:<?=($closingBalance>=0?'#2980b9':'#e74c3c')?>;font-size:18px;"></i>
            </div>
            <div>
                <div class="dsm-card-val" style="color:<?=($closingBalance>=0?'#1a5276':'#c0392b')?>;">&#8377;<?=dsum_fmt($closingBalance)?></div>
                <div class="dsm-card-lbl">Closing Balance</div>
            </div>
        </div>
    </div>

</div><!-- /.row cards -->

<!-- ===== CARD POPUP MODAL ===== -->
<div class="modal fade" id="dsm-card-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" id="dsm-modal-header" style="padding:12px 18px;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.9;font-size:22px;">&times;</button>
                <h4 class="modal-title" id="dsm-modal-title" style="font-weight:700;color:#fff;font-size:15px;"></h4>
            </div>
            <div class="modal-body" style="padding:0;" id="dsm-modal-body"></div>
        </div>
    </div>
</div>

<!-- ===== FILTER TABS ===== -->
<div class="dsm-tabs">
    <span class="dsm-tab active" data-mode="all">All Transactions</span>
    <?php foreach ($tabModes as $tm): ?>
    <?php if (isset($allModes[$tm])): // only show tabs that have data ?>
    <span class="dsm-tab" data-mode="<?=htmlspecialchars($tm)?>"><?=htmlspecialchars($tm==='Digital'?'Digital (UPI)':$tm)?></span>
    <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- ===== MAIN CONTENT ===== -->
<div class="row" style="margin-top:0;">

    <!-- LEFT: Ledger table -->
    <div class="col-sm-8">
        <div style="overflow-x:auto;margin-top:4px;">
        <table class="table table-bordered dsm-tbl" id="dsm-ledger-table" style="margin:0;background:#fff;">
            <thead>
                <tr>
                    <th style="width:70px;">Time</th>
                    <th>Particular</th>
                    <th>Student / Expense</th>
                    <th>Category</th>
                    <th>Payment Mode</th>
                    <th class="text-right" style="color:#27ae60;">Receipt (&#8377;)</th>
                    <th class="text-right" style="color:#e74c3c;">Payment (&#8377;)</th>
                    <th class="text-right">Balance (&#8377;)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Opening Balance row -->
                <tr class="txn-opening" style="background:#f0f8ff;">
                    <td style="color:#888;">—</td>
                    <td style="font-weight:700;color:#1a5276;">Opening Balance</td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                    <td class="text-right">—</td>
                    <td class="text-right">—</td>
                    <td class="text-right bal-cell" style="color:#2980b9;">&#8377;<?=dsum_fmt($totalOpening)?></td>
                </tr>

                <?php foreach ($transactions as $txn):
                    $modeColor = dsum_mode_color($txn['mode']);
                    $modeDisplay = $txn['mode'] === 'Digital' ? 'Digital (UPI)' : $txn['mode'];
                ?>
                <tr class="txn-row txn-type-<?=htmlspecialchars($txn['type'])?>"
                    data-mode="<?=htmlspecialchars($txn['mode'])?>"
                    data-receipt="<?=(float)$txn['receipt']?>"
                    data-payment="<?=(float)$txn['payment']?>">
                    <td style="color:#888;white-space:nowrap;"><?=htmlspecialchars($txn['time'])?></td>
                    <td>
                        <span class="txn-badge txn-<?=htmlspecialchars($txn['type'])?>"><?=htmlspecialchars($txn['label'])?></span>
                    </td>
                    <td><?=htmlspecialchars($txn['particular'])?></td>
                    <td style="color:#555;"><?=htmlspecialchars($txn['category'])?></td>
                    <td>
                        <span class="mode-pill" style="background:<?=$modeColor?>;"><?=htmlspecialchars($modeDisplay)?></span>
                    </td>
                    <td class="text-right" style="color:<?=($txn['receipt']>0?'#27ae60':'#bbb')?>;">
                        <?=($txn['receipt']>0 ? '&#8377;'.dsum_fmt($txn['receipt']) : '—')?>
                    </td>
                    <td class="text-right" style="color:<?=($txn['payment']>0?'#e74c3c':'#bbb')?>;">
                        <?=($txn['payment']>0 ? '&#8377;'.dsum_fmt($txn['payment']) : '—')?>
                    </td>
                    <td class="text-right bal-cell" style="color:<?=($txn['balance']>=0?'#1a5276':'#c0392b');?>;">
                        &#8377;<?=dsum_fmt($txn['balance'])?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:#f0f8ff;font-weight:700;">
                    <td colspan="5" style="color:#1a5276;">Total</td>
                    <td class="text-right" style="color:#27ae60;">&#8377;<?=dsum_fmt($totalReceipts)?></td>
                    <td class="text-right" style="color:#e74c3c;">&#8377;<?=dsum_fmt($totalPayments)?></td>
                    <td class="text-right" id="dsm-closing-cell" style="color:<?=($closingBalance>=0?'#1a5276':'#c0392b');?>;font-size:14px;">
                        &#8377;<?=dsum_fmt($closingBalance)?>
                    </td>
                </tr>
            </tfoot>
        </table>
        </div>

        <div style="margin-top:12px;padding:10px 14px;background:#f0f8ff;border-radius:6px;font-size:12px;color:#555;">
            <i class="fa fa-info-circle" style="color:#2980b9;"></i>
            <strong>Note:</strong> Closing Balance of today will be the opening balance of tomorrow (<?=$nextDate?>).
        </div>
    </div><!-- /.col-sm-8 -->

    <!-- RIGHT: Sidebar -->
    <div class="col-sm-4">

        <!-- Day Summary -->
        <div class="dsm-sidebar-box">
            <div class="dsm-sidebar-hd" style="background:#eaf4ff;color:#1a5276;">
                <i class="fa fa-bar-chart"></i> Day Summary
            </div>
            <div class="dsm-sidebar-row">
                <span style="color:#555;">Opening Balance</span>
                <strong style="color:#2980b9;">&#8377;<?=dsum_fmt($totalOpening)?></strong>
            </div>
            <div class="dsm-sidebar-row">
                <span style="color:#555;">Fee Collection</span>
                <strong style="color:#27ae60;">&#8377;<?=dsum_fmt($totalFeeReceipts)?></strong>
            </div>
            <?php if ($totalOtherIncome > 0): ?>
            <div class="dsm-sidebar-row" style="background:#fdf5ff;">
                <span style="color:#8e44ad;">Other Income</span>
                <strong style="color:#8e44ad;">&#8377;<?=dsum_fmt($totalOtherIncome)?></strong>
            </div>
            <?php endif; ?>
            <div class="dsm-sidebar-row">
                <span style="color:#555;">Total Expenses</span>
                <strong style="color:#e74c3c;">&#8377;<?=dsum_fmt($totalPayments)?></strong>
            </div>
            <div class="dsm-closing-row" style="background:<?=($closingBalance>=0?'#eaf4ff':'#fff0f0')?>;">
                <span style="font-size:13px;font-weight:700;color:#1a5276;">Closing Balance</span>
                <span style="font-size:18px;font-weight:800;color:<?=($closingBalance>=0?'#1a5276':'#c0392b')?>;">
                    &#8377;<?=dsum_fmt($closingBalance)?>
                </span>
            </div>
        </div>

        <!-- Payment Mode Summary -->
        <div class="dsm-sidebar-box">
            <div class="dsm-sidebar-hd" style="background:#f9f9f9;color:#333;">
                <i class="fa fa-credit-card"></i> Payment Mode Summary
            </div>
            <div style="overflow-x:auto;">
            <table class="table table-bordered" style="margin:0;font-size:12px;">
                <thead>
                    <tr style="background:#dde8f5;">
                        <th style="color:#000;font-weight:700;">Payment Mode</th>
                        <th class="text-right" style="color:#000;font-weight:700;">Collection</th>
                        <th class="text-right" style="color:#000;font-weight:700;">Expense</th>
                        <th class="text-right" style="color:#000;font-weight:700;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Show all tab modes in sidebar, even those with 0
                    $allSidebarModes = array_unique(array_merge(
                        ['Cash', 'Digital', 'Cheque'],
                        customCompute($banks) ? array_map(fn($b) => $b->bank_name, $banks) : [],
                        ['Others'],
                        array_keys($modeSummary)
                    ));
                    foreach ($allSidebarModes as $sm):
                        $col = (float)($modeSummary[$sm]['collection'] ?? 0);
                        $exp = (float)($modeSummary[$sm]['payment']    ?? 0);
                        $bal = $col - $exp;
                        $mColor = dsum_mode_color($sm);
                    ?>
                    <tr>
                        <td>
                            <span class="mode-pill" style="background:<?=$mColor?>;font-size:10px;">
                                <?=htmlspecialchars($sm==='Digital'?'UPI / Digital':$sm)?>
                            </span>
                        </td>
                        <td class="text-right" style="color:<?=($col>0?'#27ae60':'#999')?>;">
                            &#8377;<?=dsum_fmt($col)?>
                        </td>
                        <td class="text-right" style="color:<?=($exp>0?'#e74c3c':'#999')?>;">
                            &#8377;<?=dsum_fmt($exp)?>
                        </td>
                        <td class="text-right" style="font-weight:600;color:<?=($bal<0?'#c0392b':($bal>0?'#27ae60':'#999'))?>;">
                            <?=($bal<0?'<span style="color:#c0392b;">':'')?>&#8377;<?=dsum_fmt(abs($bal))?><?=($bal<0?'</span>':'')?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>

    </div><!-- /.col-sm-4 -->

</div><!-- /.row -->


</div><!-- /#dsummary-printable -->

<script>
var dsmOpeningBalance = <?=(float)$totalOpening?>;

function dsmFmt(n) {
    return '&#8377;' + parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function dsmRecomputeBalance() {
    var bal = dsmOpeningBalance;
    $('.txn-opening .bal-cell').html(dsmFmt(bal));
    $('.txn-row:visible').each(function() {
        var receipt = parseFloat($(this).data('receipt')) || 0;
        var payment = parseFloat($(this).data('payment')) || 0;
        bal += receipt - payment;
        var color = bal >= 0 ? '#1a5276' : '#c0392b';
        $(this).find('.bal-cell').html('<span style="color:' + color + ';">' + dsmFmt(bal) + '</span>');
    });
    var closColor = bal >= 0 ? '#1a5276' : '#c0392b';
    $('#dsm-closing-cell').html('<span style="color:' + closColor + ';">' + dsmFmt(bal) + '</span>');
}

// ---- Card popup data ----
var dsmData = {
    opening: <?=json_encode(array_map(function($acct, $amt){ return ['account'=>$acct,'amount'=>(float)$amt]; }, array_keys($openingByAccount), array_values($openingByAccount))) ?>,
    receipts: <?=json_encode(array_values(array_filter($transactions, function($t){ return $t['receipt'] > 0; }))) ?>,
    payments: <?=json_encode(array_values(array_filter($transactions, function($t){ return $t['payment'] > 0; }))) ?>,
    modeSummary: <?=json_encode(array_map(function($mode, $d){ return ['mode'=>$mode,'collection'=>$d['collection'],'payment'=>$d['payment']]; }, array_keys($modeSummary), array_values($modeSummary))) ?>,
    totalOpening:      <?=(float)$totalOpening ?>,
    totalFeeReceipts:  <?=(float)$totalFeeReceipts ?>,
    totalOtherIncome:  <?=(float)$totalOtherIncome ?>,
    totalReceipts:     <?=(float)$totalReceipts ?>,
    totalPayments:     <?=(float)$totalPayments ?>,
    closingBalance:    <?=(float)$closingBalance ?>
};

function dsmShowModal(title, headerBg, bodyHtml) {
    $('#dsm-modal-header').css('background', headerBg);
    $('#dsm-modal-title').html(title);
    $('#dsm-modal-body').html(bodyHtml);
    $('#dsm-card-modal').modal('show');
}

function dsmPopupTable(heads, rows, footRow, accentColor) {
    var t = '<div style="overflow-x:auto;"><table class="table table-bordered" style="margin:0;font-size:13px;">';
    t += '<thead><tr>';
    heads.forEach(function(h, i) {
        t += '<th style="background:#dde8f5;color:#000;font-weight:700;padding:8px 10px;' + (i > 0 ? 'text-align:right;' : '') + '">' + h + '</th>';
    });
    t += '</tr></thead><tbody>';
    if (!rows.length) {
        t += '<tr><td colspan="' + heads.length + '" style="text-align:center;color:#888;padding:16px;">No data</td></tr>';
    } else {
        rows.forEach(function(r) {
            t += '<tr>';
            r.forEach(function(c, i) {
                t += '<td style="padding:7px 10px;' + (i > 0 ? 'text-align:right;' : '') + '">' + c + '</td>';
            });
            t += '</tr>';
        });
    }
    t += '</tbody>';
    if (footRow) {
        t += '<tfoot><tr style="font-weight:700;background:#f0f0f0;">';
        footRow.forEach(function(c, i) {
            t += '<td style="padding:8px 10px;' + (i > 0 ? 'text-align:right;color:' + accentColor + ';' : '') + '">' + c + '</td>';
        });
        t += '</tr></tfoot>';
    }
    t += '</table></div>';
    return t;
}

$(document).on('click', '.dsm-clickcard', function() {
    var card = $(this).data('card');
    var body = '';

    if (card === 'opening') {
        var rows = dsmData.opening.map(function(r) {
            return [r.account, dsmFmt(r.amount)];
        });
        body = dsmPopupTable(['Account', 'Opening Balance'], rows, ['Total', dsmFmt(dsmData.totalOpening)], '#2980b9');
        dsmShowModal('<i class="fa fa-wallet"></i> Opening Balance — Account Wise', '#2980b9', body);

    } else if (card === 'receipts') {
        var feeRows = dsmData.receipts.filter(function(r){ return r.type === 'fee'; });
        var incRows = dsmData.receipts.filter(function(r){ return r.type === 'income'; });

        // Fee Collection section
        body += '<div style="padding:8px 14px;background:#e8f8f0;font-size:12px;font-weight:700;color:#1e8449;border-bottom:1px solid #c8e6d4;">'
              + '<i class="fa fa-inr"></i> Fee Collection</div>';
        var fRows = feeRows.map(function(r){
            return [r.time, r.particular, r.category, r.mode, dsmFmt(r.receipt)];
        });
        body += dsmPopupTable(['Time','Student','Category','Mode','Amount (₹)'], fRows,
            ['','','','Fee Total', dsmFmt(dsmData.totalFeeReceipts)], '#27ae60');

        // Other Income section (only if any)
        if (incRows.length > 0) {
            body += '<div style="padding:8px 14px;background:#f3e5f5;font-size:12px;font-weight:700;color:#6a1b9a;border-top:2px solid #ddd;border-bottom:1px solid #e1bee7;">'
                  + '<i class="fa fa-plus-circle"></i> Other Income</div>';
            var iRows = incRows.map(function(r){
                return [r.time, r.particular, r.category, r.mode, dsmFmt(r.receipt)];
            });
            body += dsmPopupTable(['Time','Description','Category','Mode','Amount (₹)'], iRows,
                ['','','','Income Total', dsmFmt(dsmData.totalOtherIncome)], '#8e44ad');
        }

        // Grand total footer
        body += '<div style="display:flex;justify-content:space-between;align-items:center;background:#eafaf1;padding:10px 14px;font-weight:700;border-top:2px solid #a9dfbf;">'
              + '<span style="color:#1e8449;">Total Receipts (Fees + Other Income)</span>'
              + '<span style="color:#1e8449;font-size:15px;">' + dsmFmt(dsmData.totalReceipts) + '</span></div>';
        dsmShowModal('<i class="fa fa-arrow-down"></i> Fee Collection &amp; Other Income', '#27ae60', body);

    } else if (card === 'payments') {
        var rows = dsmData.payments.map(function(r) {
            var color = r.type === 'expense' ? '#e74c3c' : '#e67e22';
            return [r.time, '<span style="background:' + color + ';color:#fff;padding:2px 7px;border-radius:10px;font-size:11px;">' + r.label + '</span>', r.particular, r.category, r.mode, dsmFmt(r.payment)];
        });
        body = dsmPopupTable(['Time','Type','Particular','Category','Mode','Payment (₹)'], rows, ['','','','','Total', dsmFmt(dsmData.totalPayments)], '#e74c3c');
        dsmShowModal('<i class="fa fa-arrow-up"></i> Total Expenses — Breakdown (Expenses + Salary)', '#e74c3c', body);

    } else if (card === 'closing') {
        var rows = dsmData.modeSummary.map(function(r) {
            var bal = r.collection - r.payment;
            var balStr = (bal < 0 ? '<span style="color:#e74c3c;">' : '<span style="color:#27ae60;">') + dsmFmt(Math.abs(bal)) + (bal < 0 ? ' (Dr)' : '') + '</span>';
            return [r.mode, dsmFmt(r.collection), dsmFmt(r.payment), balStr];
        });
        var totBal = dsmData.closingBalance;
        var totBalStr = dsmFmt(Math.abs(totBal));
        body = dsmPopupTable(['Payment Mode','Receipts','Payments','Net Balance'], rows,
            ['Total', dsmFmt(dsmData.totalReceipts), dsmFmt(dsmData.totalPayments), totBalStr], '#16a085');
        body += '<div style="display:flex;justify-content:space-between;align-items:center;background:#e8f8f5;padding:12px 16px;font-weight:700;">'
              + '<span style="color:#0e6655;font-size:14px;">Closing Balance (Opening + Receipts &minus; Payments)</span>'
              + '<span style="font-size:18px;color:' + (totBal>=0?'#0e6655':'#c0392b') + ';">&#8377;' + dsmFmt(Math.abs(totBal)) + '</span></div>';
        dsmShowModal('<i class="fa fa-university"></i> Closing Balance — Breakdown', '#16a085', body);
    }
});

$(document).on('click', '.dsm-tab', function() {
    var mode = $(this).data('mode');
    $('.dsm-tab').removeClass('active');
    $(this).addClass('active');

    if (mode === 'all') {
        $('.txn-row').show();
    } else {
        $('.txn-row').hide().filter('[data-mode="' + mode + '"]').show();
    }
    dsmRecomputeBalance();
});
</script>
