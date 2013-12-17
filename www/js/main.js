var wwwRoot = getWwwRoot();

function getWwwRoot() {
  var pos = window.location.href.indexOf('/www/');
  if (pos == -1) {
    return '/';
  } else {
    return window.location.href.substr(0, pos + 5);
  }
}

function gameFilterInit() {
  $('#userFilter').select2({
    ajax: {
      data: function(term, page) { return { term: term }; },
      dataType: 'json',
      results: function(data, page) { return data; }, 
      url: wwwRoot + 'ajax/getUsers.php',
    },
    allowClear: true,
    initSelection: initSelectionUser,
    width: '400px',
  });

  $('#agentFilter').select2({
    ajax: {
      data: function(term, page) { return { term: term }; },
      dataType: 'json',
      results: function(data, page) { return data; }, 
      url: wwwRoot + 'ajax/getAgents.php',
    },
    allowClear: true,
    initSelection: initSelectionAgent,
    minimumInputLength: 1,
    width: '400px',
  });

  $('#userFilter').change(submitIfNotEmpty);
  $('#agentFilter').change(submitIfNotEmpty);
  $('#gameFilterToggle').click(function() {
    $('#gameFilters').slideToggle();
    return false;
  });
}

function newGameInit() {
  $('input[name="newGamePlayers[]"]').select2({
    ajax: {
      data: function(term, page) { return { term: term }; },
      dataType: 'json',
      results: function(data, page) { return data; }, 
      url: wwwRoot + 'ajax/getAgents.php',
    },
    allowClear: true,
    initSelection: initSelectionAgent,
    minimumInputLength: 1,
    placeholder: 'adaugă un agent...',
    width: '400px',
  });

  $('#newGameToggle').click(function() {
    $('#newGame').slideToggle();
    return false;
  });
}

function newTourneyInit() {
  $('#tourneyPlayers').select2({
    ajax: {
      data: function(term, page) { return { term: term }; },
      dataType: 'json',
      results: function(data, page) { return data; }, 
      url: wwwRoot + 'ajax/getAgents.php',
    },
    allowClear: true,
    initSelection: initSelectionAgentMultiple,
    minimumInputLength: 1,
    multiple: true,
    placeholder: 'adaugă agenți...',
    width: '700px',
  });

  $('#addLastVersionLink').click(addLastVersion);
}

function initSelectionUser(element, callback) {
  var id = $(element).val();
  if (id) {
    $.ajax(wwwRoot + 'ajax/getUserById?id=' + id, {dataType: 'json'})
      .done(function(data) {
        callback({ id: id, text: data });
      });
  }
}

function initSelectionAgent(element, callback) {
  var id = $(element).val();
  if (id) {
    $.ajax(wwwRoot + 'ajax/getAgentById?id=' + id, {dataType: 'json'})
      .done(function(data) {
        callback({ id: id, text: data });
      });
  }
}

/** Initializes or updates the select2 values when multiple values are allowed **/
function initSelectionAgentMultiple(element, callback) {
  var data = [];

  $(element.val().split(',')).each(function (index, id) {
    $.ajax({
      url: wwwRoot + 'ajax/getAgentById?id=' + id,
      dataType: 'json',
      success: function(displayValue) {
        data.push({ id: id, text: displayValue });
      },
      async: false,
    });
  });
  callback(data);
}

/* Adds the last agent version of each user to the select2 field */
function addLastVersion() {
  $.getJSON(wwwRoot + 'ajax/getLastVersionAgents', null, function(data) {
    var existing = $('#tourneyPlayers').select2('val');
    $('#tourneyPlayers').select2('val', existing.concat(data));
  });
  return false;
}

function submitIfNotEmpty() {
  var val = $(this).val();
  if (val) {
    $(this).closest('form').submit();
  }
}

function indexInit() {
  $("#grid").jqGrid({
    autowidth: true,
    caption: '',
   	colModel:[
      {name: 'id', hidden: true},
      {name: 'userId', hidden: true},
   		{name: 'username', index: 'username', formatter: userFormatter},
   		{name: 'agentName', index: 'agent', sortable: false, formatter: agentFormatter},
   		{name: 'elo', index: 'elo', align: 'right'},
   	],
   	colNames: ['id', 'userId', 'utilizator', 'agent', 'ELO'],
	  datatype: "json",
    height: 'auto',
   	pager: '#pager',
   	rowList: [10, 20, 50, 100],
   	rowNum: 20,
   	sortname: 'elo',
    sortorder: "desc",
   	url: wwwRoot + 'ajax/getGridAgents',
    viewrecords: true,
  });
  $("#grid").jqGrid('navGrid', '#pager', {edit: false, add: false, del: false});
}

function userFormatter(cellValue, options, rowObject) {
  return '<a href="user?id=' + rowObject.userId + '">' + cellValue + '</a>';
}

function agentFormatter(cellValue, options, rowObject) {
  return '<a href="agent?id=' + rowObject.id + '">' + cellValue + '</a>';
}

