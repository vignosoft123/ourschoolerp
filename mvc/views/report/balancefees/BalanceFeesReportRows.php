<?php
// Partial view: renders only data rows for Balance Fees report (used for lazy loading)

if (!function_exists('formatIndianCurrency')) {
    function formatIndianCurrency($number, $decimals = 2) {
        $number = round($number, $decimals);
        $parts = explode('.', $number);
        $integer = $parts[0];
        $decimal = isset($parts[1]) ? $parts[1] : '00';

        $decimal = str_pad($decimal, $decimals, '0');

        $integer = (string)$integer;
        if(strlen($integer) > 3) {
            $last3digits = substr($integer, -3);
            $remaining = substr($integer, 0, -3);
            $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
            $integer = $remaining . ',' . $last3digits;
        }

        return $integer . '.' . $decimal;
    }
}

// Prepare Fee Type List (dynamic headers - must match main view)
$allFeeTypes = [];
if(isset($totalPayment_split) && customCompute($totalPayment_split)) {
    foreach ($totalPayment_split as $studentID => $feeTypes) {
        foreach ($feeTypes as $feeType => $values) {
            $allFeeTypes[$feeType] = $feeType;
        }
    }
}
$allFeeTypes = array_values($allFeeTypes);

$totalAmount   = 0;
$totalDiscount = 0;
$totalPayments = 0;
$totalWeaver   = 0;
$totalBalance  = 0;
$total_disc    = 0;
$i = isset($startIndex) ? (int)$startIndex : 0;

if(isset($students) && customCompute($students)) {
    foreach($students as $student) {
        if(!empty($totalAmountAndDiscount[$student->srstudentID]['amount'])) {
            $i++;
            ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= $student->srname ?></td>
                <td><?= $student->father_name ?></td>
                <td><?= $student->srregisterNO ?></td>
                <td><?= $student->village_name ?></td>

                <?php if(isset($classesID) && (int)$classesID == 0) { ?>
                    <td><?= isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : '' ?></td>
                <?php } ?>

                <?php if(isset($sectionID) && (int)$sectionID == 0) { ?>
                    <td><?= isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : '' ?></td>
                <?php } ?>

                <td><?= $student->phone ?></td>

                <?php 
                $all_total = $all_paid = $all_discount = $all_remaining = 0;
                foreach($allFeeTypes as $feeType) {
                    $total = $paid = $discount = $remaining = 0;

                    if (isset($totalPayment_split[$student->srstudentID][$feeType])) {
                        $feeData   = $totalPayment_split[$student->srstudentID][$feeType];
                        $total     = $feeData['total'];
                        $paid      = $feeData['paid'];
                        $discount  = $feeData['discount_plus_weaver'];
                        $remaining = isset($feeData['remaining']) ? max(0, $feeData['remaining']) : 0;
                    }
                    ?>
                    <td>
                        <?php echo formatIndianCurrency($total); 
                            $all_total += $total;
                        ?>
                    </td>
                    <td><?php echo formatIndianCurrency($paid);
                        $all_paid += $paid; ?></td>
                    <td><?= formatIndianCurrency($discount);
                        $all_discount += $discount; ?></td>
                    <td><?= formatIndianCurrency($remaining);
                        $all_remaining += $remaining; ?></td>
                <?php } ?>

                <td>
                    <?php
                        $feeamount = formatIndianCurrency($all_total);
                        echo $feeamount;
                    ?>
                </td>

                <td>
                    <?php
                        $discount_plus_waver = formatIndianCurrency($all_discount);
                        echo $discount_plus_waver;
                    ?>
                </td>

                <td>
                    <?php
                        $paidAmount = formatIndianCurrency($all_paid);
                        echo $paidAmount;
                    ?>
                </td>

                <td>
                    <?php
                        echo $Balance = formatIndianCurrency($all_remaining);

                        $Amount   = $all_total;
                        $Discount = $all_discount;
                        $Payment  = $all_paid;
                        $Weaver   = $all_discount;

                        $totalAmount   += $Amount;
                        $totalDiscount += $Discount;
                        $totalPayments += $Payment;
                        $totalWeaver   += $Weaver;
                        $totalBalance  += $all_remaining;
                        $total_disc    += $all_discount;
                    ?>
                </td>

                <td style="background:#fff3e0; color:#e65100; font-weight:700;">
                    <?php
                        $cfPrev = isset($prevBalanceMap[$student->srstudentID]) ? $prevBalanceMap[$student->srstudentID] : 0;
                        echo $cfPrev > 0 ? formatIndianCurrency($cfPrev) : '';
                    ?>
                </td>

                <td>
                    <?php
                    $fee_paid_balance = $feeamount."^".$paidAmount."^".$Balance;
                    $fee_paid_balance = encrypt_data($fee_paid_balance);
                    ?>
                    <input type="checkbox" st_ids="<?= $student->studentID ?>" st_names="<?= $student->name ?>" mobile_no="<?= $student->phone ?>" balance="<?= $fee_paid_balance ?>" name="send_sms_balance" id="send_sms_balance" class="checkbox">
                </td>
            </tr>
            <?php
        }
    }
}
