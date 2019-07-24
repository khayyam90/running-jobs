<?php 

namespace App\Entity;

use App\Utils\Root;

class Process{
    protected $data = array();
    
    public function get($field){
        return $this->data[$field];
    }
    
    public function __construct($data = array()){
        $this->data = $data;
    }
       
    public function getStandardOutput(){
        return $this->getStandardFile("out");
    }
    
    public function getStandardError(){
        return $this->getStandardFile("err");
    }

    public function getStandardContent($file){
        return file_get_contents($this->getStandardFile($file));        
    }
    
    public function getStandardFile($file){
        return Root::getPath(). "storage/processes/" . $this->data["id"] . "/" . $file;
    }
    
    public static function getAllRunning(){
        $rows = app('db')->select("SELECT * FROM process");
        $result = [];
        foreach ($rows as $row){
            $result[] = new Process((array)$row);
        }
        
        return $result;        
    }   
    
    public static function getById($id){
        $row = app('db')->selectOne("SELECT * FROM process where id = :id", ["id" => $id]);
        
        if ($row){
            return new Process((array)$row);
        }else{
            return null;
        }        
    }
    
    public function kill(){
        posix_kill($this->data["pid"], SIG_KILL );
    }
    
    public function delete(){
        app("db")->delete("process", ["id" => $this->data["id"]]);
    }
    
    public function insert(){
        $id = app("db")->table("process")->insertGetId([
            "hash" => $this->data["hash"],
            "cmd" => $this->data["cmd"],
            "start" => $this->data["start"]
        ]);
        $this->data["id"] = $id;        
    }
    
    public function update(array $data){
        foreach ($data as $k => $v){
            $this->data[$k] = $v;
        }
        
        app("db")->table("process")
            ->where("id", $this->data["id"])
            ->update($this->data)
        ;
    }
}