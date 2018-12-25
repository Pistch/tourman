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

        $this -> registerTask('set-match-score', 'setMatchScore');
        $this -> registerTask('suggest-user', 'findUser');
        $this -> registerTask('register-players-to-stage', 'registerPlayersToStage');

        $this -> registerTask('make-new-tournament', 'makeNewTournament');
        $this -> registerTask('make-new-tournament-stage', 'makeNewTournamentStage');
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

    public function getPlayersTournaments() {
        $plId = JUri::getInstance()->getVar('player');

        return $this -> sendResponse($this -> getModel() -> getPlayersTournaments($plId));
    }

    public function getClosestTournaments() {
        return $this -> sendResponse($this -> getModel() -> getClosestTournaments());
    }

    public function setMatchScore() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> setMatchScore($post));
    }

    public function findUser() {
        $player = JUri::getInstance()->getVar('q');

        return $this -> sendResponse($this -> getModel() -> findUser($player));
    }

    public function makeNewTournament() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> makeNewTournament($post));
    }

    public function makeNewTournamentStage() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> makeNewTournamentStage($post));
    }

    public function registerPlayersToStage() {
        $post = $this -> getPostData();

        return $this -> sendResponse($this -> getModel() -> registerPlayersToStage(json_decode($post['userIds'], true), $post['stageId']));
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
