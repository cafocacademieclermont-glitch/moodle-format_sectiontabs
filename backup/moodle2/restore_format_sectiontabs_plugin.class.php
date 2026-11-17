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
 * Restore of the "Tabs and section menu" format.
 *
 * @package    format_sectiontabs
 * @copyright  2026 Eric Falcon
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Restore plugin: the custom tabs refer to section ids, which change when a course is restored,
 * duplicated or imported. They are mapped to the new sections after the restore.
 */
class restore_format_sectiontabs_plugin extends restore_format_plugin {
    /**
     * A dummy path element is needed for after_restore_course() to be called.
     *
     * @return restore_path_element[]
     */
    public function define_course_plugin_structure() {
        return [new restore_path_element('dummy_course', $this->get_pathfor('/dummycourse'))];
    }

    /**
     * Nothing to do for the dummy element.
     */
    public function process_dummy_course() {
    }

    /**
     * Map the section ids of the custom tabs to the restored sections.
     */
    public function after_restore_course() {
        $courseid = $this->step->get_task()->get_courseid();
        $format = course_get_format($courseid);
        if (!$format instanceof format_sectiontabs) {
            return;
        }
        $tabs = $format->get_custom_tabs();
        if (!$tabs) {
            return;
        }
        foreach ($tabs as $i => $tab) {
            $newids = [];
            foreach ($tab['sectionids'] as $oldid) {
                $newid = $this->get_mappingid('course_section', $oldid);
                if ($newid) {
                    $newids[] = (int)$newid;
                }
            }
            $tabs[$i]['sectionids'] = $newids;
        }
        $format->set_custom_tabs($tabs);
    }
}
