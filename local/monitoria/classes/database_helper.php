<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace local_monitoria;

defined('MOODLE_INTERNAL') || die();

/**
 * High-reliability database helper with dual-mode support (Native Moodle $DB with JSON Store fallback).
 *
 * @package    local_monitoria
 * @copyright  2026 IFSP - Grupo Monitoria Online (GEN Corp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database_helper {

    private static ?string $storage_path = null;

    private static function get_storage_path(): string {
        global $CFG;
        if (self::$storage_path === null) {
            $dir = !empty($CFG->dataroot) ? $CFG->dataroot . '/monitoria_data' : __DIR__ . '/../data';
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
            self::$storage_path = $dir;
        }
        return self::$storage_path;
    }

    private static function read_table(string $table): array {
        $file = self::get_storage_path() . '/' . $table . '.json';
        if (!file_exists($file)) {
            return self::get_seed_data($table);
        }
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    private static function write_table(string $table, array $data): bool {
        $file = self::get_storage_path() . '/' . $table . '.json';
        return (bool)file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function get_records(string $table, array $conditions = []): array {
        global $DB;
        if (!empty($DB) && is_object($DB) && method_exists($DB, 'get_records')) {
            try {
                return (array)$DB->get_records($table, $conditions);
            } catch (\Exception $e) {
                // Fallback to local storage if table doesn't exist yet in Moodle DB
            }
        }

        $records = self::read_table($table);
        if (empty($conditions)) {
            return $records;
        }

        return array_values(array_filter($records, function($item) use ($conditions) {
            foreach ($conditions as $k => $v) {
                if (!isset($item[$k]) || (string)$item[$k] !== (string)$v) {
                    return false;
                }
            }
            return true;
        }));
    }

    public static function insert_record(string $table, array $data): int {
        global $DB;
        if (!empty($DB) && is_object($DB) && method_exists($DB, 'insert_record')) {
            try {
                return $DB->insert_record($table, (object)$data);
            } catch (\Exception $e) {
                // Fallback
            }
        }

        $records = self::read_table($table);
        $max_id = 0;
        foreach ($records as $r) {
            if (isset($r['id']) && $r['id'] > $max_id) {
                $max_id = (int)$r['id'];
            }
        }
        $data['id'] = $max_id + 1;
        $data['timecreated'] = time();
        $data['timemodified'] = time();
        $records[] = $data;
        self::write_table($table, $records);
        return $data['id'];
    }

    public static function update_record(string $table, array $data): bool {
        global $DB;
        if (!empty($DB) && is_object($DB) && method_exists($DB, 'update_record')) {
            try {
                return $DB->update_record($table, (object)$data);
            } catch (\Exception $e) {
                // Fallback
            }
        }

        $records = self::read_table($table);
        $updated = false;
        foreach ($records as &$item) {
            if (isset($item['id']) && (int)$item['id'] === (int)$data['id']) {
                $item = array_merge($item, $data);
                $item['timemodified'] = time();
                $updated = true;
                break;
            }
        }
        if ($updated) {
            self::write_table($table, $records);
        }
        return $updated;
    }

    private static function get_seed_data(string $table): array {
        if ($table === 'local_monitoria_courses') {
            return [
                [
                    'id' => 1,
                    'code' => 'DW101',
                    'name' => 'Desenvolvimento Web',
                    'department' => 'Informática',
                    'workload' => 80,
                    'status' => 'ativo',
                    'timecreated' => time()
                ],
                [
                    'id' => 2,
                    'code' => 'ES201',
                    'name' => 'Engenharia de Software (Projeto de Software)',
                    'department' => 'Informática',
                    'workload' => 80,
                    'status' => 'ativo',
                    'timecreated' => time()
                ],
                [
                    'id' => 3,
                    'code' => 'BD301',
                    'name' => 'Banco de Dados Relacional',
                    'department' => 'Informática',
                    'workload' => 60,
                    'status' => 'ativo',
                    'timecreated' => time()
                ]
            ];
        }

        if ($table === 'local_monitoria_monitors') {
            return [
                [
                    'id' => 1,
                    'user_id' => 2,
                    'name' => 'Eduardo Gritti Medeiros Teixeira',
                    'email' => 'eduardo.gritti@aluno.ifsp.edu.br',
                    'course_id' => 1,
                    'course_name' => 'Desenvolvimento Web',
                    'weekly_hours' => 12,
                    'meet_link' => 'https://meet.google.com/abc-defg-hij',
                    'location' => 'Laboratório 3 / Online',
                    'status' => 'ativo',
                    'timecreated' => time()
                ],
                [
                    'id' => 2,
                    'user_id' => 3,
                    'name' => 'Gabriel Rodrigues da Rocha Forti',
                    'email' => 'gabriel.forti@aluno.ifsp.edu.br',
                    'course_id' => 2,
                    'course_name' => 'Engenharia de Software (Projeto de Software)',
                    'weekly_hours' => 12,
                    'meet_link' => 'https://meet.google.com/xyz-uvwx-rst',
                    'location' => 'Sala de Projetos / Meet',
                    'status' => 'ativo',
                    'timecreated' => time()
                ]
            ];
        }

        if ($table === 'local_monitoria_slots') {
            return [
                [
                    'id' => 1,
                    'monitor_id' => 1,
                    'monitor_name' => 'Eduardo Gritti',
                    'course_id' => 1,
                    'course_name' => 'Desenvolvimento Web',
                    'weekday' => 'Terça-feira',
                    'date_slot' => '2026-11-03',
                    'start_time' => '14:00',
                    'end_time' => '16:00',
                    'max_capacity' => 5,
                    'current_bookings' => 2,
                    'type' => 'Híbrido (Lab 3 + Google Meet)',
                    'meet_url' => 'https://meet.google.com/abc-defg-hij',
                    'status' => 'disponivel'
                ],
                [
                    'id' => 2,
                    'monitor_id' => 1,
                    'monitor_name' => 'Eduardo Gritti',
                    'course_id' => 1,
                    'course_name' => 'Desenvolvimento Web',
                    'weekday' => 'Quinta-feira',
                    'date_slot' => '2026-11-05',
                    'start_time' => '10:00',
                    'end_time' => '12:00',
                    'max_capacity' => 5,
                    'current_bookings' => 1,
                    'type' => 'Online (Google Meet)',
                    'meet_url' => 'https://meet.google.com/abc-defg-hij',
                    'status' => 'disponivel'
                ],
                [
                    'id' => 3,
                    'monitor_id' => 2,
                    'monitor_name' => 'Gabriel Forti',
                    'course_id' => 2,
                    'course_name' => 'Engenharia de Software',
                    'weekday' => 'Quarta-feira',
                    'date_slot' => '2026-11-04',
                    'start_time' => '15:00',
                    'end_time' => '17:00',
                    'max_capacity' => 6,
                    'current_bookings' => 3,
                    'type' => 'Presencial (Bloco B - Sala 12)',
                    'meet_url' => 'https://meet.google.com/xyz-uvwx-rst',
                    'status' => 'disponivel'
                ]
            ];
        }

        if ($table === 'local_monitoria_bookings') {
            return [
                [
                    'id' => 1,
                    'slot_id' => 1,
                    'student_id' => 10,
                    'student_name' => 'Nicolas de Azevedo Andrade Santana',
                    'course_name' => 'Desenvolvimento Web',
                    'monitor_name' => 'Eduardo Gritti',
                    'date_slot' => '2026-11-03',
                    'time_slot' => '14:00 - 16:00',
                    'topic' => 'Dúvidas em integração de APIs e arquitetura Moodle',
                    'status' => 'agendado',
                    'timecreated' => time()
                ]
            ];
        }

        if ($table === 'local_monitoria_doubts') {
            return [
                [
                    'id' => 1,
                    'student_id' => 10,
                    'student_name' => 'Nicolas Andrade',
                    'course_id' => 1,
                    'course_name' => 'Desenvolvimento Web',
                    'title' => 'Como estruturar o db/install.xml para tabelas relacionais?',
                    'description' => 'Estou implementando o plugin e gostaria de validar se a chave estrangeira do monitor para a disciplina deve ser referenciada por ID ou código.',
                    'reply' => 'Olá Nicolas! No padrão Moodle XMLDB, usamos sempre o campo "id" da tabela referenciada com foreign-key constraint formal.',
                    'replied_by' => 'Eduardo Gritti (Monitor)',
                    'status' => 'respondida',
                    'timecreated' => time() - 3600
                ]
            ];
        }

        return [];
    }
}
