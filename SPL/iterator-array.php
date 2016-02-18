<?php
class ArrayReloaded implements Iterator
{
	private $array = array();

	private $valid = false;

	public function __construct($array) {
		$this->array = $array;
	}

	public function rewind() {
		$this->valid = (false !== reset($this->array));
	}

	public function current() {
		return current($this->array);
	}

	public function key() {
		return key($this->array);
	}

	public function next() {
		$this->valid = (false !== next($this->array));
	}

	public function valid() {
		return $this->valid;
	}
}

$colors = new ArrayReloaded(array('red', 'green', 'blue',));
foreach ($colors as $key=>$color) {
	echo $key . ": " . $color . "\n";
}

$colors->rewind();
while($colors->valid()) {
	echo $colors->key() . ": " . $colors->current() . "\n";
	$colors->next();
}
for($colors->rewind(); $colors->valid(); $colors->next()) {
	echo $colors->current() . "\n";
}