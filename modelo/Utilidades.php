<?php
abstract class Utilidades
{

	public static function getAllCanal()
	{
		try
		{
			if(!class_exists('BDManager'))
			require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/BDManager.php';
			$conBD = BDManager::getBDManager()->getConectionBD();
			$query=$conBD->prepare('SELECT * FROM canal');
			$query->execute();
			$serv=$query->fetchAll(PDO::FETCH_ASSOC);
			return $serv;
		}
		catch(PDOException $er)
		{
			return $er;
		}
	}

	public static function crearCanal(array $datos)
	{
		try
		{
			if(!class_exists('BDManager'))
			require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/BDManager.php';
			$conBD = BDManager::getBDManager()->getConectionBD();
			$query=$conBD->prepare('INSERT INTO canal(numero, nombre, categoria, calidad) VALUES (:numero,:nombre,:categoria,:calidad)');
			$query->bindParam(':numero', $datos['numero']);
			$query->bindParam(':nombre', $datos['nombre']);
			$query->bindParam(':categoria', $datos['categoria']);
			$query->bindParam(':calidad', $datos['calidad']);
			$query->execute();
			return $query;
		}
		catch(PDOException $er)
		{ 
			return null;
		}
	}

	public static function editarCanal(array $datos)
	{
		try
		{
			if(!class_exists('BDManager'))
			require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/BDManager.php';
			$conBD = BDManager::getBDManager()->getConectionBD();
			$query=$conBD->prepare('UPDATE `canal` SET `numero`=:numero,`nombre`=:nombre,`categoria`=:categoria,`calidad`=:calidad WHERE `id`=:id');
			$query->bindParam(':id', $datos['id']);
			$query->bindParam(':numero', $datos['numero']);
			$query->bindParam(':nombre', $datos['nombre']);
			$query->bindParam(':categoria', $datos['categoria']);
			$query->bindParam(':calidad', $datos['calidad']);
			$query->execute();
			return $query;
		}
		catch(PDOException $er)
		{ 
			return null;
		}
	}

	public static function eliminarCanal(array $datos)
	{
		try
		{
			if(!class_exists('BDManager'))
			require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/BDManager.php';
			$conBD = BDManager::getBDManager()->getConectionBD();
			$query=$conBD->prepare('DELETE FROM `canal` WHERE `id`=:id');
			$query->bindParam(':id', $datos['id']);
			$query->execute();
			return $query;
		}
		catch(PDOException $er)
		{ 
			return null;
		}
	}

}

?>