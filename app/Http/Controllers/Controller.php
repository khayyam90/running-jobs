<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;


use App\Entity\Process;
use App\Utils\Random;
use \DateTime;
use App\Utils\Root;

class Controller extends BaseController
{
    protected $twig;
    
    protected function initTwig(){
        $loader = new \Twig\Loader\FilesystemLoader(Root::getPath() . "/resources/views");
        $this->twig = new \Twig\Environment($loader, [
            'cache' => Root::getPath() . "/storage/cache",
        ]);        
    }
    
    public function index(){
        $this->initTwig();
        $processes = Process::getAllRunning();

        return $this->twig->render("index.html.twig", ["processes" => $processes]);
    }
    
    public function output(){
        $process = Process::getById($_POST["id"]);
        
        return new Response($process->getStandardContent("out"));
    }   
    
    public function start(){
        $cmd = $_POST["cmd"];
        
        $hash = Random::make(15);
        $process = new Process(array(
            "cmd" => $cmd,
            "start" => new DateTime(),
            "hash" => $hash
        ));
        $process->insert();
        
        $processDirectory = Root::getPath() . "storage/processes/" . $process->get("id");
        
        if (file_exists($processDirectory)){
            system("rm -rf $processDirectory");
        }
        
        mkdir($processDirectory);
        touch($process->getStandardFile("out"));
        touch($process->getStandardFile("err"));
        
        $descriptorspec = array(
            0 => array("pipe", "r"),  
            1 => array("file", $process->getStandardFile("out"), "a"),
            2 => array("file", $process->getStandardFile("err"), "a") 
        );
        
        // the background jobs are less prioritary than all the other processes. They shouldn't penalize the webserver normal activity
        $cmd = "nice -n 500 $cmd";
        
        $p = proc_open($cmd, $descriptorspec, $pipes);
        $status = proc_get_status($p);
        $pid = $status["pid"];
        $process->update(["pid" => $pid]);
        
        return new RedirectResponse("/");
    }
}
