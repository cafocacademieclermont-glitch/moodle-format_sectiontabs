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
                // Restricted for this user but displayed with its restriction message.
                'unavailable' => !$cm->uservisible || !$delegated->uservisible,
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
     * Build the tab bar and the section menu.
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
        $current = (int)($format->get_sectionnum() ?? 0);
        $shortnames = !empty($course->gtabsshortnames);

        // All the sections the user can see, in course order, by section number.
        $sections = [];
        // Section numbers by normalised name, for the custom tabs (first section wins).
        $bynames = [];
        foreach ($modinfo->get_listed_section_info_all() as $section) {
            $fullname = $format->get_section_name($section);
            foreach ([$fullname, (string)$section->name] as $name) {
                $key = \format_grettabs::normalise_name($name);
                if ($key !== '' && !isset($bynames[$key])) {
                    $bynames[$key] = (int)$section->section;
                }
            }
            if (!$format->is_section_visible($section)) {
                continue;
            }
            // Hidden sections never appear for users who cannot see hidden sections,
            // even when the course shows them as unavailable.
            if (!$section->visible && !$canviewhidden) {
                continue;
            }
            $name = $fullname;
            if ($shortnames) {
                // "Bloc de compétence 1 : Actes essentiels" becomes "Bloc de compétence 1".
                $parts = preg_split('/\s+:\s+/u', $fullname, 2);
                if (count($parts) === 2 && trim($parts[0]) !== '') {
                    $name = $parts[0];
                }
            }
            $sections[(int)$section->section] = [
                'name' => $name,
                'fullname' => $fullname,
                'title' => html_entity_decode(strip_tags($fullname), ENT_QUOTES, 'UTF-8'),
                'url' => $format->get_tab_url($section)->out(false),
                'active' => ((int)$section->section === $current),
                'hidden' => !$section->visible,
                'unavailable' => !$section->uservisible,
            ];
        }

        if ((int)($course->gtabsmode ?? 0) === \format_grettabs::TABS_CUSTOM) {
            $tabs = $this->export_custom_tabs($sections, $bynames, $current);
            $hastabs = !empty($tabs);
        } else {
            $tabs = array_values($sections);
            $hastabs = count($tabs) >= 2;
        }
        $leftmenu = !empty($course->gtabsleftmenu) && count($sections) >= 2;

        if (!$hastabs && !$leftmenu) {
            return null;
        }

        // Colours are validated hexadecimal values.
        return [
            'tabs' => $hastabs ? $tabs : [],
            'hastabs' => $hastabs,
            'sections' => array_values($sections),
            'leftmenu' => $leftmenu,
            'style' => '--gtabs-active:' . $format->get_tab_colour('activecolor') .
                ';--gtabs-active-border:' . $format->get_tab_colour('activeborder') . ';',
        ];
    }

    /**
     * Build the tabs defined by the teacher.
     *
     * @param array $sections visible sections data, by section number
     * @param array $bynames section numbers by normalised name
     * @param int $current section displayed on this page
     * @return array
     */
    protected function export_custom_tabs(array $sections, array $bynames, int $current): array {
        $course = $this->format->get_course();
        $context = $this->format->get_context();
        $parsed = \format_grettabs::parse_custom_tabs((string)($course->gtabscustom ?? ''));

        $tabs = [];
        foreach ($parsed['tabs'] as $definition) {
            $label = format_string($definition['label'], true, ['context' => $context]);
            $tab = [
                'name' => $label,
                'fullname' => $label,
                'title' => html_entity_decode(strip_tags($label), ENT_QUOTES, 'UTF-8'),
                'active' => false,
                'hidden' => false,
                'unavailable' => false,
            ];
            if ($definition['url'] !== null) {
                $tab['url'] = $definition['url'];
                $tab['external'] = true;
                $tabs[] = $tab;
                continue;
            }
            // Sections targeted by the tab that this user can see.
            $targets = [];
            foreach ($definition['sections'] as $name) {
                $num = $bynames[\format_grettabs::normalise_name($name)] ?? null;
                if ($num !== null && isset($sections[$num])) {
                    $targets[$num] = $sections[$num];
                }
            }
            if (!$targets) {
                continue;
            }
            // The tab opens the first section the user can access.
            $available = array_filter($targets, fn($target) => !$target['unavailable']);
            $first = reset($available) ?: reset($targets);
            $tab['url'] = $first['url'];
            $tab['active'] = isset($targets[$current]);
            $tab['unavailable'] = empty($available);
            $tab['hidden'] = !array_filter($targets, fn($target) => !$target['hidden']);
            $tabs[] = $tab;
        }
        return $tabs;
    }
}
