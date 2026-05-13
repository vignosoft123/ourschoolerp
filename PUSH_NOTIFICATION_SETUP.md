# Firebase Push Notification — Setup & Reference

**Last Updated:** 2026-05-14  
**Firebase Project:** `our-school-erp-cbf37`  
**Mobile App Package:** `io.ionic.ourschoolerp`

---

## Current Status

| Item | Value |
|---|---|
| Firebase Project ID | `our-school-erp-cbf37` |
| Service Account File | `mvc/third_party/firebase-service-account.json` |
| FCM SDK | Kreait Firebase PHP `^5.26` (via Composer) |
| Helper Function | `send_fcm_push_bulk()` in `mvc/helpers/fcm_helper.php` |
| Device Token Column | `student.device_token` (VARCHAR 255) |
| Platform Column | `student.platform` |

---

## Quick Setup (Do This First)

1. Go to **[Firebase Console → Service Accounts](https://console.firebase.google.com/project/our-school-erp-cbf37/settings/serviceaccounts/adminsdk)**
   - Make sure you're on project **`our-school-erp-cbf37`**
2. Click **"Generate new private key"** → download the JSON file
3. Open the downloaded JSON in a text editor → Select All → Copy
4. Go to **`/Push_notification/setup`** in the ERP
5. Paste into the textarea → click **"Update Service Account"**
6. Click **"Verify After Update"** — all 5 checks must go green
7. Test: go to **Send Notification**, pick a class, send a test message

---

## Admin Pages

| URL | Purpose |
|---|---|
| `/Push_notification` | Compose & send notifications |
| `/Push_notification/history` | View notification log (last 100) |
| `/Push_notification/setup` | Upload service account + status |
| `/Push_notification/verify` | Run 5-step verification |

---

## Sending Notifications — Recipient Options

| Option | Description |
|---|---|
| **All Students** | Sends to every student with a device token |
| **By Class** | Filters to one class |
| **By Class & Section** | Filters to one class + section |
| **Specific Students** | Multi-select individual students by name; filter by class to narrow list |

---

## Architecture

### Key Files

| File | Role |
|---|---|
| `mvc/controllers/Push_notification.php` | Admin controller (compose, send, history, setup, verify) |
| `mvc/models/Push_notification_m.php` | Model: `get_students_with_tokens()`, `log_notification()`, `get_history()`, `get_students_for_select()` |
| `mvc/helpers/fcm_helper.php` | `send_fcm_push_bulk()` — sends via Kreait SDK individually per token |
| `mvc/views/push_notification/index.php` | Compose & send view |
| `mvc/views/push_notification/history.php` | Notification log view |
| `mvc/views/push_notification/setup.php` | Service account management view |
| `mvc/controllers/api/v10/Token.php` | Mobile API endpoint to save device tokens |
| `mvc/third_party/firebase-service-account.json` | Firebase service account credentials (NOT in version control) |

### Database

| Table / Column | Purpose |
|---|---|
| `student.device_token` | FCM token saved by mobile app on login |
| `student.platform` | `android` / `ios` |
| `push_notification_log` | Full history of sent notifications |

**`push_notification_log` columns:**
`id`, `title`, `message`, `notification_type`, `recipient_type` (all/class/section), `classesID`, `sectionID`, `class_name`, `section_name`, `total_recipients`, `success_count`, `failure_count`, `sent_by_userID`, `sent_by_name`, `sent_at`

### Mobile App → Server Token Flow

```
Mobile App (login)
    ↓ POST /api/v10/token/store_token
    { studentID, device_token, platform }
    ↓
Token.php::store_token_post()
    ↓
student_m->update_student({ device_token, platform }, studentID)
    ↓
student.device_token column updated
```

### Send Notification Flow

```
Admin → Compose page
    ↓ Select recipients + type title/message
    ↓ AJAX POST /Push_notification/send
    ↓
Controller: get tokens via push_notification_m->get_students_with_tokens()
    ↓
fcm_helper: send_fcm_push_bulk(tokens, title, message, data)
    → Loops each token: CloudMessage::withTarget('token', $token) → $messaging->send()
    ↓
Log result to push_notification_log
    ↓
Return JSON { status, successCount, failureCount, totalRecipients }
```

---

## Important Technical Notes

### Google Removed the /batch Endpoint (Fixed 2026-05-14)
Kreait Firebase PHP v5.x used `sendMulticast()` which sent to Google's `/batch` endpoint.
Google **removed** this endpoint — it returns 404.
**Fix applied:** `fcm_helper.php` now loops each token and calls `$messaging->send()` individually.
This works correctly and is reliable.

### Service Account Must Match google-services.json
- `google-services.json` project: `our-school-erp-cbf37`
- Service account must also be for project: `our-school-erp-cbf37`
- The Setup page (`/Push_notification/setup`) checks this automatically
- If project IDs don't match → notifications fail silently or with auth errors

### Token Validity
- FCM tokens are typically 140–200 characters
- Tokens expire when the user reinstalls the app or clears app data
- A failed send does NOT automatically remove the old token from the database

---

## Troubleshooting

| Error | Cause | Fix |
|---|---|---|
| `Messaging error: ...404.../batch...` | Google removed the `/batch` batch endpoint | Fixed: helper now uses individual sends |
| `project_id mismatch` | Service account is for wrong Firebase project | Re-download from `our-school-erp-cbf37` project |
| `Service account file not found` | File missing at `mvc/third_party/` | Upload via `/Push_notification/setup` |
| `No students with app installed` | No device tokens in DB for selected group | Students need to log into the mobile app |
| Push sent but not received | Token expired / app reinstalled | User must re-login to the app to refresh token |
| `Invalid JSON` | Incomplete paste of service account | Re-copy full file content, including first `{` and last `}` |

---

## Permissions & Menu

- **Permission name:** `push_notification`
- **Menu:** Administrator → Push Notification (fa-bell icon, priority 200)
- **Menu language key:** `$lang['menu_push_notification']` in `topbar_menu_lang.php`
- Migration entries in `mvc/migrations/schema_updates.json` — run migration after deployment

---

## Mobile App Requirements

1. `google-services.json` in the app must use project `our-school-erp-cbf37`
2. App must call `POST /api/v10/token/store_token` on every login to refresh the token
3. FCM SDK must be initialised in the app
4. App must have notification permissions granted by the user
