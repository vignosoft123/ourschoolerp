# Report Instructions: OurSchoolERP

> [!IMPORTANT]
> **AI Assistant:** Read this before any report work. Follow all patterns exactly.

---

## 1. Report Inventory

| Report | Controller | View Folder |
|---|---|---|
| Admit Card | `Admitcardreport.php` | `report/admitcard/` |
| Progress Card | `Progresscardreport.php` | `report/progresscard/` |
| Marksheet | `Marksheetreport.php` | `report/marksheet/` |
| Tabulation Sheet | `Tabulationsheetreport.php` | `report/tabulationsheet/` |
| Exam Schedule | `Examschedulereport.php` | `report/examschedule/` |
| Attendance | `Attendancereport.php` | `report/attendance/` |
| Attendance Overview | `Attendanceoverviewreport.php` | `report/attendanceoverview/` |
| Fees | `Feesreport.php` | `report/fees/` |
| Due Fees | `Duefeesreport.php` | `report/duefees/` |
| Balance Fees | `Balancefeesreport.php` | `report/balancefees/` |
| ID Card | `Idcardreport.php` | `report/idcard/` |
| Certificate | `Certificatereport.php` | `report/certificate/` |
| Study Certificate | `Studycertificatereport.php` | `report/studycertificatereport/` |
| Salary | `Salaryreport.php` | `report/salary/` |
| Leave Application | `Leaveapplicationreport.php` | `report/leaveapplication/` |

---

## 2. File Structure (Every Report)

```
mvc/controllers/{Name}report.php
mvc/views/report/{name}/
    {Name}ReportView.php   ← filter form + cascading dropdowns + JS
    {Name}Report.php       ← AJAX-rendered HTML output
    {Name}ReportPDF.php    ← PDF layout
mvc/language/english/{name}report_lang.php
```

---

## 3. Controller Skeleton

```php
class Xyzreport extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('classes_m');
        $this->load->model('section_m');
        $this->load->model('studentrelation_m');
        $language = $this->session->userdata('lang');
        $this->lang->load('xyzreport', $language);
    }

    protected function rules() { /* form_validation rules array */ }

    public function index() {
        $this->data['headerassets'] = [
            'css' => ['assets/select2/css/select2.css','assets/select2/css/select2-bootstrap.css'],
            'js'  => ['assets/select2/select2.js']
        ];
        $this->data['classes'] = $this->classes_m->general_get_classes();
        $this->data['subview'] = 'report/xyz/XyzReportView';
        $this->load->view('_layout_main', $this->data);
    }

    // --- Cascading dropdown AJAX methods ---
    public function getSection() {
        $classesID = $this->input->post('classesID');
        if ((int)$classesID) {
            $sections = $this->section_m->general_get_order_by_section(['classesID'=>$classesID]);
            echo "<option value='0'>Please Select</option>";
            if (customCompute($sections))
                foreach ($sections as $s)
                    echo "<option value='{$s->sectionID}'>{$s->section}</option>";
        }
    }

    public function getStudent() {
        $classesID = $this->input->post('classesID');
        $sectionID = $this->input->post('sectionID');
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        if ((int)$classesID && (int)$sectionID) {
            $students = $this->studentrelation_m->general_get_order_by_student([
                'srclassesID'=>$classesID,'srsectionID'=>$sectionID,'srschoolyearID'=>$schoolyearID
            ]);
            echo "<option value='0'>Please Select</option>";
            if (customCompute($students))
                foreach ($students as $s)
                    echo "<option value='{$s->srstudentID}'>{$s->srname} (Roll: {$s->roll})</option>";
        }
    }

    public function getExam() {   // only for exam-based reports
        $classesID = $this->input->post('classesID');
        echo "<option value='0'>Please Select</option>";
        if ((int)$classesID) {
            $exams = pluck($this->marksetting_m->get_exam($this->data['siteinfos']->marktypeID, $classesID),'obj','examID');
            if (customCompute($exams))
                foreach ($exams as $e)
                    echo "<option value='{$e->examID}'>{$e->exam}</option>";
        }
    }

    // --- Main report AJAX method ---
    public function getXyzReport() {
        $retArray = ['status'=>FALSE,'render'=>''];
        if (permissionChecker('xyzreport')) {
            if ($_POST) {
                $this->form_validation->set_rules($this->rules());
                if ($this->form_validation->run() == FALSE) {
                    $retArray = $this->form_validation->error_array();
                    $retArray['status'] = FALSE;
                } else {
                    $schoolyearID = $this->session->userdata('defaultschoolyearID');
                    // ... build queryArray, fetch data, assign $this->data ...
                    $retArray['render'] = $this->load->view('report/xyz/XyzReport', $this->data, true);
                    $retArray['status'] = TRUE;
                }
            }
        } else {
            $retArray['render'] = $this->load->view('report/reporterror', $this->data, true);
            $retArray['status'] = TRUE;
        }
        echo json_encode($retArray); exit;
    }

    public function pdf() {
        // read URI segments → build $posts → call $this->reportPDF('xyz.css', $this->data, 'report/xyz/XyzReportPDF');
    }

    public function send_pdf_to_mail() {
        // read POST → call $this->reportSendToMail('xyz.css', $this->data, 'report/xyz/XyzReportPDF', $to, $subject, $msg);
    }

    public function unique_data($data) {
        if ($data === "0") { $this->form_validation->set_message('unique_data','The %s field is required.'); return FALSE; }
        return TRUE;
    }
}
```

