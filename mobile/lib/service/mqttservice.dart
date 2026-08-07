import 'package:mqtt_client/mqtt_client.dart';
import 'package:mqtt_client/mqtt_server_client.dart';

class MqttService {
  late MqttServerClient client;
  final String broker = 'broker.hivemq.com';
  // final String broker = 'test.mosquitto.org';
  final String topic = 'flutter/iot/data';

  final _messages = <String>[];
  bool isverified = false;
  Function(String)? onMessageReceived;

  Future<void> connect() async {
    client = MqttServerClient(broker, 'flutter_client_${DateTime.now().millisecondsSinceEpoch}');
    client.port = 1883;
    client.logging(on: false);
    client.keepAlivePeriod = 20;

    client.onConnected = onConnected;
    client.onDisconnected = onDisconnected;
    client.onSubscribed = (String topic) => print('Subscribed to $topic');
    client.onSubscribeFail = (String topic) => print('Failed to subscribe $topic');
    client.pongCallback = () => print('Ping response received');

    final connMessage = MqttConnectMessage()
        .withClientIdentifier('flutter_client')
        .keepAliveFor(30)
        .withWillTopic('willtopic')
        .withWillMessage('Client disconnected unexpectedly')
        .startClean();

    client.connectionMessage = connMessage;

    try {
      await client.connect();
    } catch (e) {
      print('MQTT connection failed: $e');
      client.disconnect();
      return;
    }

    if (client.connectionStatus!.state == MqttConnectionState.connected) {
      print('MQTT connected!');
      client.subscribe(topic, MqttQos.atMostOnce);

      client.updates!.listen((List<MqttReceivedMessage<MqttMessage?>>? c) {
        final payload = c![0].payload as MqttPublishMessage;
        final message = MqttPublishPayload.bytesToStringAsString(payload.payload.message);
        print('New message on <${c[0].topic}>: $message');

        _messages.add(message);
        isverified = true;
        if (onMessageReceived != null) {
          onMessageReceived!(message);
        }
      });
    } else {
      print('❗ MQTT connection failed - status: ${client.connectionStatus}');
    }
  }

  void publish(String message) {
    if (client.connectionStatus?.state == MqttConnectionState.connected) {
      final builder = MqttClientPayloadBuilder();
      builder.addString(message);
      client.publishMessage(topic, MqttQos.atMostOnce, builder.payload!);
      print('Published message: $message');
    } else {
      print('Can’t publish, MQTT not connected.');
    }
  }

  void disconnect() {
    client.disconnect();
    print('🔌 Disconnected from MQTT');
  }

  void onConnected() => print('Connected to broker');
  void onDisconnected() {
    print('Disconnected from broker');
    print('Reconnecting...');
    if (isverified == false) {
      connect();
    }
  }
}
