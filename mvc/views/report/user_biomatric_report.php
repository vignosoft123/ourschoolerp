
<div class="row">
    <div class="col-sm-12" style="margin:10px 0px">
        <?php echo btn_printReport('attendanceoverviewreport', $this->lang->line('report_print'), 'printablediv'); ?>
    </div>
</div>

<div class="box-body">
    <div class="row">
        <div class="col-sm-12">
            <div id="printablediv">

                <!-- Report Heading -->
                <div style="text-align:center; margin-bottom:18px; padding:16px 0 10px; border-bottom:2px solid #f0ad4e;">
                    <i class="fa fa-users" style="font-size:28px; color:#f0ad4e; margin-bottom:6px; display:block;"></i>
                    <h2 style="margin:0 0 4px; font-size:22px; font-weight:700; color:#8a6d3b; letter-spacing:1px;">
                        User Biometric Report
                    </h2>
                    <p style="margin:0; color:#777; font-size:13px;">Punch IN &amp; OUT records from RFID device</p>
                </div>

                <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                    <thead>
                        <tr>
                            <th class="col-sm-1">#</th>
                            <th class="col-sm-2">Name</th>
                            <th class="col-sm-1">Role</th>
                            <th class="col-sm-1">Phone</th>
                            <th class="col-sm-1">RFID</th>
                            <th class="col-sm-1">Date</th>
                            <th class="col-sm-1">Punch IN</th>
                            <th class="col-sm-1">Punch OUT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (customCompute($result)) { $i = 1; foreach ($result as $res) { ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $res['name'] ?></td>
                            <td><?= $res['role'] ?></td>
                            <td><?= $res['phone'] ?></td>
                            <td><?= $res['rfid'] ?></td>
                            <td><?= $res['date'] ?></td>
                            <td><?= $res['min'] ?></td>
                            <td><?= $res['max'] ?></td>
                        </tr>
                        <?php $i++; } } else { ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No biometric records found.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
