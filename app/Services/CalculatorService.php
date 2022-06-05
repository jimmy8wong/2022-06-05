<?php

namespace App\Services;

class CalculatorService
{
    private const PRIMARY_DELIMITER = ',';
    private const SECONDARY_DELIMITER = '|';
    private const PARAM_DELIMITER_REGEX = '/\/\/(.*?)\\n/s';

    /**
     * Add up some numbers
     *
     * @param string $numbers
     * @return int
     */
    public function add(string $numbers): int
    {
        if (empty($numbers)) return 0;

        $delimiter = $this->setPrimaryDelimiter($numbers);

        $arrNumbers = explode(
            $delimiter,
            $numbers
        );

        foreach ($arrNumbers as $number) {
            $this->checkForSecondaryDelimiter($number, $arrNumbers);
        }

        $negatives = array_filter($arrNumbers, array($this, 'checkForNegativeNumbers'));

        if ($negatives) {
            arsort($negatives);

            throw new \InvalidArgumentException(sprintf(
                'negatives not allowed: %s',
                implode(',', $negatives)
            ));
        }

        return array_sum($arrNumbers);
    }

    /**
     * Check if there's a custom delimiter in the parameter and replace primary delimiter if there is
     *
     * @param string $numbers
     * @return string
     */
    private function setPrimaryDelimiter(string $numbers): string
    {
        $customDelimiter = [];

        preg_match(
            self::PARAM_DELIMITER_REGEX,
            $numbers,
            $customDelimiter
        );

        return (array_key_exists(1, $customDelimiter))
            ? $customDelimiter[1]
            : self::PRIMARY_DELIMITER;
    }

    /**
     * Check if there's an additional delimiter we need to handle
     *
     * @param string $number
     * @param array $arrNumbers
     */
    private function checkForSecondaryDelimiter(string $number, array &$arrNumbers): void
    {
        if (strpos($number, self::SECONDARY_DELIMITER) !== false) {
            if (($index = array_search($number, $arrNumbers)) !== false) {
                unset($arrNumbers[$index]);
            }

            $pipes = explode(
                self::SECONDARY_DELIMITER,
                $number
            );

            $arrNumbers = array_merge($arrNumbers, $pipes);
        }
    }

    /**
     * return any negative numbers
     *
     * @param string $number
     * @return string|null
     */
    private function checkForNegativeNumbers(string $number): ?string
    {
        if ((int) $number < 0) {
            return $number;
        }

        return null;
    }
}
