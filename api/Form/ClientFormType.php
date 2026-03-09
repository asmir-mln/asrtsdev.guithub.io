<?php
/* AsArt'sDev | ClientFormType | Symfony Forms | Signature invisible | ASmir Milia */

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType, EmailType, TelType, ChoiceType, 
    TextareaType, CollectionType, FileType, SubmitType
};

class ClientFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Informations de base
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre nom'],
                'required' => true,
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre prénom'],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control', 'placeholder' => 'votre@email.com'],
                'required' => true,
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => ['class' => 'form-control', 'placeholder' => '0X XX XX XX XX'],
                'required' => false,
            ])
            
            // Adresse
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Rue, numéro...'],
                'required' => false,
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['class' => 'form-control', 'placeholder' => '75000'],
                'required' => false,
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Paris'],
                'required' => false,
            ])
            ->add('pays', TextType::class, [
                'label' => 'Pays',
                'attr' => ['class' => 'form-control', 'data-value' => 'France'],
                'data' => 'France',
                'required' => false,
            ])
            
            // Infos professionnelles
            ->add('entreprise', TextType::class, [
                'label' => 'Entreprise',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom de l\'entreprise'],
                'required' => false,
            ])
            ->add('fonction', TextType::class, [
                'label' => 'Fonction',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Directeur, Manager...'],
                'required' => false,
            ])
            
            // Type de client
            ->add('typeClient', ChoiceType::class, [
                'label' => 'Type de client',
                'choices' => [
                    'Particulier' => 'particulier',
                    'Entreprise' => 'entreprise',
                    'Partenaire' => 'partenaire',
                    'Investisseur' => 'investisseur',
                ],
                'attr' => ['class' => 'form-control'],
                'expanded' => false,
            ])
            
            // Intérêts (multi-select)
            ->add('interets', TextType::class, [
                'label' => 'Domaines d\'intérêt (séparés par des virgules)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'IA, Innovation, Tech...'],
                'required' => false,
                'help' => 'Ex: IA, Innovation inclusive, Livres...',
            ])
            
            // Notes internes
            ->add('notes', TextareaType::class, [
                'label' => 'Notes internes',
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Remarques, préférences...'],
                'required' => false,
            ])
            
            ->add('envoyer', SubmitType::class, [
                'label' => '✅ S\'enregistrer',
                'attr' => ['class' => 'btn btn-primary btn-lg mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'register_form',
        ]);
    }
}
