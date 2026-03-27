<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admission Enquiry - <?=$admission_enquiry->name?></title>
    <style type="text/css">
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.4; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #1a73e8; padding-bottom: 10px; }
        .school-info h2 { margin: 0; color: #1a73e8; font-size: 20px; text-transform: uppercase; }
        .school-info p { margin: 2px 0; font-size: 12px; color: #666; }
        .report-title { background: #f1f3f4; padding: 6px; margin: 10px 0; font-weight: bold; text-align: center; font-size: 15px; border-radius: 4px; }
        
        .section-header { background: #1a73e8; color: white; padding: 5px 12px; font-weight: bold; margin-top: 15px; border-radius: 4px; font-size: 13px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table th, table td { padding: 6px 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 12px; }
        table th { width: 30%; color: #555; background-color: #fafafa; font-weight: 600; }
        table td { color: #222; }
        
        .footer { margin-top: 20px; font-size: 11px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
        
        @media print {
            body { padding: 0; }
            .btn-print { display: none; }
            @page { margin: 1cm; }
        }
        
        .btn-print {
            position: fixed; top: 20px; right: 20px; background: #1a73e8; color: white; border: none; padding: 10px 20px; 
            border-radius: 4px; cursor: pointer; font-weight: bold; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .btn-print:hover { background: #1557b0; }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">Print Report</button>

    <style type="text/css">
        .school-logo { float: left; margin-right: 15px; }
        .school-logo img { width: 50px; height: 50px; border-radius: 4px; }
        .school-info { overflow: hidden; }
    </style>
    <div class="header">
        <div class="school-logo">
            <img src="<?=base_url("uploads/images/$siteinfos->photo")?>" alt="Logo">
        </div>
        <div class="school-info">
            <h2><?=strtoupper($siteinfos->sname)?></h2>
            <p><?=$siteinfos->address?></p>
            <p>Phone: <?=$siteinfos->phone?> | Email: <?=$siteinfos->email?></p>
        </div>
    </div>

    <div class="report-title">ADMISSION ENQUIRY REPORT - (<?=$schoolyearsessionobj->schoolyear?>)</div>

    <div class="section-header">ENQUIRER PERSONAL INFORMATION</div>
    <table>
        <tr>
            <th>Full Name</th>
            <td><?=$admission_enquiry->name?></td>
        </tr>
        <tr>
            <th>Phone Number</th>
            <td><?=$admission_enquiry->phone?></td>
        </tr>
        <tr>
            <th>Email Address</th>
            <td><?=$admission_enquiry->email ? $admission_enquiry->email : 'N/A'?></td>
        </tr>
        <tr>
            <th>Current Address</th>
            <td><?=nl2br($admission_enquiry->address)?></td>
        </tr>
    </table>

    <div class="section-header">INQUIRY DETAILS</div>
    <table>
        <tr>
            <th>Interested Class</th>
            <td><?=isset($class->classes) ? $class->classes : 'N/A'?></td>
        </tr>
        <tr>
            <th>Number of Children</th>
            <td><?=$admission_enquiry->num_child ? $admission_enquiry->num_child : '0'?></td>
        </tr>
        <tr>
            <th>Inquiry Description</th>
            <td><?=nl2br($admission_enquiry->description)?></td>
        </tr>
        <tr>
            <th>Fee Particulars Shared</th>
            <td><?=nl2br($admission_enquiry->fee_particulars)?></td>
        </tr>
    </table>

    <div class="section-header">FOLLOW-UP & ASSIGNMENT</div>
    <table>
        <tr>
            <th>Enquiry Date</th>
            <td><?=($admission_enquiry->date != '0000-00-00') ? date("d M Y", strtotime($admission_enquiry->date)) : 'N/A'?></td>
        </tr>
        <tr>
            <th>Next Follow-up Date</th>
            <td><?=($admission_enquiry->next_follow_up_date != '0000-00-00') ? date("d M Y", strtotime($admission_enquiry->next_follow_up_date)) : 'N/A'?></td>
        </tr>
        <tr>
            <th>Enquiry Source</th>
            <td><?=$admission_enquiry->source?></td>
        </tr>
        <tr>
            <th>Assigned To</th>
            <td>
                <?php 
                $usertype = isset($usertypes[$admission_enquiry->assigned_usertypeID]) ? $usertypes[$admission_enquiry->assigned_usertypeID] : '';
                echo $assigned_user . ($usertype ? " ($usertype)" : "");
                ?>
            </td>
        </tr>
        <tr>
            <th>Office Notes</th>
            <td><?=nl2br($admission_enquiry->note)?></td>
        </tr>
    </table>

    <div class="footer">
        <p>This is a computer generated document. Printed on <?=date("d M Y h:i A")?></p>
        <p>&copy; <?=date("Y")?> <?=$siteinfos->sname?>. All Rights Reserved.</p>
    </div>

<script type="text/javascript">
    window.onload = function() {
        // window.print(); // Optional: uncomment if you want auto-print
    }
</script>
</body>
</html>
