<?php
function ds_fmt($n) { return number_format((float)$n, 2); }
$expItemsByAcct = [];
if (customCompute($expenseItems)) {
    foreach ($expenseItems as $item) {
        $key = ($item->payment_type === 'Others' && $item->bank !== '') ? $item->bank : $item->payment_type;
        $expItemsByAcct[$key][] = $item;
    }
}
$expAcctKeys = array_keys($expItemsByAcct);

$bankDetails = [];
if (customCompute($banks)) {
    foreach ($banks as $b) {
        $bankDetails[] = [
            'name'     => $b->bank_name,
            'opening'  => (float)($opening[$b->bank_name] ?? 0),
            'received' => (float)($received[$b->bank_name] ?? 0),
            'spent'    => (float)($expByTypeAgg[$b->bank_name] ?? 0),
            'closing'  => (float)($closing[$b->bank_name] ?? 0),
        ];
    }
}

$bankClosingTotal = 0;
if (customCompute($banks)) {
    foreach ($banks as $b) { $bankClosingTotal += (float)($closing[$b->bank_name] ?? 0); }
}

$netColor   = $netCashFlow >= 0 ? '#27ae60' : '#e74c3c';
$netGrad    = $netCashFlow >= 0 ? 'linear-gradient(135deg,#219a52,#27ae60)' : 'linear-gradient(135deg,#c0392b,#e74c3c)';
$netGlow    = $netCashFlow >= 0 ? 'rgba(39,174,96,0.45)' : 'rgba(231,76,60,0.45)';
$netIcon    = $netCashFlow >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
?>

<!-- ===== CARD DATA FOR JS MODALS ===== -->
<script>
var dsCards = {
    feeByType:    <?=json_encode(customCompute($feeByType) ? array_map(function($r){ return ['label'=>($r->paymenttype==='Others'&&$r->bank!==''?$r->bank:$r->paymenttype),'total'=>(float)$r->total]; }, $feeByType) : []) ?>,
    incomeBycat:  <?=json_encode(customCompute($incomeBycat) ? array_map(function($r){ return ['category'=>($r->category_name?:'Uncategorised'),'total'=>(float)$r->total]; }, $incomeBycat) : []) ?>,
    expenseByCat: <?=json_encode(customCompute($expenseByCat) ? array_map(function($r){ return ['category'=>($r->category?:'Uncategorised'),'total'=>(float)$r->total]; }, $expenseByCat) : []) ?>,
    salaryDetail: <?=json_encode(customCompute($salaryDetail) ? array_map(function($r){
        $pm = (int)$r->payment_method;
        $pmLabel = ($pm==1?'Cash':($pm==2?'Cheque':($pm==3&&$r->bank_name?$r->bank_name:'Others')));
        return ['name'=>$r->staff_name,'amount'=>(float)$r->payment_amount,'method'=>$pmLabel];
    }, $salaryDetail) : []) ?>,
    salaryTotal:         <?=(float)$salaryTotal ?>,
    totalFeeCollection:  <?=(float)$totalFeeCollection ?>,
    totalOtherIncome:    <?=(float)$totalOtherIncome ?>,
    totalExpenses:       <?=(float)$totalExpenses ?>,
    netCashFlow:         <?=(float)$netCashFlow ?>,
    feeCount:            <?=(int)$feeCount ?>,
    cash:    { opening:<?=(float)($opening['Cash']??0)?>, received:<?=(float)($received['Cash']??0)?>, spent:<?=(float)($expByTypeAgg['Cash']??0)?>, closing:<?=(float)($closing['Cash']??0)?> },
    digital: { opening:<?=(float)($opening['Digital']??0)?>, received:<?=(float)($received['Digital']??0)?>, spent:<?=(float)($expByTypeAgg['Digital']??0)?>, closing:<?=(float)($closing['Digital']??0)?> },
    cheque:  { opening:<?=(float)($opening['Cheque']??0)?>, received:<?=(float)($received['Cheque']??0)?>, spent:<?=(float)($expByTypeAgg['Cheque']??0)?>, closing:<?=(float)($closing['Cheque']??0)?> },
    others:  { opening:<?=(float)($opening['Others']??0)?>, received:<?=(float)($received['Others']??0)?>, spent:<?=(float)($expByTypeAgg['Others']??0)?>, closing:<?=(float)($closing['Others']??0)?> },
    banks:   <?=json_encode($bankDetails) ?>,
    bankClosingTotal: <?=(float)$bankClosingTotal ?>
};
</script>

<!-- ===== CARD DETAIL MODAL ===== -->
<div class="modal fade" id="ds-card-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" id="ds-modal-header" style="border-radius:4px 4px 0 0;padding:12px 18px;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.85;">&times;</button>
                <h4 class="modal-title" id="ds-modal-title" style="font-weight:700;"></h4>
            </div>
            <div class="modal-body" style="padding:0;" id="ds-modal-body"></div>
        </div>
    </div>
</div>

