<?php
	class Funcionario extends AppModel {
		var $name = 'Funcionario';
		
		var $senhaSemHash = null; // O valor desta variavel é preenchido no beforeFilter do funcionarios_controller.php
		
		var $hasMany = array(
			"Ponto"=>array('dependent'=> true),
			"Justificativa"=>array('dependent'=> true)
		);
		
		var $validate = array(
			"usuario" => array (
				"regra_usuario_unico" => array(
					"rule" => "isUnique",
					'message' => 'Este nome de usuário já existe.'
				),
				"regra_usuario_vazio" => array(
					"rule" => "notEmpty",
					'message' => 'O nome de usuário não pode ser deixado em branco'
				)
			),
			
			"senha" => array(
				"regra_senha_vazio" => array(
					"rule" => array("_senhaVazia"),
					"message" => "A senha não pode ser deixada em branco",
					"on" => "create"
				),
				"regra_senha_confirma" => array(
					"rule" => array("_senhaConfirma"),
					"message" => "As senhas digitadas não conferem."
				)
			),
			
			"nome" => array(
				"rule" => "notEmpty",
				"message" => "O nome não pode ser deixado em branco"
			),
			
			"ippadrao" => array(
				"regra_ip_validaip" => array(
					"rule" => "ip",
					"message" => "Digite um número de IP válido"
				),
				"regra_ip_vazio" => array(
					"rule" => "notEmpty",
					"message" => "O IP não pode ser deixado em branco"
				)
			),
			
			"expediente" => array(
				"regra_expediente_numeric" => array(
					"rule" => "numeric",
					"message" => "O valor deve conver apenas números"
				),
				"regra_expediente_vazio" => array(
					"rule" => "notEmpty",
					"message" => "O expediente não pode ser deixado em branco"
				)
			),
		);
		
		function _senhaConfirma($args) {
			if($this->senhaSemHash!=$this->data['Funcionario']['senha_confirmar']) {
				return false;
			}
			return true;
		}
		
		function _senhaVazia($args) {
			if(empty($this->senhaSemHash)) {
				return false;
			}
			return true;
		}
	}
?>
