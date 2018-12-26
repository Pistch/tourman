<?php
/**
 * @package    tourman
 *
 * @author     pistch <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

require_once(realpath(__DIR__ . '/../helpers/db.php'));

/**
 * Tourman
 *
 * @package  tourman
 * @since    1.0
 */
class TourmanModelTourman extends ListModel
{
    public function getTournaments() {
        return R::find('tournament', 'ORDER BY id DESC');
    }

    public function getTournament($id) {
        $tournament = R::load('tournament', $id);

        $tournament['stages'] = $this->getTournamentStages($id);

        return $tournament;
    }

    public function getTournamentStages($tournamentID) {
        return R::find('stage', ' tournament_id = ?', [ $tournamentID ]);
    }

    public function getTournamentStage($stageID, $short = false) {
        $stage = R::load('stage', $stageID);

        if (!$short) {
            $stage['games'] = $this -> getStageGames($stage);
        }

        return $stage;
    }

    public function getStageGames($stage) {
        $games = R::find('games', ' stage_id = ? ORDER BY `phase_placement` ASC ', [ $stage['id'] ]);

        foreach ($games as $key => $game) {
            $games[$key]['user1'] = $this -> getUser($game['pl1_id']);
            $games[$key]['user2'] = $this -> getUser($game['pl2_id']);
        }

        $stage['games'] = $games;

        if ((int)$stage['status'] === 1) {
            $stage['registrations'] = $this -> getRegisteredPlayers($stage['id']);
        }

        return $games;
    }

    public function getUser($userID) {
        $fullUser = R::load('user', $userID);
        return $fullUser['name'];
    }

    private function getFullUser($userID) {
        return R::load('user', $userID);
    }

    public function findUser($searchString) {
        return R::findAll('user', ' name LIKE ? LIMIT 10 ', ["%$searchString%"]);
    }

    public function getPlayersTournaments($userID) {
        $stagesResults = R::find('user', ' user_id = ? ORDER BY id DESC ', [$userID]);

        foreach ($stagesResults as $key => $stageResult) {
            $stagesResults[$key]['stage'] = $this -> getTournamentStage($stageResult['stage_id'], true);
        }

        return $stagesResults;
    }

    public function setMatchScore($input) {
        $matchId = $input['id'];
        $pl1Score = $input['pl1_score'];
        $pl2Score = $input['pl2_score'];

        $match = R::load('games', (int)$matchId);

        $match -> pl1_score = (int)$pl1Score;
        $match -> pl2_score = (int)$pl2Score;

        R::store($match);

        return $match;
    }

    public function makeNewTournament($data) {
        $tournament = R::dispense('tournament');

        $tournament -> title = $data['title'];
        $tournament -> description = $data['description'];
        $tournament -> start_date = $data['start_date'];
        $tournament -> end_date = $data['end_date'];
        $tournament -> is_rating = $data['is_rating'];
        $tournament -> net_type = $data['net_type'];
        $tournament -> net_size = $data['net_size'];
        $tournament -> reglament = $data['reglament'];

        R::store($tournament);
        return($tournament);
    }

    public function makeNewTournamentStage($data) {
        $stage = R::dispense('stage');

        $stage -> title = $data['title'];
        $stage -> net_size = $data['net_size'];
        $stage -> net_type = $data['net_type'];
        $stage -> tournament_id = $data['tournament_id'];
        $stage -> start_date = $data['start_date'];
        $stage -> end_date = $data['end_date'];
        $stage -> status = 1;

        $stageId = R::store($stage);
        $this -> makeStageGames($stageId);
        return($stage);
    }

    private function makeStageGames($stageId) {
        $stage = R::load('stage', $stageId);

        switch ($stage -> net_type) {
            case '2-0':
                $this -> buildNet_2_0($stage -> id, $stage -> net_size);
                break;
            case '2-1':
                $this -> buildNet_2_1($stage -> id, $stage -> net_size);
        }
    }

    private function buildNet_2_0($stageId, $size) {
        for ($stageNetSize = $size, $phaseNo = 0; $stageNetSize > 1; $phaseNo++) {
            for ($i = 0; $i < $stageNetSize; $i += 2) {
                $game = R::dispense('games');

                $game -> pl1_id = 0;
                $game -> pl2_id = 0;
                $game -> pl1_score = 0;
                $game -> pl2_score = 0;
                $game -> stage_id = $stageId;
                $game -> phase_placement = $i;
                $game -> phase = 'w' . $phaseNo;

                R::store($game);
            }

            $stageNetSize = $stageNetSize / 2;
        }
    }

