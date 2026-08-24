 

<style>
/* ── Table header ───────────────────────────────── */
#myTable thead th {
    background-color: #2e7d32;
    color: #fff;
    padding: 11px 10px;
    text-align: center;
    font-weight: 700;
    border: 1px solid #388e3c;
    font-size: 13px;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 2;
}

/* ── Table body cells ───────────────────────────── */
#myTable tbody td {
    padding: 8px 10px;
    text-align: center;
    border: 1px solid #e0e0e0;
    font-size: 13px;
    background: #fff;
}

/* ── Table overall ──────────────────────────────── */
#myTable {
    border-collapse: collapse;
    width: 100%;
    min-width: 1200px;
}

/* ── Scroll wrapper ─────────────────────────────── */
.table-responsive { width: 100%; overflow-x: auto; }

/* ── Sticky Name column (left) ──────────────────── */
#myTable th:nth-child(2),
#myTable td:nth-child(2) {
    position: sticky;
    left: 0;
    z-index: 1;
    background: #e3f2fd;
    box-shadow: 3px 0 5px rgba(0,0,0,0.08);
    font-weight: 600;
    text-align: left;
}
#myTable thead th:nth-child(2) {
    background: #1565c0;
    z-index: 3;
}

/* ── Sticky Total Due column (right) ────────────── */
#myTable th:last-child,
#myTable td:last-child {
    position: sticky;
    right: 0;
    z-index: 1;
    background: #e8f5e9;
    box-shadow: -3px 0 5px rgba(0,0,0,0.08);
    font-weight: 700;
}
#myTable thead th:last-child {
    background: #2e7d32;
    z-index: 3;
}

/* ── Grand total row ────────────────────────────── */
#myTable tbody tr:last-child td {
    background: #f5f5f5 !important;
    font-weight: 700;
    border-top: 2px solid #388e3c;
}
#myTable tbody tr:last-child td:last-child {
    background: #c8e6c9 !important;
    color: #1b5e20;
    font-size: 14px;
}

/* ── Report action buttons ──────────────────────── */
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

/* ── Report box header ──────────────────────────── */
.due-report-box-header {
    background: linear-gradient(90deg, #e8f5e9, #f1f8e9);
    border-left: 4px solid #43a047;
    padding: 10px 16px;
    border-radius: 4px 4px 0 0;
    margin-bottom: 0;
}
.due-report-box-header h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #2e7d32;
}

/* ── Class / Section info bar ───────────────────── */
.due-class-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f9fbe7;
    border: 1px solid #dcedc8;
    border-radius: 4px;
    padding: 8px 14px;
    margin: 10px 0 14px;
    font-size: 13px;
    font-weight: 600;
    color: #33691e;
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
    <a href="<?=$xml_preview_uri?>" class="btn btn-success btn-rpt-action">
        <i class="fa fa-file-excel-o"></i> Export XLSX
    </a>
    <button id="exportBtn" class="btn btn-info btn-rpt-action">
        <i class="fa fa-download"></i> Download Excel – Vertical View
    </button>

    <div class="btn-group rpt-col-selector-group" id="columnSelectorGroup">
        <button type="button" class="btn btn-default btn-rpt-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

<div class="box" style="border-top: 3px solid #43a047;">
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
                        <?php
// Step 1: Collect all unique fee types
$allFeeTypes = [];
foreach($getDueFeesReports as $report) {
    $allFeeTypes[$report->feetypeID] = $feetypes[$report->feetypeID];
}

// Step 2: Pivot data by student and invoice date
$pivotData = [];

// echo "<pre>";print_r($students);die;
foreach($getDueFeesReports as $report) {
    $studentID = $report->studentID;
    $key = $studentID;

    // Initialize if not already set
    if (!isset($pivotData[$key])) {
        $pivotData[$key] = [
            'student' => isset($students[$studentID]) ? $students[$studentID] : null,
            'fees' => [],
        ];
    }

    // $discount = ($report->amount / 100) * $report->discount;
    $discount =  $report->discount;
    $paidAmount = isset($getFeesReports[$report->invoiceID]) ? $getFeesReports[$report->invoiceID] : 0;
    $due = ($report->amount - $paidAmount - $discount);

    // Sum the due amount under the corresponding fee type
    $pivotData[$key]['fees'][$report->feetypeID] = isset($pivotData[$key]['fees'][$report->feetypeID]) ? $pivotData[$key]['fees'][$report->feetypeID] + $due : $due;
}
?>

