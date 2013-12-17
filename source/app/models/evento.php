<?php
	class Evento extends AppModel {
		var $name = "Evento";
		var $validate = array(
			"data" => array(
				"valida_data_compara" => array(
					"rule" => array("_dataCompara"),
					"message"=>"A data deve ser de um dia posterior a hoje"
				),
				"valida_data_unique" => array(
					"rule"=>"isUnique",
					"message"=>"Já existe outro evento cadastrado nesta data.",
					"allowEmpty"=>false
				),
				"valida_data_date" => array(
					"rule"=>"date",
					"message"=>"A data do evento deve ser válida",
					"allowEmpty"=>false
				)
			),
			"obs" => array (
				"rule"=>"notEmpty",
				"message"=>"Você deve preencher o campo Observações"
			)
		);
		
		function _dataCompara($arg) {
			$hoje = mktime(0,0,0);
			$data = strtotime($arg['data']);
			
			if($data<=$hoje) {
				return false;
			}
			
			return true;
		}
	}
?>
