<?php

class DatabaseConnect
{
    /**
     * needsShippingCost カートの中身より送料が発生するか判定
     * @param  [Array] $cartsItems
     * @return [bool]
     */
    public function dbInit($cartsItems)
    {
        $url = parse_url(getenv('DATABASE_URL'));
        $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));
        $pdo = new PDO($dsn, $url['user'], $url['pass']);
    }
}
