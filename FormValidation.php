<?php


namespace Module;

/**
 * Class FormValidation
 * @package Module
 */
class FormValidation
{
    const REGEX_FFB_ID = InputValidation::REGEX_FFB_ID;
    const REGEX_ENG_NUM = InputValidation::REGEX_ENG_NUM;

    private $_inputs;

    public function __construct(bool $cookie = false, array $inputs = [], string $encoding = 'UTF-8')
    {
        $this->_inputs = [];

        self::setInputs($_GET, $encoding);
        self::setInputs($_POST, $encoding);
        if ($cookie) {
            self::setInputs($_COOKIE, $encoding);
        }

        if (!empty($inputs)) {
            self::setInputs($inputs, $encoding);
        }
    }

    private function setInputs(array $inputs, string $encoding)
    {
        foreach ($inputs as $key => $val) {
            $this->_inputs[$key] = new InputValidation($val, $encoding);
        }
    }

    public function __get($name): string
    {
        if (array_key_exists($name, $this->_inputs) && $this->_inputs[$name] instanceof InputValidation) {
            return $this->_inputs[$name]->getError() ?? '';
        } else {
            $this->_inputs[$name] = new InputValidation();
            return $this->_inputs[$name]->getError() ?? '';
        }
    }

    public function getErrCnt(): int
    {
        $count = 0;
        foreach ($this->_inputs as $key => $val) {
            if ($val->isError()) {
                $count++;
            }
        }
        return $count;
    }

    public function isError(): bool
    {
        if (self::getErrCnt() !== 0) {
            return true;
        } else {
            return false;
        }
    }

    public function validate($name): InputValidation
    {
        if (array_key_exists($name, $this->_inputs)) {
            return $this->_inputs[$name];
        } else {
            $this->_inputs[$name] = new InputValidation();
            return $this->_inputs[$name];
        }
    }

}