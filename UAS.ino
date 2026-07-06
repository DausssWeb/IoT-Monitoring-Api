#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <DHT.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <Servo.h>

// ================= PIN =================
#define DHTPIN      D3        // GPIO0
#define DHTTYPE     DHT22
#define FLAME_PIN   D5        // GPIO14
#define BUZZER_PIN  D6        // GPIO12
#define SERVO_PIN   D7        // GPIO13

// ================= WIFI =================
const char* ssid     = "faisal";
const char* password = "faisal123";

// ================= LARAVEL =================
const char* serverUrl = "http://10.153.189.156/fire-monitoring/public/api/sensor";
// ganti IP sesuai laptop yang menjalankan Laravel

// ================= OBJECT =================
DHT dht(DHTPIN, DHTTYPE);
LiquidCrystal_I2C lcd(0x27, 16, 2);
Servo gateServo;

// ================= VARIABLE =================
float temperature = 0.0;
float humidity = 0.0;
bool flameDetected = false;
String systemStatus = "INIT";

// ================= BUZZER CONTROL =================
// Jika buzzer ACTIVE LOW, tukar HIGH <-> LOW
void buzzerOn() {
  digitalWrite(BUZZER_PIN, HIGH);
}

void buzzerOff() {
  digitalWrite(BUZZER_PIN, LOW);
}

// ================= SETUP =================
void setup() {
  Serial.begin(115200);

  pinMode(FLAME_PIN, INPUT);
  pinMode(BUZZER_PIN, OUTPUT);
  buzzerOff();

  gateServo.attach(SERVO_PIN);
  gateServo.write(0); // servo tertutup

  Wire.begin(D2, D1);
  lcd.init();
  lcd.backlight();

  dht.begin();

  // ===== WIFI CONNECT (SAMA DENGAN RFID) =====
  WiFi.begin(ssid, password);
  Serial.print("Connecting WiFi");

  lcd.setCursor(0,0);
  lcd.print("Connecting WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi Connected!");
  lcd.clear();
  lcd.print("WiFi Connected");
  delay(1500);
  lcd.clear();
}

// ================= LOOP =================
void loop() {

  // ---- BACA SENSOR ----
  temperature   = dht.readTemperature();
  humidity      = dht.readHumidity();
  flameDetected = (digitalRead(FLAME_PIN) == LOW);

  // ---- VALIDASI DHT ----
  if (isnan(temperature) || isnan(humidity)) {
    systemStatus = "FAULT";

    gateServo.write(0);
    buzzerOff();

    lcd.setCursor(0, 0);
    lcd.print("DHT SENSOR ERR ");
    lcd.setCursor(0, 1);
    lcd.print("CHECK WIRING   ");

    Serial.println("ERROR: DHT22 not responding");
    delay(2000);
    return;
  }

  // ---- LOGIKA STATUS (SAFETY LOCAL) ----
  if (flameDetected || temperature >= 31.0) {
    systemStatus = "CRITICAL";
    gateServo.write(90);
    buzzerOn();
  }
  else if (temperature >= 30.0) {
    systemStatus = "WARNING";
    gateServo.write(0);
    buzzerOff();
  }
  else {
    systemStatus = "NORMAL";
    gateServo.write(0);
    buzzerOff();
  }

  // ---- LCD ----
  lcd.setCursor(0, 0);
  lcd.print("T:");
  lcd.print(temperature, 1);
  lcd.print("C ");

  if (flameDetected) lcd.print("FLAME ");
  else lcd.print("SAFE  ");

  lcd.setCursor(0, 1);
  lcd.print("STATUS:");
  lcd.print(systemStatus);
  lcd.print("   ");

  // ---- SERIAL ----
  Serial.print("Temp: ");
  Serial.print(temperature);
  Serial.print(" | Hum: ");
  Serial.print(humidity);
  Serial.print(" | Flame: ");
  Serial.print(flameDetected);
  Serial.print(" | Status: ");
  Serial.println(systemStatus);

  // ---- KIRIM KE LARAVEL (SAMA POLA DENGAN RFID) ----
  if (WiFi.status() == WL_CONNECTED) {

    HTTPClient http;
    WiFiClient client;

    http.begin(client, serverUrl);
    http.addHeader("Content-Type", "application/json");

    String json = "{";
    json += "\"temperature\":" + String(temperature) + ",";
    json += "\"humidity\":" + String(humidity) + ",";
    json += "\"flame\":" + String(flameDetected) + ",";
    json += "\"status\":\"" + systemStatus + "\"";
    json += "}";

    int httpResponseCode = http.POST(json);

    Serial.print("HTTP Response: ");
    Serial.println(httpResponseCode);

    http.end();
  }

  delay(1000);
}
