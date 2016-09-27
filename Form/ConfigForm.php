<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 02/10/2014
 * Time: 13:49
 */

namespace Be2Bill\Form;

use Be2Bill\Be2Bill;
use Be2Bill\Model\Be2billConfigQuery;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class ConfigForm extends BaseForm
{

    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $translator = Translator::getInstance();

        $this->formBuilder
            ->add(
                'activated',
                'text',
                array(
                    'constraints'   => array(new NotBlank()),
                    'required'      => true,
                    'label'         => $translator->trans('Activer le module', [], Be2Bill::MODULE_DOMAIN),
                    'data'          => Be2billConfigQuery::read('activated', 'yes'),
                    'label_attr'    => array('help' => $translator->trans('Active le paiement Be2Bill', [], Be2Bill::MODULE_DOMAIN))
                )
            )
            ->add(
                'identifier',
                'text',
                array(
                    'constraints'   => array(new NotBlank()),
                    'required'      => true,
                    'label'         => $translator->trans('Identifiant du compte Be 2 Bill', [], Be2Bill::MODULE_DOMAIN),
                    'data'          => Be2billConfigQuery::read('identifier'),
                    'label_attr'    => array('help' => $translator->trans('Identifiant du compte', [], Be2Bill::MODULE_DOMAIN))
                )
            )
            ->add(
                'password',
                'text',
                array(
                    'label' => $translator->trans('Mot de passe', [], Be2Bill::MODULE_DOMAIN),
                    'data' => Be2billConfigQuery::read('password'),
                    'label_attr' => array('help' => $translator->trans('Mot de passe Be2Bill', [], Be2Bill::MODULE_DOMAIN))
                )
            )
            ->add(
                'description',
                'textarea',
                array(
                    'label' => $translator->trans('Description', [], Be2Bill::MODULE_DOMAIN),
                    'data' => Be2billConfigQuery::read('description', 'Commande Be2Bill'),
                    'label_attr' => array('help' =>  $translator->trans('Description du panier de la transaction / chaîne 510 max', [], Be2Bill::MODULE_DOMAIN))
                )
            )
            ->add(
                'url',
                'text',
                array(
                    'label' => 'Url',
                    'constraints' => array(new NotBlank()),
                    'data' => Be2billConfigQuery::read('url', $translator->trans('[type d\'environnement].be2bill.com', [], Be2Bill::MODULE_DOMAIN)),
                    'required' => true,
                    'label_attr' => array('help' => $translator->trans('Url Web Service fournis par be2bill, exemple : "secure-magenta.be2bill.com" .', [], Be2Bill::MODULE_DOMAIN))
                )
            )
            ->add(
                '3dsecure',
                'text',
                array(
                    'label' => '3DSecure',
                    'data' => Be2billConfigQuery::read('3dsecure', "no")
                )
            )
            ->add(
                'ntimes',
                'text',
                array(
                    'label' => 'Paiement en plusieurs fois',
                    'data' => Be2billConfigQuery::read('ntimes', "no")
                )
            )
            ->add(
                'ntimes-count',
                'integer',
                array(
                    'constraints'   => [new GreaterThan(['value' => 1])],
                    'required'      => true,
                    'label'         => $translator->trans('Nombre d\'échéances', [], Be2Bill::MODULE_DOMAIN),
                    'data'          => Be2billConfigQuery::read('ntimes-count', 3),
                    'label_attr'    => array(
                        'help' => $translator->trans('Nombre d\'échéances', [], Be2Bill::MODULE_DOMAIN)
                    )
                )
            )
            ->add(
                'ntimes-interval',
                'text',
                [
                    'constraints'   => [
                        new Callback(
                            [
                                "methods" => [
                                    [$this, "verifyDateInterval"]
                                ],
                            ]
                        ),
                    ],
                    'label' => $translator->trans('Intervalle entre 2 échéances', [], Be2Bill::MODULE_DOMAIN),
                    'data' => Be2billConfigQuery::read('ntimes-interval', 'P1M'),
                    'label_attr' => [
                        'help' => $translator->trans(
                            "Le format utilisé est celui de l'<a href='%link' target='_blank'>ISO 8601</a>",
                            ['%link' => 'https://en.wikipedia.org/wiki/ISO_8601#Durations'],
                            Be2Bill::MODULE_DOMAIN
                        )
                    ]
                ]
            )
            ->add(
                'paypal',
                'text',
                array(
                    'label' => 'PayPal',
                    'data' => Be2billConfigQuery::read('paypal', "no")
                )
            )
            ->add(
                'paypal-identifier',
                'text',
                array(
                    'constraints'   => [],
                    'required'      => true,
                    'label'         => $translator->trans('Identifiant du compte Be 2 Bill pour PayPal', [], Be2Bill::MODULE_DOMAIN),
                    'data'          => Be2billConfigQuery::read('paypal-identifier'),
                    'label_attr'    => array('help' => $translator->trans('Identifiant du compte', [], Be2Bill::MODULE_DOMAIN))
                )
            )
            ->add(
                'paypal-password',
                'text',
                array(
                    'label' => $translator->trans('Mot de passe pour PayPal', [], Be2Bill::MODULE_DOMAIN),
                    'data' => Be2billConfigQuery::read('paypal-password'),
                    'label_attr' => array('help' => $translator->trans('Mot de passe Be2Bill pour PayPal', [], Be2Bill::MODULE_DOMAIN))
                )
            )
            ->add(
                'cancel-on-refund',
                'text',
                array(
                    'constraints'   => array(new NotBlank()),
                    'required'      => true,
                    'label'         => $translator->trans('Annuler la commande lors d\'un remboursement', [], Be2Bill::MODULE_DOMAIN),
                    'data'          => Be2billConfigQuery::read('cancel-on-refund', 'no'),
                    'label_attr'    => array('help' => $translator->trans('Passe la commande en statut annulée lors d\'un remboursement', [], Be2Bill::MODULE_DOMAIN))
                )
            )
        ;
    }

    public function verifyDateInterval($value, ExecutionContextInterface $context)
    {
        $data = $context->getRoot()->getData();

        if ($data["ntimes"] == 'yes') {
            try {
                new \DateInterval($value);
            } catch (\Exception $ex) {
                $context->addViolation(
                    Translator::getInstance()->trans(
                        "The interval should respect the ISO 8601 format",
                        [],
                        Be2Bill::MODULE_DOMAIN
                    )
                );
            }

        }

    }


    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "be2bill_configuration_form";
    }
}
