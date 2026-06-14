<style>
.table-sticky {
    border-collapse: collapse;
    width: 100%;
}

.table-sticky tbody {
    display: block;
    max-height: 400px; /* adjust as needed */
    overflow-y: auto;
}

.table-sticky thead,
.table-sticky tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed; /* keeps column widths aligned */
}


/* Table head */
#example11 thead th {
    background: #007bff;       /* Primary blue background */
    color: #fff;               /* White text */
    text-align: center;        /* Center align text */
    font-weight: 600;
    vertical-align: middle;
    padding: 10px;
}

/* Table rows */
#example11 tbody tr:nth-child(even) {
    background: #f9f9f9;       /* Light gray stripe */
}

#example11 tbody tr:hover {
    background: #e9f3ff;       /* Light blue hover effect */
}

/* Table borders */
#example11, 
#example11 th, 
#example11 td {
    border: 1px solid #dee2e6;
}

/* Checkbox alignment */
#example11 th input[type="checkbox"],
#example11 td input[type="checkbox"] {
    margin: 0 auto;
    display: block;
}

/* Action buttons (if any) */
#example11 td .btn {
    padding: 4px 8px;
    font-size: 0.85rem;
    border-radius: 5px;
}


    </style>

<?php 
    $uri = $this->uri->segment(3);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-invoice"></i> <?=$this->lang->line('panel_title')?></h3>

       
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_invoice')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

            
                <?php 
                // echo $this->session->userdata('usertypeID');die;
                if($this->session->userdata('usertypeID') == 3){?>
                    <h5 class="page-header pull-right">
                            <a class="btn btn-info" href="<?php echo base_url('Global_payment/index/').$classesID.'/'.$studentID ?>">
                                <i class="fa fa-balance-scale"></i> 
                                Global Payment
                            </a>
                        </h5>
                 <?php  }

                if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1) || ($this->session->userdata('usertypeID') == 5)) { ?>
                    <?php if(permissionChecker('invoice_add')) { ?>
                        <h5 class="page-header">
                            <a href="<?php echo base_url('invoice/add') ?>" class="btn btn-primary">
                                <i class="fa fa-plus"></i>
                                <?=$this->lang->line('add_title')?>
                            </a>

                            <a href="<?php echo base_url('invoicereport') ?>" target="_blank" class="btn btn-success">
                                <i class="fa fa-bar-chart"></i> Invoice Report
                            </a>

                                            <button type="button" id="openFeeFormBtn" class="btn btn-primary mb-3">Bulk Edit Amounts</button>

                        </h5>
                    <?php } ?>
                <?php } ?>



                
                <div class="">
<!-- Fee Update Form - Initially Hidden -->
<form id="updateAmountForm" method="post" style="display: none;" action="<?= base_url('Invoice/update_bulk_amount') ?>">
    <div class="row filter-box1">
        <div class="col-md-6">
            <div class="form-group">
                <label>Fee Type <span class="text-red">*</span></label>
                <select name="feetypeID" id="feetypeID" class="form-control select2" required>
                    <option value="">Select Fee Type</option>
                    <?php foreach($allfee as $feetype): ?>
                        <option value="<?= $feetype->feetypesID ?>"><?= $feetype->feetypes ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Amount <span class="text-red">*</span></label>
                <input type="text" name="amount" class="form-control" required/>
            </div>
        </div>
    </div>

    <!-- Hidden input for selected IDs (populated by JS) -->
    <input type="hidden" name="selectedIDs" id="selectedIDs"/>

    <button type="submit" class="btn btn-success">Update Amount</button>
