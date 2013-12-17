<?php
	class EventosController extends AppController {
		var $name = 'Eventos';
		
		function index() {
			$eventos = $this->Evento->find("all",array(
				"conditions"=>array(
					"Evento.data >="=>date("Y-m-d")
				),
				"order"=>"Evento.data"
			));
			
			$this->set("eventos",$eventos);
		}
		
		
		function add() {
			if (!empty($this->data)) {
				$this->Evento->create();
				if ($this->Evento->save($this->data)) {
					$this->Session->setFlash(__('Evento adicionado', true));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash(__('Ocorreram erros ao adicionar o evento. Tente novamente.', true));
				}
			}
		}
		
		
		function edit($id=null) {
			if (!$id && empty($this->data)) {
				$this->Session->setFlash(__('Evento inválido', true));
				$this->redirect(array('action'=>'index'));
			}
			if (!empty($this->data)) {
				if ($this->Evento->save($this->data)) {
					$this->Session->setFlash(__('O evento foi salvo', true));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash(__('Ocorreram erros ao adicionar o evento. Tente novamente.', true));
				}
			}
			if (empty($this->data)) {
				$this->data = $this->Evento->read(null, $id);
			}
		}
		
		function delete($id = null) {
			if (!$id) {
				$this->Session->setFlash(__('Evento inválido', true));
				$this->redirect(array('action'=>'index'));
			}
			if ($this->Evento->delete($id)) {
				$this->Session->setFlash(__('Evento removido', true));
				$this->redirect(array('action'=>'index'));
			}
		}
	}
?>