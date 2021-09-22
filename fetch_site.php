#!/usr/bin/php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$cli = new Garden\Cli\Cli();

$cli->description('Obtem o conteudo de um site.')
    ->opt('type:t', 'Tipo de repositório de conteúdo.')
    ->opt('input:i', 'Caminho para o repositório de conteúdo.', true)
    ->opt('output:o', 'Caminho para o diretorio de saída (onde o site será colocado).', true)
    ->opt('prefix:p', 'Prefixo para os arquivos de resultado/log/etc.');

$args = $cli->parse($argv, true);

$ds = DIRECTORY_SEPARATOR;
$type = $args->getOpt('type', 'git');
$input_path = $args->getOpt('input');
$output_path = $args->getOpt('output');
$prefix = $args->getOpt('prefix', '__ccuffs_site');

$output = [];
$result_code = 0;
$result_path = "${prefix}.json";

if(file_exists($output_path)) {
    chdir($output_path);

    // Garante que nenhum processo git anterior ficou pendurado
    $git_lock_file = $output_path . DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR . 'index.lock';
    @unlink($git_lock_file);
    
    // Seta credenciais para evitar que o git chore por causa delas
    exec('git config user.name "Deploy Bot"');
    exec('git config user.email "bot@uffs.cc"');
    
    $command = "GIT_TERMINAL_PROMPT=0 git clean --force && git reset --hard HEAD && git pull --progress --allow-unrelated-histories 2>&1";
} else {
    $command = "GIT_TERMINAL_PROMPT=0 git clone \"$input_path\" \"$output_path\" --progress 2>&1";
}

exec($command, $output, $result_code);

$result = [
    'ret_code' => $result_code,
    'time' => time(),
    'output' => $output
];

file_put_contents($result_path, json_encode($result, JSON_NUMERIC_CHECK));