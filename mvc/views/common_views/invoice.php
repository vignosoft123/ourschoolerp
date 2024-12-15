<?php
     $total = 0;
     $totalDue = 0;
     if(customCompute($invoices)) { 
         $i=1; 
         foreach ($invoices as $invoice) {
             $total += number_format($invoice->amount, 2, '.', '');

             if($invoice->discount > 0) {
                 // $total = number_format(($total - (($invoice->amount/100)*$invoice->discount)), 2, '.', '');

                 $total = number_format(($total - ( $invoice->discount )), 2, '.', '');

             }

             $payment = 0;
             if(isset($payments[$invoice->invoiceID])) {
                 $payment = number_format($payments[$invoice->invoiceID], 2, '.', '');
             }

             $due = number_format(($invoice->amount - $payment), 2, '.', '');
 
             if($invoice->discount > 0) {
                 $due = number_format(( $due -  $invoice->discount ), 2, '.', '');
             }

             if(isset($weavers[$invoice->invoiceID])) {
                 $due -= number_format($weavers[$invoice->invoiceID], 2, '.', '');
             }

             $totalDue += $due;
            }
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>School</title>
    <style>
  body {
    margin: 40px;
}
.main-wrapper {
    border: 5px rgb(141, 139, 139);
    border-style: groove;
    height: 600px;
    width: 850px;
    margin: auto;
    border-radius: 3%;
}
.logo-heading {
    display: flex;
}
.logo-heading img {
    width: 100px;
}
.main-heading h1 {
    color: #9c9898;
}
.logo {
    width: 100px;
    margin-left: 20px;
    margin-top: 5px;
}
.main-heading {
    margin: auto;
    text-align: center;
}
.table-start table {
    width: 100%;
    font-weight: bolder;
    border-collapse: collapse;
    border-bottom: 2px solid lightgray;
}
.table-start table thead {
    background-color: #4b4646 !important; /* Make sure this color is applied */
    color: #fff;
    width: 100%;
}
.table-start table thead th {
    padding: 12px;
}
.center {
    text-align: center !important;
}
.table-start table tr td, .table-start table tr {
    padding: 10px;
}
.table-start thead th {
    border-left: 0px solid black;
}
.table-start tbody td:nth-child(2) {
    border-left: 2px solid lightgray;
    width: 30%;
}
.footer {
    padding: 10px;
    display: flex;
}
.student-details {
    padding-left: 20px;
    padding-right: 20px;
    display: flex;
    justify-content: space-between;
}
.student-details table {
    font-weight: bold;
}

/* Add a style to print */
/* Default Screen Styles */
.duplicate-print {
    display: none; /* Hide the second copy on the screen */
}

/* Print Styles */
@media print {
    body {
        margin: 0;
        padding: 0;
    }

    .duplicate-print {
        display: block;
    }

    
 
    .main-wrapper {
        width: 100%;
        margin: 0 auto;
        border: 1px solid lightgray; /* Optional border */
    }

    /* Landscape Mode: Side-by-Side Layout */
    @media print and (orientation: landscape) {
        .print-container {
            grid-template-columns: 1fr 1fr; /* Two copies side-by-side */
        }
    }

    /* Hide print button */
    .print-button {
        display: none;
    }

       /* Ensure background is visible in print */
       .table-start thead {
      background-color: #4b4646 !important; /* Force background in print */
    }
    .table-start thead th {
        color: #fff !important;
    }

    
}
 

    </style>
  </head>

  <body>
    <div class="main-wrapper">
      <div class="logo-heading">
        <div class="logo">
        <img src="<?=base_url("uploads/images/$siteinfos->photo")?>" alt="Logo">
        </div>
        <div class="main-heading">
          <h1 style="text-transform: capitalize; margin: 0px;"><?=$siteinfos->sname?></h1>
          <h3 style="margin: 0px;"><?=$siteinfos->address?></h3>
          <h3 style="margin: 0px;">Phone:  <?=$siteinfos->phone?></h3>
        </div>
      </div>
      <div class="student-details">
        <div>
          <table>
            <tbody>
              <tr> <td>Admission No</td> <td>:  <?=$single_student->srregisterNO?></td> </tr>
              <tr> <td>Student Name</td> <td>:  <?=customCompute($single_student) ? $single_student->srname : ''?></td> </tr>
              <tr> <td>Father Name</td> <td>: <?=$single_student->father_name?></td> </tr>
              <tr> <td>Mother Name</td> <td>: <?=$single_student->phone?></td> </tr>
            </tbody>
          </table>
        </div>
        <table>
          <tbody>
            <tr>
              <td style="vertical-align: baseline;">
                <h2 style="margin: 0px; color: #9c9898;">FEE RECEIPT</h2>
              </td>
            </tr>
          </tbody>
        </table>

        <table>
          <tbody>

          

            <tr> <td>Receipt No</td> <td>:  INV-G-<?=$globalpayment[0]->globalpaymentID?></td> </tr>
            <tr> <td>Receipt Date</td> <td>: <?=isset($paidpayments['paiddate'][$globalpayment[0]->globalpaymentID]) ? date('d-M-Y', strtotime($paidpayments['paiddate'][$globalpayment[0]->globalpaymentID])) : '' ?></td> </tr>
            <tr> <td>Father Name</td> <td>: <?=customCompute($single_classes) ? $single_classes->classes : ''?></td> </tr>
            <tr> <td>Section</td> <td>: <?=customCompute($single_section) ? $single_section->section : ''?></td> </tr>
          </tbody>
        </table>
      </div>

      <div class="table-start">
        <table>
          <thead>
            <tr>
            <th><?=$this->lang->line('global_fees_name')?></th>
            <th><?=$this->lang->line('global_amount')?></th>
            </tr>
          </thead>
          <tbody>

                <?php $paymentedPaidAmount = 0; ?>
                <?php $paymented_status = TRUE; $paymented_invoice_total = 0; $paymented_invoice_weaver = 0; $paymented_invoice_fine = 0; 
                    ?>
                <?php foreach ($paymenteds as $paymented) {
                    if ($globalpayment[0]->globalpaymentID == $paymented->globalpaymentID && $paymented->paymentamount > 0) {
                        $paymentedPaidAmount += $paymented->paymentamount; ?>
                        <tr>
                            <td><?=isset($feetypes[$invoicefeetype[$paymented->invoiceID]]) ? $feetypes[$invoicefeetype[$paymented->invoiceID]] : ''?></td>
                            <td class="textright"><?=$paymented->paymentamount?></td>
                        </tr>
                    <?php }
                }  ?>

            

            <tr><td></td><td></td></tr>
            <tr><td></td><td></td></tr>
            <tr><td></td><td></td></tr>
            <tr><td></td><td></td></tr>
            <tr><td></td><td></td></tr>
            <tr><td></td><td></td></tr>
            <tr><td></td><td></td></tr>
            <tr><td></td><td></td></tr>
            <tr><td></td><td></td></tr>
            <tr><td></td><td></td></tr>
            <tr>
              <td style="display: flex; justify-content: space-between;">
                <div>
                  <span>
                    Mode: cash <br>
                    <span> <?= convert_number($paymentedPaidAmount + $paymentedFineAmount, 2) ?> Rupees Only </span>
                  </span>
                </div>
                <div>
                  <span>Total</span>
                </div>
              </td>
              <td class="center" style="border-top: 2px solid lightgray"><?=$paymentedPaidAmount?></td>
            </tr>
          </tbody>
        </table>

        <div class="footer">
          <div style="width: 70%">
            <span>
              Free once paid will be refunded. Collect receipt of payment.<br>
              Keep this receipt carefully & Produce on demand
              <span style="margin-left: 90px; font-weight: bolder;">
                DUE AMOUNT : <?= $totalDue ?>
              </span>
            </span>
          </div>
          <div style="width: 30%; text-align: center; vertical-align: middle;">
            <span>Authorised Signatory</span>
          </div>
        </div>
      </div>

      <!-- Print Button -->
      <div style="text-align: center; margin-top: 20px;">
        <button class="print-button" onclick="window.print()">Print Receipt</button>
      </div>
    </div>

    <div class="main-wrapper duplicate-print">
        <div class="logo-heading">
          <div class="logo">
          <img src="<?=base_url("uploads/images/$siteinfos->photo")?>" alt="Logo">
          </div>
          <div class="main-heading">
            <h1 style="text-transform: capitalize; margin: 0px;"><?=$siteinfos->sname?></h1>
            <h3 style="margin: 0px;"><?=$siteinfos->address?></h3>
            <h3 style="margin: 0px;">Phone:  <?=$siteinfos->phone?></h3>
          </div>
        </div>
        <div class="student-details">
          <div>
            <table>
              <tbody>
                <tr> <td>Admission No</td> <td>:  <?=$single_student->srregisterNO?></td> </tr>
                <tr> <td>Student Name</td> <td>:  <?=customCompute($single_student) ? $single_student->srname : ''?></td> </tr>
                <tr> <td>Father Name</td> <td>: <?=$single_student->father_name?></td> </tr>
                <tr> <td>Mother Name</td> <td>: <?=$single_student->phone?></td> </tr>
              </tbody>
            </table>
          </div>
          <table>
            <tbody>
              <tr>
                <td style="vertical-align: baseline;">
                  <h2 style="margin: 0px; color: #9c9898;">FEE RECEIPT</h2>
                </td>
              </tr>
            </tbody>
          </table>
  
          <table>
            <tbody>
  
            
  
              <tr> <td>Receipt No</td> <td>:  INV-G-<?=$globalpayment[0]->globalpaymentID?></td> </tr>
              <tr> <td>Receipt Date</td> <td>: <?=isset($paidpayments['paiddate'][$globalpayment[0]->globalpaymentID]) ? date('d-M-Y', strtotime($paidpayments['paiddate'][$globalpayment[0]->globalpaymentID])) : '' ?></td> </tr>
              <tr> <td>Father Name</td> <td>: <?=customCompute($single_classes) ? $single_classes->classes : ''?></td> </tr>
              <tr> <td>Section</td> <td>: <?=customCompute($single_section) ? $single_section->section : ''?></td> </tr>
            </tbody>
          </table>
        </div>
  
        <div class="table-start">
          <table>
            <thead>
              <tr>
              <th><?=$this->lang->line('global_fees_name')?></th>
              <th><?=$this->lang->line('global_amount')?></th>
              </tr>
            </thead>
            <tbody>
  
                  <?php $paymentedPaidAmount = 0; ?>
                  <?php $paymented_status = TRUE; $paymented_invoice_total = 0; $paymented_invoice_weaver = 0; $paymented_invoice_fine = 0; 
                      ?>
                  <?php foreach ($paymenteds as $paymented) {
                      if ($globalpayment[0]->globalpaymentID == $paymented->globalpaymentID && $paymented->paymentamount > 0) {
                          $paymentedPaidAmount += $paymented->paymentamount; ?>
                          <tr>
                              <td><?=isset($feetypes[$invoicefeetype[$paymented->invoiceID]]) ? $feetypes[$invoicefeetype[$paymented->invoiceID]] : ''?></td>
                              <td class="textright"><?=$paymented->paymentamount?></td>
                          </tr>
                      <?php }
                  }  ?>
  
              
  
              <tr><td></td><td></td></tr>
              <tr><td></td><td></td></tr>
              <tr><td></td><td></td></tr>
              <tr><td></td><td></td></tr>
              <tr><td></td><td></td></tr>
              <tr><td></td><td></td></tr>
              <tr><td></td><td></td></tr>
              <tr><td></td><td></td></tr>
              <tr><td></td><td></td></tr>
              <tr><td></td><td></td></tr>
              <tr>
                <td style="display: flex; justify-content: space-between;">
                  <div>
                    <span>
                      Mode: cash <br>
                      <span> <?= convert_number($paymentedPaidAmount + $paymentedFineAmount, 2) ?> Rupees Only </span>
                    </span>
                  </div>
                  <div>
                    <span>Total</span>
                  </div>
                </td>
                <td class="center" style="border-top: 2px solid lightgray"><?=$paymentedPaidAmount?></td>
              </tr>
            </tbody>
          </table>
  
          <div class="footer">
            <div style="width: 70%">
              <span>
                Free once paid will be refunded. Collect receipt of payment.<br>
                Keep this receipt carefully & Produce on demand
                <span style="margin-left: 90px; font-weight: bolder;">
                  DUE AMOUNT : <?= $totalDue ?>
                </span>
              </span>
            </div>
            <div style="width: 30%; text-align: center; vertical-align: middle;">
              <span>Authorised Signatory</span>
            </div>
          </div>
        </div>
  
        <!-- Print Button -->
        <div style="text-align: center; margin-top: 20px;">
          <button class="print-button" onclick="window.print()">Print Receipt</button>
        </div>
      </div>

    <script>
        // You can call the print function when needed
        function printPage() {
            window.print();
        }
    </script>
  </body>
</html>
