<?php
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'utils.php';
require_once 'utils_db.php';

startDatabase();

if ($_REQUEST) {
    $filtro_periodo = addslashes(trim($_REQUEST["p_per_codigo"]));
    $filtro_aluno = addslashes(trim($_REQUEST["p_alu_codigo"]));
	$filtro_status = addslashes(trim($_REQUEST["p_aul_status"]));
	$filtro_dataini = addslashes(trim($_REQUEST["p_data_ini"]));
	$filtro_datafim = addslashes(trim($_REQUEST["p_data_fim"]));
}

$aulas = getAulasPorFiltro($filtro_periodo, $filtro_aluno, $filtro_status, $filtro_dataini, $filtro_datafim);

$periodos = getTodosOsPeriodos();
$alunos = getTodosOsAlunos();

pageopen("periodos")
?>

<h2>Cadastro de Aulas</h2>
<br/>

<style>
#filtros {
	background: #DEDEDE;
	padding: 20px;
}
</style>

<form action="view_aulas.php" method="post" id="filtros" name="filtros" enctype="multipart/form-data">
	<div class="form-group">
		<div class="row">
			<div class="col-xs-6">
				<label>Período:</label>
				<select class="form-control" name="p_per_codigo" onchange="filtrar()">
					<option value="">Selecione</option>
					<?php while ($periodo = mysql_fetch_assoc($periodos)) { ?>
						<option value="<?php echo $periodo["per_codigo"]?>" <?php echo ($filtro_periodo == $periodo["per_codigo"] ? "selected" : "")?>><?php echo $periodo["per_descricao"]?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-xs-3">
				<label>Data Inicial:</label>
				<input type="text" class="form-control" name="p_data_ini" value="<?php echo $filtro_dataini ?>" onblur="filtrar()"/>
			</div>
			<div class="col-xs-3">
				<label>Data Final:</label>
				<input type="text" class="form-control" name="p_data_fim" value="<?php echo $filtro_datafim ?>" onblur="filtrar()"/>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-xs-6">
				<label>Aluno:</label>
				<select class="form-control" name="p_alu_codigo" onchange="filtrar()">
					<option value="">Selecione</option>
					<?php while ($aluno = mysql_fetch_assoc($alunos)) { ?>
						<option value="<?php echo $aluno["alu_codigo"]?>" <?php echo ($filtro_aluno == $aluno["alu_codigo"] ? "selected" : "")?>><?php echo $aluno["alu_nome"]?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-xs-6">
				<label>Status:</label>
				<select class="form-control" name="p_aul_status" onchange="filtrar()">
					<option value="">Selecione</option>
					<option value="1" <?php echo ($filtro_status == "1" ? "selected" : "")?>>Confirmada</option>
					<option value="9" <?php echo ($filtro_status == "9" ? "selected" : "")?>>Desmarcada</option>
				</select>
			</div>
		</div>
	</div>
</form>

<br/>

<form action="servlet_trocar.php" method="post" id="inverter" name="inverter" enctype="multipart/form-data">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Data</th>
				<th>Hora</th>
				<th>Valor</th>
				<th>Período</th>
				<th>Aluno</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php while ($aula = mysql_fetch_assoc($aulas)) { ?>
			<?php $i++ ?>
			<input type="hidden" name="p_aul_codigo[<?php echo $i ?>]" value="<?php echo $aula['aul_codigo'] ?>"/>
			<tr>
				<td><a href="view_aula.php?p_aul_codigo=<?php echo $aula['aul_codigo']?>" title="Editar Aula"><?php echo emFormatoData($aula['aul_data_aula']) ?></a></td>
				<td><a href="view_aula.php?p_aul_codigo=<?php echo $aula['aul_codigo']?>" title="Editar Aula"><?php echo emFormatoHora($aula['aul_hora_ini']) . " às " . emFormatoHora($aula['aul_hora_fim']) ?></a></td>
				<td><?php echo emFormatoDinheiro($aula['aul_preco']) ?></td>
				<td><?php echo $aula['per_descricao'] ?></td>
				<td><?php echo $aula['alu_nome'] ?></td>
				<td>
					<select class="p_aul_status" name="p_aul_status[<?php echo $i ?>]">
						<option value="1" <?php echo ($aula['aul_status'] == "1" ? "selected" : "")?>>Confirmada</option>
						<option value="9" <?php echo ($aula['aul_status'] == "9" ? "selected" : "")?>>Desmarcada</option>
					</select>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
  <?php
  $linkIncluirAulas = "view_aula.php?p_aul_status=1";
  if (!empty($filtro_periodo)) {
    $linkIncluirAulas .= "&p_per_codigo=" . $filtro_periodo;
  }
  if (!empty($filtro_aluno)) {
    $linkIncluirAulas .= "&p_alu_codigo=" . $filtro_aluno;
  }
  ?>
  <br />
  <a href="<?php echo $linkIncluirAulas ?>" class="btn btn-primary">Incluir Aula</a>
	<button type="button" class="btn btn-primary" onclick="trocar()">Inverter Geral</button>
	<button type="submit" class="btn btn-primary">Salvar Aulas</button>
	<?php
	if (!empty($filtro_periodo) && !empty($filtro_aluno)) {
		if (empty($filtro_dataini) && empty($filtro_datafim) && empty($filtro_status)) {
			echo '<a class="btn btn-primary" href="servlet_email.php?p_per_codigo=' . $filtro_periodo . '&p_alu_codigo=' . $filtro_aluno . '" onclick="return enviar()">Enviar E-mails</a> ';
		}
	}
	?>
</form>

<script>
$(document).ready(function(){
	$('#myTable').DataTable( {
		"order": [[0, "asc"],[1, "asc"]],
		"paging": false,
		"language": {
			"sEmptyTable": "Nenhum registro encontrado",
			"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
			"sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
			"sInfoFiltered": "(Filtrados de _MAX_ registros)",
			"sInfoPostFix": "",
			"sInfoThousands": ".",
			"sLengthMenu": "Exibir _MENU_ registros",
			"sLoadingRecords": "Carregando...",
			"sProcessing": "Processando...",
			"sZeroRecords": "Nenhum registro encontrado",
			"sSearch": "Pesquisar por",
			"oPaginate": { "sNext": ">", "sPrevious": "<", "sFirst": "<<", "sLast": ">>" },
			"oAria": { "sSortAscending": ": Ordem crescente", "sSortDescending": ": Ordem decrescente" }
		}
	} );
});
function filtrar() {
	document.filtros.submit();
}
function trocar() {
	var elements = document.getElementsByClassName("p_aul_status");
	for(var i = 0; i < elements.length; i++) {
		elements[i].value = elements[i].value == 1 ? 9 : 1;
	}
}
function enviar() {
	if (confirm("Você tem certeza que deseja enviar os e-mails para este aluno?")) {
		return true;
	}
	return false;
}
</script>

<?php pageclose() ?>
