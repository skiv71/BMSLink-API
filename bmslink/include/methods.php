<?php

namespace BMSLink;

class methods {

    // GET
 
	public static function GET($req) {

        $table = Common::route($req);

        $sql['table'] = $table;
        
        $data = Common::where($req,$table);
    
        if (isset($data['unknown'])) {

            print 'unknown columns: ' . implode(',',$data['unknown']);
 
        } else {

            if (isset($data['where'])) {

                $sql['where'] = $data['where'];

            }

            $data = Common::query($req);

            if (isset($data['command'])) {

                foreach ($data['command'] as $key=>$val) {

                    $array[] = "$key=$val";

                }

                return implode(' ', $array);
        
            } else {

                if (isset($data['select'])) {

                    $sql['select'] = $data['select'];
   
                }

                //var_dump($sql);

                //return;

                $data = SQL::select($sql);

                print json_encode($data);

            }

        }
 
    }

    // POST

    public static function POST($req) {

        $table = Common::route($req);

        $sql['table'] = $table;

        $data = Common::data($req->data,$table);
       
        if (isset($data['unknown'])) {

            print 'unknown columns: ' . implode(',', $data['unknown']);
 
        } else {

            if ((isset($data['columns'])) && (isset($data['values']))) {
          
                $sql['columns'] = '(' . implode(',', $data['columns']) . ')';

                $sql['values'] = '(' . implode(',', $data['values']) . ')';

                SQL::insert($sql);

            } else {

                print 'invalid data';

            }

        }

    }

    public static function PUT($req) {

        $table = Common::route($req);

        $sql['table'] = $table;
        
        $data = Common::where($req,$table);

        if (isset($data['unknown'])) {

            print 'unknown columns: ' . implode(',', $data['unknown']);
 
        } else {

            if (isset($data['where'])) {

                foreach ($data['where'] as $idx=>$val) {

                    if ($idx == 0) {

                        $sql['where'] = $val;

                    } else {

                        $sql['set'][] = str_replace("NULL","", $val);

                    }

                }

                SQL::update($sql);
           
            } else {

                print 'invalid request';

            }

        }

    }

    public static function DELETE($req) {

        $table = Common::route($req);

        $sql['table'] = $table;
        
        $data = Common::where($req,$table);

        if (isset($data['unknown'])) {

            print 'unknown columns: ' . implode(',', $data['unknown']);
 
        } else {

            if (isset($data['where'])) {

                $sql['where'] = $data['where'];

            }

            SQL::delete($sql);
 
        }

    }

}