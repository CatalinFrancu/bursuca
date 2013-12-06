<?php

class Player extends BaseObject {
  // Reasons a program may end or be killed
  const REASON_UNKNOWN = 0;    // hey, it's not an exact science
  const REASON_EXIT = 1;       // process exited or crashed by itself
  const REASON_BAD_MOVE = 2;   // process was killed due to a bad move
  const REASON_TLE = 3;        // process was killed because it took to long to make a move
  const REASON_GAME_OVER = 4;  // process was killed because the game was over
}

?>
