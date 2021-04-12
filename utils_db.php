<?php

function startDatabase() {
    // Conecta com o database
    // $link = mysql_connect('localhost','root','');
	$link = mysql_connect('mysql.hostinger.com.br','u187390300_user','u187390300_pass');
    if (!$link) {
        die('Not connected to MySQL: ' . mysql_error());
    }
    
    // Seleciona, abre e entra no database correto
    // $db_selected = mysql_select_db('englishware', $link);
	$db_selected = mysql_select_db('u187390300_db', $link);
    if (!$db_selected) {
        die ('Not connected to Englishware database: ' . mysql_error());
    }
}

function executeQuery($query) {
    $resultset = mysql_query($query);
    
    // Trata erro
    if (!$resultset) {
        $mensagem  = 'Invalid query: ' . mysql_error() . "\n";
        $mensagem .= 'Whole query: ' . $query;
    }
    
    return $resultset;
}

function setTransactionLevel() {
    $query  = sprintf(" SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED ");
	$temp   = executeQuery($query);
}

// Alunos

function getProximoCodigoAluno() {
	$query  = sprintf(" SELECT COALESCE(MAX(a.alu_codigo),0) + 1 alu_codigo ");
	$query .= sprintf(" FROM   alunos a ");
	
    $result = executeQuery($query);
	$value  = mysql_fetch_object($result);
	
	return $value->alu_codigo;
}

function getTodosOsAlunos($alu_status) {
	$query  = sprintf(" SELECT a.alu_codigo, a.alu_nome, a.alu_email, a.alu_fone, a.alu_status ");
	$query .= sprintf(" FROM   alunos a ");
	$query .= sprintf(" WHERE  1 = 1 ");
	if (!empty($alu_status)) {
		$query .= sprintf(" AND a.alu_status = " . $alu_status);	
	}
	$query .= sprintf(" ORDER  BY a.alu_nome ");
	
	$alunos = executeQuery($query);
	
	return $alunos;
}

function getAluno($alu_codigo) {
	$query  = sprintf(" SELECT a.alu_codigo, a.alu_nome, a.alu_email, a.alu_fone, a.alu_status ");
	$query .= sprintf(" FROM   alunos a ");
	$query .= sprintf(" WHERE  a.alu_codigo = " . $alu_codigo);
	
	$aluno = executeQuery($query);
	
	return mysql_fetch_object($aluno);
}

// Dias

function getDiaDeAulaDoAluno($alu_codigo, $aul_dia) {
    $query  = sprintf(" SELECT d.dia_dia_semana, d.dia_hora_ini, d.dia_hora_fim, d.dia_preco ");
    $query .= sprintf(" FROM   dias d ");
    $query .= sprintf(" WHERE  d.alu_codigo = " . $alu_codigo);
    $query .= sprintf(" AND    d.dia_dia_semana = " . $aul_dia);
    $query .= sprintf(" ORDER  BY d.dia_dia_semana ");
    
	$dias = executeQuery($query);
	
    return mysql_fetch_assoc($dias);
}

// Períodos

function getProximoCodigoPeriodo() {
	$query  = sprintf(" SELECT COALESCE(MAX(p.per_codigo),0) + 1 per_codigo ");
	$query .= sprintf(" FROM   periodos p ");
	
    $result = executeQuery($query);
	$value  = mysql_fetch_object($result);
	
	return $value->per_codigo;
}

function getTodosOsPeriodos($per_status) {
	$query  = sprintf(" SELECT p.per_codigo, p.per_descricao, p.per_data_ini, p.per_data_fim, p.per_status ");
	$query .= sprintf(" FROM   periodos p ");
	$query .= sprintf(" WHERE  1 = 1 ");
	if (!empty($per_status)) {
		$query .= sprintf(" AND p.per_status = " . $per_status);	
	}
	$query .= sprintf(" ORDER  BY p.per_descricao ");
	
	$periodos = executeQuery($query);
	
    return $periodos;
}

function getPeriodo($per_codigo) {
	$query  = sprintf(" SELECT p.per_codigo, p.per_descricao, p.per_data_ini, p.per_data_fim, p.per_status ");
	$query .= sprintf(" FROM   periodos p ");
	$query .= sprintf(" WHERE  p.per_codigo = " . $per_codigo);
	
	$periodo = executeQuery($query);
	
    return mysql_fetch_object($periodo);
}

