<?php

$ruta_log_canales="/var/log/iptv/";
$ruta_canales="/usr/local/bin/";
//$app_kill_canales_hd="kffmpeghd";
$app_clear_canales_hd="cffmpeghd";
//$app_kill_canales_sd="kffmpegsd";
$app_clear_canales_sd="cffmpegsd";

/*$bloque_canales["HD"]= array(
    "prefijo_logger" => "log-hd-",
    "puerto_origen_inicial" => "6201",
    "puerto_destino_inicial" => "7201",
    "num_canales_bloque" => 4,
    "bloques" => array(
      "hd16",
      "hd17",
      "hd18",
      //"hd19",
      "hd20",
      "hd21",
      "hd22",
      "hd23",
      "hd24",
      "hd25",
      "hd26",
      "hd27",
    ),
    "nombre_canales" =>array(
       "hd16" => array(
         //"7201" => "WIN SPORTS",
         "WIN SPORTS",
         "FRECUENCIA F",
         "DHE",
         "CABLENOTICIAS"
       ),
       "hd17" => array(
         "FOX SPORTS 1",
         "FOX SPORTS 2",
         "FOX SPORTS 3",
         "NATGEO KIDS"
       ),
       "hd18" => array(
         "AZ MUNDO",
         "AZ CORAZON",
         "AZ CLIC",
         "AZ CINEMA"
       ),
       "hd19" => array(
         "",
         "",
         "",
         ""
       ),
       "hd20" => array(
         "LIFETIME",
         "TELEMUNDO INTL",
         "TLN",
         "CANAL LAS ESTRELLAS"
       ),
       "hd21" => array(
         "DePELICULA",
         "GOLDEN",
         "EDGE",
         "CINEMAX"
       ),
       "hd22" => array(
         "WARNER CHANNEL",
         "SONY",
         "AXN",
         "!E ENTERTAINMENT"
       ),
       "hd23" => array(
         "A&E",
         "ESPN 1",
         "ESPN 2",
         "ESPN 3"
       ),
       "hd24" => array(
         "HISTORY CHANNEL 1",
         "HISTORY CHANNEL 2",
         "BABY Tv",
         "N/D"
       ),
       "hd25" => array(
         "NATGEO",
         "NATGEO WILD",
         "CINECANAL",
         "FXM"
       ),
       "hd26" => array(
         "FX",
         "FOX CHANNEL",
         "FOXLIFE",
         "STUDIO UNIVERSAL"
       ),
       "hd27" => array(
         "UNIVERSAL",
         "SyFy",
         "CIVICA Tv",
         "FRONTERA Tv"
       )
    )
);*/
$bloque_canales["HD"]= array(
    "puerto_origen_inicial" => "6201",
    "puerto_destino_inicial" => "9201",
    "nombre_canales" =>array(
         "WIN SPORTS",
         "FRECUENCIA F",
         "DHE",
         "CABLENOTICIAS",
         "FOX SPORTS 1",
         "FOX SPORTS 2",
         "FOX SPORTS 3",
         "NATGEO KIDS",
         "AZ MUNDO",
         "AZ CORAZON",
         "AZ CLIC",
         "AZ CINEMA",
         "LIFETIME",
         "TELEMUNDO INTL",
         "TLN",
         "CANAL LAS ESTRELLAS",
         "DePELICULA",
         "GOLDEN",
         "EDGE",
         "CINEMAX",
         "WARNER CHANNEL",
         "SONY",
         "AXN",
         "!E ENTERTAINMENT",
         "A&E",
         "ESPN 1",
         "ESPN 2",
         "ESPN 3",
         "HISTORY CHANNEL 1",
         "HISTORY CHANNEL 2",
         "BABY Tv",
         "N/D",
         "NATGEO",
         "NATGEO WILD",
         "CINECANAL",
         "FXM",
         "FX",
         "FOX CHANNEL",
         "FOXLIFE",
         "STUDIO UNIVERSAL",
         "UNIVERSAL",
         "SyFy",
         "CIVICA Tv",
         "FRONTERA Tv",
    )
);

