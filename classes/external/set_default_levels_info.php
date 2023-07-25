<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * External function.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\external;

use block_xp\di;

/**
 * External function.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_default_levels_info extends external_api {

    /**
     * External function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'levels' => new external_multiple_structure(new external_single_structure([
                'level' => new external_value(PARAM_INT),
                'xprequired' => new external_value(PARAM_INT),
                'name' => new external_value(PARAM_NOTAGS, '', VALUE_DEFAULT, ''),
                'description' => new external_value(PARAM_NOTAGS, '', VALUE_DEFAULT, ''),
            ])),
            'algo' => new external_single_structure([
                'enabled' => new external_value(PARAM_BOOL),
                'base' => new external_value(PARAM_INT),
                'coef' => new external_value(PARAM_FLOAT),
            ])
        ]);
    }

    /**
     * Allow AJAX use.
     *
     * @return true
     */
    public static function execute_is_allowed_from_ajax() {
        return true;
    }

    /**
     * External function.
     *
     * @param int $courseid The course ID.
     * @return object
     */
    public static function execute($levels, $algo) {
        global $USER;
        $params = self::validate_parameters(self::execute_parameters(), compact('levels', 'algo'));
        extract($params); // @codingStandardsIgnoreLine

        // Permission checks.
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        $levelsinfo = set_levels_info::clean_levels_info_data($levels, $algo);
        $config = di::get('config');
        $config->set('levelsdata', json_encode($levelsinfo->jsonSerialize()));

        return (object) ['success' => true];
    }

    /**
     * External function return definition.
     *
     * @return external_description
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL)
        ]);
    }

}
