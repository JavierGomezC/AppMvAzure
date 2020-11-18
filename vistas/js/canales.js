$(document).on("ready", inicio);

function inicio() 
{
  $("#btn_edit_canal").on("click", editCanal);
  $("#btn_crear_canal").on("click", crearCanal);
}

function eliminarCanal()
{
  $('#modal_mod_canal').modal('hide');
  var parametros = {
     url: "./controladores/iptv_admin.php",
     funcion: "getRtaEliminarCanal",
     formato: "json",
     area: "#contenido",
     msj_prog: "Buscando ...",
     datos: {
      accion: "eliminarCanal",
      id: $(this).attr("data-value")
     }
  };
  ejecutarAjax(parametros);
  return false;
}
function getRtaEliminarCanal(data) {
  if (data.hasOwnProperty('err')) {
     notificar(data.err,2000,'error');
  } 
  else if (data.hasOwnProperty('ok')) {
    notificar(data.ok,2000,'success');
    dibujarTabla();
  }
}

function abrirEditCanal() {
  for(var i = 0; i < all_canales.length; i++){
    if($(this).attr("data-value") == all_canales[i].id){
      $('#edit_nombre_canal_modal').text('Editar '+all_canales[i].nombre);
      $('#edit_nombre_canal_modal').attr("data-value",$(this).attr("data-value"));

      $('#edit_num_canal').val(all_canales[i].numero);
      $('#edit_nom_canal').val(all_canales[i].nombre);
      $('#edit_cat_canal').val(all_canales[i].categoria);
      $('#edit_cal_canal').val(all_canales[i].calidad);
      break;
    }
  }
  $('#modal_mod_canal').modal('show');
}

function editCanal() {
  $('#modal_mod_canal').modal('hide');
  var parametros = {
     url: "./controladores/iptv_admin.php",
     funcion: "getRtaEditCanal",
     formato: "json",
     area: "#contenido",
     msj_prog: "Buscando ...",
     datos: {
      accion: "editarCanal",
      id: $('#edit_nombre_canal_modal').attr("data-value"),
      numero: $('#edit_num_canal').val(),
      nombre: $('#edit_nom_canal').val(),
      categoria: $('#edit_cat_canal').val(),
      calidad: $('#edit_cal_canal').val()
     }
  };
  ejecutarAjax(parametros);
  return false;
}
function getRtaEditCanal(data) {
  if (data.hasOwnProperty('err')) {
     notificar(data.err,2000,'error');
  } 
  else if (data.hasOwnProperty('ok')) {
    notificar(data.ok,2000,'success');
    dibujarTabla();
  }
}

function crearCanal() {
  $('#modal_crear_canal').modal('hide');
  var parametros = {
     url: "./controladores/iptv_admin.php",
     funcion: "getRtaCrearCanal",
     formato: "json",
     area: "#contenido",
     msj_prog: "Buscando ...",
     datos: {
      accion: "crearCanal",
      numero: $('#num_canal').val(),
      nombre: $('#nom_canal').val(),
      categoria: $('#cat_canal').val(),
      calidad: $('#cal_canal').val()
     }
  };
  ejecutarAjax(parametros);
  return false;
}
function getRtaCrearCanal(data) {
  if (data.hasOwnProperty('err')) {
     notificar(data.err,2000,'error');
  } 
  else if (data.hasOwnProperty('ok')) {
    notificar(data.ok,2000,'success');
    dibujarTabla();
  }
}

