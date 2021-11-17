<?php
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'utils.php';
require_once 'utils_db.php';
require_once 'utils_mail.php';

startDatabase();
setTransactionLevel();

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

$periodo = getPeriodo($filtro_periodo);
$alunos = getAlunosComAulasConfirmadasNoPeriodo($filtro_periodo, $filtro_aluno);

while ($aluno = mysql_fetch_assoc($alunos)) {
	$aluno = getAluno($aluno['alu_codigo']);
	$aulas = getAulasConfirmadasDoAlunoNoPeriodo($periodo->per_codigo, $aluno->alu_codigo);
	$extra = getPeriodoAluno($periodo->per_codigo, $aluno->alu_codigo);
	
	$qtdeAulas = 0;
	$totalAulas = 0;
	$percentualAcrescimo = 0;
	$percentualDesconto = 0;
	$valorAcrescimo = 0;
	$valorDesconto = 0;
	$valorTotal = 0;
	$valorPromocional = 0;
	
	$conteudo = '<ul>';
	while ($aula = mysql_fetch_assoc($aulas)) {
		$qtdeAulas = $qtdeAulas + 1;
		$totalAulas = $totalAulas + $aula['aul_preco'];
		
		$dia = emFormatoData($aula['aul_data_aula']);
		$sem = emDiaSemanaReduzido(getDiaSemana(new DateTime($aula['aul_data_aula'])));
		$ini = emFormatoHora($aula['aul_hora_ini']);
		$fim = emFormatoHora($aula['aul_hora_fim']);
		$vlr = emFormatoDinheiro($aula['aul_preco']);
		
		$conteudo .= '<li>Dia <b>' . $dia . '</b> (' . $sem . '), das <b>' . $ini . '</b> às <b>' . $fim . '</b></li>';
	}
	$conteudo .= '</ul>';
	
	$conteudo .= 'Total de aulas no período: <b>' . $qtdeAulas . '</b><br />';
	$conteudo .= 'Valor total das aulas no período: <b>R$ ' . emFormatoDinheiro($totalAulas) . '</b><br /><br />';
	
	if ($extra->pal_per_acrescimo > 0) {
		$percentualAcrescimo = ($totalAulas * $extra->pal_per_acrescimo / 100);
	}
	if ($extra->pal_per_desconto > 0) {
		$percentualDesconto = ($totalAulas * $extra->pal_per_desconto / 100);
	}
	if ($extra->pal_vlr_acrescimo > 0) {
		$valorAcrescimo = $extra->pal_vlr_acrescimo;
		$conteudo .= 'Acréscimos lançados no período: <b>R$ ' . emFormatoDinheiro($valorAcrescimo) . '</b><br />';
	}
	if ($extra->pal_vlr_desconto > 0) {
		$valorDesconto = $extra->pal_vlr_desconto;
		$conteudo .= 'Descontos lançados no período: <b>R$ ' . emFormatoDinheiro($valorDesconto) . '</b><br /><br />';
	}
	
	$valorTotal = $totalAulas + $percentualAcrescimo + $valorAcrescimo - $valorDesconto;
	$conteudo .= '<b>Total geral: R$ ' . emFormatoDinheiro($valorTotal) . '</b><br/>';
	
	$valorPromocional = $totalAulas + $percentualAcrescimo - $percentualDesconto + $valorAcrescimo - $valorDesconto;
	if ($valorTotal != $valorPromocional) {
		$conteudo .= '<b><font color="red">Valor caso pago até o dia 10: R$ ' . emFormatoDinheiro($valorPromocional) . '</font></b>';
	}
	
	$mensagem = getEmailAulasDoPeriodo();
	$mensagem = str_replace("#PERIODO#", $periodo->per_descricao, $mensagem);
	$mensagem = str_replace("#NOME#", $aluno->alu_nome, $mensagem);
	$mensagem = str_replace("#CONTEUDO#", $conteudo, $mensagem);
	
	if (!empty($aluno->alu_email)) {
		$ema_codigo = getProximoIdEmail();
		
		$query = " insert into emails
					 ( ema_codigo, ema_data_inclusao, ema_remetente_email, ema_remetente_nome, ema_destinatario_email, ema_destinatario_nome, ema_assunto, ema_mensagem, ema_flg_enviado )
				   values 
					 ( '$ema_codigo', curdate(), 'fabibranchini@gmail.com', 'Fabiana Branchini', '$aluno->alu_email', '$aluno->alu_nome', 'Englishware :: $periodo->per_descricao :: $aluno->alu_nome', '$mensagem', 'N' ) ";
		
		$consulta = executeQuery($query);
		
		if (!$consulta) {
			$erro = true;
			showMessage(8, "Ocorreu um erro ao tentar inserir o registro na fila de e-mails!", "javascript:history.go(-1);");
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