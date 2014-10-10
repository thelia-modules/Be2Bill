<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 02/10/2014
 * Time: 13:49
 */

namespace Be2Bill\Form;


use Be2Bill\Model\Be2billConfigQuery;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

class ConfigForm  extends BaseForm
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
        $this->formBuilder
            ->add(
                'identifier',
                'text',
                array(
                    'constraints'   => array(new NotBlank()),
                    'required'      => true,
                    'label'         => 'Identifier',
                    'data'          => Be2billConfigQuery::read('identifier'),
                    'label_attr'    => array(
                                        'help' => 'Identifiant du compte'
                                    )
                )
            )
            ->add(
                'password',
                'text',
                array(
                    'label' => 'Mot de passe',
                    'data' => Be2billConfigQuery::read('password'),
                    'label_attr'    => array(
                                            'help' => 'Mote de passe Be2Bill'
                                    )
                )
            )
            ->add(
                'description',
                'textarea',
                array(
                    'label' => 'Description',
                    'data' => Be2billConfigQuery::read('description', 'Commande Be2Bill'),
                    'label_attr' => array(
                        'help' => 'Description du panier de la transaction / chaîne 510 max'
                    )
                )
            )
            ->add(
                'url',
                'text',
                array(
                    'label' => 'Url',
                    'constraints' => array(new NotBlank()),
                    'data' => Be2billConfigQuery::read('url', '[type d\'environnement].be2bill.com'),
                    'required' => true,
                    'label_attr' => array(
                        'help' => 'Url Web Service'
                    )

                )
            )
            ->add(
                '3dsecure',
                'text',
                array(
                    'label' => '3DSecure',
                    'data' => Be2billConfigQuery::read('3dsecure', 0)
                )
            );
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "be2bill_configuration_form";
    }
}