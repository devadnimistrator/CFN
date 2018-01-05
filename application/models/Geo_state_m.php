<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Geo_state_m extends My_Model {

  var $states = array();

  public function __construct($id = 0) {
    parent::__construct($id);

    $this->states["US"]["AL"] = "Alabama";
    $this->states["US"]["AK"] = "Alaska";
    $this->states["US"]["AZ"] = "Arizona";
    $this->states["US"]["AR"] = "Arkansas";
    $this->states["US"]["CA"] = "California";
    $this->states["US"]["CO"] = "Colorado";
    $this->states["US"]["CT"] = "Connecticut";
    $this->states["US"]["DE"] = "Delaware";
    $this->states["US"]["DC"] = "District of Columbia";
    $this->states["US"]["FL"] = "Florida";
    $this->states["US"]["GA"] = "Georgia";
    $this->states["US"]["HI"] = "Hawaii";
    $this->states["US"]["ID"] = "Idaho";
    $this->states["US"]["IL"] = "Illinois";
    $this->states["US"]["IN"] = "Indiana";
    $this->states["US"]["IA"] = "Iowa";
    $this->states["US"]["KS"] = "Kansas";
    $this->states["US"]["KY"] = "Kentucky";
    $this->states["US"]["LA"] = "Louisiana";
    $this->states["US"]["ME"] = "Maine";
    $this->states["US"]["MD"] = "Maryland";
    $this->states["US"]["MA"] = "Massachusetts";
    $this->states["US"]["MI"] = "Michigan";
    $this->states["US"]["MN"] = "Minnesota";
    $this->states["US"]["MS"] = "Mississippi";
    $this->states["US"]["MO"] = "Missouri";
    $this->states["US"]["MT"] = "Montana";
    $this->states["US"]["NE"] = "Nebraska";
    $this->states["US"]["NV"] = "Nevada";
    $this->states["US"]["NH"] = "New Hampshire";
    $this->states["US"]["NJ"] = "New Jersey";
    $this->states["US"]["NM"] = "New Mexico";
    $this->states["US"]["NY"] = "New York";
    $this->states["US"]["NC"] = "North Carolina";
    $this->states["US"]["ND"] = "North Dakota";
    $this->states["US"]["OH"] = "Ohio";
    $this->states["US"]["OK"] = "Oklahoma";
    $this->states["US"]["OR"] = "Oregon";
    $this->states["US"]["PA"] = "Pennsylvania";
    $this->states["US"]["RI"] = "Rhode Island";
    $this->states["US"]["SC"] = "South Carolina";
    $this->states["US"]["SD"] = "South Dakota";
    $this->states["US"]["TN"] = "Tennessee";
    $this->states["US"]["TX"] = "Texas";
    $this->states["US"]["UT"] = "Utah";
    $this->states["US"]["VT"] = "Vermont";
    $this->states["US"]["VA"] = "Virginia";
    $this->states["US"]["WA"] = "Washington";
    $this->states["US"]["WV"] = "West Virginia";
    $this->states["US"]["WI"] = "Wisconsin";
    $this->states["US"]["WY"] = "Wyoming";

    $this->states["MX"]["AGU"] = "Aguascalientes";
    $this->states["MX"]["BCS"] = "Baja California Sur";
    $this->states["MX"]["CAM"] = "Campeche";
    $this->states["MX"]["CHP"] = "Chiapas";
    $this->states["MX"]["CHH"] = "Chihuahua";
    $this->states["MX"]["COA"] = "Coahuila";
    $this->states["MX"]["COL"] = "Colima";
    $this->states["MX"]["DUR"] = "Durango";
    $this->states["MX"]["BCN"] = "Estado de Baja California";
    $this->states["MX"]["MEX"] = "Estado de Mexico";
    $this->states["MX"]["GUA"] = "Guanajuato";
    $this->states["MX"]["GRO"] = "Guerrero";
    $this->states["MX"]["HID"] = "Hidalgo";
    $this->states["MX"]["JAL"] = "Jalisco";
    $this->states["MX"]["CMX"] = "Ciudad de México";
    $this->states["MX"]["MIC"] = "Michoacán";
    $this->states["MX"]["MOR"] = "Morelos";
    $this->states["MX"]["NAY"] = "Nayarit";
    $this->states["MX"]["NLE"] = "Nuevo León";
    $this->states["MX"]["OAX"] = "Oaxaca";
    $this->states["MX"]["PUE"] = "Puebla";
    $this->states["MX"]["QUE"] = "Querétaro";
    $this->states["MX"]["ROO"] = "Quintana Roo";
    $this->states["MX"]["SLP"] = "San Luis Potosí";
    $this->states["MX"]["SIN"] = "Sinaloa";
    $this->states["MX"]["SON"] = "Sonora";
    $this->states["MX"]["TAB"] = "Tabasco";
    $this->states["MX"]["TAM"] = "Tamaulipas";
    $this->states["MX"]["TLA"] = "Tlaxcala";
    $this->states["MX"]["VER"] = "Veracruz";
    $this->states["MX"]["YUC"] = "Yucatán";
    $this->states["MX"]["ZAC"] = "Zacatecas";
  }

  public function get_states_by_country($country_code) {
    return $this->states[$country_code];


//    $this->db->select(array("subdivision_1_iso_code", "subdivision_1_name"));
//    $this->db->where("country_iso_code", $country_code);
//    $this->db->order_by("subdivision_1_iso_code");
//    $query = $this->db->get($this->table);
//
//    $result = $query->result();
//    
//    $this->states = array();
//    foreach ($result as $state) {
//      $this->states[$state->subdivision_1_iso_code] = $state->subdivision_1_name;
//    }
  }

  public function get_state_name($country_code, $state_code) {
    return $this->states[$country_code][$state_code];
  }

}
