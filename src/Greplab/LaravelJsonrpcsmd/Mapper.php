<?php namespace Greplab\LaravelJsonrpcsmd;

use \Greplab\Jsonrpcsmd\Smd;

/**
 * Prepare the Json-RPC SMD library for index the services.
 * 
 * @author Daniel Zegarra <dzegarra@greplab.com>
 */
class Mapper
{
    /**
     * Instancia de la clase Smd
     *
     * @var Smd
     */
    protected $smd;
    protected $allowed_extensions;
    protected $throw_if_path_not_exist;
    protected $paths = array();
    protected $service_validator = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->loadConfig();

        $this->buildSmd();
    }

    /**
     * Load configuration parameters for this class.
     */
    protected function loadConfig()
    {
        $this->allowed_extensions = $allowed_extensions = \Config::get('jsonrpcsmd::allowed_extensions', array('php'));
        $this->throw_if_path_not_exist = \Config::get('jsonrpcsmd::throw_if_path_not_exist');
    }

    /**
     * Create and initialize an instance of the class {@link Smd}.
     * This method also apply some configuration to the Smd library.
     */
    protected function buildSmd()
    {
        $this->smd = new Smd();
        $this->smd->setTarget($this->getTarget());
        $this->smd->setUseCanonical(\Config::get('jsonrpcsmd::use_canonical'));
        $this->smd->setServiceValidator(\Config::get('jsonrpcsmd::service_validator'));
        $this->smd->setNameResolver(\Config::get('jsonrpcsmd::name_resolver'));
    }

    /**
     * Return the URL of the target.
     * @return string
     */
    protected function getTarget()
    {
        $route = \Config::get('jsonrpcsmd::route_api');
        return url($route);
    }

    /**
     * Return an indexed array with the names of files in the directory passed. 
     *
     * @param string $path
     * @return array
     */
    public function listFilesInDir($path)
    {
        $files = array();
        
        $dir_files = scandir($path);
        foreach ($dir_files as $file) {
            
            if ($file == '.' || $file == '..') continue;
            
            if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                $dir = $this->listFilesInDir($path . DIRECTORY_SEPARATOR . $file);
                foreach ($dir as $d_file) {
                    $files[] = $file . DIRECTORY_SEPARATOR . $d_file;
                }
            } else {
                // Only files with allowed extensions
                $parts = explode('.', $file);
                if (count($parts) > 0 && in_array($parts[count($parts) - 1], $this->allowed_extensions)) {
                    $files[] = $file;
                }
            }
        }
    
        return $files;
    }
    
    /**
     * Receive a file name and return the name of the class.
     *
     * @param string $file Name of file
     * @param string $ns Namespace of the class
     * @return string Full class name
     */
    public function filenameToClassname($file, $ns)
    {
        $str = str_replace(DIRECTORY_SEPARATOR, '\\', $file);

        //Extracting the extension of the file
        $parts = explode('.', $str);
        if (count($parts) > 1) {
            array_pop($parts);
            $str = implode('.', $parts);
        }

        if (!empty($ns)) {
            $str = $ns . '\\' . $str;
        }

        return $str;
    }

    /**
     * Return the absolute path of one file.
     * 
     * @throws Exceptions\PathNotExistException
     * @param string $path
     * @return string
     */
    protected function getRealPath($path)
    {
        $real_path = null;
        if (file_exists($path)) {
            $real_path = $path;
        } else if (file_exists(app_path() . $path)) {
            $real_path = app_path() . $path;
        } else if ($this->throw_if_path_not_exist) {
            throw new Exceptions\PathNotExistException($path);
        }
        return $real_path;
    }

    /**
     * Return an indexed array with the names of files found.
     *
     * @param string $path
     * @return array
     * @throws Exceptions\PathNotExistException
     */
    protected function listServiceFilesIn($path)
    {
        $path = $this->getRealPath($path);
        return !empty($path) ? $this->listFilesInDir($path) : array();
    }

    /**
     * Throw an exception if the class passed not exist.
     * This method try to load the class dynamically. The throw is fire if the class can't be found by the autoloader.
     * 
     * @throws Exceptions\ClassNotFoundException
     * @param string $classname
     * @param string $file
     */
    protected function throwIfClassNotExist($classname, $file)
    {
        if(!class_exists($classname)){
            throw new Exceptions\ClassNotFoundException($classname, $file);
        }
    }
    
    /**
     * Search for the files in the directory passed as an argument.
     * 
     * @param string $path The directory full of services
     * @param string $ns The namespace prefix
     */
    protected function indexServicePath($path, $ns=null)
    {
        if (!is_null($ns)) {
            $ns_path = str_replace('\\', DIRECTORY_SEPARATOR, $ns);
            $path.= DIRECTORY_SEPARATOR . $ns_path;
        }

        $files = $this->listServiceFilesIn($path);

        foreach($files as $key=>$file){
            $classname = $this->filenameToClassname($file, $ns);
            $this->throwIfClassNotExist($classname, $file);
            $this->smd->addClass($classname);
        }
    }

    /**
     * Build the map of services and methods.
     * 
     * @return \Greplab\LaravelJsonrpcsmd\Mapper
     */
    public function build()
    {
        foreach ($this->paths as $path) {
            if (is_array($path)) {
                $this->indexServicePath($path[0], $path[1]);
            } else {
                $this->indexServicePath($path);
            }
        }
        return $this;
    }

    /**
     * Register a new path of services for be indexed.
     * 
     * @param string $path Directory path of services
     * @param string $ns Namespace prefix
     */
    public function addServicePath($path, $ns=null)
    {
        if (empty($path)) return;

        if (!empty($ns)) {
            $path = array($path, $ns);
        }

        array_push($this->paths, $path);
    }

    /**
     * Return the service map instance.
     * 
     * @return \Greplab\Jsonrpcsmd\Smd
     */
    public function getSmd()
    {
        return $this->smd;
    }

    /**
     * Return the map un json format.
     * 
     * @return string
     */
    public function toJson()
    {
		//Armando respuesta
		//$smdArray['type'] = 'jsonrpc';
		//$smdArray['namespace'] = SRVMAP_JSNAMESPACE;
		return (string) $this->smd;
    }

    /**
     * Return the map un json format.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

}