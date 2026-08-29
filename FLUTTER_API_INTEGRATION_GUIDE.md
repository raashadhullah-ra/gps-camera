# GeoCam — Flutter Device Registration & GPS Telemetry Integration Guide

### 1. API Endpoint

- **Method:** `POST`
- **Path:** `/api/v1/devices/register` (also aliased to `/api/v1/devices/sync`)
- **Headers:**
  ```http
  Content-Type: application/json
  Accept: application/json
  ```

#### Base URLs:
- **Same Wi-Fi Testing (Physical Device):** `http://192.168.1.X:8000/api/v1/devices/register`
- **Android Emulator:** `http://10.0.2.2:8000/api/v1/devices/register`
- **iOS Simulator:** `http://127.0.0.1:8000/api/v1/devices/register`
- **Production Server:** `https://yourdomain.com/api/v1/devices/register`

> **Note:** Call this API on app launch (first install and subsequent app opens) AFTER requesting GPS / notification permissions.

---

### 2. Why Were Location Fields (City, State, Country, Lat, Lng) Storing as `null`?

1. **Flutter must read the GPS Sensor:** The backend server cannot access the physical mobile phone's GPS chip directly. Only the Flutter app can read `latitude` & `longitude` using the device's GPS hardware (via `geolocator` package).
2. **Granting permission is not enough:** Even if permission is granted (`"location": "precise"`), the Flutter app must explicitly execute `await Geolocator.getCurrentPosition()` to get the coordinates and include `"latitude"` and `"longitude"` in the POST request body.
3. **Backend Auto-Reverse Geocoding:** When Flutter passes `latitude` and `longitude`, the Laravel backend will automatically reverse-geocode them into `city`, `state`, `country`, and `country_code` via OpenStreetMap Nominatim.

---

### 3. Flutter Implementation Code (`device_registration_service.dart`)

```dart
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:device_info_plus/device_info_plus.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:firebase_app_installations/firebase_app_installations.dart';
import 'package:geolocator/geolocator.dart';
import 'package:geocoding/geocoding.dart';
import 'package:package_info_plus/package_info_plus.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:uuid/uuid.dart';

class DeviceRegistrationService {
  static const String _apiBaseUrl = 'http://192.168.1.41:8000'; // Replace with your IP or domain

  /// Registers or syncs device information with GPS coordinates on app open
  static Future<void> registerDevice() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      
      // 1. Get or generate persistent installation ID
      String? installationId = prefs.getString('app_installation_id');
      if (installationId == null) {
        installationId = 'INS-${const Uuid().v4().substring(0, 8).toUpperCase()}';
        await prefs.setString('app_installation_id', installationId);
      }

      // 2. Fetch Firebase FID and FCM Token
      String? firebaseInstallationId;
      try {
        firebaseInstallationId = await FirebaseInstallations.instance.getId();
      } catch (_) {}

      String? fcmToken;
      try {
        fcmToken = await FirebaseMessaging.instance.getToken();
      } catch (_) {}

      // 3. Package & App Info
      final packageInfo = await PackageInfo.fromPlatform();
      final appVersion = packageInfo.version;
      final buildNumber = int.tryParse(packageInfo.buildNumber) ?? 1;

      // 4. Device Hardware Info
      final deviceInfo = DeviceInfoPlugin();
      String manufacturer = '';
      String brand = '';
      String model = '';
      String code = '';
      String osVersion = '';
      int sdkVersion = 33;
      String cpuArch = 'arm64-v8a';

      if (Platform.isAndroid) {
        final androidInfo = await deviceInfo.androidInfo;
        manufacturer = androidInfo.manufacturer;
        brand = androidInfo.brand;
        model = androidInfo.model;
        code = androidInfo.device;
        osVersion = 'Android ${androidInfo.version.release}';
        sdkVersion = androidInfo.version.sdkInt;
        cpuArch = androidInfo.supportedAbis.isNotEmpty ? androidInfo.supportedAbis.first : 'arm64-v8a';
      } else if (Platform.isIOS) {
        final iosInfo = await deviceInfo.iosInfo;
        manufacturer = 'Apple';
        brand = 'Apple';
        model = iosInfo.utsname.machine;
        code = iosInfo.model;
        osVersion = 'iOS ${iosInfo.systemVersion}';
        sdkVersion = 17;
        cpuArch = 'arm64';
      }

      // 5. GPS Location & Reverse Geocoding
      double? latitude;
      double? longitude;
      String? city;
      String? state;
      String? country;
      String? countryCode;

      try {
        LocationPermission permission = await Geolocator.checkPermission();
        if (permission == LocationPermission.denied) {
          permission = await Geolocator.requestPermission();
        }

        if (permission == LocationPermission.whileInUse || permission == LocationPermission.always) {
          final position = await Geolocator.getCurrentPosition(
            desiredAccuracy: LocationAccuracy.high,
            timeLimit: const Duration(seconds: 5),
          );
          latitude = position.latitude;
          longitude = position.longitude;

          // Optional: Geocode on device or let Laravel backend auto reverse-geocode
          try {
            final placemarks = await placemarkFromCoordinates(position.latitude, position.longitude);
            if (placemarks.isNotEmpty) {
              final place = placemarks.first;
              city = place.locality ?? place.subAdministrativeArea;
              state = place.administrativeArea;
              country = place.country;
              countryCode = place.isoCountryCode;
            }
          } catch (_) {}
        }
      } catch (e) {
        print('GPS location fetch error: $e');
      }

      // 6. Build Request Payload
      final payload = {
        'installation_id': installationId,
        'firebase_installation_id': firebaseInstallationId,
        'fcm_token': fcmToken,
        'device_manufacturer': manufacturer,
        'device_brand': brand,
        'device_model': model,
        'device_code': code,
        'cpu_architecture': cpuArch,
        'platform': Platform.isAndroid ? 'Android' : 'iOS',
        'os_version': osVersion,
        'sdk_version': sdkVersion,
        'app_version': appVersion,
        'app_build_number': buildNumber,
        'screen_resolution': '${(WidgetsBinding.instance.window.physicalSize.width).toInt()}x${(WidgetsBinding.instance.window.physicalSize.height).toInt()}',
        'language': Platform.localeName,
        'timezone': DateTime.now().timeZoneName,
        'permissions': {
          'camera': 'granted',
          'location': latitude != null ? 'precise' : 'denied',
          'notifications': fcmToken != null ? 'granted' : 'denied',
          'photos_media': 'granted',
        },
        'permissions_status': latitude != null ? 'Camera + Location + Notifications granted' : 'Camera + Notifications granted',
        'notification_status': fcmToken != null ? 'Enabled' : 'Disabled',
        'latitude': latitude,
        'longitude': longitude,
        'city': city,
        'state': state,
        'country': country,
        'country_code': countryCode,
      };

      // 7. Send POST request to Laravel
      final response = await http.post(
        Uri.parse('$_apiBaseUrl/api/v1/devices/register'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(payload),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        print('Device successfully registered with Laravel!');
      } else {
        print('Failed to register device: ${response.body}');
      }
    } catch (e) {
      print('Device registration error: $e');
    }
  }
}
```

