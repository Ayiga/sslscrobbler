<?php
/**
 * The NotificationDelayThread is a separate thread that calls the provided parameters after
 * the specified delay.  This is created to extend the Thread class in order to spawn it in
 * a separate Thread, so the main thread can continue unhalted.
 * 
 **/
class NotificationDelayThread extends Thread {

	static $threads = array();

	private
		$_obj,
		$_functionName,
		$_arguments,
		$_delay;

	/**
	 * @param $obj
	 * 		the object to call the provided function name on, after the specified delay
	 * @param $functionName
	 *		The function name to call on the provided $functionName
	 * @param $arguments
	 *		An array of arguments to pass to the object, via the function call, after the
	 *		specifeid delay
	 * @param $delay
	 *		The specified delay, in seconds, to wait before calling the function
	 * 
	 **/
	function __construct( $obj, $functionName, $arguments, $delay = 0 ){
		$this->_obj = $obj;
		$this->_functionName = $functionName;
		$this->_arguments = null;
		if($arguments && is_array($arguments)){
			$this->_arguments = $arguments;
		}
		$this->_delay = $delay * 1; //Ensure Number

		NotificationDelayThread::$threads[] = $this; // Add this to the current threads
	}

	/**
	 * Provided that the given $obj and $functionName both exist and the $obj has a method
	 * $functionName that can be called, this will first way $delay seconds, and call the
	 * $functionName on the $obj, while passing it the provided $arguments array.
	 *
	 **/
	function run ( ){
		if($this->_obj && $this->_functionName && method_exists($this->_obj, $this->_functionName)){
			sleep( $this->_delay );
			call_user_func_array( array( $_obj, $_functionName ), $_arguments );
		}
		$this->wait();
	}

	/**
	 *
	 **/
	static function cleanUp(){
		foreach(NotificationDelayThread::$threads as $thread){
			if( $thread->isWaiting() ){
				$thread->notify();
				$thread->join();
			}
		}
	}
}