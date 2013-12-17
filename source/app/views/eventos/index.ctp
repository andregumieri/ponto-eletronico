<h2>Próximos eventos</h2>
<ul class="funcionarios">
<? $contagem=0; ?>
<?php foreach($eventos as $evento) { ?>
	<li <? if($contagem%2==0) { ?>class="cor"<? } ?>>
		
		<div class="conteudo">
		<strong><?php echo date("d/m/Y",strtotime($evento['Evento']['data'])); ?></strong> 
		<?php echo $evento['Evento']['obs']; ?>
		<?php if(!empty($evento['Evento']['justificativa'])) { ?>
			(<strong><?php echo $evento['Evento']['justificativa']; ?></strong>)
		<?php } ?>
		</div>
		
		<div class="links">
			<?php echo $html->link("Editar",array("action"=>"edit",$evento['Evento']['id'])); ?>
			<?php echo $html->link("Excluir",array("action"=>"delete",$evento['Evento']['id']),array("class"=>"excluir"),"Tem certeza que deseja excluir este evento?"); ?>
		</div>
	</li>
	<? $contagem++; ?>
<?php } ?>
</ul>