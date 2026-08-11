import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_dotenv/flutter_dotenv.dart';

class VotePaperService {
  var url = dotenv.env['API_URL'];

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'Authorization': 'Bearer ${dotenv.env['API_TOKEN'] ?? ''}',
      };

  Future<Map> getVotePapers(voteId) async {
    var response = await http.post(
      Uri.parse('$url/api/votepapper?vote_id=$voteId'),
      headers: _headers,
    );
    print(json.decode(response.body));
    if (response.statusCode != 200) {
      return {
        'status': false,
        'message': 'Failed to load vote papers',
      };
    }
    var data = json.decode(response.body);
    return {
      'status': true,
      'data': data,
    };
  }

  Future<Map> verifyNis(voteId, nis) async {
    var response = await http.post(
      Uri.parse('$url/api/verify?vote_id=$voteId&nis=$nis'),
      headers: _headers,
    );
    var body = json.decode(response.body);
    print(body);
    return {
      'status': response.statusCode == 200,
      'decision': body['decision'],
      'message': body['message'],
    };
  }

  Future<bool> submitVote(voteId, paslonId, nis) async {
    var response = await http.post(
      Uri.parse('$url/api/voting?vote_id=$voteId&paslon_id=$paslonId&nis=$nis'),
      headers: _headers,
    );
    print(json.decode(response.body));
    if (response.statusCode == 200) {
      return true;
    } else {
      return false;
    }
  }
}