<!-- ===== SUMMARY CARDS — 3 GROUPS ===== -->
<div style="margin-bottom:20px;">

    <div style="display:flex;gap:14px;flex-wrap:wrap;align-items:stretch;">

        <!-- GROUP 1: Income & Expenses -->
        <div style="flex:4;min-width:290px;background:#fff;border-radius:14px;padding:14px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.08);">
            <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;color:#bbb;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #f2f2f2;">
                <i class="fa fa-bar-chart" style="margin-right:4px;"></i> INCOME &amp; EXPENSES
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">

                <!-- Today's Collection -->
                <div class="ds-card ds-mini-card" data-card="collection"
                     style="flex:1;min-width:75px;background:#f0faf3;border:1px solid #d5f0e0;border-radius:10px;padding:12px 10px;cursor:pointer;text-align:center;">
                    <div style="width:38px;height:38px;border-radius:50%;background:#d5f0e0;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                        <i class="fa fa-inr" style="color:#27ae60;font-size:16px;"></i>
                    </div>
                    <div style="font-size:15px;font-weight:800;color:#219a52;line-height:1.1;">&#8377;<?=ds_fmt($totalFeeCollection)?></div>
                    <div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.3px;margin-top:4px;">Collection</div>
                    <div style="font-size:10px;color:#bbb;margin-top:2px;"><?=$feeCount?> txn<?=($feeCount!=1?'s':'')?></div>
                </div>

                <!-- Other Income -->
                <div class="ds-card ds-mini-card" data-card="otherincome"
                     style="flex:1;min-width:75px;background:#faf5ff;border:1px solid #e8d5f5;border-radius:10px;padding:12px 10px;cursor:pointer;text-align:center;">
                    <div style="width:38px;height:38px;border-radius:50%;background:#e8d5f5;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                        <i class="fa fa-plus-circle" style="color:#8e44ad;font-size:16px;"></i>
                    </div>
                    <div style="font-size:15px;font-weight:800;color:#7d3c98;line-height:1.1;">&#8377;<?=ds_fmt($totalOtherIncome)?></div>
                    <div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.3px;margin-top:4px;">Other Income</div>
                    <div style="font-size:10px;color:#bbb;margin-top:2px;">Category wise</div>
                </div>

                <!-- Today's Expenses -->
                <div class="ds-card ds-mini-card" data-card="expenses"
                     style="flex:1;min-width:75px;background:#fff5f5;border:1px solid #f5d5d5;border-radius:10px;padding:12px 10px;cursor:pointer;text-align:center;">
                    <div style="width:38px;height:38px;border-radius:50%;background:#f5d5d5;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                        <i class="fa fa-arrow-down" style="color:#e74c3c;font-size:16px;"></i>
                    </div>
                    <div style="font-size:15px;font-weight:800;color:#c0392b;line-height:1.1;">&#8377;<?=ds_fmt($totalExpenses)?></div>
                    <div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.3px;margin-top:4px;">Expenses</div>
                    <div style="font-size:10px;color:#bbb;margin-top:2px;">Incl. salary</div>
                </div>

            </div>
        </div><!-- /Group 1 -->

        <!-- NET CASH FLOW — highlighted centre card -->
        <div class="ds-card" data-card="netcashflow"
             style="flex:2;min-width:155px;background:<?=$netGrad?>;border-radius:14px;padding:18px 14px;box-shadow:0 6px 24px <?=$netGlow?>;cursor:pointer;text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;">
            <div style="width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,0.22);display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                <i class="fa <?=$netIcon?>" style="font-size:20px;color:#fff;"></i>
            </div>
            <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;opacity:.85;margin-bottom:4px;">Net Cash Flow</div>
            <div style="font-size:26px;font-weight:900;line-height:1.1;">&#8377;<?=ds_fmt(abs($netCashFlow))?></div>
            <div style="font-size:10px;opacity:.75;margin-top:4px;">Income &minus; Expense</div>
            <?php if ($netCashFlow < 0): ?>
            <div style="font-size:10px;background:rgba(0,0,0,0.18);border-radius:20px;padding:2px 8px;margin-top:6px;">Deficit</div>
            <?php endif; ?>
        </div><!-- /Net Cash Flow -->

        <!-- GROUP 2: Account Balances -->
        <div style="flex:4;min-width:290px;background:#fff;border-radius:14px;padding:14px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.08);">
            <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;color:#bbb;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #f2f2f2;">
                <i class="fa fa-university" style="margin-right:4px;"></i> ACCOUNT BALANCES <small style="font-weight:400;">(Closing)</small>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">

                <!-- Cash in Hand -->
                <div class="ds-card ds-mini-card" data-card="cash"
                     style="flex:1;min-width:75px;background:#fffbf0;border:1px solid #f5e8c0;border-radius:10px;padding:12px 10px;cursor:pointer;text-align:center;">
                    <div style="width:38px;height:38px;border-radius:50%;background:#f5e8c0;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                        <i class="fa fa-money" style="color:#f39c12;font-size:16px;"></i>
                    </div>
                    <div style="font-size:15px;font-weight:800;color:#d68910;line-height:1.1;">&#8377;<?=ds_fmt($closing['Cash'] ?? 0)?></div>
                    <div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.3px;margin-top:4px;">Cash</div>
                    <div style="font-size:10px;color:#bbb;margin-top:2px;">In Hand</div>
                </div>

                <!-- Digital Balance -->
                <div class="ds-card ds-mini-card" data-card="digital"
                     style="flex:1;min-width:75px;background:#f0f7ff;border:1px solid #c8dff5;border-radius:10px;padding:12px 10px;cursor:pointer;text-align:center;">
                    <div style="width:38px;height:38px;border-radius:50%;background:#c8dff5;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                        <i class="fa fa-mobile" style="color:#2980b9;font-size:18px;"></i>
                    </div>
                    <div style="font-size:15px;font-weight:800;color:#2471a3;line-height:1.1;">&#8377;<?=ds_fmt($closing['Digital'] ?? 0)?></div>
                    <div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.3px;margin-top:4px;">Digital</div>
                    <div style="font-size:10px;color:#bbb;margin-top:2px;">Balance</div>
                </div>

                <!-- Bank Balance -->
                <div class="ds-card ds-mini-card" data-card="bank"
                     style="flex:1;min-width:75px;background:#f0fdf9;border:1px solid #c0e8de;border-radius:10px;padding:12px 10px;cursor:pointer;text-align:center;">
                    <div style="width:38px;height:38px;border-radius:50%;background:#c0e8de;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                        <i class="fa fa-university" style="color:#16a085;font-size:15px;"></i>
                    </div>
                    <div style="font-size:15px;font-weight:800;color:#117a65;line-height:1.1;">&#8377;<?=ds_fmt($bankClosingTotal)?></div>
                    <div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.3px;margin-top:4px;">Bank</div>
                    <div style="font-size:10px;color:#bbb;margin-top:2px;">All Accounts</div>
                </div>

            </div>
        </div><!-- /Group 2 -->

    </div><!-- /flex row -->
