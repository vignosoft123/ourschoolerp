<!DOCTYPE html>
<html lang="en">
<body style="font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; color:#000; margin:0; padding:0;">

<div style="width:750px; margin:6px auto; padding:10px; border:1px solid #1a237e; border-radius:8px;">

    <!-- ===== Header ===== -->
    <table width="100%" style="border:none;">
        <tr>
            <td style="width:15%; border:none; text-align:center;">
                <?php if($siteinfos->photo) { ?>
                    <img src="<?php echo base_url('uploads/images/'.$siteinfos->photo);?>" style="width:70px; height:70px; border-radius:50%;">
                <?php } ?>
            </td>
            <td style="width:60%; border:none;">
                <h2 style="margin:0; font-size:18px; color:#1a237e;"><?=$siteinfos->sname?></h2>
                <div style="margin-top:2px; color:#555; font-size:11px; line-height:1.4;">
                    <span style="display:block;"><?=$siteinfos->address?></span>
                    <span style="display:block;"><?=$siteinfos->email?> | <?=$siteinfos->phone?></span>
                </div>
            </td>
            <td style="width:25%; border:none;">
                <table width="100%" style="background:#1a237e; color:#fff; border-radius:4px;" cellpadding="4">
                    <tr><td style="border:none; font-size:9px; color:#fff;">ACADEMIC YEAR</td></tr>
                    <tr><td style="border:none; font-size:12px; font-weight:bold; color:#fff;"><?=isset($schoolyearsessionobj->schoolyear) ? $schoolyearsessionobj->schoolyear : ''?></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" style="border:none; background:#fff3e0; margin-top:4px;">
        <tr><td style="border:none; text-align:center; color:#e65100; font-weight:bold; padding:4px;">
            EXPENSE REPORT
        </td></tr>
    </table>

    <!-- ===== Filter Info ===== -->
    <table width="100%" style="border:none; margin-top:5px;">
        <tr>
            <td style="border:none; text-align:left; font-size:11px;">
                <?php if($fromdate != '' && $todate != '' ) { ?>
                    <b><?=$this->lang->line('productpurchasereport_fromdate')?> :</b> <?=$fromdate?>
                <?php } elseif($reference_no != '0') { ?>
                    <b><?=$this->lang->line('productpurchasereport_referenceNo')?> :</b> <?=$reference_no?>
                <?php } elseif($expensetypesID != 0) { ?>
                    <b>Category :</b>
                    <?php
                        foreach($expensetypes as $expensetype) {
                            if($expensetype->expensetypesID == $expensetypesID) {
                                echo $expensetype->expensetypes;
                            }
                        }
                    ?>
                <?php } ?>
            </td>
            <td style="border:none; text-align:right; font-size:11px;">
                <?php if($fromdate != '' && $todate != '' ) { ?>
                    <b><?=$this->lang->line('productpurchasereport_todate')?> :</b> <?=$todate?>
                <?php } ?>
            </td>
        </tr>
    </table>

    <!-- ===== Expenses Table ===== -->
    <?php if (customCompute($expenses)) { ?>
        <table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse; margin-top:6px; text-align:center; font-size:10px;">
            <tr style="background:#1a237e; color:#ffffff;">
                <th style="border:1px solid #ddd; color:#ffffff;"><?=$this->lang->line('slno')?></th>
                <th style="border:1px solid #ddd; color:#ffffff;">Reference No</th>
                <th style="border:1px solid #ddd; color:#ffffff;">Expense Category</th>
                <th style="border:1px solid #ddd; color:#ffffff;">Payment Type</th>
                <th style="border:1px solid #ddd; color:#ffffff;">Expense Date</th>
                <th style="border:1px solid #ddd; color:#ffffff;">Created By</th>
                <th style="border:1px solid #ddd; color:#ffffff;">Created Date</th>
                <th style="border:1px solid #ddd; color:#ffffff;">Note</th>
                <th style="border:1px solid #ddd; color:#ffffff;">Amount</th>
            </tr>
            <?php
                $i=1;
                $total_amount = 0;
                foreach($expenses as $expense) {
                    $paymentTypeLabel = isset($expense['expense_payment_type']) ? $expense['expense_payment_type'] : '';
                    if(strtolower($paymentTypeLabel) === 'others' && !empty($expense['expense_bank_name'])) {
                        $paymentTypeLabel = $expense['expense_bank_name'];
                    }
            ?>
                <tr style="background:<?=($i % 2 == 0) ? '#f9f9f9' : '#ffffff'?>;">
                    <td style="border:1px solid #ddd;"><?=$i?></td>
                    <td style="border:1px solid #ddd;"><?=$expense['expense_referenceno'];?></td>
                    <td style="border:1px solid #ddd; text-align:left;"><?=$expense['expensetypes'];?></td>
                    <td style="border:1px solid #ddd;"><?=$paymentTypeLabel;?></td>
                    <td style="border:1px solid #ddd;"><?=date('d M Y', strtotime($expense['date']));?></td>
                    <td style="border:1px solid #ddd;"><?=$expense['uname'];?></td>
                    <td style="border:1px solid #ddd;"><?=date('d M Y', strtotime($expense['create_date']));?></td>
                    <td style="border:1px solid #ddd; text-align:left;"><?=$expense['note'];?></td>
                    <td style="border:1px solid #ddd; text-align:right;"><?=number_format($expense['amount'],2);?></td>
                </tr>
            <?php
                    $total_amount += $expense['amount'];
                    $i++;
                }
            ?>
            <tr style="background:#eceff1;">
                <td colspan="8" style="border:1px solid #ddd; text-align:right; font-weight:bold;"><?=$this->lang->line('productpurchasereport_grandtotal')?> <?=!empty($siteinfos->currency_code) ? "(".$siteinfos->currency_code.")" : ''?></td>
                <td style="border:1px solid #ddd; text-align:right; font-weight:bold;"><?=number_format($total_amount,2)?></td>
            </tr>
        </table>
    <?php } else { ?>
        <div style="text-align:center; color:red; padding:20px;"><?=$this->lang->line('productpurchasereport_data_not_found')?></div>
    <?php } ?>

    <!-- ===== Footer ===== -->
    <table width="100%" style="border:none; margin-top:10px;">
        <tr>
            <td style="border:none; text-align:center; font-size:9px; color:#555;">
                <?=$siteinfos->footer?> | <?=$this->lang->line('productpurchasereport_hotline')?> : <?=$siteinfos->phone?>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
