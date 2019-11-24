<?php


namespace Module;

/**
 * Class InputValidation
 * @package Module
 *
 * Single Input Validate.
 * If you want multiple input validation, you can use FormValidation class.
 */
class InputValidation
{
    /**
     * regex pattern lists
     */
    const REGEX_FFB_ID = '@\A[0-9a-zA-Z]{4,20}\z@';
    const REGEX_ENG_NUM = '@\A[0-9a-zA-Z]+\z@';

    private $value;
    private $error;
    private $next_valid_flag;

    public function __construct($value = null, string $encoding = 'UTF-8')
    {
        if (is_string($value)) {
            $this->value = trim($value);
        } else {
            $this->value = $value;
        }
        $this->next_valid_flag = true;

        // set internal encoding
        mb_internal_encoding($encoding);

        // validate encoding
        $this->validEncoding($value);

        // validate null byte
        $this->validNullByte($value);
    }

    private function validEncoding($value)
    {
        if (!mb_check_encoding($value)) {
            $this->error = 'Invalid encoding.';
        }
    }

    private function validNullByte($value)
    {
        if (preg_match('@\0@', $value)) {
            $this->error = 'Including invalid letter.';
        }
    }

    private function setNextValid(bool $bool = false): void
    {
        if ($bool) {
            $this->next_valid_flag = true;
        } else {
            $this->next_valid_flag = false;
        }
    }

    private function isNextValid(): bool
    {
        if ($this->next_valid_flag) {
            return true;
        } else {
            return false;
        }
    }

    public function getInput()
    {
        return $this->value;
    }

    public function updateInput($value, bool $reset_error = false): InputValidation
    {
        $this->value = $value;
        if ($reset_error) {
            self::setNextValid(true);
            self::setError(null, true);
        }
        return $this;
    }

    public function setError(string $error_msg, bool $override = false): void
    {
        if ($override || !isset($this->error)) {
            $this->error = $error_msg;
        }
    }

    public function getError(): string
    {
        return $this->error ?? '';
    }

    public function isError(): bool
    {
        if (isset($this->error)) {
            return true;
        } else {
            return false;
        }
    }


    public function handler(callable $handler): InputValidation
    {
        if(self::isNextValid()){
            $error = function ($error_msg, bool $override = false) {
                self::setError($error_msg, $override);
                self::setNextValid();
            };
            $handler($this->value, $error);
        }
        return $this;
    }

    public function notEmpty(): InputValidation
    {
        if (self::isNextValid()) {
            if (empty($this->value)) {
                self::setNextValid();
                self::setError('Required.');
            }
        }
        return $this;
    }

    public function ifNotEmpty(): InputValidation
    {
        if (self::isNextValid()) {
            if (empty($this->value)) {
                self::setNextValid();
            }
        }
        return $this;
    }

    public function minVal($value): InputValidation
    {
        if (self::isNextValid()) {
            if ($this->value < $value) {
                self::setNextValid();
                self::setError("Required greater equal than {$value}.");
            }
        }
        return $this;
    }

    public function maxVal($value): InputValidation
    {
        if (self::isNextValid()) {
            if ($this->value > $value) {
                self::setNextValid();
                self::setError("Required smaller equal than {$value}.");
            }
        }
        return $this;
    }

    public function betweenVal($min, $max): InputValidation
    {
        if (self::isNextValid()) {
            if ($this->value < $min || $this->value > $max) {
                self::setNextValid();
                self::setError("Required between {$min} and {$max}.");
            }
        }
        return $this;
    }

    public function minLen(int $value): InputValidation
    {
        if (self::isNextValid()) {
            if (mb_strlen($this->value) < $value) {
                self::setNextValid();
                self::setError("Required greater equal than {$value} characters.");
            }
        }
        return $this;
    }

    public function maxLen(int $value): InputValidation
    {
        if (self::isNextValid()) {
            if (mb_strlen($this->value) > $value) {
                self::setNextValid();
                self::setError("Required smaller equal than {$value} characters.");
            }
        }
        return $this;
    }

    public function betweenLen(int $min, int $max): InputValidation
    {
        if (self::isNextValid()) {
            if (mb_strlen($this->value) < $min || mb_strlen($this->value) > $max) {
                self::setNextValid();
                self::setError("Required between {$min} and {$max} characters.");
            }
        }
        return $this;
    }

    public function regex(string $pattern): InputValidation
    {
        if (self::isNextValid()) {
            echo '^^';
            if (!preg_match($pattern, $this->value)) {
                self::setNextValid();
                self::setError("Invalid input.");
            }
        }
        return $this;
    }

    public function equal($value, bool $strict = true): InputValidation
    {
        if (self::isNextValid()) {
            if ($strict) {
                if ($this->value !== $value) {
                    self::setNextValid();
                    self::setError("Dont match.");
                }
            } else {
                if ($this->value != $value) {
                    self::setNextValid();
                    self::setError("Dont match.");
                }
            }
        }
        return $this;
    }
}