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
        $this->load->model('student_m');
        $this->load->model('teacher_m');
        $this->load->model('parents_m');
        $this->load->model('user_m');
        $this->load->model('systemadmin_m');
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
    $usertypeID = $this->session->userdata('usertypeID');
    $loginuserID = $this->session->userdata('loginuserID');

    // Build filter array based on user role
    $filterArray = [
        'leaveapplications.schoolyearID' => $schoolyearID
    ];

    // If not admin, filter by current user's applications
    if($usertypeID != 1) {
        $filterArray['leaveapplications.create_usertypeID'] = $usertypeID;
        $filterArray['leaveapplications.create_userID'] = $loginuserID;
    }

    $this->retdata['leaveapplications'] = $this->leaveapplication_m->get_order_by_leaveapply_with_user($filterArray);

    // If no applications found and user provided explicit filter, log for debugging
    if(empty($this->retdata['leaveapplications']) && $usertypeID != 1) {
        error_log("No leave applications found for userID: {$loginuserID}, usertypeID: {$usertypeID}, schoolyearID: {$schoolyearID}");
    }

    $leavecategories = pluck($this->leavecategory_m->get_leavecategory(), 'leavecategory', 'leavecategoryID');

    if(!empty($this->retdata['leaveapplications'])) {
        foreach ($this->retdata['leaveapplications'] as &$leave) {
            $leave->leavecategory_name = isset($leavecategories[$leave->leavecategoryID]) ? $leavecategories[$leave->leavecategoryID] : '';
        }
        unset($leave);
    }

    $this->response([
        'status'  => true,
        'message' => 'Success',
        'data'    => $this->retdata
    ], REST_Controller::HTTP_OK);
}

public function index_post()
{
    $schoolyearID = $this->session->userdata('defaultschoolyearID');
    $usertypeID = $this->session->userdata('usertypeID');
    $loginuserID = $this->session->userdata('loginuserID');

    // Build filter array
    $filterArray = [
        'leaveapplications.schoolyearID' => $schoolyearID
    ];

    // Allow filtering by specific user (admin only)
    $filterUserID = $this->post('create_userID');
    $filterUserTypeID = $this->post('create_usertypeID');
    
    if($filterUserID && $filterUserTypeID) {
        // Admin can filter by any user
        if($usertypeID == 1) {
            $filterArray['leaveapplications.create_userID'] = $filterUserID;
            $filterArray['leaveapplications.create_usertypeID'] = $filterUserTypeID;
        } else {
            // Non-admin can only filter for themselves
            $filterArray['leaveapplications.create_userID'] = $loginuserID;
            $filterArray['leaveapplications.create_usertypeID'] = $usertypeID;
        }
    } else if($usertypeID != 1) {
        // Default: non-admin users see their own applications
        $filterArray['leaveapplications.create_userID'] = $loginuserID;
        $filterArray['leaveapplications.create_usertypeID'] = $usertypeID;
    }
    
    // Optional status filter
    $status = $this->post('status');
    if($status !== false && $status !== null) {
        $filterArray['leaveapplications.status'] = $status;
    }

    $this->retdata['leaveapplications'] = $this->leaveapplication_m->get_order_by_leaveapply_with_user($filterArray);

    $leavecategories = pluck($this->leavecategory_m->get_leavecategory(), 'leavecategory', 'leavecategoryID');

    if(!empty($this->retdata['leaveapplications'])) {
        foreach ($this->retdata['leaveapplications'] as &$leave) {
            $leave->leavecategory_name = isset($leavecategories[$leave->leavecategoryID]) ? $leavecategories[$leave->leavecategoryID] : '';
        }
        unset($leave);
    }

    $this->response([
        'status'  => true,
        'message' => 'Success',
        'data'    => $this->retdata
    ], REST_Controller::HTTP_OK);
}

/**
 * API 1: GET ROLES DROPDOWN
 * Returns only Admin and Teacher roles
 * Endpoint: GET /api/v10/leaveapply/get_roles
 */
