<div class="rpt-action-bar">
    <?php
        if($fromdate != '' && $todate !='' )  {
            $pdf_preview_uri = base_url('accountledgerreport/pdf/'.$schoolyearID.'/'.strtotime($fromdate).'/'.strtotime($todate));
        } else {
            $pdf_preview_uri = base_url('accountledgerreport/pdf/'.$schoolyearID);
        }
        echo btn_printReport('accountledgerreport', $this->lang->line('report_print'), 'printablediv');
        echo btn_pdfPreviewReport('accountledgerreport',$pdf_preview_uri, $this->lang->line('report_pdf_preview'));
        echo btn_sentToMailReport('accountledgerreport', $this->lang->line('report_send_pdf_to_mail'));
    ?>
</div>
<div class="box">
    <div class="rpt-box-header">
        <h3><i class="fa fa-clipboard"></i>
            <?=$this->lang->line('accountledgerreport_report_for')?> - <?=$this->lang->line('accountledgerreport_accountledger')?>
        </h3>
    </div><!-- /.box-header -->
    <div id="printablediv">
    <!-- form start -->
        <div class="box-body" style="margin-bottom: 50px;">
            <div class="row">
                <div class="col-sm-12">
                    <div class="reportPage-header">
                        <span class="header" id="headerImage"><p class="bannerLogo"><img src="<?=base_url('uploads/images/'.$siteinfos->photo)?>"></p></span>
                        <p class="title"><?=$siteinfos->sname?></p>
                        <p class="title-desc"><?=$siteinfos->address?></p>
                        <p class="title-desc"><?=$this->lang->line('topbar_academic_year'). ' : '. $schoolyearName?></p>
                    </div>
                </div>
                <?php $m = true; if($fromdate !='' && $todate !='') { $m = false; ?>
                    <div class="col-sm-12">
                        <h5 class="pull-left"><?=$this->lang->line('accountledgerreport_fromdate')?> : <?=date('d M Y',strtotime($fromdate))?></h5>
                        <h5 class="pull-right"><?=$this->lang->line('accountledgerreport_todate')?> : <?=date('d M Y',strtotime($todate))?></h5>
                    </div>
                <?php } ?>
                <?php $currencyLabel = !empty($siteinfos->currency_code) ? " (".$siteinfos->currency_code.")" : ''; ?>
                <div class="col-sm-12" style="<?=$m ? "margin-top: 15px" :''?>">
                    <div class="rpt-ledger-grid">
                        <div class="rpt-ledger-col">
                            <div class="rpt-ledger-card rpt-ledger-card--income">
                                <p class="rpt-ledger-card-title"><?=$this->lang->line('accountledgerreport_income')?></p>
                                <p class="rpt-ledger-card-desc"><?=$this->lang->line('accountledgerreport_income_des')?></p>
                                <div class="rpt-ledger-row">
                                    <span class="rpt-ledger-row-label"><?=$this->lang->line('accountledgerreport_total')?><?=$currencyLabel?></span>
                                    <span class="rpt-ledger-row-value"><?=number_format($totalincome,2)?></span>
                                </div>
                            </div>

                            <div class="rpt-ledger-card rpt-ledger-card--balance">
                                <p class="rpt-ledger-card-title"><?=$this->lang->line('accountledgerreport_total_balance')?></p>
                                <div class="rpt-ledger-row">
                                    <span class="rpt-ledger-row-label"><?=$this->lang->line('accountledgerreport_income')?> (+)</span>
                                    <span class="rpt-ledger-row-value"><?=number_format($totalincome,2)?></span>
                                </div>
                                <div class="rpt-ledger-row">
                                    <span class="rpt-ledger-row-label"><?=$this->lang->line('accountledgerreport_fees_collections')?> (+)</span>
                                    <span class="rpt-ledger-row-value"><?=number_format($totalcollection,2)?></span>
                                </div>
                                <div class="rpt-ledger-row">
                                    <span class="rpt-ledger-row-label"><?=$this->lang->line('accountledgerreport_fines')?> (+)</span>
                                    <span class="rpt-ledger-row-value"><?=number_format($totalfine,2)?></span>
                                </div>
                                <div class="rpt-ledger-row">
                                    <span class="rpt-ledger-row-label"><?=$this->lang->line('accountledgerreport_expense')?> (-)</span>
                                    <span class="rpt-ledger-row-value"><?=number_format($totalexpense,2)?></span>
                                </div>
                                <div class="rpt-ledger-row">
                                    <span class="rpt-ledger-row-label"><?=$this->lang->line('accountledgerreport_salary')?> (-)</span>
                                    <span class="rpt-ledger-row-value"><?=number_format($totalsalarypayment,2)?></span>
                                </div>
                                <div class="rpt-ledger-row rpt-ledger-row--grand">
                                    <span class="rpt-ledger-row-label"><?=$this->lang->line('accountledgerreport_grand_total')?><?=$currencyLabel?></span>
                                    <span class="rpt-ledger-row-value">
                                        <?php
                                            $mainincome  = ($totalincome + $totalcollection + $totalfine);
                                            $mainexpense = ($totalexpense + $totalsalarypayment);
                                            $mainbalance  = ($mainincome - $mainexpense);
                                            echo number_format($mainbalance,2);
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="rpt-ledger-col">
                            <div class="rpt-ledger-card rpt-ledger-card--fees">
                                <p class="rpt-ledger-card-title"><?=$this->lang->line('accountledgerreport_fees_collections')?></p>
                                <p class="rpt-ledger-card-desc"><?=$this->lang->line('accountledgerreport_fees_collections_des')?></p>
                                <div class="rpt-ledger-row">
                                    <span class="rpt-ledger-row-label"><?=$this->lang->line('accountledgerreport_total')?><?=$currencyLabel?></span>
                                    <span class="rpt-ledger-row-value"><?=number_format($totalcollection,2)?></span>
                                </div>
                            </div>

                            <div class="rpt-ledger-card rpt-ledger-card--fines">
                                <p class="rpt-ledger-card-title"><?=$this->lang->line('accountledgerreport_fines')?></p>
                                <p class="rpt-ledger-card-desc"><?=$this->lang->line('accountledgerreport_fines_des')?></p>
                                <div class="rpt-ledger-row">
                                    <span class="rpt-ledger-row-label"><?=$this->lang->line('accountledgerreport_total')?><?=$currencyLabel?></span>
                                    <span class="rpt-ledger-row-value"><?=number_format($totalfine,2)?></span>
                                </div>
                            </div>

                            <div class="rpt-ledger-card rpt-ledger-card--expense">
                                <p class="rpt-ledger-card-title"><?=$this->lang->line('accountledgerreport_expense')?></p>
                                <p class="rpt-ledger-card-desc"><?=$this->lang->line('accountledgerreport_expense_des')?></p>
                                <div class="rpt-ledger-row">
                                    <span class="rpt-ledger-row-label"><?=$this->lang->line('accountledgerreport_total')?><?=$currencyLabel?></span>
                                    <span class="rpt-ledger-row-value"><?=number_format($totalexpense,2)?></span>
                                </div>
                            </div>

                            <div class="rpt-ledger-card rpt-ledger-card--salary">
                                <p class="rpt-ledger-card-title"><?=$this->lang->line('accountledgerreport_salary')?></p>
                                <p class="rpt-ledger-card-desc"><?=$this->lang->line('accountledgerreport_salary_des')?></p>
                                <div class="rpt-ledger-row">
                                    <span class="rpt-ledger-row-label"><?=$this->lang->line('accountledgerreport_total')?><?=$currencyLabel?></span>
                                    <span class="rpt-ledger-row-value"><?=number_format($totalsalarypayment,2)?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 text-center footerAll">
                    <?=reportfooter($siteinfos, $schoolyearsessionobj)?>
                </div>
            </div><!-- row -->
        </div><!-- Body -->
    </div>
