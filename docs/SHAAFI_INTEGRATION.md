# Shaafi App & Blood Bank System Integration

**Document version:** 1.2  
**Last updated:** July 5, 2026  
**Integration direction:** One-way (Shaafi App → Blood Bank System)

---

## 1. Objective

Enable Shaafi App users to submit blood-related requests directly to the Blood Bank system. Users can register as blood donors or request blood through the Shaafi App. The Blood Bank system receives, stores, and manages all operational follow-up (review, approval, scheduling, SMS, and fulfilment).

| System | Responsibility |
|--------|------------------|
| **Shaafi App** | Capture user data, display forms, call Blood Bank APIs, show submission confirmation |
| **Blood Bank System** | Receive requests, agent review, approvals, scheduling, SMS notifications, internal workflows |

---

## 2. Integration Scope

The integration supports **two request types**, both submitted from the Shaafi App to the Blood Bank system through a single **Submit Request API**.

| # | Request Type | API value (`request_type`) | Description |
|---|--------------|----------------------------|-------------|
| 1 | **Blood Donation Request** | `donation` | Shaafi user registers as a blood donor and selects a preferred hospital for donation. |
| 2 | **Blood Request** | `blood_request` | Shaafi user requests blood (specifying quantity in bags) for a patient at a selected hospital. |

### 2.1 Blood Donation Request

Used when a Shaafi App user wants to **donate blood**.

| Capability | Included |
|------------|----------|
| Submit donor details from Shaafi App | Yes |
| Select city and hospital | Yes |
| Specify blood group | Yes |
| Specify blood quantity (bags) | No |
| Blood Bank agent review & scheduling | Yes (internal portal) |
| SMS / follow-up by Blood Bank | Yes (internal) |

### 2.2 Blood Request

Used when a Shaafi App user needs to **request blood** for a patient.

| Capability | Included |
|------------|----------|
| Submit requester details from Shaafi App | Yes |
| Select city and hospital | Yes |
| Specify blood group | Yes |
| Specify blood quantity (1–10 bags) | **Yes — required** |
| Blood Bank agent review & fulfilment | Yes (internal portal) |
| SMS / follow-up by Blood Bank | Yes (internal) |

### 2.3 Shared integration components

Both request types use the same APIs and shared fields:

| Component | Blood Donation Request | Blood Request |
|-----------|------------------------|---------------|
| Get Cities API | Yes | Yes |
| Get Hospitals API | Yes | Yes |
| Submit Request API | Yes (`request_type: donation`) | Yes (`request_type: blood_request`) |
| Agent portal review | Yes | Yes |

### 2.4 Out of scope for Shaafi App (handled by Blood Bank only)

- Request approval / rejection decisions
- Appointment scheduling and SMS notifications to users
- Donor eligibility screening and lab tests
- Blood inventory allocation and transfusion
- Status updates back to Shaafi App (v1 is submit-only)

---

## 3. Architecture

```
┌─────────────────┐         HTTPS / JSON API          ┌──────────────────────────┐
│   Shaafi App    │  ──────────────────────────────►  │   Blood Bank System      │
│   (Mobile)      │         API Key Auth              │   sombloodbank.net       │
└─────────────────┘                                   └────────────┬─────────────┘
                                                                   │
                                                                   ▼
                                                        ┌──────────────────────┐
                                                        │   Agent Portal       │
                                                        │   /shaafi-requests   │
                                                        │   (Web – staff only) │
                                                        └──────────────────────┘
```

**Integration type:** REST JSON over HTTPS  
**API version:** `v1`  
**Base path:** `/api/v1`

---

## 4. Environment & Authentication

### 4.1 Base URL

| Environment | Base URL |
|-------------|----------|
| Production | `https://sombloodbank.net/api/v1` |
| Staging | _Configure per deployment_ |

### 4.2 API Key

All API requests must include a shared secret configured on the Blood Bank server as `SHAAFI_API_KEY`.

**Option A — Bearer token (recommended):**

```http
Authorization: Bearer {SHAAFI_API_KEY}
```

**Option B — Custom header:**

```http
X-API-Key: {SHAAFI_API_KEY}
```

### 4.3 Request headers

| Header | Required | Value |
|--------|----------|-------|
| `Authorization` or `X-API-Key` | Yes | API key |
| `Accept` | Yes | `application/json` |
| `Content-Type` | Yes (POST) | `application/json` |

### 4.4 Authentication errors

| HTTP Status | Meaning |
|-------------|---------|
| `401` | Invalid or missing API key |
| `503` | API key not configured on server (`SHAAFI_API_KEY` missing in `.env`) |