// Periodos Alunos

function getProximoCodigoPeriodoAluno() {
	$query  = sprintf(" SELECT COALESCE(MAX(p.pal_codigo),0) + 1 pal_codigo ");
	$query .= sprintf(" FROM   periodos_alunos p ");
	
    $result = executeQuery($query);
	$value  = mysql_fetch_object($result);
	
	return $value->pal_codigo;
}

function getPeriodoAluno($per_codigo, $alu_codigo) {
	$query  = sprintf(" SELECT p.pal_codigo, p.per_codigo, p.alu_codigo, p.pal_vlr_acrescimo, p.pal_per_acrescimo, p.pal_vlr_desconto, p.pal_per_desconto ");
	$query .= sprintf(" FROM   periodos_alunos p ");
	$query .= sprintf(" WHERE  p.per_codigo = " . $per_codigo);
	$query .= sprintf(" AND    p.alu_codigo = " . $alu_codigo);
	
	$periodoAluno = executeQuery($query);
	
    return mysql_fetch_object($periodoAluno);
}

function getAlunosDoPeriodo($per_codigo, $alu_codigo) {
	$query  = sprintf(" SELECT p.pal_codigo, p.per_codigo, a.alu_codigo, a.alu_nome, a.alu_status, p.pal_vlr_acrescimo, p.pal_per_acrescimo, p.pal_vlr_desconto, p.pal_per_desconto ");
	$query .= sprintf(" FROM   periodos_alunos p ");
	$query .= sprintf(" JOIN   alunos          a ON a.alu_codigo = p.alu_codigo");
	$query .= sprintf(" WHERE  1 = 1 ");
	if (!empty($per_codigo)) {
		$query .= sprintf(" AND    p.per_codigo = " . $per_codigo);
	}
	if (!empty($alu_codigo)) {
		$query .= sprintf(" AND    p.alu_codigo = " . $alu_codigo);
	}
	$query .= sprintf(" ORDER BY p.per_codigo, a.alu_nome ");
	
	$periodosAlunos = executeQuery($query);
	
    return $periodosAlunos;
}

function getAlunosNaoDoPeriodo($per_codigo) {
	$query  = sprintf(" SELECT a.alu_codigo, a.alu_nome ");
	$query .= sprintf(" FROM   alunos a ");
	$query .= sprintf(" WHERE  a.alu_status = 1 ");
	$query .= sprintf(" AND    a.alu_codigo NOT IN ( SELECT p.alu_codigo FROM periodos_alunos p WHERE p.per_codigo = " . $per_codigo . " ) ");
	$query .= sprintf(" ORDER  BY a.alu_nome ");
	
	$outros = executeQuery($query);
	
    return $outros;
}

// Aulas

function getProximoCodigoAula() {
	$query  = sprintf(" SELECT COALESCE(MAX(a.aul_codigo),0) + 1 aul_codigo ");
	$query .= sprintf(" FROM   aulas a ");
	
    $result = executeQuery($query);
	$value  = mysql_fetch_object($result);
	
	return $value->aul_codigo;
}

function getTodasAsAulas() {
	$query  = sprintf(" SELECT a.aul_codigo, p.per_codigo, p.per_descricao, l.alu_codigo, l.alu_nome ");
	$query .= sprintf(" ,      a.aul_data_aula, a.aul_hora_ini, a.aul_hora_fim, a.aul_preco, a.aul_status ");
	$query .= sprintf(" FROM   aulas    a ");
	$query .= sprintf(" JOIN   periodos p ON p.per_codigo = a.per_codigo ");
	$query .= sprintf(" JOIN   alunos   l ON l.alu_codigo = a.alu_codigo ");
	$query .= sprintf(" WHERE  1 = 1 ");
	$query .= sprintf(" ORDER  BY a.aul_data_aula, a.aul_hora_ini, a.aul_hora_fim ");
	
	$aulas = executeQuery($query);
	
    return $aulas;
}

