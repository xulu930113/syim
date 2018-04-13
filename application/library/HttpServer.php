<?php
Yaf_Loader::import( LIB_PATH."/ServerCore.php" );

/**
 * Http服务器
 * Class HttpServer
 * author ShaunXu
 * date 2018/4/12
 */
class HttpServer extends ServerCore
{
    /**
     *
     * @Variable yafAppObj
     * @author ShaunXu
     * @var
     */
    private $yafAppObj;

    /**
     *
     * @Variable instance
     * @author ShaunXu
     * @var null
     */
    protected static $instance = null;

    /**
     *
     * getInstance
     * @desc
     * @author ShaunXu
     * @date 2018/4/12
     * @return HttpServer|null
     */
    public static function getInstance()
    {
        if (empty(self::$instance) || !(self::$instance instanceof HttpServer)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * HttpServer constructor.
     */
    private function __construct()
    {
    }

    /**
     * 开启任务
     * start
     * @author ShaunXu
     * @date 2018/4/12
     */
    public function start()
    {
        $this->serverObj = new swoole_http_server($this->defaultIp, $this->defaultPort);
        $this->serverObj->set($this->serverConfig['swoole']);
        $this->serverObj->on('Start', array($this, 'onStart'));
        $this->serverObj->on('ManagerStart', array($this, 'onManagerStart'));
        $this->serverObj->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serverObj->on('WorkerStop', array($this, 'onWorkerStop'));
        $this->serverObj->on('request', array($this, 'onRequest'));
        $this->serverObj->start();
    }

    /**
     * 创建进程
     * onStart
     * @author ShaunXu
     * @date 2018/4/12
     * @param swoole_http_server $serverObj
     * @return bool
     */
    public function onStart(swoole_http_server $serverObj)
    {
        swoole_set_process_name($this->serverConfig['server']['master_process_name']);
        return true;
    }

    /**
     * 管理进程开启
     * onManagerStart
     * @author ShaunXu
     * @date 2018/4/12
     * @param swoole_http_server $serverObj
     * @return bool
     */
    public function onManagerStart(swoole_http_server $serverObj)
    {
        //rename
        swoole_set_process_name($this->serverConfig['server']['manager_process_name']);
        return true;
    }

    /**
     * 工作进程开启
     * onWorkerStart
     * @author ShaunXu
     * @date 2018/4/12
     * @param swoole_http_server $serverObj
     * @param $workerId
     * @return bool
     */
    public function onWorkerStart(swoole_http_server $serverObj, $workerId)
    {
        //rename
        $processName = sprintf($this->serverConfig['server']['event_worker_process_name'], $workerId);
        swoole_set_process_name($processName);
        //实例化yaf
        $this->yafAppObj = new Yaf_Application($this->appConfigFile);
        return true;
    }

    /**
     * 工作进程停止
     * onWorkerStop
     * @author ShaunXu
     * @date 2018/4/12
     * @param swoole_http_server $serverObj
     * @param $workerId
     * @return bool
     */
    public function onWorkerStop(swoole_http_server $serverObj, $workerId)
    {
        return true;
    }

    /**
     * http 请求部分
     * onRequest
     * @author ShaunXu
     * @date 2018/4/12
     * @param swoole_http_request $request
     * @param swoole_http_response $response
     */
    public function onRequest(swoole_http_request $request, swoole_http_response $response)
    {
        //注册全局信息
        $this->initRequestParam($request);
        Yaf_Registry::set('SWOOLE_HTTP_REQUEST', $request);
        Yaf_Registry::set('SWOOLE_HTTP_RESPONSE', $response);
        //执行
        ob_start();
        try {
            $requestObj = new Yaf_Request_Http($request->server['request_uri']);
            $configArr  = Yaf_Application::app()->getConfig()->toArray();
            if (!empty($configArr['application']['baseUri'])) { //set base_uri
                $requestObj->setBaseUri($configArr['application']['baseUri']);
            }
            $this->yafAppObj->bootstrap()->getDispatcher()->dispatch($requestObj);
        } catch (Yaf_Exception $e) {
            var_dump($e);
        }
        $result = ob_get_contents();
        ob_end_clean();
        $response->end($result);
    }
}