<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Site settings of the format.
 *
 * @package    format_grettabs
 * @copyright  2026 Eric Falcon
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcolourpicker(
        'format_grettabs/activecolor',
        new lang_string('activecolor', 'format_grettabs'),
        new lang_string('activecolor_desc', 'format_grettabs'),
        '#ecb21e'
    ));
    $settings->add(new admin_setting_configcolourpicker(
        'format_grettabs/activeborder',
        new lang_string('activeborder', 'format_grettabs'),
        new lang_string('activeborder_desc', 'format_grettabs'),
        '#2b1a10'
    ));
}
