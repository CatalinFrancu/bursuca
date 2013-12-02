<?php

class OS {
  /* Returns a list of ($output, $returnValue). Redirects stderr to stdout. */
  static function exec($cmd) {
    $output = array();
    $returnValue = false;
    exec($cmd . " 2>&1", $output, $returnValue);
    return array($output, $returnValue);
  }
}

?>
