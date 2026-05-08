<?php
// Variables: $prev_balances, $total_carry_forward_due, $feetypes, $set_classesID, $set_studentID, $globalpayment_max
if (empty($prev_balances) || $total_carry_forward_due <= 0) return;
$cf_base_inv = customCompute($globalpayment_max) ? (int)$globalpayment_max->globalpaymentID + 1 : 1;

// Build JS-side year data (used by Pay Now modal)
$cfYearData = [];
foreach ($prev_balances as $yIdx => $year) {
    $rows = [];
    foreach ($year['invoices'] as $inv) {
        $inv_paid   = isset($year['payments'][$inv->invoiceID]) ? (float)$year['payments'][$inv->invoiceID] : 0;
        $inv_waiver = isset($year['waivers'][$inv->invoiceID])  ? (float)$year['waivers'][$inv->invoiceID]  : 0;
        $inv_net    = (float)$inv->amount - (float)$inv->discount;
        $inv_due    = round($inv_net - $inv_paid - $inv_waiver, 2);
        $rows[] = [
            'invoiceID' => (int)$inv->invoiceID,
            'feetypeID' => (int)$inv->feetypeID,
            'feeName'   => isset($feetypes[$inv->feetypeID]) ? $feetypes[$inv->feetypeID] : '—',
            'amount'    => round($inv_net, 2),
            'due'       => $inv_due,
        ];
    }
    $cfYearData[$yIdx] = [
        'yearName'     => $year['year_name'],
        'schoolyearID' => (int)$year['schoolyearID'],
        'due'          => round($year['due'], 2),
        'invoiceNum'   => $cf_base_inv + $yIdx,
        'rows'         => $rows,
    ];
}
?>

