<?php

/**
 * Clase encargada de la gestion de un Abonado en el sistema
 * 20/11/2014
 */
 
if(!class_exists('Utilidades'))
  require_once 'Utilidades.php';

class Canal extends Base
{

  public function __construct()
  {   
      parent::__construct();
  }

  public function adicionar(&$error, array $datos, $exist_dbtransaction=false)
  {
    $error=NULL;
    try
    {

    }
    catch(PDOException $er)
    {
	   $this->conBD->rollBack();
	   if($er->getCode() =="23000" && substr_count($er->getMessage(), "abonado_codigo_idx"))
        $error= 'No es necesario adicionar el abonado: <b style="color:yellow">"'.$num_identidad.'"</b>, el Código: "'.$datos['codigo'].'" aparece asignado a otro abonado.';
       else if($er->getCode() =="23000" && substr_count($er->getMessage(), "abonado_correo_idx"))
        $error= 'No es posible adicionar el abonado: el correo electrónico se encuentra asignado a otro abonado.';
	   else if($er->getCode() =="23000" && substr_count($er->getMessage(), "persona_numidentidad_idx"))
        $error= 'No es necesario adicionar el abonado: <b style="color:yellow">"'.$num_identidad.'"</b>, ya aparece registrado en el sistema.';
	   else
        throw $er;
    }
	catch(Exception $er)
    {
	   throw $er;
	}
  }

