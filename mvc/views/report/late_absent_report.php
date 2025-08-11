<div style="margin-bottom: 10px;">
    <button id="exportExcel" class="btn btn-success">Export to Excel</button>
    <button id="printTable" class="btn btn-primary">Print</button>
</div>

<table  id="biometricReport" class="table table-bordered table-hover">
    <thead style="background-color:#9aecec;">
        <tr>
            <th>Teacher Name</th>
            <th>Designation</th>
            <th>Phone</th>
            <th>RFID</th>
            <th>Date</th>
            <th>First Punch-In</th>
            <th>Default Login Time</th>
            <th>Last Punch-Out</th>
            <th>Default Logout Time</th>
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
                <td class="text-orange"><?= $res['first_in'] ?: '-' ?></td>
                <td><?= $res['default_login_time'] ?></td>
                <td class="text-orange"><?= $res['last_out'] ?: '-' ?></td>
                <td><?= $res['default_logout_time'] ?></td>
                <td>
                    <?php 
                        if(empty($res['first_in'])) {
                            echo "<span class='text-danger'>Absent</span>";
                        } elseif(
                            $res['first_in'] > date('H:i:s', strtotime($res['default_login_time'].' +15 minutes')) ||
                            $res['last_out'] < date('H:i:s', strtotime($res['default_logout_time'].' -15 minutes'))
                        ) {
                            echo "<span class='text-red'><b>Late</b></span>";
                        } else {
                            echo "<span class='text-success'>On Time</span>";
                        }
                    ?>
                </td>
            </tr>
        <?php } } ?>
    </tbody>
</table>
<!-- JS for Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.getElementById("exportExcel").addEventListener("click", function () {
    var table = document.getElementById("biometricReport");
    var workbook = XLSX.utils.table_to_book(table, { sheet: "Biometric Report" });
    XLSX.writeFile(workbook, "biometric_report.xlsx");
});

 // Print Table
    document.getElementById('printTable').addEventListener('click', function () {
        var tableHTML = document.getElementById("biometricReport").outerHTML;
        var printWindow = window.open('', '', 'width=900,height=650');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Report</title>
                    <style>
                        table { border-collapse: collapse; width: 100%; }
                        th, td { border: 1px solid black; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>
                    <h2>Report</h2>
                    ${tableHTML}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    });

</script>