</div><!-- /summary cards -->

<div id="printablediv">
<div class="box-body">

<?= reportheader($siteinfos, $schoolyearsessionobj) ?>
<div class="rpt-class-info">
    <span><i class="fa fa-calendar"></i> Day Sheet Date: <strong><?=date('d M Y', strtotime($date_ymd))?></strong></span>
    <span><i class="fa fa-calendar-check-o"></i> Academic Year: <strong><?=$schoolyearsessionobj->schoolyear?></strong></span>
</div>

<!-- ACTION BAR -->
<div class="rpt-action-bar" style="margin-bottom:14px;">
    <button id="daysheet-print-btn" class="btn btn-default rpt-action-btn">
        <i class="fa fa-print"></i> Print
    </button>
</div>

<!-- ===== SECTIONS 1 + 2 SIDE BY SIDE ===== -->
<div class="row">

    <!-- SECTION 1: Opening Balance -->
    <div class="col-sm-5">
        <div class="box" style="border-top:3px solid #27ae60;">
            <div class="box-header" style="background:linear-gradient(135deg,#f8fffe,#edf7ee);border-bottom:1px solid #c8e6c9;padding:10px 14px;">
                <h4 style="margin:0;font-size:14px;font-weight:700;color:#1b5e20;">
                    <i class="fa fa-unlock-alt" style="color:#27ae60;"></i> 1. Opening Balance
                </h4>
            </div>
            <div class="box-body" style="padding:0;">
                <table class="table table-condensed table-bordered" style="margin:0;">
                    <thead>
                        <tr><th>Account</th><th class="text-right">Opening Balance</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $tot1 = 0;
                        foreach ($accounts as $acct):
                            $amt = (float)($opening[$acct] ?? 0);
                            $tot1 += $amt;
                            if ($acct === 'Cash') $icon = 'fa-money';
                            elseif ($acct === 'Digital') $icon = 'fa-mobile';
                            elseif ($acct === 'Cheque') $icon = 'fa-file-text-o';
                            elseif ($acct === 'Others') $icon = 'fa-ellipsis-h';
                            else $icon = 'fa-university';
                        ?>
                        <tr>
                            <td><i class="fa <?=$icon?>" style="color:#27ae60;width:16px;"></i> <?=htmlspecialchars($acct)?></td>
                            <td class="text-right">&#8377;<?=ds_fmt($amt)?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:#e8f5e9;font-weight:700;">
                            <td style="color:#1b5e20;">Total</td>
                            <td class="text-right" style="color:#1b5e20;">&#8377;<?=ds_fmt($totalOpening)?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- SECTION 2: Today's Fee Collection -->
    <div class="col-sm-7">
        <div class="box" style="border-top:3px solid #2980b9;">
            <div class="box-header" style="background:linear-gradient(135deg,#f0f8ff,#e3f2fd);border-bottom:1px solid #bbdefb;padding:10px 14px;">
                <h4 style="margin:0;font-size:14px;font-weight:700;color:#0d47a1;">
                    <i class="fa fa-inr" style="color:#2980b9;"></i> 2. Today's Fee Collection
                    <small style="font-weight:400;color:#666;">(Payment Mode Wise)</small>
                </h4>
            </div>
            <div class="box-body" style="padding:0;">
                <table class="table table-condensed table-bordered" style="margin:0;">
                    <thead>
                        <tr><th>Payment Mode</th><th class="text-right">Amount</th></tr>
                    </thead>
                    <tbody>
                        <?php if (customCompute($feeByType)): ?>
                        <?php foreach ($feeByType as $row):
                            $label = ($row->paymenttype === 'Others' && $row->bank !== '') ? $row->bank : $row->paymenttype;
                            if ($label === 'Cash') $icon = 'fa-money';
                            elseif ($label === 'Digital') $icon = 'fa-mobile';
                            elseif ($label === 'Cheque') $icon = 'fa-file-text-o';
                            else $icon = 'fa-university';
                        ?>
                        <tr>
                            <td><i class="fa <?=$icon?>" style="color:#2980b9;width:16px;"></i> <?=htmlspecialchars($label)?></td>
                            <td class="text-right">&#8377;<?=ds_fmt($row->total)?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="2" class="text-center text-muted">No fee collections for this date.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:#e3f2fd;font-weight:700;">
                            <td style="color:#0d47a1;">Total Collection</td>
                            <td class="text-right" style="color:#0d47a1;">&#8377;<?=ds_fmt($totalFeeCollection)?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div><!-- /.row sections 1+2 -->