</div>


<!-- email modal starts here -->
<form class="form-horizontal" role="form" action="<?=base_url('accountledgerreport/send_pdf_to_mail');?>" method="post">
    <div class="modal fade" id="mail">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=$this->lang->line('accountledgerreport_close')?></span></button>
                <h4 class="modal-title"><?=$this->lang->line('accountledgerreport_mail')?></h4>
            </div>
            <div class="modal-body">

                <?php
                    if(form_error('to'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                ?>
                    <label for="to" class="col-sm-2 control-label">
                        <?=$this->lang->line("accountledgerreport_to")?> <span class="text-red">*</span>
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
                        <?=$this->lang->line("accountledgerreport_subject")?> <span class="text-red">*</span>
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
                        <?=$this->lang->line("accountledgerreport_message")?>
                    </label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="message" style="resize: vertical;" name="message" value="<?=set_value('message')?>" ></textarea>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                <input type="button" id="send_pdf" class="btn btn-success" value="<?=$this->lang->line("accountledgerreport_send")?>" />
            </div>
        </div>
      </div>
    </div>
</form>
<!-- email end here -->

<script type="text/javascript">
    
    function check_email(email) {
        var status = false;
        var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        if (email.search(emailRegEx) == -1) {
            $("#to_error").html('');
            $("#to_error").html("<?=$this->lang->line('accountledgerreport_mail_valid')?>").css("text-align", "left").css("color", 'red');
        } else {
            status = true;
        }
        return status;
    }


    $('#send_pdf').click(function() {
        var field = {
            'to'          : $('#to').val(), 
            'subject'     : $('#subject').val(), 
            'message'     : $('#message').val(),
            'fromdate'    : '<?=$fromdate?>',
            'todate'      : '<?=$todate?>',
            'schoolyearID': '<?=$schoolyearID?>',
        };

        var to = $('#to').val();
        var subject = $('#subject').val();
        var error = 0;

        $("#to_error").html("");
        $("#subject_error").html("");

        if(to == "" || to == null) {
            error++;
            $("#to_error").html("<?=$this->lang->line('accountledgerreport_mail_to')?>").css("text-align", "left").css("color", 'red');
        } else {
            if(check_email(to) == false) {
                error++
            }
        }

        if(subject == "" || subject == null) {
            error++;
            $("#subject_error").html("<?=$this->lang->line('accountledgerreport_mail_subject')?>").css("text-align", "left").css("color", 'red');
        } else {
            $("#subject_error").html("");
        }

        if(error == 0) {
            $('#send_pdf').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: "<?=base_url('accountledgerreport/send_pdf_to_mail')?>",
                data: field,
                dataType: "html",
                success: function(data) {
                    var response = JSON.parse(data);
                    if (response.status == false) {
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