    private function buildNet_2_1($stageId, $size) {
        // Виннеры
        for ($i = 0; $i < $size; $i += 2) {
            $game = R::dispense('games');

            $game -> pl1_id = 0;
            $game -> pl2_id = 0;
            $game -> pl1_score = 0;
            $game -> pl2_score = 0;
            $game -> stage_id = $stageId;
            $game -> phase_placement = $i;
            $game -> phase = 'w0';

            R::store($game);
        }

        for ($i = 0; $i < $size / 2; $i += 2) {
            $game = R::dispense('games');

            $game -> pl1_id = 0;
            $game -> pl2_id = 0;
            $game -> pl1_score = 0;
            $game -> pl2_score = 0;
            $game -> stage_id = $stageId;
            $game -> phase_placement = $i;
            $game -> phase = 'w1';

            R::store($game);
        }

        for ($i = 0; $i < $size / 4; $i += 2) {
            $game = R::dispense('games');

            $game -> pl1_id = 0;
            $game -> pl2_id = 0;
            $game -> pl1_score = 0;
            $game -> pl2_score = 0;
            $game -> stage_id = $stageId;
            $game -> phase_placement = $i;
            $game -> phase = 'w2';

            R::store($game);
        }

        // Лузеры
        for ($i = 0; $i < $size / 2; $i += 2) {
            $game = R::dispense('games');

            $game -> pl1_id = 0;
            $game -> pl2_id = 0;
            $game -> pl1_score = 0;
            $game -> pl2_score = 0;
            $game -> stage_id = $stageId;
            $game -> phase_placement = $i;
            $game -> phase = 'l0';

            R::store($game);
        }

        for ($i = 0; $i < $size / 2; $i += 2) {
            $game = R::dispense('games');

            $game -> pl1_id = 0;
            $game -> pl2_id = 0;
            $game -> pl1_score = 0;
            $game -> pl2_score = 0;
            $game -> stage_id = $stageId;
            $game -> phase_placement = $i;
            $game -> phase = 'l1';

            R::store($game);
        }

        for ($i = 0; $i < $size / 4; $i += 2) {
            $game = R::dispense('games');

            $game -> pl1_id = 0;
            $game -> pl2_id = 0;
            $game -> pl1_score = 0;
            $game -> pl2_score = 0;
            $game -> stage_id = $stageId;
            $game -> phase_placement = $i;
            $game -> phase = 'l2';

            R::store($game);
        }

        for ($i = 0; $i < $size / 4; $i += 2) {
            $game = R::dispense('games');

            $game -> pl1_id = 0;
            $game -> pl2_id = 0;
            $game -> pl1_score = 0;
            $game -> pl2_score = 0;
            $game -> stage_id = $stageId;
            $game -> phase_placement = $i;
            $game -> phase = 'l3';

            R::store($game);
        }

        // Олимпийка
        for ($stageNetSize = $size / 4, $phaseNo = 0; $stageNetSize > 1; $phaseNo++) {
            for ($i = 0; $i < $stageNetSize; $i += 2) {
                $game = R::dispense('games');

                $game -> pl1_id = 0;
                $game -> pl2_id = 0;
                $game -> pl1_score = 0;
                $game -> pl2_score = 0;
                $game -> stage_id = $stageId;
                $game -> phase_placement = $i;
                $game -> phase = 'o' . $phaseNo;

                R::store($game);
            }

            $stageNetSize = $stageNetSize / 2;
        }
    }

    public function getRegisteredPlayers($stageId) {
        $registeredIds = R::find('registration', ' stage_id = ? ', [$stageId]);
        $registeredUsers = [];

        foreach ($registeredIds as $key => $registration) {
            $registeredUsers[] = $this -> getFullUser((int) $registration['player_id']);
        }

        return $registeredUsers;
    }

    public function registerPlayersToStage($playerIds, $stageId) {
        $errors = [];

        foreach ($playerIds as $id) {
            $result = $this -> registerPlayerToStage($id, $stageId);
            if (!$result) {
                $errors[] = $id;
            }
        }

        if (count($errors) === 0) {
            return $this -> getRegisteredPlayers($stageId);
        } else {
            return [
                "result" => "Error",
                "info" => $errors
            ];
        }
    }

    private function registerPlayerToStage($playerId, $stageId) {
        $stage = R::load('stage', $stageId);

        if (isset($stage['id'])) {
            $player = $this -> getFullUser($playerId);

            if (isset($player['id'])) {
                if (R::findOne('registration', ' player_id = ? ', [$playerId]) !== null) {
                    return false;
                }

                $registration = R::dispense('registration');

                $registration -> stage_id = $stageId;
                $registration -> player_id = $playerId;

                R::store($registration);

                return true;
            }
        }
    }

    public function closeRegistration($stageId) {
        $stage = R::load('stage', $stageId);
        $gamesQuantity = $stage -> net_size / 2;

        if ((int)$stage['state'] !== 1) {
            return [
                "result" => "ERROR",
                "info" => "Wrong stage state"
            ];
        }

        if (isset($stage['id'])) {
            $this -> seedPlayers($stageId, $gamesQuantity);
            $stage['state'] = 2;

            R::store($stage);
        }

        return ["result" => "OK"];
    }

    private function seedPlayers($stageId, $gamesQuantity) {
        $registrations = R::findAll('registration', ' stage_id = ? ', [$stageId]);

        $playerIds = [];

        foreach ($registrations as $key => $registration) {
            $playerIds[] = $registration['player_id'];
        }

        shuffle($playerIds);

        for ($i = 0; $i < $gamesQuantity; $i++) {
            $player1Id = 0;
            $player2Id = 0;

            if (isset($playerIds[$i])) {
                $player1Id = $playerIds[$i];
            }

            if (isset($playerIds[$i + $gamesQuantity])) {
                $player2Id = $playerIds[$i + $gamesQuantity];
            }

            $game = R::findOne('games', ' phase = "w0" AND phase_placement = ? ', [$i]);
            $game -> pl1_id = $player1Id;
            $game -> pl2_id = $player2Id;

            R::store($game);
        }

        R::trashAll($registrations);
    }
}
