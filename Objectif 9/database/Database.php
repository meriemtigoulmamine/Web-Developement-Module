<?php
class Database
{
    private const HOST     = "localhost";
    private const USER     = "root";
    private const PASSWORD = "";
    private const NAME     = "TDW";

    private static $connection = null;

    public static function getConnection()
    {
        if (self::$connection === null) {
            $c = @mysqli_connect(self::HOST, self::USER, self::PASSWORD, self::NAME);
            if (!$c) {
                die("Database connection failed: " . mysqli_connect_error());
            }
            mysqli_set_charset($c, "utf8mb4");
            self::$connection = $c;
        }
        return self::$connection;
    }
}