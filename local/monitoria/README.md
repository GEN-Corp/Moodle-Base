# 🎓 Plugin Moodle & Sistema: Monitoria Online (`local_monitoria`)

> **Módulo Oficial de Gerenciamento, Agendamento e Atendimento de Monitorias Acadêmicas**  
> **Instituto Federal de Educação, Ciência e Tecnologia de São Paulo (IFSP)**  
> **Cursos:** Engenharia de Software / Técnico em Informática  
> **Disciplinas:** Desenvolvimento Web & Projeto de Software  
> **Equipe (GEN Corp):** Gabriel Rodrigues da Rocha Forti, Eduardo Gritti Medeiros Teixeira, Nicolas de Azevedo Andrade Santana  
> **Orientador:** Prof. Caio  

---

## 🌐 Deploy em Produção (Vercel)

A plataforma de testes e aplicação pública encontra-se implantada e 100% operacional no ar:

* 🚀 **Ambiente de Produção (Vercel):** [**https://monitoria-online.vercel.app**](https://monitoria-online.vercel.app)
* ⚙️ **Painel Administrativo:** [**https://monitoria-online.vercel.app/admin/**](https://monitoria-online.vercel.app/admin/)
* 📦 **Repositório da Aplicação Web:** [GEN-Corp/Monitoria-Online](https://github.com/GEN-Corp/Monitoria-Online)
* 🧩 **Repositório Base / Plugin Moodle:** [GEN-Corp/Moodle-Base](https://github.com/GEN-Corp/Moodle-Base)

---

## 🎯 Objetivo & Impacto Social: Superando Barreiras de Locomoção no IFSP

No Instituto Federal de São Paulo (IFSP), as atividades de monitoria acadêmica são essenciais para o aprendizado e retenção dos estudantes.

Entretanto, **um desafio crucial mapeado nas entrevistas com alunos e professores é a barreira de transporte e locomoção:**
* Muitos estudantes residem em bairros distantes ou em outras cidades da região, dependendo de ônibus circulares ou intermunicipais com grades de horários rígidas.
* Por essa razão, **os estudantes não conseguem permanecer no campus no contra-turno escolar** para participar das monitorias presenciais tradicionais.
* Anteriormente, tentativas informais de atendimento via WhatsApp ou e-mails resultavam em perda de histórico, desorganização e impossibilidade de mensuração por parte dos docentes.

### Solução Entregue
A plataforma **Monitoria Online** centraliza e viabiliza:
1. **Agendamento com Salas Virtuais:** Sessões integradas a links do Google Meet para que alunos tirem dúvidas ao vivo diretamente de suas casas.
2. **Central Assíncrona de Dúvidas / Fórum:** Alunos que não conseguem participar de chamadas síncronas podem postar suas dúvidas com descrição e código para que os monitores analisem e respondam.
3. **Painéis por Perfil:** Visões dedicadas para Alunos, Monitores, Professores e Coordenação acompanharem atendimentos, frequência e matérias com maior índice de dúvidas.

---

## 🔑 Credenciais de Acesso para Testes e Avaliação

Utilize as seguintes contas para homologação imediata da plataforma em produção:

| Perfil de Acesso | Usuário (`username`) | E-mail Institucional | Senha de Acesso | URL de Acesso | Capacidades Principais |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 🛡️ **Superusuário / Administrador** | `admin` | `admin@ifsp.edu.br` | `Admin@IFSP2026` | [`/admin/`](https://monitoria-online.vercel.app/admin/) ou [`/login/`](https://monitoria-online.vercel.app/login/) | Gestão de banco, modelos, logs e permissões globais. |
| 🏫 **Coordenador Acadêmico** | `coordenador` | `coordenacao@ifsp.edu.br` | `Coord@IFSP2026` | [`/login/`](https://monitoria-online.vercel.app/login/) | Visão analítica, aprovação de matérias e status de turmas. |
| 👨‍🏫 **Professor Responsável** | `prof_caio` | `caio.docente@ifsp.edu.br` | `Prof@IFSP2026` | [`/login/`](https://monitoria-online.vercel.app/login/) | Acompanhamento pedagógico das monitorias de sua docência. |
| 👨‍💻 **Monitor Oficial** | `monitor_gritti` | `eduardo.gritti@aluno.ifsp.edu.br` | `Monitor@IFSP2026` | [`/login/`](https://monitoria-online.vercel.app/login/) | Fila de atendimento, publicação de horários e respostas a dúvidas. |
| 🎓 **Estudante / Aluno** | `aluno_nicolas` | `nicolas.andrade@aluno.ifsp.edu.br` | `Aluno@IFSP2026` | [`/login/`](https://monitoria-online.vercel.app/login/) | Agendamento de vagas, envio de dúvidas e histórico pessoal. |

---

## 🏛️ Arquitetura do Plugin Moodle (`local/monitoria`)

```text
local/monitoria/
├── version.php                  # Metadados do plugin e compatibilidade Moodle 4.5+
├── lib.php                      # Extensão da navegação primária (local_monitoria_extend_navigation)
├── styles.css                   # Design System "Quiet Luxury", mobile-first e touch targets >= 48px
├── index.php                    # Hub dinâmico e dashboards contextuais
├── agendamento.php              # Grade de horários e reserva de atendimentos
├── duvidas.php                  # Central de dúvidas e fórum de suporte
├── admin_gestao.php             # Painel administrativo de disciplinas e monitores
├── api.php                      # Endpoint REST JSON para consumo assíncrono
├── db/
│   ├── install.xml              # Schema XMLDB completo com 6 tabelas relacionais
│   ├── access.php               # Definição formal de 5 capabilities Moodle por papel
│   └── services.php             # Registro de Web Services
├── lang/
│   ├── pt_br/local_monitoria.php# Internacionalização em Português do Brasil
│   └── en/local_monitoria.php   # Internacionalização em Inglês
└── classes/
    ├── controller.php           # Camada de apresentação e autenticação
    ├── monitoria_manager.php    # Camada de serviços e regras de negócio
    └── database_helper.php      # Persistência segura com suporte nativo a Moodle $DB
```

---

## 📊 Engenharia de Software Ágil & Rastreabilidade Jira (Sprint 1)

O projeto seguiu a metodologia ágil **Scrum** no Jira Oficial ([gabrielforti11.atlassian.net](https://gabrielforti11.atlassian.net/jira/software/projects/SCRUM/boards/1/backlog)).

### Épicos Estruturados
* **`SCRUM-56` (Épico 1):** *Autenticação, Perfis e Acessos Acadêmicos*
* **`SCRUM-57` (Épico 2):** *Gestão de Disciplinas e Monitores*
* **`SCRUM-58` (Épico 3):** *Agendamento e Salas de Monitoria*
* **`SCRUM-59` (Épico 4):** *Atendimento de Dúvidas e Fórum Acadêmico*
* **`SCRUM-60` (Épico 5):** *Painéis de Controle e Dashboards*

### Rastreabilidade das Histórias de Usuário na Sprint 1

| Chave | Tipo | User Story | SP | Status | Critérios de Aceite |
| :---: | :---: | :--- | :---: | :---: | :--- |
| **`SCRUM-61`** | Story | **US01: Autenticação e Perfis** | 5 | ✅ Concluído | Identificação de papel e exibição de recursos exclusivos no Moodle. |
| **`SCRUM-65`** | Story | **US02: Gestão de Disciplinas & Monitores** | 8 | ✅ Concluído | Cadastro de matérias e alocação de monitores com carga horária. |
| **`SCRUM-69`** | Story | **US03: Agendamento de Sessões** | 8 | ✅ Concluído | Reserva de vagas com controle de capacidade e link Meet. |
| **`SCRUM-73`** | Story | **US04: Dashboards Dedicados** | 5 | ✅ Concluído | Painéis consolidados para acompanhamento de rotina acadêmica. |
| **`SCRUM-77`** | Story | **US05: Central de Dúvidas & Fórum** | 5 | ✅ Concluído | Envio de dúvidas prévias com suporte a respostas de monitores. |

**Total de Story Points Entregues:** **31 SP (100% Concluído)**.

---

Desenvolvido com excelência por **NextCodeGen </>** para o **IFSP**.
