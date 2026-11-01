<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Main entry point and dynamic dashboard for local_monitoria.
 *
 * @package    local_monitoria
 * @copyright  2026 IFSP - Grupo Monitoria Online (GEN Corp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (file_exists(__DIR__ . '/../../config.php')) {
    try {
        require_once(__DIR__ . '/../../config.php');
        if (!empty($PAGE)) {
            $PAGE->set_url(new moodle_url('/local/monitoria/index.php'));
            $PAGE->set_context(\context_system::instance());
            $PAGE->set_pagelayout('standard');
            $PAGE->set_title('Monitoria Online — IFSP');
            $PAGE->set_heading('Monitoria Online');
        }
    } catch (\Throwable $e) {
        if (!defined('MOODLE_INTERNAL')) define('MOODLE_INTERNAL', true);
    }
} else {
    if (!defined('MOODLE_INTERNAL')) define('MOODLE_INTERNAL', true);
}

require_once(__DIR__ . '/classes/controller.php');
require_once(__DIR__ . '/classes/monitoria_manager.php');

use local_monitoria\controller;
use local_monitoria\monitoria_manager;

$role = controller::get_active_role();
$user = controller::get_active_user();
$metrics = monitoria_manager::get_dashboard_metrics($role);
$slots = monitoria_manager::get_available_slots();
$bookings = monitoria_manager::get_all_bookings();
$doubts = monitoria_manager::get_all_doubts();

controller::render_header('dashboard', 'Painel Geral — Monitoria Online');
?>

<!-- METRICS OVERVIEW -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon">📚</div>
    <div class="stat-details">
      <h3><?php echo $metrics['total_courses']; ?></h3>
      <span>Disciplinas Ofertadas</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">👨‍💻</div>
    <div class="stat-details">
      <h3><?php echo $metrics['total_monitors']; ?></h3>
      <span>Monitores Ativos</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">🗓️</div>
    <div class="stat-details">
      <h3><?php echo $metrics['available_slots']; ?></h3>
      <span>Horários Disponíveis</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">💬</div>
    <div class="stat-details">
      <h3><?php echo $metrics['open_doubts']; ?></h3>
      <span>Dúvidas Pendentes</span>
    </div>
  </div>
</div>

<!-- COURSE CARDS CATALOG -->
<div class="card-box">
  <div class="card-header-flex">
    <h2>Disciplinas e Monitores do Semestre</h2>
    <a href="agendamento.php?role=<?php echo $role; ?>" class="btn-mo btn-mo-primary">Ver Todos os Horários</a>
  </div>
  <div class="course-grid">
    <?php foreach (monitoria_manager::get_all_courses() as $c): ?>
      <?php
        $mon = array_values(array_filter(monitoria_manager::get_all_monitors(), fn($m) => (int)$m['course_id'] === (int)$c['id']))[0] ?? null;
      ?>
      <div class="moodle-course-card">
        <div class="course-card-banner">
          <span class="course-code-badge"><?php echo htmlspecialchars($c['code']); ?></span>
          <span class="badge badge-confirmado">Carga: <?php echo $c['workload']; ?>h</span>
        </div>
        <div class="course-card-body">
          <h3><?php echo htmlspecialchars($c['name']); ?></h3>
          <div class="course-meta">
            <span><strong>Departamento:</strong> <?php echo htmlspecialchars($c['department']); ?></span><br>
            <span><strong>Monitor:</strong> <?php echo $mon ? htmlspecialchars($mon['name']) : 'A definir'; ?></span>
          </div>
          <div class="course-card-actions">
            <?php if ($mon && !empty($mon['meet_link'])): ?>
              <a href="<?php echo htmlspecialchars($mon['meet_link']); ?>" target="_blank" rel="noopener noreferrer" class="btn-mo btn-mo-secondary" style="flex:1; min-height:42px; font-size:0.875rem;">
                🔗 Sala Meet
              </a>
            <?php endif; ?>
            <a href="agendamento.php?role=<?php echo $role; ?>&course_id=<?php echo $c['id']; ?>" class="btn-mo btn-mo-primary" style="flex:1; min-height:42px; font-size:0.875rem;">
              🗓️ Agendar
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php if ($role === 'aluno'): ?>
<!-- VISÃO DO ALUNO -->
<div class="card-box">
  <div class="card-header-flex">
    <h2>Meus Agendamentos Recentes</h2>
    <a href="agendamento.php?role=aluno" class="btn-mo btn-mo-primary">+ Agendar Atendimento</a>
  </div>
  <div class="table-responsive">
    <table class="table-mo">
      <thead>
        <tr>
          <th>Disciplina</th>
          <th>Monitor Responsável</th>
          <th>Data & Horário</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($bookings)): ?>
          <?php foreach ($bookings as $b): ?>
            <?php
              $st = strtolower($b['status'] ?? 'agendado');
              $badge_cls = 'badge-agendado';
              if ($st === 'confirmado') $badge_cls = 'badge-confirmado';
              if ($st === 'pendente') $badge_cls = 'badge-pendente';
              if ($st === 'concluido') $badge_cls = 'badge-concluido';
              if ($st === 'recusado') $badge_cls = 'badge-recusado';
            ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($b['course_name']); ?></strong></td>
              <td><?php echo htmlspecialchars($b['monitor_name']); ?></td>
              <td>📅 <?php echo htmlspecialchars($b['date_slot']); ?> às <?php echo htmlspecialchars($b['time_slot']); ?></td>
              <td><span class="badge <?php echo $badge_cls; ?>"><?php echo ucfirst($st); ?></span></td>
              <td>
                <a href="duvidas.php?role=aluno" class="btn-mo btn-mo-secondary" style="min-height:36px; padding:0.25rem 0.75rem; font-size:0.85rem;">Enviar Dúvida Prévia</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" style="text-align:center; color:#64748B;">Nenhum agendamento realizado até o momento.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php elseif ($role === 'monitor'): ?>
