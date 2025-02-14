<?php

@include_once "./config.php";
@include_once "./include/db_connect.php";
@include_once "./include/cache_handler.php";

if(!isset($_SESSION)){
    session_start();
}

global $localeData;
global $localePkmnData;
global $localeItemsData;

function get_form_name($pokemon_id, $form_id) {

   global $monsters_json;
   $json = json_decode($monsters_json, true);

   foreach ($json as $name => $pokemon) {

      if ($pokemon['id'] == "$pokemon_id") { 
         if ( $pokemon['form']['id'] == "$form_id" && $pokemon['form']['id'] <> 0) {
            return $pokemon['form']['name'];
         }
      }
   }
}

function get_item_name($item_id) {

   global $items_json;
   $json = json_decode($items_json, true);

   foreach ($json as $id => $item) { 
      if ($id == "$item_id") { 
            return translate_item($item['name']);
      }
   }
}

function get_all_forms($pokemon_id) {

   global $monsters_json;
   $json = json_decode($monsters_json, true);
   $form_exclude = array("Shadow", "Purified", "Copy 2019", "Fall 2019", "Spring 2020", "Vs 2019");
   $forms=array();
   $forms[0] = "All";

   foreach ($json as $name => $pokemon) {

      if ($pokemon['id'] == "$pokemon_id") {
         if ( $pokemon['form']['id'] <> "0" && !in_array( ucfirst($pokemon['form']['name']), $form_exclude ) ) {
            $forms[$pokemon['form']['id']] = $pokemon['form']['name'];
         }
      }
   }
   return $forms;
}

function get_all_mons() {

   global $monsters_json;
   $json = json_decode($monsters_json, true);
   $monsters=array();

   foreach ($json as $name => $pokemon) {
	$arr = explode("_", $name, 2);
        $pokemon_id = $arr[0];
	$monsters[$pokemon_id] = translate_mon($pokemon['name']);
   }
   $monsters=array_unique($monsters);
   return $monsters;
}

function get_nest_species() {

   global $nest_species_json; 
   $json = json_decode($nest_species_json, true);
   $nest_species=$json['all']; 
   return $nest_species;
}

function get_mons($pokemon_id) {

   global $monsters_json;
   $found_name="";
   $json = json_decode($monsters_json, true);
   $monsters=array();

   foreach ($json as $name => $pokemon) {
      $arr = explode("_", $name, 2);
      if ($arr['0'] == "$pokemon_id") {
         $found_name = translate_mon($pokemon['name']); 
      }
   }
  return $found_name; 
}

function translate_mon($word)
{
    $locale = @$_SESSION['locale']; 
    if ($locale == "en") {
        return $word; exit();
    }

    global $localePkmnData;
    global $localePkmnData_json;

    if ($localePkmnData == null) {
        $localePkmnData = json_decode($localePkmnData_json, true);
    }

    if (isset($localePkmnData[$word])) {
        return $localePkmnData[$word];
    } else {
        return $word;
    }
}

function translate_item($word)
{
    $locale = @$_SESSION['locale'];
    if ($locale == "en") {
        return $word; exit();
    }

    global $localeItemsData;
    global $localeItemsData_json;

    if ($localeItemsData == null) {
        $localeItemsData = json_decode($localeItemsData_json, true);
    }

    if (isset($localeItemsData[$word])) {
        return $localeItemsData[$word];
    } else {
        return $word;
    }
}

function get_areas() {

    $areas = $_SESSION['areas']; 
    $areas = array();

    foreach ($_SESSION['areas'] as $i => $area) {

        foreach ($area as $type => $value) { 
                if ($type === "group" ) { $group = $value; }
                if ($type === "name" ) { $areaName = $value;}
                if ($type === "userSelectable" ) { $userselectable = $value;}
                if ($type === "description" ) { $description = $value;}
	}

	if ( $userselectable == 1 || isset($_SESSION['admin_id']) || isset($_SESSION['poracle_admin']) ) {
            if(array_key_exists($group, $areas)){
                $groupAreas = $areas[$group];
                array_push($groupAreas, $areaName);
                $areas[$group] = $groupAreas;
            } else {
                $areas[$group] = array($areaName);
   	    }
	}

    }

    ksort($areas);

    return $areas;
}

