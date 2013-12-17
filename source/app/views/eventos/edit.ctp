<h2>Editar evento</h2>
<?php echo $form->create("Evento"); ?>
<?php echo $form->input("id"); ?>
<?php 
	echo $form->input("data",array(
		"dateFormat"=>"DMY", 
		"minYear"=>date('Y'), 
		"maxYear"=>intval(date('Y'))+1
	)); 
?>
<?php echo $form->input("justificativa",array("options"=>array(""=>"Nenhuma","feriado"=>"Feriado"), "default"=>"")); ?>
<?php echo $form->input("obs"); ?>

<?php echo $form->end("Salvar evento");?>

