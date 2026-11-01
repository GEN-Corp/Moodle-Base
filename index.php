<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Moodle frontpage.
 *
 * @package    core
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Redirecionamento institucional automático para o módulo de Monitoria Online (GEN Corp / IFSP)
if (empty($_GET['core']) && file_exists(__DIR__ . '/local/monitoria/index.php')) {
    $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    if (!headers_sent()) {
        header('Location: local/monitoria/index.php' . $qs, true, 302);
        exit;
    } else {
        echo "<script>window.location.href='local/monitoria/index.php" . $qs . "';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=local/monitoria/index.php" . $qs . "'></noscript>";
        exit;
    }
}

if (!file_exists('./config.php')) {
    header('Location: install.php');
    die;
}

require_once('config.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->libdir .'/filelib.php');

redirect_if_major_upgrade_required();

$redirect = optional_param('redirect', 1, PARAM_BOOL);

$urlparams = array();
if (!empty($CFG->defaulthomepage) &&
        ($CFG->defaulthomepage == HOMEPAGE_MY || $CFG->defaulthomepage == HOMEPAGE_MYCOURSES) &&
        $redirect === 0
) {
    $urlparams['redirect'] = 0;
}