function get_raid_bosses_json() {

   include_once "./config.php";
   include_once "./include/db_connect.php";
   global $bosses_json;
   $json = json_decode($bosses_json, true);
   $bosses=array(); 

   foreach ($json as $level => $list_id) { 

      foreach ($list_id as $id => $boss_values) { 
         
         $id = $boss_values['id'];
         $form = $boss_values['form'];
         $evolution = $boss_values['temp_evolution_id'];

         if ( isset($evolution) ) { $boss=$id."_".$form."_".$evolution; } else { $boss=$id."_".$form; }
         array_push($bosses, $boss);

      }

   }
   return $bosses;

}

function get_grunt($type,$gender) {

   global $grunts_json;
   $json = json_decode($grunts_json, true);
   $grunts=array();

   foreach ($json as $key => $value) { 
	   if ( strtoupper($value['type']) == strtoupper($type) && $value['gender'] == $gender ) 
	   { 
                   return $key;
	   }
	   else if ( strtoupper($value['type']) == strtoupper($type) && $gender == 0 )
           {
                   return $key;
           }

   }

}

function get_lure_name($id) {

	if ( $id == "0") {
                $lure_name = "ALL";
        } else if ( $id == "501") { 
		$lure_name = "Normal Lure";
	} else if ( $id == "502") {
		$lure_name = "Glacial Lure";
	} else if ( $id == "503") {
                $lure_name = "Mossy Lure";
        } else if ( $id == "504") {
                $lure_name = "Magnetic Lure";
        } else if ( $id == "505") {
                $lure_name = "Rainy Lure";
        }

        return $lure_name;	

}

function get_gym_name($id) {

        if ( $id == "0") {
                $name = "Harmony";
        } else if ( $id == "1") {
                $name = "Mystic";
        } else if ( $id == "2") {
                $name = "Valor";
        } else if ( $id == "3") {
                $name = "Instinct";
        }

        return $name;

}

function get_gym_color($id) {

        if ( $id == "0") {
                $color = "Grey";
        } else if ( $id == "1") {
                $color = "Blue";
        } else if ( $id == "2") {
                $color = "Red";
        } else if ( $id == "3") {
                $color = "Yellow";
        }

        return $color;

}

function set_locale() {
   global $conn;
   if (isset($_SESSION['id'])) {
      include_once "./config.php";
      include_once "./include/db_connect.php";
      $sql = "select language FROM humans WHERE id = '" . $_SESSION['id'] . "'"; 
      $result = $conn->query($sql) or die(mysqli_error($conn));
      while ($row = $result->fetch_assoc()) {  
         if ( $row['language'] <> "" ) { 
            $_SESSION['locale'] = $row['language'];
         } else { 
            $_SESSION['locale'] = $_SESSION['server_locale'];
         }
      }
   }
}

function get_address($lat, $lon) {

   $filepath=$_SESSION['providerURL']."/reverse?lat=$lat&lon=$lon&format=json";
   if ( strlen($_SESSION['staticKey']) == 32  ) {
           $filepath.="&key=".$_SESSION['staticKey'];
   }

   $request = file_get_contents($filepath);

   $json = json_decode($request, true);

   foreach ($json as $key => $value) {
      if ( is_array($value) ) {
         foreach ($value as $key => $value2) {
            if ( "$key" == "road") { $road = $value2;} 
            if ( "$key" == "village") { $village=$value2;} 
            if ( "$key" == "town") { $town=$value2;} 
            if ( "$key" == "city") { $city=$value2;} 
            if ( "$key" == "city_district") { $city_district=$value2;} 
            if ( "$key" == "country_code") { $country=strtoupper($value2);} 
         }
      }
      if ( @$city_district <> "" ) {  $city=$city_district; }
      if ( @$town <> "" ) {  $city=$town; }
      if ( @$village <> "" ) {  $city=$village; }
   }
   $address = @$road." | ".@$city." | ".@$country;
   return $address;
}


function i8ln($word)
{
    $locale = @$_SESSION['locale'];
    if ($locale == "en") {
        return $word;
    }

    global $localeData;
    if ($localeData == null) {
        $filepath = 'locales/' . $locale . '.json';
        if (file_exists($filepath)) {
            $json_contents = file_get_contents($filepath);
            $localeData = json_decode($json_contents, true);
        } else {
            return $word;
        }
    }

    if (isset($localeData[$word])) {
        return $localeData[$word];
    } else {
        return $word;
    }
}

function stripComments( $str ) 
{
        $str = preg_replace('![ \t]*[^:]//.*[ \t]*[\r\n]!', '', $str);       //Strip single-line comments: '// comment'
        return $str;
}


function checkRemoteFile($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    curl_close($ch);
    if($result !== FALSE)
    {
        return true;
    }
    else
    {
        return false;
    }
}
