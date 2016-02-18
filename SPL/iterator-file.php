<?php
class fileReloaded implements Iterator
{

	private $valid = false;

	private $result = array();

	public function __construct($path)
	{
		$file = fopen($path, "r") or die("Unable to open file");
		while (!feof($file)) {
			$this->result[] = fgets($file);
		}
		fclose($file);
	}

	public function rewind()
	{
		$this->valid = (false !== reset($this->result));
	}

	public function current()
	{
		return current($this->result);
	}

	public function key()
	{
		return key($this->result);
	}

	public function next()
	{
		return $this->valid = (false !== next($this->result));
	}

	public function valid()
	{
		return $this->valid;
	}
}

$fileReloaded = new fileReloaded('../syncIaskVplay.php');
foreach ($fileReloaded as $line => $value) {
	if ($line == 102) echo $line . ": " . $value . "\n";
}
for($fileReloaded->rewind(); $fileReloaded->valid(); $fileReloaded->next()) {
	if ($fileReloaded->key() >= 1 && $fileReloaded->key() <= 10) echo $fileReloaded->current();
}


class fileReloaded1 implements Iterator
{
	protected $currentRow = 0;

	protected $result = array();

	public function __construct($path)
	{
		$file = fopen($path, "r") or die("Unable to open file");
		while (!feof($file)) {
			$this->result[] = fgets($file);
		}
		fclose($file);
	}

	public function rewind()
	{
		$this->currentRow = 0;
	}

	public function next()
	{
		++$this->currentRow;
	}

	public function current()
	{
		return $this->result[ $this->currentRow ];
	}

	public function key()
	{
		return $this->currentRow;
	}

	public function valid()
	{
		return isset($this->result[ $this->currentRow ]);
	}
}

$fileReloaded1 = new fileReloaded1('iterator-array.php');
foreach($fileReloaded1 as $key => $value) {
	if ($key >= 1 && $key <= 10) echo $key . ': ' . $value . "\n";
}
$fileReloaded1->rewind();
while ($fileReloaded1->valid()) {
	$line = $fileReloaded1->key();
	if ($line >= 10 && $line <= 20) echo $line . ": " . $fileReloaded1->current() . "\n";
	$fileReloaded1->next();
}