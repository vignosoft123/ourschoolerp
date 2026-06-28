<div class="row">
    <div class="col-sm-3">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa icon-invoice"></i> <?= $this->lang->line('panel_title') ?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <form role="form" method="post" enctype="multipart/form-data" id="invoiceDataForm">

                    <div class="classesDiv form-group <?= form_error('classesID') ? 'has-error' : '' ?>">
                        <label for="classesID">
                            <?= $this->lang->line("invoice_classesID") ?> <span class="text-red">*</span>
                        </label>
                        <?php
                        $classesArray = array('0' => $this->lang->line("invoice_select_classes"));
                        if (customCompute($classes)) {
                            foreach ($classes as $classa) {
                                $classesArray[$classa->classesID] = $classa->classes;
                            }
                        }
                        echo form_dropdown("classesID", $classesArray, set_value("classesID"), "id='classesID' class='form-control select2'");
                        ?>
                        <span class="text-red">
                            <?php echo form_error('classesID'); ?>
                        </span>
                    </div>

                    <div class="classesDiv form-group <?= form_error('sectionID') ? 'has-error' : '' ?>">
                        <label for="sectionID">
                            <?= $this->lang->line("invoice_sectionID") ?> <span class="text-red">*</span>
                        </label>
                        <?php
                        $classesArray = array('0' => $this->lang->line("invoice_select_sections"));
                        // if (customCompute($classes)) {
                        //     foreach ($classes as $classa) {
                        //         $classesArray[$classa->classesID] = $classa->classes;
                        //     }
                        // }
                        echo form_dropdown("sectionID", $classesArray, set_value("sectionID"), "id='sectionID' class='form-control select2'");
                        ?>
                        <span class="text-red">
                            <?php echo form_error('sectionID'); ?>
                        </span>
                    </div>

                    <div class="studentDiv form-group">
                        <label for="studentID">
                            <?= $this->lang->line("invoice_studentID") ?> <span class="text-red">*</span>
                        </label>
                        <select name="studentID[]" id="studentID" class="form-control" multiple="multiple" style="width:100%">
                            <option value="0"><?= $this->lang->line("invoice_all_student") ?></option>
                        </select>
                        <small class="text-muted">Leave blank / select "All Students" to apply to entire section.</small>
                    </div>

                    <div class="dateDiv form-group <?= form_error('date') ? 'has-error' : '' ?>">
                        <label for="date">
                            <?= $this->lang->line("invoice_date") ?> <span class="text-red">*</span>
                        </label>
                        <input type="text" class="form-control" id="date" name="date" value="<?= set_value('date') ?>" autocomplete="off">
                        <span class="text-red">
                            <?php echo form_error('date'); ?>
                        </span>
                    </div>

                    <div class="statusDiv form-group <?= form_error('statusID') ? 'has-error' : '' ?>">
                        <label for="statusID">
                            <?= $this->lang->line("invoice_status") ?> <span class="text-red">*</span>
                        </label>
                        <?php
                        $statusArray = array(
                            // 5 => $this->lang->line("invoice_select_paymentstatus"),
                            0 => $this->lang->line("invoice_notpaid"),
                            1 => $this->lang->line("invoice_partially_paid"),
                            2 => $this->lang->line("invoice_fully_paid")
                        );

                        echo form_dropdown("statusID", $statusArray, set_value("statusID"), "id='statusID' class='form-control select2'");
                        ?>
                        <span class="text-red">
                            <?php echo form_error('statusID'); ?>
                        </span>
                    </div>

                    <div class="paymentmethodDiv hide form-group <?= form_error('payment_method') ? 'has-error' : '' ?>">
                        <label for="payment_method">
                            <?= $this->lang->line("invoice_paymentmethod") ?> <span class="text-red">*</span>
                        </label>
                        <?php
                        $paymentmethodArray = array(
                            '0' => $this->lang->line("invoice_select_paymentmethod"),
                            'Cash' => $this->lang->line('Cash'),
                            'Cheque' => $this->lang->line('Cheque'),
                        );
                        echo form_dropdown("payment_method", $paymentmethodArray, set_value("payment_method"), "id='payment_method' class='form-control select2'");
                        ?>
                        <span class="text-red">
                            <?php echo form_error('payment_method'); ?>
                        </span>
                    </div>

                    <input id="addInvoiceButton" type="button" class="btn btn-success" value="<?= $this->lang->line("add_invoice") ?>">
                </form>
            </div>
        </div>
    </div>


    <div class="col-sm-9">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa icon-feetypes"></i> <?= $this->lang->line('invoice_feetype_list') ?></h3>
                <ol class="breadcrumb">
                    <li><a href="<?= base_url("dashboard/index") ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
                    <li><a href="<?= base_url("invoice/index") ?>"><?= $this->lang->line('menu_invoice') ?></a></li>
                    <li class="active"><?= $this->lang->line('menu_add') ?> <?= $this->lang->line('menu_invoice') ?></li>
                </ol>
            </div><!-- /.box-header -->
            <div class="box-body">
                <form class="" role="form" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group <?= form_error('feetypeID') ? 'has-error' : '' ?>">
                                <label for="feetypeID" class="control-label">
                                    <?= $this->lang->line("invoice_feetype") ?> <span class="text-red">*</span>
                                </label>
                                <?php
                                $feetypeArray = array('0' => $this->lang->line("invoice_select_feetype"));
                                foreach ($feetypes as $feetype) {
                                    $feetypeArray[$feetype->feetypesID] = $feetype->feetypes;
                                }
                                echo form_dropdown("feetypeID", $feetypeArray, set_value("feetypeID"), "id='feetypeID' class='form-control select2'");
                                ?>
                                <span class="control-label">
                                    <?php echo form_error('feetypeID'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered feetype-style" style="font-size: 16px;">
                        <thead>
                            <tr>
                                <th class="col-sm-1"><?= $this->lang->line('slno') ?></th>
                                <th class="col-sm-3"><?= $this->lang->line('invoice_feetype') ?></th>
                                <th class="col-sm-2"><?= $this->lang->line('invoice_amount') ?></th>
                                <th class="col-sm-1"><?= $this->lang->line('invoice_discount') ?></th>
                                <th class="col-sm-2"><?= $this->lang->line('invoice_subtotal') ?></th>
                                <th class="col-sm-2"><?= $this->lang->line('invoice_paid_amount') ?></th>
                                <th class="col-sm-1"><?= $this->lang->line('action') ?></th>
                            </tr>
                        </thead>
                        <tbody id="feetypeList">
                        </tbody>

                        <tfoot id="feetypeListFooter">
                            <tr>
                                <td colspan="2" style="font-weight: bold"><?= $this->lang->line('invoice_total') ?></td>
                                <td id="totalAmount" style="font-weight: bold">0.00</td>
                                <td id="totalDiscount" style="font-weight: bold">0.00</td>
                                <td id="totalSubtotal" style="font-weight: bold">0.00</td>
                                <td id="totalPaidAmount" style="font-weight: bold">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Duplicate students confirmation modal (bulk invoice add) -->
<div class="modal fade" id="dupStudentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; border-bottom:none; padding:14px 20px;">
                <h4 class="modal-title" style="color:#fff; font-size:15px; font-weight:700;">
                    <i class="fa fa-exclamation-triangle"></i>
                    &nbsp;Duplicate Fee Types Found
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.85;">&times;</button>
            </div>
            <div class="modal-body" style="padding:18px 22px;">
                <!-- Partial duplicates (will still receive missing fee types) -->
                <div id="dupPartialSection" style="display:none; margin-bottom:14px;">
                    <div style="background:#d6eaf8; border-left:4px solid #3c8dbc; border-radius:3px; padding:8px 12px; margin-bottom:8px;">
                        <span style="font-weight:600; color:#1a5276; font-size:13px;">
                            <i class="fa fa-info-circle"></i>
                            &nbsp;These students already have <strong>some</strong> fee types — only the missing ones will be added:
                        </span>
                    </div>
                    <ul id="dupPartialList" style="max-height:200px; overflow-y:auto; padding-left:0; list-style:none; margin:0;"></ul>
                </div>
                <!-- Fully duplicate (will be skipped entirely) -->
                <div id="dupFullSection" style="display:none; margin-bottom:14px;">
                    <div style="background:#fdecea; border-left:4px solid #c0392b; border-radius:3px; padding:8px 12px; margin-bottom:8px;">
                        <span style="font-weight:600; color:#922b21; font-size:13px;">
                            <i class="fa fa-times-circle"></i>
                            &nbsp;These students already have <strong>all</strong> selected fee types — no invoice will be created:
                        </span>
                    </div>
                    <ul id="dupFullList" style="max-height:130px; overflow-y:auto; padding-left:0; list-style:none; margin:0;"></ul>
                </div>
                <div style="background:#eaf4fb; border:1px solid #aed6f1; border-radius:4px; padding:10px 14px; margin-top:4px;">
                    <p id="dupConfirmMsg" style="font-weight:600; margin:0; color:#1a5276; font-size:13px;"></p>
                </div>
            </div>
            <div class="modal-footer" style="background:#f4f6f7; border-top:1px solid #e0e0e0; padding:12px 20px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="dupProceedBtn" style="background:#3c8dbc; border-color:#367fa9; font-weight:600;">
                    <i class="fa fa-check"></i> &nbsp;Proceed
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function dd(data) {
        console.log(data);
    }

    $('.select2').select2();

    // Multi-select for students
    $('#studentID').select2({
        placeholder: '<?= $this->lang->line("invoice_all_student") ?>',
        allowClear: true,
        width: '100%'
    });

    $('#date').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy',
        startDate: '<?= $schoolyearsessionobj->startingdate ?>',
        endDate: '<?= $schoolyearsessionobj->endingdate ?>',
    });

    $('#classesID').change(function(event) {
        var classesID = $(this).val();
        $('#sectionID').val('0').trigger('change');
        $('#studentID').val(null).trigger('change');
        if (classesID === '0') {
            $('#sectionID').html('<option value="0">Select Section</option>');
        } else {
            $.ajax({
                type: 'POST',
                url: "<?= base_url('student/sectioncall') ?>",
                data: { 'id': classesID },
                dataType: "html",
                success: function(data) {
                    $('#sectionID').html(data);
                }
            });
        }
    });

    function getRandomInt() {
        return Math.floor(Math.random() * Math.floor(9999999999999999));
    }

    function productItemDesign(feetypeID, productText, price = null) {
        var randID = getRandomInt();
        if ($('#feetypeList tr:last').text() == '') {
            var lastTdNumber = 0;
        } else {
            var lastTdNumber = $("#feetypeList tr:last td:eq(0)").text();
        }

        lastTdNumber = parseInt(lastTdNumber);
        lastTdNumber++;

        var text = '<tr id="tr_' + randID + '" invoicefeetypeID="' + feetypeID + '">';
        text += '<td>';
        text += lastTdNumber;
        text += '</td>';

        text += '<td>';
        text += productText;
        text += '</td>';

        if (price == null) {
            text += '<td>';
            text += ('<input type="text" class="form-control change-amount" id="td_amount_id_' + randID + '" data-amount-id="' + randID + '">');
            text += '</td>';
        } else {
            text += '<td>';
            text += ('<input type="text" value="' + price + '" class="form-control change-amount" id="td_amount_id_' + randID + '" data-amount-id="' + randID + '">');
            text += '</td>';
        }


        text += '<td>';
        text += ('<input type="text" class="form-control change-discount" id="td_discount_id_' + randID + '" data-discount-id="' + randID + '" readonly style="background:#f0f0f0;cursor:not-allowed;" tabindex="-1">');
        text += '</td>';


        if (price == null) {
            text += '<td>';
            text += '0.00';
            text += '</td>';
        } else {
            text += '<td>';
            text += price;
            text += '</td>';
        }



        text += '<td>';
        if ($('#statusID').val() != 0 && $('#statusID').val() != 5) {
            text += ('<input type="text" class="form-control change-paidamount" id="td_paidamount_id_' + randID + '" data-paidamount-id="' + randID + '">');
        } else {
            text += ('<input type="text" class="form-control change-paidamount" id="td_paidamount_id_' + randID + '" data-paidamount-id="' + randID + '" readonly="readonly">');
        }
        text += '</td>';

        text += '<td>';
        text += ('<a style="margin-top:3px" href="#" class="btn btn-danger btn-sm deleteBtn" id="feetype_' + randID + '" data-feetype-id="' + randID + '"><i class="fa fa-trash-o"></i></a>');
        text += '</td>';
        text += '</tr>';

        return text;
    }

    $('#feetypeID').change(function(e) {
        var feetypeID = $(this).val();
        if (feetypeID == 0) return;

        var feetypeText = $(this).find(":selected").text();

        // Get specifically-selected students (exclude the "All Students" sentinel value 0)
        var selectedStudents = $('#studentID').val() || [];
        var specificStudents = $.grep(selectedStudents, function(v) { return v != '0'; });

        if (specificStudents.length > 0) {
            // AJAX check: does any selected student already have this fee type this year?
            $.ajax({
                type: 'POST',
                url: '<?= base_url("invoice/check_duplicate_feetype") ?>',
                data: { feetypeID: feetypeID, studentIDs: specificStudents },
                dataType: 'json',
                success: function(res) {
                    if (res.has_dup) {
                        toastr["error"]('"' + feetypeText + '" already exists for: ' + res.students.join(', ') + '. Remove the existing invoice first.');
                        toastr.options = { "closeButton": true, "timeOut": "6000" };
                    } else {
                        $('#feetypeList').append(productItemDesign(feetypeID, feetypeText));
                    }
                },
                error: function() {
                    // On network error fall back to just adding (server will block on save)
                    $('#feetypeList').append(productItemDesign(feetypeID, feetypeText));
                }
            });
        } else {
            // No specific student selected (all-students mode) — add the row; server validates per student
            $('#feetypeList').append(productItemDesign(feetypeID, feetypeText));
        }
    });

    function toFixedVal(x) {
        if (Math.abs(x) < 1.0) {
            var e = parseFloat(x.toString().split('e-')[1]);
            if (e) {
                x *= Math.pow(10, e - 1);
                x = '0.' + (new Array(e)).join('0') + x.toString().substring(2);
            }
        } else {
            var e = parseFloat(x.toString().split('+')[1]);
            if (e > 20) {
                e -= 20;
                x /= Math.pow(10, e);
                x += (new Array(e + 1)).join('0');
            }
        }
        return x;
    }

    function isNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

    function dotAndNumber(data) {
        var retArray = [];
        var fltFlag = true;
        if (data.length > 0) {
            for (var i = 0; i <= (data.length - 1); i++) {
                if (i == 0 && data.charAt(i) == '.') {
                    fltFlag = false;
                    retArray.push(true);
                } else {
                    if (data.charAt(i) == '.' && fltFlag == true) {
                        retArray.push(true);
                        fltFlag = false;
                    } else {
                        if (isNumeric(data.charAt(i))) {
                            retArray.push(true);
                        } else {
                            retArray.push(false);
                        }
                    }

                }
            }
        }

        if (jQuery.inArray(false, retArray) == -1) {
            return true;
        }
        return false;
    }

    function floatChecker(value) {
        var val = value;
        if (isNumeric(val)) {
            return true;
        } else {
            return false;
        }
    }

    function lenChecker(data, len) {
        var retdata = 0;
        var lencount = 0;
        data = toFixedVal(data);
        if (data.length > len) {
            lencount = (data.length - len);
            data = data.toString();
            data = data.slice(0, -lencount);
            retdata = parseFloat(data);
        } else {
            retdata = parseFloat(data);
        }

        return toFixedVal(retdata);
    }

    function parseSentenceForNumber(sentence) {
        var matches = sentence.replace(/,/g, '').match(/(\+|-)?((\d+(\.\d+)?)|(\.\d+))/);
        return matches && matches[0] || null;
    }

    function currencyConvert(data) {
        return data.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    }

    var globaltotalamount = 0;
    var globaltotaldiscount = 0;
    var globaltotalsubtotal = 0;
    var globaltotalpaidamount = 0;

    function totalInfo() {
        var i = 1;
        var j = 1;

        var totalAmount = 0;
        var totalDiscount = 0;
        var totalSubtotal = 0;
        var totalPaidAmount = 0;

        var discount = 0;

        $('#feetypeList tr').each(function(index, value) {
            if ($(this).children().eq(2).children().val() != '' && $(this).children().eq(2).children().val() != null && $(this).children().eq(2).children().val() != '.') {
                var amount = parseFloat($(this).children().eq(2).children().val());
                totalAmount += amount;
            }
        });
        globaltotalamount = totalAmount;
        $('#totalAmount').text(currencyConvert(totalAmount));

        $('#feetypeList tr').each(function(index, value) {
            if ($(this).children().eq(3).children().val() != '' && $(this).children().eq(3).children().val() != null && $(this).children().eq(3).children().val() != '.') {
                var discount = parseFloat($(this).children().eq(3).children().val());
                totalDiscount += discount;
            }
        });
        globaltotaldiscount = totalDiscount;
        $('#totalDiscount').text(currencyConvert(totalDiscount));


        $('#feetypeList tr').each(function(index, value) {
            var amount = parseFloat($(this).children().eq(2).children().val());
            var discount = parseFloat($(this).children().eq(3).children().val());
            var subtotal = 0;
            if (amount > 0) {
                if (discount > 0) {
                    // if(discount == 100) {
                    //     subtotal = 0;
                    // } else {
                    //     subtotal = (amount - ((amount/100) * discount));
                    // }
                    subtotal = (amount - discount);
                } else {
                    subtotal = amount;
                }
            }

            $(this).children().eq(4).text(subtotal);
            totalSubtotal += subtotal;
        });
        globaltotalsubtotal = totalSubtotal;
        $('#totalSubtotal').text(currencyConvert(totalSubtotal));

        $('#feetypeList tr').each(function(index, value) {
            if ($(this).children().eq(5).children().val() != '' && $(this).children().eq(5).children().val() != null && $(this).children().eq(5).children().val() != '.') {
                var paidamount = parseFloat($(this).children().eq(5).children().val());
                totalPaidAmount += paidamount;
            }
        });
        globaltotalpaidamount = totalPaidAmount;
        $('#totalPaidAmount').text(currencyConvert(totalPaidAmount));

    }

    $(document).on('keyup', '.change-amount', function() {
        var amount = toFixedVal($(this).val());
        var amountID = $(this).attr('data-amount-id');

        if (dotAndNumber(amount)) {
            if (amount.length > 15) {
                amount = lenChecker(amount, 15);
                $(this).val(amount);
            }

            if (amount != '' && amount != null) {
                $(this).val(amount);
                totalInfo();
            } else {
                totalInfo();
            }
        } else {
            var amount = parseSentenceForNumber(toFixedVal($(this).val()));
            $(this).val(amount);
        }

        removePaidAmount(amountID);
    });

    $(document).on('keyup', '.change-paidamount', function() {
        var trID = $(this).parent().parent().attr('id').replace('tr_', '');
        var amount = $('#' + 'td_amount_id_' + trID).val();
        var discount = $('#' + 'td_discount_id_' + trID).val();

        if (discount != '' && discount != null) {
            // amount = (amount - ((amount/100) * discount));
            amount = (amount - discount);
        }

        if (amount != '' && amount != null) {
            var paidamount = toFixedVal($(this).val());
            var paidamountID = $(this).attr('data-paidamount-id');

            if (dotAndNumber(paidamount)) {
                if (paidamount.length > 15) {
                    paidamount = lenChecker(paidamount, 15);
                    if (parseFloat(paidamount) > parseFloat(amount)) {
                        $(this).val(amount);
                    } else {
                        $(this).val(paidamount);
                    }
                }

                if (paidamount != '' && paidamount != null) {
                    if (parseFloat(paidamount) > parseFloat(amount)) {
                        $(this).val(amount);
                    } else {
                        $(this).val(paidamount);
                    }
                    totalInfo();
                } else {
                    totalInfo();
                }
            } else {
                var paidamount = parseSentenceForNumber(toFixedVal($(this).val()));
                if (parseFloat(paidamount) > parseFloat(amount)) {
                    $(this).val(amount);
                } else {
                    $(this).val(paidamount);
                }
            }
        } else {
            $(this).val('');
        }
    });

    $(document).on('click focus', '.change-discount', function() {
        alert('You can give discount in global payment page.');
        $(this).blur();
    });

    $(document).on('keyup', '.change-discount', function() {
        var trID = $(this).parent().parent().attr('id').replace('tr_', '');
        var randID = $(this).attr('data-discount-id');
        var amount = $('#' + 'td_amount_id_' + trID).val();
        if (amount != '' && amount != null) {
            var discount = toFixedVal($(this).val());
            var discountID = $(this).attr('data-discount-id');
            // console.log(discount);
            if (dotAndNumber(discount)) {
                // if(discount > 100) {
                //     discount = 100;
                // }
                if (discount != '' && discount != null) {
                    if (parseFloat(discount) > parseFloat(amount)) {
                        discount = amount;
                    }
                }
                $(this).val(discount);
                totalInfo();
            } else {
                var discount = parseSentenceForNumber(toFixedVal($(this).val()));
                if (discount != '' && discount != null) {
                    if (parseFloat(discount) > parseFloat(amount)) {
                        discount = amount;
                    }
                }
                $(this).val(discount);
            }
        } else {
            $(this).val('');
        }

        removePaidAmount(randID);
    });

    $(document).on('click', '.deleteBtn', function(er) {
        er.preventDefault();
        var feetypeID = $(this).attr('data-feetype-id');
        $('#tr_' + feetypeID).remove();

        var i = 1;
        $('#feetypeList tr').each(function(index, value) {
            $(this).children().eq(0).text(i);
            i++;
        });
        totalInfo();
    });

    function removePaidAmount(randID) {
        var ramount = $('#td_amount_id_' + randID).val();
        var rdiscount = $('#td_discount_id_' + randID).val();
        var rpaidamount = ($('#td_paidamount_id_' + randID).val());

        if (ramount == '' && ramount == null) {
            ramount = 0;
        }

        if (rdiscount == '' && rdiscount == null) {
            rdiscount = 0;
        }

        if (rpaidamount != '' && rpaidamount != null) {
            // ramount = parseFloat((ramount - (ramount/100) * rdiscount)); 
            ramount = parseFloat((ramount - rdiscount));
            rpaidamount = parseFloat(rpaidamount);
            if (rpaidamount > ramount) {
                $('#td_paidamount_id_' + randID).val('');
            }
        }
    }


    $(document).on('change', '#sectionID', function() {
        var sectionID = $(this).val();
        var classesID = $("#classesID").val();
        $('#studentID').val(null).trigger('change');
        if (sectionID === '0') {
            $('#studentID').empty().append('<option value="0"><?= $this->lang->line('invoice_all_student') ?></option>');
            $('#studentID').trigger('change');
        } else {
            $.ajax({
                type: 'POST',
                url: "<?= base_url('invoice/getstudent') ?>",
                data: { 'classesID': classesID, 'sectionID': sectionID },
                dataType: "html",
                success: function(data) {
                    $('#studentID').empty().append(data);
                    $('#studentID').trigger('change');
                }
            });
        }
    });

