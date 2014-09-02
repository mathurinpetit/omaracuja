<?php

namespace Omaracuja\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PresentationType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title', 'text', array(
                    'label' => 'Titre :'
                ))
                ->add('paragraph1', 'textarea', array(
                    'label' => '1er paragraphe :'
                ))
                ->add('paragraph2', 'textarea', array(
                    'label' => '2nd paragraphe :'
                ))
                ->add('paragraph3', 'textarea', array(
                    'label' => '3eme paragraphe :'
                ))
                ->add('map_link', 'text', array(
                    'label' => 'Lien google map :'
                ))
                ->add('adresse', 'textarea', array(
                    'label' => 'Adresse :'
                ))
                ->add('contact', 'textarea', array(
                    'label' => 'Contact :'
                ))
                ->add('fb_link', 'text', array(
                    'label' => 'Lien Facebook :'
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Omaracuja\AdminBundle\Entity\Presentation'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'omaracuja_frontbundle_presentation';
    }

}
