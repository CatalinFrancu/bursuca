<?php 

require_once '../lib/Util.php';
Util::requireLoggedIn();

$name = Util::getRequestParameter('name');
$submitButton = Util::getRequestParameter('submitButton');

$user = Session::getUser();

if ($submitButton) {
  $rec = $_FILES['file'];
  try {
    if ($rec['error'] > 0) {
      throw new Exception('Eroare ' . $rec['error'] . ' la încărcarea fișierului.');
    }
    if ($rec['size'] > Agent::MAX_SOURCE_SIZE) {
      throw new Exception(sprintf('Dimensiunea maximă admisă este de %d octeți.', Agent::MAX_SOURCE_SIZE));
    }
    $extension = strtolower(pathinfo($rec['name'], PATHINFO_EXTENSION));
    if ($extension != 'c' && $extension != 'cpp') {
      throw new Exception('Sunt permise numai fișiere .c sau .cpp.');
    }

    $agent = Model::factory('Agent')->create();
    $agent->userId = $user->id;
    $agent->version = 1 + Agent::getMaxVersion($user->id);
    $agent->name = $name;
    $agent->language = ($extension == 'c') ? 'c' : 'c++';
    $agent->rated = 1;

    // These throw exceptions on errors
    $agent->validate();
    $agent->setSourceCode(file_get_contents($rec['tmp_name']));

    $agent->save();

    // Make the previous agent version unrated
    if ($agent->version > 1) {
      $prev = Agent::get_by_userId_version($user->id, $agent->version - 1);
      $prev->rated = 0;
      $prev->save();
    }
    FlashMessage::add('Am adăugat agentul.', 'info');
    Util::redirect('agents');
  } catch (Exception $e) {
    FlashMessage::add($e->getMessage());
  }
}

$agents = Model::factory('Agent')->where('userId', $user->id)->order_by_desc('version')->find_many();
SmartyWrap::assign('agents', $agents);
SmartyWrap::assign('pageTitle', 'programele mele');
SmartyWrap::display('agents.tpl');

?>
