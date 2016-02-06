<?php

namespace BMSLink;

Class sql {

    // initialise

    public static function init() {

        $host = 'ubuntu';

        $database = 'bmslink';

        $user = 'root';

        $pass = '';

        $PDO = [

            'mysql:host=' . $host . ';dbname=' . $database,

            $user,

            $pass

        ];
 
        \Flight::register('db','PDO',$PDO,function($db){

            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        });

    }

    // main SQL query

    public static function query($statement) {

        $db = \Flight::db();

        return $db->query($statement);
    
    }

    // select

    public static function select($sql) {

        if (isset($sql['table'])) {

            $statement = 'SELECT ';

        } else {

            print 'invalid request';

        }

        if (isset($sql['select'])) {

            $statement .= implode(',', $sql['select']) . ' ';

        } else {

            $statement .= '* ';

        } 

        $statement .= 'FROM ' . $sql['table'] . ' ';

        if (isset($sql['where'])) {

            $statement .= 'WHERE ' . implode(' AND ', $sql['where']);

        }

        if (isset($sql['order'])) {

            $statement .= ' ORDER BY ' . implode(',', $sql['order']);

        }

        if (isset($sql['sort'])) {

            $statement .= ' ' . $sql['sort'];

        }

        $query = self::query($statement);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
 
    }

    // insert

    public static function insert($sql) {

        $reqd = ['table','columns','values'];

        $n = 0;

        foreach ($sql as $key=>$val) {

            if (in_array($key, $reqd))

                $n++;
       
        }

        if ($n == count($reqd)) {

            $statement = 'INSERT INTO ' . $sql['table'] . ' '. $sql['columns'] . ' VALUES ' . $sql['values'];
   
            $query = self::query($statement);

            $records = self::records($query);

            print $records;

        } else {

            print 'invalid request!';

        }

    }

    // update

    public static function update($sql) {

        $reqd = ['table','where','set'];

        $n = 0;

        foreach ($sql as $key=>$val) {

            if (in_array($key, $reqd))

                $n++;
       
        }

        if ($n == count($reqd)) {

            $statement = 'UPDATE ' . $sql['table'] . ' SET ' . implode(',', $sql['set']) . ' WHERE ' . $sql['where'];

            $query = self::query($statement);

            $records = self::records($query);

            print $records;

        } else {

            print 'invalid request';

        }

    }

    // delete

    public static function delete($sql) {

        $reqd = ['table','where'];

        $n = 0;

        foreach ($sql as $key=>$val) {

            if (in_array($key, $reqd))

                $n++;
       
        }

        if ($n == count($reqd)) {

            $statement = 'DELETE FROM ' . $sql['table'] . ' WHERE ' . implode(' AND ', $sql['where']);

            $query = self::query($statement);

            $records = self::records($query);

            print $records;

        } else {

            print 'invalid request';

        }
  
    }

    // describe

    public static function describe($sql) {

        if (isset($sql['table'])) {

            $statement = 'DESCRIBE ' . $sql['table'];

            $query = self::query($statement);

            $data =  $query->fetchAll(\PDO::FETCH_ASSOC);

            return $data;

        } else {

            print 'invalid request';

        }

    }

    // records

    public static function records($query) {

        $count = $query->rowCount();

        return $count;

    }

}