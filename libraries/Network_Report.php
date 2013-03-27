<?php

/**
 * Network report class.
 *
 * @category   Apps
 * @package    Network_Report
 * @subpackage Libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/network_report/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\network_report;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('base');
clearos_load_language('network');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\network\Iface_Manager as Iface_Manager;
use \clearos\apps\network\Network_Stats as Network_Stats;
use \clearos\apps\reports_database\Database_Report as Database_Report;

clearos_load_library('network/Iface_Manager');
clearos_load_library('network/Network_Stats');
clearos_load_library('reports_database/Database_Report');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Network report class.
 *
 * @category   Apps
 * @package    Network_Report
 * @subpackage Libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/network_report/
 */

class Network_Report extends Database_Report
{
    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Network report constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);

        parent::__construct();
    }

    /**
     * Returns load summary data.
     *
     * @param string $iface interface
     * @param string $range range information
     *
     * @return array load summary data
     * @throws Engine_Exception
     */

    public function get_interface_data($iface, $range = 'today')
    {
        clearos_profile(__METHOD__, __LINE__);

        // Get report data
        //----------------

        $sql['timeline_select'] = array('rx_rate', 'tx_rate');
        $sql['timeline_from'] = 'network';
        $sql['timeline_where'] = 'iface = \'' . $iface . '\'';

        $options['range'] = $range;

        $entries = $this->_run_query('network', $sql, $options);

        // Format report data
        //-------------------

        $report_data = $this->_get_data_info($iface);

        foreach ($entries as $entry) {
            $report_data['data'][] = array(
                $entry['timestamp'], 
                (int) round(8 * ($entry['rx_rate'] / 1024)),
                (int) round(8 * ($entry['tx_rate'] / 1024))
            );
        }

        return $report_data;
    }

    /**
     * Inserts network data into database.
     *
     * @return void
     * @throws Engine_Exception
     */

    public function insert_data()
    {
        clearos_profile(__METHOD__, __LINE__);

        // Initialize
        //-----------

        $this->_initialize_tables('network_report', 'network');

        // Get stats
        //----------

        $network_stats = new Network_Stats();
        $stats = $network_stats->get_interface_stats_and_rates();

        // Insert report data
        //-------------------

        foreach ($stats as $iface => $details) { 
            $sql['insert'] = "network (`iface`, `rx_bytes`, `rx_packets`, `rx_errors`, `rx_drop`, `rx_rate`, " .
                "`tx_bytes`, `tx_packets`, `tx_errors`, `tx_drop`, `tx_rate`)";

            $sql['values']
                = "'" . $iface . "'," .
                $details['rx_bytes'] . ',' .
                $details['rx_packets'] . ',' .
                $details['rx_errors'] . ',' .
                $details['rx_drop'] . ',' .
                $details['rx_rate'] . ',' .
                $details['tx_bytes'] . ',' .
                $details['tx_packets'] . ',' .
                $details['tx_errors'] . ',' .
                $details['tx_drop'] . ',' .
                $details['tx_rate']; 

            $this->_run_insert('network', $sql);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////
    // P R I V A T E   M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Report engine definition.
     *
     * @return array report definition
     */
    
    protected function _get_definition()
    {
        $iface_manager = new Iface_Manager();

        $ifaces = $iface_manager->get_interfaces();

        foreach ($ifaces as $iface) {
            $reports[$iface] = array(
                'app' => 'network_report',
                'title' => lang('network_interface') . ' - ' . $iface,
                'basename' => 'iface',
                'api_data' => 'get_interface_data',
                'key_value' => $iface,
                'chart_type' => 'timeline',
                'format' => array(
                    'series_label' => lang('base_kilobits_per_second'),
                    'baseline_format' => 'timestamp',
                ),
                'headers' => array(
                    lang('base_date'),
                    lang('network_received'),
                    lang('network_transmitted'),
                ),
                'types' => array(
                    'timestamp',
                    'int',
                    'int'
                ),
            );
        }

        // Done
        //-----

        return $reports;
    }
}
