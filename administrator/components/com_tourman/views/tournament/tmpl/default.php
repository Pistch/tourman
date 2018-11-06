<?php
/**
 * @package    tourman
 *
 * @author     pistch <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

?>
<div id="j-main-container" class="span10">
    <style>
        .tour_table td {
            border: 1px black solid;
            border-collapse: collapse;
            padding: 10px;
        }
    </style>
    <h2><?php echo($this->tournament['title']) ?></h2>
    <table class="tour_table">
        <tr>
            <td>Название</td>
            <td>Время проведения</td>
            <td>Сетка</td>
        </tr>
        <?php
        foreach ($this->stages as $stage) {
            $fields = ['title', 'dates', 'net'];

            echo('<tr>');
            foreach ($fields as $field) {
                echo('<td>');
                switch ($field) {
                    case 'net':
                        $net_type = $stage['net_type'];
                        $net_size = $stage['net_size'];
                        echo("$net_type на $net_size игроков");
                        break;
                    case 'title':
                        $title = $stage['title'];
                        $sId = $stage['id'];
                        echo("<a href='index.php?option=com_tourman&view=stage&stage=$sId'>$title</a>");
                        break;
                    case 'dates':
                        $start = $stage['start_date'];
                        $end = $stage['end_date'];
                        echo("$start - $end");
                        break;
                    default:
                        echo($stage[$field]);
                }
                echo('</td>');
            }
            echo('</tr>');
        }
        ?>
    </table>
</div>
