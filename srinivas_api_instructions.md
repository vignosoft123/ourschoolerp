# API Instructions: OurSchoolERP

> [!IMPORTANT]
> **AI Assistant:** Read this before any API work. Follow all patterns exactly.

---

## 1. API Architecture Overview

- **Base Class**: `Api_Controller` (located in `mvc/libraries/Api_Controller.php`).
- **Framework**: CodeIgniter with `codeigniter-restserver` library.
- **Location**: All REST APIs are in `mvc/controllers/api/v10/`.
- **Authentication**: JWT (JSON Web Token) based.
- **Permissions**: URI-based permission checking against the session `master_permission_set`.

---

## 2. API Controller Pattern

Every API controller follows this standard structure:

```php
<?php
use Restserver\Libraries\REST_Controller;

class Xyz extends Api_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('xyz_m');
    }

    // GET request (e.g., api/v10/xyz/index or api/v10/xyz/1)
    public function index_get($id = null) {
        // Use $this->retdata to collect response data
        $this->retdata['xyz'] = $this->xyz_m->get_xyz($id);
        
        $this->response([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $this->retdata
        ], REST_Controller::HTTP_OK);
    }

    // POST request (e.g., api/v10/xyz/add)
    public function add_post() {
        $data = [
            'name' => $this->post('name'),
            'status' => $this->post('status')
        ];
        
        if($this->xyz_m->insert_xyz($data)) {
            $this->response(['status' => true, 'message' => 'Created'], REST_Controller::HTTP_OK);
        }
    }
}
```

### HTTP Method Suffixes
- `_get`: For fetching data.
- `_post`: For creating or complex filtering.
- `_put`: For updates (though `edit_post` is often used).
- `_delete`: For removals (though `delete_post` is often used).

---

## 3. Core Flow & Authentication

1. **Constructor Flow**:
   - Sets CORS headers (Origin: *).
   - Calls `tokeChecking()`:
     - Extracts JWT from `Authorization: Bearer <token>` or `jwt` param.
     - Decodes JWT. If valid, calls `userInfo()` to mock a session from database labels.
   - Calls `permissionControl()`:
     - Maps URI segments (e.g., `api/v10/subject/index`) to permission keys (e.g., `subject`).
     - Blocks request with `401 Unauthorized` if permission is missing.

2. **Session Persistence**: 
   Even though it's an API, the system populates `$this->session` with user data (usertype, loginID, etc.) to reuse existing Model logic.

---

## 4. Response Standard

All responses **MUST** return a JSON object with this structure:

| Key | Type | Description |
|---|---|---|
| `status` | Boolean | `true` for success, `false` for error. |
| `message` | String | Human-readable result or error message. |
| `data` | Mixed | The payload (usually an array or object). |

**Common Status Codes:**
- `REST_Controller::HTTP_OK` (200)
- `REST_Controller::HTTP_BAD_REQUEST` (400)
- `REST_Controller::HTTP_UNAUTHORIZED` (401)
- `REST_Controller::HTTP_NOT_FOUND` (404)

---

## 5. Global Utilities in `Api_Controller`

These methods are available in any API controller:

- `$this->getHolidaysSession()`: Returns an array of holiday dates for the current year.
- `$this->getWeekendDaysSession()`: Returns an array of weekend dates based on site settings.
- `$this->data['siteinfos']`: Global site configuration details.
- `$this->data['myclass']`: Automatically set for student users (usertype 3).

---

## 6. Key Models for API

- `studentrelation_m`: Fetching student class/section context.
- `site_m`: General school settings.
- `schoolyear_m`: Academic year context.
- `permission_m`: Internal permission lookups.

---

## 7. Role-Based Restrictions & Permissions

Control over who can **Add, Update, or Delete** is managed at two levels:

### 1. Global Permission Level (Api_Controller)
The `permissionControl()` method in the base class is the primary guard. It checks the URL segments against the `master_permission_set` in the session.
- **URL**: `api/v10/subject/add` → **Key**: `subject_add`.
- If the session key is `'no'`, the API returns `401 Unauthorized`.

### 2. Data/Role Level (Method Context)
Individual methods often have hardcoded checks for `usertypeID`. Common roles:
- **Admin (`usertypeID = 1`)**: Usually has full access to all records across the system.
- **Teacher (`usertypeID = 2`)**: Access is often limited to subjects/classes they are assigned to.
- **Student (`usertypeID = 3`)**: Usually restricted to their own class (`$this->data['myclass']`) and their own records (marks, attendance).
- **Parent (`usertypeID = 4`)**: Restricted to data related to their children.

### 3. Ownership Checks
For features like **Complaints** or **Assignments**, non-admin users are restricted to records they created:
```php
// Example pattern for non-admins
if($this->session->userdata('usertypeID') != 1) {
    $queryArray['create_userID'] = $this->session->userdata('loginuserID');
    $queryArray['create_usertypeID'] = $this->session->userdata('usertypeID');
}
```

---

---

## 9. API Documentation Standard

To keep the system easy to maintain, follow these rules for each API file:

1. **Self-Documenting Ends**: Always add a list of available endpoints and sample JSON payloads at the **END of the respective API controller file** (e.g., at the bottom of `Subject.php`). Include the full **cURL** command for each endpoint so mobile developers can test them immediately.
2. **Standard Suffixes**: Clearly label which methods map to which HTTP verbs (`_get` vs. `_post`).
3. **Permission Handling**: **DO NOT** manually call `$this->permissionControl()` inside the API methods. The `Api_Controller` base class handles this automatically via the URI segments. Manually calling the private parent method will cause a **500 Internal Server Error**.
4. **Shared Model Safety**: **NEVER** modify shared models in `mvc/models/` for API-specific needs. These models are used by the web application. If you need special filtering or fewer columns, handle that transformation within the API controller instead.
5. **Data Minimization (Mapping)**: Always map/filter database results in the API controller before sending the response. Remove internal fields (like `create_date`, `modify_date`, `password`, etc.) to keep the mobile app payload small and secure.
6. **Dropdown Optimization**: For fetching lists for dropdowns (Classes, Teachers, etc.), use the `pluck()` helper to return only `[ID => Name]` to reduce payload size.
7. **Optional Files**: Unless explicitly requested, file uploads should be **optional**. Ensure the `fileupload` callback returns `TRUE` even if no file is provided, and set default empty strings in the database array to avoid "NOT NULL" errors.
8. **Property Declaration**: Always declare properties like `public $upload_data = array();` at the top of the class to prevent "dynamic property" errors in newer PHP versions.

---

## 10. Maintenance Log

- **2026-03-22**: Created `srinivas_api_instructions.md`. Documented JWT flow, controller naming conventions, and `Api_Controller` core logic.
- **2026-03-22**: Added Role-Based Restrictions & Permissions section (Admin vs. others, ownership checks).
- **2026-03-22**: Added API Documentation Standard (Self-documenting files, Shared model safety, and Data minimization).
- **2026-03-22**: Implemented Subject CRUD APIs and optimized them for mobile app performance (reduced payload).
