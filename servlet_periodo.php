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
	$per_codigo    = addslashes(trim($_POST["p_per_codigo"]));
	$per_descricao = addslashes(trim($_POST["p_per_descricao"]));
	$per_data_ini  = addslashes(trim($_POST["p_per_data_ini"]));
	$per_data_fim  = addslashes(trim($_POST["p_per_data_fim"]));
	$per_status    = addslashes(trim($_POST["p_per_status"]));
	
	// quando é array não deve colocar addslashes e trim
	$arr_alunos        = $_POST["p_alu_codigo"];
    $pal_vlr_acrescimo = $_POST["p_pal_vlr_acrescimo"];
    $pal_per_acrescimo = $_POST["p_pal_per_acrescimo"];
    $pal_vlr_desconto  = $_POST["p_pal_vlr_desconto"];
	$pal_per_desconto  = $_POST["p_pal_per_desconto"];
}


/*
// Debug
echo "<tt>";

echo "per_codigo......: " . $per_codigo        . "</br>";
echo "per_descricao...: " . $per_descricao     . "</br>";
echo "per_data_ini....: " . $per_data_ini      . "</br>";
echo "per_data_fim....: " . $per_data_fim      . "</br>";
echo "per_status......: " . $per_status        . "</br></br>";
echo "arr_alunos......: " . count($arr_alunos) . "</br></br>";

foreach($arr_alunos as $alu_codigo) {
	$vlr_acrescimo = addslashes(trim($pal_vlr_acrescimo[$alu_codigo]));
    $per_acrescimo = addslashes(trim($pal_per_acrescimo[$alu_codigo]));
    $vlr_desconto  = addslashes(trim($pal_vlr_desconto[$alu_codigo]));
    $per_desconto  = addslashes(trim($pal_per_desconto[$alu_codigo]));
	
	echo "alu_codigo......: " . $alu_codigo    . "</br>";
	echo "vlr_acrescimo...: " . $vlr_acrescimo . "</br>";
	echo "per_acrescimo...: " . $per_acrescimo . "</br>";
	echo "vlr_desconto....: " . $vlr_desconto  . "</br>";
	echo "per_desconto....: " . $per_desconto  . "</br></br>";
}

echo "</tt>";
//Fim Debug
*/

if (!$erro && empty($per_descricao)) {
    $erro = true;
    $mensagem = "A DESCRIÇÃO DO PERÍODO É OBRIGATÓRIA!";
}
if (!$erro && empty($per_data_ini)) {
    $erro = true;
    $mensagem = "A DATA INICIAL DO PERÍODO É OBRIGATÓRIA!";
}
if (!$erro && !empty($per_data_ini) && !validateDate($per_data_ini)) {
    $erro = true;
    $mensagem = "A DATA INICIAL DO PERÍODO É INVÁLIDA!";
}
if (!$erro && empty($per_data_fim)) {
    $erro = true;
    $mensagem = "A DATA FINAL DO PERÍODO É OBRIGATÓRIA!";
}
if (!$erro && !empty($per_data_fim) && !validateDate($per_data_fim)) {
	$erro = true;
	$mensagem = "A DATA FINAL DO PERÍODO É INVÁLIDA!";
}
if (!$erro && empty($per_status)) {
    $erro = true;
    $mensagem = "O STATUS DO PERÍODO É OBRIGATÓRIO!";
}

foreach($arr_alunos as $alu_codigo) {
	$vlr_acrescimo = addslashes(trim($pal_vlr_acrescimo[$alu_codigo]));
    $per_acrescimo = addslashes(trim($pal_per_acrescimo[$alu_codigo]));
    $vlr_desconto  = addslashes(trim($pal_vlr_desconto[$alu_codigo]));
    $per_desconto  = addslashes(trim($pal_per_desconto[$alu_codigo]));
	
	if (!$erro && !empty($vlr_acrescimo) && !validateFloat($vlr_acrescimo)) {
        $erro = true;
        $mensagem = "O VALOR DE ACRÉSCIMO DE R$ " . $vlr_acrescimo . " É INVÁLIDO!";
    }
	if (!$erro && !empty($per_acrescimo) && !validateFloat($per_acrescimo)) {
        $erro = true;
        $mensagem = "O PERCENTUAL DE ACRÉSCIMO DE R$ " . $per_acrescimo . " É INVÁLIDO!";
    }
	if (!$erro && !empty($vlr_desconto) && !validateFloat($vlr_desconto)) {
        $erro = true;
        $mensagem = "O VALOR DE DESCONTO DE R$ " . $vlr_desconto . " É INVÁLIDO!";
    }
	if (!$erro && !empty($per_desconto) && !validateFloat($per_desconto)) {
        $erro = true;
        $mensagem = "O PERCENTUAL DE DESCONTO DE R$ " . $per_desconto . " É INVÁLIDO!";
    }
}

