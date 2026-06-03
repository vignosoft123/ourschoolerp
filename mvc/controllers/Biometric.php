<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Biometric extends CI_Controller {
 
    function __construct() {
        parent::__construct();
        // $this->load->model("biometric_m");
        $this->load->library("session"); 
       
    }
 
	public function index() {

        //http://ourschoolerp.com/Biometric?$99999&99&1234567890&20082023123025*4
        //orgid, mecineid, rfid, datetme

        $qs = str_replace('$', '', $_SERVER['QUERY_STRING']); // get rid of the $
        $qs = str_replace('*', '', $qs); // get rid of the *

        $submissions = explode(',', $qs); // split the subs

        $SID = ""; // store for sid
        $MID = ""; // store for mid

        $sql = "select schoolyearID from schoolyear order by schoolyearID desc limit 0,1";
        $schoolyearID = $this->db->query($sql)->row()->schoolyearID;

        $sql_setting = "SELECT * FROM setting where fieldoption='teacher_present_time' ";
		$present_timings = $this->db->query($sql_setting)->row()->value;

        // $sql_setting1 = "SELECT * FROM setting where fieldoption='teacher_late_present_time' ";
		// $late_present_timings = $this->db->query($sql_setting1)->row()->value;

        //  echo "<pre>";print_r($present_timings);
        //  echo "<pre>";print_r($late_present_timings);die;
       // $teacher_setting_time = 

        for ($i = 0; $i < count($submissions); $i++) {
            $sections = explode('&', $submissions[$i]);
        
            if($i == 0) {
                $SID = $sections[0];
                $MID = $sections[1];
                $RFID = $sections[2];
                $DOT = $sections[3];
            } else {
                $RFID = $sections[0];
                 $DOT = $sections[1];
               
            }
            // Get mysql database details from config.php file 
            // Insert the query parameters into the table
            // include("config.php");//mysql connection 
            //$date = '30/01/2011, 4:57 pm';
            //$to_mysql_date = $DOT;
            //$sd = date("Y-m-d H:i:s",$to_mysql_date)
            $string = $DOT;
            
            $day = substr($string, 0, 2);
            $month = substr($string, 2, 2);
            $year = substr($string, 4, 4);
            
            $hour = substr($string, 8, 2);
            $min = substr($string, 10, 2);
            $sec = substr($string, 12, 2);
            
             $result_date = $year.'-'.$month.'-'.$day.' '.$hour.':'.$min.':'.$sec;
             $punchin_time = $hour.':'.$min.':'.$sec;
             
             $punchin_time = strtotime($punchin_time);
             $present_setting_timings = strtotime($present_timings);

             if($present_setting_timings >= $punchin_time){
                $present_or_latepresent = "P";
             }else{
                $present_or_latepresent = "L";
             }

             echo '$RFID=0#';
             
           $data = array(
                'sid' => $SID,
                'mid' => $MID,
                'rfid' => $RFID,
                'date_time' => $result_date,
                'date' => $year.'-'.$month.'-'.$day,
                'time' => $hour.':'.$min.':'.$sec

           );        

           $insert = $this->db->insert('biometric',$data);
           
           if($insert)
           {
            $day_num = $day;
            $prefix  = strtolower(substr($RFID, 0, 1));

            if ($prefix === 's') {
                // ── STUDENT ──────────────────────────────────────────────────
                $sq = $this->db->query("SELECT studentID FROM student WHERE rf_id = ?", [$RFID])->row();
                if ($sq) {
                    $studentID = $sq->studentID;
                    $s_row = $this->db->query("SELECT value FROM setting WHERE fieldoption='student_present_time' LIMIT 1")->row();
                    $student_present_ts = strtotime($s_row ? $s_row->value : $present_timings);
                    $student_status = ($student_present_ts >= $punchin_time) ? "P" : "L";

                    $sr = $this->db->query(
                        "SELECT srclassesID, srsectionID FROM studentrelation WHERE srstudentID=? AND srschoolyearID=? LIMIT 1",
                        [$studentID, $schoolyearID]
                    )->row();

                    if ($sr) {
                        $day_col = 'a' . $day_num;
                        $att = $this->db->get_where('attendance', [
                            'studentID'    => $studentID,
                            'monthyear'    => $month.'-'.$year,
                            'schoolyearID' => $schoolyearID,
                        ])->row();
                        if ($att) {
                            if (empty($att->$day_col)) {
                                $this->db->where('attendanceID', $att->attendanceID);
                                $this->db->update('attendance', [$day_col => $student_status]);
                            }
                        } else {
                            $this->db->insert('attendance', [
                                'schoolyearID' => $schoolyearID,
                                'studentID'    => $studentID,
                                'classesID'    => $sr->srclassesID,
                                'sectionID'    => $sr->srsectionID,
                                'userID'       => 0,
                                'usertype'     => 'Biometric',
                                'monthyear'    => $month.'-'.$year,
                                $day_col       => $student_status,
                            ]);
                        }
                    }
                }

            } elseif ($prefix === 'u') {
                // ── USER / NON-TEACHING STAFF ─────────────────────────────────
                $urow = $this->db->query("SELECT userID, usertypeID FROM user WHERE rf_id = ?", [$RFID])->row();
                if ($urow) {
                    $s_row = $this->db->query("SELECT value FROM setting WHERE fieldoption='student_present_time' LIMIT 1")->row();
                    $user_present_ts = strtotime($s_row ? $s_row->value : $present_timings);
                    $user_status = ($user_present_ts >= $punchin_time) ? "P" : "L";

                    $day_col = 'a' . $day_num;
                    $uatt = $this->db->get_where('uattendance', [
                        'userID'       => $urow->userID,
                        'monthyear'    => $month.'-'.$year,
                        'schoolyearID' => $schoolyearID,
                    ])->row();
                    if ($uatt) {
                        if (empty($uatt->$day_col)) {
                            $this->db->where('uattendanceID', $uatt->uattendanceID);
                            $this->db->update('uattendance', [$day_col => $user_status]);
                        }
                    } else {
                        $this->db->insert('uattendance', [
                            'schoolyearID' => $schoolyearID,
                            'userID'       => $urow->userID,
                            'usertypeID'   => $urow->usertypeID,
                            'monthyear'    => $month.'-'.$year,
                            $day_col       => $user_status,
                        ]);
                    }
                }

            } else {
                // ── TEACHER (existing logic — unchanged) ──────────────────────
                $teacherID = null;
                $query = $this->db->query("SELECT teacherID FROM teacher WHERE rfid = ?", [$RFID]);
                if ($query->num_rows() > 0) {
                    $teacherID = $query->row()->teacherID;
                }

                if (!empty($teacherID)) {
                    //checking attendance exists or not start
                    $this->db->where('teacherID',$teacherID);
                    $this->db->where('monthyear',$month.'-'.$year);
                    $this->db->where('schoolyearID',$schoolyearID);
                    $this->db->where('usertypeID',2);

                    $res = $this->db->get('tattendance')->result_array();

                    if(empty($res[0]['a'.$day_num]) ){  //if new date comes then update is_attendance as 0

                        $this->db->where('teacherID',$teacherID);
                        $this->db->where('monthyear',$month.'-'.$year);
                        $this->db->where('schoolyearID',$schoolyearID);
                        $this->db->where('usertypeID',2);
                        $this->db->update("tattendance",array('is_attendance'=>0) );
                    }

                    $this->db->where('teacherID',$teacherID);
                    $this->db->where('monthyear',$month.'-'.$year);
                    $this->db->where('schoolyearID',$schoolyearID);
                    $this->db->where('usertypeID',2);

                    $res = $this->db->get('tattendance')->result_array();
                    $cnt = $this->db->get('tattendance')->num_rows();
                    //checking attendance exists or not end

                    if($res[0]['is_attendance'] == 0){
                        if($cnt > 0){
                            $attendance_update = array(
                                'schoolyearID' => $schoolyearID,
                                'teacherID'    => $teacherID,
                                'usertypeID'   => 2,
                                'monthyear'    => $month.'-'.$year,
                                'a'.$day_num   => $present_or_latepresent,
                                'is_attendance'=> 1,
                            );
                            $this->db->where('teacherID',$teacherID);
                            $this->db->update('tattendance',$attendance_update);
                        } else {
                            $attendance_insert = array(
                                'schoolyearID' => $schoolyearID,
                                'teacherID'    => $teacherID,
                                'usertypeID'   => 2,
                                'monthyear'    => $month.'-'.$year,
                                'a'.$day_num   => $present_or_latepresent,
                                'is_attendance'=> 1,
                            );
                            $this->db->insert('tattendance',$attendance_insert);
                        }
                    }
                }
            }

           }
           else
           {
            echo 'Values are not registered';
           }
        }


       
	}

  

}