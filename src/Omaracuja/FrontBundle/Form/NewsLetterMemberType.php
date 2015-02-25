<?php

namespace Omaracuja\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsLetterMemberType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('name', 'text', array(
                    'label' => 'Nom : ',
                    'required' => true
                ))
                ->add('firstname', 'text', array(
                    'label' => 'Prénom : ',
                    'required' => true
                ))
                ->add('email', 'email', array(
                    'label' => 'Email : ',
                    'required' => true
                ));
    }

  

    /**
     * @return string
     */
    public function getName() {
        return 'omaracuja_frontbundle_newsletter_member';
    }

}
