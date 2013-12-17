<?php
	class PontosController extends AppController {
		var $name = 'Pontos';
		
		function index() {
			$funcionario_id = $this->Session->read("Auth.Funcionario.id");
			$this->loadModel("Funcionario");
			$this->Funcionario->recursive=-1;
			$funcionario = $this->Funcionario->findById($funcionario_id);
			
			$this->Ponto->recursive=-1;
			$ultimoponto = $this->Ponto->find("first",array(
				"conditions"=>array(
					"date(Ponto.created)"=>date("Y-m-d"),
					"funcionario_id"=>$funcionario_id,
				),
				"order"=>"Ponto.created DESC"
			));
			
			
			// Seleciona todos os pontos
			$pontos = $this->Ponto->find("all",array(
				"conditions"=>array(
					"date(Ponto.created)"=>date("Y-m-d"),
					"funcionario_id"=>$funcionario_id,
				),
				"order"=>"Ponto.created DESC"
			));
			
			
			// Coloca os pontos num array por tipo
			$pontosTipo = array();
			foreach($pontos as $ponto) {
				$pontosTipo[$ponto['Ponto']['tipo']] = $ponto['Ponto'];
			}
			
			$this->set("funcionario",$funcionario);
			$this->set("ultimoponto",$ultimoponto);
			$this->set("pontosTipo",$pontosTipo);
		}
		
		
		function dia() {
			$this->loadModel("Funcionario");
			$this->Funcionario->recursive=-1;
			$funcionarios = $this->Funcionario->find("all",array(
				"conditions"=>array(
					"Funcionario.bateponto"=>"sim",
					"Funcionario.ativo"=>"sim"
				)
			
			));
			
			
			$listaGeral = array();
			foreach($funcionarios as $funcionario) {
				$info = array();
				
				$this->Ponto->recursive=-1;
				$ultimoponto = $this->Ponto->find("first",array(
					"conditions"=>array(
						"date(Ponto.created)"=>date("Y-m-d"),
						"funcionario_id"=>$funcionario['Funcionario']['id'],
					),
					"order"=>"Ponto.created DESC"
				));
				
				
				// Seleciona todos os pontos
				$pontos = $this->Ponto->find("all",array(
					"conditions"=>array(
						"date(Ponto.created)"=>date("Y-m-d"),
						"funcionario_id"=>$funcionario['Funcionario']['id']
					),
					"order"=>"Ponto.created DESC"
				));
				
				
				// Coloca os pontos num array por tipo
				$pontosTipo = array();
				foreach($pontos as $ponto) {
					$pontosTipo[$ponto['Ponto']['tipo']] = $ponto['Ponto'];
				}

				$info['Funcionario'] = $funcionario['Funcionario'];
				$info['PontoUltimo'] = $ultimoponto;
				$info['PontosTipo']=$pontosTipo;
				
				$listaGeral[]=$info;
			}
			
			$this->set("listaGeral",$listaGeral);
		}
		
		
		function baterponto($tipo=null) {
		
			if(is_null($tipo)) {
				$this->Session->setFlash("Nenhum tipo de ponto foi informado.");
				$this->redirect(array("action"=>"index"));
			}
			
			// Verifica se foi passado um tipo válido
			if($tipo!=="entrada" && $tipo!="almoco_inicio" && $tipo!="almoco_fim" && $tipo!="saida") {
				$this->Session->setFlash("Tipo de ponto inválido [$tipo]");
				$this->redirect(array("action"=>"index"));
			}
		
			$funcionario_id = $this->Session->read("Auth.Funcionario.id");
			
			$ultimoponto = $this->Ponto->find("first",array(
				"conditions"=>array(
					"date(Ponto.created)"=>date("Y-m-d"),
					"funcionario_id"=>$funcionario_id,
				),
				"order"=>"Ponto.created DESC"
			));
			
			// Valida para ver se é possível bater o ponto
			if(empty($ultimoponto)) {
				if($tipo!=="entrada") {
					$this->Session->setFlash("Não é permitido bater o ponto de $tipo sem dar entrada.");
					$this->redirect(array("action"=>"index"));
				}
			} else {
				$ultimoPontoTipo = $ultimoponto['Ponto']['tipo'];
				if($ultimoPontoTipo=="entrada") {
					if($tipo!="almoco_inicio" && $tipo!="saida") {
						$this->Session->setFlash("Não foi possível bater o ponto de $tipo.");
						$this->redirect(array("action"=>"index"));
					}
				} elseif($ultimoPontoTipo=="almoco_inicio") {
					if($tipo!=="almoco_fim") {
						$this->Session->setFlash("Não foi possível bater o ponto de $tipo sem finalizar o almoço.");
						$this->redirect(array("action"=>"index"));			
					}
				} elseif($ultimoPontoTipo=="almoco_fim") {
					if($tipo!=="saida") {
						$this->Session->setFlash("Não foi possível bater o ponto de $tipo.");
						$this->redirect(array("action"=>"index"));
					}
				} elseif($ultimoPontoTipo=="saida") {
					$this->Session->setFlash("O dia de hoje já foi finalizado. Não é possível bater mais nenhum ponto.");
					$this->redirect(array("action"=>"index"));	
				}
			}
			
			

			
			$this->loadModel("Funcionario");
			$this->loadModel("Justificativa");
						
			$this->Funcionario->recursive=-1;
			$this->Justificativa->recursive=-1;
			
			$funcionario = $this->Funcionario->findById($funcionario_id);
			
			// Verifica se este funcionario bate ponto
			if($this->Session->read("Auth.Funcionario.bateponto")=="nao") {
				$this->Session->setFlash("Este funcionario não esta autorizado a bater ponto.");
				$this->redirect(array("action"=>"index"));
			}
			
			// Pega as informacoes do Funcionario
			$ippadrao = $funcionario['Funcionario']['ippadrao'];
			$expediente = intval($funcionario['Funcionario']['expediente'])*60;
			
			// Verifica se a Justificativa existe
			$justificativa=$this->Justificativa->find("first", array(
				"conditions"=>array(
					"funcionario_id"=>$funcionario_id,
					"Justificativa.created"=>date("Y-m-d")
				)
			));
			
			// Salva a justificativa, se já não existir
			if(!$justificativa) {
				$data = array (
					"Justificativa"=>array (
						"funcionario_id" => $funcionario_id,
						"expediente" => $expediente
					)
				);
				
				$this->Justificativa->create();
				if(!$this->Justificativa->save($data)) {
					$this->Session->setFlash("Ocorreram erros ao bater o ponto de $tipo [Justificativa]");
				}
			}
			
			
			$data = array (
				"Ponto"=>array (
					"funcionario_id" => $funcionario_id,
					"tipo" => $tipo,
					"datahora" => "NOW()",
					"ip" => $_SERVER["REMOTE_ADDR"],
					"ippadrao" => $ippadrao
				)
			);
			
			$this->Ponto->create();
			if($this->Ponto->save($data)) {
				$this->Session->setFlash("Ponto de $tipo batido.");
			} else {
				$this->Session->setFlash("Ocorreram erros ao bater o ponto de $tipo");
				$this->Justificativa->delete($this->Justificativa->id);
			}
			$this->redirect(array("action"=>"index"));
			
		}
		
		
		function ver($id=null,$mes=null,$ano=null) {
		
			// Determina a data que será usada para filtrar
			// a visualização dos pontos batidos
			$dia = 1;
			if(is_null($mes)) {
				$mes = intval(date("m"));
			}
			if(is_null($ano)) {
				$ano = intval(date("Y"));
			}
			$dataAtual = date("Y-m-d",mktime(0,0,0,$mes,$dia,$ano));


			// Prepara a array que recebe todos os meses e anos
			// cadastrados na tabela de pontos
			$mesesAnos = array();
		
		
			if($id==null) {
				$id = $this->Session->read("Auth.Funcionario.id");
			}
			
			if($this->Session->read("Auth.Funcionario.admin")=="nao" && $id!=$this->Session->read("Auth.Funcionario.id")) {
				$this->redirect(array("action"=>"ver"));
			}
		
			if(!$id) {
				$this->Session->setFlash("ID do colaborador inválido");
				$this->redirect(array("controller"=>"funcionarios","action"=>"index"));
			} else {
				$pontos = $this->Ponto->find("all",array(
					"order"=>"Ponto.created",
					"conditions"=>array("funcionario_id"=>$id)
				));
				
				$this->loadModel("Funcionario");
				$this->Funcionario->recursive=-1;
				$funcionario = $this->Funcionario->findById($id);
				
			
				// Ordena a array de pontos, deixando todos em uma única key
				// do array separado por data	
				$pontos_arrumados = array();
				foreach($pontos as $ponto) {
					$timeData = strtotime($ponto['Ponto']['created']);
					$data = date("Y-m-d" ,$timeData);
					$dataponto = date("d/m/Y" ,$timeData);
					$diasemana = substr($this->_diaDaSemana(date("w" ,$timeData)),0,3);
					$hora = date("H:i" ,$timeData);
					
					$ponto['Ponto']['hora']=$hora;
					$ponto['Ponto']['dataponto']=$dataponto;
					$ponto['Ponto']['diasemana']=$diasemana;
					if(substr($ponto['Ponto']['tipo'],0,5)!="pausa") {
						$pontos_arrumados[$data][$ponto['Ponto']['tipo']] = $ponto['Ponto'];
					} else {
						$pontos_arrumados[$data][$ponto['Ponto']['tipo']][] = $ponto['Ponto'];
					}
				}
			
			
				// Carrega o modelo de justificativas
				$this->loadModel("Justificativa");
				$modJustificativas = $this->Justificativa->find("all",array(
					"conditions"=>array("funcionario_id"=>$id),
					"order"=>"Justificativa.created"
				));


				
				// Coloca as justificativas num Array por data
				$justificativas=array();
				foreach($modJustificativas as $just) {
					$data = $just['Justificativa']['created'];
					$justificativas[$data] = $just['Justificativa'];
				}
			
			
				// Contabiliza o banco de horas
				$extra_contabilizado = 0;
				foreach($pontos_arrumados as $dataPonto=>&$ponto) {
					$entrada = strtotime($ponto['entrada']['created']);
					$almoco_inicio = null;
					$almoco_fim = null;
					$saida = null;
					if(isset($ponto['almoco_inicio'])) {
						$almoco_inicio = strtotime($ponto['almoco_inicio']['created']);
					}
					if(isset($ponto['almoco_fim'])) {
						$almoco_fim = strtotime($ponto['almoco_fim']['created']);
					}
					if(isset($ponto['saida'])) {
						$saida = strtotime($ponto['saida']['created']);
					}

					$justificativa = $justificativas[date("Y-m-d",$entrada)];
					
					$expediente = $justificativa['expediente'];
					$extra = 0;
					
					$ponto['expediente'] = $expediente;
					$ponto['obs'] = $justificativa['obs'];
					$ponto['justificativa'] = $justificativa['justificativa'];
					
					if( (isset($ponto['entrada']) && isset($ponto['saida'])) && ($entrada!=$saida) && $ponto['justificativa']!="feriado" && $ponto['justificativa']!="atestado" ) {
						$periodo1 = 0;
						$periodo2 = 0;
						
						$loopData = date("Y-m-d",$entrada);
						$loopDataProcurada = "";
						
					
						if(isset($ponto['almoco_inicio']) && isset($ponto['almoco_fim'])) {
							if($loopData==$loopDataProcurada) {
								echo "Com almoço<br />";
							}
							$date_diff = $this->_timeDiff($entrada,$almoco_inicio);
							$periodo1 = $date_diff['minute'] + $date_diff['hour']*60;
					
							$date_diff = $this->_timeDiff($almoco_fim,$saida);
							$periodo2 = $date_diff['minute'] + $date_diff['hour']*60;
						} else {
							if($loopData==$loopDataProcurada) {
								echo "Sem almoço<br />";
							}
							$date_diff = $this->_timeDiff($entrada,$saida);
							$periodo1 = $date_diff['minute'] + $date_diff['hour']*60;

						}
	
						$periodo=$periodo1+$periodo2;
						$extra = $periodo-$expediente;
						
						if($loopData==$loopDataProcurada) {
							echo "Periodo 1: $periodo1 <br />";
							echo "Periodo 2: $periodo2 <br />";
							echo "Periodo Total: $periodo <br />";
							echo "Extra: $extra <br />";
						}
						
						$ponto['expediente_feito'] = $periodo;
						$ponto['extra'] = $extra;
						
						if($extra >= 0) {
							$timeextra = mktime(0 , $extra);
							$extra_string = date("H:i",$timeextra);
							$ponto['extra_string'] = $extra_string;
						} else {
							$timeextra = mktime(0 , $extra*-1);
							$extra_string = date("H:i",$timeextra);
							$ponto['extra_string'] = "-$extra_string";
						}
					} elseif($entrada==$saida) {
						// Verifica se é fim de semana
						if(date("w",$entrada)=="6" || date("w",$entrada)=="0" || $ponto['justificativa']=="feriado") {
							$extra = 0;
							$ponto['expediente_feito'] = 0;
							$ponto['extra'] = 0;				
							$ponto['extra_string'] = "-";
							$ponto['entrada']['hora']="-";
							$ponto['almoco_inicio']['hora']="-";
							$ponto['almoco_fim']['hora']="-";
							$ponto['saida']['hora']="-";
							
							$ponto['marcar'] = "fds";
						} else {
							$extra = $expediente*-1;
							$ponto['expediente_feito'] = 0;
							$ponto['extra'] = $extra;
	
							$timeextra = mktime(0 , $extra*-1);
							$extra_string = date("H:i",$timeextra);
							$ponto['extra_string'] = "-$extra_string";
							
							$ponto['marcar'] = "falta";
							if(empty($ponto['obs'])) {
								$ponto['marcar'] = "falta_s_justificativa";
							}
						}
					} else {
						$ponto['expediente_feito'] = 0;
						$ponto['extra'] = 0;				
						$ponto['extra_string'] = "00:00";
					}
					
					// Debug de horas extras
					//echo date("d/m/Y",$entrada) . ": " . $extra . "<br />";
					
					// Soma na contabilização a quantidade de horas extras
					// do dia dentro do loop
					$extra_contabilizado+=$extra;
					
					
					// Cria variaveis usadas para filtrar a visualização
					// de pontos
					$dataPontoTime = strtotime($dataPonto);
					$dataAtualTime = strtotime($dataAtual);
					$mesPonto = date("m",$dataPontoTime);
					$anoPonto = date("Y",$dataPontoTime);
					$mesAtual = date("m",$dataAtualTime);
					$anoAtual = date("Y",$dataAtualTime);

					
					// Cadastra no array de Meses e Anos
					// o mes e ano que esta passando pelo loop
					if(!isset($mesesAnos[$anoPonto."-".$mesPonto])) {
						$mesesAnos[$anoPonto."-".$mesPonto]=$anoPonto."-".$mesPonto;
					}

					
					// Verifica se esta passagem do loop esta no mes
					// e ano estabelecido para visualização
					if($mesPonto!=$mesAtual || $anoPonto!=$anoAtual) {
						//echo "Diferente $dataPonto X $dataAtual <br />";
						unset($pontos_arrumados[$dataPonto]);
					} else {
						//echo "Igual $dataPonto X $dataAtual <br />";
					}
				}
				
				$extra_contabilizado_string = null;
				if($extra_contabilizado<0) {
					$extra_contabilizado_string = "-" . $this->_BancoDeHoras2String($extra_contabilizado*-1);		
				} else {
					$extra_contabilizado_string = $this->_BancoDeHoras2String($extra_contabilizado); //date("d \d\i\a\s \e H:i",$timeextra_contabilizado);
				}
				
				$pontos_arrumados = array_reverse($pontos_arrumados);
				
				//debug($pontos_arrumados);
			
				$this->set("funcionario",$funcionario);
				$this->set("pontos",$pontos_arrumados);
				$this->set("extra_contabilizado_string",$extra_contabilizado_string);
				$this->set("mesesAnos",$mesesAnos);
			}
		}
		
		
		function edit($id=null) {
			if (!$id && empty($this->data)) {
				$this->Session->setFlash(__('Ponto inválido', true));
				$this->redirect(array('action'=>'index'));
			}
			if (!empty($this->data)) {
			
				// Arruma a hora do created
				$pontoPreEditado = $this->Ponto->read(null,$id);
				
				$tc=strtotime($pontoPreEditado['Ponto']['created']);
				$dataHoraCreated = date("Y-m-d H:i:s", mktime( 
					intval($this->data['Ponto']['created']['hour']),
					intval($this->data['Ponto']['created']['min']),
					intval(date("s",$tc)),
					intval(date("m",$tc)),
					intval(date("d",$tc)),
					intval(date("Y",$tc))
				));
				
				// Muda na variavel $this->data a hora do created
				$this->data['Ponto']['created']=$dataHoraCreated;
				
				if ($this->Ponto->save($this->data)) {
				//if ($this->Ponto->query("UPDATE pontos SET created")) {
					$pontoEditado = $this->Ponto->read();
					
					$this->Session->setFlash(__('Ponto salvo', true));
					$this->redirect(array('action'=>'ver',$pontoEditado['Funcionario']['id'], date("m",strtotime($pontoEditado['Ponto']['created'])), date("Y",strtotime($pontoEditado['Ponto']['created'])) ));
				} else {
					$this->Session->setFlash(__('O ponto não pode ser salvo. Tente novamente.', true));
				}
			}
			if (empty($this->data)) {
				$this->data = $this->Ponto->read(null, $id);
			}
		}
		

		function _BancoDeHoras2String($intmin) {
			$horas = 0;
			$minutos = 0;
			
			$minutos = $intmin%60;
			$horas = intval($intmin/60);
			
			return str_pad($horas, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutos, 2, "0", STR_PAD_LEFT);
		}

		function _timeDiff($ts1, $ts2) {
			if($ts1 < $ts2) {
				$temp = $ts1;
				$ts1 = $ts2;
				$ts2 = $temp;
			}
			$format = 'Y-m-d H:i:s';
			$ts1 = date_parse(date($format, $ts1));
			$ts2 = date_parse(date($format, $ts2));
			$arrBits = explode('|', 'year|month|day|hour|minute|second');
			$arrTimes = array(0, 12, date("t", $temp), 24, 60, 60);
			foreach ($arrBits as $key => $bit) {
				$diff[$bit] = $ts1[$bit] - $ts2[$bit];
				if ($diff[$bit] < 0) {
					$diff[$arrBits[$key - 1]]--;
					$diff[$bit] = $arrTimes[$key] - $ts2[$bit] + $ts1[$bit];
				}
			}
			return $diff;
		}
	}
?>