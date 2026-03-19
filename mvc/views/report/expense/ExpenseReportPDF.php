<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <?=reportheader($siteinfos, $schoolyearsessionobj, true); ?>
        <h3 style="margin-bottom: 0px;">Expense Report</h3>
        <div>
            <?php if($fromdate != '' && $todate != '' ) { ?>
                <h5 class="pull-left">
                    <?=$this->lang->line('productpurchasereport_fromdate')?> : <?=$fromdate?></p>
                </h5>
                <h5 class="pull-right">
                    <?=$this->lang->line('productpurchasereport_todate')?> : <?=$todate?></p>
                </h5>
            <?php } elseif($reference_no != '0') { ?>
                <h5 class="pull-left">
                    <?php
                        echo $this->lang->line('productpurchasereport_referenceNo')." : ";
                        echo $reference_no;
                    ?>
                </h5>
            <?php } elseif($expensetypesID != 0) { ?>
                <h5 class="pull-left">
                    <?php
                        echo "Category : ";
                        foreach($expensetypes as $expensetype) {
                            if($expensetype->expensetypesID == $expensetypesID) {
                                echo $expensetype->expensetypes;
                            }
                        }
                    ?>
                </h5>
            <?php } ?>
        </div>
        <?php if (customCompute($expenses)) { ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?=$this->lang->line('slno')?></th>
                        <th>Reference No</th>
                        <th>Expense Category</th> 
                        <th>Expense Date</th>
                        <th>Created By</th> 
                        <th>Created Date</th>
                        <th>Note</th>
                        <th>Amount</th> 
                    </tr>
                </thead>
                <tbody>
                <?php
                    $i=1;
                    $total_amount = 0;
                    foreach($expenses as $expense) { ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$expense['expense_referenceno'];?></td>
                            <td><?=$expense['expensetypes'];?></td>
                            <td><?=date('d M Y', strtotime($expense['date']));?></td>
                            <td><?=$expense['uname'];?></td>
                            <td><?=date('d M Y', strtotime($expense['create_date']));?></td>
                            <td><?=$expense['note'];?></td>
                            <td><?=number_format($expense['amount'],2);?></td>
                            <?php $total_amount += $expense['amount']; ?>
                        </tr>
                    <?php $i++; } ?>
                    <tr>
                        <td colspan="7" class="text-right text-bold"><?=$this->lang->line('productpurchasereport_grandtotal')?> <?=!empty($siteinfos->currency_code) ? "(".$siteinfos->currency_code.")" : ''?></td>
                        <td class="text-bold"><?=number_format($total_amount,2)?></td>
                    </tr>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="notfound">
                <?=$this->lang->line('productpurchasereport_data_not_found')?>
            </div>
        <?php } ?>
    <?=reportfooter($siteinfos, $schoolyearsessionobj)?>
</body>
</html>
