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
require_once(realpath(__DIR__ . '/../helpers/functions.php'));

/**
 * reset
 * 
 * UPDATE game SET pl1_id = 0, pl2_id = 0, pl1_score = 0, pl2_score = 0, game.status = 'NOT_STARTED' WHERE stage_id = 1266 AND NOT game.phase = 'w0';
 * DELETE FROM `result` WHERE tournament_stage_id = 1266;
 * UPDATE game SET pl1_score = 0, pl2_score = 0, game.status = 'NOT_STARTED' WHERE stage_id = 1266 AND game.phase = 'w0';
 */

/**
 * Tourman
 *
 * @package  tourman
 * @since    1.0
 */
class TourmanModelTourman extends ListModel {
    public function getTournaments() {
        return R::find('tournament', 'ORDER BY id DESC');
    }

    public function getTournament($id) {
        $tournament = R::load('tournament', $id);

        $tournament['stages'] = $this -> getTournamentStages($id);

        return $tournament;
    }

    public function getTournamentStages($tournamentID) {
        return R::find('stage', ' tournament_id = ? ORDER BY id ASC ', [ $tournamentID ]);
    }

    public function getTournamentStage($stageID, $short = false) {
        $stage = R::load('stage', $stageID);

        if (!$short) {
            $stage['games'] = $this -> getStageGames($stage);
        }

        return $stage;
    }

    public function getStageGames($stage) {
        $games = R::find('game', ' stage_id = ? ORDER BY `phase_placement` ASC ', [ $stage['id'] ]);

        foreach ($games as $key => $game) {
            $games[$key]['user1'] = $this -> getUser($game['pl1_id']);
            $games[$key]['user1_handicap'] = $this -> getUserStageHandicap($game['pl1_id'], $stage['id']);
            $games[$key]['user2'] = $this -> getUser($game['pl2_id']);
            $games[$key]['user2_handicap'] = $this -> getUserStageHandicap($game['pl2_id'], $stage['id']);
        }

        $stage['games'] = $games;

        if ((int)$stage['status'] === 1) {
            $stage['registrations'] = $this -> getRegisteredPlayers($stage['id']);
        }

        return $games;
    }

    public function getDuedGames() {
        $collection = R::findCollection('game', ' status = "STARTED" ORDER BY due_time ASC');
        $result = [];

        while ($game = $collection -> next()) {
            $game['user1'] = $this -> getUser($game['pl1_id']);
            $game['user1_handicap'] = $this -> getUserStageHandicap($game['pl1_id'], $stage['id']);
            $game['user2'] = $this -> getUser($game['pl2_id']);
            $game['user2_handicap'] = $this -> getUserStageHandicap($game['pl2_id'], $stage['id']);

            $result[] = $game;
        }

        return $result;
    }

    public function getUser($userID) {
        $fullUser = R::load('user', $userID);

        return $fullUser['name'];
    }

    private function getUserStageHandicap($playerId, $stageId) {
        $handicap = R::findOne('stagehandicap', ' stage_id = ? AND player_id = ? ', [$stageId, $playerId]);

        if ($handicap && isset($handicap['value'])) {
            return $handicap['value'];
        }

        return 0;
    }

    public function getFullUser($userID) {
        $user = R::load('user', $userID);

        return $user;
    }

    public function upsertUser($data) {
        $userFields = ['id', 'name', 'login', 'password', 'email', 'birthday', 'photo', 'rank', 'region', 'info', 'reg_time', 'role', 'block', 'auth_time', `sid`];

        if ($data['id']) {
            $user = R::load('user', $data['id']);
        } else {
            $user = R::dispense('user');
        }

        foreach ($data as $key => $value) {
            if ($key !== 'id' && array_search($key, $userFields)) {
                $user[$key] = $value;
            }
        }

        R::store($user);

        return $user;
    }

