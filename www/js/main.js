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
    width: '300px',
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
    width: '300px',
  });

  $('#userFilter').change(submitIfNotEmpty);
  $('#agentFilter').change(submitIfNotEmpty);
  $('#gameFilterToggle').click(function() {
    $('#gameFilters').slideToggle();
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
