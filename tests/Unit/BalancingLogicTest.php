<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Logic\BalancingLogic;
use InvalidArgumentException;

class BalancingLogicTest extends TestCase
{
    public function test_normal_sample()
    {
        $input = [10, 20, 0, 30];
        $output = BalancingLogic::balance($input);
        
        $this->assertEquals([20, 20, 0, 20], $output);
        $this->assertEquals(array_sum($input), array_sum($output));
        $this->assertEquals(0, $output[2]);
        $this->assertMaxDifference($output, $input);
    }

    public function test_evenly_divisible()
    {
        $input = [5, 5, 5];
        $output = BalancingLogic::balance($input);
        
        $this->assertEquals([5, 5, 5], $output);
        $this->assertEquals(array_sum($input), array_sum($output));
        $this->assertMaxDifference($output, $input);
    }

    public function test_remainder_distribution()
    {
        $input = [3, 5, 7];
        $output = BalancingLogic::balance($input);
        
        $this->assertEquals([5, 5, 5], $output);
        $this->assertEquals(array_sum($input), array_sum($output));
        $this->assertMaxDifference($output, $input);
    }

    public function test_all_zeros()
    {
        $input = [0, 0, 0];
        $output = BalancingLogic::balance($input);
        
        $this->assertEquals([0, 0, 0], $output);
        $this->assertEquals(array_sum($input), array_sum($output));
    }

    public function test_single_active_slot()
    {
        $input = [0, 0, 15, 0];
        $output = BalancingLogic::balance($input);
        
        $this->assertEquals([0, 0, 15, 0], $output);
        $this->assertEquals(array_sum($input), array_sum($output));
    }

    public function test_tie_condition()
    {
        $input = [3, 5, 5, 0, 2];
        $output = BalancingLogic::balance($input);
        
        $this->assertEquals([4, 4, 4, 0, 3], $output);
        $this->assertEquals(array_sum($input), array_sum($output));
        $this->assertEquals(0, $output[3]);
        $this->assertMaxDifference($output, $input);
    }

    public function test_invalid_input_negative()
    {
        $this->expectException(InvalidArgumentException::class);
        BalancingLogic::balance([-1, 5, 3]);
    }

    public function test_invalid_input_float()
    {
        $this->expectException(InvalidArgumentException::class);
        BalancingLogic::balance([1.5, 5, 3]);
    }

    public function test_invalid_input_non_numeric()
    {
        $this->expectException(InvalidArgumentException::class);
        BalancingLogic::balance(['abc', 5]);
    }

    public function test_edge_single_element()
    {
        $input = [42];
        $output = BalancingLogic::balance($input);
        
        $this->assertEquals([42], $output);
        $this->assertEquals(array_sum($input), array_sum($output));
    }

    public function test_edge_large_numbers()
    {
        $input = [1000000, 2000000, 3000000];
        $output = BalancingLogic::balance($input);
        
        $this->assertEquals([2000000, 2000000, 2000000], $output);
        $this->assertEquals(array_sum($input), array_sum($output));
        $this->assertMaxDifference($output, $input);
    }

    public function test_edge_alternating_zeros()
    {
        $input = [0, 5, 0, 5, 0, 5];
        $output = BalancingLogic::balance($input);
        
        $this->assertEquals([0, 5, 0, 5, 0, 5], $output);
        $this->assertEquals(array_sum($input), array_sum($output));
        $this->assertMaxDifference($output, $input);
    }

    private function assertMaxDifference(array $output, array $input)
    {
        $activeValues = [];
        foreach ($output as $index => $value) {
            if ($input[$index] > 0) {
                $activeValues[] = $value;
            }
        }
        
        if (!empty($activeValues)) {
            $max = max($activeValues);
            $min = min($activeValues);
            $this->assertLessThanOrEqual(1, $max - $min);
        }
    }
}
