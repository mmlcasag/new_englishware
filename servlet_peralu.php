<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'On');

require_once 'utils.php';
require_once 'utils_db.php';

startDatabase();

// Variáveis
$erro = false;
$mensagem = "";

// Parâmetros
if ($_POST) {
	$per_codigo = addslashes(trim($_POST["p_per_codigo"]));
	$alu_codigo = addslashes(trim($_POST["p_alu_codigo"]));
}

/*
// Debug
echo "<tt>";
echo "per_codigo......: " . $per_codigo        . "</br>";
echo "alu_codigo......: " . $alu_codigo        . "</br>";
echo "</tt>";
//Fim Debug
*/

if (!$erro && empty($per_codigo)) {
    $erro = true;
    $mensagem = "É OBRIGATÓRIO INFORMAR O PERÍODO!";
}
if (!$erro && empty($alu_codigo)) {
    $erro = true;
    $mensagem = "É OBRIGATÓRIO INFORMAR O ALUNO!";
}

if ($erro) {
    showMessage(9, $mensagem, "javascript:history.go(-1);");
} else {
    $pal_codigo = getProximoCodigoPeriodoAluno();
	
	$query = " insert into periodos_alunos
				 ( pal_codigo, per_codigo, alu_codigo, pal_vlr_acrescimo, pal_per_acrescimo, pal_vlr_desconto, pal_per_desconto ) 
			   values 
				 ( '$pal_codigo', '$per_codigo', '$alu_codigo', 0, 0, 0, 5 ) ";
	
	$consulta = executeQuery($query);
	
	if (!$consulta) {
		$erro = true;
		showMessage(8, "Ocorreu um erro ao tentar inserir os alunos do período!", "javascript:history.go(-1);");
	}
}

if (!$erro) {
	showMessage(1, "Operação realizada com sucesso!", "view_periodo.php?p_per_codigo=" . $per_codigo);
}