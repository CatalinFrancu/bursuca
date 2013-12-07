<?php

class Player extends BaseObject {
  // Reasons a program may end or be killed
  const REASON_UNKNOWN = 0;    // hey, it's not an exact science
  const REASON_EXIT = 1;       // process exited or crashed by itself
  const REASON_BAD_MOVE = 2;   // process was killed due to a bad move
  const REASON_TLE = 3;        // process was killed because it took to long to make a move
  const REASON_GAME_OVER = 4;  // process was killed because the game was over
  static $REASON_NAMES = array(self::REASON_UNKNOWN => 'motiv necunoscut',
                               self::REASON_EXIT => 'programul s-a terminat singur',
                               self::REASON_BAD_MOVE => 'programul a făcut o mutare incorectă',
                               self::REASON_TLE => 'programul a depășit timpul',
                               self::REASON_GAME_OVER => 'programul a fost omorât la sfârșitul jocului');

  function getKillReason() {
    return self::$REASON_NAMES[$this->killReason];
  }
}

?>
