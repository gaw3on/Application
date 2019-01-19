<?php

namespace Application\Objects;

abstract class ObjectApi {

	public function __construct(array $data) {

		foreach($data as $key => $value) {
        $this->{$key} = $value;
		}
	}


}