  public function adicionar1(&$error, array $datos, $exist_dbtransaction=false)
  {
    $error=NULL;
    try
    {
		$num_identidad=utf8_encode(trim(utf8_decode($datos['num_identidad'])));
		$query=$this->conBD->prepare('SELECT abonado.id FROM abonado INNER JOIN persona ON abonado.id_persona=persona.id WHERE persona.num_identidad = :ced');
	    $query->bindParam(':ced', $num_identidad);
	    $query->execute();
	    $abonado=$query->fetch(PDO::FETCH_NUM);
	    if(!empty($abonado))
		{
		  $error= 'No es necesario adicionar el Abonado: <b style="color:yellow">"'.$num_identidad.'"</b>, ya aparece registrado en el sistema.';
		  return false;
		}
		
		$query=$this->conBD->prepare('SELECT persona.id FROM persona WHERE persona.num_identidad = :ced');
	    $query->bindParam(':ced', $num_identidad);
	    $query->execute();
	    $persona=$query->fetch(PDO::FETCH_NUM);
		
		if(!$exist_dbtransaction)
		 $this->conBD->beginTransaction();
	 
	    if(empty($persona))
		{
		   $query=$this->conBD->prepare('INSERT INTO persona (nombres,apellidos,num_identidad,telf_fijo,telf_movil) VALUES (:nomb,:apell,:ced,:telf,:telm)');
		   $nombres=utf8_encode(trim(utf8_decode($datos['nombres'])));
		   $query->bindParam(':nomb', $nombres);
		   $apellidos=utf8_encode(trim(utf8_decode($datos['apellidos'])));
		   if(empty($apellidos))
	         $apellidos=null;
		   $query->bindParam(':apell', $apellidos);
		   $query->bindParam(':ced', $num_identidad);
		   $telf_fijo=utf8_encode(trim(utf8_decode($datos['telf_fijo'])));
		   if(empty($telf_fijo))
	         $telf_fijo=null;
		   $query->bindParam(':telf', $telf_fijo);
		   $telf_movil=utf8_encode(trim(utf8_decode($datos['telf_movil'])));
		   if(empty($telf_movil))
	         $telf_movil=null;
		   $query->bindParam(':telm', $telf_movil);
		   $query->execute();
		   $id_persona=$this->conBD->lastInsertId();
		}
		else
	    {
		   $query=$this->conBD->prepare('UPDATE persona SET nombres=:nomb,apellidos=:apell,telf_fijo=:telf,telf_movil=:telm WHERE persona.id=:pers');
		   $query->bindParam(':pers', $persona[0]);
		   $nombres=utf8_encode(trim(utf8_decode($datos['nombres'])));
		   $query->bindParam(':nomb', $nombres);
		   $apellidos=utf8_encode(trim(utf8_decode($datos['apellidos'])));
		   if(empty($apellidos))
	         $apellidos=null;
		   $query->bindParam(':apell', $apellidos);
		  
		   $telf_fijo=utf8_encode(trim(utf8_decode($datos['telf_fijo'])));
		   if(empty($telf_fijo))
	         $telf_fijo=null;
		   $query->bindParam(':telf', $telf_fijo);
		   $telf_movil=utf8_encode(trim(utf8_decode($datos['telf_movil'])));
		   if(empty($telf_movil))
	         $telf_movil=null;
		   $query->bindParam(':telm', $telf_movil);
		   $query->execute();
		  
		   $id_persona=$persona[0];
		}  
		
		if(!empty($datos['struct_direccion']))
		{
		    $struct_dir=$datos['struct_direccion'];
			$query=$this->conBD->prepare('INSERT INTO direccion (id_ciudad,direccion,direccion_abreviada,estructura,longitud,latitud) VALUES (:ciu,:dir,:dir_abr,:str,:lon,:lat)');
			$query->bindParam(':ciu', $struct_dir['id_ciudad']);
			$query->bindParam(':dir', $struct_dir['dir_completa']);
			$query->bindParam(':dir_abr', $struct_dir['dir_nomenclatura']);
			$longitud=utf8_encode(trim(utf8_decode($datos['longitud'])));
			if(empty($longitud))
			  $longitud=null;
			$query->bindParam(':lon', $longitud);
			
			$latitud=utf8_encode(trim(utf8_decode($datos['latitud'])));
			if(empty($latitud))
			  $latitud=null;
			$query->bindParam(':lat', $latitud);
				
			$struct=json_encode($struct_dir['tipo']);
			$query->bindParam(':str', $struct);
			$query->execute();
			$id_direccion=$this->conBD->lastInsertId();
			
			$query=$this->conBD->prepare('INSERT INTO persona_direccion (id_direccion,id_persona,reside) VALUES (:dir,:pers,1)');
			$query->bindParam(':dir', $id_direccion);
			$query->bindParam(':pers', $id_persona);
			$query->execute();
			
			foreach($struct_dir['tipo'] as $tipo_dir)
			{
			   $query=$this->conBD->prepare('INSERT INTO detalle_direccion (id_direccion,id_nomenclatura,valor,canonical) VALUES (:dir,:nomc,:vlr,:can)');
			   $query->bindParam(':dir', $id_direccion);
			   $tp_nmc_dir=utf8_encode(trim(utf8_decode($tipo_dir['tipo_dir'])));
			   $query->bindParam(':nomc',$tp_nmc_dir);
			   $vlr_nmc_dir=utf8_encode(trim(utf8_decode($tipo_dir['tipo_dir_valor'])));
			   $query->bindParam(':vlr',$vlr_nmc_dir);
			   $canonical=Utilidades::normalizarTexto($vlr_nmc_dir);
			   $query->bindParam(':can',$canonical);
			   $query->execute();
			}	
		}
		else
		{
		   $id_direccion=$datos['id_direccion_residencia'];
           
           if(empty($persona))
		   {
              $query=$this->conBD->prepare('INSERT INTO persona_direccion (id_direccion,id_persona,reside) VALUES (:dir,:pers,1)');
			  $query->bindParam(':dir', $id_direccion);
			  $query->bindParam(':pers', $id_persona);
			  $query->execute();

		   }
		}
		
        $query=$this->conBD->prepare('INSERT INTO abonado (id_persona,codigo_migracion,id_direccion_residencia,estrato,correo,id_estado,precinto,fecha_creado,fecha_mod) VALUES (:pers,:cod,:dir,:estr,:eml,:est,:prec,:fch,:fchm)');
        $query->bindParam(':pers', $id_persona);
		$codigo=utf8_encode(trim(utf8_decode($datos['cod_migracion'])));
		if(empty($codigo))
		  $codigo=null;
        $query->bindParam(':cod', $codigo);
		$estrato=utf8_encode(trim(utf8_decode($datos['estrato'])));
		$query->bindParam(':estr', $estrato);
        $query->bindParam(':dir', $id_direccion);
		$correo=utf8_encode(trim(utf8_decode($datos['correo'])));
		if(empty($correo))
	      $correo=null;
        $query->bindParam(':eml', $correo);
		$id_estado=utf8_encode(trim(utf8_decode($datos['id_estado'])));
        $query->bindParam(':est', $id_estado);
		$precinto=utf8_encode(trim(utf8_decode($datos['precinto'])));
		if(empty($precinto))
	      $precinto=null;
        $query->bindParam(':prec', $precinto);
        date_default_timezone_set('America/Bogota');
        $fecha_creado = date("Y-m-d H:i:s");
        $query->bindParam(':fch', $fecha_creado);
		$query->bindParam(':fchm', $fecha_creado);
        $query->execute();
		$id_abonado=$this->conBD->lastInsertId();
		
		$info_historial=array(
		   'id_abonado' => $id_abonado,
		   'num_identidad' => $num_identidad,
		   'id_estado' => $id_estado,
		   'nombres' => $nombres,
		   'apellidos' => $apellidos,
		   'direccion' => isset($struct_dir['dir_completa'])?$struct_dir['dir_completa']:$datos['dir_completa'],
		   'telf_fijo' => $telf_fijo,
		   'telf_movil' => $telf_movil,
		   'estrato' => $estrato,
		   'longitud' => $longitud,
		   'latitud' => $latitud,
		   'correo' => $correo,
		   'precinto' => $precinto
		);
		   
		$this->crearHistorialAbonado($info_historial,"adicionar");
		
		if(!$exist_dbtransaction)
		  $this->conBD->commit();
	  
		return array('id_abonado' => $id_abonado, 'new_dir' => $id_direccion);
    }
    catch(PDOException $er)
    {
	   $this->conBD->rollBack();
	   if($er->getCode() =="23000" && substr_count($er->getMessage(), "abonado_codigo_idx"))
        $error= 'No es necesario adicionar el abonado: <b style="color:yellow">"'.$num_identidad.'"</b>, el Código: "'.$datos['codigo'].'" aparece asignado a otro abonado.';
       else if($er->getCode() =="23000" && substr_count($er->getMessage(), "abonado_correo_idx"))
        $error= 'No es posible adicionar el abonado: el correo electrónico se encuentra asignado a otro abonado.';
	   else if($er->getCode() =="23000" && substr_count($er->getMessage(), "persona_numidentidad_idx"))
        $error= 'No es necesario adicionar el abonado: <b style="color:yellow">"'.$num_identidad.'"</b>, ya aparece registrado en el sistema.';
	   else
        throw $er;
    }
	catch(Exception $er)
    {
	   throw $er;
	}
  }

