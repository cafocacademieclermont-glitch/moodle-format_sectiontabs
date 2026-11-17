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
 * Page to define the custom tabs of a course.
 *
 * @package    format_sectiontabs
 * @copyright  2026 Eric Falcon
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

$courseid = required_param('id', PARAM_INT);

$course = get_course($courseid);
require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/course:update', $context);

/** @var format_sectiontabs $format */
$format = course_get_format($course);
$courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);
if ($format->get_format() !== 'sectiontabs') {
    redirect($courseurl);
}

$url = new moodle_url('/course/format/sectiontabs/tabs.php', ['id' => $course->id]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('managetabs', 'format_sectiontabs') . moodle_page::TITLE_SEPARATOR . $course->shortname);
$PAGE->set_heading(format_string($course->fullname, true, ['context' => $context]));

// Sections of the course (subsections are not listed: they are displayed as tabs inside their section).
$sectionnames = [];
$sections = [];
$hiddenstr = get_string('hiddenfromstudents', 'format_sectiontabs');
foreach (get_fast_modinfo($course)->get_listed_section_info_all() as $section) {
    $name = html_entity_decode(strip_tags($format->get_section_name($section)), ENT_QUOTES, 'UTF-8');
    $sectionnames[$section->id] = $name;
    $sections[$section->id] = $name . ($section->visible ? '' : " ($hiddenstr)");
}

// Current tabs; when none are defined yet, propose one tab per section.
$tabs = $format->get_custom_tabs();
if (!$tabs) {
    foreach ($sectionnames as $sectionid => $name) {
        $tabs[] = ['label' => $name, 'sectionids' => [$sectionid], 'url' => ''];
    }
}

$form = new \format_sectiontabs\form\tabs_form($url, [
    'courseid' => $course->id,
    'sections' => $sections,
    'repeats' => count($tabs),
]);

if ($form->is_cancelled()) {
    redirect($courseurl);
}

$newtabs = $form->get_tabs();
if ($newtabs !== null) {
    $format->set_custom_tabs($newtabs);
    // Saving the tabs means the teacher wants to use them.
    if ((int)($format->get_course()->gtabsmode ?? 0) !== format_sectiontabs::TABS_CUSTOM) {
        $format->update_course_format_options(['gtabsmode' => format_sectiontabs::TABS_CUSTOM]);
        rebuild_course_cache($course->id, true);
    }
    redirect($courseurl, get_string('tabssaved', 'format_sectiontabs'), null, \core\output\notification::NOTIFY_SUCCESS);
}

$defaults = ['id' => $course->id, 'tablabel' => [], 'tabsections' => [], 'taburl' => [], 'tabposition' => []];
foreach (array_values($tabs) as $i => $tab) {
    $defaults['tablabel'][$i] = $tab['label'];
    $defaults['tabsections'][$i] = $tab['sectionids'];
    $defaults['taburl'][$i] = $tab['url'];
    $defaults['tabposition'][$i] = $i + 1;
}
$form->set_data($defaults);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managetabs', 'format_sectiontabs'));
$form->display();
echo $OUTPUT->footer();
