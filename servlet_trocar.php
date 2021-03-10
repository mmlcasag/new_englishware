<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'utils.php';
require_once 'utils_db.php';

startDatabase();

// Variáveis
$erro = false;
$mensagem = "";

// Parâmetros
if ($_POST) {
    // quando é array não deve colocar addslashes e trim
    $aul_codigo = $_POST["p_aul_codigo"];
    $aul_status = $_POST["p_aul_status"];
}

/*
// Debug
echo "<tt>";
echo "aul_codigo....: " . $aul_codigo . "</br>";
echo "aul_status....: " . $aul_status . "</br>";
for ($i = 1; $i <= count($aul_codigo); $i++ ) {
	$cod = addslashes(trim($aul_codigo[$i]));
    $sit = addslashes(trim($aul_status[$i]));
	
	echo "--> aul_codigo....: " . $aul_codigo[$i] . "</br>";
	echo "--> aul_status....: " . $aul_status[$i] . "</br>";
}
echo "</tt>";
//Fim Debug
*/

for ($i = 1; $i <= count($aul_codigo); $i++ ) {
    $cod = addslashes(trim($aul_codigo[$i]));
    $sit = addslashes(trim($aul_status[$i]));
    
	// atualiza cadastro da aula
	$query = " update aulas
			   set    aul_status = '$sit'
			   where  aul_codigo = '$cod' ";
	
	$consulta = executeQuery($query);
	
	if (!$consulta) {
		$erro = true;
		showMessage(8,"Ocorreu um erro ao tentar atualizar o cadastro da aula!","javascript:history.go(-1);");
	}
}

if (!$erro) {
    showMessage(1,"Operação realizada com sucesso!","javascript:history.go(-1);");
}