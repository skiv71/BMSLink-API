<?php

namespace BMSLink;

class common {

    public static function route($req) {

        $path = explode('/',self::path($req));

        return $path[1];

    }

    // URL

    public static function URL($req) {

        $url = parse_url($req->url);

        return $url;
 
    }

    // path

    public static function path($req) {

        $url = self::URL($req);

        if (isset($url['path']))

            return $url['path'];

    }

    // URI

    public static function URI($path) {
  
        foreach (explode('/',$path) as $idx => $val) {

            if ($idx == 1) {

                $array['table'] = $val;

                $array['columns'] = self::columns($val);

            }

            if ($idx > 1) {

                if ($idx & 1) {

                    if (in_array($key, $array['columns'])) {

                        $array['where'][$key] = urldecode($val);

                    } else {

                        $array['unknown'][] = $key;

                    }

                } else {
                  
                    $key = $val;

                }

            }
  
        }
               
        if (isset($array))

            return $array;
     
    }

    // form data

    public static function data($table,$data) {

        $cols = self::columns($table);

        foreach ($data as $key => $val) {

            if (in_array($key, $cols)) {

                $array['columns'][] = $key;

                $array['values'][] = $val;

            } else {

                $array['unknown'][] = $key;
           
            }

        }

        if (isset($array))

            return $array;

    }
   
    // query data

    public static function query($req) {

        foreach (($req->query) as $key => $val) {

            if ($val) {

                $temp['command'][$key] = $val;

            } else {

                $temp['select'][] = $key;

            }
  
        }

        if (isset($temp['command']))

            $array['command'] = $temp['command'];

        if (isset($temp['select']))

            $array['select'] = self::select($temp['select']);

        if (isset($array))

            return $array;

    }
   
    // table columns

    public static function columns($table) {
 
        $sql = 'DESCRIBE ' . $table;

        $query = self::SQL($sql);

        $data = $query->fetchAll(\PDO::FETCH_ASSOC);

        $len = count($data);

        for ($i=0; $i<$len; $i++) {

            $array[] = $data[$i]['Field'];

        }

        if (isset($array))

            return $array;

    }

    // where

    public static function where($data) {

        foreach($data as $col=>$val) {

            if (isset($str)) {

                $str .= " AND `$col`='$val'";

            } else {

                if (strpos($val,',')) {

                    $str = "`$col` IN ($val)";

                    break;

                } else {

                    $str = "`$col`='$val'";

                }

            }

        }

        if (isset($str))

            return $str;

    }

    // SQL values

    public static function values($data) {

        foreach ($data['columns'] as $idx=>$val) {

            $cols[] = '`' . $val . '`';

        }

        foreach ($data['values'] as $idx=>$val) {

            $vals[] = '\'' . $val . '\'';

        }

        if ( (isset($cols)) && (isset($vals)) ) {

            $str = '(' . implode(',',$cols) . ') VALUES (' . implode(',',$vals) . ')';

            return $str;

        }

    }

    // select

    public static function select($data) {

        foreach($data as $idx=>$val) {

            if (isset($str)) {

                $str .= ",`$val`";

            } else {

                $str = "`$val`";

            }

        }

        if (isset($str))

            return $str;

    }

    // sql

    

    // SQL records

    public static function records($query) {

        $count = $query->rowCount();

        print $count;

    }

}