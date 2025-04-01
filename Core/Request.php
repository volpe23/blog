<?php

namespace Core;

class Request {
    private array $query;
    private array $body;
    private array $server;
    public function __construct(array $query, array $body, array $server)
    {
        $this->query = $query;
        $this->body = $body;
        $this->server = $server; 
    }
}