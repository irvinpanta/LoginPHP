<?php
$link = null;

class connectdb{
    private $host = '127.0.0.1';
    private $port = 3306;
    private $database = 'weblogin';
    private $username = 'root';
    private $password = '';
    public  $countRow   = 0;
    public  $countColumn = 0;
    private $msg;
    private $desarrollo = 'PROD';
    
    public function __construct() {
        $dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->database";
        try{
            if($GLOBALS["link"] === null){
                $GLOBALS["link"]= new PDO(
                                        $dsn,
                                        $this->username,
                                        $this->password,
                                        array(
                                                PDO::ATTR_PERSISTENT => false,
                    							PDO::MYSQL_ATTR_LOCAL_INFILE=>true
                                             )
                                    );

                $GLOBALS["link"]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $GLOBALS["link"]->exec("set names utf8");
            }
        }catch(PDOException  $e){
            echo "ERROR: " . $e->getMessage();
        }
    }
 
    

    public function query($query){
        try {

            $statement = $GLOBALS["link"]->query($query);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $this->countRow = $statement->rowCount();
            $this->countColumn = $statement->columnCount();

        } catch (PDOException $e) {
            $er = $e->getTrace();
            $bug = $er[1]['args'][0];

            if($this->desarrollo == 'PROD'){
                $result = array('error'=>$this->messageError($e->errorInfo[1]));
            }
        }

        return $result;
    }


    public function setMsgErr($_msg) {
        $this->msg = $_msg;
    }

    public function getMsgErr() {
        return $this->msg;
    }

    private function messageError($code) {
        $msg = '';
        switch ($code) {
            case 1305:
                $msg = 'Procedimiento almacenado no existe.';
                break;   
            default:
                $msg = 'Codigo de error: ' . $code . ': 
                        Por favor comun&iacute;que de este problema a la Oficina de Sistemas.';
        }
        return $msg;
    }

}

?>
