<?php

namespace Layotter;

/**
 * Used for error handling
 *
 * May be used for more complex error handling, logging etc. in the future.
 */
class Errors {

    /**
     * Handle recoverable invalid argument
     *
     * @param $argument string Argument name
     */
    public static function invalid_argument_recoverable($argument) {
        trigger_error('Missing or invalid argument: ' . $argument . ', using default value instead.', E_USER_WARNING);
    }

    /**
     * Handle unrecoverable invalid argument
     *
     * @param $argument string Argument name
     */
    public static function invalid_argument_not_recoverable($argument) {
        trigger_error('Missing or invalid required argument: ' . $argument, E_USER_ERROR);
    }

}
