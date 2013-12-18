<?php

class Elo {
  const STARTING_ELO = 1600;
  const DISPARITY = 400.0;
  const K = 32.0;

  static function ratingChange($winnerElo, $loserElo) {
    $factor = ($loserElo - $winnerElo) / self::DISPARITY;
    $estimate = 1.0 / (1.0 + pow(10.0, $factor));
    return (int)round(self::K * (1 - $estimate));
  }
}

?>
