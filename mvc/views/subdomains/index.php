<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-sitemap"></i> SubDomain Management</h3>
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
                
                <div class="table-responsive">
                    <table id="subdomains-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="12%">Server</th>
                                <th width="15%">SubDomain</th>
                                <th width="15%">DB Host</th>
                                <th width="15%">DB Name</th>
                                <th width="15%">Site Name</th>
                                <th width="10%">Status</th>
                                <th width="13%">Actions</th>
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
    $('#subdomains-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo base_url('subdomains/ajax_list'); ?>",
            "type": "POST"
        },
        "columns": [
            { "data": 0, "orderable": false },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5 },
            { "data": 6, "orderable": false },
            { "data": 7, "orderable": false }
        ],
        "order": [[ 1, "desc" ]],
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "emptyTable": "No subdomains found",
            "zeroRecords": "No matching subdomains found"
        }
    });
});
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
</style>