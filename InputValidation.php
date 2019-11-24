<?php


/**
 * Class InputValidation
 *
 * This Class can validate single input.
 * If you want to validate multiple inputs, you can use FormValidation class.
 */
class InputValidation
{
  private $value;
  private $error;
  private $next_valid_flag;

  /**
   * InputValidation constructor.
   * @param null $value
   * @param string $encoding
   */
  public function __construct($value = null, string $encoding = 'UTF-8')
  {
    // init
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

  /**
   * Validate encoding.
   * @param $value
   */
  private function validEncoding($value)
  {
    if (!mb_check_encoding($value)) {
      $this->error = 'Invalid encoding.';
    }
  }

  /**
   * Validate null byte.
   * @param $value
   */
  private function validNullByte($value)
  {
    if (preg_match('@\0@', $value)) {
      $this->error = 'Including invalid letter.';
    }
  }

  /**
   * Set $next_valid_flag.
   * This value is necessary when you use chain method validation.
   * @param bool $bool
   */
  private function setNextValid(bool $bool = false): void
  {
    if ($bool) {
      $this->next_valid_flag = true;
    } else {
      $this->next_valid_flag = false;
    }
  }

  /**
   * Check $next_valid_flag.
   * If this value is true, next chain method validation will execute.
   * @return bool
   */
  private function isNextValid(): bool
  {
    if ($this->next_valid_flag) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Return $value.
   * @return null
   */
  public function getInput()
  {
    return $this->value;
  }

  /**
   * Set $value, and reset $error/$next_valid_flag.
   * Basically, this function should not be used.
   * @param $value
   * @param bool $reset_error
   * @return InputValidation
   */
  public function updateInput($value, bool $reset_error = false): InputValidation
  {
    $this->value = $value;
    if ($reset_error) {
      self::setNextValid(true);
      self::setError(null, true);
    }
    return $this;
  }

  /**
   * Set $error.
   * If you want add original error, you can use this.
   * But maybe recommend using self::handler() or FormValidation class.
   * @param string $error_msg
   * @param bool $override
   */
  public function setError(string $error_msg, bool $override = false): void
  {
    if ($override || !isset($this->error)) {
      $this->error = $error_msg;
    }
  }

  /**
   * Return $error.
   * @return string
   */
  public function getError(): string
  {
    return $this->error ?? '';
  }

  /**
   * Return boolean, is $value including $error.
   * @return bool
   */
  public function isError(): bool
  {
    if (isset($this->error)) {
      return true;
    } else {
      return false;
    }
  }


  /**
   * Make original validation.
   * If you want original validation, you can use this.
   *
   * ex)
   * handler(function ($value, $error) {
   *  if (!$value) {
   *      $error(message);
   *  }
   * })
   *
   * @param callable $handler
   * @return InputValidation
   */
  public function handler(callable $handler): InputValidation
  {
    if (self::isNextValid()) {
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