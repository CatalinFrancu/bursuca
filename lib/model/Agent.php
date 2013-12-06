<?php

class Agent extends BaseObject {
  const MAX_SOURCE_SIZE = 65536;
  const SOURCES_DIR = 'sources'; /* binaries are in the same directory, without the extension */
  static $EXTENSION = array('c' => 'c',
                            'c++' => 'cpp');
  static $COMPILER = array('c' => 'gcc -Wall -O2 -static -lm -o %s %s',
                           'c++' => 'g++ -Wall -O2 -static -std=c++0x -lm -o %s %s');

  static function getMaxVersion($userId) {
    $agent = Model::factory('Agent')->where('userId', $userId)->order_by_desc('version')->find_one();
    return $agent ? $agent->version : 0;
  }

  function getFullBinaryName() {
    return sprintf("%s/%s/%04s_%04s", Util::$rootPath, self::SOURCES_DIR, $this->userId, $this->version);
  }

  function getFullDataName() {
    return sprintf("%s.dat", $this->getFullBinaryName());
  }

  function getFullSourceName() {
    return sprintf("%s.%s", $this->getFullBinaryName(), self::$EXTENSION[$this->language]);
  }

  function getSourceCode() {
    return file_get_contents($this->getFullSourceName());
  }

  /** Throws an Exception on validation errors **/
  function validate() {
    if (!$this->name) {
      throw new Exception('Numele este obligatoriu.');
    }
  }

  /* Attempts to compile the source code. Throws an Exception on errors */
  function setSourceCode($sourceCode) {
    // Compile the source code
    $extension = self::$EXTENSION[$this->language];
    $fileName = OS::tempnam('/tmp/', '', $extension);
    $binaryName = OS::tempnam('/tmp/');
    file_put_contents($fileName, $sourceCode);
    $command = sprintf(self::$COMPILER[$this->language], $binaryName, $fileName);
    list($output, $returnValue) = OS::exec($command);
    if ($returnValue) {
      @unlink($fileName);
      @unlink($binaryName);
      throw new Exception('Eroare la compilare:<br>' . implode('<br>', $output));
    }
    if (!@rename($fileName, $this->getFullSourceName())) {
      throw new Exception('Nu pot salva sursa în directorul de surse.');
    }
    if (!@rename($binaryName, $this->getFullBinaryName())) {
      throw new Exception('Nu pot salva binarul în directorul de binare.');
    }
  }

}

?>
