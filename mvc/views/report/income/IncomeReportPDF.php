<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <div class="row">
        <div class="col-sm-12">
            <?=reportheader($siteinfos, $schoolyearsessionobj)?>
        </div>

        <div class="col-sm-12">
            <h4 class="text-center"><?=$this->lang->line('panel_title')?></h4>
            <?php if($fromdate != '' && $todate != '' ) { ?>
                <h5 class="pull-left">
                    <?=$this->lang->line('incomereport_fromdate')?> : <?=date('d M Y',strtotime($fromdate))?>
                </h5>
                <h5 class="pull-right">
                    <?=$this->lang->line('incomereport_todate')?> : <?=date('d M Y',strtotime($todate))?>
                </h5>
            <?php } ?>
        </div>

        <div class="col-sm-12" style="margin-top:5px">
            <?php if (customCompute($incomes)) { ?>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><?=$this->lang->line('incomereport_slno')?></th>
                            <th><?=$this->lang->line('incomereport_name')?></th>
                            <th>Category</th>
                            <th><?=$this->lang->line('incomereport_date')?></th>
                            <th><?=$this->lang->line('incomereport_user')?></th> 
                            <th><?=$this->lang->line('incomereport_note')?></th>
                            <th><?=$this->lang->line('incomereport_amount')?></th> 
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $i=1;
                        $total_amount = 0;
                        foreach($incomes as $income) { ?>
                            <tr>
                                <td><?=$i?></td>
                                <td><?=$income['name'];?></td>
                                <td><?=$income['category_name'];?></td>
                                <td><?=date('d M Y', strtotime($income['date']));?></td>
                                <td><?=$income['uname'];?></td>
                                <td><?=$income['note'];?></td>
                                <td><?=number_format($income['amount'],2);?></td>
                                <?php 
                                    $total_amount += $income['amount'];
                                ?>
                            </tr>
                        <?php $i++; } ?>
                        <tr>
                            <td colspan="6" style="text-align: right; font-weight: bold;"><?=$this->lang->line('incomereport_grand_total')?> <?=!empty($siteinfos->currency_code) ? "(".$siteinfos->currency_code.")" : ''?></td>
                            <td style="font-weight: bold;"><?=number_format($total_amount,2)?></td>
                        </tr>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="text-center">
                    <p><b><?=$this->lang->line('incomereport_data_not_found')?></b></p>
                </div>
            <?php } ?>
        </div>
        <div class="col-sm-12 text-center footerAll">
            <?=reportfooter($siteinfos, $schoolyearsessionobj)?>
        </div>
    </div>
</body>
</html>
