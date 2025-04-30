<?php

namespace Tests\Unit\Services;

use App\Services\Auth\RegisterService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RegisterServiceTest extends TestCase
{
    /**
     * Test that payment plan distribution roughly follows the 50/50 pattern.
     */
    public function test_payment_plan_distribution()
    {
        $service = new RegisterService();
        
        // Run 1000 iterations to test distribution
        $results = [
            0 => 0,
            1 => 0,
        ];
        
        for ($i = 0; $i < 1000; $i++) {
            $plan = $service->getPaymentPlan();
            $results[$plan]++;
        }
        
        // We expect roughly 50/50 distribution (with some variance)
        // Test that each plan is within 40-60% range
        foreach ($results as $plan => $count) {
            $percentage = $count / 1000 * 100;
            $this->assertGreaterThan(40, $percentage, "Plan $plan has less than 40% distribution");
            $this->assertLessThan(60, $percentage, "Plan $plan has more than 60% distribution");
        }
    }
    
    /**
     * Test custom distribution by modifying internal properties.
     */
    public function test_custom_distribution()
    {
        $service = new RegisterService();
        
        // Use reflection to modify protected properties
        $reflection = new ReflectionClass($service);
        
        $planDistributionProp = $reflection->getProperty('planDistribution');
        $planDistributionProp->setAccessible(true);
        
        // Set a custom distribution: 70% for plan 0, 30% for plan 1
        $planDistributionProp->setValue($service, [
            0 => 70,
            1 => 30,
        ]);
        
        // Run 1000 iterations to test distribution
        $results = [
            0 => 0,
            1 => 0,
        ];
        
        for ($i = 0; $i < 1000; $i++) {
            $plan = $service->getPaymentPlan();
            $results[$plan]++;
        }
        
        // Allow for some statistical variance (±10%)
        $this->assertGreaterThan(60, $results[0] / 10, "Plan 0 distribution should be roughly 70%");
        $this->assertLessThan(80, $results[0] / 10, "Plan 0 distribution should be roughly 70%");
        
        $this->assertGreaterThan(20, $results[1] / 10, "Plan 1 distribution should be roughly 30%");
        $this->assertLessThan(40, $results[1] / 10, "Plan 1 distribution should be roughly 30%");
    }
}