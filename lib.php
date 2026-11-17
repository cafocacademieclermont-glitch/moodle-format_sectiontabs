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
        $options['gtabsnav'] = [
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
            // The custom tabs are edited on their own page (tabs.php), not in the course form.
            unset($options['gtabscustom']);
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
            $options['gtabsnav'] += [
                'label' => new lang_string('gtabsnav', 'format_sectiontabs'),
                'help' => 'gtabsnav',
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
     * Keep the custom tabs option, which is not in the course form but is saved by the tabs page.
     *
     * @param array $rawdata
     * @param int|null $sectionid
     * @return array
     */
    protected function validate_format_options(array $rawdata, ?int $sectionid = null): array {
        $data = parent::validate_format_options($rawdata, $sectionid);
        if (!$sectionid && array_key_exists('gtabscustom', $rawdata)) {
            $data['gtabscustom'] = clean_param((string)$rawdata['gtabscustom'], PARAM_TEXT);
        }
        return $data;
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
        if (!$forsection && $mform->elementExists('gtabsmode')) {
            if ($this->courseid && $this->courseid != SITEID) {
                $link = html_writer::link(
                    new moodle_url('/course/format/sectiontabs/tabs.php', ['id' => $this->courseid]),
                    get_string('managetabs', 'format_sectiontabs')
                );
                $text = get_string('managetabs_info', 'format_sectiontabs', $link);
            } else {
                $text = get_string('managetabs_newcourse', 'format_sectiontabs');
            }
            $element = $mform->createElement('static', 'gtabscustomlink', '', $text);
            if ($mform->elementExists('gtabshome')) {
                $mform->insertElementBefore($element, 'gtabshome');
            } else {
                $mform->addElement($element);
            }
            $mform->hideIf('gtabscustomlink', 'gtabsmode', 'neq', self::TABS_CUSTOM);
        }
        return $elements;
    }

    /**
     * Check the tab colours.
     *
     * @param array $data
     * @param array $files
     * @param array $errors
     * @return array
     */
    public function edit_form_validation($data, $files, $errors) {
        $formerrors = parent::edit_form_validation($data, $files, $errors);
        foreach (['gtabsactivecolor', 'gtabsactiveborder'] as $name) {
            $value = trim($data[$name] ?? '');
            if ($value !== '' && self::clean_colour($value) === null) {
                $formerrors[$name] = get_string('invalidcolour', 'format_sectiontabs');
            }
        }
        return $formerrors;
    }

    /**
     * The custom tabs of the course.
     *
     * @return array list of ['label' => string, 'sectionids' => int[], 'url' => string]
     */
    public function get_custom_tabs(): array {
        $data = json_decode((string)($this->get_course()->gtabscustom ?? ''), true);
        if (!is_array($data)) {
            return [];
        }
        $tabs = [];
        foreach ($data as $tab) {
            if (!is_array($tab) || trim((string)($tab['label'] ?? '')) === '') {
                continue;
            }
            $url = clean_param((string)($tab['url'] ?? ''), PARAM_URL);
            $tabs[] = [
                'label' => trim((string)$tab['label']),
                'sectionids' => array_values(array_unique(array_filter(array_map('intval', (array)($tab['sectionids'] ?? []))))),
                'url' => preg_match('~^https?://~i', $url) ? $url : '',
            ];
        }
        return $tabs;
    }

    /**
     * Save the custom tabs of the course.
     *
     * @param array $tabs list of ['label' => string, 'sectionids' => int[], 'url' => string]
     */
    public function set_custom_tabs(array $tabs): void {
        $clean = [];
        foreach ($tabs as $tab) {
            $clean[] = [
                'label' => trim((string)$tab['label']),
                'sectionids' => array_values(array_map('intval', $tab['sectionids'] ?? [])),
                'url' => (string)($tab['url'] ?? ''),
            ];
        }
        // JSON_HEX_TAG keeps the "<" and ">" of the labels through the PARAM_TEXT cleaning.
        $json = json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
        $this->update_course_format_options(['gtabscustom' => $json]);
        rebuild_course_cache($this->courseid, true);
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
 * Add the "Course tabs" page to the course administration ("More" menu).
 *
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param context_course $context
 */
function format_sectiontabs_extend_navigation_course(navigation_node $navigation, stdClass $course, context_course $context) {
    if ($course->format !== 'sectiontabs' || !has_capability('moodle/course:update', $context)) {
        return;
    }
    $navigation->add(
        get_string('managetabs', 'format_sectiontabs'),
        new moodle_url('/course/format/sectiontabs/tabs.php', ['id' => $course->id]),
        navigation_node::TYPE_SETTING,
        null,
        'format_sectiontabs_tabs',
        new pix_icon('i/settings', '')
    );
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
