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

require_once(__DIR__ . '/db.php');

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

    public function getTournamentRating($id) {
        $ratings = R::find('rating', ' tournament_id = ? ORDER BY `points` DESC ', [$id]);

        $result = [];
        foreach ($ratings as $key => $ratingRecord) {
            $ratingRecord['user_name'] = $this -> getUser($ratingRecord['user_id']);
            $result[] = $ratingRecord;
        }

        return $result;
    }

    public function getTournamentStages($tournamentID) {
        return R::find('stage', ' tournament_id = ? ', [ $tournamentID ]);
    }

    public function getTournamentStage($stageID, $short = false) {
        $stage = R::load('stage', $stageID);

        if (!$short) {
            $stage['games'] = $this -> getStageGames($stageID);
            $stage['results'] = $this -> getStageResults($stageID);
        }

        return $stage;
    }

    public function getStageGames($stageID) {
        $games = R::find('game', ' stage_id = ? ORDER BY phase_placement ASC ', [ $stageID ]);

        foreach ($games as $key => $game) {
            $games[$key]['user1'] = $this -> getUser($game['pl1_id']);
            $games[$key]['user1_handicap'] = $this -> getUserStageHandicap($game['pl1_id'], $stageID);
            $games[$key]['user2'] = $this -> getUser($game['pl2_id']);
            $games[$key]['user2_handicap'] = $this -> getUserStageHandicap($game['pl2_id'], $stageID);
        }

        return $games;
    }

    private function getStageResults($stageID) {
        $results = R::findAll('result', ' tournament_stage_id = ? ', [$stageID]);
        $usersResults = [];

        foreach ($results as $result) {
            $usersResults[] = [
                'user' => $this -> getUser($result['user_id']),
                'place' => $result['place']
            ];
        }

        return $usersResults;
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

    public function getPlayersTournaments($userID) {
        $stagesResults = R::find('user', ' user_id = ? ORDER BY id DESC', [$userID]);

        foreach ($stagesResults as $key => $stageResult) {
            $stagesResults[$key]['stage'] = $this -> getTournamentStage($stageResult['stage_id'], true);
        }

        return $stagesResults;
    }

    public function getClosestTournaments() {
        $today = date('Y-m-d');
        return R::find('tournament', ' end_date > ? ORDER BY start_date ASC LIMIT 3', [$today]);
    }

    public function getRatings() {
        R::find('rating');
    }
}
