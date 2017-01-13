<?php
namespace lib;

class MySQLConnector {
	public static function executeSql($sql) {
		$server = 'localhost';
		$port = 3306;
		$db = 'slim_test';
		$dsn = "mysql:host=$server:$port;dbname=$db";
		$user = 'test_user';
		$pass = '';

		$dbo = new \PDO($dsn, $user, $pass);
		$query = $dbo->prepare($sql);
		$query->execute();
		$data = [];
		$sql = trim($sql);

		if(stripos($sql, 'select') === 0) {
			while($row = $query->fetch(\PDO::FETCH_ASSOC)) {
				$data[] = $row;
			}

			return $data;
		} else {
			return $query->rowCount();
		}
	}
}