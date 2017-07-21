<?php

namespace csvimport;

use csvimport\DBConnect;

include_once './DBConnect.php';

class ReplaceClass {

    private $host = '127.0.0.1';
    private $user = 'root';
    private $password = 'root';
    private $database = 'testdb';
    private $newDbConnect;
    private $org_value;
    private $org_id_record;
    private $org_tag_type_id;
    private $org_status;
    private $org_lp;
    private $first_element_id;
    private $first_element_value;
    private $item_to_replace;
    private $id_tag_to_replace;

    public function __construct() {
        $this->newDbConnect = new DBConnect($this->host, $this->user, $this->password, $this->database);
        $this->createBackupTable();
        $this->getRecords();
    }

    private function getRecords() {
        $sql = "SELECT * FROM Tag";
        $result = $this->newDbConnect->execute($sql);

        foreach ($result as $rekord) {
            $this->org_id_record = $rekord['id'];
            $this->org_tag_type_id = $rekord['tag_type_id'];
            $this->org_value = $rekord['value'];
            $this->org_status = $rekord['status'];
            $this->org_lp = $rekord['lp'];

            $this->getRecordToReplace();
        }
    }

    private function getRecordToReplace() {
        $sql = "SELECT * FROM item2tag where tag_id = " . $this->org_id_record . ";";
        $result = $this->newDbConnect->execute($sql);
        $status = "Status OK";
        $this->getRecordWithMinId();
        foreach ($result as $rekord) {
            $this->item_to_replace = $rekord['item_id'];
            $this->id_tag_to_replace = $rekord['tag_id'];
            if ($this->id_tag_to_replace != $this->first_element_id) {
                printf("Replaced item_id: %d, Org tag_id: %d, Replaced by: %d<br />", $this->item_to_replace, $rekord['tag_id'], $this->first_element_id);
                $status = "<b>Status To Replace</b>";
                $this->updateRecord();
            }
        }
        printf("Wyszukiwanie taga: %d: %s. Status: %s<br />", $this->org_id_record, $this->org_value, $status);
    }

    private function getRecordWithMinId() {
        $sql = "SELECT min(id) id, tag_type_id, value, status FROM (SELECT id, tag_type_id, trim(value) AS value, status FROM Tag ORDER BY value DESC) AS record WHERE  tag_type_id = " . $this->org_tag_type_id . " and value = trim(\"" . $this->org_value . "\") AND status = " . $this->org_status . " group by value, tag_type_id, value, status;";
        $result = $this->newDbConnect->execute($sql);

        foreach ($result as $rekord) {
            $this->first_element_id = $rekord['id'];
            $this->first_element_value = $rekord['value'];
        }
        return true;
    }

    private function updateRecord() {
        $sql = "UPDATE item2tag set tag_id = \"" . $this->first_element_id . "\" where item_id = " . $this->item_to_replace . ";";
        $this->newDbConnect->execute($sql);
        $this->moveWrongTag();
        return true;
    }

    private function moveWrongTag() {
        $sql = "INSERT INTO Tag_wrong (id, tag_type_id, value, status, lp) SELECT id, tag_type_id, value, status, lp FROM Tag where id = " . $this->id_tag_to_replace . ";";
        $this->newDbConnect->execute($sql);
        $this->removeWrongTag();
        return true;
    }

    private function removeWrongTag() {
        $sql = "DELETE FROM Tag where id = " . $this->id_tag_to_replace . ";";
        $this->newDbConnect->execute($sql);
        return true;
    }

    private function createBackupTable() {
        $sql = "SELECT id from Tag_wrong";
        $result = $this->newDbConnect->execute($sql);

        if (empty($result)) {
            $query = "CREATE TABLE `Tag_wrong` (
                `id` int(11) NOT NULL,
                `tag_type_id` int(11) NOT NULL,
                `value` varchar(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                `status` int(11) NOT NULL,
                `lp` int(11) DEFAULT '0'
              ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

            $result = $this->newDbConnect->execute($query);
        }
        
        return true;
    }

}
