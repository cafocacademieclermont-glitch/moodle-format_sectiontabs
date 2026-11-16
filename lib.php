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
 * Main class of the "Sections with tabs" course format.
 *
 * It behaves exactly like the core "Custom sections" (topics) format and adds
 * an automatic tab bar, built from the course sections, above every course page.
 *
 * @package    format_grettabs
 * @copyright  2026 Eric Falcon
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/lib.php');
require_once($CFG->dirroot . '/course/format/topics/lib.php');

use core\lang_string;

/**
 * Course format class.
 */
class format_grettabs extends format_topics {

    /**
     * Course format options: the topics ones plus the tab bar options.
     *
     * @param bool $foreditform
     * @return array
     */
    public function course_format_options($foreditform = false) {
        $options = parent::course_format_options($foreditform);

        $options['gtabshome'] = [
            'default' => 1,
            'type' => PARAM_BOOL,
        ];
        $options['gtabsshortnames'] = [
            'default' => 0,
            'type' => PARAM_BOOL,
        ];

        if ($foreditform) {
            $options['gtabshome'] += [
                'label' => new lang_string('gtabshome', 'format_grettabs'),
                'help' => 'gtabshome',
                'help_component' => 'format_grettabs',
                'element_type' => 'advcheckbox',
            ];
            $options['gtabsshortnames'] += [
                'label' => new lang_string('gtabsshortnames', 'format_grettabs'),
                'help' => 'gtabsshortnames',
                'help_component' => 'format_grettabs',
                'element_type' => 'advcheckbox',
            ];
        }
        return $options;
    }

    /**
     * URL of the tab of a section.
     *
     * @param section_info $section
     * @return moodle_url
     */
    public function get_tab_url(section_info $section): moodle_url {
        $course = $this->get_course();
        if ($section->section == 0) {
            $params = ['id' => $course->id];
            if (empty($course->gtabshome)) {
                $params['section'] = 0;
            }
            return new moodle_url('/course/view.php', $params);
        }
        return new moodle_url('/course/section.php', ['id' => $section->id]);
    }
}

/**
 * Implements callback inplace_editable() allowing to edit values in-place.
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return \core\output\inplace_editable
 */
function format_grettabs_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        $section = $DB->get_record_sql(
            'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
            [$itemid, 'grettabs'],
            MUST_EXIST
        );
        return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
}
