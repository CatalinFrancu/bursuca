<?php

class OS {
  /* Returns a list of ($output, $returnValue). Redirects stderr to stdout. */
  static function exec($cmd) {
    $output = array();
    $returnValue = false;
    exec($cmd . " 2>&1", $output, $returnValue);
    return array($output, $returnValue);
  }

  /**
   * Generates a temporary, unique, non-existant file name with an optional extension.
   * Does not create the file.
   **/
  static function tempnam($dir, $prefix = '', $extension = '') {
    do {
      $fileName = tempnam($dir, $prefix);
      @unlink($fileName);
      if ($extension) {
        $fileName .= '.' . $extension;
      }
    } while (file_exists($fileName));
    return $fileName;
  }

  /* Creates a temporary, unique, non-existant directory. */
  static function tempdir($dir, $prefix = '') {
    $dirName = tempnam('/tmp', 'referee_');
    unlink($dirName);     // tempnam actually creates a file, but we want a directory
    mkdir($dirName);
    return $dirName;
  }
}

?>
