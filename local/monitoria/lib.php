<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Standard Moodle library functions and navigation hooks for local_monitoria.
 *
 * @package    local_monitoria
 * @copyright  2026 IFSP - Grupo Monitoria Online (GEN Corp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extends the global navigation to include Monitoria Online.
 *
 * @param global_navigation $navigation
 */
function local_monitoria_extend_navigation(global_navigation $navigation) {
    global $CFG, $USER;

    if (!isloggedin() || isguestuser()) {
        return;
    }

    $url = new moodle_url('/local/monitoria/index.php');
    $node = navigation_node::create(
        get_string('pluginname', 'local_monitoria'),
        $url,
        navigation_node::TYPE_CUSTOM,
        'local_monitoria',
        'local_monitoria',
        new pix_icon('i/calendar', '')
    );
    $node->showinflatnavigation = true;
    $navigation->add_node($node);
}

/**
 * Injects plugin styles into page header.
 */
function local_monitoria_before_footer() {
    global $PAGE;
    // Can attach assets if needed
}