<style>
.cf-year-due { color: #c05621; font-weight: 700; font-size: 14px; }
.cf-grand-total-row { background: #fef3e2; font-weight: 700; font-size: 13px; }
.cf-modal-header-orange {
    background: #c97a2a;
    color: #fff;
    border-radius: 6px 6px 0 0;
    padding: 13px 20px;
}
.cf-modal-header-orange .close { color: #fff; opacity: 1; font-size: 22px; margin-top: -2px; }
.cf-summary-table thead th {
    background: #f1f5f9;
    color: #374151;
    font-size: 12px;
    font-weight: 700;
    padding: 9px 10px;
    border: 1px solid #e2e8f0 !important;
    white-space: nowrap;
}
.cf-summary-table td { vertical-align: middle !important; font-size:13px; border: 1px solid #e9eef5 !important; }
.cf-summary-table tfoot td { background:#fef3e2; font-weight:700; font-size:13px; }
#cfPayNowBody table thead th {
    background: #f1f5f9;
    color: #374151; font-size: 12px; font-weight:700; padding: 8px;
    border: 1px solid #e2e8f0 !important;
}
#cfPayNowBody .cf-total-row td { background: #fef9f0; font-weight: 600; }
</style>

<!-- ── Card 3 wrapper ────────────────────────────────────────────────────── -->
<div class="gp-card">
    <div class="gp-card-header" style="background:#fef3e4; color:#8a4a10; border-bottom:2px solid #f5d49a; padding:11px 18px; display:flex; align-items:center; gap:10px; font-size:13px; font-weight:700;">
        <i class="fa fa-clock-o fa-lg"></i>
        <?=$this->lang->line('global_prev_balance_title')?>
        <span style="background:rgba(138,74,16,.12); border:1px solid rgba(138,74,16,.25); color:#8a4a10; border-radius:20px; padding:2px 12px; font-size:11px; font-weight:700; margin-left:4px;">
            Due: &#8377;<?=number_format($total_carry_forward_due, 2)?>
        </span>
    </div>
    <div class="gp-card-body" style="padding:16px 20px;">

<div class="table-responsive" style="margin-bottom:0;">
    <table class="table table-bordered cf-summary-table" style="margin-bottom:0;">
        <thead>
            <tr>
                <th style="width:22%"><?=$this->lang->line('global_prev_year')?></th>
                <th><?=$this->lang->line('global_prev_total_fees')?></th>
                <th><?=$this->lang->line('global_prev_paid')?></th>
                <th><?=$this->lang->line('global_prev_waiver')?></th>
                <th><?=$this->lang->line('global_prev_balance_due')?></th>
                <th style="width:120px"><?=$this->lang->line('action')?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prev_balances as $yIdx => $year): ?>
            <tr>
                <td><strong><?=htmlspecialchars($year['year_name'])?></strong></td>
                <td class="text-right"><?=number_format($year['total_fee'], 2)?></td>
                <td class="text-right">
                    <?=number_format($year['total_paid'], 2)?>
                    <?php if ($year['total_paid'] > 0): ?>
                    <button type="button"
                            class="btn btn-info btn-xs cf-paid-detail-btn"
                            data-syid="<?=$year['schoolyearID']?>"
                            data-year="<?=htmlspecialchars($year['year_name'])?>"
                            style="margin-left:4px; padding:1px 6px; font-size:11px; vertical-align:middle;"
                            title="View payment details">
                        <i class="fa fa-info-circle"></i>
                    </button>
                    <?php endif; ?>
                </td>
                <td class="text-right"><?=number_format($year['total_waiver'], 2)?></td>
                <td class="text-right cf-year-due"><?=number_format($year['due'], 2)?></td>
                <td class="text-center">
                    <button type="button"
                            class="btn btn-warning btn-xs cf-pay-now-btn"
                            data-year-idx="<?=$yIdx?>"
                            data-year-name="<?=htmlspecialchars($year['year_name'])?>">
                        <i class="fa fa-credit-card"></i>
                        <?=$this->lang->line('global_pay_now')?>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="cf-grand-total-row">
                <td colspan="4"><?=$this->lang->line('global_carry_forward_total')?></td>
                <td class="text-right cf-year-due"><?=number_format($total_carry_forward_due, 2)?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- ── Pay Now Modal ──────────────────────────────────────────────────────── -->
<div class="modal fade" id="cfPayNowModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:90%; max-width:820px;">
        <div class="modal-content" style="border:none; border-radius:6px; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
            <div class="modal-header cf-modal-header-orange">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-credit-card"></i>&nbsp;
                    Pay Previous Year Dues &mdash; <span id="cfPayNowYear"></span>
                </h4>
            </div>
            <div class="modal-body" id="cfPayNowBody" style="padding:20px;"></div>
            <div class="modal-footer" style="background:#f9f9f9; border-radius:0 0 6px 6px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Payment History Modal ─────────────────────────────────────────────── -->
<div class="modal fade" id="cfPaidDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:92%; max-width:1050px;">
        <div class="modal-content" style="border:none; border-radius:6px; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
            <div class="modal-header cf-modal-header-orange">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-history"></i>&nbsp;
                    Payment History &mdash; <span id="cfDetailYearName"></span>
                </h4>
            </div>
            <div class="modal-body" id="cfDetailModalBody" style="padding:20px; min-height:100px;">
                <div class="text-center" style="padding:30px;">
                    <i class="fa fa-spinner fa-spin fa-2x" style="color:#e67e22;"></i>
                </div>
            </div>
            <div class="modal-footer" style="background:#f9f9f9; border-radius:0 0 6px 6px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var cfYearData = <?=json_encode($cfYearData)?>;

(function () {

    // ── Pay Now: open modal ───────────────────────────────────────────────
    $(document).on('click', '.cf-pay-now-btn', function () {
        var yIdx     = $(this).data('year-idx');
        var yearName = $(this).data('year-name') || '';
        var d        = cfYearData[yIdx];
        if (!d) return;

        $('#cfPayNowYear').text(d.yearName || yearName);
        $('#cfPayNowBody').html(buildPayNowForm(d, yIdx));
        $('#cfPayNowModal').modal('show');
    });

    // ── Build Pay Now form HTML ───────────────────────────────────────────
    function buildPayNowForm(d, yIdx) {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2,'0');
        var mm = String(today.getMonth()+1).padStart(2,'0');
        var todayStr = today.getFullYear() + '-' + mm + '-' + dd;

        var html = '';

        // Row 1: invoice fields + payment controls — all above the table
        var lbl = 'font-size:10px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;display:block;';
        html += '<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;margin-bottom:14px;">';
        html += '<div><label style="' + lbl + '"><?=$this->lang->line('global_invoice_name')?> <span style="color:#e53e3e;">*</span></label>';
        html += '<input type="text" id="cfpn-invoicename" class="form-control" value="Previous Year Dues ' + d.yearName + '"></div>';
        html += '<div><label style="' + lbl + '"><?=$this->lang->line('global_invoice_number')?> <span style="color:#e53e3e;">*</span></label>';
        html += '<input type="text" id="cfpn-invoicenumber" class="form-control" value="INV-CF-' + d.invoiceNum + '" readonly style="background:#f1f5f9;font-weight:600;"></div>';
        html += '<div><label style="' + lbl + '"><?=$this->lang->line('global_payment_year')?> <span style="color:#e53e3e;">*</span></label>';
        html += '<input type="text" id="cfpn-paymentyear" class="form-control" value="' + today.getFullYear() + '" maxlength="4"></div>';
        html += '<div><label style="' + lbl + '"><?=$this->lang->line('global_payment_status')?></label>';
        html += '<select id="cfpn-paymentstatus" class="form-control"><option value="paid"><?=$this->lang->line('global_paid')?></option><option value="partial"><?=$this->lang->line('global_partial')?></option><option value="unpaid"><?=$this->lang->line('global_unpaid')?></option></select></div>';
        html += '<div><label style="' + lbl + '"><?=$this->lang->line('global_payment_type')?></label>';
        html += '<select id="cfpn-paymenttype" class="form-control"><option value="cash"><?=$this->lang->line('global_cash')?></option><option value="chaque"><?=$this->lang->line('global_chaque')?></option><option value="digital">Digital</option></select></div>';
        html += '<div><label style="' + lbl + '"><?=$this->lang->line('global_payment_date')?></label>';
        html += '<input type="date" id="cfpn-date" class="form-control" value="' + todayStr + '"></div>';
        html += '<div></div>';
        html += '</div>';

        // Invoice rows (no Fine column)
        html += '<div class="table-responsive">';
        html += '<table class="table table-bordered table-condensed" style="margin-bottom:5px; font-size:13px;">';
        html += '<thead><tr>';
        html += '<th><?=$this->lang->line('global_fees_name')?></th>';
        html += '<th style="text-align:right;"><?=$this->lang->line('global_fees_amount')?></th>';
        html += '<th style="text-align:right;"><?=$this->lang->line('global_due')?></th>';
        html += '<th style="min-width:120px;"><?=$this->lang->line('global_paid_amount')?></th>';
        html += '<th style="min-width:110px;"><?=$this->lang->line('global_weaver')?></th>';
        html += '</tr></thead><tbody>';

        $.each(d.rows, function (i, row) {
            var isPaid = (row.due <= 0);
            html += '<tr>';
            html += '<td>' + row.feeName + '</td>';
            html += '<td class="text-right">' + parseFloat(row.amount).toFixed(2) + '</td>';
            html += '<td class="text-right" data-inv-due="' + row.due + '">';
            html += isPaid
                ? '<span class="text-success"><b>Paid</b></span>'
                : parseFloat(row.due).toFixed(2);
            html += '</td>';
            // Pay Amount
            html += '<td>';
            if (isPaid) {
                html += '<input type="text" name="cfpn-paid-' + row.invoiceID + '-' + row.feetypeID + '" class="cfpn-paid-input form-control input-sm" value="" style="display:none;">';
                html += '<span class="text-success"><i class="fa fa-check"></i> Paid</span>';
            } else {
                html += '<input type="text" name="cfpn-paid-' + row.invoiceID + '-' + row.feetypeID + '" class="cfpn-paid-input form-control input-sm" placeholder="0.00">';
            }
            html += '</td>';
            // Waiver
            html += '<td>';
            if (isPaid) {
                html += '<input type="text" name="cfpn-weaver-' + row.invoiceID + '-' + row.feetypeID + '" class="cfpn-weaver-input form-control input-sm" value="" style="display:none;">';
            } else {
                html += '<input type="text" name="cfpn-weaver-' + row.invoiceID + '-' + row.feetypeID + '" class="cfpn-weaver-input form-control input-sm" placeholder="0.00">';
            }
            html += '</td>';
            html += '</tr>';
        });

        // Totals footer
        html += '</tbody><tfoot>';
        html += '<tr class="cf-total-row"><td colspan="2"><b><?=$this->lang->line('global_total')?></b></td>';
        html += '<td class="text-right"><b>' + parseFloat(d.due).toFixed(2) + '</b></td>';
        html += '<td id="cfpn-totalpaid" class="text-right">0.00</td>';
        html += '<td id="cfpn-totalweaver" class="text-right">0.00</td></tr>';
        html += '<tr class="cf-total-row"><td colspan="2"><?=$this->lang->line('global_total_collection')?> (<?=$this->lang->line('global_paid')?>)</td>';
        html += '<td colspan="3" id="cfpn-totalcollection" class="text-right">0.00</td></tr>';
        html += '</tfoot></table></div>';

        // Submit button
        html += '<div style="text-align:right;padding-top:4px;">';
        html += '<button type="button" class="cfpn-submit-btn"';
        html += ' data-yidx="' + yIdx + '" data-syid="' + d.schoolyearID + '" data-year-name="' + d.yearName + '"';
        html += ' style="background:#c97a2a;color:#fff;border:none;border-radius:6px;padding:9px 28px;font-size:13px;font-weight:700;cursor:pointer;">';
        html += '<i class="fa fa-check-circle"></i> <?=$this->lang->line('global_submit')?></button>';
        html += '</div>';

        return html;
    }

    // ── Live totals in Pay Now modal ──────────────────────────────────────
    $(document).on('keyup input', '.cfpn-paid-input, .cfpn-weaver-input', function () {
        // Paid + Waiver for each row must not exceed that row's due amount
        var $tr          = $(this).closest('tr');
        var $dueTd       = $tr.find('[data-inv-due]');
        var maxDue       = parseFloat($dueTd.data('inv-due')) || 0;
        var $paidInput   = $tr.find('.cfpn-paid-input');
        var $weaverInput = $tr.find('.cfpn-weaver-input');

        if ($(this).hasClass('cfpn-paid-input')) {
            var waiver  = parseFloat($weaverInput.val()) || 0;
            var entered = parseFloat($(this).val()) || 0;
            var maxPaid = Math.max(0, maxDue - waiver);
            if (entered > maxPaid) $(this).val(maxPaid.toFixed(2));
        } else {
            var paidVal  = parseFloat($paidInput.val()) || 0;
            var entered  = parseFloat($(this).val()) || 0;
            var maxWvr   = Math.max(0, maxDue - paidVal);
            if (entered > maxWvr) $(this).val(maxWvr.toFixed(2));
        }

        var paid = 0, weaver = 0;
        $('.cfpn-paid-input:visible').each(function () { paid   += parseFloat($(this).val()) || 0; });
        $('.cfpn-weaver-input:visible').each(function () { weaver += parseFloat($(this).val()) || 0; });
        $('#cfpn-totalpaid').text(paid.toFixed(2));
        $('#cfpn-totalweaver').text(weaver.toFixed(2));
        $('#cfpn-totalcollection').text(paid.toFixed(2));
    });

    // ── Submit Pay Now ────────────────────────────────────────────────────
    $(document).on('click', '.cfpn-submit-btn', function () {
        var $btn     = $(this);
        var yIdx     = $btn.data('yidx');
        var syid     = $btn.data('syid');
        var yearName = $btn.data('year-name');

        var invname = $('#cfpn-invoicename').val();
        var invdesc = $('#cfpn-invoicedesc').val();
        var invnum  = $('#cfpn-invoicenumber').val();
        var invyear = $('#cfpn-paymentyear').val();

        var hasErr = false;
        if (!invname)  { $('#cfpn-invoicename').addClass('errorClass');   hasErr = true; } else { $('#cfpn-invoicename').removeClass('errorClass'); }
        if (!invnum)   { $('#cfpn-invoicenumber').addClass('errorClass'); hasErr = true; } else { $('#cfpn-invoicenumber').removeClass('errorClass'); }
        if (!invyear || invyear.length !== 4) { $('#cfpn-paymentyear').addClass('errorClass'); hasErr = true; } else { $('#cfpn-paymentyear').removeClass('errorClass'); }
        if (hasErr) return;

        var paid = [];
        $('[name^="cfpn-paid-"]').each(function () {
            var parts = this.name.split('-'); // ['cfpn','paid',invoiceID,feetypeID]
            paid.push({ paidFieldID: 'paid-' + parts[2] + '-' + parts[3], value: this.value });
        });
        var weaver = [];
        $('[name^="cfpn-weaver-"]').each(function () {
            var parts = this.name.split('-');
            weaver.push({ weaverFieldID: 'weaver-' + parts[2] + '-' + parts[3], value: this.value });
        });

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

        $.ajax({
            type    : 'POST',
            url     : '<?=base_url('global_payment_new/paymentSend')?>',
            data    : {
                classesID           : <?=(int)$set_classesID?>,
                studentID           : <?=(int)$set_studentID?>,
                invoicename         : invname,
                invoicedescription  : invdesc,
                invoicenumber       : invnum,
                paymentyear         : invyear,
                payment_status      : $('#cfpn-paymentstatus').val(),
                payment_type        : $('#cfpn-paymenttype').val(),
                paid                : paid,
                weaver              : weaver,
                fine                : [],
                created_date        : $('#cfpn-date').val(),
                send_whatsapp       : 0,
                schoolyearID        : syid,
                is_previous_year_amount: yearName
            },
            dataType: 'html',
            success : function (data) {
                var r = jQuery.parseJSON(data);
                if (r.status) {
                    if (r.message) alert(r.message);
                    location.href = '<?=base_url()?>Global_payment_new/print_reciept/' + r.studentID + '/' + r.globalLastID + '/' + syid;
                } else {
                    $btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> <?=$this->lang->line('global_submit')?>');
                    if (r.invoicename)   $('#cfpn-invoicename').addClass('errorClass');
                    if (r.invoicenumber) $('#cfpn-invoicenumber').addClass('errorClass');
                    if (r.paymentyear)   $('#cfpn-paymentyear').addClass('errorClass');
                    if (r.message) alert(r.message);
                }
            },
            error: function () {
                $btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> <?=$this->lang->line('global_submit')?>');
                alert('Request failed. Please try again.');
            }
        });
    });

    // ── Payment History popup ─────────────────────────────────────────────
    $(document).on('click', '.cf-paid-detail-btn', function () {
        var syid_val = $(this).data('syid');
        var yearName = $(this).data('year');
        var studentID = <?=(int)$set_studentID?>;

        $('#cfDetailYearName').text(yearName);
        $('#cfDetailModalBody').html(
            '<div class="text-center" style="padding:30px;">' +
            '<i class="fa fa-spinner fa-spin fa-2x" style="color:#e67e22;"></i>' +
            '</div>'
        );
        $('#cfPaidDetailModal').modal('show');

        $.ajax({
            type    : 'POST',
            url     : '<?=base_url('global_payment_new/getPaymentDetails')?>',
            data    : { studentID: studentID, schoolyearID: syid_val },
            dataType: 'html',
            success : function (data) {
                var r;
                try { r = jQuery.parseJSON(data); } catch (e) {
                    $('#cfDetailModalBody').html('<div class="callout callout-danger"><p>Failed to parse response.</p></div>');
                    return;
                }
                if (!r.status || !r.rows || r.rows.length === 0) {
                    $('#cfDetailModalBody').html('<div class="callout callout-warning"><p>No payment records found.</p></div>');
                    return;
                }

                // Group by globalpaymentID for rowspan + single Print button
                var gpGroups = {}, gpOrder = [];
                $.each(r.rows, function (i, row) {
                    var gid = row.globalpaymentID || ('x' + i);
                    if (!gpGroups[gid]) { gpGroups[gid] = []; gpOrder.push(gid); }
                    gpGroups[gid].push(row);
                });

                var html = '<div class="table-responsive">';
                html += '<table class="table table-bordered" style="font-size:13px; margin-bottom:8px;">';
                html += '<thead><tr style="background:#e67e22; color:#fff;">';
                html += '<th>Invoice No.</th>';
                html += '<th>Fee Type</th>';
                html += '<th class="text-right">Total Pay</th>';
                html += '<th class="text-right">Discount</th>';
                html += '<th class="text-right">Total Collection</th>';
                html += '<th>Clearance</th>';
                html += '<th>Payment Date</th>';
                html += '<th style="text-align:center; width:90px;">Receipt</th>';
                html += '</tr></thead><tbody>';

                var totPay = 0, totDisc = 0, totColl = 0;

                $.each(gpOrder, function (gi, gid) {
                    var rows = gpGroups[gid];
                    var span = rows.length;

                    $.each(rows, function (ri, row) {
                        var pay  = parseFloat(row.paymentamount)    || 0;
                        var disc = parseFloat(row.discount)         || 0;
                        var coll = parseFloat(row.total_collection) || 0;
                        totPay += pay; totDisc += disc; totColl += coll;

                        var st    = (row.payment_status || 'paid').toLowerCase();
                        var badge = st === 'paid'
                            ? '<span class="label label-success">Paid</span>'
                            : (st === 'partial'
                                ? '<span class="label label-warning">Partial</span>'
                                : '<span class="label label-danger">Unpaid</span>');

                        // Invoice No cell — rowspan on first row only
                        var invCell = '';
                        if (ri === 0) {
                            invCell = '<td rowspan="' + span + '" style="vertical-align:middle; background:#fff8f0; font-weight:700;">' +
                                      (row.invoicenumber || '—') + '</td>';
                        }

                        // Print Receipt cell — rowspan on first row only
                        var receiptCell = '';
                        if (ri === 0) {
                            var receiptUrl = '<?=base_url()?>Global_payment_new/print_reciept/' + studentID + '/' + gid + '/' + syid_val;
                            receiptCell = '<td rowspan="' + span + '" style="vertical-align:middle; text-align:center; background:#f0fff4;">' +
                                '<a href="' + receiptUrl + '" target="_blank" class="btn btn-success btn-xs" style="white-space:nowrap;">' +
                                '<i class="fa fa-print"></i> Print' +
                                '</a></td>';
                        }

                        html += '<tr>';
                        html += invCell;
                        html += '<td><span style="color:#27ae60; font-weight:600;">' + (row.feetype || '—') + '</span></td>';
                        html += '<td class="text-right" style="color:#2980b9; font-weight:600;">' + pay.toFixed(2) + '</td>';
                        html += '<td class="text-right">' + disc.toFixed(2) + '</td>';
                        html += '<td class="text-right" style="font-weight:600;">' + coll.toFixed(2) + '</td>';
                        html += '<td>' + badge + '</td>';
                        html += '<td>' + (row.payment_date || '—') + '</td>';
                        html += receiptCell;
                        html += '</tr>';
                    });
                });

                // Total row
                html += '</tbody><tfoot>';
                html += '<tr style="background:#fef9e7; font-weight:700;">';
                html += '<td colspan="2"><strong>Total</strong></td>';
                html += '<td class="text-right" style="color:#2980b9;">' + totPay.toFixed(2) + '</td>';
                html += '<td class="text-right">' + totDisc.toFixed(2) + '</td>';
                html += '<td class="text-right">' + totColl.toFixed(2) + '</td>';
                html += '<td colspan="3"></td>';
                html += '</tr></tfoot></table></div>';

                $('#cfDetailModalBody').html(html);
            },
            error: function () {
                $('#cfDetailModalBody').html('<div class="callout callout-danger"><p>Request failed. Please try again.</p></div>');
            }
        });
    });

})();
</script>

    </div><!-- /.gp-card-body -->
</div><!-- /.gp-card (Card 3) -->
