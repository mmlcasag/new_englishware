<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'On');

require_once 'utils.php';
require_once 'utils_db.php';

startDatabase();

$alu_codigo = "";
if ($_GET) {
    $alu_codigo = addslashes(trim($_GET["p_alu_codigo"]));
}

$aluno = getAluno($alu_codigo);

pageopen("alunos");
?>

<form action="servlet_aluno.php" method="post" name="main">
	<h2>Cadastro de Alunos</h2>
	<br/>
	
	<input type="hidden" name="p_alu_codigo" value="<?php echo $alu_codigo; ?>"/>
	<div class="form-group">
		<div class="row">
			<div class="col-xs-12">
				<label>Nome:</label>
				<input type="text" class="form-control" name="p_alu_nome" value="<?php echo $aluno->alu_nome; ?>"/>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-xs-9">
				<label>E-mail:</label>
				<input type="text" class="form-control" name="p_alu_email" value="<?php echo $aluno->alu_email; ?>"/>
			</div>
			<div class="col-xs-3">
				<label>Status:</label>
				<select class="form-control" name="p_alu_status">
					<option value="1" <?php echo ($aluno->alu_status == "1" ? "selected" : "")?>>Ativo</option>
					<option value="9" <?php echo ($aluno->alu_status == "9" ? "selected" : "")?>>Inativo</option>
				</select>
			</div>
		</div>
	</div>
	
	<br/>
	<h2>Aulas do Aluno</h2>
	<br/>
	
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="col-md-6">Dia da Semana</th>
				<th class="col-md-2">Início</th>
				<th class="col-md-2">Término</th>
				<th class="col-md-2">Preço</th>
			</tr>
		</thead>
		<tbody>
			<?php
			for ($i = 1; $i <= 7; $i++) {
				// busca dados da aula no dia
				$aulas = getDiaDeAulaDoAluno($alu_codigo, $i);
				
				echo '<tr>';
				echo '	<td><input type="hidden" name="p_dia_dia_semana[' . $i . ']" value="' . $i . '"/><font face="tahoma" size="2">' . emDiaSemana($i) . '</font></td> ';
				echo '	<td><input type="text" class="form-control" name="p_dia_hora_ini[' . $i . ']" maxlength="5" style="width:100%; text-align:center;" value="' . emFormatoHora($aulas['dia_hora_ini']) . '"/></td> ';
				echo '	<td><input type="text" class="form-control" name="p_dia_hora_fim[' . $i . ']" maxlength="5" style="width:100%; text-align:center;" value="' . emFormatoHora($aulas['dia_hora_fim']) . '"/></td> ';
				echo '	<td><input type="text" class="form-control" name="p_dia_preco[' . $i . ']" maxlength="5" style="width:100%; text-align:right; " value="' . emFormatoDinheiro($aulas['dia_preco']) . '"/></td> ';
				echo '</tr>';
			}
			?>
		</tbody>
	</table>
	
	<button type="submit" class="btn btn-primary">Salvar Alterações</button>
</form>

<?php pageclose() ?>