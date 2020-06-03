<?php
header("Content-Type: text/html; charset=utf-8");

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

function validateTime($hora) {    $ok = true;    if ( preg_match('/^[0-9]{2}:[0-9]{2}$/', $hora) ) {        if (substr($hora,0,2) > "23") {            $ok = false;        }        if (substr($hora,3,2) > "59") {            $ok = false;        }    } else {        $ok = false;    }    return $ok;}
function validateFloat($number) {    if ( preg_match( '/^[\-+]?[0-9]*\,?[0-9]+$/', $number) ) {        return true;    } else {        return false;    }}
function validateEmail($email) {    if ( filter_var($email, FILTER_VALIDATE_EMAIL) ) {        return true;    } else {        return false;    }}
function validateDate($date) {    if ( preg_match( '/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/', $date) ) {        return true;    } else {        return false;    }}
function getDiaSemana($data) {
	return (date('w', strtotime($data->format('Y-m-d')))+1);
}

function diasemana($data) {    $dia =  substr("$data", 0, 2);    $mes =  substr("$data", 3, 2);    $ano =  substr("$data", 6, 4);        $diasemana = date("w", mktime(0,0,0,$mes,$dia,$ano) );
    switch($diasemana) {        case "0": $diasemana = "Dom"; break;        case "1": $diasemana = "Seg"; break;        case "2": $diasemana = "Ter"; break;        case "3": $diasemana = "Qua"; break;        case "4": $diasemana = "Qui"; break;        case "5": $diasemana = "Sex"; break;        case "6": $diasemana = "Sáb"; break;    }
    return $diasemana;}
function emDiaSemanaReduzido($dia_semana) {
	$dia_extenso = "";
	
	switch ($dia_semana) {
		case 1: $dia_extenso = "Dom"; break;
		case 2: $dia_extenso = "Seg"; break;
		case 3: $dia_extenso = "Ter"; break;
		case 4: $dia_extenso = "Qua"; break;
		case 5: $dia_extenso = "Qui"; break;
		case 6: $dia_extenso = "Sex"; break;
		case 7: $dia_extenso = "Sáb"; break;
	}
	
	return $dia_extenso;
}
function emDiaSemana($dia_semana) {
	$dia_extenso = "";
	
	switch ($dia_semana) {
		case 1: $dia_extenso = "Domingo"; break;
		case 2: $dia_extenso = "Segunda-feira"; break;
		case 3: $dia_extenso = "Terça-feira"; break;
		case 4: $dia_extenso = "Quarta-feira"; break;
		case 5: $dia_extenso = "Quinta-feira"; break;
		case 6: $dia_extenso = "Sexta-feira"; break;
		case 7: $dia_extenso = "Sábado"; break;
	}
	
	return $dia_extenso;
}

function emMesExtenso($mes) {
	$mes_extenso = "";
	
	switch ($mes) {
		case 1: $mes_extenso = "Janeiro"; break;
		case 2: $mes_extenso = "Fevereiro"; break;
		case 3: $mes_extenso = "Março"; break;
		case 4: $mes_extenso = "Abril"; break;
		case 5: $mes_extenso = "Maio"; break;
		case 6: $mes_extenso = "Junho"; break;
		case 7: $mes_extenso = "Julho"; break;
		case 8: $mes_extenso = "Agosto"; break;
		case 9: $mes_extenso = "Setembro"; break;
		case 10: $mes_extenso = "Outubro"; break;
		case 11: $mes_extenso = "Novembro"; break;
		case 12: $mes_extenso = "Dezembro"; break;
	}
	
	return $mes_extenso;
}

function emFormatoData($data) {
	if (empty($data)) {
		return "";
	} else {
		return date('d/m/Y',strtotime($data));
	}
}

function emFormatoHora($hora) {
	if (empty($hora)) {
		return "";
	} else {
		return date('H:i',strtotime($hora));
	}
}

function emFormatoDinheiro($valor) {
	if (empty($valor)) {
		return "";
	} else {
		return number_format($valor,2,',','.');
	}
}

function emFormatoDataSQL($data) {
    if (empty($data)) {
		return "";
	} else {
		$dia =  substr("$data", 0, 2);
		$mes =  substr("$data", 3, 2);
		$ano =  substr("$data", 6, 4);
		
		$diasql = date("Y-m-d", mktime(0,0,0,$mes,$dia,$ano) );
		
		return $diasql;
	}
}

