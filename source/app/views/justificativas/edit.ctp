<?php echo $form->create(); ?>
<?php echo $form->input('id'); ?>

<?php echo $form->input('obs'); ?>
<?php echo $form->input('justificativa',array("options"=>array(""=>"Nenhuma","atestado"=>"Atestado","feriado"=>"Feriado"), "default"=>"")); ?>

<?php echo $form->input('expediente',array("label"=>"Expediente (em minutos)")); ?>



<?php echo $form->end("Salvar"); ?>

<?php echo $html->link("Voltar para o banco de horas",array("controller"=>"pontos","action"=>"ver",$funcionario_id)); ?>