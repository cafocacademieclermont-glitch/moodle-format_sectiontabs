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
 * Main class of the "Tabs and section menu" course format.
 *
 * It behaves exactly like the core "Custom sections" (topics) format and adds
 * an automatic tab bar, built from the course sections, above every course page.
 *
 * @package    format_sectiontabs
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
class format_sectiontabs extends format_topics {
    /** @var int One tab per section. */
    public const TABS_SECTIONS = 0;

    /** @var int Tabs defined by the teacher. */
    public const TABS_CUSTOM = 1;

    /**
     * Course format options: the topics ones plus the tab bar options.
     *
     * @param bool $foreditform
     * @return array
     */
    public function course_format_options($foreditform = false) {
        $options = parent::course_format_options($foreditform);

        $options['gtabsmode'] = [
            'default' => self::TABS_SECTIONS,
            'type' => PARAM_INT,
        ];
        $options['gtabscustom'] = [
            'default' => '',
            'type' => PARAM_TEXT,
        ];
        $options['gtabshome'] = [
            'default' => 1,
            'type' => PARAM_BOOL,
        ];
        $options['gtabsshortnames'] = [
            'default' => 0,
            'type' => PARAM_BOOL,
        ];
        $options['gtabsleftmenu'] = [
            'default' => 1,
            'type' => PARAM_BOOL,
        ];
        $options['gtabssubtabs'] = [
            'default' => 1,
            'type' => PARAM_BOOL,
        ];
        $options['gtabsactivecolor'] = [
            'default' => '',
            'type' => PARAM_TEXT,
        ];
        $options['gtabsactiveborder'] = [
            'default' => '',
            'type' => PARAM_TEXT,
        ];

        if ($foreditform) {
            $options['gtabsmode'] += [
                'label' => new lang_string('gtabsmode', 'format_sectiontabs'),
                'help' => 'gtabsmode',
                'help_component' => 'format_sectiontabs',
                'element_type' => 'select',
                'element_attributes' => [
                    [
                        self::TABS_SECTIONS => new lang_string('gtabsmode_sections', 'format_sectiontabs'),
                        self::TABS_CUSTOM => new lang_string('gtabsmode_custom', 'format_sectiontabs'),
                    ],
                ],
            ];
            $options['gtabscustom'] += [
                'label' => new lang_string('gtabscustom', 'format_sectiontabs'),
                'help' => 'gtabscustom',
                'help_component' => 'format_sectiontabs',
                'element_type' => 'textarea',
                'element_attributes' => [
                    ['rows' => 8, 'cols' => 60,
                     'placeholder' => get_string('gtabscustom_placeholder', 'format_sectiontabs')],
                ],
            ];
            $options['gtabshome'] += [
                'label' => new lang_string('gtabshome', 'format_sectiontabs'),
                'help' => 'gtabshome',
                'help_component' => 'format_sectiontabs',
                'element_type' => 'advcheckbox',
            ];
            $options['gtabsshortnames'] += [
                'label' => new lang_string('gtabsshortnames', 'format_sectiontabs'),
                'help' => 'gtabsshortnames',
                'help_component' => 'format_sectiontabs',
                'element_type' => 'advcheckbox',
            ];
            $options['gtabsleftmenu'] += [
                'label' => new lang_string('gtabsleftmenu', 'format_sectiontabs'),
                'help' => 'gtabsleftmenu',
                'help_component' => 'format_sectiontabs',
                'element_type' => 'advcheckbox',
            ];
            $options['gtabssubtabs'] += [
                'label' => new lang_string('gtabssubtabs', 'format_sectiontabs'),
                'help' => 'gtabssubtabs',
                'help_component' => 'format_sectiontabs',
                'element_type' => 'advcheckbox',
            ];
            foreach (['gtabsactivecolor' => '#ecb21e', 'gtabsactiveborder' => '#2b1a10'] as $name => $placeholder) {
                $options[$name] += [
                    'label' => new lang_string($name, 'format_sectiontabs'),
                    'help' => $name,
                    'help_component' => 'format_sectiontabs',
                    'element_type' => 'text',
                    'element_attributes' => [
                        ['size' => 10, 'maxlength' => 7, 'placeholder' => $placeholder],
                    ],
                ];
            }
        }
        return $options;
    }

