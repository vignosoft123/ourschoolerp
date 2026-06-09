<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-sitemap"></i> SubDomain Management</h3>
        <div class="box-tools pull-right" style="margin-top:5px;">
            <button id="btn_start_python" class="btn btn-sm btn-danger" onclick="startPythonServer()" title="Start Python API server" style="display:none;">
                <i class="fa fa-play"></i> Start Python Server
            </button>
            <button id="btn_stop_python" class="btn btn-sm btn-success" onclick="stopPythonServer()" title="Stop Python API server" style="display:none;">
                <i class="fa fa-stop"></i> Python Server Running
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
                    <div class="col-sm-8" style="margin-top: 25px;">

                        <!-- ── GROUP 1: One-Time Setup (per new subdomain) ────────────── -->
                        <div class="btn-group-label" style="font-size:11px;color:#888;margin-bottom:3px;display:block;"><i class="fa fa-wrench"></i> ONE-TIME SETUP</div>
                        <div style="margin-bottom:8px;">
                            <button id="cpanel_create_btn" class="btn btn-info btn-sm" onclick="openCpanelCreateModal()" disabled title="Create a new subdomain folder in cPanel directly from localhost — no manual login needed (HostGator / MySchools / GoDaddy)">
                                <i class="fa fa-server"></i> Create in cPanel
                            </button>
                            &nbsp;
                            <button id="auto_create_btn" class="btn btn-success btn-sm" onclick="openAutoCreateModal()" disabled title="Auto-create subdomain: enter a live URL, system reads DB details from /main1 and inserts record automatically">
                                <i class="fa fa-magic"></i> Auto insert Subdomain
                            </button>
                            &nbsp;
                            <button id="bulk_migration_btn" class="btn btn-warning btn-sm" onclick="migrationAll()" disabled title="Create DB tables for all subdomains on selected server">
                                <i class="fa fa-shuttle-van"></i> Create Tables
                            </button>
                            &nbsp;
                            <button id="bulk_bootstrap_btn" class="btn btn-bootstrap btn-sm" onclick="bulkBootstrap()" disabled title="Bootstrap: copy Mvcdeploy.php + Cssupdate.php to each subdomain (first-time setup only)">
                                <i class="fa fa-plug"></i> Bootstrap All
                            </button>
                            &nbsp;
                            <button id="bulk_full_deploy_btn" class="btn btn-full-deploy btn-sm" onclick="bulkFullDeploy()" disabled title="Full Deploy: extract ALL zip files (assets, frontend, mvc, vendor etc.) — for new subdomain setup only">
                                <i class="fa fa-archive"></i> Full Deploy All
                            </button>
                        </div>

                        <!-- ── GROUP 2: Regular Updates (run each time you make changes) ─ -->
                        <div class="btn-group-label" style="font-size:11px;color:#888;margin-bottom:3px;display:block;"><i class="fa fa-refresh"></i> REGULAR UPDATES</div>
                        <div style="margin-bottom:8px;">
                            <button id="bulk_css_btn" class="btn btn-info btn-sm" onclick="bulkUpdateCss()" disabled title="Sync CSS files from localhost to all subdomains on selected server">
                                <i class="fa fa-cloud-upload"></i> Sync CSS to All
                            </button>
                            &nbsp;
                            <button id="upload_mvc_zip_btn" class="btn btn-warning btn-sm" onclick="uploadMvcZipToServer()" disabled title="HostGator/MySchools: Upload local mvc.zip to dummy server ONCE — then click Rocket on each subdomain (no FTP per subdomain)">
                                <i class="fa fa-upload"></i> Upload MVC to Dummy
                            </button>
                            &nbsp;
                            <button id="bulk_deploy_btn" class="btn btn-deploy-mvc btn-sm" onclick="bulkDeployMvc()" disabled title="Deploy MVC to all subdomains. For HostGator/MySchools: click 'Upload MVC to Dummy' first">
                                <i class="fa fa-rocket"></i> Deploy MVC to All
                            </button>
                            &nbsp;
                            <button id="ftp_upload_btn" class="btn btn-ftp-upload btn-sm" onclick="openFtpUploadModal()" disabled title="Upload any file from localhost to the same path on all active subdomains of the selected server">
                                <i class="fa fa-file-o"></i> FTP Upload File
                            </button>
                        </div>

                        <!-- ── GROUP 3: Info / Admin ────────────────────────────────────── -->
                        <div class="btn-group-label" style="font-size:11px;color:#888;margin-bottom:3px;display:block;"><i class="fa fa-info-circle"></i> INFO / ADMIN</div>
                        <div>
                            <button id="refresh_age_btn" class="btn btn-refresh-age btn-sm" onclick="refreshSchoolsAge()" disabled title="Refresh school age data for all subdomains on selected server">
                                <i class="fa fa-refresh"></i> Refresh Schools Age
                            </button>
                        </div>

                    </div>
                </div>

                <!-- ── Server Capability Matrix ─────────────────────────────── -->
                <div style="margin-bottom:16px;">
                    <div style="font-size:11px;color:#888;margin-bottom:4px;"><i class="fa fa-info-circle"></i> SERVER CAPABILITY MATRIX</div>
                    <table class="table table-bordered table-condensed" style="font-size:12px;margin-bottom:4px;background:#fff;">
                        <thead>
                            <tr style="background:#37474f;">
                                <th style="width:12%;padding:5px 8px;color:#fff;">Server</th>
                                <th style="width:13%;padding:5px 8px;color:#fff;">Domain</th>
                                <th style="width:10%;padding:5px 8px;text-align:center;color:#fff;"><i class="fa fa-plug"></i> Bootstrap</th>
                                <th style="width:10%;padding:5px 8px;text-align:center;color:#fff;"><i class="fa fa-cloud-upload"></i> CSS Sync</th>
                                <th style="width:13%;padding:5px 8px;text-align:center;color:#fff;"><i class="fa fa-upload"></i> Upload Dummy</th>
                                <th style="width:10%;padding:5px 8px;text-align:center;color:#fff;"><i class="fa fa-rocket"></i> Rocket</th>
                                <th style="width:10%;padding:5px 8px;text-align:center;color:#fff;"><i class="fa fa-archive"></i> Full Deploy</th>
                                <th style="width:10%;padding:5px 8px;color:#fff;">Handler</th>
                                <th style="width:12%;padding:5px 8px;text-align:center;color:#fff;"><i class="fa fa-play-circle"></i> Python Needed?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:4px 8px;font-weight:bold;color:#e65100;">Hostgator</td>
                                <td style="padding:4px 8px;color:#666;">ourschoolerp.com</td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;color:#555;">PHP FTP</td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#e65100;font-size:11px;" title="Bootstrap &amp; Full Deploy still use Python">⚠ Bootstrap &amp; Full Deploy only</span></td>
                            </tr>
                            <tr style="background:#fafafa;">
                                <td style="padding:4px 8px;font-weight:bold;color:#c62828;">Myschools</td>
                                <td style="padding:4px 8px;color:#666;">myschoolserp.com</td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;color:#555;">PHP FTP</td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#e65100;font-size:11px;" title="Bootstrap &amp; Full Deploy still use Python">⚠ Bootstrap &amp; Full Deploy only</span></td>
                            </tr>
                            <tr>
                                <td style="padding:4px 8px;font-weight:bold;color:#1565c0;">Schoolhour</td>
                                <td style="padding:4px 8px;color:#666;">schoolhour.in</td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;color:#555;">PHP FTP</td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#e65100;font-size:11px;" title="Bootstrap &amp; Full Deploy still use Python">⚠ Bootstrap &amp; Full Deploy only</span></td>
                            </tr>
                            <tr style="background:#fafafa;">
                                <td style="padding:4px 8px;font-weight:bold;color:#6a1b9a;">Collegehour</td>
                                <td style="padding:4px 8px;color:#666;">collegeerp.in</td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;color:#555;">PHP FTP</td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#e65100;font-size:11px;" title="Bootstrap &amp; Full Deploy still use Python">⚠ Bootstrap &amp; Full Deploy only</span></td>
                            </tr>
                            <tr>
                                <td style="padding:4px 8px;font-weight:bold;color:#2e7d32;">Godaddy</td>
                                <td style="padding:4px 8px;color:#666;">ourcollegeerp.com</td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#999;">—</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;text-align:center;"><span style="color:#2e7d32;">✔</span></td>
                                <td style="padding:4px 8px;color:#555;">Python POST</td>
                                <td style="padding:4px 8px;text-align:center;"><span style="background:#c62828;color:#fff;padding:2px 6px;border-radius:3px;font-size:11px;font-weight:bold;">YES — All ops</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="font-size:11px;color:#888;"><i class="fa fa-play-circle" style="color:#c62828;"></i> <strong style="color:#c62828;">Python server must be running</strong> when using: Godaddy (all buttons) &nbsp;|&nbsp; Bootstrap Plug on any server &nbsp;|&nbsp; Full Deploy on any server. &nbsp; CSS Sync &amp; Rocket for Hostgator / Myschools / Schoolhour / Collegehour = <strong>no Python needed</strong>.</div>
                </div>

                <div class="table-responsive">
                    <table id="subdomains-table" class="table table-striped table-bordered table-hover" style="cursor:pointer;">
                        <thead>
                            <tr>
                                <th width="3%" class="text-center"><input type="checkbox" id="select-all-checkbox" title="Select all visible rows"></th>
                                <th width="3%">#</th>
                                <th width="8%">Server</th>
                                <th width="10%">SubDomain</th>
                                <th width="16%">Domain</th>
                                <th width="11%">DB Name</th>
                                <th width="8%">School Age</th>
                                <th width="9%">Students</th>
                                <th width="9%">App Users</th>
                                <th width="18%">Actions</th>
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

