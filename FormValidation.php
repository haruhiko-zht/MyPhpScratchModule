<?php


/**
 * Class FormValidation
 *
 * This class is wrapper class of InputValidation.
 */
class FormValidation
{
  private $_inputs;

  /**
   * FormValidation constructor.
   * @param bool $cookie
   * @param array $inputs
   * @param string $encoding
   */
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

  /**
   * @param array $inputs
   * @param string $encoding
   */
  private function setInputs(array $inputs, string $encoding)
  {
    foreach ($inputs as $key => $val) {
      $this->_inputs[$key] = new InputValidation($val, $encoding);
    }
  }

  /**
   * Get InputValidation $error of setting $name.
   * @param $name
   * @return string
   */
  public function __get($name): string
  {
    if (array_key_exists($name, $this->_inputs) && $this->_inputs[$name] instanceof InputValidation) {
      return $this->_inputs[$name]->getError() ?? '';
    } else {
      $this->_inputs[$name] = new InputValidation();
      return $this->_inputs[$name]->getError() ?? '';
    }
  }

  /**
   * Wrapper function of InputValidation::setError().
   * @param $name
   * @param string $error_msg
   * @param bool $override
   */
  public function setError($name, string $error_msg, bool $override = false): void
  {
    if (array_key_exists($name, $this->_inputs) && $this->_inputs[$name] instanceof InputValidation) {
      $this->_inputs[$name]->setError($error_msg, $override);
    } else {
      $this->_inputs[$name] = new InputValidation();
      $this->_inputs[$name]->setError($error_msg, $override);
    }
  }

  /**
   * Get error count.
   * @return int
   */
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

  /**
   * Return boolean, is including error.
   * @return bool
   */
  public function isError(): bool
  {
    if (self::getErrCnt() !== 0) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Get validated inputs.
   * @param array $excepts
   * @return array
   */
  public function getValidatedInputs(array $excepts = []): array
  {
    $validated_inputs = [];
    foreach ($this->_inputs as $key => $val) {
      if (array_search($key, $excepts, true) === false && !$val->isError()) {
        $validated_inputs[$key] = $val->getInput();
      }
    }
    return $validated_inputs;
  }

  /**
   * Select $value you want validation
   * @param $name
   * @return InputValidation
   */
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