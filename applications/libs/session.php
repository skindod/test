<?php

/**
 * Class for session handling
 */
class session
{
	static function get($key = '')
	{
		if (isset($_SESSION[$key])) {
			return unserialize($_SESSION[$key]);
		}
		
		return false;
	}

	static function set($key, $value)
	{
		$_SESSION[$key] = serialize($value);
	}

	static function remove($key)
	{
		unset($_SESSION[$key]);
	}

	static function destroy() {
		session_destroy();
	}
        
        static function is_exist($key)
        {
            if(isset($_SESSION[$key]))
                return true;
            else
                return false;
        }
}

?>