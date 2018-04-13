<?php
/**
 * Created by PhpStorm.
 * User: ShaunXu
 * Date: 2018/4/12
 * Time: 11:31
 */

Yaf_Loader::import( LIB_PATH."/ServerCore.php" );
/**
 * Class WebsocketServer
 * author ShaunXu
 * date 2018/4/12
 */
class WebsocketServer extends ServerCore
{
    /**
     * 实例对象
     * @Variable instance
     * @author ShaunXu
     * @var null
     */
    protected static $instance = null;

    /**
     * 获取实例
     * getInstance
     * @author ShaunXu
     * @date 2018/4/12
     * @return null|WebsocketServer
     */
    public static function getInstance() {
        if (empty(self::$instance) || !(self::$instance instanceof WebsocketServer)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * __construct
     */
    private function __construct() {
    }

    /**
     * 开启websocket服务器
     * start
     * @author ShaunXu
     * @date 2018/4/12
     */
    public function start() {
        $this->serverObj = new swoole_websocket_server($this->defaultIp, $this->defaultPort);
        $this->serverObj->on('open', array($this, 'onOpen'));
        $this->serverObj->on('message', array($this, 'onMessage'));
        $this->serverObj->on('close', array($this, 'onClose'));
        $this->serverObj->start();
    }

    /**
     *
     * onOpen
     * @author ShaunXu
     * @date 2018/4/12
     * @param swoole_websocket_server $server
     * @param swoole_websocket_request $request
     */
    public function onOpen(swoole_websocket_server $server, $request){
        echo "server: handshake success with fd{$request->fd}\n";
    }

    /**
     *
     * onMessage
     * @author ShaunXu
     * @date 2018/4/12
     * @param swoole_websocket_server $server
     * @param $frame
     */
    public function onMessage(swoole_websocket_server $server, $frame){
        foreach ($server->connections as $fd) {
            if($frame->fd != $fd){
                $server->push($fd, $frame->data);
            }
        }
    }

    /**
     *
     * onClose
     * @author ShaunXu
     * @date 2018/4/12
     * @param swoole_websocket_server $server
     * @param $fd
     */
    public function onClose(swoole_websocket_server $server, $fd){
        echo "client {$fd} closed\n";
    }
}