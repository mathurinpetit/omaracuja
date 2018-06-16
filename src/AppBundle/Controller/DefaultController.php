<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $string_dates = file('../dates.csv');
        $dates = array();
        foreach ($string_dates as $line_num => $date) {
            if($date){
                $dates[$line_num] = str_getcsv($date,';');
            }
        }
        $string_onyetait = file('../onyetait.csv');
        $onyetait = array();
        foreach ($string_onyetait as $line_num => $onyetait_line) {
            if($onyetait_line){
                $onyetait[$line_num] = str_getcsv($onyetait_line,';');
            }
        }
        //var_dump($onyetait); exit;
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'dates' => $dates,'onyetait' => $onyetait
        ]);
    }
}
