<?php
namespace Src;

class Photo{
	private const IMG_DIR = "/../img/";
	public function putFile(string $name, string $bin){
		$data = base64_decode($bin);
		file_put_contents(dirname(__FILE__).self::IMG_DIR.$name, $data);
	}
}
