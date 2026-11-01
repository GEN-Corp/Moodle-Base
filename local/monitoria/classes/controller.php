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
 * Formatted to Moodle 4.x Boost theme design specifications.
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

        // Switch role via GET param
        if (!empty($_GET['role'])) {
            $r = strtolower(trim($_GET['role']));
            if (in_array($r, ['aluno', 'monitor', 'professor', 'coordenador'])) {
                $_SESSION['monitoria_active_role'] = $r;
            }
        }

        if (!empty($_SESSION['monitoria_active_role'])) {
            return $_SESSION['monitoria_active_role'];
        }

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
            'aluno' => 'Nicolas de Azevedo Andrade Santana',
            'monitor' => 'Eduardo Gritti Medeiros Teixeira',
            'professor' => 'Prof. Caio',
            'coordenador' => 'Gabriel Rodrigues da Rocha Forti'
        ];

        $emails = [
            'aluno' => 'nicolas.andrade@aluno.ifsp.edu.br',
            'monitor' => 'eduardo.gritti@aluno.ifsp.edu.br',
            'professor' => 'caio.docente@ifsp.edu.br',
            'coordenador' => 'coordenacao@ifsp.edu.br'
        ];

        $initials = [
            'aluno' => 'NA',
            'monitor' => 'EG',
            'professor' => 'PC',
            'coordenador' => 'GF'
        ];

        return [
            'id' => !empty($USER->id) ? $USER->id : 10,
            'name' => $names[$role] ?? 'Usuário Acadêmico',
            'email' => $emails[$role] ?? 'usuario@ifsp.edu.br',
            'initials' => $initials[$role] ?? 'IF',
            'role' => $role
        ];
    }

    public static function render_header(string $active_tab = 'dashboard', string $page_title = 'Monitoria Online', array $breadcrumbs = []): void {
        global $OUTPUT;

        $has_moodle_output = (!empty($OUTPUT) && method_exists($OUTPUT, 'header'));

        if ($has_moodle_output) {
            echo $OUTPUT->header();
        } else {
            // Standalone HTML5 Document Shell
            echo '<!DOCTYPE html>';
            echo '<html lang="pt-BR">';
            echo '<head>';
            echo '  <meta charset="UTF-8">';
            echo '  <meta name="viewport" content="width=device-width, initial-scale=1.0">';
            echo '  <title>' . htmlspecialchars($page_title) . ' — Moodle IFSP</title>';
            echo '  <link rel="icon" href="https://raw.githubusercontent.com/moodle/moodle/main/.github/moodlelogo.svg" type="image/svg+xml">';
            echo '</head>';
            echo '<body>';
        }

        $user = self::get_active_user();
        $role = $user['role'];

        echo '<link rel="stylesheet" href="styles.css?v=' . time() . '">';

        // Moodle 4.x Top Navbar
        echo '<header class="moodle-top-navbar">';
        echo '  <div class="moodle-navbar-left">';
        echo '    <button type="button" class="moodle-drawer-toggle" onclick="document.body.classList.toggle(\'drawer-collapsed\')" aria-label="Alternar Menu Lateral">';
        echo '      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>';
        echo '    </button>';
        echo '    <a href="index.php?role=' . $role . '" class="moodle-brand-logo">';
        echo '      <span class="moodle-logo-icon">🎓</span>';
        echo '      <span class="moodle-brand-text">Moodle <strong>IFSP</strong> <span class="moodle-version-tag">4.5</span></span>';
        echo '    </a>';
        echo '    <nav class="moodle-primary-nav">';
        echo '      <a href="index.php?role=' . $role . '" class="' . ($active_tab === 'dashboard' ? 'active' : '') . '">Página Inicial</a>';
        echo '      <a href="agendamento.php?role=' . $role . '" class="' . ($active_tab === 'agendamento' ? 'active' : '') . '">Monitorias</a>';
        echo '      <a href="duvidas.php?role=' . $role . '" class="' . ($active_tab === 'duvidas' ? 'active' : '') . '">Dúvidas & Fórum</a>';
        echo '    </nav>';
        echo '  </div>';

        echo '  <div class="moodle-navbar-right">';
        echo '    <div class="profile-switch-dropdown">';
        echo '      <label for="moodle-role-select" class="sr-only">Trocar Perfil</label>';
        echo '      <select id="moodle-role-select" onchange="window.location.href=\'?role=\' + this.value;">';
        $roles = [
            'aluno' => '🎓 Aluno (' . $user['name'] . ')',
            'monitor' => '👨‍💻 Monitor (' . $user['name'] . ')',
            'professor' => '👨‍🏫 Professor (' . $user['name'] . ')',
            'coordenador' => '🏫 Coordenador / Admin'
        ];
        foreach ($roles as $k => $label) {
            $sel = ($k === $role) ? 'selected' : '';
            echo "<option value=\"$k\" $sel>$label</option>";
        }
        echo '      </select>';
        echo '    </div>';
        echo '    <div class="moodle-user-avatar" title="' . htmlspecialchars($user['name']) . ' (' . ucfirst($role) . ')">';
        echo '      ' . $user['initials'];
        echo '    </div>';
        echo '  </div>';
        echo '</header>';

        // Breadcrumbs Bar (Trilha de Navegação Moodle 4.x)
        echo '<div class="moodle-breadcrumbs-bar">';
        echo '  <nav class="moodle-breadcrumbs" aria-label="Trilha de navegação">';
        echo '    <a href="index.php?role=' . $role . '">Início</a>';
        echo '    <span class="separator">/</span>';
        echo '    <span>Meus Cursos</span>';
        echo '    <span class="separator">/</span>';
        echo '    <a href="index.php?role=' . $role . '">Monitoria Online</a>';
        if (!empty($breadcrumbs)) {
            foreach ($breadcrumbs as $crumb) {
                echo '    <span class="separator">/</span>';
                if (!empty($crumb['url'])) {
                    echo '    <a href="' . htmlspecialchars($crumb['url']) . '">' . htmlspecialchars($crumb['label']) . '</a>';
                } else {
                    echo '    <span class="active-crumb">' . htmlspecialchars($crumb['label']) . '</span>';
                }
            }
        } else {
            echo '    <span class="separator">/</span>';
            echo '    <span class="active-crumb">' . htmlspecialchars($page_title) . '</span>';
        }
        echo '  </nav>';
        echo '</div>';

        // Main Layout Wrapper with Sidebar Drawer
        echo '<div class="moodle-layout-wrapper">';
        
        // Drawer Navigation (Sidebar LMS)
        echo '  <aside class="moodle-drawer" id="moodleDrawer">';
        echo '    <div class="drawer-header">';
        echo '      <h3>Menu Acadêmico</h3>';
        echo '      <span class="badge badge-success">' . ucfirst($role) . '</span>';
        echo '    </div>';
        echo '    <nav class="drawer-nav">';
        
        $drawer_items = [
            'dashboard' => ['label' => 'Painel Geral', 'icon' => '📊', 'url' => 'index.php'],
            'agendamento' => ['label' => 'Agendamentos & Vagas', 'icon' => '🗓️', 'url' => 'agendamento.php'],
        ];

        if (in_array($role, ['monitor', 'professor', 'coordenador'])) {
            $drawer_items['atendimentos'] = ['label' => 'Gestão de Atendimentos', 'icon' => '📋', 'url' => 'atendimentos.php'];
        }

        $drawer_items['duvidas'] = ['label' => 'Central de Dúvidas', 'icon' => '💬', 'url' => 'duvidas.php'];

        if (in_array($role, ['professor', 'coordenador'])) {
            $drawer_items['gestao'] = ['label' => 'Disciplinas & Monitores', 'icon' => '⚙️', 'url' => 'admin_gestao.php'];
        }

        foreach ($drawer_items as $key => $item) {
            $isActive = ($key === $active_tab) ? 'active' : '';
            echo "    <a href=\"{$item['url']}?role=$role\" class=\"drawer-item $isActive\">";
            echo "      <span class=\"drawer-icon\">{$item['icon']}</span>";
            echo "      <span class=\"drawer-label\">{$item['label']}</span>";
            echo "    </a>";
        }

        echo '    </nav>';
        echo '    <div class="drawer-footer-card">';
        echo '      <p><strong>Apoio ao Estudante</strong></p>';
        echo '      <small>Atendimento remoto para alunos com dificuldades de transporte no contraturno.</small>';
        echo '    </div>';
        echo '  </aside>';

        // Main Content Container
        echo '  <main class="moodle-main-content">';
        echo '    <div class="moodle-page-header">';
        echo '      <div>';
        echo '        <h1>' . htmlspecialchars($page_title) . '</h1>';
        echo '        <p class="subtitle">Instituto Federal de São Paulo — Ambiente Virtual de Aprendizagem</p>';
        echo '      </div>';
        echo '      <div class="moodle-header-actions">';
        echo '        <span class="user-status-pill"><span class="status-indicator"></span> Conectado como <strong>' . htmlspecialchars($user['name']) . '</strong></span>';
        echo '      </div>';
        echo '    </div>';
    }

    public static function render_footer(): void {
        global $OUTPUT;

        $has_moodle_output = (!empty($OUTPUT) && method_exists($OUTPUT, 'footer'));

        echo '    <footer class="moodle-footer-signature">';
        echo '      <div class="footer-inner">';
        echo '        <p class="inst-text"><strong>Instituto Federal de Educação, Ciência e Tecnologia de São Paulo (IFSP)</strong> — Câmpus São Paulo</p>';
        echo '        <p class="academic-text">Disciplina: Desenvolvimento Web • Projeto de Extensão: <em>Monitoria Online</em></p>';
        echo '        <p class="credits-text">Equipe GEN Corp: Gabriel Forti • Eduardo Gritti • Nicolas Santana</p>';
        echo '        <p class="sig-text">Desenvolvido com excelência por <strong>NextCodeGen &lt;/&gt;</strong></p>';
        echo '      </div>';
        echo '    </footer>';
        echo '  </main>'; // Close .moodle-main-content
        echo '</div>';   // Close .moodle-layout-wrapper

        if ($has_moodle_output) {
            echo $OUTPUT->footer();
        } else {
            echo '</body>';
            echo '</html>';
        }
    }
}
