<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Teacher Name</th>
            <th>Designation</th>
            <th>Phone</th>
            <th>RFID</th>
            <th>Date</th>
            <th>First Punch-In</th>
            <th>Default Login Time</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($result)) { foreach($result as $res) { ?>
            <tr>
                <td><?= $res['name'] ?></td>
                <td><?= $res['designation'] ?></td>
                <td><?= $res['phone'] ?></td>
                <td><?= $res['rfid'] ?></td>
                <td><?= $res['date'] ?></td>
                <td><?= $res['first_in'] ?: '-' ?></td>
                <td><?= $res['default_login_time'] ?></td>
                <td>
                    <?php 
                        if(empty($res['first_in'])) {
                            echo "<span class='text-danger'>Absent</span>";
                        } elseif($res['first_in'] > $res['default_login_time']) {
                            echo "<span class='text-warning'>Late</span>";
                        } else {
                            echo "<span class='text-success'>On Time</span>";
                        }
                    ?>
                </td>
            </tr>
        <?php } } ?>
    </tbody>
</table>
