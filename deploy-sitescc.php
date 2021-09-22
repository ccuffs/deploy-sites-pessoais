#!/usr/bin/php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Garden\Cli\Cli;
use Colors\Color;

function yellow($str, $eol = false) {
    $c = new Color();
    return $c($str)->yellow . ($eol ? PHP_EOL : '');
}

function magenta($str, $eol = false) {
    $c = new Color();
    return $c($str)->magenta . ($eol ? PHP_EOL : '');
}

function green($str, $eol = false) {
    $c = new Color();
    return $c($str)->green . ($eol ? PHP_EOL : '');
}

function white($str, $eol = false) {
    $c = new Color();
    return $c($str)->white . ($eol ? PHP_EOL : '');
}

function fetch_site($site, $output_dir_path, $control_dir, $force) {
    $fetcher_path = __DIR__ . DIRECTORY_SEPARATOR . 'fetch_site.php';
    $output_path = join(DIRECTORY_SEPARATOR, [$output_dir_path, $site->serve_url]);
    $prefix = join(DIRECTORY_SEPARATOR, [$control_dir, $site->id]);
    
    // Corrige eventual bug onde usuários informavam a URL completa do github na intranet.
    $site_source_url = str_replace('httpsgithubcom', '', $site->source_url);
    
    $command = "php $fetcher_path --type=$site->source_type --input=\"$site_source_url\" --output=\"$output_path\" --prefix=\"$prefix\" ".($force ? '--force' : '')." > /dev/null 2>/dev/null &";

    exec($command);
}

$cli = new Cli();

$cli->description('Faz deploy dos sites pessoais do curso a partir de uma lista de usuários e fontes.')
    ->opt('input-list:l', 'Caminho para um arquivo JSON com a lista de usuários e sites.')
    ->opt('output-dir:o', 'Pasta onde os sites serão colocados.')
    ->opt('control-dir:c', 'Pasta onde os resultados/logs/stc serão colocados.')
    ->opt('batch-size:b', 'Quantos sites devem ser processados por lote.', false, 'integer')
    ->opt('batch-internval:i', 'Tempo, em milisegundos, entre o processamento de um lote e outro.', false, 'integer')
    ->opt('site-interval:s', 'Tempo, em milisegundos, entre o processamento de um site e outro.', false, 'integer')
    ->opt('force:f', 'Força a remoção de todos os arquivos.')
    ->opt('quiet:q', 'Suprime várias mensagens de saída.');

$args = $cli->parse($argv, true);

$ds = DIRECTORY_SEPARATOR;
$input_list_path = $args->getOpt('input-list', __DIR__ . $ds . 'input-list-exemplo.json');
$outpur_dir_path = $args->getOpt('output-dir', sys_get_temp_dir());
$control_dir_path = $args->getOpt('control-dir', __DIR__);
$batch_size = $args->getOpt('batch-size', 50);
$batch_interval = $args->getOpt('batch-internval', 1000);
$site_interval = $args->getOpt('site-internval', 200);
$quiet = $args->getOpt('quiet', false);
$force = $args->getOpt('force', false);

@$input_list = file_get_contents($input_list_path);

if($input_list === false) {
    echo "Erro ao ler lista informada em --input-list: '$input_list_path'.";
    exit(1);
}

if(!file_exists($outpur_dir_path) || !is_writable($outpur_dir_path)) {
    echo "Não é possivel usar diretório de saída informado em --output-dir: '$outpur_dir_path'.";
    exit(2);
}

if(!file_exists($control_dir_path) || !is_writable($control_dir_path)) {
    echo "Não é possivel usar diretório de controle informado em --control-dir: '$control_dir_path'.";
    exit(3);
}

$sites = @json_decode($input_list);

if($sites === null) {
    echo 'Erro processar lista de entrada: ' . json_last_error_msg();
    exit(4);
}

$total_batches = ceil(count($sites) / $batch_size);
$batchs_processed = 1;
$batch_items_processed = 0;
$items_processed = 0;
$time_start = time();

foreach($sites as $site) {
    if(!$quiet && $batch_items_processed == 0) {
        echo white('Processando lote ') . yellow(sprintf('%03d', $batchs_processed)) . '/' . yellow(sprintf('%03d', $total_batches)) . white(':'). PHP_EOL;
    }

    if(!empty($site->source_url)) {
        echo white('  - ') . green($site->source_url) . white(' -> ') . yellow('/' . $site->serve_url) . PHP_EOL;
        fetch_site($site, $outpur_dir_path, $control_dir_path, $force);
        usleep($site_interval);
    } else {
        echo magenta('  SKIP (empty source) ') . white('uid = ' . $site->uid . ', id = ' . $site->id) . PHP_EOL;
    }

    $items_processed++;

    if($batch_items_processed++ >= $batch_size) {
        $batch_items_processed = 0;
        $batchs_processed++;
        usleep($batch_interval);
    }
}

$time_end = time();
$duration = $time_end - $time_start;

echo green("Deploy finalizado! Sites processados: ") . white($items_processed) . green(", tempo: ") . white("${duration}s") . PHP_EOL;
exit(0);