<?php
	class FuncionariosController extends AppController {
		var $name = 'Funcionarios';
		
		function index() {
			$this->Funcionario->recursive=0;

			$funcionarios=null;
			if($this->Session->read("Auth.Funcionario.admin")=="sim") {
				$funcionarios = $this->Funcionario->find("all",array(
					"order"=>"nome",
					"conditions"=>array("Funcionario.ativo"=>"sim")
				));
				
				$funcionariosInativos = $this->Funcionario->find("all",array(
					"order"=>"nome",
					"conditions"=>array("Funcionario.ativo"=>"nao")
				));
			} else {
				//$funcionarios = $this->Funcionario->findById($this->Session->read("Auth.Funcionario.id"),array("order"=>"nome"));
				$this->redirect(array("controller"=>"pontos","action"=>"ver",$this->Session->read("Auth.Funcionario.id")));
			}
			
			
			$this->set("funcionarios",$funcionarios);
			$this->set("funcionariosInativos",$funcionariosInativos);
		}
		
		function add() {
			$ippadrao_value="192.168.18.";
			if (!empty($this->data)) {
				$ippadrao_value = $this->data['Funcionario']['ippadrao'];
			
				$this->Funcionario->create();
				if ($this->Funcionario->save($this->data)) {
					$this->Session->setFlash(__('Funcionario cadastrado', true));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash(__('Ocorreram erros ao cadastrar o funcionário', true));
				}
			}
			
			$this->set("ippadrao_value",$ippadrao_value);
		}
		
		function edit($id = null) {
			if(is_null($id)) {
				$id = $this->Session->read("Auth.Funcionario.id");
			}
		
			if (!$id && empty($this->data)) {
				$this->Session->setFlash(__('Funcionário inválido', true));
				$this->redirect(array('action'=>'index'));
			}
			
			if (!empty($this->data)) {
				if ($this->Funcionario->save($this->data)) {
					$this->Session->setFlash(__('O funcionário foi salvo', true));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash(__('O Funcionário não pode ser salvo.', true));
				}
			}
			
			if (empty($this->data)) {
				$this->data = $this->Funcionario->read(null, $id);
			}
		}
		
		function delete($id=null) {
			if (!$id) {
				$this->Session->setFlash(__('ID inválido', true));
				$this->redirect(array('action'=>'index'));
			}
			if ($this->Funcionario->delete($id)) {
				$this->Session->setFlash(__('Funcionario deletado', true));
				$this->redirect(array('action'=>'index'));
			}
		}
		
		function login() {
			if ($this->Session->read('Auth.Funcionario')) {
				if (!empty($this->data)) {
					// Executar assim que logar
					
					// Verifica os dias faltantes de todos os funcionarios
					$funcionarios = $this->Funcionario->findAllByAtivo("sim");
					foreach($funcionarios as $funcionario) {
						$this->_verificarDiasFaltantes($funcionario['Funcionario']['id']);
					}
				}
				$this->Session->setFlash('Você esta logado');
				
				if($this->Session->read("Auth.Funcionario.bateponto")=="nao") {
					$this->redirect(array("controller"=>"pontos","action"=>"dia"));
				} else {
					$this->redirect($this->Auth->redirect());
				}
			}
		}
		
		function logout() {
			$this->Session->setFlash('Good-Bye');
			$this->redirect($this->Auth->logout());	
		}
		
		
		function beforeFilter() {
			parent::beforeFilter();
			
			if($this->action=="add") {
				$this->Funcionario->senhaSemHash=$this->data['Funcionario']['senha'];
			}
			
			if($this->action=="edit") {
				if(!isset($this->data['Funcionario']['senha']) || empty($this->data['Funcionario']['senha'])) {
					unset($this->data['Funcionario']['senha']);
					unset($this->data['Funcionario']['senha_confirmar']);
				} else {
					$this->Funcionario->senhaSemHash=$this->data['Funcionario']['senha'];
				}
			}
		}
	}
?>