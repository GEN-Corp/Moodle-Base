# 🎓 Plugin Moodle: Monitoria Online (`local_monitoria`)

> **Módulo Oficial de Gerenciamento e Atendimento de Monitorias Acadêmicas**  
> **Instituto Federal de São Paulo (IFSP)**  
> **Disciplina:** Desenvolvimento Web / Projeto de Software  
> **Equipe (GEN Corp):** Gabriel Rodrigues da Rocha Forti, Eduardo Gritti Medeiros Teixeira, Nicolas de Azevedo Andrade Santana  
> **Orientador:** Prof. Caio  

---

## 📌 Visão Geral do Projeto

O **Monitoria Online** é um plugin nativo de nível de sistema (`local_` plugin) desenvolvido para o ambiente **Moodle 4.5+**, integrando os perfis acadêmicos da instituição (**Alunos**, **Monitores**, **Professores** e **Coordenadores**) em um ecossistema unificado para:

1. **Gestão de Disciplinas & Designação de Monitores:** Cadastro de matérias e vínculo formal de alunos com bolsas ou voluntariado de monitoria.
2. **Grade de Horários & Agendamento de Vagas:** Disponibilização de sessões presenciais em laboratórios ou virtuais via Google Meet com controle rigoroso de concorrência e capacidade máxima.
3. **Painéis de Controle Customizados (Dashboards):** Visão centralizada das próximas sessões, histórico de atendimentos e frequência.
4. **Central de Dúvidas / Fórum Rápido:** Canal prévio para alunos enviarem dúvidas de código e exercícios para os monitores responderem antes ou durante o atendimento.

---

## 🏛️ Arquitetura Técnica

```text
local/monitoria/
├── version.php                  # Metadados e compatibilidade Moodle (v1.0.0-sprint1)
├── lib.php                      # Hooks de navegação global (local_monitoria_extend_navigation)
├── styles.css                   # Design System "Quiet Luxury", mobile-first e touch targets >= 48px
├── index.php                    # Hub dinâmico e dashboards por perfil
├── agendamento.php              # Grade de horários e reserva de atendimentos
├── duvidas.php                  # Fórum de dúvidas e respostas rápidas
├── admin_gestao.php             # Painel administrativo de disciplinas e monitores
├── api.php                      # Endpoint REST JSON para consumo assíncrono
├── db/
│   ├── install.xml              # Schema XMLDB completo com chaves primárias e estrangeiras
│   ├── access.php               # Definição formal de capabilities e papéis Moodle
│   └── services.php             # Registro de Web Services
├── lang/
│   ├── pt_br/local_monitoria.php# Internacionalização em Português do Brasil
│   └── en/local_monitoria.php   # Internacionalização em Inglês
└── classes/
    ├── controller.php           # Camada de apresentação e autenticação
    ├── monitoria_manager.php    # Camada de serviços e regras de negócio
    └── database_helper.php      # Abstração de persistência segura com fallback inteligente
```

---

## 📊 Mapeamento e Rastreabilidade com o Jira (Sprint 1)

| Chave | Tipo | Título da História / Tarefa | Story Points | Status |
| :--- | :--- | :--- | :---: | :---: |
| **SCRUM-56** | Épico | Épico 1: Autenticação, Perfis e Acessos Acadêmicos | — | Em andamento |
| **SCRUM-61** | Story | US01 - Autenticação e Controle de Acesso por Perfil Acadêmico | 5 SP | Concluído |
| **SCRUM-57** | Épico | Épico 2: Gestão de Disciplinas e Monitores | — | Em andamento |
| **SCRUM-65** | Story | US02 - Cadastro e Vínculo de Monitores às Disciplinas | 8 SP | Concluído |
| **SCRUM-58** | Épico | Épico 3: Agendamento e Salas de Monitoria | — | Em andamento |
| **SCRUM-69** | Story | US03 - Agendamento e Consulta de Sessões de Monitoria | 8 SP | Concluído |
| **SCRUM-60** | Épico | Épico 5: Painéis de Controle e Dashboards | — | Em andamento |
| **SCRUM-73** | Story | US04 - Painéis Dedicados para Aluno e Monitor (Dashboards) | 5 SP | Concluído |
| **SCRUM-59** | Épico | Épico 4: Atendimento de Dúvidas e Fórum Acadêmico | — | Em andamento |
| **SCRUM-77** | Story | US05 - Central de Dúvidas e Fórum Rápido de Suporte | 5 SP | Concluído |

**Total de Story Points Entregues na Sprint 1:** **31 Story Points**

---

## 🚀 Como Instalar e Testar no Moodle

1. Copie ou mantenha esta pasta dentro do diretório `/local/monitoria/` da sua instalação do Moodle.
2. Acesse a área de administração do Moodle (`/admin/index.php`) para executar a atualização automática de banco via XMLDB.
3. O link para o **Monitoria Online** aparecerá na barra de navegação global superior do Moodle.
4. Para testar o protótipo de forma independente, execute qualquer servidor local (ex: PHP Built-in server ou Apache) e acesse:
   ```bash
   php -S localhost:8000
   ```
   E navegue para: `http://localhost:8000/local/monitoria/index.php`

---

Desenvolvido com excelência por **NextCodeGen </>** para o **IFSP**.
