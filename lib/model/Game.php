<?php

class Game extends BaseObject {
  const MIN_PLAYERS = 2;
  const MAX_PLAYERS = 6;

  const STATUS_NEW = 0;
  const STATUS_FINISHED = 1;
  const STATUS_TRANSIENT = 2;  // just created, but not ready to be judged yet
  static $STATUS_NAMES = array(self::STATUS_NEW => 'neîncepută',
                               self::STATUS_FINISHED => 'terminată',
                               self::STATUS_TRANSIENT => 'tranzitorie');

  const MIN_INITIAL_PRICE = 2;
  const MAX_INITIAL_PRICE = 6;

  // Idiorm doesn't allow constructors
  static function create() {
    $g = Model::factory('Game')->create();
    $g->status = self::STATUS_TRANSIENT;
    $g->price1 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    $g->price2 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    $g->price3 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    $g->price4 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    $g->price5 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    $g->price6 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    return $g;
  }

  function isFinished() {
    return $this->status == self::STATUS_FINISHED;
  }

  function getStatusName() {
    return self::$STATUS_NAMES[$this->status];
  }

  function getStartingPrices() {
    return array($this->price1, $this->price2, $this->price3, $this->price4, $this->price5, $this->price6);
  }
}

?>