</form>
                    </div>



                <?php 

                    $categories = array();
                    foreach ($maininvoices as $source) {
                        array_push($categories, $source->maininvoiceclassesID);
                    }
                    $categories = (array_unique($categories));

            //    $maininvoiceclassesID =  array_unique(("maininvoiceclassesID", $maininvoices));
            //    echo "<pre>";print_r($categories);die;
                ?>

                    <?php if($this->session->userdata('usertypeID') == 1 || $this->session->userdata('usertypeID') == 5){?>
                    <style>
                        .inv-class-tab { display:inline-block; padding:5px 15px; border-radius:20px; border:1.5px solid #3c8dbc; color:#3c8dbc; background:#fff; font-size:12px; font-weight:600; text-decoration:none; white-space:nowrap; transition:background .15s,color .15s; }
                        .inv-class-tab:hover { background:#d6eaf8; color:#2876a5; text-decoration:none; }
                        .inv-class-tab.inv-active { background:#3c8dbc; color:#fff; border-color:#3c8dbc; }
                    </style>
                    <?php $curClassID = (int)(isset($maininvoiceclassesID) ? $maininvoiceclassesID : 0); ?>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;">
                        <a href="<?= base_url('invoice/index/0') ?>" class="inv-class-tab <?= $curClassID === 0 ? 'inv-active' : '' ?>">All Classes</a>
                        <?php foreach ($all_classes as $section): ?>
                            <a href="<?= base_url('invoice/index/'.$section->classesID) ?>"
                               class="inv-class-tab <?= $curClassID === (int)$section->classesID ? 'inv-active' : '' ?>">
                                <?= $section->classes ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <?php }?>
                        

                <div id="hide-table">

 



                    <table id="example11" class="table table-striped table-bordered table-hover dataTable no-footer table-sticky-no">
                        <thead>
                            <tr>
                                            <th><input type="checkbox" id="selectAll"/></th>

                                <th><?=$this->lang->line('slno')?></th>
                                <th><?=$this->lang->line('invoice_student')?></th>
                                <th><?=$this->lang->line('invoice_classesID')?></th>
                                <th><?=$this->lang->line('invoice_sectionID')?></th>
                                <th>Fee Type</th>
                                <th><?=$this->lang->line('invoice_total')?></th>
                                <th><?=$this->lang->line('invoice_discount')?></th>
                                <th><?=$this->lang->line('invoice_paid')?></th>
                                <!-- <th><?=$this->lang->line('invoice_weaver')?></th> -->
                                <th><?=$this->lang->line('invoice_balance')?></th>
                                <th><?=$this->lang->line('invoice_onlystatus')?></th>
                                <th><?=$this->lang->line('invoice_date')?></th>
                                <?php if(permissionChecker('invoice_view') || permissionChecker('invoice_edit') || permissionChecker('invoice_delete')) { ?>
                                    <th><?=$this->lang->line('action')?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                        //    echo "<pre>"; print_r($maininvoices);die;
                             if(customCompute($maininvoices)) {$i = 1; 
                             foreach($maininvoices as $maininvoice) { ?>
                                <tr>
                                    <td><input type="checkbox" class="invoice-checkbox" value="<?= $maininvoice->maininvoiceID ?>" name="maininvoiceIDs[]"/>   </td>

                                    <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('invoice_student')?>">
                                        <?php echo $maininvoice->srname; ?><br/>
                                        
                                        <?php echo "Father: " .$maininvoice->parent_name; ?>
                                    </td>

                                    <td data-title="<?=$this->lang->line('invoice_classesID')?>">
                                        <?php echo $maininvoice->srclasses; ?>
                                    </td>

                                    <td data-title="<?=$this->lang->line('invoice_sectionID')?>">
                                        <?php echo $maininvoice->srsection; ?>
                                    </td>

                                    <td data-title="Fee Type">

                                            <?php 
                                               /* if($maininvoice->feetypes){
                                                    echo $maininvoice->feetypes; 

                                                }else{
                                                //   echo  $sql= "select feetype from invoice where maininvoiceID=".$maininvoice->maininvoiceID. " and studentID=".$maininvoice->srstudentID;
                                                //    $result =  $this->db->query($sql)->row_array(); 
                                                    // echo $result['feetype'];
                                                }*/
                                            ?>

                                        <?php
                                        if (!empty($grandtotalandpayment['fee_types'][$maininvoice->maininvoiceID])) {
                                            foreach($grandtotalandpayment['fee_types'][$maininvoice->maininvoiceID] as $key => $feeType) { ?>
                                        <?= isset($feetypes[$feeType]) ? $feetypes[$feeType] : '' ?>
                                        <?php if(count($grandtotalandpayment['fee_types'][$maininvoice->maininvoiceID]) > 1){ echo "<br>"; } ?>
                                        <?php }
                                        } ?>
                                    </td>

                                    <td data-title="<?=$this->lang->line('invoice_total')?>">
                                        <?php if(isset($grandtotalandpayment['totalamount'][$maininvoice->maininvoiceID])) { echo $totl = number_format($grandtotalandpayment['totalamount'][$maininvoice->maininvoiceID], 2); } else { echo '0.00'; } ?>
                                    </td>

                                    <td data-title="<?=$this->lang->line('invoice_discount')?>">
                                        <?php if(isset($grandtotalandpayment['totaldiscount'][$maininvoice->maininvoiceID])) {
                                             
                                             $discount_plus_weaver = $grandtotalandpayment['totaldiscount'][$maininvoice->maininvoiceID] + $grandtotalandpayment['totalweaver'][$maininvoice->maininvoiceID] ;
                                             echo number_format($discount_plus_weaver, 2); 
                                             

                                            //  echo number_format($grandtotalandpayment['totaldiscount'][$maininvoice->maininvoiceID], 2); 

                                            } else { echo '0.00';
                                             
                                             } ?>

                                          <!-- <a  data-toggle="modal" data-target="#change_discount<?= $maininvoice->maininvoiceID?>" > <i title="Change discount amount" class="fa fa-edit"></i>  </a> -->

                                          <a onclick="checkDiscountValidation(<?= $maininvoice->maininvoiceID?>)"> <i title="Change discount amount" class="fa fa-edit"></i>  </a>

                                    </td>

                                    <td data-title="<?=$this->lang->line('invoice_paid')?>">
                                        <?php if(isset($grandtotalandpayment['totalpayment'][$maininvoice->maininvoiceID])) { echo number_format($grandtotalandpayment['totalpayment'][$maininvoice->maininvoiceID], 2); } else { echo '0.00'; } ?>
                                    </td>

                                    <!-- <td data-title="<?=$this->lang->line('invoice_weaver')?>">
                                        <?php if(isset($grandtotalandpayment['totalweaver'][$maininvoice->maininvoiceID])) { echo number_format($grandtotalandpayment['totalweaver'][$maininvoice->maininvoiceID], 2); } else { echo '0.00'; } ?>
                                    </td> -->

                                    <td data-title="<?=$this->lang->line('invoice_balance')?>">
                                        <?php 
                                        $balance = "";
                                            if(isset($grandtotalandpayment['grandtotal'][$maininvoice->maininvoiceID])) { 
                                                if(isset($grandtotalandpayment['totalpayment'][$maininvoice->maininvoiceID])) { 
                                                    if(isset($grandtotalandpayment['totalweaver'][$maininvoice->maininvoiceID])) { 
                                                        $paymentandweaver = ($grandtotalandpayment['totalpayment'][$maininvoice->maininvoiceID] + $grandtotalandpayment['totalweaver'][$maininvoice->maininvoiceID]);
                                                        echo $balance =  number_format(((float)$grandtotalandpayment['grandtotal'][$maininvoice->maininvoiceID] - (float)$paymentandweaver), 2);
                                                    } else {
                                                        echo $balance =  number_format(((float)$grandtotalandpayment['grandtotal'][$maininvoice->maininvoiceID] - (float)$grandtotalandpayment['totalpayment'][$maininvoice->maininvoiceID]), 2);
                                                    }
                                                } else { 
                                                    if(isset($grandtotalandpayment['totalweaver'][$maininvoice->maininvoiceID])) {
                                                       echo $balance =  number_format(((float)$grandtotalandpayment['grandtotal'][$maininvoice->maininvoiceID] - (float)$grandtotalandpayment['totalweaver'][$maininvoice->maininvoiceID]), 2);
                                                    } else {
                                                       echo $balance =  number_format((float)$grandtotalandpayment['grandtotal'][$maininvoice->maininvoiceID], 2);
                                                    }
                                                }
                                                //$maininvoice->maininvoicestatus = 10;   //assign 10 ,because of some time main invoice status applied as 2
                                                //$balance = $grandtotalandpayment['grandtotal'];
                                            } else { 
                                                echo '0.00'; 
                                                //  $balance = '0.00';
                                            } 
                                        ?>
                                        <input type="hidden" id="invoice_<?= $maininvoice->maininvoiceID?>" value="<?= $balance?>">
                                    </td>

                                    <td data-title="<?=$this->lang->line('invoice_onlystatus')?>">
                                        <?php 
                                         
                                            $status = $maininvoice->maininvoicestatus;
                                            $setButton = ''; 
                                            $partial =  0;
                                            if($status == 0) {
                                                // $status = $this->lang->line('invoice_notpaid');
                                                // $setButton = 'btn-danger';

                                                //  if($balance != '' || $balance != 0.00){
                                                 if( $totl != $balance ){
                                                        $status = $this->lang->line('invoice_partially_paid');
                                                        $setButton = 'btn-warning';
                                                        $partial =  1;
                                                    }else{
                                                       $status = $this->lang->line('invoice_notpaid');
                                                        $setButton = 'btn-danger';
                                                    }

                                            } elseif(($status == 1) ) {
                                                $status = $this->lang->line('invoice_partially_paid');
                                                $setButton = 'btn-warning';
                                                $partial =  1;
                                            } elseif($status == 2) {
                                                    if($balance == '' || $balance == 0.00){
                                                        $status = $this->lang->line('invoice_fully_paid');
                                                        $setButton = 'btn-success';
                                                    }else{
                                                       $status = $this->lang->line('invoice_partially_paid');
                                                        $setButton = 'btn-warning';
                                                        $partial =  1;
                                                    }
                                               
                                            }

                                            echo "<button class='btn ".$setButton." btn-xs'>".$status."</button>";
                                        ?>
                                    </td>

                                    <td data-title="<?=$this->lang->line('invoice_date')?>">
                                        <?php echo date("d M Y", strtotime($maininvoice->maininvoicedate)) ; ?>
                                    </td>

                                    <?php if(permissionChecker('invoice_view') || permissionChecker('invoice_edit') || permissionChecker('invoice_delete')) { ?>
                                    <td data-title="<?=$this->lang->line('action')?>">
                                        <?php echo btn_view('invoice/view/'.$maininvoice->maininvoiceID.'/'.$maininvoice->srstudentID, $this->lang->line('view')) ?>
                                        <?php if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1) || ($this->session->userdata('usertypeID') == 5)) { ?>
                                            <?php 
                                                if($maininvoice->maininvoicestatus != 1 && $maininvoice->maininvoicestatus != 2 && $partial != 1) {
                                                     echo btn_edit('invoice/edit/'.$maininvoice->maininvoiceID.'/'.$uri, $this->lang->line('edit')); 

                                                      echo btn_delete('invoice/delete/'.$maininvoice->maininvoiceID.'/'.$uri, $this->lang->line('delete'));
                                                      
                                            } ?>
                                            <?php if($maininvoice->maininvoicestatus != 1 && $maininvoice->maininvoicestatus != 2) {
                                                //  echo btn_delete('invoice/delete/'.$maininvoice->maininvoiceID.'/'.$uri, $this->lang->line('delete'));
                                            } ?>
                                        <?php } ?>
                                        <?php if(permissionChecker('invoice_view')) {
                                            //  if($maininvoice->maininvoicestatus != 2) { 
                                                if($balance != '' || $balance != 0.00){
                                               // echo btn_invoice('invoice/payment/'.$maininvoice->maininvoiceID, $this->lang->line('payment')); 
                                                }
                                                // }
                                            } ?>
                                        <?php 
                                            if(permissionChecker('invoice_view')) { 
                                                echo '<a href="#paymentlist" id="'.$maininvoice->maininvoiceID.'" class="btn btn-info btn-xs mrg getpaymentinfobtn" rel="tooltip" data-toggle="modal"><i class="fa fa-list-ul" data-toggle="tooltip" data-placement="top" data-original-title="'.$this->lang->line('invoice_view_payments').'"></i></a>';
                                            }
                                        ?>

                                    </td>



                                                                            
                                        <!-- change discount   Modal  start Structure -->
                                        <div class="modal fade" id="change_discount<?= $maininvoice->maininvoiceID?>" tabindex="-1" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="fileUploadModalLabel">Change Amount</h5>
                                                            <button style="margin-left: 98% !important;" type="button" class="btn-close" data-dismiss="modal" aria-label="Close"> X </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Form for File Upload -->
                                                            <form id="" enctype="multipart/form-data" method="post" action="<?php echo base_url('Invoice/change_discount')?>">
                                                                <div class="mb-3">
                                                                    <label for="formFile" class="form-label">Discount Amount</label>
                                                                    <input  class="form-control" type="hidden" id="invoice_id" name="invoice_id" value="<?= $maininvoice->maininvoiceID?>">
                                                                    <input  class="form-control" type="hidden" id="srstudentID" name="srstudentID" value="<?= $maininvoice->srstudentID?>">

                                                                    <input type="hidden" id="balance_<?= $maininvoice->maininvoiceID ?>" value="">
                                                                    <input class="form-control" type="text" id="disc_amount" name="disc_amount" value="" 
                                                                        oninput="validate_disc(<?= $maininvoice->maininvoiceID ?>, this.value)">
                                                                    <span class="error" style="color: red;"></span>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <button type="submit" class="btn btn-primary submit_button" id="submit_button" >Submit</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                        <!-- change discount modal end -->




                                    <?php } ?>
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>

                    <?php if($this->session->userdata('usertypeID') == 1 || $this->session->userdata('usertypeID') == 5) { ?>
                        <?php if(isset($total_records) && isset($loaded_records) && $loaded_records < $total_records) { ?>
                            <div class="text-center" style="margin-top: 20px;">
                                <button id="load-more-btn" class="btn btn-primary" data-offset="<?= $loaded_records ?>" data-classid="<?= isset($maininvoiceclassesID) ? $maininvoiceclassesID : '' ?>">
                                    <i class="fa fa-refresh"></i> Load More (Showing <?= $loaded_records ?> of <?= $total_records ?>)
                                </button>
                                <button id="load-all-btn" class="btn btn-success" data-offset="<?= $loaded_records ?>" data-classid="<?= isset($maininvoiceclassesID) ? $maininvoiceclassesID : '' ?>" style="margin-left: 10px;">
                                    <i class="fa fa-download"></i> Load All Invoices
                                </button>
                                <div id="loading-spinner" style="display: none; margin-top: 10px;">
                                    <i class="fa fa-spinner fa-spin"></i> Loading...
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentlist">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"><?=$this->lang->line('invoice_view_payments')?></h4>
            </div>
            <div class="modal-body">
                <div id="hide-table">
                    <table class="table table-bordered table-sticky">
                        <thead>
                            <tr>
                                <th><?=$this->lang->line('slno')?></th>
                                <th><?=$this->lang->line('invoice_date')?></th>
                                <th><?=$this->lang->line('invoice_paidby')?></th>
                                <th><?=$this->lang->line('invoice_paymentamount')?></th>
                                <th><?=$this->lang->line('invoice_weaver')?></th>
                                <th><?=$this->lang->line('invoice_fine')?></th>
                                <th><?=$this->lang->line('action')?></th>
                            </tr>
                        </thead>
                        <tbody id="payment-list-body">
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?=$this->lang->line('close')?></button>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">
    $('.getpaymentinfobtn').click(function() {
        var maininvoiceID =  $(this).attr('id');
        if(maininvoiceID > 0) {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('invoice/paymentlist')?>",
                data: {'maininvoiceID' : maininvoiceID},
                dataType: "html",
                success: function(data) {
                    $('#payment-list-body').children().remove();
                    $('#payment-list-body').append(data);
                }
            });
        }   
    });

    function checkDiscountValidation(invoice_id){ 
        var balance = $("#invoice_"+invoice_id).val();  
        $("#balance_"+invoice_id).val(balance);       
        $("#change_discount"+invoice_id).modal('show');
    }

    function validate_disc(invoice_id, discAmount) { 
        // var balance = parseFloat($('#balance_' + invoice_id).val());

        var balance = parseFloat($('#balance_' + invoice_id).val().replace(/,/g, ''));

        var discAmount = parseFloat(discAmount); 
        var errorMessage = '';
        var isError = false;
        
        if (isNaN(discAmount)) {
            errorMessage = 'Please enter a valid number.';
            isError = true;
        } else if (discAmount > balance) {
            errorMessage = 'Discount amount cannot exceed the balance.';
            isError = true;
        }

        $('.error').text(errorMessage);

        // Disable or enable the button based on the error
        $('.submit_button').prop('disabled', isError);
    }


</script>

<!-- ✅ jQuery -->
<script>
    $(document).ready(function() {
        // Show form when Open button is clicked
        $('#openFeeFormBtn').click(function() {
            $('#updateAmountForm').slideDown();
        });

        // Select all checkboxes
        $('#selectAll').click(function() {
            $('.invoice-checkbox').prop('checked', this.checked);
        });

        // On submit, validate checkboxes
        $('#updateAmountForm').submit(function(e) {
            const selected = $('.invoice-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selected.length === 0) {
                alert("⚠️ Please select at least 1 checkbox.");
                e.preventDefault();
                return false;
            }

            $('#selectedIDs').val(selected.join(','));
        });

        // Lazy loading - Load More button
        $('#load-more-btn').click(function() {
            var $btn = $(this);
            var offset = parseInt($btn.data('offset'));
            var classID = $btn.data('classid');
            
            // Show loading spinner
            $('#loading-spinner').show();
            $btn.prop('disabled', true);
            $('#load-all-btn').prop('disabled', true);
            
            $.ajax({
                url: '<?= base_url('invoice/load_more_invoices') ?>',
                type: 'POST',
                data: {
                    offset: offset,
                    maininvoiceclassesID: classID
                },
                dataType: 'json',
                success: function(response) {
                    if(response.html && response.count > 0) {
                        // Add rows through DataTables API so search/filter index is updated
                        var $newRows = $(response.html);
                        $('#example11').DataTable().rows.add($newRows.get()).draw(false);

                        // Update offset
                        var newOffset = offset + response.count;
                        $btn.data('offset', newOffset);

                        // Update button text
                        var totalRecords = parseInt($btn.text().match(/of (\d+)/)[1]);
                        var loadedRecords = newOffset;

                        if(loadedRecords >= totalRecords) {
                            $btn.hide();
                            $('#load-all-btn').hide();
                        } else {
                            $btn.html('<i class="fa fa-refresh"></i> Load More (Showing ' + loadedRecords + ' of ' + totalRecords + ')');
                            $('#load-all-btn').data('offset', newOffset);
                        }

                        // Re-attach event listeners for new rows
                        attachEventListeners();
                    } else {
                        $btn.hide();
                        alert('No more records to load.');
                    }
                },
                error: function() {
                    alert('Error loading more records. Please try again.');
                },
                complete: function() {
                    $('#loading-spinner').hide();
                    $btn.prop('disabled', false);
                    $('#load-all-btn').prop('disabled', false);
                }
            });
        });

        // Load All button - loads all remaining invoices at once
        $('#load-all-btn').click(function() {
            var $btn = $(this);
            var $loadMoreBtn = $('#load-more-btn');
            var offset = parseInt($btn.data('offset'));
            var classID = $btn.data('classid');
            
            if(!confirm('This will load all remaining invoices. Continue?')) {
                return;
            }
            
            // Show loading spinner
            $('#loading-spinner').show();
            $btn.prop('disabled', true);
            $loadMoreBtn.prop('disabled', true);
            
            $.ajax({
                url: '<?= base_url('invoice/load_all_invoices') ?>',
                type: 'POST',
                data: {
                    offset: offset,
                    maininvoiceclassesID: classID
                },
                dataType: 'json',
                success: function(response) {
                    if(response.html && response.count > 0) {
                        // Add rows through DataTables API so search/filter index is updated
                        var $newRows = $(response.html);
                        $('#example11').DataTable().rows.add($newRows.get()).draw(false);

                        // Hide both buttons as all records are now loaded
                        $btn.hide();
                        $loadMoreBtn.hide();

                        // Re-attach event listeners for new rows
                        attachEventListeners();

                        alert('Successfully loaded ' + response.count + ' more records. All invoices are now displayed.');
                    } else {
                        $btn.hide();
                        $loadMoreBtn.hide();
                        alert('No more records to load.');
                    }
                },
                error: function() {
                    alert('Error loading all records. Please try again.');
                    $btn.prop('disabled', false);
                    $loadMoreBtn.prop('disabled', false);
                },
                complete: function() {
                    $('#loading-spinner').hide();
                }
            });
        });

        // Function to attach event listeners to dynamically loaded content
        function attachEventListeners() {
            // Re-attach payment info button click handler
            $('.getpaymentinfobtn').off('click').on('click', function() {
                var maininvoiceID = $(this).attr('id');
                if(maininvoiceID > 0) {
                    $.ajax({
                        type: 'POST',
                        url: "<?=base_url('invoice/paymentlist')?>",
                        data: {'maininvoiceID': maininvoiceID},
                        dataType: "html",
                        success: function(data) {
                            $('#payment-list-body').children().remove();
                            $('#payment-list-body').append(data);
                        }
                    });
                }
            });
        }
        
        // Initial attachment
        attachEventListeners();
    });
</script>