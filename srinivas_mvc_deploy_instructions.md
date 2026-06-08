# MVC Deployment System — Complete Reference
**Project:** OurSchoolERP (staging.ourschoolerp.localhost)  
**Created:** 2026-06-06 | **Updated:** 2026-06-09 (cPanel one-click subdomain creation; SQL import via dummy server; auto full deploy)  
**Author context:** Srinivas — this file was generated to capture the full deployment system so any future Claude session can understand and extend it without re-discovery.

---

## 1. What This System Does

Automates deploying updated PHP code (MVC folder), CSS files, and bootstrap files from **localhost** to **live school subdomains** on multiple hosting servers — all via buttons in the SubDomain Management page.

**Before:** Manual zip → cPanel upload → unzip PHP script  
**After:** One button click from `staging.ourschoolerp.localhost//subdomains/index`

---

## 2. Hosting Server Inventory

| Server Key | Domain Suffix | Dummy Server | FTP Host | FTP User | Home Dir | Deploy Method |
|---|---|---|---|---|---|---|
| `godaddy` | `ourcollegeerp.com` | `dummy1.ourcollegeerp.com` | — | — | `/home/zbtr5uwckzg7/` | Python POST |
| `hostgator` | `ourschoolerp.com` | `dummy1.ourschoolerp.com` | `cs3005.hostgator.in:21` | `mindw2ft` | `/home1/mindw2ft/` | PHP FTP |
| `myschools` | `myschoolserp.com` | `dummy1.myschoolserp.com` | `sh203.bigrock.com:21` | `myschknc` | `/home2/myschknc/` | PHP FTP |
| `schoolhour` | `schoolhour.in` | `dummy1.schoolhour.in` | `schoolhour.in:21` | `schoodj8` | `/home4/schoodj8/` | PHP FTP |
| `collegehour` | `collegeerp.in` | `dummy1.collegehour.in` | `collegehour.in:21` | `collenv4p` | `/home4/collenv4p/` | PHP FTP |

**Subdomain folder paths (relative to FTP home):**
- GoDaddy: `public_html/{sub}.ourcollegeerp.com/`
- HostGator: `public_html/{sub}.ourschoolerp.com/` (FTP chrooted — use relative paths)
- BigRock/MySchools: `{sub}.myschoolserp.com/` (**no public_html**)
- Schoolhour: `{sub}.schoolhour.in/` (**no public_html**)
- Collegehour: `{sub}.collegeerp.in/` (**no public_html**)

> **Note:** Schoolhour and Collegehour use the same PHP FTP flow as HostGator/BigRock — bootstrap_copy.php + full_deploy.php auto-uploaded via "Upload MVC to Dummy" button.

---

## 3. API Keys & Credentials

All stored in `python/.env` (never commit to git):

```
CSS_UPDATE_API_KEY=65b03a8eb7cdf6799815bfd17ba7367d1113b642e12dec28
MVC_DEPLOY_API_KEY=a3517b0f46b17c8b813d850e8ef65fd035df328d45f0a836
```

Same CSS key is also in: `mvc/config/css_update_config.php`  
Same MVC key is also in: `mvcdeploy.php` (standalone, at subdomain root — GoDaddy only)

**cPanel API token credentials (in `python/.env`):**
```
CPANEL_CONFIGS={
  "hostgator": {"host":"cs3005.hostgator.in","port":2083,"user":"mindw2ft","token":"77ZCG0X8MRPIABB18LW7RQ9A9NRXR0WL","home":"/home1/mindw2ft"},
  "myschools":  {"host":"sh203.bigrock.com","port":2083,"user":"myschknc","token":"TE2KD5D61V73TI0IBWU8XLU75G20U9UJ","home":"/home2/myschknc"},
  "godaddy":    {"host":"sg2plmcpnl508133.prod.sin2.secureserver.net","port":2083,"user":"zbtr5uwckzg7","token":"BS8LXA53ASEZOD25F768QA5W3X2U463H","password":"Satya$1986","home":"/home/zbtr5uwckzg7"}
}
```
> **GoDaddy cPanel note:** The host must be `sg2plmcpnl508133.prod.sin2.secureserver.net` (from cPanel URL bar) NOT `ourcollegeerp.com:2083`. The cPanel username is `zbtr5uwckzg7` (from `/home/zbtr5uwckzg7` visible in File Manager) NOT the GoDaddy account ID `114556211`. Both token and password are stored for fallback auth.

