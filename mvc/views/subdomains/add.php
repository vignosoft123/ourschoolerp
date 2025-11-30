<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-plus"></i> Add SubDomain</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> Dashboard</a></li>
            <li><a href="<?=base_url("subdomains/index")?>"><i class="fa fa-sitemap"></i> SubDomains</a></li>
            <li class="active">Add</li>
        </ol>
    </div><!-- /.box-header -->
    
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <?php echo form_open(base_url('subdomains/add'), array('class' => 'form-horizontal', 'role' => 'form', 'id' => 'subdomain-form')); ?>
                
                    <div class="form-group">
                        <label for="server" class="col-sm-2 control-label">
                            Server <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $serverArray = array(
                                    '' => 'Select Server',
                                    'hostgator' => 'HostGator',
                                    'godaddy' => 'GoDaddy', 
                                    'myschools' => 'MySchools',
                                    'schoolhour' => 'SchoolHour',
                                    'collegehour' => 'CollegeHour'
                                );
                                echo form_dropdown("server", $serverArray, set_value("server"), "id='server' class='form-control select2'");
                            ?>
                            <?php echo form_error('server', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subdomain" class="col-sm-2 control-label">
                            SubDomain <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php echo form_input(array('id' => 'subdomain', 'name' => 'subdomain', 'value' => set_value('subdomain'), 'class' => 'form-control', 'placeholder' => 'Enter subdomain')); ?>
                            <?php echo form_error('subdomain', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="db_host" class="col-sm-2 control-label">
                            Database Host <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php echo form_input(array('id' => 'db_host', 'name' => 'db_host', 'value' => set_value('db_host'), 'class' => 'form-control', 'placeholder' => 'Database host will auto-fill', 'readonly' => 'readonly')); ?>
                            <?php echo form_error('db_host', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="db_name" class="col-sm-2 control-label">
                            Database Name <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php echo form_input(array('id' => 'db_name', 'name' => 'db_name', 'value' => set_value('db_name'), 'class' => 'form-control', 'placeholder' => 'Enter database name')); ?>
                            <?php echo form_error('db_name', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="db_user" class="col-sm-2 control-label">
                            Database User <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php echo form_input(array('id' => 'db_user', 'name' => 'db_user', 'value' => set_value('db_user'), 'class' => 'form-control', 'placeholder' => 'Enter database username')); ?>
                            <?php echo form_error('db_user', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="db_pass" class="col-sm-2 control-label">
                            Database Password <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php echo form_password(array('id' => 'db_pass', 'name' => 'db_pass', 'value' => set_value('db_pass'), 'class' => 'form-control', 'placeholder' => 'Enter database password')); ?>
                            <?php echo form_error('db_pass', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="site_name" class="col-sm-2 control-label">
                            Site Name
                        </label>
                        <div class="col-sm-6">
                            <?php echo form_input(array('id' => 'site_name', 'name' => 'site_name', 'value' => set_value('site_name'), 'class' => 'form-control', 'placeholder' => 'Enter site name')); ?>
                            <?php echo form_error('site_name', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="logo_url" class="col-sm-2 control-label">
                            Logo URL
                        </label>
                        <div class="col-sm-6">
                            <?php echo form_input(array('id' => 'logo_url', 'name' => 'logo_url', 'value' => set_value('logo_url'), 'class' => 'form-control', 'placeholder' => 'Enter logo URL')); ?>
                            <?php echo form_error('logo_url', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="theme_color" class="col-sm-2 control-label">
                            Theme Color
                        </label>
                        <div class="col-sm-6">
                            <?php echo form_input(array('id' => 'theme_color', 'name' => 'theme_color', 'value' => set_value('theme_color', '#ffffff'), 'class' => 'form-control', 'placeholder' => '#ffffff', 'type' => 'color')); ?>
                            <?php echo form_error('theme_color', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="main_domain" class="col-sm-2 control-label">
                            Main Domain
                        </label>
                        <div class="col-sm-6">
                            <?php echo form_input(array('id' => 'main_domain', 'name' => 'main_domain', 'value' => set_value('main_domain'), 'class' => 'form-control', 'placeholder' => 'Main domain will auto-fill', 'readonly' => 'readonly')); ?>
                            <?php echo form_error('main_domain', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="col-sm-2 control-label">
                            Status <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $statusArray = array(
                                    'active' => 'Active',
                                    'inactive' => 'Inactive'
                                );
                                echo form_dropdown("status", $statusArray, set_value("status", 'active'), "id='status' class='form-control select2'");
                            ?>
                            <?php echo form_error('status', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input type="submit" class="btn btn-success" value="Save">
                            <a href="<?=base_url('subdomains/index')?>" class="btn btn-default">Cancel</a>
                        </div>
                    </div>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('.select2').select2();
    
    // Handle server dropdown change
    $('#server').on('change', function() {
        var selectedServer = $(this).val();
        if (selectedServer) {
            $.ajax({
                url: '<?php echo base_url("subdomains/get_db_host"); ?>',
                type: 'POST',
                data: { server: selectedServer },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#db_host').val(response.db_host);
                        $('#main_domain').val(response.main_domain);
                    } else {
                        $('#db_host').val('');
                        $('#main_domain').val('');
                    }
                },
                error: function() {
                    $('#db_host').val('');
                    $('#main_domain').val('');
                }
            });
        } else {
            $('#db_host').val('');
            $('#main_domain').val('');
        }
    });
});
</script>

<style>
.error-message {
    color: #d9534f;
    font-size: 12px;
    margin-top: 5px;
}

.text-red {
    color: #dd4b39;
}

.form-horizontal .control-label {
    padding-top: 7px;
    margin-bottom: 0;
    text-align: right;
}

.form-group {
    margin-bottom: 15px;
}
</style>