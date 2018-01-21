<?php

namespace App\Models;


class Response
{
    var $Code, $Message, $Error, $Payload;
    const SUCCESS_CODE = 0;
    const INVALID_CREDENTIALS = 1;
    const NOT_FOUND = 2;
    const VALIDATION_ERROR = 3;
    const ALREADY_PRESENT = 4;
    const UNKNOWN_ERROR= 5;

    function __construct()
    {
    }

    /**
     * @param mixed $Code
     */
    public function setCode($Code)
    {
        $this->Code = $Code;
    }

    /**
     * @param mixed $Error
     */
    public function setError($Error)
    {
        $this->Error = $Error;
    }

    /**
     * @param mixed $Message
     */
    public function setMessage($Message)
    {
        $this->Message = $Message;
    }

    /**
     * @param mixed $Payload
     */
    public function setPayload($Payload)
    {
        $this->Payload = $Payload;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->Code;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->Error;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->Message;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->Payload;
    }
    public function getJsonResponse($status = 200)
    {
        return response()->json($this, $status);
    }
    public function getResponse() {
        return $this;
    }

    public function getSuccessResponse($message = 'Success!', $payload = []) {
        $this->setCode(self::SUCCESS_CODE);
        $this->setMessage($message);
        $this->setError(null);
        $this->setPayload($payload);
        return $this->getJsonResponse();
    }
    public function getInvalidCredentials($err = 'Invalid Credentials!', $status = 200) {
        $this->setCode(self::INVALID_CREDENTIALS);
        $this->setMessage('Invalid Credentials!');
        $this->setError($err);
        return $this->getJsonResponse($status);
    }
    public function getNotFound($err = 'Not Found Error!') {
        $this->setCode(self::NOT_FOUND);
        $this->setMessage('Not Found');
        $this->setError($err);
        return $this->getJsonResponse();
    }
    public function getValidationError($validationError) {
        $this->setCode(self::VALIDATION_ERROR);
        $this->setMessage('Validation Error');
        $this->setError(['fields' => $validationError]);
        return $this->getJsonResponse();
    }
    public function getAlreadyPresent($err = 'Name Already Present') {
        $this->setCode(self::ALREADY_PRESENT);
        $this->setMessage('Already Present Error');
        $this->setError($err);
        return $this->getJsonResponse();
    }
    public function getUnknownError($err) {
        $this->setCode(self::UNKNOWN_ERROR);
        $this->setMessage('Unknown Error!');
        $this->setError($err);
        return $this->getJsonResponse();
    }
}
