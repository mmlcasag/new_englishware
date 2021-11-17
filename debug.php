<?php
header("Content-Type: text/html; charset=utf-8");

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require 'vendor/autoload.php';

function sendmail($to, $subject, $message) {
    $sendgrid = new SendGrid("mmlcasag", "q1Q!w2W@");
    $email    = new SendGrid\Email();
    
	$email->addTo($to)
	      ->addTo("fabibranchini@gmail.com")
          ->setFrom("fabibranchini@gmail.com")
          ->setSubject($subject)
          ->setHtml($message);
	
    $sendgrid->send($email);
}

function getEmailAulasDoPeriodo() {
	return '
	<!DOCTYPE html>
	<html lang="en">
	
	<head>
		<title>:: ENGLISHWARE ::</title>
	</head>
	
	<body>
		
		<h3>Demonstrativo das Aulas do Período #PERIODO#</h3> 
		<p align="left">
			#NOME#,
			<br>
			<br>Confirmando as aulas para o período #PERIODO#:
			#CONTEUDO#
			<br>
			<br>Abaixo seguem os dados bancários:
			<br>
			<br><strong>Banco Itaú</strong>
			<br>Agência: 3249
			<br>C.Corrente: 22.536-2
			<br>CPF: 699.616.030.87
			<br>
			<br><strong>Banco Santander</strong>
			<br>Agência: 0189
			<br>C.Corrente: 01.030573.1
			<br>CPF: 699.616.030.87
			<br>
			<br>Esta mensagem foi gerada automaticamente. Favor não responder este e-mail.
			<br>
			<br>Att,
			<br>
			<br><strong>Fabiana Branchini</strong>
			<br>Celular: (54) 99974-6881
			<br>Residencial: (54) 3201-1616
			<br>E-mail: fabibr@gmail.com
			<br>
			<br>
		</p>
		
	</body>
	
	</html>
	';
}

function getEmailReenvio() {
	return '
	<!DOCTYPE html>
	<html lang="en">
	
	<head>
		<title>:: ENGLISHWARE ::</title>
	</head>

	<body>
		
		<h3>#NOME#,</h3>
		<p align="left">
			<br>Este é apenas um lembrete de que o pagamento do mês ainda não foi efetuado.
			<br>Abaixo seguem os dados bancários:
			<br>
			<br><strong>Banco Itaú</strong>
			<br>Agência: 3249
			<br>C.Corrente: 22.536-2
			<br>CPF: 699.616.030.87
			<br>
			<br><strong>Banco Santander</strong>
			<br>Agência: 0189
			<br>C.Corrente: 01.030573.1
			<br>CPF: 699.616.030.87
			<br>
			<br>Esta mensagem foi gerada automaticamente. Favor não responder este e-mail.
			<br>
			<br>Att,
			<br>
			<br><strong>Fabiana Branchini</strong>
			<br>Celular: (54) 99974-6881
			<br>Residencial: (54) 3201-1616
			<br>E-mail: fabibr@gmail.com
			<br>
			<br>
		</p>
		
	</body>
	
	</html>
	';
}