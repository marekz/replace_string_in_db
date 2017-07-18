<?php

namespace csvimport;

use csvimport\DBConnect;

include_once './DBConnect.php';

/**
 * Description of ReplaceClass
 *
 * @author mzdybel
 */
class ReplaceClass {

    private $host = '127.0.0.1';
    private $user = 'root';
    private $password = 'root';
    private $database = 'testdbkp';
    private $newDbConnect;
    private $regex_pattern = '/[„”\'\/]/';
    private $pattern = array("/„/","/”/","/'/","/\//","/\"/","/</","/>/","/\|/","/;/","/\)/","/\(/","/“/","/\?/","/«/","/»/","/\./");
    private $replacement = array("","","","","","","",""," ","","","","","","");
// « Libella »
    public function __construct() {
        $this->newDbConnect = new DBConnect($this->host, $this->user, $this->password, $this->database);

        $this->getRecords();
    }

    private function getRecords() {
        $sql = "SELECT * FROM TAG_Test order by value DESC";
        $result = $this->newDbConnect->execute($sql);

        foreach ($result as $rekord) {
            
            $tag = str_replace('\\', '', $rekord['value']);
            
//            if(preg_match($this->regex_pattern, $tag)){
                $tag = preg_replace($this->pattern, $this->replacement, $tag);
                printf("<p>Value: %s</p>", $tag);
//            }
            
        }
    }

}
