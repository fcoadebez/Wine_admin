<?php

namespace App\Http;

use Illuminate\Http\Request;

class APIResponse{
	private $_request;

	private $status;
	private $headers;
	private $data;
	private $body;

	public function __construct(Request $request){
		$this->_request = $request;
		
		$this->status = 101;
		$this->headers = null;
		$this->data = $this->_request->input();
		$this->body = null;
	}

	public function setStatus(int $code){
		$this->status = $code;
	}

	public function setHeaders(array $headers){
		$this->headers = $headers;
	}

	public function setData(array $data){
		$this->data = $data;
	}

	public function setBody($body){
		$this->body = $body;
	}

	public function json($callback = null){
		return response()->json([
			"status" => $this->status,
			"headers" => $this->headers,
			"data" => $this->data,
			"body" => $this->body
		], $this->status)->withCallback((is_null($callback) ? $this->_request->input('callback') : $callback));
	}
	public function xml($status = 200){
		return response()->xml([
			"status" => $this->status,
			"headers" => $this->headers,
			"data" => $this->data,
			"body" => $this->body
		], $status);
	}
}