---

## 4. Cascading Dropdown Chain

**Load order:** Class (pre-loaded) → Section (AJAX) → Student (AJAX). Exam / Subject also fire on Class change.

| Dropdown | Trigger | Controller Method | POST params |
|---|---|---|---|
| Class | pre-loaded | — | — |
| Section | `#classesID` change | `getSection` | `classesID` |
| Student | `#sectionID` change | `getStudent` | `classesID`, `sectionID` |
| Exam | `#classesID` change | `getExam` | `classesID` |
| Subject | `#classesID` change | `getSubject` | `classID` |
| Fee Type | pre-loaded | — | — |
| Date | datepicker | — | — |

### View HTML (all dropdowns)

```php
<!-- Class — pre-loaded -->
<?php
$arr = ["0"=>"Please Select"];
if(customCompute($classes)) foreach($classes as $c) $arr[$c->classesID]=$c->classes;
echo form_dropdown("classesID",$arr,set_value("classesID"),"id='classesID' class='form-control select2'");
?>

<!-- Section — empty, filled by AJAX -->
<?php echo form_dropdown("sectionID",["0"=>"Please Select"],set_value("sectionID"),"id='sectionID' class='form-control select2'"); ?>

<!-- Student — empty, filled by AJAX (single) -->
<?php echo form_dropdown("studentID",["0"=>"Please Select"],set_value("studentID"),"id='studentID' class='form-control select2'"); ?>

<!-- Student — multi-select -->
<?php echo form_dropdown("studentID[]",["0"=>"Please Select"],set_value("studentID"),"id='studentID' class='form-control select2' multiple"); ?>

<!-- Exam — empty, filled by AJAX -->
<?php echo form_dropdown("examID",["0"=>"Please Select"],set_value("examID"),"id='examID' class='form-control select2'"); ?>

<!-- Fee Type — pre-loaded -->
<?php
$arr = ["0"=>"Please Select"];
if(customCompute($feetypes)) foreach($feetypes as $f) $arr[$f->feetypesID]=$f->feetypes;
echo form_dropdown("feetypeID",$arr,set_value("feetypeID"),"id='feetypeID' class='form-control select2'");
?>

<!-- Date pickers -->
<input type="text" name="fromdate" id="fromdate" class="form-control">
<input type="text" name="todate"   id="todate"   class="form-control">
```

### View JS (complete standard block)

```js
$('.select2').select2();

$(function() {
    $('#sectionDiv').hide();
    $('#studentDiv').hide();

    // Date pickers (fee/attendance reports)
    $('#fromdate,#todate').datepicker({
        autoclose:true, format:'dd-mm-yyyy',
        startDate:'<?=$schoolyearsessionobj->startingdate?>',
        endDate:'<?=$schoolyearsessionobj->endingdate?>'
    });
});

// Class change → load Section + Exam (clear children on 0)
$(document).on('change','#classesID',function(){
    var id=$(this).val();
    if(id=='0'){
        $('#sectionID').html('<option value="0">Please Select</option>');
        $('#studentID').html('<option value="0">Please Select</option>');
        $('#examID').html('<option value="0">Please Select</option>');
        $('#sectionDiv').hide('slow'); $('#studentDiv').hide('slow');
    } else {
        $('#sectionDiv').show('slow');
        $.ajax({type:'POST',url:"<?=base_url('xyzreport/getSection')?>",
            data:{classesID:id},dataType:"html",
            success:function(d){$('#sectionID').html(d);}});
        $.ajax({type:'POST',url:"<?=base_url('xyzreport/getExam')?>",
            data:{classesID:id},dataType:"html",
            success:function(d){$('#examID').html(d);}});
    }
});

// Section change → load Student
$(document).on('change','#sectionID',function(){
    var sid=$(this).val(), cid=$('#classesID').val();
    if(sid=='0'){
        $('#studentID').html('<option value="0">Please Select</option>');
    } else {
        $('#studentDiv').show('slow');
        $.ajax({type:'POST',url:"<?=base_url('xyzreport/getStudent')?>",
            data:{classesID:cid,sectionID:sid},dataType:"html",
            success:function(d){$('#studentID').html(d);}});
    }
});

// Submit button → validate → AJAX
$(document).on('click','#get_xyzreport',function(){
    var error=0;
    var field={
        classesID:$('#classesID').val(),
        sectionID:$('#sectionID').val(),
        studentID:$('#studentID').val()||[],
        examID:$('#examID').val(),
        // fromdate, todate, feetypeID as needed
    };
    // Validate required fields
    if(field.classesID==0){$('#classesDiv').addClass('has-error');error++;}
    else{$('#classesDiv').removeClass('has-error');}
    // add more field checks as needed...

    // From/To date pair validation
    if(field.fromdate && !field.todate){$('#todateDiv').addClass('has-error');error++;}
    if(!field.fromdate && field.todate){$('#fromdateDiv').addClass('has-error');error++;}

    if(error==0) makingPostDataPreviousofAjaxCall(field);
});

function makingPostDataPreviousofAjaxCall(field){passData=field;ajaxCall(passData);}

function ajaxCall(passData){
    $.ajax({type:'POST',url:"<?=base_url('xyzreport/getXyzReport')?>",
        data:passData,dataType:"html",
        success:function(data){renderLoder(JSON.parse(data),passData);}});
}

function renderLoder(response,passData){
    if(response.status){
        $('#load_xyzreport').html(response.render);
        for(var k in passData) $('#'+k).parent().removeClass('has-error');
    } else {
        for(var k in passData) $('#'+k).parent().removeClass('has-error');
        for(var k in response) $('#'+k).parent().addClass('has-error');
    }
}
```

