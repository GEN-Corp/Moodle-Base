<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Q&A and Academic Doubts Center (US05 - SCRUM-77)
 *
 * @package    local_monitoria
 * @copyright  2026 IFSP - Grupo Monitoria Online (GEN Corp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (file_exists(__DIR__ . '/../../config.php')) {
    try {
        require_once(__DIR__ . '/../../config.php');
        if (!empty($PAGE)) {
            $PAGE->set_url(new moodle_url('/local/monitoria/duvidas.php'));
            $PAGE->set_context(\context_system::instance());
            $PAGE->set_pagelayout('standard');
            $PAGE->set_title('Central de Dúvidas — Monitoria Online');
            $PAGE->set_heading('Central de Dúvidas & Fórum');
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

    // Student submits a new doubt
    if ($action === 'enviar_duvida') {
        $student_name = $_POST['student_name'] ?? 'Aluno IFSP';
        $course_id = (int)($_POST['course_id'] ?? 0);
        $title = $_POST['title'] ?? '';
        $desc = $_POST['description'] ?? '';

        $courses = monitoria_manager::get_all_courses();
        $course = array_values(array_filter($courses, fn($c) => (int)$c['id'] === $course_id))[0] ?? null;

        if ($course && !empty($title) && !empty($desc)) {
            monitoria_manager::submit_doubt($student_name, $course_id, $course['name'], $title, $desc);
            $feedback_msg = ['type' => 'success', 'text' => 'Dúvida enviada com sucesso! Os monitores foram notificados.'];
        }
    }

    // Monitor or Professor replies to doubt
    if ($action === 'responder_duvida') {
        $doubt_id = (int)($_POST['doubt_id'] ?? 0);
        $replied_by = $_POST['replied_by'] ?? 'Monitor Responsável';
        $reply = $_POST['reply'] ?? '';

        if (!empty($reply)) {
            monitoria_manager::reply_doubt($doubt_id, $replied_by, $reply);
            $feedback_msg = ['type' => 'success', 'text' => 'Resposta publicada com sucesso no fórum acadêmico!'];
        }
    }
}

$courses = monitoria_manager::get_all_courses();
$doubts = monitoria_manager::get_all_doubts();

controller::render_header('duvidas', 'Central de Dúvidas & Fórum Acadêmico');
?>

<?php if ($feedback_msg): ?>
  <div style="padding:1rem; margin-bottom:1.5rem; border-radius:8px; background:<?php echo $feedback_msg['type'] === 'success' ? '#DCFCE7' : '#FEE2E2'; ?>; color:<?php echo $feedback_msg['type'] === 'success' ? '#16A34A' : '#DC2626'; ?>; font-weight:600;">
    ✅ <?php echo htmlspecialchars($feedback_msg['text']); ?>
  </div>
<?php endif; ?>

<!-- FORM: ENVIAR DÚVIDA -->
<div class="card-box" style="margin-bottom:2rem;">
  <div class="card-header-flex">
    <h2>💬 Enviar Nova Dúvida para a Monitoria</h2>
  </div>
  <p style="color:var(--mo-text-muted); font-size:0.95rem; margin-top:0;">
    Envie sua pergunta antecipada. O monitor poderá responder online ou adiantar o material para a próxima sessão presencial/Meet.
  </p>
  <form method="POST" action="">
    <input type="hidden" name="action" value="enviar_duvida">
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:1rem; margin-bottom:1rem;">
      <div class="form-group" style="margin-bottom:0;">
        <label>Seu Nome / Identificação</label>
        <input type="text" name="student_name" class="form-control-mo" value="Nicolas de Azevedo Andrade Santana" required>
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label>Disciplina Relacionada</label>
        <select name="course_id" class="form-control-mo" required>
          <?php foreach ($courses as $c): ?>
            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['code'] . ' - ' . $c['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label>Título Resumido da Dúvida</label>
      <input type="text" name="title" class="form-control-mo" placeholder="Ex: Dificuldade em normalização de banco de dados (3FN)" required>
    </div>
    <div class="form-group">
      <label>Descrição Detalhada do Problema ou Exercício</label>
      <textarea name="description" class="form-control-mo" rows="4" style="height:auto;" placeholder="Explique o que você tentou fazer, mensagens de erro ou dúvidas conceituais..." required></textarea>
    </div>
    <button type="submit" class="btn-mo btn-mo-primary">+ Publicar Dúvida no Fórum</button>
  </form>
</div>

<!-- LISTA DE DÚVIDAS E RESPOSTAS -->
<div class="card-box">
  <div class="card-header-flex">
    <h2>Fórum de Perguntas e Respostas da Monitoria</h2>
    <span class="badge badge-confirmado"><?php echo count($doubts); ?> Tópicos Cadastrados</span>
  </div>

  <?php if (!empty($doubts)): ?>
    <?php foreach ($doubts as $d): ?>
      <?php $is_respondida = ($d['status'] === 'respondida'); ?>
      <div style="border:1px solid var(--mo-border); border-radius:12px; padding:1.5rem; margin-bottom:1.5rem; background:#FFFFFF;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem;">
          <div>
            <span class="badge badge-neutral" style="margin-bottom:0.5rem;"><?php echo htmlspecialchars($d['course_name']); ?></span>
            <h3 style="margin:0.25rem 0 0 0; font-size:1.2rem; color:var(--mo-text-main);"><?php echo htmlspecialchars($d['title']); ?></h3>
          </div>
          <span class="badge <?php echo $is_respondida ? 'badge-confirmado' : 'badge-pendente'; ?>">
            <?php echo $is_respondida ? 'Respondida' : 'Aguardando Resposta'; ?>
          </span>
        </div>

        <p style="color:var(--mo-text-muted); font-size:0.95rem; line-height:1.5; margin:0 0 1rem 0;">
          <?php echo nl2br(htmlspecialchars($d['description'])); ?>
        </p>

        <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.85rem; color:var(--mo-text-subtle); margin-bottom:1rem;">
          <span>👤 Autor: <strong><?php echo htmlspecialchars($d['student_name']); ?></strong></span>
          <span>•</span>
          <span>📅 <?php echo date('d/m/Y H:i', $d['timecreated'] ?? time()); ?></span>
        </div>

        <?php if (!empty($d['reply'])): ?>
          <!-- RESPOSTA DO MONITOR -->
          <div style="background:var(--mo-primary-light); border-left:4px solid var(--mo-primary); padding:1rem 1.25rem; border-radius:0 8px 8px 0; margin-top:1rem;">
            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.35rem;">
              <span style="font-weight:700; color:var(--mo-primary); font-size:0.9rem;">
                👨‍🏫 Resposta de <?php echo htmlspecialchars($d['replied_by']); ?>:
              </span>
            </div>
            <p style="margin:0; font-size:0.925rem; color:var(--mo-text-main); line-height:1.5;">
              <?php echo nl2br(htmlspecialchars($d['reply'])); ?>
            </p>
          </div>
        <?php else: ?>
          <!-- ÁREA PARA RESPONDER SE FOR MONITOR OU PROFESSOR -->
          <?php if (in_array($role, ['monitor', 'professor', 'coordenador'])): ?>
            <form method="POST" action="" style="background:var(--mo-bg); padding:1rem; border-radius:8px; border:1px solid var(--mo-border); margin-top:1rem;">
              <input type="hidden" name="action" value="responder_duvida">
              <input type="hidden" name="doubt_id" value="<?php echo $d['id']; ?>">
              <input type="hidden" name="replied_by" value="<?php echo $user['name'] . ' (' . ucfirst($role) . ')'; ?>">
              <label style="font-weight:600; font-size:0.875rem; display:block; margin-bottom:0.5rem;">
                Responder a esta dúvida como <?php echo ucfirst($role); ?>:
              </label>
              <textarea name="reply" class="form-control-mo" rows="2" style="height:auto; margin-bottom:0.5rem;" placeholder="Digite a explicação ou orientação pedagógica..." required></textarea>
              <button type="submit" class="btn-mo btn-mo-primary" style="min-height:38px; padding:0.35rem 1rem; font-size:0.875rem;">
                Publicar Resposta Oficial
              </button>
            </form>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="color:var(--mo-text-muted);">Nenhuma dúvida enviada até o momento. Seja o primeiro a postar!</p>
  <?php endif; ?>
</div>

<?php controller::render_footer(); ?>
