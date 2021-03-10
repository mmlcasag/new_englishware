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

$operacao = "I";
if (!empty($per_codigo)) {
	$operacao = "A";
}

$periodo = getPeriodo($per_codigo);

if ($operacao == "I") {
	$alunos = getTodosOsAlunos("1");
} else {
	$alunos = getAlunosDoPeriodo($per_codigo);
    $outros = getAlunosNaoDoPeriodo($per_codigo);
}

$i = 0;

pageopen("periodos");
?>

<form action="servlet_periodo.php" method="post" name="main">
	<h2>Cadastro de Períodos</h2>
	<br/>
	
	<input type="hidden" name="p_per_codigo" value="<?php echo $per_codigo; ?>"/>
	<div class="form-group">
		<div class="row">
			<div class="col-xs-12">
				<label>Descrição:</label>
				<input type="text" class="form-control" name="p_per_descricao" value="<?php echo $periodo->per_descricao; ?>"/>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-xs-3">
				<label>Data Início:</label>
				<input type="text" class="form-control" name="p_per_data_ini" value="<?php echo emFormatoData($periodo->per_data_ini); ?>"/>
			</div>
			<div class="col-xs-3">
				<label>Data Fim:</label>
				<input type="text" class="form-control" name="p_per_data_fim" value="<?php echo emFormatoData($periodo->per_data_fim); ?>"/>
			</div>
			<div class="col-xs-6">
				<label>Status:</label>
				<select class="form-control" name="p_per_status">
					<option value="1" <?php echo ($periodo->per_status == "1" ? "selected" : "")?>>Ativo</option>
					<option value="9" <?php echo ($periodo->per_status == "9" ? "selected" : "")?>>Inativo</option>
				</select>
			</div>
		</div>
	</div>
	
	<br/>
	<h2>Alunos do Período</h2>
	<br/>
	
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="col-md-3">Aluno</th>
				<th>Acréscimo R$</th>
				<th>Acréscimo %</th>
				<th>Desconto R$</th>
				<th>Desconto %</th>
				<th class="col-md-3">Ações</th>
			</tr>
		</thead>
		<tbody>
			<?php
			while ($aluno = mysql_fetch_assoc($alunos)) {
				$i++;
				$periodoAluno = getPeriodoAluno($per_codigo, $aluno['alu_codigo']);
				
				if ($operacao == "I") {
					$periodoAluno->pal_vlr_acrescimo = emFormatoDinheiro(0);
					$periodoAluno->pal_per_acrescimo = emFormatoDinheiro(0);
					$periodoAluno->pal_vlr_desconto  = emFormatoDinheiro(0);
					$periodoAluno->pal_per_desconto  = emFormatoDinheiro(5);
				}
				
				echo '<input type="hidden" name="p_alu_codigo[' . $i . ']" value="' . $aluno['alu_codigo'] . '" />';
				echo '<tr>';
				echo '	<td ><a href="view_aulas.php?p_per_codigo=' . $per_codigo . '&p_alu_codigo=' . $aluno['alu_codigo'] . '">' . $aluno['alu_nome'] . '</a></td> ';
				echo '	<td class="text-center"><input type="text" class="form-control" name="p_pal_vlr_acrescimo[' . $aluno['alu_codigo'] . ']" maxlength="5" style="width:100%; text-align:center;" value="' . emFormatoDinheiro($periodoAluno->pal_vlr_acrescimo) . '"/></td> ';
				echo '	<td class="text-center"><input type="text" class="form-control" name="p_pal_per_acrescimo[' . $aluno['alu_codigo'] . ']" maxlength="5" style="width:100%; text-align:center;" value="' . emFormatoDinheiro($periodoAluno->pal_per_acrescimo) . '"/></td> ';
				echo '	<td class="text-center"><input type="text" class="form-control" name="p_pal_vlr_desconto[' . $aluno['alu_codigo'] . ']"  maxlength="5" style="width:100%; text-align:center;" value="' . emFormatoDinheiro($periodoAluno->pal_vlr_desconto)  . '"/></td> ';
				echo '	<td class="text-center"><input type="text" class="form-control" name="p_pal_per_desconto[' . $aluno['alu_codigo'] . ']"  maxlength="5" style="width:100%; text-align:center;" value="' . emFormatoDinheiro($periodoAluno->pal_per_desconto)  . '"/></td> ';
				echo '  <td> ';
				echo '  	<a class="btn btn-default" href="servlet_dias.php?p_per_codigo=' . $per_codigo . '&p_alu_codigo=' . $aluno['alu_codigo'] . '" title="Calcular Aulas" onclick="return calcular()"><i class="fas fa-calculator"></i></a> ';
				echo '  	<a class="btn btn-default" href="view_aulas.php?p_per_codigo=' . $per_codigo . '&p_alu_codigo=' . $aluno['alu_codigo'] . '" title="Gerenciar Aulas"><i class="fas fa-user"></i></a> ';
				echo '  	<a class="btn btn-default" href="servlet_email.php?p_per_codigo=' . $per_codigo . '&p_alu_codigo=' . $aluno['alu_codigo'] . '" title="Enviar Emails" onclick="return enviar()"><i class="fas fa-envelope"></i></a> ';
				echo '      <a class="btn btn-default" target="_blank" href="view_demonstrativo.php?p_per_codigo=' . $per_codigo . '&p_alu_codigo=' . $aluno['alu_codigo'] . '" title="Gerar Demonstrativo"><i class="fas fa-file-pdf"></i></a> ';
				echo '      <a class="btn btn-default" target="_blank" href="view_recibo.php?p_per_codigo=' . $per_codigo . '&p_alu_codigo=' . $aluno['alu_codigo'] . '" title="Gerar Recibo"><i class="fas fa-file-invoice-dollar"></i></a> ';
				echo '  </td> ';
				echo '</tr>';
			}
			?>
		</tbody>
	</table>
	
	<button type="submit" class="btn btn-primary">Salvar Alterações</button>
</form>

<?php if ($operacao == "A") { ?>
	<br/>
	<h2>Incluir Aluno</h2>
	<br/>

	<form action="servlet_peralu.php" method="post" name="peralu" class="form-inline">
		<input type="hidden" name="p_per_codigo" value="<?php echo $per_codigo ?>" />
		<div class="form-group">
			<select class="form-control" name="p_alu_codigo" style="width:300px">
				<option value="">Selecione</option>
				<?php while ($outro = mysql_fetch_assoc($outros)) { ?>
					<option value="<?php echo $outro["alu_codigo"]?>"><?php echo $outro["alu_nome"]?></option>
				<?php } ?>
			</select>
			<button type="submit" class="btn btn-primary">Incluir Aluno</button>
		</div>
	</form>
	
	<br/>
	<br/>
<?php } ?>

<script>
function calcular() {
	if (confirm("Você tem certeza que deseja calcular os dias para este aluno?")) {
		return true;
	}
	return false;
}
function enviar() {
	if (confirm("Você tem certeza que deseja enviar os e-mails para este aluno?")) {
		return true;
	}
	return false;
}
</script>

<?php pageclose() ?>