  public function actualizar(&$error, array $datos,$exist_dbtransaction=false)
  {
    $error=NULL;
    try
    {
		if(!$exist_dbtransaction)
		 $this->conBD->beginTransaction();
	 
	    $id=(int)$datos['id'];
	    $struct_dir=$datos['struct_direccion'];
		
		$query=$this->conBD->prepare('SELECT abonado.id_direccion_residencia,abonado.id_persona FROM abonado WHERE abonado.id=:id');
		$query->bindParam(':id',$id);
		$query->execute();
		$abonado=$query->fetch(PDO::FETCH_NUM);
		$id_direccion_bd=$abonado[0];
		
		$new_dir=null;
		if(empty($datos['id_direccion'])) //Nueva direccion
		{
			$query=$this->conBD->prepare('INSERT INTO direccion (id_ciudad,direccion,direccion_abreviada,estructura,longitud,latitud) VALUES (:ciu,:dir,:dir_abr,:str,:lon,:lat)');
			$query->bindParam(':ciu', $struct_dir['id_ciudad']);
			$query->bindParam(':dir', $struct_dir['dir_completa']);
			$query->bindParam(':dir_abr', $struct_dir['dir_nomenclatura']);
			
			$longitud=utf8_encode(trim(utf8_decode($datos['longitud'])));
			if(empty($longitud))
			  $longitud=null;
			$query->bindParam(':lon', $longitud);
			
			$latitud=utf8_encode(trim(utf8_decode($datos['latitud'])));
			if(empty($latitud))
			  $latitud=null;
			$query->bindParam(':lat', $latitud);
		
			$struct=json_encode($struct_dir['tipo']);
			$query->bindParam(':str', $struct);
			$query->execute();
			$new_dir=$id_direccion=$this->conBD->lastInsertId();
			  
			foreach($struct_dir['tipo'] as $tipo_dir)
			{
			   $query=$this->conBD->prepare('INSERT INTO detalle_direccion (id_direccion,id_nomenclatura,valor,canonical) VALUES (:dir,:nomc,:vlr,:can)');
			   $query->bindParam(':dir', $id_direccion);
			   $tp_nmc_dir=utf8_encode(trim(utf8_decode($tipo_dir['tipo_dir'])));
			   $query->bindParam(':nomc',$tp_nmc_dir);
			   $vlr_nmc_dir=utf8_encode(trim(utf8_decode($tipo_dir['tipo_dir_valor'])));
			   $query->bindParam(':vlr',$vlr_nmc_dir);
			   $canonical=Utilidades::normalizarTexto($vlr_nmc_dir);
			   $query->bindParam(':can',$canonical);
			   $query->execute();
			}
			  
			$query=$this->conBD->prepare('INSERT INTO persona_direccion (id_direccion,id_persona,reside) VALUES (:dir,:pers,1)');
			$query->bindParam(':dir', $id_direccion);
			$query->bindParam(':pers', $abonado[1]);
			$query->execute();
			
			$query=$this->conBD->prepare('UPDATE persona_direccion SET reside=1 WHERE id_direccion=:dir AND  id_persona=:pers');
			$query->bindParam(':dir', $id_direccion_bd);
			$query->bindParam(':pers', $abonado[1]);
			$query->execute();
			
			$query=$this->conBD->prepare('UPDATE persona_direccion SET reside=0 WHERE id_direccion=:dir AND  id_persona=:pers');
			$query->bindParam(':dir', $id_direccion_bd);
			$query->bindParam(':pers', $abonado[1]);
			$query->execute();
		}
		else //Direccion existe
		{
			$id_direccion=$datos['id_direccion'];
			if($id_direccion != $id_direccion_bd)// direccion existente diferente a la asignada
		    {
			   $query=$this->conBD->prepare('SELECT persona_direccion.id_persona FROM persona_direccion WHERE persona_direccion.id_direccion=:dir LIMIT 1');
			   $query->bindParam(':dir', $id_direccion);
			   $query->execute();
			   $persona=$query->fetch(PDO::FETCH_NUM);
				
			   if(!empty($persona))
			   {
				   $query=$this->conBD->prepare('SELECT abonado.id_persona FROM abonado WHERE abonado.id=:id');
				   $query->bindParam(':id', $id);
				   $query->execute();
				   $abonado_pers=$query->fetch(PDO::FETCH_NUM);
				   
				   if($persona[0] != $abonado_pers[0])
				   {
					  $error= 'La dirección de residencia no pertenece a los asociados al abonado.';
					  return false;
				   }
			   }
			   else
			   {
				  $error= 'La dirección de resisdencia no aparece registrada a ninguna persona en el sistema.';
				  return false;
			   }
			   
			   $query=$this->conBD->prepare('UPDATE persona_direccion SET reside=0 WHERE id_direccion=:dir AND  id_persona=:pers');
			   $query->bindParam(':dir', $id_direccion_bd);
			   $query->bindParam(':pers', $abonado[1]);
			   $query->execute();
			
			   $query=$this->conBD->prepare('UPDATE persona_direccion SET reside=1 WHERE id_direccion=:dir AND  id_persona=:pers');
			   $query->bindParam(':dir', $id_direccion);
			   $query->bindParam(':pers', $abonado[1]);
			   $query->execute();
			
			}
			
			if(!empty($struct_dir))
			{
				$query=$this->conBD->prepare('UPDATE direccion SET id_ciudad=:ciu,direccion=:dir,direccion_abreviada=:dir_abr,estructura=:str,longitud=:lon,latitud=:lat WHERE direccion.id=:id_dir');
				$query->bindParam(':ciu', $struct_dir['id_ciudad']);
				$query->bindParam(':id_dir',$id_direccion);
				$query->bindParam(':dir', $struct_dir['dir_completa']);
				$query->bindParam(':dir_abr', $struct_dir['dir_nomenclatura']);
				$longitud=utf8_encode(trim(utf8_decode($datos['longitud'])));
				if(empty($longitud))
				  $longitud=null;
				$query->bindParam(':lon', $longitud);
				
				$latitud=utf8_encode(trim(utf8_decode($datos['latitud'])));
				if(empty($latitud))
				  $latitud=null;
				$query->bindParam(':lat', $latitud);
				
				$struct=json_encode($struct_dir['tipo']);
				$query->bindParam(':str', $struct);
				$query->execute();
				
			    $query=$this->conBD->prepare('SELECT detalle_direccion.id,detalle_direccion.id_nomenclatura,detalle_direccion.canonical FROM detalle_direccion WHERE id_direccion=:id_dir');
				$query->bindParam(':id_dir', $id_direccion);
				$query->execute();
				$frag_direcciones=$query->fetchAll(PDO::FETCH_ASSOC);
				
				foreach($frag_direcciones as $key1 => $bd_dir)
				{
				   foreach($struct_dir['tipo'] as $key2 => $dat_dir)
				   {
					   if($bd_dir['id_nomenclatura'] == $dat_dir['tipo_dir'])
					   {
						   $canonical_dir=Utilidades::normalizarTexto($dat_dir['tipo_dir_valor']);
						   if($bd_dir['canonical'] == $canonical_dir)
						   {
							   unset($struct_dir['tipo'][$key2]);
							   unset($frag_direcciones[$key1]);
							   break;
						   }
					   }
				   }	
				}
				
				foreach($struct_dir['tipo'] as $key2 => $dat_dir)
				{
				   $modificado=false;
				   if(!empty($frag_direcciones))
				   {
					   foreach($frag_direcciones as $key1 => $bd_dir)
					   {
						   if($bd_dir['id_nomenclatura'] == $dat_dir['tipo_dir'])
						   {
							  $query=$this->conBD->prepare('UPDATE detalle_direccion SET valor=:vlr,canonical=:can WHERE id=:id');
							  $query->bindParam(':id',$bd_dir['id']);
							  $query->bindParam(':vlr', $dat_dir['tipo_dir_valor']);
							  $canonical=Utilidades::normalizarTexto($dat_dir['tipo_dir_valor']);
							  $query->bindParam(':can',$canonical);
							  $query->execute();
							  unset($struct_dir['tipo'][$key2]);
							  unset($frag_direcciones[$key1]);
							  $modificado=true;
							  break;
						   }
					   }
				   }
				   
				   if(!$modificado)
				   {
					  $query=$this->conBD->prepare('INSERT INTO detalle_direccion (id_direccion,id_nomenclatura,valor,canonical) VALUES (:dir,:nmc,:vlr,:can)');
					  $query->bindParam(':dir',$id_direccion);
					  $query->bindParam(':nmc', $dat_dir['tipo_dir']);
					  $query->bindParam(':vlr', $dat_dir['tipo_dir_valor']);
					  $canonical=Utilidades::normalizarTexto($dat_dir['tipo_dir_valor']);
					  $query->bindParam(':can',$canonical);
					  $query->execute();
					  unset($struct_dir['tipo'][$key2]);
				   }
				}
				
				if(!empty($frag_direcciones))
				{
				   foreach($frag_direcciones as $key1 => $bd_dir)
				   {
					  $query=$this->conBD->prepare('DELETE FROM detalle_direccion WHERE id=:id');
					  $query->bindParam(':id',$bd_dir['id']);
					  $query->execute();
					  unset($frag_direcciones[$key1]);
				   }
				}
			}
			
		}
		
        $query=$this->conBD->prepare('UPDATE abonado INNER JOIN persona ON abonado.id_persona=persona.id SET persona.nombres=:nomb,persona.apellidos=:apell,persona.telf_fijo=:telf,persona.telf_movil=:telm,abonado.id_direccion_residencia=:dir,abonado.estrato=:estr,abonado.correo=:eml,abonado.id_estado=:est,abonado.precinto=:prec,abonado.fecha_mod=:fchm WHERE abonado.id=:id');
        $id=(int)$datos['id'];
        $query->bindParam(':id',$id);
        $nombres=utf8_encode(trim(utf8_decode($datos['nombres'])));
		$query->bindParam(':nomb', $nombres);
		$apellidos=utf8_encode(trim(utf8_decode($datos['apellidos'])));
		if(empty($apellidos))
	      $apellidos=null;
		$query->bindParam(':apell', $apellidos);
		$telf_fijo=utf8_encode(trim(utf8_decode($datos['telf_fijo'])));
		if(empty($telf_fijo))
	      $telf_fijo=null;
		$query->bindParam(':telf', $telf_fijo);
		$telf_movil=utf8_encode(trim(utf8_decode($datos['telf_movil'])));
		if(empty($telf_movil))
	      $telf_movil=null;
		$query->bindParam(':telm', $telf_movil);
		//$direccion=utf8_encode(trim(utf8_decode($datos['direccion'])));
        $query->bindParam(':dir', $id_direccion);
		$correo=utf8_encode(trim(utf8_decode($datos['correo'])));
		if(empty($correo))
	      $correo=null;
        $query->bindParam(':eml', $correo);
		$id_estado=utf8_encode(trim(utf8_decode($datos['id_estado'])));
        $query->bindParam(':est', $id_estado);
		$precinto=utf8_encode(trim(utf8_decode($datos['precinto'])));
		if(empty($precinto))
	      $precinto=null;
        $query->bindParam(':prec', $precinto);
		$estrato=utf8_encode(trim(utf8_decode($datos['estrato'])));
        $query->bindParam(':estr', $estrato);
		date_default_timezone_set('America/Bogota');
        $fecha_mod = date("Y-m-d H:i:s");
		$query->bindParam(':fchm', $fecha_mod);
        $query->execute();

		$datos['fecha_creado']=$fecha_mod;
		$cad_crm="";
		if(!empty($datos['id_crms']))
		{
			$datos['id_abonado']=$id;
			foreach($datos['id_crms'] as $id_crm)
			{
			   $datos['id_crm']=$id_crm;
			   $this->adicionarCrm($error,$datos);
			   $query=$this->conBD->prepare('SELECT crm.nombre FROM crm WHERE crm.id=:crm');
			   $query->bindParam(':crm', $id_crm);
			   $query->execute();
			   $nomb=$query->fetch(PDO::FETCH_NUM);
			   $cad_crm.=$nomb[0]."\n";
			}
		}
		
		if(empty($struct_dir))
		{
            $query=$this->conBD->prepare('SELECT direccion.direccion FROM direccion WHERE id=:id_dir');
			$query->bindParam(':id_dir', $id_direccion);
			$query->execute();
            $qry_dir=$query->fetch(PDO::FETCH_NUM);
            $struct_dir['dir_completa']=$qry_dir[0];
		}

		$info_historial=array(
		   'id_abonado' => $id,
		   'id_estado' => $id_estado,
		   'nombres' => $nombres,
		   'apellidos' => $apellidos,
		   'direccion' => $struct_dir['dir_completa'],
		   'telf_fijo' => $telf_fijo,
		   'telf_movil' => $telf_movil,
		   'estrato' => $estrato,
		   'longitud' => $longitud,
		   'latitud' => $latitud,
		   'correo' => $correo,
		   'precinto' => $precinto,
		   'cad_crm' => $cad_crm
		);
		   
		$this->crearHistorialAbonado($info_historial,"actualizar");
		
		if(!$exist_dbtransaction)
		  $this->conBD->commit();
	  
        if(!empty($new_dir))
          return $new_dir;
	  
	    return true;
    }
    catch(PDOException $er)
    {
	   $this->conBD->rollBack();
	   if($er->getCode() =="23000" && substr_count($er->getMessage(), "abonado_codigo_idx"))
        $error= 'No es necesario modificar el abonado: <b style="color:yellow">"'.$datos['num_identidad'].'"</b>, el Código: "'.$datos['codigo'].'" aparece asignado a otro abonado.';
       else if($er->getCode() =="23000" && substr_count($er->getMessage(), "abonado_correo_idx"))
        $error= 'No es posible actualizar el abonado: el correo electrónico se encuentra asignado a otro abonado.';
	   else if($er->getCode() =="23000" && substr_count($er->getMessage(), "persona_numidentidad_idx"))
        $error= 'No es necesario modificar el abonado: <b style="color:yellow">"'.$datos['num_identidad'].'"</b>, ya aparece registrado en el sistema.';
	   else
        throw $er;
    }
	catch(Exception $er)
    {
	   throw $er;
	}
  }

