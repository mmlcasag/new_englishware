<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'utils.php';
require_once 'utils_db.php';

startDatabase();

if ($_GET) {
    $per_codigo = addslashes(trim($_GET["p_per_codigo"]));
	$alu_codigo = addslashes(trim($_GET["p_alu_codigo"]));
}

$periodo = getPeriodo($per_codigo);
$aluno = getAluno($alu_codigo);
$extra = getPeriodoAluno($per_codigo, $alu_codigo);
$aulas = getAulasPorFiltro($per_codigo, $alu_codigo, 1);

$qtdeAulas = 0;
$totalAulas = 0;
?>

<html>

<head>
	<title>Englishware :: Aulas do Período <?php echo $periodo->per_descricao ?> :: <?php echo $aluno->alu_nome ?></title>
</head>

<body>
	
	<table align="center" border="1" cellspacing="1" cellpadding="4" width="100%">
		<thead>
			<tr>
				<th align="center" colspan="5">Demonstrativo de Aulas</th>
			</tr>
			<tr>
				<th align="center">Período: </th>
				<td align="left" colspan="4"><?php echo $periodo->per_descricao ?></td>
			</tr>
			<tr>
				<th align="center">Aluno: </th>
				<td align="left" colspan="4"><?php echo $aluno->alu_nome ?></td>
			</tr>
			<tr>
				<th align="center" colspan="5"><br/></th>
			</tr>
			<tr>
				<th align="center" width="20%">Data</th>
				<th align="center" width="20%">Dia</th>
				<th align="center" width="20%">Início</th>
				<th align="center" width="20%">Fim</th>
				<th align="right"  width="20%">Valor (R$)</th>
			</tr>
		</thead>
		<tbody>
			<?php while ($aula = mysql_fetch_assoc($aulas)) { ?>
			<?php $qtdeAulas += 1; ?>
			<?php $totalAulas += $aula['aul_preco']; ?>
			<tr>
				<td align="center"><?php echo emFormatoData($aula['aul_data_aula']) ?></td>
				<td align="center"><?php echo emDiaSemana(getDiaSemana(new DateTime($aula['aul_data_aula']))) ?></td>
				<td align="center"><?php echo emFormatoHora($aula['aul_hora_ini']) ?></td>
				<td align="center"><?php echo emFormatoHora($aula['aul_hora_fim']) ?></td>
				<td align="right" ><?php echo emFormatoDinheiro($aula['aul_preco']) ?></td>
			</tr>
			<?php } ?>
			<tr>
				<th align="center" colspan="5"><br/></th>
			</tr>
			<tr>
				<td align="right" colspan="4">Total de aulas no período: </td>
				<th align="right"><?php echo $qtdeAulas ?></th>
			</tr>
			<tr>
				<td align="right" colspan="4">Valor total das aulas no período: </td>
				<th align="right"><?php echo emFormatoDinheiro($totalAulas) ?></th>
			</tr>
			<tr>
				<th align="center" colspan="5"><br/></th>
			</tr>
			<?php
			if ($extra->pal_per_acrescimo > 0) {
				$percentualAcrescimo = ($totalAulas * $extra->pal_per_acrescimo / 100);
			}
			if ($extra->pal_per_desconto > 0) {
				$percentualDesconto = ($totalAulas * $extra->pal_per_desconto / 100);
			}
			if ($extra->pal_vlr_acrescimo > 0) {
				$valorAcrescimo = $extra->pal_vlr_acrescimo;
				echo '<tr><td align="right" colspan="4">Acréscimos lançados no período: </td><th align="right">' . emFormatoDinheiro($valorAcrescimo) . '</th></tr>';
			}
			if ($extra->pal_vlr_desconto > 0) {
				$valorDesconto = $extra->pal_vlr_desconto;
				echo '<tr><td align="right" colspan="4">Descontos lançados no período: </td><th align="right">' . emFormatoDinheiro($valorDesconto) . '</th></tr>';
			}
			?>
			<tr>
				<th align="center" colspan="5"><br/></th>
			</tr>
			<?php
			$valorTotal = $totalAulas + $percentualAcrescimo + $valorAcrescimo - $valorDesconto;
			echo '<tr><th align="right" colspan="4">Total geral: </th><th align="right">' . emFormatoDinheiro($valorTotal) . '</th></tr>';
			
			$valorPromocional = $totalAulas + $percentualAcrescimo - $percentualDesconto + $valorAcrescimo - $valorDesconto;
			if ($valorTotal != $valorPromocional) {
				echo '<tr><th align="right" colspan="4"><font color="red">Valor caso pago até o dia 10: </font></th><th align="right"><font color="red">' . emFormatoDinheiro($valorPromocional) . '</font></th></tr>';
			}
			?>
		</tbody>
	</table>
</body>
</html>

<script>window.print()</script>