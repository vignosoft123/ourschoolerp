
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-payment"></i> <?=$this->lang->line('panel_title')?></h3>

       
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("paymenthistory/index")?>"><?=$this->lang->line('menu_paymenthistory')?></a></li>
            <li class="active"><?=$this->lang->line('menu_edit')?> <?=$this->lang->line('menu_paymenthistory')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-10">
                <form class="form-horizontal" role="form" method="post">
                    <?php 
                        if(form_error('amount')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="amount" class="col-sm-2 control-label">
                            <?=$this->lang->line("paymenthistory_amount")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="amount" name="amount" value="<?=set_value('amount', $payment->paymentamount)?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('amount'); ?>
                        </span>
                    </div>

                    <?php 
                        if(form_error('payment_method')) 
                            echo "<div class='form-group has-error' >";
                        else     
                            echo "<div class='form-group' >";
                    ?>
                        <label for="payment_method" class="col-sm-2 control-label">
                            <?=$this->lang->line("paymenthistory_paymentmethod")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $array = array('0' => $this->lang->line("paymenthistory_select_paymentmethod"));
                                $array['Cash'] = $this->lang->line('Cash');
                                $array['Digital'] = 'Digital';
                                $array['Cheque'] = $this->lang->line('Cheque');
                                $array['Others'] = 'Others';
                                echo form_dropdown("payment_method", $array, set_value("payment_method", $payment->paymenttype), "id='payment_method' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('payment_method'); ?>
                        </span>
                    </div>

                    <?php
                        $selectedPaymentType = strtolower(set_value('payment_method', isset($payment->paymenttype) ? $payment->paymenttype : ''));
                        $curBank = set_value('payment_bank_name', !empty($payment->payment_other_details) ? $payment->payment_other_details : '');
                        $showBankField = ($selectedPaymentType === 'others');
                    ?>
                    <div class="form-group" id="payment_bank_div" style="<?= $showBankField ? '' : 'display:none;' ?>">
                        <label for="payment_bank_name" class="col-sm-2 control-label">
                            Bank Name <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <select name="payment_bank_name" id="payment_bank_name" class="form-control select2">
                                <option value="">-- Select Bank --</option>
                                <?php if(customCompute($banks)) foreach($banks as $bank): ?>
                                    <option value="<?=htmlspecialchars($bank->bank_name)?>" <?=($curBank == $bank->bank_name ? 'selected' : '')?>><?=htmlspecialchars($bank->bank_name)?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <?php 
                        if(form_error('payment_date'))
                            echo "<div class='form-group has-error' >";
                        else
                            echo "<div class='form-group' >";
                    ?>
                        <label for="payment_date" class="col-sm-2 control-label">
                            <?=$this->lang->line("paymenthistory_date")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?=set_value('payment_date', !empty($payment->paymentdate) ? date('Y-m-d', strtotime($payment->paymentdate)) : date('Y-m-d'))?>" >
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('payment_date'); ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input type="submit" class="btn btn-success" value="<?=$this->lang->line("update_payment")?>" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function(){
    function toggleBankField() {
        var paymentType = $('#payment_method').val();
        if (paymentType && paymentType.toLowerCase() === 'others') {
            $('#payment_bank_div').show();
        } else {
            $('#payment_bank_div').hide();
            $('#payment_bank_name').val('');
        }
    }

    $('#payment_method').on('change', toggleBankField);
    toggleBankField();
    $('#payment_method').trigger('change');
    $('.select2').select2();
});
</script>