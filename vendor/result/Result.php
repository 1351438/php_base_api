<?php

/// https://github.com/omniti-labs/jsend
class Result
{
    private $errorsList;

    /**
     * @param mixed $errorsList
     */
    public function setErrorsList($errorsList)
    {
        $this->errorsList = $errorsList;
        return $this;
    }

    public function success($data = null)
    {
        return [
            'status' => 'success',
            'data' => $data,
        ];
    }

    public function error($errorCode, $message = "Error not found.")
    {
        if (isset($this->errorsList[$errorCode])) {
            $message = $this->errorsList[$errorCode];
        }
        return [
            'status' => 'error',
            'error_code' => $errorCode,
            'message' => $message,
        ];
    }

    public function fail($data = null)
    {
        return [
            'status' => 'fail',
            'data' => $data,
        ];
    }
}