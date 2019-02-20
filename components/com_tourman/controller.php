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
        $this -> registerTask('get-tournament-rating', 'getTournamentRating');
        $this -> registerTask('get-tournaments', 'getTournaments');
        $this -> registerTask('get-stage', 'getStage');
        $this -> registerTask('get-players-tournaments', 'getPlayersTournaments');
        $this -> registerTask('get-closest-tournaments', 'getClosestTournaments');
    }

    public function getModel($name = 'Tourman', $prefix = 'TourmanModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function getTournament() {
        $tId = JUri::getInstance()->getVar('tournament');

        return $this -> sendResponse($this->getModel()->getTournament($tId));
    }

    public function getTournamentRating() {
        $tId = JUri::getInstance()->getVar('tournament');

        return $this -> sendResponse($this->getModel()->getTournamentRating($tId));
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

    private function sendResponse($arr) {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo(json_encode($arr, JSON_UNESCAPED_UNICODE));
        JFactory::getApplication() -> close();
    }
}

