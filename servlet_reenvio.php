<?php
header("Content-Type: text/plain");

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'On');

require_once 'utils.php';
require_once 'utils_db.php';
require_once 'utils_mail.php';

startDatabase();
setTransactionLevel();

foreach ($_POST["p_arr_alunos"] as $alu_codigo) {
	set_time_limit(1000);
	$aluno = getAluno($alu_codigo);
	
    $mensagem = getEmailReenvio();
	$mensagem = str_replace("#NOME#", $aluno->alu_nome, $mensagem);
	
    if (!empty($aluno->alu_email)) {
        sendmail($aluno->alu_email, 'Lembrete :: Último dia de pagamento com desconto :: ' . $aluno->alu_nome, $mensagem); sleep(1); flush();
    }
}

showMessage(1, "Operação realizada com sucesso", "view_reenviar.php");