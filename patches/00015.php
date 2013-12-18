<?php

$users = Model::factory('User')->find_many();

$sumOfDifferences = 0;
foreach ($users as $u) {
  // This user's ELO is the average of their agents' ELOs.
  $diffElo = 0;
  $agents = Agent::get_all_by_userId($u->id);
  foreach ($agents as $a) {
    $diffElo += $a->elo - Elo::STARTING_ELO;
    // Make all the user's agents unrated, except for the latest one
    if ($a->version != count($agents)) {
      $a->rated = 0;
      $a->save();
    }
  }
  $u->elo = ELO::STARTING_ELO + $diffElo;
  $u->save();
  $sumOfDifferences += $diffElo;
  print "User {$u->username} has ELO {$u->elo}\n";
}

print "The sum of differences is {$sumOfDifferences}\n";

?>