<!-- ══ School Year Analysis Pivot Table ══════════════════════════════════════ -->
<div id="school-age-section" style="margin-top:24px;">
    <div class="box box-primary">
        <div class="box-header with-border" id="pivot-collapse-toggle" style="cursor:pointer;" title="Click to collapse/expand">
            <h3 class="box-title">
                <i class="fa fa-table"></i>
                School Year Analysis — <span style="color:#3c8dbc;font-weight:700;">All Servers</span>
            </h3>
            <div class="box-tools pull-right">
                <span class="text-muted" style="font-size:12px;margin-right:10px;">
                    <i class="fa fa-info-circle"></i>
                    Click <strong>Refresh Schools Age</strong> to update this data
                </span>
                <button type="button" class="btn btn-box-tool" id="pivot-collapse-btn">
                    <i class="fa fa-minus" id="pivot-collapse-icon"></i>
                </button>
            </div>
        </div>
        <div id="pivot-collapsible-body">
        <div class="box-body">
            <div id="pivot-loading" style="text-align:center;padding:30px;display:none;">
                <i class="fa fa-spinner fa-spin fa-2x" style="color:#3c8dbc;"></i>
                <p style="margin-top:10px;color:#888;">Loading analysis data…</p>
            </div>
            <div id="pivot-empty" style="display:none;" class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                No data found. Click <strong>Refresh Schools Age</strong> to populate.
            </div>
            <div id="pivot-error" style="display:none;" class="alert alert-danger">
                <i class="fa fa-exclamation-triangle"></i> <span id="pivot-error-msg"></span>
            </div>
            <div id="pivot-table-wrap" class="table-responsive" style="display:none;"></div>
        </div>
        </div><!-- /#pivot-collapsible-body -->
    </div><!-- /.box -->
</div><!-- /#school-age-section -->

<!-- ══ Auto Create Subdomain Modal ══════════════════════════════════════════ -->
<!-- ── Create in cPanel Modal ──────────────────────────────────────────────── -->
<div class="modal fade" id="cpanelCreateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#1565c0;color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-server"></i> Create Subdomain in cPanel</h4>
            </div>
            <div class="modal-body">
                <p style="margin:0 0 10px;font-size:13px;">Server: <strong id="cpanel_create_server_label" style="color:#1565c0;">—</strong></p>
                <input type="text" id="cpanel_create_name" class="form-control" placeholder="e.g. newschool (name only, no domain)" style="margin-bottom:8px;">
                <p id="cpanel_create_preview" style="color:#888;font-size:12px;margin:0 0 10px;"></p>
                <div id="cpanel_create_result" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" id="cpanel_create_submit_btn" class="btn btn-primary" onclick="submitCpanelCreate()">
                    <i class="fa fa-plus"></i> Create Subdomain
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="autoCreateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#27ae60;color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-magic"></i> Auto Create Subdomain</h4>
            </div>
            <div class="modal-body">
                <p style="font-size:13px;color:#555;margin-bottom:12px;">
                    Enter the live subdomain URL. The system will fetch DB details from <code>/main1</code> and insert the record automatically.
                </p>
                <div class="form-group">
                    <label style="font-size:13px;">Subdomain URL</label>
                    <input type="text" id="auto_create_url" class="form-control"
                        placeholder="https://gowthamcumbum.ourcollegeerp.com/"
                        style="font-size:13px;">
                    <span class="help-block" style="font-size:11px;color:#888;">
                        Server: <strong id="auto_create_server_label">—</strong>
                    </span>
                </div>
                <div id="auto_create_result" style="display:none;margin-top:10px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" id="auto_create_submit_btn" class="btn btn-success" onclick="submitAutoCreate()">
                    <i class="fa fa-magic"></i> Fetch &amp; Create
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══ FTP Upload File Modal ══════════════════════════════════════════════════ -->
<div class="modal fade" id="ftpUploadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#37474f;color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-file-o"></i> FTP Upload File to All Subdomains</h4>
            </div>
            <div class="modal-body">
                <p style="margin:0 0 10px;font-size:13px;">Server: <strong id="ftp_upload_server_label" style="color:#37474f;">—</strong></p>
                <div class="form-group">
                    <label style="font-size:12px;color:#555;">File path (relative to project root):</label>
                    <input type="text" id="ftp_upload_path" class="form-control"
                           placeholder="e.g. frontend/default/views/partials/footer.blade.php"
                           style="font-size:13px;">
                    <p style="font-size:11px;color:#888;margin-top:5px;margin-bottom:0;">
                        Reads from <code>C:\xampp\htdocs\ourschoolerp\{path}</code><br>
                        and uploads to the same relative path on every active subdomain.
                    </p>
                </div>
                <div id="ftp_upload_result" style="display:none;margin-top:10px;max-height:220px;overflow-y:auto;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" id="ftp_upload_submit_btn" class="btn btn-primary" onclick="submitFtpUpload()">
                    <i class="fa fa-upload"></i> Upload to All
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══ Row Info Modal ════════════════════════════════════════════════════════ -->
<div class="modal fade" id="rowInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header rowinfo-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">
                    <i class="fa fa-info-circle"></i>
                    <span id="ri-title"></span>
                </h4>
            </div>
            <div class="modal-body" style="padding:0;">
                <table class="table table-bordered table-condensed rowinfo-table" style="margin:0;">
                    <tbody>
                        <tr><td class="ri-label">SubDomain</td>     <td id="ri-subdomain"></td></tr>
                        <tr><td class="ri-label">Site Name</td>     <td id="ri-site_name"></td></tr>
                        <tr><td class="ri-label">Server</td>        <td id="ri-server"></td></tr>
                        <tr><td class="ri-label">Main Domain</td>   <td id="ri-main_domain"></td></tr>
                        <tr><td class="ri-label">DB Host</td>       <td id="ri-db_host"></td></tr>
                        <tr><td class="ri-label">DB Name</td>       <td id="ri-db_name"></td></tr>
                        <tr><td class="ri-label">DB User</td>       <td id="ri-db_user"></td></tr>
                        <tr><td class="ri-label">Theme Color</td>   <td id="ri-theme_color"></td></tr>
                        <tr><td class="ri-label">Logo URL</td>      <td id="ri-logo_url" style="word-break:break-all;"></td></tr>
                        <tr><td class="ri-label">Status</td>        <td id="ri-status"></td></tr>
                        <tr><td class="ri-label">School Age</td>    <td id="ri-school_age"></td></tr>
                        <tr><td class="ri-label">Total Students</td><td id="ri-total_students"></td></tr>
                        <tr><td class="ri-label">App Users</td>     <td id="ri-total_app_users"></td></tr>
                        <tr><td class="ri-label">Created At</td>    <td id="ri-created_at"></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ══ Statistics Modal ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="statisticsModal" tabindex="-1" role="dialog" aria-labelledby="statisticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width:92%;margin:30px auto;">
        <div class="modal-content">
            <div class="modal-header stats-modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="statisticsModalLabel">
                    <i class="fa fa-bar-chart"></i>
                    Statistics — <span id="stats-site-name"></span>
                </h4>
            </div>
            <div class="modal-body" id="stats-modal-body">
                <!-- Loading state -->
                <div id="stats-loading" class="text-center" style="padding:40px 0;">
                    <i class="fa fa-spinner fa-spin fa-3x" style="color:#9b59b6;"></i>
                    <p style="margin-top:12px;color:#888;">Fetching statistics from tenant database…</p>
                </div>
                <!-- Error state -->
                <div id="stats-error" style="display:none;" class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> <span id="stats-error-msg"></span>
                </div>
                <!-- Content state -->
                <div id="stats-content" style="display:none;">
                    <!-- Summary bar -->
                    <div class="stats-summary-bar">
                        <div class="stats-summary-item">
                            <div class="stats-summary-value" id="stat-total-students">0</div>
                            <div class="stats-summary-label">Total Students</div>
                        </div>
                        <div class="stats-summary-item">
                            <div class="stats-summary-value" style="color:#27ae60;" id="stat-total-app">0</div>
                            <div class="stats-summary-label">App Users</div>
                        </div>
                        <div class="stats-summary-item">
                            <div class="stats-summary-value" style="color:#e67e22;" id="stat-overall-pct">0%</div>
                            <div class="stats-summary-label">App Adoption</div>
                        </div>
                        <div class="stats-summary-item">
                            <div class="stats-summary-value" style="color:#3498db;" id="stat-total-years">0</div>
                            <div class="stats-summary-label">Academic Years</div>
                        </div>
                    </div>
                    <!-- Year cards (horizontal scroll) -->
                    <div class="stats-years-wrapper">
                        <div id="stats-years-container" class="stats-years-container"></div>
                    </div>
                    <!-- No-device-token notice -->
                    <div id="stats-no-token-notice" style="display:none;" class="alert alert-warning" style="margin-top:12px;">
                        <i class="fa fa-info-circle"></i>
                        <strong>Note:</strong> The <code>device_token</code> column was not found in the student table of this tenant database. App-user counts are shown as 0.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

