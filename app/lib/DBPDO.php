<?php
namespace app\lib;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

class DBPDO
{
    /**
     * @var PDO
     */
    public static $dbLink = null;
	public static $log = '';
	public static $logTime = 0;

	public static function dbConnect()
	{
		$hostname = Config::DB_HOST;
		$username = Config::DB_USERNAME;
		$password = Config::DB_PASS;
		$dbName = Config::DB_NAME;
//		$dbPort = Config::DB_PORT;

//		self::$dbLink = mysqli_connect($hostname, $username, $password, $dbName, $dbPort);
        try {
            self::$dbLink = new PDO('mysql:host=' . $hostname . ';dbname=' . $dbName, $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            print "DB connect error: " . $e->getMessage();
            die();
        }
		if(!self::$dbLink) die('Could not connect to DB');
	}

	public static function query($query, $params = [])
	{
		if (!self::$dbLink) self::dbConnect();
		$dbLink = self::$dbLink;
		if (!$query) return null;
//        $start = microtime(true);
		if (Config::DB_LOG) {
		    self::$log .= $query . "\n";
        }

        try {
            $statement = $dbLink->prepare($query); //print $query.'<br>'; //        print_r($params);
            $statement->execute($params);
        } catch (Exception $e) {
            $statement = false;
            $logger = new Logger("upload/temp/logs/error_db.txt");
            $logger->write(
                $e->getMessage() . "\n"
                . $query . "\n"
                . print_r($params, true)
            );
            $logger->close();
        }
//        $statement->debugDumpParams();die();

//        $time = microtime(true) - $start;
        return $statement;
	}

	public static function fetchAssoc(PDOStatement $statement)
	{
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public static function getData($query, $params = [])
	{
		$arr = array();
        $statement = self::query($query, $params);
		if ($statement && $statement->rowCount()) {
			$arr = $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		return $arr;
	}

	public static function getDataOne($query, $params = [])
	{
		$arr = array();
//        print $query; print_r($params);
        $statement = self::query($query, $params);
		if ($statement && $statement->rowCount()) {
            $arr = self::fetchAssoc($statement);
        }
		return $arr;
	}

	public static function numRows(PDOStatement $statement)
	{
		return $statement->rowCount();
	}

	public static function insertId()
	{
		return self::$dbLink ? self::$dbLink->lastInsertId() : 0;
	}

	public static function error(): string
    {
		return self::$dbLink ? self::$dbLink->errorCode() . ' ' . print_r(self::$dbLink->errorInfo(), true) : '';
	}

	public static function dbClose($dbLink = null) {
        self::$dbLink = null;
	}

	public static function beginTransaction()
    {
        if (!self::$dbLink) self::dbConnect();
        self::$dbLink->beginTransaction();
	}

	public static function commit()
    {
        self::$dbLink->commit();
	}

	public static function rollBack()
    {
        self::$dbLink->rollBack();
	}
}
?>