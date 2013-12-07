var moveNumber = 0;
var numPlayers;

function replayInit() {
  numPlayers = $('.playerRow').length;
  $('#controlNext').click(replayForward);
}

function replayForward() {
  var move = $('#move_' + moveNumber);
  var player = moveNumber % numPlayers;
  var arg = move.data('arg');
  var company = move.data('company');

  // set the die images
  $('#die1').attr('class', 'die die' + arg);
  $('#die2').attr('class', 'die die' + company);

  // update the div text
  var playerName = $('#username_' + player).text() + ' ' + $('#agentName_' + player).text();
  $('#moveText').text(playerName + ' la compania ' + company);
  moveNumber++;
  return false;
}
