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
 * @package    format_sectiontabs
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
$string['gtabshome'] = 'Course home shows only the general section';
$string['gtabshome_help'] = 'If enabled, the course home page only displays the general section, like a "one section per tab" format. Each other section is displayed on its own page. In editing mode the full list of sections is still displayed so that sections can be added and moved.';
$string['gtabsleftmenu'] = 'Section menu on the left';
$string['gtabsleftmenu_help'] = 'If enabled, the list of the course sections is displayed on the left of the course content, in addition to the tab bar. It is hidden on small screens, where the tab bar remains.';
$string['gtabsmode'] = 'Tabs';
$string['gtabsmode_custom'] = 'Custom tabs';
$string['gtabsmode_help'] = 'One tab per section: the tab bar lists every section of the course.

Custom tabs: you define the tabs yourself, and which sections each of them opens, on the "Course tabs" page (link below, or "More" menu of the course). The section menu on the left still lists every section.';
$string['gtabsmode_sections'] = 'One tab per section';
$string['gtabsnav'] = 'Previous / Next buttons';
$string['gtabsnav_help'] = 'If enabled, a bar with "Previous" and "Next" buttons is displayed at the bottom of each section. While the section has subsections left, the buttons open the previous or next subsection; on the last subsection, "Next" opens the next section. Sections the user cannot access are skipped. This bar replaces the section navigation links of Moodle.';
$string['gtabsshortnames'] = 'Short tab names';
$string['gtabsshortnames_help'] = 'If enabled, only the part of the section name before " : " is used as tab name. For example "Block 1 : Essential acts" becomes "Block 1". The full name is displayed when hovering the tab.';
$string['gtabssubtabs'] = 'Subsections as tabs';
$string['gtabssubtabs_help'] = 'If enabled, on a section page the subsections are displayed as tabs: only the subsection of the selected tab is displayed. In editing mode an extra "Show all" tab displays every subsection, to move them.';
$string['hiddenfromstudents'] = 'hidden from students';
$string['hidefromothers'] = 'Hide';
$string['invalidcolour'] = 'Enter a hexadecimal colour code, for example #ecb21e.';
$string['managetabs'] = 'Course tabs';
$string['managetabs_info'] = '{$a}: define the tabs and the sections they open.';
$string['managetabs_intro'] = 'One block per tab. Give it a name, then choose the sections it opens, or a web address. A tab can group several sections: it opens the first one the user can access and stays highlighted on each of them. Empty blocks are ignored. Renaming a section does not break the tabs.';
$string['managetabs_newcourse'] = 'Save the course first, then define the tabs from the "Course tabs" page ("More" menu of the course).';
$string['navlabel'] = 'Previous and next';
$string['navnextsection'] = 'Next section';
$string['navnextsubsection'] = 'Next';
$string['navprevsection'] = 'Previous section';
$string['navprevsubsection'] = 'Previous';
$string['newsection'] = 'New section';
$string['notavailable'] = 'not available';
$string['opensinnewwindow'] = 'opens in a new window';
$string['page-course-view-sectiontabs'] = 'Any course main page in tabs and section menu format';
$string['page-course-view-sectiontabs-x'] = 'Any course page in tabs and section menu format';
$string['plugin_description'] = 'One section per page, with a tab bar and a section menu giving access to every section, and subsections displayed as tabs.';
$string['pluginname'] = 'Tabs and section menu';
$string['privacy:metadata'] = 'The Tabs and section menu format plugin does not store any personal data.';
$string['section0name'] = 'General';
$string['sectionname'] = 'Section';
$string['showfromothers'] = 'Show';
$string['sidemenunav'] = 'Course sections menu';
$string['subtabsnav'] = 'Subsections';
$string['subtabsshowall'] = 'Show all';
$string['tabadd'] = 'Add a tab';
$string['tabdelete'] = 'Delete this tab';
$string['tablabel'] = 'Tab name';
$string['tablabel_required'] = 'Give this tab a name.';
$string['tabno'] = 'Tab {no}';
$string['tabposition'] = 'Position';
$string['tabposition_help'] = 'Order of the tab in the tab bar: 1 for the first one, 2 for the second one... Tabs with the same position keep the order of this page.';
$string['tabsections'] = 'Sections';
$string['tabsections_help'] = 'Sections opened by this tab. With several sections, the tab opens the first one the user can access and stays highlighted on each of them. A tab is not displayed to a user who cannot see any of its sections.';
$string['tabsections_none'] = 'No section selected';
$string['tabsections_placeholder'] = 'Search for a section';
$string['tabsnav'] = 'Course sections';
$string['tabssaved'] = 'The course tabs have been saved.';
$string['tabtarget_both'] = 'Choose either sections or a web address, not both.';
$string['tabtarget_required'] = 'Choose at least one section, or enter a web address.';
$string['taburl'] = 'Or web address';
$string['taburl_help'] = 'Instead of sections, the tab can open a web address (starting with http:// or https://). It opens in a new window.';
$string['taburl_invalid'] = 'Enter a web address starting with http:// or https://.';