**Example — 401 response:**

```json
{
  "success": false,
  "message": "Unauthorized. Invalid or missing API key."
}
```

---

## 5. Supported Request Types

| `request_type` value | Display name | Description |
|----------------------|--------------|-------------|
| `donation` | Blood Donation Request | User wants to register and donate blood at the selected hospital |
| `blood_request` | Blood Request | User needs blood (quantity in bags) for a patient at the selected hospital |

---

## 6. Field Reference

| Field | Type | Required | Blood Donation Request | Blood Request | Description |
|-------|------|----------|------------------------|---------------|-------------|
| `request_type` | string | Yes | Yes | Yes | `donation` or `blood_request` |
| `full_name` | string | Yes | Yes | Yes | User's full name (max 255 chars). Auto-populated from Shaafi profile. |
| `mobile_number` | string | Yes | Yes | Yes | Contact number (max 20 chars). Auto-populated from Shaafi profile. |
| `blood_group` | string | Yes | Yes | Yes | One of: `A+`, `A-`, `B+`, `B-`, `AB+`, `AB-`, `O+`, `O-` |
| `blood_quantity` | integer | — | No | **Yes** | Number of bags: `1`–`10`. Required only for Blood Request. |
| `city` | string | Yes | Yes | Yes | City from Get Cities API |
| `hospital_id` | integer | Yes | Yes | Yes | Hospital ID from Get Hospitals API (filtered by city) |
| `additional_notes` | string | No | Yes | Yes | Free-text notes (max 2000 chars) |
| `shaafi_user_id` | string | No | Yes | Yes | Shaafi App internal user identifier |
| `external_reference` | string | No | Yes | Yes | Unique idempotency key from Shaafi App (max 100 chars) |

---

## 7. API Endpoints

### 7.1 Get Cities

Returns a list of cities that have at least one **active** hospital registered in the Blood Bank system.

**Request**

```http
GET /api/v1/cities
Authorization: Bearer {SHAAFI_API_KEY}
Accept: application/json
```

**Success response — `200 OK`**

```json
{
  "success": true,
  "data": [
    { "name": "Hargeisa" },
    { "name": "Burao" }
  ]
}
```

**Shaafi App usage:** Populate the **Location (City)** dropdown.

**cURL**

```bash
curl -X GET "https://sombloodbank.net/api/v1/cities" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"
```

---

### 7.2 Get Hospitals by City

Returns active hospitals filtered by the selected city.

**Request**

```http
GET /api/v1/hospitals?city=Hargeisa
Authorization: Bearer {SHAAFI_API_KEY}
Accept: application/json
```

| Query parameter | Required | Description |
|-----------------|----------|-------------|
| `city` | Yes | Exact city name from Get Cities API |

**Success response — `200 OK`**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Shaafi General Hospital",
      "address": "Wadada Madaxtooyada",
      "city": "Hargeisa",
      "phone": "+252612345678",
      "email": "info@hospital.com"
    }
  ]
}
```

**Validation error — `422 Unprocessable Entity`** (missing `city`):

```json
{
  "message": "The city field is required.",
  "errors": {
    "city": ["The city field is required."]
  }
}
```

**Shaafi App usage:** Populate the **Hospital** dropdown after city selection. Store `id` for submission.

**cURL**

```bash
curl -X GET "https://sombloodbank.net/api/v1/hospitals?city=Hargeisa" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"
```

---

### 7.3 Submit Request

Submits a **Blood Donation Request** or **Blood Request** to the Blood Bank system. Use `request_type` to distinguish the two.

**Request**

```http
POST /api/v1/requests
Authorization: Bearer {SHAAFI_API_KEY}
Accept: application/json
Content-Type: application/json
```

#### Example A — Blood Donation Request (`request_type: donation`)

```json
{
  "request_type": "donation",
  "full_name": "Ahmed Hassan Ali",
  "mobile_number": "6345671311",
  "blood_group": "A+",
  "city": "Hargeisa",
  "hospital_id": 1,
  "additional_notes": "Available on weekdays after 2 PM",
  "shaafi_user_id": "shaafi-user-1024",
  "external_reference": "shaafi-req-20260705-001"
}
```

#### Example B — Blood Request (`request_type: blood_request`)

```json
{
  "request_type": "blood_request",
  "full_name": "Fatima Mohamed",
  "mobile_number": "634160295",
  "blood_group": "O+",
  "blood_quantity": 2,
  "city": "Hargeisa",
  "hospital_id": 1,
  "additional_notes": "Emergency surgery scheduled tomorrow",
  "shaafi_user_id": "shaafi-user-2048",
  "external_reference": "shaafi-req-20260705-002"
}
```

**Success response — `201 Created`**

```json
{
  "success": true,
  "message": "Request submitted successfully.",
  "data": {
    "reference_number": "SR-20260705-A1B2C3",
    "request_type": "blood_request",
    "full_name": "Fatima Mohamed",
    "mobile_number": "634160295",
    "blood_group": "O+",
    "blood_quantity": 2,
    "city": "Hargeisa",
    "hospital": {
      "id": 1,
      "name": "Shaafi General Hospital"
    },
    "additional_notes": "Emergency surgery scheduled tomorrow",
    "status": "pending",
    "submitted_at": "2026-07-05T10:30:00+00:00"
  }
}
```

**Idempotent resubmit — `200 OK`** (when `external_reference` already exists):

```json
{
  "success": true,
  "message": "Request already submitted.",
  "data": { "...same structure as above..." }
}
```

**Shaafi App usage:** Display `reference_number` and `message` as confirmation to the user.

**cURL — Blood Donation Request**

```bash
curl -X POST "https://sombloodbank.net/api/v1/requests" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "request_type": "donation",
    "full_name": "Ahmed Hassan Ali",
    "mobile_number": "6345671311",
    "blood_group": "A+",
    "city": "Hargeisa",
    "hospital_id": 1,
    "additional_notes": "Available on weekdays after 2 PM",
    "shaafi_user_id": "shaafi-user-1024",
    "external_reference": "shaafi-req-20260705-001"
  }'