// Global — accessible by bulkUpdateCss() and other functions outside $(document).ready()
var selectedIds = new Set();

function updateBulkCssBtn() {
    var count  = selectedIds.size;
    var server = $('#server_filter').val();
    if (count > 0) {
        $('#bulk_css_btn').prop('disabled', false)
            .html('<i class="fa fa-cloud-upload"></i> Sync CSS to ' + count + ' Selected');
    } else if (server) {
        $('#bulk_css_btn').prop('disabled', false)
            .html('<i class="fa fa-cloud-upload"></i> Sync CSS to All ' + server);
    } else {
        $('#bulk_css_btn').prop('disabled', true)
            .html('<i class="fa fa-cloud-upload"></i> Bulk CSS Update');
    }
}

function updateBulkDeployBtn() {
    var count  = selectedIds.size;
    var server = $('#server_filter').val();
    // "Upload MVC to Dummy" only relevant for FTP servers (hostgator, myschools, schoolhour, collegehour)
    var serverLower   = server.toLowerCase();
    var ftpServers    = ['hostgator', 'myschools', 'schoolhour', 'collegehour'];
    var cpanelServers = ['hostgator', 'myschools', 'godaddy'];
    var showUploadBtn  = ftpServers.indexOf(serverLower) !== -1;
    var showCpanelBtn  = cpanelServers.indexOf(serverLower) !== -1;
    if (count > 0) {
        $('#cpanel_create_btn').prop('disabled', !showCpanelBtn);
        $('#auto_create_btn').prop('disabled', false);
        $('#bulk_bootstrap_btn').prop('disabled', false).html('<i class="fa fa-plug"></i> Bootstrap ' + count + ' Selected');
        $('#bulk_deploy_btn').prop('disabled', false).html('<i class="fa fa-rocket"></i> Deploy MVC to ' + count + ' Selected');
        $('#bulk_full_deploy_btn').prop('disabled', false).html('<i class="fa fa-archive"></i> Full Deploy ' + count + ' Selected');
        $('#ftp_upload_btn').prop('disabled', false);
        if (showUploadBtn) {
            $('#upload_mvc_zip_btn').prop('disabled', false).html('<i class="fa fa-upload"></i> Upload MVC to Dummy (' + server + ')');
        } else {
            $('#upload_mvc_zip_btn').prop('disabled', true).html('<i class="fa fa-upload"></i> Upload MVC to Dummy');
        }
    } else if (server) {
        $('#cpanel_create_btn').prop('disabled', !showCpanelBtn);
        $('#auto_create_btn').prop('disabled', false);
        $('#bulk_bootstrap_btn').prop('disabled', false).html('<i class="fa fa-plug"></i> Bootstrap All ' + server);
        $('#bulk_deploy_btn').prop('disabled', false).html('<i class="fa fa-rocket"></i> Deploy MVC to All ' + server);
        $('#bulk_full_deploy_btn').prop('disabled', false).html('<i class="fa fa-archive"></i> Full Deploy All ' + server);
        $('#ftp_upload_btn').prop('disabled', false);
        if (showUploadBtn) {
            $('#upload_mvc_zip_btn').prop('disabled', false).html('<i class="fa fa-upload"></i> Upload MVC to Dummy (' + server + ')');
        } else {
            $('#upload_mvc_zip_btn').prop('disabled', true).html('<i class="fa fa-upload"></i> Upload MVC to Dummy');
        }
    } else {
        $('#cpanel_create_btn').prop('disabled', true);
        $('#auto_create_btn').prop('disabled', true);
        $('#bulk_bootstrap_btn').prop('disabled', true).html('<i class="fa fa-plug"></i> Bootstrap via FTP');
        $('#bulk_deploy_btn').prop('disabled', true).html('<i class="fa fa-rocket"></i> Bulk Deploy MVC');
        $('#bulk_full_deploy_btn').prop('disabled', true).html('<i class="fa fa-archive"></i> Bulk Full Deploy');
        $('#ftp_upload_btn').prop('disabled', true);
        $('#upload_mvc_zip_btn').prop('disabled', true).html('<i class="fa fa-upload"></i> Upload MVC to Dummy');
    }
}

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
            {
                "data": null, "orderable": false, "className": "text-center",
                "render": function(data, type, row) {
                    return '<input type="checkbox" class="row-checkbox" data-id="' + row._id + '">';
                }
            },
            { "data": "serial",          "orderable": false },
            { "data": "server" },
            { "data": "subdomain" },
            { "data": "domain",          "orderable": false },
            { "data": "db_name" },
            { "data": "school_age",      "orderable": false, "className": "text-center" },
            { "data": "total_students",  "orderable": false, "className": "text-center" },
            { "data": "total_app_users", "orderable": false, "className": "text-center" },
            { "data": "actions",         "orderable": false }
        ],
        "order": [[ 2, "asc" ]],
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "language": {
            "emptyTable": "No subdomains found",
            "zeroRecords": "No matching subdomains found"
        },
        "createdRow": function(row, data) {
            $(row).addClass('row-clickable');
        }
    });

    // ── Checkbox selection tracking ──────────────────────────────────────────

    // Restore checkbox state after every DataTable redraw
    table.on('draw', function() {
        $('#subdomains-table tbody .row-checkbox').each(function() {
            var id = parseInt($(this).data('id'));
            $(this).prop('checked', selectedIds.has(id));
        });
        var total   = $('#subdomains-table tbody .row-checkbox').length;
        var checked = $('#subdomains-table tbody .row-checkbox:checked').length;
        $('#select-all-checkbox').prop('checked', total > 0 && checked === total);
        updateBulkCssBtn();
        updateBulkDeployBtn();
    });

    // Individual checkbox change
    $('#subdomains-table tbody').on('change', '.row-checkbox', function() {
        var id = parseInt($(this).data('id'));
        if ($(this).is(':checked')) { selectedIds.add(id); } else { selectedIds.delete(id); }
        var total   = $('#subdomains-table tbody .row-checkbox').length;
        var checked = $('#subdomains-table tbody .row-checkbox:checked').length;
        $('#select-all-checkbox').prop('checked', total > 0 && checked === total);
        updateBulkCssBtn();
        updateBulkDeployBtn();
    });

    // Prevent row-click popup when clicking checkbox
    $('#subdomains-table tbody').on('click', '.row-checkbox', function(e) {
        e.stopPropagation();
    });

    // Select-all checkbox
    $('#select-all-checkbox').on('change', function() {
        var checked = $(this).is(':checked');
        $('#subdomains-table tbody .row-checkbox').each(function() {
            $(this).prop('checked', checked);
            var id = parseInt($(this).data('id'));
            if (checked) { selectedIds.add(id); } else { selectedIds.delete(id); }
        });
        updateBulkCssBtn();
        updateBulkDeployBtn();
    });

    // Load pivot table on page ready
    loadSchoolAgeAnalysis();

    // Collapse/expand toggle
    $('#pivot-collapse-toggle').on('click', function() {
        var body = $('#pivot-collapsible-body');
        var icon = $('#pivot-collapse-icon');
        if (body.is(':visible')) {
            body.slideUp(200);
            icon.removeClass('fa-minus').addClass('fa-plus');
        } else {
            body.slideDown(200);
            icon.removeClass('fa-plus').addClass('fa-minus');
        }
    });

    // Row click → show full-info popup
    $('#subdomains-table tbody').on('click', 'tr.row-clickable', function() {
        var d = table.row(this).data();
        if (!d) return;
        showRowInfo(d);
    });

    $('#server_filter').change(function() {
        table.draw();
        selectedIds.clear();
        var selectedServer = $(this).val();
        if (selectedServer) {
            $('#bulk_migration_btn').prop('disabled', false);
            $('#bulk_migration_btn').html('<i class="fa fa-database"></i> Create Tables for All ' + selectedServer + ' Domains');
            $('#refresh_age_btn').prop('disabled', false);
            $('#refresh_age_btn').html('<i class="fa fa-refresh"></i> Refresh Schools Age (' + selectedServer + ')');
        } else {
            $('#bulk_migration_btn').prop('disabled', true);
            $('#bulk_migration_btn').html('<i class="fa fa-shuttle-van"></i> Bulk Migration');
            $('#refresh_age_btn').prop('disabled', true);
            $('#refresh_age_btn').html('<i class="fa fa-refresh"></i> Refresh Schools Age');
        }
        updateBulkCssBtn();
        updateBulkDeployBtn();
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
    document.getElementById('btn_start_python').style.display = isRunning ? 'none' : '';
    document.getElementById('btn_stop_python').style.display  = isRunning ? '' : 'none';
}

function startPythonServer() {
    var btn = document.getElementById('btn_start_python');
    btn.disabled   = true;
    btn.innerHTML  = '<i class="fa fa-spinner fa-spin"></i> Starting...';
    $.ajax({
        url: '<?php echo base_url("subdomains/start_python_server"); ?>',
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa fa-play"></i> Start Python Server';
            if (res.success) {
                setPythonServerUI(true);
            } else {
                alert('Error: ' + res.message);
            }
        },
        error: function() {
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa fa-play"></i> Start Python Server';
            alert('Failed to contact the server starter. Check PHP error log.');
        }
    });
}