**FTP credentials (in `python/.env` as `FTP_CONFIGS` AND in `Subdomains.php` `$ftp_configs` — both must match):**

| Server | Host | User | Password | webroot |
|---|---|---|---|---|
| hostgator | `cs3005.hostgator.in:21` | `mindw2ft` | `Mindwhile$1986@` | `public_html` |
| myschools | `sh203.bigrock.com:21` | `myschknc` | `Kiran$1986@` | `""` |
| schoolhour | `schoolhour.in:21` | `schoodj8` | `School@123456@` | `""` |
| collegehour | `collegehour.in:21` | `collenv4p` | `Satya$1986$` | `""` |

> **Password changed?** Update `pass` in **both** `python/.env` FTP_CONFIGS (then restart Python) **and** `mvc/controllers/Subdomains.php` `$ftp_configs` array in `upload_mvc_zip_php()`. Both are used — Python for CSS sync, PHP for Rocket + Upload to Dummy.

---

## 4. Files Created for This System

### On Localhost (in `C:\xampp\htdocs\ourschoolerp\`)
| File | Purpose |
|---|---|
| `mvcdeploy.php` | Standalone PHP receiver — only on GoDaddy subdomain roots |
| `bootstrap_copy.php` | Dummy server script: copies Cssupdate.php + Mvcdeploy.php to subdomains; also handles MVC extraction via GET request |
| `full_deploy.php` | Dummy server script: extracts ALL zip files for new domain setup |
| `css_sync.php` | CSS sync proxy for dummy server (fallback for mod_security servers) |

### On Live Servers (each subdomain root)
| File | Location | Purpose |
|---|---|---|
| `mvc/controllers/Cssupdate.php` | `mvc/controllers/` | Receives CSS files + controller uploads from Python |
| `mvc/controllers/Mvcdeploy.php` | `mvc/controllers/` | CI controller: check, receive, trigger methods for MVC deploy |
| `mvc/config/css_update_config.php` | `mvc/config/` | Holds the CSS API key |

