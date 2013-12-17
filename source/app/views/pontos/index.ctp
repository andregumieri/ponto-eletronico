<h2>Olá, <?php echo $funcionario['Funcionario']['nome']; ?></h2>

<?php
	$entrada		= $html->link("Bater Ponto", array("controller"=>"pontos","action"=>"baterponto","entrada"),array("class"=>"botao"));
	$almoco_inicio	= $html->link("Bater Ponto", array("controller"=>"pontos","action"=>"baterponto","almoco_inicio"),array("class"=>"botao"));
	$almoco_fim		= $html->link("Bater Ponto", array("controller"=>"pontos","action"=>"baterponto","almoco_fim"),array("class"=>"botao"));
	$pausa_inicio	= $html->link("Bater Ponto", array("controller"=>"pontos","action"=>"baterponto","pausa_inicio"),array("class"=>"botao"));
	$pausa_fim		= $html->link("Bater Ponto", array("controller"=>"pontos","action"=>"baterponto","pausa_fim"),array("class"=>"botao"));
	$saida			= $html->link("Bater Ponto", array("controller"=>"pontos","action"=>"baterponto","saida"),array("class"=>"botao"));
	$saida_conf		= $html->link("Bater Ponto", array("controller"=>"pontos","action"=>"baterponto","saida"),array("class"=>"botao"),"Tem certeza que deseja bater o ponto de saida sem almoço?");
?>

<?php
	$confPontos = array("entrada"=>array(),"almoco_inicio"=>array(),"almoco_fim"=>array(),"saida"=>array());

	$confPontos['entrada']['hora']="00:00";
	if(isset($pontosTipo['entrada'])) {
		$confPontos['entrada']['hora']=date("H:i",strtotime($pontosTipo['entrada']['created']));
	}
	
	$confPontos['almoco_inicio']['hora']="00:00";
	if(isset($pontosTipo['almoco_inicio'])) {
		$confPontos['almoco_inicio']['hora']=date("H:i",strtotime($pontosTipo['almoco_inicio']['created']));
	}
	
	$confPontos['almoco_fim']['hora']="00:00";
	if(isset($pontosTipo['almoco_fim'])) {
		$confPontos['almoco_fim']['hora']=date("H:i",strtotime($pontosTipo['almoco_fim']['created']));
	}
	
	$confPontos['saida']['hora']="00:00";
	if(isset($pontosTipo['saida'])) {
		$confPontos['saida']['hora']=date("H:i",strtotime($pontosTipo['saida']['created']));
	}	
	
	if(empty($ultimoponto)) {
		$confPontos['entrada']['classe'] = "ativo";
		$confPontos['almoco_inicio']['classe'] = "";
		$confPontos['almoco_fim']['classe'] = "";
		$confPontos['saida']['classe'] = "";
	} else {
		if($ultimoponto['Ponto']['tipo']=="entrada") {
			$confPontos['entrada']['classe'] = "batido";
			$confPontos['almoco_inicio']['classe'] = "ativo";
			$confPontos['almoco_fim']['classe'] = "";
			$confPontos['saida']['classe'] = "ativo";
			$saida = $saida_conf;
		} elseif($ultimoponto['Ponto']['tipo']=="almoco_inicio") {
			$confPontos['entrada']['classe'] = "batido";
			$confPontos['almoco_inicio']['classe'] = "batido";
			$confPontos['almoco_fim']['classe'] = "ativo";
			$confPontos['saida']['classe'] = "";
		} elseif($ultimoponto['Ponto']['tipo']=="almoco_fim") {
			$confPontos['entrada']['classe'] = "batido";
			$confPontos['almoco_inicio']['classe'] = "batido";
			$confPontos['almoco_fim']['classe'] = "batido";
			$confPontos['saida']['classe'] = "ativo";
		} elseif($ultimoponto['Ponto']['tipo']=="saida") {
			$confPontos['entrada']['classe'] = "batido";
			if(isset($pontosTipo['almoco_inicio'])) {
				$confPontos['almoco_inicio']['classe'] = "batido";
			} else {
				$confPontos['almoco_inicio']['classe'] = "naobatido";
			}
			if(isset($pontosTipo['almoco_fim'])) {
				$confPontos['almoco_fim']['classe'] = "batido";
			} else {
				$confPontos['almoco_fim']['classe'] = "naobatido";
			}
			$confPontos['saida']['classe'] = "batido";
		}
	}
?>

<?php if($funcionario['Funcionario']['bateponto']=="sim") { ?>
<ul class="baterponto">
	<li class="primeiro <?php echo $confPontos['entrada']['classe']; ?>">
		<h3>Entrada</h3>
		<p class="hora"><?php echo $confPontos['entrada']['hora']; ?></p>
		<?php echo $entrada; ?>
	</li>
	<li class="<?php echo $confPontos['almoco_inicio']['classe']; ?>">
		<h3>Almoço</h3>
		<p class="hora"><?php echo $confPontos['almoco_inicio']['hora']; ?></p>
		<?php echo $almoco_inicio; ?>
	</li>
	<li class="<?php echo $confPontos['almoco_fim']['classe']; ?>">
		<h3>Almoço (Retorno)</h3>
		<p class="hora"><?php echo $confPontos['almoco_fim']['hora']; ?></p>
		<?php echo $almoco_fim; ?>
	</li>
	<li class="<?php echo $confPontos['saida']['classe']; ?>">
		<h3>Saída</h3>
		<p class="hora"><?php echo $confPontos['saida']['hora']; ?></p>
		<?php echo $saida; ?>
	</li>
</ul>
<?php } ?>

<hr />
