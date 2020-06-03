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
	$alu_codigo = addslashes(trim($_POST["p_alu_codigo"]));
	$alu_nome   = addslashes(trim($_POST["p_alu_nome"]));
	$alu_email  = addslashes(trim($_POST["p_alu_email"]));
	$alu_fone   = addslashes(trim($_POST["p_alu_fone"]));
	$alu_status = addslashes(trim($_POST["p_alu_status"]));
	
	// quando é array não deve colocar addslashes e trim
	$dia_dia_semana = $_POST["p_dia_dia_semana"];
    $dia_hora_ini   = $_POST["p_dia_hora_ini"];
    $dia_hora_fim   = $_POST["p_dia_hora_fim"];
    $dia_preco      = $_POST["p_dia_preco"];
}

if (!$erro && empty($alu_nome)) {
    $erro = true;
    $mensagem = "O NOME DO ALUNO É OBRIGATÓRIO!";
}

if (!$erro && !empty($alu_email)) {
    if ( !validateEmail($alu_email) ) {
        $erro = true;
        $mensagem = "O E-MAIL INFORMADO É INVÁLIDO!";
    }
}

if (!$erro && empty($alu_status)) {
    $erro = true;
    $mensagem = "INFORME O STATUS DO ALUNO!";
}

for ($i = 1; $i <= count($dia_dia_semana); $i++ ) {
    $dia = addslashes(trim($dia_dia_semana[$i]));
    $ini = addslashes(trim($dia_hora_ini[$i]));
    $fim = addslashes(trim($dia_hora_fim[$i]));
    $vlr = addslashes(trim($dia_preco[$i]));
 
    // Checa se os três campos da linha estão corretos
    $ok  = true;
    // Checa se os três campos da linha estão em branco (aí desconsidera)
    if ( empty($ini) && empty($fim) && empty($vlr) ) {
        $ok = true;
    } else {
        // Checa se os três campos da linha estão preenchidos (aí salva)
        if ( !empty($ini) && !empty($fim) && !empty($vlr) ) {
            $ok = true;
            // Condição de erro. Apresentar mensagem para o usuário
            } else {
                $ok = false;
            }
    }
    if (!$erro && !$ok) {
        $erro = true;
        $mensagem = "PARA CADASTRAR UMA AULA É NECESSÁRIO INFORMAR OS 3 CAMPOS!";
    }
    // Valida a hora inicial
    if (!$erro && !empty($ini) && !validateTime($ini)) {
        $erro = true;
        $mensagem = "A HORA " . $ini . " É INVÁLIDA!";
    }
    // Valida a hora final
    if (!$erro && !empty($fim) && !validateTime($fim)) {
        $erro = true;
        $mensagem = "A HORA " . $fim . " É INVÁLIDA!";
    }
    // Valida o valor da aula
    if (!$erro && !empty($vlr) && !validateFloat($vlr)) {
        $erro = true;
        $mensagem = "O PREÇO DE R$ " . $vlr . " É INVÁLIDO!";
    }
}

if ($erro) {
    showMessage(9,$mensagem,"javascript:history.go(-1);");
} else {
    if (empty($alu_codigo)) {
        $alu_codigo = getProximoCodigoAluno();
        
        // insere aluno
        $query = " insert into alunos
                     ( alu_codigo, alu_nome, alu_email, alu_fone, alu_status )
                   values
                     ( '$alu_codigo', '$alu_nome','$alu_email','$alu_fone', '$alu_status' ) ";
		
        $consulta = executeQuery($query);
		
        if (!$consulta) {
            $erro = true;
            showMessage(8,"Ocorreu um erro ao tentar inserir um novo aluno!","javascript:history.go(-1);");
        }
    } else {
	    // atualiza cadastro do aluno
	    $query = " update alunos
     	           set    alu_nome   = '$alu_nome'
                   ,      alu_email  = '$alu_email'
                   ,      alu_fone   = '$alu_fone'
				   ,      alu_status = '$alu_status'
                   where  alu_codigo = '$alu_codigo' ";

        $consulta = executeQuery($query);

		if (!$consulta) {
            $erro = true;
            showMessage(8,"Ocorreu um erro ao tentar atualizar o cadastro do aluno!","javascript:history.go(-1);");
        }
    }
	
    // remove dias de aula do aluno...
    if (!$erro) {
        $query = " delete from dias where alu_codigo = '$alu_codigo' ";
        $consulta = executeQuery($query);
		
        if (!$consulta) {
            $erro = true;
            showMessage(8,"Ocorreu um erro ao excluir os dias de aula do aluno!","javascript:history.go(-1);");
        }
    }
	
    // ... e as inclui novamente, com as atualizações da tela
    if (!$erro) {
        // varre dias da semana
        for ($i = 1; $i <= count($dia_dia_semana); $i++ ) {
            $dia = addslashes(trim($dia_dia_semana[$i]));
            $ini = addslashes(trim($dia_hora_ini[$i]));
            $fim = addslashes(trim($dia_hora_fim[$i]));
            $vlr = emFormatoDinheiroSQL(addslashes(trim($dia_preco[$i])));
			
            // apenas adiciona as linhas onde todas as informações estão preenchidas
            if (!$erro && !empty($ini) && !empty($fim) && !empty($vlr)) {
                // insere aula
                $query = " insert into dias
                             ( alu_codigo, dia_dia_semana, dia_hora_ini, dia_hora_fim, dia_preco ) 
                           values 
                             ( '$alu_codigo', '$dia','$ini','$fim','$vlr' ) ";
				
                $consulta = executeQuery($query);
				
                if (!$consulta) {
                    $erro = true;
                    showMessage(8,"Ocorreu um erro ao tentar inserir os dias de aula do aluno!","javascript:history.go(-1);");
                }
            }
        }
    }
	
    if (!$erro) {
        showMessage(1,"Operação realizada com sucesso!","view_alunos.php");
    }
}