/*$bloque_canales["SD"]= array(
    "prefijo_logger" => "log-sd-",
    "puerto_origen_inicial" => "6101",
    "puerto_destino_inicial" => "7101",
    "num_canales_bloque" => 8,
    "bloques" => array(
      "sd01",
      "sd02",
      "sd03",
      "sd04",
      "sd05",
      "sd06",
      "sd07",
      "sd08",
      "sd09",
      "sd10",
      "sd11",
      "sd12",
    ),
    "nombre_canales" =>array(
       "sd01" => array(
          "DISNEY Jr",
          "DISCOVERY KIDS",
          "NICKELODEON",
          "NATGEO KIDS",
          "ESPN 3",
          "FOX SPORTS 1",
          "FOX SPORTS 3",
          "DISCOVERY CHANNEL"
        ),
       "sd02" => array(
          "DISCOVERY SCIENCE",
          "HISTORY CHANNEL 1",
          "CARACOL NACIONAL",
          "ANIMAL PLANET",
          "NATGEO",
          "NATGEO WILD",
          "RCN NACIONAL",
          "VePLUS"
        ),
       "sd03" => array(
          "LIFETIME",
          "TELEMUNDO",
          "ESPN 1",
          "TNT",
          "TNT SERIES",
          "CINECANAL",
          "TCM",
          "DW LATAM"
        ),
       "sd04" => array(
          "FOX SPORTS 2",
          "DISCOVERY CIVILIZATION",
          "RUMBA Tv",
          "FOX CHANNEL",
          "FX",
          "TELEISLAS",
          "N/D",
          "TELECAFE"
        ),
       "sd05" => array(
          "De PELICULA",
          "GOLDEN",
          "GOLDEN EDGE",
          "CINEMA +",
          "SPACE",
          "DHE",
          "DISNEY CHANNEL",
          "DISNEY XD"
        ),
       "sd06" => array(
          "BABY Tv",
          "CARTOON NETWORK",
          "TOONCAST",
          "BOOMERANG",
          "ESPN 2",
          "WIN SPORTS",
          "CableEXITO",
          "DISCOVERY TURBO"
        ),
       "sd07" => array(
          "HISTORY CHANNEL 2",
          "DISCOVERY H&H",
          "TNT",
          "CIVICA Tv",
          "TvAGRO",
          "TLN",
          "CANAL LAS ESTRELLAS",
          "CINEMAX"
        ),
       "sd08" => array(
          "WARNER CHANNEL",
          "FXM",
          "FOXLIFE",
          "STUDIO",
          "UNIVERSAL",
          "SyFy",
          "TRU Tv",
          "SONY"
        ),
       "sd09" => array(
          "AXN",
          "!E ENTERTAINMENT",
          "A&E",
          "ID DISCOVERY",
          "TLC",
          "N/D",
          "CNN LATAM",
          "VENEVISION"
        ),
       "sd10" => array(
          "TELEVEN",
          "TELEAMIGA",
          "BETHEL",
          "ATN",
          "TLN",
          "MI GENTE Tv",
          "HTV",
          "ZOOM"
        ),
       "sd11" => array(
          "J Tv","N/D",
          "MTv LATINO",
          "COMEDY CENTRAL",
          "CANAL CONGRESO",
          "TBS",
          "N/D",
          "PARAMOUNT PICTURES"
        ),
       "sd12" => array(
          "CITY Tv",
          "CRISTOVISION",
          "EWTN",
          "ENLACE",
          "N/D",
          "DW",
          "TELEMICRO",
          "TELEANTILLAS"
       ),
    )
);*/
$bloque_canales["SD"]= array(
    "puerto_origen_inicial" => "6101",
    "puerto_destino_inicial" => "9101",
    "nombre_canales" =>array(
          "DISNEY Jr",
          "DISCOVERY KIDS",
          "NICKELODEON",
          "NATGEO KIDS",
          "ESPN 3",
          "FOX SPORTS 1",
          "FOX SPORTS 3",
          "DISCOVERY CHANNEL",
          "DISCOVERY SCIENCE",
          "HISTORY CHANNEL 1",
          "CARACOL NACIONAL",
          "ANIMAL PLANET",
          "NATGEO",
          "NATGEO WILD",
          "RCN NACIONAL",
          "VePLUS",
          "LIFETIME",
          "TELEMUNDO",
          "ESPN 1",
          "TNT",
          "TNT SERIES",
          "CINECANAL",
          "TCM",
          "DW LATAM",
          "FOX SPORTS 2",
          "DISCOVERY CIVILIZATION",
          "RUMBA Tv",
          "FOX CHANNEL",
          "FX",
          "TELEISLAS",
          "N/D",
          "TELECAFE",
          "De PELICULA",
          "GOLDEN",
          "GOLDEN EDGE",
          "CINEMA +",
          "SPACE",
          "DHE",
          "DISNEY CHANNEL",
          "DISNEY XD",
          "BABY Tv",
          "CARTOON NETWORK",
          "TOONCAST",
          "BOOMERANG",
          "ESPN 2",
          "WIN SPORTS",
          "CableEXITO",
          "DISCOVERY TURBO",
          "HISTORY CHANNEL 2",
          "DISCOVERY H&H",
          "TNT",
          "CIVICA Tv",
          "TvAGRO",
          "TLN",
          "CANAL LAS ESTRELLAS",
          "CINEMAX",
          "WARNER CHANNEL",
          "FXM",
          "FOXLIFE",
          "STUDIO",
          "UNIVERSAL",
          "SyFy",
          "TRU Tv",
          "SONY",
          "AXN",
          "!E ENTERTAINMENT",
          "A&E",
          "ID DISCOVERY",
          "TLC",
          "N/D",
          "CNN LATAM",
          "VENEVISION",
          "TELEVEN",
          "TELEAMIGA",
          "BETHEL",
          "ATN",
          "TLN",
          "MI GENTE Tv",
          "HTV",
          "ZOOM",
          "J Tv","N/D",
          "MTv LATINO",
          "COMEDY CENTRAL",
          "CANAL CONGRESO",
          "TBS",
          "N/D",
          "PARAMOUNT PICTURES",
          "CITY Tv",
          "CRISTOVISION",
          "EWTN",
          "ENLACE",
          "N/D",
          "DW",
          "TELEMICRO",
          "TELEANTILLAS"
    )
);


?>
