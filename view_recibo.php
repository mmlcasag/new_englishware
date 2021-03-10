<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'utils.php';
require_once 'utils_db.php';

startDatabase();

if ($_REQUEST) {
    $per_codigo = addslashes(trim($_REQUEST["p_per_codigo"]));
	$alu_codigo = addslashes(trim($_REQUEST["p_alu_codigo"]));
}

$periodo = getPeriodo($per_codigo);
$aluno = getAluno($alu_codigo);
$extra = getPeriodoAluno($per_codigo, $alu_codigo);
$aulas = getAulasPorFiltro($per_codigo, $alu_codigo, 1);

$qtdeAulas = 0;
$totalAulas = 0;

while ($aula = mysql_fetch_assoc($aulas)) {
	$qtdeAulas += 1;
	$totalAulas += $aula['aul_preco'];
}
if ($extra->pal_per_acrescimo > 0) {
	$percentualAcrescimo = ($totalAulas * $extra->pal_per_acrescimo / 100);
}
if ($extra->pal_per_desconto > 0) {
	$percentualDesconto = ($totalAulas * $extra->pal_per_desconto / 100);
}
if ($extra->pal_vlr_acrescimo > 0) {
	$valorAcrescimo = $extra->pal_vlr_acrescimo;
}
if ($extra->pal_vlr_desconto > 0) {
	$valorDesconto = $extra->pal_vlr_desconto;
}
$valorTotal = $totalAulas + $percentualAcrescimo + $valorAcrescimo - $valorDesconto;
$valorPromocional = $totalAulas + $percentualAcrescimo - $percentualDesconto + $valorAcrescimo - $valorDesconto;

if ($_POST) {
	$etapa = addslashes(trim($_POST["etapa"]));
	$valor = addslashes(trim($_POST["valor"]));
	$obs   = addslashes(trim($_POST["obs"]));
	
	$valor = emFormatoDinheiroSQL($valor);
} else {
	$etapa = 1;
	$valor = $valorPromocional;
	$obs   = "";
	
	pageopen("periodos");
}
?>

<html>

<head>
	<title>Englishware :: Recibo do Período <?php echo $periodo->per_descricao ?> :: <?php echo $aluno->alu_nome ?></title>
</head>

<body>
	<?php if ($etapa == 1) {?>
		<form method="post">
			<h2>Geração de Recibo</h2>
			<br/>
			
			<input type="hidden" name="p_per_codigo" value="<?php echo $per_codigo ?>" />
			<input type="hidden" name="p_alu_codigo" value="<?php echo $alu_codigo ?>" />
			<input type="hidden" name="etapa" value="2" />
			
			<div class="form-group">
				<div class="row">
					<div class="col-xs-12">
						<label>Valor:</label>
						<input type="text" class="form-control" name="valor" value="<?php echo emFormatoDinheiro($valor); ?>"/>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<div class="row">
					<div class="col-xs-12">
						<label>Observação:</label>
						<textarea class="form-control" name="obs" rows="5" cols="50"><?php echo $obs ?></textarea>
					</div>
				</div>
			</div>
			
			<button type="submit" class="btn btn-primary">Gerar Recibo</button>
		</form>
	<?php } else { ?>
		<style>
			* {
				line-height: 25px;
			}
		</style>
		
		<table border="1" align="center" cellspacing="1" cellpadding="4" width="800">
			<tr>
				<td>
					<table border="0" align="center" width="100%">
						<tr>
							<td align="left"><font size="7">RECIBO</font></td>
							<td align="right"><font size="5"><strong>Nº</strong></font></td>
							<td align="center" bgcolor="#DEDEDE" width="100px"><font size="5"><?php echo ' ' ?></font></td>
							<td align="right"><font size="5"><strong>VALOR</strong></font></td>
							<td align="center" bgcolor="#DEDEDE" width="150px"><font size="5"><?php echo 'R$ ' . emFormatoDinheiro($valor) ?></font></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table border="0" align="center" width="100%">
						<tr>
							<td align="left" width="15%"><strong>Recebi de</strong></td>
							<td align="left" width="85%" bgcolor="#DEDEDE" style="border-bottom: 1px solid #333"><?php echo $aluno->alu_nome ?></td>
						</tr>
						<tr>
							<td align="left" width="15%"><strong>A quantia de</strong></td>
							<td align="left" width="85%" bgcolor="#DEDEDE" style="border-bottom: 1px solid #333"><?php echo 'R$ ' . emFormatoDinheiro($valor) . ' (' . valorPorExtenso(emFormatoDinheiro($valor), true, false) . ')' ?></td>
						</tr>
						<tr>
							<td align="left" width="15%"><strong>Referente à</strong></td>
							<td align="left" width="85%" bgcolor="#DEDEDE" style="border-bottom: 1px solid #333"><?php echo 'Aulas de inglês no período ' . $periodo->per_descricao ?></td>
						</tr>
						<tr>
							<td align="left" width="15%"><strong>Observações</strong></td>
							<td align="left" width="85%" bgcolor="#DEDEDE" style="border-bottom: 1px solid #333"><?php echo $obs ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table border="0" align="center" width="100%">
						<tr>
							<td align="left" bgcolor="#DEDEDE" style="border-bottom: 1px solid #333"><?php echo 'Caxias do Sul' ?></td>
							<td align="center" width="10px"><?php echo ' , ' ?></td>
							<td align="left" width="10%" bgcolor="#DEDEDE" style="border-bottom: 1px solid #333"><?php echo date("d") ?></td>
							<td align="center" width="10px"><?php echo ' de ' ?></td>
							<td align="left" width="25%" bgcolor="#DEDEDE" style="border-bottom: 1px solid #333"><?php echo emMesExtenso(date("m")) ?></td>
							<td align="center" width="10px"><?php echo ' de ' ?></td>
							<td align="left" width="10%" bgcolor="#DEDEDE" style="border-bottom: 1px solid #333"><?php echo date("Y") ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table border="0" align="center" width="100%">
						<tr>
							<td align="left" width="15%"><strong>Nome</strong></td>
							<td align="left" width="30%" bgcolor="#DEDEDE" style="border-bottom: 1px solid #333"><?php echo 'Fabiana Branchini' ?></td>
						</tr>
						<tr>
							<td align="left" width="15%"><strong>CPNJ</strong></td>
							<td align="left" width="85%" bgcolor="#DEDEDE" style="border-bottom: 1px solid #333"><?php echo '17.555.375/0001.10' ?></td>
						</tr>
						<tr>
							<td align="left" width="15%"><strong>Endereço</strong></td>
							<td align="left" width="85%" bgcolor="#DEDEDE" style="border-bottom: 1px solid #333"><?php echo 'Rua Ângelo Lourenço Tesser, 1258, A1101, Bairro De Lazzer, Caxias do Sul, RS, 95055-100' ?></td>
						</tr>
						<tr>
							<td align="left" width="15%"><strong>Assinatura</strong></td>
							<td align="left" width="85%" style="border-bottom: 1px solid #333"><img src="assinatura.png" width="200" style="position: absolute; z-index:-1;"/><?php echo '<br /><br />' ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	<?php } ?>
</body>
</html>

<?php if ($etapa == 2) { echo '<script>window.print()</script>'; } ?>