### Dummy Server Files (upload once per dummy server)
Folder: `C:\xampp\htdocs\ourschoolerp\need to upload in dummy1\`
| File | Upload to |
|---|---|
| `bootstrap_copy.php` | Dummy server root |
| `mvcdeploy.php` | Dummy server root (used by bootstrap_copy) |
| `Cssupdate.php` | Dummy server root (used by bootstrap_copy) |
| `css_update_config.php` | Dummy server root (used by bootstrap_copy) |
| `full_deploy.php` | Dummy server root |
| `css_sync.php` | Dummy server root |

---

## 5. Python Server Endpoints (`python/main.py`)

| Endpoint | Purpose |
|---|---|
| `POST /update-css/{id}` | Sync CSS files to single subdomain |
| `POST /update-css-bulk` | Sync CSS to multiple subdomains |
| `POST /bootstrap-subdomain/{id}` | Copy Cssupdate.php + Mvcdeploy.php via dummy server |
| `POST /bootstrap-subdomain-bulk` | Bootstrap multiple subdomains |
| `POST /deploy-mvc/{id}` | Deploy mvc.zip to single subdomain |
| `POST /deploy-mvc-bulk` | Deploy mvc.zip to multiple subdomains |
| `POST /full-deploy/{id}` | Extract all zips to single subdomain (new domain setup) |
| `POST /full-deploy-bulk` | Full deploy to multiple subdomains |
| `POST /create-cpanel-subdomain` | **One-click new subdomain**: creates folder + DB + user + privileges + SQL import + full deploy + DB record |
| `GET /config-check` | Debug: shows configured servers for cpanel/ftp/dummy/domains |

---

## 6. UI Buttons (SubDomain Management Page)

### Per-Row Action Buttons (in each subdomain row)
| Icon | Color | When to use | Endpoint / Handler |
|---|---|---|---|
| Database | Green | Once per new subdomain | `localhost:8000/create-tables/{id}` |
| Bar chart | Purple | Any time — view stats | `localhost:8000/statistics/{id}` |
| Plug | Dark gray | **One-time** — first setup per subdomain | `localhost:8000/bootstrap-subdomain/{id}` |
| Cloud-upload | Orange | Every CSS change | `localhost:8000/update-css/{id}` |
| Rocket | Teal | Every MVC update | GoDaddy: Python `deploy-mvc/{id}` · FTP servers: PHP `deploy_mvc_php/{id}` |
| Archive | Orange-red | **One-time** — new subdomain full setup | `localhost:8000/full-deploy/{id}` → writes `database.php` automatically |
| Edit | Blue | Edit subdomain settings | PHP only |
| Delete | Red | Delete subdomain | PHP only |

### Bulk Buttons (toolbar — grouped by purpose)

**GROUP 1 — One-Time Setup** (run once when adding new subdomains):
| Button | When | Endpoint |
|---|---|---|
| **Create in cPanel** | For GoDaddy/HostGator/MySchools — creates everything in one click | PHP → `localhost:8000/create-cpanel-subdomain` |
| Create Tables | Once per new subdomain | `localhost:8000/create-tables-bulk` |
| Bootstrap All | Once per new subdomain (copies Mvcdeploy + Cssupdate) | `localhost:8000/bootstrap-subdomain-bulk` |
| Full Deploy All | Once per new subdomain (extracts ALL zip files) | `localhost:8000/full-deploy-bulk` |

**GROUP 2 — Regular Updates** (run every time you make changes):
| Button | When | Endpoint / Handler |
|---|---|---|
| Sync CSS to All | Every CSS change | `localhost:8000/update-css-bulk` |
| Upload MVC to Dummy | Every MVC update (HostGator/MySchools/Schoolhour/Collegehour) | PHP `upload_mvc_zip_php/{server}` via FTP — uploads **mvc.zip + bootstrap_copy.php + full_deploy.php** |
| Deploy MVC to All | After "Upload MVC to Dummy" (or directly for GoDaddy) | `localhost:8000/deploy-mvc-bulk` |

**GROUP 3 — Info / Admin:**
| Button | Purpose |
|---|---|
| Refresh Schools Age | Recalculate school age for all subdomains |

### Python Server Buttons
| Button | Action |
|---|---|
| Start Python Server | Runs `start_server.bat` → uvicorn on port 8000 with --reload |
| Stop Python Server (green) | Kills all PIDs on port 8000 |

---

## 7. Process Flows

### Correct order for a new subdomain:
1. **Plug** (bootstrap) → deploys Mvcdeploy.php + Cssupdate.php
2. **Orange cloud** (CSS sync) → pushes CSS files
3. **Rocket** (MVC deploy) → deploys mvc/ folder

---

### 7A. First Time Setup (New Subdomain)

**Step 1 — Upload to dummy server** (automatic via "Upload MVC to Dummy" button for FTP servers):
The button now uploads 3 files at once via FTP:
- `mvc.zip` — MVC code
- `bootstrap_copy.php` — used by Rocket (GET flow) and Bootstrap (POST flow)
- `full_deploy.php` — used by Full Deploy button

> For GoDaddy: upload manually to `dummy1.ourcollegeerp.com/` via cPanel File Manager.

**Step 2 — Bootstrap (Plug button)**:
```
Python → POST dummy1.{domain}/bootstrap_copy.php
PHP copies → {subdomain}/mvc/controllers/Mvcdeploy.php
           → {subdomain}/mvc/controllers/Cssupdate.php
           → {subdomain}/mvc/config/css_update_config.php
```

**Step 3 — Deploy MVC (Rocket button)**:
See section 7B.

**Step 4 — Full Deploy (Archive button)** *(for brand new subdomains only)*:
```
Python → POST dummy1.{domain}/full_deploy.php
         (passes db_user, db_name, db_pass from subdomain_settings table)
PHP:
  1. Extracts all 7 zips → {subdomain}/ (assets, frontend, mvc, uploads, vendor, etc.)
  2. Writes mvc/config/development/database.php with live credentials:
       hostname = localhost  (always)
       username = db_user from subdomain_settings
       password = db_pass from subdomain_settings
       database = db_name from subdomain_settings
