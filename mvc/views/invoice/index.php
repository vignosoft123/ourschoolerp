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
                    <ul class="nav nav-tabs">
                            <li class="active"><a href="<?php echo base_url('invoice/index/0');?>" aria-expanded="true">All Classes</a></li>
                            <?php foreach ($all_classes as $key => $section) {
                                echo '<li class=""><a href= "'.base_url("invoice/index/").$section->classesID.'"> '.$section->classes.' </a></li>';
                            } ?>
                        </ul>

                        <?php }?>
                        

                <div id="hide-table">

 



                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
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
                        //    echo "<pre>"; print_r($maininvoices);
                             if(customCompute($maininvoices)) {$i = 1; 
                             foreach($maininvoices as $maininvoice) { ?>
                                <tr>
                                    <td><input type="checkbox" class="invoice-checkbox" value="<?= $maininvoice->maininvoiceID ?>" name="maininvoiceIDs[]"/></td>

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
                                        <?php foreach($grandtotalandpayment['fee_types'][$maininvoice->maininvoiceID] as $key => $feeType){
                                            
                                            ?>

                                           
                                        <?=isset($feetypes[$feeType]) ? $feetypes[$feeType] : ''?>
                                        <?php if(count($grandtotalandpayment['fee_types'][$maininvoice->maininvoiceID])>1){ echo "<br>";}?>

                                        <?php //}
                                    }?>
                                    </td>

                                    <td data-title="<?=$this->lang->line('invoice_total')?>">
                                        <?php if(isset($grandtotalandpayment['totalamount'][$maininvoice->maininvoiceID])) { echo number_format($grandtotalandpayment['totalamount'][$maininvoice->maininvoiceID], 2); } else { echo '0.00'; } ?>
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
                                            if($status == 0) {
                                                $status = $this->lang->line('invoice_notpaid');
                                                $setButton = 'btn-danger';
                                            } elseif(($status == 1) ) {
                                                $status = $this->lang->line('invoice_partially_paid');
                                                $setButton = 'btn-warning';
                                            } elseif($status == 2) {
                                                    if($balance == '' || $balance == 0.00){
                                                        $status = $this->lang->line('invoice_fully_paid');
                                                        $setButton = 'btn-success';
                                                    }else{
                                                        $status = $this->lang->line('invoice_partially_paid');
                                                        $setButton = 'btn-warning';
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
                                                if($maininvoice->maininvoicestatus != 1 && $maininvoice->maininvoicestatus != 2) {
                                                     echo btn_edit('invoice/edit/'.$maininvoice->maininvoiceID.'/'.$uri, $this->lang->line('edit')); 
                                            } ?>
                                            <?php if($maininvoice->maininvoicestatus != 1 && $maininvoice->maininvoicestatus != 2) {
                                                 echo btn_delete('invoice/delete/'.$maininvoice->maininvoiceID.'/'.$uri, $this->lang->line('delete'));
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
                    <table class="table table-bordered">
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
    });
</script>