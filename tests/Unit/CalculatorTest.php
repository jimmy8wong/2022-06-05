<?php

namespace Tests\Unit;

use App\Services\CalculatorService;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function test_simple_addition()
    {
        $calculatorService = new CalculatorService();

        $empty = $calculatorService->add("");
        $this->assertEquals(0, $empty);

        $single = $calculatorService->add("1");
        $this->assertEquals(1, $single);

        $double = $calculatorService->add("1,2");
        $this->assertEquals(3, $double);

        $triple = $calculatorService->add("1,2,4");
        $this->assertEquals(7, $triple);
    }

    public function test_pipe_delimiter()
    {
        $calculatorService = new CalculatorService();

        $pipe1 = $calculatorService->add("1|2,3");
        $this->assertEquals(6, $pipe1);

        $pipe2 = $calculatorService->add("1,2,3|4,5");
        $this->assertEquals(15, $pipe2);

        $pipe3 = $calculatorService->add("1|2,3|4,5");
        $this->assertEquals(15, $pipe3);
    }

    public function test_custom_delimiter()
    {
        $calculatorService = new CalculatorService();

        $customEmpty = $calculatorService->add("//;\n");
        $this->assertEquals(0, $customEmpty);

        $customSingle = $calculatorService->add("//;\n1");
        $this->assertEquals(1, $customSingle);

        $customDouble = $calculatorService->add("//;\n1;2");
        $this->assertEquals(3, $customDouble);

        $customTriple = $calculatorService->add("//;\n1;2;5");
        $this->assertEquals(8, $customTriple);

        $customPipe1 = $calculatorService->add("//;\n1|2;3");
        $this->assertEquals(6, $customPipe1);

        $customPipe2 = $calculatorService->add("//;\n1;2;3|4;5");
        $this->assertEquals(15, $customPipe2);

        $customPipe3 = $calculatorService->add("//;\n1;2;3|4;5|6");
        $this->assertEquals(21, $customPipe3);
    }

    public function test_negative_number_exception_single()
    {
        $calculatorService = new CalculatorService();

        $this->expectExceptionMessage('negatives not allowed: -1');
        $calculatorService->add("-1");
    }

    public function test_negative_number_exception_with_pipe()
    {
        $calculatorService = new CalculatorService();

        $this->expectExceptionMessage('negatives not allowed: -3,-5');
        $calculatorService->add("1,2,-3|4,-5");
    }

    public function test_negative_number_exception_custom_delimiter()
    {
        $calculatorService = new CalculatorService();

        $this->expectExceptionMessage('negatives not allowed: -2');
        $calculatorService->add("//;\n1;-2");
    }
}
