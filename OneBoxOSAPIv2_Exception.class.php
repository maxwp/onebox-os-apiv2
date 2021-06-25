<?php
class OneBoxOSAPIv2_Exception extends Exception {

    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        if (is_array($message)) {
            $this->_errorArray = $message;
        } else {
            $this->_errorArray[] = $message;
        }

        parent::__construct(implode($this->_errorArray), $code, $previous);
    }

    public function getErrorArray() {
        return $this->_errorArray;
    }

    private $_errorArray = [];

}