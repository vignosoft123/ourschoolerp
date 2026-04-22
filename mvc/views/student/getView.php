<?php 
	 $inv = $this->uri->segment(5);
if (customCompute($profile)) { ?>
	<div class="well top-panel-bg">
		<div class="row">
			<div class="col-sm-6">
				<?php if (!permissionChecker('student_view') && permissionChecker('student_add')) {
					echo btn_sm_add('student/add', $this->lang->line('add_student'));
				} ?>
				<button class="btn-white btn-sm-cs" onclick="javascript:printDiv('printablediv')"><span class="fa fa-print pe-1"></span> <?= $this->lang->line('print') ?> </button>
				<?php
				echo btn_add_pdf('student/print_preview/' . $profile->srstudentID . "/" . $set, $this->lang->line('pdf_preview'))
				?>

				<?php if ($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) {
					if (permissionChecker('student_edit')) {
						echo btn_sm_edit('student/edit/' . $profile->srstudentID . "/" . $set, $this->lang->line('edit'));
					}
				}
				?>

				<button class="btn-white btn-sm-cs" data-toggle="modal" data-target="#mail"><span class="fa fa-envelope-o"></span> <?= $this->lang->line('mail') ?></button>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb bg-transparent">
					<li><a href="<?= base_url("dashboard/index") ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
					<li><a href="<?= base_url("student/index/$set") ?>"><?= $this->lang->line('menu_student') ?></a></li>
					<li class="active"><?= $this->lang->line('view') ?></li>
				</ol>
			</div>
		</div>
	</div>

	<div id="printablediv">
		<div class="row">
			<div class="col-sm-3">
				<div class="box box-primary backgroud-image">
					<div class="box-profile">
						<div class="profile-card">						
						<div class="profile-header">
							<img class="profile-header-bg" src="./../../../uploads/images/profile-bg-blue.jpg" alt="">
						</div>
						<div class="profile-body">
						<div class="profile-img">
							<?php //echo "<pre>";print_r($profile)?>
							<?= profileviewimage($profile->photo) ?>
						</div>
						<div class="profile-name-info">
							<h3 class="profile-username text-center"><?= $profile->srname ?></h3>
							<p class="user-type text-center"><?= $usertype->usertype ?></p>
						</div>
						

						<ul class="list-group list-group-unbordered profile-list-info">
							<li class="list-group-item">
								<span class="h5-title"><?= $this->lang->line('student_registerNO') ?></span> <span class="h6-title pull-right"><?= $profile->srregisterNO ?></span>
							</li>
							<li class="list-group-item">
								<span class="h5-title"><?= $this->lang->line('student_roll') ?></span> <span class="h6-title pull-right"><?= $profile->srroll ?></span>
							</li>

							<li class="list-group-item">
								<span class="h5-title">PEN Number</span> <span class="h6-title pull-right"><?= $profile->pen_number ?></span>
							</li>

							<li class="list-group-item">
								<span class="h5-title">Child ID</span> <span class="h6-title pull-right"><?= $profile->child_id ?></span>
							</li>


							<li class="list-group-item">
								<span class="h5-title"><?= $this->lang->line('student_classes') ?></span> <span class="h6-title pull-right"><?= customCompute($class) ? $class->classes : '' ?></span>
							</li>
							<li class="list-group-item">
								<span class="h5-title"><?= $this->lang->line('student_section') ?></span> <span class="h6-title pull-right"><?= customCompute($section) ? $section->section : '' ?></span>
							</li>
							<li class="list-group-item">
								<span class="h5-title">RF ID</span> <span class="h6-title pull-right"><?= $profile->rf_id ?></span>
							</li>
							<li class="list-group-item">
								<span class="h5-title">Medium</span> <span class="h6-title pull-right"><?= $profile->medium ?></span>
							</li>

							<li class="list-group-item">
								<span class="h5-title">Joined Class</span> <span class="h6-title pull-right"><?= isset($all_classes[$profile->joined_class]) ? $all_classes[$profile->joined_class] : '' ?></span>
							</li>

							<li class="list-group-item">
								<span class="h5-title">Joined Date</span> <span class="h6-title pull-right">
									<?= date("d-M-Y",strtotime($profile->admission_date ?? $profile->create_date))?>
								</span>
							</li>
<li class="list-group-item">
								<span class="h5-title">Device token</span> 								<span class="h6-title pull-right">
									<?= $profile->device_token?>
								</span>
							</li>
							<li class="list-group-item">
								<span class="h5-title">Student Type</span> <span class="h6-title pull-right"><?= $profile->studentType ?></span>
							</li>
							<!-- 
							<li class="list-group-item" style="background-color: #FFF">
								<b>Joined Class</b> <a class="pull-right"><?= customCompute($section) ? $section->section : '' ?></a>
							</li> -->

						</ul>
						</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-sm-9">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#profile" data-toggle="tab"><?= $this->lang->line('student_profile') ?></a></li>
						<?php //if (customCompute($parents)) {
							 ?> <li><a href="#parents" data-toggle="tab"><?= $this->lang->line('student_parents') ?></a></li>
							  <?php //} ?>
						<?php if ((permissionChecker('student_add') && permissionChecker('student_delete')) || ($this->session->userdata('usertypeID') == $profile->usertypeID && $this->session->userdata('loginuserID') == $profile->srstudentID)) {  ?>
							<li><a href="#routine" data-toggle="tab"><?= $this->lang->line('student_routine') ?></a></li>
							<li><a href="#attendance" data-toggle="tab"><?= $this->lang->line('student_attendance') ?></a></li>
							<li><a href="#mark" data-toggle="tab"><?= $this->lang->line('student_mark') ?></a></li>
							<li><a href="#invoice" data-toggle="tab"><?= $this->lang->line('student_invoice') ?></a></li>
							<li><a href="#payment" data-toggle="tab"><?= $this->lang->line('student_payment') ?></a></li>
							<li><a href="#document" data-toggle="tab"><?= $this->lang->line('student_document') ?></a></li>
							<li><a href="#studentType" data-toggle="tab"><?= $this->lang->line('student_type') ?></a></li>
						<?php } ?>
					</ul>

					<div class="tab-content">
						<div class="active tab-pane" id="profile">
							<div class="panel-body profile-view-dis student-view-info">
							  <h2 class="h2-title">Add Student Form</h2>
								<div class="st-detail-list">
								<div class="profile-view-tab">
									    <div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_username") ?> </label>
											<div class="para"><?= $profile->username ?></div>
										</div>											
									</div>
									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_admission_date") ?> </label>
											<div class="para"><?php if ($profile->admission_date) {
												echo date("d M Y", strtotime($profile->admission_date));
											} ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_studentgroup") ?></label>
											<div class="para"><?= customCompute($group) ? $group->group : '' ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_optionalsubject") ?></label>
											<div class="para"><?= customCompute($optionalsubject) ? $optionalsubject->subject : '' ?></div>
										</div>										
									</div>
									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_remarks") ?> </label>
											<div class="para"><?= $profile->remarks ?></div>
										</div>	
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Mother Toungue </label>
											<div class="para"><?php 
												$m_t = array(''=> '-','1'=> 'Telugu','2'=> 'English','3'=> 'Hindi','4'=> 'Kannada','5'=> 'Malayalam','6'=> 'Urdhu',);
												echo $m_t[$profile->mother_toungue] ?></div>
										</div>	
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Refered By </label>
											<div class="para"><?= $refered_by_name ?></div>
										</div>
									</div>

								</div>

								<?php if (!empty($siblings)): ?>
								<h2 class="h2-title">Siblings</h2>
								<div class="st-detail-list">
									<table class="table table-bordered table-sm">
										<thead>
											<tr>
												<th>Name</th>
												<th>Class</th>
												<th>Section</th>
												<th>View</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($siblings as $sib): ?>
											<tr>
												<td><?= htmlspecialchars($sib->name) ?></td>
												<td><?= htmlspecialchars($sib->classes) ?></td>
												<td><?= htmlspecialchars($sib->section) ?></td>
												<td><a href="<?= base_url('student/view/' . $sib->sibling_studentID . '/' . $set) ?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a></td>
											</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<?php else: ?>
								<h2 class="h2-title">Siblings</h2>
								<div class="st-detail-list">
									<p class="text-muted">No siblings added.</p>
								</div>
								<?php endif; ?>

								<h2 class="h2-title">Student Details</h2>

								 


								<div class="st-detail-list">
									<div class="profile-view-tab">
										<div class="profile-details">
										<label class="label-txt">First Name</label>
											<div class="para"><?= $profile->first_name ?></div>
										</div>										
									</div>
									<div class="profile-view-tab">
										<div class="profile-details">
										<label class="label-txt">Last Name</label>
											<div class="para"><?= $profile->last_name ?></div>
										</div>
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
										<label class="label-txt">ID Card Name</label>
											<div class="para"><?= $profile->name ?></div>
										</div>										
									</div>
								</div>



								<div class="st-detail-list">
									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_dob") ?></label>
											<div class="para"><?php if ($profile->dob) {
												echo date("d M Y", strtotime($profile->dob));
											} ?></div>
										</div>										
									</div>
									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_sex") ?></label>
											<div class="para"><?= $profile->sex ?></div>
										</div>
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_bloodgroup") ?></label>
											<div class="para"><?php if (isset($allbloodgroup[$profile->bloodgroup])) {
														echo $profile->bloodgroup;
												} ?>
											</div>
										</div>										
									</div>
									
									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_religion") ?></label>
											<div class="para"><?= $profile->religion ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Caste</label>
											<div class="para"><?= $profile->caste ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Sub Caste</label>
											<div class="para"><?= $profile->sub_caste ?></div>
										</div>										
									</div>


									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_email") ?></label>
											<div class="para"><?= $profile->email ?></div>
										</div>										
									</div>
									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_phone") ?></label>
											<div class="para"><?= $profile->phone ?></div>
										</div>
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Alternative Phone1</label>
											<div class="para"><?= $profile->alternative_phone1 ?></div>
										</div>
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Alternative Phone2</label>
											<div class="para"><?= $profile->alternative_phone2 ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Caste</label>
											<div class="para"><?= $profile->caste ?></div>
										</div>										
									</div>

									
									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Sub Caste</label>
											<div class="para"><?= $profile->sub_caste ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("mole1") ?></label>
											<div class="para"> <?= $profile->mole1 ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("mole2") ?></label>
											<div class="para"> <?= $profile->mole2 ?></div>
										</div>										
									</div>


									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Ration Card</label>
											<div class="para"> <?= $profile->ration_card ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Account Number</label>
											<div class="para"> <?= $profile->account_no ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Bank Name</label>
											<div class="para"> <?= $profile->bank_name ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">IFSC CODE</label>
											<div class="para"> <?= $profile->ifsc_code ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt">Branch Nme</label>
											<div class="para"> <?= $profile->branch_name ?></div>
										</div>										
									</div> 

								</div>


								<h2 class="h2-title">Student Address Details</h2>
								<div class="st-detail-list">

								    <div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_address") ?> </label>
											<div class="para"> <?= $profile->address ?></div>
										</div>										
									</div>
									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_village") ?></label>
											<div class="para"> <?= $profile->village_name ?></div>
										</div>										
									</div>
									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_state") ?></label>
											<div class="para"><?= $profile->state ?></div>
										</div>											
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_country") ?> </label>
											<div class="para"><?php if (isset($allcountry[$profile->country])) {
												echo $allcountry[$profile->country];
											} ?></div>
										</div>										
									</div>

									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("student_extracurricularactivities") ?> </label>
											<div class="para"> <?= $profile->extracurricularactivities ?></div>
										</div>										
									</div>
									
									<div class="profile-view-tab">
										<div class="profile-details">
											<label class="label-txt"><?= $this->lang->line("aadharCardNumber") ?></label>
											<div class="para"> <?= $profile->aadharCardNumber ?></div>
										</div>										
									</div>
								</div>
							</div>
						</div>

						<?php 
						//echo "<pre>";print_r($parents);
						//if (customCompute($parents)) { ?>
							<div class="tab-pane" id="parents">
								<div class="panel-body profile-view-dis student-view-info">
									<div class="st-detail-list">
										<div class="profile-view-tab">										
											<div class="profile-details">
												<label class="label-txt"><?= $this->lang->line("parent_guargian_name") ?> </label>
												<div class="para"> <?= $parents->name ?></div>
											</div>
										</div>
										<div class="profile-view-tab">
											<div class="profile-details">
												<label class="label-txt"><?= $this->lang->line("parent_father_name") ?>  </label>
												<div class="para"> <?= $parents->father_name ?></div>
											</div>											
										</div>

										<div class="profile-view-tab">
											<div class="profile-details">
												<label class="label-txt">Father Aadhar</label>
												<div class="para"> <?= $parents->father_aadhar ?></div>
											</div>											
										</div>


										<div class="profile-view-tab">
											<div class="profile-details">
												<label class="label-txt"> <?= $this->lang->line("parent_mother_name") ?> </span> </label>
												<div class="para">  <?= $parents->mother_name ?></div>
											</div>											
										</div>

										<div class="profile-view-tab">
											<div class="profile-details">
												<label class="label-txt">Mother Aadhar</label>
												<div class="para"> <?= $parents->mother_aadhar ?></div>
											</div>											
										</div>

										<div class="profile-view-tab">
											<div class="profile-details">
												<label class="label-txt"> <?= $this->lang->line("parent_father_profession") ?></label>
												<div class="para"><?= $parents->father_profession ?> </div>
											</div>											
										</div>
										<div class="profile-view-tab">
											<div class="profile-details">
												<label class="label-txt"> <?= $this->lang->line("parent_mother_profession") ?> </label>
												<div class="para"><?= $parents->mother_profession ?> </div>
											</div>											
										</div>
										<div class="profile-view-tab">
											<div class="profile-details">
												<label class="label-txt"><?= $this->lang->line("parent_email") ?>   </label>
												<div class="para"> <?= $parents->email ?> </div>
											</div>											
										</div>
										<div class="profile-view-tab">
											<div class="profile-details">
												<label class="label-txt"> <?= $this->lang->line("parent_phone") ?>  </label>
												<div class="para"> <?= $parents->phone ?></div>
											</div>											
										</div>
										<div class="profile-view-tab">
											<div class="profile-details">
												<label class="label-txt"> <?= $this->lang->line("parent_username") ?> </label>
												<div class="para"> <?= $parents->username ?></div>
											</div>											
										</div>
										<div class="profile-view-tab">
											<div class="profile-details">
												<label class="label-txt"> <?= $this->lang->line("parent_address") ?> </label>
												<div class="para"><?= $parents->address ?> </div>
											</div>											
										</div>
									</div>
								</div>
							</div>
						<?php //} ?>

						<?php if ((permissionChecker('student_add') && permissionChecker('student_delete')) || ($this->session->userdata('usertypeID') == $profile->usertypeID && $this->session->userdata('loginuserID') == $profile->srstudentID)) {  ?>

							<div class="tab-pane" id="routine">
								<?php
								$days = [
									0 => $this->lang->line('sunday'),
									1 => $this->lang->line('monday'),
									2 => $this->lang->line('tuesday'),
									3 => $this->lang->line('wednesday'),
									4 => $this->lang->line('thursday'),
									5 => $this->lang->line('friday'),
									6 => $this->lang->line('saturday'),
								];

								if (customCompute($routines)) {
									$maxClass = 0;
									foreach ($routines as $routineKey => $routine) {
										if (customCompute($routine) > $maxClass) {
											$maxClass = customCompute($routine);
										}
									} ?>

									<div class="table-responsive">
										<table class="table table-bordered table-responsive">
											<thead>
												<th class="text-center"><?= $this->lang->line('student_day'); ?></th>
												<?php for ($i = 1; $i <= $maxClass; $i++) { ?>
													<th class="text-center"><?= addOrdinalNumberSuffix($i) . " " . $this->lang->line('student_period'); ?></th>
												<?php } ?>
											</thead>
											<tbody>
												<?php foreach ($days as $dayKey => $day) {
													if (!in_array($dayKey, $routineweekends) && isset($routines[$dayKey])) {
														$i = 0; ?>
														<tr>
															<td><?= $day ?></td>
															<?php foreach ($routines[$dayKey] as $routine) {
																$i++; ?>
																<td class="text-center">
																	<p style="margin: 0px 0px 1px"><?= $routine->start_time; ?>-<?= $routine->end_time; ?></p>
																	<p style="margin: 0px 0px 1px">
																		<span class="left"><?= $this->lang->line('student_subject') ?> :</span>
																		<span class="right">
																			<?php if (isset($subjects[$routine->subjectID])) {
																				echo $subjects[$routine->subjectID]->subject;
																			} ?>
																		</span>
																	</p>
																	<p style="margin: 0px 0px 1px">
																		<span class="left"><?= $this->lang->line('student_teacher') ?> :</span>
																		<span class="right">
																			<?php if (isset($teachers[$routine->teacherID])) {
																				echo $teachers[$routine->teacherID];
																			} ?>
																		</span>
																	</p>
																	<p style="margin: 0px 0px 1px"><span class="left"><?= $this->lang->line('student_room') ?> : </span><span class="right"><?= $routine->room; ?></span></p>
																</td>
															<?php }
															$j = ($maxClass - $i);
															if ($i < $maxClass) {
																for ($i = 1; $i <= $j; $i++) {
																	echo "<td class='text-center'>N/A</td>";
																}
															} ?>
														</tr>
												<?php }
												} ?>
											</tbody>
										</table>
									</div>
								<?php } ?>
							</div>

							<div class="tab-pane" id="attendance">
								<?php
								$monthArray = array(
									"01" => "jan", "02" => "feb", "03" => "mar", "04" => "apr", "05" => "may", "06" => "jun", "07" => "jul", "08" => "aug", "09" => "sep", "10" => "oct", "11" => "nov", "12" => "dec"
								);
								?>
								<?php if ($setting->attendance == 'subject') {

									if (customCompute($attendancesubjects)) {
										foreach ($attendancesubjects as $subject) {
											$holidayCount = 0;
											$weekendayCount = 0;
											$leavedayCount = 0;
											$presentCount = 0;
											$lateexcuseCount = 0;
											$lateCount = 0;
											$absentCount = 0;
											if ($subject->type === '1') {
												echo "<h4>" . $subject->subject . "</h4>";
								?>
												<div class="row">
													<div class="col-sm-12">
														<div class="studentDIV">
															<table class="attendance_table">
																<thead>
																	<tr>
																		<th>#</th>
																		<?php
																		for ($i = 1; $i <= 31; $i++) {
																			echo  "<th>" . $this->lang->line('student_' . $i) . "</th>";
																		}
																		?>
																	</tr>
																</thead>
																<tbody>
																	<?php
																	$schoolyearstartingdate = $schoolyearsessionobj->startingdate;
																	$schoolyearendingdate = $schoolyearsessionobj->endingdate;

																	$allMonths = get_month_and_year_using_two_date($schoolyearstartingdate, $schoolyearendingdate);
																	$holidaysArray = explode('","', $holidays);

																	$allMonthsArray = array();

																	foreach ($allMonths as $yearKey => $allMonth) {
																		foreach ($allMonth as $month) {
																			$monthAndYear = $month . '-' . $yearKey;
																			if (isset($attendances_subjectwisess[$subject->subjectID][$monthAndYear])) {
																				$attendanceMonthAndYear = $attendances_subjectwisess[$subject->subjectID][$monthAndYear];
																				echo "<tr>";
																				echo "<td>" . ucwords($monthArray[$month]) . "</td>";
																				for ($i = 1; $i <= 31; $i++) {
																					$acolumnname = 'a' . $i;
																					$d = sprintf('%02d', $i);

																					$date = $d . "-" . $month . "-" . $yearKey;
																					if (in_array($date, $holidaysArray)) {
																						$holidayCount++;
																						echo "<td class='ini-bg-primary'>" . 'H' . "</td>";
																					} elseif (in_array($date, $getWeekendDays)) {
																						$weekendayCount++;
																						echo "<td class='ini-bg-info'>" . 'W' . "</td>";
																					} elseif (in_array($date, $leaveapplications)) {
																						$leavedayCount++;
																						echo "<td class='ini-bg-success'>" . 'LA' . "</td>";
																					} else {
																						$textcolorclass = '';
																						$val = false;
																						if (isset($attendanceMonthAndYear) && $attendanceMonthAndYear->$acolumnname == 'P') {
																							$presentCount++;
																							$textcolorclass = 'ini-bg-success';
																						} elseif (isset($attendanceMonthAndYear) && $attendanceMonthAndYear->$acolumnname == 'LE') {
																							$lateexcuseCount++;
																							$textcolorclass = 'ini-bg-success';
																						} elseif (isset($attendanceMonthAndYear) && $attendanceMonthAndYear->$acolumnname == 'L') {
																							$lateCount++;
																							$textcolorclass = 'ini-bg-success';
																						} elseif (isset($attendanceMonthAndYear) && $attendanceMonthAndYear->$acolumnname == 'A') {
																							$absentCount++;
																							$textcolorclass = 'ini-bg-danger';
																						} elseif ((isset($attendanceMonthAndYear) && ($attendanceMonthAndYear->$acolumnname == NULL || $attendanceMonthAndYear->$acolumnname == ''))) {
																							$textcolorclass = 'ini-bg-secondary';
																							$defaultVal = 'N/A';
																							$val = true;
																						}

																						if ($val) {
																							echo "<td class='" . $textcolorclass . "'>" . $defaultVal . "</td>";
																						} else {
																							echo "<td class='" . $textcolorclass . "'>" . $attendanceMonthAndYear->$acolumnname . "</td>";
																						}
																					}
																				}
																				echo "</tr>";
																			} else {

																				echo "<tr>";
																				echo "<td>" . ucwords($monthArray[$month]) . "</td>";
																				for ($i = 1; $i <= 31; $i++) {
																					$acolumnname = 'a' . $i;
																					$d = sprintf('%02d', $i);

																					$date = $d . "-" . $month . "-" . $yearKey;
																					if (in_array($date, $holidaysArray)) {
																						$holidayCount++;
																						echo "<td class='ini-bg-primary'>" . 'H' . "</td>";
																					} elseif (in_array($date, $getWeekendDays)) {
																						$weekendayCount++;
																						echo "<td class='ini-bg-info'>" . 'W' . "</td>";
																					} elseif (in_array($date, $leaveapplications)) {
																						$leavedayCount++;
																						echo "<td class='ini-bg-success'>" . 'LA' . "</td>";
																					} else {
																						$textcolorclass = 'ini-bg-secondary';
																						echo "<td class='" . $textcolorclass . "'>" . 'N/A' . "</td>";
																					}
																				}
																				echo "</tr>";
																			}
																		}
																	}
																	?>
																</tbody>
															</table>
														</div>
													</div>
													<div class="col-sm-12">
														<p class="totalattendanceCount">
															<?php
															echo $this->lang->line('student_total_holiday') . ':' . $holidayCount . ', ';
															echo $this->lang->line('student_total_weekenday') . ':' . $weekendayCount . ', ';
															echo $this->lang->line('student_total_leaveday') . ':' . $leavedayCount . ', ';
															echo $this->lang->line('student_total_present') . ':' . $presentCount . ', ';
															echo $this->lang->line('student_total_latewithexcuse') . ':' . $lateexcuseCount . ', ';
															echo $this->lang->line('student_total_late') . ':' . $lateCount . ', ';
															echo $this->lang->line('student_total_absent') . ':' . $absentCount;
															?>
														</p>
														
													</div>
												</div>
												<br />
												<?php } else {
												if ($subject->subjectID == $profile->sroptionalsubjectID) { ?>
													<h4><?= $subject->subject; ?></h4>
													<div class="row">
														<div class="col-sm-12">
															<div class="studentDIV">
																<table class="attendance_table">
																	<thead>
																		<tr>
																			<th>#</th>
																			<?php
																			for ($i = 1; $i <= 31; $i++) {
																				echo  "<th>" . $this->lang->line('student_' . $i) . "</th>";
																			}
																			?>
																		</tr>
																	</thead>
																	<tbody>
																		<?php
																		$schoolyearstartingdate = $schoolyearsessionobj->startingdate;
																		$schoolyearendingdate = $schoolyearsessionobj->endingdate;

																		$allMonths = get_month_and_year_using_two_date($schoolyearstartingdate, $schoolyearendingdate);
																		$holidaysArray = explode('","', $holidays);

																		$allMonthsArray = array();

																		foreach ($allMonths as $yearKey => $allMonth) {
																			foreach ($allMonth as $month) {
																				$monthAndYear = $month . '-' . $yearKey;
																				if (isset($attendances_subjectwisess[$subject->subjectID][$monthAndYear])) {
																					$attendanceMonthAndYear = $attendances_subjectwisess[$subject->subjectID][$monthAndYear];
																					echo "<tr>";
																					echo "<td>" . ucwords($monthArray[$month]) . "</td>";
																					for ($i = 1; $i <= 31; $i++) {
																						$acolumnname = 'a' . $i;
																						$d = sprintf('%02d', $i);

																						$date = $d . "-" . $month . "-" . $yearKey;
																						if (in_array($date, $holidaysArray)) {
																							$holidayCount++;
																							echo "<td class='ini-bg-primary'>" . 'H' . "</td>";
																						} elseif (in_array($date, $getWeekendDays)) {
																							$weekendayCount++;
																							echo "<td class='ini-bg-info'>" . 'W' . "</td>";
																						} elseif (in_array($date, $leaveapplications)) {
																							$leavedayCount++;
																							echo "<td class='ini-bg-success'>" . 'LA' . "</td>";
																						} else {
																							$textcolorclass = '';
																							$val = false;
																							if (isset($attendanceMonthAndYear) && $attendanceMonthAndYear->$acolumnname == 'P') {
																								$presentCount++;
																								$textcolorclass = 'ini-bg-success';
																							} elseif (isset($attendanceMonthAndYear) && $attendanceMonthAndYear->$acolumnname == 'LE') {
																								$lateexcuseCount++;
																								$textcolorclass = 'ini-bg-success';
																							} elseif (isset($attendanceMonthAndYear) && $attendanceMonthAndYear->$acolumnname == 'L') {
																								$lateCount++;
																								$textcolorclass = 'ini-bg-success';
																							} elseif (isset($attendanceMonthAndYear) && $attendanceMonthAndYear->$acolumnname == 'A') {
																								$absentCount++;
																								$textcolorclass = 'ini-bg-danger';
																							} elseif ((isset($attendanceMonthAndYear) && ($attendanceMonthAndYear->$acolumnname == NULL || $attendanceMonthAndYear->$acolumnname == ''))) {
																								$textcolorclass = 'ini-bg-secondary';
																								$defaultVal = 'N/A';
																								$val = true;
																							}

																							if ($val) {
																								echo "<td class='" . $textcolorclass . "'>" . $defaultVal . "</td>";
																							} else {
																								echo "<td class='" . $textcolorclass . "'>" . $attendanceMonthAndYear->$acolumnname . "</td>";
																							}
																						}
																					}
																					echo "</tr>";
																				} else {

																					echo "<tr>";
																					echo "<td>" . ucwords($monthArray[$month]) . "</td>";
																					for ($i = 1; $i <= 31; $i++) {
																						$acolumnname = 'a' . $i;
																						$d = sprintf('%02d', $i);

																						$date = $d . "-" . $month . "-" . $yearKey;
																						if (in_array($date, $holidaysArray)) {
																							$holidayCount++;
																							echo "<td class='ini-bg-primary'>" . 'H' . "</td>";
																						} elseif (in_array($date, $getWeekendDays)) {
																							$weekendayCount++;
																							echo "<td class='ini-bg-info'>" . 'W' . "</td>";
																						} elseif (in_array($date, $leaveapplications)) {
																							$leavedayCount++;
																							echo "<td class='ini-bg-success'>" . 'LA' . "</td>";
																						} else {
																							$textcolorclass = 'ini-bg-secondary';
																							echo "<td class='" . $textcolorclass . "'>" . 'N/A' . "</td>";
																						}
																					}
																					echo "</tr>";
																				}
																			}
																		}
																		?>
																	</tbody>
																</table>
															</div>
														</div>
														<div class="col-sm-12">
															<p class="totalattendanceCount">
																<?php
																echo $this->lang->line('student_total_holiday') . ':' . $holidayCount . ', ';
																echo $this->lang->line('student_total_weekenday') . ':' . $weekendayCount . ', ';
																echo $this->lang->line('student_total_leaveday') . ':' . $leavedayCount . ', ';
																echo $this->lang->line('student_total_present') . ':' . $presentCount . ', ';
																echo $this->lang->line('student_total_latewithexcuse') . ':' . $lateexcuseCount . ', ';
																echo $this->lang->line('student_total_late') . ':' . $lateCount . ', ';
																echo $this->lang->line('student_total_absent') . ':' . $absentCount;
																?>
															</p>
														</div>
													</div>
													<br />
									<?php }
											}
										}
									} ?>
								<?php } else {
									$holidayCount = 0;
									$weekendayCount = 0;
									$leavedayCount = 0;
									$presentCount = 0;
									$lateexcuseCount = 0;
									$lateCount = 0;
									$absentCount = 0;
								?>
									<div class="row">
										<div class="col-md-12">
											<div class="studentDIV">
												<table class="attendance_table">
													<thead>
														<tr>
															<th>#</th>
															<?php
															for ($i = 1; $i <= 31; $i++) {
																echo  "<th>" . $this->lang->line('student_' . $i) . "</th>";
															}
															?>
														</tr>
													</thead>
													<tbody>
														<?php
														$schoolyearstartingdate = $schoolyearsessionobj->startingdate;
														$schoolyearendingdate = $schoolyearsessionobj->endingdate;
														$allMonths = get_month_and_year_using_two_date($schoolyearstartingdate, $schoolyearendingdate);
														$holidaysArray = explode('","', $holidays);

														foreach ($allMonths as $yearKey => $months) {
															foreach ($months as $month) {
																$monthyear = $month . "-" . $yearKey;
																if (isset($attendancesArray[$monthyear])) {
																	echo "<tr>";
																	echo "<td>" . ucwords($monthArray[$month]) . "</td>";
																	for ($i = 1; $i <= 31; $i++) {
																		$acolumnname = 'a' . $i;
																		$d = sprintf('%02d', $i);

																		$date = $d . "-" . $month . "-" . $yearKey;
																		if (in_array($date, $holidaysArray)) {
																			$holidayCount++;
																			echo "<td class='ini-bg-primary'>" . 'H' . "</td>";
																		} elseif (in_array($date, $getWeekendDays)) {
																			$weekendayCount++;
																			echo "<td class='ini-bg-info'>" . 'W' . "</td>";
																		} elseif (in_array($date, $leaveapplications)) {
																			$leavedayCount++;
																			echo "<td class='ini-bg-success'>" . 'LA' . "</td>";
																		} else {
																			$textcolorclass = '';
																			$val = false;
																			if (isset($attendancesArray[$monthyear]) && $attendancesArray[$monthyear]->$acolumnname == 'P') {
																				$presentCount++;
																				$textcolorclass = 'ini-bg-success';
																			} elseif (isset($attendancesArray[$monthyear]) && $attendancesArray[$monthyear]->$acolumnname == 'LE') {
																				$lateexcuseCount++;
																				$textcolorclass = 'ini-bg-success';
																			} elseif (isset($attendancesArray[$monthyear]) && $attendancesArray[$monthyear]->$acolumnname == 'L') {
																				$lateCount++;
																				$textcolorclass = 'ini-bg-success';
																			} elseif (isset($attendancesArray[$monthyear]) && $attendancesArray[$monthyear]->$acolumnname == 'A') {
																				$absentCount++;
																				$textcolorclass = 'ini-bg-danger';
																			} elseif ((isset($attendancesArray[$monthyear]) && ($attendancesArray[$monthyear]->$acolumnname == NULL || $attendancesArray[$monthyear]->$acolumnname == ''))) {
																				$textcolorclass = 'ini-bg-secondary';
																				$defaultVal = 'N/A';
																				$val = true;
																			}

																			if ($val) {
																				echo "<td class='" . $textcolorclass . "'>" . $defaultVal . "</td>";
																			} else {
																				echo "<td class='" . $textcolorclass . "'>" . $attendancesArray[$monthyear]->$acolumnname . "</td>";
																			}
																		}
																	}
																	echo "</tr>";
																} else {
																	$monthyear = $month . "-" . $yearKey;
																	echo "<tr>";
																	echo "<td>" . ucwords($monthArray[$month]) . "</td>";
																	for ($i = 1; $i <= 31; $i++) {
																		$acolumnname = 'a' . $i;
																		$d = sprintf('%02d', $i);

																		$date = $d . "-" . $month . "-" . $yearKey;
																		if (in_array($date, $holidaysArray)) {
																			$holidayCount++;
																			echo "<td class='ini-bg-primary'>" . 'H' . "</td>";
																		} elseif (in_array($date, $getWeekendDays)) {
																			$weekendayCount++;
																			echo "<td class='ini-bg-info'>" . 'W' . "</td>";
																		} elseif (in_array($date, $leaveapplications)) {
																			$leavedayCount++;
																			echo "<td class='ini-bg-success'>" . 'LA' . "</td>";
																		} else {
																			$textcolorclass = 'ini-bg-secondary';
																			echo "<td class='" . $textcolorclass . "'>" . 'N/A' . "</td>";
																		}
																	}
																	echo "</tr>";
																}
															}
														}
														?>
													</tbody>
												</table>
											</div>
										</div>
										<div class="col-sm-12">
											<p class="totalattendanceCount">
												<?php
												echo '<div class="st-attendance-info">';
												echo '<div class="footer-item">' . $this->lang->line('student_total_holiday') . ': <span class="text-red text-bold">' . $holidayCount . ', ' . '</span>' . '</div>';
												echo '<div class="footer-item">' . $this->lang->line('student_total_weekenday') . ': <span class="text-red text-bold">' . $weekendayCount . ', ' . '</span>' . '</div>';
												echo '<div class="footer-item">' . $this->lang->line('student_total_leaveday') . ': <span class="text-red text-bold">' . $leavedayCount . ', ' . '</span>' . '</div>';
												
												echo '<div class="footer-item">' . $this->lang->line('student_total_present') . ': <span class="text-red text-bold">' . $presentCount . ', ' . '</span>' . '</div>';
												echo '<div class="footer-item">' . $this->lang->line('student_total_latewithexcuse') . ': <span class="text-red text-bold">' . $lateexcuseCount . ', ' . '</span>' . '</div>';
												echo '<div class="footer-item">' . $this->lang->line('student_total_late') . ': <span class="text-red text-bold">' . $lateCount . ', ' . '</span>' . '</div>';
												echo '<div class="footer-item">' . $this->lang->line('student_total_absent') . ': <span class="text-red text-bold">' . $absentCount . ', ' . '</span>' . '</div>';

												echo '</div>';
												?>
											</p>
										</div>
									</div>
								<?php } ?>
							</div>

							<div class="tab-pane" id="mark">
								<?php
								$optionalsubjectID = $profile->sroptionalsubjectID;
								if (customCompute($marksettings)) {
									foreach ($marksettings as $examID => $marksetting) {
										echo '<div style="border:1px solid #ddd" class="box" id="e' . $examID . '">';
										echo '<div class="box-header" style="background-color:#ddedfd;">';
										echo '<h3 class="box-title" style="color:#23292F;">';
										echo (isset($exams[$examID]) ? $exams[$examID] : '');
										echo '</h3>';
										echo '</div>';

										echo '<div class="box-body mark-bodyID" style="">';
										echo "<table class=\"table table-striped table-bordered\" >";
										echo "<thead>";
										echo "<tr>";
										echo "<th class='text-center' style='background-color:#016bd6;color:#fff; vertical-align:middle;' data-title='" . $this->lang->line("student_subject") . "'>";
										echo $this->lang->line("student_subject");
										echo "</th>";

										foreach ($marksetting as $subjectID => $markpercentageArr) {
											foreach ($markpercentageArr[(($settingmarktypeID == 4) || ($settingmarktypeID == 6)) ? 'unique' : 'own'] as $markpercentageID) {
												$markpercentagetypelabel =  isset($markpercentages[$markpercentageID]) ? $markpercentages[$markpercentageID]->markpercentagetype : '';
												echo "<th colspan='' class='text-center' style='background-color:#016bd6;color:#fff;' data-title='" . $markpercentagetypelabel . "'>";
												echo $markpercentagetypelabel;
												echo "</th>";
											}
											break;
										}
										// echo "<th colspan='' class='text-center' style='background-color:#016bd6;color:#fff;' data-title='" . $this->lang->line("student_total") . "'>";
										// echo $this->lang->line("student_total");
										// echo "</th>";
										echo "</tr>";
										foreach ($marksetting as $subjectID => $markpercentageArr) {
											echo "<tr>";
											foreach ($markpercentageArr[(($settingmarktypeID == 4) || ($settingmarktypeID == 6)) ? 'unique' : 'own'] as $markpercentageID) {
												// echo "<th class='text-center' data-title='" . $this->lang->line('student_obtained_mark') . "'>";
												// echo $this->lang->line("student_obtained_mark");
												// echo "</th>";

												// echo "<th class='text-center' data-title='" . $this->lang->line('student_highest_mark') . "'>";
												// echo $this->lang->line("student_highest_mark");
												// echo "</th>";
											}
											// echo "<th class='text-center' data-title='" . $this->lang->line('student_mark') . "'>";
											// echo $this->lang->line("student_mark");
											echo "</th>";
											// echo "<th class='text-center' data-title='" . $this->lang->line('student_point') . "'>";
											// echo $this->lang->line("student_point");
											// echo "</th>";

											// echo "<th class='text-center' data-title='" . $this->lang->line('student_grade') . "'>";
											// echo $this->lang->line("student_grade");
											// echo "</th>";


											echo "</tr>";
											break;
										}
										echo "</thead>";
										echo "<tbody>";
										$totalMark           = 0;
										$totalFinalMark      = 0;
										$totalSubject        = 0;
										$averagePoint        = 0;
										$opmarkpercentageArr = [];
										foreach ($marksetting as $subjectID => $markpercentageArr) {
											if ($subjectID == $optionalsubjectID) {
												$opmarkpercentageArr = $markpercentageArr;
											}
											if (!in_array($subjectID, $optionalsubjectArr)) {
												$totalSubject++;


												echo "<tr>";
												echo "<td class='text-black' data-title='" . $this->lang->line('student_subject') . "'>";
												echo isset($subjects[$subjectID]) ? $subjects[$subjectID]->subject : '';
												echo "</td>";

												$subjectfinalmark = isset($subjects[$subjectID]) ? (int)$subjects[$subjectID]->finalmark : 0;
												$totalSubjectMark = 0;
												$percentageMark   = 0;
												foreach ($markpercentageArr[(($settingmarktypeID == 4) || ($settingmarktypeID == 6)) ? 'unique' : 'own'] as $markpercentageID) {

													$f = false;
													if (isset($markpercentageArr['own']) && in_array($markpercentageID, $markpercentageArr['own'])) {
														$f = true;
														$percentageMark   += (isset($markpercentages[$markpercentageID]) ? $markpercentages[$markpercentageID]->percentage : 0);
													}

													// echo "<td class='text-black' data-title='" . $this->lang->line('student_mark') . "'>";
													// if (isset($marks[$examID][$subjectID][$markpercentageID]) && $f) {
													// 	echo "99999". $marks[$examID][$subjectID][$markpercentageID];
													 	$totalSubjectMark += $marks[$examID][$subjectID][$markpercentageID];
													// } else {
													// 	if ($f) {
													// 		echo 'N/A';
													// 	}
													// }
													// echo "</td>";

													// echo "<td class='text-black' data-title='" . $this->lang->line('student_highest_mark') . "'>";
													// if (isset($highestmarks[$examID][$subjectID][$markpercentageID]) && ($highestmarks[$examID][$subjectID][$markpercentageID] != -1) && $f) {
													// 	echo 'ffff'.$highestmarks[$examID][$subjectID][$markpercentageID];
													// } else {
													// 	if ($f) {
													// 		echo 'N/A';
													// 	}
													// }
													// echo "</td>";
												}
												$finalpercentageMark = convertMarkpercentage($percentageMark, $subjectfinalmark);


												echo "<td class='text-black' data-title='" . $this->lang->line('student_mark') . "'>";

												//code for checking absent or present
												//  $sql = "select eattendance from eattendance where studentID = $profile->srstudentID and examID = $examID and subjectID = $subjectID"; 


												 $sql = "select eattendance from mark where studentID = $profile->srstudentID and examID = $examID and subjectID = $subjectID"; 
												$exam_status = $this->db->query($sql)->row()->eattendance;
												$absent = 0;
											  if($exam_status == 'Absent'){//echo 'if';
												$totalSubjectMark = 0;
												  echo 'Absent';
												  $absent = 1;
											  }else{// echo 'else';
												$absent = 0;
												  echo $totalSubjectMark = $totalSubjectMark;
											  }

												
												$totalMark        += $totalSubjectMark;
												$totalFinalMark   += $finalpercentageMark;
												$totalSubjectMark  = markCalculationView($totalSubjectMark, $subjectfinalmark, $percentageMark);
												echo "</td>";

											// 	if (customCompute($grades)) {
											// 		foreach ($grades as $grade) {
											// 			if (($grade->gradefrom <= $totalSubjectMark) && ($grade->gradeupto >= $totalSubjectMark)) {
											// 				echo "<td class='text-black' data-title='" . $this->lang->line('student_point') . "'>";

															//code for checking absent or present
											// 	$sql = "select eattendance from mark where studentID = $profile->srstudentID and examID = $examID and subjectID = $subjectID"; 
											// 	$exam_status = $this->db->query($sql)->row()->eattendance;

											//   if($absent){
											// 	$grade->point = 0;
											// 	  echo '0';
											//   }else{
												 
											// 	echo $grade->point;
											//   }
															


											// 				$averagePoint += $grade->point;
											// 				echo "</td>";
											// 				echo "<td class='text-black' data-title='" . $this->lang->line('student_grade') . "'>";
											// 				echo $grade->grade;
											// 				echo "</td>";
											// 			}
											// 		}
											// 	} else {
											// 		echo "<td class='text-black' data-title='" . $this->lang->line('student_point') . "'>";
											// 		echo 'N/A';
											// 		echo '</td>';
											// 		echo "<td class='text-black' data-title='" . $this->lang->line('student_grade') . "'>";
											// 		echo 'N/A';
											// 		echo '</td>';
											// 	}
												echo "</tr>";
											}
										}

										if (($optionalsubjectID > 0) && customCompute($opmarkpercentageArr)) {
											$totalSubject++;
											echo "<tr>";
											echo "<td class='text-black' data-title='" . $this->lang->line('student_subject') . "'>";
											echo isset($subjects[$optionalsubjectID]) ? $subjects[$optionalsubjectID]->subject : '';
											echo "</td>";
											$subjectfinalmark  = isset($subjects[$optionalsubjectID]) ? $subjects[$optionalsubjectID]->finalmark : 0;

											$totalSubjectMark = 0;
											$percentageMark   = 0;
											foreach ($opmarkpercentageArr[(($settingmarktypeID == 4) || ($settingmarktypeID == 6)) ? 'unique' : 'own'] as $markpercentageID) {

												$f = false;
												if (isset($opmarkpercentageArr['own']) && in_array($markpercentageID, $opmarkpercentageArr['own'])) {
													$f = true;
													$percentageMark   += (isset($markpercentages[$markpercentageID]) ? $markpercentages[$markpercentageID]->percentage : 0);
												}

												echo "<td class='text-black' data-title='" . $this->lang->line('student_mark') . "'>";
												if (isset($marks[$examID][$optionalsubjectID][$markpercentageID]) && $f) {
													echo $marks[$examID][$optionalsubjectID][$markpercentageID];
													$totalSubjectMark += $marks[$examID][$optionalsubjectID][$markpercentageID];
												} else {
													if ($f) {
														echo 'N/A';
													}
												}
												echo "</td>";

												echo "<td class='text-black' data-title='" . $this->lang->line('student_highest_mark') . "'>";
												if (isset($highestmarks[$examID][$optionalsubjectID][$markpercentageID]) && ($highestmarks[$examID][$optionalsubjectID][$markpercentageID] != -1) && $f) {
													echo $highestmarks[$examID][$optionalsubjectID][$markpercentageID];
												} else {
													if ($f) {
														echo 'N/A';
													}
												}
												echo "</td>";
											}
											$finalpercentageMark = convertMarkpercentage($percentageMark, $subjectfinalmark);


											echo "<td class='text-black' data-title='" . $this->lang->line('student_mark') . "'>";
											echo $totalSubjectMark; 
											$totalMark        += $totalSubjectMark;
											$totalFinalMark   += $finalpercentageMark;




											$totalSubjectMark  = markCalculationView($totalSubjectMark, $subjectfinalmark, $percentageMark);
											echo "</td>";

											if (customCompute($grades)) {
												foreach ($grades as $grade) {
													if (($grade->gradefrom <= $totalSubjectMark) && ($grade->gradeupto >= $totalSubjectMark)) {
														echo "<td class='text-black' data-title='" . $this->lang->line('student_point') . "'>";
														echo $grade->point;
														$averagePoint += $grade->point;
														echo "</td>";
														echo "<td class='text-black' data-title='" . $this->lang->line('student_grade') . "'>";
														echo $grade->grade;
														echo "</td>";
													}
												}
											} else {
												echo "<td class='text-black' data-title='" . $this->lang->line('student_point') . "'>";
												echo 'N/A';
												echo '</td>';
												echo "<td class='text-black' data-title='" . $this->lang->line('student_grade') . "'>";
												echo 'N/A';
												echo '</td>';
											}
											echo "</tr>";
										}
										echo "</tbody>";
										echo "</table>";

										// echo "<pre>";print_r($all_subjects);
										// echo '&&&&'.$examID;
										$out_of = array_sum(
											array_map(function($row) use ($examID) {
												return ((int)$row->examID === (int)$examID) ? (int)$row->max_mark : 0;
											}, $all_subjects)
										);

										// echo '@@@@@@@@@@@@'.$sum_max_mark;

											

										echo '<div class="box-footer st-attendance-info">';
										echo '<div class="footer-item">' . $this->lang->line('student_total_marks') . ' : <span class="text-red text-bold">' . ini_round($out_of) . '</span>' . ',' . '</div>';
										echo '<div class="footer-item">' . $this->lang->line('student_total_obtained_marks') . ' : <span class="text-red text-bold">' . ini_round($totalMark) . '</span>' . ',' . '</div>'; 
										// $totalAverageMark = $totalMark / $totalSubject;
										// echo '<div class="footer-item">' . $this->lang->line('student_total_average_marks') . ' : <span class="text-red text-bold">' . ini_round($totalAverageMark) . '</span>' . ',' . '</div>';

										// $totalmarkpercentage  = markCalculationView($totalMark, $totalFinalMark);
										// echo '<div class="footer-item">' . $this->lang->line('student_total_average_marks_percetage') . ' : <span class="text-red text-bold">' . ini_round($totalmarkpercentage) . '</span>' . ',' . '</div>';

										// $gpaAveragePoint = $averagePoint / $totalSubject;
										// echo '<div class="footer-item">' . $this->lang->line('student_gpa') . ' : <span class="text-red text-bold">' . ini_round($gpaAveragePoint) . '</span>' . ',' . '</div>';

										 $out_of = $out_of != 0 ? $out_of : 1;
                                            $percent_cal = ($tot / $out_of) * 100;

                                            if ($percent_cal >= 95 && $zero_mark == 0) {
                                                $grade = "A+";
                                                $gradeClass = "grade-a-plus";
                                            } else if ($percent_cal >= 90 && $percent_cal < 95 && $zero_mark == 0) {
                                                $grade = "A";
                                                $gradeClass = "grade-a";
                                            } else if ($percent_cal >= 80 && $percent_cal < 90 && $zero_mark == 0) {
                                                $grade = "B+";
                                                $gradeClass = "grade-b-plus";
                                            } else if ($percent_cal >= 70 && $percent_cal < 80 && $zero_mark == 0) {
                                                $grade = "B";
                                                $gradeClass = "grade-b";
                                            } else if ($percent_cal >= 60 && $percent_cal < 70 && $zero_mark == 0) {
                                                $grade = "C+";
                                                $gradeClass = "grade-c-plus";
                                            } else if ($percent_cal >= 50 && $percent_cal < 60 && $zero_mark == 0) {
                                                $grade = "C";
                                                $gradeClass = "grade-c";
                                            } else {
                                                $grade = "D";
                                                $gradeClass = "grade-d";

											$out_of = 0;

                                            }


										echo '<div class="footer-item"> Grade : <span class="text-red text-bold grade-label {$gradeClass}">' . $grade . '</span>' . ',' . '</div>';
										
										echo '</div>';

										echo '</div>';	
										echo "</div>";
									}
								}
								?>
							</div>

							<div class="tab-pane" id="invoice">
								<div id="hide-table">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th><?= $this->lang->line('slno') ?></th>
												<th><?= $this->lang->line('student_feetype') ?></th>
												<th><?= $this->lang->line('student_date') ?></th>
												<th><?= $this->lang->line('student_fees_amount') ?></th>
												<th><?= $this->lang->line('student_discount') ?></th>
												<th><?= $this->lang->line('student_paid') ?></th>
												<th><?= $this->lang->line('student_weaver') ?></th>
												<th><?= $this->lang->line('student_fine') ?></th>
												<th><?= $this->lang->line('student_due') ?></th>
												<th><?= $this->lang->line('student_status') ?></th>

											</tr>
										</thead>
										<tbody>
											<?php $totalInvoice = 0;
											$totalDiscount = 0;
											$totalPaid = 0;
											$totalWeaver = 0;
											$totalDue = 0;
											$totalFine = 0;
											if (customCompute($invoices)) {
												$i = 1;
												foreach ($invoices as $invoice) { ?>
													<tr>
														<td data-title="<?= $this->lang->line('slno') ?>">
															<?php echo $i; ?>
														</td>

														<td data-title="<?= $this->lang->line('student_feetype') ?>">
															<?= isset($feetypes[$invoice->feetypeID]) ? $feetypes[$invoice->feetypeID] : '' ?>
														</td>

														<td data-title="<?= $this->lang->line('student_date') ?>">
															<?= !empty($invoice->date) ? date('d M Y', strtotime($invoice->date)) : '' ?>
														</td>

														

														<td data-title="<?= $this->lang->line('student_fees_amount') ?>">
															<?php $invoiceAmount = $invoice->amount;
															echo number_format($invoiceAmount, 2);
															$totalInvoice += $invoiceAmount; ?>
														</td>

														<td data-title="<?= $this->lang->line('student_discount') ?>">
															<?php $discountAmount = 0;
															if ($invoice->discount > 0) {
																// $discountAmount = (($invoice->amount / 100) * $invoice->discount);
																$discountAmount =  $invoice->discount;
															}
															echo number_format($discountAmount, 2);
															$totalDiscount += $discountAmount; ?>
														</td>

														<td data-title="<?= $this->lang->line('student_paid') ?>">
															<?php $paymentAmount = 0;
															if (isset($allpaymentbyinvoice[$invoice->invoiceID])) {
																$paymentAmount = $allpaymentbyinvoice[$invoice->invoiceID];
															}
															echo number_format($paymentAmount, 2);
															$totalPaid += $paymentAmount; ?>
														</td>

														<td data-title="<?= $this->lang->line('student_weaver') ?>">
															<?php $weaverAmount = 0;
															if (isset($allweaverandpaymentbyinvoice[$invoice->invoiceID]['weaver'])) {
																$weaverAmount = $allweaverandpaymentbyinvoice[$invoice->invoiceID]['weaver'];
															}
															echo number_format($weaverAmount, 2);
															$totalWeaver += $weaverAmount; ?>
														</td>

														<td data-title="<?= $this->lang->line('student_fine') ?>">
															<?php $fineAmount = 0;
															if (isset($allweaverandpaymentbyinvoice[$invoice->invoiceID]['fine'])) {
																$fineAmount = $allweaverandpaymentbyinvoice[$invoice->invoiceID]['fine'];
															}
															echo number_format($fineAmount, 2);
															$totalFine += $fineAmount; ?>
														</td>

														<td data-title="<?= $this->lang->line('student_due') ?>">
															<?php $dueAmount = ((($invoiceAmount - $discountAmount) - $paymentAmount) - $weaverAmount);
															echo number_format($dueAmount, 2);
															$totalDue += $dueAmount; ?>
														</td>
														
													<td data-title="<?= $this->lang->line('student_status') ?>">
															<?php
															// echo "<pre>";print_r($invoice);
															$status = $invoice->paidstatus;
															$setButton = '';
 															if ($status == 0) { 
																 if( $invoiceAmount != $dueAmount ){
                                                        $status = "Partially paid";
                                                        $setButton = 'btn-warning';
                                                    }else{
                                                       $status = "Not paid";
                                                        $setButton = 'btn-danger';
                                                    }

																// $status = $this->lang->line('student_notpaid');
																// $setButton = 'text-danger';
															} elseif ($status == 1) {
																$status = $this->lang->line('student_partially_paid');
																$setButton = 'text-warning';
															} elseif ($status == 2) {
																$status = $this->lang->line('student_fully_paid');
																$setButton = 'text-success';
															}

															echo "<span class='" . $setButton . "'>" . $status . "</span>";
															?>
														</td>

													</tr>

											<?php $i++;
												}
											} ?>

											<tr>
												<td colspan="3" data-title="<?= $this->lang->line('student_total') ?>">
													<?php if ($siteinfos->currency_code) {
														echo '<b>' . $this->lang->line('student_total') . ' (' . $siteinfos->currency_code . ')' . '</b>';
													} else {
														echo '<b>' . $this->lang->line('student_total') . '</b>';
													}
													?>
												</td>
												<td data-title="<?= $this->lang->line('student_total') ?> <?= $this->lang->line('student_fees_amount') ?>">
													<?= number_format($totalInvoice, 2) ?>
												</td>
												<td data-title="<?= $this->lang->line('student_total') ?> <?= $this->lang->line('student_discount') ?>">
													<?= number_format($totalDiscount, 2) ?>
												</td>
												<td data-title="<?= $this->lang->line('student_total') ?> <?= $this->lang->line('student_paid') ?>">
													<?= number_format($totalPaid, 2) ?>
												</td>
												<td data-title="<?= $this->lang->line('student_total') ?> <?= $this->lang->line('student_weaver') ?>">
													<?= number_format($totalWeaver, 2) ?>
												</td>
												<td data-title="<?= $this->lang->line('student_total') ?> <?= $this->lang->line('student_fine') ?>">
													<?= number_format($totalFine, 2) ?>
												</td>
												<td data-title="<?= $this->lang->line('student_total') ?> <?= $this->lang->line('student_due') ?>">
													<?= number_format($totalDue, 2) ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<div class="tab-pane" id="payment">
								<div id="hide-table">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th><?= $this->lang->line('slno') ?></th>
												<th><?= $this->lang->line('student_feetype') ?></th>
												<th><?= $this->lang->line('student_date') ?></th>
												<th><?= $this->lang->line('student_paid') ?></th>
												<th><?= $this->lang->line('student_weaver') ?></th>
												<th><?= $this->lang->line('student_fine') ?></th>
												<th>Comments</th>
											</tr>
										</thead>
										<tbody>
											<?php $totalPaymentPaid = 0;
											$totalPaymentWeaver = 0;
											$totalPaymentFine = 0;
											// echo "<pre>";print_r($payments);die;
											if (customCompute($payments)) {
												$i = 1;
												foreach ($payments as $payment) {  ?>
													<tr>
														<td data-title="<?= $this->lang->line('slno') ?>">
															<?php echo $i; ?>
														</td>

														<td data-title="<?= $this->lang->line('student_feetype') ?>">
															<?= isset($feetypes[$payment->feetypeID]) ? $feetypes[$payment->feetypeID] : '' ?>
														</td>

														<td data-title="<?= $this->lang->line('student_date') ?>">
															<?= !empty($payment->paymentdate) ? date('d M Y', strtotime($payment->paymentdate)) : '' ?>
														</td>

														<td data-title="<?= $this->lang->line('student_paid') ?>">
															<?php $paymentpaidAmount = $payment->paymentamount;
															echo number_format($paymentpaidAmount, 2);
															$totalPaymentPaid += $paymentpaidAmount; ?>
														</td>

														<td data-title="<?= $this->lang->line('student_weaver') ?>">
															<?php $paymentWeaverAmount = $payment->weaver;
															echo number_format($paymentWeaverAmount, 2);
															$totalPaymentWeaver += $paymentWeaverAmount; ?>
														</td>

														<td data-title="<?= $this->lang->line('student_fine') ?>">
															<?php $paymentFineAmount = $payment->fine;
															echo number_format($paymentFineAmount, 2);
															$totalPaymentFine += $paymentFineAmount; ?>
														</td>
														<td><?= $payment->comment?></td>
													</tr>
											<?php $i++;
												}
											} ?>

											<tr>
												<td colspan="3" data-title="<?= $this->lang->line('student_total') ?>">
													<?php if ($siteinfos->currency_code) {
														echo '<b>' . $this->lang->line('student_total') . ' (' . $siteinfos->currency_code . ')' . '</b>';
													} else {
														echo '<b>' . $this->lang->line('student_total') . '</b>';
													}
													?>
												</td>
												<td data-title="<?= $this->lang->line('student_total') ?> <?= $this->lang->line('student_paid') ?>">
													<?= number_format($totalPaymentPaid, 2) ?>
												</td>
												<td data-title="<?= $this->lang->line('student_total') ?> <?= $this->lang->line('student_weaver') ?>">
													<?= number_format($totalPaymentWeaver, 2) ?>
												</td>
												<td data-title="<?= $this->lang->line('student_total') ?> <?= $this->lang->line('student_fine') ?>">
													<?= number_format($totalPaymentFine, 2) ?>
												</td>
												
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<div class="tab-pane" id="document">
								<?php if (permissionChecker('student_add')) { ?>
									<div class="doc-btn">
										<input class="btn-sm ose-btn" type="button" value="<?= $this->lang->line('student_add_document') ?>" data-toggle="modal" data-target="#documentupload">
									</div>
								<?php } ?>
								<div id="hide-table">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th><?= $this->lang->line('slno') ?></th>
												<th><?= $this->lang->line('student_title') ?></th>
												<th><?= $this->lang->line('student_date') ?></th>
												<th><?= $this->lang->line('action') ?></th>
											</tr>
										</thead>
										<tbody>
											<?php if (customCompute($documents)) {
												$i = 1;
												foreach ($documents as $document) {  ?>
													<tr>
														<td data-title="<?= $this->lang->line('slno') ?>">
															<?php echo $i; ?>
														</td>

														<td data-title="<?= $this->lang->line('student_title') ?>">
															<?= $document->title ?>
														</td>

														<td data-title="<?= $this->lang->line('student_date') ?>">
															<?= date('d M Y', strtotime($document->create_date)) ?>
														</td>

														<td data-title="<?= $this->lang->line('action') ?>">
															<?php
															if ((permissionChecker('student_add') && permissionChecker('student_delete')) || ($this->session->userdata('usertypeID') == 3 && $this->session->userdata('loginuserID') == $profile->srstudentID)) {
																echo btn_download('student/download_document/' . $document->documentID . '/' . $profile->srstudentID . '/' . $profile->srclassesID, $this->lang->line('download'));
															}

															if (permissionChecker('student_add') && permissionChecker('student_delete')) {
																echo btn_delete_show('student/delete_document/' . $document->documentID . '/' . $profile->srstudentID . "/" . $profile->srclassesID, $this->lang->line('delete'));
															}
															?>
														</td>
													</tr>
											<?php $i++;
												}
											} ?>
										</tbody>
									</table>
								</div>
							</div>
							<?php if (customCompute($studntTransportDetails) || customCompute($studntHostelDetails)) { ?>
								<div class="tab-pane" id="studentType">
									<div class="panel-body profile-view-dis">
										<div class="row">
											<table class="attendance_table">
												<thead>
													<?php if (customCompute($studntTransportDetails)) { ?>
														<tr>
															<th>#</th>
															<th>Type</th>
															<th>Route/HostelName</th>
															<th>Fee</th>
															<th>Join Date</th>
														</tr>
													<?php } ?>
												</thead>
												<tbody>
													<?php if (customCompute($studntTransportDetails)) { ?>
														<tr>
															<td>1</td>
															<td>TransPort</td>
															<td><?php echo $transports->route; ?></td>
															<td><?php echo $studntTransportDetails->tbalance; ?></td>
															<td><?php echo $studntTransportDetails->tjoindate; ?></td>
														</tr>
													<?php } ?>
													<?php if (customCompute($studntHostelDetails)) { ?>
														<tr>
															<td>2</td>
															<td>Hostel</td>
															<td><?php echo $hostels->name; ?></td>
															<td><?php echo $studntHostelDetails->hbalance; ?></td>
															<td><?php echo $studntHostelDetails->hjoindate; ?></td>
														</tr>
													<?php } ?>
												</tbody>

										</div>
									</div>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if (permissionChecker('student_add')) { ?>
		<form id="documentUploadDataForm" class="form-horizontal" enctype="multipart/form-data" role="form" action="<?= base_url('student/send_mail'); ?>" method="post">
			<div class="modal fade" id="documentupload">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title"><?= $this->lang->line('student_document_upload') ?></h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="title" class="col-sm-2 control-label">
									<?= $this->lang->line("student_title") ?> <span class="text-red">*</span>
								</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="title" name="title" value="<?= set_value('title') ?>">
								</div>
								<span class="col-sm-8 control-label" id="title_error">
								</span>
							</div>

							<div class="form-group">
								<label for="file" class="col-sm-2 control-label">
									<?= $this->lang->line("student_file") ?> <span class="text-red">*</span>
								</label>
								<div class="col-sm-8">
									<div class="input-group image-preview">
										<input type="text" class="form-control image-preview-filename" disabled="disabled">
										<span class="input-group-btn">
											<button type="button" class="btn btn-default image-preview-clear" style="display:none;">
												<span class="fa fa-remove"></span>
												<?= $this->lang->line('student_clear') ?>
											</button>
											<div class="btn btn-primary image-preview-input">
												<span class="fa fa-repeat"></span>
												<span class="image-preview-input-title">
													<?= $this->lang->line('student_file_browse') ?></span>
												<input type="file" id="file" name="file" />
											</div>
										</span>
									</div>
								</div>
								<span class="col-sm-8 control-label" id="file_error">
								</span>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?= $this->lang->line('close') ?></button>
							<input type="button" id="uploadfile" class="btn btn-primary" value="<?= $this->lang->line("student_upload") ?>" />
						</div>
					</div>
				</div>
			</div>
		</form>

		<script type="text/javascript">
			$(document).on('click', '#uploadfile', function() {
				var title = $('#title').val();
				var file = $('#file').val();
				var error = 0;

				if (title == '' || title == null) {
					error++;
					$('#title_error').html("<?= $this->lang->line('student_title_required') ?>");
					$('#title_error').parent().addClass('has-error');
				} else {
					$('#title_error').html('');
					$('#title_error').parent().removeClass('has-error');
				}

				if (file == '' || file == null) {
					error++;
					$('#file_error').html("<?= $this->lang->line('student_file_required') ?>");
					$('#file_error').parent().addClass('has-error');
				} else {
					$('#file_error').html('');
					$('#file_error').parent().removeClass('has-error');
				}

				if (error == 0) {
					var studentID = "<?= $profile->srstudentID ?>";
					var formData = new FormData($('#documentUploadDataForm')[0]);
					formData.append("studentID", studentID);
					$.ajax({
						type: 'POST',
						dataType: "json",
						url: "<?= base_url('student/documentUpload') ?>",
						data: formData,
						async: false,
						dataType: "html",
						success: function(data) {
							var response = jQuery.parseJSON(data);
							if (response.status) {
								$('#title_error').html();
								$('#title_error').parent().removeClass('has-error');

								$('#file_error').html();
								$('#file_error').parent().removeClass('has-error');
								location.reload();
							} else {
								if (response.errors['title']) {
									$('#title_error').html(response.errors['title']);
									$('#title_error').parent().addClass('has-error');
								} else {
									$('#title_error').html();
									$('#title_error').parent().removeClass('has-error');
								}

								if (response.errors['file']) {
									$('#file_error').html(response.errors['file']);
									$('#file_error').parent().addClass('has-error');
								} else {
									$('#file_error').html();
									$('#file_error').parent().removeClass('has-error');
								}
							}
						},
						cache: false,
						contentType: false,
						processData: false
					});
				}
			});

			$(function() {
				var closebtn = $('<button/>', {
					type: "button",
					text: 'x',
					id: 'close-preview',
					style: 'font-size: initial;',
				});
				closebtn.attr("class", "close pull-right");

				$('.image-preview').popover({
					trigger: 'manual',
					html: true,
					title: "<strong>Preview</strong>" + $(closebtn)[0].outerHTML,
					content: "There's no image",
					placement: 'bottom'
				});

				$('.image-preview-clear').click(function() {
					$('.image-preview').attr("data-content", "").popover('hide');
					$('.image-preview-filename').val("");
					$('.image-preview-clear').hide();
					$('.image-preview-input input:file').val("");
					$(".image-preview-input-title").text("<?= $this->lang->line('student_file_browse') ?>");
				});

				$(".image-preview-input input:file").change(function() {
					var img = $('<img/>', {
						id: 'dynamic',
						width: 250,
						height: 200,
						overflow: 'hidden'
					});

					var file = this.files[0];
					var reader = new FileReader();
					reader.onload = function(e) {
						$(".image-preview-input-title").text("<?= $this->lang->line('student_file_browse') ?>");
						$(".image-preview-clear").show();
						$(".image-preview-filename").val(file.name);
					}
					reader.readAsDataURL(file);
				});
			});
		</script>
	<?php } ?>

	<form class="form-horizontal" role="form" action="<?= base_url('student/send_mail'); ?>" method="post">
		<div class="modal fade" id="mail">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title"><?= $this->lang->line('mail') ?></h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="to" class="col-sm-2 control-label">
								<?= $this->lang->line("to") ?> <span class="text-red">*</span>
							</label>
							<div class="col-sm-6">
								<input type="email" class="form-control" id="to" name="to" value="<?= set_value('to') ?>">
							</div>
							<span class="col-sm-4 control-label" id="to_error">
							</span>
						</div>

						<div class="form-group">
							<label for="subject" class="col-sm-2 control-label">
								<?= $this->lang->line("subject") ?> <span class="text-red">*</span>
							</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="subject" name="subject" value="<?= set_value('subject') ?>">
							</div>
							<span class="col-sm-4 control-label" id="subject_error">
							</span>
						</div>

						<div class="form-group">
							<label for="message" class="col-sm-2 control-label">
								<?= $this->lang->line("message") ?>
							</label>
							<div class="col-sm-6">
								<textarea class="form-control" id="message" style="resize: vertical;" name="message" value="<?= set_value('message') ?>"></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" style="margin-bottom:0px;" data-dismiss="modal"><?= $this->lang->line('close') ?></button>
						<input type="button" id="send_pdf" class="btn btn-success" value="<?= $this->lang->line("send") ?>" />
					</div>
				</div>
			</div>
		</div>
	</form>

	<script language="javascript" type="text/javascript">
		function printDiv(divID) {
			var divElements = document.getElementById(divID).innerHTML;
			var oldPage = document.body.innerHTML;
			document.body.innerHTML =
				"<html><head><title></title></head><body>" +
				divElements + "</body>";
			window.print();
			document.body.innerHTML = oldPage;
			window.location.reload();
		}

		function closeWindow() {
			location.reload();
		}

		function check_email(email) {
			var status = false;
			var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
			if (email.search(emailRegEx) == -1) {
				$("#to_error").html('');
				$("#to_error").html("<?= $this->lang->line('mail_valid') ?>").css("text-align", "left").css("color", 'red');
			} else {
				status = true;
			}
			return status;
		}

		$('#send_pdf').click(function() {
			var to = $('#to').val();
			var subject = $('#subject').val();
			var message = $('#message').val();
			var id = "<?= $profile->srstudentID; ?>";
			var set = "<?= $set; ?>";
			var error = 0;

			$("#to_error").html("");
			if (to == "" || to == null) {
				error++;
				$("#to_error").html("");
				$("#to_error").html("<?= $this->lang->line('mail_to') ?>").css("text-align", "left").css("color", 'red');
			} else {
				if (check_email(to) == false) {
					error++
				}
			}

			if (subject == "" || subject == null) {
				error++;
				$("#subject_error").html("");
				$("#subject_error").html("<?= $this->lang->line('mail_subject') ?>").css("text-align", "left").css("color", 'red');
			} else {
				$("#subject_error").html("");
			}

			if (error == 0) {
				$('#send_pdf').attr('disabled', 'disabled');
				$.ajax({
					type: 'POST',
					url: "<?= base_url('student/send_mail') ?>",
					data: 'to=' + to + '&subject=' + subject + "&studentID=" + id + "&message=" + message + "&classesID=" + set,
					dataType: "html",
					success: function(data) {
						var response = JSON.parse(data);
						if (response.status == false) {
							$('#send_pdf').removeAttr('disabled');
							$.each(response, function(index, value) {
								if (index != 'status') {
									toastr["error"](value)
									toastr.options = {
										"closeButton": true,
										"debug": false,
										"newestOnTop": false,
										"progressBar": false,
										"positionClass": "toast-top-right",
										"preventDuplicates": false,
										"onclick": null,
										"showDuration": "500",
										"hideDuration": "500",
										"timeOut": "5000",
										"extendedTimeOut": "1000",
										"showEasing": "swing",
										"hideEasing": "linear",
										"showMethod": "fadeIn",
										"hideMethod": "fadeOut"
									}
								}
							});
						} else {
							location.reload();
						}
					}
				});
			}
		});

		$('.mark-bodyID').mCustomScrollbar({
			axis: "x"
		});

		$('.studentDIV').each(function() {
			$(this).mCustomScrollbar({
				axis: "x"
			});
		});

		$(document).ready(function(){
			var inv ="<?php echo $inv?$inv: '0' ?>";

			if(inv == 'inv'){
				$(".nav-tabs a:eq(5)").tab("show");
			}
		})
	</script>
<?php } ?>