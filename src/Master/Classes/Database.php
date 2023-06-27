<?php
	
namespace Masterzain\Classes;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database {
    function __construct() {
        $capsule = new Capsule;
		$connection = config('database.connections.'. config('database.default') );
        $capsule->addConnection(
         $connection
        );
        $capsule->bootEloquent();
    }
}
