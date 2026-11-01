<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Course and Tutor Management Panel (US02 - SCRUM-65)
 *
 * @package    local_monitoria
 * @copyright  2026 IFSP - Grupo Monitoria Online (GEN Corp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (file_exists(__DIR__ . '/../../config.php')) {
    require_once(__DIR__ . '/../../config.php');
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

    if ($action === 'cadastrar_disciplina') {
        $code = $_POST['code'] ?? '';
        $name = $_POST['name'] ?? '';
        $dept = $_POST['department'] ?? '';
        $workload = (int)($_POST['workload'] ?? 0);

        if (!empty($code) && !empty($name)) {
            monitoria_manager::create_course($code, $name, $dept, $workload);
            $feedback_msg = ['type' => 'success', 'text' => "Disciplina {$code} - {$name} cadastrada com sucesso!"];
        }
    }

    if ($action === 'vincular_monitor') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $course_id = (int)($_POST['course_id'] ?? 0);
        $hours = (int)($_POST['weekly_hours'] ?? 12);
        $meet = $_POST['meet_link'] ?? '';
        $location = $_POST['location'] ?? '';

        $courses = monitoria_manager::get_all_courses();
        $course = array_values(array_filter($courses, fn($c) => (int)$c['id'] === $course_id))[0] ?? null;

        if ($course && !empty($name) && !empty($email)) {
            monitoria_manager::assign_monitor($name, $email, $course_id, $course['name'], $hours, $meet, $location);
            $feedback_msg = ['type' => 'success', 'text' => "Monitor {$name} vinculado com sucesso à disciplina {$course['name']}!"];
        }
    }
}

$courses = monitoria_manager::get_all_courses();
$monitors = monitoria_manager::get_all_monitors();

controller::render_header('gestao');
?>

<?php if ($feedback_msg): ?>
  <div style="padding:1rem; margin-bottom:1.5rem; border-radius:8px; background:<?php echo $feedback_msg['type'] === 'success' ? '#DCFCE7' : '#FEE2E2'; ?>; color:<?php echo $feedback_msg['type'] === 'success' ? '#16A34A' : '#DC2626'; ?>; font-weight:600;">
    ✅ <?php echo htmlspecialchars($feedback_msg['text']); ?>
  </div>
<?php endif; ?>

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:1.5rem; margin-bottom:2rem;">
  <!-- FORM: CADASTRAR DISCIPLINA -->
  <div class="card-box">
    <h2 style="font-size:1.25rem; font-weight:700; margin-top:0; margin-bottom:1rem; border-bottom:1px solid var(--mo-border); padding-bottom:0.5rem;">
      ➕ Cadastrar Disciplina
    </h2>
    <form method="POST" action="">
      <input type="hidden" name="action" value="cadastrar_disciplina">
      <div class="form-group">
        <label>Código da Matéria (ex: DW101)</label>
        <input type="text" name="code" class="form-control-mo" placeholder="Ex: ES302" required>
      </div>
      <div class="form-group">
        <label>Nome Completo da Disciplina</label>
        <input type="text" name="name" class="form-control-mo" placeholder="Ex: Algoritmos e Estruturas de Dados" required>
      </div>
      <div class="form-group">
        <label>Departamento / Coordenadoria</label>
        <input type="text" name="department" class="form-control-mo" value="Coordenadoria de Informática">
      </div>
      <div class="form-group">
        <label>Carga Horária Semestral (Horas)</label>
        <input type="number" name="workload" class="form-control-mo" value="80">
      </div>
      <button type="submit" class="btn-mo btn-mo-primary" style="width:100%;">Salvar Disciplina</button>
    </form>
  </div>

  <!-- FORM: VINCULAR MONITOR -->
  <div class="card-box">
    <h2 style="font-size:1.25rem; font-weight:700; margin-top:0; margin-bottom:1rem; border-bottom:1px solid var(--mo-border); padding-bottom:0.5rem;">
      👨‍💻 Designar Monitor
    </h2>
    <form method="POST" action="">
      <input type="hidden" name="action" value="vincular_monitor">
      <div class="form-group">
        <label>Nome do Aluno Monitor</label>
        <input type="text" name="name" class="form-control-mo" placeholder="Ex: Gabriel Forti" required>
      </div>
      <div class="form-group">
        <label>E-mail Institucional</label>
        <input type="email" name="email" class="form-control-mo" placeholder="nome@aluno.ifsp.edu.br" required>
      </div>
      <div class="form-group">
        <label>Disciplina Designada</label>
        <select name="course_id" class="form-control-mo" required>
          <?php foreach ($courses as $c): ?>
            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['code'] . ' - ' . $c['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Link Google Meet Padrão</label>
        <input type="url" name="meet_link" class="form-control-mo" placeholder="https://meet.google.com/..." value="https://meet.google.com/ifsp-monitoria">
      </div>
      <div class="form-group">
        <label>Local Físico / Laboratório</label>
        <input type="text" name="location" class="form-control-mo" placeholder="Laboratório 4 / Online" value="Laboratório 2 - IFSP">
      </div>
      <button type="submit" class="btn-mo btn-mo-primary" style="width:100%;">Vincular Monitor Oficial</button>
    </form>
  </div>
</div>

<!-- LISTAGEM DE MONITORES ATIVOS -->
<div class="card-box">
  <div class="card-header-flex">
    <h2>Quadro de Monitores Oficiais e Matérias</h2>
    <span class="badge badge-success"><?php echo count($monitors); ?> Monitores Ativos</span>
  </div>
  <div class="table-responsive">
    <table class="table-mo">
      <thead>
        <tr>
          <th>Monitor</th>
          <th>E-mail</th>
          <th>Disciplina</th>
          <th>Carga Semanal</th>
          <th>Local / Sala Virtual</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($monitors as $m): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($m['name']); ?></strong></td>
            <td><?php echo htmlspecialchars($m['email']); ?></td>
            <td><span class="badge badge-neutral"><?php echo htmlspecialchars($m['course_name']); ?></span></td>
            <td><?php echo $m['weekly_hours']; ?>h / semana</td>
            <td>
              <?php echo htmlspecialchars($m['location'] ?? 'Lab IFSP'); ?><br>
              <small><a href="<?php echo htmlspecialchars($m['meet_link']); ?>" target="_blank" rel="noopener noreferrer" style="color:var(--mo-primary);">Sala Virtual</a></small>
            </td>
            <td><span class="badge badge-success"><?php echo ucfirst($m['status']); ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php controller::render_footer(); ?>