    public function getRatings($limit = 30, $offset = 0, $tournamentId = 0) {
        if ($tournamentId === 0) {
            $ratings = R::findAll('rating', ' LIMIT ?, ? ORDER BY points DESC ', [$offset, $offset + $limit]);
        } else {
            $ratings = R::findAll('rating', ' tournament_stage_id = ? LIMIT ?, ? ORDER BY points DESC ', [$tournamentId, $offset, $offset + $limit]);
        }

        $users = [];

        foreach ($ratings as $key => $rating) {
            $users[] = [
                "name" => $this -> getUser($rating['user_id']),
                "points" => $rating['points']
            ];
        }

        return $users;
    }

    public function getUsersRatings($users, $tournamentId = null) {
        $result = [];

        foreach ($users as $key => $userId) {
            $result[] = [
                "id" => $userId,
                "name" => $this -> getUser($userId),
                "points" => $this -> getUserRating($userId, $tournamentId)
            ];
        }

        function sortPlayersByRating($u1, $u2) {
            return (int)$u2['points'] - (int)$u1['points'];
        }

        usort($result, 'sortPlayersByRating');

        return $result;
    }

    private function getUserRating($userId, $tournamentId) {
        if ($tournamentId) {
            $rating = R::findOne('rating', ' user_id = ? AND tournament_id = ? ', [$userId, $tournamentId]);
        } else {
            $rating = R::findOne('rating', ' user_id = ? ', [$userId]);
        }

        if ($rating === null) {
            return 0;
        }

        return $rating['points'];
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

        $match = R::load('game', (int)$matchId);

        $match -> pl1_score = (int)$pl1Score;
        $match -> pl2_score = (int)$pl2Score;

        R::store($match);

        return $match;
    }


    public function startMatch($input) {
        $matchId = $input['id'];
        $match = R::load('game', (int)$matchId);

        if (!$match -> start_time) {
            $match -> start_time = R::isoDateTime();
        }

        if (isset($input['due_time'])) {
            $match -> due_time = $input['due_time'];
        }

        if (isset($input['table'])) {
            $match -> table = $input['table'];
        }

        $match -> status = 'STARTED';

        R::store($match);

        return $match;
    }


    public function finalizeMatch($matchId, $winnerPhasePlacement = null) {
        $match = R::load('game', (int)$matchId);

        if (
            ((int)$match['pl1_id'] === 0 && (int)$match['pl2_id'] === 0) ||
            (
                (int)$match['pl1_id'] !== 0 &&
                (int)$match['pl2_id'] !== 0 &&
                (int)$match['pl1_score'] === 0 &&
                (int)$match['pl2_score'] === 0
            )
        ) {
            return $match;
        }

        $phaseType = substr($match['phase'], 0, 1);
        $phaseNo = (int)substr($match['phase'], 1, 1);
        $nextPhaseNo = $phaseNo + 1;
        $isLastPhase = R::findOne('game', ' stage_id = ? AND phase = ? ', [$match['stage_id'], "$phaseType$nextPhaseNo"]) === null;

        $stage = R::load('stage', $match['stage_id']);

        if ((int)$match['pl1_score'] > (int)$match['pl2_score']) {
            $winnerId = $match['pl1_id'];
            $loserId = $match['pl2_id'];
        } elseif ((int)$match['pl1_score'] < (int)$match['pl2_score'])  {
            $winnerId = $match['pl2_id'];
            $loserId = $match['pl1_id'];
        } elseif ((int)$match['pl1_score'] === 0) {
            $this -> proceedWinner((int)$match['pl2_id'] === 0 ? $match['pl1_id'] : $match['pl2_id'], $match, $stage, $phaseType, $phaseNo, $isLastPhase);
            $match -> status = 'FINISHED';
            R::store($match);
            return $match;
        } else {
            return $match;
        }

        $this -> proceedWinner($winnerId, $match, $stage, $phaseType, $phaseNo, $isLastPhase, $winnerPhasePlacement);
        $this -> proceedLoser($loserId, $match, $stage, $phaseType, $phaseNo, $isLastPhase);

        $match -> due_time = R::isoDateTime();
        $match -> status = 'FINISHED';

        R::store($match);

        return $match;
    }

