<!DOCTYPE html>
<html>
<head>
    <title>Unique Domain List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Unique Domain List</h2>
    <button id="exportBtn" class="btn btn-success mb-3">Download as Excel</button>
    <table id="domainTable" class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Domain</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($domains as $index => $domain): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($domain) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- jQuery + table2excel -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/table2excel@1.1.2/dist/table2excel.min.js"></script>
<script>
    $('#exportBtn').click(function () {
        let table2excel = new Table2Excel();
        table2excel.export(document.querySelectorAll("#domainTable"));
    });
</script>
</body>
</html>
