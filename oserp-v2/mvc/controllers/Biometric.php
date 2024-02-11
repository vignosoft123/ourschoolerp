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

            $sql1 = "select teacherID from teacher where rfid=".$RFID;
            $teacherID = $this->db->query($sql1)->row()->teacherID;

            $day_num = $day;

           
            
           

            //checking attendance exists or not start
                $this->db->where('teacherID',$teacherID);
                $this->db->where('monthyear',$month.'-'.$year);
                $this->db->where('schoolyearID',$schoolyearID);
                $this->db->where('usertypeID',2);

                $res = $this->db->get('tattendance')->result_array();

                //$cnt = $this->db->get('tattendance')->num_rows();
                
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
                        'teacherID' => $teacherID,
                        'usertypeID' => 2,
                        'monthyear' => $month.'-'.$year,
                        'a'.$day_num => $present_or_latepresent,
                        'is_attendance' => 1,
                    );
                    $this->db->where('teacherID',$teacherID);
                    $this->db->update('tattendance',$attendance_update);
                }else{
                    $attendance_insert = array(
                        'schoolyearID' => $schoolyearID,
                        'teacherID' => $teacherID,
                        'usertypeID' => 2,
                        'monthyear' => $month.'-'.$year,
                        'a'.$day_num => $present_or_latepresent,
                        'is_attendance' => 1,
                    );
                    $this->db->insert('tattendance',$attendance_insert);
                }  
            }          
            // echo $this->db->last_query();die;
           
            echo '$RFID=0#';
           }
           else
           {
            echo 'Values are not registered';
           }
        }


       
	}

  

}