function gamesPageInit() {
  var url = wwwRoot + 'ajax/getGridGames';
  var userId = $('#userFilter').val();
  var agentId = $('#agentFilter').val();
  var all = $('#all').text();
  if (userId) {
    url += '?userId=' + userId;
  } else if (agentId) {
    url += '?agentId=' + agentId;
  } else if (all) {
    url += '?all=1';
  }
  $("#gameListingGrid").jqGrid({
    autowidth: true,
    caption: '',
   	colModel:[
      {name: 'id', align: 'center', formatter: 'showlink', formatoptions: { baseLinkUrl: 'game' }, width: 20},
   		{name: 'tourneyId', align: 'center', formatter: tourneyFormatter, width: 20},
   		{name: 'round', align: 'center', formatter: zeroFormatter, width: 20},
   		{name: 'playerData', sortable: false, formatter: gamePlayerFormatter},
   		{name: 'status', hidden: true},
   		{name: 'statusName', index: 'status', formatter: gameStatusFormatter, width: 20},
   	],
   	colNames: ['ID', 'turneu', 'rundă', 'participanți', 'ascuns', 'stare'],
	  datatype: "json",
    height: 'auto',
    onSelectRow: gridGameClick,
   	pager: '#pager',
   	rowList: [10, 20, 50, 100],
   	rowNum: 20,
   	sortname: 'game.id',
    sortorder: 'desc',
   	url: url,
    viewrecords: true,
  });
  $("#gameListingGrid").jqGrid('navGrid', '#pager', {edit: false, add: false, del: false});
}

function tourneysPageInit() {
  $("#tourneysGrid").jqGrid({
    autowidth: true,
    caption: '',
   	colModel:[
      {name: 'id', align: 'center', formatter: 'showlink', formatoptions: { baseLinkUrl: 'tourney' }, width: 40},
      {name: 'userId', hidden: true},
   		{name: 'username', index: 'username', formatter: userFormatter},
   		{name: 'participants', align: 'center' },
   		{name: 'numRounds', align: 'center' },
   		{name: 'gameSize', align: 'center' },
   		{name: 'status', hidden: true},
   		{name: 'statusName', index: 'status', formatter: tourneyStatusFormatter},
   	],
   	colNames: ['ID', 'ascuns', 'creator', 'participanți', 'runde', 'mărimea mesei', 'ascuns', 'stare'],
	  datatype: "json",
    height: 'auto',
    onSelectRow: gridTourneyClick,
   	pager: '#pager',
   	rowList: [10, 20, 50, 100],
   	rowNum: 20,
   	sortname: 'id',
    sortorder: 'desc',
   	url: wwwRoot + 'ajax/getGridTourneys',
    viewrecords: true,
  });
  $("#tourneyGrid").jqGrid('navGrid', '#pager', {edit: false, add: false, del: false});
}

function tourneyGamesInit() {
  $("#gameListingGrid").jqGrid({
    autowidth: true,
    caption: '',
   	colModel:[
      {name: 'id', formatter: 'showlink', formatoptions: { baseLinkUrl: 'game' }, width: 20},
   		{name: 'round', width: 20},
   		{name: 'playerData', sortable: false, formatter: gamePlayerFormatter},
   		{name: 'status', hidden: true},
   		{name: 'statusName', index: 'status', formatter: gameStatusFormatter, width: 20},
   	],
   	colNames: ['ID', 'rundă', 'participanți', 'ascuns', 'stare'],
	  datatype: "json",
    height: 'auto',
    onSelectRow: gridGameClick,
   	pager: '#pager',
   	rowList: [10, 20, 50, 100],
   	rowNum: 20,
   	sortname: 'round',
    sortorder: 'desc',
   	url: wwwRoot + 'ajax/getGridGames?tourneyId=' + $('#tourneyId').val(),
    viewrecords: true,
  });
  $("#gameListingGrid").jqGrid('navGrid', '#pager', {edit: false, add: false, del: false});
}

function gridGameClick(rowId) {
  window.location = wwwRoot + 'game?id=' + rowId;
}

function gridTourneyClick(rowId) {
  window.location = wwwRoot + 'tourney?id=' + rowId;
}

function gamePlayerFormatter(cellValue, options, rowObject) {
  var s = '';
  for (var i = 0; i < cellValue.length; i++) {
    if (i) {
      s += ' • ';
    }
    s += '<a href="user?id=' + cellValue[i].userId + '">' + cellValue[i].username + '</a> ';
    s += '<a href="agent?id=' + cellValue[i].agentId + '">v' + cellValue[i].version + '</a>';
  }
  return s;
}

function gameStatusFormatter(cellValue, options, rowObject) {
  return '<div class="gameStatus gameStatus' + rowObject.status + '">' + cellValue + '</span>';
}

function tourneyStatusFormatter(cellValue, options, rowObject) {
  return '<div class="tourneyStatus tourneyStatus' + rowObject.status + '">' + cellValue + '</span>';
}

function zeroFormatter(cellValue, options, rowObject) {
  return (cellValue == '0') ? '' : cellValue;
}

function tourneyFormatter(cellValue, options, rowObject) {
  return (cellValue == '0') ? '' : ('<a href="tourney?id=' + cellValue + '">' + cellValue + '</a>');
}
