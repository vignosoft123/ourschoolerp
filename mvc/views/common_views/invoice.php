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
        margin: 30px;
    }
    .main-wrapper {
        border: 2px solid #1a237e;
        width: 95%;
        margin: auto;
        border-radius: 4px;
        overflow: hidden;
    }

    /* ── Improved Receipt Header ── */
    .receipt-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 10px 16px 8px;
        border-bottom: 3px solid #1a237e;
        background: #fff;
    }
    .receipt-header .rh-logo img {
        width: 72px;
        height: 72px;
        object-fit: contain;
    }
    .receipt-header .rh-info {
        flex: 1;
        text-align: center;
    }
    .rh-school-name {
        font-size: 22px;
        font-weight: 900;
        color: #1a237e;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        line-height: 1.2;
    }
    .rh-school-addr {
        font-size: 11px;
        color: #555;
        margin-top: 3px;
    }
    .rh-divider {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 4px auto;
        max-width: 300px;
    }
    .rh-divider .rh-line { flex: 1; height: 1px; background: linear-gradient(to right, #1a237e, #4caf50, #1a237e); }
    .rh-divider .rh-dm   { color: #4caf50; font-size: 10px; }
    .rh-school-phone { font-size: 11px; color: #1a237e; font-weight: 600; margin-top: 2px; }
    .phone-toggle-bar { text-align: right; padding: 4px 16px; font-size: 12px; color: #555; }
    .phone-toggle-bar label { cursor: pointer; user-select: none; }
    .receipt-title-bar {
        text-align: center;
        background: #1a237e;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 4px;
        padding: 5px 0;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    /* keep old class names working */
    .logo-heading { display: none; }
    .main-heading  { display: none; }
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
        padding: 5px;
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
        /*padding-top: 50px;*/
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
    
    /* .duplicate-print{
      display:  none;
    } */
    /* Add a style to print */
    /* @media print {
        body {
            margin: 0;
            padding: 0;
        }

        .duplicate-print{
      display:  block;
    }

    strong {
    display: none !important;
  }

        .main-wrapper {
        border: 5px rgb(141, 139, 139);
        border-style: groove;
        height: 500px;
        width: 95%;
        margin: auto;
        border-radius: 3%;
    }
        .print-button {
            display: none;
        } */

        .duplicate-print{
      display:  none;
    }
        @media print {
          @page { size: A4 portrait; margin: 5mm; }
          .print-button {
            display: none;
        }
          strong {
    display: none !important;
  }
  body {
            margin: 0 !important;
            padding: 0 !important;
        }
  .main-wrapper {
        border: 2px solid #1a237e !important;
        width: 98%;
        margin: 0 auto !important;
        border-radius: 3%;
        page-break-inside: avoid;
    }
  .duplicate-print.admin-copy {
        margin-top: 6px !important;
    }
  .receipt-header {
        padding: 5px 10px 4px !important;
    }
  .rh-school-name {
        font-size: 18px !important;
    }
  .receipt-title-bar {
        padding: 3px 0 !important;
        font-size: 13px !important;
    }
  .student-details {
        padding: 4px 10px !important;
    }
  .table-start {
        font-size: 11px !important;
    }
  .footer {
        padding: 4px 10px !important;
        font-size: 11px !important;
    }

  .student-copy, .admin-copy {
    display: none; /* Default: Hide all sections */
  }
  .print-student .student-copy {
    display: block !important; /* Show student copy only */
  }
  .print-admin .admin-copy {
    display: block !important; /* Show admin copy only */
  }
  .print-both .student-copy, .print-both .admin-copy {
    display: block !important; /* Show both copies */
  }
}

@media print {
    .no-print {
        display: none !important;
    }
}



        /* Ensure backgrounds visible in print */
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
        .table-start thead { background-color: #4b4646 !important; }
        .table-start thead th { color: #fff !important; }
        .receipt-title-bar { background: #1a237e !important; color: #fff !important; }
        .main-wrapper { border: 2px solid #1a237e !important; }
    
        </style>
  </head>

  <body>

  
<div class="box">
    <div class="box-header no-print">
        <h3 class="box-title"><i class="fa fa-balance-scale"></i> <?=$this->lang->line('panel_title')?></h3>
        <?php if (!empty($receipt_back_url)): ?>
        <a href="<?=htmlspecialchars($receipt_back_url)?>" class="btn btn-default btn-sm" style="margin-bottom:6px;">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <?php endif; ?>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><a href="<?=base_url("global_payment")?>"><?=$this->lang->line('menu_global_payment')?></a></li>
        </ol>
    </div><!-- /.box-header -->
  </div>

    <!-- Phone number toggle (screen only) -->
    <div class="no-print phone-toggle-bar">
        <label>
            <input type="checkbox" id="show_phone_chk" checked onchange="toggleReceiptPhone(this.checked)">
            &nbsp;Show Phone Number on Receipt
        </label>
    </div>

    <div class="main-wrapper student-copy">
    <div class="center scopy" style="text-align:center; font-size:11px; color:#888; padding:2px 0;"></div>

      <!-- Receipt Header -->
      <div class="receipt-header">
        <div class="rh-logo">
          <img src="<?= base_url('uploads/images/' . $siteinfos->photo) ?>" alt="Logo">
        </div>
        <div class="rh-info">
          <div class="rh-school-name"><?= htmlspecialchars($siteinfos->sname) ?></div>
          <div class="rh-school-addr">
            <?= htmlspecialchars($siteinfos->address) ?>
            <?php if (!empty($siteinfos->village_name)): ?>
                , <?= htmlspecialchars($siteinfos->village_name) ?>
            <?php endif; ?>
          </div>
          <div class="rh-school-phone receipt-phone">
            <?php if (!empty($siteinfos->phone)): ?>
                &#128222; <?= htmlspecialchars($siteinfos->phone) ?>
            <?php endif; ?>
          </div>
          <div class="rh-divider"><span class="rh-line"></span><span class="rh-dm">&#9670;</span><span class="rh-line"></span></div>
        </div>
      </div>
      <div class="receipt-title-bar">FEE RECEIPT</div>

      <div class="student-details">
        <div>
          <table>
            <tbody>
              <tr> <td>Admission No</td> <td>:  <?=$single_student->srregisterNO?></td> </tr>
              <tr> <td>Student Name</td> <td>:  <?=customCompute($single_student) ? $single_student->srname : ''?></td> </tr>
              <tr> <td>Father Name</td> <td>: <?=$single_student->father_name?></td> </tr>
              <?php if($is_phone_display){?>
              <tr> <td>Mobile Number</td> <td>: <?=$single_student->phone?></td> </tr>
              <?php }else{?>
              <tr> <td>Mother Name</td> <td>: <?=$single_student->mother_name?></td> </tr>

                <?php }?>
            </tbody>
          </table>
        </div>
        <table>
          <tbody>
            <tr> <td>Manual Receipt</td> <td>:  <?=$globalpayment[0]->invoicedescription?></td> </tr>
            <tr> <td>Receipt No</td> <td>:  INV-G-<?=$globalpayment[0]->globalpaymentID?></td> </tr>
            <tr> <td>Receipt Date</td> <td>: <?=isset($paidpayments['paiddate'][$globalpayment[0]->globalpaymentID]) ? date('d-M-Y', strtotime($paidpayments['paiddate'][$globalpayment[0]->globalpaymentID])) : '' ?></td> </tr>
            <tr> <td>Class Name</td> <td>: <?=customCompute($single_classes) ? $single_classes->classes : ''?></td> </tr>
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
                <?php 
                // echo "<pre>";
                // print_r($paymenteds);
                $count1 = 1;
                foreach ($paymenteds as $paymented) {
                    if ($globalpayment[0]->globalpaymentID == $paymented->globalpaymentID && $paymented->paymentamount > 0) {
                        $paymentedPaidAmount += $paymented->paymentamount;
                        $count1++;?>
                        <tr>
                            <td>
                                <?=isset($feetypes[$invoicefeetype[$paymented->invoiceID]]) ? $feetypes[$invoicefeetype[$paymented->invoiceID]] : ''?>
                                <?php if (!empty($is_prev_year_receipt) && !empty($receipt_year_name)): ?>
                                    <small style="color:#8a4a10; font-size:11px; margin-left:4px;">(<?= htmlspecialchars($receipt_year_name) ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td class="center"><?=$paymented->paymentamount.'.00'?></td>
                        </tr>
                    <?php  $mode = $paymented->paymenttype;
                      if($mode == 'Digita'){
                        $mode = 'Digital';
                      }
                  }
                }  ?>
                     <?php  
                  
                while($count1 <= 5){ ?>
                      <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                      </tr>
                      
                  <?php $count1++; }
                  
                  ?>
            

           
            <tr>
              <td style="display: flex; justify-content: space-between;">
                <div>
                  <span>
                    Mode: <?= $mode?> <br>
                    <span> <?= convert_number($paymentedPaidAmount + $paymentedFineAmount, 2) ?> Rupees Only </span>
                  </span>
                </div>
                <div>
                  <span>Total</span>
                </div>
              </td>
              <td class="center" style="border-top: 2px solid lightgray;text-aligh:center;vertical-align:baseline"><?=$paymentedPaidAmount.'.00'?></td>
            </tr>
          </tbody>
        </table>

        <div class="footer">
          <div style="width: 70%">
            <span>
              Fee once paid will not be refunded. Collect receipt of payment.<br>
              Keep this receipt carefully & Produce on demand
              <span style="margin-left: 80px; font-weight: bolder;font-size:14px">
                DUE AMOUNT : <?= $totalDue ?>
              </span>
            </span>
          </div>
          <div style="width: 30%; text-align: center; vertical-align: middle;bottom: -20px;position:relative">
            <p style = "font-size:18px">Authorised Signatory</p>
          </div>
        </div>
      </div>

     
    </div>

    
    <div class="main-wrapper duplicate-print admin-copy" style="margin-top:20px;">
    <div class="center acopy" style="text-align:center; font-size:11px; color:#888; padding:2px 0;"></div>

        <!-- Receipt Header (Admin Copy) -->
        <div class="receipt-header">
          <div class="rh-logo">
            <img src="<?= base_url('uploads/images/' . $siteinfos->photo) ?>" alt="Logo">
          </div>
          <div class="rh-info">
            <div class="rh-school-name"><?= htmlspecialchars($siteinfos->sname) ?></div>
            <div class="rh-school-addr">
              <?= htmlspecialchars($siteinfos->address) ?>
              <?php if (!empty($siteinfos->village_name)): ?>
                  , <?= htmlspecialchars($siteinfos->village_name) ?>
              <?php endif; ?>
            </div>
            <div class="rh-school-phone receipt-phone">
              <?php if (!empty($siteinfos->phone)): ?>
                  &#128222; <?= htmlspecialchars($siteinfos->phone) ?>
              <?php endif; ?>
            </div>
            <div class="rh-divider"><span class="rh-line"></span><span class="rh-dm">&#9670;</span><span class="rh-line"></span></div>
          </div>
        </div>
        <div class="receipt-title-bar">FEE RECEIPT</div>

        <div class="student-details">
          <div>
            <table>
              <tbody>
                <tr> <td>Admission No</td> <td>:  <?=$single_student->srregisterNO?></td> </tr>
                <tr> <td>Student Name</td> <td>:  <?=customCompute($single_student) ? $single_student->srname : ''?></td> </tr>
                <tr> <td>Father Name</td> <td>: <?=$single_student->father_name?></td> </tr>
                <?php if($is_phone_display){?>
              <tr> <td>Mobile Number</td> <td>: <?=$single_student->phone?></td> </tr>
              <?php }else{?>
              <tr> <td>Mother Name</td> <td>: <?=$single_student->mother_name?></td> </tr>
                <?php }?>
              </tbody>
            </table>
          </div>

          <table>
            <tbody>
              <tr> <td>Receipt No</td> <td>:  INV-G-<?=$globalpayment[0]->globalpaymentID?></td> </tr>
              <tr> <td>Receipt Date</td> <td>: <?=isset($paidpayments['paiddate'][$globalpayment[0]->globalpaymentID]) ? date('d-M-Y', strtotime($paidpayments['paiddate'][$globalpayment[0]->globalpaymentID])) : '' ?></td> </tr>
              <tr> <td>Class Name</td> <td>: <?=customCompute($single_classes) ? $single_classes->classes : ''?></td> </tr>
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
                  <?php $count = 1; foreach ($paymenteds as $paymented) {
                      if ($globalpayment[0]->globalpaymentID == $paymented->globalpaymentID && $paymented->paymentamount > 0) {
                          $paymentedPaidAmount += $paymented->paymentamount; 
                          $count++;
                          ?>
                          <tr>
                              <td>
                                  <?=isset($feetypes[$invoicefeetype[$paymented->invoiceID]]) ? $feetypes[$invoicefeetype[$paymented->invoiceID]] : ''?>
                                  <?php if (!empty($is_prev_year_receipt) && !empty($receipt_year_name)): ?>
                                      <small style="color:#8a4a10; font-size:11px; margin-left:4px;">(<?= htmlspecialchars($receipt_year_name) ?>)</small>
                                  <?php endif; ?>
                              </td>
                              <td class="center"><?=$paymented->paymentamount.'.00'?></td>
                          </tr>
                      <?php  $mode = $paymented->paymenttype;
                       if($mode == 'Digita'){
                        $mode = 'Digital';
                      } }
                  }
                  
                  ?>
                  <?php  
                  
                while($count <= 5){ ?>
                      <tr>
                            <td></td>
                            <td></td>
                      </tr>
                      
                  <?php $count++; }
                  
                  ?>
               
             
              <tr>
                <td style="display: flex; justify-content: space-between;">
                  <div>
                    <span>
                      Mode: <?= $mode?> <br>
                      <span> <?= convert_number($paymentedPaidAmount + $paymentedFineAmount, 2) ?> Rupees Only </span>
                    </span>
                  </div>
                  <div>
                    <span>Total</span>
                  </div>
                </td>
                <td class="center" style="border-top: 2px solid lightgray; text-aligh:center; vertical-align:initial"><?=$paymentedPaidAmount.'.00'?></td>
              </tr>
            </tbody>
          </table>
  
          <div class="footer">
            <div style="width: 70%">
              <span>
                Fee once paid will not be refunded. Collect receipt of payment.<br>
                Keep this receipt carefully & Produce on demand
                <span style="margin-left: 80px; font-weight: bolder;font-size:14px">
                  DUE AMOUNT : <?= $totalDue ?>
                </span>
              </span>
            </div>
            <div style="width: 30%; text-align: center; vertical-align: middle;bottom: -35px;position:relative"; >
              <p style = "font-size:18px">Authorised Signatory</p>
            </div>
          </div>
        </div>
  
       
      </div>


    <!-- Print Buttons -->
<div style="text-align: center; margin-top: 20px;">
  <button class="print-button btn btn-success" onclick="printSection('student')">Print Student Copy</button>
  <button class="print-button btn btn-success" onclick="printSection('admin')">Print Admin Copy</button>
  <button class="print-button btn btn-success" onclick="printSection('both')">Print Both</button>
</div>

    <script>
        // You can call the print function when needed
        // function printPage() {
        //     window.print();
        // }
 
  // Phone number toggle — controls all .receipt-phone elements in both copies
  function toggleReceiptPhone(show) {
      var els = document.querySelectorAll('.receipt-phone');
      for (var i = 0; i < els.length; i++) {
          els[i].style.display = show ? '' : 'none';
      }
  }

  function printSection(type) {
    // Remove any existing print-specific classes
    document.body.classList.remove('print-student', 'print-admin', 'print-both');
    
    // Add the appropriate class based on the button clicked
    if (type === 'student') {
      $('.scopy').html('Student Copy');
      document.body.classList.add('print-student');
    } else if (type === 'admin') {
      $('.acopy').html('Admin Copy');

      document.body.classList.add('print-admin');
    } else if (type === 'both') {
      $('.scopy').html('Student Copy');
      $('.acopy').html('Admin Copy');

      document.body.classList.add('print-both');
    }

    // Trigger the print function
    window.print();
  }
</script>
 
  </body>
</html>
