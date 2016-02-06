<?php

namespace BMSLink\routes;

use \BMSLink\methods as methods;

use \BMSLink\common as common;

use \BMSLink\sql as sql;

class control extends methods {

}

class devices extends methods {

	public static function POST($req) {
	
		$cmds = ['dev-scan','dev-ctrl'];

		foreach ($req->data as $cmd=>$args) {

			if (in_array($cmd,$cmds)) {

				if (is_array($args)) {

					$cmd .= ' ' . implode(' ', $args);

				} else {

					$cmd .= ' ' . $args;

				}

				Common::API($cmd);

			} else {

				print 'Failed!';

			}

		}

	}

	public static function DELETE($req) {

		$path = explode('/', Common::path($req));

		$id = array_slice($path,-2,2);

		if ($id[0] == 'id') {

			$sql['table'] = 'devices';

			$sql['select'][] = 'serial';

			$sql['where'][] = "`id`='$id[1]'";

			$data = SQL::select($sql);

			if (isset($data[0]['serial'])) {

				$cmd = 'dev-del ' . $data[0]['serial'];

				Common::API($cmd);

				Methods::DELETE($req);

			}

		}

	}

}

class points extends methods {

}

class data extends methods {

}

class automation extends methods {

}

class logs extends methods {

	private static function logIDs($log) {

		$sql['table'] = 'logs';

		$sql['select'][] = 'id';

		$sql['where'][] = "`log`='$log'";

		$ids = SQL::select($sql);

		$tmp = [];

		foreach ($ids as $idx=>$array) {

			foreach ($array as $key=>$val) {

				array_push($tmp,$val);

			}

		}

		$list = array_chunk($tmp, 1000);

		foreach ($list as $idx=>$array) {

			$pages[] = [reset($array), end($array)];

		}

		return $pages;

	}

	private static function baseRequest() {

		$sql['table'] = 'logs';

		$sql['select'][] = 'DISTINCT log';

		$data = SQL::select($sql);

		foreach ($data as $idx=>$array) {

			if (isset($array['log'])) {

				$log = $array['log'];

				$logs[$log] = self::logIDs($log);

			}

		}

		print json_encode($logs);

	}

	public static function GET($req) {

		$url = Common::URL($req);

		if (isset($url['path'])) {

			$path = explode('/', $url['path']);

			$len = count($path);

			if (($len == 2) && ($path[1] == 'logs')) {

				self::baseRequest();

			} else if (($len == 4) && ($path[2] == 'log')) {

				$sql['table'] = 'logs';

				$log = $path[3];

				$sql['where'][] = "`log`='$log'";

				if (isset($url['query'])) {

					$query = explode('=', $url['query']);

					if ($query[0] == 'id') {

						$range = explode('-', $query[1]);

						if (count($range) == 2) {

							if ($range[0] < $range[1]) {

								$sql['where'][] = "`id` BETWEEN $range[0] AND $range[1]";

							} else {

								$sql['where'][] = "`id` BETWEEN $range[1] AND $range[0]";

								$sql['order'][] = "`id`";

								$sql['sort'] = "DESC";

							}

						}

					}

				}

				if (isset($sql)) {

					$data = SQL::select($sql);

					print json_encode($data);

				}

			} else {

				Methods::GET($req);

			}

		}

	}

}

class settings extends methods {

}

class scada {

	private static function cmd($name,$val) {

		$keys = ['device','point'];

		$sql['table'] = 'control';

		$sql['select'] = $keys;

		$sql['where'][] = "`name`='$name'";

		$data = SQL::select($sql);

		if (isset($data[0])) {

		    if (array_keys($data[0]) == $keys) {

            	$point = $data[0][$keys[0]] . ' ' . $data[0][$keys[1]];

            	$cmd = 'set-val ' . $point . ' ' . $val;

            	Common::API($cmd);

            }

        }
    
    }

	public static function GET($req) {

		$url = explode('?',$req->url);

		$name = explode('=',urldecode($url[1]));

		self::cmd($name[0],$name[1]);

	}

	public static function POST($req) {

		foreach ($req->data as $key=>$val) {

			self::cmd($key,$val);
	
		}

	}

}