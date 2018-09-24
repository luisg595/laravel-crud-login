<?php

namespace App\Utilities\Response;

class Responses {

	static public function response200($data) {
		return (object) array(
			'code' => 200,
            'status' => "Ok",
            'results' => $data
        );
	}

	static public function response200Update($data) {
		return (object) array(
			'code' => 200,
            'status' => "Register with id ".$data['id']." was updated",
            'id' => $data['id']
        );
	}

	static public function response200Delete($data) {
		return (object) array(
			'code' => 200,
            'status' => "Register with id ".$data['id']." was deleted",
            'id' => $data['id']
        );
	}

	static public function response201($data) {
		return (object) array(
			'code' => 201,
            'status' => "Register with id ".$data['id']." was created",
            'id' => $data['id']
        );
	}

	static public function response404() {
		return (object) array(
            'code' => 404,
            'status' => "We couldn't find that id."
        );
	}

	static public function response409($data) {
		return (object) array(
            'code' => 409,
            'status' => "We couldn't recognize the parameter $data"
        );
	}
}