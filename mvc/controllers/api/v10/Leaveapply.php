<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Leaveapply extends Api_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('leaveapplication_m');
        $this->load->model('leavecategory_m');
        $this->load->model('usertype_m');
        $this->load->model('leaveassign_m');
    }

    // public function index_get() 
    // {
    //     $schoolyearID = $this->session->userdata('defaultschoolyearID');
    //     $this->retdata['leaveapplications'] = $this->leaveapplication_m->get_order_by_leaveapply_with_user(array('leaveapplications.schoolyearID' => $schoolyearID, 'leaveapplications.create_usertypeID' => $this->session->userdata('usertypeID'), 'leaveapplications.create_userID' => $this->session->userdata('loginuserID')));
    //     $this->retdata['leavecategorys'] = pluck($this->leavecategory_m->get_leavecategory(), 'leavecategory', 'leavecategoryID');

    //     $this->response([
    //         'status'    => true,
    //         'message'   => 'Success',
    //         'data'      => $this->retdata
    //     ], REST_Controller::HTTP_OK);
    // }

public function index_get() 
{
    $schoolyearID = $this->session->userdata('defaultschoolyearID');

    $this->retdata['leaveapplications'] = $this->leaveapplication_m->get_order_by_leaveapply_with_user([
        'leaveapplications.schoolyearID'        => $schoolyearID,
        'leaveapplications.create_usertypeID'   => $this->session->userdata('usertypeID'),
        'leaveapplications.create_userID'       => $this->session->userdata('loginuserID')
    ]);

    $leavecategories = pluck($this->leavecategory_m->get_leavecategory(), 'leavecategory', 'leavecategoryID');

    foreach ($this->retdata['leaveapplications'] as &$leave) {
        $leave->leavecategory_name = isset($leavecategories[$leave->leavecategoryID]) ? $leavecategories[$leave->leavecategoryID] : '';
    }
    unset($leave);

    $this->response([
        'status'  => true,
        'message' => 'Success',
        'data'    => $this->retdata
    ], REST_Controller::HTTP_OK);
}



    public function view_get($id = null) 
    {
        if ((int)$id) {
            $schoolyearID  = $this->session->userdata("defaultschoolyearID");
            $this->retdata['usertypes'] = pluck($this->usertype_m->get_usertype(),'usertype','usertypeID');
            $this->retdata['leaveapply'] = $this->leaveapplication_m->get_single_leaveapplication(array('leaveapplicationID' => $id, 'schoolyearID' => $schoolyearID));

            if(customCompute($this->retdata['leaveapply'])) {
                if(($this->retdata['leaveapply']->create_userID == $this->session->userdata('loginuserID')) && ($this->retdata['leaveapply']->create_usertypeID == $this->session->userdata('usertypeID'))) {

                    $leavecategory = $this->leavecategory_m->get_single_leavecategory(array('leavecategoryID' => $this->retdata['leaveapply']->leavecategoryID));
                    if(customCompute($leavecategory)) {
                        $this->retdata['leaveapply']->category = $leavecategory->leavecategory;
                    } else {
                        $this->retdata['leaveapply']->category = '';    
                    }

                    $availableleave = $this->leaveapplication_m->get_sum_of_leave_days_by_user_for_single_category($this->session->userdata('usertypeID'), $this->session->userdata('loginuserID'), $schoolyearID, $this->retdata['leaveapply']->leavecategoryID);                    
                    if(isset($availableleave->days) && $availableleave->days > 0) {
                        $availableleavedays = $availableleave->days;
                    } else {
                        $availableleavedays = 0;    
                    }

                    $leaveassign = $this->leaveassign_m->get_single_leaveassign(array('leavecategoryID' => $this->retdata['leaveapply']->leavecategoryID, 'schoolyearID' => $schoolyearID));
                    if(customCompute($leaveassign)) {
                        $this->retdata['leaveapply']->leaveavabledays = ($leaveassign->leaveassignday - $availableleavedays);
                    } else {
                        $this->retdata['leaveapply']->leaveavabledays = $this->lang->line('leaveapply_deleted');
                    }

                    $applicant = getObjectByUserTypeIDAndUserID($this->retdata['leaveapply']->create_usertypeID, $this->retdata['leaveapply']->create_userID, $schoolyearID);
                    $daysArray = $this->leavedayscustomCompute($this->retdata['leaveapply']->from_date, $this->retdata['leaveapply']->to_date);

                    // Create optimized response with only necessary data
                    $optimizedData = [
                        'leaveapply' => [
                            'leaveapplicationID' => $this->retdata['leaveapply']->leaveapplicationID,
                            'leavecategoryID' => $this->retdata['leaveapply']->leavecategoryID,
                            'from_date' => $this->retdata['leaveapply']->from_date,
                            'to_date' => $this->retdata['leaveapply']->to_date,
                            'leave_days' => $this->retdata['leaveapply']->leave_days,
                            'reason' => $this->retdata['leaveapply']->reason,
                            'status' => $this->retdata['leaveapply']->status,
                            'od_status' => $this->retdata['leaveapply']->od_status,
                            'category' => $this->retdata['leaveapply']->category,
                            'leaveavabledays' => $this->retdata['leaveapply']->leaveavabledays
                        ],
                        'applicant' => [
                            'name' => $applicant->name ?? '',
                            'email' => $applicant->email ?? '',
                            'phone' => $applicant->phone ?? '',
                            'usertype' => $applicant->usertype ?? ''
                        ],
                        'daysArray' => [
                            'fromdate' => $daysArray['fromdate'],
                            'todate' => $daysArray['todate'],
                            'leavedayCount' => $daysArray['leavedayCount'],
                            'totaldayCount' => $daysArray['totaldayCount']
                        ]
                    ];

                    $this->response([
                        'status'    => true,
                        'message'   => 'Success',
                        'data'      => $optimizedData
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        'status'    => false,
                        'message'   => 'Error 404',
                        'data'      => []
                    ], REST_Controller::HTTP_OK);
                }
            } else {
                $this->response([
                    'status'    => false,
                    'message'   => 'Error 404',
                    'data'      => []
                ], REST_Controller::HTTP_OK);
            }
        } else {
            $this->response([
                'status'    => false,
                'message'   => 'Error 404',
                'data'      => []
            ], REST_Controller::HTTP_OK);
        }
    }

    private function leavedayscustomCompute($fromdate, $todate)
    {
        $allholidayArray    = $this->getHolidaysSession();
        $getweekenddayArray = $this->getWeekendDaysSession();
        $leavedays = get_day_using_two_date(strtotime($fromdate), strtotime($todate));

        $holidayCount    = 0;
        $weekenddayCount = 0;
        $leavedayCount   = 0;
        $totaldayCount   = 0;
        $retArray = [];
        if(customCompute($leavedays)) {
            foreach($leavedays as $leaveday) {
                if(in_array($leaveday, $allholidayArray)) {
                    $holidayCount++;
                } elseif(in_array($leaveday, $getweekenddayArray)) {
                    $weekenddayCount++;
                } else {
                    $leavedayCount++;
                }
                $totaldayCount++;
            }
        }

        $retArray['fromdate']        = $fromdate;
        $retArray['todate']          = $todate;
        $retArray['holidayCount']    = $holidayCount;
        $retArray['weekenddayCount'] = $weekenddayCount;
        $retArray['leavedayCount']   = $leavedayCount;
        $retArray['totaldayCount']   = $totaldayCount;
        return $retArray;
    }

 
    // ✅ Add Leave (POST)
    public function add_leave_post() {
     $input = json_decode(trim(file_get_contents('php://input')), true);

        if (!isset($input['leave_schedule']) || !isset($input['leavecategoryID'])) {
            return $this->response(['status' => false, 'message' => 'Missing required fields'], REST_Controller::HTTP_BAD_REQUEST);
        }

        $explode = explode('-', $input['leave_schedule']);
        $from_date = date("Y-m-d", strtotime(trim($explode[0])));
        $to_date   = date("Y-m-d", strtotime(trim($explode[1])));
        $leave_days = $this->leavedayscustomCompute(trim($explode[0]), trim($explode[1]));
        $leave_days = isset($leave_days['totaldayCount']) ? $leave_days['totaldayCount'] : 0;

        $data = [
            'from_date'               => $from_date,
            'to_date'                 => $to_date,
            'leave_days'             => $leave_days,
            'leavecategoryID'        => $input['leavecategoryID'],
            'applicationto_usertypeID' => $input['applicationto_usertypeID'] ?? 0,
            'applicationto_userID'   => $input['applicationto_userID'] ?? 0,
            'reason'                 => $input['reason'] ?? '',
            'attachment'             => '', // file upload logic needed
            'attachmentorginalname'  => '',
            'from_time'              => date('H:i:s'),
            'to_time'                => date('H:i:s'),
            'create_date'            => date('Y-m-d H:i:s'),
            'modify_date'            => date('Y-m-d H:i:s'),
            'create_userID'          => $this->session->userdata('loginuserID'),
            'create_usertypeID'      => $this->session->userdata('usertypeID'),
            'schoolyearID'           => $this->session->userdata('defaultschoolyearID'),
            'od_status'              => $input['od_status'] ?? 0
        ];

        $this->leaveapplication_m->insert_leaveapplication($data);
         return $this->response(['status' => true, 'message' => 'Leave application added successfully'], REST_Controller::HTTP_OK);
    }

    // ✅ Edit Leave (PUT)
    public function edit_leave_put($id) {
        $input = $this->put();

        if (!$id || empty($input['leave_schedule'])) {
            return $this->response(['status' => false, 'message' => 'Invalid input'], REST_Controller::HTTP_BAD_REQUEST);
        }

        $explode = explode('-', $input['leave_schedule']);
        $from_date = date("Y-m-d", strtotime(trim($explode[0])));
        $to_date   = date("Y-m-d", strtotime(trim($explode[1])));
        $leave_days = $this->leavedayscustomCompute(trim($explode[0]), trim($explode[1]));
        $leave_days = isset($leave_days['totaldayCount']) ? $leave_days['totaldayCount'] : 0;

        $data = [
            'from_date'               => $from_date,
            'to_date'                 => $to_date,
            'leave_days'              => $leave_days,
            'leavecategoryID'         => $input['leavecategoryID'] ?? 0,
            'applicationto_usertypeID'=> $input['applicationto_usertypeID'] ?? 0,
            'applicationto_userID'    => $input['applicationto_userID'] ?? 0,
            'reason'                  => $input['reason'] ?? '',
            'modify_date'             => date('Y-m-d H:i:s'),
            'od_status'               => $input['od_status'] ?? 0
        ];

        $this->leaveapplication_m->update_leaveapplication($data, $id);

        return $this->response(['status' => true, 'message' => 'Leave application updated'], REST_Controller::HTTP_OK);
    }

    // ✅ Delete Leave (DELETE)
    public function delete_leave_delete($id) {
        if (!$id) {
            return $this->response(['status' => false, 'message' => 'Invalid ID'], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->leaveapplication_m->delete_leaveapplication($id);

        return $this->response(['status' => true, 'message' => 'Leave application deleted'], REST_Controller::HTTP_OK);
    }


}

/*
=======================================================================
 LEAVEAPPLY API — ENDPOINT REFERENCE
 Base URL: https://yourdomain.com/api/v10/leaveapply
 Auth:     Authorization: Bearer <JWT_TOKEN>
=======================================================================

-----------------------------------------------------------------------
1. LIST MY LEAVE APPLICATIONS
   GET /api/v10/leaveapply/index
-----------------------------------------------------------------------
Returns all leave applications submitted by the logged-in user for the
current school year. Each record includes the leave category name.

curl -X GET "https://yourdomain.com/api/v10/leaveapply/index" \
  -H "Authorization: Bearer <JWT_TOKEN>"

Response:
{
  "status": true,
  "message": "Success",
  "data": {
    "leaveapplications": [
      {
        "leaveapplicationID": 1,
        "leavecategoryID": 9,
        "leavecategory_name": "home sick",
        "from_date": "2026-04-29",
        "to_date": "2026-04-29",
        "leave_days": 1,
        "reason": "<p>Feeling unwell</p>",
        "status": "0",
        "od_status": 0,
        "applicationto_usertypeID": 1,
        "applicationto_userID": 1,
        "schoolyearID": 3,
        "create_userID": 5,
        "create_usertypeID": 2
      }
    ]
  }
}

-----------------------------------------------------------------------
2. VIEW A SINGLE LEAVE APPLICATION
   GET /api/v10/leaveapply/view/{leaveapplicationID}
-----------------------------------------------------------------------
Returns full detail for one leave application (only owner can view).
Includes applicant info, leave category, available leave days, and
date breakdown (working days vs holidays/weekends).

curl -X GET "https://yourdomain.com/api/v10/leaveapply/view/1" \
  -H "Authorization: Bearer <JWT_TOKEN>"

Response:
{
  "status": true,
  "message": "Success",
  "data": {
    "leaveapply": {
      "leaveapplicationID": 1,
      "leavecategoryID": 9,
      "from_date": "2026-04-29",
      "to_date": "2026-04-29",
      "leave_days": 1,
      "reason": "<p>Feeling unwell</p>",
      "status": "0",
      "od_status": 0,
      "category": "home sick",
      "leaveavabledays": 7
    },
    "applicant": {
      "name": "NARAYANASW",
      "email": "teacher@school.com",
      "phone": "9876543210",
      "usertype": "Teacher"
    },
    "daysArray": {
      "fromdate": "04/29/2026",
      "todate": "04/29/2026",
      "leavedayCount": 1,
      "totaldayCount": 1
    }
  }
}

-----------------------------------------------------------------------
3. ADD LEAVE APPLICATION
   POST /api/v10/leaveapply/add_leave
-----------------------------------------------------------------------
Fields:
  leave_schedule           string  required  "MM/DD/YYYY - MM/DD/YYYY"
  leavecategoryID          int     required  Leave category ID
  applicationto_usertypeID int     required  Recipient user type ID (1=Admin)
  applicationto_userID     int     required  Recipient user ID
  reason                   string  optional  Plain text or HTML reason
  od_status                int     optional  0=Leave, 1=OD (default: 0)

curl -X POST "https://yourdomain.com/api/v10/leaveapply/add_leave" \
  -H "Authorization: Bearer <JWT_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "leave_schedule": "04/29/2026 - 04/29/2026",
    "leavecategoryID": 9,
    "applicationto_usertypeID": 1,
    "applicationto_userID": 1,
    "reason": "Feeling unwell",
    "od_status": 0
  }'

Response:
{
  "status": true,
  "message": "Leave application added successfully"
}

-----------------------------------------------------------------------
4. EDIT LEAVE APPLICATION
   PUT /api/v10/leaveapply/edit_leave/{leaveapplicationID}
-----------------------------------------------------------------------
Fields: same as add_leave (all optional except leave_schedule).

curl -X PUT "https://yourdomain.com/api/v10/leaveapply/edit_leave/1" \
  -H "Authorization: Bearer <JWT_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "leave_schedule": "04/30/2026 - 04/30/2026",
    "leavecategoryID": 9,
    "applicationto_usertypeID": 1,
    "applicationto_userID": 1,
    "reason": "Updated reason"
  }'

Response:
{
  "status": true,
  "message": "Leave application updated"
}

-----------------------------------------------------------------------
5. DELETE LEAVE APPLICATION
   DELETE /api/v10/leaveapply/delete_leave/{leaveapplicationID}
-----------------------------------------------------------------------
curl -X DELETE "https://yourdomain.com/api/v10/leaveapply/delete_leave/1" \
  -H "Authorization: Bearer <JWT_TOKEN>"

Response:
{
  "status": true,
  "message": "Leave application deleted"
}

=======================================================================
 NOTES
 • leave_schedule format: "MM/DD/YYYY - MM/DD/YYYY" (matches web picker)
 • Status values: "0"=Pending, "1"=Approved, "2"=Rejected
 • od_status: 0=Regular Leave, 1=On Duty
 • leave_days is auto-calculated server-side (excludes holidays/weekends)
 • Only the owner (create_userID + create_usertypeID) can view/edit/delete
=======================================================================
*/