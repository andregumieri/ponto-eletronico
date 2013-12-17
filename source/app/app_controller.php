<?php
	class AppController extends Controller {
		var $components = array("Auth");
		var $helpers = array('Html', 'Form', 'Javascript');
		
		function beforeFilter() {
			$this->Auth->userModel = "Funcionario";
			$this->Auth->fields = array("username"=>"usuario", "password"=>"senha");
			$this->Auth->authorize = "controller";
			$this->Auth->userScope = array('Funcionario.ativo' => 'sim');
			$this->Auth->loginAction = array("controller"=>"funcionarios", "action"=>"login");
			$this->Auth->logoutRedirect = array("controller"=>"funcionarios", "action"=>"login");
			$this->Auth->loginRedirect = array("controller"=>"pontos", "action"=>"index");
			$this->Auth->autoRedirect = false;
			
			//$this->Auth->allowedActions = array("*");
			
			$this->set("infoLogado",$this->Session->read("Auth.Funcionario"));
		}
		
		function isAuthorized() {
			if ($this->Auth->user('admin') == 'sim') {
				return true;
			} else {
				return $this->_validaPermissao();
			}
			return true;
		}
		
		
		function _validaPermissao() {
			$permissoes = array();
			$permissoes['funcionarios']['add']=false;
			$permissoes['funcionarios']['edit']=false;
			$permissoes['funcionarios']['delete']=false;
			$permissoes['pontos']['add']=false;
			$permissoes['pontos']['edit']=false;
			$permissoes['pontos']['delete']=false;
			$permissoes['pontos']['dia']=false;
			$permissoes['justificativas']['add']=false;
			$permissoes['justificativas']['edit']=false;
			$permissoes['justificativas']['delete']=false;
			$permissoes['justificativas']['justificar']=false;
			$permissoes['eventos']['index']=false;
			$permissoes['eventos']['add']=false;
			$permissoes['eventos']['edit']=false;
			$permissoes['eventos']['delete']=false;
			
			$controller = strtolower($this->name);
			$action = strtolower($this->action);
			
			if(isset($permissoes[$controller][$action])) {
				return $permissoes[$controller][$action];
			}
			return true;
		}
		
		
		/*
		_verificarDiasFaltantes($id)
		Função que verifica o gap de dias
		entre a data atual e a última cadastrada
		no banco de dados
		*/
		function _verificarDiasFaltantes($id) {
			// Carrega os Modelos
			$this->loadModel("Funcionario");
			$this->loadModel("Justificativa");			
			$this->loadModel("Ponto");
			$this->loadModel("Evento");
			

			// Carrega o modelo Funcionario
			$this->Funcionario->recursive=-1;
			$funcionario = $this->Funcionario->findById($id);
			

			// Verifica se o funcionario esta ativo e se ele bate ponto.
			// O sistema só vai contabilizar os dias faltantes em um usuário ativo
			if($funcionario['Funcionario']['ativo']=="sim" && $funcionario['Funcionario']['bateponto']=="sim") {
				
				// Pega as informações do Funcionario
				$expediente = intval($funcionario['Funcionario']['expediente'])*60;
				$ipPadrao = $funcionario['Funcionario']['ippadrao'];
				
			
				// Pega o último dia com ponto batido
				$this->Justificativa->recursive=-1;
				$justificativa = $this->Justificativa->find("first",array(
					"conditions"=>array("funcionario_id"=>$id),
					"order"=>"Justificativa.created DESC"
				));
				
				
				// Pega o último dia cadastrado no Banco de dados,
				// primeiro pelo funcionario para caso ele nunca tenha batido ponto.
				// Se ele já bateu ponto alguma vez, pega pelo último ponto batido
				$dataBanco = strtotime($funcionario['Funcionario']['created']);
				$dataBanco = mktime(0,0,0,intval(date("m",$dataBanco)), intval(date("d",$dataBanco))-1, intval(date("Y",$dataBanco)));
				
				if($justificativa!==false) {
					$dataBanco = strtotime($justificativa['Justificativa']['created']);
				}
				
				// Pega o dia mais atual, ignorando hoje
				$dataAtual = mktime(0,0,0,intval(date("m")), intval(date("d"))-1, intval(date("Y")));
				
				
				// Coloca os eventos num array por data
				$this->Evento->recursive=-1;
				$eventosCru = $this->Evento->find("all",array("conditions"=>array("Evento.data >"=>date("Y-m-d",$dataBanco))));
				$eventos = array();
				foreach($eventosCru as $evento)	{
					$eventos[$evento['Evento']['data']] = $evento['Evento'];
				}
	
				// Se o dia do Banco for menor que o dia atual, cadastra os outros dias
				if($dataBanco<$dataAtual) {
					$diaBanco = intval(date("d",$dataBanco));
					$mesBanco = intval(date("m",$dataBanco));
					$anoBanco = intval(date("Y",$dataBanco));
				
					// Faz loop até preencher todas as datas
					$finalizado=false;
					$somaDias=1;
					while($finalizado==false) {
					
						// Cria nova data
						$dataNovo = mktime(0,0,0,$mesBanco,$diaBanco+$somaDias,$anoBanco);
						
						// Soma no loop de dias
						$somaDias++;
						
						// Verifica se é Fim de semana. Se for, zera o expediente
						$dadosExpediente=$expediente;
						$dadosObs="";
						$dadosJustificativa="";
						if(date("w",$dataNovo)=="0" || date("w",$dataNovo)=="6") {
							$dadosExpediente=0;
							$dadosObs=$this->_diaDaSemana(intval(date("w",$dataNovo)));
						}
						
						// Verifica se tem evento nesse dia
						if(isset($eventos[date("Y-m-d", $dataNovo)])) {
							$eventoAtual = $eventos[date("Y-m-d", $dataNovo)];
							
							// Se o evento for um feriado, coloca o expediente como Zero horas
							if($eventoAtual['justificativa']=="feriado") {
								$dadosExpediente=0;
								$dadosJustificativa=$eventoAtual['justificativa'];
							}
							
							$dadosObs=$eventoAtual['obs'];
						}
						
						// Prepara variavel para cadastrar a Justificativa
						$justificativaDados = array(
							"Justificativa"=>array(
								"funcionario_id"=>$id,
								"created"=>date("Y-m-d", $dataNovo),
								"expediente"=>$dadosExpediente,
								"justificativa"=>$dadosJustificativa,
								"obs"=>$dadosObs
							)
						);
						
						// Cadastra a justificativa
						$this->Justificativa->id=null;
						$this->Justificativa->create();
						$this->Justificativa->save($justificativaDados);
						
						
						// Loop para cadastrar os pontos
						$pontoTipos = array("entrada","almoco_inicio","almoco_fim","saida");
						foreach($pontoTipos as $tipo) {
							$pontoDados = array(
								"Ponto" => array (
									"funcionario_id"=>$id,
									"created"=>date("Y-m-d", $dataNovo) . " 00:00:00",
									"tipo"=>$tipo,
									"ippadrao"=>$ipPadrao
								)
							);
							
							$this->Ponto->id=null;
							$this->Ponto->create();
							$this->Ponto->save($pontoDados);
						}
						
						// Se a data criada for igual a data mais atual, finaliza.
						if($dataNovo==$dataAtual) {
							$finalizado=true;
						}
					}// Fim - Loop para cadastrar datas
				} // Fim - if($dataBanco<$dataAtual)
			} // Fim - Se o funcionario esta ativo
		} // Fim - Function _verificarDiasFaltantes
		
		
		function _diaDaSemana($num) {

			$ret = "";
			switch($num) {
				case 0:
					$ret = "Domingo";
					break;
				case 1:
					$ret = "Segunda";
					break;
				case 2:
					$ret = "Terça";
					break;
				case 3:
					$ret = "Quarta";
					break;
				case 4:
					$ret = "Quinta";
					break;
				case 5:
					$ret = "Sexta";
					break;
				case 6:
					$ret = "Sabado";
					break;
			}
			
			return $ret;
		}
				
	}
?>