<?php
function numberToWords($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Forty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'numberToWords only accepts values between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . numberToWords(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . numberToWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= numberToWords($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}
?>

<div id="printablediv">
    <div class="row">
        <div class="col-sm-12">
            <div class="no-print" style="text-align: right; margin-bottom: 20px;">
                <button class="btn btn-primary" onclick="window.print()"><i class="fa fa-print"></i> Print Slips</button>
            </div>
            
            <style>
                .fee-slip-container {
                    width: 100%;
                    max-width: 800px;
                    margin: 0 auto 30px auto;
                    border: 2px solid #000;
                    padding: 15px;
                    font-family: Arial, sans-serif;
                    box-sizing: border-box;
                    display: flex;
                    flex-direction: column;
                }
                .fee-slip-header {
                    text-align: center;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 10px;
                    margin-bottom: 15px;
                }
                .fee-slip-header h2 {
                    margin: 0;
                    text-transform: uppercase;
                    font-weight: bold;
                    font-size: 24px;
                }
                .fee-slip-header p {
                    margin: 2px 0;
                    font-size: 14px;
                }
                .slip-title-box {
                    background-color: #e0e0e0;
                    border: 1px solid #000;
                    padding: 5px;
                    margin: 10px 0;
                    text-align: center;
                    font-weight: bold;
                    font-style: italic;
                }
                .slip-body {
                    line-height: 1.8;
                    font-size: 16px;
                    flex-grow: 1;
                }
                .amount-bold {
                    font-weight: bold;
                    text-decoration: underline;
                }
                .fee-slip-footer {
                    margin-top: auto;
                    display: flex;
                    justify-content: space-between;
                    font-weight: bold;
                    padding-top: 20px;
                }
                .dashed-divider {
                    border-top: 2px dashed #000;
                    margin: 40px 0;
                }
                @media print {
                    @page {
                        margin: 5mm;
                        size: A4 portrait;
                    }
                    /* Remove all dashboard UI elements for printing */
                    header, .main-header, .main-sidebar, footer, .main-footer, .breadcrumb, .box-header, .nav-tabs-custom, .no-print, #balanceFeeForm, #classWiseForm, #feeDueSlipForm {
                        display: none !important;
                    }
                    /* Reset layout containers to allow content flow across multiple pages */
                    .wrapper, .content-wrapper, .content, .box, .box-body, #load_balancefeesreport {
                        margin: 0 !important;
                        padding: 0 !important;
                        border: none !important;
                        display: block !important;
                    }
                    .fee-slip-container {
                        height: 90mm !important;
                        margin: 0 auto !important;
                        padding: 10px !important;
                        page-break-inside: avoid;
                        display: block !important;
                        border: 2px solid #000 !important;
                    }
                    .dashed-divider {
                        margin: 2mm 0 !important;
                        border-top: 2px dashed #000 !important;
                    }
                    body {
                        background: #fff;
                        margin: 0;
                        padding: 0;
                    }
                    .fee-slip-header h2 {
                        font-size: 20px !important;
                    }
                    .slip-title-box {
                        margin: 5px 0 !important;
                        padding: 3px !important;
                        background-color: #e0e0e0 !important;
                        -webkit-print-color-adjust: exact;
                    }
                    .slip-body {
                        line-height: 1.4 !important;
                        font-size: 14px !important;
                    }
                    .fee-slip-footer {
                        margin-top: auto !important;
                        display: flex !important;
                    }
                }
            </style>

            <?php if(customCompute($students)) { 
                $i = 0;
                foreach($students as $student) { 
                    $amount = 0;
                    $discount = 0;
                    $payment = 0;
                    $weaver = 0;

                    if(isset($totalAmountAndDiscount[$student->srstudentID]['amount'])) {
                        $amount = $totalAmountAndDiscount[$student->srstudentID]['amount'];
                    }
                    if(isset($totalAmountAndDiscount[$student->srstudentID]['discount'])) {
                        $discount = $totalAmountAndDiscount[$student->srstudentID]['discount'];
                    }
                    if(isset($totalPayment[$student->srstudentID]['payment'])) {
                        $payment = $totalPayment[$student->srstudentID]['payment'];
                    }
                    if(isset($totalweavar[$student->srstudentID]['weaver'])) {
                        $weaver = $totalweavar[$student->srstudentID]['weaver'];
                    }

                    $balance = ($amount - $discount) - ($payment + $weaver);
                    
                    if($balance <= 0) continue; // Only show if there is a balance
                    
                    $i++;
                    if($i > 1) {
                        echo '<div class="dashed-divider"></div>';
                    }
            ?>
                <div class="fee-slip-container">
                    <div class="fee-slip-header">
                        <h2><?=isset($siteinfos->sname) ? htmlspecialchars($siteinfos->sname) : 'School Name'?></h2>
                        <?php if(isset($siteinfos->affiliation)) { ?>
                            <p>(Recognized by Govt. of AP. Vide Rc. <?=htmlspecialchars($siteinfos->affiliation)?>)</p>
                        <?php } ?>
                        <p><?=isset($siteinfos->address) ? htmlspecialchars($siteinfos->address) : ''?></p>
                        <div class="slip-title-box">
                            Fee Due Slip on Dt. <?=htmlspecialchars($slip_date)?>
                        </div>
                    </div>
                    <div class="slip-body">
                        <div>
                            Dear Parent, &nbsp;&nbsp; <b>Sri <?=isset($parents[$student->parentID]) ? htmlspecialchars($parents[$student->parentID]) : ''?> garu</b>
                        </div>
                        <div>
                            Please pay the Fee Due amount of Rs. 
                            <span class="amount-bold"><?=number_format($balance, 2)?></span> 
                            (<?=numberToWords($balance)?> Rupees only) 
                            of your child &nbsp;&nbsp; <b><?=htmlspecialchars($student->srname)?></b> 
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            Class /Section : &nbsp; <b><?=isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : ''?> /<?=isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : ''?></b>
                        </div>
                        <div>
                            on before due Date. &nbsp; <b><?=htmlspecialchars($due_date)?></b>
                        </div>
                    </div>
                    <div class="fee-slip-footer">
                        <span>Parent Signature</span>
                        <span>Incharge</span>
                    </div>
                </div>
            <?php } ?>
            <?php if($i == 0) { ?>
                <div class="alert alert-info">No students with pending balance found for the selected filters.</div>
            <?php } ?>
            <?php } else { ?>
                <div class="alert alert-info">No students found.</div>
            <?php } ?>
        </div>
    </div>
</div>
