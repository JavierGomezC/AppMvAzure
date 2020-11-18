$(document).on("ready", inicio_admin);

function inicio_admin() {
}

function ejecutarAjax(parametros, validar_form) {
   validar_form = validar_form || false;
   if (validar_form) {
      if ($(parametros.formulario).length) {
         if ($(parametros.formulario).valid() == false) {
            return false;
         }
      }
   }
   $.ajax({
      url: parametros.url,
      type: "POST",
      timeout: 100000,
      dataType: parametros.formato,
      data: parametros.datos,
      error: function(XMLHttpRequest, textStatus, errorThrown) {
         parametros.result = null;
         var err;
         if (XMLHttpRequest.status === 0) {
            if (parametros.datos.hasOwnProperty("id_cmd")) {
               var msj_cmd;
               if (parametros.datos.id_cmd.hasOwnProperty("a_sistema")) {
                  msj_cmd = "Apagado de la máquina realizado correctamente";
               } else {
                  if (parametros.datos.id_cmd.hasOwnProperty("r_sistema")) {
                     msj_cmd = "Reinicio de la máquina realizado correctamente";
                  } else {
                     $(parametros.area).waitMe("hide");
                     err = "No conectado. Por favor, compruebe la conexión de red.";
                     return true;
                  }
               }
               setTimeout(function() {
                  $(parametros.area).waitMe("hide");
                  notificar("Ejecutar Comando",2000,'success');
               }, 30000);
               return true;
            } else if (parametros.datos.accion == 'estado-etd') {
               if (cont == 0) err = 'No conectado. Por favor, compruebe la conexión de red.';
               clearInterval(setIntReinicio);
               cont++;
            } else {
               err = "No conectado. Por favor, compruebe la conexión de red.";
            }
         } else {
            if (XMLHttpRequest.status == 404) {
               err = "La página solicitada no se encuentra. [404]";
               location.href = "index.php";
            } else {
               if (XMLHttpRequest.status == 500) {
                  err = "Error de Servidor Interno [500].";
               } else {
                  if (errorThrown === "parsererror") {
                     err = "Estructura Peticion JSON falló.";
                  } else {
                     if (errorThrown === "timeout") {
                        err = "Error de tiempo de espera.";
                     } else {
                        if (errorThrown === "abort") {
                           err = "Peticion abortada.";
                        } else {
                           err = "Error: " + XMLHttpRequest.responseText;
                        }
                     }
                  }
               }
            }
         }
         $(parametros.area).waitMe("hide");
         notificar(err,2000,'error');
      },
      success: function(data, textStatus, XMLHttpRequest) {
         parametros.result = data;
         if (!parametros.area) {
            eval(parametros.funcion)(parametros.result);
         } else {
            $(parametros.area).waitMe("hide");
         }
      },
      beforeSend: function() {
         if (!$(parametros.area).hasClass("waitMe_container")) {
            $(parametros.area).waitMe({
               effect: "win8",
               text: parametros.msj_prog,
               bg: "rgba(55,52,72,0.8)",
               color: "#fff",
               onClose: function() {
                  if (parametros.result) {
                     eval(parametros.funcion)(parametros.result);
                  }
               }
            });
         } else {
            parametros.area = null;
         }
      }
   });
}