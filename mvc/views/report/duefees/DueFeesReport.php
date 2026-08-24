
<link rel="stylesheet" href="/assets/css/report-buttons.css">
<style>
/* ── Horizontal report table ────────────────────── */
#due-fees-h-table thead th {
    background: #1a237e;
    color: #fff;
    padding: 11px 10px;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
    border: 1px solid #283593;
    position: sticky;
    top: 0;
    z-index: 2;
}
#due-fees-h-table tbody td {
    padding: 8px 10px;
    font-size: 13px;
    border: 1px solid #e0e0e0;
    background: #fff;
}

/* Sticky Name column (left) */
#due-fees-h-table th:nth-child(3),
#due-fees-h-table td:nth-child(3) {
    position: sticky;
    left: 0;
    z-index: 1;
    background: #e3f2fd;
    box-shadow: 3px 0 5px rgba(0,0,0,0.08);
    font-weight: 600;
    text-align: left;
}
#due-fees-h-table thead th:nth-child(3) {
    background: #0d47a1;
    z-index: 3;
}

/* Sticky Due column (right) */
#due-fees-h-table th:last-child,
#due-fees-h-table td:last-child {
    position: sticky;
    right: 0;
    z-index: 1;
    background: #e8f5e9;
    box-shadow: -3px 0 5px rgba(0,0,0,0.08);
    font-weight: 700;
}
#due-fees-h-table thead th:last-child {
    background: #1b5e20;
    z-index: 3;
}

/* Grand total row */
#due-fees-h-table tbody tr:last-child td {
    background: #f5f5f5 !important;
    font-weight: 700;
    border-top: 2px solid #388e3c;
}
#due-fees-h-table tbody tr:last-child td:last-child {
    background: #c8e6c9 !important;
    color: #1b5e20;
    font-size: 14px;
}

/* ── Shared report layout helpers ───────────────── */
.due-report-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 0 8px;
    flex-wrap: wrap;
}
.btn-rpt-action {
    padding: 7px 16px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 4px;
    letter-spacing: 0.3px;
    transition: all 0.2s;
    white-space: nowrap;
}
.btn-rpt-action i { margin-right: 6px; }
.btn-rpt-action:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,0,0,0.15); }
.due-report-box-header {
    background: linear-gradient(90deg, #e8eaf6, #f3f4ff);
    border-left: 4px solid #3949ab;
    padding: 10px 16px;
    border-radius: 4px 4px 0 0;
}
.due-report-box-header h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #1a237e;
}
.due-class-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f3f4ff;
    border: 1px solid #c5cae9;
    border-radius: 4px;
    padding: 8px 14px;
    margin: 10px 0 14px;
    font-size: 13px;
    font-weight: 600;
    color: #283593;
}
</style>

<?php
    if($fromdate !='' && $todate !='') {
        $xml_preview_uri = base_url('duefeesreport/xlsx/'.$classesID.'/'.$sectionID.'/'.$studentID.'/'.$feetypeID.'/'.strtotime($fromdate).'/'.strtotime($todate));
    } else {
        $xml_preview_uri = base_url('duefeesreport/xlsx/'.$classesID.'/'.$sectionID.'/'.$studentID.'/'.$feetypeID);
    }
?>
<div class="due-report-actions">
    <a href="<?=$xml_preview_uri?>" id="dueExportXlsxBtn" data-base-href="<?=$xml_preview_uri?>" class="btn btn-success btn-rpt-action">
        <i class="fa fa-file-excel-o"></i> Export XLSX
    </a>

    <div class="btn-group rpt-col-selector-group" id="columnSelectorGroup">
        <button type="button" class="btn btn-info btn-rpt-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-columns"></i> Columns <span class="caret"></span>
        </button>
        <div class="dropdown-menu rpt-col-selector-menu" id="columnSelectorMenu">
            <div class="rpt-col-selector-header">
                <span class="rpt-col-selector-title"><i class="fa fa-eye"></i> Show Columns</span>
                <span class="rpt-col-selector-actions">
                    <a href="javascript:void(0)" id="columnSelectAll">Select All</a>
                    <a href="javascript:void(0)" id="columnDeselectAll">Deselect All</a>
                </span>
            </div>
            <div class="rpt-col-selector-list" id="columnSelectorList">
                <!-- checkboxes injected by JS -->
            </div>
            <div class="rpt-col-selector-footer">
                <span id="columnSelectedCount">0</span>/<span id="columnTotalCount">0</span> columns shown
            </div>
        </div>
    </div>
