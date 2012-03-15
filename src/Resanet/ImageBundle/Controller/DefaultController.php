<?php

namespace Resanet\ImageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('ResanetImageBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function uploaderAction()
    {
        return $this->render('ResanetImageBundle:Default:uploader.html.twig');
    }
    
    public function receptionAction()
    {   
        $imgs=$_FILES;
        $target_path = 'uploader/img/';
        
        foreach($imgs as $img){
            if (($img["type"] == "image/jpeg") ) {
                  $nom=date_create()->format('U').$this->cleanNameAction($img["name"]);
                     
                  $bool=move_uploaded_file($img["tmp_name"], $target_path.$nom);
                  $this->createThumbnail($nom, $target_path);
                  
            } 
            
        }
        return new Response('test');
    }
    
    public function cleanNameAction($string){
        $tab=array('à', ' ','á','â','ã','ä','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ','À','Â','Ã','Ä','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','@');       $chaine=strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        $chaine=  str_replace($tab, "_", $string);
        return strtolower($chaine);
    }
    
    public function createThumbnail($name, $target_path){
        $hauteur=100;
        $largeur=100;
        $destination=  imagecreatetruecolor($hauteur,$largeur);
        list($width, $height) = getimagesize($target_path.$name); 
        imagecopyresized($destination,imagecreatefromjpeg($target_path.$name), 0,0, 0,0,$hauteur,$largeur,$width,$height);
        imagejpeg($destination, $target_path.'/thumbnail/'.$name);
    }
    
    public function listeImageAction(){
        $path = 'uploader/img/';
        $dir = opendir($path); 
        while($file = readdir($dir)) { 
            $tab[]=$file; 
        } 
        closedir($dir); 
        $tab=array_slice($tab,2);
        $reponse=array();
        foreach ($tab as $img){
            if ($img!="thumbnail")  $reponse[]=array('img'=>$img);
        }
        return new Response(json_encode($reponse));
    }
    
    public function supprimerImageAction(){
        $path = 'uploader/img/';
        $img=  $this->getRequest()->get('img');
        unlink($path.$img);
        unlink($path.'thumbnail/'.$img);
        return new Response($img);
    }
    
}
