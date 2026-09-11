# GPS Camera — App Version API (Flutter Guide)

## 1. API Endpoint

- **URL**: `https://gps-camera.onrender.com/api/v1/app-version`
- **Method**: `GET`
- **Headers**: `Accept: application/json`

### Query Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `version` | string | **Yes** | Currently installed app version (e.g. `1.2.0`) |
| `platform` | string | No | Device OS: `android` or `ios` (default: `android`) |

---

## 2. Example Request

```http
GET https://gps-camera.onrender.com/api/v1/app-version?version=1.2.0&platform=android
```

---

## 3. Example Response

```json
{
    "status": true,
    "message": "App version configuration retrieved successfully.",
    "data": {
        "client_version": "1.2.0",
        "current_version": "1.5.0",
        "minimum_version": "1.4.0",
        "force_update": true,
        "is_update_available": true,
        "update_title": "New Update Available",
        "update_message": "A new version of GPS Camera is available. Please update the app to continue.",
        "store_url": "https://play.google.com/store/apps/details?id=com.geocam.app",
        "play_store_url": "https://play.google.com/store/apps/details?id=com.geocam.app",
        "app_store_url": "https://apps.apple.com/app/gps-camera"
    }
}
```

---

## 4. How Flutter Should Handle the Response

| Condition | Action in Flutter |
| :--- | :--- |
| `data.force_update == true` | **Force Update**: Show a non-dismissible popup (back button blocked). Clicking **Update Now** opens `data.store_url`. |
| `data.is_update_available == true` (and `force_update == false`) | **Optional Update**: Show a popup with **"Update"** and **"Maybe Later"** buttons. |
| `data.is_update_available == false` | **Up to Date**: Do nothing, let user continue. |
