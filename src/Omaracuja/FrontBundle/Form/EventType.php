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
                    'label' => 'Titre',
                    'required' => true
                ))
                ->add('description')
                ->add('public', 'checkbox', array(
                    'label' => 'Evènement publique'
                ))
                ->add('startAtFr', 'text', array('label' => 'Date de début', 'attr' => array('class' => 'datetimepicker')))
                ->add('endAtFr', 'text', array('label' => 'Date de fin', 'attr' => array('class' => 'datetimepicker')))
                ->add('place', 'text', array(
                    'label' => 'Lieu'
                ))
                ->add('proposedTeam', 'entity', array(
                    'class' => 'OmaracujaUserBundle:User',
                    'label' => 'Proposer à',
                    'property' => 'username', 'expanded' => false, 'multiple' => true))
                ->add('actualTeam', 'entity', array(
                    'class' => 'OmaracujaUserBundle:User',
                    'label' => 'Qui y va déjà',
                    'property' => 'username', 'expanded' => false, 'multiple' => true))
                ->add('mapX', 'hidden')
                ->add('mapY', 'hidden');
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
