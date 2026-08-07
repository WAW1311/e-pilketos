/*
 Basic ESP8266 MQTT example
 This sketch demonstrates the capabilities of the pubsub library in combination
 with the ESP8266 board/library.
 It connects to an MQTT server then:
  - publishes "hello world" to the topic "outTopic" every two seconds
  - subscribes to the topic "inTopic", printing out any messages
    it receives. NB - it assumes the received payloads are strings not binary
  - If the first character of the topic "inTopic" is an 1, switch ON the ESP Led,
    else switch it off
 It will reconnect to the server if the connection is lost using a blocking
 reconnect function. See the 'mqtt_reconnect_nonblocking' example for how to
 achieve the same result without blocking the main loop.
 To install the ESP8266 board, (using Arduino 1.6.4+):
  - Add the following 3rd party board manager under "File -> Preferences -> Additional Boards Manager URLs":
       http://arduino.esp8266.com/stable/package_esp8266com_index.json
  - Open the "Tools -> Board -> Board Manager" and click install for the ESP8266"
  - Select your ESP8266 in "Tools -> Board"
*/

#include <WiFi.h>
#include <PubSubClient.h>

const char* ssid = "lorem";
const char* password = "12345678";
const char* mqtt_server = "broker.hivemq.com";
// const char* mqtt_server = "test.mosquitto.org";

WiFiClient espClient;
PubSubClient client(espClient);

#define BUTTON_PIN 0
#define LED_PIN 2

void setup_wifi() {
  delay(10);
  Serial.println("Connecting to WiFi...");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("WiFi connected");
  digitalWrite(LED_PIN, HIGH);
  delay(500);
  digitalWrite(LED_PIN, LOW);
}

void reconnect() {
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    if (client.connect("esp32_client_5220411238")) {
      Serial.println("connected");
      digitalWrite(LED_PIN, HIGH);
      delay(5000);
      digitalWrite(LED_PIN, LOW);
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      delay(2000);
    }
  }
}

void setup() {
  Serial.begin(115200);
  pinMode(BUTTON_PIN, INPUT_PULLUP);
  pinMode(LED_PIN, OUTPUT);
  setup_wifi();
  client.setServer(mqtt_server, 1883);
}

void loop() {
  if (!client.connected()) {
    reconnect();
  }
  client.loop();

  if (digitalRead(BUTTON_PIN) == LOW) {  // Tombol ditekan
    client.publish("flutter/iot/data", "verified");
    Serial.println("Button pressed - verified sent");
    digitalWrite(LED_PIN, HIGH);
    delay(500);  // Debounce
  } else {
    digitalWrite(LED_PIN, LOW);
  }
}
