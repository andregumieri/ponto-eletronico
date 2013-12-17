<h2>Editar funcionário</h2>

<?php echo $form->create(); ?>
<?php echo $form->input('id'); ?>

<?php echo $form->input('nome'); ?>
<?php echo $form->input('usuario',array("label"=>"Usuário")); ?>

<?php echo $form->input('ippadrao',array("label"=>"IP Padrão")); ?>
<?php echo $form->input('expediente'); ?>

<?php echo $form->input('ativo',array('options' => array("sim"=>"Sim","nao"=>"Não"), "label"=>"Está ativo?")); ?>
<?php echo $form->input('bateponto',array('options' => array("sim"=>"Sim","nao"=>"Não"), "label"=>"Bate ponto?")); ?>
<?php echo $form->input('admin',array('options' => array("sim"=>"Sim","nao"=>"Não"), "label"=>"Admin?")); ?>


<p><strong>Preencha os campos apenas se quiser alterar a senha.</strong></p>

<?php echo $form->input('senha',array("type"=>"password","value"=>"")); ?>
<?php echo $form->input('senha_confirmar',array("type"=>"password","value"=>"","label"=>"Confirmar senha")); ?>


<?php echo $form->end("Salvar"); ?>