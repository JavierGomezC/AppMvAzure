<?php 
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

    /*try
    {*/

      if($_POST['accion'] == "validar")
      {
        require_once '../modelo/Utilidades.php';
        $error=true;
        $add=Utilidades::validarSerial($error,$_POST['sn']);
        if($add)
          $data['ok']="Se agrego el canal correctamente";
        else
          $data['err']= $error;

        header('Content-Type: application/json');
        echo json_encode($arrayName = array('data' => $data));

      }

      else if($_POST['accion'] == "adicionar")
      {


        require_once '../modelo/Utilidades.php';
        $error=true;
        $add=Utilidades::adicionarCanal($error,$_POST);
        if($add){

          $contenedor=Utilidades::buscarContenedorById($_POST['contenedor']);
          $canales=Utilidades::buscarCanalesCont($error, $_POST['contenedor']);


          $texto = file_get_contents("/var/www/html/mbox_v2/file/".$contenedor['canonical'], FILE_USE_INCLUDE_PATH);
          $fh = fopen("/var/www/html/mbox_v2/file/".$contenedor['canonical'], 'w');
          foreach($canales as $cmd)
          {

            //preg_match($r,$cmd,$t);

            /*if($texto){
              $lineas = preg_split('/\r\n\r\n|\r\r|\n\n/', $texto);
              if((count($lineas)>=8 && $formato=="SD") || (count($lineas)>=4 && $formato=="HD")){
                  exit(json_encode(array(
                      'info' => "Portadora llena, por favor intente guardar en otra portadora"
                  )));
              }
            }*/


            /*if($texto)
              $cmd=$texto."\n".$cmd;
            else
              $cmd=$cmd;*/

            fwrite($fh, $cmd['comando'].' -metadata comment="'.$cmd['num_canal'].'"'."\n");
          }

          fclose($fh);
          $portadoras = array();
          if ($handle = opendir('/var/www/html/mbox_v2/file/')) {
              while (false !== ($entry = readdir($handle))) {
                  if ($entry != "." && $entry != "..") {
                    array_push($portadoras, $entry);
                  }
              }
              closedir($handle);
          }
          $resultado = chmod("/var/www/html/mbox_v2/file/".$contenedor['canonical'], 0777);
          
          $data['ok']="Se agrego el comando correctamente";
        }
        else
          $data['err']= $error;





        header('Content-Type: application/json');
        echo json_encode($data); 
      }

      else if($_POST['accion'] == "modificar")
      {

        require_once '../modelo/Utilidades.php';
        $error=true;
        $mod=Utilidades::modificarCanal($error,$_POST);
        if($mod)
          $data['ok']="Se modifico el comando correctamente";



        header('Content-Type: application/json');
        echo json_encode($data); 
      }
      else if($_POST['accion'] == "upload-lista-m3u8")
      {

        require_once '../modelo/Utilidades.php';

            set_time_limit(0);
            header('Content-Type: application/json');
            if (empty($_FILES['up_lista_m3u8']) && $_POST['estado']=="false") echo json_encode(['error' => 'No se encontraron archivos para subir.']);
            else if (!filter_var($_POST['url_lista'], FILTER_VALIDATE_URL) && $_POST['estado']=="true") echo json_encode(['error' => 'La URL ingresada es invalida.']);
            else
            {
              if($_POST['estado']=="false"){
                $file = $_FILES['up_lista_m3u8'];
                $filenames = $file['name'];
                for ($i = 0;$i < count($filenames);$i++)
                {
                    $ext = explode('.', basename($filenames[$i]));
                    $nomb_archivo = md5(uniqid());
                    $target = "../archivos/uploads/" . $nomb_archivo . "." . array_pop($ext);
                    if (!move_uploaded_file($file['tmp_name'][$i], $target))
                    {
                        echo json_encode(['error' => 'Problemas al tratar de subir archivos.']);
                        break;
                    }
                }
              }else
                $target = $_POST['url_lista'];

                /*
                #EXTM3U
                #EXTINF:0 tvg-id="1" tvg-name="Nombre" group-title="Categoria"
                #EXTVLCOPT:network-caching=1000
                http://canal
                */

                //var_dump($target); die;

                $string = file_get_contents($target);
                //var_dump($string); die;
                $string = str_replace("tvg-id", "num", $string);
                $string = str_replace("tvg-name", "name", $string);
                $string = str_replace("group-title", "categoria", $string);

                //var_dump($string); die;

                preg_match_all('/(?P<tag>#EXTINF:0)|(?:(?P<prop_key>[-a-z]+)=\"(?P<prop_val>[^"]+)")|(?<something>,[^\r\n]+)|(?<url>http[^\s]+)/', $string, $match );

                $count = count( $match[0] );

                $result = [];
                $index = -1;
                for( $i =0; $i < $count; $i++ ){
                    $item = $match[0][$i];
                    if( !empty($match['tag'][$i])){
                        ++$index;
                    }elseif( !empty($match['prop_key'][$i])){
                        $result[$index][$match['prop_key'][$i]] = $match['prop_val'][$i];
                        if($match['prop_key'][$i]=="name"){
                          $canonical=Utilidades::normalizarTexto2($match['prop_val'][$i]);
                          $result[$index]['logo'] = $canonical;
                        }
                    }elseif( !empty($match['something'][$i])){
                        $result[$index]['something'] = $item;
                    }elseif( !empty($match['url'][$i])){
                        $result[$index]['url'] = $item ;
                    }
                    $result[$index]['programas'] = array();
                }
                $result = array('data' => $result);

                $my_file = $_SERVER['DOCUMENT_ROOT'] . "/file/lista_canales.json";

                $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
                fwrite($handle, json_encode($result));
                $data['ok']="Se genero la lista de canales correctamente";
            }





        header('Content-Type: application/json');
        echo json_encode($data); 
      }
      else if($_POST['accion'] == "add_grilla")
      {

        require_once '../modelo/Utilidades.php';
        $error=true;
        $add=Utilidades::adicionarGrilla($error,$_POST);
        if($add)
          $data['ok']="Se agrego el canal correctamente";
        else
          $data['err']= $error;


        header('Content-Type: application/json');
        echo json_encode($data); 
      }

      else if($_POST['accion'] == "buscar-canal")
      {

        require_once '../modelo/Utilidades.php';
        $error=true;
        $qry_canales=Utilidades::buscarCanalesCont($error,$_POST['id_cont']);

        if (!empty($qry_canales)) $data = array(
          'ok' => "Canal(es) encontrado(s) correctamente",
          'canales' => $qry_canales
        );
        else $data = array(
          'info' => "No se han encontrado canales"
        );
        header('Content-Type: application/json');
        echo json_encode($data); 
      }

      else if($_POST['accion'] == "add-comando-band")
      {
        require_once '../modelo/Utilidades.php';
        $error=true;
        $add=Utilidades::adicionarComandoBand($error,$_POST);
        if($add){
          $data['ok']="Se agrego el comando correctamente";
          $data['id_bandera']=$add;
        }
        else
          $data['err']= $error;


        header('Content-Type: application/json');
        echo json_encode($data); 
      }
      else if($_POST['accion'] == "mod-comando-band")
      {
        require_once '../modelo/Utilidades.php';
        $error=true;
        $add=Utilidades::modificarComandoBand($error,$_POST);
        if($add){
          $data['ok']="Se modifico el comando correctamente";
        }
        else
          $data['err']= $error;


        header('Content-Type: application/json');
        echo json_encode($data); 
      }
      else if($_POST['accion'] == "gen_canales")
      {
        if(!empty($_POST['lst_canales']))
        {
           $fh = fopen("/usr/local/bin/".$_POST['archivo'], 'w+');
           foreach($_POST['lst_canales'] as $canal)
                 {
            $cad_canal="#EXTM3U\n#EXTINF:0,".$canal['nombre']."\n";
            $cad_canal.="#EXTVLCOPT:network-caching=1000\n".$canal['url']."\n";
            fwrite($fh, $cad_canal); 
           }  
                 
           fclose($fh);
           
                 $resultado = chmod("/usr/local/bin/".$_POST['archivo'], 0777);
           $data['ok']="Lista canales generados correctamente";
        }
        else
          $data['info']="No hay canales que generar";
          header('Content-Type: application/json');
          echo json_encode($data); 
      }
      else if($_POST['accion'] == "det_canales")
      {
        $comando = "sudo killall ffmpeg 2>&1";
        exec($comando, $salida, $estado);
        if($estado === 0){
            $data['ok']= "Canales detenidos correctamente";
        } else {
          if($salida[0] == "ffmpeg: no process found")
            $data['info']= "No se encontraron canales en ejecución";

          else
            $data['err']= "Error al detener los canales";
        }
          header('Content-Type: application/json');
          echo json_encode($data); 
      }

      else if($_POST['accion'] == "elim_portadora")
      {
        $archivo = "/usr/local/bin/".$_POST['id_portadora'];

        if (!unlink($archivo))
        {
          $data['err'] = "Error al eliminar el archivo";
        }
        else
        {
          $data['ok'] = "Archivo eliminado correctamente";
        }
          header('Content-Type: application/json');
          echo json_encode($data); 
      }
      else if($_POST['accion'] == "ver_portadora")
      {
        $archivo = "/usr/local/bin/".$_POST['id_portadora'];
        $texto = file_get_contents($archivo, FILE_USE_INCLUDE_PATH);

        if (empty($texto) || $texto == null)
        {
          $data['err'] = "Error al mostrar el archivo";
        }
        else
        {
          $data['texto']=$texto;
          $data['ok'] = "Archivo eliminado correctamente";
        }
          header('Content-Type: application/json');
          echo json_encode($data); 
      }
      else if($_POST['accion'] == "detener_canal")
      {

        require_once '../modelo/Utilidades.php';
        $error=true;
        $canal=Utilidades::getCanalById($_POST['id_canal']);

        if (!empty($canal)){
          $entrada=strtolower($canal['nom_serv_ent']).'://@'.$canal['ip_entrada'].':'.$canal['puerto_entrada'];
          $salida=strtolower($canal['nom_serv_sal']).'://'.$canal['ip_salida'].':'.$canal['puerto_salida'];
          $comando = shell_exec('ps -ef | grep '. $entrada);
          $cmd = explode("\n", $comando);
          $data = array('info' => "Canal no se esta ejecutando, no es necesario detenerlo.");
          foreach($cmd as $value)
          {
            if (strpos($value, $entrada)  !== false && strpos($value, $salida) !== false) {
                $div=array_filter(explode(" ", $value));
                $valores=array_combine(range(1, count($div)), array_values($div));
                exec('sudo kill -9 '. $valores[2]);
                var_dump('kill -9 '. $valores[2]);
                $data = array('ok' => "Canal detenido correctamente",'canal' => $canal);
            }/* else {
              $data = array(
                'info' => "Canal no se esta ejecutando, no es necesario detenerlo."
              );
            }*/
          }
        }
        else $data = array(
          'info' => "No se han encontrado el canal"
        );
          header('Content-Type: application/json');
          echo json_encode($data); 
      }

   /* }
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
    } */

    /*function iniciar($vista_twig=true)
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
        
    }*/
 ?>

