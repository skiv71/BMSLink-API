<?php

namespace BMSLink;

class common {

    // route

    public static function route($req) {

        $path = explode('/',self::path($req));

        return $path[1];

    }

    // routes

    public static function routes() {

        foreach (get_declared_classes() as $class) {

            if (strpos($class,'Link\routes')) {

                $route = end(explode('\\',$class));

                $routes[$route] = $class;

            }
             
        }

        if (isset($routes))

            return $routes;

    }
   
    // URL

    public static function URL($req) {

        return parse_url($req->url);
    
    }

    // path

    public static function path($req) {

        $url = self::URL($req);

        if (isset($url['path']))

            return $url['path'];

    }
 
    // data

    public static function data($data,$table) {

        $cols = self::columns($table);

        foreach ($data as $key => $val) {

            if (in_array($key, $cols)) {

                $array['columns'][] = "`$key`";

                $array['values'][] = "'$val'";

            } else {

                $array['unknown'][] = "'$key'";
           
            }

        }

        if (isset($array))

            return $array;

    }
   
    // query

    public static function query($req) {
       
        foreach (($req->query) as $key => $val) {

            if ($val) {

                $array['command'][$key] = $val;

            } else {

                $array['select'][] = "`$key`";

            }
  
        }

        if (isset($array))

            return $array;

    }
   
    // columns

    public static function columns($table) {
 
        $sql['table'] = $table;
        
        $data = SQL::describe($sql);

        $len = count($data);

        for ($i=0; $i<$len; $i++) {

            $array[] = $data[$i]['Field'];

        }

        if (isset($array))

            return $array;

    }

    // where

    public static function where($req,$table) {

        $path = explode('/',self::path($req));

        $cols = self::columns($table);

        foreach($path as $idx=>$str) {

            $val = urldecode($str);

            if ($idx > 1) {

                if ($idx & 1) {

                    if ($col) {

                        if (strpos($val,',')) {

                            $arr = explode(',', $val);

                            $range = "'" . implode("','",$arr) . "'";

                            $array['where'][] = "`$col` IN ($range)";

                        } else {

                            $array['where'][] = "`$col`='$val'";

                        }

                    }

                } else {

                    if (in_array($val, $cols)) {

                        $col = $val;

                    } else {

                        $array['unknown'][] = "'$val'";

                        $col = null;

                    }

                }

            }

        }

        if (isset($array))

            return $array;

    }

    // API

    public static function API($cmd) {

        $path = '/scada/temp/API';

        $file = $path . '/' . gettimeofday(true) . '.cmd';

        exec("echo $cmd > $file");

        print '0';

    }

}