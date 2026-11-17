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

namespace format_sectiontabs\output\courseformat;

use format_topics\output\courseformat\content as content_topics;
use renderer_base;

/**
 * Course content output: the topics content plus the tab bar.
 *
 * @package    format_sectiontabs
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
        $data->gnav = $this->export_navigation();
        if ($data->gnav) {
            // The previous / next bar replaces the core section navigation and section selector.
            unset($data->sectionnavigation, $data->sectionselector);
        }
        return $data;
    }

    /**
     * Previous / next section of the displayed section, among the sections the user can access.
     *
     * The subsection tabs script turns these links into previous / next subsection links
     * while there are subsections left.
     *
     * @return array|null null when the bar is disabled or there is no single section
     */
    protected function export_navigation(): ?array {
        /** @var \format_sectiontabs $format */
        $format = $this->format;
        $course = $format->get_course();
        $current = $format->get_sectionnum();
        if (empty($course->gtabsnav) || $current === null) {
            return null;
        }
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $format->get_context());
        $list = [];
        foreach ($format->get_modinfo()->get_listed_section_info_all() as $section) {
            if (!$section->uservisible || (!$section->visible && !$canviewhidden)) {
                continue;
            }
            $list[] = $section;
        }
        $index = null;
        foreach ($list as $i => $section) {
            if ((int)$section->section === (int)$current) {
                $index = $i;
            }
        }
        if ($index === null) {
            return null;
        }
        $link = function (?\section_info $section) use ($format): ?array {
            if (!$section) {
                return null;
            }
            $name = $format->get_section_name($section);
            return [
                'title' => html_entity_decode(strip_tags($name), ENT_QUOTES, 'UTF-8'),
                'url' => $format->get_tab_url($section)->out(false),
            ];
        };
        return [
            'prev' => $link($list[$index - 1] ?? null),
            'next' => $link($list[$index + 1] ?? null),
            'style' => $format->get_tab_style(),
        ];
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
        /** @var \format_sectiontabs $format */
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
            'style' => $format->get_tab_style(),
        ];
    }

    /**
     * Build the tab bar and the section menu.
     *
     * @return array|null null when there is nothing to show
     */
    protected function export_tabs(): ?array {
        /** @var \format_sectiontabs $format */
        $format = $this->format;
        $course = $format->get_course();
        $modinfo = $format->get_modinfo();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $format->get_context());

        // Section shown on this page (the general section on the course home).
        $current = (int)($format->get_sectionnum() ?? 0);
        $shortnames = !empty($course->gtabsshortnames);

        // All the sections the user can see, in course order, by section id.
        $sections = [];
        foreach ($modinfo->get_listed_section_info_all() as $section) {
            $fullname = $format->get_section_name($section);
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
                // Short name: "Bloc de compétence 1 : Actes essentiels" becomes "Bloc de compétence 1".
                $parts = preg_split('/\s+:\s+/u', $fullname, 2);
                if (count($parts) === 2 && trim($parts[0]) !== '') {
                    $name = $parts[0];
                }
            }
            $sections[(int)$section->id] = [
                'name' => $name,
                'fullname' => $fullname,
                'title' => html_entity_decode(strip_tags($fullname), ENT_QUOTES, 'UTF-8'),
                'url' => $format->get_tab_url($section)->out(false),
                'active' => ((int)$section->section === $current),
                'hidden' => !$section->visible,
                'unavailable' => !$section->uservisible,
            ];
        }

        $customtabs = $format->get_custom_tabs();
        if ((int)($course->gtabsmode ?? 0) === \format_sectiontabs::TABS_CUSTOM && $customtabs) {
            $tabs = $this->export_custom_tabs($customtabs, $sections);
            $hastabs = !empty($tabs);
        } else {
            // One tab per section (also when the custom tabs are not defined yet).
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
            'style' => $format->get_tab_style(),
        ];
    }

    /**
     * Build the tabs defined by the teacher.
     *
     * @param array $customtabs the custom tabs definition
     * @param array $sections visible sections data, by section id
     * @return array
     */
    protected function export_custom_tabs(array $customtabs, array $sections): array {
        $context = $this->format->get_context();
        $tabs = [];
        foreach ($customtabs as $definition) {
            $label = format_string($definition['label'], true, ['context' => $context]);
            $tab = [
                'name' => $label,
                'fullname' => $label,
                'title' => html_entity_decode(strip_tags($label), ENT_QUOTES, 'UTF-8'),
                'active' => false,
                'hidden' => false,
                'unavailable' => false,
            ];
            if ($definition['url'] !== '') {
                $tab['url'] = $definition['url'];
                $tab['external'] = true;
                $tabs[] = $tab;
                continue;
            }
            // Sections targeted by the tab that this user can see (deleted sections are ignored).
            $targets = [];
            foreach ($definition['sectionids'] as $sectionid) {
                if (isset($sections[$sectionid])) {
                    $targets[$sectionid] = $sections[$sectionid];
                }
            }
            if (!$targets) {
                continue;
            }
            // The tab opens the first section the user can access.
            $available = array_filter($targets, fn($target) => !$target['unavailable']);
            $first = reset($available) ?: reset($targets);
            $tab['url'] = $first['url'];
            $tab['active'] = (bool)array_filter($targets, fn($target) => $target['active']);
            $tab['unavailable'] = empty($available);
            $tab['hidden'] = !array_filter($targets, fn($target) => !$target['hidden']);
            $tabs[] = $tab;
        }
        return $tabs;
    }
}
