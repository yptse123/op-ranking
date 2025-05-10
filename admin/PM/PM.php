<?php
spl_autoload_register(array(PM::instance(), 'autoload'));
class PM 
{
    static private $_classesSingleton = array();
    static private $_calssDir;
    static private $_instance;
    
    const CLASS_FILE_EXTENSION = "class.php";
    const CLASS_NAME_SEPARATOR = "_";
    const CLASS_NAME_PREFIX = "PM_";
    
    public function __construct()
    {
    }
    
    static public function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new PM();
        }
        return self::$_instance;
    }

    
    public static function getClassDir()
    {
        if(is_null(self::$_calssDir))
            self::$_calssDir = __DIR__ . DIRECTORY_SEPARATOR . "classes/"; 
        return self::$_calssDir;
    }
    
    
    public static function getSingleton($classPath)
    {
        $className = self::CLASS_NAME_PREFIX . str_replace(DIRECTORY_SEPARATOR, self::CLASS_NAME_SEPARATOR, $classPath);
        if(!isset(self::$_classesSingleton[$className]))
        {
            if(file_exists(self::getClassDir().$classPath.".".self::CLASS_FILE_EXTENSION))
            {
                require_once(self::getClassDir().$classPath.".".self::CLASS_FILE_EXTENSION);
                
                self::$_classesSingleton[$className] = new $className();
            }
        }
        
        return self::$_classesSingleton[$className];
    }
    
    public static function getInstance($classPath)
    {
        if(file_exists(self::getClassDir().$classPath.".".self::CLASS_FILE_EXTENSION))
        {
            require_once(self::getClassDir().$classPath.".".self::CLASS_FILE_EXTENSION);
            $className = self::CLASS_NAME_PREFIX . str_replace(DIRECTORY_SEPARATOR, self::CLASS_NAME_SEPARATOR, $classPath);
            $class = new $className();
        }
        
        return $class;
    }
    
    public static function getScriptlet($scriptlet)
    {
        if(file_exists(MY_ROOT_DIR . "/admin/PM/scriptlet/". $scriptlet . ".php"))
        {
            return require(MY_ROOT_DIR . "/admin/PM/scriptlet/". $scriptlet . ".php");
        }

        return false;
    }

    public function autoload($className)
    {
        $classPath = str_replace(self::CLASS_NAME_SEPARATOR, DIRECTORY_SEPARATOR, preg_replace('/^'.preg_quote(self::CLASS_NAME_PREFIX, '/').'/','',$className));
        $class = null;
        if(file_exists(self::getClassDir().$classPath.".".self::CLASS_FILE_EXTENSION))
        {
                require_once(self::getClassDir().$classPath.".".self::CLASS_FILE_EXTENSION);
        }else
        {

        }

        return $class;
    }

}