<!-- ===== SECTION 3: Other Income (half width) ===== -->
<div class="row">
<div class="col-sm-6">
<div class="box" style="border-top:3px solid #8e44ad;">
    <div class="box-header" style="background:linear-gradient(135deg,#fdf8ff,#f3e5f5);border-bottom:1px solid #e1bee7;padding:10px 14px;">
        <h4 style="margin:0;font-size:14px;font-weight:700;color:#4a148c;">
            <i class="fa fa-plus-circle" style="color:#8e44ad;"></i> 3. Other Income
            <small style="font-weight:400;color:#666;">(Category Wise)</small>
        </h4>
    </div>
    <div class="box-body" style="padding:0;">
        <?php if (customCompute($incomeBycat)): ?>
        <table class="table table-condensed table-bordered" style="margin:0;">
            <thead><tr><th>Category</th><th class="text-right">Amount</th></tr></thead>
            <tbody>
                <?php foreach ($incomeBycat as $row): ?>
                <tr>
                    <td><?=htmlspecialchars($row->category_name ?: 'Uncategorised')?></td>
                    <td class="text-right">&#8377;<?=ds_fmt($row->total)?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:#f3e5f5;font-weight:700;">
                    <td style="color:#4a148c;">Total Other Income</td>
                    <td class="text-right" style="color:#4a148c;">&#8377;<?=ds_fmt($totalOtherIncome)?></td>
                </tr>
            </tfoot>
        </table>
        <?php else: ?>
        <div class="text-center text-muted" style="padding:14px;">No other income recorded for this date.</div>
        <?php endif; ?>
    </div>
</div>
</div><!-- /.col-sm-6 -->
</div><!-- /.row section 3 -->

<!-- ===== SECTION 4: Expenses Account Wise ===== -->
<div class="box" style="border-top:3px solid #e74c3c;">
    <div class="box-header" style="background:linear-gradient(135deg,#fff8f8,#ffebee);border-bottom:1px solid #ffcdd2;padding:10px 14px;">
        <h4 style="margin:0;font-size:14px;font-weight:700;color:#b71c1c;">
            <i class="fa fa-arrow-down" style="color:#e74c3c;"></i> 4. Expenses (Account Wise)
        </h4>
    </div>
    <div class="box-body">
        <?php if (customCompute($expAcctKeys)): ?>
        <div class="row">
            <?php foreach ($expAcctKeys as $acctKey):
                $acctItems = $expItemsByAcct[$acctKey] ?? [];
                $acctTotal = array_sum(array_column($acctItems, 'amount'));
                $colClass  = count($expAcctKeys) <= 2 ? 'col-sm-6' : (count($expAcctKeys) == 3 ? 'col-sm-4' : 'col-sm-3');
            ?>
            <div class="<?=$colClass?>" style="margin-bottom:12px;">
                <div style="background:#fff;border:1px solid #ffcdd2;border-radius:8px;overflow:hidden;">
                    <div style="background:#ffebee;padding:8px 12px;font-size:12px;font-weight:700;color:#c62828;">
                        <i class="fa fa-<?=($acctKey==='Cash'?'money':($acctKey==='Digital'?'mobile':'university'))?>"></i>
                        &nbsp;<?=htmlspecialchars($acctKey)?> Expenses
                    </div>
                    <table class="table table-condensed" style="margin:0;font-size:12px;">
                        <thead><tr style="background:#fff3f3;"><th>Category</th><th class="text-right">Amount</th></tr></thead>
                        <tbody>
                            <?php foreach ($acctItems as $item): ?>
                            <tr>
                                <td><?=htmlspecialchars($item->category ?: $item->expense)?></td>
                                <td class="text-right">&#8377;<?=ds_fmt($item->amount)?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="font-weight:700;background:#ffebee;">
                                <td style="color:#c62828;">Total</td>
                                <td class="text-right" style="color:#c62828;">&#8377;<?=ds_fmt($acctTotal)?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center text-muted">No expenses recorded for this date.</div>
        <?php endif; ?>
        <?php if ($salaryTotal > 0): ?>
        <div style="margin-top:12px;">
            <div style="background:#fff3cd;padding:7px 12px;font-size:12px;font-weight:700;color:#856404;border-radius:6px 6px 0 0;">
                <i class="fa fa-user"></i> Salary Paid (Payroll)
            </div>
            <?php if (customCompute($salaryDetail)): ?>
            <table class="table table-condensed table-bordered" style="margin:0;font-size:12px;">
                <thead>
                    <tr style="background:#fffde7;">
                        <th>Staff Name</th>
                        <th class="text-center">Method</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salaryDetail as $sd):
                        $pm = (int)$sd->payment_method;
                        $pmLabel = ($pm==1?'Cash':($pm==2?'Cheque':($pm==3&&$sd->bank_name?$sd->bank_name:'Others')));
                    ?>
                    <tr>
                        <td><?=htmlspecialchars($sd->staff_name)?></td>
                        <td class="text-center"><span style="font-size:11px;"><?=htmlspecialchars($pmLabel)?></span></td>
                        <td class="text-right" style="color:#856404;">&#8377;<?=ds_fmt($sd->payment_amount)?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background:#fff3cd;font-weight:700;">
                        <td colspan="2" style="color:#856404;">Total Salary Paid</td>
                        <td class="text-right" style="color:#856404;">&#8377;<?=ds_fmt($salaryTotal)?></td>
                    </tr>
                </tfoot>
            </table>
            <?php else: ?>
            <div style="padding:8px 12px;background:#fffde7;border:1px solid #ffe082;font-size:13px;color:#856404;">
                &#8377;<?=ds_fmt($salaryTotal)?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===== SECTION 5: Today's Summary ===== -->