> Result container: `<div id="load_xyzreport"></div>` placed after the filter box.

---

## 5. Data & Query Rules

- `$schoolyearID = $this->session->userdata('defaultschoolyearID')` — always from session.
- Always `ORDER BY` date/sequence columns: e.g. `ORDER BY examschedule.edate ASC`.
- Use `pluck($result,'obj','keyField')` to make associative arrays for view lookup.
- Use `customCompute($array)` before every `foreach`.
- **Reorder plucked arrays** in controller when display order must match date order (see below).

### Reorder-by-Date (Admit Card pattern)
```php
$this->data['examScheduleReports'] = $this->subject_m->general_get_order_by_subject_with_exam($queryArray);
$ordered = [];
foreach ($this->data['examScheduleReports'] as $item) {
    $sid = $item->subjectID;
    if (isset($this->data['subjects'][$sid])) $ordered[$sid] = $this->data['subjects'][$sid];
}
foreach ($this->data['subjects'] as $sid => $sub) {
    if (!isset($ordered[$sid])) $ordered[$sid] = $sub;
}
$this->data['subjects'] = $ordered;
```

### Multi-Student Selection
```php
$studentIDs = $this->input->post('studentID');
if (is_array($studentIDs)) {
    $studentIDs = array_filter($studentIDs, fn($v) => $v != "0" && $v != null && $v != "");
    if (!empty($studentIDs)) $queryArray['srstudentID'] = $studentIDs;
}
$students = $this->studentrelation_m->general_get_order_by_student_multi_selction($queryArray);
```

---

## 6. Key Models

| Model | Purpose |
|---|---|
| `classes_m` | `general_get_classes()` — class list |
| `section_m` | `general_get_order_by_section(['classesID'=>$id])` |
| `studentrelation_m` | `general_get_order_by_student($arr)` / `_multi_selction($arr)` |
| `exam_m` | `get_exam($examID)` |
| `marksetting_m` | `get_exam($marktypeID, $classesID)` — exam dropdown |
| `subject_m` | `general_get_order_by_subject_left_examschedule()` / `_with_exam()` |
| `examschedule_m` | exam date/time per subject |
| `schoolyear_m` | `get_single_schoolyear(['schoolyearID'=>$id])` |
| `mark_m` | student marks |
| `grade_m` | grade config |
| `feetype_m` | `general_get_feetype()` — fee type list |

---

## 7. PDF & Email

```php
$this->reportPDF('reportname.css', $this->data, 'report/xyz/XyzReportPDF');
$this->reportSendToMail('reportname.css', $this->data, 'report/xyz/XyzReportPDF', $to, $subject, $msg);
```
Both defined in `Admin_Controller`. CSS file in `assets/css/`.

---

## 8. View Conventions

- `$siteinfos` — school name, address, phone, email, logo, signatures (global via `Admin_Controller`).
- School logo: `base_url('uploads/images/'.$siteinfos->photo)`
- Student photo: `imagelink($student->photo)`
- Signatures: `base_url('/uploads/signatures/').$siteinfos->correspondent_signature`
- Print CSS: `@media print { }` + `@page { size:A4; margin:10mm; }` + `-webkit-print-color-adjust:exact`.

---

## 9. Language Files

- Path: `mvc/language/english/{reportname}_lang.php`
- Key pattern: `$lang['reportname_fieldname'] = 'Label';`
- Load: `$this->lang->load('reportname', $language);`
- Use: `$this->lang->line('reportname_fieldname')`

---

## 10. Maintenance Log

- **2026-03-16**: Initialized. Documented all patterns from Admitcardreport + Progresscardreport.
- **2026-03-16**: Added full dropdown section (cascade chain, AJAX methods, JS block, select2, datepicker, multi-select, validation).
- **2026-03-16**: Exam date ordering fix — add `ORDER BY examschedule.edate ASC` in model; reorder `$subjects` in controller to match date order before passing to view.
- **2026-03-16**: Compacted file for efficient AI context loading. Next work: Invoice Report.
