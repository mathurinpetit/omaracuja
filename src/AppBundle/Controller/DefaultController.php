<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\NotBlank;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request,\Swift_Mailer $mailer)
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
        $defaultData = array('message' => '');
        $emailForm = $this->createFormBuilder($defaultData)
               ->add('name', TextType::class,array('constraints' => array( new NotBlank()), "attr" => array('class' => 'form-control', 'placeholder' => 'Nom', 'oninvalid' => "this.setCustomValidity('Dis nous ton nom ou un pseudo au pire!')", 'oninput' => "setCustomValidity('')")))
               ->add('email', EmailType::class,array('constraints' => array( new NotBlank()), "attr" => array('class' => 'form-control', 'placeholder' => 'Email','oninvalid' => "this.setCustomValidity('Donnes nous ton email, on t\'écrira!')", 'oninput' => "setCustomValidity('')")))
               ->add('phone', TelType::class,array('constraints' => array( new NotBlank()), "attr" => array('class' => 'form-control', 'placeholder' => 'Tél','oninvalid' => "this.setCustomValidity('N\'oublies pas de mettre ton numéro de téléphone')", 'oninput' => "setCustomValidity('')")))
               ->add('message', TextareaType::class,array('constraints' => array( new NotBlank()), "attr" => array('class' => 'form-control', 'placeholder' => 'Message', 'rows' => 5, 'oninvalid' => "this.setCustomValidity('Livres-toi un peu')", 'oninput' => "setCustomValidity('')")))
               ->add('Envoyer', SubmitType::class,array("attr" => array('class' => 'form-control', 'value' => 'Envoyer', 'class' => 'btn btn-primary')))
               ->getForm();

        $emailForm->handleRequest($request);

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $data = $emailForm->getData();

            $message = (new \Swift_Message($data['name'].' : Nouveau contact OMaracuja'))
        ->setFrom($data['email'])
        ->setTo('contact@omaracuja.com')
        ->setBody($data['name'].' / '.$data['email'].' / '.$data['phone'].' a écrit :


        '.$data['message'],
            'text/plain'
        );

            $mailer->send($message);
        }

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR, 'form' => $emailForm->createView(),
            'dates' => $dates,'onyetait' => $onyetait
        ]);
    }
}
