<?php header('Content-Type: text/html; charset=utf-8');
ini_set('default_charset', 'UTF-8');ini_set('display_errors', 'Off');
require_once 'utils.php';require_once 'utils_db.php';startDatabase();$per_status = "1";if ($_GET) {    $per_status = addslashes(trim($_GET["p_per_status"]));}$periodos = getTodosOsPeriodos($per_status);pageopen("periodos");?><h2>Cadastro de Períodos</h2><br/><ul class="nav nav-tabs">	<li <?php echo $per_status == '1' ? 'class="active"' : '' ?>><a href="view_periodos.php?p_per_status=1">Ativos</a></li>	<li <?php echo $per_status == '9' ? 'class="active"' : '' ?>><a href="view_periodos.php?p_per_status=9">Inativos</a></li>	<li <?php echo $per_status == ''  ? 'class="active"' : '' ?>><a href="view_periodos.php?p_per_status=">Todos</a></li></ul><br /><table class="table table-striped" id="myTable">	<thead>		<tr>			<th class="col-md-4">Descrição</th>			<th>Data Início</th>			<th>Data Fim</th>			<th>Status</th>			<th class="col-md-2">Ações</th>		</tr>	</thead>	<tbody>	<?php while ($periodo = mysql_fetch_assoc($periodos)) { ?>	  <tr>		<td><a href="view_periodo.php?p_per_codigo=<?php echo $periodo['per_codigo']?>" title="Editar Período"><?php echo $periodo['per_descricao']?></a></td>		<td><?php echo emFormatoData($periodo['per_data_ini'])?></td>		<td><?php echo emFormatoData($periodo['per_data_fim'])?></td>		<td><?php echo ($periodo['per_status'] == 9 ? "Inativo" : "Ativo")?></td>		<td>			<a class="btn btn-default" href="servlet_dias.php?p_per_codigo=<?php echo $periodo['per_codigo']?>" title="Calcular Aulas" onclick="return calcular()"><i class="fas fa-calculator"></i></a>			<a class="btn btn-default" href="view_aulas.php?p_per_codigo=<?php echo $periodo['per_codigo']?>" title="Gerenciar Aulas"><i class="fas fa-user"></i></a>			<a class="btn btn-default" href="servlet_email.php?p_per_codigo=<?php echo $periodo['per_codigo']?>" title="Enviar Emails" onclick="return enviar()"><i class="fas fa-envelope"></i></a>			<a class="btn btn-default" target="_blank" href="view_relatorio.php?p_per_codigo=<?php echo $periodo['per_codigo']?>" title="Gerar Relatório"><i class="fas fa-file-pdf"></i></a>		</td>	  </tr>	<?php } ?>	</tbody></table><a href="view_periodo.php" class="btn btn-primary">Incluir Período</a><script>$(document).ready(function(){	$('#myTable').DataTable( {		"order": [[0, "desc"]],		"language": {			"sEmptyTable": "Nenhum registro encontrado",			"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",			"sInfoEmpty": "Mostrando 0 até 0 de 0 registros",			"sInfoFiltered": "(Filtrados de _MAX_ registros)",			"sInfoPostFix": "",			"sInfoThousands": ".",			"sLengthMenu": "Exibir _MENU_ registros",			"sLoadingRecords": "Carregando...",			"sProcessing": "Processando...",			"sZeroRecords": "Nenhum registro encontrado",			"sSearch": "Pesquisar por",			"oPaginate": { "sNext": ">", "sPrevious": "<", "sFirst": "<<", "sLast": ">>" },			"oAria": { "sSortAscending": ": Ordem crescente", "sSortDescending": ": Ordem decrescente" }		}	} );});function calcular() {	if (confirm("Você tem certeza que deseja calcular os dias para todos os alunos do período?")) {		return true;	}	return false;}function enviar() {	if (confirm("Você tem certeza que deseja enviar os e-mails para todos os alunos do período?")) {		return true;	}	return false;}</script><?php pageclose() ?>