  public function eliminar(&$error, $id)
  {
    $error=NULL;
    try
    {
	   $id=(int)$id;
	   $qry_abonado=$this->getDetalleAbonado($id);
	   $this->conBD->beginTransaction();
	   
	   $query=$this->conBD->prepare('SELECT abonado.id_direccion_residencia,abonado.id_persona FROM abonado WHERE id=:id');
       $query->bindParam(':id', $id);
       $query->execute();
       $direccion_abon=$query->fetch(PDO::FETCH_NUM);
	   
       $query=$this->conBD->prepare('DELETE FROM abonado WHERE id=:id');
       $query->bindParam(':id',$id);
       $query->execute();
	   
	   $query=$this->conBD->prepare('DELETE FROM direccion WHERE id=:id');
       $query->bindParam(':id',$direccion_abon[0]);
       $query->execute();
	   
	   $qry_abonado['id_abonado']=$qry_abonado['id'];
	   
	   $this->crearHistorialAbonado($qry_abonado,"eliminar");
	   
	   $query=$this->conBD->prepare('SELECT persona_direccion.id_direccion FROM persona_direccion WHERE persona_direccion.id_persona=:pers');
       $query->bindParam(':pers', $direccion_abon[1]);
       $query->execute();
       $direcciones_pers=$query->fetchAll(PDO::FETCH_NUM);
	   
	   if(!empty($direcciones_pers))
	   {
		   foreach($direcciones_pers as $id_dir)
		   {
			  $query=$this->conBD->prepare('DELETE FROM direccion WHERE id=:id');
              $query->bindParam(':id',$id_dir[0]);
              $query->execute();
		   }
	   }
	   
	   $this->conBD->commit();
       
	   return true;
    }
    catch(Exception $er)
    {
        throw $er;
    }   
  }