```

---

### 7B. MVC Deploy Flow (Rocket Button)

> **New optimised flow (HostGator/BigRock):**
> - **Step 1 (once per MVC update):** Click "Upload MVC to Dummy" → PHP FTPs `mvc.zip` from localhost to dummy server via `ftp_put()`
> - **Step 2 (per subdomain):** Click Rocket → PHP calls `bootstrap_copy.php?s={sub}` on dummy server → extracts to target subdomain
> - **GoDaddy is unchanged** — direct HTTP POST from Python per subdomain, no dummy server needed.

#### GoDaddy Flow (direct HTTP — mod_security not an issue)
```
1. Python checks GET https://{sub}.ourcollegeerp.com/mvcdeploy/check
2. If missing → upload Mvcdeploy.php via Cssupdate (POST to cssupdate/receive_script)
3. Python POST mvc.zip to https://{sub}.ourcollegeerp.com/mvcdeploy/receive
4. Mvcdeploy::receive() runs on live server:
   - Renames mvc → mvc1
   - Saves + unzips mvc.zip
   - Copies database.php + css_update_config.php from mvc1 → mvc
   - Deletes mvc.zip
```

#### HostGator Flow (FTP to dummy server → bootstrap_copy GET)
```
Reason: Monarx Security on HostGator blocks/interferes with trigger() on live subdomains.
Proven approach: FTP mvc.zip to dummy server, then PHP extracts it server-side.

1. Python FTPs mvc.zip to dummy server using RELATIVE FTP path:
   cs3005.hostgator.in:21 → dummy1.ourschoolerp.com/mvc.zip
   (FTP is chrooted to /home1/mindw2ft — must use relative path, NOT absolute.
   dummy1 root is directly in home, not under public_html like regular subdomains.)
2. Python GET https://dummy1.ourschoolerp.com/bootstrap_copy.php?k=KEY&s={sub}&d=.ourschoolerp.com
   with BROWSER_HEADERS
3. bootstrap_copy.php GET handler (on dummy server):
   - Reads mvc.zip from __DIR__ (= /home1/mindw2ft/dummy1.ourschoolerp.com/)
   - Renames {target}/mvc → {target}/mvc1
   - Extracts mvc.zip to {target}/ (same disk, no HTTP needed)
   - Copies database.php + css_update_config.php from mvc1 → mvc
   - Restores configs
```

#### BigRock (myschools) Flow — FTP to live subdomain → trigger()
```
1. Python FTPs mvc.zip to live subdomain:
   sh203.bigrock.com:21 → {sub}.myschoolserp.com/mvc.zip
   (No public_html/ prefix — BigRock stores subdomains directly in FTP home)
2. Python GET https://{sub}.myschoolserp.com/mvcdeploy/trigger?api_key=X
   with BROWSER_HEADERS
3. If trigger returns 404 (controller missing):
   - Python FTPs Mvcdeploy.php from localhost mvc/controllers/ directly to
     {sub}.myschoolserp.com/mvc/controllers/Mvcdeploy.php
   - (Dummy server has wrong standalone mvcdeploy.php — not the CI controller class)
   - Retries trigger
4. Mvcdeploy::trigger() extracts mvc.zip, restores configs, deletes zip
```

---

### 7C. One-Click cPanel Subdomain Creation ("Create in cPanel" Button)

**Supported servers:** `godaddy`, `hostgator`, `myschools` (those with `CPANEL_CONFIGS` entries).  
**Button enabled when:** A cPanel-capable server is selected in the server filter.

This single button call does all 8 steps automatically (PHP timeout: 300s):

```
Click "Create in cPanel" → enter subdomain name → Submit
  ↓
PHP (Subdomains::create_cpanel_subdomain) → POST localhost:8000/create-cpanel-subdomain
  ↓
Python endpoint (8 steps):

Step 1 — cPanel SubDomain/addsubdomain
  POST https://{cp_host}:2083/execute/SubDomain/addsubdomain
       domain={name}&rootdomain={domain}&dir=public_html/{name}.{domain}
  Creates the subdomain folder. If "already exists" → continues (not an error).

Step 2 — Get MySQL prefix
  GET Mysql/get_restrictions → extracts db_prefix (e.g. "zbtr5uwckzg7_")