---

### 4. Sample JSON Request Body (with Real Location e.g. Chennai)

```json
{
  "installation_id": "INS-8E90B8C1",
  "firebase_installation_id": "dQLPi5NtRJOuqU3Gbp4byV",
  "hardware_id": "TP1A.220624.014",
  "fcm_token": "dQLPi5NtRJOuqU3Gbp4byV:APA91bFE7Q2PCX9Jkv90BaQELn97...",
  "device_manufacturer": "Samsung",
  "device_brand": "Samsung",
  "device_model": "SM-A715F",
  "device_code": "a71",
  "cpu_architecture": "arm64-v8a",
  "platform": "Android",
  "os_version": "Android 13",
  "sdk_version": 33,
  "app_version": "0.1.0",
  "app_build_number": 2001,
  "screen_resolution": "1080x2400",
  "language": "en_GB",
  "timezone": "IST",
  "permissions": {
    "camera": "granted",
    "location": "precise",
    "notifications": "granted",
    "photos_media": "granted"
  },
  "permissions_status": "Camera + Location + Photos + Notifications granted",
  "notification_status": "Enabled",
  "latitude": 13.082680,
  "longitude": 80.270718,
  "city": "Chennai",
  "state": "Tamil Nadu",
  "country": "India",
  "country_code": "IN"
}
```

---

### 5. API Response (`200 OK` / `201 Created`)

```json
{
  "success": true,
  "message": "Device session synced successfully.",
  "data": {
    "id": 26,
    "installation_id": "INS-8E90B8C1",
    "is_active": true,
    "platform": "Android",
    "app_version": "0.1.0",
    "city": "Chennai",
    "state": "Tamil Nadu",
    "country": "India",
    "latitude": "13.08268000",
    "longitude": "80.27071800",
    "total_sessions_count": 2,
    "last_active_at": "2026-08-26T10:45:00.000000Z"
  }
}
```
