# 🎓 Monitoria Online (Moodle-Base)

> **Plataforma Acadêmica de Gerenciamento, Agendamento e Atendimento de Monitorias no Moodle LMS**  
> **Instituto Federal de Educação, Ciência e Tecnologia de São Paulo (IFSP) — Câmpus São Paulo**  
> **Disciplina:** Desenvolvimento Web & Projeto de Software  
> **Equipe (GEN Corp):** Gabriel Rodrigues da Rocha Forti, Eduardo Gritti Medeiros Teixeira, Nicolas de Azevedo Andrade Santana  
> **Docente / Orientador:** Prof. Caio  

---

## 🌐 Links Oficiais do Projeto & Acesso em Produção

| Recurso | Destino / URL | Descrição |
| :--- | :--- | :--- |
| 🚀 **Deploy em Produção (Render)** | [**https://moodle-base.onrender.com**](https://moodle-base.onrender.com) | Ambiente em nuvem conteinerizado com Docker & PostgreSQL |
| 🌐 **Deploy Web Alternativo (Vercel)** | [**https://monitoria-online.vercel.app**](https://monitoria-online.vercel.app) | Aplicação web para demonstração rápida e responsiva |
| 📦 **Repositório GitHub (Moodle-Base)** | [**https://github.com/GEN-Corp/Moodle-Base**](https://github.com/GEN-Corp/Moodle-Base) | Core Moodle com plugin nativo `local/monitoria` |
| 📊 **Quadro Ágil no Jira (Scrum)** | [**gabrielforti11.atlassian.net (SCRUM)**](https://gabrielforti11.atlassian.net/jira/software/projects/SCRUM/boards/1/backlog) | Sprint 1 com 31 Story Points (100% Concluída) |

[![Deploy to Render](https://render.com/images/deploy-to-render-button.svg)](https://render.com/deploy?repo=https://github.com/GEN-Corp/Moodle-Base)

---

## 🎯 Objetivo Acadêmico & Impacto Social (IFSP)

No Instituto Federal de São Paulo (IFSP), o programa de monitoria é indispensável para combater a retenção e nivelar conhecimentos em matérias exatas e de tecnologia.

Entretanto, **um desafio crítico enfrentado pela comunidade acadêmica é a barreira de locomoção e transporte:**
* Diversos estudantes moram em regiões periféricas ou cidades vizinhas e dependem de transporte público intermunicipal com horários restritos.
* Por conta dessa limitação logística, **os estudantes ficam impossibilitados de comparecer fisicamente ao contra-turno escolar** para participar das monitorias presenciais.
* O sistema **Monitoria Online** integrado ao Moodle elimina essa barreira ao proporcionar:
  1. **Agendamento de sessões síncronas via Google Meet** ou presenciais em laboratórios de informática.
  2. **Central de dúvidas e fórum assíncrono**, garantindo suporte contínuo mesmo que o aluno não esteja no câmpus.
  3. **Painel do monitor** para homologação, controle de presenças e anotações pedagógicas.
  4. **Painel da coordenação** para métricas de atendimento e acompanhamento de turmas.

---

## 👥 Credenciais de Acesso para Testes e Avaliação

O sistema conta com seletor instantâneo de papéis no cabeçalho e usuários pré-configurados:

| Papel Acadêmico | Usuário (`username`) | E-mail Institucional | Senha de Acesso | Permissões no Sistema |
| :--- | :--- | :--- | :--- | :--- |
| 🛡️ **Administrador / Superuser** | `admin` | `admin@ifsp.edu.br` | `Admin@IFSP2026` | Acesso total ao Moodle, banco de dados e logs |
| 🏫 **Coordenador Acadêmico** | `coordenador` | `coordenacao@ifsp.edu.br` | `Coord@IFSP2026` | Gestão de disciplinas, turmas e homologação de monitores |
| 👨‍🏫 **Professor Responsável** | `prof_caio` | `caio.docente@ifsp.edu.br` | `Prof@IFSP2026` | Supervisão pedagógica, relatórios e respostas no fórum |
| 👨‍💻 **Monitor Oficial** | `monitor_gritti` | `eduardo.gritti@aluno.ifsp.edu.br` | `Monitor@IFSP2026` | Publicação de horários, aprovação de sessões e presença |
| 🎓 **Estudante / Aluno** | `aluno_nicolas` | `nicolas.andrade@aluno.ifsp.edu.br` | `Aluno@IFSP2026` | Consulta de vagas, agendamento de sessões e envio de dúvidas |

---

## 🚀 Funcionalidades Implementadas

### 🎓 Visão do Aluno
* **Catálogo de Disciplinas:** Visualização de matérias ofertadas (`Desenvolvimento Web`, `Engenharia de Software`, `Banco de Dados`), carga horária e monitores responsáveis.
* **Agendamento de Horários:** Consulta de vagas disponíveis por dia da semana e agendamento instantâneo com descrição do tema da dúvida.
* **Fórum / Central de Dúvidas:** Publicação de dúvidas assíncronas com categorização por matéria e histórico de respostas do monitor.
* **Link Direto do Google Meet:** Acesso imediato à sala virtual de videoconferência.

### 👨‍💻 Visão do Monitor e Professor
* **Publicação de Horários:** Definição de grade de disponibilidade (dia, horário de início/fim, modalidade híbrida/online e capacidade máxima de alunos).
* **Gestão e Aprovação de Atendimentos:** Painel para aceitar ou recusar solicitações de agendamento.
* **Registro de Presença & Notas:** Homologação de presença discente e anotações pedagógicas pós-atendimento.
* **Moderação do Fórum:** Resposta a perguntas enviadas pelos alunos com destaque de orientação acadêmica.

### 🏫 Visão da Coordenação
* **Gestão de Disciplinas & Monitores:** Cadastro de novos códigos curriculares e vinculação formal de monitores com carga horária semanal.
* **Indicadores em Tempo Real:** Total de matérias ativas, vagas abertas, atendimentos realizados e dúvidas pendentes.

---

## 🛠️ Tecnologias e Arquitetura do Software

* **LMS Base:** Moodle 4.5+ (LMS oficial em PHP 8.2).
* **Padrão Visual:** Tema Boost refinado (Moodle 4.x), com Navbar superior, Drawer lateral retrátil, breadcrumbs e componentes acessíveis com touch targets $\ge 48\text{px}$.
* **Arquitetura do Plugin (`local/monitoria`):**
  - Esquema Relacional XMLDB (`db/install.xml`): 6 tabelas relacionais com chaves primárias e estrangeiras.
  - Controle de Acesso e Permissões (`db/access.php`): Capabilities granulares para Aluno, Monitor, Professor e Coordenador.
  - Navegação Institucional (`lib.php`): Hook `local_monitoria_extend_navigation` inserindo o módulo no menu principal.
  - Camada de Persistência Híbrida (`classes/database_helper.php`): Interação com o `$DB` nativo do Moodle e fallback dinâmico.
  - Camada de Negócio (`classes/monitoria_manager.php`): Regras de agendamento, capacidade de vagas, moderação e métricas.
* **Conteinerização & Nuvem:** `Dockerfile` com Apache, extensões PHP oficiais (`intl`, `gd`, `zip`, `soap`, `opcache`, `pgsql`, `mysqli`) e blueprint `render.yaml` com banco de dados PostgreSQL gerenciado.

---

## 📂 Estrutura de Pastas

```text
Moodle-Base/
├── Dockerfile                      # Imagem conteinerizada PHP 8.2 Apache para o Render
├── render.yaml                     # Blueprint IaC (Web Service + Banco PostgreSQL)
├── docker/
│   ├── apache.conf                 # Configuração do VirtualHost Apache
│   ├── config.php                  # Configuração dinâmica de banco e SSL proxy
│   └── entrypoint.sh               # Inicialização, checagem de banco e migração
├── local/
│   └── monitoria/                  # 🧩 Plugin Oficial da Monitoria Online
│       ├── version.php             # Metadados e versão do plugin
│       ├── lib.php                 # Hooks de navegação e menu do Moodle
│       ├── styles.css              # Estilos no padrão Moodle 4.x Boost
│       ├── index.php               # Painel Geral e Métricas
│       ├── agendamento.php         # Agendamento de Horários e Vagas
│       ├── atendimentos.php        # Gestão de Atendimentos e Presenças
│       ├── duvidas.php             # Fórum e Central de Dúvidas
│       ├── admin_gestao.php        # Gestão de Disciplinas e Monitores
│       ├── api.php                 # Endpoints REST para consumo externo
│       ├── classes/
│       │   ├── controller.php      # Renderização de Navbar, Drawer e Breadcrumbs
│       │   ├── database_helper.php # Camada de persistência segura
│       │   └── monitoria_manager.php # Regras de negócio da monitoria
│       ├── db/
│       │   ├── access.php          # Capabilities e permissões Moodle
│       │   └── install.xml         # Definição das tabelas relacionais XMLDB
│       └── lang/
│           ├── pt_br/              # Internacionalização em Português do Brasil
│           └── en/                 # Internacionalização em Inglês
└── README.md                       # Documentação técnica e acadêmica do projeto
```

---

## 💻 Como Rodar o Projeto Localmente

### Opção 1: Via Docker (Recomendado)
```bash
# 1. Clonar o repositório
git clone https://github.com/GEN-Corp/Moodle-Base.git
cd Moodle-Base

# 2. Construir a imagem Docker
docker build -t moodle-monitoria .

# 3. Executar o container na porta 8080
docker run -d -p 8080:80 --name moodle-local moodle-monitoria

# 4. Acessar no navegador
http://localhost:8080/local/monitoria/
```

### Opção 2: Via XAMPP / Laragon (PHP 8.1 ou 8.2 + Apache)
```bash
# 1. Clonar na pasta raiz do servidor web (ex: htdocs ou www)
git clone https://github.com/GEN-Corp/Moodle-Base.git C:/laragon/www/moodle

# 2. Criar uma pasta moodledata externa com permissão de escrita
mkdir C:/laragon/moodledata

# 3. Iniciar Apache e MySQL no Laragon/XAMPP
# 4. Acessar a aplicação no navegador
http://localhost/moodle/local/monitoria/
```

---

## 📊 Metodologia Ágil no Jira (Scrum)

O desenvolvimento segue boas práticas ágeis documentadas no [Jira Oficial](https://gabrielforti11.atlassian.net/jira/software/projects/SCRUM/boards/1/backlog):

### Épicos Estruturados
* **`SCRUM-56` (Épico 1):** *Autenticação, Perfis e Acesso Institucional*
* **`SCRUM-57` (Épico 2):** *Gestão de Disciplinas e Monitores*
* **`SCRUM-58` (Épico 3):** *Agendamento e Salas de Monitoria*
* **`SCRUM-59` (Épico 4):** *Atendimento de Dúvidas e Fórum Acadêmico*
* **`SCRUM-60` (Épico 5):** *Painéis de Controle e Dashboards*

### Rastreabilidade das Histórias de Usuário da Sprint 1 (31 Story Points — 100% Concluído)
* **`SCRUM-61` (US01) [8 SP]:** *Autenticação e Perfis de Acesso* — Subtarefas: `SCRUM-62` (Front), `SCRUM-63` (Back), `SCRUM-64` (DB).
* **`SCRUM-65` (US02) [5 SP]:** *Consulta de Disciplinas e Monitores* — Subtarefas: `SCRUM-66` (Front), `SCRUM-67` (Back), `SCRUM-68` (DB).
* **`SCRUM-69` (US03) [8 SP]:** *Agendamento de Horários pelo Aluno* — Subtarefas: `SCRUM-70` (Front), `SCRUM-71` (Back), `SCRUM-72` (DB).
* **`SCRUM-73` (US04) [5 SP]:** *Painel do Monitor para Aprovação de Atendimentos* — Subtarefas: `SCRUM-74` (Front), `SCRUM-75` (Back), `SCRUM-76` (DB).
* **`SCRUM-77` (US05) [5 SP]:** *Central de Dúvidas e Fórum Integrado* — Subtarefas: `SCRUM-78` (Front), `SCRUM-79` (Back), `SCRUM-80` (DB).

---

Desenvolvido com excelência por **NextCodeGen </>** para a equipe **GEN Corp / IFSP**.
