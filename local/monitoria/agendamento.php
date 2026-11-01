<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Scheduling and Booking Sessions Panel (US03 - SCRUM-69)
 *
 * @package    local_monitoria
 * @copyright  2026 IFSP - Grupo Monitoria Online (GEN Corp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (file_exists(__DIR__ . '/../../config.php')) {
    try {
        require_once(__DIR__ . '/../../config.php');
        if (!empty($PAGE)) {
            $PAGE->set_url(new moodle_url('/local/monitoria/agendamento.php'));
            $PAGE->set_context(\context_system::instance());
            $PAGE->set_pagelayout('standard');
            $PAGE->set_title('Agendamentos — Monitoria Online');
            $PAGE->set_heading('Agendamento de Monitorias');
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
$feedback_msg = null;

// POST Handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Student books a slot
    if ($action === 'agendar_sessao') {
        $slot_id = (int)($_POST['slot_id'] ?? 0);
        $student_name = $_POST['student_name'] ?? 'Aluno IFSP';
        $topic = $_POST['topic'] ?? '';

        $res = monitoria_manager::book_session($slot_id, $student_name, $topic);
        if ($res['success']) {
            $feedback_msg = ['type' => 'success', 'text' => $res['message']];
        } else {
            $feedback_msg = ['type' => 'error', 'text' => $res['message']];
        }
    }

    // Monitor creates a new available slot
    if ($action === 'criar_horario') {
        $monitor_id = (int)($_POST['monitor_id'] ?? 1);
        $weekday = $_POST['weekday'] ?? 'Segunda-feira';
        $date_slot = $_POST['date_slot'] ?? date('Y-m-d');
        $start_time = $_POST['start_time'] ?? '14:00';
        $end_time = $_POST['end_time'] ?? '16:00';
        $max_capacity = (int)($_POST['max_capacity'] ?? 5);
        $type = $_POST['type'] ?? 'Híbrido (Lab + Google Meet)';
        $meet_url = $_POST['meet_url'] ?? 'https://meet.google.com/ifsp-monitoria';

        $monitors = monitoria_manager::get_all_monitors();
        $mon = array_values(array_filter($monitors, fn($m) => (int)$m['id'] === $monitor_id))[0] ?? null;

        if ($mon) {
            monitoria_manager::create_slot(
                $mon['id'],
                $mon['name'],
                $mon['course_id'],
                $mon['course_name'],
                $weekday,
                $date_slot,
                $start_time,
                $end_time,
                $max_capacity,
                $type,
                $meet_url
            );
            $feedback_msg = ['type' => 'success', 'text' => 'Novo horário de monitoria publicado com sucesso na grade!'];
        }
    }
}

$slots = monitoria_manager::get_available_slots();
$bookings = monitoria_manager::get_all_bookings();
$monitors = monitoria_manager::get_all_monitors();

controller::render_header('agendamento', 'Agendamentos & Horários de Monitoria');
?>

<?php if ($feedback_msg): ?>
  <div style="padding:1rem; margin-bottom:1.5rem; border-radius:8px; background:<?php echo $feedback_msg['type'] === 'success' ? '#DCFCE7' : '#FEE2E2'; ?>; color:<?php echo $feedback_msg['type'] === 'success' ? '#16A34A' : '#DC2626'; ?>; font-weight:600;">
    <?php echo $feedback_msg['type'] === 'success' ? '✅' : '⚠️'; ?> <?php echo htmlspecialchars($feedback_msg['text']); ?>
  </div>
<?php endif; ?>

<?php if (in_array($role, ['monitor', 'professor', 'coordenador'])): ?>
<!-- FORM PARA MONITOR CADASTRAR DISPONIBILIDADE -->
<div class="card-box" style="margin-bottom:2rem;">
  <div class="card-header-flex">
    <h2>➕ Disponibilizar Novo Horário de Atendimento</h2>
  </div>
  <form method="POST" action="" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:1rem; align-items:end;">
    <input type="hidden" name="action" value="criar_horario">
    <div class="form-group" style="margin-bottom:0;">
      <label>Monitor Responsável</label>
      <select name="monitor_id" class="form-control-mo">
        <?php foreach ($monitors as $m): ?>
          <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['name'] . ' (' . $m['course_name'] . ')'); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group" style="margin-bottom:0;">
      <label>Dia da Semana</label>
      <select name="weekday" class="form-control-mo">
        <option>Segunda-feira</option>
        <option>Terça-feira</option>
        <option>Quarta-feira</option>
        <option>Quinta-feira</option>
        <option>Sexta-feira</option>
      </select>
    </div>
    <div class="form-group" style="margin-bottom:0;">
      <label>Data</label>
      <input type="date" name="date_slot" class="form-control-mo" value="<?php echo date('Y-m-d', strtotime('+2 days')); ?>" required>
    </div>
    <div class="form-group" style="margin-bottom:0;">
      <label>Início</label>
      <input type="time" name="start_time" class="form-control-mo" value="14:00" required>
    </div>
    <div class="form-group" style="margin-bottom:0;">
      <label>Fim</label>
      <input type="time" name="end_time" class="form-control-mo" value="16:00" required>
    </div>
    <div class="form-group" style="margin-bottom:0;">
      <label>Vagas Máximas</label>
      <input type="number" name="max_capacity" class="form-control-mo" value="5" min="1" max="20" required>
    </div>
    <div style="grid-column: 1 / -1; margin-top:0.5rem;">
      <button type="submit" class="btn-mo btn-mo-primary">+ Publicar Horário na Grade</button>
    </div>
  </form>
</div>
<?php endif; ?>

<!-- GRADE DE HORÁRIOS DISPONÍVEIS PARA OS ALUNOS -->
<div class="card-box">
  <div class="card-header-flex">
    <h2>🗓️ Grade de Horários Disponíveis para Monitoria</h2>
    <span class="badge badge-confirmado"><?php echo count($slots); ?> Sessões Abertas</span>
  </div>
  <p style="color:var(--mo-text-muted); font-size:0.95rem;">
    Escolha uma das sessões abaixo para garantir sua vaga presencial ou receber o link do Google Meet com o monitor.
  </p>

  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:1.25rem;">
    <?php foreach ($slots as $s): ?>
      <?php
        $vagas_restantes = (int)$s['max_capacity'] - (int)$s['current_bookings'];
        $is_lotado = $vagas_restantes <= 0;
      ?>
      <div style="border:1px solid var(--mo-border); border-radius:12px; padding:1.25rem; background:#FFFFFF; display:flex; flex-direction:column; justify-content:space-between; box-shadow:var(--mo-shadow-sm);">
        <div>
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem;">
            <span class="badge badge-neutral"><?php echo htmlspecialchars($s['weekday']); ?></span>
            <span class="badge <?php echo $is_lotado ? 'badge-danger' : 'badge-confirmado'; ?>">
              <?php echo $is_lotado ? 'Lotado' : "$vagas_restantes vagas livres"; ?>
            </span>
          </div>
          <h3 style="margin:0 0 0.5rem 0; font-size:1.15rem; color:var(--mo-text-main);"><?php echo htmlspecialchars($s['course_name']); ?></h3>
          <p style="margin:0 0 0.25rem 0; font-size:0.9rem; color:var(--mo-text-muted);">
            👨‍💻 Monitor: <strong><?php echo htmlspecialchars($s['monitor_name']); ?></strong>
          </p>
          <p style="margin:0 0 0.25rem 0; font-size:0.9rem; color:var(--mo-text-muted);">
            🕒 Horário: <strong><?php echo htmlspecialchars($s['start_time']); ?> às <?php echo htmlspecialchars($s['end_time']); ?></strong> (<?php echo htmlspecialchars($s['date_slot']); ?>)
          </p>
          <p style="margin:0 0 1rem 0; font-size:0.875rem; color:var(--mo-text-subtle);">
            📍 Modalidade: <?php echo htmlspecialchars($s['type']); ?>
          </p>
        </div>

        <?php if (!$is_lotado): ?>
          <form method="POST" action="" style="border-top:1px solid var(--mo-border); padding-top:1rem; margin-top:0.5rem;">
            <input type="hidden" name="action" value="agendar_sessao">
            <input type="hidden" name="slot_id" value="<?php echo $s['id']; ?>">
            <div class="form-group" style="margin-bottom:0.75rem;">
              <label style="font-size:0.85rem;">Nome do Aluno</label>
              <input type="text" name="student_name" class="form-control-mo" style="min-height:40px; font-size:0.9rem;" value="Nicolas de Azevedo Andrade Santana" required>
            </div>
            <div class="form-group" style="margin-bottom:0.75rem;">
              <label style="font-size:0.85rem;">Qual o tema da sua dúvida?</label>
              <input type="text" name="topic" class="form-control-mo" style="min-height:40px; font-size:0.9rem;" placeholder="Ex: Exercício 4 de PHP / Dúvida em CSS" required>
            </div>
            <button type="submit" class="btn-mo btn-mo-primary" style="width:100%; min-height:44px;">
              Confirmar Agendamento
            </button>
          </form>
        <?php else: ?>
          <button class="btn-mo btn-mo-secondary" disabled style="width:100%; min-height:44px; opacity:0.6; cursor:not-allowed;">
            Sessão Esgotada
          </button>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php controller::render_footer(); ?>
