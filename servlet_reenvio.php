<?php
header("Content-Type: text/plain");

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'utils.php';
require_once 'utils_db.php';
require_once 'utils_mail.php';

startDatabase();
setTransactionLevel();

$per_codigo = $_POST["p_per_codigo"];
$arr_alunos = $_POST["p_arr_alunos"];

foreach ($arr_alunos as $alu_codigo) {
	$aluno = getAluno($alu_codigo);
	$periodo = getPeriodo($per_codigo);
	$valores = getValoresPeriodoAluno($per_codigo, $alu_codigo);
	
    $mensagem = getEmailReenvio();
	$mensagem = str_replace("#NOME#", $aluno->alu_nome, $mensagem);
	$mensagem = str_replace("#PERIODO#", $periodo->per_descricao, $mensagem);
	$mensagem = str_replace("#VALOR_SEM_DESCONTO#", emFormatoDinheiro($valores->total_aulas), $mensagem);
	$mensagem = str_replace("#VALOR_COM_DESCONTO#", emFormatoDinheiro($valores->total_geral), $mensagem);
    
    if (!empty($aluno->alu_email)) {
        $ema_codigo = getProximoIdEmail();
		
		$query = " insert into emails
					 ( ema_codigo, ema_data_inclusao, ema_remetente_email, ema_remetente_nome, ema_destinatario_email, ema_destinatario_nome, ema_assunto, ema_mensagem, ema_flg_enviado )
				   values 
					 ( '$ema_codigo', curdate(), 'fabibr@gmail.com', 'Fabiana Branchini', '$aluno->alu_email', '$aluno->alu_nome', 'Englishware :: Último dia de pagamento com desconto :: $aluno->alu_nome', '$mensagem', 'N' ) ";
		
		$consulta = executeQuery($query);
		
		if (!$consulta) {
			$erro = true;
			showMessage(8, "Ocorreu um erro ao tentar inserir o registro na fila de e-mails!", "javascript:history.go(-1);");
		}
    }
}

showMessage(1, "Operação realizada com sucesso", "view_reenviar.php");
