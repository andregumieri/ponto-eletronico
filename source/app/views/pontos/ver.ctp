<h2><?php echo $funcionario['Funcionario']['nome']; ?> <small>Banco de horas: <?php echo $extra_contabilizado_string; ?></small></h2>

<?php if($funcionario['Funcionario']['bateponto']=="nao") { ?>
<p>Este funcionário foi cadastrado para não bater ponto.</p>
<?php } else { ?>

<ul>
<?php foreach($mesesAnos as $mesano) { ?>
	<li style="display: inline;">
	<?php 
		$mesanoArray = explode("-",$mesano);
		echo $html->link($mesanoArray[1]."/".$mesanoArray[0],array("action"=>"ver",$funcionario['Funcionario']['id'],$mesanoArray[1],$mesanoArray[0]));
	?>
	</li>
<?php } ?>
</ul>

<table class="pontos">
	<tbody>
	<?php foreach($pontos as $data=>$ponto) { ?>
		<tr <? if(isset($ponto['marcar'])) { ?>class="<?=$ponto['marcar']?>"<? } ?>>
			<td class="data"><?php echo $ponto['entrada']['dataponto']; ?> <br /><small><?php echo $ponto['entrada']['diasemana']; ?></small></td>
			<td>
				<?php
				$tipoPonto = "entrada";
				if(isset($ponto[$tipoPonto])) { 
					if(!empty($ponto[$tipoPonto]['hora']) && $ponto[$tipoPonto]['hora']!="-" ) {
						echo $html->link($ponto[$tipoPonto]['hora'],array("action"=>"edit",$ponto[$tipoPonto]['id']),null,"Editar este horário?");
					} else {
						echo $ponto[$tipoPonto]['hora'];
					}
					
					if(isset($ponto[$tipoPonto]['ip']) && isset($ponto[$tipoPonto]['ippadrao']) && $ponto[$tipoPonto]['ip']!=$ponto[$tipoPonto]['ippadrao'] && $infoLogado['admin']=='sim') {
						echo "<small> (IP)</small>";
					}
				} 
				?>
			</td>
			
			<td>
				<?php
				$tipoPonto = "almoco_inicio";
				if(isset($ponto[$tipoPonto])) { 
					if(!empty($ponto[$tipoPonto]['hora']) && $ponto[$tipoPonto]['hora']!="-" ) {
						echo $html->link($ponto[$tipoPonto]['hora'],array("action"=>"edit",$ponto[$tipoPonto]['id']),null,"Editar este horário?");
					} else {
						echo $ponto[$tipoPonto]['hora'];
					}
					
					if(isset($ponto[$tipoPonto]['ip']) && isset($ponto[$tipoPonto]['ippadrao']) && $ponto[$tipoPonto]['ip']!=$ponto[$tipoPonto]['ippadrao'] && $infoLogado['admin']=='sim') {
						echo "<small> (IP)</small>";
					}
				} 
				?>
			</td>

			<td>
				<?php
				$tipoPonto = "almoco_fim";
				if(isset($ponto[$tipoPonto])) { 
					if(!empty($ponto[$tipoPonto]['hora']) && $ponto[$tipoPonto]['hora']!="-" ) {
						echo $html->link($ponto[$tipoPonto]['hora'],array("action"=>"edit",$ponto[$tipoPonto]['id']),null,"Editar este horário?");
					} else {
						echo $ponto[$tipoPonto]['hora'];
					}
					
					if(isset($ponto[$tipoPonto]['ip']) && isset($ponto[$tipoPonto]['ippadrao']) && $ponto[$tipoPonto]['ip']!=$ponto[$tipoPonto]['ippadrao'] && $infoLogado['admin']=='sim') {
						echo "<small> (IP)</small>";
					}
				} 
				?>
			</td>
			
			<td>
				<?php
				$tipoPonto = "saida";
				if(isset($ponto[$tipoPonto])) { 
					if(!empty($ponto[$tipoPonto]['hora']) && $ponto[$tipoPonto]['hora']!="-" ) {
						echo $html->link($ponto[$tipoPonto]['hora'],array("action"=>"edit",$ponto[$tipoPonto]['id']),null,"Editar este horário?");
					} else {
						echo $ponto[$tipoPonto]['hora'];
					}
					
					if(isset($ponto[$tipoPonto]['ip']) && isset($ponto[$tipoPonto]['ippadrao']) && $ponto[$tipoPonto]['ip']!=$ponto[$tipoPonto]['ippadrao'] && $infoLogado['admin']=='sim') {
						echo "<small> (IP)</small>";
					}
				} 
				?>
			</td>
			
			<td><?php echo date("H:i",mktime(0,intval($ponto['expediente_feito']))); ?></td>
			<td><?php echo date("H:i",mktime(0,intval($ponto['expediente']))); ?></td>
			
			<td <? if($ponto['extra_string']{0}=="-") { ?>class="negativo"<? } ?>><?php echo $ponto['extra_string']; ?></td>
			
			<td><strong><?php echo $ponto['justificativa']; ?></strong> <?php echo $ponto['obs']; ?></td>
		
			<?php if($infoLogado['admin']=="sim") { ?>
			<td>
				<?php echo $html->link("Justificar",array("controller"=>"justificativas","action"=>"justificar",$funcionario['Funcionario']['id'],$data)); ?>
			</td>
			<? } ?>

		</li>	
	<?php } ?>
	</tbody>	
	
	<thead>
		<tr>
			<th class="naotem"></th>
			<th class="esquerda">Entrada</th>
			<th>Almoço (Início)</th>
			<th>Almoço (Fim)</th>
			<th>Saída</th>
			
			<th>Horas trabalhadas</th>
			<th>Expediente</th>

			
			<?php if($infoLogado['admin']=="sim") { ?>
				<th>Banco de horas</th>
				<th class="direita">Justificativa</th>
				<th class="naotem"><!--Ações--></th>
			<? } else { ?>
				<th class="direita">Banco de horas</th>
				<th class="naotem">Justificativa</th>
			<? } ?>
		</tr>
	</thead>
</table>
<?php } ?>