<div class="row">
    <div class="col-sm-6 col-sm-offset-6">
        <div class="box" style="border-top:3px solid #f39c12;">
            <div class="box-header" style="background:linear-gradient(135deg,#fffdf0,#fff8e1);border-bottom:1px solid #ffe082;padding:10px 14px;">
                <h4 style="margin:0;font-size:14px;font-weight:700;color:#e65100;">
                    <i class="fa fa-bar-chart" style="color:#f39c12;"></i> 5. Today's Summary
                </h4>
            </div>
            <div class="box-body" style="padding:0;">
                <table class="table table-condensed" style="margin:0;font-size:13px;">
                    <tbody>
                        <tr>
                            <td>Total Fee Collection</td>
                            <td class="text-right" style="color:#27ae60;font-weight:600;">&#8377;<?=ds_fmt($totalFeeCollection)?></td>
                        </tr>
                        <tr>
                            <td>Other Income</td>
                            <td class="text-right" style="color:#8e44ad;font-weight:600;">&#8377;<?=ds_fmt($totalOtherIncome)?></td>
                        </tr>
                        <tr style="border-top:2px solid #eee;">
                            <td><strong>Total Income</strong></td>
                            <td class="text-right"><strong style="color:#27ae60;">&#8377;<?=ds_fmt($totalFeeCollection + $totalOtherIncome)?></strong></td>
                        </tr>
                        <tr>
                            <td>Total Expenses</td>
                            <td class="text-right" style="color:#e74c3c;font-weight:600;">&#8377;<?=ds_fmt($totalExpenses)?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background:#fff8e1;">
                            <td style="font-size:15px;font-weight:800;color:#e65100;">Net Balance Today</td>
                            <td class="text-right" style="font-size:18px;font-weight:800;color:<?=($netCashFlow>=0?'#27ae60':'#e74c3c')?>;">
                                &#8377;<?=ds_fmt(abs($netCashFlow))?>
                                <?=($netCashFlow < 0 ? '<span style="font-size:11px;">(deficit)</span>' : '')?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ===== SECTION 6: Closing Balance ===== -->
<div class="box" style="border-top:3px solid #16a085;">
    <div class="box-header" style="background:linear-gradient(135deg,#f0fffd,#e0f2f1);border-bottom:1px solid #b2dfdb;padding:10px 14px;">
        <h4 style="margin:0;font-size:14px;font-weight:700;color:#004d40;">
            <i class="fa fa-lock" style="color:#16a085;"></i> 6. Closing Balance
            <small style="font-weight:400;color:#666;">(Tomorrow's Opening)</small>
        </h4>
    </div>
    <div class="box-body" style="padding:0;">
        <div class="rpt-table-wrap" id="rpt-wrap-daysheet">
        <table class="table table-bordered rpt-table" style="margin:0;" id="daysheet-closing-table">
            <thead>
                <tr>
                    <th class="rpt-sticky-left-hd">Account</th>
                    <th class="text-right">Opening</th>
                    <th class="text-right" style="color:#27ae60;">Received</th>
                    <th class="text-right" style="color:#e74c3c;">Spent</th>
                    <th class="rpt-sticky-right-hd text-right">Closing Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $acct):
                    $op = (float)($opening[$acct] ?? 0);
                    $rc = (float)($received[$acct] ?? 0);
                    $sp = (float)($expByTypeAgg[$acct] ?? 0);
                    $cl = (float)($closing[$acct] ?? 0);
                    if ($acct === 'Cash') $icon = 'fa-money';
                    elseif ($acct === 'Digital') $icon = 'fa-mobile';
                    elseif ($acct === 'Cheque') $icon = 'fa-file-text-o';
                    elseif ($acct === 'Others') $icon = 'fa-ellipsis-h';
                    else $icon = 'fa-university';
                ?>
                <tr>
                    <td class="rpt-sticky-left">
                        <i class="fa <?=$icon?>" style="color:#16a085;width:16px;"></i> <?=htmlspecialchars($acct)?>
                    </td>
                    <td class="text-right">&#8377;<?=ds_fmt($op)?></td>
                    <td class="text-right" style="color:<?=($rc>0?'#27ae60':'#999')?>;">&#8377;<?=ds_fmt($rc)?></td>
                    <td class="text-right" style="color:<?=($sp>0?'#e74c3c':'#999')?>;">&#8377;<?=ds_fmt($sp)?></td>
                    <td class="rpt-sticky-right text-right" style="font-weight:700;color:<?=($cl>=0?'#1b5e20':'#c62828')?>;">
                        &#8377;<?=ds_fmt($cl)?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:#e0f2f1;font-weight:700;">
                    <td class="rpt-sticky-left" style="color:#004d40;">Total</td>
                    <td style="text-align:center;color:#004d40;">&#8377;<?=ds_fmt($totalOpening)?></td>
                    <td style="text-align:center;color:#27ae60;">&#8377;<?=ds_fmt($totalReceived)?></td>
                    <td style="text-align:center;color:#e74c3c;">&#8377;<?=ds_fmt($totalSpent)?></td>
                    <td class="rpt-sticky-right" style="text-align:center;color:#004d40;font-size:15px;">&#8377;<?=ds_fmt($totalClosing)?></td>
                </tr>
            </tfoot>
        </table>
        </div>
        <div class="rpt-hscroll-bar" id="hbar-daysheet"><div class="rpt-hscroll-inner"></div></div>
    </div>
