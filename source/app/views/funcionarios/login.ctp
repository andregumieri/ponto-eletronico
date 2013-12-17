<?php
	echo $form->create('Funcionario', array('url' => array('controller' => 'funcionarios', 'action' =>'login')));
	echo $form->input('Funcionario.usuario');
	echo $form->input('Funcionario.senha', array("type"=>"password"));
	echo $form->end('Entrar');
?>