<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Push_notification_m extends CI_Model {

    public function get_students_with_tokens($classesID = null, $sectionID = null, $studentIDs = null) {
        $this->db->select('s.studentID, s.name, s.device_token, s.classesID, s.sectionID, c.classes, sec.section');
        $this->db->from('student s');
        $this->db->join('classes c', 'c.classesID = s.classesID', 'left');
        $this->db->join('section sec', 'sec.sectionID = s.sectionID', 'left');
        $this->db->where('s.device_token IS NOT NULL', null, false);
        $this->db->where("s.device_token != ''");
        if ($classesID) {
            $this->db->where('s.classesID', $classesID);
        }
        if ($sectionID) {
            $this->db->where('s.sectionID', $sectionID);
        }
        if (!empty($studentIDs)) {
            $this->db->where_in('s.studentID', $studentIDs);
        }
        return $this->db->get()->result();
    }

    // Returns students list for select2 dropdown (with or without token filter)
    public function get_students_for_select($classesID = null) {
        $this->db->select('s.studentID, s.name, c.classes, sec.section');
        $this->db->from('student s');
        $this->db->join('classes c', 'c.classesID = s.classesID', 'left');
        $this->db->join('section sec', 'sec.sectionID = s.sectionID', 'left');
        if ($classesID) {
            $this->db->where('s.classesID', $classesID);
        }
        $this->db->order_by('c.classes_numeric, s.name');
        return $this->db->get()->result();
    }

    // Used by cascading filter (Role→Year→Class→Section→Users) — only returns students with app installed
    public function load_students_for_filter($schoolyearID = null, $classesID = null, $sectionID = null) {
        $this->db->select('s.studentID, s.name');
        $this->db->from('student s');
        $this->db->join('studentrelation sr', 'sr.srstudentID = s.studentID', 'inner');
        $this->db->where('s.device_token IS NOT NULL', null, false);
        $this->db->where("s.device_token != ''");
        if ($schoolyearID) $this->db->where('sr.srschoolyearID', $schoolyearID);
        if ($classesID)    $this->db->where('sr.srclassesID', $classesID);
        if ($sectionID > 0) $this->db->where('sr.srsectionID', $sectionID);
        $this->db->group_by('s.studentID');
        $this->db->order_by('s.name', 'ASC');
        return $this->db->get()->result();
    }

    public function log_notification($data) {
        return $this->db->insert('push_notification_log', $data);
    }

    public function get_history($limit = 50) {
        $this->db->order_by('sent_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('push_notification_log')->result();
    }
}
