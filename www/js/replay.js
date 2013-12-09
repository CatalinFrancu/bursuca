var CANVAS_SHIFT = -1000;

var numPlayers;
var numMoves;
var moveNumber = 0;

function replayInit() {
  numPlayers = $('.playerRow').length;
  numMoves = $('ul#moves li').length;
  $('#controlNext').click(replayForward);
  $('#controlLast').click(replayAllForward);

  paintProgressBars();
}

function replayAllForward() {
  while (moveNumber < numMoves) {
    replayForward();
  }
  return false;
}

function replayForward() {
  if (moveNumber == numMoves) {
    return false;
  }
  var move = $('#move_' + moveNumber);
  var player = moveNumber % numPlayers;
  var oldPlayer = (player + numPlayers - 1) % numPlayers;
  var action = move.data('action');
  var arg = move.data('arg');
  var company = move.data('company');
  var time = move.data('time');

  // set the die images and row background
  $('#die1').attr('class', 'die die' + arg);
  $('#die2').attr('class', 'die die' + company);
  $('.playerRow').eq(oldPlayer).removeClass('activePlayer');
  $('.playerRow').eq(player).addClass('activePlayer');

  // update the div text
  $('#mUser').text($('#username_' + player).text());
  $('#mAgent').text($('#agentName_' + player).text());
  var message;
  switch (action) {
  case 'B': message = 'cumpără ' + arg + ' acțiuni la compania ' + company; break;
  case 'S': message = 'vinde ' + arg + ' acțiuni la compania ' + company; break;
  case 'R': message = 'ridică prețul cu $' + arg + ' la compania ' + company; break;
  case 'L': message = 'scade prețul cu $' + arg + ' la compania ' + company; break;
  case 'P': message = 'zice pas'; break;
  }
  $('#mAction').text(message);
  $('#mTime').text(time);
  $('#moveText').show();

  // update the player's total time
  var totalTime = time + parseInt($('#time_' + player).text());
  $('#time_' + player).text(totalTime);

  makeMove(player, action, arg, company);

  // update the move counter
  var roundNumber = 1 + ~~(moveNumber / numPlayers);
  var roundPlayer = 1 + moveNumber % numPlayers;
  $('#moveCounter').text('Tura ' + roundNumber + ', mutarea ' + roundPlayer);

  moveNumber++;
  return false;
}

function makeMove(player, action, arg, company) {
  $('#gameBoard td').removeClass('recent');
  $('#gameBoard th').removeClass('recent');

  var stockPrice = $('#stockPrice_' + company);
  var stock = $('#stock_' + player + '_' + company);
  var cash = $('#cash_' + player);

  switch (action) {
  case 'B':
    stock.text(+stock.text() + arg);
    cash.text(+cash.text() - arg * stockPrice.text());
    stockPrice.text(+stockPrice.text() + ~~(arg / 3));
    stock.addClass('recent');
    break;

  case 'S':
    stock.text(+stock.text() - arg);
    cash.text(+cash.text() + arg * stockPrice.text());
    stockPrice.text(Math.max(+stockPrice.text() - ~~(arg / 3), 1));
    stock.addClass('recent');
    break;

  case 'R':
    stockPrice.text(+stockPrice.text() + arg);
    stockPrice.addClass('recent');
    break;

  case 'L':
    stockPrice.text(+stockPrice.text() - arg);
    stockPrice.addClass('recent');
    break;

  }
  updateTotals();
}

function updateTotals() {
  for (p = 0; p < numPlayers; p++) {
    var total = $('#cash_' + p).text();
    for (c = 1; c <= 6; c++) {
      total = +total + $('#stockPrice_' + c).text() * $('#stock_' + p + '_' + c).text();
    }
    $('#total_' + p).text(total);
  }
  paintProgressBars();
}

function paintProgressBars() {
  var labels = ['#cash_', '#total_'];
  for (i = 0; i < labels.length; i++) {
    for (p = 0; p < numPlayers; p++) {
      var obj = $(labels[i] + p);
      var value = Math.min(100, obj.text());
      var pixelValue = Math.floor(obj.width() * value / 100);
      var shift = CANVAS_SHIFT + pixelValue;
      obj.css('background-position', shift + 'px');
    }
  }
}
