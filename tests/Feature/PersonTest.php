<?php

namespace Tests\Feature;

use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNotNull;

class PersonTest extends TestCase
{
    public function testPerson()
    {
        $person =  new Person();
        $person->first_name='Hanif';
        $person->last_name='Roihan';
        $person->save();

        assertEquals('Hanif Roihan', $person->full_name);

        $person->full_name='Ganteng Banget';
        $person->save();

        assertEquals('Ganteng', $person->first_name);
        assertEquals('Banget', $person->last_name);
    }

    public function testPerson2()
    {
        $person =  new Person();
        $person->first_name='Hanif';
        $person->last_name='Roihan';
        $person->save();

        assertEquals('HANIF Roihan', $person->full_name);

        $person->full_name='Ganteng Banget';
        $person->save();

        assertEquals('GANTENG', $person->first_name);
        assertEquals('Banget', $person->last_name);
    }

    public function testAttributeCasting()
    {
        $person =  new Person();
        $person->first_name='Hanif';
        $person->last_name='Roihan';
        $person->save();

        assertNotNull($person);
        assertNotNull($person->created_at);
        assertNotNull($person->updated_at);
        assertInstanceOf(Carbon::class, $person->created_at);
        assertInstanceOf(Carbon::class, $person->created_at);
    }
}
