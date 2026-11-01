<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace local_monitoria;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/database_helper.php');

/**
 * Core business logic manager for local_monitoria.
 *
 * @package    local_monitoria
 * @copyright  2026 IFSP - Grupo Monitoria Online (GEN Corp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class monitoria_manager {

    // --- DISCIPLINES / COURSES ---
    public static function get_all_courses(): array {
        return database_helper::get_records('local_monitoria_courses');
    }

    public static function create_course(string $code, string $name, string $department, int $workload): int {
        return database_helper::insert_record('local_monitoria_courses', [
            'code' => trim($code),
            'name' => trim($name),
            'department' => trim($department),
            'workload' => $workload,
            'status' => 'ativo'
        ]);
    }

    // --- MONITORS ---
    public static function get_all_monitors(): array {
        return database_helper::get_records('local_monitoria_monitors');
    }

    public static function assign_monitor(string $name, string $email, int $course_id, string $course_name, int $weekly_hours, string $meet_link, string $location): int {
        return database_helper::insert_record('local_monitoria_monitors', [
            'user_id' => rand(100, 999),
            'name' => trim($name),
            'email' => trim($email),
            'course_id' => $course_id,
            'course_name' => trim($course_name),
            'weekly_hours' => $weekly_hours,
            'meet_link' => trim($meet_link),
            'location' => trim($location),
            'status' => 'ativo'
        ]);
    }

    // --- SLOTS & AGENDAMENTOS ---
    public static function get_available_slots(?int $course_id = null): array {
        $slots = database_helper::get_records('local_monitoria_slots');
        if ($course_id !== null && $course_id > 0) {
            $slots = array_filter($slots, fn($s) => (int)$s['course_id'] === $course_id);
        }
        return array_values($slots);
    }

    public static function create_slot(int $monitor_id, string $monitor_name, int $course_id, string $course_name, string $weekday, string $date_slot, string $start_time, string $end_time, int $max_capacity, string $type, string $meet_url): int {
        return database_helper::insert_record('local_monitoria_slots', [
            'monitor_id' => $monitor_id,
            'monitor_name' => $monitor_name,
            'course_id' => $course_id,
            'course_name' => $course_name,
            'weekday' => $weekday,
            'date_slot' => $date_slot,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'max_capacity' => $max_capacity,
            'current_bookings' => 0,
            'type' => $type,
            'meet_url' => $meet_url,
            'status' => 'disponivel'
        ]);
    }

    public static function book_session(int $slot_id, string $student_name, string $topic): array {
        $slots = database_helper::get_records('local_monitoria_slots', ['id' => $slot_id]);
        if (empty($slots)) {
            return ['success' => false, 'message' => 'Horário não encontrado.'];
        }
        $slot = $slots[0];
        if ((int)$slot['current_bookings'] >= (int)$slot['max_capacity']) {
            return ['success' => false, 'message' => 'Não há vagas disponíveis neste horário.'];
        }

        // Increment booking count
        $slot['current_bookings'] = (int)$slot['current_bookings'] + 1;
        if ($slot['current_bookings'] >= $slot['max_capacity']) {
            $slot['status'] = 'lotado';
        }
        database_helper::update_record('local_monitoria_slots', $slot);

        // Insert booking
        $booking_id = database_helper::insert_record('local_monitoria_bookings', [
            'slot_id' => $slot_id,
            'student_id' => 10,
            'student_name' => trim($student_name),
            'course_name' => $slot['course_name'],
            'monitor_name' => $slot['monitor_name'],
            'date_slot' => $slot['date_slot'],
            'time_slot' => $slot['start_time'] . ' - ' . $slot['end_time'],
            'topic' => trim($topic),
            'status' => 'agendado'
        ]);

        return ['success' => true, 'booking_id' => $booking_id, 'message' => 'Monitoria agendada com sucesso!'];
    }

    public static function get_all_bookings(): array {
        return database_helper::get_records('local_monitoria_bookings');
    }

    // --- DOUBTS / FÓRUM ---
    public static function get_all_doubts(?int $course_id = null): array {
        $doubts = database_helper::get_records('local_monitoria_doubts');
        if ($course_id !== null && $course_id > 0) {
            $doubts = array_filter($doubts, fn($d) => (int)$d['course_id'] === $course_id);
        }
        // Order by most recent
        usort($doubts, fn($a, $b) => ($b['timecreated'] ?? 0) <=> ($a['timecreated'] ?? 0));
        return $doubts;
    }

    public static function submit_doubt(string $student_name, int $course_id, string $course_name, string $title, string $description): int {
        return database_helper::insert_record('local_monitoria_doubts', [
            'student_id' => 10,
            'student_name' => trim($student_name),
            'course_id' => $course_id,
            'course_name' => trim($course_name),
            'title' => trim($title),
            'description' => trim($description),
            'reply' => '',
            'replied_by' => '',
            'status' => 'aberta'
        ]);
    }

    public static function reply_doubt(int $doubt_id, string $replied_by, string $reply): bool {
        $doubts = database_helper::get_records('local_monitoria_doubts', ['id' => $doubt_id]);
        if (empty($doubts)) return false;
        $d = $doubts[0];
        $d['reply'] = trim($reply);
        $d['replied_by'] = trim($replied_by);
        $d['status'] = 'respondida';
        return database_helper::update_record('local_monitoria_doubts', $d);
    }

    // --- METRICS & DASHBOARD STATS ---
    public static function get_dashboard_metrics(string $role = 'aluno'): array {
        $courses = self::get_all_courses();
        $monitors = self::get_all_monitors();
        $slots = self::get_available_slots();
        $bookings = self::get_all_bookings();
        $doubts = self::get_all_doubts();

        $open_doubts = count(array_filter($doubts, fn($d) => ($d['status'] ?? '') === 'aberta'));
        $scheduled_count = count(array_filter($bookings, fn($b) => ($b['status'] ?? '') === 'agendado'));

        return [
            'total_courses' => count($courses),
            'total_monitors' => count($monitors),
            'available_slots' => count($slots),
            'total_bookings' => count($bookings),
            'active_bookings' => $scheduled_count,
            'open_doubts' => $open_doubts
        ];
    }
}
