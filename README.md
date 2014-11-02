## JSON-RPC Service Mapping Description for Laravel 4.2

This library function as a port to make available the smd map in laravel 4.2 with the minimun effort.

### Usage

This library works making all the public methods of the classes in a directory available as a services

    //You can start like this:
    $smd = new \Greplab\Jsonrpcsmd\Smd('http://my-website/path/of/the/endpoint');
    
    //Or like that:
    $smd = new \Greplab\Jsonrpcsmd\Smd();
    $smd->setTarget('http://my-website/path/of/the/endpoint');
    

### License

This library is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