    private function proceedLoser($loserId, $match, $stage, $phaseType, $phaseNo, $isLastPhase) {
        if ($stage['net_type'] === '2-0') {
            $place = pow(2, log((int)$stage['net_size'], 2) - $phaseNo - 1) + 1;

            $this -> makeResultRecord($match['stage_id'], $loserId, $place);
            return;
        } else {
            switch ($phaseType) {
                case 'w':
                    // Падаем только в 1, 2 и 4 раунды сетки проигравший, 3й играется среди игроков, упавших ранее
                    $targetPhaseMap = [0, 1, 3];

                    $newPhase = 'l' . (string)($targetPhaseMap[$phaseNo]);

                    // Определяем игру, в которую падает проигравший, для каждого раунда свой алгоритм
                    switch ($phaseNo) {
                        case 0:
                            // Тут всё просто, каждый падает в свою дырку
                            $fromPhasePlacement = (int)$match['phase_placement'];
                            if ($fromPhasePlacement % 2 === 0) {
                                $phasePlacement = $fromPhasePlacement / 2;
                                $targetPlayerSlot = 1;
                            } else {
                                $phasePlacement = ($fromPhasePlacement - 1) / 2;
                                $targetPlayerSlot = 2;
                            }

                            break;

                        case 1:
                            // Здесь каждый должен упасть крест-накрест со своим положением в верхней сетке
                            $phasePlacement = (int)$stage['net_size'] / 4 - (int)$match['phase_placement'] - 1;

                            break;

                        case 2:
                            // В сетке на 16 меняем местами
                            if ((int)$stage['net_size'] === 16) {
                                $phasePlacement = (int)$match['phase_placement'] === 1 ? 0 : 1;
                                break;
                            // В сетке на 32 перетасовываем пары
                            } elseif ((int)$stage['net_size'] === 32) {
                                $wasPhasePlacement = (int)$match['phase_placement'];
                                $phasePlacement = $wasPhasePlacement % 2 === 0
                                    ? $wasPhasePlacement + 1
                                    : $wasPhasePlacement - 1;
                                break;
                            }

                            // А здесь механизм определения довольно сложен
                            // Сначала нужно разбить на четвёрки
                            // Затем крест-накрест поменять местами пары в четвёрке
                            // Получиться должно так:
                            // 1 -> 3
                            // 2 -> 4
                            // 3 -> 1
                            // 4 -> 2
                            // 5 -> 7
                            // 6 -> 8
                            // 7 -> 5
                            // 8 -> 6
                            $fromPhasePlacement = (int)$match['phase_placement'];
                            $fourNumber = floor($fromPhasePlacement / 4);
                            $gameNumberInFour = $fromPhasePlacement % 4;

                            $targetGameNumberInFour = $gameNumberInFour + 2 > 3
                                ? $gameNumberInFour - 2
                                : $gameNumberInFour + 2;

                            $phasePlacement = $fourNumber * 4 + $targetGameNumberInFour;

                            break;

                        default:
                            $match['phase_placement'];
                    }

                    $newGame = R::findOne('game', ' stage_id = ? AND phase = ? AND phase_placement = ? ', [$match['stage_id'], $newPhase, $phasePlacement]);

                    if ($phaseNo === 0) {
                        // Специфика первого раунда сетки проигравших, нужно понимать, в верхний или нижний слот в игре
                        // следует определить игрока
                        $newGame['pl' . $targetPlayerSlot . '_id'] = $loserId;
                    } else {
                        $newGame -> pl1_id = $loserId;
                    }

                    R::store($newGame);

                    break;
                case 'l':
                    // Высчитанные значения доли игроков, прошедших дальше того, для которого в данный момент определяем
                    // пул занятых мест, при сетке до двух поражений с выходом в олимпийку
                    $placeMupltiplierMap = [ 3/4, 1/2, 3/8, 1/4 ];

                    $place = (int)$stage['net_size'] * $placeMupltiplierMap[$phaseNo] + 1;

                    $this -> makeResultRecord($match['stage_id'], $loserId, $place);
                    break;
                case 'o':
                    $place = pow(2, log((int)$stage['net_size'] / 4, 2) - $phaseNo - 1) + 1;

                    $this -> makeResultRecord($match['stage_id'], $loserId, $place);
                    break;
            }
        }
    }

