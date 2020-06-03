<?php
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'utils.php';
require_once 'utils_db.php';

startDatabase();

$aul_codigo = "";
if ($_GET) {
    $aul_codigo = addslashes(trim($_GET["p_aul_codigo"]));
    $aul_status = addslashes(trim($_GET["p_aul_status"]));
    $per_codigo = addslashes(trim($_GET["p_per_codigo"]));
    $alu_codigo = addslashes(trim($_GET["p_alu_codigo"]));
}

if (empty($aul_codigo)) {
  $periodos = getTodosOsPeriodos(1);
  $alunos   = getTodosOsAlunos(1);
  $aula     = getAula($aul_codigo);
  
  $aula["per_codigo"] = $per_codigo;
  $aula["alu_codigo"] = $alu_codigo;
  $aula["aul_status"] = $aul_status;
} else {
  $periodos = getTodosOsPeriodos();
  $alunos   = getTodosOsAlunos();
  $aula     = getAula($aul_codigo);
}

pageopen("periodos");
?>

<form action="servlet_aula.php" method="post" name="main" enctype="multipart/form-data">
	<h2>Cadastro de Aulas</h2>
	<br/>

	<input type="hidden" name="p_aul_codigo" value="<?php echo $aul_codigo ?>"/>
	<div class="form-group">
		<div class="row">
			<div class="col-xs-3">
				<label>Dia:</label>
				<input type="text" class="form-control" name="p_aul_data_aula" value="<?php echo emFormatoData($aula['aul_data_aula']) ?>"/>
			</div>
			<div class="col-xs-3">
				<label>Início:</label>
				<input type="text" class="form-control" name="p_aul_hora_ini" value="<?php echo emFormatoHora($aula['aul_hora_ini']) ?>"/>
			</div>
			<div class="col-xs-3">
				<label>Fim:</label>
				<input type="text" class="form-control" name="p_aul_hora_fim" value="<?php echo emFormatoHora($aula['aul_hora_fim']) ?>"/>
			</div>
			<div class="col-xs-3">
				<label>Preço:</label>
				<input type="text" class="form-control" name="p_aul_preco" value="<?php echo emFormatoDinheiro($aula['aul_preco']) ?>"/>
			</div>
		</div>
	</div>
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
			<div class="col-xs-9">
				<label>Aluno:</label>
				<select class="form-control" name="p_alu_codigo">
					<option value="">Selecione</option>
					<?php while ($aluno = mysql_fetch_assoc($alunos)) { ?>
						<option value="<?php echo $aluno["alu_codigo"]?>" <?php echo ($aula['alu_codigo'] == $aluno["alu_codigo"] ? "selected" : "")?>><?php echo $aluno["alu_nome"]?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-xs-3">
				<label>Status:</label>
				<select class="form-control" name="p_aul_status">
					<option value="1" <?php echo ($aula['aul_status'] == "1" ? "selected" : "")?>>Confirmada</option>
					<option value="9" <?php echo ($aula['aul_status'] == "9" ? "selected" : "")?>>Desmarcada</option>
				</select>
			</div>
		</div>
	</div>
	<button type="submit" class="btn btn-primary">Salvar Alterações</button>
</form>

<?php pageclose() ?>