</div>

<!-- ===== SECTIONS 7 + 8 SIDE BY SIDE ===== -->
<div class="row">

    <!-- SECTION 7: Expense Category Wise -->
    <div class="col-sm-6">
        <div class="box" style="border-top:3px solid #e67e22;">
            <div class="box-header" style="background:linear-gradient(135deg,#fffaf5,#fff3e0);border-bottom:1px solid #ffe0b2;padding:10px 14px;">
                <h4 style="margin:0;font-size:14px;font-weight:700;color:#bf360c;">
                    <i class="fa fa-tags" style="color:#e67e22;"></i> 7. Expense Category Wise
                </h4>
            </div>
            <div class="box-body" style="padding:0;">
                <?php if (customCompute($expenseByCat)): ?>
                <table class="table table-condensed table-bordered" style="margin:0;">
                    <thead><tr><th>Category</th><th class="text-right">Amount</th></tr></thead>
                    <tbody>
                        <?php $tot7 = 0; foreach ($expenseByCat as $row): $tot7 += (float)$row->total; ?>
                        <tr>
                            <td><?=htmlspecialchars($row->category ?: 'Uncategorised')?></td>
                            <td class="text-right">&#8377;<?=ds_fmt($row->total)?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if ($salaryTotal > 0): ?>
                        <tr style="background:#fffde7;">
                            <td>
                                <i class="fa fa-user" style="color:#f39c12;"></i>
                                Salary (Payroll)
                                <?php if (customCompute($salaryDetail)): ?>
                                <button type="button" class="btn btn-xs btn-warning" id="show-salary-detail"
                                        style="margin-left:6px;padding:1px 7px;font-size:10px;">
                                    <i class="fa fa-eye"></i> Details
                                </button>
                                <?php endif; ?>
                            </td>
                            <td class="text-right" style="font-weight:600;color:#e67e22;">&#8377;<?=ds_fmt($salaryTotal)?></td>
                        </tr>
                        <?php $tot7 += $salaryTotal; endif; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:#fff3e0;font-weight:700;">
                            <td style="color:#bf360c;">Total</td>
                            <td class="text-right" style="color:#bf360c;">&#8377;<?=ds_fmt($tot7)?></td>
                        </tr>
                    </tfoot>
                </table>
                <?php else: ?>
                <div class="text-center text-muted" style="padding:14px;">No expenses recorded.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SECTION 8: Collection Category Wise -->
    <div class="col-sm-6">
        <div class="box" style="border-top:3px solid #2980b9;">
            <div class="box-header" style="background:linear-gradient(135deg,#f0f8ff,#e3f2fd);border-bottom:1px solid #bbdefb;padding:10px 14px;">
                <h4 style="margin:0;font-size:14px;font-weight:700;color:#0d47a1;">
                    <i class="fa fa-list-alt" style="color:#2980b9;"></i> 8. Collection Category Wise
                    <small style="font-weight:400;color:#666;">(By Fee Type)</small>
                </h4>
            </div>
            <div class="box-body" style="padding:0;">
                <?php if (customCompute($feeByFeetype)): ?>
                <table class="table table-condensed table-bordered" style="margin:0;">
                    <thead><tr><th>Fee Type</th><th class="text-right">Amount</th></tr></thead>
                    <tbody>
                        <?php $tot8 = 0; foreach ($feeByFeetype as $row): $tot8 += (float)$row->total; ?>
                        <tr>
                            <td><?=htmlspecialchars($row->feetype ?: 'Others')?></td>
                            <td class="text-right">&#8377;<?=ds_fmt($row->total)?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:#e3f2fd;font-weight:700;">
                            <td style="color:#0d47a1;">Total</td>
                            <td class="text-right" style="color:#0d47a1;">&#8377;<?=ds_fmt($tot8)?></td>
                        </tr>
                    </tfoot>
                </table>
                <?php else: ?>
                <div class="text-center text-muted" style="padding:14px;">No fee collections recorded.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div><!-- /.row sections 7+8 -->

<?= reportfooter($siteinfos, $schoolyearsessionobj) ?>

