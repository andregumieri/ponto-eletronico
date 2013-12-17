<h2>Editar ponto</h2>

<?php echo $form->create("Ponto"); ?>
<?php echo $form->input("id"); ?>
<?php echo $form->input("created", array("dateFormat"=>"NONE","timeFormat"=>"24")); ?>
<?php echo $form->end("Salvar horário");?>