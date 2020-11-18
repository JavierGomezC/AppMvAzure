<?php 
  //include $_SERVER["DOCUMENT_ROOT"].'/sesion_redis.php'; 
  if(isset($_SESSION['autenticado']) && $_SESSION['url_sesion'] == $_SERVER['HTTP_HOST'])
  {
    try
    {

      require_once '../modelo/Base.php';
      //require_once '../modelo/Rol.php';
      //$rol= new Rol();
      //$rol_superadmin= $rol->getRolByCanonical('super-administrador');
      //if($_SESSION['id_rol'] != $rol_superadmin['id']){
        require_once '../modelo/Utilidades.php';
        $modulos=Utilidades::getAllModulos();
        $id_modulo=array_search($_SESSION['my_modulo'], array_column($_SESSION['modulos'], 'canonical'));
        if($id_modulo!==false){
            if($modulos[$id_modulo]['estado']==0){
              header('Content-Type: application/json');
              echo json_encode(array('err' => "Consulta no permitida, refresque la página por favor."));
              exit;
            }
        }
        else
          header('Location: index.php');
      //}

      if($_POST['accion'] == "buscar") //buscar canales
      {
          $twig = iniciar(false);
          $canal = new Canal();
		  
		  if(isset($_POST['num_identidad']))
		  {
			$qry_canal=$canal->getDetalleCanalByNumIdentidad($_POST['num_identidad']);
			if(!empty($qry_canal))
			{
				if($_POST['modulo'] == 'canal')
				{
					$qry_abonado['estructura']=json_decode($qry_abonado['estructura']);
					$data = array('ok' => "Abonado encontrado correctamente", 'abonado'=> $qry_abonado);
					
					require_once $_SERVER['DOCUMENT_ROOT']."/modelo/Contrato.php";
					$contrato= new Contrato();
					$qry_contrato=$contrato->getDetalleContratosByAbonado($qry_abonado['id']);
					if(!empty($qry_contrato))
					  $data['contratos']= $qry_contrato;
				  
				    require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Lugar.php';
				    $lugar=new Lugar();
				    $qry_direcciones=$lugar->getDireccionesByPersona($qry_abonado['id_persona']);
					$data['direcciones']= $qry_direcciones;
					
				    require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Crm.php';
			        $crm=new Crm();
			        $qry_crms=$crm->getListadoCrmsByAbonado($qry_abonado['id']);
			        $data['crms']= $qry_crms;
					
				    date_default_timezone_set('America/Bogota');
                    $data['fecha_hoy']= date("Y-m-d H:i:s");
				} 
			    else if($_POST['modulo'] == 'etd' || $_POST['modulo'] == 'contrato' || $_POST['modulo'] == 'orden')
				{
				  require_once $_SERVER['DOCUMENT_ROOT']."/modelo/Estado.php";
				  $estado= new Estado();
				  $id_estado=$estado->getEstadoByNombre('Activado');
				  
				  if($qry_abonado['id_estado'] == $id_estado)
				  {
					 require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Lugar.php';
				     $lugar=new Lugar();
				     $qry_direcciones=$lugar->getDireccionesByPersona($qry_abonado['id_persona']);
					 $qry_abonado['estructura']=json_decode($qry_abonado['estructura']);
					 $data = array('ok' => "Abonado encontrado correctamente", 'direcciones'=> $qry_direcciones, 'abonado'=> $qry_abonado);
				  }
			      else
				  {
					$data = array('info' => 'Abonado no se encuentra <b style="color:yellow">Activado</b> actualmente');
					if($_POST['modulo'] == 'contrato')
					  $data['abonado']=$qry_abonado;
				  }
				}
				else if($_POST['modulo'] == 'solicitud')
				{
					require_once $_SERVER['DOCUMENT_ROOT']."/modelo/Estado.php";
					$estado= new Estado();
					$id_estado=$estado->getEstadoByNombre('Activado');
					  
					if($qry_abonado['id_estado'] == $id_estado)
					{
					    $qry_abonado['estructura']=json_decode($qry_abonado['estructura']);
						$data = array('ok' => "Abonado encontrado correctamente", 'abonado'=> $qry_abonado);
						
						require_once $_SERVER['DOCUMENT_ROOT']."/modelo/Contrato.php";
						$contrato= new Contrato();
						$qry_contrato=$contrato->getDetalleContratosByAbonado($qry_abonado['id']);
						if(!empty($qry_contrato))
						  $data['contratos']= $qry_contrato;
					  
						require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Lugar.php';
						$lugar=new Lugar();
						$qry_direcciones=$lugar->getDireccionesByPersona($qry_abonado['id_persona']);
						$data['direcciones']= $qry_direcciones;
						
						date_default_timezone_set('America/Bogota');
						$data['fecha_hoy']= date("Y-m-d H:i:s");
					}
					else
					{
					   $data = array('info' => 'Abonado no se encuentra <b style="color:yellow">Activado</b> actualmente');
					   if($_POST['modulo'] == 'contrato')
						 $data['abonado']=$qry_abonado;
					}
				}
			}
		    else
		      $data = array('info' => "No aparece ningún Abonado registrado con ese número de identidad en el sistema");
		  }
		  else if(isset($_POST['criterio']))
		  {
			  $qry_abonados=$abonado->getListadoByCriterio($_POST);
			  if(!empty($qry_abonados))
				$data = array('ok' => "Abonados encontrados correctamente", 'abonados'=> $qry_abonados);  
			  else
				$data = array('info' => "No se han encontrado Abonados con ese criterio");
		  }
		  else if(isset($_POST['id_abonado']))
		  {
			$qry_abonado=$abonado->getDetalleAbonado($_POST['id_abonado']);
			if(!empty($qry_abonado))
			{
				$qry_abonado['estructura']=json_decode($qry_abonado['estructura']);
				
				require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Lugar.php';
				$lugar=new Lugar();
				$qry_direcciones=$lugar->getDireccionesByPersona($qry_abonado['id_persona']);
				$data = array('ok' => "Abonado encontrado correctamente", 'abonado'=> $qry_abonado, 'direcciones'=> $qry_direcciones);
				
				require_once $_SERVER['DOCUMENT_ROOT']."/modelo/Contrato.php";
				$contrato= new Contrato();
				$qry_contrato=$contrato->getDetalleContratosByAbonado($qry_abonado['id']);
				if(!empty($qry_contrato))
				  $data['contratos']= $qry_contrato;
			  
			    require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Crm.php';
		        $crm=new Crm();
		        $qry_crms=$crm->getListadoCrmsByAbonado($qry_abonado['id']);
		        $data['crms']= $qry_crms;
				
			    date_default_timezone_set('America/Bogota');
                $data['fecha_hoy']= date("Y-m-d H:i:s");
			}
		    else
		      $data = array('info' => "No aparece ningún Abonado registrado en el sistema");
		  }
		  else
          {
			$qry_abonados=$abonado->obtenerTodo(array('orderBy'=>'fecha_creado'));
            $data = array('ok' => "Abonados encontrados correctamente", 'abonados'=> $qry_abonados);
		  }
          
		  header('Content-Type: application/json');
          echo json_encode($data);
      }
      else if($_POST['accion'] == "add")
      {
          $twig = iniciar(false);
		  header('Content-Type: application/json');
		  
          require_once $_SERVER['DOCUMENT_ROOT']."/modelo/Estado.php";
		  $estado= new Estado();
		
          $abonado = new Abonado();
		  if($_POST['modulo'] == 'etd' || $_POST['modulo'] == 'contrato')
			$_POST['id_estado']=$estado->getEstadoByNombre('Activado');
		  else
		  {
  		    $id_retirado=$estado->getEstadoByNombre('Retirado');
			if($_POST['id_estado'] == $id_retirado)
			  exit(json_encode(array('info' => "Un Abonado no se puede crear con un estado: Retirado, verifique su estado")));
		  }
		 
		  $datos_abon=$abonado->adicionar($error,$_POST);
          if(!is_null($error))
            $data = array('err' => $error);
          else
          {
			$qry_abonado=$abonado->getDetalleAbonado($datos_abon['id_abonado']);
			$data = array('ok' => "Abonado adicionado correctamente", 'abonado'=> $qry_abonado, 'new_dir'=> $datos_abon['new_dir']);
		  }  
		  
          echo json_encode($data);
      }
      else if($_POST['accion'] == "mod")
      {
		  header('Content-Type: application/json');
          $twig = iniciar(false);
          $abonado = new Abonado();
		  
		  if(isset($_POST['id_estado']))
		  {
		     switch($_POST['id_estado'])
			 {
				case '1':  //Activo
				case '10': //Reportado
				  $new_dir=$abonado->actualizar($error,$_POST);
				  if(!is_null($error))
					 exit(json_encode(array('err' => $error)));
				  else
				  {
				    if(!is_bool($new_dir))
					 $data['new_dir'] = $new_dir;
				  }
				  break;
				case '5':  //Retirado
				  if(isset($_POST['transferir'])  && $_POST['transferir'] == true)
				  {
					//Realizar el proceso de transferencia de contrato a otro abonado  
				  }
				  else
				  {
					require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Contrato.php';
					$contrato= new Contrato();
					
					$conBD = BDManager::getBDManager()->getConectionBD();
					$conBD->beginTransaction();
					$new_dir=$abonado->actualizar($error,$_POST,true);
					if(!is_null($error))
					  exit(json_encode(array('err' => $error)));
					else
					{
					  if(!is_bool($new_dir))
			            $data['new_dir'] = $new_dir;
			
					  $qry_contratos_abonado=$contrato->getDetalleContratosByAbonado($_POST['id']);
					  if(!empty($qry_contratos_abonado))
					  {
						require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Estado.php';
			            $estado= new Estado();
						
						require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Cablemodem.php';
						$modem = new Cablemodem();
						
						require_once '../modelo/SetTopBox.php';
                        $stb = new SetTopBox();

						foreach($qry_contratos_abonado as $contrato_abonado)
						{
						   switch($contrato_abonado['estado'])
						   {
							  case 'Activado':
							  case 'Suspendido':
							    $stb_asig=null;
							    $qry_modem_contrato=$modem->buscarAprovisionamientoByIdContrato($contrato_abonado['id']);
							    
							    if ($qry_modem_contrato['id_servicio'] == 1) //Internet
								    $_POST['id_dev']=2;
								else if ($qry_modem_contrato['id_servicio'] == 2) //television digital
								{
								    $_POST['id_dev']=3;
								    $qry_aprov_stb = $modem->buscarAprovisionamientoByIdContratoSTB($contrato_abonado['id']);
									$stb_asig=$stb->buscarByID($contrato_abonado['id_cablemodem']); 
									$stb_asig_maestro=$stb->buscarByID($stb_asig['id_stb_maestro']); 
									$stb_asig['stb_maestro']=$stb_asig['id_smartcard'];
								    if($stb_asig_maestro['id_stb_maestro'])
								      $stb_asig['stb_maestro']=$stb_asig_maestro['id_stb_maestro'];
								}
							    
								$info_est_contr=array(
								   'id_estado'=> $estado->getEstadoByNombre('Terminado'),
								   'id' => $contrato_abonado['id']
								);
									
								$contrato->actualizarEstado($error,$info_est_contr,true);
								if(!is_null($error))
								  exit(json_encode(array('err' => $error)));
								
								if ($qry_modem_contrato['id_servicio'] == 1) 
					            {
									$info_aprovisionamiento= array(
										'mac' => $qry_modem_contrato['mac'],
										//'ip' => $qry_modem_contrato['ip_privada'],
										'validacion' => $qry_modem_contrato['validacion'],
										'dhcp_dinamico' => $qry_modem_contrato['dhcp_dinamico'],
							            'dhcp_estatico' => $qry_modem_contrato['dhcp_estatico']
									);
								
									if(!empty($qry_modem_contrato['ip_privada'])) //Ip Estatico
									   $info_aprovisionamiento['ip']=$qry_modem_contrato['ip_privada'];
									else if(!empty($qry_modem_contrato['ip_agencia'])) //Ip Estatico redireccionamiento
									   $info_aprovisionamiento['ip']=$qry_modem_contrato['ip_agencia'];
									else
	                              	  $info_aprovisionamiento['ip']=null;
								
									if(!empty($qry_modem_contrato['puertos']))
									{
									   $query_ip=$conBD->prepare('SELECT ip_agencia.ip as ip_agencia,puerto_cablemodem_contrato.puerto_agencia,ip_estaticas.ip as ip_priv,puerto_cablemodem_contrato.puerto_privado FROM cablemodem_contrato INNER JOIN puerto_cablemodem_contrato ON puerto_cablemodem_contrato.id_cablemodem_contrato=cablemodem_contrato.id INNER JOIN ip_agencia ON puerto_cablemodem_contrato.id_ip_agencia=ip_agencia.id INNER JOIN ip_estaticas ON puerto_cablemodem_contrato.id_ip_privada=ip_estaticas.id WHERE puerto_cablemodem_contrato.id_cablemodem_contrato=:id');
									   $query_ip->bindParam(':id', $qry_modem_contrato['id']);
									   $query_ip->execute();
									   $info_puertos=$query_ip->fetchAll(PDO::FETCH_ASSOC);
									   $info_aprovisionamiento['info_puertos']=$info_puertos;
									}
									else
									  $info_aprovisionamiento['info_puertos']=null;
								  
									if(!empty($qry_modem_contrato))
								    {
									  $qry_abonado=$abonado->getDetalleAbonado($_POST['id']);
					                  $modem->eliminarAprovisionamiento($error,$contrato_abonado,$qry_abonado,$qry_modem_contrato,$stb_asig,$_POST,true);
					                  if(!is_null($error))
			                             exit(json_encode(array('err' => $error)));   
								    }
								
									$info=$modem->retirarDHCPyBinAprovisionamiento($error,$info_aprovisionamiento);

									if(isset($info['data_remoto']))
									  $data['estado_sinc']=$info['data_remoto']['estado_sincronizacion'];
							  
									if(!is_null($error))
									{
										if(isset($error['tipo']) && $error['tipo'] == 'acceso_remoto')
										{
										   $data['tipo_err']="acceso_remoto";
										   $data['err']=$error['err'];
										   if($data['estado_sinc'] != 'vacio')
											$data['ok']="Abonado modificado correctamente. Esto ha sido registrado en la base de datos, pero NO a nivel de archivos bin y dhcp en el servidor. Ver la notificación de error siguiente.";
										}
										else
										  exit(json_encode(array('err' => $error)));
									}
									else
									{
										//Realizar snmp para reiniciar el cablemodem cuando se elimina el DHCP y Bin
										require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Cmts.php';
										require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Snmp.php';
									    include $_SERVER['DOCUMENT_ROOT'].'/archivos/datos_app/var_remoto.php';
										
										$cmts= new Cmts();
										$snmp= new SnmpDev();
										$info_cmts=$cmts->getDetalleBasicoCmtsByMacEtd($qry_modem_contrato['mac']);
										$qry_cmts=$cmts->getDetalleRouterBoardByCmts($qry_modem_contrato['id_cmts']);
								
								        if(!empty($qry_modem_contrato['ip_privada']))
										{
										  $error=NULL;
										  $modem->retirarReglasAprovisionamiento($error,$qry_cmts,$info_aprovisionamiento);
										  if(!is_null($error))
											$data['info']=$error;
										}
										  
										if(is_null($ip_remoto_puerto))
										  $snmp->reiniciarETD($error,$info_cmts['ip'],$qry_modem_contrato['mac']);
										else
										{
											$info['accion']='eject-attr-snmp';
											$info['ip_remoto']=$ip_remoto_puerto;
											$info['ip_cmts']=$info_cmts['ip'];
											$info['mac_etd']=$qry_modem_contrato['mac'];
											$url="http://".$ip_remoto_puerto."/busq_dev_snmp_server.php";
										
											$resp=Utilidades::accesoRemoto($error,$url,$info);
										}
										
										if(!is_null($error))
										{
											if(is_array($error))
											{
												$data['err']="No es posible Reiniciar el ETD";
												if(isset($error['err']))
										          $data['err'].=": ".$error['err'];
											}
											else
			                                   $data['err']="No es posible Reiniciar el ETD: ".$error;
										}
									}
								}
								else if ($qry_modem_contrato['id_servicio'] == 2) 
								{
                                    $modem->eliminarAprovisionamiento($error,$contrato_abonado,$qry_abonado,$qry_aprov_stb,$stb_asig,$_POST,true);
							        if(!is_null($error))
							          exit(json_encode(array('err' => $error)));

					                require_once 'app_cas_dvbvision.php';
					                $accion_cas = 'elm_stb';
					                $datos['stbid'] = $qry_aprov_stb['id_smartcard'];
					                $rta_cablebox = ejecutarAccionCas($accion_cas, $datos);

					                if (isset($rta_cablebox['err'])) {
					                    echo json_encode($rta_cablebox);
					                    die;
					                }
								}
							    break;
							  case 'Aprovisionado':
							    $stb_asig=null;
							    $qry_modem_contrato=$modem->buscarAprovisionamientoByIdContrato($contrato_abonado['id']);
								if(!empty($qry_modem_contrato))
							    {
							      if ($qry_modem_contrato['id_servicio'] == 1) 
							      	$_POST['id_dev']=2;
							      else if ($qry_modem_contrato['id_servicio'] == 2) 
							      {
									 $_POST['id_dev']=3;
								     $qry_aprov_stb = $modem->buscarAprovisionamientoByIdContratoSTB($qry_modem_contrato['id']);
									 $stb_asig=$stb->buscarByID($qry_aprov_stb['id_cablemodem']); 
									 $stb_asig_maestro=$stb->buscarByID($stb_asig['id_stb_maestro']); 
									 $stb_asig['stb_maestro']=$stb_asig['id_smartcard'];
								     if($stb_asig_maestro['id_stb_maestro'])
								       $stb_asig['stb_maestro']=$stb_asig_maestro['id_stb_maestro'];
							      }
							      
								  $qry_abonado=$abonado->getDetalleAbonado($_POST['id']);
				                  $modem->eliminarAprovisionamiento($error,$contrato_abonado,$qry_abonado,$qry_modem_contrato,$stb_asig,$_POST,true);
				                  if(!is_null($error))
		                             exit(json_encode(array('err' => $error))); 
                                  
                                  require_once '../modelo/Estado.php';
								  $estado= new Estado();
									 
								  $id_estado_oper=$estado->getEstadoByNombre('Operativo');
								  $fecha=$modem->eliminarPreactivacion($error,array('id_cablemodem' => $qry_modem_contrato['id_cablemodem'], 'id_estado' => $id_estado_oper)); 
								
								  if ($qry_modem_contrato['id_servicio'] == 1) 
					              {
					                  $info_preactivacion= array(
										 'id_cablemodem' => $qry_modem_contrato['id_cablemodem'],
										 'mac' => $qry_modem_contrato['mac'],
										 'dhcp_dinamico' => 'etd_preactivos',
										 //'tftp' => 'tftpboot_preactv',
									  );
									  						
									  $info=$modem->retirarDHCPyBinPreactivacion($error,$info_preactivacion);
									  if(isset($info['data_remoto']))
										$data['estado_sinc']=$info['data_remoto']['estado_sincronizacion'];
									  
									  if(!is_null($error))
									  {
										  if(isset($error['tipo']) && $error['tipo'] == 'acceso_remoto')
										  {
											 $data['tipo_err']="acceso_remoto";
											 $data['err']=$error['err'];
											 if($data['estado_sinc'] != 'vacio')
											   $data['ok']="El Abonado se ha Retirado correctamente y los Cablemodems todavia no han quedado disponibles para Aprovisionar. Esto es debido a que está registrado en la base de datos, pero NO a nivel de archivos bin y dhcp en el servidor. Ver la notificación de error siguiente.";
											 else
											   $data['ok']='El Abonado se ha Retirado correctamente y los Cablemodems han quedado disponibles para Aprovisionar.';
										  }
										  else
										    exit(json_encode(array('err' => $error)));
									  }
									  else							
									  {
										  //Realizar snmp para reiniciar el cablemodem cuando se elimina el DHCP y Bin
										  require_once '../modelo/Cmts.php';
										  require_once '../modelo/Snmp.php';
										  include '../archivos/datos_app/var_remoto.php';
											
										  $cmts= new Cmts();
										  $snmp= new SnmpDev();
										  $info_cmts=$cmts->getDetalleBasicoCmtsByMacEtd($qry_modem_contrato['mac']);
										  
										  if(is_null($ip_remoto_puerto))
										    $snmp->reiniciarETD($error,$info_cmts['ip'],$qry_modem_contrato['mac']);
										  else
										  {
											 $info['accion']='eject-attr-snmp';
											 $info['ip_remoto']=$ip_remoto_puerto;
											 $info['ip_cmts']=$info_cmts['ip'];
											 $info['mac_etd']=$qry_modem_contrato['mac'];
											 $url="http://".$ip_remoto_puerto."/busq_dev_snmp_server.php";
											
											 $resp=Utilidades::accesoRemoto($error,$url,$info);
										  }
											
										  if(!is_null($error))
										  {
											 if(is_array($error))
											 {
												$data['err']="No es posible Reiniciar el ETD";
												if(isset($error['err']))
										          $data['err'].=": ".$error['err'];
											 }
											 else
			                                   $data['err']="No es posible Reiniciar el ETD: ".$error;
										  }
									  }			
					              }
					              else if ($qry_modem_contrato['id_servicio'] == 2) 
					              {

					                require_once 'app_cas_dvbvision.php';
					                $accion_cas = 'elm_stb';
					                $datos['stbid'] = $qry_modem_contrato['id_smartcard'];
					                $rta_cablebox = ejecutarAccionCas($accion_cas, $datos);

					                if (isset($rta_cablebox['err'])) 
					                   exit(json_encode(($rta_cablebox)));
					              }
							    }
							  case 'Transferencia':
							  case 'Preaprovisionado':
							    $info_est_contr=array(
								   'id_estado'=> $estado->getEstadoByNombre('Anulado'),
								   'id' => $contrato_abonado['id']
								);
									
								$contrato->actualizarEstado($error,$info_est_contr,true);
								if(!is_null($error))
								  exit(json_encode(array('err' => $error)));
							    
							    break;
							  case 'Terminado':
							  case 'Anulado':
							    break;
						   }
						}							
					  }
					}
					
					$conBD->commit();
				  }
				  break;
			 }

			 if(!isset($data['ok']))
			  $data['ok'] = "Abonado modificado correctamente";
		  }
		  else
		  {
			$new_dir=$abonado->actualizar($error,$_POST);
			if(!is_null($error))
			   exit(json_encode(array('err' => $error)));
			else
			{
			   $data = array('ok' => "Abonado modificado correctamente");
			   if(!is_bool($new_dir))
			    $data['new_dir'] = $new_dir;
			}
		  }

          date_default_timezone_set('America/Bogota');
          $data['fecha_hoy']= date("Y-m-d H:i:s");
          echo json_encode($data);
      }
      else if($_POST['accion'] == "elm")
      {
          iniciar(false);
          $abonado = new Abonado();
		  
		  if(!$abonado->hasAprovisionamiento($_POST['id']))
		  {
		    $abonado->eliminar($error,$_POST['id']);
		    $data = array('ok' => "Abonado eliminado correctamente");
		  }
		  else
		   $data = array('info' => "Abonado no se puede eliminar, tiene Contratos aprovisionados.");
		  
          header('Content-Type: application/json');
          echo json_encode($data); 
      }
    }
    catch(PDOException $er)
    {
        $data = array('err' => "Se ha generado un error");
        date_default_timezone_set('America/Bogota');
        $idf=fopen($_SERVER['DOCUMENT_ROOT'].'/archivos/log/logErroresApp.log','a');
        fwrite($idf,date("Y-m-d h:i:a")." Codigo: ".$er->getCode()."  "."Descripcion: ".$er->getMessage()." Archivo: ".$er->getFile()." Linea: ".$er->getLine().chr(13).chr(10));
        fclose($idf);
		chmod($_SERVER['DOCUMENT_ROOT'].'/archivos/log/logErroresApp.log', 0777);
        header('Content-Type: application/json');        
        echo json_encode($data);      
    }   
    catch(Exception $er)
    {
        $data = array('err' => "Se ha generado un error");
        date_default_timezone_set('America/Bogota');
        $idf=fopen($_SERVER['DOCUMENT_ROOT'].'/archivos/log/logErroresApp.log','a');
        fwrite($idf,date("Y-m-d h:i:a")." Codigo: ".$er->getCode()."  "."Descripcion: ".$er->getMessage()." Archivo: ".$er->getFile()." Linea: ".$er->getLine().chr(13).chr(10));
        fclose($idf);
		chmod($_SERVER['DOCUMENT_ROOT'].'/archivos/log/logErroresApp.log', 0777);
        header('Content-Type: application/json');
        echo json_encode($data);
    } 
  }
  else
    header('Location: /index.php');

    function iniciar($vista_twig=true)
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/BDManager.php';
		require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Base.php';
        require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/Abonado.php';
          
        if($vista_twig)
        {
            require_once $_SERVER['DOCUMENT_ROOT'].'/librerias_backend/twig/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();

            $loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'].'/vistas');
            return new Twig_Environment($loader);  
        }
        
    }
 ?>

