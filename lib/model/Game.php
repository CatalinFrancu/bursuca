<?php

class Game extends BaseObject {
  const MIN_PLAYERS = 2;
  const MAX_PLAYERS = 6;

  const STATUS_NEW = 0;
  const STATUS_FINISHED = 1;
  static $STATUS_NAMES = array(self::STATUS_NEW => 'neîncepută',
                               self::STATUS_FINISHED => 'terminată');

  const MIN_INITIAL_PRICE = 2;
  const MAX_INITIAL_PRICE = 6;

  // Idiorm doesn't allow constructors
  public static function create() {
    $g = Model::factory('Game')->create();
    $g->status = self::STATUS_NEW;
    $g->price1 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    $g->price2 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    $g->price3 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    $g->price4 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    $g->price5 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    $g->price6 = rand(self::MIN_INITIAL_PRICE, self::MAX_INITIAL_PRICE);
    return $g;
  }

  public function isFinished() {
    return $this->status == self::STATUS_FINISHED;
  }

  public function getStatusName() {
    return self::$STATUS_NAMES[$this->status];
  }
}

?>