<!-- VISÃO DO MONITOR -->
<div class="card-box">
  <div class="card-header-flex">
    <h2>Minha Escala de Monitoria & Vagas</h2>
    <a href="agendamento.php?role=monitor" class="btn-mo btn-mo-primary">+ Novo Horário de Atendimento</a>
  </div>
  <div class="table-responsive">
    <table class="table-mo">
      <thead>
        <tr>
          <th>Dia da Semana</th>
          <th>Horário</th>
          <th>Modalidade / Local</th>
          <th>Ocupação</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($slots as $s): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($s['weekday']); ?> (<?php echo htmlspecialchars($s['date_slot']); ?>)</strong></td>
            <td><?php echo htmlspecialchars($s['start_time']); ?> - <?php echo htmlspecialchars($s['end_time']); ?></td>
            <td>
              <?php echo htmlspecialchars($s['type']); ?><br>
              <small><a href="<?php echo htmlspecialchars($s['meet_url']); ?>" target="_blank" rel="noopener noreferrer" style="color:var(--mo-primary); font-weight:600;">🔗 Entrar na Sala</a></small>
            </td>
            <td><span class="badge badge-neutral"><?php echo $s['current_bookings']; ?> / <?php echo $s['max_capacity']; ?> inscritos</span></td>
            <td><span class="badge badge-confirmado">Disponível</span></td>
            <td>
              <a href="atendimentos.php?role=monitor" class="btn-mo btn-mo-secondary" style="min-height:36px; padding:0.25rem 0.75rem; font-size:0.85rem;">Gerenciar Alunos</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php else: ?>
<!-- VISÃO DO PROFESSOR / COORDENADOR -->
<div class="card-box">
  <div class="card-header-flex">
    <h2>Visão Geral da Coordenação Acadêmica</h2>
    <a href="admin_gestao.php?role=<?php echo $role; ?>" class="btn-mo btn-mo-primary">⚙️ Painel de Gestão Completo</a>
  </div>
  <p style="color:var(--mo-text-muted); font-size:0.95rem;">
    Acompanhamento das turmas, alocação de monitores e indicadores de produtividade do Instituto Federal.
  </p>
  <div class="table-responsive">
    <table class="table-mo">
      <thead>
        <tr>
          <th>Código</th>
          <th>Disciplina</th>
          <th>Monitores Atribuídos</th>
          <th>Horários Oferecidos</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (monitoria_manager::get_all_courses() as $c): ?>
          <tr>
            <td><span class="badge badge-neutral"><?php echo htmlspecialchars($c['code']); ?></span></td>
            <td><strong><?php echo htmlspecialchars($c['name']); ?></strong></td>
            <td>
              <?php
                $assigned = array_filter(monitoria_manager::get_all_monitors(), fn($m) => (int)$m['course_id'] === (int)$c['id']);
                echo !empty($assigned) ? count($assigned) . ' monitor(es)' : '<span style="color:#DC2626;">Sem monitor</span>';
              ?>
            </td>
            <td><?php echo count(array_filter($slots, fn($s) => (int)$s['course_id'] === (int)$c['id'])); ?> sessões semanais</td>
            <td><span class="badge badge-confirmado">Ativa</span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- ÚLTIMAS DÚVIDAS DO FÓRUM -->
<div class="card-box">
  <div class="card-header-flex">
    <h2>Dúvidas Recentes no Fórum</h2>
    <a href="duvidas.php?role=<?php echo $role; ?>" class="btn-mo btn-mo-secondary">Acessar Fórum Completo</a>
  </div>
  <?php if (!empty($doubts)): ?>
    <?php foreach (array_slice($doubts, 0, 3) as $d): ?>
      <div style="padding: 1rem 0; border-bottom: 1px solid var(--mo-border);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.25rem;">
          <h4 style="margin:0; font-size:1.05rem; color:var(--mo-text-main);"><?php echo htmlspecialchars($d['title']); ?></h4>
          <span class="badge <?php echo ($d['status'] === 'respondida') ? 'badge-confirmado' : 'badge-pendente'; ?>"><?php echo ucfirst($d['status']); ?></span>
        </div>
        <p style="margin:0 0 0.5rem 0; font-size:0.9rem; color:var(--mo-text-muted);"><?php echo htmlspecialchars($d['description']); ?></p>
        <small style="color:var(--mo-text-subtle);">Por <strong><?php echo htmlspecialchars($d['student_name']); ?></strong> em <em><?php echo htmlspecialchars($d['course_name']); ?></em></small>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="color:var(--mo-text-muted);">Nenhuma dúvida registrada ainda.</p>
  <?php endif; ?>
</div>

<?php controller::render_footer(); ?>
