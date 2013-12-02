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

    $sourceCode = file_get_contents($rec['tmp_name']);
    $language = ($extension == 'c') ? 'c' : 'c++';
    Agent::validate($sourceCode, $language);

    $agent = Model::factory('Agent')->create();
    $agent->language = $language;
    $agent->name = $name;
    $agent->userId = $user->id;
    $agent->version = 1 + Agent::getMaxVersion($user->id);
    $compileError = $agent->saveFileAndCompile($sourceCode);
    $agent->save();

    FlashMessage::add('Am adăugat strategia.', 'info');
    Util::redirect('agents');
  } catch (Exception $e) {
    FlashMessage::add($e->getMessage());
  }
}

SmartyWrap::assign('agents', Agent::get_all_by_userId($user->id));
SmartyWrap::assign('pageTitle', 'programele mele');
SmartyWrap::display('agents.tpl');

?>