function getAulasPorFiltro($per_codigo, $alu_codigo, $aul_status, $aul_data_ini, $aul_data_fim) {
	$query  = sprintf(" SELECT a.aul_codigo, p.per_codigo, p.per_descricao, l.alu_codigo, l.alu_nome ");
	$query .= sprintf(" ,      a.aul_data_aula, a.aul_hora_ini, a.aul_hora_fim, a.aul_preco, a.aul_status ");
	$query .= sprintf(" FROM   aulas    a ");
	$query .= sprintf(" JOIN   periodos p ON p.per_codigo = a.per_codigo ");
	$query .= sprintf(" JOIN   alunos   l ON l.alu_codigo = a.alu_codigo ");
	$query .= sprintf(" WHERE  1 = 1 ");
	if (!empty($per_codigo)) {
		$query .= sprintf(" AND    a.per_codigo = " . $per_codigo);
	}
	if (!empty($alu_codigo)) {
		$query .= sprintf(" AND    a.alu_codigo = " . $alu_codigo);
	}
	if (!empty($aul_status)) {
		$query .= sprintf(" AND    a.aul_status = " . $aul_status);
	}
	if (!empty($aul_data_ini)) {
		$query .= sprintf(" AND    a.aul_data_aula >= '" . emFormatoDataSql($aul_data_ini) . "'");
	}
	if (!empty($aul_data_fim)) {
		$query .= sprintf(" AND    a.aul_data_aula <= '" . emFormatoDataSql($aul_data_fim) . "'");
	}
	$query .= sprintf(" ORDER  BY a.aul_data_aula, a.aul_hora_ini, a.aul_hora_fim ");
	
	$aulas = executeQuery($query);
	
    return $aulas;
}

function getAlunosComAulasConfirmadasNoPeriodo($per_codigo, $alu_codigo) {
	$query  = sprintf(" SELECT DISTINCT a.alu_codigo ");
	$query .= sprintf(" FROM   aulas    a ");
	$query .= sprintf(" WHERE  1 = 1 ");
	if (!empty($per_codigo)) {
		$query .= sprintf(" AND    a.per_codigo = " . $per_codigo);
	}
	if (!empty($alu_codigo)) {
		$query .= sprintf(" AND    a.alu_codigo = " . $alu_codigo);
	}
	$query .= sprintf(" AND    a.aul_status = 1 ");
	$query .= sprintf(" ORDER  BY a.per_codigo, a.alu_codigo, a.aul_data_aula, a.aul_hora_ini, a.aul_hora_fim ");
	
	$aulas = executeQuery($query);
	
    return $aulas;
}

function getAulasConfirmadasDoAlunoNoPeriodo($per_codigo, $alu_codigo) {
	$query  = sprintf(" SELECT a.aul_codigo, a.per_codigo, a.alu_codigo ");
	$query .= sprintf(" ,      a.aul_data_aula, a.aul_hora_ini, a.aul_hora_fim, a.aul_preco, a.aul_status ");
	$query .= sprintf(" FROM   aulas    a ");
	$query .= sprintf(" WHERE  a.per_codigo = " . $per_codigo);
	$query .= sprintf(" AND    a.alu_codigo = " . $alu_codigo);
	$query .= sprintf(" AND    a.aul_status = 1 ");
	$query .= sprintf(" ORDER  BY a.per_codigo, a.alu_codigo, a.aul_data_aula, a.aul_hora_ini, a.aul_hora_fim ");
	
	$aulas = executeQuery($query);
	
    return $aulas;
}

function getAula($aul_codigo) {
	$query  = sprintf(" SELECT a.aul_codigo, p.per_codigo, p.per_descricao, l.alu_codigo, l.alu_nome ");
	$query .= sprintf(" ,      a.aul_data_aula, a.aul_hora_ini, a.aul_hora_fim, a.aul_preco, a.aul_status ");
	$query .= sprintf(" FROM   aulas    a ");
	$query .= sprintf(" JOIN   periodos p ON p.per_codigo = a.per_codigo ");
	$query .= sprintf(" JOIN   alunos   l ON l.alu_codigo = a.alu_codigo ");
	$query .= sprintf(" WHERE  a.aul_codigo = " . $aul_codigo);
	$query .= sprintf(" ORDER  BY a.aul_data_aula, a.aul_hora_ini, a.aul_hora_fim ");
	
	$aula = executeQuery($query);
	
    return mysql_fetch_assoc($aula);
}

// Relatórios

