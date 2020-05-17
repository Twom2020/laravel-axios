<?php


namespace Twom\Axios\Facade;


use Illuminate\Support\Facades\Facade;


/**
 * Class Axios
 * @package Twom\Axios\Facade
 *
 * @method static setOptions(array $options)
 * @method static fetchOptions()
 * @method static \Twom\Axios\Axios go()
 * @method static output($type = null)
 * @method static makeQueryData($data)
 * @method static makeJsonData($data)
 * @method static \Twom\Axios\Axios setHeader($headers)
 * @method static \Twom\Axios\Axios setUrl($url)
 * @method static \Twom\Axios\Axios setMethod($method)
 * @method static \Twom\Axios\Axios setData($data)
 * @method static \Twom\Axios\Axios setDataType($type)
 * @method static \Twom\Axios\Axios setReturnTransfer($status)
 * @method static \Twom\Axios\Axios setContent($contentType = "json")
 * @method static \Twom\Axios\Axios setOption($option, $value)
 * @method static \Twom\Axios\Axios make(array $options = [])
 * @method static \Twom\Axios\Axios get($url, $options = [])
 * @method static \Twom\Axios\Axios put($url, $data = [], $options = [])
 * @method static \Twom\Axios\Axios delete($url, $options = [])
 * @method static \Twom\Axios\Axios post($url, $data = [], $options = [])
 */
class Axios extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Twom\Axios\Axios::class;
    }
}
