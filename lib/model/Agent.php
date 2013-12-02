<?php

class Agent extends BaseObject {
  const MAX_SOURCE_SIZE = 65536;
  static $EXTENSION = array('c' => 'c',
                            'c++' => 'cpp');
  static $COMPILER = array('c' => 'gcc -Wall -O2 -static -lm',
                           'c++' => 'g++ -Wall -O2 -static -std=c++0x -lm');

  /* Attempts to compile the source code. Throws an Exception on errors */
  static function validate($sourceCode, $language) {
    // Compile the source code
    $extension = self::$EXTENSION[$language];
    $compiler = self::$COMPILER[$language];
    $fileName = tempnam('/tmp/', '') . '.' . $extension;
    $binaryName = tempnam('/tmp/', '');
    file_put_contents($fileName, $sourceCode);
    list($output, $returnValue) = OS::exec("$compiler -o $binaryName $fileName");
    if ($returnValue) {
      @unlink($binaryName);
      throw new Exception('Eroare la compilare:<br/>' . implode('<br/>', $output));
    }
    @unlink($fileName);
    @unlink($binaryName);
  }


}

?>
