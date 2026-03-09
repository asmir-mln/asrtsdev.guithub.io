<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulaire de donation complète ou anonyme
 */
class DonationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultData = $options['data'] ?? [];
        $isAnonyme = $defaultData === [] || ($defaultData['nom_donateur'] ?? null) === null;

        // ============================================
        // SECTION 1: MONTANT & TYPE
        // ============================================

        $builder
            ->add('montant', MoneyType::class, [
                'label' => '💰 Montant de la donation',
                'currency' => 'EUR',
                'divisor' => 100,
                'constraints' => [
                    new Assert\NotBlank(message: 'Veuillez entrer un montant'),
                    new Assert\Positive(message: 'Le montant doit être positif'),
                    new Assert\GreaterThanOrEqual([
                        'value' => 5,
                        'message' => 'Montant minimum: 5€'
                    ])
                ],
                'attr' => [
                    'placeholder' => '50',
                    'min' => '5',
                    'step' => '1',
                ],
                'help' => 'Minimum 5€ pour bénéficier d\'une contrepartie'
            ])

            ->add('typeDonation', ChoiceType::class, [
                'label' => '👤 Type de donation',
                'choices' => [
                    '✅ Donation complète (avec cadeau)' => 'complete',
                    '🔒 Donation anonyme (sans cadeau)' => 'anonyme',
                ],
                'expanded' => false,
                'help' => 'Choisissez "Anonyme" si vous préférez rester discret (pas de contrepartie)',
                'attr' => ['class' => 'donation-type-selector']
            ]);

        // ============================================
        // SECTION 2: INFORMATIONS DONATEUR
        // (Requises uniquement si NOT anonyme)
        // ============================================

        $donationCompleteOptions = [
            'label' => '👤 Nom complet',
            'required' => false,
            'attr' => [
                'placeholder' => 'Jean Dupont',
                'data-required-if-complete' => 'true',
                'class' => 'donation-complete-field'
            ],
            'help' => '⚠️ Requis pour recevoir votre cadeau'
        ];

        if (!$isAnonyme) {
            $donationCompleteOptions['constraints'] = [
                new Assert\NotBlank(message: 'Le nom est requis pour donation complète'),
                new Assert\Length(['min' => 3, 'minMessage' => 'Nom trop court (3 caractères min)'])
            ];
        }

        $builder->add('nomDonateur', TextType::class, $donationCompleteOptions);

        $builder
            ->add('emailDonateur', EmailType::class, [
                'label' => '✉️ Email',
                'required' => false,
                'attr' => [
                    'placeholder' => 'votre@email.com',
                    'data-required-if-complete' => 'true',
                    'class' => 'donation-complete-field'
                ],
                'help' => 'Pour confirmer et suivre votre donation',
                'constraints' => [
                    new Assert\Email(message: 'Email invalide')
                ]
            ])

            ->add('telephoneDonateur', TelType::class, [
                'label' => '📞 Téléphone (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => '+33 7 81 58 68 82',
                    'class' => 'donation-complete-field'
                ]
            ])

            ->add('adresseDonateur', TextareaType::class, [
                'label' => '📮 Adresse (requise pour l\'envoi du cadeau)',
                'required' => false,
                'rows' => 3,
                'attr' => [
                    'placeholder' => 'Numéro, rue, code postal, ville, pays',
                    'data-required-if-complete' => 'true',
                    'class' => 'donation-complete-field'
                ],
                'help' => '⚠️ Requise pour recevoir votre contrepartie'
            ]);

        // ============================================
        // SECTION 3: TYPE DE CONTREPARTIE
        // (Visible seulement si donation complète)
        // ============================================

        $builder
            ->add('typeCadeau', ChoiceType::class, [
                'label' => '🎁 Contrepartie souhaitée',
                'choices' => [
                    '👤 Remerciement personnel (5€+)' => 'remerciement',
                    '📚 Bibliothèque numérique 1 an (25€+)' => 'bibliotheque',
                    '🎨 Œuvre numérique exclusive (50€+)' => 'oeuvre',
                    '💝 Pack VIP donateur (100€+)' => 'vip',
                    '🏆 Reconnaissance publique (500€+)' => 'reconnaissance',
                    '👑 Partenariat stratégique (5000€+)' => 'partenariat',
                ],
                'placeholder' => '-- Choisir une contrepartie --',
                'required' => false,
                'attr' => [
                    'class' => 'donation-complete-field',
                    'data-required-if-complete' => 'true'
                ],
                'help' => 'Disponibilité selon votre montant de donation'
            ]);

        // ============================================
        // SECTION 4: COMMENTAIRES
        // ============================================

        $builder
            ->add('notes', TextareaType::class, [
                'label' => '💬 Message ou commentaire (optionnel)',
                'required' => false,
                'rows' => 3,
                'attr' => [
                    'placeholder' => 'Laissez-nous un message (publié si anonyme = non)',
                    'class' => 'form-control'
                ]
            ]);

        // ============================================
        // SECTION 5: CONDITIONS & ACCEPTATION
        // ============================================

        $builder
            ->add('accepteConditions', CheckboxType::class, [
                'label' => '✅ J\'accepte les conditions et la politique de confidentialité',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue(message: 'Vous devez accepter les conditions')
                ]
            ])

            ->add('accepteMarketing', CheckboxType::class, [
                'label' => '📧 Me tenir informé des nouvelles donations/projets',
                'required' => false,
                'mapped' => false,
                'help' => 'Vous pourrez vous désabonner à tout moment'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null, // On utilise pas d'entité directement
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'donation_form',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'donation_form';
    }
}