</script>

<script type="text/javascript">
    $('#statusID').change(function() {
        if (($(this).val() != 0) && ($(this).val() != 5)) {
            $('.paymentmethodDiv').removeClass('hide');

            $('#feetypeList tr').each(function(index, value) {
                $(this).children().eq(5).children().removeAttr('readonly');
            });
        } else {
            $('.paymentmethodDiv').addClass('hide');

            $('#feetypeList tr').each(function(index, value) {
                $(this).children().eq(5).children().attr('readonly', 'readonly');
            });
        }
    });

    $(document).on('click', '#addInvoiceButton', function() {
        var error = 0;
        var classesID = $('#classesID').val();
        var selectedStudents = $('#studentID').val(); // array or null
        var date = $('#date').val();
        var statusID = $('#statusID').val();
        var payment_method = $('#payment_method').val();

        if (classesID === '0') {
            $('.classesDiv').addClass('has-error');
            error++;
        } else {
            $('.classesDiv').removeClass('has-error');
        }

        if (date === '') {
            $('.dateDiv').addClass('has-error');
            error++;
        } else {
            $('.dateDiv').removeClass('has-error');
        }

        if (statusID === '5') {
            $('.statusDiv').addClass('has-error');
            error++;
        } else {
            $('.statusDiv').removeClass('has-error');
        }

        if (statusID != 0 && statusID != 5) {
            if (payment_method === '0') {
                $('.paymentmethodDiv').addClass('has-error');
                error++;
            } else {
                $('.paymentmethodDiv').removeClass('has-error');
            }
        }

        var totalsubtotal = 0;
        var totalpaidamount = 0;
        var feetypeitems = $('tr[id^=tr_]').map(function() {
            if ($(this).children().eq(4).text() != '' && $(this).children().eq(4).text() != null) {
                totalsubtotal += parseFloat($(this).children().eq(4).text());
            }
            if ($(this).children().eq(5).children().val() != '' && $(this).children().eq(5).children().val() != null) {
                totalpaidamount += parseFloat($(this).children().eq(5).children().val());
            }
            return {
                feetypeID: $(this).attr('invoicefeetypeid'),
                amount: $(this).children().eq(2).children().val(),
                discount: $(this).children().eq(3).children().val(),
                subtotal: $(this).children().eq(4).text(),
                paidamount: $(this).children().eq(5).children().val()
            };
        }).get();

        if (typeof feetypeitems == 'undefined' || feetypeitems.length <= 0) {
            error++;
            toastr["error"]('The fee type item is required.');
        }

        if (error === 0) {
            // Determine effective studentID
            var studentIDVal;
            if (!selectedStudents || selectedStudents.length === 0 || (selectedStudents.length === 1 && selectedStudents[0] == '0')) {
                studentIDVal = '0';
            } else {
                studentIDVal = selectedStudents.filter(function(v) { return v != '0'; });
            }

            var isBulkMode = (studentIDVal === '0');

            // Base data without studentID — reused by both direct submit and modal proceed
            var baseData = {
                classesID:       classesID,
                sectionID:       $('#sectionID').val(),
                date:            date,
                statusID:        statusID,
                payment_method:  payment_method,
                feetypeitems:    JSON.stringify(feetypeitems),
                totalsubtotal:   totalsubtotal,
                totalpaidamount: totalpaidamount
            };

            if (isBulkMode) {
                // Bulk mode: pre-check which students already have these fee types
                $(this).attr('disabled', 'disabled');
                var feetypeIDs = $('tr[id^=tr_]').map(function() {
                    return $(this).attr('invoicefeetypeid');
                }).get();

                $.ajax({
                    type: 'POST',
                    url: '<?= base_url("invoice/get_bulk_duplicate_students") ?>',
                    data: { classesID: classesID, sectionID: baseData.sectionID, feetypeIDs: feetypeIDs },
                    dataType: 'json',
                    success: function(res) {
                        $('#addInvoiceButton').removeAttr('disabled');
                        if (res.status && res.has_issues) {
                            // Build modal content
                            // Section 1: partial duplicates (will receive missing fee types)
                            if (res.partial_count > 0) {
                                var pHtml = '';
                                $.each(res.partial_students, function(i, d) {
                                    pHtml += '<li style="padding:7px 10px; margin-bottom:4px; background:#f8fbff; border:1px solid #d6eaf8; border-radius:4px; font-size:13px;">'
                                           + '<strong style="color:#1a3a4e;">' + d.name + '</strong>'
                                           + '&nbsp;&nbsp;<span style="background:#fdecea; color:#922b21; border-radius:3px; padding:1px 7px; font-size:11px; font-weight:600; margin-right:4px;">Already has: ' + d.already_has.join(', ') + '</span>'
                                           + '<span style="color:#5d8aa8;">&#8594;</span>&nbsp;'
                                           + '<span style="background:#d6eaf8; color:#1a5276; border-radius:3px; padding:1px 7px; font-size:11px; font-weight:600;">Will add: ' + d.will_add.join(', ') + '</span>'
                                           + '</li>';
                                });
                                $('#dupPartialList').html(pHtml);
                                $('#dupPartialSection').show();
                            } else {
                                $('#dupPartialSection').hide();
                            }

                            // Section 2: fully duplicate (will be skipped)
                            if (res.fully_dup_count > 0) {
                                var fHtml = '';
                                $.each(res.fully_duplicate, function(i, d) {
                                    fHtml += '<li style="padding:7px 10px; margin-bottom:4px; background:#fef9f9; border:1px solid #fadbd8; border-radius:4px; font-size:13px;">'
                                           + '<strong style="color:#922b21;">' + d.name + '</strong>'
                                           + '&nbsp;&nbsp;<span style="background:#fadbd8; color:#922b21; border-radius:3px; padding:1px 7px; font-size:11px; font-weight:600;">' + d.already_has.join(', ') + '</span>'
                                           + '&nbsp;<em style="color:#aaa; font-size:11px;">(no new invoice)</em>'
                                           + '</li>';
                                });
                                $('#dupFullList').html(fHtml);
                                $('#dupFullSection').show();
                            } else {
                                $('#dupFullSection').hide();
                            }

                            // Summary message
                            var totalProceeding = res.partial_count + res.clean_count;
                            if (totalProceeding > 0) {
                                $('#dupConfirmMsg').text('Proceed to add invoices for ' + totalProceeding + ' student(s)?');
                                $('#dupProceedBtn').show();
                            } else {
                                $('#dupConfirmMsg').text('All students already have all these fee types. Nothing to add.');
                                $('#dupProceedBtn').hide();
                            }

                            // On proceed: send partial_ids + clean_ids with skip_duplicates=1
                            var proceedIDs = res.partial_ids.concat(res.clean_ids);
                            window._pendingInvoice = { baseData: baseData, studentIDs: proceedIDs, skipDup: true };
                            $('#dupStudentModal').modal('show');
                        } else {
                            // No duplicates — submit normally for all students
                            $('#addInvoiceButton').attr('disabled', 'disabled');
                            doSubmitInvoice(baseData, '0', false);
                        }
                    },
                    error: function() {
                        toastr['error']('Could not check for duplicate students. Please try again.');
                        $('#addInvoiceButton').removeAttr('disabled');
                    }
                });
            } else {
                // Individual student mode — submit directly; server blocks if duplicate
                $(this).attr('disabled', 'disabled');
                doSubmitInvoice(baseData, studentIDVal, false);
            }
        }
    });

    // Shared submit helper — builds FormData and sends to saveinvoice
    // skipDup=true adds skip_duplicates=1 so server skips per-combo duplicates
    function doSubmitInvoice(baseData, studentIDVal, skipDup) {
        var fd = new FormData();
        fd.append('classesID',       baseData.classesID);
        fd.append('sectionID',       baseData.sectionID);
        fd.append('date',            baseData.date);
        fd.append('statusID',        baseData.statusID);
        fd.append('payment_method',  baseData.payment_method);
        fd.append('feetypeitems',    baseData.feetypeitems);
        fd.append('totalsubtotal',   baseData.totalsubtotal);
        fd.append('totalpaidamount', baseData.totalpaidamount);
        fd.append('editID',          0);
        if (skipDup) { fd.append('skip_duplicates', '1'); }
        if (Array.isArray(studentIDVal)) {
            $.each(studentIDVal, function(i, v) { fd.append('studentID[]', v); });
        } else {
            fd.append('studentID[]', studentIDVal);
        }
        makingPostDataPreviousofAjaxCall(fd);
    }

    // Modal proceed button — submit for partial + clean students with skip_duplicates=1
    $('#dupProceedBtn').on('click', function() {
        $('#dupStudentModal').modal('hide');
        $('#addInvoiceButton').attr('disabled', 'disabled');
        var p = window._pendingInvoice;
        doSubmitInvoice(p.baseData, p.studentIDs, p.skipDup);
    });

    function makingPostDataPreviousofAjaxCall(field) {
        passData = field;
        ajaxCall(passData);
    }

    function ajaxCall(passData) {
        $.ajax({
            type: 'POST',
            url: "<?= base_url('invoice/saveinvoice') ?>",
            data: passData,
            async: true,
            dataType: "html",
            success: function(data) {
                var response = JSON.parse(data);
                errrorLoader(response);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }

    function errrorLoader(response) {
        if (response.status) {
            window.location = "<?= base_url("invoice/index") ?>";
        } else {
            $('#addInvoiceButton').removeAttr('disabled');
            $.each(response.error, function(index, val) {
                toastr["error"](val)
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
            });
        }
    }


    // Multi-select: no auto hostel fee per student (hostel applies per individual invoice after save)

</script>