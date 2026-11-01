<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Portuguese language strings for local_monitoria plugin.
 *
 * @package    local_monitoria
 * @copyright  2026 IFSP - Grupo Monitoria Online (GEN Corp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Monitoria Online';
$string['modulename'] = 'Sistema de Monitoria Acadêmica';
$string['monitoria:view'] = 'Visualizar plataforma de monitoria';
$string['monitoria:booksession'] = 'Agendar sessões de monitoria';
$string['monitoria:beamonitor'] = 'Atuar como monitor e gerenciar horários';
$string['monitoria:managemonitors'] = 'Gerenciar matérias e alocação de monitores';
$string['monitoria:manageall'] = 'Administração global do sistema de monitoria';

// Navigation & Tabs
$string['nav_dashboard'] = 'Painel Geral';
$string['nav_agendamentos'] = 'Agendamentos';
$string['nav_duvidas'] = 'Central de Dúvidas';
$string['nav_gestao'] = 'Gestão de Disciplinas & Monitores';
$string['nav_meus_horarios'] = 'Meus Atendimentos';

// Roles
$string['role_aluno'] = 'Aluno';
$string['role_monitor'] = 'Monitor';
$string['role_professor'] = 'Professor';
$string['role_coordenador'] = 'Coordenador / Admin';

// Actions
$string['action_agendar'] = 'Agendar Monitoria';
$string['action_nova_duvida'] = 'Enviar Nova Dúvida';
$string['action_cadastrar_disciplina'] = 'Cadastrar Disciplina';
$string['action_vincular_monitor'] = 'Vincular Monitor';
$string['action_salvar'] = 'Salvar Alterações';
$string['action_cancelar'] = 'Cancelar';
$string['action_confirmar'] = 'Confirmar Agendamento';
$string['action_concluir'] = 'Marcar como Concluído';

// Status
$string['status_agendado'] = 'Agendado';
$string['status_em_andamento'] = 'Em Atendimento';
$string['status_concluido'] = 'Concluído';
$string['status_cancelado'] = 'Cancelado';
$string['status_ativo'] = 'Ativo';
$string['status_inativo'] = 'Inativo';

// Messages
$string['msg_sucesso_agendamento'] = 'Sessão de monitoria agendada com sucesso!';
$string['msg_sucesso_duvida'] = 'Dúvida registrada e encaminhada ao monitor da disciplina!';
$string['msg_sucesso_disciplina'] = 'Disciplina cadastrada com sucesso!';
$string['msg_sucesso_vinculo'] = 'Monitor vinculado com sucesso à disciplina!';
$string['msg_sem_vagas'] = 'Não há mais vagas disponíveis para este horário.';
$string['msg_sem_permissao'] = 'Você não possui permissão para acessar esta área.';
