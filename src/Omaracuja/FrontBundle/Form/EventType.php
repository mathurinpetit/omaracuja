<?php

namespace Omaracuja\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
        ->add('title', 'text', array(
        'label' => 'Titre'
        ))
        ->add('description')
        ->add('public', 'checkbox', array(
        'label' => 'Evènement publique'
        ))
        ->add('startAt','datetime',array('label' => 'Date de début','widget' => 'single_text','attr' => array('class' => 'datetimepicker')))
        ->add('endAt', 'datetime',array('label' => 'Date de fin','widget' => 'single_text','attr' => array('class' => 'datetimepicker')))
        
        ->add('place', 'text', array(
        'label' => 'Lieu'
        ))
        ->add('proposedTeam', 'entity', array(
        'class' => 'OmaracujaUserBundle:User',
        'property' => 'username', 'expanded' => false, 'multiple' => true));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Omaracuja\FrontBundle\Entity\Event'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'omaracuja_frontbundle_event';
    }

}
