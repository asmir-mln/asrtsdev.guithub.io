<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('entreprise', TextType::class, [
                'constraints' => [new Assert\NotBlank()]
            ])
            ->add('raisonSociale', TextType::class)
            ->add('siret', TextType::class, [
                'constraints' => [new Assert\Length(['min' => 14, 'max' => 14])]
            ])
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('montant', NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan(['value' => 50000])
                ]
            ])
            ->add('justificatif', FileType::class, [
                'label' => 'Justificatif financier (PDF)',
                'mapped' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['application/pdf'],
                        'maxSize' => '5M'
                    ])
                ]
            ])
            ->add('strategie', TextareaType::class, [
                'label' => 'Pourquoi souhaitez-vous acceder a nos technologies ?',
                'constraints' => [new Assert\NotBlank()]
            ])
            ->add('nda', CheckboxType::class, [
                'label' => 'J\'accepte le NDA',
                'mapped' => false,
                'constraints' => [new Assert\IsTrue(['message' => 'Vous devez accepter le NDA'])]
            ]);
    }
}