function stopPythonServer() {
    var btn = document.getElementById('btn_stop_python');
    btn.disabled   = true;
    btn.innerHTML  = '<i class="fa fa-spinner fa-spin"></i> Stopping...';
    $.ajax({
        url: '<?php echo base_url("subdomains/stop_python_server"); ?>',
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa fa-stop"></i> Python Server Running';
            if (res.success) {
                setPythonServerUI(false);
            } else {
                setPythonServerUI(true);
                alert('Error: ' + res.message);
            }
        },
        error: function() {
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa fa-stop"></i> Python Server Running';
            alert('Failed to stop server.');
        }
    });
}

// Check Python server status on page load
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

// ── Bulk CSS Update ───────────────────────────────────────────────────────────

function bulkUpdateCss() {
    var ids    = Array.from(selectedIds);
    var server = $('#server_filter').val();

    if (ids.length === 0 && !server) {
        alert('Please check subdomains or select a server first.');
        return;
    }

    var label = ids.length > 0
        ? ids.length + ' selected subdomain(s)'
        : 'ALL active subdomains on ' + server + ' server';

    if (!confirm('Sync all CSS files to ' + label + '?\nThis will overwrite CSS files on the live server(s).')) return;

    var btn = $('#bulk_css_btn');
    var originalHtml = btn.html();
    btn.html('<i class="fa fa-spinner fa-spin"></i> Syncing...').prop('disabled', true);

    var payload = ids.length > 0 ? { subdomain_ids: ids } : { server: server };

    $.ajax({
        url: 'http://localhost:8000/update-css-bulk',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        dataType: 'json',
        success: function(response) {
            btn.html(originalHtml).prop('disabled', false);
            var msg = 'CSS Sync Complete\n';
            msg += 'Success: ' + response.success_count + ' / ' + response.total + '\n\n';
            if (response.details && response.details.length) {
                response.details.forEach(function(d) {
                    msg += (d.success ? '✓ ' : '✗ ') + d.subdomain + ': ' + d.message + '\n';
                });
            }
            alert(msg);
        },
        error: function(xhr) {
            btn.html(originalHtml).prop('disabled', false);
            var detail = xhr.responseJSON ? xhr.responseJSON.detail : 'Unknown error';
            alert('Bulk CSS Update failed:\n' + detail);
        }
    });
}

// ── Bootstrap via FTP (Single) ───────────────────────────────────────────────

function bootstrapSubdomain(btn, subdomainId, subdomainName) {
    if (!confirm('Bootstrap "' + subdomainName + '" via FTP?\n\nThis uploads Cssupdate.php + css_update_config.php directly to the live server.\n\nMake sure FTP credentials are set in python/.env → FTP_CREDENTIALS')) return;

    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    $(btn).addClass('disabled');

    $.ajax({
        url: 'http://localhost:8000/bootstrap-subdomain/' + subdomainId,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            alert(response.success ? '✓ ' + response.message : '✗ ' + response.message);
        },
        error: function(xhr) {
            var detail = xhr.responseJSON ? xhr.responseJSON.detail : 'Unknown error';
            alert('Bootstrap failed: ' + detail);
        },
        complete: function() {
            btn.innerHTML = originalHtml;
            $(btn).removeClass('disabled');
        }
    });
}

// ── Bootstrap via FTP (Bulk) ──────────────────────────────────────────────────

function bulkBootstrap() {
    var ids    = Array.from(selectedIds);
    var server = $('#server_filter').val();
    if (ids.length === 0 && !server) { alert('Please check subdomains or select a server.'); return; }

    var label = ids.length > 0 ? ids.length + ' selected subdomain(s)' : 'ALL active on ' + server;
    if (!confirm('Bootstrap ' + label + ' via FTP?\n\nUploads Cssupdate.php + css_update_config.php to each live server.\nFTP credentials must be set in python/.env')) return;

    var btn = $('#bulk_bootstrap_btn');
    var originalHtml = btn.html();
    btn.html('<i class="fa fa-spinner fa-spin"></i> Bootstrapping...').prop('disabled', true);

    $.ajax({
        url: 'http://localhost:8000/bootstrap-subdomain-bulk',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(ids.length > 0 ? { subdomain_ids: ids } : { server: server }),
        dataType: 'json',
        success: function(response) {
            var msg = 'Bootstrap Complete: ' + response.success_count + '/' + response.total + '\n\n';
            response.details.forEach(function(d) { msg += (d.success ? '✓ ' : '✗ ') + d.subdomain + ': ' + d.message + '\n'; });
            alert(msg);
        },
        error: function(xhr) { alert('Failed: ' + (xhr.responseJSON ? xhr.responseJSON.detail : 'Unknown')); },
        complete: function() { btn.html(originalHtml).prop('disabled', false); }
    });
}

// ── Upload mvcdeploy.php (Single) ────────────────────────────────────────────

function uploadDeployScript(btn, subdomainId, subdomainName) {
    if (!confirm('Upload mvcdeploy.php to "' + subdomainName + '" live server?\n(Requires Cssupdate.php to be deployed there first)')) return;

    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    $(btn).addClass('disabled');

    $.ajax({
        url: 'http://localhost:8000/upload-deploy-script/' + subdomainId,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            alert(response.success ? '✓ ' + response.message : '✗ ' + response.message);
        },
        error: function(xhr) {
            var detail = xhr.responseJSON ? xhr.responseJSON.detail : 'Unknown error';
            alert('Failed: ' + detail);
        },
        complete: function() {
            btn.innerHTML = originalHtml;
            $(btn).removeClass('disabled');
        }
    });
}

// ── Upload mvcdeploy.php (Bulk) ───────────────────────────────────────────────

function bulkUploadDeployScript() {
    var ids    = Array.from(selectedIds);
    var server = $('#server_filter').val();
    if (ids.length === 0 && !server) { alert('Please check subdomains or select a server.'); return; }

    var label = ids.length > 0 ? ids.length + ' selected subdomain(s)' : 'ALL active on ' + server;
    if (!confirm('Upload mvcdeploy.php to ' + label + '?')) return;

    var btn = $('#bulk_upload_script_btn');
    var originalHtml = btn.html();
    btn.html('<i class="fa fa-spinner fa-spin"></i> Uploading...').prop('disabled', true);

    $.ajax({
        url: 'http://localhost:8000/upload-deploy-script-bulk',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(ids.length > 0 ? { subdomain_ids: ids } : { server: server }),
        dataType: 'json',
        success: function(response) {
            var msg = 'Upload Complete: ' + response.success_count + '/' + response.total + '\n\n';
            response.details.forEach(function(d) { msg += (d.success ? '✓ ' : '✗ ') + d.subdomain + ': ' + d.message + '\n'; });
            alert(msg);
        },
        error: function(xhr) { alert('Failed: ' + (xhr.responseJSON ? xhr.responseJSON.detail : 'Unknown error')); },
        complete: function() { btn.html(originalHtml).prop('disabled', false); }
    });
}

// ── Full Deploy — New Domain Setup (Single) ───────────────────────────────────

