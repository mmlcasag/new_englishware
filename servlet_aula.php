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
    $aul_codigo    = addslashes(trim($_POST["p_aul_codigo"]));
	$per_codigo    = addslashes(trim($_POST["p_per_codigo"]));
	$alu_codigo    = addslashes(trim($_POST["p_alu_codigo"]));
    $aul_data_aula = addslashes(trim($_POST["p_aul_data_aula"]));
    $aul_hora_ini  = addslashes(trim($_POST["p_aul_hora_ini"]));
    $aul_hora_fim  = addslashes(trim($_POST["p_aul_hora_fim"]));
    $aul_preco     = addslashes(trim($_POST["p_aul_preco"]));
	$aul_status    = addslashes(trim($_POST["p_aul_status"]));
}

if (!$erro && empty($aul_data_aula)) {
    $erro = true;
    $mensagem = "A DATA DA AULA É OBRIGATÓRIA!";
}
if (!$erro && !empty($aul_data_aula) && !validateDate($aul_data_aula)) {
    $erro = true;
    $mensagem = "A DATA DA AULA " . $aul_data_aula . " É INVÁLIDA!";
}
if (!$erro && empty($aul_hora_ini)) {
    $erro = true;
    $mensagem = "A HORA DE INÍCIO DA AULA É OBRIGATÓRIA!";
}
if (!$erro && !empty($aul_hora_ini) && !validateTime($aul_hora_ini)) {
    $erro = true;
    $mensagem = "A HORA DE INÍCIO DA AULA " . $aul_hora_ini . " É INVÁLIDA!";
}
if (!$erro && empty($aul_hora_fim)) {
    $erro = true;
    $mensagem = "A HORA DE TÉRMINO DA AULA É OBRIGATÓRIA!";
}
if (!$erro && !empty($aul_hora_fim) && !validateTime($aul_hora_fim)) {
    $erro = true;
    $mensagem = "A HORA DE TÉRMINO DA AULA " . $aul_hora_fim . " É INVÁLIDA!";
}
if (!$erro && empty($aul_preco)) {
    $erro = true;
    $mensagem = "O PREÇO DA AULA É INVÁLIDO!";
}   
if (!$erro && !empty($aul_preco) && !validateFloat($aul_preco)) {
    $erro = true;
    $mensagem = "O PREÇO DA AULA DE R$ " . $aul_preco . " É INVÁLIDO!";
}
if (!$erro && empty($per_codigo)) {
    $erro = true;
    $mensagem = "É OBRIGATÓRIO SELECIONAR O PERÍODO!";
}
if (!$erro && empty($alu_codigo)) {
    $erro = true;
    $mensagem = "É OBRIGATÓRIO SELECIONAR O ALUNO!";
}
if (!$erro && empty($aul_status)) {
	$erro = true;
	$mensagem = "O STATUS DA AULA É OBRIGATÓRIO!";
}

// Carrega dados necessários para inserir na tabela
if (!$erro) {
    $aul_data_aula  = emFormatoDataSQL($aul_data_aula);
	$aul_preco      = emFormatoDinheiroSQL($aul_preco);
}

if ($erro) {
    showMessage(9,$mensagem,"javascript:history.go(-1);");
} else {
	if (empty($aul_codigo)) {
        $aul_codigo = getProximoCodigoAula();
        
        // insere aula
		$query = "  insert into aulas
					  ( aul_codigo, per_codigo, alu_codigo
					  , aul_data_aula, aul_hora_ini, aul_hora_fim, aul_preco, aul_status
					  )
					values
					  ( '$aul_codigo', '$per_codigo', '$alu_codigo'
					  , '$aul_data_aula', '$aul_hora_ini', '$aul_hora_fim', '$aul_preco', '$aul_status'
					) ";
		
		$consulta = executeQuery($query);
		
        if (!$consulta) {
            $erro = true;
            showMessage(8,"Ocorreu um erro ao tentar inserir uma nova aula!","javascript:history.go(-1);");
        }
	} else {
		// altera aula
		$query = " update aulas
				   set    per_codigo       = '$per_codigo'
				   ,      alu_codigo       = '$alu_codigo'
				   ,      aul_data_aula    = '$aul_data_aula'
				   ,      aul_hora_ini     = '$aul_hora_ini'
				   ,      aul_hora_fim     = '$aul_hora_fim'
				   ,      aul_preco        = '$aul_preco'
				   ,      aul_status       = '$aul_status'
				   where  aul_codigo       = '$aul_codigo' ";
		
		$consulta = executeQuery($query);
		
		if (!$consulta) {
			$erro = true;
			showMessage(8,"Ocorreu um erro ao tentar atualizar o cadastro da aula!","javascript:history.go(-1);");
		}
	}
	
	if (!$erro) {
        showMessage(1,"Operação realizada com sucesso!","view_aulas.php?p_per_codigo=" . $per_codigo . "&p_alu_codigo=" . $alu_codigo);
    }
}