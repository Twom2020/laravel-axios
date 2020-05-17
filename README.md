
## Axios for PHP
### Installation:
```
composer require twom/php-axios
```

### Requests methods:
| name    | parameters  |
|---------|-------------|
| post    | `post($url, $data = [], $options = [])` |
| get     | `get($url, $options = [])` |
| put     | `put($url, $data = [], $options = [])` |
| delete  | `delete($url, $options = [])` |


### Options:
| name  | type | example |
|-------|------|---------|
|header | `string` or `array`  | `name: value` or `[name => value]`  |
| url  | `string` | `http://url.domain/...` |
| method | `string` | `http://url.domain/...` |
| return_transfer  | `boolean` | `true` or `false` |
| content  | `string` | select from contents like `json` |
| data| `string ` or `array` | `[name => value]` |

### Mor methods:
| name | parameters | description |
|------|------------| ------- |
| output |  `$type` default as `json` | get request response |
| setContent |  `$contentType` default as `json` | set content type |
| setOption|  `($option, $value)` | set a curl option |
| setDataType|  `$type` | set sender dataType can be `json, query` |
| setData|  `$data` | set send data |
| setMethod|  `$method` | set request send method |
| setUrl|  `$url` | set request url |
| setHeader|  `$headers` | curl headers |


## Examples:
```php
$data = Axios::post("http://your-url.com/...",  
 [  
	 "name" => "ali",  
 ], 
 [  
	 "header" => [  
		  "Authorization" => "any"  
	  ],  
 ]) 
 ->setContent()  
 ->go()  
 ->output('json');
```
> **Note:** convert output to `json`.

you can make a request like this:
```php
$axios = Axios::make([  
		  "url" => $url,  
		  "method" => "post",
		]);
```
