<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class sessions extends Entities implements iRest
{
    const PRIMARY = [
    'session_id',
    ];

    const COLUMNS = [
    'user_id','user_ip','session_id','session_expires','session_data','user_online_status',
    ];

    const VALIDATION = [];

    const BINARY = [
    'user_id',
    ];

    /**
     * @param array $return
     * @param string|null $primary
     * @param array $argv
     * @return bool
     */
    public static function Get(array &$return, string $primary = null, array $argv) : bool
    {
        $get = isset($argv['select']) ? $argv['select'] : self::COLUMNS;
        $where = isset($argv['where']) ? $argv['where'] : [];

        $group = $sql = '';

        if (isset($argv['pagination'])) {
            if (!empty($argv['pagination']) && !is_array($argv['pagination'])) {
                $argv['pagination'] = json_decode($argv['pagination'], true);
            }
            if (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] != null) {
                $limit = ' LIMIT ' . $argv['pagination']['limit'];
            } else {
                $limit = '';
            }

            $order = '';
            if (!empty($limit)) {

                 $order = ' ORDER BY ';

                if (isset($argv['pagination']['order']) && $argv['pagination']['order'] != null) {
                    if (is_array($argv['pagination']['order'])) {
                        foreach ($argv['pagination']['order'] as $item => $sort) {
                            $order .= $item .' '. $sort;
                        }
                    } else {
                        $order .= $argv['pagination']['order'];
                    }
                } else {
                    $order .= self::PRIMARY[0] . ' ASC';
                }
            }
            $limit = $order .' '. $limit;
        } else {
            $limit = ' ORDER BY ' . self::PRIMARY[0] . ' ASC LIMIT 100';
        }

        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
                $group .= ', ';
            }
            if (in_array($column, self::BINARY)) {
                $sql .= "HEX($column) as $column";
                $group .= "$column";
            } else {
                $sql .= $column;
                $group .= $column;
            }
        }

        if (isset($argv['aggregate']) && (is_array($argv['aggregate']) || $argv['aggregate'] = json_decode($argv['aggregate'], true))) {
            foreach($argv['aggregate'] as $key => $value){
                switch ($key){
                    case 'count':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "COUNT($value) AS count ";
                        break;
                    case 'AVG':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "AVG($value) AS avg ";
                        break;
                    case 'MIN':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "MIN($value) AS min ";
                        break;
                    case 'MAX':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "MAX($value) AS max ";
                        break;
                }
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM RootPrerogative.sessions';

        $pdo = Database::database();

        if (empty($primary)) {
            if (!empty($where)) {
                $build_where = function (array $set, $join = 'AND') use (&$pdo, &$build_where) {
                    $sql = '(';
                    foreach ($set as $column => $value) {
                        if (is_array($value)) {
                            $sql .= $build_where($value, $join === 'AND' ? 'OR' : 'AND');
                        } else {
                            if (in_array($column, self::BINARY)) {
                                $sql .= "($column = UNHEX(" . $pdo->quote($value) . ")) $join ";
                            } else {
                                $sql .= "($column = " . $pdo->quote($value) . ") $join ";
                            }
                        }
                    }
                    return substr($sql, 0, strlen($sql) - (strlen($join) + 1)) . ')';
                };
                $sql .= ' WHERE ' . $build_where($where);
            }
        } else {
            $primary = $pdo->quote($primary);
            $sql .= ' WHERE  session_id=' . $primary .'';
        }

        if (isset($argv['aggregate'])) {
            $sql .= ' GROUP BY ' . $group . ' ';
        }

        $sql .= $limit;

        $return = self::fetch($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        /**
        *   The next part is so every response from the rest api
        *   formats to a set of rows. Even if only one row is returned.
        *   You must set the third parameter to true, otherwise '0' is
        *   apparently in the self::COLUMNS
        */

        
        if (empty($primary) && ($argv['pagination']['limit'] ?? false) !== 1 && count($return) && in_array(array_keys($return)[0], self::COLUMNS, true)) {  // You must set tr
            $return = [$return];
        }

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        $sql = 'INSERT INTO RootPrerogative.sessions (user_id, user_ip, session_id, session_expires, session_data, user_online_status) VALUES ( UNHEX(:user_id), :user_ip, :session_id, :session_expires, :session_data, :user_online_status)';
        $stmt = Database::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            
                $user_id = $argv['user_id'];
                $stmt->bindParam(':user_id',$user_id, 2, 16);
                    
                $user_ip = isset($argv['user_ip']) ? $argv['user_ip'] : null;
                $stmt->bindParam(':user_ip',$user_ip, 2, 255);
                    
                $session_id = $argv['session_id'];
                $stmt->bindParam(':session_id',$session_id, 2, 255);
                    $stmt->bindValue(':session_expires',$argv['session_expires'], 2);
                    $stmt->bindValue(':session_data',$argv['session_data'], 2);
                    
                $user_online_status = isset($argv['user_online_status']) ? $argv['user_online_status'] : '1';
                $stmt->bindParam(':user_online_status',$user_online_status, 0, 1);
        

        return $stmt->execute();
    }

    /**
    * @param array $return
    * @param string $primary
    * @param array $argv
    * @return bool
    */
    public static function Put(array &$return, string $primary, array $argv) : bool
    {
        if (empty($primary)) {
            return false;
        }

        foreach ($argv as $key => $value) {
            if (!in_array($key, self::COLUMNS)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE RootPrerogative.sessions ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (!empty($argv['user_id'])) {
            $set .= 'user_id=UNHEX(:user_id),';
        }
        if (!empty($argv['user_ip'])) {
            $set .= 'user_ip=:user_ip,';
        }
        if (!empty($argv['session_id'])) {
            $set .= 'session_id=:session_id,';
        }
        if (!empty($argv['session_expires'])) {
            $set .= 'session_expires=:session_expires,';
        }
        if (!empty($argv['session_data'])) {
            $set .= 'session_data=:session_data,';
        }
        if (!empty($argv['user_online_status'])) {
            $set .= 'user_online_status=:user_online_status,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        
        $primary = $db->quote($primary);
        $sql .= ' WHERE  session_id=' . $primary .'';

        $stmt = $db->prepare($sql);

        global $json;

        if (empty($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        if (!empty($argv['user_id'])) {
            $user_id = $argv['user_id'];
            $stmt->bindParam(':user_id',$user_id, 2, 16);
        }
        if (!empty($argv['user_ip'])) {
            $user_ip = $argv['user_ip'];
            $stmt->bindParam(':user_ip',$user_ip, 2, 255);
        }
        if (!empty($argv['session_id'])) {
            $session_id = $argv['session_id'];
            $stmt->bindParam(':session_id',$session_id, 2, 255);
        }
        if (!empty($argv['session_expires'])) {
            $stmt->bindValue(':session_expires',$argv['session_expires'], 2);
        }
        if (!empty($argv['session_data'])) {
            $stmt->bindValue(':session_data',$argv['session_data'], 2);
        }
        if (!empty($argv['user_online_status'])) {
            $user_online_status = $argv['user_online_status'];
            $stmt->bindParam(':user_online_status',$user_online_status, 0, 1);
        }

        if (!$stmt->execute()){
            return false;
        }

        $return = array_merge($return, $argv);

        return true;

    }

    /**
    * @param array $remove
    * @param string|null $primary
    * @param array $argv
    * @return bool
    */
    public static function Delete(array &$remove, string $primary = null, array $argv) : bool
    {
        $sql = 'DELETE FROM RootPrerogative.sessions ';

        foreach($argv as $column => $constraint){
            if (!in_array($column, self::COLUMNS)){
                unset($argv[$column]);
            }
        }

        if (empty($primary)) {
            /**
            *   While useful, we've decided to disallow full
            *   table deletions through the rest api. For the
            *   n00bs and future self, "I got chu."
            */
            if (empty($argv)) {
                return false;
            }
            $pdo = self::database();

            $build_where = function (array $set, $join = 'AND') use (&$pdo, &$build_where) {
                $sql = '(';
                foreach ($set as $column => $value) {
                    if (is_array($value)) {
                        $sql .= $build_where($value, $join === 'AND' ? 'OR' : 'AND');
                    } else {
                        if (in_array($column, self::BINARY)) {
                            $sql .= "($column = UNHEX(" . $pdo->quote($value) . ")) $join ";
                        } else {
                            $sql .= "($column = " . $pdo->quote($value) . ") $join ";
                        }
                    }
                }
                return substr($sql, 0, strlen($sql) - (strlen($join) + 1)) . ')';
            };
            $sql .= ' WHERE ' . $build_where($argv);
        } else {
            $primary = Database::database()->quote($primary);
            $sql .= ' WHERE  session_id=' . $primary .'';
        }

        $remove = null;

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        return self::execute($sql);
    }
}