</div><!-- /.box-body -->
</div><!-- /#printablediv -->

<button class="rpt-scroll-top-btn" id="scroll-to-top-btn" title="Back to top">&#8679;</button>

<script>
// ---- Card hover ----
$(document).on('mouseenter', '.ds-mini-card', function(){ $(this).css({'transform':'translateY(-2px)','box-shadow':'0 4px 14px rgba(0,0,0,0.12)'}); });
$(document).on('mouseleave', '.ds-mini-card', function(){ $(this).css({'transform':'','box-shadow':''}); });
$(document).on('mouseenter', '.ds-card[data-card="netcashflow"]', function(){ $(this).css('transform','translateY(-2px)'); });
$(document).on('mouseleave', '.ds-card[data-card="netcashflow"]', function(){ $(this).css('transform',''); });

// ---- Helpers ----
function dsFmt(n) {
    return '&#8377;' + parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}
function dsTbl(heads, rows, footRow, accentColor) {
    var h = '<table class="table table-condensed table-bordered" style="margin:0;font-size:13px;">';
    h += '<thead style="background:#f9f9f9;"><tr>';
    heads.forEach(function(hd, i){ h += '<th' + (i > 0 ? ' class="text-right"' : '') + '>' + hd + '</th>'; });
    h += '</tr></thead><tbody>';
    if (!rows.length) {
        h += '<tr><td colspan="' + heads.length + '" class="text-center text-muted" style="padding:14px;">No data</td></tr>';
    } else {
        rows.forEach(function(r){
            h += '<tr>';
            r.forEach(function(c, i){ h += '<td' + (i > 0 ? ' class="text-right"' : '') + '>' + c + '</td>'; });
            h += '</tr>';
        });
    }
    h += '</tbody>';
    if (footRow) {
        h += '<tfoot><tr style="font-weight:700;background:#f0f0f0;">';
        footRow.forEach(function(c, i){ h += '<td' + (i > 0 ? ' class="text-right" style="color:' + accentColor + '"' : '') + '>' + c + '</td>'; });
        h += '</tr></tfoot>';
    }
    return h + '</table>';
}
function dsAccountBlock(d, color) {
    return dsTbl(['Particulars','Amount'],
        [['Opening Balance', dsFmt(d.opening)],
         ['+ Received Today', dsFmt(d.received)],
         ['&minus; Spent Today', dsFmt(d.spent)]],
        ['Closing Balance', dsFmt(d.closing)], color);
}
function dsShowModal(title, headerBg, body) {
    $('#ds-modal-header').css('background', headerBg);
    $('#ds-modal-title').html(title);
    $('#ds-modal-body').html(body);
    $('#ds-card-modal').modal('show');
}

// ---- Card clicks ----
$(document).on('click', '.ds-card', function() {
    var card = $(this).data('card');
    if (!card) return;
    var body = '';

    if (card === 'collection') {
        var rows = dsCards.feeByType.map(function(r){ return [r.label, dsFmt(r.total)]; });
        body = dsTbl(['Payment Mode','Amount'], rows, ['Total Collection', dsFmt(dsCards.totalFeeCollection)], '#27ae60');
        dsShowModal("<i class='fa fa-inr'></i> Today's Fee Collection", '#27ae60', body);

    } else if (card === 'otherincome') {
        var rows = dsCards.incomeBycat.map(function(r){ return [r.category, dsFmt(r.total)]; });
        body = dsTbl(['Category','Amount'], rows, ['Total Other Income', dsFmt(dsCards.totalOtherIncome)], '#8e44ad');
        dsShowModal("<i class='fa fa-plus-circle'></i> Other Income Breakdown", '#8e44ad', body);

    } else if (card === 'expenses') {
        var rows = dsCards.expenseByCat.map(function(r){ return [r.category, dsFmt(r.total)]; });
        body = dsTbl(['Expense Category','Amount'], rows, null, '#e74c3c');
        // Salary breakdown section
        if (dsCards.salaryTotal > 0) {
            body += '<div style="border-top:2px solid #ffe082;background:#fffde7;padding:10px 14px 0;">';
            body += '<div style="font-size:12px;font-weight:700;color:#e65100;margin-bottom:8px;"><i class="fa fa-user"></i> Salary Paid (Payroll)</div>';
            if (dsCards.salaryDetail.length > 0) {
                var sRows = dsCards.salaryDetail.map(function(r){ return [r.name, r.method, dsFmt(r.amount)]; });
                body += dsTbl(['Staff Member','Method','Amount'], sRows, ['Total Salary', '', dsFmt(dsCards.salaryTotal)], '#f39c12');
            } else {
                body += '<div style="padding:8px 0 10px;color:#888;">Total: <strong>' + dsFmt(dsCards.salaryTotal) + '</strong></div>';
            }
            body += '</div>';
        }
        // Grand total footer
        body += '<div style="background:#ffebee;padding:10px 14px;font-weight:700;font-size:13px;display:flex;justify-content:space-between;">'
              + '<span style="color:#c62828;">Grand Total Expenses</span>'
              + '<span style="color:#c62828;">' + dsFmt(dsCards.totalExpenses) + '</span></div>';
        dsShowModal("<i class='fa fa-arrow-down'></i> Expenses Breakdown", '#e74c3c', body);

    } else if (card === 'netcashflow') {
        var nc = dsCards.netCashFlow;
        var color = nc >= 0 ? '#27ae60' : '#e74c3c';
        var rows = [
            ['Fee Collection',   dsFmt(dsCards.totalFeeCollection)],
            ['Other Income',     dsFmt(dsCards.totalOtherIncome)],
            ['<strong>Total Income</strong>', '<strong>' + dsFmt(dsCards.totalFeeCollection + dsCards.totalOtherIncome) + '</strong>'],
            ['Total Expenses',   dsFmt(dsCards.totalExpenses)],
        ];
        body = dsTbl(['Particulars','Amount'], rows,
            ['Net Cash Flow', (nc < 0 ? '<span style="color:#e74c3c">' : '') + dsFmt(Math.abs(nc)) + (nc < 0 ? ' (Deficit)</span>' : '')],
            color);
        dsShowModal("<i class='fa fa-exchange'></i> Net Cash Flow", (nc >= 0 ? '#27ae60' : '#e74c3c'), body);

    } else if (card === 'cash') {
        body = dsAccountBlock(dsCards.cash, '#f39c12');
        dsShowModal("<i class='fa fa-money'></i> Cash Account Detail", '#f39c12', body);

    } else if (card === 'digital') {
        body = dsAccountBlock(dsCards.digital, '#2980b9');
        dsShowModal("<i class='fa fa-mobile'></i> Digital Account Detail", '#2980b9', body);

    } else if (card === 'bank') {
        if (!dsCards.banks.length) {
            body = '<div class="text-center text-muted" style="padding:20px;">No banks configured.</div>';
        } else {
            var rows = dsCards.banks.map(function(b){
                return [b.name, dsFmt(b.opening), dsFmt(b.received), dsFmt(b.spent),
                        '<strong style="color:#16a085;">' + dsFmt(b.closing) + '</strong>'];
            });
            body = dsTbl(['Bank','Opening','Received','Spent','Closing'], rows,
                ['Total',
                 dsFmt(dsCards.banks.reduce(function(s,b){return s+b.opening;},0)),
                 dsFmt(dsCards.banks.reduce(function(s,b){return s+b.received;},0)),
                 dsFmt(dsCards.banks.reduce(function(s,b){return s+b.spent;},0)),
                 dsFmt(dsCards.bankClosingTotal)], '#16a085');
        }
        dsShowModal("<i class='fa fa-university'></i> Bank Accounts Detail", '#16a085', body);
    }
});

