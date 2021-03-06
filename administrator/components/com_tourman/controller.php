<?php
/**
 * @package    tourman
 *
 * @author     pistch <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

/**
 * Tourman Controller.
 *
 * @package  tourman
 * @since    1.0
 */
class TourmanController extends BaseController
{
    function __construct($params = []) {
        parent::__construct($params);

        $this -> registerTask('get-tournament', 'getTournament');
        $this -> registerTask('get-tournaments', 'getTournaments');
        $this -> registerTask('get-stage', 'getStage');
        $this -> registerTask('get-players-tournaments', 'getPlayersTournaments');
        $this -> registerTask('get-closest-tournaments', 'getClosestTournaments');
        $this -> registerTask('get-ratings', 'getRatings');
        $this -> registerTask('get-dued-games', 'getDuedGames');

        $this -> registerTask('set-match-score', 'setMatchScore');
        $this -> registerTask('set-player-stage-handicap', 'setPlayerStageHandicap');
        $this -> registerTask('start-match', 'startMatch');
        $this -> registerTask('finalize-match', 'finalizeMatch');

        $this -> registerTask('suggest-user', 'findUser');
        $this -> registerTask('upsert-user', 'upsertUser');
        $this -> registerTask('register-players-to-stage', 'registerPlayersToStage');
        $this -> registerTask('unregister-player-from-stage', 'unregisterPlayerFromStage');

        $this -> registerTask('upsert-tournament', 'upsertTournament');
        $this -> registerTask('remove-tournament', 'removeTournament');
        $this -> registerTask('upsert-stage', 'upsertStage');
        $this -> registerTask('remove-stage', 'removeStage');
        $this -> registerTask('close-registration', 'closeRegistration');
        $this -> registerTask('close-stage', 'closeStage');

        $this -> registerTask('recalculate-rating-by-period', 'recalculatePeriodRating');
        $this -> registerTask('swap-player', 'swapPlayer');
        $this -> registerTask('reset-game', 'resetGame');
    }

    public function getModel($name = 'Tourman', $prefix = 'TourmanModel', $config = array()) {
        return parent::getModel($name, $prefix, $config);
    }
    public function getTournament() {
        $tId = JUri::getInstance()->getVar('tournament');

        return $this -> sendResponse($this->getModel()->getTournament($tId));
    }

    public function getTournaments() {
        return $this -> sendResponse($this -> getModel() -> getTournaments());
    }

    public function getStage() {
        $sId = JUri::getInstance()->getVar('stage');

        return $this -> sendResponse($this -> getModel() -> getTournamentStage($sId));
    }

    public function getDuedGames() {
        return $this -> sendResponse($this -> getModel() -> getDuedGames());
    }

    public function getPlayersTournaments() {
        $plId = JUri::getInstance()->getVar('player');

        return $this -> sendResponse($this -> getModel() -> getPlayersTournaments($plId));
    }

    public function getRatings() {
        $tournamentId = JUri::getInstance()->getVar('tournamentId') || 0;
        $offset = JUri::getInstance()->getVar('offset') || 0;
        $limit = JUri::getInstance()->getVar('limit') || 30;

        return $this -> sendResponse($this -> getModel() -> getRatings($limit, $offset, $tournamentId));
    }

    public function getClosestTournaments() {
        return $this -> sendResponse($this -> getModel() -> getClosestTournaments());
    }

    public function setMatchScore() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> setMatchScore($post));
    }

    public function startMatch() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> startMatch($post));
    }

    public function upsertUser() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> upsertUser($post));
    }

    public function finalizeMatch() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> finalizeMatch($post['matchId'], $post['winnerPhasePlacement']));
    }

    public function findUser() {
        $player = JUri::getInstance()->getVar('q');

        return $this -> sendResponse($this -> getModel() -> findUser($player));
    }

    public function upsertTournament() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> upsertTournament($post));
    }

    public function removeTournament() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> removeTournament($post));
    }

    public function upsertStage() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> upsertStage($post));
    }

    public function removeStage() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> removeStage($post));
    }

    public function registerPlayersToStage() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> registerPlayersToStage(json_decode($post['userIds'], true), $post['stageId']));
    }

    public function unregisterPlayerFromStage() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> unregisterPlayerFromStage($post['userId'], $post['stageId']));
    }

    public function closeRegistration() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> closeRegistration($post['stageId']));
    }

    public function closeStage() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> closeStage($post['stageId']));
    }

    public function recalculatePeriodRating() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> recalculatePeriodRating($post['from'], $post['to']));
    }

    public function setPlayerStageHandicap() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> setPlayerStageHandicap($post['player_id'], $post['tournament_id'], $post['stage_id'], $post['value']));
    }

    public function resetGame() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> resetGame($post['game_id']));
    }

    public function swapPlayer() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> swapPlayer($post['stage_id'], $post['wrong_player_id'], $post['right_player_id']));
    }


    private function sendResponse($arr) {
        header('Content-Type: application/json');
        echo(json_encode($arr, JSON_UNESCAPED_UNICODE));
        JFactory::getApplication() -> close();
    }

    private function getPostData() {
        return JFactory::getApplication() -> input -> getArray();
    }
}
