<?php declare(strict_types=1);

namespace EmsApi\Test;

use EmsApi\Params;
use EmsApi\ParamsIterator;
use Exception;

class ParamsTest extends Base
{
    /**
     * @return void
     */
    final public function testConstructorWithNull()
    {
        $params = new Params();
        $this->assertInstanceOf(Params::class, $params);
        $this->assertEquals(0, $params->getCount());
        $this->assertFalse($params->getReadOnly());
    }

    /**
     * @return void
     */
    final public function testConstructorWithArray()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $params = new Params($data);
        
        $this->assertEquals(2, $params->getCount());
        $this->assertEquals('value1', $params->itemAt('key1'));
        $this->assertEquals('value2', $params->itemAt('key2'));
    }

    /**
     * @return void
     */
    final public function testConstructorWithReadOnly()
    {
        $data = ['key' => 'value'];
        $params = new Params($data, true);
        
        $this->assertTrue($params->getReadOnly());
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The params map is read only.');
        $params->add('newKey', 'newValue');
    }

    /**
     * @return void
     */
    final public function testConstructorWithTraversable()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $paramsSource = new Params($data);
        $params = new Params($paramsSource);
        
        $this->assertEquals(2, $params->getCount());
        $this->assertEquals('value1', $params->itemAt('key1'));
    }

    /**
     * @return void
     */
    final public function testConstructorWithInvalidData()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Params map data must be an array or an object implementing Traversable.');
        new Params('invalid_data');
    }

    /**
     * @return void
     */
    final public function testItemAt()
    {
        $params = new Params(['key' => 'value', 'null_key' => null]);
        
        $this->assertEquals('value', $params->itemAt('key'));
        $this->assertNull($params->itemAt('null_key'));
        $this->assertNull($params->itemAt('non_existent'));
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testAdd()
    {
        $params = new Params();
        
        $params->add('key1', 'value1');
        $this->assertEquals('value1', $params->itemAt('key1'));
        $this->assertEquals(1, $params->getCount());
        
        // Overwrite existing key
        $params->add('key1', 'new_value');
        $this->assertEquals('new_value', $params->itemAt('key1'));
        $this->assertEquals(1, $params->getCount());
        
        // Add with null key (should append)
        $params->add(null, 'appended_value');
        $this->assertEquals(2, $params->getCount());
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testAddToReadOnly()
    {
        $params = new Params(['key' => 'value'], true);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The params map is read only.');
        $params->add('newKey', 'newValue');
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testRemove()
    {
        $params = new Params(['key1' => 'value1', 'key2' => 'value2', 'null_key' => null]);
        
        $removed = $params->remove('key1');
        $this->assertEquals('value1', $removed);
        $this->assertEquals(2, $params->getCount());
        $this->assertFalse($params->contains('key1'));
        
        // Remove null value
        $removedNull = $params->remove('null_key');
        $this->assertNull($removedNull);
        $this->assertEquals(1, $params->getCount());
        
        // Remove non-existent key
        $removedNonExistent = $params->remove('non_existent');
        $this->assertNull($removedNonExistent);
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testRemoveFromReadOnly()
    {
        $params = new Params(['key' => 'value'], true);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The params map is read only.');
        $params->remove('key');
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testClear()
    {
        $params = new Params(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']);
        
        $this->assertEquals(3, $params->getCount());
        $params->clear();
        $this->assertEquals(0, $params->getCount());
        $this->assertFalse($params->contains('key1'));
    }

    /**
     * @return void
     */
    final public function testContains()
    {
        $params = new Params(['key' => 'value', 'null_key' => null]);
        
        $this->assertTrue($params->contains('key'));
        $this->assertTrue($params->contains('null_key'));
        $this->assertFalse($params->contains('non_existent'));
    }

    /**
     * @return void
     */
    final public function testToArray()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $params = new Params($data);
        
        $array = $params->toArray();
        $this->assertIsArray($array);
        $this->assertEquals($data, $array);
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testCopyFrom()
    {
        $params = new Params(['old_key' => 'old_value']);
        $newData = ['key1' => 'value1', 'key2' => 'value2'];
        
        $params->copyFrom($newData);
        
        $this->assertEquals(2, $params->getCount());
        $this->assertFalse($params->contains('old_key'));
        $this->assertTrue($params->contains('key1'));
        $this->assertEquals('value1', $params->itemAt('key1'));
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testCopyFromParams()
    {
        $params = new Params(['old_key' => 'old_value']);
        $sourceParams = new Params(['key1' => 'value1', 'key2' => 'value2']);
        
        $params->copyFrom($sourceParams);
        
        $this->assertEquals(2, $params->getCount());
        $this->assertEquals('value1', $params->itemAt('key1'));
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testCopyFromInvalid()
    {
        $params = new Params();
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Params map data must be an array or an object implementing Traversable.');
        $params->copyFrom('invalid_data');
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testMergeWithNonRecursive()
    {
        $params = new Params(['key1' => 'value1', 'key2' => 'value2']);
        $newData = ['key2' => 'new_value2', 'key3' => 'value3'];
        
        $params->mergeWith($newData, false);
        
        $this->assertEquals(3, $params->getCount());
        $this->assertEquals('value1', $params->itemAt('key1'));
        $this->assertEquals('new_value2', $params->itemAt('key2'));
        $this->assertEquals('value3', $params->itemAt('key3'));
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testMergeWithRecursive()
    {
        $params = new Params([
            'key1' => 'value1',
            'nested' => ['a' => 'original_a', 'b' => 'original_b'],
            'indexed' => [1, 2, 3]
        ]);
        
        $newData = [
            'nested' => ['a' => 'new_a', 'c' => 'new_c'],
            'indexed' => [4, 5],
            'key2' => 'value2'
        ];
        
        $params->mergeWith($newData, true);
        
        $this->assertEquals('value1', $params->itemAt('key1'));
        $this->assertEquals('value2', $params->itemAt('key2'));
        
        $nested = $params->itemAt('nested');
        $this->assertEquals('new_a', $nested['a']);
        $this->assertEquals('original_b', $nested['b']);
        $this->assertEquals('new_c', $nested['c']);
        
        $indexed = $params->itemAt('indexed');
        $this->assertCount(5, $indexed);
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testMergeWithParams()
    {
        $params = new Params(['key1' => 'value1']);
        $sourceParams = new Params(['key2' => 'value2']);
        
        $params->mergeWith($sourceParams);
        
        $this->assertEquals(2, $params->getCount());
        $this->assertEquals('value2', $params->itemAt('key2'));
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testMergeWithInvalid()
    {
        $params = new Params();
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Params map data must be an array or an object implementing Traversable.');
        $params->mergeWith('invalid_data');
    }

    /**
     * @return void
     */
    final public function testMergeArray()
    {
        $a = ['key1' => 'a1', 'nested' => ['x' => 'ax']];
        $b = ['key2' => 'b2', 'nested' => ['y' => 'by']];
        
        $result = Params::mergeArray($a, $b);
        
        $this->assertEquals('a1', $result['key1']);
        $this->assertEquals('b2', $result['key2']);
        $this->assertEquals('ax', $result['nested']['x']);
        $this->assertEquals('by', $result['nested']['y']);
    }

    /**
     * @return void
     */
    final public function testGetCount()
    {
        $params = new Params();
        $this->assertEquals(0, $params->getCount());
        
        $params = new Params(['key1' => 'value1', 'key2' => 'value2']);
        $this->assertEquals(2, $params->getCount());
    }

    /**
     * @return void
     */
    final public function testCountable()
    {
        $params = new Params(['key1' => 'value1', 'key2' => 'value2']);
        $this->assertEquals(2, count($params));
    }

    /**
     * @return void
     */
    final public function testGetKeys()
    {
        $params = new Params(['key1' => 'value1', 'key2' => 'value2']);
        $keys = $params->getKeys();
        
        $this->assertIsArray($keys);
        $this->assertCount(2, $keys);
        $this->assertContains('key1', $keys);
        $this->assertContains('key2', $keys);
    }

    /**
     * @return void
     */
    final public function testArrayAccessOffsetExists()
    {
        $params = new Params(['key' => 'value', 'null_key' => null]);
        
        $this->assertTrue(isset($params['key']));
        $this->assertTrue(isset($params['null_key']));
        $this->assertFalse(isset($params['non_existent']));
    }

    /**
     * @return void
     */
    final public function testArrayAccessOffsetGet()
    {
        $params = new Params(['key' => 'value']);
        
        $this->assertEquals('value', $params['key']);
        $this->assertNull($params['non_existent']);
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testArrayAccessOffsetSet()
    {
        $params = new Params();
        
        $params['key1'] = 'value1';
        $this->assertEquals('value1', $params['key1']);
        
        $params['key1'] = 'new_value';
        $this->assertEquals('new_value', $params['key1']);
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testArrayAccessOffsetUnset()
    {
        $params = new Params(['key1' => 'value1', 'key2' => 'value2']);
        
        unset($params['key1']);
        $this->assertFalse(isset($params['key1']));
        $this->assertEquals(1, $params->getCount());
    }

    /**
     * @return void
     */
    final public function testIteratorAggregate()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];
        $params = new Params($data);
        
        $iterator = $params->getIterator();
        $this->assertInstanceOf(ParamsIterator::class, $iterator);
        
        $result = [];
        foreach ($params as $key => $value) {
            $result[$key] = $value;
        }
        
        $this->assertEquals($data, $result);
    }

    /**
     * @return void
     */
    final public function testForeachIteration()
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3];
        $params = new Params($data);
        
        $count = 0;
        foreach ($params as $key => $value) {
            $this->assertEquals($data[$key], $value);
            $count++;
        }
        
        $this->assertEquals(3, $count);
    }
}