function emFormatoDinheiroSQL($valor) {
	if (empty($valor)) {
		return "";
	} else {
		return str_replace(',','.',$valor);
	}
}
function showMessage($type, $message, $url) {
	if ($type == 1) {
		echo '<script>location.href="'. $url .'"</script>';
	} else {
		echo '
		<!DOCTYPE html>
		<html lang="en">

		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			
			<title>:: ENGLISHWARE ::</title>
		</head>
		
		<body>
			<h1>Erro</h1>
			
			<div>O seguinte erro ocorreu: </div>
			<br />
			<div><b>' . $message . '</b></div>
			<br />
			<div>Anote estas informações e entre em contato com o administrador do sistema</div>
			<br />
			<a href="' . $url . '">[Retornar]</a>
		</body>
		
		</html>
		';
	}
}

function pageopen($destacar = "") {
	echo '
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<title>:: ENGLISHWARE ::</title>
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/r/bs-3.3.5/jq-2.1.4,dt-1.10.8/datatables.min.css"/>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="https://cdn.datatables.net/r/bs-3.3.5/jqc-1.11.3,dt-1.10.8/datatables.min.js"></script>
	</head>

	<body>
		
		<nav class="navbar navbar-default">
		  <div class="container-fluid">
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span> 
			  </button>
			  <a class="navbar-brand" href="index.php">Englishware</a>
			</div>
			<div class="collapse navbar-collapse" id="myNavbar">
			  <ul class="nav navbar-nav">
				<li id="alunos"><a href="view_alunos.php">Gerenciar Alunos</a></li>
				<li id="periodos"><a href="view_periodos.php">Gerenciar Períodos</a></li>
				<li id="reenviar"><a href="view_reenviar.php">Reenviar E-mails</a></li>
			  </ul>
			</div>
		  </div>
		</nav>
		
		<div class="container">
	';
	
	if (!empty($destacar)) {
		echo '
		<script>
		  var element = document.getElementById("' . $destacar . '");
          element.className += " " + "active";
		</script>
		';
	}
}

function pageclose() {
	echo '
	    </div>
	  </body>
	</html>
	';
}

function removerFormatacaoNumero( $strNumero )
{
	$strNumero = trim( str_replace( "R$", null, $strNumero ) );

	$vetVirgula = explode( ",", $strNumero );
	if ( count( $vetVirgula ) == 1 )
	{
		$acentos = array(".");
		$resultado = str_replace( $acentos, "", $strNumero );
		return $resultado;
	}
	else if ( count( $vetVirgula ) != 2 )
	{
		return $strNumero;
	}

	$strNumero = $vetVirgula[0];
	$strDecimal = mb_substr( $vetVirgula[1], 0, 2 );

	$acentos = array(".");
	$resultado = str_replace( $acentos, "", $strNumero );
	$resultado = $resultado . "." . $strDecimal;
	
	return $resultado;
	
}

function valorPorExtenso( $valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false )
{
	$valor = removerFormatacaoNumero( $valor );

	$singular = null;
	$plural = null;

	if ( $bolExibirMoeda )
	{
		$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
		$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
	}
	else
	{
		$singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
		$plural = array("", "", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
	}

	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");

	if ( $bolPalavraFeminina )
	{
		if ($valor == 1) 
		{
			$u = array("", "uma", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
		}
		else 
		{
			$u = array("", "um", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
		}
		
		$c = array("", "cem", "duzentas", "trezentas", "quatrocentas","quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");
	}

	$z = 0;

	$valor = number_format( $valor, 2, ".", "." );
	$inteiro = explode( ".", $valor );

	for ( $i = 0; $i < count( $inteiro ); $i++ ) 
	{
		for ( $ii = mb_strlen( $inteiro[$i] ); $ii < 3; $ii++ ) 
		{
			$inteiro[$i] = "0" . $inteiro[$i];
		}
	}

	// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
	$rt = null;
	$fim = count( $inteiro ) - ($inteiro[count( $inteiro ) - 1] > 0 ? 1 : 2);
	for ( $i = 0; $i < count( $inteiro ); $i++ )
	{
		$valor = $inteiro[$i];
		$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
		$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
		$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

		$r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
		$t = count( $inteiro ) - 1 - $i;
		$r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
		if ( $valor == "000")
			$z++;
		elseif ( $z > 0 )
			$z--;
			
		if ( ($t == 1) && ($z > 0) && ($inteiro[0] > 0) )
			$r .= ( ($z > 1) ? " de " : "") . $plural[$t];
			
		if ( $r )
			$rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
	}

	$rt = mb_substr( $rt, 1 );

	return($rt ? trim( $rt ) : "zero");
}