    private function proceedWinner($winnerId, $match, $stage, $phaseType, $phaseNo, $isLastPhase, $desired_phase_placement = null) {
        if ($stage['net_type'] === '2-0') {
            if ($isLastPhase) {
                $this -> makeResultRecord($match['stage_id'], $winnerId, 1);
                return;
            } else {
                $newPhase = $phaseType . (string)($phaseNo + 1);
                $phasePlacement = (int)$match['phase_placement'] / 2;
                $newGame = R::findOne('game', ' stage_id = ? AND phase = ? AND phase_placement = ? ', [$match['stage_id'], $newPhase, floor($phasePlacement)]);

                if ((int)$match['phase_placement'] % 2 === 0) {
                    $newGame -> pl1_id = $winnerId;
                } else {
                    $newGame -> pl2_id = $winnerId;
                }

                R::store($newGame);
            }
        } else {
            if ($isLastPhase) {
                switch ($phaseType) {
                    case 'w':
                        $newPhase = 'o0';
                        $newGame = R::findOne('game', ' stage_id = ? AND phase = ? AND phase_placement = ? ', [$match['stage_id'], $newPhase, $match['phase_placement']]);

                        $newGame -> pl1_id = $winnerId;

                        R::store($newGame);
                        break;
                    case 'l':
                        $newPhase = 'o0';

                        if (isset($desired_phase_placement)) {
                            $potentialGame = R::findOne('game', ' stage_id = ? AND phase = ? AND phase_placement = ? ', [$match['stage_id'], $newPhase, $desired_phase_placement]);

                            if ((int)($potentialGame -> pl2_id) === 0) {
                                $phasePlacement = $desired_phase_placement;
                            }
                        } else {
                            $phasePlacement = $match['phase_placement'];
                        }

                        $newGame = R::findOne('game', ' stage_id = ? AND phase = ? AND phase_placement = ? ', [$match['stage_id'], $newPhase, $phasePlacement]);

                        $newGame -> pl2_id = $winnerId;

                        R::store($newGame);
                        break;
                    case 'o':
                        $this -> makeResultRecord($match['stage_id'], $winnerId, 1);
                        break;
                }
            } else {
                $newPhase = $phaseType . (string)($phaseNo + 1);
                $phasePlacement = ($phaseType === 'l' && $phaseNo % 2 === 0)
                    ? (int)$match['phase_placement']
                    : floor((int)$match['phase_placement'] / 2);
                $newGame = R::findOne('game', ' stage_id = ? AND phase = ? AND phase_placement = ? ', [$match['stage_id'], $newPhase, $phasePlacement]);

                if ($phaseType === 'l' && $phaseNo % 2 === 0) {
                    $newGame -> pl2_id = $winnerId;
                } else {
                    if ((int)$match['phase_placement'] % 2 === 0) {
                        $newGame -> pl1_id = $winnerId;
                    } else {
                        $newGame -> pl2_id = $winnerId;
                    }
                }

                R::store($newGame);
            }
        }
    }