```

**cURL — Blood Request**

```bash
curl -X POST "https://sombloodbank.net/api/v1/requests" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "request_type": "blood_request",
    "full_name": "Fatima Mohamed",
    "mobile_number": "634160295",
    "blood_group": "O+",
    "blood_quantity": 2,
    "city": "Hargeisa",
    "hospital_id": 1,
    "additional_notes": "Emergency surgery scheduled tomorrow",
    "shaafi_user_id": "shaafi-user-2048",
    "external_reference": "shaafi-req-20260705-002"
  }'
```

#### Validation errors — `422 Unprocessable Entity`

```json
{
  "message": "Blood quantity is required for blood request submissions.",
  "errors": {
    "blood_quantity": ["Blood quantity is required for blood request submissions."]
  }
}
```

#### Hospital / city mismatch — `422 Unprocessable Entity`

```json
{
  "success": false,
  "message": "The selected hospital is not available in the chosen city."
}
```

---

## 8. Functional Workflow

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                           SHAAFI APP (User)                                  │
└──────────────────────────────────────────────────────────────────────────────┘
        │
        │  1. User opens Blood Donation Request OR Blood Request form
        ▼
        │  2. GET /api/v1/cities  →  populate City dropdown
        ▼
        │  3. User selects city
        │  4. GET /api/v1/hospitals?city={city}  →  populate Hospital dropdown
        ▼
        │  5. User fills form:
        │     • Both types: name, phone, blood group, city, hospital, notes
        │     • Blood Request only: blood quantity (bags)
        │     Fields auto-populated from Shaafi profile where applicable
        ▼
        │  6. POST /api/v1/requests  (request_type: donation | blood_request)
        ▼
        │  7. Show confirmation screen with reference_number
        ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│                        BLOOD BANK SYSTEM (Backend)                           │
└──────────────────────────────────────────────────────────────────────────────┘
        │
        │  8. Request stored with status: pending
        ▼
        │  9. Agent reviews in portal (/shaafi-requests)
        ▼
        │ 10. Agent approves / rejects / schedules appointment
        ▼
        │ 11. Blood Bank contacts user (SMS / phone) — handled internally
        ▼
        │ 12. Donation or blood fulfilment completed
        ▼
```

---

## 9. Request Status Lifecycle (Blood Bank Internal)

These statuses are managed by Blood Bank agents. **Shaafi App does not receive status updates via API** in v1 (one-way integration).

| Status | Description |
|--------|-------------|
| `pending` | Newly submitted from Shaafi App |
| `under_review` | Agent is reviewing the request |
| `approved` | Request approved |
| `rejected` | Request rejected |
| `scheduled` | Appointment scheduled |
| `completed` | Request fulfilled |
| `cancelled` | Request cancelled |

---

## 10. Blood Bank Agent Portal

Agents manage all submitted requests through the web portal (not via API).

| Item | Detail |
|------|--------|
| **URL** | `https://sombloodbank.net/shaafi-requests` |
| **Access** | Authenticated Blood Bank staff session |
| **Roles** | super_admin, admin, hospital_admin, reception, nurse |