function fullDeploy(btn, subdomainId, subdomainName) {
    if (!confirm('Full Deploy to "' + subdomainName + '"?\n\n⚠ This extracts ALL zip files:\nassets, frontend, main2, mvc, others, uploads, vendor\n\nUse this for NEW domain setup only.')) return;

    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    $(btn).addClass('disabled');

    $.ajax({
        url: 'http://localhost:8000/full-deploy/' + subdomainId,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            var msg = (response.success ? '✓ ' : '✗ ') + response.message + '\n\n';
            if (response.details) {
                response.details.forEach(function(d) {
                    msg += (d.success ? '✓ ' : '✗ ') + d.file + ': ' + d.message + '\n';
                });
            }
            alert(msg);
        },
        error: function(xhr) {
            var detail = xhr.responseJSON ? xhr.responseJSON.detail : 'Unknown error';
            alert('Full Deploy failed: ' + detail);
        },
        complete: function() {
            btn.innerHTML = originalHtml;
            $(btn).removeClass('disabled');
        }
    });
}

// ── Full Deploy (Bulk) ────────────────────────────────────────────────────────

function bulkFullDeploy() {
    var ids    = Array.from(selectedIds);
    var server = $('#server_filter').val();
    if (ids.length === 0 && !server) { alert('Please check subdomains or select a server.'); return; }

    var label = ids.length > 0 ? ids.length + ' selected subdomain(s)' : 'ALL active on ' + server;
    if (!confirm('Full Deploy to ' + label + '?\n\n⚠ Extracts ALL zip files (assets, frontend, mvc, etc.) to each subdomain.\nUse for new domain setup only.')) return;

    var btn = $('#bulk_full_deploy_btn');
    var originalHtml = btn.html();
    btn.html('<i class="fa fa-spinner fa-spin"></i> Deploying...').prop('disabled', true);

    $.ajax({
        url: 'http://localhost:8000/full-deploy-bulk',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(ids.length > 0 ? { subdomain_ids: ids } : { server: server }),
        dataType: 'json',
        success: function(response) {
            var msg = 'Full Deploy: ' + response.success_count + '/' + response.total + '\n\n';
            response.details.forEach(function(d) { msg += (d.success ? '✓ ' : '✗ ') + d.subdomain + ': ' + d.message + '\n'; });
            alert(msg);
        },
        error: function(xhr) { alert('Failed: ' + (xhr.responseJSON ? xhr.responseJSON.detail : 'Unknown')); },
        complete: function() { btn.html(originalHtml).prop('disabled', false); }
    });
}

// ── Deploy MVC (Single) ───────────────────────────────────────────────────────

function deployMvc(btn, subdomainId, subdomainName, server) {
    if (!confirm('Deploy MVC to "' + subdomainName + '"?\n\n⚠ This will:\n• Rename mvc → mvc1\n• Upload & unzip new mvc.zip\n• Copy database.php from mvc1\n\nAre you sure?')) return;

    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    $(btn).addClass('disabled');

    // HostGator, BigRock, Schoolhour, Collegehour: use PHP endpoint (bootstrap_copy.php on dummy server)
    // mvc.zip must already be uploaded via "Upload MVC to Dummy" button
    // GoDaddy: use Python endpoint (direct HTTP POST)
    var ftpServers = ['hostgator', 'myschools', 'schoolhour', 'collegehour'];
    var usePhp = ftpServers.indexOf(server) !== -1;

    $.ajax({
        url: usePhp
            ? '<?=base_url("subdomains/deploy_mvc_php")?>/' + subdomainId
            : 'http://localhost:8000/deploy-mvc/' + subdomainId,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('✓ Success: ' + response.message);
            } else {
                alert('✗ Error: ' + response.message);
            }
        },
        error: function(xhr) {
            var detail = xhr.responseJSON ? xhr.responseJSON.detail : 'Unknown error';
            alert('Request failed.\n' + detail);
        },
        complete: function() {
            btn.innerHTML = originalHtml;
            $(btn).removeClass('disabled');
        }
    });
}

// ── Upload MVC Zip to Dummy Server ───────────────────────────────────────────
function uploadMvcZipToServer() {
    var server = $('#server_filter').val();
    if (!server) {
        alert('Please select a server first.');
        return;
    }
    var ftpServers = ['hostgator', 'myschools', 'schoolhour', 'collegehour'];
    if (ftpServers.indexOf(server) === -1) {
        alert(server + ' does not use a dummy server. Only Hostgator, Myschools, Schoolhour and Collegehour need this step.');
        return;
    }

    var btn = $('#upload_mvc_zip_btn');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading mvc.zip...');

    $.ajax({
        url: '<?=base_url("subdomains/upload_mvc_zip_php")?>/' + server,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res && res.success) {
                alert('✅ mvc.zip uploaded to dummy server (' + server + ')!\n\n' + res.message);
            } else {
                alert('❌ Upload failed: ' + (res ? res.message : 'Unknown error'));
            }
        },
        error: function(xhr) {
            alert('❌ Request failed: ' + (xhr.responseJSON ? xhr.responseJSON.detail : xhr.statusText));
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fa fa-upload"></i> Upload MVC to Dummy (' + server + ')');
        }
    });
}

// ── Auto Create Subdomain ─────────────────────────────────────────────────────

// ── Create in cPanel ─────────────────────────────────────────────────────────

var cPanelDomains = {
    hostgator: 'ourschoolerp.com',
    myschools:  'myschoolserp.com',
    godaddy:    'ourcollegeerp.com',
};

function openCpanelCreateModal() {
    var server = $('#server_filter').val().toLowerCase();
    if (!cPanelDomains[server]) {
        alert('cPanel subdomain creation is only available for HostGator, MySchools, and GoDaddy.\nSelect one of those servers first.');
        return;
    }
    $('#cpanel_create_server_label').text(server);
    $('#cpanel_create_name').val('');
    $('#cpanel_create_preview').text('Will create: (enter name above)');
    $('#cpanel_create_result').hide().html('');
    $('#cpanel_create_submit_btn').prop('disabled', false).html('<i class="fa fa-plus"></i> Create Subdomain');
    $('#cpanelCreateModal').modal('show');
    setTimeout(function() { $('#cpanel_create_name').focus(); }, 500);
}

$(document).on('input', '#cpanel_create_name', function() {
    var name   = $(this).val().trim().toLowerCase();
    var server = $('#server_filter').val().toLowerCase();
    var domain = cPanelDomains[server] || '';
    $('#cpanel_create_preview').text(name ? 'Will create: ' + name + '.' + domain : 'Enter a subdomain name above');
});

function submitCpanelCreate() {
    var name   = $('#cpanel_create_name').val().trim().toLowerCase();
    var server = $('#server_filter').val().toLowerCase();
    if (!name) { alert('Please enter a subdomain name.'); return; }

    var $btn = $('#cpanel_create_submit_btn');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');
    $('#cpanel_create_result').hide().html('');

    $.ajax({
        url: '<?=base_url("subdomains/create_cpanel_subdomain")?>',
        type: 'POST',
        dataType: 'json',
        data: { server: server, subdomain: name },
        success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-plus"></i> Create Subdomain');
            var cls  = res.success ? 'alert-success' : 'alert-danger';
            var html = '<div class="alert ' + cls + '" style="font-size:13px;margin:0 0 8px;">' +
                       '<i class="fa fa-' + (res.success ? 'check-circle' : 'times-circle') + '"></i> ' +
                       res.message + '</div>';
            if (res.success) {
                html += '<table class="table table-condensed table-bordered" style="font-size:12px;margin:0;">' +
                    '<tr><th style="width:35%">Domain</th><td><strong>' + res.full_domain + '</strong></td></tr>' +
                    '<tr><th>DB Name</th><td>' + res.db_name + '</td></tr>' +
                    '<tr><th>DB User</th><td>' + res.db_user + '</td></tr>' +
                    '<tr><th>DB Password</th><td><code style="background:#f5f5f5;padding:2px 4px;">' + res.db_password + '</code></td></tr>' +
                    '<tr><th>DB Host</th><td>' + res.db_host + '</td></tr>' +
                    '</table>';
                if (res.steps && res.steps.record) {
                    html += '<p style="font-size:11px;color:#27ae60;margin:6px 0 0;"><i class="fa fa-check"></i> ' + res.steps.record + '</p>';
                }
                if (res.steps && res.steps.sql_import) {
                    var sqlOk = res.steps.sql_import.indexOf('imported') === 0;
                    var sqlColor = sqlOk ? '#27ae60' : '#e67e22';
                    var sqlIcon  = sqlOk ? 'database' : 'exclamation-triangle';
                    html += '<p style="font-size:11px;color:' + sqlColor + ';margin:3px 0 0;"><i class="fa fa-' + sqlIcon + '"></i> SQL: ' + res.steps.sql_import + '</p>';
                }
                if (res.steps && res.steps.full_deploy) {
                    var fdMsg  = res.steps.full_deploy;
                    var fdOk   = fdMsg.toLowerCase().indexOf('success') !== -1 || fdMsg.toLowerCase().indexOf('extracted') !== -1;
                    var fdColor = fdOk ? '#27ae60' : '#e67e22';
                    var fdIcon  = fdOk ? 'archive' : 'exclamation-triangle';
                    html += '<p style="font-size:11px;color:' + fdColor + ';margin:3px 0 0;"><i class="fa fa-' + fdIcon + '"></i> Deploy: ' + fdMsg + '</p>';
                }
                setTimeout(function() {
                    $('#cpanelCreateModal').modal('hide');
                    $('#subdomains-table').DataTable().ajax.reload();
                }, 4000);
            }
            $('#cpanel_create_result').html(html).show();
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fa fa-plus"></i> Create Subdomain');
            $('#cpanel_create_result').html('<div class="alert alert-danger" style="font-size:13px;margin:0;">Request failed: ' + xhr.statusText + '</div>').show();
        }
    });
}

