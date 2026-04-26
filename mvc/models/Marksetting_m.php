<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Marksetting_m extends MY_Model {

	protected $_table_name     = 'marksetting';
	protected $_primary_key    = 'marksettingID';
	protected $_primary_filter = 'intval';
	protected $_order_by       = "marksettingID";

	function __construct() {
		parent::__construct();
		$this->load->model('exam_m');
		$this->load->model('classes_m');
		$this->load->model('subject_m');
		$this->load->model('markpercentage_m');
	}

	public function get_marksetting($array=NULL, $single=FALSE) {
		return parent::get($array, $single);
	}

	public function get_order_by_marksetting($array=NULL) {
		return parent::get_order_by($array);
	}

	public function get_single_marksetting($array=NULL) {
		return parent::get_single($array);
	}

	public function insert_marksetting($array) {
		return parent::insert($array);
	}

	public function insert_batch_marksetting($array) {
		return parent::insert_batch($array);
	}

	public function update_marksetting($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function delete_marksetting($id){
		return parent::delete($id);
	}

	public function delete_marksetting_by_array($array=[]) {
		if(customCompute($array)) {
			$this->db->where($array);
			return $this->db->delete($this->_table_name);
		} 
		return FALSE;
	}

	public function get_marksetting_with_marksettingrelation($array=[]) {
		$this->db->select('*');
		$this->db->from('marksetting');
		$this->db->join('marksettingrelation', 'marksetting.marksettingID=marksettingrelation.marksettingID');
		if(customCompute($array)) {
			foreach ($array as $key=>$value) {
				$this->db->where("marksetting.$key", $value);
			}
		}
		$this->db->order_by('marksetting.marksettingID DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_exam($marktypeID= '', $classesID=0) {

		$schoolyearID  = $this->session->userdata('defaultschoolyearID');
		$classesID     = (int)$classesID;

		// Only return exams that have at least one subject scheduled in examschedule for this class.
		$scheduleFilter = $classesID
			? "EXISTS (SELECT 1 FROM examschedule WHERE examschedule.examID = exam.examID AND examschedule.classesID = $classesID)"
			: null;

		if($marktypeID == 4) {
			// marktypeID 4 delegates to exam table directly; apply schedule filter when classesID given
			$this->db->select('exam.*');
			$this->db->from('exam');
			if($scheduleFilter) {
				$this->db->where($scheduleFilter, null, false);
			}
			return $this->db->get()->result();
		} elseif(($marktypeID == 5) || ($marktypeID == 6)) {
			if($classesID) {
				$this->db->select('marksetting.*, exam.exam,exam.date');
				$this->db->from('marksetting');
				$this->db->join('exam', 'marksetting.examID=exam.examID');
				$this->db->where('marksetting.marktypeID', $marktypeID);
				$this->db->where('marksetting.classesID', $classesID);
				$this->db->where('exam.academic_year', $schoolyearID);
				if($scheduleFilter) {
					$this->db->where($scheduleFilter, null, false);
				}
				$query = $this->db->get();
				return $query->result();
			}
			return [];
		} else {
			$this->db->select('marksetting.*, exam.exam,exam.date');
			$this->db->from('marksetting');
			$this->db->join('exam', 'marksetting.examID=exam.examID');
			$this->db->where('marksetting.marktypeID', $marktypeID);
			$this->db->where('exam.academic_year', $schoolyearID);
			if($scheduleFilter) {
				$this->db->where($scheduleFilter, null, false);
			}
			$query = $this->db->get();
			return $query->result();
		}
	}

	public function get_exam_array($marktypeID= '', $classesID=0) {

		$schoolyearID = $this->session->userdata('defaultschoolyearID');

		if($marktypeID == 4) {
			return $this->exam_m->get_exam();
		} elseif(($marktypeID == 5) || ($marktypeID == 6)) {
			if((int)$classesID) {
				$this->db->select('marksetting.*, exam.exam,exam.date');
				$this->db->from('marksetting');
				$this->db->join('exam', 'marksetting.examID=exam.examID');
				$this->db->where('marksetting.marktypeID', $marktypeID);
				$this->db->where('marksetting.classesID', $classesID);
				$this->db->where('exam.academic_year', $schoolyearID);

				$query = $this->db->get();
				return $query->result_array();
			} 
			return [];
		} else {
			$this->db->select('marksetting.*, exam.exam,exam.date');
			$this->db->from('marksetting');
			$this->db->join('exam', 'marksetting.examID=exam.examID');
			$this->db->where('marksetting.marktypeID', $marktypeID);
			$this->db->where('exam.academic_year', $schoolyearID);

			$query = $this->db->get();
			return $query->result_array();
		}
	}

    public function get_exam_with_class( $classesID = 0 )
    {
		$schoolyearID = $this->session->userdata('defaultschoolyearID');

        $exams      = [];
        $marktypeID = $this->data['siteinfos']->marktypeID;
        if ( $marktypeID == 4 ) {
            $exams = $this->exam_m->get_exam();
        } elseif ( ( $marktypeID == 5 ) || ( $marktypeID == 6 ) ) {
            if ( (int) $classesID ) {
                $this->db->select('marksetting.*, exam.exam');
                $this->db->from('marksetting');
                $this->db->join('exam', 'marksetting.examID=exam.examID');
                $this->db->where('marksetting.marktypeID', $marktypeID);
                $this->db->where('marksetting.classesID', $classesID);
				$this->db->where('exam.academic_year', $schoolyearID);

                $query = $this->db->get();
                $exams =  $query->result();
            }
        } else {
            $this->db->select('marksetting.*, exam.exam');
            $this->db->from('marksetting');
            $this->db->join('exam', 'marksetting.examID=exam.examID');
            $this->db->where('marksetting.marktypeID', $marktypeID);
			$this->db->where('exam.academic_year', $schoolyearID);

            $query = $this->db->get();
            $exams = $query->result();
        }

        if(customCompute($exams)) {
            $exams = pluck($exams, 'obj', 'examID');
            return $exams;
        }
        return [];
    }

	
    public function get_exam_with_class_array( $classesID = 0 )
    {
		$schoolyearID = $this->session->userdata('defaultschoolyearID');

        $exams      = [];
        $marktypeID = $this->data['siteinfos']->marktypeID;
        if ( $marktypeID == 4 ) {
            $exams = $this->exam_m->get_exam();
        } elseif ( ( $marktypeID == 5 ) || ( $marktypeID == 6 ) ) {
            if ( (int) $classesID ) {
                $this->db->select('marksetting.*, exam.exam');
                $this->db->from('marksetting');
                $this->db->join('exam', 'marksetting.examID=exam.examID');
                $this->db->where('marksetting.marktypeID', $marktypeID);
                $this->db->where('marksetting.classesID', $classesID);
				$this->db->where('exam.academic_year', $schoolyearID);

                $query = $this->db->get();
                $exams =  $query->result_array();
            }
        } else {
            $this->db->select('marksetting.*, exam.exam');
            $this->db->from('marksetting');
            $this->db->join('exam', 'marksetting.examID=exam.examID');
            $this->db->where('marksetting.marktypeID', $marktypeID);
			$this->db->where('exam.academic_year', $schoolyearID);

            $query = $this->db->get();
            $exams = $query->result_array();
        }

        if(customCompute($exams)) {
            $exams = pluck($exams, 'obj', 'examID');
            return $exams;
        }
        return [];
    }

	public function get_marksetting_markpercentages_add($array) {
		extract($array);
		$finalmark = 100;
		if(customCompute($subject)) {
			$finalmark = $subject->finalmark;
		}

		$queryArray['marktypeID']   = (int)$marktypeID;
		
		if(($marktypeID == 2) || ($marktypeID == 3) || ($marktypeID == 5) || ($marktypeID == 6)) {
			$queryArray['examID']   = (int)$examID;
		}
		
		if(($marktypeID == 1) || ($marktypeID == 4) || ($marktypeID == 5) || ($marktypeID == 6)) {
			$queryArray['classesID']= (int)$classesID;
		}
		if(($marktypeID == 4) || ($marktypeID == 6)) {
			$queryArray['subjectID']= (int)$subjectID;
		}
		$marksettingArr       = pluck($this->get_marksetting_with_marksettingrelation($queryArray), 'markpercentageID', 'markpercentageID');
		$markpercentages  = $this->markpercentage_m->get_markpercentage();
		
		$retMarkpercentages = [];
		if(customCompute($markpercentages)) {
			foreach ($markpercentages as $markpercentage) {
				if(in_array($markpercentage->markpercentageID, $marksettingArr)) {
				// 	$markpercentage->percentage = convertMarkpercentage($markpercentage->percentage, $finalmark);
					$retMarkpercentages[$markpercentage->markpercentageID] = $markpercentage;
				}
			}
		}
		return $retMarkpercentages;
	}

	private function prefixLoad($array) {
		if(is_array($array)) {
			if(customCompute($array)) {
				foreach ($array as $arkey =>  $ar) {
					$array['examschedule'.'.'.$arkey] = $ar;
					unset($array[$arkey]);
				}
			}
		}
		return $array;
	}

	/*public function get_join_examschedule_with_exam_classes_section_subject($classID,$sectionID) {
		$array = $this->prefixLoad($array);
		$this->db->distinct(); // Make results distinct
		$this->db->select('exam.examID'); // Select only examID (you can add exam.exam if needed)
		$this->db->from('examschedule');
		$this->db->join('exam', 'exam.examID = examschedule.examID', 'LEFT');
		$this->db->join('classes', 'classes.classesID = examschedule.classesID', 'LEFT');
		$this->db->join('section', 'section.sectionID = examschedule.sectionID', 'LEFT');
		$this->db->join('subject', 'subject.subjectID = examschedule.subjectID', 'LEFT');
		$this->db->join('mark', 'mark.examID = examschedule.examID', 'LEFT');
		$this->db->join('markrelation', 'markrelation.markID = mark.markID', 'LEFT');
		
		$this->db->where('markrelation.mark IS NOT NULL');
		$this->db->where('mark.studentID', 1);
		$this->db->where('mark.classesID', $classID);
 		$query = $this->db->get();
		// echo $this->db->last_query();die;
		return $query->result();
		 
	}*/

	public function get_join_examschedule_with_exam_classes_section_subject($classID, $sectionID) {
		$schoolyearID = $this->session->userdata('defaultschoolyearID');

		$this->db->select('
			exam.examID,
			mark.studentID,
			mark.classesID,
			mark.subjectID,
			markrelation.mark,
			markrelation.markpercentageID,
			subject.subjectID,
			examschedule.sectionID
		');
		$this->db->from('examschedule');
		$this->db->join('exam', 'exam.examID = examschedule.examID', 'LEFT');
		$this->db->join('classes', 'classes.classesID = examschedule.classesID', 'LEFT');
		$this->db->join('section', 'section.sectionID = examschedule.sectionID', 'LEFT');
		$this->db->join('subject', 'subject.subjectID = examschedule.subjectID', 'LEFT');
		$this->db->join('mark', 'mark.examID = examschedule.examID AND mark.subjectID = examschedule.subjectID', 'LEFT');
		$this->db->join('markrelation', 'markrelation.markID = mark.markID', 'LEFT');
	
		// Filtering
		$this->db->where('markrelation.mark IS NOT NULL');
		$this->db->where('mark.classesID', $classID);
		$this->db->where('examschedule.sectionID', $sectionID);
		$this->db->where('mark.schoolyearID', $schoolyearID);
	
		$query = $this->db->get();
		// echo $this->db->last_query();die;
		return $query->result();
	}
	


	public function get_marksetting_markpercentages() {
		$marktypeID = (int)$this->data['siteinfos']->marktypeID;
		$exclassID  = (int)$this->data['siteinfos']->ex_class;
        $examID = (isset($_POST['examID'])) ? $_POST['examID'] : 0;
		$classes    = $this->classes_m->get_order_by_classes([]);//'classesID !='=> $exclassID
		$exams      = $this->exam_m->get_order_by_exam(['examID'=>$examID],FALSE);
		 
		if($examID==0)
		{
		    $exams      = $this->exam_m->get_order_by_exam([],FALSE);
		}
		
		$subjects   = pluck_multi_array($this->subject_m->get_subject(), 'obj', 'classesID');
		
		$marksettingrelations          = $this->get_marksetting_with_marksettingrelation();
		$retglobalmarksettingArr       = [];
		$retclasswisemarksettingArr    = [];
		$retsubjectwisemarksettingArr  = [];
		$retclassexamwisemarksettingArr= [];
		$retclassexamsubjectsettingArr = [];

		if(customCompute($marksettingrelations)) {
			foreach ($marksettingrelations as $marksettingrelation) {
				if($marksettingrelation->marktypeID != $marktypeID) {
					continue;
				}
				$retglobalmarksettingArr[$marksettingrelation->examID][$marksettingrelation->markpercentageID] = $marksettingrelation->markpercentageID;
				$retclasswisemarksettingArr[$marksettingrelation->examID][$marksettingrelation->classesID][$marksettingrelation->markpercentageID] = (int)$marksettingrelation->markpercentageID;
				$retsubjectwisemarksettingArr[$marksettingrelation->classesID][$marksettingrelation->subjectID][$marksettingrelation->markpercentageID] = (int)$marksettingrelation->markpercentageID;
				$retclassexamwisemarksettingArr[$marksettingrelation->classesID][$marksettingrelation->examID][$marksettingrelation->markpercentageID] = (int)$marksettingrelation->markpercentageID;
				$retclassexamsubjectsettingArr[$marksettingrelation->classesID][$marksettingrelation->examID][$marksettingrelation->subjectID][$marksettingrelation->markpercentageID] = (int)$marksettingrelation->markpercentageID;
						
			}
		}

		$retMarkpercentages = [];
		if(customCompute($classes)) {
			foreach ($classes as $class) {
				if($marktypeID == 0) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retglobalmarksettingArr[$exam->examID])) {
								$subjectsArr          = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								$retmarkpercentageArr = $retglobalmarksettingArr[$exam->examID];
								asort($retmarkpercentageArr);
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;
									}
								}
							}
						}
					}
				} else if($marktypeID == 1) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retclasswisemarksettingArr[$exam->examID])) {
								$subjectsArr               = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								$retclassmarkpercentageArr = isset($retclasswisemarksettingArr[$exam->examID][$class->classesID]) ? $retclasswisemarksettingArr[$exam->examID][$class->classesID] : [];
								asort($retclassmarkpercentageArr);
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retclassmarkpercentageArr;
									}
								}
							}
						}
					}
				} else if($marktypeID == 2) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retglobalmarksettingArr[$exam->examID])) {
								$subjectsArr          = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								$retmarkpercentageArr = $retglobalmarksettingArr[$exam->examID];
								asort($retmarkpercentageArr);
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;
									}
								}
							}
						}
					}
				} else if($marktypeID == 3) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retglobalmarksettingArr[$exam->examID])) {
								$subjectsArr          = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								$retmarkpercentageArr = $retglobalmarksettingArr[$exam->examID];
								asort($retmarkpercentageArr);
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;
									}
								}
							}
						}
					}
				} else if($marktypeID == 4) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							$subjectsArr         = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
							$uniquePercentageArr = [];
							if(customCompute($subjectsArr)) {
								foreach ($subjectsArr as $subject) {
									$retmarkpercentageArr    = isset($retsubjectwisemarksettingArr[$class->classesID][$subject->subjectID]) ? $retsubjectwisemarksettingArr[$class->classesID][$subject->subjectID] : [];
									asort($retmarkpercentageArr);
									$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;

									if(customCompute($retmarkpercentageArr)) {
										foreach ($retmarkpercentageArr as $markpercentageID) {
											if(!isset($uniquePercentageArr[$markpercentageID])) {
												$uniquePercentageArr[$markpercentageID] = $markpercentageID; 
											}
										}
									}
								}
							}

							asort($uniquePercentageArr);
							if(customCompute($subjectsArr)) {
								foreach ($subjectsArr as $subject) {
									$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['unique'] = $uniquePercentageArr;
								}
							}
						}
					}
				} else if($marktypeID == 5) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retclassexamwisemarksettingArr[$class->classesID][$exam->examID])) {
								$retmarkpercentageArr    = $retclassexamwisemarksettingArr[$class->classesID][$exam->examID];
								asort($retmarkpercentageArr);
								$subjectsArr  = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;
									}
								}
							}
						}
					}
				} else if($marktypeID == 6) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retclassexamsubjectsettingArr[$class->classesID][$exam->examID])) {
								$subjectsArr         = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								$uniquePercentageArr = [];
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										if(isset($retclassexamsubjectsettingArr[$class->classesID][$exam->examID][$subject->subjectID])) {

											$retmarkpercentageArr    = $retclassexamsubjectsettingArr[$class->classesID][$exam->examID][$subject->subjectID];
											asort($retmarkpercentageArr);
											$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;

											if(customCompute($retmarkpercentageArr)) {
												foreach ($retmarkpercentageArr as $markpercentageID) {
													if(!isset($uniquePercentageArr[$markpercentageID])) {
														$uniquePercentageArr[$markpercentageID] = $markpercentageID; 
													}
												}
											}

										}
									}

								}

								asort($uniquePercentageArr);
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										if(isset($retclassexamsubjectsettingArr[$class->classesID][$exam->examID][$subject->subjectID])) {
											$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['unique'] = $uniquePercentageArr;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $retMarkpercentages;
	}

	public function get_marksetting_markpercentages_new($classID,$sectionID) {
		$marktypeID = (int)$this->data['siteinfos']->marktypeID;
		$exclassID  = (int)$this->data['siteinfos']->ex_class;
        $examID = (isset($_POST['examID'])) ? $_POST['examID'] : 0;
		$classes    = $this->classes_m->get_order_by_classes([]);//'classesID !='=> $exclassID
		// $exams      = $this->exam_m->get_order_by_exam(['examID'=>$examID],FALSE);
		$exams      = $this->get_join_examschedule_with_exam_classes_section_subject($classID,$sectionID);
		if($examID==0)
		{
		    $exams      = $this->get_join_examschedule_with_exam_classes_section_subject($classID,$sectionID);
		    // $exams      = $this->exam_m->get_order_by_exam([],FALSE);
		}
		
		$subjects   = pluck_multi_array($this->subject_m->get_subject(), 'obj', 'classesID');
		
		$marksettingrelations          = $this->get_marksetting_with_marksettingrelation();
		$retglobalmarksettingArr       = [];
		$retclasswisemarksettingArr    = [];
		$retsubjectwisemarksettingArr  = [];
		$retclassexamwisemarksettingArr= [];
		$retclassexamsubjectsettingArr = [];

		if(customCompute($marksettingrelations)) {
			foreach ($marksettingrelations as $marksettingrelation) {
				if($marksettingrelation->marktypeID != $marktypeID) {
					continue;
				}
				$retglobalmarksettingArr[$marksettingrelation->examID][$marksettingrelation->markpercentageID] = $marksettingrelation->markpercentageID;
				$retclasswisemarksettingArr[$marksettingrelation->examID][$marksettingrelation->classesID][$marksettingrelation->markpercentageID] = (int)$marksettingrelation->markpercentageID;
				$retsubjectwisemarksettingArr[$marksettingrelation->classesID][$marksettingrelation->subjectID][$marksettingrelation->markpercentageID] = (int)$marksettingrelation->markpercentageID;
				$retclassexamwisemarksettingArr[$marksettingrelation->classesID][$marksettingrelation->examID][$marksettingrelation->markpercentageID] = (int)$marksettingrelation->markpercentageID;
				$retclassexamsubjectsettingArr[$marksettingrelation->classesID][$marksettingrelation->examID][$marksettingrelation->subjectID][$marksettingrelation->markpercentageID] = (int)$marksettingrelation->markpercentageID;
						
			}
		}

		$retMarkpercentages = [];
		if(customCompute($classes)) {
			foreach ($classes as $class) {
				if($marktypeID == 0) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retglobalmarksettingArr[$exam->examID])) {
								$subjectsArr          = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								$retmarkpercentageArr = $retglobalmarksettingArr[$exam->examID];
								asort($retmarkpercentageArr);
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;
									}
								}
							}
						}
					}
				} else if($marktypeID == 1) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retclasswisemarksettingArr[$exam->examID])) {
								$subjectsArr               = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								$retclassmarkpercentageArr = isset($retclasswisemarksettingArr[$exam->examID][$class->classesID]) ? $retclasswisemarksettingArr[$exam->examID][$class->classesID] : [];
								asort($retclassmarkpercentageArr);
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retclassmarkpercentageArr;
									}
								}
							}
						}
					}
				} else if($marktypeID == 2) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retglobalmarksettingArr[$exam->examID])) {
								$subjectsArr          = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								$retmarkpercentageArr = $retglobalmarksettingArr[$exam->examID];
								asort($retmarkpercentageArr);
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;
									}
								}
							}
						}
					}
				} else if($marktypeID == 3) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retglobalmarksettingArr[$exam->examID])) {
								$subjectsArr          = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								$retmarkpercentageArr = $retglobalmarksettingArr[$exam->examID];
								asort($retmarkpercentageArr);
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;
									}
								}
							}
						}
					}
				} else if($marktypeID == 4) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							$subjectsArr         = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
							$uniquePercentageArr = [];
							if(customCompute($subjectsArr)) {
								foreach ($subjectsArr as $subject) {
									$retmarkpercentageArr    = isset($retsubjectwisemarksettingArr[$class->classesID][$subject->subjectID]) ? $retsubjectwisemarksettingArr[$class->classesID][$subject->subjectID] : [];
									asort($retmarkpercentageArr);
									$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;

									if(customCompute($retmarkpercentageArr)) {
										foreach ($retmarkpercentageArr as $markpercentageID) {
											if(!isset($uniquePercentageArr[$markpercentageID])) {
												$uniquePercentageArr[$markpercentageID] = $markpercentageID; 
											}
										}
									}
								}
							}

							asort($uniquePercentageArr);
							if(customCompute($subjectsArr)) {
								foreach ($subjectsArr as $subject) {
									$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['unique'] = $uniquePercentageArr;
								}
							}
						}
					}
				} else if($marktypeID == 5) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retclassexamwisemarksettingArr[$class->classesID][$exam->examID])) {
								$retmarkpercentageArr    = $retclassexamwisemarksettingArr[$class->classesID][$exam->examID];
								asort($retmarkpercentageArr);
								$subjectsArr  = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;
									}
								}
							}
						}
					}
				} else if($marktypeID == 6) {
					if(customCompute($exams)) {
						foreach($exams as $exam) {
							if(isset($retclassexamsubjectsettingArr[$class->classesID][$exam->examID])) {
								$subjectsArr         = isset($subjects[$class->classesID]) ? $subjects[$class->classesID] : [];
								$uniquePercentageArr = [];
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										if(isset($retclassexamsubjectsettingArr[$class->classesID][$exam->examID][$subject->subjectID])) {

											$retmarkpercentageArr    = $retclassexamsubjectsettingArr[$class->classesID][$exam->examID][$subject->subjectID];
											asort($retmarkpercentageArr);
											$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['own'] = $retmarkpercentageArr;

											if(customCompute($retmarkpercentageArr)) {
												foreach ($retmarkpercentageArr as $markpercentageID) {
													if(!isset($uniquePercentageArr[$markpercentageID])) {
														$uniquePercentageArr[$markpercentageID] = $markpercentageID; 
													}
												}
											}

										}
									}

								}

								asort($uniquePercentageArr);
								if(customCompute($subjectsArr)) {
									foreach ($subjectsArr as $subject) {
										if(isset($retclassexamsubjectsettingArr[$class->classesID][$exam->examID][$subject->subjectID])) {
											$retMarkpercentages[$class->classesID][$exam->examID][$subject->subjectID]['unique'] = $uniquePercentageArr;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $retMarkpercentages;
	}

}