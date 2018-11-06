<?php
/**
 * @package    tourman
 *
 * @author     pistch <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * Tourman view.
 *
 * @package  tourman
 * @since    1.0
 */
class TourmanViewTournament extends HtmlView
{
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise a JError object.
     *
     * @see     fetch()
     * @since   1.0
     */
    public function display($tpl = null)
    {
        // Show the toolbar
        $this->toolbar();

        $tId = JUri::getInstance()->getVar('tournament');
        $this->tournament = $this->getModel()->getTournament($tId);
        $this->stages = $this->tournament['stages'];

        // Display it all
        return parent::display($tpl);
    }

    /**
     * Displays a toolbar for a specific page.
     *
     * @return  void.
     *
     * @since   1.0
     */
    private function toolbar()
    {
        JToolBarHelper::title(Text::_('COM_TOURMAN'), '');

        // Options button.
        if (Factory::getUser()->authorise('core.admin', 'com_tourman'))
        {
            JToolBarHelper::preferences('com_tourman');
        }
    }
}
