<?php
/**
 * @package    tourman
 *
 * @author     pistch <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

/**
 * Tourman Controller.
 *
 * @package  tourman
 * @since    1.0
 */
class TourmanControllerTourman extends AdminController
{
    public function getModel($name = 'Tourman', $prefix = 'TourmanModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}
