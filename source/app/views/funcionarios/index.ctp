<h2>Funcionários</h2>
<ul class="funcionarios">
<? $contagem=0; ?>
<?php foreach($funcionarios as $funcionario) { ?>
	<li <? if($contagem%2==0) { ?>class="cor"<? } ?>>
		<div class="conteudo">
			<?php echo $funcionario['Funcionario']['nome']; ?> 
			<?php if($funcionario['Funcionario']['admin']=="sim") { ?> 
			<small>(Administrador)</small>
			<? } ?>
		</div>
		<div class="links">
			<?php echo $html->link("Ver pontos", array("controller"=>"pontos","action"=>"ver",$funcionario['Funcionario']['id'])); ?>
			<?php echo $html->link("Editar", array("action"=>"edit",$funcionario['Funcionario']['id'])); ?>
			<?php echo $html->link("Excluir", array("action"=>"delete",$funcionario['Funcionario']['id']),array("class"=>"excluir"),"Tem certeza que deseja deletar este funcionário? Esta ação apagará TODOS os pontos batidos do usuário."); ?>
		</div>
	</li>
	<? $contagem++; ?>
<?php } ?>
</ul>

<h3>Inativos</h3>
<ul class="funcionarios">
<? $contagem=0; ?>
<?php foreach($funcionariosInativos as $funcionario) { ?>
	<li <? if($contagem%2==0) { ?>class="cor"<? } ?>>
		<div class="conteudo">
			<?php echo $funcionario['Funcionario']['nome']; ?> 
			<?php if($funcionario['Funcionario']['admin']=="sim") { ?> 
			<small>(Administrador)</small>
			<? } ?>
		</div>
		<div class="links">
			<?php echo $html->link("Ver pontos", array("controller"=>"pontos","action"=>"ver",$funcionario['Funcionario']['id'])); ?>
			<?php echo $html->link("Editar", array("action"=>"edit",$funcionario['Funcionario']['id'])); ?>
			<?php echo $html->link("Excluir", array("action"=>"delete",$funcionario['Funcionario']['id']),array("class"=>"excluir"),"Tem certeza que deseja deletar este funcionário? Esta ação apagará TODOS os pontos batidos do usuário."); ?>
		</div>
	</li>
	<? $contagem++; ?>
<?php } ?>
</ul>
