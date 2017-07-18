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
    private $pattern = array("/„/", "/”/", "/'/", "/\//", "/\"/", "/</", "/>/", "/\|/", "/;/", "/\)/", "/\(/", "/“/", "/\?/", "/«/", "/»/");
    private $replacement = array("", "", "", "", "", "", "", "", " ", "", "", "", "", "");

    public function __construct() {
        $this->newDbConnect = new DBConnect($this->host, $this->user, $this->password, $this->database);

        $this->getRecords();
    }

    private function getRecords() {
        $sql = "SELECT * FROM TAG_Test order by value DESC";
        $result = $this->newDbConnect->execute($sql);

        foreach ($result as $rekord) {
            $db_result = $rekord['value'];
            $id = $rekord['id'];
            $result = $this->compareResult($db_result);
            printf($result);
        }
    }

    private function compareResult($data) {
        $tag = str_replace('\\', '', $data);
        $tag = preg_replace($this->pattern, $this->replacement, $tag);
        printf("<p>Org_data: %s, Tag: %s</p>", $data, $tag);
        
        if($data !== $tag) {
            return true;
        } else {
            return false;
        }
    }

}