    private function makeResultRecord($stageId, $userId, $place) {
        $existingRecord = R::findOne('result', ' tournament_stage_id = ? AND user_id = ? ', [$stageId, $userId]);

        if ($existingRecord === null) {
            $resultsRecord = R::dispense('result');

            $resultsRecord['tournament_stage_id'] = $stageId;
            $resultsRecord['user_id'] = $userId;
            $resultsRecord['place'] = $place;
            R::store($resultsRecord);
        } else {
            $existingRecord['place'] = $place;

            R::store($existingRecord);
        }
    }

    public function upsertTournament($data) {
        $tournament = R::load('tournament', $data['id']);

        if (!$tournament) {
            $tournament = R::dispense('tournament');
        }

        $tournament -> discipline = $data['discipline'];
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

    public function removeTournament($data) {
        $tournament = R::load('tournament', $data['id']);

        if (!$tournament) {
            return ['error' => 'Турнир не найден'];
        }

        $tournamentStages = R::find('stage', ' tournament_id = ? ', [$data['id']]);

        if ($tournamentStages) {
            return ['error' => 'Турнир содержит этапы, удалите сначала их'];
        }

        R::trash($tournament);
        return ['status' => 'OK'];
    }

    public function upsertStage($data) {
        $stageId = $data['id'];

        if ($stageId) {
            $stage = R::load('stage', $stageId);

            $stage -> title = $data['title'];
            $stage -> start_date = $data['start_date'];
            $stage -> end_date = $data['end_date'];

            if (isset($data['discipline'])) {
                $stage -> discipline = $data['discipline'];
            }

            if ((int)$stage -> status === 1) {
                $stage -> net_size = $data['net_size'];
                $stage -> net_type = $data['net_type'];
                $stage -> entry_fee = $data['entry_fee'];

                R::store($stage);
                $this -> remakeStageGames($stageId);
                return($stage);
            }

            R::store($stage);
            return($stage);
        } else {
            return $this -> makeNewStage($data);
        }
    }

    private function makeNewStage($data) {
        $stage = R::dispense('stage');

        $tournament = R::load('tournament', $data['tournament_id']);

        if (!$tournament) {
            return ['error' => 'Турнир не найден'];
        }

        if (isset($data['discipline'])) {
            $stage -> discipline = $data['discipline'];
        } else {
            $stage -> discipline = $tournament -> discipline;
        }

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

    public function removeStage($data) {
        $stageId = $data['id'];
        $stage = R::load('stage', $stageId);

        if (!$stage) {
            return ['error' => 'Этап не найден'];
        }

        $handicaps = R::find('stagehandicap', ' stage_id = ? ', [$stageId]);

        if (!is_null($handicaps)) {
            R::trashAll($handicaps);
        }

        $games = R::find('game', ' stage_id = ? ', [$stageId]);

        if (!is_null($games)) {
            R::trashAll($games);
        }

        R::trash($stage);
        return ['status' => 'OK'];
    }

    private function remakeStageGames($stageId) {
        $stage = R::load('stage', $stageId);

        $games = R::find('game', ' stage_id = ? ', [$stageId]);

        if (!is_null($games)) {
            R::trashAll($games);
        }

        switch ($stage -> net_type) {
            case '2-0':
                $this -> buildNet_2_0($stage -> id, $stage -> net_size);
                break;
            case '2-1':
                $this -> buildNet_2_1($stage -> id, $stage -> net_size);
        }
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

    private function makeGame($params, $net_size, $net_type) {
        $game = R::dispense('game');

        $game -> pl1_id = 0;
        $game -> pl2_id = 0;
        $game -> pl1_score = 0;
        $game -> pl2_score = 0;
        $game -> status = 'NOT_STARTED';
        $game -> stage_id = $params['stage_id'];
        $game -> phase_placement = $params['phase_placement'];
        $game -> phase = $params['phase'];

        $phaseType = $params['phase'][0];
        $phaseNo = $params['phase'][1];

        $game -> actions = getPlayersActions(
            $params['phase_placement'],
            $net_size,
            $net_type,
            $phaseType,
            $phaseNo
        );

        R::store($game);

        return game;
    }

    private function buildNet_2_0($stageId, $size) {
        for ($stageNetSize = $size, $phaseNo = 0; $stageNetSize > 1; $phaseNo++) {
            for ($i = 0; $i < $stageNetSize; $i += 2) {
                $this -> makeGame([
                    "stage_id" => $stageId,
                    "phase_placement" => $i / 2,
                    "phase" => 'w' . $phaseNo
                ], $size, '2-0');
            }

            $stageNetSize = $stageNetSize / 2;
        }
    }

    private function buildNet_2_1($stageId, $size) {
        // Виннеры
        for ($i = 0; $i < $size; $i += 2) {
            $this -> makeGame([
                "stage_id" => $stageId,
                "phase_placement" => $i / 2,
                "phase" => 'w0'
            ], $size, '2-1');
        }

        for ($i = 0; $i < $size / 2; $i += 2) {
            $this -> makeGame([
                "stage_id" => $stageId,
                "phase_placement" => $i / 2,
                "phase" => 'w1'
            ], $size, '2-1');
        }

        for ($i = 0; $i < $size / 4; $i += 2) {
            $this -> makeGame([
                "stage_id" => $stageId,
                "phase_placement" => $i / 2,
                "phase" => 'w2'
            ], $size, '2-1');
        }

        // Лузеры
        for ($i = 0; $i < $size / 2; $i += 2) {
            $this -> makeGame([
                "stage_id" => $stageId,
                "phase_placement" => $i / 2,
                "phase" => 'l0'
            ], $size, '2-1');
        }

        for ($i = 0; $i < $size / 2; $i += 2) {
            $this -> makeGame([
                "stage_id" => $stageId,
                "phase_placement" => $i / 2,
                "phase" => 'l1'
            ], $size, '2-1');
        }

        for ($i = 0; $i < $size / 4; $i += 2) {
            $this -> makeGame([
                "stage_id" => $stageId,
                "phase_placement" => $i / 2,
                "phase" => 'l2'
            ], $size, '2-1');
        }

        for ($i = 0; $i < $size / 4; $i += 2) {
            $this -> makeGame([
                "stage_id" => $stageId,
                "phase_placement" => $i / 2,
                "phase" => 'l3'
            ], $size, '2-1');
        }

        // Олимпийка
        for ($stageNetSize = $size / 4, $phaseNo = 0; $stageNetSize > 1; $phaseNo++) {
            for ($i = 0; $i < $stageNetSize; $i += 2) {
                $this -> makeGame([
                    "stage_id" => $stageId,
                    "phase_placement" => $i / 2,
                    "phase" => 'o' . $phaseNo
                ], $size, '2-1');
            }

            $stageNetSize = $stageNetSize / 2;
        }
    }

    public function getRegisteredPlayers($stageId) {
        $registeredIds = R::find('registration', ' stage_id = ? ', [$stageId]);
        $registeredUsers = [];

        foreach ($registeredIds as $key => $registration) {
            $playerId = (int)$registration['player_id'];

            $user = $this -> getFullUser($playerId);
            $handicap = R::findOne('stagehandicap', ' stage_id = ? AND player_id = ? ', [$stageId, $playerId]);

            if ($handicap && isset($handicap['value'])) {
                $user -> handicap = $handicap['value'];
            }

            $registeredUsers[] = $user;
        }

        return $registeredUsers;
    }

    public function registerPlayersToStage($playerIds, $stageId) {
        foreach ($playerIds as $id) {
            $this -> tryRegisterPlayerToStage($id, $stageId);
        }

        return $this -> getRegisteredPlayers($stageId);
    }

    private function tryRegisterPlayerToStage($playerId, $stageId) {
        $stage = R::load('stage', $stageId);

        if (isset($stage['id'])) {
            $player = $this -> getFullUser($playerId);

            if (isset($player['id'])) {
                if (R::findOne('registration', ' player_id = ? AND stage_id = ?', [$playerId, $stageId]) !== null) {
                    return false;
                }

                $registration = R::dispense('registration');

                $registration -> stage_id = $stageId;
                $registration -> player_id = $playerId;

                R::store($registration);
                $this -> setPlayerStageHandicap($playerId, $stage['tournament_id'], $stageId);

                return true;
            }
        }
    }

    public function unregisterPlayerFromStage($playerId, $stageId) {
        $stage = R::load('stage', $stageId);

        if (isset($stage['id'])) {
            $player = $this -> getFullUser($playerId);

            if (isset($player['id'])) {
                $registration = R::findOne('registration', ' player_id = ? AND stage_id = ? ', [$playerId, $stageId]);

                if ($registration) {
                    R::trash($registration);
                }
            }
        }

        return $this -> getRegisteredPlayers($stageId);
    }

    public function closeRegistration($stageId) {
        $stage = R::load('stage', $stageId);
        $gamesQuantity = $stage -> net_size / 2;

        if ((int)$stage['status'] !== 1) {
            return ["error" => "Нельзя начать уже начатый этап"];
        }

        if (isset($stage['id'])) {
            $this -> seedPlayers($stage, $gamesQuantity);
            $stage['status'] = 2;

            R::store($stage);
        }

        return $this -> getTournamentStage($stageId);
    }

    private function seedPlayers($stage, $gamesQuantity) {
        $stageId = $stage['id'];
        $registrations = R::findAll('registration', ' stage_id = ? ', [$stageId]);

        $playerIds = [];

        foreach ($registrations as $key => $registration) {
            $playerIds[] = $registration['player_id'];
        }

        $players = $this -> getUsersRatings($playerIds, $stage['tournament_id']);

        $preservedPlayerIds = [];
        $playerIds = [];

        for ($i = 0; $i < count($players); $i++) {
            if (count($preservedPlayerIds) < 8) {
                $preservedPlayerIds[] = $players[$i]['id'];
            } else {
                $playerIds[] = $players[$i]['id'];
            }
        }

        shuffle($playerIds);

        $playerIds = array_merge($preservedPlayerIds, $playerIds);

        $seedingSpots = json_decode(file_get_contents(__DIR__ . '/seeding_spots.json'), true)[$stage['net_size']];

        for ($i = 0; $i < (int)$stage['net_size']; $i++) {
            if (!isset($playerIds[$i])) {
                break;
            }

            $seedingPoint = $seedingSpots[$i] - 1;

            $targetGameSlot = $seedingPoint % 2;
            $phasePlacement = ($seedingPoint - $targetGameSlot) / 2;

            $game = R::findOne('game', ' phase = "w0" AND phase_placement = ? AND stage_id = ?', [$phasePlacement, $stageId]);

            $game['pl' . ($targetGameSlot + 1) . '_id'] = $playerIds[$i];
            
            R::store($game);
        }

        for ($i = 0; $i < (int)$stage['net_size'] / 2; $i++) {
            $game = R::findOne('game', ' phase = "w0" AND phase_placement = ? AND stage_id = ?', [$i, $stageId]);

            if ($game['pl1_id'] == 0 || $game['pl2_id'] == 0) {
                $this -> finalizeMatch($game['id']);
            }
        }

        R::trashAll($registrations);
    }

    public function closeStage($stageId) {
        $stage = R::load('stage', $stageId);

        if(!$stage) {
            return;
        }

        $this -> calculateStageRatings($stageId, $stage['net_size']);

        $stage['status'] = 3;

        R::store($stage);

        return $stage;
    }

    public function recalculatePeriodRating($from, $to) {
        $stages = R::findAll('stage', ' start_date > ? AND end_date < ? ', [$from, $to]);

        foreach ($stages as $key => $stage) {
            $this -> calculateStageRatings($stage['id'], $stage['net_size']);
        }
    }

    public function calculateTournamentRating($tournamentId) {
        $stages = R::findAll('stage', ' tournament_id = ? ', [$tournamentId]);

        foreach ($stages as $key => $stage) {
            $this -> calculateStageRatings($stage['id'], $stage['net_size']);
        }
    }

    private function calculateStageRatings($stageId, $netSize) {
        $stage = R::load('stage', $stageId);

        if (!$stage) {
            return;
        }

        $tournament = $this -> getTournament($stage['tournament_id']);

        if ((int)$tournament['is_rating'] === 0) {
            return;
        }

        $existingRecords = R::findAll('result', ' tournament_stage_id = ? ', [$stageId]);

        $scoresMap = json_decode(file_get_contents(__DIR__ . '/nehodyachka_points.json'), true)[$netSize];

        foreach ($existingRecords as $key => $result) {
            if (isset($scoresMap[$result['place']])) {
                $result['points'] = $scoresMap[$result['place']];
            } else {
                $placesAvailable = [];

                foreach ($scoresMap as $place => $points) {
                    $placesAvailable[] = $place;
                }

                sort($placesAvailable, SORT_NUMERIC);

                for ($i = 0; $i < count($placesAvailable); $i++) {
                    if (
                        $i === count($placesAvailable) - 1 ||
                        $placesAvailable[$i] === $result['place'] ||
                        ($placesAvailable[$i] < $result['place'] && $placesAvailable[$i + 1] > $result['place'])
                    ) {
                        $result['place'] = $placesAvailable[$i];
                        $result['points'] = $scoresMap[$result['place']];
                        break;
                    }
                }
            }

            R::store($result);

            $this -> recalculateUserPoints($result['user_id'], $stage['tournament_id']);
        }
    }

    private function recalculateUserPoints($userId, $tournamentId) {
        $rating = R::findOne('rating', ' user_id = ? AND tournament_id = ? ', [$userId, $tournamentId]);

        if ($rating === null) {
            $rating = R::dispense('rating');

            $rating['user_id'] = $userId;
            $rating['tournament_id'] = $tournamentId;
        }

        $resultingScore = R::getAssoc('SELECT SUM(points) FROM result WHERE user_id = ? AND tournament_stage_id IN (SELECT id FROM stage WHERE tournament_id = ?)', [$userId, $tournamentId]);

        foreach ($resultingScore as $value) {
            $rating['points'] = $value;
        }

        R::store($rating);

        return $rating;
    }

    public function setPlayerStageHandicap($playerId, $tournamentId, $stageId, $value = null) {
        $tournamentHandicap = R::findOne('tournamenthandicap', ' tournament_id = ? AND player_id = ? ', [$tournamentId, $playerId]);

        if (!$tournamentHandicap) {
            $tournamentHandicap = R::dispense('tournamenthandicap');

            $tournamentHandicap -> tournament_id = $tournamentId;
            $tournamentHandicap -> player_id = $playerId;
        }

        $stageHandicap = R::findOne('stagehandicap', ' stage_id = ? AND player_id = ? ', [$stageId, $playerId]);

        if (!$stageHandicap) {
            $stageHandicap = R::dispense('stagehandicap');

            $stageHandicap -> stage_id = $stageId;
            $stageHandicap -> player_id = $playerId;
        }

        if ($value !== null) {
            $tournamentHandicap -> value = $value;
            R::store($tournamentHandicap);

            $stageHandicap -> value = $value;
            R::store($stageHandicap);
        } else {
            if ($tournamentHandicap['value']) {
                $stageHandicap -> value = $tournamentHandicap['value'];
                R::store($stageHandicap);
            } else {
                $tournamentHandicap -> value = 0;
                R::store($tournamentHandicap);

                $stageHandicap -> value = 0;
                R::store($stageHandicap);
            }
        }

        return $this -> getRegisteredPlayers($stageId);
    }
}
