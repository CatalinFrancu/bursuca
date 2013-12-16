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

function initSelectionUser(element, callback) {
  var id = $(element).val();
  if (id) {
    $.ajax(wwwRoot + 'ajax/getUserById.php?id=' + id, {dataType: 'json'})
      .done(function(data) {
        callback({ id: id, text: data });
      });
  }
}

function initSelectionAgent(element, callback) {
  var id = $(element).val();
  if (id) {
    $.ajax(wwwRoot + 'ajax/getAgentById.php?id=' + id, {dataType: 'json'})
      .done(function(data) {
        callback({ id: id, text: data });
      });
  }
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
      {name: 'userId', hidden: true},
      {name: 'agentId', hidden: true},
   		{name: 'username', index: 'username', formatter: userFormatter},
   		{name: 'agent', index: 'agent', sortable: false, formatter: agentFormatter},
   		{name: 'elo', index: 'elo', align: 'right'},
   	],
   	colNames: ['userId', 'agentId', 'utilizator', 'agent', 'ELO'],
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
  return '<a href="user?id=' + rowObject[0] + '">' + cellValue + '</a>';
}

function agentFormatter(cellValue, options, rowObject) {
  return '<a href="agent?id=' + rowObject[1] + '">' + cellValue + '</a>';
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
      {name: 'id', formatter: 'showlink', formatoptions: { baseLinkUrl: 'game' }, width: 20},
   		{name: 'players', sortable: false, formatter: gamePlayerFormatter},
   		{name: 'status', hidden: true},
   		{name: 'statusName', index: 'status', formatter: gameStatusFormatter, width: 20},
   	],
   	colNames: ['ID', 'participanți', 'ascuns', 'stare'],
	  datatype: "json",
    height: 'auto',
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
  return '<div class="gameStatus gameStatus' + rowObject[2] + '">' + cellValue + '</span>';
}
