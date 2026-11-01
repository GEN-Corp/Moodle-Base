<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * REST API Endpoint for local_monitoria.
 *
 * @package    local_monitoria
 * @copyright  2026 IFSP - Grupo Monitoria Online (GEN Corp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

header('Content-Type: application/json; charset=utf-8');

if (file_exists(__DIR__ . '/../../config.php')) {
    require_once(__DIR__ . '/../../config.php');
} else {
    if (!defined('MOODLE_INTERNAL')) define('MOODLE_INTERNAL', true);
}

require_once(__DIR__ . '/classes/monitoria_manager.php');

use local_monitoria\monitoria_manager;

$action = $_GET['action'] ?? ($_POST['action'] ?? 'status');

try {
    switch ($action) {
        case 'courses':
            echo json_encode(['success' => true, 'data' => monitoria_manager::get_all_courses()]);
            break;

        case 'monitors':
            echo json_encode(['success' => true, 'data' => monitoria_manager::get_all_monitors()]);
            break;

        case 'slots':
            $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : null;
            echo json_encode(['success' => true, 'data' => monitoria_manager::get_available_slots($course_id)]);
            break;

        case 'bookings':
            echo json_encode(['success' => true, 'data' => monitoria_manager::get_all_bookings()]);
            break;

        case 'book':
            $slot_id = (int)($_POST['slot_id'] ?? 0);
            $student_name = $_POST['student_name'] ?? 'Aluno IFSP';
            $topic = $_POST['topic'] ?? '';
            $result = monitoria_manager::book_session($slot_id, $student_name, $topic);
            echo json_encode($result);
            break;

        case 'doubts':
            $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : null;
            echo json_encode(['success' => true, 'data' => monitoria_manager::get_all_doubts($course_id)]);
            break;

        case 'submit_doubt':
            $student_name = $_POST['student_name'] ?? 'Aluno IFSP';
            $course_id = (int)($_POST['course_id'] ?? 0);
            $course_name = $_POST['course_name'] ?? 'Disciplina';
            $title = $_POST['title'] ?? '';
            $desc = $_POST['description'] ?? '';
            $id = monitoria_manager::submit_doubt($student_name, $course_id, $course_name, $title, $desc);
            echo json_encode(['success' => true, 'id' => $id]);
            break;

        default:
            echo json_encode([
                'success' => true,
                'name' => 'local_monitoria API',
                'version' => '1.0.0-sprint1',
                'status' => 'operational'
            ]);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
