<style>
/* ── Universal card system ───────────────────────────────────────────── */
.gp-card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.07);
    margin-bottom: 22px;
    overflow: hidden;
    background: #fff;
}
.gp-card-header {
    padding: 11px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .2px;
    border-bottom: 1px solid rgba(0,0,0,0.06);
}
.gp-card-header .gp-badge {
    background: rgba(255,255,255,0.35);
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid rgba(255,255,255,0.4);
}
.gp-card-body { padding: 16px 18px; background: #fff; }

/* Header colours — light professional */
.gp-hdr-blue   { background: #eaf1fb; color:#1d4e9e; border-bottom:2px solid #c5d9f5; }
.gp-hdr-green  { background: #e8f5ed; color:#1a6b3e; border-bottom:2px solid #b8dfc8; }
.gp-hdr-orange { background: #fef3e4; color:#8a4a10; border-bottom:2px solid #f5d49a; }
.gp-hdr-slate  { background: #f1f4f8; color:#374151; border-bottom:2px solid #d1d9e6; }
.gp-hdr-blue .gp-badge   { background:rgba(29,78,158,.10); border-color:rgba(29,78,158,.2); color:#1d4e9e; }
.gp-hdr-green .gp-badge  { background:rgba(26,107,62,.10); border-color:rgba(26,107,62,.2); color:#1a6b3e; }
.gp-hdr-orange .gp-badge { background:rgba(138,74,16,.10); border-color:rgba(138,74,16,.2); color:#8a4a10; }

/* ── Section 1 – Student profile ──────────────────────────────────────── */
.gp-student-wrap {
    display: flex;
    align-items: center;
    gap: 0;
    padding: 10px 0;
}
.gp-student-photo {
    flex: 0 0 90px;
    text-align: center;
    padding: 0 16px 0 6px;
    border-right: 2px solid #edf2f7;
}
.gp-student-photo img {
    width: 68px; height: 68px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #3a7bd5;
    box-shadow: 0 2px 8px rgba(58,123,213,.18);
}
.gp-student-fields {
    flex: 1;
    display: flex;
    flex-wrap: wrap;
    gap: 7px 10px;
    padding: 4px 16px;
}
.gp-field-pill {
    background: #f7faff;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
    border: 1px solid #dde8f8;
}
.gp-field-pill .lbl {
    color: #718096;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
}
.gp-field-pill .sep { color: #cbd5e0; }
.gp-field-pill .val {
    color: #1a202c;
    font-weight: 700;
    font-size: 13px;
}

/* ── Sub-section labels ──────────────────────────────────────────────── */
.gp-sub-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    padding: 6px 0 8px 0;
    margin-bottom: 10px;
    border-bottom: 2px solid #edf2f7;
    display: flex;
    align-items: center;
    gap: 7px;
}
.gp-sub-label.blue  { color: #3a7bd5; border-color: #dde8f8; }
.gp-sub-label.green { color: #2e8b57; border-color: #d4eddf; }

/* ── Tables ──────────────────────────────────────────────────────────── */
.gp-table { font-size: 13px; margin-bottom: 0; }
.gp-table thead th {
    /* background: #e2e8f0; */
    color: #000000 !important;
    font-size: 12px;
    font-weight: 700;
    padding: 9px 10px;
    border: 1px solid #cbd5e0 !important;
    white-space: nowrap;
}
.gp-table tbody tr:hover { background: #fafbff; }
.gp-table tfoot td {
    background: #f8fafc;
    font-weight: 700;
    font-size: 13px;
    border: 1px solid #e2e8f0 !important;
}
.gp-table tbody td { border: 1px solid #e9eef5 !important; vertical-align: middle !important; }

/* ── Invoice entry form grid ─────────────────────────────────────────── */
.gp-inv-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 10px 14px;
    margin-bottom: 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px 16px;
}
.gp-inv-grid label {
    font-size: 10px;
    font-weight: 700;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-bottom: 4px;
    display: block;
}

/* ── Submit bar ──────────────────────────────────────────────────────── */
.gp-submit-bar {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 10px 10px;
    padding: 12px 18px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.gp-ctrl-row {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
}
.gp-ctrl-row > div label {
    font-size: 10px;
    font-weight: 700;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-bottom: 4px;
    display: block;
}

/* ── Search card ─────────────────────────────────────────────────────── */
.gp-search-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 12px;
    align-items: flex-end;
}
.gp-search-grid label {
    font-size: 10px; font-weight:700; color:#718096;
    text-transform:uppercase; letter-spacing:.4px;
    margin-bottom:4px; display:block;
}

/* ── Misc ────────────────────────────────────────────────────────────── */
.errorClass { border:1px solid #e53e3e !important; }
.label-paid    { background:#c6f6d5; color:#276749; border:1px solid #9ae6b4; border-radius:4px; padding:2px 8px; font-size:11px; font-weight:700; }
.label-partial { background:#fef3c7; color:#92400e; border:1px solid #fcd34d; border-radius:4px; padding:2px 8px; font-size:11px; font-weight:700; }
.label-unpaid  { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; border-radius:4px; padding:2px 8px; font-size:11px; font-weight:700; }
</style>

<!-- ── Page wrapper ─────────────────────────────────────────────────────── -->
<div style="margin-bottom:6px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
    <h4 style="margin:0; font-size:16px; font-weight:700; color:#2d3748; display:flex; align-items:center; gap:8px;">
        <i class="fa fa-balance-scale" style="color:#3a7bd5;"></i> <?=$this->lang->line('panel_title')?>
    </h4>
    <ol class="breadcrumb" style="margin:0; padding:5px 14px; background:#f1f5f9; border-radius:20px; font-size:12px; border:1px solid #e2e8f0;">
        <li><a href="<?=base_url('dashboard/index')?>" style="color:#3a7bd5;"><i class="fa fa-home"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
        <li class="active" style="color:#718096;"><?=$this->lang->line('menu_global_payment_new')?></li>
    </ol>
</div>

<!-- ── SEARCH BAR ─────────────────────────────────────────────────────────── -->
<?php if($this->uri->segment(3) == ""): ?>
<div class="gp-card">
    <div class="gp-card-header gp-hdr-slate">
        <i class="fa fa-search"></i> Find Student
    </div>
    <div class="gp-card-body" style="padding:14px 18px;">
        <form method="POST">
            <div class="gp-search-grid">
                <div>
                    <label><?=$this->lang->line('global_classes')?> <span style="color:#e53e3e;">*</span></label>
                    <?php
                        $classArray = ["0" => $this->lang->line("global_select_classes")];
                        foreach ($classes as $c) { $classArray[$c->classesID] = $c->classes; }
                        echo form_dropdown("classesID", $classArray, set_value("classesID", $set_classesID), "id='classesID' class='form-control select2'");
                    ?>
                </div>
                <div>
                    <label><?=$this->lang->line('global_section')?></label>
                    <?php
                        $sectionArray = ["0" => $this->lang->line("global_select_section")];
                        if ($sections != 0) { foreach ($sections as $s) { $sectionArray[$s->sectionID] = $s->section; } }
                        echo form_dropdown("sectionID", $sectionArray, set_value("sectionID", $set_sectionID), "id='sectionID' class='form-control select2'");
                    ?>
                </div>
                <div>
                    <label><?=$this->lang->line('global_student')?> <span style="color:#e53e3e;">*</span></label>
                    <?php
                        $studentArray = ["0" => $this->lang->line("global_select_student")];
                        if (customCompute($students)) { foreach ($students as $st) { $studentArray[$st->srstudentID] = $st->srname.' - '.$this->lang->line('global_roll').' - '.$st->srroll; } }
                        echo form_dropdown("studentID", $studentArray, set_value("studentID", $set_studentID), "id='studentID' class='form-control select2'");
                    ?>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="height:34px; padding:0 20px; font-weight:600; font-size:13px; background:#3a7bd5; border-color:#3a7bd5; border-radius:6px;">
                        <i class="fa fa-search"></i> <?=$this->lang->line('global_payment_search')?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if (customCompute($single_student)): ?>

<!-- ══════════════════════════════════════════════════════════════════════ -->
<!-- CARD 1 – Student Details                                              -->
<!-- ══════════════════════════════════════════════════════════════════════ -->
<div class="gp-card">
    <div class="gp-card-header gp-hdr-blue">
        <i class="fa fa-user-circle-o"></i> Student Details
        <span class="gp-badge"><?=$single_student->srregisterNO?></span>
    </div>
    <div class="gp-card-body" style="padding:10px 16px;">
        <div class="gp-student-wrap">
            <div class="gp-student-photo">
                <img src="<?=base_url('uploads/images/'.$single_student->photo)?>" alt="photo"
                     onerror="this.src='<?=base_url('uploads/images/default.png')?>'">
            </div>
            <div class="gp-student-fields">
                <div class="gp-field-pill">
                    <span class="lbl">Name</span><span class="sep">|</span>
                    <span class="val"><?=$single_student->srname?></span>
                </div>
                <div class="gp-field-pill">
                    <span class="lbl">Class</span><span class="sep">|</span>
                    <span class="val"><?=customCompute($single_classes) ? $single_classes->classes : '—'?></span>
                </div>
                <div class="gp-field-pill">
                    <span class="lbl">Section</span><span class="sep">|</span>
                    <span class="val"><?=customCompute($single_section) ? $single_section->section : '—'?></span>
                </div>
                <div class="gp-field-pill">
                    <span class="lbl">Roll</span><span class="sep">|</span>
                    <span class="val"><?=$single_student->srroll?></span>
                </div>
                <div class="gp-field-pill">
                    <span class="lbl">Reg No</span><span class="sep">|</span>
                    <span class="val"><?=$single_student->srregisterNO?></span>
                </div>
                <?php if (customCompute($single_group)): ?>
                <div class="gp-field-pill">
                    <span class="lbl">Group</span><span class="sep">|</span>
                    <span class="val"><?=$single_group->group?></span>
                </div>
                <?php endif; ?>
                <div class="gp-field-pill">
                    <span class="lbl">Father</span><span class="sep">|</span>
                    <span class="val"><?=$single_student->father_name?></span>
                </div>
                <?php if (!empty($single_student->phone)): ?>
                <div class="gp-field-pill">
                    <span class="lbl">Phone</span><span class="sep">|</span>
                    <span class="val"><?=$single_student->phone?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════ -->
<!-- CARD 2 – Payments & Invoice Entry                                     -->
<!-- ══════════════════════════════════════════════════════════════════════ -->
<div class="gp-card">
    <div class="gp-card-header gp-hdr-green">
        <i class="fa fa-file-text-o"></i> Payments &amp; Invoice Entry
        <span class="gp-badge">Current Year</span>
        <button type="button" id="openPayHistBtn"
                onclick="$('#payHistModal').modal('show')"
                style="margin-left:auto; background:#1a6b3e; color:#fff; border:none; border-radius:6px; padding:5px 14px; font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px;">
            <i class="fa fa-history"></i> Payment History
        </button>
    </div>
    <div class="gp-card-body" style="padding:16px 18px;">

        <?php if (customCompute($invoices)):
            $loginuserID = $this->session->userdata("loginuserID");
            $loggedUser  = $this->db->get_where('user', ['userID' => $loginuserID])->row();
            $accountant  = (($loggedUser && $loggedUser->is_able_payment_discount == 1) || $this->session->userdata('usertype') == 'Admin') ? "" : "d-none";
        ?>

        <!-- 2a. New Invoice Entry form (shown FIRST) -->
        <div class="gp-sub-label blue">
            <i class="fa fa-plus-square-o"></i> New Payment Entry
        </div>
        <div class="gp-inv-grid">
            <div>
                <label><?=$this->lang->line('global_invoice_name')?> <span style="color:#e53e3e;">*</span></label>
                <input class="form-control" id="invoicename" type="text" name="invoicename"
                       value="<?=$single_student->srregisterNO.'-'.$single_student->srname?>">
                <input type="hidden" id="inv_name" value="<?=$single_student->srregisterNO.'-'.$single_student->srname?>">
            </div>
            <div>
                <label>Manual Receipt</label>
                <input class="form-control" id="invoicedescription" type="text" name="invoicedescription">
            </div>
            <div>
                <label><?=$this->lang->line('global_invoice_number')?> <span style="color:#e53e3e;">*</span></label>
                <input class="form-control" id="invoicenumber" type="text" name="invoicenumber"
                       value="INV-G-<?=(customCompute($globalpayment_max)>0) ? $globalpayment_max->globalpaymentID+1 : '1'?>" readonly
                       style="background:#f1f5f9; color:#4a5568; font-weight:600;">
            </div>
            <div>
                <label><?=$this->lang->line('global_payment_year')?> <span style="color:#e53e3e;">*</span></label>
                <input class="form-control" id="paymentyear" type="text" name="paymentyear" value="<?=date('Y')?>" maxlength="4">
            </div>
            <!-- Row 2: payment controls -->
            <div>
                <label><?=$this->lang->line('global_payment_status')?></label>
                <select class="form-control" id="payment_status">
                    <option value="paid"><?=$this->lang->line('global_paid')?></option>
                    <option value="partial"><?=$this->lang->line('global_partial')?></option>
                    <option value="unpaid"><?=$this->lang->line('global_unpaid')?></option>
                </select>
            </div>
            <div>
                <label><?=$this->lang->line('global_payment_type')?></label>
                <select class="form-control" id="payment_type">
                    <option value="cash"><?=$this->lang->line('global_cash')?></option>
                    <option value="chaque"><?=$this->lang->line('global_chaque')?></option>
                    <option value="digital">Digital</option>
                    <option value="others">Others</option>
                </select>
                <div id="payment_other_details_div" style="display:none; margin-top:6px;">
                    <div class="input-group">
                        <select id="payment_other_details" class="form-control" style="width:calc(100% - 38px);">
                            <option value="">-- Select Bank --</option>
                        </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" id="add_bank_btn" title="Add new bank"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div>
                <label>Payment Date</label>
                <input type="date" name="created_date" id="created_date" class="form-control">
            </div>
            <div style="display:flex; align-items:flex-end; padding-bottom:4px;">
                <label for="send_whatsapp" id="wa_label" style="
                    display:inline-flex; align-items:center; gap:7px;
                    border:2px solid #25D366; border-radius:8px;
                    padding:5px 11px 5px 8px; cursor:pointer; margin:0;
                    background:#f0fdf4; transition:background .15s, border-color .15s;
                    user-select:none; white-space:nowrap;
                ">
                    <input type="checkbox" name="send_whatsapp" id="send_whatsapp" checked
                        style="width:15px;height:15px;accent-color:#25D366;cursor:pointer;flex-shrink:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#25D366" style="flex-shrink:0;">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.85L.057 23.16a.75.75 0 0 0 .916.916l5.344-1.472A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22a9.942 9.942 0 0 1-5.13-1.426l-.36-.214-3.733 1.028 1.046-3.624-.235-.373A9.944 9.944 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                    </svg>
                    <span id="wa_label_text" style="font-size:12px;font-weight:600;color:#16a34a;">Send WhatsApp</span>
                </label>
                <script>
                    (function(){
                        var cb = document.getElementById('send_whatsapp');
                        var lbl = document.getElementById('wa_label');
                        var txt = document.getElementById('wa_label_text');
                        var icon = lbl.querySelector('svg');
                        function applyState(){
                            if(cb.checked){
                                lbl.style.borderColor='#25D366'; lbl.style.background='#f0fdf4';
                                icon.style.fill='#25D366'; txt.style.color='#16a34a';
                            } else {
                                lbl.style.borderColor='#cbd5e1'; lbl.style.background='#f8fafc';
                                icon.style.fill='#94a3b8'; txt.style.color='#94a3b8';
                            }
                        }
                        cb.addEventListener('change', applyState);
                        applyState();
                    })();
                </script>
            </div>
        </div>

        <!-- 2b. Fee entry table -->
        <div class="table-responsive" style="margin-bottom:0;">
            <table class="table table-bordered gp-table">
                <thead>
                    <tr>
                        <th style="width:3%">#</th>
                        <th style="width:4%"></th>
                        <th><?=$this->lang->line('global_fees_name')?></th>
                        <th class="text-right"><?=$this->lang->line('global_fees_amount')?></th>
                        <th class="text-right"><?=$this->lang->line('global_due')?></th>
                        <th style="min-width:120px;"><?=$this->lang->line('global_paid_amount')?></th>
                        <th class="<?=$accountant?>" style="min-width:110px;">Discount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total=0; $totalDue=0; $i=1;
                    foreach ($invoices as $invoice) {
                        $total += $invoice->amount;
                        if ($invoice->discount > 0) $total -= $invoice->discount;
                        $payment = isset($payments[$invoice->invoiceID]) ? $payments[$invoice->invoiceID] : 0;
                        $due = $invoice->amount - $payment;
                        if ($invoice->discount > 0) $due -= $invoice->discount;
                        if (isset($weavers[$invoice->invoiceID])) $due -= $weavers[$invoice->invoiceID];
                        $due = round($due, 2);
                        $totalDue += $due;
                    ?>
                    <tr>
                        <td style="color:#718096;"><?=$i?></td>
                        <td>
                            <!-- <input type="checkbox" class="record_checkbox" value="<?=$invoice->invoiceID?>" data-maininvoiceid="<?=$invoice->maininvoiceID?>"> -->
                            <!-- <a class="update_single btn btn-xs" data-id="<?=$invoice->invoiceID?>" data-mainid="<?=$invoice->maininvoiceID?>"
                               style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; border-radius:4px; padding:2px 6px;">
                                <i class="fa fa-trash-o"></i>
                            </a> -->
                        </td>
                        <td style="font-weight:600; color:#2d3748;"><?=isset($feetypes[$invoice->feetypeID]) ? $feetypes[$invoice->feetypeID] : ''?></td>
                        <td class="text-right" style="color:#4a5568;">
                            <?=number_format(($invoice->discount > 0) ? ($invoice->amount - $invoice->discount) : $invoice->amount, 2)?>
                        </td>
                        <td class="text-right" id="_due_<?=$i-1?>" data-original="<?=$due?>"
                            style="font-weight:700; color:<?=($due<=0)?'#276749':'#c05621'?>;">
                            <?=$due?>
                        </td>
                        <td>
                            <?php if ($due <= 0): ?>
                                <span class="label-paid">Paid</span>
                                <input style="display:none" name="paid-<?=$invoice->invoiceID?>-<?=$invoice->feetypeID?>" class="form-control paid_amount _paid_<?=$i-1?>" type="text">
                            <?php else: ?>
                                <input name="paid-<?=$invoice->invoiceID?>-<?=$invoice->feetypeID?>" class="form-control input-sm paid_amount _paid_<?=$i-1?>" type="text" placeholder="0.00">
                            <?php endif; ?>
                        </td>
                        <td class="<?=$accountant?>">
                            <?php if ($due > 0): ?>
                                <input name="weaver-<?=$invoice->invoiceID?>-<?=$invoice->feetypeID?>" class="form-control input-sm weaver _weaver_<?=$i-1?>" type="text" placeholder="0.00">
                            <?php else: ?>
                                <input style="display:none" name="weaver-<?=$invoice->invoiceID?>-<?=$invoice->feetypeID?>" class="form-control weaver _weaver_<?=$i-1?>" type="text">
                            <?php endif; ?>
                        </td>
                        <input type="hidden" name="fine-<?=$invoice->invoiceID?>-<?=$invoice->feetypeID?>" class="fine" value="">
                    </tr>
                    <?php $i++; } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"></td>
                        <td><b><?=$this->lang->line('global_total')?></b></td>
                        <td class="text-right"><b><?=number_format($total,2)?></b></td>
                        <td class="text-right"><b><?=number_format($totalDue,2)?></b></td>
                        <td id="set_paid_amount" class="text-right <?=$accountant?>">0.00</td>
                        <td id="set_weaver" class="text-right <?=$accountant?>">0.00</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right" style="color:#c05621; font-weight:700;">Remaining Due</td>
                        <td colspan="3" id="set_total_due" class="text-right" style="color:#c05621; font-weight:700; font-size:14px;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right" style="color:#2d3748; font-weight:700;">
                            <?=$this->lang->line('global_total_collection').' ('.$this->lang->line('global_paid').')'?>
                        </td>
                        <td colspan="3" id="TottalCollection" class="text-right" style="color:#2e8b57; font-weight:700; font-size:14px;">0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 2c. Submit bar -->
        <div style="text-align:right; padding:10px 0 14px 0;">
            <button id="add_payment" type="button"
                    style="background:#2e8b57; color:#fff; border:none; border-radius:6px; padding:9px 32px; font-size:13px; font-weight:700; letter-spacing:.3px; cursor:pointer; box-shadow:0 2px 6px rgba(46,139,87,.25);">
                <i class="fa fa-check-circle"></i> &nbsp;<?=$this->lang->line('global_submit')?>
            </button>
        </div>

        <!-- ── separator ── -->
        <div style="height:1px; background:#edf2f7; margin:20px 0;"></div>

        <?php endif; // customCompute($invoices) ?>

    </div><!-- /.gp-card-body -->
</div><!-- /.gp-card (Card 2) -->

<!-- ── Payment History Modal ─────────────────────────────────────────────── -->
<div class="modal fade" id="payHistModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:92%; max-width:1000px;">
        <div class="modal-content" style="border:none; border-radius:10px; box-shadow:0 8px 32px rgba(0,0,0,0.15); overflow:hidden;">
            <div class="modal-header" style="background:#e8f5ed; border-bottom:2px solid #b8dfc8; padding:12px 18px; display:flex; align-items:center; gap:10px;">
                <h4 class="modal-title" style="margin:0; font-size:14px; font-weight:700; color:#1a6b3e; display:flex; align-items:center; gap:8px;">
                    <i class="fa fa-history"></i> Payment History &mdash; Current Year
                </h4>
                <button type="button" class="close" data-dismiss="modal"
                        style="margin-left:auto; background:none; border:none; font-size:20px; color:#1a6b3e; opacity:.7; cursor:pointer; line-height:1;">&times;</button>
            </div>
            <div class="modal-body" style="padding:18px 20px;">
                <div class="table-responsive">
                    <table class="table table-bordered gp-table" style="font-size:13px; margin-bottom:0;">
                        <thead>
                            <tr>
                                <th><?=$this->lang->line('global_invoice_number')?></th>
                                <th>Fee Type</th>
                                <th class="text-right"><?=$this->lang->line('global_total_pay')?></th>
                                <th class="text-right">Discount</th>
                                <th class="text-right"><?=$this->lang->line('global_total_collection')?></th>
                                <th><?=$this->lang->line('global_clearance')?></th>
                                <th><?=$this->lang->line('global_payment_date')?></th>
                                <th style="text-align:center;"><?=$this->lang->line('action')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $invoice_total=0; $invoice_weaver=0; $invoice_paid_fine=0; $has_history=false;
                            if (customCompute($globalpayments)) { foreach ($globalpayments as $gp) {
                                $gid   = $gp->globalpaymentID;
                                $tpaid = isset($paidpayments['paid'][$gid])   ? $paidpayments['paid'][$gid]   : 0;
                                $twvr  = isset($paidpayments['weaver'][$gid]) ? $paidpayments['weaver'][$gid] : 0;
                                $tinvdisc = isset($paidpayments['invoice_discount'][$gid]) ? $paidpayments['invoice_discount'][$gid] : 0;
                                $tdiscount = $twvr + $tinvdisc;
                                $tfine = isset($paidpayments['fine'][$gid])   ? $paidpayments['fine'][$gid]   : 0;
                                if ($tpaid == 0 && $tfine == 0) continue;
                                $has_history = true;
                                $invoice_total     += $tpaid;
                                $invoice_weaver    += $tdiscount;
                                $invoice_paid_fine += $tpaid;

                                $feeTypes    = isset($paidpayments['invoice_id'][$gid])    ? $paidpayments['invoice_id'][$gid]    : ['—'];
                                $paidPerType = isset($paidpayments['paid_per_type'][$gid]) ? $paidpayments['paid_per_type'][$gid] : [$tpaid];
                                $rowspan     = count($feeTypes);
                                $cl          = strtolower($gp->clearancetype);
                                $badge       = ($cl=='paid') ? '<span class="label-paid">Paid</span>'
                                             : (($cl=='partial') ? '<span class="label-partial">Partial</span>'
                                             : '<span class="label-unpaid">Unpaid</span>');
                                $payDate = isset($paidpayments['paiddate'][$gid]) ? date('d-M-Y', strtotime($paidpayments['paiddate'][$gid])) : '';

                                foreach ($feeTypes as $ri => $feeName):
                                    $rowPaid = isset($paidPerType[$ri]) ? $paidPerType[$ri] : 0;
                            ?>
                                <tr>
                                    <?php if ($ri === 0): ?>
                                    <td rowspan="<?=$rowspan?>" style="font-weight:600; color:#3a7bd5; vertical-align:middle;"><?='INV-G-'.$gid?></td>
                                    <?php endif; ?>
                                    <td style="color:#2e8b57; font-weight:600;"><?=htmlspecialchars($feeName)?></td>
                                    <td class="text-right" style="color:#3a7bd5; font-weight:600;"><?=number_format($rowPaid,2)?></td>
                                    <?php if ($ri === 0): ?>
                                    <td rowspan="<?=$rowspan?>" class="text-right" style="vertical-align:middle;"><?=number_format($tdiscount,2)?></td>
                                    <td rowspan="<?=$rowspan?>" class="text-right" style="font-weight:600; vertical-align:middle;"><?=number_format($tpaid,2)?></td>
                                    <td rowspan="<?=$rowspan?>" style="vertical-align:middle;"><?=$badge?></td>
                                    <td rowspan="<?=$rowspan?>" style="color:#4a5568; vertical-align:middle;"><?=$payDate?></td>
                                    <td rowspan="<?=$rowspan?>" style="text-align:center; vertical-align:middle;">
                                        <a href="<?=base_url('Global_payment_new/print_reciept/'.$set_studentID.'/'.$gid)?>" target="_blank"
                                           style="background:#e8f5e9; color:#2e8b57; border:1px solid #a8d5b5; border-radius:4px; padding:4px 9px; font-size:12px; font-weight:600; text-decoration:none; display:inline-block;">
                                            <i class="fa fa-print"></i> Print
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; } } ?>
                            <?php if (!$has_history): ?>
                                <tr>
                                    <td colspan="8" class="text-center" style="padding:24px; color:#a0aec0; font-style:italic;">
                                        <i class="fa fa-inbox" style="margin-right:6px;"></i>No payments recorded for current year
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if ($has_history): ?>
                        <tfoot>
                            <tr>
                                <td colspan="2"><b><?=$this->lang->line('global_total')?></b></td>
                                <td class="text-right"><b><?=number_format($invoice_total,2)?></b></td>
                                <td class="text-right"><b><?=number_format($invoice_weaver,2)?></b></td>
                                <td class="text-right"><b><?=number_format($invoice_paid_fine,2)?></b></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:10px 18px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════ -->
<!-- CARD 3 – Previous Year Balance Statement                              -->
<!-- ══════════════════════════════════════════════════════════════════════ -->
<?php if (!empty($prev_balances) && $total_carry_forward_due > 0): ?>
    <?php $this->load->view('global_payment_new/prev_balance_statement', $this->data); ?>
<?php endif; ?>

<?php endif; // customCompute($single_student) ?>

<script type="text/javascript">
$('.select2').select2();

// ── Cascade dropdowns ──────────────────────────────────────────────────────
$("#classesID").change(function() {
    var id = $(this).val();
    if (parseInt(id) && id !== '0') {
        $.ajax({ type:'POST', url:"<?=base_url('global_payment_new/sectioncall')?>",
            data:{id:id}, dataType:"html",
            success:function(d){ $('#sectionID').html(d); $('#studentID').html('<option value="0">Select Student</option>'); }
        });
    }
});
$("#sectionID").change(function() {
    var sid = $(this).val(), cid = $('#classesID').val();
    $.ajax({ type:'POST', url:"<?=base_url('global_payment_new/studentcall')?>",
        data:{classesID:cid, sectionID:sid}, dataType:"html",
        success:function(d){ $('#studentID').html(d); }
    });
});

// Banks dropdown helper
function loadBanksDropdown(selector, selectVal) {
    $.post('<?= base_url('banks/getBanksList') ?>', {}, function (data) {
        var r = JSON.parse(data);
        var opts = '<option value="">-- Select Bank --</option>';
        if (r.status) {
            $.each(r.banks, function (i, b) {
                opts += '<option value="' + b.bank_name + '"' + (b.bank_name === selectVal ? ' selected' : '') + '>' + b.bank_name + '</option>';
            });
        }
        $(selector).html(opts);
    }, 'html');
}

// Show/hide "Others" bank dropdown based on payment type selection
$(document).on('change', '#payment_type', function () {
    if ($(this).val() === 'others') {
        $('#payment_other_details_div').show();
        loadBanksDropdown('#payment_other_details', null);
    } else {
        $('#payment_other_details_div').hide();
        $('#payment_other_details').val('');
    }
});

// Add bank inline — open modal
$(document).on('click', '#add_bank_btn', function () {
    $('#new_bank_name').val('');
    $('#addBankError').hide().text('');
    $('#addBankModal').modal('show');
});

// Save new bank from modal
$(document).on('click', '#saveBankBtn', function () {
    var name = $.trim($('#new_bank_name').val());
    if (!name) { $('#addBankError').text('Bank name is required').show(); return; }
    $.post('<?= base_url('banks/addBankAjax') ?>', { bank_name: name }, function (data) {
        var r = JSON.parse(data);
        if (r.status) {
            $('#addBankModal').modal('hide');
            loadBanksDropdown('#payment_other_details', r.bank_name);
        } else {
            $('#addBankError').text(r.msg).show();
        }
    }, 'html');
});

// ── Number helpers ─────────────────────────────────────────────────────────
var globalPaid=0, globalFine=0, globalWeaver=0;

function recalculateGrandTotalDue() {
    var totalDue = parseFloat("<?=$totalDue ?? 0?>");
    var remaining = totalDue - globalPaid - globalWeaver;
    $("#set_total_due").text(remaining.toFixed(2));
}

function recalcPaidAndWeaver() {
    var paidSum = 0, weaverSum = 0;
    $(".paid_amount").each(function(i) {
        var original = parseFloat($('#_due_' + i).data('original')) || 0;
        var weaver   = parseFloat($('._weaver_' + i).val()) || 0;
        var val      = parseFloat($(this).val()) || 0;
        if (val + weaver > original) {
            $(this).val(Math.max(0, original - weaver).toFixed(2));
            val = parseFloat($(this).val()) || 0;
        }
        paidSum += val;
    });
    $(".weaver").each(function() { weaverSum += parseFloat($(this).val()) || 0; });
    globalPaid   = paidSum;
    globalWeaver = weaverSum;
    $("#set_paid_amount").text(paidSum.toFixed(2));
    $("#TottalCollection").text(globalPaid.toFixed(2));
    $("#set_weaver").text(weaverSum.toFixed(2));
    recalculateGrandTotalDue();
}

$(document).on("keyup input", ".paid_amount", function() {
    var match = $(this).attr('class').match(/_paid_(\d+)/);
    if (match) {
        var idx      = match[1];
        var original = parseFloat($('#_due_' + idx).data('original')) || 0;
        var weaver   = parseFloat($('._weaver_' + idx).val()) || 0;
        var maxPaid  = Math.max(0, original - weaver);
        if ((parseFloat($(this).val()) || 0) > maxPaid) {
            $(this).val(maxPaid.toFixed(2));
        }
    }
    recalcPaidAndWeaver();
});

$(document).on("keyup input", ".weaver", function() {
    var match = $(this).attr('class').match(/_weaver_(\d+)/);
    if (match) {
        var idx      = match[1];
        var original = parseFloat($('#_due_' + idx).data('original')) || 0;
        var paidVal  = parseFloat($('._paid_' + idx).val()) || 0;
        var maxWvr   = Math.max(0, original - paidVal);
        if ((parseFloat($(this).val()) || 0) > maxWvr) {
            $(this).val(maxWvr.toFixed(2));
        }
    }
    recalcPaidAndWeaver();
});

// ── Current-year payment submit ────────────────────────────────────────────
$('#add_payment').on('click', function() {
    var invoicename   = $('#invoicename');
    var invoicenumber = $('#invoicenumber');
    var paymentyear   = $('#paymentyear');
    var error = 0;

    if (!invoicename.val())  { invoicename.addClass('errorClass'); error++; } else { invoicename.removeClass('errorClass'); }
    if (!invoicenumber.val()){ invoicenumber.addClass('errorClass'); error++; } else { invoicenumber.removeClass('errorClass'); }
    if (!paymentyear.val() || paymentyear.val().length != 4) { paymentyear.addClass('errorClass'); error++; } else { paymentyear.removeClass('errorClass'); }
    if (error > 0) return;

    var paid   = $('input[name^="paid-"]').filter(function(){ return parseFloat(this.value) > 0; }).map(function(){ return {paidFieldID:this.name, value:this.value}; }).get();
    var weaver = $('input[name^="weaver-"]').filter(function(){ return parseFloat(this.value) > 0; }).map(function(){ return {weaverFieldID:this.name, value:this.value}; }).get();
    var paymentOtherDetails = $('#payment_type').val() === 'others' ? $('#payment_other_details').val() : '';

    $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

    $.ajax({
        type:'POST', url:"<?=base_url('global_payment_new/paymentSend')?>",
        data:{
            classesID:          <?=$set_classesID?>,
            studentID:          <?=$set_studentID?>,
            invoicename:        invoicename.val(),
            invoicedescription: $('#invoicedescription').val(),
            invoicenumber:      invoicenumber.val(),
            paymentyear:        paymentyear.val(),
            payment_status:     $('#payment_status').val(),
            payment_type:       $('#payment_type').val(),
            paid:                    paid,
            weaver:                  weaver,
            payment_other_details:   paymentOtherDetails,
            created_date:            $('#created_date').val(),
            send_whatsapp:      $('#send_whatsapp').is(':checked') ? 1 : 0
        },
        dataType:'html',
        success:function(data) {
            var r = jQuery.parseJSON(data);
            if (r.status) {
                if (r.message) alert(r.message);
                location.href = "<?=base_url()?>Global_payment_new/print_reciept/" + r.studentID + '/' + r.globalLastID;
            } else {
                $('#add_payment').prop('disabled',false).html('<i class="fa fa-check-circle"></i> &nbsp;<?=$this->lang->line('global_submit')?>');
                if (r.invoicename)   $('#invoicename').addClass('errorClass');
                if (r.invoicenumber) $('#invoicenumber').addClass('errorClass');
                if (r.paymentyear)   $('#paymentyear').addClass('errorClass');
            }
        },
        error: function() {
            $('#add_payment').prop('disabled',false).html('<i class="fa fa-check-circle"></i> &nbsp;<?=$this->lang->line('global_submit')?>');
        }
    });
});

// ── Delete invoice ─────────────────────────────────────────────────────────
$(document).on('click', '.update_single', function() {
    var invoiceID     = $(this).data('id');
    var maininvoiceID = $(this).data('mainid');
    if (confirm('Delete this invoice? (Only if no payments exist)')) {
        $.post("<?=base_url('global_payment_new/updateSingle')?>",
            {invoiceID: invoiceID, maininvoiceID: maininvoiceID},
            function() { location.reload(); }
        );
    }
});
</script>

<!-- Add Bank Modal -->
<div class="modal fade" id="addBankModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width:380px;">
        <div class="modal-content">
            <div class="modal-header" style="background:#1d4e9e; color:#fff; border-radius:4px 4px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-university"></i> Add New Bank</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" id="new_bank_name" class="form-control" placeholder="e.g. HDFC, ICICI, Axis...">
                    <span id="addBankError" class="text-danger" style="display:none;"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveBankBtn">Save Bank</button>
            </div>
        </div>
    </div>
</div>
