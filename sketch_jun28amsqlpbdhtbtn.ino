#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <ArduinoJson.h>
#include <DHT.h>

const char* ssid = "jio_fiber";
const char* password = "12345645";

const char* serverUpdate = "http://192.168.29.136/GETPOST/update.php"; // Replace with your server URL
const char* serverGet = "http://192.168.29.136/GETPOST/getLedStatus.php";

const int btnPin1 = D5;
const int btnPin2 = D1;
const int btnPin3 = D2;
const int btnPin4 = D6;

const int ledPin1 = D7;
const int ledPin2 = D8;
const int ledPin3 = D3;
const int ledPin4 = D0;

#define DHTPIN D4 // DHT11 sensor connected to D4
#define DHTTYPE DHT11 // DHT 11

DHT dht(DHTPIN, DHTTYPE);

bool btnStatus1 = false;
bool btnStatus2 = false;
bool btnStatus3 = false;
bool btnStatus4 = false;

WiFiClient wifiClient;

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);

  // Wait for connection
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");

  pinMode(btnPin1, INPUT_PULLUP);
  pinMode(btnPin2, INPUT_PULLUP);
  pinMode(btnPin3, INPUT_PULLUP);
  pinMode(btnPin4, INPUT_PULLUP);

  pinMode(ledPin1, OUTPUT);
  pinMode(ledPin2, OUTPUT);
  pinMode(ledPin3, OUTPUT);
  pinMode(ledPin4, OUTPUT);

  dht.begin();
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    // Get LED and button status from server
    http.begin(wifiClient, serverGet);
    int httpResponseCode = http.GET();

    if (httpResponseCode > 0) {
      String payload = http.getString();
      Serial.println(payload);

      DynamicJsonDocument doc(1024);
      deserializeJson(doc, payload);

      int led1 = doc["led1"];
      int led2 = doc["led2"];
      int led3 = doc["led3"];
      int led4 = doc["led4"];

      digitalWrite(ledPin1, led1);
      digitalWrite(ledPin2, led2);
      digitalWrite(ledPin3, led3);
      digitalWrite(ledPin4, led4);
    } else {
      Serial.println("Error on HTTP request");
    }

    http.end();
  }

  // Check button states and update local status
  checkButtonState(btnPin1, btnStatus1, ledPin1, "led1");
  checkButtonState(btnPin2, btnStatus2, ledPin2, "led2");
  checkButtonState(btnPin3, btnStatus3, ledPin3, "led3");
  checkButtonState(btnPin4, btnStatus4, ledPin4, "led4");

  delay(500); // Check every 500 milliseconds

  // Read and send DHT11 sensor data
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();

  if (!isnan(humidity) && !isnan(temperature)) {
    sendDHTData(temperature, humidity);
  } else {
    Serial.println("Failed to read from DHT sensor!");
  }
}

void checkButtonState(int pin, bool &status, int ledPin, const char* ledName) {
  static unsigned long lastPressTime = 0;
  unsigned long currentTime = millis();
  
  if (digitalRead(pin) == LOW && (currentTime - lastPressTime > 200)) {
    status = !status;
    digitalWrite(ledPin, status);
    Serial.print(ledName);
    Serial.print(": ");
    Serial.println(status);
    sendButtonState(ledName, status);
    lastPressTime = currentTime;
    while (digitalRead(pin) == LOW); // Wait for button release
  }
}

void sendButtonState(const char* ledName, bool status) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(serverUpdate) + "?led=" + ledName + "&status=" + (status ? "1" : "0");
    http.begin(wifiClient, url);
    Serial.print("Requesting URL: ");
    Serial.println(url);

    int httpCode = http.GET();

    if (httpCode > 0) {
      String payload = http.getString();
      Serial.print("Response: ");
      Serial.println(payload);
    } else {
      Serial.print("Error on HTTP request: ");
      Serial.println(httpCode);
    }

    http.end();
  } else {
    Serial.println("WiFi not connected");
  }
}

void sendDHTData(float temperature, float humidity) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(serverUpdate) + "?temperature=" + String(temperature) + "&humidity=" + String(humidity);
    http.begin(wifiClient, url);
    Serial.print("Requesting URL: ");
    Serial.println(url);

    int httpCode = http.GET();

    if (httpCode > 0) {
      String payload = http.getString();
      Serial.print("Response: ");
      Serial.println(payload);
    } else {
      Serial.print("Error on HTTP request: ");
      Serial.println(httpCode);
    }

    http.end();
  } else {
    Serial.println("WiFi not connected");
  }
}
