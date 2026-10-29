<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class EmployeeTest extends TestCase
{
    public function testFactory()
    {
        $employee = Employee::factory()->programmer()->create([
            'id'=>'1',
            'name'=>'Budi'
        ]);

        assertNotNull($employee);

        $employee2 = Employee::factory()->seniorProgrammer()->create([
            'id'=>'2',
            'name'=>'Hanif Roihan'
        ]);

        assertNotNull($employee2);
    }
}
