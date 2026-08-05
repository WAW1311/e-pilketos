#include <WiFi.h>
#include <HTTPClient.h>
#include <Adafruit_Fingerprint.h>
#include "base64.h"
// ================= WIFI =================
const char* ssid = "Pora Puo pok";
const char* password = "wahyu1311";
// const char* ssid = "Insomnia";
// const char* password = "Rangerti";
// ================= API ==================
const char* apiUrl = "http://172.20.10.4:8000/api/fingerprint/store";
// const char* apiUrl = "http://192.168.101.77:8000/api/fingerprint/store";
// ================= FINGERPRINT =========
#define TXD2 2
#define RXD2 4
HardwareSerial mySerial(2);
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial);
// ================= GLOBAL ===============
uint8_t fingerTemplate[512];
// ================= WIFI CONNECT =========
void connectWiFi() {
  delay(10);
  Serial.println("Connecting to WiFi...");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("WiFi connected");
}
// ================= ENROLL ID 1 ==========
bool enrollFinger(uint8_t id) {
  int p = -1;

  Serial.println("Place finger...");
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
  }

  finger.image2Tz(1);
  Serial.println("Remove finger");
  delay(2000);

  while (finger.getImage() != FINGERPRINT_NOFINGER);

  Serial.println("Place same finger again");
  while (finger.getImage() != FINGERPRINT_OK);

  finger.image2Tz(2);

  if (finger.createModel() != FINGERPRINT_OK) return false;
  if (finger.storeModel(id) != FINGERPRINT_OK) return false;

  Serial.println("Fingerprint stored");
  return true;
}
// ================= EXPORT TEMPLATE ======
bool exportTemplate(uint16_t id) {
  if (finger.loadModel(id) != FINGERPRINT_OK) return false;
  if (finger.getModel() != FINGERPRINT_OK) return false;

  uint8_t raw[534];
  memset(raw, 0, 534);

  int i = 0;
  uint32_t start = millis();
  while (i < 534 && millis() - start < 20000) {
    if (mySerial.available()) raw[i++] = mySerial.read();
  }
  int u = 9;
  memcpy(fingerTemplate, raw + u, 256);
  u += 256 + 2 + 9;
  memcpy(fingerTemplate + 256, raw + u, 256);

  Serial.println("Template exported");
  return true;
}
// ================= UPLOAD API ===========
bool uploadTemplate(uint8_t id) {
  String b64 = base64::encode(fingerTemplate, 512);

  String payload =
    "{"
    "\"device_id\":\"esp32-001\","
    "\"fingerprint_id\":" + String(id) + ","
    "\"template_base64\":\"" + b64 + "\""
    "}";

  HTTPClient http;
  http.begin(apiUrl);
  http.addHeader("Content-Type", "application/json");

  int code = http.POST(payload);

  Serial.print("HTTP Status Code : ");
  Serial.println(code);

  // ambil response body
  String response = http.getString();
  Serial.println("Response Body:");
  Serial.println(response);

  http.end();

  return (code == 200 || code == 201);
}

void restartDevice(int delayMs = 3000) {
  Serial.println("Restarting device...");
  delay(delayMs);
  ESP.restart();
}

// ================= SETUP ================
void setup() {
  Serial.begin(115200);
  delay(1000);
  connectWiFi();
  mySerial.begin(57600, SERIAL_8N1, RXD2, TXD2);
  finger.begin(57600);
  if (!finger.verifyPassword()) {
    Serial.println("Fingerprint sensor not found");
    restartDevice();
  }
  Serial.println("Found fingerprint sensor!");
  if (!enrollFinger(1)) {
    Serial.println("Enroll failed");
    restartDevice();
  }
  if (!exportTemplate(1)) {
    Serial.println("Export failed");
    restartDevice();
  }
  if (uploadTemplate(1)) {
    Serial.println("Upload success");
    finger.emptyDatabase();
    Serial.println("empty database");
    restartDevice();
  } else {
    Serial.println("Upload failed");
    restartDevice();
  }
}

void loop() {}