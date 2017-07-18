<?php

namespace csvimport;

interface DBConnectInterface {
    public function connect();
    public function execute($sql);
}
