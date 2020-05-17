<?php


namespace Twom\Axios;

use Twom\Axios\Facade\Axios as AxiosFacade;

/**
 * Class Axios
 */
class Axios
{
    /**
     * @var false|resource|null
     */
    protected $request = null;
    /**
     * @var array
     */
    protected $allowedMethods = ["post", "get", "put", "delete"];
    /**
     * @var false|resource|null
     */
    protected $method = null;
    /**
     * @var string
     */
    protected $content = "json";
    /**
     * @var array
     */
    protected $data = [];
    /**
     * @var array
     */
    protected $options = [
        'header' => [],
        "return_transfer" => true,
    ];
    /**
     * @var string
     */
    protected $dataType = 'query';
    /**
     * @var array
     */
    protected $allowedDataType = ['json', 'query'];
    /**
     * @var array
     */
    protected $optionsKeys = [
        "header" => "setHeader",
        "url" => "setUrl",
        "method" => "setMethod",
        "return_transfer" => "setReturnTransfer",
        "content" => "setContent",
        "data" => "setData"
    ];
    /**
     * @var array
     */
    protected $allowedContentsTypes = [
        "json" => [
            "content" => "application/json",
            "dataType" => "json",
        ],
    ];
    /**
     * @var string
     */
    protected $output = null;

    /**
     * Axios constructor.
     */
    public function __construct()
    {
        $this->request = curl_init();
    }

    /**
     * set options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = array_replace($this->options, $options);
        return $this;
    }

    /**
     * fetch options and set those
     *
     * @return $this
     * @throws \Exception
     */
    public function fetchOptions()
    {
        foreach ($this->options as $option => $value) {
            $method_name = $this->optionsKeys[$option];
            if (!is_callable([$this, $method_name]))
                throw new \Exception("option {$option} not found");

            call_user_func([$this, $method_name], $value);
        }

        return $this;
    }

    /**
     * exec curl
     *
     * @return $this
     * @throws \Exception
     */
    public function go()
    {
        $this->fetchOptions();

        $this->makeContent();
        $this->makeUrl();
        $this->makeMethod();
        $this->makeReturnTransfer();
        $this->makeHeader();
        $this->makeData();

        $this->output = curl_exec($this->request);

        if (!$this->output)
            $this->output = "Error: " . curl_error($this->request);

        curl_close($this->request);

        return $this;
    }

    /**
     * get output (response of request)
     * you can decode it to your dataType like json pass the "json" to this method
     *
     * @param null $type
     * @return mixed|string
     */
    public function output($type = null)
    {
        if (is_null($type))
            return $this->output;

        if ($type == "json")
            return json_decode($this->output);
    }

    /**
     * make data for query dataType
     * like this: "name=ali&last_name=ghaleyan"
     *
     * @param $data
     */
    protected function makeQueryData($data)
    {
        $this->options['url'] .= ('?' . http_build_query($data));
    }

    /**
     * make data for json dataType
     *
     * @param $data
     */
    protected function makeJsonData($data)
    {
        curl_setopt($this->request, CURLOPT_POSTFIELDS, json_encode($data));
    }

    /**
     * set header on this request is (curl object)
     *
     * @param $headers
     * @return $this
     * @throws \Exception
     */
    public function setHeader($headers)
    {
        $this->options['header'] = ($this->options['header'] && is_array($this->options['header'])) ? $this->options['header'] : [];

        if (is_array($headers)) {
            foreach ($headers as $key => $header) {
                $this->options['header'][$key] = $header;
            }
        } elseif (is_string($headers)) {
            $split = explode(":", $headers, 2);
            if (!count($split)) throw new Exception("header {$headers} is invalid data!");

            $this->options['header'][trim($split[0])] = trim($split[1]);
        }

        return $this;
    }


    /**
     * set request url
     *
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->options['url'] = $url;
        return $this;
    }


    /**
     * set request method after check this is in allowed types
     *
     * @param $method
     * @return $this
     * @throws \Exception
     */
    public function setMethod($method)
    {
        if (!in_array(strtolower($method), $this->allowedMethods))
            throw new \Exception("method {$method} not allowed");

        $this->method = $method;
        return $this;
    }