function getRelatorioGeralPeriodo($per_codigo) {
	$query  = sprintf(" select al.alu_codigo, al.alu_nome, pa.pal_vlr_acrescimo, pa.pal_vlr_desconto, pa.pal_per_acrescimo, pa.pal_per_desconto ");
	$query .= sprintf(" ,      sum(au.aul_preco) total_aulas ");
	$query .= sprintf(" ,      sum(au.aul_preco) * (1 + (pa.pal_per_acrescimo / 100)) * (1 - (pa.pal_per_desconto  / 100)) + pa.pal_vlr_acrescimo - pa.pal_vlr_desconto total_geral ");
	$query .= sprintf(" from   aulas           au ");
	$query .= sprintf(" join   periodos_alunos pa on pa.per_codigo = au.per_codigo and pa.alu_codigo = au.alu_codigo ");
	$query .= sprintf(" join   periodos        pe on pe.per_codigo = au.per_codigo ");
	$query .= sprintf(" join   alunos          al on al.alu_codigo = au.alu_codigo ");
	$query .= sprintf(" where  au.per_codigo = " . $per_codigo);
	$query .= sprintf(" and    au.aul_status = 1 ");
	$query .= sprintf(" group  by al.alu_codigo, al.alu_nome, pa.pal_vlr_acrescimo, pa.pal_vlr_desconto, pa.pal_per_acrescimo, pa.pal_per_desconto ");
	$query .= sprintf(" order  by al.alu_nome ");
	
	return executeQuery($query);
}

function getValoresPeriodoAluno($per_codigo, $alu_codigo) {
	$query  = sprintf(" select al.alu_codigo, al.alu_nome, pa.pal_vlr_acrescimo, pa.pal_vlr_desconto, pa.pal_per_acrescimo, pa.pal_per_desconto ");
	$query .= sprintf(" ,      sum(au.aul_preco) total_aulas ");
	$query .= sprintf(" ,      sum(au.aul_preco) * (1 + (pa.pal_per_acrescimo / 100)) + pa.pal_vlr_acrescimo total_sem_desconto ");
	$query .= sprintf(" ,      sum(au.aul_preco) * (1 + (pa.pal_per_acrescimo / 100)) * (1 - (pa.pal_per_desconto  / 100)) + pa.pal_vlr_acrescimo - pa.pal_vlr_desconto total_com_desconto ");
	$query .= sprintf(" from   aulas           au ");
	$query .= sprintf(" join   periodos_alunos pa on pa.per_codigo = au.per_codigo and pa.alu_codigo = au.alu_codigo ");
	$query .= sprintf(" join   periodos        pe on pe.per_codigo = au.per_codigo ");
	$query .= sprintf(" join   alunos          al on al.alu_codigo = au.alu_codigo ");
	$query .= sprintf(" where  au.per_codigo = " . $per_codigo);
    $query .= sprintf(" and    au.alu_codigo = " . $alu_codigo);
	$query .= sprintf(" and    au.aul_status = 1 ");
	$query .= sprintf(" group  by al.alu_codigo, al.alu_nome, pa.pal_vlr_acrescimo, pa.pal_vlr_desconto, pa.pal_per_acrescimo, pa.pal_per_desconto ");
	$query .= sprintf(" order  by al.alu_nome ");
	
	$valores = executeQuery($query);

	return mysql_fetch_object($valores);
}

// Emails

function getProximoIdEmail() {
	$query  = sprintf(" SELECT COALESCE(MAX(e.ema_codigo),0) + 1 ema_codigo ");
	$query .= sprintf(" FROM   emails e ");
	
    $result = executeQuery($query);
	$value  = mysql_fetch_object($result);
	
	return $value->ema_codigo;
}

function getEmailsPendentesDeEnvio() {
	$query  = sprintf(" select e.ema_codigo, e.ema_data_inclusao ");
	$query .= sprintf(" ,      e.ema_remetente_email, e.ema_remetente_nome ");
	$query .= sprintf(" ,      e.ema_destinatario_email, e.ema_destinatario_nome ");
	$query .= sprintf(" ,      e.ema_assunto, e.ema_mensagem ");
	$query .= sprintf(" from   emails e ");
	$query .= sprintf(" where  e.ema_flg_enviado = 'N' ");
	$query .= sprintf(" order  by e.ema_codigo ");
	
	return executeQuery($query);
}
