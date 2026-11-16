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

namespace format_grettabs\output\courseformat;

use format_topics\output\courseformat\content as content_topics;
use renderer_base;

/**
 * Course content output: the topics content plus the tab bar.
 *
 * @package    format_grettabs
 * @copyright  2026 Eric Falcon
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content extends content_topics {

    /**
     * Export for template.
     *
     * @param renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = parent::export_for_template($output);
        $data->gtabs = $this->export_tabs();
        $data->gsubtabs = $this->export_subtabs();
        return $data;
    }

    /**
     * Build the tabs of the subsections of the displayed section.
     *
     * Only on a single section page. In editing mode an extra "Show all" tab
     * displays every subsection so that they can be moved.
     *
     * @return array|null null when there is nothing to show
     */
    protected function export_subtabs(): ?array {
        /** @var \format_grettabs $format */
        $format = $this->format;
        $course = $format->get_course();
        $sectionnum = $format->get_sectionnum();
        if (empty($course->gtabssubtabs) || $sectionnum === null) {
            return null;
        }
        $modinfo = $format->get_modinfo();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $format->get_context());
        $subtabs = [];
        foreach ($modinfo->sections[$sectionnum] ?? [] as $cmid) {
            $cm = $modinfo->get_cm($cmid);
            $delegated = $cm->get_delegated_section_info();
            if (!$delegated || !$cm->is_visible_on_course_page()) {
                continue;
            }
            // Hidden subsections never appear for users who cannot see hidden sections.
            if ((!$cm->visible || !$delegated->visible) && !$canviewhidden) {
                continue;
            }
            $fullname = $format->get_section_name($delegated);
            $subtabs[] = [
                'cmid' => $cm->id,
                'num' => $delegated->section,
                'name' => $fullname,
                'title' => html_entity_decode(strip_tags($fullname), ENT_QUOTES, 'UTF-8'),
                'hidden' => !$cm->visible,
                'active' => empty($subtabs),
            ];
        }
        if (empty($subtabs)) {
            return null;
        }
        return [
            'uniqid' => \html_writer::random_id('gsubtabs'),
            'subtabs' => $subtabs,
            'editing' => $format->show_editor(),
            'style' => '--gtabs-active:' . $format->get_tab_colour('activecolor') . ';',
        ];
    }

    /**
     * Build the tab bar from the course sections.
     *
     * @return array|null null when there is nothing to show
     */
    protected function export_tabs(): ?array {
        /** @var \format_grettabs $format */
        $format = $this->format;
        $course = $format->get_course();
        $modinfo = $format->get_modinfo();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $format->get_context());

        // Section shown on this page (the general section on the course home).
        $current = $format->get_sectionnum() ?? 0;
        $shortnames = !empty($course->gtabsshortnames);

        $tabs = [];
        foreach ($modinfo->get_listed_section_info_all() as $section) {
            if (!$format->is_section_visible($section)) {
                continue;
            }
            // Hidden sections never appear for users who cannot see hidden sections,
            // even when the course shows them as unavailable.
            if (!$section->visible && !$canviewhidden) {
                continue;
            }
            $fullname = $format->get_section_name($section);
            $name = $fullname;
            if ($shortnames) {
                // "Bloc de compétence 1 : Actes essentiels" becomes "Bloc de compétence 1".
                $parts = preg_split('/\s+:\s+/u', $fullname, 2);
                if (count($parts) === 2 && trim($parts[0]) !== '') {
                    $name = $parts[0];
                }
            }
            $tabs[] = [
                'name' => $name,
                'fullname' => $fullname,
                'title' => html_entity_decode(strip_tags($fullname), ENT_QUOTES, 'UTF-8'),
                'url' => $format->get_tab_url($section)->out(false),
                'active' => ((int)$section->section === (int)$current),
                'hidden' => !$section->visible,
                'unavailable' => !$section->uservisible,
            ];
        }

        if (count($tabs) < 2) {
            return null;
        }

        // Colours are validated hexadecimal values.
        return [
            'tabs' => $tabs,
            'leftmenu' => !empty($course->gtabsleftmenu),
            'style' => '--gtabs-active:' . $format->get_tab_colour('activecolor') .
                ';--gtabs-active-border:' . $format->get_tab_colour('activeborder') . ';',
        ];
    }
}