</div>

<div class="box" style="border-top: 3px solid #3949ab;">
    <div class="due-report-box-header">
        <h3><i class="fa fa-clipboard"></i>
            <?=$this->lang->line('duefeesreport_report_for')?> &mdash; <?=$this->lang->line('duefeesreport_duefees')?>
        </h3>
    </div><!-- /.box-header -->
    <div id="printablediv">
    <!-- form start -->
        <div class="box-body" style="margin-bottom: 50px;">
            <div class="row">
                <div class="col-sm-12">
                    <?=reportheader($siteinfos, $schoolyearsessionobj)?>
                </div>
                <?php if($classesID >= 0 || $sectionID >= 0 ) { ?>
                    <div class="col-sm-12">
                        <div class="due-class-info">
                            <span><i class="fa fa-graduation-cap"></i>&nbsp;
                                <?=$this->lang->line('duefeesreport_class')?>:&nbsp;
                                <strong><?=isset($classes[$classesID]) ? $classes[$classesID] : $this->lang->line('duefeesreport_all_class')?></strong>
                            </span>
                            <span><i class="fa fa-users"></i>&nbsp;
                                <?=$this->lang->line('duefeesreport_section')?>:&nbsp;
                                <strong><?=isset($sections[$sectionID]) ? $sections[$sectionID] : $this->lang->line('duefeesreport_all_section')?></strong>
                            </span>
                        </div>
                    </div>
                <?php }
                if(customCompute($getDueFeesReports)) { ?>
                    <div class="col-sm-12">
                        <div id="hide-table">
                            <div id="due-table-scroll" style="overflow-x: auto;">
                            <table id="due-fees-h-table" class="table table-bordered" style="min-width:900px;">
                                <thead>
                                    <tr>
                                        <th data-col="slno"><?=$this->lang->line('slno')?></th>
                                        <th data-col="invoicedate"><?=$this->lang->line('duefeesreport_invoice_date')?></th>
                                        <th data-col="name"><?=$this->lang->line('duefeesreport_name')?></th>
                                        <th data-col="registerno"><?=$this->lang->line('duefeesreport_registerNO')?></th>
                                        <?php if($classesID == 0) { ?>
                                          <th data-col="class"><?=$this->lang->line('duefeesreport_class')?></th>
                                        <?php } ?>
                                        <?php if($sectionID == 0) { ?>
                                          <th data-col="section"><?=$this->lang->line('duefeesreport_section')?></th>
                                        <?php } ?>
                                        <th data-col="roll"><?=$this->lang->line('duefeesreport_roll')?></th>
                                        <th data-col="feetype"><?=$this->lang->line('duefeesreport_feetype')?></th>
                                        <th data-col="discount"><?=$this->lang->line('duefeesreport_discount')?></th>
                                        <th data-col="due"><?=$this->lang->line('duefeesreport_due') ?></th>
                                        <th data-col="prevcf" style="background:#e65100;">Prev C/F</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $totalDue = 0; $totalPrevCF = 0; $i = 0; $lastStudentID = 0; $seenStudents = []; foreach($getDueFeesReports as $getDueFeesReport) {
                                        if($sectionID > 0) { if(isset($students[$getDueFeesReport->studentID]) && $students[$getDueFeesReport->studentID]->srsectionID == $sectionID) { $i++; ?>
                                            <tr>
                                                <td data-col="slno" data-title="<?=$this->lang->line('slno')?>"><?=$i?></td>
                                                <td data-col="invoicedate" data-title="<?=$this->lang->line('duefeesreport_invoice_date')?>"><?=date('d M Y',strtotime($getDueFeesReport->create_date))?></td>
                                                <td data-col="name" data-title="<?=$this->lang->line('duefeesreport_name')?>"><?=isset($students[$getDueFeesReport->studentID]) ? $students[$getDueFeesReport->studentID]->srname : '' ?></td>
                                                <td data-col="registerno" data-title="<?=$this->lang->line('duefeesreport_registerNO')?>">
                                                    <?=isset($students[$getDueFeesReport->studentID]) ? $students[$getDueFeesReport->studentID]->srregisterNO : '' ?>
                                                </td>
                                                <?php
                                                    if($classesID == 0) {
                                                        echo "<td data-col='class' data-title='".$this->lang->line('duefeesreport_class')."'>";
                                                        if(isset($students[$getDueFeesReport->studentID])) {
                                                            $stclassID = $students[$getDueFeesReport->studentID]->srclassesID;
                                                            echo isset($classes[$stclassID]) ? $classes[$stclassID] : '';
                                                        }
                                                        echo "</td>";
                                                    }
                                                ?>
                                                <?php
                                                    if($sectionID == 0) {
                                                        echo "<td data-col='section' data-title='".$this->lang->line('duefeesreport_section')."'>";
                                                            if(isset($students[$getDueFeesReport->studentID])) {
                                                            $stsectionID = $students[$getDueFeesReport->studentID]->srsectionID;
                                                            echo isset($sections[$stsectionID]) ? $sections[$stsectionID] : '';
                                                        }
                                                        echo "</td>";
                                                    }
                                                ?>
                                                <td data-col="roll" data-title="<?=$this->lang->line('duefeesreport_roll')?>"><?=isset($students[$getDueFeesReport->studentID]) ? $students[$getDueFeesReport->studentID]->srroll : '' ?></td>
                                                <td data-col="feetype" data-title="<?=$this->lang->line('duefeesreport_feetype')?>">
                                                    <?php
                                                        if(isset($feetypes[$getDueFeesReport->feetypeID])) {
                                                            echo $feetypes[$getDueFeesReport->feetypeID];
                                                        }
                                                    ?>
                                                </td>
                                                <td data-col="discount" data-title="<?=$this->lang->line('duefeesreport_discount')?>"><?=number_format($getDueFeesReport->discount, 2);?></td>
                                                <td data-col="due" data-title="<?=$this->lang->line('duefeesreport_due')?>">
                                                    <?php
                                                        $discount = $getDueFeesReport->discount;
                                                        if(isset($getFeesReports[$getDueFeesReport->invoiceID])) {
                                                            $due = (($getDueFeesReport->amount - $getFeesReports[$getDueFeesReport->invoiceID]) - $discount);
                                                            echo number_format($due,2);
                                                            $totalDue += $due;
                                                        } else {
                                                            $due = ($getDueFeesReport->amount - $discount);
                                                            echo number_format($due,2);
                                                            $totalDue += $due;
                                                        }
                                                    ?>
                                                </td>
                                                <td data-col="prevcf" data-title="Prev C/F" style="background:#fff3e0;">
                                                    <?php
                                                        $cf_sid = $getDueFeesReport->studentID;
                                                        if (!isset($seenStudents[$cf_sid])) {
                                                            $seenStudents[$cf_sid] = true;
                                                            $cfVal = isset($prevBalanceMap[$cf_sid]) ? $prevBalanceMap[$cf_sid] : 0;
                                                            $totalPrevCF += $cfVal;
                                                            echo $cfVal > 0 ? '<strong style="color:#e65100;">'.number_format($cfVal,2).'</strong>' : '';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } } else { $i++;?>
                                            <tr>
                                                <td data-col="slno" data-title="<?=$this->lang->line('slno')?>"><?=$i?></td>
                                                <td data-col="invoicedate" data-title="<?=$this->lang->line('duefeesreport_invoice_date')?>"><?=date('d M Y',strtotime($getDueFeesReport->create_date))?></td>
                                                <td data-col="name" data-title="<?=$this->lang->line('duefeesreport_name')?>"><?=isset($students[$getDueFeesReport->studentID]) ? $students[$getDueFeesReport->studentID]->srname : '' ?></td>
                                                <td data-col="registerno" data-title="<?=$this->lang->line('duefeesreport_registerNO')?>">
                                                    <?=isset($students[$getDueFeesReport->studentID]) ? $students[$getDueFeesReport->studentID]->srregisterNO : '' ?>
                                                </td>
                                                <?php
                                                    if($classesID == 0) {
                                                        echo "<td data-col='class' data-title='".$this->lang->line('duefeesreport_class')."'>";
                                                        if(isset($students[$getDueFeesReport->studentID])) {
                                                            $stclassID = $students[$getDueFeesReport->studentID]->srclassesID;
                                                            echo isset($classes[$stclassID]) ? $classes[$stclassID] : '';
                                                        }
                                                        echo "</td>";
                                                    }
                                                ?>
                                                <?php
                                                    if($sectionID == 0) {
                                                        echo "<td data-col='section' data-title='".$this->lang->line('duefeesreport_section')."'>";
                                                            if(isset($students[$getDueFeesReport->studentID])) {
                                                            $stsectionID = $students[$getDueFeesReport->studentID]->srsectionID;
                                                            echo isset($sections[$stsectionID]) ? $sections[$stsectionID] : '';
                                                        }
                                                        echo "</td>";
                                                    }
                                                ?>
                                                <td data-col="roll" data-title="<?=$this->lang->line('duefeesreport_roll')?>"><?=isset($students[$getDueFeesReport->studentID]) ? $students[$getDueFeesReport->studentID]->srroll : '' ?></td>
                                                <td data-col="feetype" data-title="<?=$this->lang->line('duefeesreport_feetype')?>">
                                                    <?php
                                                        if(isset($feetypes[$getDueFeesReport->feetypeID])) {
                                                            echo $feetypes[$getDueFeesReport->feetypeID];
                                                        }
                                                    ?>
                                                </td>
                                                <td data-col="discount" data-title="<?=$this->lang->line('duefeesreport_discount')?>"><?=number_format($getDueFeesReport->discount, 2);?></td>
                                                <td data-col="due" data-title="<?=$this->lang->line('duefeesreport_due')?>">
                                                    <?php
                                                        $discount = $getDueFeesReport->discount;
                                                        if(isset($getFeesReports[$getDueFeesReport->invoiceID])) {
                                                            $due = (($getDueFeesReport->amount - $getFeesReports[$getDueFeesReport->invoiceID]) - $discount);
                                                            echo number_format($due,2);
                                                            $totalDue += $due;
                                                        } else {
                                                            $due = ($getDueFeesReport->amount - $discount);
                                                            echo number_format($due,2);
                                                            $totalDue += $due;
                                                        }
                                                    ?>
                                                </td>
                                                <td data-col="prevcf" data-title="Prev C/F" style="background:#fff3e0;">
                                                    <?php
                                                        $cf_sid = $getDueFeesReport->studentID;
                                                        if (!isset($seenStudents[$cf_sid])) {
                                                            $seenStudents[$cf_sid] = true;
                                                            $cfVal = isset($prevBalanceMap[$cf_sid]) ? $prevBalanceMap[$cf_sid] : 0;
                                                            $totalPrevCF += $cfVal;
                                                            echo $cfVal > 0 ? '<strong style="color:#e65100;">'.number_format($cfVal,2).'</strong>' : '';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                    <?php  } } ?>
                                    <tr>
                                        <?php
                                            $colspan = 7;
                                            if($classesID == 0) {
                                                $colspan = 8;
                                            }
                                            if($sectionID == 0) {
                                                $colspan = 8;
                                            }
                                            if($classesID == 0 && $sectionID == 0) {
                                                $colspan = 9;
                                            }
                                        ?>
                                        <td data-col-group="group1" data-title="<?=$this->lang->line('duefeesreport_grand_total')?>" class="text-right text-bold" colspan="<?=$colspan?>">
                                            <?=$this->lang->line('duefeesreport_grand_total')?> <?=!empty($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : '' ?>
                                        </td>
                                        <td data-col="due" data-title="<?=$this->lang->line('duefeesreport_total_due')?>" class="text-bold"><?=number_format($totalDue,2)?></td>
                                        <td data-col="prevcf" class="text-bold" style="background:#ffe0b2; color:#e65100;">
                                            <?=$totalPrevCF > 0 ? number_format($totalPrevCF, 2) : ''?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div><!-- /overflow-x -->
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-sm-12">
                        <br/>
                        <div class="callout callout-danger">
                            <p><b class="text-info"><?=$this->lang->line('duefeesreport_data_not_found')?></b></p>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-sm-12 text-center footerAll">
                    <?=reportfooter($siteinfos, $schoolyearsessionobj)?>
                </div>
            </div><!-- row -->
        </div><!-- Body -->
    </div>
</div>


<!-- email modal starts here -->
<form class="form-horizontal" role="form" action="<?=base_url('feesreport/send_pdf_to_mail');?>" method="post">
    <div class="modal fade" id="mail">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=$this->lang->line('duefeesreport_close')?></span></button>
                <h4 class="modal-title"><?=$this->lang->line('duefeesreport_mail')?></h4>
            </div>
            <div class="modal-body">

                <?php
                    if(form_error('to'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                ?>
                    <label for="to" class="col-sm-2 control-label">
                        <?=$this->lang->line("duefeesreport_to")?> <span class="text-red">*</span>
                    </label>
                    <div class="col-sm-6">
                        <input type="email" class="form-control" id="to" name="to" value="<?=set_value('to')?>" >
                    </div>
                    <span class="col-sm-4 control-label" id="to_error">
                    </span>
                </div>

                <?php
                    if(form_error('subject'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                ?>
                    <label for="subject" class="col-sm-2 control-label">
                        <?=$this->lang->line("duefeesreport_subject")?> <span class="text-red">*</span>
                    </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="subject" name="subject" value="<?=set_value('subject')?>" >
                    </div>
                    <span class="col-sm-4 control-label" id="subject_error">
                    </span>

                </div>

                <?php
                    if(form_error('message'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                ?>
                    <label for="message" class="col-sm-2 control-label">
                        <?=$this->lang->line("duefeesreport_message")?>
                    </label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="message" style="resize: vertical;" name="message" value="<?=set_value('message')?>" ></textarea>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                <input type="button" id="send_pdf" class="btn btn-success" value="<?=$this->lang->line("duefeesreport_send")?>" />
            </div>
        </div>
      </div>
    </div>
</form>



<!-- Sticky horizontal scrollbar -->
<div id="sticky-hscroll-bar" style="display:none; position:fixed; bottom:0; overflow-x:auto; overflow-y:hidden; z-index:1049; height:14px; background:#f0f0f0; border-top:1px solid #ccc;">
    <div id="sticky-hscroll-inner" style="height:1px;"></div>
</div>

<script type="text/javascript">
(function() {
    var wrap  = document.getElementById('due-table-scroll');
    var bar   = document.getElementById('sticky-hscroll-bar');
    var inner = document.getElementById('sticky-hscroll-inner');
    if (!wrap) return;

    function reposition() {
        var rect = wrap.getBoundingClientRect();
        bar.style.left  = rect.left + 'px';
        bar.style.width = rect.width + 'px';
        inner.style.width = wrap.scrollWidth + 'px';
    }

    function checkVisibility() {
        var rect  = wrap.getBoundingClientRect();
        var winH  = window.innerHeight;
        var wide  = wrap.scrollWidth > wrap.clientWidth;
        // Show only when table is horizontally scrollable AND its native
        // bottom scrollbar is hidden below the viewport fold
        bar.style.display = (wide && rect.top < winH && rect.bottom > winH) ? 'block' : 'none';
    }

    reposition();
    checkVisibility();

    bar.addEventListener('scroll', function() { wrap.scrollLeft = bar.scrollLeft; });
    wrap.addEventListener('scroll', function() { bar.scrollLeft  = wrap.scrollLeft; });
    window.addEventListener('scroll', function() { reposition(); checkVisibility(); });
    window.addEventListener('resize', function() { reposition(); checkVisibility(); });
})();
</script>

<button id="scroll-to-top-btn" title="Back to top" style="
    display: none;
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1100;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: rgba(51,122,183,0.80);
    color: #fff;
    font-size: 18px;
    line-height: 40px;
    text-align: center;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.20);
    transition: opacity 0.3s;
    padding: 0;
">&#8679;</button>

<script type="text/javascript">
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 200) {
            $('#scroll-to-top-btn').fadeIn(300);
        } else {
            $('#scroll-to-top-btn').fadeOut(300);
        }
    });
    $('#scroll-to-top-btn').on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 400);
    });
</script>

<script type="text/javascript">
    function check_email(email) {
        var status = false;
        var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        if (email.search(emailRegEx) == -1) {
            $("#to_error").html('');
            $("#to_error").html("<?=$this->lang->line('duefeesreport_mail_valid')?>").css("text-align", "left").css("color", 'red');
        } else {
            status = true;
        }
        return status;
    }

    $("#send_pdf").click(function() {
        var field = {
            'to'         : $('#to').val(), 
            'subject'    : $('#subject').val(), 
            'message'    : $('#message').val(),
            'schoolyearID': '<?=$schoolyearID?>',
            'classesID'  : '<?=$classesID?>',
            'sectionID'  : '<?=$sectionID?>',
            'studentID'  : '<?=$studentID?>',
            'feetypeID'  : '<?=$feetypeID?>',
            'fromdate'   : '<?=$fromdate?>',
            'todate'     : '<?=$todate?>'
        };

        var to = $('#to').val();
        var subject = $('#subject').val();
        var error = 0;

        $("#to_error").html("");
        $("#subject_error").html("");

        if(to == "" || to == null) {
            error++;
            $("#to_error").html("<?=$this->lang->line('duefeesreport_mail_to')?>").css("text-align", "left").css("color", 'red');
        } else {
            if(check_email(to) == false) {
                error++
            }
        }

        if(subject == "" || subject == null) {
            error++;
            $("#subject_error").html("<?=$this->lang->line('duefeesreport_mail_subject')?>").css("text-align", "left").css("color", 'red');
        } else {
            $("#subject_error").html("");
        }

        if(error == 0) {
            $('#send_pdf').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: "<?=base_url('duefeesreport/send_pdf_to_mail')?>",
                data: field,
                dataType: "html",
                success: function(data) {
                    var response = JSON.parse(data);
                    if(response.status == false) {
                        $('#send_pdf').removeAttr('disabled');
                        $.each(response, function(index, value) {
                            if(index != 'status') {
                                toastr["error"](value)
                                toastr.options = {
                                  "closeButton": true,
                                  "debug": false,
                                  "newestOnTop": false,
                                  "progressBar": false,
                                  "positionClass": "toast-top-right",
                                  "preventDuplicates": false,
                                  "onclick": null,
                                  "showDuration": "500",
                                  "hideDuration": "500",
                                  "timeOut": "5000",
                                  "extendedTimeOut": "1000",
                                  "showEasing": "swing",
                                  "hideEasing": "linear",
                                  "showMethod": "fadeIn",
                                  "hideMethod": "fadeOut"
                                }
                            }
                        });
                    } else {
                        location.reload();
                    }
                }
            });
        }
    });
</script>

<script>
    $(document).ready(function () {
        var $table = $('#due-fees-h-table');
        if (!$table.length) {
            return;
        }

        // Columns whose grand-total label cell spans via data-col-group="group1"
        // (everything except "due" and "prevcf", which have their own individually
        // mapped total cells in the same row — see DueFeesReport.php's grand-total <tr>).
        var GROUP1_COLS = ['slno', 'invoicedate', 'name', 'registerno', 'class', 'section', 'roll', 'feetype', 'discount'];

        // Build the "Columns" checkbox list from whatever headers the server actually rendered
        var $menu = $('#columnSelectorMenu');
        var $list = $('#columnSelectorList');
        $table.find('thead th[data-col]').each(function () {
            var col = $(this).attr('data-col');
            var label = $.trim($(this).text());
            var $item = $('<label>', { class: 'rpt-col-selector-item' });
            var $checkbox = $('<input>', {
                type: 'checkbox',
                'data-col': col,
                checked: true
            });
            var $text = $('<span>', { text: label, title: label });
            $item.append($checkbox).append($text);
            $list.append($item);
        });

        function updateSelectedCount() {
            var total = $list.find('input[type="checkbox"][data-col]').length;
            var selected = $list.find('input[type="checkbox"][data-col]:checked').length;
            $('#columnSelectedCount').text(selected);
            $('#columnTotalCount').text(total);
        }
        updateSelectedCount();

        function recomputeGroupColspans() {
            var group1Visible = GROUP1_COLS.filter(function (col) {
                var $th = $table.find('thead th[data-col="' + col + '"]');
                return $th.length && $th.css('display') !== 'none';
            }).length;

            $table.find('td[data-col-group="group1"]').attr('colspan', Math.max(group1Visible, 1));
        }

        function toggleColumn(col, visible) {
            $table.find('[data-col="' + col + '"]').css('display', visible ? '' : 'none');
            recomputeGroupColspans();
            updateSelectedCount();
        }

        $list.on('change', 'input[type="checkbox"][data-col]', function () {
            toggleColumn($(this).attr('data-col'), $(this).is(':checked'));
        });

        $('#columnSelectAll').on('click', function () {
            $list.find('input[type="checkbox"][data-col]').prop('checked', true).each(function () {
                toggleColumn($(this).attr('data-col'), true);
            });
        });

        $('#columnDeselectAll').on('click', function () {
            $list.find('input[type="checkbox"][data-col]').prop('checked', false).each(function () {
                toggleColumn($(this).attr('data-col'), false);
            });
        });

        // Keep the dropdown open while interacting with checkboxes
        $menu.on('click', function (e) {
            e.stopPropagation();
        });

        // Wire the server-side "Export XLSX" link (PhpSpreadsheet, duefeesreport/xlsx/...)
        // to only include the columns still checked here. "prevcf" (Prev C/F) has no matching
        // column in the export at all, so it's silently ignored server-side if unchecked/checked.
        $('#dueExportXlsxBtn').on('click', function (e) {
            e.preventDefault();
            var visibleCols = [];
            $list.find('input[type="checkbox"][data-col]:checked').each(function () {
                visibleCols.push($(this).attr('data-col'));
            });
            var base = $(this).attr('data-base-href');
            var sep = base.indexOf('?') === -1 ? '?' : '&';
            window.location.href = base + sep + 'cols=' + encodeURIComponent(visibleCols.join(','));
        });
    });
</script>