  public function obtenerTodo(Array $config)
  {
    try
    {
        $cadsql='SELECT * FROM abonado';
        if(isset($config['orderBy']))
         $cadsql.=' ORDER BY '.$config['orderBy'];
       
        $query=$this->conBD->prepare($cadsql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(Exception $er)
    {
        throw $er;
    }  
  }

  public function buscarById($id)
  {
    try
    {
        $query=$this->conBD->prepare('SELECT * FROM abonado WHERE id=:id');
        $id=(int)$id;
        $query->bindParam(':id', $id);
        $query->execute();
        $abonado=$query->fetch(PDO::FETCH_ASSOC);
        if(!empty($abonado))
          return $abonado;

        return null;
    }
    catch(Exception $er)
    {
        throw $er;
    }  
  }
  
  public function buscarByContrato($id)
  {
    try
    {
        //$query=$this->conBD->prepare('SELECT persona.*, abonado.fecha_creado FROM abonado INNER JOIN persona ON abonado.id_persona=persona.id INNER JOIN cablemodem_contrato ON abonado.id=cablemodem_contrato.id_abonado WHERE cablemodem_contrato.id=:id');

        $query=$this->conBD->prepare('SELECT persona.*, abonado.fecha_creado, abonado.correo, direccion.direccion FROM abonado INNER JOIN persona ON abonado.id_persona=persona.id INNER JOIN cablemodem_contrato ON abonado.id=cablemodem_contrato.id_abonado INNER JOIN direccion ON abonado.id_direccion_residencia=direccion.id WHERE cablemodem_contrato.id=:id');

        $id=(int)$id;
        $query->bindParam(':id', $id);
        $query->execute();
        $abonado=$query->fetch(PDO::FETCH_ASSOC);
        if(!empty($abonado))
          return $abonado;

        return null;
    }
    catch(Exception $er)
    {
        throw $er;
    }  
  }
  public function hasAprovisionamiento($id)
  {
	$error=NULL;
    try
    {
       $query=$this->conBD->prepare('SELECT cablemodem_contrato.id FROM cablemodem_contrato WHERE cablemodem_contrato.id_abonado=:id LIMIT 1');
       $id=(int)$id;
       $query->bindParam(':id',$id);
       $query->execute();
       $modem=$query->fetch(PDO::FETCH_NUM);
	   if(!empty($modem))
         return true;
       
	   return false;
    }
    catch(Exception $er)
    {
        throw $er;
    }   
  }
  
  public function getDetalleAbonado($id)
  {
    try
    {
        $query=$this->conBD->prepare('SELECT abonado.id,abonado.codigo_migracion,abonado.id_persona,persona.nombres,persona.apellidos,persona.num_identidad,abonado.id_direccion_residencia,persona.telf_fijo,persona.telf_movil,direccion.id_ciudad,direccion.direccion,direccion.direccion_abreviada,direccion.estructura,direccion.longitud,direccion.latitud,abonado.estrato,abonado.fecha_creado,abonado.fecha_mod,abonado.id_estado,abonado.correo,abonado.precinto,estado.nombre as estado FROM abonado INNER JOIN persona ON abonado.id_persona=persona.id INNER JOIN estado ON abonado.id_estado=estado.id INNER JOIN direccion ON abonado.id_direccion_residencia=direccion.id WHERE abonado.id = :id');
        $id=(int)$id;
        $query->bindParam(':id', $id);
        $query->execute();
        $abonado=$query->fetch(PDO::FETCH_ASSOC);
        if(!empty($abonado))
          return $abonado;

        return null;
    }
    catch(Exception $er)
    {
        throw $er;
    }  
  }
  
  public function getDetalleAbonadoByNumIdentidad($num_id)
  {
    try
    {
        $query=$this->conBD->prepare('SELECT abonado.id,abonado.codigo_migracion,abonado.id_persona,persona.nombres,persona.apellidos,persona.num_identidad,abonado.id_direccion_residencia,persona.telf_fijo,persona.telf_movil,direccion.id_ciudad,direccion.direccion,direccion.direccion_abreviada,direccion.estructura,direccion.longitud,direccion.latitud,abonado.estrato,abonado.fecha_creado,abonado.fecha_mod,abonado.id_estado,abonado.correo,abonado.precinto,estado.nombre as estado FROM abonado INNER JOIN persona ON abonado.id_persona=persona.id INNER JOIN estado ON abonado.id_estado=estado.id INNER JOIN direccion ON abonado.id_direccion_residencia=direccion.id WHERE persona.num_identidad = :ced');
        $num_identidad=utf8_encode(trim(utf8_decode($num_id)));
		$query->bindParam(':ced', $num_identidad);
        $query->execute();
        $abonado=$query->fetch(PDO::FETCH_ASSOC);
        if(!empty($abonado))
          return $abonado;

        return null;
    }
    catch(Exception $er)
    {
        throw $er;
    }  
  }
  
  public function adicionarCrm(&$error, array $datos)
  {
    $error=NULL;
    try
    {
	   $query=$this->conBD->prepare('INSERT INTO crm_abonado (id_crm,id_abonado,fecha_creado) VALUES (:crm,:abon,:fch)');
	   $id_crm=trim(utf8_decode($datos['id_crm']));
	   $query->bindParam(':crm', $id_crm);
	   $id_abonado=trim(utf8_decode($datos['id_abonado']));
	   $query->bindParam(':abon', $id_abonado);
	   $query->bindParam(':fch', $datos['fecha_creado']);
	   $query->execute();
	   return $this->conBD->lastInsertId();
    }
    catch(PDOException $er)
    {
	   throw $er;
    }
    catch(Exception $er)
    {
	   throw $er;
    }
  }
  
  public function getListadoByCriterio($datos)
  {
    $error=NULL;
	try
	{
		$cad_where="";
		$cad_join="";
		$txt_busq=utf8_encode(trim(utf8_decode($datos['txt_busq'])));
		switch($datos['criterio'])
		{
			case "1": //nit- c.c
				$cad_where=" persona.num_identidad=:txt";
				break;
			case "2": //nombres
				$cad_where=" persona.nombres LIKE :txt";
				$txt_busq="%".$txt_busq."%";
				break;
			case "3": //apellidos
				$cad_where=" persona.apellidos LIKE :txt";
				$txt_busq="%".$txt_busq."%";
				break;
			case "4": //codigo autonumerico
				$cad_where=" abonado.id=:txt";
				break;
			case "5": //codigo migracion
				$cad_where=" abonado.codigo_migracion=:txt";
				break;
			case "6": //contrato
				$cad_join=" INNER JOIN contrato ON contrato.id_abonado=abonado.id";
				$cad_where=" contrato.id=:txt";
				break;
			case "7": //precinto
				$cad_where=" abonado.precinto=:txt";
				break;
			case "8": //crm - crm
				$cad_join=" INNER JOIN crm_abonado ON crm_abonado.id_abonado=abonado.id";
				$cad_where=" crm_abonado.id_crm=:txt";
				break;
		}
		
		$cad_sql_search='SELECT DISTINCT abonado.id,abonado.codigo_migracion,persona.nombres,persona.apellidos,persona.num_identidad,abonado.id_estado,estado.nombre as estado FROM abonado INNER JOIN persona ON abonado.id_persona=persona.id INNER JOIN estado ON abonado.id_estado=estado.id'.$cad_join." WHERE".$cad_where;
		$query=$this->conBD->prepare($cad_sql_search);
		$query->bindParam(':txt', $txt_busq);
		$query->execute();
		$qry_abonados=$query->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($qry_abonados))
          return $qry_abonados;

        return null;
	}
	catch(Exception $er)
	{
	  throw $er;
	}
  }
  
  private function crearHistorialAbonado($datos,$accion)
  {
       $error=NULL;
       try
       {
		 $query=$this->conBD->prepare('SELECT persona.num_identidad,usuario.nombre FROM persona INNER JOIN usuario ON persona.id=usuario.id_persona WHERE usuario.id=:usr');
		 $query->bindParam(':usr', $_SESSION["iduser"]);
		 $query->execute();
		 $info_persona=$query->fetch(PDO::FETCH_NUM);
	   
	     if(!empty($datos['id_estado']))
		 {
			$query=$this->conBD->prepare('SELECT estado.nombre FROM estado WHERE estado.id=:est');
		    $query->bindParam(':est', $datos['id_estado']);
			$query->execute();
			$estado=$query->fetch(PDO::FETCH_ASSOC); 
		 }
		 else
		  $estado['nombre']='';
		
		 if(empty($datos['num_identidad']))
		 {
		   $query=$this->conBD->prepare('SELECT persona.num_identidad FROM persona INNER JOIN abonado ON abonado.id_persona=persona.id WHERE abonado.id=:abon');
		   $query->bindParam(':abon', $datos['id_abonado']);
		   $query->execute();
		   $abonado=$query->fetch(PDO::FETCH_NUM);
		   $datos['num_identidad']=$abonado[0];
		 }
		 
		 $query=$this->conBD->prepare('INSERT INTO historico_abonado (num_identidad,id_abonado,nombres,apellidos,direccion,estado,estrato,correo,telf_fijo,telf_movil,longitud,latitud,precinto,crm,responsable,usuario,fecha_creado,accion) VALUES (:cc_nit,:abon,:nom,:apel,:dir,:est,:estr,:corr,:telf,:telm,:lon,:lat,:prec,:crm,:pers,:usr,:fech,:acc)');
		 $query->bindParam(':cc_nit', $datos['num_identidad']);
		 $query->bindParam(':abon', $datos['id_abonado']);
		 $query->bindParam(':nom', $datos['nombres']);
		 $query->bindParam(':apel', $datos['apellidos']);
		 $query->bindParam(':dir', $datos['direccion']);
		 $query->bindParam(':est', $estado['nombre']);
		 $query->bindParam(':estr', $datos['estrato']);
		 $query->bindParam(':corr', $datos['correo']);
		 $query->bindParam(':telf', $datos['telf_fijo']);
		 $query->bindParam(':telm', $datos['telf_movil']);
		 $query->bindParam(':lon', $datos['longitud']);
		 $query->bindParam(':lat', $datos['latitud']);
		 $query->bindParam(':prec', $datos['precinto']);
		 
		 if(!isset($datos['cad_crm']))
			$datos['cad_crm']=null;
		  
		 $query->bindParam(':crm', $datos['cad_crm']);
		 
		 date_default_timezone_set('America/Bogota');
         $fecha_hoy = date("Y-m-d H:i:s");
		  
		 $query->bindParam(':pers', $info_persona[0]);
		 $query->bindParam(':usr', $info_persona[1]);
		 $query->bindParam(':fech', $fecha_hoy);
		 $query->bindParam(':acc', $accion);
		 $query->execute();
       }
       catch(PDOException $er)
       {
          throw $er;
       }
  }
  
  public function generarReporteAbonados($campos)
  {
       try
       {
		   $tablas=0;
		   $cad_columnas="";
		   $headers_campos=array();
		   foreach($campos as $key => $item)
		   {
			  if(!empty($item) || $campos['num_campos']== '10')
			  {
				switch($key)
				{
				  case 'num_identidad':
				    $headers_campos[]="C.C - NIT";
					$columna="persona.num_identidad";
					if(!($tablas & 2))
					  $tablas+=2;
					break;
				  case 'nombres':
				    $headers_campos[]="NOMBRES   ";
					$columna="persona.nombres";
					if(!($tablas & 2))
					  $tablas+=2;
					break;
				  case 'apellidos':
				    $headers_campos[]="APELLIDOS   ";
					$columna="persona.apellidos";
					if(!($tablas & 2))
					  $tablas+=2;
					break;
			      case 'correo':
				    $headers_campos[]="CORREO   ";
					$columna="abonado.correo";
					break;
				  case 'precinto':
				    $headers_campos[]="PRECINTO   ";
					$columna="abonado.precinto";
					break;
				  case 'estrato':
				    $headers_campos[]="ESTRATO   ";
					$columna="abonado.estrato";
					break;
			      case 'telefonos':
				    $headers_campos[]="TELEFONOS   ";
					$columna="CONCAT(persona.telf_movil, ' ', persona.telf_fijo)";
					if(!($tablas & 2))
					  $tablas+=2;
					break;
				  case 'estado':
				    $headers_campos[]="ESTADO   ";
					$columna="estado.nombre as estado";
					if(!($tablas & 1))
					  $tablas+=1;
					break;
				  case 'direccion':
				    $headers_campos[]="DIRECCION   ";
					$columna="direccion.direccion_abreviada";
					if(!($tablas & 3))
					  $tablas+=3;
					break;
				  case 'fecha':
				    $headers_campos[]="FECHA CREADO";
					$columna="abonado.fecha_creado";
					break;
				  default:
				    $columna="";
					break;
				}  
				
				if($columna != "")
				{
				   if($cad_columnas == "")
					$cad_columnas=$columna;
                   else
				    $cad_columnas.=",".$columna;
				}
			  }
		   }
		
		   $cad_sql="SELECT ".$cad_columnas." FROM abonado";
		   $cad_joins="";
           if($tablas & 1)
			 $cad_joins.=" INNER JOIN estado ON abonado.id_estado=estado.id";
		 
		   if($tablas & 2)
			 $cad_joins.=" INNER JOIN persona ON abonado.id_persona=persona.id";
		   
		   if($tablas & 3)
			  $cad_joins.=" INNER JOIN direccion ON abonado.id_direccion_residencia=direccion.id";   
		   
		   $cad_sql.=$cad_joins." ORDER BY abonado.fecha_creado DESC";
		   
           $query=$this->conBD->prepare($cad_sql);
           $query->execute();
           return array( 
		     "headers" => $headers_campos,
		     "registros" => $query->fetchAll(PDO::FETCH_ASSOC)
		   );
       }
       catch(Exception $er)
       {
          throw $er;
       }  
  }
  
  public function getHistorialAbonado(&$error,$datos)
  {
	   $error=NULL;
	   try
	   {
		 $sql_fechas="";
		 $format="d/m/Y";
		 if(isset($datos['fecha_desde']) && isset($datos['fecha_hasta']))
		 {
		   $date1=DateTime::createFromFormat($format, $datos['fecha_desde']);
		   $date2=DateTime::createFromFormat($format, $datos['fecha_hasta']);
		   if ($date1 > $date2)
		   {
			 $error = 'Rango de fechas no es correcto, fecha <b>"Desde"</b> debe ser menor o igual a fecha <b>"Hasta"</b>';
			 return null;
		   }
		  
		   $datos['fecha_desde']=$date1->format('Y-m-d');
		   $datos['fecha_hasta']=$date2->format('Y-m-d');
		   $sql_fechas=" WHERE hc.fecha_creado BETWEEN '".$datos['fecha_desde']." 00:00:00' AND '".$datos['fecha_hasta']." 23:59:59'";
		   //$sql_fechas=" WHERE hc.fecha_creado BETWEEN :fch_desde AND :fch_hasta"; 
		 }  
		 else if(isset($datos['fecha_desde']))
		 {
			$date1=DateTime::createFromFormat($format, $datos['fecha_desde']);
			$datos['fecha_desde']=$date1->format('Y-m-d');
			//$sql_fechas=" WHERE hc.fecha_creado >= :fch_desde"; 
			$sql_fechas=" WHERE hc.fecha_creado >= '".$datos['fecha_desde']." 00:00:00'"; 
		 }  
		 else if(isset($datos['fecha_hasta']))
		 {
			$date2=DateTime::createFromFormat($format, $datos['fecha_hasta']);
			$datos['fecha_hasta']=$date2->format('Y-m-d');
			//$sql_fechas=" WHERE hc.fecha_creado <= :fch_hasta"; 
			$sql_fechas=" WHERE hc.fecha_creado <= '".$datos['fecha_hasta']." 23:59:59'"; 
		 }  
		 
		 $query=$this->conBD->prepare('SELECT hc.id_abonado,hc.num_identidad,hc.estado,hc.nombres,hc.apellidos,hc.direccion,hc.longitud,hc.latitud,hc.estrato,hc.correo,hc.telf_movil,hc.telf_fijo,hc.precinto,hc.crm,hc.responsable,hc.usuario,hc.fecha_creado,hc.accion FROM historico_abonado as hc'.$sql_fechas.' ORDER BY hc.fecha_creado');
		 /*if(isset($datos['fecha_desde']))
		 {
			$fecha=$datos['fecha_desde']." 00:00:00";
			$query->bindParam(':fch_desde', $fecha, PDO::PARAM_STR); 
		 }
		 
		 if(isset($datos['fecha_hasta']))
		 {
			$fecha=$datos['fecha_hasta']." 23:59:59";
			$query->bindParam(':fch_hasta', $fecha, PDO::PARAM_STR);
		 }*/
		 
		 $query->execute();
		 return array(
			"headers" => array("ID   ","C.C - NIT ","ESTADO  ","NOMBRES","APELLIDOS ","DIRECCION  ","LONGITUD   ","LATITUD  ","ESTRATO  ","CORREO ","TELF. MOVIL  ","TELF. FIJO  ","PRECINTO   ","CRM   ","RESPONSABLE   ","USUARIO   ","FECHA ACCION","ACCION"),
			"registros" => $query->fetchAll(PDO::FETCH_ASSOC)
		  );
	   }
	   catch(PDOException $er)
	   {
		  throw $er;
	   }
  }
	
}