<div class="table-responsive" id="due-table-scroll">
<table class="table table-bordered" id="myTable">
    <thead>
        <tr>
            <th data-col="slno"><?=$this->lang->line('slno')?></th>
            <th data-col="name"><?=$this->lang->line('duefeesreport_name')?></th>
            <th data-col="father">Father</th>
            <th data-col="phone">Phone</th>
            <th data-col="registerno"><?=$this->lang->line('duefeesreport_registerNO')?></th>
            <th data-col="roll"><?=$this->lang->line('duefeesreport_roll')?></th>

            <?php if($classesID == 0) echo '<th data-col="class">'.$this->lang->line('duefeesreport_class').'</th>'; ?>
            <?php if($sectionID == 0) echo '<th data-col="section">'.$this->lang->line('duefeesreport_section').'</th>'; ?>

            <?php foreach($allFeeTypes as $feetypeID => $feetypeName): ?>
                <th data-col="ft<?=$feetypeID?>"><?=$feetypeName?></th>
            <?php endforeach; ?>
            <th data-col="total"><?=$this->lang->line('duefeesreport_total_due')?></th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; $grandTotal = 0; ?>
        <?php foreach($pivotData as $key => $entry): ?>
            <tr>
                <td data-col="slno"><?=$i++?></td>
                <td data-col="name"><?=$entry['student'] ? $entry['student']->srname : ''?></td>
                <td data-col="father"><?=$entry['student'] ? $entry['student']->father_name : ''?></td>
                <td data-col="phone"><?=$entry['student'] ? $entry['student']->phone : ''?></td>

                <td data-col="registerno"><?=$entry['student'] ? $entry['student']->srregisterNO : ''?></td>
                <td data-col="roll"><?=$entry['student'] ? $entry['student']->srroll : ''?></td>

                <?php if($classesID == 0): ?>
                    <td data-col="class">
                        <?php
                            $cid = $entry['student'] ? $entry['student']->srclassesID : 0;
                            echo isset($classes[$cid]) ? $classes[$cid] : '';
                        ?>
                    </td>
                <?php endif; ?>

                <?php if($sectionID == 0): ?>
                    <td data-col="section">
                        <?php
                            $sid = $entry['student'] ? $entry['student']->srsectionID : 0;
                            echo isset($sections[$sid]) ? $sections[$sid] : '';
                        ?>
                    </td>
                <?php endif; ?>

                <?php
                    $totalDue = 0;
                    // Display all fee types in separate columns
                    foreach($allFeeTypes as $feetypeID => $feetypeName) {
                        $due = isset($entry['fees'][$feetypeID]) ? $entry['fees'][$feetypeID] : 0;
                        echo "<td data-col=\"ft".$feetypeID."\">".number_format($due,2)."</td>";
                        $totalDue += $due;
                    }
                    $grandTotal += $totalDue;
                ?>
                <td data-col="total"><strong><?=number_format($totalDue, 2)?></strong></td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td data-col-group="group1" colspan="<?=6 + ($classesID == 0 ? 1 : 0) + ($sectionID == 0 ? 1 : 0) + count($allFeeTypes)?>" class="text-right text-bold">
                <?=$this->lang->line('duefeesreport_grand_total')?> <?=!empty($siteinfos->currency_code) ? '('.$siteinfos->currency_code.')' : '' ?>
            </td>
            <td data-col="total" class="text-bold"><?=number_format($grandTotal,2)?></td>
        </tr>
    </tbody>
</table>
</div><!-- /table-responsive -->

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

 
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>


<script>
        $(document).ready(function () {
            $("#exportBtn").click(function () {
                var table = document.getElementById("myTable");
                // display: true makes SheetJS skip any column hidden via the Columns
                // selector below (it reads the live DOM table directly, so a plain
                // css('display','none') on a cell is enough - no cloning needed).
                var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1", display: true });
                XLSX.writeFile(wb, "DueFeeReport.xlsx");
            });
        });
    </script>

    <!-- Columns show/hide dropdown for the Vertical view's pivot table. NOTE: this only
         affects the on-screen table and the "Download Excel – Vertical View" button above
         (client-side SheetJS, reading this table's live DOM). The separate "Export XLSX"
         link at the top of this page is unrelated - it calls the server-side PhpSpreadsheet
         export shared with the Horizontal report, which has always produced a fixed
         10-column layout that does not match this pivot table's columns (one column per
         fee type). That mismatch pre-exists this feature and is out of scope here. -->
    <script>
    $(document).ready(function () {
        var $table = $('#myTable');
        if (!$table.length) { return; }

        // Fixed (non fee-type) columns that the Grand Total row's label cell spans across.
        // Class/Section may not be rendered at all depending on the filter - the $th.length
        // check below skips those automatically.
        var BASE_LABEL_COLS = ['slno', 'name', 'father', 'phone', 'registerno', 'roll', 'class', 'section'];

        var $menu = $('#columnSelectorMenu');
        var $list = $('#columnSelectorList');
        $table.find('thead th[data-col]').each(function () {
            var col = $(this).attr('data-col');
            var label = $.trim($(this).text());
            var $item = $('<label>', { class: 'rpt-col-selector-item' });
            var $checkbox = $('<input>', { type: 'checkbox', 'data-col': col, checked: true });
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

        // Distinct "ftN" fee-type columns currently in the header (varies per search).
        function getFeetypeCols() {
            var cols = [];
            $table.find('thead th[data-col^="ft"]').each(function () {
                cols.push($(this).attr('data-col'));
            });
            return cols;
        }

        function recomputeGroupColspans() {
            var baseVisible = BASE_LABEL_COLS.filter(function (col) {
                var $th = $table.find('thead th[data-col="' + col + '"]');
                return $th.length && $th.css('display') !== 'none';
            }).length;

            var feetypeVisible = getFeetypeCols().filter(function (col) {
                var $th = $table.find('thead th[data-col="' + col + '"]');
                return $th.length && $th.css('display') !== 'none';
            }).length;

            $table.find('td[data-col-group="group1"]').attr('colspan', Math.max(baseVisible + feetypeVisible, 1));
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
        $menu.on('click', function (e) { e.stopPropagation(); }); // keeps dropdown open while clicking checkboxes
    });
    </script>