// Carrega dados necessários para inserir na tabela
if (!$erro) {
    $per_data_ini  = emFormatoDataSQL($per_data_ini);
	$per_data_fim  = emFormatoDataSQL($per_data_fim);
}

if ($erro) {
    showMessage(9, $mensagem, "javascript:history.go(-1);");
} else {
    if (empty($per_codigo)) {
        $per_codigo = getProximoCodigoPeriodo();
        
        // insere periodo
        $query = " insert into periodos
                     ( per_codigo, per_descricao, per_data_ini, per_data_fim, per_status )
                   values
                     ( '$per_codigo', '$per_descricao', '$per_data_ini', '$per_data_fim', '$per_status' ) ";
		
        $consulta = executeQuery($query);
		
        if (!$consulta) {
            $erro = true;
            showMessage(8, "Ocorreu um erro ao tentar inserir um novo período! Será que já não há um período cadastrado com essas datas?", "javascript:history.go(-1);");
        }
    } else {
	    // atualiza cadastro do período
	    $query = " update periodos
     	           set    per_descricao = '$per_descricao'
                   ,      per_data_ini  = '$per_data_ini'
                   ,      per_data_fim  = '$per_data_fim'
				   ,      per_status    = '$per_status'
                   where  per_codigo    = '$per_codigo' ";

        $consulta = executeQuery($query);

		if (!$consulta) {
            $erro = true;
            showMessage(8, "Ocorreu um erro ao tentar atualizar o cadastro do período!", "javascript:history.go(-1);");
        }
    }
	
    // remove dias de aula do aluno...
    if (!$erro) {
        $query = " delete from periodos_alunos where per_codigo = '$per_codigo' ";
        $consulta = executeQuery($query);
		
        if (!$consulta) {
            $erro = true;
            showMessage(8, "Ocorreu um erro ao excluir os alunos do período!", "javascript:history.go(-1);");
        }
    }
	
    // ... e as inclui novamente, com as atualizações da tela
    if (!$erro) {
		foreach($arr_alunos as $alu_codigo) {
			$vlr_acrescimo = emFormatoDinheiroSQL(addslashes(trim($pal_vlr_acrescimo[$alu_codigo])));
			$per_acrescimo = emFormatoDinheiroSQL(addslashes(trim($pal_per_acrescimo[$alu_codigo])));
			$vlr_desconto  = emFormatoDinheiroSQL(addslashes(trim($pal_vlr_desconto[$alu_codigo])));
			$per_desconto  = emFormatoDinheiroSQL(addslashes(trim($pal_per_desconto[$alu_codigo])));
			
			$vlr_acrescimo = ( empty($vlr_acrescimo) ? 0 : $vlr_acrescimo );
			$per_acrescimo = ( empty($per_acrescimo) ? 0 : $per_acrescimo );
			$vlr_desconto  = ( empty($vlr_desconto)  ? 0 : $vlr_desconto  );
			$per_desconto  = ( empty($per_desconto)  ? 0 : $per_desconto  );
			
            if (!$erro) {
				$pal_codigo = getProximoCodigoPeriodoAluno();
				
                $query = " insert into periodos_alunos
                             ( pal_codigo, per_codigo, alu_codigo, pal_vlr_acrescimo, pal_per_acrescimo, pal_vlr_desconto, pal_per_desconto ) 
                           values 
                             ( '$pal_codigo', '$per_codigo', '$alu_codigo', '$vlr_acrescimo', '$per_acrescimo', '$vlr_desconto', '$per_desconto' ) ";
				
                $consulta = executeQuery($query);
				
                if (!$consulta) {
                    $erro = true;
                    showMessage(8, "Ocorreu um erro ao tentar inserir os alunos do período!", "javascript:history.go(-1);");
                }
            }
        }
    }

	if (!$erro) {
        showMessage(1,"Operação realizada com sucesso!","view_periodos.php");
    }
}