### Agent capabilities

- View all Shaafi App submissions
- Search by name, phone, or reference number
- Filter by request type, status, city, hospital
- Approve or reject requests
- Schedule donation / collection appointments
- Record agent notes and follow-up actions

Hospital-scoped staff only see requests for their assigned hospital. Super admins can view all hospitals or filter by active tenant context.

---

## 11. Error Handling Summary

| HTTP Status | Scenario |
|-------------|----------|
| `200` | Successful GET; idempotent resubmit of existing request |
| `201` | New request created successfully |
| `401` | Missing or invalid API key |
| `422` | Validation failed or hospital/city mismatch |
| `503` | API integration not configured on server |

All error responses follow JSON format. Validation errors include an `errors` object with field-level messages.

---

## 12. cURL API Reference (Copy & Paste)

Set your API key once, then run any command below.

```bash
export BASE_URL="https://sombloodbank.net/api/v1"
export API_KEY="YOUR_API_KEY"
```

> **Alternative auth:** replace `Authorization: Bearer $API_KEY` with `-H "X-API-Key: $API_KEY"`

---

### 12.1 Get Cities

```bash
curl -X GET "$BASE_URL/cities" \
  -H "Authorization: Bearer $API_KEY" \
  -H "Accept: application/json"
```

**Expected response (`200`):**

```json
{
  "success": true,
  "data": [
    { "name": "Hargeisa" },
    { "name": "Burao" }
  ]
}
```

---

### 12.2 Get Hospitals by City

```bash
curl -X GET "$BASE_URL/hospitals?city=Hargeisa" \
  -H "Authorization: Bearer $API_KEY" \
  -H "Accept: application/json"
```

**Expected response (`200`):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Shaafi General Hospital",
      "address": "Wadada Madaxtooyada",
      "city": "Hargeisa",
      "phone": "+252612345678",
      "email": "info@hospital.com"
    }
  ]
}
```

---

### 12.3 Submit Blood Donation Request

```bash
curl -X POST "$BASE_URL/requests" \
  -H "Authorization: Bearer $API_KEY" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "request_type": "donation",
    "full_name": "Ahmed Hassan Ali",
    "mobile_number": "6345671311",
    "blood_group": "A+",
    "city": "Hargeisa",
    "hospital_id": 1,
    "additional_notes": "Available on weekdays after 2 PM",
    "shaafi_user_id": "shaafi-user-1024",
    "external_reference": "shaafi-req-20260705-001"
  }'
```

**Expected response (`201`):**

```json
{
  "success": true,
  "message": "Request submitted successfully.",
  "data": {
    "reference_number": "SR-20260705-A1B2C3",
    "request_type": "donation",
    "status": "pending",
    "submitted_at": "2026-07-05T10:30:00+00:00"
  }
}
```

---

### 12.4 Submit Blood Request

```bash
curl -X POST "$BASE_URL/requests" \
  -H "Authorization: Bearer $API_KEY" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "request_type": "blood_request",
    "full_name": "Fatima Mohamed",
    "mobile_number": "634160295",
    "blood_group": "O+",
    "blood_quantity": 2,
    "city": "Hargeisa",
    "hospital_id": 1,
    "additional_notes": "Emergency surgery scheduled tomorrow",
    "shaafi_user_id": "shaafi-user-2048",
    "external_reference": "shaafi-req-20260705-002"
  }'
```

**Expected response (`201`):**

```json
{
  "success": true,
  "message": "Request submitted successfully.",
  "data": {
    "reference_number": "SR-20260705-X9Y8Z7",
    "request_type": "blood_request",
    "blood_quantity": 2,
    "status": "pending",
    "submitted_at": "2026-07-05T10:35:00+00:00"
  }
}
```

---

### 12.5 Full integration test (run in order)

```bash
# 1. Set credentials
export BASE_URL="https://sombloodbank.net/api/v1"
export API_KEY="YOUR_API_KEY"

# 2. Get cities
curl -s -X GET "$BASE_URL/cities" \
  -H "Authorization: Bearer $API_KEY" \
  -H "Accept: application/json" | jq

# 3. Get hospitals in Hargeisa
curl -s -X GET "$BASE_URL/hospitals?city=Hargeisa" \
  -H "Authorization: Bearer $API_KEY" \
  -H "Accept: application/json" | jq

# 4. Submit blood donation request
curl -s -X POST "$BASE_URL/requests" \
  -H "Authorization: Bearer $API_KEY" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "request_type": "donation",
    "full_name": "Ahmed Hassan",
    "mobile_number": "6345671311",
    "blood_group": "A+",
    "city": "Hargeisa",
    "hospital_id": 1,
    "additional_notes": "Test donation request"
  }' | jq

