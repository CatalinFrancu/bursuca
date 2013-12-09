var CANVAS_SHIFT = -1000;

var numPlayers;
var numMoves;
var moveNumber = 0;

function replayInit() {
  numPlayers = $('.playerRow').length;
  numMoves = $('ul#moves li').length;
  $('#controlFirst').click(replayAllBack);
  $('#controlPrev').click(replayBack);
  $('#controlNext').click(replayForward);
  $('#controlLast').click(replayAllForward);

  drawProgressBars();
}

function replayAllBack() {
  while (moveNumber > 1) {
    replayBack();
  }
  return false;
}

function replayAllForward() {
  while (moveNumber < numMoves) {
    replayForward();
  }
  return false;
}

function replayBack() {
  if (moveNumber <= 1) {
    return false;
  }

  showMove(moveNumber - 2);
  addTime(moveNumber - 1, -1);
  makeMove(moveNumber - 1, -1);
  updateTotals();
  moveNumber--;
  return false;
}

function replayForward() {
  if (moveNumber == numMoves) {
    return false;
  }

  showMove(moveNumber);
  addTime(moveNumber, +1);
  makeMove(moveNumber, 1);
  updateTotals();
  moveNumber++;
  return false;
}

/* Redraws all the information pertaining to a move: dice, table row highlight, move text etc. */
function showMove(x) {
  var move = $('#move_' + x);
  var player = x % numPlayers;
  var action = move.data('action');
  var arg = move.data('arg');
  var company = move.data('company');
  var time = move.data('time');

  // set the die images
  $('#die1').attr('class', 'die die' + arg);
  $('#die2').attr('class', 'die die' + company);

  // highlight the current player
  $('.playerRow').removeClass('activePlayer');
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

  // highlight the recently modified cells
  $('#gameBoard td').removeClass('recent');
  $('#gameBoard th').removeClass('recent');  
  var stockPrice = $('#stockPrice_' + company);
  var stock = $('#stock_' + player + '_' + company);
  if ((action == 'B') || (action == 'S')) {
    stock.addClass('recent');
  } else if ((action == 'R') || (action == 'L')) {
    stockPrice.addClass('recent');
  }

  // update the turn and move counters
  var roundNumber = 1 + ~~(x / numPlayers);
  var roundPlayer = 1 + x % numPlayers;
  $('#moveCounter').text('Tura ' + roundNumber + ', mutarea ' + roundPlayer);
}

/* Adds or subtracts the time for the given move */
function addTime(x, direction) {
  var move = $('#move_' + x);
  var player = x % numPlayers;
  var time = move.data('time');
  var elt = $('#time_' + player);

  var totalTime = parseInt(elt.text()) + direction * time;
  elt.text(totalTime);
}

/* Makes a move, updating stock prices and stock counts */
function makeMove(x, direction) {
  var move = $('#move_' + x);
  var player = x % numPlayers;
  var action = move.data('action');
  var arg = move.data('arg');
  var company = move.data('company');

  var stockPrice = $('#stockPrice_' + company);
  var stock = $('#stock_' + player + '_' + company);
  var cash = $('#cash_' + player);

  switch (action) {
  case 'B':
    var finalPrice = +stockPrice.text() + direction * ~~(arg / 3);
    var sharePrice = (direction == 1) ? +stockPrice.text() : finalPrice;
    stock.text(+stock.text() + arg * direction);
    cash.text(+cash.text() - arg * direction * sharePrice);
    stockPrice.text(finalPrice);
    break;

  case 'S':
    // TODO: this is buggy. If the price after selling 6 shares was $1, what was the price before the sale?
    // It could have been $1, $2 or $3.
    var finalPrice = Math.max(+stockPrice.text() - direction * ~~(arg / 3), 1);
    var sharePrice = (direction == 1) ? +stockPrice.text() : finalPrice;
    stock.text(+stock.text() - arg * direction);
    cash.text(+cash.text() + arg * direction * sharePrice);
    stockPrice.text(finalPrice);
    break;

  case 'R':
    stockPrice.text(+stockPrice.text() + arg * direction);
    break;

  case 'L':
    stockPrice.text(+stockPrice.text() - arg * direction);
    break;
  }
}

function updateTotals() {
  for (p = 0; p < numPlayers; p++) {
    var sum = +$('#cash_' + p).text();
    for (c = 1; c <= 6; c++) {
      sum += $('#stockPrice_' + c).text() * $('#stock_' + p + '_' + c).text();
    }
    $('#total_' + p).text(sum);
  }
  drawProgressBars();
}

function drawProgressBars() {
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
