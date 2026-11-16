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
 * Course page for the "Sections with tabs" format.
 *
 * Included by course/view.php ($course, $displaysection, $PAGE... are defined there).
 *
 * @package    format_grettabs
 * @copyright  2026 Eric Falcon
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/completionlib.php');

$format = course_get_format($course);
$course = $format->get_course();
$context = context_course::instance($course->id);

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    if ($marker == 0) {
        \core_courseformat\formatactions::section($course->id)->remove_all_markers();
    } else {
        $sectioninfo = get_fast_modinfo($course->id)->get_section_info($marker);
        \core_courseformat\formatactions::section($course->id)->set_marker($sectioninfo, true);
    }
}

// Make sure section 0 is created.
course_create_sections_if_missing($course, 0);

// Like a "one section per tab" format, the course home only shows the general section.
// In editing mode the full list is kept so that sections can still be added and moved.
if (is_null($displaysection) && !empty($course->gtabshome) && !$PAGE->user_is_editing()) {
    $displaysection = 0;
}

$renderer = $PAGE->get_renderer('format_grettabs');

if (!is_null($displaysection)) {
    $format->set_sectionnum($displaysection);
}
$outputclass = $format->get_output_classname('content');
$widget = new $outputclass($format);
echo $renderer->render($widget);