    /**
     * set data for send
     *
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }


    /**
     * set dataType for send to request like "json" or "query"
     * query = "name=asgar&any=sagdhgshdg"
     *
     * @param $type
     * @return $this
     * @throws \Exception
     */
    public function setDataType($type)
    {
        $this->checkDataType($type);
        $this->dataType = $type;
        return $this;
    }


    /**
     * set return transfer for can get data from request
     *
     * @param $status
     * @return $this
     */
    public function setReturnTransfer($status)
    {
        $this->options['return_transfer'] = $status;
        return $this;
    }


    /**
     * set content type for request
     *
     * @param string $contentType
     * @return $this
     * @throws \Exception
     */
    public function setContent($contentType = "json")
    {
        if (!in_array($contentType, array_keys($this->allowedContentsTypes))) {
            throw new \Exception("content {$contentType} not allowed type!");
        }

        $this->content = $contentType;

        return $this;
    }


    /**
     * @param $type
     * @throws \Exception
     */
    protected function checkDataType($type)
    {
        if (!in_array(strtolower($type), $this->allowedDataType))
            throw new \Exception("type {$type} not allowed!");
    }


    /************************************       Static Methods      ************************************/


    /**
     * make a request
     *
     * @param array $options
     * @return Axios
     */
    public function make(array $options = [])
    {
        return (new Axios())->setOptions($options);
    }

    /**
     * get request
     * @param $url
     * @param array $options
     * @return Axios
     */
    public function get($url, $options = [])
    {
        $axios = AxiosFacade::make(array_replace($options, [
            "url" => $url,
            "method" => "GET",
            "return_transfer" => true,
        ]));

        return $axios;
    }

    /**
     * put request
     * @param $url
     * @param array $data
     * @param array $options
     * @return Axios
     */
    public function put($url, $data = [], $options = [])
    {
        $axios = AxiosFacade::make(array_replace($options, [
            "url" => $url,
            "method" => "PUT",
            "return_transfer" => true,
        ]));

        if (count($data) > 0)
            $axios->setData($data);

        return $axios;
    }

    /**
     * delete request
     * @param $url
     * @param array $options
     * @return Axios
     */
    public function delete($url, $options = [])
    {
        $axios = AxiosFacade::make(array_replace($options, [
            "url" => $url,
            "method" => "DELETE",
            "return_transfer" => true,
        ]));

        return $axios;
    }

    /**
     * get request
     * @param $url
     * @param array $data
     * @param array $options
     * @return Axios
     */
    public function post($url, $data = [], $options = [])
    {
        $axios = AxiosFacade::make(array_replace($options, [
            "url" => $url,
            "method" => "POST",
            "return_transfer" => true,
        ]));

        if (count($data) > 0)
            $axios->setData($data);

        return $axios;
    }


    /************************************       Make Methods       ************************************/

    protected function makeHeader()
    {
        $headers = ($this->options['header'] && is_array($this->options['header'])) ? $this->options['header'] : [];
        $mapped = array_map(function ($key, $item) {
            return "{$key}: {$item}";
        }, array_keys($headers), array_values($headers));

        curl_setopt($this->request, CURLOPT_HTTPHEADER, $mapped);
    }


    protected function makeUrl()
    {
        curl_setopt($this->request, CURLOPT_URL, $this->options['url']);
    }


    protected function makeMethod()
    {
        curl_setopt($this->request, CURLOPT_CUSTOMREQUEST, strtoupper($this->method));
    }


    protected function makeReturnTransfer()
    {
        curl_setopt($this->request, CURLOPT_RETURNTRANSFER, $this->options['return_transfer']);
    }


    protected function makeContent()
    {
        if (!is_null($this->content)) {
            $content = $this->allowedContentsTypes[$this->content];
            $dataType = is_array($content) ? $content['dataType'] : null;
            $content = is_array($content) ? $content['content'] : $content;
            if (!is_null($dataType))
                $this->setDataType($dataType);
            $this->options['header']['Content-Type'] = $content;
        }
    }


    protected function makeData()
    {
        $data = $this->data;
        $type = ucfirst($this->dataType);
        $this->checkDataType($type);
        call_user_func_array([$this, "make{$type}Data"], [$data, $this->request]);
    }
}