public function get_roles_get()
{
    // Get all usertypes and filter only Admin (1) and Teacher (2)
    $allUsertypes = pluck(
        $this->usertype_m->get_usertype(),
        'usertype',
        'usertypeID'
    );
    
    $this->retdata['roles'] = [];
    
    // Add only Admin and Teacher
    if(isset($allUsertypes[1])) {
        $this->retdata['roles'][] = ['id' => 1, 'name' => $allUsertypes[1]];
    }
    if(isset($allUsertypes[2])) {
        $this->retdata['roles'][] = ['id' => 2, 'name' => $allUsertypes[2]];
    }

    $this->response([
        'status'  => true,
        'message' => 'Success',
        'data'    => $this->retdata
    ], REST_Controller::HTTP_OK);
}

/**
 * API 2: GET CATEGORIES DROPDOWN
 * Returns all leave categories
 * Endpoint: GET /api/v10/leaveapply/get_categories
 */
public function get_categories_get()
{
    // Get all leave categories
    $categories = pluck(
        $this->leavecategory_m->get_leavecategory(),
        'leavecategory',
        'leavecategoryID'
    );
    
    $this->retdata['categories'] = [];
    foreach($categories as $id => $name) {
        $this->retdata['categories'][] = ['id' => $id, 'name' => $name];
    }

    $this->response([
        'status'  => true,
        'message' => 'Success',
        'data'    => $this->retdata
    ], REST_Controller::HTTP_OK);
}

/**
 * API 3: GET USERS BY ROLE
 * Returns users (Application To) based on selected role
 * Endpoint: POST /api/v10/leaveapply/get_users_by_role
 * Payload: {"role_id": 1} or {"role_id": 2}
 */
