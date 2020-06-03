<?php
header("Content-Type: text/html; charset=utf-8");

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

//require 'vendor/autoload.php';

function sendmail($to, $subject, $message) {
    /*
	$sendgrid = new SendGrid("englishware", "englishware123");
    $email    = new SendGrid\Email();
    
	$email->addTo($to)
	      ->addTo("fabibr@gmail.com")
          ->setFrom("fabibr@gmail.com")
          ->setSubject($subject)
          ->setHtml($message);
	
	$sendgrid->send($email);
	*/
	
	$from = "fabibr@gmail.com";
	
	$headers  = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= "From: " . $from . "\r\n";
	
	mail($to  , $subject, $message, $headers);
	mail($from, $subject, $message, $headers);
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
			<br><strong>Banco Itaú (341)</strong>
			<br>Agência: 3249
			<br>C/C: 22.536-2
			<br>
			<br><strong>Banco Santander (033)</strong>
			<br>Agência: 0189
			<br>C/C: 01.030573.1
			<br>
			<br><strong>Banco Nubank (260)</strong>
			<br>Agência: 0001
			<br>C/C: 1275194-6
			<br>
			<br><strong>Banco Inter (077)</strong>
			<br>Agência: 0001-9
			<br>C/C: 979926-5
			<br>
			<br>CPF: 699.616.030.87
			<br>
			<br><b>Se desejar, solicitar pagamento via boleto.</b>
			<br><b>Para formas de pagamento com desconto, que não sejam depósito ou transferência, pagamento até o dia 08 de cada mês.</b>
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
			<br><strong>Banco Itaú (341)</strong>
			<br>Agência: 3249
			<br>C/C: 22.536-2
			<br>
			<br><strong>Banco Santander (033)</strong>
			<br>Agência: 0189
			<br>C/C: 01.030573.1
			<br>
			<br><strong>Banco Nubank (260)</strong>
			<br>Agência: 0001
			<br>C/C: 1275194-6
			<br>
			<br><strong>Banco Inter (077)</strong>
			<br>Agência: 0001-9
			<br>C/C: 979926-5
			<br>
			<br>CPF: 699.616.030.87
			<br>
			<br><b>Se desejar, solicitar pagamento via boleto.</b>
			<br><b>Para formas de pagamento com desconto, que não sejam depósito ou transferência, pagamento até o dia 08 de cada mês.</b>
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