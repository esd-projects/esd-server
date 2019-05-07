<?php
/**
 * Created by PhpStorm.
 * User: administrato
 * Date: 2019/4/28
 * Time: 17:45
 */

namespace GoSwoole\Go;


use GoSwoole\BaseServer\Server\Beans\Request;
use GoSwoole\BaseServer\Server\Beans\Response;
use GoSwoole\BaseServer\Server\Beans\WebSocketFrame;
use GoSwoole\BaseServer\Server\ServerPort;

class GoPort extends ServerPort
{

    public function onTcpConnect(int $fd, int $reactorId)
    {
        // TODO: Implement onTcpConnect() method.
    }

    public function onTcpClose(int $fd, int $reactorId)
    {
        // TODO: Implement onTcpClose() method.
    }

    public function onTcpReceive(int $fd, int $reactorId, string $data)
    {
        // TODO: Implement onTcpReceive() method.
    }

    public function onUdpPacket(string $data, array $client_info)
    {
        // TODO: Implement onUdpPacket() method.
    }

    public function onHttpRequest(Request $request, Response $response)
    {

    }

    public function onWsMessage(WebSocketFrame $frame)
    {
        // TODO: Implement onWsMessage() method.
    }

    public function onWsOpen(Request $request)
    {
        // TODO: Implement onWsOpen() method.
    }

    public function onWsPassCustomHandshake(Request $request): bool
    {
        // TODO: Implement onWs
    }
}