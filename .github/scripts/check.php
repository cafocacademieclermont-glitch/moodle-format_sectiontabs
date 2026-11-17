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
 * CI checks run outside Moodle: version.php content and language files consistency.
 *
 * Usage: php .github/scripts/check.php [--print-release]
 *
 * @package    format_sectiontabs
 * @copyright  2026 Eric Falcon
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalGlobalState

define('MOODLE_INTERNAL', true);
define('MATURITY_ALPHA', 50);
define('MATURITY_BETA', 100);
define('MATURITY_RC', 150);
define('MATURITY_STABLE', 200);
define('ANY_VERSION', 'any');

$root = dirname(__DIR__, 2);
$errors = [];

// Version file.
$plugin = new stdClass();
require($root . '/version.php');
if (($plugin->component ?? '') !== 'format_sectiontabs') {
    $errors[] = 'version.php: component must be format_sectiontabs.';
}
if (!preg_match('/^\d{10}$/', (string)($plugin->version ?? ''))) {
    $errors[] = 'version.php: version must be YYYYMMDDXX.';
}
if (!preg_match('/^\d+\.\d+(\.\d+)?$/', (string)($plugin->release ?? ''))) {
    $errors[] = 'version.php: release must look like 1.0 or 1.0.1.';
}

if (in_array('--print-release', $argv, true)) {
    // Used by the release workflow: release number, then "stable" or "prerelease".
    echo ($plugin->release ?? '') . ' ' . (($plugin->maturity ?? 0) === MATURITY_STABLE ? 'stable' : 'prerelease') . "\n";
    exit($errors ? 1 : 0);
}

// Language files: same strings in English and French.
$keys = [];
foreach (['en', 'fr'] as $lang) {
    $string = [];
    $file = "$root/lang/$lang/format_sectiontabs.php";
    if (!is_file($file)) {
        $errors[] = "Missing language file lang/$lang/format_sectiontabs.php.";
        continue;
    }
    include($file);
    $keys[$lang] = array_keys($string);
    $sorted = $keys[$lang];
    sort($sorted);
    if ($sorted !== $keys[$lang]) {
        $errors[] = "lang/$lang: strings are not in alphabetical order.";
    }
}
if (isset($keys['en'], $keys['fr'])) {
    foreach (array_diff($keys['en'], $keys['fr']) as $key) {
        $errors[] = "lang/fr: missing string '$key'.";
    }
    foreach (array_diff($keys['fr'], $keys['en']) as $key) {
        $errors[] = "lang/en: missing string '$key'.";
    }
}

// Release notes for the current release.
$changes = @file_get_contents("$root/CHANGES.md");
if ($changes === false || !preg_match('/^## ' . preg_quote((string)$plugin->release, '/') . '\b/m', $changes)) {
    echo "Warning: CHANGES.md has no section for release {$plugin->release}.\n";
}

if ($errors) {
    fwrite(STDERR, implode("\n", $errors) . "\n");
    exit(1);
}
echo "OK: format_sectiontabs {$plugin->release} ({$plugin->version}), " . count($keys['en'] ?? []) . " language strings.\n";
