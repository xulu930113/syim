<?php
/**
 * Created by PhpStorm.
 * User: ShaunXu
 * Date: 2018/4/12
 * Time: 13:46
 */

/**
 * desc
 * Class ServerCore
 * author ShaunXu
 * date 2018/4/12
 */
class ServerCore
{
    /**
     * 默认监听绑定IP
     * Variable defaultIp
     * @author ShaunXu
     * @var string
     */
    protected $defaultIp = '0.0.0.0';

    /**
     * 默认监听绑定端口
     * Variable defaultPort
     * @author ShaunXu
     * @var int
     */
    protected $defaultPort = 8080;

    /**
     * 设置server_config
     * Variable serverConfig
     * @author ShaunXu
     * @var
     */
    protected $serverConfig;

    /**
     * 设置app_Config
     * Variable appConfigFile
     * @author ShaunXu
     * @var
     */
    protected $appConfigFile;

    /**
     * 服务器对象
     * Variable serverObj
     * @author ShaunXu
     * @var
     */
    protected $serverObj;

    /**
     * 设置Server_Config
     * setServerConfigIni
     * @desc
     * @author ShaunXu
     * @date 2018/4/12
     * @param $serverConfigIni
     */
    public function setServerConfigIni($serverConfigIni)
    {
        if (!is_file($serverConfigIni)) {
            trigger_error('Server Config File Not Exist!', E_USER_ERROR);
        }
        $serverConfig = parse_ini_file($serverConfigIni, true);
        if (empty($serverConfig)) {
            trigger_error('Server Config Content Empty!', E_USER_ERROR);
        }
        $this->serverConfig = $serverConfig;
        if(isset($this->serverConfig['server']['ip'])) $this->defaultIp = $this->serverConfig['server']['ip'];
        if(isset($this->serverConfig['server']['port'])) $this->defaultPort = $this->serverConfig['server']['port'];
    }

    /**
     * 设置App_Config
     * setAppConfigIni
     * @desc
     * @author ShaunXu
     * @date 2018/4/12
     * @param $appConfigIni
     */
    public function setAppConfigIni($appConfigIni)
    {
        if (!is_file($appConfigIni)) {
            trigger_error('Server Config File Not Exist!', E_USER_ERROR);
        }
        $this->appConfigFile = $appConfigIni;
    }

    /**
     * 将请求信息放入全局注册器中
     * initRequestParam
     * @author ShaunXu
     * @date 2018/4/12
     * @param swoole_http_request $request
     * @return bool
     */
    protected function initRequestParam(swoole_http_request $request)
    {
        //将请求的一些环境参数放入全局变量桶中
        $server = isset($request->server) ? $request->server : array();
        $header = isset($request->header) ? $request->header : array();
        $get    = isset($request->get) ? $request->get : array();
        $post   = isset($request->post) ? $request->post : array();
        $cookie = isset($request->cookie) ? $request->cookie : array();
        $files  = isset($request->files) ? $request->files : array();
        Yaf_Registry::set('REQUEST_SERVER', $server);
        Yaf_Registry::set('REQUEST_HEADER', $header);
        Yaf_Registry::set('REQUEST_GET', $get);
        Yaf_Registry::set('REQUEST_POST', $post);
        Yaf_Registry::set('REQUEST_COOKIE', $cookie);
        Yaf_Registry::set('REQUEST_FILES', $files);
        Yaf_Registry::set('REQUEST_RAW_CONTENT', $request->rawContent());
        return true;
    }
}