function openAutoCreateModal() {
    var server = $('#server_filter').val();
    if (!server) { alert('Please select a server first.'); return; }
    $('#auto_create_server_label').text(server);
    $('#auto_create_url').val('');
    $('#auto_create_result').hide().html('');
    $('#auto_create_submit_btn').prop('disabled', false).html('<i class="fa fa-magic"></i> Fetch &amp; Create');
    $('#autoCreateModal').modal('show');
    setTimeout(function() { $('#auto_create_url').focus(); }, 500);
}

function submitAutoCreate() {
    var url    = $.trim($('#auto_create_url').val());
    var server = $('#server_filter').val();

    if (!url) { alert('Please enter a URL.'); return; }
    if (!server) { alert('Please select a server.'); return; }

    var $btn = $('#auto_create_submit_btn');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Fetching...');
    $('#auto_create_result').hide().html('');

    $.ajax({
        url: '<?=base_url("subdomains/auto_create_subdomain")?>',
        type: 'POST',
        dataType: 'json',
        data: { url: url, server: server },
        success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-magic"></i> Fetch &amp; Create');
            var cls  = res.success ? 'alert-success' : 'alert-danger';
            var icon = res.success ? 'check-circle' : 'times-circle';
            var html = '<div class="alert ' + cls + '" style="font-size:13px;margin:0;">' +
                       '<i class="fa fa-' + icon + '"></i> ' + res.message + '</div>';
            if (res.success && res.data) {
                html += '<table class="table table-condensed" style="font-size:12px;margin:10px 0 0;">' +
                    '<tr><th>Subdomain</th><td>' + res.data.subdomain + '</td></tr>' +
                    '<tr><th>DB Host</th><td>' + res.data.db_host + '</td></tr>' +
                    '<tr><th>DB Name</th><td>' + res.data.db_name + '</td></tr>' +
                    '<tr><th>DB User</th><td>' + res.data.db_user + '</td></tr>' +
                    '</table>';
            }
            $('#auto_create_result').html(html).show();
            if (res.success) {
                setTimeout(function() {
                    $('#autoCreateModal').modal('hide');
                    $('#subdomains-table').DataTable().ajax.reload();
                }, 2000);
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fa fa-magic"></i> Fetch &amp; Create');
            $('#auto_create_result').html('<div class="alert alert-danger" style="font-size:13px;margin:0;">Request failed: ' + xhr.statusText + '</div>').show();
        }
    });
}

// ── Deploy MVC (Bulk) ─────────────────────────────────────────────────────────

function bulkDeployMvc() {
    var ids    = Array.from(selectedIds);
    var server = $('#server_filter').val();

    if (ids.length === 0 && !server) {
        alert('Please check subdomains or select a server first.');
        return;
    }

    var label = ids.length > 0
        ? ids.length + ' selected subdomain(s)'
        : 'ALL active subdomains on ' + server + ' server';

    if (!confirm('Deploy MVC to ' + label + '?\n\n⚠ This will rename mvc → mvc1, upload new code, and copy database.php on each selected server.\n\nThis cannot be undone easily. Are you sure?')) return;

    var btn = $('#bulk_deploy_btn');
    var originalHtml = btn.html();
    btn.html('<i class="fa fa-spinner fa-spin"></i> Deploying...').prop('disabled', true);

    var payload = ids.length > 0 ? { subdomain_ids: ids } : { server: server };

    $.ajax({
        url: 'http://localhost:8000/deploy-mvc-bulk',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        dataType: 'json',
        success: function(response) {
            btn.html(originalHtml).prop('disabled', false);
            var msg = 'MVC Deploy Complete\n';
            msg += 'Success: ' + response.success_count + ' / ' + response.total + '\n\n';
            if (response.details && response.details.length) {
                response.details.forEach(function(d) {
                    msg += (d.success ? '✓ ' : '✗ ') + d.subdomain + ': ' + d.message + '\n';
                });
            }
            alert(msg);
        },
        error: function(xhr) {
            btn.html(originalHtml).prop('disabled', false);
            var detail = xhr.responseJSON ? xhr.responseJSON.detail : 'Unknown error';
            alert('Bulk Deploy failed:\n' + detail);
        }
    });
}

// ── FTP Upload File ───────────────────────────────────────────────────────────

function openFtpUploadModal() {
    var server = $('#server_filter').val();
    if (!server) { alert('Please select a server first.'); return; }
    $('#ftp_upload_server_label').text(server);
    $('#ftp_upload_path').val('');
    $('#ftp_upload_result').hide().html('');
    $('#ftp_upload_submit_btn').prop('disabled', false).html('<i class="fa fa-upload"></i> Upload to All');
    $('#ftpUploadModal').modal('show');
    setTimeout(function() { $('#ftp_upload_path').focus(); }, 400);
}

function submitFtpUpload() {
    var path   = $.trim($('#ftp_upload_path').val());
    var server = $('#server_filter').val();
    if (!path) { alert('Please enter a file path.'); return; }
    var $btn = $('#ftp_upload_submit_btn');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
    $('#ftp_upload_result').hide().html('');
    $.ajax({
        url: '<?=base_url("subdomains/ftp_upload_file")?>',
        type: 'POST',
        dataType: 'json',
        data: { server: server, file_path: path },
        timeout: 130000,
        success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-upload"></i> Upload to All');
            var cls  = res.success ? 'alert-success' : 'alert-danger';
            var msg  = res.message !== undefined ? res.message : (res.detail || 'Unknown error');
            var html = '<div class="alert ' + cls + '" style="font-size:12px;margin:0 0 6px;">' +
                       '<i class="fa fa-' + (res.success ? 'check' : 'times') + '-circle"></i> ' +
                       msg + '</div>';
            if (res.results) {
                html += '<table class="table table-condensed table-bordered" style="font-size:11px;margin:0;">';
                $.each(res.results, function(sub, status) {
                    var ok  = status === 'uploaded';
                    var row = ok ? '#e8f5e9' : '#fff3e0';
                    var ico = ok ? 'check' : 'times';
                    html += '<tr style="background:' + row + ';">' +
                            '<td style="padding:3px 6px;">' + sub + '</td>' +
                            '<td style="padding:3px 6px;"><i class="fa fa-' + ico + '"></i> ' + status + '</td></tr>';
                });
                html += '</table>';
            }
            $('#ftp_upload_result').html(html).show();
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fa fa-upload"></i> Upload to All');
            var detail = xhr.responseJSON ? (xhr.responseJSON.detail || xhr.statusText) : xhr.statusText;
            $('#ftp_upload_result').html('<div class="alert alert-danger" style="font-size:12px;margin:0;">Error: ' + detail + '</div>').show();
        }
    });
}

// ── Update CSS (Single) ───────────────────────────────────────────────────────

function updateCss(btn, subdomainId, subdomainName) {
    if (!confirm('Push local inilabs.css to live server for "' + subdomainName + '"?\nThis will overwrite the remote CSS file.')) return;

    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    $(btn).addClass('disabled');

    $.ajax({
        url: 'http://localhost:8000/update-css/' + subdomainId,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Success: ' + response.message);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            var detail = xhr.responseJSON ? xhr.responseJSON.detail : 'Unknown error';
            alert('Request failed. Make sure Python server is running.\n' + detail);
        },
        complete: function() {
            btn.innerHTML = originalHtml;
            $(btn).removeClass('disabled');
        }
    });
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

/* ── Pivot table ───────────────────────────────────── */
#pivot-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.pivot-table {
    font-size: 13px;
    border-collapse: collapse;
    white-space: nowrap;     /* keeps all cells on one line → forces horiz scroll */
    width: auto;
}
.pivot-table thead tr:first-child th { text-align: center; }

/* Fixed left columns (SubDomain + Site Name) */
.pivot-th-fixed {
    background: #2c3e50 !important;
    color: #fff !important;
    vertical-align: middle !important;
    min-width: 110px;
    position: sticky;
    left: 0;
    z-index: 2;
}

