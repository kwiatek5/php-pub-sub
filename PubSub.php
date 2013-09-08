<?php

class PubSubException extends Exception {
	
}

class PubSub {
	private static $events = array();

	public static function on($eventName, Closure $closure) {
		self::$events[$eventName] = $closure;
	}

	public static function off($eventName) {
		self::$events[$eventName] = null;
	}


	public static function trigger($eventName) {
		if (isset(self::$events[$eventName]) && self::$events[$eventName] instanceof Closure) {
			$params = func_get_args();
			array_shift($params);
			return call_user_func_array(self::$events[$eventName], $params);
		} else {
			throw new PubSubException(sprintf('Event "%s" does not exists!', $eventName));
		}
	}
}
