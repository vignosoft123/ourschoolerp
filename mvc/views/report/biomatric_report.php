
<div class="row">
    <div class="col-sm-12" style="margin:10px 0px">
    <?php 
        echo btn_printReport('attendanceoverviewreport', $this->lang->line('report_print'), 'printablediv');

    ?>
    </div>
</div>


<div class="box-body">
        <div class="row">
            <div class="col-sm-12">

               
                <h5 class="page-header"> 
                </h5>

              

                <div id="printablediv">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-1">Teacher Name</th>
                                <th class="col-sm-1">Designation</th>
                                <th class="col-sm-1">Phone</th>
                                <th class="col-sm-1">RFID</th>
                                <th class="col-sm-1">Date</th>
                                <th class="col-sm-1">Punch IN</th>
                                <th class="col-sm-1">Punch OUT</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($result)) {$i = 1; foreach($result as $res) { ?>
                                <tr>
                                    <!-- <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td> -->
                                    <td><?= $res['name']?></td>
                                    <td><?= $res['designation']?></td>
                                    <td><?= $res['phone']?></td>
                                    <td><?= $res['rfid']?></td>
                                    <td><?= $res['date']?></td>
                                    <td><?= $res['min']?></td>
                                    <td><?= $res['max']?></td>
                                    
                                    <?php } ?>
                                </tr>
                            <?php $i++; } ?>
                        </tbody>
                    </table>
                </div>

            </div> <!-- col-sm-12 -->
        </div><!-- row -->
    </div><!-- Body -->