    /**
     * Add the course format options to the course form, and show the custom tabs field only when needed.
     *
     * @param MoodleQuickForm $mform
     * @param bool $forsection
     * @return array
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        $elements = parent::create_edit_form_elements($mform, $forsection);
        if (!$forsection && $mform->elementExists('gtabscustom')) {
            $mform->hideIf('gtabscustom', 'gtabsmode', 'neq', self::TABS_CUSTOM);
        }
        return $elements;
    }

    /**
     * Check the tab colours and the custom tabs definition.
     *
     * @param array $data
     * @param array $files
     * @param array $errors
     * @return array
     */
    public function edit_form_validation($data, $files, $errors) {
        $formerrors = parent::edit_form_validation($data, $files, $errors);
        if ((int)($data['gtabsmode'] ?? self::TABS_SECTIONS) === self::TABS_CUSTOM) {
            $parsed = self::parse_custom_tabs($data['gtabscustom'] ?? '');
            $messages = [];
            foreach ($parsed['errors'] as $linenum) {
                $messages[] = get_string('gtabscustom_badline', 'format_sectiontabs', $linenum);
            }
            if (empty($parsed['tabs']) && empty($parsed['errors'])) {
                $messages[] = get_string('gtabscustom_empty', 'format_sectiontabs');
            }
            // Check the section names against the sections of the course (when it already exists).
            // The format instance used for validation is not always tied to the course.
            $courseid = $this->courseid ?: (int)($data['id'] ?? 0);
            if ($courseid && $courseid != SITEID) {
                $known = [];
                $courseformat = course_get_format($courseid);
                foreach (get_fast_modinfo($courseid)->get_listed_section_info_all() as $section) {
                    $known[self::normalise_name($courseformat->get_section_name($section))] = true;
                    $known[self::normalise_name((string)$section->name)] = true;
                }
                foreach ($parsed['tabs'] as $tab) {
                    foreach ($tab['sections'] as $name) {
                        if (empty($known[self::normalise_name($name)])) {
                            $messages[] = get_string(
                                'gtabscustom_unknown',
                                'format_sectiontabs',
                                ['line' => $tab['line'], 'name' => s($name)]
                            );
                        }
                    }
                }
            }
            if ($messages) {
                $formerrors['gtabscustom'] = implode('<br>', $messages);
            }
        }
        foreach (['gtabsactivecolor', 'gtabsactiveborder'] as $name) {
            $value = trim($data[$name] ?? '');
            if ($value !== '' && self::clean_colour($value) === null) {
                $formerrors[$name] = get_string('invalidcolour', 'format_sectiontabs');
            }
        }
        return $formerrors;
    }

    /**
     * Parse the custom tabs definition.
     *
     * One tab per line: "Tab name | Section name; Other section name" or "Tab name | https://...".
     * Empty lines and lines starting with # are ignored.
     *
     * @param string $text
     * @return array ['tabs' => [['line', 'label', 'sections' => string[], 'url' => ?string]], 'errors' => int[] line numbers]
     */
    public static function parse_custom_tabs(string $text): array {
        $tabs = [];
        $errors = [];
        foreach (preg_split('/\R/u', $text) as $index => $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $parts = explode('|', $line, 2);
            $label = trim($parts[0]);
            $target = trim($parts[1] ?? '');
            if ($label === '' || $target === '') {
                $errors[] = $index + 1;
                continue;
            }
            $tab = ['line' => $index + 1, 'label' => $label, 'sections' => [], 'url' => null];
            if (preg_match('~^https?://~i', $target)) {
                $url = clean_param($target, PARAM_URL);
                if ($url === '') {
                    $errors[] = $index + 1;
                    continue;
                }
                $tab['url'] = $url;
            } else {
                $tab['sections'] = array_values(array_filter(array_map('trim', explode(';', $target)), 'strlen'));
                if (!$tab['sections']) {
                    $errors[] = $index + 1;
                    continue;
                }
            }
            $tabs[] = $tab;
        }
        return ['tabs' => $tabs, 'errors' => $errors];
    }

    /**
     * Normalise a section name for comparison: no tags, no case, single spaces.
     *
     * @param string $name
     * @return string
     */
    public static function normalise_name(string $name): string {
        $name = html_entity_decode(strip_tags($name), ENT_QUOTES, 'UTF-8');
        $name = preg_replace('/\s+/u', ' ', str_replace("\u{00A0}", ' ', $name));
        return core_text::strtolower(trim($name));
    }

    /**
     * Return a valid hexadecimal colour or null.
     *
     * @param string|null $value
     * @return string|null
     */
    public static function clean_colour(?string $value): ?string {
        $value = trim((string)$value);
        if ($value !== '' && $value[0] !== '#') {
            $value = '#' . $value;
        }
        return preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $value) ? strtolower($value) : null;
    }

    /**
     * Text colour readable on a background colour (WCAG contrast): white or near black.
     *
     * @param string $background hexadecimal colour
     * @return string
     */
    public static function get_text_colour_for(string $background): string {
        $hex = ltrim(self::clean_colour($background) ?? '#ecb21e', '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $luminance = function (string $hex): float {
            $channels = [];
            foreach ([0, 2, 4] as $offset) {
                $value = hexdec(substr($hex, $offset, 2)) / 255;
                $channels[] = $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
            }
            return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
        };
        $bg = $luminance($hex);
        $dark = $luminance('1d2125');
        $withwhite = 1.05 / ($bg + 0.05);
        $withdark = ($bg + 0.05) / ($dark + 0.05);
        return $withwhite >= $withdark ? '#ffffff' : '#1d2125';
    }

    /**
     * CSS custom properties of the tab colours.
     *
     * @return string
     */
    public function get_tab_style(): string {
        $active = $this->get_tab_colour('activecolor');
        return '--gtabs-active:' . $active .
            ';--gtabs-active-text:' . self::get_text_colour_for($active) .
            ';--gtabs-active-border:' . $this->get_tab_colour('activeborder') . ';';
    }

    /**
     * Colour of a tab element: the course setting, else the site setting, else the default.
     *
     * @param string $name 'activecolor' or 'activeborder'
     * @return string
     */
    public function get_tab_colour(string $name): string {
        $defaults = ['activecolor' => '#ecb21e', 'activeborder' => '#2b1a10'];
        $course = $this->get_course();
        return self::clean_colour($course->{'gtabs' . $name} ?? null)
            ?? self::clean_colour(get_config('format_sectiontabs', $name))
            ?? $defaults[$name];
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
function format_sectiontabs_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        $section = $DB->get_record_sql(
            'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
            [$itemid, 'sectiontabs'],
            MUST_EXIST
        );
        return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
}