public function get_users_by_role_post()
{
    $roleId = $this->post('role_id');
    
    if(!$roleId) {
        return $this->response([
            'status'  => false,
            'message' => 'Role ID is required'
        ], REST_Controller::HTTP_BAD_REQUEST);
    }
    
    $this->retdata['users'] = [];
    
    // Map roleId to table and id field
    $roleMapping = [
        1 => ['table' => 'systemadmin', 'id_field' => 'systemadminID', 'role_name' => 'Admin'],
        2 => ['table' => 'teacher', 'id_field' => 'teacherID', 'role_name' => 'Teacher']
    ];
    
    if(!isset($roleMapping[$roleId])) {
        return $this->response([
            'status'  => false,
            'message' => 'Invalid role ID'
        ], REST_Controller::HTTP_BAD_REQUEST);
    }
    
    $config = $roleMapping[$roleId];
    $users = $this->db->get_where($config['table'], ['active' => 1])->result();
    
    foreach($users as $user) {
        $this->retdata['users'][] = [
            'id' => $user->{$config['id_field']},
            'name' => $user->name
        ];
    }

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

    /**
     * APPROVE LEAVE APPLICATION
     * Endpoint: POST /api/v10/leaveapply/approve
     * Payload: {"leaveapplicationID": 1}
     */
    public function approve_post()
    {
        $leaveapplicationID = $this->post('leaveapplicationID');
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $usertypeID = $this->session->userdata('usertypeID');
        $loginuserID = $this->session->userdata('loginuserID');

        if(!$leaveapplicationID) {
            return $this->response([
                'status'  => false,
                'message' => 'Leave application ID is required'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // Check if leave application exists
        $leaveapp = $this->leaveapplication_m->get_single_leaveapplication([
            'leaveapplicationID' => $leaveapplicationID,
            'schoolyearID' => $schoolyearID
        ]);

        if(!$leaveapp) {
            return $this->response([
                'status'  => false,
                'message' => 'Leave application not found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // Check authorization: Only admin or the recipient can approve
        if($usertypeID != 1 && !($leaveapp->applicationto_usertypeID == $usertypeID && $leaveapp->applicationto_userID == $loginuserID)) {
            return $this->response([
                'status'  => false,
                'message' => 'You are not authorized to approve this application'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        // Update status to approved (1)
        $this->leaveapplication_m->update_leaveapplication([
            'status' => 1,
            'modify_date' => date('Y-m-d H:i:s')
        ], $leaveapplicationID);

        return $this->response([
            'status'  => true,
            'message' => 'Leave application approved successfully'
        ], REST_Controller::HTTP_OK);
    }

    /**
     * DECLINE/REJECT LEAVE APPLICATION
     * Endpoint: POST /api/v10/leaveapply/decline
     * Payload: {"leaveapplicationID": 1, "reason": "optional decline reason"}
     */
    public function decline_post()
    {
        $leaveapplicationID = $this->post('leaveapplicationID');
        $declineReason = $this->post('reason') ?? '';
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $usertypeID = $this->session->userdata('usertypeID');
        $loginuserID = $this->session->userdata('loginuserID');

        if(!$leaveapplicationID) {
            return $this->response([
                'status'  => false,
                'message' => 'Leave application ID is required'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // Check if leave application exists
        $leaveapp = $this->leaveapplication_m->get_single_leaveapplication([
            'leaveapplicationID' => $leaveapplicationID,
            'schoolyearID' => $schoolyearID
        ]);

        if(!$leaveapp) {
            return $this->response([
                'status'  => false,
                'message' => 'Leave application not found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // Check authorization: Only admin or the recipient can decline
        if($usertypeID != 1 && !($leaveapp->applicationto_usertypeID == $usertypeID && $leaveapp->applicationto_userID == $loginuserID)) {
            return $this->response([
                'status'  => false,
                'message' => 'You are not authorized to decline this application'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        // Update status to rejected/declined (2)
        // Note: Store decline reason in the reason field or add a new field if available
        $updateData = [
            'status' => 2,
            'modify_date' => date('Y-m-d H:i:s')
        ];

        // If decline reason provided, append to existing reason
        if($declineReason) {
            $updateData['reason'] = 'DECLINED: ' . $declineReason;
        }

        $this->leaveapplication_m->update_leaveapplication($updateData, $leaveapplicationID);

        return $this->response([
            'status'  => true,
            'message' => 'Leave application declined successfully'
        ], REST_Controller::HTTP_OK);
    }


}

/*
=======================================================================
 LEAVEAPPLY API — ENDPOINT REFERENCE
 Base URL: https://yourdomain.com/api/v10/leaveapply
 Auth:     Authorization: Bearer <JWT_TOKEN>
=======================================================================

-----------------------------------------------------------------------
0a. GET ROLES DROPDOWN
    GET /api/v10/leaveapply/get_roles
-----------------------------------------------------------------------
Returns list of roles (Admin and Teacher only).

curl -X GET "https://yourdomain.com/api/v10/leaveapply/get_roles" \
  -H "Authorization: Bearer <JWT_TOKEN>"

Response:
{
  "status": true,
  "message": "Success",
  "data": {
    "roles": [
      {"id": 1, "name": "Admin"},
      {"id": 2, "name": "Teacher"}
    ]
  }
}

-----------------------------------------------------------------------
0b. GET CATEGORIES DROPDOWN
    GET /api/v10/leaveapply/get_categories
-----------------------------------------------------------------------
Returns list of all available leave categories.

curl -X GET "https://yourdomain.com/api/v10/leaveapply/get_categories" \
  -H "Authorization: Bearer <JWT_TOKEN>"

Response:
{
  "status": true,
  "message": "Success",
  "data": {
    "categories": [
      {"id": 1, "name": "Casual Leave"},
      {"id": 9, "name": "Home Sick"},
      {"id": 2, "name": "Earned Leave"}
    ]
  }
}

-----------------------------------------------------------------------
0c. GET USERS BY ROLE
    POST /api/v10/leaveapply/get_users_by_role
-----------------------------------------------------------------------
Returns list of users (Application To) based on selected role.
Currently supports role_id: 1 (Admin) and 2 (Teacher).

curl -X POST "https://yourdomain.com/api/v10/leaveapply/get_users_by_role" \
  -H "Authorization: Bearer <JWT_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"role_id": 1}'

Response:
{
  "status": true,
  "message": "Success",
  "data": {
    "users": [
      {"id": 1, "name": "Admin User"},
      {"id": 2, "name": "Another Admin"}
    ]
  }
}

Supported role_id values:
  1 = Admin (fetches from systemadmin table)
  2 = Teacher (fetches from teacher table)

-----------------------------------------------------------------------
1. LIST LEAVE APPLICATIONS
   GET /api/v10/leaveapply/index
-----------------------------------------------------------------------
Returns leave applications:
- For ADMIN users (usertypeID=1): All applications in the school year
- For other users: Only their own applications submitted in the school year

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
1b. FILTER LEAVE APPLICATIONS (POST)
   POST /api/v10/leaveapply/index
-----------------------------------------------------------------------
Filter applications with optional parameters. Admin users can filter
by specific user, others can only filter their own records.

Payload (all optional):
{
  "create_userID": 5,           // User ID (admin only for others)
  "create_usertypeID": 2,       // User type ID (admin only for others)
  "status": 1                   // 0=Pending, 1=Approved, 2=Rejected
}

curl -X POST "https://yourdomain.com/api/v10/leaveapply/index" \
  -H "Authorization: Bearer <JWT_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"status": 1}'

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

-----------------------------------------------------------------------
6. APPROVE LEAVE APPLICATION
   POST /api/v10/leaveapply/approve
-----------------------------------------------------------------------
Approves a pending leave application. Only the recipient (admin/person
application was sent to) or an admin user can approve.

Payload:
{
  "leaveapplicationID": 1
}

curl -X POST "https://yourdomain.com/api/v10/leaveapply/approve" \
  -H "Authorization: Bearer <JWT_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"leaveapplicationID": 1}'

Response:
{
  "status": true,
  "message": "Leave application approved successfully"
}

Error Responses:
{
  "status": false,
  "message": "Leave application not found"
}

{
  "status": false,
  "message": "You are not authorized to approve this application"
}

Status Values Updated:
  0 = Pending
  1 = Approved ✓
  2 = Rejected

-----------------------------------------------------------------------
7. DECLINE/REJECT LEAVE APPLICATION
   POST /api/v10/leaveapply/decline
-----------------------------------------------------------------------
Declines/rejects a pending leave application. Only the recipient or
an admin user can decline. Optional reason can be provided.

Payload:
{
  "leaveapplicationID": 1,
  "reason": "optional decline reason"
}

curl -X POST "https://yourdomain.com/api/v10/leaveapply/decline" \
  -H "Authorization: Bearer <JWT_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"leaveapplicationID": 1, "reason": "Not approved at this time"}'

Response:
{
  "status": true,
  "message": "Leave application declined successfully"
}

Error Responses:
{
  "status": false,
  "message": "Leave application not found"
}

{
  "status": false,
  "message": "You are not authorized to decline this application"
}

Status Values Updated:
  0 = Pending
  1 = Approved
  2 = Rejected/Declined ✓

=======================================================================
 MOBILE APP WORKFLOW
=======================================================================
1. Load form: GET /get_roles → GET /get_categories
2. On role select: POST /get_users_by_role {"role_id": selected_id}
3. Submit form: POST /add_leave with all required fields
4. View list: GET /index
5. View detail: GET /view/{id}
6. Edit: PUT /edit_leave/{id}
7. APPROVE: POST /approve {"leaveapplicationID": id}
8. DECLINE: POST /decline {"leaveapplicationID": id, "reason": "optional"}
9. Delete: DELETE /delete_leave/{id}

=======================================================================
 NOTES
=======================================================================
• leave_schedule format: "MM/DD/YYYY - MM/DD/YYYY" (matches web picker)
• Status values: "0"=Pending, "1"=Approved, "2"=Rejected
• od_status: 0=Regular Leave, 1=On Duty
• leave_days is auto-calculated server-side (excludes holidays/weekends)
• Only the owner (create_userID + create_usertypeID) can view/edit/delete
• get_users_by_role currently supports only Admin (1) and Teacher (2)
• Approve/Decline: Only recipient or admin can approve/decline applications
=======================================================================
*/