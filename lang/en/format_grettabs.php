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
 * English strings.
 *
 * @package    format_grettabs
 * @copyright  2026 Eric Falcon
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activeborder'] = 'Active tab underline colour';
$string['activeborder_desc'] = 'Colour of the line under the active tab.';
$string['activecolor'] = 'Active tab colour';
$string['activecolor_desc'] = 'Default background colour of the tab of the section being displayed. Each course can set its own colour.';
$string['currentsection'] = 'Current section';
$string['gtabsactiveborder'] = 'Active tab underline colour';
$string['gtabsactiveborder_help'] = 'Colour of the line under the active tab, as a hexadecimal code (for example #2b1a10). Leave empty to use the site colour.';
$string['gtabsactivecolor'] = 'Active tab colour';
$string['gtabsactivecolor_help'] = 'Background colour of the tab of the section being displayed, as a hexadecimal code (for example #ecb21e). Leave empty to use the site colour.';
$string['gtabscustom'] = 'Custom tabs';
$string['gtabscustom_badline'] = 'Line {$a}: write "Tab name | Section name".';
$string['gtabscustom_empty'] = 'Enter at least one tab.';
$string['gtabscustom_help'] = 'One tab per line, in the form:

<code>Tab name | Section name</code>

A tab can group several sections, separated by semicolons: <code>Course area | Block 1; Block 2; Block 3</code>. It opens the first one and stays highlighted on each of them.

A tab can also open a web address: <code>Resources | https://...</code>

Section names are not case sensitive. Lines starting with # are ignored. A tab is not displayed to a user who cannot see any of its sections.';
$string['gtabscustom_placeholder'] = 'General information | General
Course area | Block 1; Block 2';
$string['gtabscustom_unknown'] = 'Line {$a->line}: no section is named "{$a->name}".';
$string['gtabshome'] = 'Course home shows only the general section';
$string['gtabshome_help'] = 'If enabled, the course home page only displays the general section, like a "one section per tab" format. Each other section is displayed on its own page. In editing mode the full list of sections is still displayed so that sections can be added and moved.';
$string['gtabsleftmenu'] = 'Section menu on the left';
$string['gtabsleftmenu_help'] = 'If enabled, the list of the course sections is displayed on the left of the course content, in addition to the tab bar. It is hidden on small screens, where the tab bar remains.';
$string['gtabsmode'] = 'Tabs';
$string['gtabsmode_custom'] = 'Custom tabs';
$string['gtabsmode_help'] = 'One tab per section: the tab bar lists every section of the course.

Custom tabs: you define the tabs yourself, and which sections each of them opens. The section menu on the left still lists every section.';
$string['gtabsmode_sections'] = 'One tab per section';
$string['gtabsshortnames'] = 'Short tab names';
$string['gtabsshortnames_help'] = 'If enabled, only the part of the section name before " : " is used as tab name. For example "Block 1 : Essential acts" becomes "Block 1". The full name is displayed when hovering the tab.';
$string['gtabssubtabs'] = 'Subsections as tabs';
$string['gtabssubtabs_help'] = 'If enabled, on a section page the subsections are displayed as tabs: only the subsection of the selected tab is displayed. In editing mode an extra "Show all" tab displays every subsection, to move them.';
$string['hiddenfromstudents'] = 'hidden from students';
$string['hidefromothers'] = 'Hide';
$string['invalidcolour'] = 'Enter a hexadecimal colour code, for example #ecb21e.';
$string['newsection'] = 'New section';
$string['notavailable'] = 'not available';
$string['opensinnewwindow'] = 'opens in a new window';
$string['page-course-view-grettabs'] = 'Any course main page in sections with tabs format';
$string['page-course-view-grettabs-x'] = 'Any course page in sections with tabs format';
$string['plugin_description'] = 'Custom sections with an automatic tab bar giving access to every section.';
$string['pluginname'] = 'Sections with tabs';
$string['privacy:metadata'] = 'The Sections with tabs format plugin does not store any personal data.';
$string['section0name'] = 'General';
$string['sectionname'] = 'Section';
$string['showfromothers'] = 'Show';
$string['sidemenunav'] = 'Course sections menu';
$string['subtabsnav'] = 'Subsections';
$string['subtabsshowall'] = 'Show all';
$string['tabsnav'] = 'Course sections';
