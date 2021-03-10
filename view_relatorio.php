<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'utils.php';
require_once 'utils_db.php';

startDatabase();

$per_codigo = "";
if ($_GET) {
    $per_codigo = addslashes(trim($_GET["p_per_codigo"]));
}

$periodo   = getPeriodo($per_codigo);
$registros = getRelatorioGeralPeriodo($per_codigo);

$cont_total_aulas   = 0;
$cont_vlr_acrescimo = 0;
$cont_vlr_desconto  = 0;
$cont_total_geral   = 0;
?>

<html>

<head>
	<title>Englishware :: Relatório Geral do Período :: <?php echo $periodo->per_descricao ?></title>
</head>

<body>
	
	<table align="center" border="1" cellspacing="1" cellpadding="4" width="100%">
		<thead>
			<tr>
				<th align="center" colspan="7">Relatório Geral do Período :: <?php echo $periodo->per_descricao ?></th>
			</tr>
			<tr>
				<th align="left" rowspan="2">Aluno</th>
				<th align="center" rowspan="2">Total Aulas</th>
				<th align="center" colspan="2">Acréscimo</th>
				<th align="center" colspan="2">Desconto</th>
				<th align="center" rowspan="2">Total Geral</th>
			</tr>
			<tr>
				<th align="center">R$</th>
				<th align="center">%</th>
				<th align="center">R$</th>
				<th align="center">%</th>
			</tr>
		</thead>
		<tbody>
			<?php while ($registro = mysql_fetch_assoc($registros)) { ?>
			<?php
			$cont_total_aulas   += $registro['total_aulas'];
			$cont_vlr_acrescimo += $registro['pal_vlr_acrescimo'];
			$cont_vlr_desconto  += $registro['pal_vlr_desconto'];
			$cont_total_geral   += $registro['total_geral'];
			?>
			<tr>
				<td align="left"><?php echo $registro['alu_nome'] ?></td>
				<td align="right"><?php echo ( $registro['total_aulas']       > 0 ? emFormatoDinheiro($registro['total_aulas'])       : '' ) ?></td>
				<td align="right"><?php echo ( $registro['pal_vlr_acrescimo'] > 0 ? emFormatoDinheiro($registro['pal_vlr_acrescimo']) : '' ) ?></td>
				<td align="right"><?php echo ( $registro['pal_per_acrescimo'] > 0 ? emFormatoDinheiro($registro['pal_per_acrescimo']) : '' ) ?></td>
				<td align="right"><?php echo ( $registro['pal_vlr_desconto']  > 0 ? emFormatoDinheiro($registro['pal_vlr_desconto'])  : '' ) ?></td>
				<td align="right"><?php echo ( $registro['pal_per_desconto']  > 0 ? emFormatoDinheiro($registro['pal_per_desconto'])  : '' ) ?></td>
				<td align="right"><?php echo ( $registro['total_geral']       > 0 ? emFormatoDinheiro($registro['total_geral'])       : '' ) ?></td>
			</tr>
			<?php } ?>
			<tr>
				<th align="left" rowspan="2"><br /></th>
				<th align="right"><?php echo emFormatoDinheiro($cont_total_aulas) ?></th>
				<th align="right"><?php echo emFormatoDinheiro($cont_vlr_acrescimo) ?></th>
				<th align="right"><br /></th>
				<th align="right"><?php echo emFormatoDinheiro($cont_vlr_desconto) ?></th>
				<th align="right"><br /></th>
				<th align="right"><?php echo emFormatoDinheiro($cont_total_geral) ?></th>
			</tr>
		</tbody>
	</table>
	
</body>
</html>

<script>window.print()</script>