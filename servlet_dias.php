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
if ($_REQUEST) {
    $filtro_periodo = addslashes(trim($_REQUEST["p_per_codigo"]));
    $filtro_aluno = addslashes(trim($_REQUEST["p_alu_codigo"]));
}

/*
// Debug
echo "<tt>";
echo "filtro_periodo....: " . $filtro_periodo . "</br>";
echo "filtro_aluno......: " . $filtro_aluno . "</br>";
echo "</tt>";
//Fim Debug
*/

$periodosAlunos = getAlunosDoPeriodo($filtro_periodo, $filtro_aluno);

while ($periodoAluno = mysql_fetch_assoc($periodosAlunos)) {
	$periodo = getPeriodo($periodoAluno['per_codigo']);
	$aluno = getAluno($periodoAluno['alu_codigo']);
	
	$periodoInicial = new DateTime($periodo->per_data_ini);
	$periodoFinal = new DateTime($periodo->per_data_fim);
	
	$per_codigo = $periodoAluno['per_codigo'];
	$alu_codigo = $periodoAluno['alu_codigo'];
	$aul_per_ini = $periodoInicial->format('Y-m-d');
	$aul_per_fim = $periodoFinal->format('Y-m-d');
	
	// apaga aulas
	$query = "  delete from aulas
	            where  per_codigo = '$per_codigo'
				and    alu_codigo = '$alu_codigo'
				and    aul_data_aula between '$aul_per_ini' and '$aul_per_fim' ";
	
	$consulta = executeQuery($query);
	
	if (!$consulta) {
		$erro = true;
		showMessage(8, "Ocorreu um erro ao tentar apagar as aulas antes de calcular os dias!", "javascript:history.go(-1);");
	}
	
	for($dataAtual = $periodoInicial; $dataAtual <= $periodoFinal; $dataAtual->modify('+1 day')) {
		$diaSemana = getDiaSemana($dataAtual);
		$aulas = getDiaDeAulaDoAluno($periodoAluno['alu_codigo'], $diaSemana);
		
		if (!empty($aulas['dia_preco'])) {
			$aul_codigo = getProximoCodigoAula();
			$per_codigo = $periodoAluno['per_codigo'];
			$alu_codigo = $periodoAluno['alu_codigo'];
			$aul_data_aula = $dataAtual->format('Y-m-d');
			$aul_hora_ini = $aulas['dia_hora_ini'];
			$aul_hora_fim = $aulas['dia_hora_fim'];
			$aul_preco = $aulas['dia_preco'];
			$aul_status = 1;
			
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
				showMessage(8, "Ocorreu um erro ao tentar calcular os dias!", "javascript:history.go(-1);");
			}
		}
	}
	
	if (!$erro) {
		if (empty($filtro_aluno)) {
			showMessage(1, "Operação realizada com sucesso!", "view_periodos.php");
		} else {
			showMessage(1, "Operação realizada com sucesso!", "view_periodo.php?p_per_codigo=" . $filtro_periodo);
		}
    }
}