<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Tutor Session Management & Approvals Panel (SCRUM-46 / SCRUM-48 / US04)
 *
 * @package    local_monitoria
 * @copyright  2026 IFSP - Grupo Monitoria Online (GEN Corp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (file_exists(__DIR__ . '/../../config.php')) {
    try {
        require_once(__DIR__ . '/../../config.php');
        if (!empty($PAGE)) {
            $PAGE->set_url(new moodle_url('/local/monitoria/atendimentos.php'));
            $PAGE->set_context(\context_system::instance());
            $PAGE->set_pagelayout('standard');
            $PAGE->set_title('Gestão de Atendimentos — Monitoria Online');
            $PAGE->set_heading('Atendimentos do Monitor');
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
$feedback_msg = null;

// POST actions for session management
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $booking_id = (int)($_POST['booking_id'] ?? 0);

    if ($action === 'aprovar_agendamento' && $booking_id > 0) {
        monitoria_manager::update_booking_status($booking_id, 'confirmado');
        $feedback_msg = ['type' => 'success', 'text' => "Atendimento #{$booking_id} aprovado e confirmado na agenda do monitor!"];
    }

    if ($action === 'recusar_agendamento' && $booking_id > 0) {
        $motivo = $_POST['motivo'] ?? 'Horário indisponível por motivo acadêmico.';
        monitoria_manager::update_booking_status($booking_id, 'recusado', 0, $motivo);
        $feedback_msg = ['type' => 'warning', 'text' => "Atendimento #{$booking_id} recusado com justificativa encaminhada ao aluno."];
    }

    if ($action === 'concluir_atendimento' && $booking_id > 0) {
        $presenca = isset($_POST['attended']) ? 1 : 0;
        $notes = $_POST['notes'] ?? 'Atendimento finalizado com resolução da dúvida.';
        monitoria_manager::update_booking_status($booking_id, 'concluido', $presenca, $notes);
        $feedback_msg = ['type' => 'success', 'text' => "Atendimento #{$booking_id} concluído com sucesso e presença registrada!"];
    }
}

$all_bookings = monitoria_manager::get_all_bookings();
$slots = monitoria_manager::get_available_slots();

// Count metrics
$pendentes = count(array_filter($all_bookings, fn($b) => in_array($b['status'] ?? '', ['agendado', 'pendente'])));
$confirmados = count(array_filter($all_bookings, fn($b) => ($b['status'] ?? '') === 'confirmado'));
$concluidos = count(array_filter($all_bookings, fn($b) => ($b['status'] ?? '') === 'concluido'));

controller::render_header('atendimentos', 'Gestão de Atendimentos do Monitor');
?>

<?php if ($feedback_msg): ?>
  <div style="padding:1rem; margin-bottom:1.5rem; border-radius:8px; background:<?php echo $feedback_msg['type'] === 'success' ? '#DCFCE7' : '#FEF3C7'; ?>; color:<?php echo $feedback_msg['type'] === 'success' ? '#16A34A' : '#D97706'; ?>; font-weight:600;">
    ✅ <?php echo htmlspecialchars($feedback_msg['text']); ?>
  </div>
<?php endif; ?>

<!-- METRICS CARDS DO MONITOR -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon">⏳</div>
    <div class="stat-details">
      <h3><?php echo $pendentes; ?></h3>
      <span>Aguardando Aprovação</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">📅</div>
    <div class="stat-details">
      <h3><?php echo $confirmados; ?></h3>
      <span>Sessões Confirmadas</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">🎓</div>
    <div class="stat-details">
      <h3><?php echo $concluidos; ?></h3>
      <span>Atendimentos Realizados</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">👨‍🏫</div>
    <div class="stat-details">
      <h3><?php echo count($slots); ?></h3>
      <span>Minhas Vagas Ofertadas</span>
    </div>
  </div>
</div>

<!-- GESTÃO DE ATENDIMENTOS E APROVAÇÕES -->
<div class="card-box">
  <div class="card-header-flex">
    <h2>📋 Painel de Gestão e Aprovação de Atendimentos</h2>
    <span class="badge badge-confirmado"><?php echo count($all_bookings); ?> Solicitações Registradas</span>
  </div>
  <p style="color:var(--mo-text-muted); font-size:0.95rem;">
    Acompanhe as solicitações de alunos, aprove horários, entre na sala virtual do Google Meet e registre a assiduidade discente.
  </p>

  <div class="table-responsive">
    <table class="table-mo">
      <thead>
        <tr>
          <th>Aluno Solicitante</th>
          <th>Disciplina & Tema</th>
          <th>Data & Horário</th>
          <th>Status Atual</th>
          <th>Presença</th>
          <th style="min-width:240px;">Ações do Monitor</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($all_bookings)): ?>
          <?php foreach ($all_bookings as $b): ?>
            <?php
              $st = $b['status'] ?? 'agendado';
              $badgeClass = match($st) {
                'confirmado' => 'badge-confirmado',
                'concluido' => 'badge-concluido',
                'recusado', 'cancelado' => 'badge-recusado',
                default => 'badge-pendente'
              };
            ?>
            <tr>
              <td>
                <strong><?php echo htmlspecialchars($b['student_name'] ?? 'Aluno IFSP'); ?></strong><br>
                <small style="color:var(--mo-text-subtle);">Monitor: <?php echo htmlspecialchars($b['monitor_name'] ?? ''); ?></small>
              </td>
              <td>
                <span class="badge badge-neutral"><?php echo htmlspecialchars($b['course_name'] ?? 'Geral'); ?></span><br>
                <small style="color:var(--mo-text-muted);"><em>"<?php echo htmlspecialchars($b['topic'] ?? 'Dúvida geral'); ?>"</em></small>
              </td>
              <td>
                📅 <?php echo htmlspecialchars($b['date_slot'] ?? ''); ?><br>
                🕒 <?php echo htmlspecialchars($b['time_slot'] ?? ''); ?>
              </td>
              <td>
                <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($st); ?></span>
              </td>
              <td>
                <?php if ($st === 'concluido'): ?>
                  <?php echo (!empty($b['attended'])) ? '<span class="badge badge-confirmado">Presente</span>' : '<span class="badge badge-recusado">Ausente</span>'; ?>
                <?php else: ?>
                  <span style="color:var(--mo-text-subtle);">Pendente</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($st === 'agendado' || $st === 'pendente'): ?>
                  <!-- APROVAR OU RECUSAR -->
                  <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <form method="POST" action="" style="display:inline;">
                      <input type="hidden" name="action" value="aprovar_agendamento">
                      <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                      <button type="submit" class="btn-mo btn-mo-primary" style="min-height:36px; padding:0.25rem 0.75rem; font-size:0.85rem;">
                        ✓ Aprovar
                      </button>
                    </form>
                    <form method="POST" action="" style="display:inline;">
                      <input type="hidden" name="action" value="recusar_agendamento">
                      <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                      <button type="submit" class="btn-mo btn-mo-secondary" style="min-height:36px; padding:0.25rem 0.75rem; font-size:0.85rem; color:#DC2626;">
                        ✕ Recusar
                      </button>
                    </form>
                  </div>

                <?php elseif ($st === 'confirmado'): ?>
                  <!-- CONCLUIR E MARCAR PRESENÇA -->
                  <form method="POST" action="" style="background:var(--mo-bg); padding:0.65rem; border-radius:8px; border:1px solid var(--mo-border);">
                    <input type="hidden" name="action" value="concluir_atendimento">
                    <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                    <div style="margin-bottom:0.4rem;">
                      <label style="font-size:0.825rem; font-weight:600; cursor:pointer;">
                        <input type="checkbox" name="attended" value="1" checked> Aluno compareceu à monitoria
                      </label>
                    </div>
                    <div style="margin-bottom:0.4rem;">
                      <input type="text" name="notes" placeholder="Resumo dos tópicos sanados..." class="form-control-mo" style="min-height:36px; font-size:0.85rem; padding:0.25rem 0.5rem;">
                    </div>
                    <button type="submit" class="btn-mo btn-mo-primary" style="min-height:36px; padding:0.3rem 0.75rem; font-size:0.85rem; width:100%;">
                      Finalizar Atendimento
                    </button>
                  </form>

                <?php else: ?>
                  <span style="color:var(--mo-text-muted); font-size:0.85rem;">
                    <?php echo !empty($b['notes']) ? htmlspecialchars($b['notes']) : 'Concluído'; ?>
                  </span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" style="text-align:center; color:var(--mo-text-muted);">Nenhum agendamento para gerenciar.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php controller::render_footer(); ?>
