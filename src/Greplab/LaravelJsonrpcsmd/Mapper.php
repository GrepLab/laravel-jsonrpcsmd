<?php namespace Greplab\LaravelJsonrpcsmd;

use \Greplab\Jsonrpcsmd\Smd;

/**
 * Prepara la libreria SMD para inventariar los servicios.
 * 
 * @author Daniel Zegarra <dzegarra@greplab.com>
 */
class Mapper
{

    protected $smd;
    protected $allowed_extensions;
    protected $throwIfPathNotExist;
    protected $paths;

    /**
     * Constructor
     *
     * Setup server description
     */
    public function __construct()
    {
        $this->allowed_extensions = $allowed_extensions = \Config::get('jsonrpcsmd::allowed_extensions', array('php'));
        $this->throwIfPathNotExist = \Config::get('jsonrpcsmd::throwIfPathNotExist');
        $this->paths = \Config::get('jsonrpcsmd::service_paths');

        $this->smd = new Smd();
        $this->smd->setTarget(\Config::get('jsonrpcsmd::route_api'));
        $this->smd->setUseCanonical(\Config::get('jsonrpcsmd::use_canonical'));
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
                foreach ($dir as $dfile) {
                    $files[] = $file . DIRECTORY_SEPARATOR . $dfile;
                }
            } else {
                // Solo archivos con extension permitidos
                $parts = explode('.', $file);
                if (count($parts) > 0 && in_array($parts[count($parts) - 1], $this->allowed_extensions)) {
                    $files[] = $file;
                }
            }
        }
    
        return $files;
    }
    
    /**
     * Recibe el nombre de un archivo y devuelve el nombre de una clase.
     *
     * @param string $file
     * @param string $ns
     * @return string
     */
    public function filenameToClassname($file, $ns)
    {
        $str = str_replace(DIRECTORY_SEPARATOR, '\\', $file);

        //Extrayendo la extensión del archivo
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
     * Obtiene la ruta absoluta.
     * 
     * @throws PathNotExistException
     * @param string $path
     * @return string
     */
    protected function getRealPath($path)
    {
        if (file_exists($path)) {
            return $path;
        } else if (file_exists(app_path() . $path)) {
            return app_path() . $path;
        } else if ($this->throwIfPathNotExist) {
            throw new Exceptions\PathNotExistException($path);
        }
    }
    
    /**
     * Return an indexed array with the filenames found.
     * 
     * @return array
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
     * @throws ClassNotFoundException
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
     * Indexa todos los servicios que se encuentren en el directorio especificado.
     * 
     * @param string $path
     * @param string $ns
     */
    protected function indexServicePath($path, $ns='') 
    {
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
     * @return \Greplab\Jsonrpcsmd\Mapper
     */
    public function build()
    {
        foreach ($this->paths as $path) {
            if (is_array($path)) {
                $this->indexServicePath($path[1], $path[0]);
            } else {
                $this->indexServicePath($path);
            }
        }
        return $this;
    }

    /**
     * Agrega una nueva ruta de servicios que deben ser indexados.
     * 
     * @param string $path
     * @param string $ns
     */
    public function addServicePath($path, $ns='')
    {
        if (empty($path)) return;

        if (!empty($ns)) {
            $path = array($ns, $path);
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
     * Entrega el mapa en formato json.
     * 
     * @return string
     */
    public function toJson()
    {
		//Armando respuesta
		//$smdArray['type'] = 'jsonrpc';
		//$smdArray['namespace'] = SRVMAP_JSNAMESPACE;
		return (string) $this->getSmd();
    }
    
    public function __toString()
    {
        return $this->toJson();
    }
}