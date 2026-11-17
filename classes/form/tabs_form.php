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

namespace format_sectiontabs\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form to define the custom tabs of a course: one block of fields per tab.
 *
 * Custom data: 'sections' => [sectionid => name], 'repeats' => number of tabs to display.
 *
 * @package    format_sectiontabs
 * @copyright  2026 Eric Falcon
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tabs_form extends \moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;
        $sections = $this->_customdata['sections'];
        $repeats = max(1, (int)$this->_customdata['repeats']);

        $mform->addElement('hidden', 'id', $this->_customdata['courseid']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('html', \html_writer::div(
            get_string('managetabs_intro', 'format_sectiontabs'),
            'alert alert-info'
        ));

        $repeat = [];
        $repeat[] = $mform->createElement('header', 'tabheader', get_string('tabno', 'format_sectiontabs'));
        $repeat[] = $mform->createElement('text', 'tablabel', get_string('tablabel', 'format_sectiontabs'), ['size' => 40]);
        $repeat[] = $mform->createElement(
            'autocomplete',
            'tabsections',
            get_string('tabsections', 'format_sectiontabs'),
            $sections,
            [
                'multiple' => true,
                'noselectionstring' => get_string('tabsections_none', 'format_sectiontabs'),
                'placeholder' => get_string('tabsections_placeholder', 'format_sectiontabs'),
            ]
        );
        $repeat[] = $mform->createElement(
            'text',
            'taburl',
            get_string('taburl', 'format_sectiontabs'),
            ['size' => 50, 'placeholder' => 'https://']
        );
        $repeat[] = $mform->createElement('text', 'tabposition', get_string('tabposition', 'format_sectiontabs'), ['size' => 3]);
        $repeat[] = $mform->createElement('submit', 'tabdelete', get_string('tabdelete', 'format_sectiontabs'), [], false);

        $options = [
            'tablabel' => ['type' => PARAM_TEXT],
            'tabsections' => ['type' => PARAM_INT, 'helpbutton' => ['tabsections', 'format_sectiontabs']],
            'taburl' => ['type' => PARAM_RAW_TRIMMED, 'helpbutton' => ['taburl', 'format_sectiontabs']],
            'tabposition' => ['type' => PARAM_INT, 'helpbutton' => ['tabposition', 'format_sectiontabs']],
        ];

        $this->repeat_elements(
            $repeat,
            $repeats,
            $options,
            'tabrepeats',
            'tabadd',
            1,
            get_string('tabadd', 'format_sectiontabs'),
            true,
            'tabdelete'
        );

        // Expand every tab block (the "expanded" option of repeat_elements() fails on deleted blocks).
        $total = $this->optional_param('tabrepeats', $repeats, PARAM_INT) + 1;
        for ($i = 0; $i < $total; $i++) {
            if ($mform->elementExists("tabheader[$i]")) {
                $mform->setExpanded("tabheader[$i]", true);
            }
        }

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * Validation: each tab needs a name and either sections or a web address.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        foreach (array_keys($data['tablabel'] ?? []) as $i) {
            $label = trim((string)($data['tablabel'][$i] ?? ''));
            $sectionids = array_filter((array)($data['tabsections'][$i] ?? []));
            $url = trim((string)($data['taburl'][$i] ?? ''));
            if ($label === '' && !$sectionids && $url === '') {
                // Empty block: ignored.
                continue;
            }
            if ($label === '') {
                $errors["tablabel[$i]"] = get_string('tablabel_required', 'format_sectiontabs');
            }
            if (!$sectionids && $url === '') {
                $errors["tabsections[$i]"] = get_string('tabtarget_required', 'format_sectiontabs');
            } else if ($sectionids && $url !== '') {
                $errors["taburl[$i]"] = get_string('tabtarget_both', 'format_sectiontabs');
            }
            if ($url !== '' && (!preg_match('~^https?://~i', $url) || clean_param($url, PARAM_URL) === '')) {
                $errors["taburl[$i]"] = get_string('taburl_invalid', 'format_sectiontabs');
            }
        }
        return $errors;
    }

    /**
     * The tabs entered in the form, in their display order.
     *
     * @return array|null list of ['label', 'sectionids', 'url'], or null when the form is not submitted
     */
    public function get_tabs(): ?array {
        $data = $this->get_data();
        if (!$data) {
            return null;
        }
        $rows = [];
        foreach (array_keys($data->tablabel ?? []) as $i) {
            $label = trim((string)$data->tablabel[$i]);
            if ($label === '') {
                continue;
            }
            $url = trim((string)($data->taburl[$i] ?? ''));
            $rows[] = [
                'position' => (int)($data->tabposition[$i] ?? 0) ?: 1000 + $i,
                'index' => $i,
                'label' => $label,
                'sectionids' => array_values(array_map('intval', array_filter((array)($data->tabsections[$i] ?? [])))),
                'url' => $url !== '' ? clean_param($url, PARAM_URL) : '',
            ];
        }
        usort($rows, fn($a, $b) => [$a['position'], $a['index']] <=> [$b['position'], $b['index']]);
        return array_map(fn($row) => array_intersect_key($row, array_flip(['label', 'sectionids', 'url'])), $rows);
    }
}
