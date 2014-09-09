<?php

namespace Omaracuja\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BlogPostType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content','textarea', array(
        'attr' => array(
            'class' => 'summernote'
        )))->add('title')->add('public', 'checkbox', array(
                    'label' => 'Post publique'
                ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Omaracuja\FrontBundle\Entity\BlogPost'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'omaracuja_frontbundle_blogpost';
    }
}
