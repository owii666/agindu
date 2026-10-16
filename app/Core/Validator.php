<?php
/**
 * AGINDU - Simple Form Validator
 */

declare(strict_types=1);

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function validate(array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleList) {
            $value = $this->data[$field] ?? null;
            if (is_string($value)) {
                $value = trim($value);
            }

            $ruleItems = is_array($ruleList) ? $ruleList : explode('|', $ruleList);

            foreach ($ruleItems as $rule) {
                if ($rule === 'required') {
                    if ($value === null || $value === '') {
                        $this->errors[$field] = "Bidang ini wajib diisi.";
                        break;
                    }
                } elseif ($rule === 'email') {
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$field] = "Format alamat email tidak valid.";
                        break;
                    }
                } elseif ($rule === 'numeric') {
                    if (!empty($value) && !is_numeric($value)) {
                        $this->errors[$field] = "Nilai harus berupa angka.";
                        break;
                    }
                } elseif (str_starts_with($rule, 'min:')) {
                    $min = (int)substr($rule, 4);
                    if (is_numeric($value)) {
                        if ((float)$value < $min) {
                            $this->errors[$field] = "Nilai minimal adalah {$min}.";
                            break;
                        }
                    } else {
                        if (mb_strlen((string)$value) < $min) {
                            $this->errors[$field] = "Minimal {$min} karakter.";
                            break;
                        }
                    }
                } elseif (str_starts_with($rule, 'max:')) {
                    $max = (int)substr($rule, 4);
                    if (is_numeric($value)) {
                        if ((float)$value > $max) {
                            $this->errors[$field] = "Nilai maksimal adalah {$max}.";
                            break;
                        }
                    } else {
                        if (mb_strlen((string)$value) > $max) {
                            $this->errors[$field] = "Maksimal {$max} karakter.";
                            break;
                        }
                    }
                } elseif ($rule === 'date') {
                    if (!empty($value) && !strtotime($value)) {
                        $this->errors[$field] = "Format tanggal tidak valid.";
                        break;
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
}