// Salary details button in section 7
$(document).on('click', '#show-salary-detail', function(e) {
    e.stopPropagation();
    var body = '';
    if (dsCards.salaryDetail.length > 0) {
        var rows = dsCards.salaryDetail.map(function(r){ return [r.name, r.method, dsFmt(r.amount)]; });
        body = dsTbl(['Staff Member','Method','Amount Paid'], rows, ['Total Salary', '', dsFmt(dsCards.salaryTotal)], '#f39c12');
    } else {
        body = '<div style="padding:16px;text-align:center;color:#888;">Total Salary Paid: <strong>' + dsFmt(dsCards.salaryTotal) + '</strong></div>';
    }
    dsShowModal("<i class='fa fa-user'></i> Salary Paid — Breakdown", '#f39c12', body);
});

// ---- Sticky scrollbar ----
(function() {
    var wrap  = document.getElementById('rpt-wrap-daysheet');
    var bar   = document.getElementById('hbar-daysheet');
    var inner = bar ? bar.querySelector('.rpt-hscroll-inner') : null;
    if (!wrap || !bar || !inner) return;
    function reposition() {
        var rect = wrap.getBoundingClientRect();
        bar.style.left  = rect.left + 'px';
        bar.style.width = rect.width + 'px';
        inner.style.width = wrap.scrollWidth + 'px';
    }
    function checkVisibility() {
        var rect = wrap.getBoundingClientRect();
        bar.style.display = (wrap.scrollWidth > wrap.clientWidth && rect.top < window.innerHeight && rect.bottom > window.innerHeight) ? 'block' : 'none';
    }
    reposition(); checkVisibility();
    bar.addEventListener('scroll', function() { wrap.scrollLeft = bar.scrollLeft; });
    wrap.addEventListener('scroll', function() { bar.scrollLeft  = wrap.scrollLeft; });
    window.addEventListener('scroll', function() { reposition(); checkVisibility(); });
    window.addEventListener('resize', function() { reposition(); checkVisibility(); });
})();

// ---- Scroll to top ----
$(window).on('scroll', function() {
    $(this).scrollTop() > 200 ? $('#scroll-to-top-btn').fadeIn(300) : $('#scroll-to-top-btn').fadeOut(300);
});
$('#scroll-to-top-btn').on('click', function() {
    $('html, body').animate({ scrollTop: 0 }, 400);
});
</script>

<style>
.ds-mini-card { transition: transform .15s, box-shadow .15s; }
@media print {
    .rpt-scroll-top-btn, .rpt-action-bar, .rpt-filter-card, .box-header .breadcrumb,
    #ds-card-modal, .modal, #show-salary-detail { display:none !important; }
    #printablediv { margin:0; padding:0; }
    .box { box-shadow:none !important; border:1px solid #ddd !important; page-break-inside:avoid; }
}
</style>
