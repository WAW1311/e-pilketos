import 'dart:convert';
import 'package:http/http.dart' as http;
// import 'package:flutter_dotenv/flutter_dotenv.dart';

class VotePaperService {
  var url = "https://epilketos.wawtech.id";
  // var url = dotenv.env['API_URL'];

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'Authorization': 'Bearer G0m0ak4s2REEIK60wNPhyw71PpIDQAU8UPpNVTbC23ce8326',
        // 'Authorization': 'Bearer ${dotenv.env['API_TOKEN'] ?? ''}',
      };

  Future<Map> getVotePapers(voteId) async {
    var response = await http.post(
      Uri.parse('$url/api/votepapper?vote_id=$voteId'),
      headers: _headers,
    );
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
    return {
      'status': response.statusCode == 200,
      'decision': body['decision'],
      'message': body['message'],
      // JWT jangka pendek untuk autentikasi /voting (hanya ada saat matched).
      'token': body['token'],
    };
  }

  Future<bool> submitVote(voteId, paslonId, votingToken) async {
    var response = await http.post(
      Uri.parse('$url/api/voting?paslon_id=$paslonId'),
      headers: {
        'Accept': 'application/json',
        // Autentikasi memakai JWT hasil verifikasi NIS, bukan API_TOKEN.
        'Authorization': 'Bearer ${votingToken ?? ''}',
      },
    );
    if (response.statusCode == 200) {
      return true;
    } else {
      return false;
    }
  }
}
