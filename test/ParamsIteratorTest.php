<?php declare(strict_types=1);

namespace EmsApi\Test;

use EmsApi\ParamsIterator;

class ParamsIteratorTest extends Base
{
    /**
     * @return void
     */
    final public function testConstructorWithEmptyArray()
    {
        $data = [];
        $iterator = new ParamsIterator($data);
        
        $this->assertInstanceOf(ParamsIterator::class, $iterator);
        $this->assertFalse($iterator->valid());
    }

    /**
     * @return void
     */
    final public function testConstructorWithData()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $iterator = new ParamsIterator($data);
        
        $this->assertInstanceOf(ParamsIterator::class, $iterator);
        $this->assertTrue($iterator->valid());
    }

    /**
     * @return void
     */
    final public function testKey()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $iterator = new ParamsIterator($data);
        
        $this->assertEquals('key1', $iterator->key());
    }

    /**
     * @return void
     */
    final public function testCurrent()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $iterator = new ParamsIterator($data);
        
        $this->assertEquals('value1', $iterator->current());
    }

    /**
     * @return void
     */
    final public function testNext()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];
        $iterator = new ParamsIterator($data);
        
        $this->assertEquals('key1', $iterator->key());
        $this->assertEquals('value1', $iterator->current());
        
        $iterator->next();
        $this->assertEquals('key2', $iterator->key());
        $this->assertEquals('value2', $iterator->current());
        
        $iterator->next();
        $this->assertEquals('key3', $iterator->key());
        $this->assertEquals('value3', $iterator->current());
    }

    /**
     * @return void
     */
    final public function testValid()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $iterator = new ParamsIterator($data);
        
        $this->assertTrue($iterator->valid());
        
        $iterator->next();
        $this->assertTrue($iterator->valid());
        
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @return void
     */
    final public function testValidWithEmptyArray()
    {
        $data = [];
        $iterator = new ParamsIterator($data);
        
        $this->assertFalse($iterator->valid());
    }

    /**
     * @return void
     */
    final public function testRewind()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];
        $iterator = new ParamsIterator($data);
        
        // Move to second element
        $iterator->next();
        $this->assertEquals('key2', $iterator->key());
        
        // Rewind back to first
        $iterator->rewind();
        $this->assertEquals('key1', $iterator->key());
        $this->assertEquals('value1', $iterator->current());
    }

    /**
     * @return void
     */
    final public function testFullIteration()
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3];
        $iterator = new ParamsIterator($data);
        
        $result = [];
        while ($iterator->valid()) {
            $result[$iterator->key()] = $iterator->current();
            $iterator->next();
        }
        
        $this->assertEquals($data, $result);
    }

    /**
     * @return void
     */
    final public function testMultipleIterations()
    {
        $data = ['x' => 10, 'y' => 20];
        $iterator = new ParamsIterator($data);
        
        // First iteration
        $count1 = 0;
        while ($iterator->valid()) {
            $count1++;
            $iterator->next();
        }
        $this->assertEquals(2, $count1);
        
        // Rewind and iterate again
        $iterator->rewind();
        $count2 = 0;
        while ($iterator->valid()) {
            $count2++;
            $iterator->next();
        }
        $this->assertEquals(2, $count2);
    }

    /**
     * @return void
     */
    final public function testIteratorWithNumericKeys()
    {
        $data = [0 => 'zero', 1 => 'one', 2 => 'two'];
        $iterator = new ParamsIterator($data);
        
        $this->assertEquals(0, $iterator->key());
        $this->assertEquals('zero', $iterator->current());
        
        $iterator->next();
        $this->assertEquals(1, $iterator->key());
        $this->assertEquals('one', $iterator->current());
    }

    /**
     * @return void
     */
    final public function testIteratorWithMixedKeys()
    {
        $data = ['string_key' => 'value1', 0 => 'value2', 'another' => 'value3'];
        $iterator = new ParamsIterator($data);
        
        $keys = [];
        while ($iterator->valid()) {
            $keys[] = $iterator->key();
            $iterator->next();
        }
        
        $this->assertCount(3, $keys);
        $this->assertEquals(array_keys($data), $keys);
    }

    /**
     * @return void
     */
    final public function testIteratorWithNullValue()
    {
        $data = ['key1' => 'value1', 'null_key' => null, 'key2' => 'value2'];
        $iterator = new ParamsIterator($data);
        
        $iterator->next();
        $this->assertEquals('null_key', $iterator->key());
        $this->assertNull($iterator->current());
        $this->assertTrue($iterator->valid());
    }

    /**
     * @return void
     */
    final public function testIteratorReferenceBehavior()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $iterator = new ParamsIterator($data);
        
        // Modify the original array
        $data['key1'] = 'modified_value';
        
        // Iterator should see the modification because it uses a reference
        $this->assertEquals('modified_value', $iterator->current());
    }
}
