# GeoCam — Flutter Device Registration API

### 1. API Endpoint

- **Method:** `POST`
- **Path:** `/api/v1/devices/register`
- **Headers:**
  ```http
  Content-Type: application/json
  Accept: application/json
  ```

#### Base URLs:
- **Same Wi-Fi Testing (Physical Device):** `http://192.168.1.41:8000/api/v1/devices/register`
- **Android Emulator:** `http://10.0.2.2:8000/api/v1/devices/register`
- **iOS Simulator:** `http://127.0.0.1:8000/api/v1/devices/register`
- **Production Server:** `https://yourdomain.com/api/v1/devices/register`

> **Note:** Call this API on app launch (first install and subsequent app opens).

---

### 2. Fields Required from Flutter App

| Key | Type | Required | Description / Example |
| :--- | :--- | :---: | :--- |
| `installation_id` | `String` | **Yes** | Unique app install UUID (stored in `SharedPreferences`). E.g. `INS-8F29A1B4` |
| `firebase_installation_id` | `String` | No | Firebase FID from `FirebaseInstallations.instance.getId()` |
| `hardware_id` | `String` | No | Android ID / iOS `identifierForVendor` |
| `fcm_token` | `String` | No | FCM push token from `FirebaseMessaging.instance.getToken()` |
| `device_manufacturer` | `String` | No | `Samsung`, `Xiaomi`, `Apple`, etc. |
| `device_brand` | `String` | No | `Samsung`, `Redmi`, `Apple`, etc. |
| `device_model` | `String` | No | `Galaxy S24`, `iPhone 15`, etc. |
| `device_code` | `String` | No | Hardware model code (e.g. `SM-S921B`, `iPhone16,1`) |
| `cpu_architecture` | `String` | No | `arm64-v8a`, `armeabi-v7a`, `arm64` |
| `platform` | `String` | No | `Android` or `iOS` |
| `os_version` | `String` | No | `Android 15`, `iOS 18.2` |
| `sdk_version` | `int` | No | Target SDK integer (e.g. `35`) |
| `app_version` | `String` | No | App version name (e.g. `1.0.0`) |
| `app_build_number` | `int` | No | App build number (e.g. `42`) |
| `screen_resolution` | `String` | No | Physical resolution (e.g. `1080x2340`) |
| `language` | `String` | No | Device language (e.g. `English`, `en_US`) |
| `timezone` | `String` | No | Device timezone (e.g. `Asia/Kolkata`) |
| `permissions` | `Object` | No | `{"camera":"granted","location":"precise","notifications":"granted","photos_media":"granted"}` |
| `permissions_status` | `String` | No | Summary text (e.g. `Camera + Location granted`) |
| `notification_status` | `String` | No | `Enabled` or `Disabled` |
| `latitude` | `double` | No | GPS Latitude (when location permission is granted) |
| `longitude` | `double` | No | GPS Longitude (when location permission is granted) |
| `city` | `String` | No | City (optional) |
| `state` | `String` | No | State (optional) |
| `country` | `String` | No | Country (optional) |
| `country_code` | `String` | No | ISO country code (e.g. `IN`, `US`) |

---

### 3. Sample JSON Request Body

```json
{
  "installation_id": "INS-8F29A1B4",
  "firebase_installation_id": "c7F9aBcDeFgHiJkLmNoP",
  "hardware_id": "9774d56d682e549c",
  "fcm_token": "dK3_yZs8exampleFCMTokenString...",
  "device_manufacturer": "Samsung",
  "device_brand": "Samsung",
  "device_model": "Galaxy S24 Ultra",
  "device_code": "SM-S921B",
  "cpu_architecture": "arm64-v8a",
  "platform": "Android",
  "os_version": "Android 15",
  "sdk_version": 35,
  "app_version": "1.0.0",
  "app_build_number": 42,
  "screen_resolution": "1080x2340",
  "language": "English",
  "timezone": "Asia/Kolkata",
  "permissions": {
    "camera": "granted",
    "location": "precise",
    "notifications": "granted",
    "photos_media": "granted"
  },
  "permissions_status": "Camera + Location granted",
  "notification_status": "Enabled",
  "latitude": 8.7139126,
  "longitude": 77.7567123,
  "city": "Tirunelveli",
  "state": "Tamil Nadu",
  "country": "India",
  "country_code": "IN"
}
```

---

### 4. API Response

#### Success (`200 OK` / `201 Created`)
```json
{
  "success": true,
  "message": "Device registered successfully.",
  "data": {
    "id": 1,
    "installation_id": "INS-8F29A1B4",
    "is_active": true,
    "platform": "Android",
    "app_version": "1.0.0",
    "total_sessions_count": 1,
    "last_active_at": "2026-08-24T05:30:00.000000Z"
  }
}
```

#### Validation Error (`422 Unprocessable Content`)
```json
{
  "success": false,
  "message": "Validation error.",
  "errors": {
    "installation_id": [
      "The installation id field is required."
    ]
  }
}
```
