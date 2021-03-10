<?php
header("Content-Type: text/html; charset=utf-8");

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'utils.php';
require_once 'utils_db.php';

startDatabase();

$alunos = getTodosOsAlunos("1");

pageopen("reenviar");
?>

<form action="servlet_reenvio.php" method="post" onsubmit="return reenviar()">
	<h2>Reenviar E-mails</h2>
	<br/>
	
	<div class="form-group">
		<label>Alunos:</label>
		<select multiple name="p_arr_alunos[]" class="form-control" style="height:350px">
		<?php while ($aluno = mysql_fetch_assoc($alunos)) { ?>
			<option value="<?php echo $aluno['alu_codigo']?>"><?php echo $aluno['alu_nome']?></option>
		<?php } ?>
		</select>
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