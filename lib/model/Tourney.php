<?php

class Tourney extends BaseObject {
  const STATUS_NEW = 0;
  const STATUS_ONGOING = 1;
  const STATUS_FINISHED = 2;
  static $STATUS_NAMES = array(self::STATUS_NEW => 'neînceput',
                               self::STATUS_ONGOING => 'în desfășurare',
                               self::STATUS_FINISHED => 'terminat');

  function scheduleRound($r) {
    $participants = Participant::get_all_by_tourneyId($this->id);
    shuffle($participants);
    foreach ($participants as $i => $part) {
      if ($i % $this->gameSize == 0) {
        $g = Game::create();
        $g->tourneyId = $this->id;
        $g->round = $r;
        $g->save();
      }
      $p = Model::factory('Player')->create();
      $p->gameId = $g->id;
      $p->agentId = $part->agentId;
      $p->position = 1 + $i % $this->gameSize;
      $p->save();
    }
  }

  function countParticipants() {
    return Model::factory('Participant')->where('tourneyId', $this->id)->count();
  }

  function getStatus() {
    $numParticipants = $this->countParticipants();
    $neededGames = $this->numRounds * $numParticipants / $this->gameSize;
    $finishedGames = Model::factory('Game')->where('tourneyId', $this->id)->where('status', Game::STATUS_FINISHED)->count();
    if ($finishedGames == 0) {
      return self::STATUS_NEW;
    } else if ($finishedGames < $neededGames) {
      return self::STATUS_ONGOING;
    } else {
      return self::STATUS_FINISHED;
    }
  }

  function getStatusName($status = null) {
    if ($status === null) {
      $status = $this->getStatus();
    }
    return self::$STATUS_NAMES[$status];
  }

}

?>