function dibujarTabla() {
  var parametros = {
     url: "./controladores/iptv_admin.php",
     funcion: "getRtaDibujarTabla",
     formato: "json",
     area: "#contenido",
     msj_prog: "Buscando ...",
     datos: {
      accion: "getCanales"
     }
  };
  ejecutarAjax(parametros);
  return false;
}
function getRtaDibujarTabla(data) {
  if (data.hasOwnProperty('err')) {
     notificar(data.err,2000,'error');
  } 
  else if (data.hasOwnProperty('ok')) {
    if(data.canales.length > 10){
      var html = '<div class="col-12 col-sm-12 col-lg-6" style="padding-left: 20px;padding-right: 20px;">          <div id="table_a" table-id="tab_a">            <div class="pull-right search">              <button data-value="" type="button" class="btn btn_filt">All</button>              <button data-value="HD" type="button" class="btn btn_filt">HD</button>              <button data-value="SD" type="button" class="btn btn_filt">SD</button>              <input class="form-control" type="text" placeholder="Buscar">            </div>            <table id="tab_a" class="table_cmedia">              <thead>                <tr>                  <th class="img_column th_custom"></th>                  <th class="th_custom filt-info" filt-info="true"></th>                  <th class="th_custom filt-info" filt-info="true"></th>                  <th class="th_custom filt-info" filt-info="true"></th>                  <th class="th_custom filt-info" filt-info="true"></th>                  <th class="btn_column th_custom"></th>                </tr>              </thead>';
      html += '<tbody>';
      for(var  i = 0; i < parseInt(data.canales.length/2); i++){
        var logo = data.canales[i]['nombre'].replace(/ /g,"-")+"-["+data.canales[i]['calidad']+"]";
        logo = "../logos/"+logo.toLowerCase()+".png";
        html += '<tr id="tab_a_'+i+'">';
        html += '<td class="align-items-center justify-content-center"><img src="'+logo+'" width="30" height="30"></td>';
        html += '<td class="min_column th_custom" data-info="'+data.canales[i]['numero']+'">'+data.canales[i]['numero']+'</td>';
        html += '<td class="th_custom" data-info="'+data.canales[i]['nombre']+'">'+data.canales[i]['nombre']+'</td>';
        html += '<td class="min_column th_custom" data-info="'+data.canales[i]['calidad']+'">'+data.canales[i]['calidad']+'</td>';
        html += '<td class="min_column th_custom" data-info="'+data.canales[i]['categoria']+'">'+data.canales[i]['categoria']+'</td>';
        html += '<td><div style="display: flex;">';
        html += '<button id="dir-edit-'+data.canales[i]['id']+'" type="button" class="btn btn_edit btn_dir" data-value="'+data.canales[i]['id']+'" aria-label="Editar" data-balloon-pos="up"><i class="fas fa-edit"></i></button>';
        html += '<button id="btn-elim-'+data.canales[i]['id']+'" type="button" class="btn bg-gradient-danger btn_eliminar_grupo btn-list btn_elim" data-value="'+data.canales[i]['id']+'" aria-label="Eliminar" data-balloon-pos="left"><i class="fas fa-trash-alt"></i></button>';
        html += '</div></td></tr>';
      }
      html += '</tbody>';
      html += '            </table>          </div>           </div>          <div class="col-12 col-sm-12 col-lg-6" style="padding-left: 20px;padding-right: 20px;">          <div id="table_a" table-id="tab_b">            <div class="pull-right search">              <button data-value="" type="button" class="btn btn_filt">All</button>              <button data-value="HD" type="button" class="btn btn_filt">HD</button>              <button data-value="SD" type="button" class="btn btn_filt">SD</button>              <input class="form-control" type="text" placeholder="Buscar">            </div>            <table id="tab_b" class="table_cmedia">              <thead>                <tr>                  <th class="img_column th_custom"></th>                  <th class="th_custom filt-info" filt-info="true"></th>                  <th class="th_custom filt-info" filt-info="true"></th>                  <th class="th_custom filt-info" filt-info="true"></th>                  <th class="th_custom filt-info" filt-info="true"></th>                  <th class="btn_column th_custom"></th>                </tr>              </thead>';
      html += '<tbody>';
      var count = 0;
      for(var  i = parseInt(data.canales.length/2); i < data.canales.length; i++){
        var logo = data.canales[i]['nombre'].replace(/ /g,"-")+"-["+data.canales[i]['calidad']+"]";
        logo = "../logos/"+logo.toLowerCase()+".png";
        html += '<tr id="tab_b_'+count+'">';
        count++;
        html += '<td class="align-items-center justify-content-center"><img src="'+logo+'" width="30" height="30"></td>';
        html += '<td class="min_column th_custom" data-info="'+data.canales[i]['numero']+'">'+data.canales[i]['numero']+'</td>';
        html += '<td class="th_custom" data-info="'+data.canales[i]['nombre']+'">'+data.canales[i]['nombre']+'</td>';
        html += '<td class="min_column th_custom" data-info="'+data.canales[i]['calidad']+'">'+data.canales[i]['calidad']+'</td>';
        html += '<td class="min_column th_custom" data-info="'+data.canales[i]['categoria']+'">'+data.canales[i]['categoria']+'</td>';
        html += '<td><div style="display: flex;">';
        html += '<button id="dir-edit-'+data.canales[i]['id']+'" type="button" class="btn btn_edit btn_dir" data-value="'+data.canales[i]['id']+'" aria-label="Editar" data-balloon-pos="up"><i class="fas fa-edit"></i></button>';
        html += '<button id="btn-elim-'+data.canales[i]['id']+'" type="button" class="btn bg-gradient-danger btn_eliminar_grupo btn-list btn_elim" data-value="'+data.canales[i]['id']+'" aria-label="Eliminar" data-balloon-pos="left"><i class="fas fa-trash-alt"></i></button>';
        html += '</div></td></tr>';
      }
      html += '</tbody></table></div></div>';
      $('#tabla_canales').html(html);
      table_a = new initTable($("#table_a"));
      table_b = new initTable($("#table_b"));
    }else{
      var html = '<thead><tr><th class="img_column th_custom"></th><th class="th_custom filt-info" filt-info="true"></th><th class="th_custom filt-info" filt-info="true"></th><th class="th_custom filt-info" filt-info="true"></th><th class="th_custom filt-info" filt-info="true"></th><th class="btn_column th_custom"></th></tr></thead>';
      html += '<tbody>';
      for(var  i = 0; i < data.canales.length; i++){
        var logo = data.canales[i]['nombre'].replace(/ /g,"-")+"-["+data.canales[i]['calidad']+"]";
        logo = "../logos/"+logo.toLowerCase()+".png";
        console.log(logo);
        html += '<tr id="tab_a_'+i+'">';
        html += '<td class="align-items-center justify-content-center"><img src="'+logo+'" width="30" height="30"></td>';
        html += '<td class="min_column th_custom" data-info="'+data.canales[i]['numero']+'">'+data.canales[i]['numero']+'</td>';
        html += '<td class="th_custom" data-info="'+data.canales[i]['nombre']+'">'+data.canales[i]['nombre']+'</td>';
        html += '<td class="min_column th_custom" data-info="'+data.canales[i]['calidad']+'">'+data.canales[i]['calidad']+'</td>';
        html += '<td class="min_column th_custom" data-info="'+data.canales[i]['categoria']+'">'+data.canales[i]['categoria']+'</td>';
        html += '<td><div style="display: flex;">';
        html += '<button id="dir-edit-'+data.canales[i]['id']+'" type="button" class="btn btn_edit btn_dir" data-value="'+data.canales[i]['id']+'" aria-label="Editar" data-balloon-pos="up"><i class="fas fa-edit"></i></button>';
        html += '<button id="btn-elim-'+data.canales[i]['id']+'" type="button" class="btn bg-gradient-danger btn_eliminar_grupo btn-list btn_elim" data-value="'+data.canales[i]['id']+'" aria-label="Eliminar" data-balloon-pos="left"><i class="fas fa-trash-alt"></i></button>';
        html += '</div></td></tr>';
      }
      html += '</tbody>';
      $('#tab_a').html(html);
      table_a = new initTable($("#table_a"));
    }
  }
}


function notificar(mensaje,tiempo,tipo)
{
   const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: tiempo
    });
    Toast.fire({
        type: tipo,
        title: mensaje
    });
    marginAlerta();
}

function marginAlerta()
{
    var desplazamientoActual = $(document).scrollTop();
    
    if(desplazamientoActual > 100){
        $(".swal2-top-end").css("margin-top", "0px");
    }
    if(desplazamientoActual < 100){
        $(".swal2-top-end").css("margin-top", "45px");
    }
}

//Funcion obligatoria para asignar acciones a los botones de la tabla
function initFuncion() {
  $(".btn_elim").unbind('click');
  $(".btn_edit").unbind('click');
  
  $(".btn_elim").on("click", eliminarCanal);
  $(".btn_edit").on("click", abrirEditCanal);
}