/* Year header – odd columns (default blue) */
.pivot-th-year {
    background: #3c8dbc !important;
    color: #fff !important;
    text-align: center;
    font-weight: 700;
    min-width: 80px;
}
/* Year header – even columns (lighter teal) */
.pivot-th-year.pivot-yr-alt {
    background: #1abc9c !important;
}

/* Sub-headers row – odd */
.pivot-th-sub         { font-size: 11px; text-align: center; font-weight: 600; }
.pivot-students       { background: #eaf4fb !important; color: #2471a3; }
.pivot-appusers       { background: #eafaf1 !important; color: #1e8449; }
/* Sub-headers row – even */
.pivot-students.pivot-yr-alt { background: #d5f5ef !important; color: #0e6655; }
.pivot-appusers.pivot-yr-alt { background: #d0ede8 !important; color: #0e6655; }

/* Body cells */
.pivot-subdomain { font-weight:700; color:#2c3e50; }
.pivot-sitename  { color:#555; }
.pivot-cell      { text-align: center; }

/* Odd year pair body cells */
.pivot-cell-students            { background: #fdfefe; color: #1a5276; font-weight: 600; }
.pivot-cell-appusers            { background: #f9fffe; color: #145a32; font-weight: 600; }
/* Even year pair body cells – slightly shaded */
.pivot-cell-students.pivot-yr-alt { background: #e8f8f5; color: #0e6655; font-weight: 600; }
.pivot-cell-appusers.pivot-yr-alt { background: #e3f6f1; color: #0e6655; font-weight: 600; }
.pivot-cell-empty               { color: #ccc; text-align: center; background: #fafafa; }
.pivot-cell-empty.pivot-yr-alt  { background: #f0faf7; }

/* ── Row hover highlight ───────────────────────────── */
#subdomains-table tbody tr.row-clickable:hover {
    background-color: #f0eafc !important;
}

/* ── Row Info modal ────────────────────────────────── */
.rowinfo-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: #fff;
    border-radius: 4px 4px 0 0;
    padding: 12px 18px;
}
.rowinfo-header .close { color:#fff; opacity:.8; font-size:22px; margin-top:-2px; }
.rowinfo-header .modal-title { font-size:16px; font-weight:600; }
.rowinfo-table td { vertical-align: middle !important; font-size: 13px; }
.rowinfo-table .ri-label {
    width: 38%;
    background: #f7f7f7;
    font-weight: 600;
    color: #555;
    white-space: nowrap;
}

/* ── Bootstrap via FTP button ───────────────────────── */
.btn-bootstrap {
    background-color: #546e7a;
    border-color: #37474f;
    color: #fff;
}
.btn-bootstrap:hover,
.btn-bootstrap:focus { background-color: #37474f; border-color: #263238; color: #fff; }
.btn-bootstrap:disabled { background-color: #b0bec5; border-color: #b0bec5; color: #fff; }

/* ── Upload Deploy Script button ───────────────────── */
.btn-upload-script {
    background-color: #6f42c1;
    border-color: #5a32a3;
    color: #fff;
}
.btn-upload-script:hover,
.btn-upload-script:focus { background-color: #5a32a3; border-color: #4a2790; color: #fff; }
.btn-upload-script:disabled { background-color: #c3aff0; border-color: #c3aff0; color: #fff; }

/* ── Action button groups ───────────────────────────── */
.action-group { display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
.btn-group-wrap { display: flex; gap: 2px; }
.btn-group-sep {
    display: inline-block;
    width: 1px;
    height: 28px;
    background: #ccc;
    margin: 0 4px;
    vertical-align: middle;
}
.action-group .btn-sm { margin: 0; }

/* ── Full Deploy button ────────────────────────────── */
.btn-full-deploy { background-color: #e65100; border-color: #bf360c; color: #fff; }
.btn-full-deploy:hover,
.btn-full-deploy:focus { background-color: #bf360c; border-color: #a53000; color: #fff; }
.btn-full-deploy:disabled { background-color: #ffccbc; border-color: #ffccbc; color: #fff; }

/* ── Deploy MVC button ─────────────────────────────── */
.btn-deploy-mvc {
    background-color: #00796b;
    border-color: #00574b;
    color: #fff;
}
.btn-deploy-mvc:hover,
.btn-deploy-mvc:focus {
    background-color: #00574b;
    border-color: #004d40;
    color: #fff;
}
.btn-deploy-mvc:disabled {
    background-color: #80cbc4;
    border-color: #80cbc4;
    color: #fff;
}

/* ── Refresh Schools Age button ────────────────────── */
.btn-refresh-age {
    background-color: #1abc9c;
    border-color: #16a085;
    color: #fff;
}
.btn-refresh-age:hover,
.btn-refresh-age:focus {
    background-color: #16a085;
    border-color: #138d75;
    color: #fff;
}
.btn-refresh-age:disabled {
    background-color: #a8e6da;
    border-color: #a8e6da;
    color: #fff;
    cursor: not-allowed;
}

/* ── Statistics button ─────────────────────────────── */
.btn-statistics {
    background-color: #9b59b6;
    border-color: #8e44ad;
    color: #fff;
}
.btn-statistics:hover,
.btn-statistics:focus {
    background-color: #8e44ad;
    border-color: #7d3c98;
    color: #fff;
}

/* ── Statistics modal ──────────────────────────────── */
.stats-modal-header {
    background: linear-gradient(135deg, #6c3483, #9b59b6);
    color: #fff;
    border-radius: 4px 4px 0 0;
    padding: 14px 20px;
}
.stats-modal-header .close {
    color: #fff;
    opacity: .8;
    font-size: 22px;
    margin-top: -2px;
}
.stats-modal-header .modal-title { font-size: 18px; font-weight: 600; }

/* Summary bar */
.stats-summary-bar {
    display: flex;
    justify-content: space-around;
    background: #f8f4fc;
    border-radius: 8px;
    padding: 18px 10px;
    margin-bottom: 22px;
    border: 1px solid #e0d0f0;
}
.stats-summary-item { text-align: center; flex: 1; }
.stats-summary-value { font-size: 28px; font-weight: 700; color: #9b59b6; line-height: 1; }
.stats-summary-label { font-size: 12px; color: #888; margin-top: 4px; text-transform: uppercase; letter-spacing: .5px; }

/* Horizontal year scroll */
.stats-years-wrapper {
    overflow-x: auto;
    padding-bottom: 8px;
}
.stats-years-container {
    display: flex;
    gap: 14px;
    min-width: max-content;
    padding: 4px 2px 10px;
}
.stat-year-card {
    min-width: 160px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 16px 14px 14px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,.07);
    transition: transform .15s, box-shadow .15s;
}
.stat-year-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(155,89,182,.2);
}
.stat-year-card .year-title {
    font-size: 13px;
    font-weight: 700;
    color: #6c3483;
    margin-bottom: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.stat-year-card .year-stat { margin-bottom: 8px; }
.stat-year-card .year-stat-value {
    font-size: 26px;
    font-weight: 700;
    line-height: 1;
}
.stat-year-card .year-stat-label {
    font-size: 11px;
    color: #999;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.stat-year-card .app-bar-wrap {
    margin-top: 10px;
    background: #f0e8f8;
    border-radius: 6px;
    height: 8px;
    overflow: hidden;
}
.stat-year-card .app-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #27ae60, #2ecc71);
    border-radius: 6px;
    transition: width .4s ease;
}
.stat-year-card .app-pct-label {
    font-size: 11px;
    color: #27ae60;
    font-weight: 600;
    margin-top: 4px;
}

/* ── FTP Upload File button ────────────────────────── */
.btn-ftp-upload { background-color: #37474f; border-color: #263238; color: #fff; }
.btn-ftp-upload:hover,
.btn-ftp-upload:focus { background-color: #263238; border-color: #1a2226; color: #fff; }
.btn-ftp-upload:disabled { background-color: #b0bec5; border-color: #b0bec5; color: #fff; cursor: not-allowed; }
</style>

<script type="text/javascript">
// ── School Year Analysis Pivot Table ─────────────────────────────────────────

function loadSchoolAgeAnalysis() {
    document.getElementById('pivot-loading').style.display    = 'block';
    document.getElementById('pivot-empty').style.display      = 'none';
    document.getElementById('pivot-error').style.display      = 'none';
    document.getElementById('pivot-table-wrap').style.display = 'none';

    $.ajax({
        url:      '<?php echo base_url("subdomains/ajax_school_age_info"); ?>',
        type:     'GET',
        dataType: 'json',
        success: function(res) {
            document.getElementById('pivot-loading').style.display = 'none';

            if (!res.years || res.years.length === 0 || !res.subdomains || res.subdomains.length === 0) {
                document.getElementById('pivot-empty').style.display = 'block';
                return;
            }

            var years = res.years;
            var subs  = res.subdomains;

            // ── Build pivot table ──────────────────────────────────────────
            // Even-indexed year pairs get an alternating background class
            var html = '<table class="table table-bordered table-condensed pivot-table">';

            // Header row 1 – year labels
            html += '<thead><tr>';
            html += '<th rowspan="2" class="pivot-th-fixed pivot-th-subdomain">SubDomain</th>';
            html += '<th rowspan="2" class="pivot-th-fixed pivot-th-subdomain">Site Name</th>';
            years.forEach(function(yr, i) {
                var alt = (i % 2 === 1) ? ' pivot-yr-alt' : '';
                html += '<th colspan="2" class="pivot-th-year' + alt + '">' + yr + '</th>';
            });
            html += '</tr>';

            // Header row 2 – Students / App Users sub-headers
            html += '<tr>';
            years.forEach(function(yr, i) {
                var alt = (i % 2 === 1) ? ' pivot-yr-alt' : '';
                html += '<th class="pivot-th-sub pivot-students' + alt + '">Students</th>';
                html += '<th class="pivot-th-sub pivot-appusers' + alt + '">App Users</th>';
            });
            html += '</tr></thead>';

            // Body rows
            html += '<tbody>';
            subs.forEach(function(sub) {
                html += '<tr>';
                html += '<td class="pivot-subdomain">' + sub.subdomain + '</td>';
                html += '<td class="pivot-sitename">'  + (sub.site_name || sub.subdomain) + '</td>';
                years.forEach(function(yr, i) {
                    var alt = (i % 2 === 1) ? ' pivot-yr-alt' : '';
                    if (sub.data && sub.data[yr]) {
                        var d = sub.data[yr];
                        html += '<td class="pivot-cell pivot-cell-students' + alt + '">' + (d.students  || 0).toLocaleString() + '</td>';
                        html += '<td class="pivot-cell pivot-cell-appusers' + alt + '">' + (d.app_users || 0).toLocaleString() + '</td>';
                    } else {
                        html += '<td class="pivot-cell pivot-cell-empty' + alt + '">—</td>';
                        html += '<td class="pivot-cell pivot-cell-empty' + alt + '">—</td>';
                    }
                });
                html += '</tr>';
            });
            html += '</tbody></table>';

            document.getElementById('pivot-table-wrap').innerHTML      = html;
            document.getElementById('pivot-table-wrap').style.display  = 'block';
        },
        error: function(xhr) {
            document.getElementById('pivot-loading').style.display = 'none';
            var msg = 'Failed to load analysis data.';
            if (xhr.responseJSON && xhr.responseJSON.detail) msg = xhr.responseJSON.detail;
            document.getElementById('pivot-error-msg').textContent = msg;
            document.getElementById('pivot-error').style.display   = 'block';
        }
    });
}

// ── Row Info Popup ────────────────────────────────────────────────────────────

function showRowInfo(d) {
    function set(id, val) {
        var el = document.getElementById(id);
        if (el) el.innerHTML = val !== undefined && val !== null && val !== '' ? val : '<span class="text-muted">—</span>';
    }

    document.getElementById('ri-title').textContent = (d['_site_name'] || d['subdomain']) + '  (' + d['server'] + ')';

    set('ri-subdomain',       d['subdomain']);
    set('ri-site_name',       d['_site_name']);
    set('ri-server',          d['server']);
    set('ri-main_domain',     d['_main_domain']);
    set('ri-db_host',         d['_db_host']);
    set('ri-db_name',         d['db_name']);
    set('ri-db_user',         d['_db_user']);
    set('ri-logo_url',        d['_logo_url']);
    set('ri-theme_color',
        d['_theme_color']
            ? '<span style="display:inline-block;width:16px;height:16px;background:' + d['_theme_color'] + ';border:1px solid #ccc;border-radius:3px;vertical-align:middle;margin-right:6px;"></span>' + d['_theme_color']
            : '');
    set('ri-status',
        '<span class="badge badge-' + (d['_status'] === 'active' ? 'success' : 'danger') + '">' + d['_status'] + '</span>');
    set('ri-school_age',
        d['school_age'] !== '—' ? d['school_age'] + ' year(s)' : '—');
    set('ri-total_students',  d['total_students']);
    set('ri-total_app_users', d['total_app_users']);
    set('ri-created_at',      d['_created_at']);

    $('#rowInfoModal').modal('show');
}

// ── Refresh Schools Age ───────────────────────────────────────────────────────

function refreshSchoolsAge() {
    var server = $('#server_filter').val();
    if (!server) {
        alert('Please select a server first.');
        return;
    }

    if (!confirm('Refresh school age, total students and app users for ALL active "' + server + '" domains?\n\nThis will connect to each tenant database and update subdomain_settings.')) {
        return;
    }

    var btn = document.getElementById('refresh_age_btn');
    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Refreshing...';
    btn.disabled  = true;

    $.ajax({
        url:  'http://localhost:8000/refresh-schools-age?server=' + encodeURIComponent(server),
        type: 'POST',
        success: function(response) {
            btn.innerHTML = originalHtml;
            btn.disabled  = false;

            var lines = ['Done! ' + response.success_count + '/' + response.domains_processed + ' domains updated.\n'];
            if (response.details && response.details.length) {
                response.details.forEach(function(d) {
                    if (d.success) {
                        lines.push('✓ ' + d.subdomain + ' — age: ' + d.school_age + ' yr(s), students: ' + d.total_students + ', app users: ' + d.total_app_users);
                    } else {
                        lines.push('✗ ' + d.subdomain + ' — ' + d.message);
                    }
                });
            }
            alert(lines.join('\n'));

            // Reload DataTable + pivot table with fresh data
            $('#subdomains-table').DataTable().draw();
            loadSchoolAgeAnalysis();
        },
        error: function(xhr) {
            btn.innerHTML = originalHtml;
            btn.disabled  = false;
            var msg = 'Error refreshing schools age.';
            if (xhr.responseJSON && xhr.responseJSON.detail) {
                msg = xhr.responseJSON.detail;
            }
            alert('Error: ' + msg);
        }
    });
}

// ── Statistics ────────────────────────────────────────────────────────────────

function showStatistics(subdomainId, siteName) {
    // Reset modal state
    document.getElementById('stats-site-name').textContent = siteName;
    document.getElementById('stats-loading').style.display  = 'block';
    document.getElementById('stats-error').style.display    = 'none';
    document.getElementById('stats-content').style.display  = 'none';
    document.getElementById('stats-years-container').innerHTML = '';

    $('#statisticsModal').modal('show');

    $.ajax({
        url:  'http://localhost:8000/statistics/' + subdomainId,
        type: 'GET',
        success: function(data) {
            document.getElementById('stats-loading').style.display = 'none';

            // Summary totals
            document.getElementById('stat-total-students').textContent = data.total_students.toLocaleString();
            document.getElementById('stat-total-app').textContent      = data.total_app_users.toLocaleString();
            document.getElementById('stat-overall-pct').textContent    = data.overall_app_percentage + '%';
            document.getElementById('stat-total-years').textContent    = data.years.length;

            // Device-token warning
            var notice = document.getElementById('stats-no-token-notice');
            notice.style.display = data.has_device_token_column ? 'none' : 'block';

            // Build year cards
            var container = document.getElementById('stats-years-container');
            if (data.years.length === 0) {
                container.innerHTML = '<p class="text-muted" style="padding:20px;">No academic years found in this tenant database.</p>';
            } else {
                data.years.forEach(function(yr) {
                    var pct = yr.app_percentage;
                    var card = document.createElement('div');
                    card.className = 'stat-year-card';
                    card.innerHTML =
                        '<div class="year-title" title="' + yr.year_label + '">' + yr.year_label + '</div>' +
                        '<div class="year-stat">' +
                            '<div class="year-stat-value" style="color:#2c3e50;">' + yr.total_students.toLocaleString() + '</div>' +
                            '<div class="year-stat-label">Students</div>' +
                        '</div>' +
                        '<div class="year-stat">' +
                            '<div class="year-stat-value" style="color:#27ae60;">' + yr.app_users.toLocaleString() + '</div>' +
                            '<div class="year-stat-label">App Users</div>' +
                        '</div>' +
                        '<div class="app-bar-wrap">' +
                            '<div class="app-bar-fill" style="width:' + pct + '%;"></div>' +
                        '</div>' +
                        '<div class="app-pct-label">' + pct + '% app adoption</div>';
                    container.appendChild(card);
                });
            }

            document.getElementById('stats-content').style.display = 'block';
        },
        error: function(xhr) {
            document.getElementById('stats-loading').style.display = 'none';
            var msg = 'Failed to fetch statistics.';
            if (xhr.responseJSON && xhr.responseJSON.detail) {
                msg = xhr.responseJSON.detail;
            }
            document.getElementById('stats-error-msg').textContent = msg;
            document.getElementById('stats-error').style.display   = 'block';
        }
    });
}
</script>