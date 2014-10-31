<?php

namespace Omaracuja\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VideoType extends AbstractType {

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
                ->add('url', 'text', array(
                    'label' => 'Url de la vidéo',
                    'required' => true
                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Omaracuja\FrontBundle\Entity\Video'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'omaracuja_frontbundle_video';
    }

}
