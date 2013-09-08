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

//---------------------------------------
session_start();

PubSub::on('start', function() {
	echo "Let's go!\n";
});

PubSub::on('stop', function() {
	echo "The end!\n";
});

PubSub::on('addMessage', function($name, $msg) {
	if (empty($_SESSION[$name])) {
		$_SESSION[$name] = array();
	}
	$_SESSION[$name][] = $msg;
});

PubSub::on('getAllMessages', function($name) {
	var_dump($_SESSION[$name]);
	$_SESSION[$name] = null;
});
//---------------------------------------

PubSub::trigger('start');

PubSub::trigger('addMessage', 'loremMessages', 'Lorem ipsum');
PubSub::trigger('addMessage', 'loremMessages', 'dolor sit amet');
PubSub::trigger('addMessage', 'loremMessages', 'consectetur adipiscing elit');

PubSub::trigger('getAllMessages', 'loremMessages');

PubSub::trigger('addMessage', 'loremMessages', 'Etiam congue adipiscing sem');
PubSub::trigger('addMessage', 'loremMessages', 'at molestie ligula luctus et');

PubSub::trigger('getAllMessages', 'loremMessages');

PubSub::off('stop');
try {
	PubSub::trigger('stop');
} catch (PubSubException $e) {
	echo $e->getMessage() . "\n";
}

