<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="Mon, 06 Jan 1980 00:00:01 GMT" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="distribution" content="global" />
	<meta name="resource-type" content="document" />
	<meta name="language" content="pt-br" />
	<meta name="robots" content="all" />
	
	<title><?php echo $title_for_layout?></title>
		
	<!--	
	<link rel="stylesheet" href="style.css" type="text/css" charset="utf-8">
	<script type="text/javascript" src="http://jqueryjs.googlecode.com/files/jquery-1.3.1.js"></script>
	<script type="text/javascript" src="js/funcoes.js"></script>
	-->
	
	<?php echo $html->css("style") ?>
	<?php //echo $javascript->link("funcoes"); ?>
	<?php echo $scripts_for_layout ?>
</head>

<body>
<div id="estrutura">
	
	<div id="corpo">
	
		<div id="topo">
			<div id="topo_superior">
				<h1><a href="">Banco de Horas</a></h1>
				<div id="menu_superior">
					<? if(isset($infoLogado)) { ?>
					<?=$infoLogado['nome']; ?> | <?php echo $html->link("Logout", array("controller"=>"funcionarios","action"=>"logout")); ?>
					<? } ?>
				</div>
			</div>
			<ul id="menu">
				<?php if($infoLogado['bateponto']=="sim") { ?>
				<li><?php echo $html->link("Bater Ponto", array("controller"=>"pontos","action"=>"index")); ?></li>
				<?php } ?>
				
				<?php if($infoLogado['admin']=="sim") { ?>
				<li><?php echo $html->link("Pontos do dia", array("controller"=>"pontos","action"=>"dia")); ?></li>
				<?php } ?>
				
				<?php if($infoLogado['bateponto']=="sim") { ?>
				<li><?php echo $html->link("Banco de Horas", array("controller"=>"pontos","action"=>"ver")); ?></li>
				<?php } ?>
				
				<?php if($infoLogado['admin']=="sim") { ?>
				<li><?php echo $html->link("Funcionários", array("controller"=>"funcionarios","action"=>"index")); ?></li>
				<?php } ?>
				
				<?php if($infoLogado['admin']=="sim") { ?>
				<li><?php echo $html->link("Eventos", array("controller"=>"eventos","action"=>"index")); ?></li>
				<?php } ?>
			</ul>
			
		</div><!--FIM TOPO-->
		
		<div id="conteudo">
			<?php 
				$temSidebar = false;
				$include_nome = "sidebar_" . strtolower($this->name) . "_" . strtolower($this->action) . ".ctp";
				if(file_exists($_SERVER["DOCUMENT_ROOT"] . $this->base . "/app/views/layouts/" . $include_nome)) {
					$temSidebar=true;
				} else {
					$include_nome = "sidebar_" . strtolower($this->name) . ".ctp";
					if(file_exists($_SERVER["DOCUMENT_ROOT"] . $this->base . "/app/views/layouts/" . $include_nome)) {
						$temSidebar=true;
					}
				}
			?>
		
			<span id="rodape_conteudo"></span>
			<div id="conteudo_interno" <? if($temSidebar) { ?>class="com_sidebar"<? } ?>>
				<?php $session->flash(); ?>
				<?php echo $content_for_layout ?>
			</div>
			
			<div id="sidebar">
				<?php
					if($temSidebar) {
						require($include_nome);
					}
				?>
			
			</div><!--FIM SIDEBAR-->

		</div><!--FIM CONTEUDO-->

		<div class="clear"></div>

	</div><!--FIM CORPO-->
	
	<div id="rodape">
	</div><!--FIM RODAPÉ-->

</div><!--FIM ESTRUTURA-->

</body>
</html>