# 5. Submit blood request
curl -s -X POST "$BASE_URL/requests" \
  -H "Authorization: Bearer $API_KEY" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "request_type": "blood_request",
    "full_name": "Fatima Mohamed",
    "mobile_number": "634160295",
    "blood_group": "O+",
    "blood_quantity": 2,
    "city": "Hargeisa",
    "hospital_id": 1,
    "additional_notes": "Test blood request"
  }' | jq
```

> `jq` is optional — remove `| jq` if not installed. Use `-v` on any curl for verbose debug output.

---

### 12.6 Error test examples

**Invalid API key (`401`):**

```bash
curl -X GET "$BASE_URL/cities" \
  -H "Authorization: Bearer wrong-key" \
  -H "Accept: application/json"
```

**Missing city parameter (`422`):**

```bash
curl -X GET "$BASE_URL/hospitals" \
  -H "Authorization: Bearer $API_KEY" \
  -H "Accept: application/json"
```

**Blood request without quantity (`422`):**

```bash
curl -X POST "$BASE_URL/requests" \
  -H "Authorization: Bearer $API_KEY" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "request_type": "blood_request",
    "full_name": "Test User",
    "mobile_number": "634000000",
    "blood_group": "A+",
    "city": "Hargeisa",
    "hospital_id": 1
  }'
```

---

## 13. Blood Bank Server Setup

### 13.1 Environment variables

Add to production `.env`:

```env
APP_URL=https://sombloodbank.net
SHAAFI_API_KEY=your-secure-random-key-here
```

Generate a secure key:

```bash
openssl rand -hex 32
```

Share the key securely with the Shaafi App development team.

### 13.2 Deployment checklist

```bash
cd /usr/share/nginx/html/ShaafiBloodBnk
git pull
php artisan migrate --force
sudo -u apache php artisan config:clear
sudo -u apache php artisan cache:clear
chown -R apache:apache storage bootstrap/cache
```

### 13.3 Hospital data requirements

For cities and hospitals to appear in the APIs:

- Each hospital must have `status = active`
- Each hospital must have a non-empty `city` value
- Hospital `city` must match exactly what is sent in API requests

Manage hospitals via Super Admin portal or hospital admin UI.

---

## 14. Shaafi App Implementation Checklist

- [ ] Implement **two separate forms** in Shaafi App:
  - **Blood Donation Request** → `request_type: donation`
  - **Blood Request** → `request_type: blood_request` (include blood quantity field)
- [ ] Store `SHAAFI_API_KEY` securely (never in client-side code if possible; use backend proxy if needed)
- [ ] On form load: call **Get Cities** and populate city dropdown
- [ ] On city change: call **Get Hospitals** with selected city
- [ ] Auto-fill `full_name` and `mobile_number` from Shaafi user profile
- [ ] Show blood group dropdown with allowed values
- [ ] Show blood quantity dropdown (1–10 bags) only for **Blood Request** form
- [ ] On submit: POST to **Submit Request** with all required fields
- [ ] Send `external_reference` (unique per submission) to prevent duplicates on retry
- [ ] Send `shaafi_user_id` to link request to Shaafi account
- [ ] On success: show `reference_number` and confirmation message to user
- [ ] Handle `401`, `422`, and network errors with user-friendly messages

---

## 15. Out of Scope (v1)

The following are **not** included in this integration version:

- Status callback / webhook from Blood Bank to Shaafi App
- Real-time push notifications to Shaafi App on approval or scheduling
- User authentication via Shaafi OAuth (API key only)
- Payment processing
- Direct donor registration in Blood Bank donor module (handled by agents after review)

Future versions may add status polling or webhook endpoints if required.

---

## 16. Support & Contacts

| Role | Contact |
|------|---------|
| Blood Bank technical team | _Add contact_ |
| Shaafi App development team | _Add contact_ |
| API key provisioning | Blood Bank system administrator |

---

## Appendix A — Allowed Values

### Blood groups

`A+`, `A-`, `B+`, `B-`, `AB+`, `AB-`, `O+`, `O-`

### Blood quantities (blood_request only)

`1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10` (bags)

### Request types

`donation`, `blood_request`

---

## Appendix B — Reference Number Format

Each submitted request receives a unique reference number:

```
SR-YYYYMMDD-XXXXXX
```

Example: `SR-20260705-A1B2C3`

This is the confirmation number shown to the user in Shaafi App and used by Blood Bank agents for lookup.
