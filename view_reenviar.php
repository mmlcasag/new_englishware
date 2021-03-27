<?php
header("Content-Type: text/html; charset=utf-8");

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'utils.php';
require_once 'utils_db.php';

startDatabase();

$periodos = getTodosOsPeriodos(1);
$alunos = getTodosOsAlunos(1);

pageopen("reenviar");
?>

<form action="servlet_reenvio.php" method="post" onsubmit="return reenviar()">
	<h2>Reenviar E-mails</h2>
	<br/>
	
	<div class="form-group">
		<div class="row">
			<div class="col-xs-12">
				<label>Período:</label>
				<select class="form-control" name="p_per_codigo">
					<option value="">Selecione</option>
					<?php while ($periodo = mysql_fetch_assoc($periodos)) { ?>
						<option value="<?php echo $periodo["per_codigo"]?>" <?php echo ($aula['per_codigo'] == $periodo["per_codigo"] ? "selected" : "")?>><?php echo $periodo["per_descricao"]?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="row">
			<div class="col-xs-12">
				<label>Alunos:</label>
				<select multiple name="p_arr_alunos[]" class="form-control" style="height:350px">
				<?php while ($aluno = mysql_fetch_assoc($alunos)) { ?>
					<option value="<?php echo $aluno['alu_codigo']?>"><?php echo $aluno['alu_nome']?></option>
				<?php } ?>
				</select>
			</div>
		</div>
	</div>
	
	<button type="submit" class="btn btn-primary">Reenviar E-mails</button>
</form>

<script>
function reenviar() {
	if (confirm("Você tem certeza que deseja reenviar os e-mails para todos os alunos selecionados?")) {
		document.main.submit();
	}
}
</script>

<?php pageclose() ?>
