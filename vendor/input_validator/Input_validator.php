<?php

class InputValidator
{
    private array $pattern;
    private array $inputs;
    private FilterMode $filterMode;
    private bool $disableValidator = false;
    private bool $aggressiveInputs = true;

    private $error;

    public function showError()
    {
        return $this->error;
    }

    /**
     * @param bool $aggressiveInputs
     */
    public function setAggressiveInputs(bool $aggressiveInputs): void
    {
        $this->aggressiveInputs = $aggressiveInputs;
    }

    /**
     * @param mixed $filterMode
     */
    public function setFilterMode(FilterMode $filterMode): void
    {
        $this->filterMode = $filterMode;
    }

    /**
     * @param bool $disableValidator
     */
    public function setDisableValidator(bool $disableValidator): void
    {
        $this->disableValidator = $disableValidator;
    }

    public function __construct($pattern, $inputs, FilterMode $filterMode = FilterMode::BOTH)
    {
        $this->filterMode = $filterMode;
        $this->pattern = $pattern;
        $this->inputs = $inputs;
    }

    public function validate(): bool
    {
        if (is_array($this->pattern) && is_array($this->inputs)) {
            $validateStatus = true;
            if ($this->aggressiveInputs) {
                foreach ($this->pattern[0] as $key => $value) {
                    if (array_key_exists($key, $this->inputs) && $this->inputs[$key] != null) {
                        $validation = $this->checkValidation($key, $this->inputs[$key]);
                        if ($validation) {
                            $validateStatus = true;
                            $this->error .= "$key Validate.\r\n";
                        } else {
                            $this->error .= "Error: <$key> is not valid <{$this->inputs[$key]} != {$value['type']}>.\r\n";
                            $validateStatus = false;
                        }
                        if ($this->pattern[$key]['require'] === true && !$validation) {
                            $this->error .= "Error: <$key> is required and is not valid.\r\n";
                            return false;
                        }
                    } else {
                        $this->error .= "Error: <$key> is not in input.\r\n";
                        $validateStatus = false;
                    }
                }
            }
            return $validateStatus;
        } else {
            $this->error .= "Error: Inputs or pattern is not array.\r\n";
            return false;
        }
    }

    private function checkValidation($key, $value): bool
    {
        if (array_key_exists($key, $this->pattern[0])) {
            $patternOfKey = $this->pattern[0][$key];
            switch ($this->filterMode) {
                case FilterMode::BOTH:

                    if (!$this->checkInputType($value, $patternOfKey['type'], $patternOfKey['enum_name'])) {
                        return false;
                    }
                    if (isset($patternOfKey['regex']) && !$this->checkInputRegex($value, $patternOfKey['regex'], $patternOfKey['enum_name'])) {
                        return false;
                    }
                    break;
                case FilterMode::REGEX:
                    if (isset($patternOfKey['regex']) && !$this->checkInputType($value, $patternOfKey['type'], $patternOfKey['enum_name'])) {
                        return false;
                    }
                    break;
                case FilterMode::TYPE:
                    if (!$this->checkInputRegex($value, $patternOfKey['regex'], $patternOfKey['enum_name'])) {
                        return false;
                    }
                    break;
            }
            return true;
        } else {
            return false;
        }
    }

    private function checkInputRegex(mixed $data, string $regex, bool $recursive): bool
    {
        if ($recursive && is_array($data)) {
            foreach ($data as $key => $value) {
                if (!$this->checkInputType($value, $regex, false)) return false;
            }
        }
        return preg_match($regex, $data) === 1;
    }

    private function checkInputType(mixed &$data, string $type, string $enum_name = null): bool
    {
        if (str_contains($type, '|')) {
            $tmp = explode('|', $type);
            foreach ($tmp as $t) {
                if ($this->checkInputType($data, $t)) return true;
            }
        }
        switch (strtoupper($type)) {
            case 'STRING':
                if (is_string($data)) return true;
                break;
            case 'BOOL':
                if (is_bool($data)) return true;
                break;
            case 'INT':
                if (is_int($data)) return true;
                break;
            case 'ARRAY':
                if (is_array($data)) return true;
                break;
            case 'FLOAT':
                if (is_float($data)) return true;
                break;
            case 'DOUBLE':
                if (is_double($data)) return true;
                break;
            case 'NULL':
                if (is_null($data)) return true;
                break;
            case 'ENUM':
                if (isset($enum_name) && enum_exists($enum_name)) {
                    $cases = $enum_name::cases();
                    foreach ($cases as $case) {
                        if ($case->name === $data) {
                            $data = $case;
                            return true;
                        }
                    }
                }
                break;
        }
        return false;
    }
}


enum FilterMode
{
    case TYPE;
    case REGEX;
    case BOTH;
}
