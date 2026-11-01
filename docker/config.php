<?php
unset($CFG);
global $CFG;
$CFG = new stdClass();

// 1. Configuração do Banco de Dados (Suporte a DATABASE_URL do Render / PostgreSQL / MySQL)
$database_url = getenv('DATABASE_URL');
if (!empty($database_url)) {
    $db = parse_url($database_url);
    $is_pgsql = isset($db['scheme']) && (strpos($db['scheme'], 'postgres') !== false || strpos($db['scheme'], 'pgsql') !== false);
    $CFG->dbtype    = $is_pgsql ? 'pgsql' : 'mysqli';
    $CFG->dblibrary = 'native';
    $CFG->dbhost    = $db['host'];
    $CFG->dbname    = ltrim($db['path'], '/');
    $CFG->dbuser    = $db['user'];
    $CFG->dbpass    = $db['pass'] ?? '';
    $CFG->dbport    = $db['port'] ?? ($is_pgsql ? 5432 : 3306);
} else {
    $CFG->dbtype    = getenv('MOODLE_DB_TYPE') ?: 'pgsql';
    $CFG->dblibrary = 'native';
    $CFG->dbhost    = getenv('MOODLE_DB_HOST') ?: 'localhost';
    $CFG->dbname    = getenv('MOODLE_DB_NAME') ?: 'moodle';
    $CFG->dbuser    = getenv('MOODLE_DB_USER') ?: 'moodle';
    $CFG->dbpass    = getenv('MOODLE_DB_PASSWORD') ?: '';
    $CFG->dbport    = getenv('MOODLE_DB_PORT') ?: '';
}

$CFG->prefix    = 'mdl_';
$CFG->dboptions = array(
    'dbpersist' => 0,
    'dbport'    => $CFG->dbport,
    'dbsocket'  => '',
);

// 2. Detecção Dinâmica da URL do Moodle (WWWROOT)
$moodle_url = getenv('MOODLE_URL');
if (!empty($moodle_url)) {
    $CFG->wwwroot = rtrim($moodle_url, '/');
} else {
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $proto = $is_https ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
    $CFG->wwwroot = $proto . $host;
}

// 3. Diretório de Arquivos & Permissões
$CFG->dataroot  = getenv('MOODLE_DATA_ROOT') ?: '/var/www/moodledata';
$CFG->admin     = 'admin';
$CFG->directorypermissions = 0777;
$CFG->sslproxy  = true;

// 4. Inicialização do Core do Moodle
require_once(__DIR__ . '/lib/setup.php');
