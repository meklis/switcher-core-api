<?php

namespace App\Application\Actions\RR;

use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Spiral\Goridge;

class RestartAll extends Action
{
    protected $rpc;

    function __construct(Goridge\RPC\RPC $rpc, LoggerInterface $logger)
    {
        $this->rpc = $rpc;
        parent::__construct($logger);
    }
    protected function action(): Response
    {
        return $this->respondWithData($this->rpc->call('resetter.Reset', 'http'));
    }

}