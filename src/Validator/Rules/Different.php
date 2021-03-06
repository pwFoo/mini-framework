<?php
/**
 * This file is part of Mini.
 * @auth lupeng
 */
declare(strict_types=1);

namespace Mini\Validator\Rules;

use Mini\Exceptions\MissingRequiredParameterException;
use Mini\Validator\Rule;

class Different extends Rule
{

    /** @var string */
    protected string $message = "The :attribute must be different with :field";

    /** @var array */
    protected array $fillableParams = ['field'];

    /**
     * Check the $value is valid
     * @param mixed $value
     * @return bool
     * @throws MissingRequiredParameterException
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $field = $this->parameter('field');
        $anotherValue = $this->validation->getValue($field);

        return $value !== $anotherValue;
    }
}