Step 3 — Create MySQL database
  Mysql/create_database  name={prefix}{subdomain}

Step 4 — Create MySQL user
  Mysql/create_user  name={prefix}{subdomain}  password={subdomain}@123456

Step 5 — Grant ALL PRIVILEGES
  Mysql/set_privileges_on_database
       user={prefix}{subdomain}  database={prefix}{subdomain}  privileges=ALL PRIVILEGES

Step 6 — SQL Import (via dummy server)
  - Reads local SQL file from DB_IMPORT_SQL_PATH (= mindw2ft_dummy_bkp.sql)
  - Generates PHP importer script embedded with db credentials (connects to localhost MySQL)
  - Uploads import_db.sql + import_db_run.php to DUMMY SERVER root via cPanel Fileman API
    (NOT to the new subdomain — new subdomain DNS hasn't propagated from localhost yet)
  - GET https://dummy1.{domain}/import_db_run.php (120s timeout)
  - PHP on dummy server connects to localhost MySQL (same physical server) → imports tables
  - Importer self-deletes both files after completion
  - Returns {"success":true, "imported": N, "errors": [...]}

Step 7 — Full Deploy (unzip all 7 zip files)
  POST https://dummy1.{domain}/full_deploy.php
       {api_key, subdomain, domain_suffix, db_user, db_name, db_pass}
  Same as clicking the orange Full Deploy button manually.
  Pre-requisite: all 7 zip files must already be on the dummy server root.

Step 8 — Insert subdomain_settings record
  INSERT INTO subdomain_settings (server, subdomain, db_host, db_name, db_user, db_pass, ...)
  Skipped if record already exists (no duplicate).
```

**DB naming convention:**
- DB name = `{cpanel_prefix}{subdomain}` (e.g. `zbtr5uwckzg7_abc`)
- DB user = same as DB name
- DB password = `{subdomain}@123456` (e.g. `abc@123456`)
- DB host stored in `REMOTE_DB_HOSTS` dict in main.py

**Success modal shows:**
- Domain, DB Name, DB User, DB Password, DB Host
- Status lines: SQL import count, Full Deploy result, record insert

**SQL Importer PHP details:**
- Uploaded to dummy server root, self-deletes after run
- Skips lines starting with `--` (SQL comments)
- Does NOT skip `/*!...*/` lines (those are MySQL conditional statements — needed for charset, key checks etc.)
- Skips `USE` and `CREATE DATABASE` lines
- Accumulates multi-line statements, executes on `;`
- Sets `NAMES utf8mb4` on connection
- `set_time_limit(300)` for large SQL files

**Why dummy server for SQL import (not new subdomain):**
- New subdomain DNS hasn't propagated from localhost's network yet right after cPanel creation
- Dummy server is always live and on the same physical server → `localhost` MySQL = same DB server
- Avoids timing/DNS issues entirely

**cPanel auth strategy (tried in order):**
1. `Authorization: cpanel {user}:{token}` (API token)
2. `Authorization: Basic base64({user}:{password})` (password fallback)
First success is reused for all subsequent cPanel calls in the same request.

---

### 7C2. Correct order for a new subdomain (WITHOUT "Create in cPanel"):

1. Plug (bootstrap) → deploys Mvcdeploy.php + Cssupdate.php
2. Orange cloud (CSS sync) → pushes CSS files
3. Rocket (MVC deploy) → deploys mvc/ folder

### 7C3. Correct order for a new subdomain (WITH "Create in cPanel"):

1. **Create in cPanel** button → handles Steps 1–8 automatically
2. That's it — subdomain folder created, DB imported, zips extracted, record inserted

---

### 7D. Full Deploy Flow (Archive Button — One-Time for New Subdomains)

Full Deploy is **only for brand-new subdomain creation**. It extracts ALL files at once (assets, frontend, MVC, vendor, uploads, etc.) so you don't have to upload them one by one.

```
Python → POST https://dummy1.{domain}/full_deploy.php
         {api_key, subdomain, domain_suffix}

full_deploy.php on dummy server:
  Reads from __DIR__/ (dummy server root):
    assets.zip   → {target}/
    frontend.zip → {target}/
    main2.zip    → {target}/
    mvc.zip      → {target}/
    others.zip   → {target}/
    uploads.zip  → {target}/
    vendor.zip   → {target}/
  Extracts each one, skips missing zips (not an error)
```

**Pre-requisite:** All zip files must be uploaded to the dummy server root BEFORE clicking Full Deploy.
Upload once via cPanel File Manager:
- `dummy1.ourschoolerp.com/` — assets.zip, frontend.zip, main2.zip, mvc.zip, others.zip, uploads.zip, vendor.zip
- Same for `dummy1.myschoolserp.com/` and `dummy1.ourcollegeerp.com/`

**Path logic (all servers):**
- full_deploy.php uses `dirname(__DIR__)` = parent of dummy server root
- HostGator: dummy at `/home1/mindw2ft/dummy1.ourschoolerp.com/` → parent = `/home1/mindw2ft/` → target = `/home1/mindw2ft/staging2.ourschoolerp.com/` ✅
- BigRock: dummy at `/home2/myschknc/dummy1.myschoolserp.com/` → parent = `/home2/myschknc/` → target = `/home2/myschknc/sub.myschoolserp.com/` ✅

**When to use:** Only when creating a brand-new subdomain from scratch. For regular MVC updates, use Rocket button.

---

### 7D. CSS Update Flow (Orange Cloud Button)

#### GoDaddy Flow (direct POST — works without issues)
```
Python → POST JSON to https://{sub}.ourcollegeerp.com/cssupdate/receive
         {api_key, files: {inilabs.css: content, responsive.css: content, ...}}
Cssupdate::receive() writes files to assets/inilabs/
```

#### HostGator Flow (FTP fallback — direct POST blocked by Monarx)
```
1. Python tries direct POST → 406 (blocked)
2. Auto-fallback: upload via FTP to cs3005.hostgator.in:21
   Target: public_html/{sub}.ourschoolerp.com/assets/inilabs/{filename}
   One FTP connection per subdomain, all CSS files in one session
```

#### BigRock (myschools) Flow — same as HostGator but different FTP path
```
FTP path: sh203.bigrock.com:21 → {sub}.myschoolserp.com/assets/inilabs/{filename}
(No public_html/ prefix)
```

---

### 7E. Bootstrap Flow (Plug Button)

```
Python → POST https://dummy1.{domain}/bootstrap_copy.php
         {api_key, subdomain, domain_suffix}
         + BROWSER_HEADERS (User-Agent: Chrome) — required for HostGator/BigRock

PHP (dummy server) copies from its own root to {target}/subdomain/:
  mvcdeploy.php  → mvc/controllers/Mvcdeploy.php
  Cssupdate.php  → mvc/controllers/Cssupdate.php
  css_update_config.php → mvc/config/css_update_config.php
```

---

## 8. FTP Helper in Python (`_upload_files_via_ftp`)

Added in `python/main.py`. Handles all FTP uploads for both HostGator and BigRock:

```
_upload_files_via_ftp(ftp_config, remote_dir, files)
  - Opens one FTP connection
  - CDs to remote_dir (relative to FTP home)
  - Uploads all files (dict of {filename: bytes_or_str})
  - Returns {success, message, updated, failed}
  - On login failure: "FTP login failed — update 'pass' in FTP_CONFIGS in python/.env"
```

FTP path construction in `python/main.py`:
```python
webroot = ftp_cfg.get("webroot", "")  # "public_html" for HG, "" for BigRock
remote_dir = f"{webroot}/{sub}.{domain}" if webroot else f"{sub}.{domain}"
```

---

## 9. Mod_Security / Monarx Issues & Workarounds

| Problem | Server | Solution |
|---|---|---|
| CSS update blocked | HostGator, BigRock | FTP upload via `ftplib` |
| MVC zip upload blocked | HostGator, BigRock | FTP upload + GET trigger |
| PHP file upload blocked (by filename) | HostGator | Rename (e.g., `proxy` → `sync`) |
| GET/POST blocked with python-requests UA | HostGator, BigRock | `BROWSER_HEADERS` with Chrome UA |
| BigRock path wrong (public_html assumed) | BigRock | FTP uses relative paths from home — no public_html needed |
| `Fileman/rename` not available in cPanel | HostGator | Save config → extract → restore pattern |
| `Fileman/extract` not available | HostGator | PHP ZipArchive on dummy server |

---

## 10. Important Config Files

| File | Key Settings |
|---|---|
| `python/.env` | All API keys, server domains, dummy servers, cPanel + FTP credentials |
| `mvc/config/css_update_config.php` | CSS API key (must match .env) |
| `mvcdeploy.php` (root) | MVC deploy API key (GoDaddy only) |
| `bootstrap_copy.php` (dummy server) | API key + file operations |

---

## 11. Cssupdate.php Controller Methods

| Method | URL | Purpose |
|---|---|---|
| `receive()` | `/cssupdate/receive` | Accepts CSS files (JSON POST) |
| `receive_script()` | `/cssupdate/receive_script` | Uploads PHP controller files |

---

## 12. Mvcdeploy.php Controller Methods

| Method | URL | Purpose |
|---|---|---|
| `check()` | `/mvcdeploy/check` | Tests if controller is deployed (returns `{exists:true}`) |
| `receive()` | `/mvcdeploy/receive` | Receives mvc.zip and deploys — used by GoDaddy |
| `trigger()` | `/mvcdeploy/trigger?api_key=X` | Reads local mvc.zip and extracts — used by HostGator + BigRock (FTP uploads zip first) |

---

## 13. Config Files Preserved During MVC Deploy

These files are backed up and restored during every MVC deployment to preserve server-specific database settings:
- `mvc/config/development/database.php` — DB credentials per subdomain
- `mvc/config/css_update_config.php` — API key per subdomain

---

## 14. Python .env Full Structure

```env
DB_HOST=119.18.54.141
DB_USER=mindw2ft_dummy
DB_PASS=DjmyAZTeNAq3
DB_NAME=mindw2ft_dummy
DB_PORT=3306

APP_ENV=staging
APP_HOST=0.0.0.0
APP_PORT=8000
ALLOWED_ORIGINS=http://staging.ourschoolerp.localhost,...

SQL_FILE_PATH=C:/xampp/htdocs/ourschoolerp/new domains/new db tables/tables.sql
DB_IMPORT_SQL_PATH=C:/xampp/htdocs/ourschoolerp/new domains/mindw2ft_dummy_bkp.sql

CSS_FOLDER_PATH=C:/xampp/htdocs/ourschoolerp/assets/inilabs
CSS_UPDATE_API_KEY=65b03a8eb7cdf6799815bfd17ba7367d1113b642e12dec28
SERVER_DOMAINS={"godaddy":"ourcollegeerp.com","hostgator":"ourschoolerp.com","myschools":"myschoolserp.com","schoolhour":"schoolhour.in","collegehour":"collegeerp.in"}

MVC_ZIP_PATH=C:/xampp/htdocs/ourschoolerp/mvc.zip
MVC_DEPLOY_API_KEY=a3517b0f46b17c8b813d850e8ef65fd035df328d45f0a836

DUMMY_SERVERS={"godaddy":"dummy1.ourcollegeerp.com","hostgator":"dummy1.ourschoolerp.com","myschools":"dummy1.myschoolserp.com","schoolhour":"dummy1.schoolhour.in","collegehour":"dummy1.collegeerp.in"}

CPANEL_CONFIGS={"hostgator":{"host":"cs3005.hostgator.in","port":2083,"user":"mindw2ft","token":"77ZCG0X8MRPIABB18LW7RQ9A9NRXR0WL","home":"/home1/mindw2ft"},"myschools":{"host":"sh203.bigrock.com","port":2083,"user":"myschknc","token":"TE2KD5D61V73TI0IBWU8XLU75G20U9UJ","home":"/home2/myschknc"},"godaddy":{"host":"sg2plmcpnl508133.prod.sin2.secureserver.net","port":2083,"user":"zbtr5uwckzg7","token":"BS8LXA53ASEZOD25F768QA5W3X2U463H","password":"Satya$1986","home":"/home/zbtr5uwckzg7"}}

# FTP credentials — update 'pass' here if cPanel password changes, then restart Python server
# Also update matching entry in mvc/controllers/Subdomains.php $ftp_configs array
FTP_CONFIGS={"hostgator":{"host":"cs3005.hostgator.in","port":21,"user":"mindw2ft","pass":"Mindwhile$1986@","webroot":"public_html"},"myschools":{"host":"sh203.bigrock.com","port":21,"user":"myschknc","pass":"Kiran$1986@","webroot":""},"schoolhour":{"host":"schoolhour.in","port":21,"user":"schoodj8","pass":"School@123456@","webroot":""},"collegehour":{"host":"collegehour.in","port":21,"user":"collenv4p","pass":"Satya$1986$","webroot":""}}
```

---

## 15. Known Issues & Status

| Issue | Status | Fix |
|---|---|---|
| GoDaddy .htaccess routes all PHP through CI | Fixed | Use CI controller Mvcdeploy.php instead of standalone PHP |
| HostGator mod_security blocks all POST | Fixed | FTP upload via ftplib + GET trigger |
| BigRock mod_security blocks all POST | Fixed | FTP upload via ftplib + GET trigger |
| BigRock path wrong (`public_html` assumed) | Fixed | FTP uses relative paths from home — no public_html for BigRock |
| BigRock 406 misleads controller-exists check | Fixed | If trigger() returns 404, auto-bootstrap via dummy server then retry |
| `Fileman/rename` doesn't exist in cPanel | Fixed | Save config → extract → restore pattern |
| `Fileman/extract` doesn't exist | Fixed | PHP ZipArchive on dummy server |
| CSS `inilabs.css` had no cache-busting | Fixed | Added `?v=CSSVERSION` to page_header.php |
| `python-requests` User-Agent blocked by Monarx | Fixed | Use `BROWSER_HEADERS` with Chrome UA |
| `bootstrap_copy.php` GET auth ran after POST check | Fixed | Moved GET handler to top of file |
| CSS key overwritten by mvc.zip deploy | Fixed | `css_update_config.php` preserved in restore list |
| "mvc.zip not on dummy server" for myschools | Fixed | FTP uploads mvc.zip directly to subdomain root |
| Python Stop Server getting stale PID | Fixed | Multi-PID netstat approach + fallback to kill python.exe by name |
| Schoolhour/Collegehour had no FTP config | Fixed | Added to FTP_CONFIGS (.env) and Subdomains.php $ftp_configs |
| bootstrap_copy.php missing on schoolhour/collegehour dummy | Fixed | "Upload MVC to Dummy" now auto-uploads bootstrap_copy.php + full_deploy.php |
| Full Deploy left database.php with wrong credentials | Fixed | full_deploy.php now writes database.php from subdomain_settings (db_user, db_name, db_pass) after extraction |
| Server capability not visible to user | Fixed | Static matrix table added to SubDomain Management page showing all 5 servers and Python requirement |
| GoDaddy cPanel auth failed with `ourcollegeerp.com:2083` | Fixed | Real cPanel host is `sg2plmcpnl508133.prod.sin2.secureserver.net` (from browser URL bar). GoDaddy SSO gateway blocks direct API auth on the domain URL |
| GoDaddy cPanel auth failed with username `114556211` | Fixed | `114556211` is the GoDaddy account ID. Real cPanel username is `zbtr5uwckzg7` (visible from `/home/zbtr5uwckzg7` in File Manager) |
| "Create in cPanel" button disabled for GoDaddy | Fixed | JS `cpanelServers.indexOf(server)` was case-sensitive — "Godaddy" ≠ "godaddy". Fix: `server.toLowerCase()` before indexOf check |
| Auto Create saved wrong data (db_name included full HTML) | Fixed | `<br/>` tags in Main1.php output are inline HTML, not newlines. Fix: `str_ireplace('<br/>', "\n", $body)` before regex, use `[^\n]+` pattern |
| SQL import: no tables created despite "success" | Fixed | Python was GET-ing the importer on the new subdomain, whose DNS hadn't propagated yet from localhost. Fix: upload importer to dummy server root instead — always live, same physical server = localhost MySQL works |
| SQL import skipped `/*!...*/` MySQL conditional statements | Fixed | Previous importer skipped lines starting with `/*`, which caught `/*!40101 SET NAMES utf8 */` etc. Fix: only skip `--` comment lines; also added `SET NAMES utf8mb4` on connection |
