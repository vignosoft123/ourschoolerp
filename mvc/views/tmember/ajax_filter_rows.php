<?php if(customCompute($students)) { $i = 1; foreach($students as $student) { ?>
<tr data-transport="<?=$student->transport?>">
    <?php if(permissionChecker('tmember_add') && (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1))) { ?>
    <td>
        <?php if($student->transport == 0) { ?>
        <input type="checkbox" class="student-cb" data-student-id="<?=$student->studentID?>">
        <?php } ?>
    </td>
    <?php } ?>
    <td><?=$i?></td>
    <td><?=profileimage($student->photo)?></td>
    <td><?=$student->srname?></td>
    <td><?=$student->srroll?></td>
    <td><?=$student->phone?></td>
    <?php if(permissionChecker('tmember_add') || permissionChecker('tmember_edit') || permissionChecker('tmember_delete') || permissionChecker('tmember_view')) { ?>
    <td>
        <?php if($student->transport == 0) {
            if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                echo btn_add('tmember/add/'.$student->studentID.'/'.$set, $this->lang->line('tmember'));
            }
        } else {
            echo btn_view('tmember/view/'.$student->studentID.'/'.$set, $this->lang->line('view')).' ';
            if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                echo btn_edit('tmember/edit/'.$student->studentID.'/'.$set, $this->lang->line('edit')).' ';
                echo btn_delete('tmember/delete/'.$student->studentID.'/'.$set, $this->lang->line('delete'));
            }
        } ?>
    </td>
    <?php } ?>
</tr>
<?php $i++; } } ?>
