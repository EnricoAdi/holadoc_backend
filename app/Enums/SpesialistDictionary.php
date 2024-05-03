<?php

namespace App\Enums;

use InvalidArgumentException;

class SpesialistDictionary
{
  private $data = [
    "Umum" => "",
    "Ortopedi" => ", Sp.OT",
    "Anak" => ", Sp.A",
    "Penyakit Dalam" => ", Sp.PD",
    "THT" => ", Sp.THT",
    "Paru-paru" => ", Sp.P",
  ];

  public function set($key, $value)
  {
    $this->data[$key] = $value;
  }

  public function get($key)
  {
    if (!array_key_exists($key, $this->data)) {
      throw new InvalidArgumentException("Key '$key' does not exist in the list");
    }

    return $this->data[$key];
  }
}
  // Accessing values
//   echo $specialists["Umum"]; // Output: ""
//   echo $specialists["Ortopedi"]; // Output: "Sp.OT"

//   // Adding a new entry
//   $specialists["Gigi"] = "Sp.GM";

//   // Looping through the hashmap
//   foreach ($specialists as $key => $value) {
//     echo "Specialist for $key: $value" . PHP_EOL;
//   }
