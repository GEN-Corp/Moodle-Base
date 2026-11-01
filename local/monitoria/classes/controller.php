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
 * Main presentation and access controller for local_monitoria.
 *
 * @package    local_monitoria
 * @copyright  2026 IFSP - Grupo Monitoria Online (GEN Corp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controller {

    public static function get_active_role(): string {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        // Allow explicit switch via GET query param
        if (!empty($_GET['role'])) {
            $r = strtolower(trim($_GET['role']));
            if (in_array($r, ['aluno', 'monitor', 'professor', 'coordenador'])) {
                $_SESSION['monitoria_active_role'] = $r;
            }
        }

        if (!empty($_SESSION['monitoria_active_role'])) {
            return $_SESSION['monitoria_active_role'];
        }

        // Default role based on Moodle capabilities
        global $USER;
        if (!empty($USER) && is_siteadmin()) {
            return 'coordenador';
        }
        return 'aluno';
    }

    public static function get_active_user(): array {
        global $USER;
        $role = self::get_active_role();

        $names = [
            'aluno' => 'Nicolas de Azevedo Andrade Santana (Aluno)',
            'monitor' => 'Eduardo Gritti Medeiros Teixeira (Monitor)',
            'professor' => 'Prof. Caio (Docente Responsável)',
            'coordenador' => 'Gabriel Rodrigues da Rocha Forti (Coordenação)'
        ];

        return [
            'id' => !empty($USER->id) ? $USER->id : 10,
            'name' => $names[$role] ?? 'Usuário Acadêmico',
            'role' => $role
        ];
    }

    public static function render_header(string $active_tab = 'dashboard'): void {
        global $OUTPUT, $PAGE;

        // If running inside full Moodle environment, render Moodle header
        if (!empty($OUTPUT) && method_exists($OUTPUT, 'header')) {
            echo $OUTPUT->header();
        }

        $user = self::get_active_user();
        $role = $user['role'];

        echo '<link rel="stylesheet" href="styles.css?v=' . time() . '">';
        echo '<div class="monitoria-container">';
        
        // Header
        echo '<div class="monitoria-header">';
        echo '  <div class="monitoria-title-group">';
        echo '    <h1>🎓 Monitoria Online <span class="badge badge-success">IFSP</span></h1>';
        echo '    <p>Plataforma Centralizada de Apoio Acadêmico, Agendamentos e Fórum de Dúvidas</p>';
        echo '  </div>';
        echo '  <div class="profile-selector-box">';
        echo '    <span><strong>Perfil Ativo:</strong></span>';
        echo '    <select onchange="window.location.href=\'?role=\' + this.value;">';
        $roles = [
            'aluno' => '🎓 Aluno',
            'monitor' => '👨‍💻 Monitor',
            'professor' => '👨‍🏫 Professor',
            'coordenador' => '🏫 Coordenador / Admin'
        ];
        foreach ($roles as $k => $label) {
            $sel = ($k === $role) ? 'selected' : '';
            echo "<option value=\"$k\" $sel>$label</option>";
        }
        echo '    </select>';
        echo '  </div>';
        echo '</div>';

        // Nav Tabs
        echo '<div class="monitoria-nav-tabs">';
        $tabs = [
            'dashboard' => ['label' => '📊 Painel Geral', 'url' => 'index.php'],
            'agendamento' => ['label' => '🗓️ Agendamentos & Horários', 'url' => 'agendamento.php'],
        ];

        if (in_array($role, ['monitor', 'professor', 'coordenador'])) {
            $tabs['atendimentos'] = ['label' => '📋 Gestão de Atendimentos', 'url' => 'atendimentos.php'];
        }

        $tabs['duvidas'] = ['label' => '💬 Central de Dúvidas', 'url' => 'duvidas.php'];

        if (in_array($role, ['professor', 'coordenador'])) {
            $tabs['gestao'] = ['label' => '⚙️ Gestão de Disciplinas & Monitores', 'url' => 'admin_gestao.php'];
        }

        foreach ($tabs as $key => $tab) {
            $activeClass = ($key === $active_tab) ? 'active' : '';
            echo "<a href=\"{$tab['url']}?role=$role\" class=\"tab-link $activeClass\">{$tab['label']}</a>";
        }
        echo '</div>';
    }

    public static function render_footer(): void {
        global $OUTPUT;

        echo '  <div class="monitoria-footer-signature">';
        echo '    <p>Instituto Federal de Educação, Ciência e Tecnologia de São Paulo (IFSP) — Monitoria Online</p>';
        echo '    <p>Equipe: Gabriel Forti • Eduardo Gritti • Nicolas Andrade | <em>Desenvolvido com excelência por NextCodeGen &lt;/&gt;</em></p>';
        echo '  </div>';
        echo '</div>'; // End container

        // If running inside full Moodle environment, render Moodle footer
        if (!empty($OUTPUT) && method_exists($OUTPUT, 'footer')) {
            echo $OUTPUT->footer();
        }
    }
}
