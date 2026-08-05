import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_dotenv/flutter_dotenv.dart';

class VotePaperService {
  var url = dotenv.env['API_URL'];

  Future<Map> getVotePapers(voteId) async {
    var response = await http.post(
      Uri.parse('$url/api/votepapper?vote_id=$voteId'),
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

  Future<bool> submitVote(voteId,paslonId,idFp) async {
    var response = await http.post(
      Uri.parse('$url/api/voting?vote_id=$voteId&paslon_id=$paslonId&id_fp=$idFp'),
    );
    print(json.decode(response.body));
    if (response.statusCode == 200) {
      return true;
    } else {
      return false;
    }
  }
}
