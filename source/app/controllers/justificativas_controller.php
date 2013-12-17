<?php
	class JustificativasController extends AppController {
		var $name = 'Justificativas';
		
		function index() {
			
		}
		
		function edit($id=null) {
			if (!$id && empty($this->data)) {
				$this->Session->setFlash(__('ID da Justificativa inválido', true));
				$this->redirect("/");
			}
		
			if (!empty($this->data)) {
				// Pega os dados do funcionario
				$justificativa = $this->Justificativa->findById($id);
				
				if(empty($this->data['Justificativa']['expediente'])) {
					$this->data['Justificativa']['expediente']=0;
				}

				if($this->data['Justificativa']['justificativa']!="atestado" && $this->data['Justificativa']['justificativa']!="feriado"){
					if($this->data['Justificativa']['expediente']==0) {
						// Se não for feriado ou atestado e o expediente for zero, coloca
						// o expediente padrao e multiplica por 60, para virar minutos
						$this->data['Justificativa']['expediente']=intval($justificativa['Funcionario']['expediente'])*60;
					}
				} else {
					// Se for feriado ou atestado, e o expediente for igual
					// ao padrao da tabela de funcionario, deixa zerado
					if($this->data['Justificativa']['expediente']==intval($justificativa['Funcionario']['expediente'])*60) {
						$this->data['Justificativa']['expediente']=0;
					}
				}
			
				if ($this->Justificativa->save($this->data)) {
					$justificativa = $this->Justificativa->read();
					$this->Session->setFlash(__('Justificativa salva', true));
					$this->redirect(array("controller"=>"pontos",'action'=>'ver',$justificativa['Justificativa']['funcionario_id']));
				} else {
					$this->Session->setFlash(__('Problemas para salvar a justificativa.', true));
					$this->redirect(array("controller"=>"pontos",'action'=>'ver',$justificativa['Justificativa']['funcionario_id']));
				}
			}
			
			if (empty($this->data)) {
				$this->data = $this->Justificativa->read(null,$id);
			}
			
			$funcionario_id = $this->data['Justificativa']['funcionario_id'];
			$this->set("funcionario_id",$funcionario_id);
		}
		
		function justificar($funcionario_id=null,$data=null) {
			if((is_null($funcionario_id) || is_null($data))) {
				$this->Session->setFlash("A Justificativa deve receber o ID e Data");
				$this->redirect("/");
			}
			
			$justificativa = $this->Justificativa->find("first", array(
				"conditions" => array(
					"Justificativa.created"=>$data,
					"Justificativa.funcionario_id"=>$funcionario_id
				)
			));
			
			if(!empty($justificativa)) {
				$this->redirect(array("action"=>"edit",$justificativa['Justificativa']['id']));
			} else {
				$this->Session->setFlash("Justificativa não encontrada.");
				$this->redirect(array("controller"=>"pontos","action"=>"ver",$funcionario_id));
			}
		}
	}
?>