<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-sitemap"></i> SubDomain Management</h3>
        <div class="box-tools pull-right" style="margin-top:5px;">
            <button id="python_server_btn" class="btn btn-sm btn-default" onclick="togglePythonServer()" title="Start/Stop Python API Server">
                <i id="python_server_icon" class="fa fa-circle" style="color:#aaa;"></i>
                <span id="python_server_label">Python Server</span>
            </button>
        </div>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> Dashboard</a></li>
            <li class="active">SubDomains</li>
        </ol>
    </div><!-- /.box-header -->
    
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="page-header">
                    <a class="ose-btn create-btn" href="<?php echo base_url('subdomains/add') ?>">
                        <i class="fa fa-plus"></i> 
                        Add SubDomain
                    </a>
                </h5>
                
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Filter by Server</label>
                            <select id="server_filter" class="form-control select2">
                                <option value="">Select Server</option>
                                <?php if(customCompute($servers)) {
                                    foreach($servers as $server) {
                                        if(!empty($server->server)) {
                                            echo "<option value='".htmlspecialchars($server->server)."'>".htmlspecialchars(ucfirst($server->server))."</option>";
                                        }
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4" style="margin-top: 25px;">
                        <button id="bulk_migration_btn" class="btn btn-warning" onclick="migrationAll()" disabled>
                            <i class="fa fa-shuttle-van"></i> Bulk Migration
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="subdomains-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Server</th>
                                <th width="12%">SubDomain</th>
                                <th width="12%">DB Host</th>
                                <th width="12%">DB Name</th>
                                <th width="12%">Site Name</th>
                                <th width="12%">Main Domain</th>
                                <th width="8%">Status</th>
                                <th width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    var table = $('#subdomains-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo base_url('subdomains/ajax_list'); ?>",
            "type": "POST",
            "data": function(d) {
                d.server = $('#server_filter').val();
            }
        },
        "columns": [
            { "data": 0, "orderable": false },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5 },
            { "data": 6 },
            { "data": 7, "orderable": false },
            { "data": 8, "orderable": false }
        ],
        "order": [[ 1, "desc" ]],
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "emptyTable": "No subdomains found",
            "zeroRecords": "No matching subdomains found"
        }
    });

    $('#server_filter').change(function() {
        table.draw();
        var selectedServer = $(this).val();
        if (selectedServer) {
            $('#bulk_migration_btn').prop('disabled', false);
            $('#bulk_migration_btn').html('<i class="fa fa-database"></i> Create Tables for All ' + selectedServer + ' Domains');
        } else {
            $('#bulk_migration_btn').prop('disabled', true);
            $('#bulk_migration_btn').html('<i class="fa fa-database"></i> Bulk Migration');
        }
    });
});

function migrationAll() {
    var server = $('#server_filter').val();
    if (!server) {
        alert('Please select a server first.');
        return;
    }

    if (confirm('Are you sure you want to create tables for ALL active domains on the ' + server + ' server? This will not delete any existing data.')) {
        var btn = $('#bulk_migration_btn');
        var originalHtml = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);

        $.ajax({
            url: 'http://localhost:8000/create-tables-bulk?server=' + encodeURIComponent(server),
            type: 'POST',
            success: function(response) {
                btn.html(originalHtml).prop('disabled', false);
                if (response.success) {
                    alert('Bulk Success: ' + response.message + '\nDomains processed: ' + response.domains_processed);
                } else {
                    alert('Partial Success: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                btn.html(originalHtml).prop('disabled', false);
                var errorMsg = 'An error occurred during bulk migration.';
                if (xhr.responseJSON && xhr.responseJSON.detail) {
                    errorMsg = xhr.responseJSON.detail;
                }
                alert('Error: ' + errorMsg);
            }
        });
    }
}

// ── Python Server Status ────────────────────────────────────────────────────

function checkPythonServerStatus() {
    $.ajax({
        url: '<?php echo base_url("subdomains/python_server_status"); ?>',
        type: 'GET',
        success: function(res) {
            setPythonServerUI(res.running);
        },
        error: function() {
            setPythonServerUI(false);
        }
    });
}

function setPythonServerUI(isRunning) {
    var icon  = document.getElementById('python_server_icon');
    var label = document.getElementById('python_server_label');
    var btn   = document.getElementById('python_server_btn');
    if (isRunning) {
        icon.style.color  = '#5cb85c';
        label.textContent = 'Python Server Running';
        btn.className     = 'btn btn-sm btn-success';
        btn.title         = 'Server is running on port 8000';
    } else {
        icon.style.color  = '#d9534f';
        label.textContent = 'Start Python Server';
        btn.className     = 'btn btn-sm btn-danger';
        btn.title         = 'Click to start Python API server';
    }
}

function togglePythonServer() {
    var btn = document.getElementById('python_server_btn');
    var isRunning = btn.className.indexOf('btn-success') !== -1;
    if (isRunning) {
        alert('Server is already running on http://localhost:8000\nTo stop it, close the terminal window running uvicorn.');
        return;
    }

    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Starting...';
    btn.disabled  = true;

    $.ajax({
        url: '<?php echo base_url("subdomains/start_python_server"); ?>',
        type: 'POST',
        success: function(res) {
            btn.disabled = false;
            if (res.success) {
                setPythonServerUI(true);
            } else {
                setPythonServerUI(false);
                alert('Error: ' + res.message);
            }
        },
        error: function() {
            btn.disabled = false;
            setPythonServerUI(false);
            alert('Failed to contact the server starter. Check PHP error log.');
        }
    });
}

// Check status once on page load only
checkPythonServerStatus();

// ── Table Creator ────────────────────────────────────────────────────────────

function createTables(btn, subdomainId) {
    console.log("createTables function called with ID:", subdomainId);
    if (confirm('Are you sure you want to create tables for this subdomain? This will execute the SQL from tables.sql on the target database.')) {
        console.log("Confirmation accepted for ID:", subdomainId);
        // Show loading (simple alert or button disable could be used, here I'll use a simple alert)
        var originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        $(btn).addClass('disabled');

        $.ajax({
            url: 'http://localhost:8000/create-tables/' + subdomainId,
            type: 'POST',
            success: function(response) {
                btn.innerHTML = originalHtml;
                $(btn).removeClass('disabled');
                if (response.success) {
                    alert('Success: ' + response.message);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                btn.innerHTML = originalHtml;
                $(btn).removeClass('disabled');
                var errorMsg = 'An error occurred while creating tables.';
                if (xhr.responseJSON && xhr.responseJSON.detail) {
                    errorMsg = xhr.responseJSON.detail;
                }
                alert('Error: ' + errorMsg);
            }
        });
    }
}
</script>

<style>
.ose-btn {
    background-color: #3c8dbc;
    border-color: #3c8dbc;
    color: white;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 4px;
    text-decoration: none;
    display: inline-block;
}

.ose-btn:hover {
    background-color: #2e6da4;
    border-color: #2e6da4;
    color: white;
    text-decoration: none;
}

.badge {
    display: inline-block;
    min-width: 10px;
    padding: 3px 7px;
    font-size: 12px;
    font-weight: bold;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    border-radius: 10px;
}

.badge-success {
    background-color: #5cb85c;
}

.badge-danger {
    background-color: #d9534f;
}

.btn-success {
    background-color: #5cb85c;
    border-color: #4cae4c;
}

.btn-success:hover {
    background-color: #449d44;
    border-color: #398439;
}

.btn-group .btn {
    margin-right: 2px;
}
</style>