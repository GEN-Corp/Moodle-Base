# 🎓 Projeto de Extensão: Monitoria Online — IFSP

> **Módulo Oficial de Gerenciamento, Agendamento e Atendimento de Monitorias Acadêmicas**  
> **Instituto Federal de Educação, Ciência e Tecnologia de São Paulo (IFSP)**  
> **Base de Desenvolvimento:** Moodle 4.5+ (Plugin nativo em `local/monitoria`)  
> **Equipe (GEN Corp):** Gabriel Rodrigues da Rocha Forti, Eduardo Gritti Medeiros Teixeira, Nicolas de Azevedo Andrade Santana  
> **Orientador:** Prof. Caio  

---

## 🌐 Deploy em Produção (Vercel)

* 🚀 **Ambiente no Ar:** [**https://monitoria-online.vercel.app**](https://monitoria-online.vercel.app)
* ⚙️ **Painel Administrativo:** [**https://monitoria-online.vercel.app/admin/**](https://monitoria-online.vercel.app/admin/)
* 📦 **Repositório da Aplicação Web:** [GEN-Corp/Monitoria-Online](https://github.com/GEN-Corp/Monitoria-Online)
* 🧩 **Plugin Moodle Implementado:** [`local/monitoria/`](local/monitoria/)

---

## 🎯 Objetivo & Impacto Social: Superando Barreiras de Locomoção no IFSP

No Instituto Federal de São Paulo (IFSP), as atividades de monitoria são fundamentais para o aprendizado e retenção discente.

Entretanto, **um desafio crucial mapeado nas pesquisas é a barreira de transporte e locomoção:**
* Muitos estudantes residem em bairros distantes ou municípios vizinhos e dependem de transporte público intermunicipal com horários rígidos.
* Por essa razão, **os estudantes não conseguem comparecer ao contra-turno escolar** para participar das monitorias presenciais tradicionais.
* O sistema **Monitoria Online** soluciona essa dor ao centralizar:
  1. **Agendamento de sessões remotas via Google Meet** ou presenciais em laboratórios.
  2. **Central de dúvidas e tickets assíncronos**, permitindo que o aluno tire dúvidas mesmo sem estar presente fisicamente no câmpus.
  3. **Painéis de controle especializados** para Alunos, Monitores, Professores e Coordenação.

---

## 🔑 Credenciais de Acesso para Testes e Avaliação

| Perfil de Acesso | Usuário (`username`) | E-mail Institucional | Senha de Acesso | URL de Acesso | Papel e Permissões |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 🛡️ **Superusuário / Administrador** | `admin` | `admin@ifsp.edu.br` | `Admin@IFSP2026` | [`/admin/`](https://monitoria-online.vercel.app/admin/) ou [`/login/`](https://monitoria-online.vercel.app/login/) | Gestão de dados, modelos e logs globais. |
| 🏫 **Coordenador Acadêmico** | `coordenador` | `coordenacao@ifsp.edu.br` | `Coord@IFSP2026` | [`/login/`](https://monitoria-online.vercel.app/login/) | Indicadores gerais e homologação de turmas. |
| 👨‍🏫 **Professor Responsável** | `prof_caio` | `caio.docente@ifsp.edu.br` | `Prof@IFSP2026` | [`/login/`](https://monitoria-online.vercel.app/login/) | Acompanhamento das turmas de sua docência. |
| 👨‍💻 **Monitor Oficial** | `monitor_gritti` | `eduardo.gritti@aluno.ifsp.edu.br` | `Monitor@IFSP2026` | [`/login/`](https://monitoria-online.vercel.app/login/) | Atendimento de alunos, fila de dúvidas e grade. |
| 🎓 **Estudante / Aluno** | `aluno_nicolas` | `nicolas.andrade@aluno.ifsp.edu.br` | `Aluno@IFSP2026` | [`/login/`](https://monitoria-online.vercel.app/login/) | Envio de dúvidas, agendamentos e histórico. |

---

## 📊 Engenharia de Software Ágil & Rastreabilidade Jira (Sprint 1)

O projeto é gerenciado via metodologia ágil **Scrum** no Jira Oficial ([gabrielforti11.atlassian.net](https://gabrielforti11.atlassian.net/jira/software/projects/SCRUM/boards/1/backlog)).

### Épicos Estruturados
* **`SCRUM-56` (Épico 1):** *Autenticação, Perfis e Acessos Acadêmicos*
* **`SCRUM-57` (Épico 2):** *Gestão de Disciplinas e Monitores*
* **`SCRUM-58` (Épico 3):** *Agendamento e Salas de Monitoria*
* **`SCRUM-59` (Épico 4):** *Atendimento de Dúvidas e Fórum Acadêmico*
* **`SCRUM-60` (Épico 5):** *Painéis de Controle e Dashboards*

### Rastreabilidade das Histórias de Usuário (31 Story Points — 100% Concluído)
* `SCRUM-61` (US01): Autenticação e Perfis (5 SP)
* `SCRUM-65` (US02): Gestão de Disciplinas e Monitores (8 SP)
* `SCRUM-69` (US03): Agendamento e Salas de Monitoria (8 SP)
* `SCRUM-73` (US04): Painéis Dedicados para Aluno e Monitor (5 SP)
* `SCRUM-77` (US05): Central de Dúvidas e Fórum Rápido de Suporte (5 SP)

---


---

## ☁️ Deploy do Moodle-Base no Render (Infraestrutura como Código)

O repositório contém a infraestrutura completa de conteinerização Docker e Blueprint pronta para nuvem no **Render**:

* 📄 **`Dockerfile`**: Imagem PHP 8.2 Apache com todas as extensões do Moodle (`intl`, `gd`, `zip`, `soap`, `opcache`, `pgsql`, `mysqli`, `exif`, `bcmath`).
* 📄 **`render.yaml`**: Declaração IaC do Web Service e do banco de dados relacional PostgreSQL (`moodle-db`).
* 📄 **`docker/entrypoint.sh`**: Script de inicialização automática que configura portas dinâmicas, valida conexão com o banco e executa instalação/migração do plugin `local/monitoria`.

[![Deploy to Render](https://render.com/images/deploy-to-render-button.svg)](https://render.com/deploy?repo=https://github.com/GEN-Corp/Moodle-Base)

### Como ativar o deploy no Render em 1 clique:
1. Acesse o painel do [Render Dashboard](https://dashboard.render.com).
2. Clique em **New +** > **Blueprint**.
3. Selecione o repositório GEN-Corp/Moodle-Base (branch main ou sprint-1-monitoria-online).
4. O Render detectará automaticamente o arquivo `render.yaml`, provisionará o banco PostgreSQL e subirá o container do Moodle com o plugin `local/monitoria` integrado.

---

Desenvolvido com excelência por **NextCodeGen </>** para a equipe **GEN Corp / IFSP**.
