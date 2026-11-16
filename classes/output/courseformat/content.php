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
        return $data;
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

        // Section shown on this page (the general section on the course home).
        $current = $format->get_sectionnum() ?? 0;
        $shortnames = !empty($course->gtabsshortnames);

        $tabs = [];
        foreach ($modinfo->get_listed_section_info_all() as $section) {
            if (!$format->is_section_visible($section)) {
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
