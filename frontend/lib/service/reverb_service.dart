// import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:simple_flutter_reverb/simple_flutter_reverb.dart';
import 'package:simple_flutter_reverb/simple_flutter_reverb_options.dart';

class ReverbClientService {

  Future<SimpleFlutterReverb> connectReverb() async {
    final options = SimpleFlutterReverbOptions(
      scheme: "ws",
      host: "epilketos.wawtech.id",
      port: "443",
      appKey: "um7itnniuifmsw7afzso",
      authUrl: "",
      authToken: "",
      privatePrefix: "private-",
      usePrefix: false,
      // scheme: dotenv.env['REVERB_SCHEME']!,
      // host: dotenv.env['DOMAIN']!,
      // port: dotenv.env['REVERB_PORT']!,
      // appKey: dotenv.env['REVERB_APP_KEY']!,
      // authUrl: "",
      // authToken: "",
      // privatePrefix: "private-",
      // usePrefix: dotenv.env['REVERB_IS_PRIVATE'] == 'true',
    );
    return SimpleFlutterReverb(options: options);
  }
}
