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

namespace format_grettabs\output;

use format_topics\output\renderer as renderer_topics;

/**
 * Renderer of the "Sections with tabs" format.
 *
 * @package    format_grettabs
 * @copyright  2026 Eric Falcon
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends renderer_topics {
    /**
     * Render the course content with the tab bar on top.
     *
     * @param \renderable $widget
     * @return string
     */
    public function render_content(\renderable $widget): string {
        if ($widget instanceof courseformat\content) {
            return $this->render_from_template('format_grettabs/local/content', $widget->export_for_template($this));
        }
        // Any other widget named "content": default rendering.
        return $this->render_from_template($widget->get_template_name($this), $widget->export_for_template($this));
    }
}
